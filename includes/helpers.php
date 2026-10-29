<?php

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function current_role()
{
    return strtolower(trim((string) ($_SESSION['role'] ?? '')));
}

function login_home($role = null)
{
    $role = strtolower(trim((string) ($role ?? ($_SESSION['role'] ?? ''))));

    if ($role === 'admin') {
        return 'admin/admin.php';
    }

    if ($role === 'user' || $role === 'student' || $role === 'professor') {
        return 'dashboard.php';
    }

    return 'login.php';
}

function post($key, $default = '')
{
    return trim($_POST[$key] ?? $default);
}

function post_int($key)
{
    $value = trim($_POST[$key] ?? '');
    return $value === '' ? 0 : (int) $value;
}

function get_int($key)
{
    return (int) ($_GET[$key] ?? 0);
}

function flash_set($type, $message)
{
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

function flash_get()
{
    if (empty($_SESSION['flash_message'])) {
        return null;
    }

    $flash = [
        'type' => $_SESSION['flash_type'] ?? 'success',
        'message' => $_SESSION['flash_message'],
    ];

    unset($_SESSION['flash_type'], $_SESSION['flash_message']);
    return $flash;
}

function db_query($sql, $types = '', $params = [])
{
    global $conn;

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Database error: ' . e($conn->error));
    }

    if ($types !== '' && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();
        die('Query failed: ' . e($error));
    }

    return $stmt;
}

function db_all($sql, $types = '', $params = [])
{
    $stmt = db_query($sql, $types, $params);
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    return $rows;
}

function db_one($sql, $types = '', $params = [])
{
    $rows = db_all($sql, $types, $params);
    return $rows[0] ?? null;
}

function db_count($sql, $types = '', $params = [])
{
    $row = db_one($sql, $types, $params);
    if (!$row) {
        return 0;
    }
    return (int) array_values($row)[0];
}

function db_run($sql, $types = '', $params = [])
{
    $stmt = db_query($sql, $types, $params);
    $insertId = $stmt->insert_id;
    $ok = $stmt->affected_rows >= 0;
    $stmt->close();
    return $insertId ?: $ok;
}

function empty_to_null($value)
{
    $value = trim((string) $value);
    return $value === '' ? null : $value;
}
