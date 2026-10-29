<?php
    include 'database.php';

    $departments = db_all(
        "SELECT department_name, description
         FROM departments
         WHERE status = 'active' AND publicly_visible = 1
         ORDER BY department_name
         LIMIT 6"
    );

    $studentCount = db_count("SELECT COUNT(*) AS total FROM users WHERE role = 'student'");
    $courseCount  = db_count("SELECT COUNT(*) AS total FROM courses WHERE status = 'active'");
    $eventCount   = db_count("SELECT COUNT(*) AS total FROM events WHERE status = 'published'");
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>StudentHub - Home</title>


    <style>

        /* =====================================================
           GLOBAL
        ===================================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;

            font-family:
                'Segoe UI',
                system-ui,
                -apple-system,
                sans-serif;
        }


        html {
            scroll-behavior: smooth;
        }


        body {
            min-height: 100vh;

            background: #f4f7f6;

            color: #222;

            transition:
                background 0.3s ease,
                color 0.3s ease;
        }


        a {
            text-decoration: none;
        }


        button {
            font-family: inherit;
            cursor: pointer;
        }



        /* =====================================================
           HEADER
        ===================================================== */

        header {
            width: 100%;

            position: sticky;

            top: 0;

            z-index: 1000;

            background: rgba(255, 255, 255, 0.95);

            border-bottom: 1px solid #eeeeee;

            box-shadow:
                0 2px 10px rgba(0, 0, 0, 0.05);

            backdrop-filter: blur(10px);
        }


        .navbar {
            max-width: 1200px;

            min-height: 75px;

            margin: auto;

            padding: 0 2rem;

            display: flex;

            align-items: center;

            justify-content: space-between;
        }



        /* =====================================================
           LOGO
        ===================================================== */

        .logo {
            display: flex;

            align-items: center;

            gap: 10px;
        }


        .logo img {
            width: 48px;

            height: 48px;

            border-radius: 50%;

            object-fit: cover;

            background: #ffffff;

            border: 1px solid #eeeeee;
        }


        .logo-text {
            font-size: 1.35rem;

            font-weight: 700;

            color: #222;
        }



        /* =====================================================
           NAVIGATION
        ===================================================== */

        .nav-links {
            display: flex;

            align-items: center;

            gap: 32px;
        }


        .nav-links a {
            color: #333;

            font-size: 0.95rem;

            font-weight: 600;

            transition: 0.3s;

            position: relative;
        }


        .nav-links a::after {
            content: "";

            position: absolute;

            left: 0;

            bottom: -7px;

            width: 0;

            height: 2px;

            background: #222;

            transition: 0.3s;
        }


        .nav-links a:hover::after {
            width: 100%;
        }



        /* =====================================================
           THEME BUTTON
        ===================================================== */

        #themeBtn {
            width: 40px;

            height: 40px;

            border: none;

            border-radius: 8px;

            background: #f1f3f5;

            color: #222;

            font-size: 20px;

            display: flex;

            align-items: center;

            justify-content: center;

            transition: 0.3s;
        }


        #themeBtn:hover {
            background: #e5e7eb;

            transform: translateY(-1px);
        }



        /* =====================================================
           HAMBURGER
        ===================================================== */

        .hamburger {
            display: none;

            width: 40px;

            height: 40px;

            border: none;

            border-radius: 8px;

            background: #f1f3f5;

            color: #222;

            font-size: 22px;

            align-items: center;

            justify-content: center;
        }



        /* =====================================================
           HERO
        ===================================================== */

        .hero-section {
            max-width: 1150px;

            min-height: calc(100vh - 75px);

            margin: auto;

            padding: 70px 2rem;

            display: grid;

            grid-template-columns: 1fr 1fr;

            align-items: center;

            gap: 60px;
        }


        .hero-text {
            max-width: 600px;
        }


        .hero-label {
            display: inline-block;

            padding: 7px 14px;

            margin-bottom: 20px;

            border-radius: 20px;

            background: #eeeeee;

            color: #444;

            font-size: 0.8rem;

            font-weight: 600;
        }


        .hero-text h1 {
            font-size: clamp(2.7rem, 5vw, 4.5rem);

            line-height: 1.05;

            font-weight: 700;

            letter-spacing: -2px;

            margin-bottom: 24px;
        }


        .hero-text h1 span {
            display: block;
        }


        .hero-text p {
            max-width: 520px;

            color: #666;

            font-size: 1.05rem;

            line-height: 1.7;

            margin-bottom: 32px;
        }



        /* =====================================================
           BUTTONS
        ===================================================== */

        .button-group {
            display: flex;

            align-items: center;

            gap: 12px;

            flex-wrap: wrap;
        }


        .btn {
            display: inline-flex;

            align-items: center;

            justify-content: center;

            min-width: 120px;

            padding: 12px 25px;

            border-radius: 8px;

            font-size: 0.95rem;

            font-weight: 600;

            transition: 0.3s;
        }


        .btn-primary {
            background: #111;

            color: #fff;

            border: 1px solid #111;
        }


        .btn-secondary {
            background: #fff;

            color: #111;

            border: 1px solid #111;
        }


        .btn:hover {
            transform: translateY(-2px);

            opacity: 0.9;
        }



        /* =====================================================
           HERO IMAGE
        ===================================================== */

        .hero-image {
            display: flex;

            justify-content: center;

            align-items: center;
        }


        .hero-image img {
            width: 100%;

            max-width: 480px;

            height: 350px;

            object-fit: cover;

            border-radius: 18px;

            border: 1px solid #dddddd;

            box-shadow:
                0 20px 50px rgba(0, 0, 0, 0.08);
        }



        /* =====================================================
           ABOUT
        ===================================================== */

        .about-section {
            max-width: 1150px;

            margin: 0 auto;

            padding: 90px 2rem;

            border-top: 1px solid #dddddd;
        }


        .about-heading {
            max-width: 700px;

            margin-bottom: 45px;
        }


        .about-heading h2 {
            font-size: 2.5rem;

            margin-bottom: 15px;
        }


        .about-heading p {
            color: #666;

            font-size: 1rem;

            line-height: 1.7;
        }


        .about-cards {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;
        }


        .card {
            padding: 28px;

            background: #ffffff;

            border: 1px solid #e3e3e3;

            border-radius: 14px;

            transition: 0.3s;
        }


        .card:hover {
            transform: translateY(-5px);

            box-shadow:
                0 15px 35px rgba(0, 0, 0, 0.08);
        }


        .card-number {
            width: 38px;

            height: 38px;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom: 20px;

            border-radius: 50%;

            background: #111;

            color: #fff;

            font-size: 0.85rem;

            font-weight: 700;
        }


        .card h3 {
            margin-bottom: 10px;

            font-size: 1.25rem;
        }


        .card p {
            color: #666;

            line-height: 1.6;

            font-size: 0.95rem;
        }



        /* =====================================================
           FOOTER
        ===================================================== */

        footer {
            margin-top: 40px;

            padding: 25px 20px;

            text-align: center;

            background: #e5e5e5;

            color: #444;

            font-size: 0.9rem;
        }



        /* =====================================================
           DARK MODE
        ===================================================== */

        body.dark {
            background: #121212;

            color: #eeeeee;
        }


        body.dark header {
            background: rgba(30, 30, 30, 0.95);

            border-bottom-color: #333;
        }


        body.dark .logo-text {
            color: #ffffff;
        }


        body.dark .nav-links a {
            color: #eeeeee;
        }


        body.dark .nav-links a::after {
            background: #ffffff;
        }


        body.dark #themeBtn,
        body.dark .hamburger {
            background: #292929;

            color: #ffffff;
        }


        body.dark .hero-label {
            background: #292929;

            color: #cccccc;
        }


        body.dark .hero-text p {
            color: #bbbbbb;
        }


        body.dark .btn-primary {
            background: #ffffff;

            color: #111;

            border-color: #ffffff;
        }


        body.dark .btn-secondary {
            background: #222222;

            color: #ffffff;

            border-color: #ffffff;
        }


        body.dark .about-section {
            border-color: #333333;
        }


        body.dark .about-heading p {
            color: #bbbbbb;
        }


        body.dark .card {
            background: #1e1e1e;

            border-color: #333333;
        }


        body.dark .card:hover {
            box-shadow:
                0 15px 35px rgba(0, 0, 0, 0.3);
        }


        body.dark .card-number {
            background: #ffffff;

            color: #111111;
        }


        body.dark .card p {
            color: #bbbbbb;
        }


        body.dark footer {
            background: #1e1e1e;

            color: #bbbbbb;
        }



        /* =====================================================
           TABLET
        ===================================================== */

        @media (max-width: 900px) {

            .hero-section {
                grid-template-columns: 1fr;

                text-align: center;

                min-height: auto;

                padding-top: 70px;

                padding-bottom: 80px;
            }


            .hero-text {
                margin: auto;
            }


            .hero-text p {
                margin-left: auto;

                margin-right: auto;
            }


            .button-group {
                justify-content: center;
            }


            .hero-image img {
                max-width: 550px;
            }


            .about-cards {
                grid-template-columns: 1fr;
            }

        }



        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 768px) {

            .navbar {
                min-height: 70px;

                padding: 0 1rem;
            }


            .logo img {
                width: 43px;

                height: 43px;
            }


            .logo-text {
                display: none;
            }


            .hamburger {
                display: flex;
            }


            .nav-links {
                display: none;

                position: absolute;

                top: 78px;

                right: 15px;

                width: 210px;

                padding: 20px;

                flex-direction: column;

                align-items: stretch;

                gap: 18px;

                background: #ffffff;

                border: 1px solid #eeeeee;

                border-radius: 12px;

                box-shadow:
                    0 10px 30px
                    rgba(0, 0, 0, 0.12);
            }


            .nav-links.show {
                display: flex;
            }


            .nav-links a {
                padding: 5px 0;
            }


            .nav-links #themeBtn {
                align-self: flex-start;
            }


            body.dark .nav-links {
                background: #1e1e1e;

                border-color: #333333;
            }


            .hero-section {
                padding: 60px 1.2rem;
            }


            .hero-text h1 {
                font-size: 2.7rem;

                letter-spacing: -1px;
            }


            .hero-text p {
                font-size: 0.95rem;
            }


            .hero-image img {
                height: 280px;
            }


            .about-section {
                padding: 70px 1.2rem;
            }


            .about-heading h2 {
                font-size: 2rem;
            }


            .card {
                padding: 22px;
            }

        }



        /* =====================================================
           SMALL MOBILE
        ===================================================== */

        @media (max-width: 480px) {

            .hero-text h1 {
                font-size: 2.3rem;
            }


            .button-group {
                flex-direction: column;

                width: 100%;
            }


            .btn {
                width: 100%;
            }


            .hero-image img {
                height: 230px;

                border-radius: 12px;
            }


            .about-heading h2 {
                font-size: 1.8rem;
            }

        }

    </style>

