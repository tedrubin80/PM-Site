<?php
require_once '../config/database.php';

$auth = new Auth();
$auth->logout();

header('Location: login.php?message=You have been logged out successfully');
exit;
?>