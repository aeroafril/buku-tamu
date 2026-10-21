<?php
    session_start();
    if(!isset($_SESSION["admin"])){
        header("Location: login.php");
        exit;
    }

    $errors = $_SESSION["errors"] ?? [];
    unset($_SESSION["errors"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="../static/style.css">
</head>
<body>
    <header class="topbar">
        <div class="link">
            <a href="tampil.php">Lhat buku tamu</a>
            <a class="logout" href="../logout.php">Logout</a>
        </div>
    </header>

    <div class="container">
        <h2>Contact</h2>

        <?php if(isset($_GET["status"]) && $_GET["status"] === "ok"): ?>
            <p style="color: green;">Pesan berhasil terkirim</p>
        <?php endif; ?>

        <?php if(!empty($errors)): ?>
            <div class="alert merah">
                <ul>
                    <?php foreach($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="../simpan.php" name="tamu" method="post">
            <div class="area">
                <label for="nama">Nama</label>
                <input type="text" name="nama" required>
            </div>

            <div class="area">
                <label for="email">Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="area">
                <label for="alamat">Alamat</label>
                <input type="text" name="alamat">
            </div>
            
            <div class="area">
                <label for="kota">Kota</label>
                <input type="text" name="kota">
            </div>

            <div class="area full">
                <label for="pesan">Pesan</label>
                <textarea name="msg" required></textarea>
            </div>

            <div class="tombol full">
                <input type="submit" name="submit" value="send">
                <input type="reset" value="reset">
            </div>
        </form>
    </div>
</body>
</html>