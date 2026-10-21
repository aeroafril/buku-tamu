<?php 
    session_start();

    if(!isset($_SESSION["admin"])){
        header("Location: login.php");
        exit;
    }

    include("../connect.php");

    $errors = [];
    $editId = (int)($_GET["edit"] ?? 0);
    $editData = null;

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $aksi = $_POST["aksi"] ?? "";
        $id = (int)($_POST["id"] ?? 0);

        if($aksi === "update"){
            $nama = trim($_POST["nama"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $alamat = trim($_POST["alamat"] ?? "");
            $kota = trim($_POST["kota"] ?? "");
            $pesan = trim($_POST["msg"] ?? "");

            if($nama === "") $errors[] = "Nama tidak boleh kosong";
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid";
            if($pesan === "") $errors[] = "Pesan tidak boleh kosong";

            if(empty($errors)){
                $query = $conn->prepare("UPDATE tamu SET nama=?, email=?, alamat=?, kota=?, msg=? WHERE ID=?");
                $query->bind_param("sssssi", $nama, $email, $alamat, $kota, $pesan, $id);
                $query->execute();
                $query->close();
                header("Location: tampil.php?status=updated");
                exit;
            }

            $editId = $id;
            $editData = [
                "ID" => $id, "nama" => $nama, "email" => $email, "alamat" => $alamat, "kota" => $kota, "msg" => $pesan
            ];
        }

        if($aksi === "hapus"){
            $query = $conn->prepare("DELETE FROM tamu WHERE ID=?");
            $query->bind_param("i", $id);
            $query->execute();
            $query->close();
            header("Location: tampil.php?status=deleted");
            exit;
        }

    }

    if($editId > 0 && $editData === null){
        $query = $conn->prepare("SELECT ID, nama, email, alamat, kota, msg FROM tamu WHERE ID=?");
        $query->bind_param("i", $editId);
        $query->execute();
        $editData = $query->get_result()->fetch_assoc();
        $query->close();

        if(!$editData) $editData = 0;
    }

    $q = trim($_GET["q"] ?? "");

    if($q !== ""){
        $like = "%". $q ."%";
        $query = $conn->prepare("SELECT ID, nama, email, alamat, kota, msg FROM tamu WHERE nama LIKE ? OR email LIKE ? OR kota LIKE ? ORDER BY ID DESC");
        $query->bind_param("sss", $like, $like, $like);
        $query->execute();
        $result = $query->get_result();
    }else{
        $result = $conn->query("SELECT ID, nama, email, alamat, kota, msg FROM tamu ORDER BY ID DESC");
    }

    $jumlah = $result ? $result->num_rows : 0;
    $status = $_GET["status"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu</title>
    <link rel="stylesheet" href="../static/tampil.css">
</head>
<body>
    <div class="layout">
        <div class="main">
            <header class="topbar">
                <div class="link">
                    <a href="index.php">Form buku tamu</a>
                    <a href="../logout.php" class="logout">Logout</a>
                </div>
            </header>

            <main class="isi">
                <h1>Buku Tamu</h1>
                <p class="sub">Kelola buku tamu. Klik edit untuk mengubah data</p>

                <form method="get" class="cari">
                    <input type="search" name="q" placeholder="cari nama, email, kota, dll">
                    <button type="submit">Cari</button>
                </form>

                <?php if($status === "updated"): ?>
                    <p class="alert hijau">Data berhasil diupdate</p>
                <?php elseif($status === "deleted"): ?>
                    <p class="alert hijau">Data berhasil dihapus</p>
                <?php endif; ?>

                <?php if(!empty($errors)): ?>
                    <div class="alert merah">
                        <ul>
                            <?php foreach($errors as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if($editId > 0 && $editData): ?>

                <section class="panel">
                    <h2>Edit Data #<?= (int)$editData['ID'] ?></h2>

                    <form method="post" action="tampil.php" class="form">
                        <input type="hidden" name="aksi" value="update">
                        <input type="hidden" name="id" value="<?= (int)$editData['ID'] ?>">

                        <div class="area">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" value="<?= htmlspecialchars($editData["nama"]) ?>" required>
                        </div>

                        <div class="area">
                            <label for="email">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($editData["email"]) ?>" required>
                        </div>

                        <div class="area">
                            <label for="alamat">Alamat</label>
                            <input type="text" name="alamat" value="<?= htmlspecialchars($editData["alamat"]) ?>">
                        </div>
                        
                        <div class="area">
                            <label for="kota">Kota</label>
                            <input type="text" name="kota" value="<?= htmlspecialchars($editData["kota"]) ?>">
                        </div>

                        <div class="area full">
                            <label for="pesan">Pesan</label>
                            <textarea name="msg" required><?= htmlspecialchars($editData["msg"]) ?></textarea>
                        </div>

                        <div class="tombol full">
                            <input class="btn kecil" type="submit" value="Simpan">
                            <a href="tampil.php" class="btn kecil batal">Batal</a>
                        </div>
                    </form>
                </section>
                <?php endif; ?>

                <section class="panel">
                    <h2>Daftar Pesan</h2>
                    <p class="sub">Menampilkan <?= $jumlah ?> pesan <?= $q !== "" ? " untuk &quot;".htmlspecialchars($q)."&quot;" : "" ?></p>

                    <div class="tabel-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Alamat</th>
                                    <th>Kota</th>
                                    <th>Pesan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($jumlah > 0): ?>
                                    <?php while($row = $result->fetch_assoc()):?>
                                    <tr class="<?= $row["ID"] == $editId ? "sedang" : "" ?>">
                                        <td><?= htmlspecialchars($row["ID"]) ?></td>
                                        <td><?= htmlspecialchars($row["nama"]) ?></td>
                                        <td><?= htmlspecialchars($row["email"]) ?></td>
                                        <td><?= htmlspecialchars($row["alamat"]) ?></td>
                                        <td><?= htmlspecialchars($row["kota"]) ?></td>
                                        <td class="pesan"><?= nl2br(htmlspecialchars($row["msg"])) ?></td>
                                        <td class="aksi">
                                            <a href="tampil.php?edit=<?= (int)$row["ID"] ?>" class="btn kecil">Edit</a>

                                            <form method="post" action="tampil.php" class="inline" onsubmit="return confirm('Hapus pesan dari <?= htmlspecialchars($row["nama"], ENT_QUOTES) ?>?')">
                                                <input type="hidden" name="aksi" value="hapus">
                                                <input type="hidden" name="id" value="<?= (int)$row["ID"] ?>">
                                                <button type="submit" class="btn kecil bahaya">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="kosong">Belum ada Belum</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>
</html>