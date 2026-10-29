<!-- Header -->


<nav class="navbar top-navbar">
 <div class="container-fluid d-flex align-items-center h-100">
  <button class="hamburger-menu" id="sidebarToggle" type="button" aria-label="Toggle sidebar"><i class="bi bi-list"></i></button>
  <nav aria-label="breadcrumb" class="d-none d-lg-block ms-3"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="admin.php">Home</a></li><li class="breadcrumb-item active"><?php echo htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES, 'UTF-8'); ?></li></ol></nav>
  <div class="navbar-brand d-lg-none fw-bold me-auto">StudentHub</div><div class="flex-grow-1 d-none d-lg-block"></div>
  <div class="d-flex align-items-center gap-2">
   <button class="btn btn-light btn-icon theme-toggle" id="adminThemeToggle" type="button" aria-label="Switch to dark mode" title="Switch theme"><i class="bi bi-moon-stars-fill"></i></button>
   <a class="btn btn-light btn-icon" href="mailbox.php" aria-label="Notifications"><i class="bi bi-bell"></i></a>
   <div class="dropdown"><button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" type="button"><i class="bi bi-person-circle me-1"></i> <?php echo e($adminName ?? 'Admin'); ?></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="admin.php">Dashboard</a></li><li><a class="dropdown-item" href="../logout.php">Logout</a></li></ul></div>
  </div>
 </div>
</nav>
