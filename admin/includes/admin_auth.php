<?php
session_start();

// Restrict access to userid = 1
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    die('Access denied. This page is restricted.');
}
?>
