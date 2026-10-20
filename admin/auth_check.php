<?php
// admin/auth_check.php
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin_user'])) {
    header('Location: login.php');
    exit;
}

