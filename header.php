<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (current_role() === 'admin') {
    header('Location: admin/admin.php');
    exit;
}

if (!isset($pageTitle)) {
    $pageTitle = 'StudentHub';
}

// User data from session
$sessionUserName    = $_SESSION['full_name'] ?? 'User';
$sessionUserInitial = strtoupper(substr($sessionUserName, 0, 1));
?>

<!DOCTYPE html>
<html lang="en">


<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $pageTitle; ?> - StudentHub</title>

    <style>


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

        body {
            min-height: 100vh;

            background: #f4f7f6;
            color: #333;

            transition:
                background-color 0.3s ease,
                color 0.3s ease;
        }


        /* =====================================================
           HEADER
        ===================================================== */

        .user-header {
            width: 100%;
            height: 75px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 2rem;

            background: #ffffff;

            box-shadow:
                0 2px 10px rgba(0, 0, 0, 0.05);

            position: sticky;
            top: 0;

            z-index: 1000;

            transition: 0.3s;
        }


        /* Logo */

        .logo-container {
            display: flex;
            align-items: center;

            gap: 10px;

            color: #333;

            text-decoration: none;

            font-size: 20px;
            font-weight: 700;

            flex-shrink: 0;
        }

        .logo-icon {
            width: 40px;
            height: 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #4f46e5;
            color: white;

            border-radius: 10px;

            font-size: 20px;
        }


        /* Navigation */

        .user-nav {
            display: flex;
            align-items: center;
            justify-content: center;

            gap: 18px;

            flex: 1;
        }

        .user-nav a {
            text-decoration: none;

            color: #555;

            font-size: 15px;
            font-weight: 500;

            padding: 6px 4px;

            transition: 0.2s;
        }

        .user-nav a:hover,
        .user-nav a.active {
            color: #4f46e5;
        }

        .user-nav a.logout {
            color: #d9534f;
        }


        /* Header Right */

        .header-right {
            display: flex;
            align-items: center;

            gap: 8px;

            flex-shrink: 0;
        }


        /* Theme + Menu */

        #themeBtn,
        #menuBtn {
            width: 40px;
            height: 40px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border: none;
            border-radius: 8px;

            background: #f1f5f9;
            color: #333;

            cursor: pointer;

            font-size: 18px;

            transition: 0.2s;
        }

        #themeBtn:hover,
        #menuBtn:hover {
            background: #e2e8f0;
        }

        #menuBtn {
            display: none;
        }


        /* User Profile */

        .user-profile {
            display: flex;
            align-items: center;

            gap: 8px;

            color: #333;

            text-decoration: none;

            font-weight: 600;

            white-space: nowrap;
        }

        .avatar {
            width: 40px;
            height: 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #4f46e5;
            color: white;

            font-weight: bold;
        }

        /* Radio Buttons */
        /* .radio-group {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 15px;
        }

        .radio-group input[type="radio"] {
            width: 18px;
            height: 18px;
            margin: 0;
            cursor: pointer;
            accent-color: #2b7fff;
        } */
        /* =====================================================
           MAIN LAYOUT
        ===================================================== */

        .user-layout {
            display: flex;

            min-height:
                calc(100vh - 75px);
        }

        main {
            flex: 1;

            width: 100%;
            min-width: 0;

            padding: 30px;

            overflow-y: auto;
        }

        main > h2 {
            color: #1e293b;

            font-size: 24px;
            font-weight: 600;

            margin-bottom: 5px;
        }


        /* =====================================================
           COMMON
        ===================================================== */

        .page-text {
            color: #64748b;

            font-size: 14px;

            margin-top: 5px;
            margin-bottom: 25px;
        }

        .card {
            background: #ffffff;

            border: 1px solid #e5e7eb;

            border-radius: 10px;

            padding: 25px;
        }

        button {
            font-family: inherit;
        }


        /* =====================================================
           FORMS
        ===================================================== */

        label {
            color: #334155;

            font-size: 14px;
            font-weight: 600;
        }

        input,
        textarea {
            width: 100%;

            padding: 11px 12px;

            border: 1px solid #dbe1e8;

            border-radius: 7px;

            background: #f8fafc;
            color: #334155;

            font-size: 14px;

            outline: none;

            transition: 0.2s;
        }

        input:focus,
        textarea:focus {
            background: #ffffff;

            border-color: #2b7fff;

            box-shadow:
                0 0 0 3px rgba(43, 127, 255, 0.10);
        }

        textarea {
            resize: vertical;
        }


        /* Common submit button */

        button[type="submit"] {
            padding: 10px 20px;

            border: none;
            border-radius: 7px;

            background: #2b7fff;
            color: white;

            font-size: 14px;
            font-weight: 600;

            cursor: pointer;

            transition: 0.2s;
        }

        button[type="submit"]:hover {
            background: #1769d1;
        }


        /* =====================================================
           DASHBOARD
        ===================================================== */

        .dashboard {
            max-width: 1200px;

            margin: auto;
            padding: 0;
        }

        .welcome {
            background: #1e293b;
            color: white;

            padding: 30px;

            border-radius: 15px;

            margin-bottom: 25px;
        }

        .welcome h1 {
            margin-bottom: 8px;
        }

        .welcome p {
            color: #cbd5e1;
        }


        /* Dashboard Statistics */

        .dashboard-stats {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 20px;

            margin-bottom: 25px;
        }

        .dashboard-box {
            background: white;

            padding: 20px;

            border: 1px solid #e5e7eb;

            border-radius: 12px;
        }

        .dashboard-box h3 {
            color: #64748b;

            font-size: 14px;

            margin-bottom: 10px;
        }

        .dashboard-box h2 {
            color: #1e293b;

            font-size: 28px;

            margin-bottom: 5px;
        }

        .dashboard-box p {
            color: #64748b;

            font-size: 13px;
        }


        /* Dashboard Content */

        .dashboard-content {
            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 20px;

            margin-bottom: 25px;
        }

        .dashboard-card {
            background: white;

            padding: 25px;

            border: 1px solid #e5e7eb;

            border-radius: 12px;
        }

        .dashboard-card h2 {
            color: #1e293b;

            font-size: 20px;

            margin-bottom: 15px;
        }


        /* Activity */

        .activity {
            display: flex;

            gap: 12px;

            padding: 14px 0;

            border-bottom: 1px solid #e5e7eb;
        }

        .activity:last-child {
            border-bottom: none;
        }

        .activity > span {
            width: 30px;
            height: 30px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #dcfce7;
            color: #16a34a;

            border-radius: 50%;

            font-weight: bold;

            flex-shrink: 0;
        }

        .activity strong {
            color: #1e293b;

            font-size: 14px;
        }

        .activity p {
            color: #64748b;

            font-size: 13px;

            margin-top: 5px;
        }


        /* Student Information */

        .info-row {
            display: flex;

            justify-content: space-between;

            padding: 14px 0;

            border-bottom: 1px solid #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row span {
            color: #64748b;

            font-size: 14px;
        }

        .info-row strong {
            color: #1e293b;

            font-size: 14px;
        }

        .info-row .active {
            color: #16a34a;
        }


        /* Quick Actions */

        .quick-actions {
            background: white;

            padding: 25px;

            border: 1px solid #e5e7eb;

            border-radius: 12px;
        }

        .quick-actions h2 {
            color: #1e293b;

            font-size: 20px;

            margin-bottom: 20px;
        }

        .action-container {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 15px;
        }

        .action-container a {
            display: flex;

            align-items: center;
            justify-content: center;

            gap: 10px;

            padding: 18px;

            background: #f8fafc;

            border-radius: 10px;

            color: #1e293b;

            text-decoration: none;

            font-size: 20px;

            transition: 0.2s;
        }

        .action-container a:hover {
            background: #eef2ff;

            transform: translateY(-2px);
        }

        .action-container span {
            font-size: 14px;

            font-weight: 600;
        }


        /* =====================================================
           ATTENDANCE
        ===================================================== */

        .attendance-page {
            max-width: 1100px;

            margin: auto;
        }

        .attendance-summary {
            display: grid;

            grid-template-columns:
                2fr repeat(4, 1fr);

            gap: 15px;

            margin-bottom: 25px;
        }

        .overall,
        .summary-item {
            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 10px;

            padding: 20px;
        }

        .overall span,
        .summary-item span {
            display: block;

            color: #64748b;

            font-size: 13px;

            margin-bottom: 8px;
        }

        .overall strong {
            font-size: 32px;

            color: #1e293b;
        }

        .summary-item strong {
            font-size: 22px;

            color: #1e293b;
        }

        .subject-card {
            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 10px;

            padding: 20px;
        }

        .subject-card h3 {
            color: #1e293b;

            margin-bottom: 10px;
        }

        .subject-row {
            display: flex;

            justify-content: space-between;
            align-items: center;

            padding: 16px 0;

            border-bottom: 1px solid #e5e7eb;
        }

        .subject-row:last-child {
            border-bottom: none;
        }

        .subject-row strong {
            display: block;

            color: #1e293b;

            font-size: 14px;
        }

        .subject-row span {
            display: block;

            color: #64748b;

            font-size: 12px;

            margin-top: 4px;
        }

        .subject-row b {
            color: #16a34a;

            font-size: 14px;
        }


        /* =====================================================
           CONTACT
        ===================================================== */

        .contact-grid {
            display: grid;

            grid-template-columns:
                1.2fr 0.8fr;

            gap: 20px;
        }

        .contact-form form {
            display: flex;

            flex-direction: column;

            gap: 15px;
        }

        .contact-form form > div {
            display: flex;

            flex-direction: column;

            gap: 7px;
        }

        .right-column {
            display: flex;

            flex-direction: column;

            gap: 20px;
        }

        .info-card h3,
        .map-card h3 {
            color: #1e293b;

            margin-bottom: 15px;

            font-size: 18px;
        }

        .info-card p {
            color: #64748b;

            font-size: 14px;

            margin-bottom: 10px;
        }

        .info-card p:last-child {
            margin-bottom: 0;
        }

        .map-card img {
            width: 100%;

            height: 200px;

            object-fit: cover;

            border-radius: 8px;

            display: block;
        }


        /* =====================================================
           PROFILE
        ===================================================== */

        .profile-page {
            max-width: 950px;

            margin: auto;
        }

        .profile-card {
            background: #ffffff;

            border: 1px solid #e5e7eb;

            border-radius: 12px;

            margin-top: 20px;

            overflow: hidden;

            box-shadow:
                0 3px 12px rgba(0, 0, 0, 0.04);
        }

        .profile-top {
            display: flex;

            align-items: center;

            gap: 20px;

            padding: 25px;

            background: #f8fafc;

            border-bottom: 1px solid #e5e7eb;
        }

        .profile-image {
            width: 85px;
            height: 85px;

            border-radius: 50%;

            object-fit: cover;

            border: 3px solid white;

            box-shadow:
                0 2px 8px rgba(0, 0, 0, 0.12);
        }

        .profile-top h3 {
            color: #1e293b;

            font-size: 20px;

            margin-bottom: 4px;
        }

        .profile-top p {
            color: #64748b;

            font-size: 14px;

            margin-bottom: 12px;
        }

        .profile-top button {
            padding: 8px 14px;

            border: 1px solid #d1d5db;

            background: white;
            color: #334155;

            border-radius: 7px;

            cursor: pointer;

            font-size: 13px;
        }

        .profile-top button:hover {
            background: #f1f5f9;
        }

        .profile-info {
            padding: 25px;
        }

        .profile-info h3 {
            color: #1e293b;

            font-size: 17px;

            margin-bottom: 20px;
        }

        .profile-grid {
            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 18px;
        }

        .profile-grid div {
            display: flex;

            flex-direction: column;
        }

        .profile-grid .full-width {
            grid-column: span 2;
        }

        .profile-grid label {
            color: #64748b;

            font-size: 13px;

            margin-bottom: 7px;
        }

        .profile-grid input {
            background: #f8fafc;
        }

        .save-btn {
            margin-top: 22px;
        }


        /* =====================================================
           FEEDBACK
        ===================================================== */

        .feedback-page {
            max-width: 800px;

            margin: auto;
        }

        .feedback-card {
            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 10px;

            padding: 25px;
        }

        .feedback-card form {
            display: flex;

            flex-direction: column;

            gap: 12px;
        }

        .rating {
            display: flex;

            gap: 20px;

            flex-wrap: wrap;

            margin-bottom: 15px;
        }

        .rating label {
            color: #334155;

            font-weight: 400;

            cursor: pointer;
        }

        .rating input {
            width: auto;

            margin-right: 5px;
        }

        .feedback-card textarea {
            min-height: 130px;
        }


        /* =====================================================
           FAQ
        ===================================================== */

        
        .faq-page {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .faq-page h2 {
            margin-bottom: 10px;
        }
        
        .faq-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 25px 0 15px;
        }
        
        .faq-controls input,
        .faq-controls select {
            min-width: 150px;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fff;
            color: #333;
            font-size: 14px;
        }
        
        .faq-controls input {
            flex: 1;
        }
        
        .faq-list details {
            padding: 18px;
            margin-bottom: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
        }
        
        .faq-list summary {
            cursor: pointer;
            font-weight: 600;
            line-height: 1.6;
        }
        
        .faq-list details p {
            margin: 12px 0;
            color: #666;
            line-height: 1.7;
        }
        
        .faq-category {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            background: #eef2ff;
            color: #4f46e5;
            font-size: 12px;
        }
        
        .faq-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin: 25px 0;
            flex-wrap: wrap;
        }
        
        .faq-pagination button {
            padding: 10px 16px;
            border: none;
            border-radius: 7px;
            background: #4f46e5;
            color: white;
            cursor: pointer;
        }
        
        .faq-pagination button:disabled {
            background: #aaa;
            cursor: not-allowed;
        }
        
        #faqMessage {
            color: #666;
            margin: 15px 0;
        }
        
        /* Dark mode */
        body.dark-mode .faq-list details {
            background: #1e1e1e;
            color: #eee;
            border-color: #333;
        }
        
        body.dark-mode .faq-list details p,
        body.dark-mode #faqMessage {
            color: #ccc;
        }
        
        body.dark-mode .faq-controls input,
        body.dark-mode .faq-controls select {
            background: #1e1e1e;
            color: #eee;
            border-color: #444;
        }
        
        /* Mobile responsive */
        @media (max-width: 600px) {
            .faq-controls {
                flex-direction: column;
            }
        
            .faq-controls input,
            .faq-controls select {
                width: 100%;
            }
        
            .faq-list details {
                padding: 14px;
            }
        
            .faq-pagination {
                gap: 8px;
            }
        }
        
        
        
        /* =====================================================
           DARK MODE
        ===================================================== */

        body.dark {
            background: #121212;

            color: #eeeeee;

            color-scheme: dark;
        }


        /* Header */

        body.dark .user-header {
            background: #1e1e1e;

            box-shadow:
                0 2px 10px rgba(0, 0, 0, 0.4);
        }

        body.dark .logo-container,
        body.dark .user-profile {
            color: #ffffff;
        }


        /* Navigation */

        body.dark .user-nav a {
            color: #cccccc;
        }

        body.dark .user-nav a:hover,
        body.dark .user-nav a.active {
            color: #818cf8;
        }

        body.dark .user-nav a.logout {
            color: #f87171;
        }


        /* Buttons */

        body.dark #themeBtn,
        body.dark #menuBtn {
            background: #333333;

            color: white;
        }

        body.dark #themeBtn:hover,
        body.dark #menuBtn:hover {
            background: #444444;
        }


        /* Main headings */

        body.dark main > h2 {
            color: #ffffff;
        }

        body.dark .page-text {
            color: #999999;
        }


        /* Cards */

        body.dark .card,
        body.dark .dashboard-box,
        body.dark .dashboard-card,
        body.dark .quick-actions,
        body.dark .overall,
        body.dark .summary-item,
        body.dark .subject-card,
        body.dark .feedback-card,
        body.dark .profile-card {
            background: #1e1e1e;

            border-color: #333333;

            box-shadow: none;
        }


        /* Dashboard */

        body.dark .dashboard-box h3,
        body.dark .dashboard-box p {
            color: #999999;
        }

        body.dark .dashboard-box h2 {
            color: #ffffff;
        }

        body.dark .dashboard-card h2,
        body.dark .quick-actions h2 {
            color: #cccccc;
        }

        body.dark .activity {
            border-color: #333333;
        }

        body.dark .activity strong {
            color: #cccccc;
        }

        body.dark .activity p {
            color: #999999;
        }

        body.dark .info-row {
            border-color: #333333;
        }

        body.dark .info-row span {
            color: #999999;
        }

        body.dark .info-row strong {
            color: #cccccc;
        }

        body.dark .info-row .active {
            color: #4ade80;
        }

        body.dark .action-container a {
            background: #252525;

            color: #cccccc;
        }

        body.dark .action-container a:hover {
            background: #303030;
        }

        body.dark .welcome {
            background: #171717;
        }


        /* Attendance */

        body.dark .overall span,
        body.dark .summary-item span,
        body.dark .subject-row span {
            color: #999999;
        }

        body.dark .overall strong,
        body.dark .summary-item strong,
        body.dark .subject-card h3,
        body.dark .subject-row strong {
            color: #cccccc;
        }

        body.dark .subject-row {
            border-color: #333333;
        }

        body.dark .subject-row b {
            color: #4ade80;
        }


        /* Contact */

        body.dark label {
            color: #cccccc;
        }

        body.dark .info-card h3,
        body.dark .map-card h3 {
            color: #ffffff;
        }

        body.dark .info-card p {
            color: #999999;
        }


        /* Inputs */

        body.dark input,
        body.dark textarea {
            background: #292929;

            border-color: #444444;

            color: #ffffff;
        }

        body.dark input:focus,
        body.dark textarea:focus {
            background: #333333;

            border-color: #5da9ff;

            color: white;
        }

        body.dark input::placeholder,
        body.dark textarea::placeholder {
            color: #999999;
        }


        /* Profile */

        body.dark .profile-top {
            background: #252525;

            border-color: #333333;
        }

        body.dark .profile-top h3,
        body.dark .profile-info h3 {
            color: #cccccc;
        }

        body.dark .profile-top p,
        body.dark .profile-grid label {
            color: #999999;
        }

        body.dark .profile-top button {
            background: #1e1e1e;

            border-color: #444444;

            color: #cccccc;
        }

        body.dark .profile-top button:hover {
            background: #303030;
        }


        /* Feedback */

        body.dark .rating label {
            color: #cccccc;
        }

        /* Events */

        
        /* Events page */

        .events-page {
            width: 100%;
            min-width: 0;
            padding: 25px;
        }

        .events-heading {
            margin-bottom: 25px;
        }

        .events-heading h2 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .events-heading p {
            color: #64748b;
            line-height: 1.6;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .event-card {
            display: flex;
            align-items: flex-start;
            gap: 18px;
            min-width: 0;
            padding: 22px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
            overflow-wrap: anywhere;
        }

        .event-date {
            flex-shrink: 0;
            width: 75px;
            padding: 12px 5px;
            text-align: center;
            background: #eef2ff;
            color: #4f46e5;
            border-radius: 10px;
        }

        .event-date span,
        .event-date strong,
        .event-date small {
            display: block;
        }

        .event-date span {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .event-date strong {
            font-size: 28px;
            margin: 5px 0;
        }

        .event-date small {
            font-size: 12px;
        }

        .event-content {
            flex: 1;
            min-width: 0;
        }

        .event-content h3 {
            margin: 0 0 12px;
            font-size: 19px;
        }

        .event-description {
            margin-bottom: 15px;
            color: #64748b;
            line-height: 1.6;
        }

        .event-info {
            margin-top: 8px;
            font-size: 14px;
            line-height: 1.5;
        }

        .no-events {
            grid-column: 1 / -1;
            padding: 40px 20px;
            text-align: center;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
        }

        .no-events p {
            margin-top: 10px;
            color: #64748b;
        }

        /* Dark mode */

        body.dark .event-card,
        body.dark .no-events {
            background: #1e1e1e;
            color: #e5e7eb;
            border-color: #333;
        }

        body.dark .event-description,
        body.dark .event-info,
        body.dark .events-heading p,
        body.dark .no-events p {
            color: #cbd5e1;
        }

        body.dark .event-date {
            background: #312e81;
            color: #c7d2fe;
        }

        /* Tablet */

        @media (max-width: 900px) {
            .events-grid {
                grid-template-columns: 1fr;
            }

            .events-page {
                padding: 20px;
            }
        }

        /* Mobile */

        @media (max-width: 600px) {
            .events-page {
                padding: 15px;
            }

            .events-heading h2 {
                font-size: 23px;
            }

            .event-card {
                gap: 12px;
                padding: 15px;
            }

            .event-date {
                width: 62px;
            }

            .event-date strong {
                font-size: 24px;
            }

            .event-content h3 {
                font-size: 17px;
            }
        }

        @media (max-width: 360px) {
            .event-card {
                flex-direction: column;
            }

            .event-date {
                width: 100%;
            }
        }



        /* FAQ */

        body.dark .faq-list details {
            background: #1e1e1e;

            border-color: #333333;
        }

        body.dark .faq-list summary {
            color: #cccccc;
        }

        body.dark .faq-list p {
            color: #999999;
        }


        /* =====================================================
           MOBILE MENU
        ===================================================== */

        @media (max-width: 768px) {

            .user-header {
                height: 70px;

                padding: 0 15px;
            }

            .logo-container {
                font-size: 18px;
            }

            .logo-icon {
                width: 36px;
                height: 36px;

                font-size: 16px;
            }

            .user-nav {
                display: none;

                position: absolute;

                top: 70px;
                left: 0;

                width: 100%;

                flex-direction: column;

                gap: 0;

                background: white;

                box-shadow:
                    0 8px 16px rgba(0, 0, 0, 0.12);
            }

            .user-nav.active {
                display: flex;
            }

            .user-nav a {
                padding: 14px 20px;

                border-bottom: 1px solid #eeeeee;
            }

            #menuBtn {
                display: inline-flex;
            }

            .user-profile span {
                display: none;
            }


            /* Main */

            .user-layout {
                min-height:
                    calc(100vh - 70px);
            }

            main {
                padding: 20px 15px;
            }


            /* Dashboard */

            .dashboard-stats {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .dashboard-content {
                grid-template-columns: 1fr;
            }

            .action-container {
                grid-template-columns: 1fr;
            }


            /* Attendance */

            .attendance-summary {
                grid-template-columns:
                    repeat(2, 1fr);
            }

            .overall {
                grid-column: span 2;
            }


            /* Contact */

            .contact-grid {
                grid-template-columns: 1fr;
            }


            /* Profile */

            .profile-grid {
                grid-template-columns: 1fr;
            }

            .profile-grid .full-width {
                grid-column: span 1;
            }

            .profile-top {
                padding: 20px;
            }

            .profile-info {
                padding: 20px;
            }


            /* FAQ */

            .faq-page,
            .feedback-page {
                width: 100%;
            }

        }


        /* =====================================================
           SMALL MOBILE
        ===================================================== */

        @media (max-width: 500px) {

            .logo-container span {
                display: none;
            }

            #themeBtn,
            #menuBtn {
                width: 36px;
                height: 36px;
            }

            .avatar {
                width: 34px;
                height: 34px;
            }


            /* Dashboard */

            .dashboard-stats {
                grid-template-columns: 1fr;
            }

            .welcome {
                padding: 22px;
            }

            .welcome h1 {
                font-size: 23px;
            }


            /* Attendance */

            .attendance-summary {
                grid-template-columns: 1fr 1fr;
            }

            .overall {
                grid-column: span 2;
            }


            /* Profile */

            .profile-top {
                align-items: flex-start;
            }

            .profile-image {
                width: 70px;
                height: 70px;
            }


            /* Feedback */

            .feedback-card {
                padding: 20px;
            }

        }


        /* =====================================================
           DARK MOBILE MENU
        ===================================================== */

        @media (max-width: 768px) {

            body.dark .user-nav {
                background: #1e1e1e;
            }

            body.dark .user-nav a {
                color: #cccccc;

                border-color: #333333;
            }

            body.dark .user-nav a:hover,
            body.dark .user-nav a.active {
                background: #292929;

                color: #818cf8;
            }

        }

    
        /* =====================================================
           PROFILE + FEEDBACK FORM FIXES
           Added after reviewing profile.php and feedback.php.
        ===================================================== */

        .profile-page,
        .feedback-page {
            width: 100%;
            min-width: 0;
            padding: 24px;
        }

        .profile-page > h2,
        .feedback-page > h2 {
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .profile-page .profile-card {
            width: 100%;
        }

        .profile-info {
            min-width: 0;
        }

        .profile-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: start;
        }

        .profile-grid > div {
            min-width: 0;
        }

        .profile-grid input,
        .profile-grid select,
        .profile-grid textarea,
        .feedback-card select,
        .feedback-card textarea {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            padding: 11px 12px;
            border: 1px solid #dbe1e8;
            border-radius: 7px;
            background: #f8fafc;
            color: #334155;
            font: inherit;
            font-size: 14px;
            line-height: 1.5;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background-color .2s;
        }

        .profile-grid select,
        .feedback-card select {
            min-height: 42px;
            cursor: pointer;
        }

        .profile-grid input:focus,
        .profile-grid select:focus,
        .profile-grid textarea:focus,
        .feedback-card select:focus,
        .feedback-card textarea:focus {
            border-color: #4f46e5;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .12);
        }

        .profile-grid input[readonly] {
            background: #eef2f7;
            color: #64748b;
            cursor: not-allowed;
        }

        .profile-grid textarea,
        .feedback-card textarea {
            resize: vertical;
        }

        .profile-grid .full-width {
            grid-column: 1 / -1;
        }

        .profile-info hr {
            border: 0;
            border-top: 1px solid #e5e7eb;
        }

        .profile-info .save-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 11px 20px;
            margin-top: 24px;
            border: 0;
            border-radius: 8px;
            background: #4f46e5;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
        }

        .profile-info .save-btn:hover {
            background: #4338ca;
        }

        .feedback-page .page-text {
            margin: 0 0 22px;
            color: #64748b;
            line-height: 1.6;
        }

        .feedback-card {
            width: 100%;
            padding: 28px;
            margin-top: 16px;
        }

        .feedback-card form > label {
            display: block;
            margin-top: 4px;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
        }

        .feedback-card .rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 12px;
        }

        .feedback-card .rating label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
            color: #334155;
            font-size: 14px;
        }

        .feedback-card .rating input {
            width: auto;
            margin: 0;
            accent-color: #4f46e5;
        }

        .feedback-card button[type="submit"] {
            align-self: flex-start;
            min-height: 42px;
            padding: 11px 20px;
            margin-top: 8px;
            border-radius: 8px;
            background: #4f46e5;
        }

        .feedback-card button[type="submit"]:hover {
            background: #4338ca;
        }

        /* Dark mode: keep form controls readable on both pages. */
        body.dark .profile-grid input,
        body.dark .profile-grid select,
        body.dark .profile-grid textarea,
        body.dark .feedback-card select,
        body.dark .feedback-card textarea {
            background: #292929;
            border-color: #444;
            color: #f3f4f6;
            color-scheme: dark;
        }

        body.dark .profile-grid input[readonly] {
            background: #222;
            color: #aaa;
        }

        body.dark .profile-grid input:focus,
        body.dark .profile-grid select:focus,
        body.dark .profile-grid textarea:focus,
        body.dark .feedback-card select:focus,
        body.dark .feedback-card textarea:focus {
            background: #303030;
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(129, 140, 248, .16);
        }

        body.dark .profile-info hr {
            border-top-color: #383838;
        }

        body.dark .feedback-page .page-text {
            color: #a1a1aa;
        }

        body.dark .feedback-card form > label {
            color: #e5e7eb;
        }

        body.dark .feedback-card .rating label {
            background: #252525;
            border-color: #414141;
            color: #e5e7eb;
        }

        @media (max-width: 768px) {
            .profile-page,
            .feedback-page {
                padding: 18px;
            }

            .profile-top {
                padding: 20px;
                gap: 15px;
            }

            .profile-info {
                padding: 20px;
            }

            .feedback-card {
                padding: 20px;
            }

            .feedback-card .rating {
                gap: 8px;
            }

            .feedback-card .rating label {
                padding: 8px 10px;
            }
        }

        @media (max-width: 560px) {
            .profile-page,
            .feedback-page {
                padding: 14px;
            }

            .profile-grid {
                grid-template-columns: minmax(0, 1fr);
                gap: 14px;
            }

            .profile-grid .full-width {
                grid-column: 1;
            }

            .profile-top {
                align-items: flex-start;
                padding: 16px;
                gap: 12px;
            }

            .profile-image {
                width: 64px;
                height: 64px;
                flex-shrink: 0;
            }

            .profile-top h3 {
                font-size: 18px;
                overflow-wrap: anywhere;
            }

            .profile-info {
                padding: 16px;
            }

            .feedback-card {
                padding: 16px;
            }

            .feedback-card .rating {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .feedback-card .rating label {
                min-width: 0;
            }

            .profile-info .save-btn,
            .feedback-card button[type="submit"] {
                width: 100%;
            }
        }

    </style>

</head>


<body>

<script>

    /* Apply saved theme before page is displayed */

    if (localStorage.getItem("theme") === "dark") {
        document.body.classList.add("dark");
    }

</script>


<header class="user-header">

    <!-- Logo -->

    <a href="dashboard.php" class="logo-container">

        <div class="logo-icon">
            🎓
        </div>

        <span>StudentHub</span>

    </a>


    <!-- Navigation -->

    <nav class="user-nav" id="userNav">

        <a href="dashboard.php">Dashboard</a>

        <a href="attendance.php">Attendance</a>

        <a href="events.php">Events</a>

        <a href="library.php">Library</a>

        <a href="contact.php">Contact</a>

        <a href="feedback.php">Feedback</a>

        <a href="faq.php">FAQ</a>

        <a href="logout.php" class="logout">Logout</a>

    </nav>


    <!-- Right Side -->

    <div class="header-right">

        <button
            id="themeBtn"
            type="button"
            title="Change Theme"
        >
            🌙
        </button>


        <a href="profile.php" class="user-profile">

            <div class="avatar">
                <?php echo htmlspecialchars($sessionUserInitial); ?>
            </div>

            <span><?php echo htmlspecialchars($sessionUserName); ?></span>

        </a>


        <button
            id="menuBtn"
            type="button"
            title="Open Menu"
            aria-label="Open Menu"
        >
            ☰
        </button>

    </div>

</header>


<script>

    /* =====================================================
       THEME
    ===================================================== */

    const themeBtn =
        document.getElementById("themeBtn");


    function updateThemeIcon() {

        if (document.body.classList.contains("dark")) {

            themeBtn.textContent = "☀️";

        } else {

            themeBtn.textContent = "🌙";

        }

    }


    updateThemeIcon();


    themeBtn.addEventListener("click", function () {

        document.body.classList.toggle("dark");


        if (document.body.classList.contains("dark")) {

            localStorage.setItem("theme", "dark");

        } else {

            localStorage.setItem("theme", "light");

        }


        updateThemeIcon();

    });


    /* =====================================================
       MOBILE MENU
    ===================================================== */

    const menuBtn =
        document.getElementById("menuBtn");

    const userNav =
        document.getElementById("userNav");


    menuBtn.addEventListener("click", function () {

        userNav.classList.toggle("active");


        if (userNav.classList.contains("active")) {

            menuBtn.textContent = "✕";

        } else {

            menuBtn.textContent = "☰";

        }

    });


    /* Close mobile menu after clicking link */

    const navLinks =
        document.querySelectorAll(".user-nav a");


    navLinks.forEach(function (link) {

        link.addEventListener("click", function () {

            userNav.classList.remove("active");

            menuBtn.textContent = "☰";

        });

    });

</script>