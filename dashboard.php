<?php
session_start();
if (!isset($_SESSION['user'])) { header("Location: index(login).php"); exit; }
$role = $_SESSION['user']['role'];
if ($role == "admin") {
    header("Location: admin.php"); exit;
} else {
    header("Location: student.php"); exit;
}
