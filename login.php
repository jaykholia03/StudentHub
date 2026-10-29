<?php
    session_start();
    include 'database.php';

    // Redirect if already logged in
    if (isset($_SESSION['user_id'])) {
        header('Location: ' . login_home($_SESSION['role'] ?? ''));
        exit;
    }

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email !== '' && $password !== '') {

            $stmt = $conn->prepare(
                "SELECT id, full_name, username, email, password, role, status
                 FROM users WHERE email = ?"
            );
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {

                $user = $result->fetch_assoc();

                if ($user['status'] !== 'active') {
                    $error = 'This account is not active.';
                } elseif (password_verify($password, $user['password'])) {

                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['username']  = $user['username'];
                    $_SESSION['email']     = $user['email'];
                    $_SESSION['role']      = $user['role'];

                    $role = strtolower(trim((string) $user['role']));

                    if ($role === 'admin') {
                        header('Location: admin/admin.php');
                        exit;
                    } elseif ($role === 'user' || $role === 'student' || $role === 'professor') {
                        header('Location: dashboard.php');
                        exit;
                    } else {
                        session_unset();
                        session_destroy();
                        $error = 'This account does not have access.';
                    }

                } else {
                    $error = 'Invalid email or password.';
                }

            } else {
                $error = 'Invalid email or password.';
            }

            $stmt->close();

        } else {
            $error = 'Please enter email and password.';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StudentHub</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
        }

        body {
            background-color: #ffffff;
            transition: 0.3s;
        }

        /* =========================
           THEME BUTTON
        ========================= */

        #themeBtn {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 42px;
            height: 42px;
            border: none;
            border-radius: 50%;
            background-color: #f1f1f1;
            font-size: 20px;
            cursor: pointer;
            z-index: 100;
        }

        #themeBtn:hover {
            background-color: #dddddd;
        }


        /* =========================
           MAIN CONTAINER
        ========================= */

        .container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }


        /* =========================
           LEFT IMAGE
        ========================= */

        .left {
            flex: 1;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .left img {
            display: block;
            width: 100%;
            max-width: 600px;
            height: auto;
            max-height: 80vh;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 8px;
        }


        /* =========================
           RIGHT LOGIN SECTION
        ========================= */

        .right {
            flex: 1;
            background-color: #eeeeee;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }


        /* =========================
           LOGIN FORM
        ========================= */

        .form-box {
            width: 100%;
            max-width: 380px;
            text-align: center;
        }

        .form-box h2 {
            font-size: 2rem;
            font-weight: 500;
            margin-bottom: 35px;
            color: #111111;
        }

        .form-box input[type="email"],
        .form-box input[type="password"] {
            width: 100%;
            padding: 14px 18px;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            background-color: #ffffff;
            color: #222222;
        }

        .form-box input::placeholder {
            color: #b0b0b0;
        }

        .form-box input:focus {
            border-color: #2b7fff;
        }


        /* =========================
           FORGOT PASSWORD
        ========================= */

        .forgot {
            text-align: right;
            margin-top: -10px;
            margin-bottom: 25px;
        }

        .forgot a {
            color: #2b7fff;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .forgot a:hover {
            text-decoration: underline;
        }


        /* =========================
           LOGIN BUTTON
        ========================= */

        .login-btn {
            width: 100%;
            padding: 14px;
            background-color: #8c8c8c;
            color: #000000;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
        }

        .login-btn:hover {
            background-color: #777777;
            color: #ffffff;
        }


        /* =========================
           REGISTER
        ========================= */

        .register-text {
            margin-top: 35px;
            font-size: 1rem;
            color: #111111;
        }

        .register-text a {
            color: #2b7fff;
            text-decoration: none;
        }

        .register-text a:hover {
            text-decoration: underline;
        }


        /* =========================
           DARK MODE
        ========================= */

        body.dark {
            background-color: #121212;
        }

        body.dark .left {
            background-color: #121212;
        }

        body.dark .right {
            background-color: #1e1e1e;
        }

        body.dark .form-box h2 {
            color: #ffffff;
        }

        body.dark .register-text {
            color: #eeeeee;
        }

        body.dark .form-box input[type="email"],
        body.dark .form-box input[type="password"] {
            background-color: #292929;
            border-color: #444444;
            color: #ffffff;
        }

        body.dark .form-box input::placeholder {
            color: #aaaaaa;
        }

        body.dark .login-btn {
            background-color: #555555;
            color: #ffffff;
        }

        body.dark .login-btn:hover {
            background-color: #666666;
        }

        body.dark #themeBtn {
            background-color: #333333;
            color: #ffffff;
        }


        /* =================================================
           MOBILE
           IMAGE BECOMES BACKGROUND
        ================================================= */

        @media (max-width: 768px) {

            body {
                min-height: 100vh;
            }

            .container {
                position: relative;
                width: 100%;
                min-height: 100vh;

                /* Image becomes background */
                background-image: url("images/login.jpg");
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;

                display: flex;
                align-items: center;
                justify-content: center;
            }


            /* Hide original image section */
            .left {
                display: none;
            }


            /* Login area becomes transparent */
            .right {
                width: 100%;
                min-height: 100vh;
                padding: 30px 20px;

                background-color: rgba(0, 0, 0, 0.45);

                display: flex;
                align-items: center;
                justify-content: center;
            }


            /* Login form box */
            .form-box {
                width: 100%;
                max-width: 400px;

                padding: 35px 25px;

                background-color: rgba(255, 255, 255, 0.93);

                border-radius: 15px;

                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            }


            .form-box h2 {
                font-size: 1.8rem;
                margin-bottom: 30px;
            }


            /* Dark mode form */
            body.dark .right {
                background-color: rgba(0, 0, 0, 0.65);
            }

            body.dark .form-box {
                background-color: rgba(30, 30, 30, 0.95);
            }


            #themeBtn {
                top: 15px;
                right: 15px;
            }
        }


        @media (max-width: 480px) {

            .right {
                padding: 20px 15px;
            }

            .form-box {
                padding: 30px 20px;
                max-width: 360px;
            }

            .form-box h2 {
                font-size: 1.6rem;
                margin-bottom: 25px;
            }

            .form-box input[type="email"],
            .form-box input[type="password"] {
                padding: 13px 15px;
                font-size: 0.95rem;
            }

            .login-btn {
                padding: 13px;
                font-size: 1rem;
            }

            .register-text {
                font-size: 0.9rem;
                margin-top: 25px;
            }
        }

    </style>
