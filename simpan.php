<?php
    session_start();
    if(!isset($_SESSION["admin"])){
        header("Location: templates/login.php");
        exit;
    }

    include("connect.php");

    if($_SERVER["REQUEST_METHOD"] !== "POST"){
        header("Location: templates/index.php");
        exit;
    }

    $nama = trim($_POST["nama"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $alamat = trim($_POST["alamat"] ?? "");
    $kota = trim($_POST["kota"] ?? "");
    $pesan = trim($_POST["msg"] ?? "");

    $error = [];
    if($nama === "") $error[] = "Nama kosong, silahkan diisi";
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $error[] = "Format email tidak valid";
    if($pesan === "") $error[] = "Pesan kosong, silahkan diisi";

    if(!empty($error)){
        $_SESSION["error"] = $error;
        header("Location: templates/index.php");
        exit;
    }

    $query = $conn->prepare("INSERT INTO tamu (nama, email, alamat, kota, msg) VALUES (?, ?, ?, ?, ?)");
    $query->bind_param("sssss", $nama, $email, $alamat, $kota, $pesan);

    if($query->execute()){
        $query->close();
        $conn->close();
        header("Location: templates/index.php?status=ok");
        exit;
    }

    $_SESSION["errors"] = ["Gagal menyimpan pesan, silahkan coba lagi"];
    $query->close();
    $conn->close();
    header("Location: templates/index.php");
    exit;
?>