<?php
session_start();
include 'db.php';

if (!isset($_SESSION['status_login']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (isset($_POST['submit'])) {
    $nama_album = $_POST['nama_album'];
    $deskripsi = $_POST['deskripsi'];
    $user_id = $_SESSION['id'];
    $tanggal_dibuat = date('Y-m-d');

    $query = "INSERT INTO Album (NamaAlbum, Deskripsi, TanggalDibuat, UserID) 
              VALUES ('$nama_album', '$deskripsi', '$tanggal_dibuat', '$user_id')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Album berhasil ditambahkan');</script>";
    } else {
        echo "<script>alert('Gagal menambahkan album');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Album</title>
</head>
<body>
    <h2>Tambah Album</h2>
    <form method="POST">
        <label>Nama Album:</label>
        <input type="text" name="nama_album" required />
        <label>Deskripsi:</label>
        <textarea name="deskripsi" required></textarea>
        <button type="submit" name="submit">Tambah Album</button>
    </form>
</body>
</html>
