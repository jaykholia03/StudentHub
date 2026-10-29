
<?php
include "database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    header("Content-Type: application/json; charset=UTF-8");

    $fullname = trim($_POST["fullname"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $dob = $_POST["dob"] ?? "";
    $gender = $_POST["gender"] ?? "";
    $address = trim($_POST["address"] ?? "");

    $errors = [];

    // Check whether passwords match
    if ($password !== $confirm_password) {
        $errors["confirm_password"] = "Passwords do not match.";
    }

    // Check whether username already exists
    $checkUsername = $conn->prepare(
        "SELECT id FROM users WHERE username = ?"
    );

    if (!$checkUsername) {
        echo json_encode([
            "success" => false,
            "message" => "Database error. Please try again."
        ]);
        exit;
    }

    $checkUsername->bind_param("s", $username);
    $checkUsername->execute();

    $usernameResult = $checkUsername->get_result();

    if ($usernameResult->num_rows > 0) {
        $errors["username"] =
            "Username already exists. Please choose another.";
    }

    $checkUsername->close();

    // Check whether email already exists
    $checkEmail = $conn->prepare(
        "SELECT id FROM users WHERE email = ?"
    );

    if (!$checkEmail) {
        echo json_encode([
            "success" => false,
            "message" => "Database error. Please try again."
        ]);
        exit;
    }

    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();

    $emailResult = $checkEmail->get_result();

    if ($emailResult->num_rows > 0) {
        $errors["email"] =
            "Email already exists. Please use another email.";
    }

    $checkEmail->close();

    // Return errors on the same page
    if (!empty($errors)) {
        echo json_encode([
            "success" => false,
            "errors" => $errors
        ]);

        exit;
    }

    // Hash password before saving it
    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    // Insert new user
    $sql = "INSERT INTO users
            (full_name, username, email, phone, password, dob, gender, address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "Database error. Please try again."
        ]);

        exit;
    }

    $stmt->bind_param(
        "ssssssss",
        $fullname,
        $username,
        $email,
        $phone,
        $hashedPassword,
        $dob,
        $gender,
        $address
    );

    if ($stmt->execute()) {

        $newUserId = $stmt->insert_id;

        // Create student profile
        $profileStmt = $conn->prepare(
            "INSERT INTO student_profiles (user_id) VALUES (?)"
        );

        if ($profileStmt) {
            $profileStmt->bind_param("i", $newUserId);
            $profileStmt->execute();
            $profileStmt->close();
        }

        echo json_encode([
            "success" => true,
            "message" => "Registration successful!"
        ]);

    } else {
        echo json_encode([
            "success" => false,
            "message" => "Registration failed. Please try again."
        ]);
    }

    $stmt->close();
    $conn->close();

    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - StudentHub</title>

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
            min-height: 100vh;
        }

        body {
            transition: 0.3s;
        }

        .container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* LEFT IMAGE */

        .left {
            flex: 1;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .left img {
            width: 100%;
            max-width: 900px;
            height: auto;
            object-fit: cover;
            background-color: #e0e0e0;
            border: 1px solid #ccc;
        }

        /* RIGHT FORM */

        .right {
            flex: 1;
            background-color: #eeeeee;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px 40px;
            transition: 0.3s;
        }

        .form-box {
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .form-box h2 {
            font-size: 2rem;
            font-weight: 500;
            margin-bottom: 30px;
            color: #111111;
        }

        /* INPUTS */

        .form-box input,
        .form-box select,
        .form-box textarea {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 4px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
            background-color: #ffffff;
            color: #333333;
            transition: 0.2s;
        }

        .form-box input:focus,
        .form-box select:focus,
        .form-box textarea:focus {
            border-color: #2b7fff;
        }

        .form-box input::placeholder,
        .form-box textarea::placeholder {
            color: #b0b0b0;
        }

        .form-box select {
            appearance: none;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath fill='%23666666' d='M0 0h12L6 8z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 10px auto;
        }

        .form-box textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* VALIDATION MESSAGES */

        .error {
            display: block;
            min-height: 18px;
            margin: 0 0 8px 3px;
            text-align: left;
            color: #e53935;
            font-size: 0.78rem;
        }

        .input-error {
            border-color: #e53935 !important;
            background-color: #fff8f8 !important;
        }

        .input-success {
            border-color: #28a745 !important;
        }

        /* REGISTER BUTTON */

        .register-btn {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            background-color: #8c8c8c;
            color: #000000;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
        }

        .register-btn:hover {
            background-color: #777777;
            color: #ffffff;
        }

        .register-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .login-text {
            margin-top: 25px;
            font-size: 0.95rem;
            color: #111111;
        }

        .login-text a {
            color: #2b7fff;
            text-decoration: none;
        }

        .login-text a:hover {
            text-decoration: underline;
        }

        /* THEME BUTTON */

        #themeBtn {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 45px;
            height: 45px;
            border: none;
            border-radius: 50%;
            background-color: #ffffff;
            color: #111111;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: 0.3s;
        }

        #themeBtn:hover {
            transform: scale(1.05);
        }

        /* DARK MODE */

        body.dark {
            background-color: #121212;
            color: #eeeeee;
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

        body.dark .form-box input,
        body.dark .form-box select,
        body.dark .form-box textarea {
            background-color: #2a2a2a;
            color: #ffffff;
            border-color: #444444;
        }

        body.dark .form-box input::placeholder,
        body.dark .form-box textarea::placeholder {
            color: #999999;
        }

        body.dark .login-text {
            color: #eeeeee;
        }

        body.dark #themeBtn {
            background-color: #333333;
            color: #ffffff;
        }

        body.dark .input-error {
            background-color: #351f1f !important;
        }

        /* MOBILE */

        @media (max-width: 768px) {
            .container {
                min-height: 100vh;
                background-image: url("images/register.png");
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .left {
                display: none;
            }

            .right {
                width: 100%;
                min-height: 100vh;
                padding: 80px 20px 30px;
                background-color: rgba(0, 0, 0, 0.45);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .form-box {
                width: 100%;
                max-width: 420px;
                padding: 30px 22px;
                background-color: rgba(255, 255, 255, 0.93);
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            }

            .form-box h2 {
                font-size: 1.8rem;
                margin-bottom: 25px;
            }

            .form-box input,
            .form-box select,
            .form-box textarea {
                padding: 12px 14px;
            }

            .register-btn {
                padding: 13px;
            }

            body.dark .right {
                background-color: rgba(0, 0, 0, 0.65);
            }

            body.dark .form-box {
                background-color: rgba(30, 30, 30, 0.95);
            }
        }

        /* SMALL MOBILE */

        @media (max-width: 400px) {
            .right {
                padding: 70px 12px 20px;
            }

            .form-box {
                padding: 25px 17px;
            }

            .form-box h2 {
                font-size: 1.6rem;
                margin-bottom: 20px;
            }

            .form-box input,
            .form-box select,
            .form-box textarea {
                font-size: 0.9rem;
                padding: 11px 12px;
            }

            .register-btn {
                font-size: 1rem;
            }

            .login-text {
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>

    <button id="themeBtn" type="button" aria-label="Toggle dark mode">
        🌙
    </button>

    <div class="container">

        <div class="left">
            <img src="images/register.png" alt="Register Image">
        </div>

        <div class="right">

            <div class="form-box">

                <h2>Register</h2>

                <form id="registerForm" action="register.php" method="POST" novalidate>

                    <input
                        type="text"
                        id="fullname"
                        name="fullname"
                        placeholder="Full Name"
                    >
                    <span class="error" id="fullnameError"></span>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Username"
                    >
                    <span class="error" id="usernameError"></span>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Email"
                    >
                    <span class="error" id="emailError"></span>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        placeholder="Phone Number"
                    >
                    <span class="error" id="phoneError"></span>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Password"
                    >
                    <span class="error" id="passwordError"></span>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm Password"
                    >
                    <span class="error" id="confirmPasswordError"></span>

                    <input
                        type="date"
                        id="dob"
                        name="dob"
                        title="Date of Birth"
                    >
                    <span class="error" id="dobError"></span>

                    <select id="gender" name="gender">
                        <option value="" disabled selected>
                            Select Gender
                        </option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    <span class="error" id="genderError"></span>

                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        placeholder="Address"
                    ></textarea>
                    <span class="error" id="addressError"></span>

                    <button type="submit" class="register-btn">
                        Register
                    </button>

                </form>

                <p class="login-text">
                    Already have an account?
                    <a href="login.php">Login</a>
                </p>

            </div>

        </div>

    </div>

    <script>
        const themeBtn = document.getElementById("themeBtn");
        const registerForm = document.getElementById("registerForm");
        const registerBtn = registerForm.querySelector(".register-btn");

        // Dark mode
        themeBtn.addEventListener("click", function () {
            document.body.classList.toggle("dark");

            themeBtn.textContent =
                document.body.classList.contains("dark") ? "☀️" : "🌙";
        });

        // Form validation
        registerForm.addEventListener("submit", function (event) {
            event.preventDefault();

            let isValid = true;

            const fullname = document.getElementById("fullname").value.trim();
            const username = document.getElementById("username").value.trim();
            const email = document.getElementById("email").value.trim();
            const phone = document.getElementById("phone").value.trim();
            const password = document.getElementById("password").value;
            const confirmPassword =
                document.getElementById("confirm_password").value;
            const dob = document.getElementById("dob").value;
            const gender = document.getElementById("gender").value;
            const address = document.getElementById("address").value.trim();

            const namePattern = /^[A-Za-z ]+$/;
            const usernamePattern = /^[A-Za-z0-9_]{4,20}$/;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phonePattern = /^[0-9]{10}$/;
            const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;

            clearErrors();

            // Full name
            if (fullname === "") {
                showError("fullname", "fullnameError", "Full name is required.");
                isValid = false;
            } else if (!namePattern.test(fullname)) {
                showError(
                    "fullname",
                    "fullnameError",
                    "Name should contain only letters and spaces."
                );
                isValid = false;
            } else if (fullname.length < 3) {
                showError(
                    "fullname",
                    "fullnameError",
                    "Name must contain at least 3 characters."
                );
                isValid = false;
            } else {
                showSuccess("fullname");
            }

            // Username
            if (username === "") {
                showError("username", "usernameError", "Username is required.");
                isValid = false;
            } else if (!usernamePattern.test(username)) {
                showError(
                    "username",
                    "usernameError",
                    "Username must be 4-20 characters using letters, numbers or underscores."
                );
                isValid = false;
            } else {
                showSuccess("username");
            }

            // Email
            if (email === "") {
                showError("email", "emailError", "Email is required.");
                isValid = false;
            } else if (!emailPattern.test(email)) {
                showError(
                    "email",
                    "emailError",
                    "Enter a valid email address."
                );
                isValid = false;
            } else {
                showSuccess("email");
            }

            // Phone
            if (phone === "") {
                showError("phone", "phoneError", "Phone number is required.");
                isValid = false;
            } else if (!phonePattern.test(phone)) {
                showError(
                    "phone",
                    "phoneError",
                    "Phone number must contain exactly 10 digits."
                );
                isValid = false;
            } else {
                showSuccess("phone");
            }

            // Password
            if (password === "") {
                showError("password", "passwordError", "Password is required.");
                isValid = false;
            } else if (!passwordPattern.test(password)) {
                showError(
                    "password",
                    "passwordError",
                    "Password must be at least 8 characters and contain a letter and number."
                );
                isValid = false;
            } else {
                showSuccess("password");
            }

            // Confirm password
            if (confirmPassword === "") {
                showError(
                    "confirm_password",
                    "confirmPasswordError",
                    "Please confirm your password."
                );
                isValid = false;
            } else if (password !== confirmPassword) {
                showError(
                    "confirm_password",
                    "confirmPasswordError",
                    "Passwords do not match."
                );
                isValid = false;
            } else {
                showSuccess("confirm_password");
            }

            // Date of birth
            if (dob === "") {
                showError("dob", "dobError", "Date of birth is required.");
                isValid = false;
            } else {
                const selectedDate = new Date(dob + "T00:00:00");
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (selectedDate > today) {
                    showError(
                        "dob",
                        "dobError",
                        "Date of birth cannot be in the future."
                    );
                    isValid = false;
                } else {
                    showSuccess("dob");
                }
            }

            // Gender
            if (gender === "") {
                showError(
                    "gender",
                    "genderError",
                    "Please select your gender."
                );
                isValid = false;
            } else {
                showSuccess("gender");
            }

            // Address
            if (address === "") {
                showError("address", "addressError", "Address is required.");
                isValid = false;
            } else if (address.length < 10) {
                showError(
                    "address",
                    "addressError",
                    "Address must contain at least 10 characters."
                );
                isValid = false;
            } else {
                showSuccess("address");
            }

            // Submit without reloading the page
            if (isValid) {
                registerBtn.disabled = true;
                registerBtn.textContent = "Registering...";

                fetch("register.php", {
                    method: "POST",
                    body: new FormData(registerForm),
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error("Server returned an error.");
                    }

                    return response.json();
                })
                .then(function (data) {

                    // Display duplicate username/email errors
                    if (data.errors) {

                        if (data.errors.username) {
                            showError(
                                "username",
                                "usernameError",
                                data.errors.username
                            );
                        }

                        if (data.errors.email) {
                            showError(
                                "email",
                                "emailError",
                                data.errors.email
                            );
                        }

                        if (data.errors.confirm_password) {
                            showError(
                                "confirm_password",
                                "confirmPasswordError",
                                data.errors.confirm_password
                            );
                        }

                        return;
                    }

                    if (data.success) {
                        alert(data.message);
                        window.location.href = "login.php";
                    } else {
                        alert(data.message || "Registration failed.");
                    }

                })
                .catch(function (error) {
                    console.error(error);
                    alert(
                        "Unable to process registration. Please check your connection and try again."
                    );
                })
                .finally(function () {
                    registerBtn.disabled = false;
                    registerBtn.textContent = "Register";
                });
            }
        });

        // Display validation error
        function showError(inputId, errorId, message) {
            const input = document.getElementById(inputId);
            const error = document.getElementById(errorId);

            input.classList.add("input-error");
            input.classList.remove("input-success");
            error.textContent = message;
        }

        // Display valid input
        function showSuccess(inputId) {
            const input = document.getElementById(inputId);

            input.classList.remove("input-error");
            input.classList.add("input-success");
        }

        // Clear previous errors
        function clearErrors() {
            const inputs = registerForm.querySelectorAll(
                "input, select, textarea"
            );

            inputs.forEach(function (input) {
                input.classList.remove("input-error", "input-success");
            });

            const errors = registerForm.querySelectorAll(".error");

            errors.forEach(function (error) {
                error.textContent = "";
            });
        }
    </script>

</body>
</html>
