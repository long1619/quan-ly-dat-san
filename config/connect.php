<?php
$conn = mysqli_connect("localhost", "root", "", "quan_ly_dat_san");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