</head>

<body>

    <!-- Theme Button -->
    <button id="themeBtn">🌙</button>


    <!-- Main Container -->
    <div class="container">

        <!-- Desktop Image -->
        <div class="left">
            <img src="images/login.jpg" alt="Project Image">
        </div>


        <!-- Login Section -->
        <div class="right">

            <div class="form-box">

                <h2>Login</h2>

                <?php if ($error): ?>
                    <p style="color: #d9534f; font-size: 0.9rem; margin-bottom: 15px;">
                        <?php echo htmlspecialchars($error); ?>
                    </p>
                <?php endif; ?>

                <form method="POST" action="">

                    <input
                        type="email"
                        name="email"
                        placeholder="Email"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required
                    >

                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        required
                    >

                    <div class="forgot">
                        <a href="#">Forgot Password?</a>
                    </div>

                    <button
                        type="submit"
                        class="login-btn">
                        Login
                    </button>

                </form>

                <p class="register-text">
                    Don't have any account?
                    <a href="register.php">Register</a>
                </p>

            </div>

        </div>

    </div>


    <!-- JavaScript -->
    <script>

        let themeBtn = document.getElementById("themeBtn");

        themeBtn.addEventListener("click", function() {

            document.body.classList.toggle("dark");

            if (document.body.classList.contains("dark")) {

                themeBtn.innerHTML = "☀️";

            } else {

                themeBtn.innerHTML = "🌙";

            }

        });

    </script>

</body>
</html>