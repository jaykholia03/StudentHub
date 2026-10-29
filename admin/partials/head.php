<?php
if (!isset($conn)) {
    require_once __DIR__ . '/../../database.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="StudentHub administration dashboard">
  <title><?php echo htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES, 'UTF-8'); ?> - StudentHub Admin</title>
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="src/css/dashboard.css">
  <link rel="stylesheet" href="src/css/charts-layout.css">
  <link rel="stylesheet" href="src/css/xampp-fixes.css">
</head>
<body>
<script>
(function () {
  try {
    if (localStorage.getItem('theme') === 'dark') document.body.classList.add('dark');
  } catch (e) {}
})();
</script>
<a href="#main-content" class="visually-hidden-focusable">Skip to main content</a>
<div class="search-backdrop" id="searchBackdrop"></div>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