</head>


<body>


    <!-- =====================================================
         HEADER
    ===================================================== -->

    <header>

        <nav class="navbar">


            <!-- LOGO -->

            <div class="logo">

                <img src="images/studenthub-logo.svg"
                     alt="StudentHub Logo">

                <span class="logo-text">
                    StudentHub
                </span>

            </div>


            <!-- NAVIGATION -->

            <div class="nav-links"
                 id="navLinks">

                <a href="#home">
                    Home
                </a>

                <a href="#about">
                    About
                </a>

                <a href="login.php">
                    Login
                </a>

                <a href="register.php">
                    Register
                </a>

                <button id="themeBtn"
                        type="button"
                        aria-label="Change theme">
                    🌙
                </button>

            </div>


            <!-- MOBILE MENU -->

            <button class="hamburger"
                    id="hamburger"
                    type="button"
                    aria-label="Open menu">

                ☰

            </button>

        </nav>

    </header>



    <!-- =====================================================
         MAIN
    ===================================================== -->

    <main>


        <!-- =================================================
             HERO
        ================================================= -->

        <section id="home"
                 class="hero-section">


            <div class="hero-text">

                <span class="hero-label">
                    Student Platform
                </span>


                <h1>
                    Welcome to
                    <span>StudentHub</span>
                </h1>


                <p>
                    A simple platform designed to help
                    students manage their academic life,
                    connect with others, and stay organized
                    in one place.
                    Currently serving <?php echo (int) $studentCount; ?> students,
                    <?php echo (int) $courseCount; ?> active courses and
                    <?php echo (int) $eventCount; ?> published events.
                </p>


                <div class="button-group">

                    <a href="login.php"
                       class="btn btn-primary">

                        Login

                    </a>


                    <a href="register.php"
                       class="btn btn-secondary">

                        Register

                    </a>

                </div>

            </div>



            <div class="hero-image">

                <img src="images/register.png"
                     alt="StudentHub Project Image">

            </div>


        </section>



        <!-- =================================================
             ABOUT
        ================================================= -->

        <section id="about"
                 class="about-section">


            <div class="about-heading">

                <h2>
                    About StudentHub
                </h2>

                <p>
                    StudentHub brings useful student
                    activities and academic resources
                    together in one centralized platform.
                </p>

            </div>



            <div class="about-cards">

                <?php if (!empty($departments)): ?>
                    <?php foreach ($departments as $index => $department): ?>
                        <div class="card">
                            <div class="card-number">
                                <?php echo str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?>
                            </div>
                            <h3><?php echo e($department['department_name']); ?></h3>
                            <p><?php echo e($department['description'] ?: 'Academic department at StudentHub.'); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card">
                        <div class="card-number">01</div>
                        <h3>Departments</h3>
                        <p>Department details will appear here after they are added from the admin panel.</p>
                    </div>
                <?php endif; ?>

            </div>

        </section>


    </main>



    <!-- =====================================================
         FOOTER
    ===================================================== -->

    <footer>

        <p>
            &copy; 2026 StudentHub.
            All Rights Reserved.
        </p>

    </footer>



    <!-- =====================================================
         JAVASCRIPT
    ===================================================== -->

    <script>

        /* =========================
           MOBILE MENU
        ========================= */

        let hamburger =
            document.getElementById("hamburger");

        let navLinks =
            document.getElementById("navLinks");


        hamburger.addEventListener("click", function () {

            navLinks.classList.toggle("show");

        });



        /* =========================
           CLOSE MOBILE MENU
           AFTER CLICKING A LINK
        ========================= */

        let navItems =
            navLinks.querySelectorAll("a");


        navItems.forEach(function (link) {

            link.addEventListener("click", function () {

                navLinks.classList.remove("show");

            });

        });



        /* =========================
           DARK / LIGHT MODE
        ========================= */

        let themeBtn =
            document.getElementById("themeBtn");


        themeBtn.addEventListener("click", function () {

            document.body.classList.toggle("dark");


            if (document.body.classList.contains("dark")) {

                themeBtn.innerHTML = "☀️";

            }
            else {

                themeBtn.innerHTML = "🌙";

            }

        });

    </script>


</body>

</html>
