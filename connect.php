<?php
    $hostmysql = "localhost";
    $username = "root";
    $password = "";
    $database = "bukutamu";

    $conn = mysqli_connect($hostmysql, $username, $password, $database);

    if(!$conn){
        die ("<b> Koneksi gagal</b>");
    }
?>