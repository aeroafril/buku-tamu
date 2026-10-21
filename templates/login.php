<?php 
    session_start();

    if(isset($_SESSION["admin"])){
        header("Location: index.php");
        exit;
    }

    include("../connect.php");
    $error = "";

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $username = trim($_POST["username"] ?? "");
        $password = trim($_POST["password"] ?? "");

        $query = $conn->prepare("SELECT password FROM admin WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $query->bind_result($hash);

        if($query->fetch() && $password === $hash){
            session_regenerate_id(true);
            $_SESSION["admin"] = $username;
            $query->close();
            $conn->close();
            header("Location: index.php");
            exit;
        }

        $error = "Username atau Password salah";
        $query->close();
    }
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu</title>
    <link rel="stylesheet" href="../static/login.css">
</head>
<body>
    <div class="kiri">
        <h1 class="lilita-one-regular">LOGIN</h1>
        <p class="ket">Masuk dulu untuk mengisi buku tamu</p>
    </div>
    <div class="container">
        <h2>Selamat Datang</h2>

        <?php if($error !== ""): ?>
            <p class="alert merah"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="login.php" method="post" class="form satu">
            <div class="area">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="area">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="tombol">
                <input type="submit" value="Login">
            </div>
        </form>
    </div>
    
</body>
</html>