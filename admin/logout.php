<?php
// admin/logout.php
require_once __DIR__ . '/../config/database.php';

unset($_SESSION['admin_user']);
header('Location: login.php');
exit;

