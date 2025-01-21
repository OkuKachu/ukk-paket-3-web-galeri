<?php
session_start();
include 'db.php';

if (!isset($_SESSION['status_login'])) {
    header('Location: login.php');
    exit;
}

if (isset($_POST['submit'])) {
    $judul_foto = $_POST['judul_foto'];
    $deskripsi = $_POST['deskripsi'];
    $album_id = $_POST['album_id'];
    $user_id = $_SESSION['id'];
    $tanggal_unggah = date('Y-m-d');
    $gambar = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];

    move_uploaded_file($tmp_name, "foto/" . $gambar);

    $query = "INSERT INTO Foto (JudulFoto, Deskripsi, TanggalUnggah, Gambar, Album_ID, UserID)
              VALUES ('$judul_foto', '$deskripsi', '$tanggal_unggah', '$gambar', '$album_id', '$user_id')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Foto berhasil diunggah');</script>";
    } else {
        echo "<script>alert('Gagal mengunggah foto');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Unggah Foto</title>
</head>
<body>
    <h2>Unggah Foto</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Judul Foto:</label>
        <input type="text" name="judul_foto" required />
        <label>Deskripsi:</label>
        <textarea name="deskripsi" required></textarea>
        <label>Album:</label>
        <select name="album_id" required>
            <option value="">Pilih Album</option>
            <?php
            $albums = mysqli_query($conn, "SELECT * FROM Album WHERE UserID = " . $_SESSION['id']);
            while ($album = mysqli_fetch_array($albums)) {
                echo "<option value='" . $album['AlbumID'] . "'>" . $album['NamaAlbum'] . "</option>";
            }
            ?>
        </select>
        <label>Gambar:</label>
        <input type="file" name="gambar" required />
        <button type="submit" name="submit">Unggah</button>
    </form>
</body>
</html>
