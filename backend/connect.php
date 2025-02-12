<?php
$servername = getenv('DB_SERVER') ?: 'your server';
$username = getenv('DB_USERNAME') ?: 'yor username';
$password = getenv('DB_PASSWORD') ?: 'your password';
$dbname = getenv('DB_NAME') ?: 'your db name';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // เปิด error report

try {
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    mysqli_set_charset($conn, "utf8");
} catch (Exception $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Sorry, we're experiencing some technical difficulties. Please try again later.");
}
