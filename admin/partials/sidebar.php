<?php
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'admin.php');
function sidebarActive(string $page, string $currentPage): string {
    return $page === $currentPage ? ' active' : '';
}
?>
<aside class="sidebar" id="sidebar" aria-label="Main navigation">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <h5><i class="bi bi-mortarboard-fill"></i> StudentHub</h5>
            <button class="sidebar-close" id="sidebarClose" type="button" aria-label="Close navigation">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="menu-section">
            <div class="menu-section-title">Main</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link<?= sidebarActive('admin.php', $currentPage) ?>" href="admin.php">
                        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="menu-section">
            <div class="menu-section-title">Management</div>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link<?= sidebarActive('all-professors.php', $currentPage) ?>" href="all-professors.php"><i class="bi bi-person-badge"></i><span>Professors</span></a></li>
                <li class="nav-item"><a class="nav-link<?= sidebarActive('all-students.php', $currentPage) ?>" href="all-students.php"><i class="bi bi-people"></i><span>Students</span></a></li>
                <li class="nav-item"><a class="nav-link<?= sidebarActive('all-courses.php', $currentPage) ?>" href="all-courses.php"><i class="bi bi-book"></i><span>Courses</span></a></li>
                <li class="nav-item"><a class="nav-link<?= sidebarActive('library-assets.php', $currentPage) ?>" href="library-assets.php"><i class="bi bi-journal-bookmark"></i><span>Library</span></a></li>
                <li class="nav-item"><a class="nav-link<?= sidebarActive('departments.php', $currentPage) ?>" href="departments.php"><i class="bi bi-building"></i><span>Department</span></a></li>
                <li class="nav-item"><a class="nav-link<?= sidebarActive('events.php', $currentPage) ?>" href="events.php"><i class="bi bi-calendar-event"></i><span>Events</span></a></li>
                <li class="nav-item"><a class="nav-link<?= sidebarActive('attendance.php', $currentPage) ?>" href="attendance.php"><i class="bi bi-clipboard-check"></i><span>Attendance</span></a></li>
                <li class="nav-item"><a class="nav-link<?= sidebarActive('feedback.php', $currentPage) ?>" href="feedback.php"><i class="bi bi-chat-dots"></i><span>Feedback</span></a></li>
                <li class="nav-item"><a class="nav-link<?= sidebarActive('mailbox.php', $currentPage) ?>" href="mailbox.php"><i class="bi bi-envelope"></i><span>Mailbox</span></a></li>
                <li class="nav-item"><a class="nav-link<?= sidebarActive('adminfaq.php', $currentPage) ?>" href="adminfaq.php"><i class="bi bi-question"></i><span>Faq</span></a></li>
            </ul>
        </div>
    </nav>
</aside>
