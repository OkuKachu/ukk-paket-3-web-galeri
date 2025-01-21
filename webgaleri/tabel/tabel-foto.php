<?php
include 'db.php';

// Handle form submission for creating or updating foto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if ($action == 'tambah') {
        $judul = $_POST['judul'];
        $deskripsi = $_POST['deskripsi'];
        $album_id = $_POST['album_id'];
        $user_id = $_SESSION['id'];

        // File upload handling
        $file_name = $_FILES['gambar']['name'];
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_destination = "uploads/" . $file_name;

        if (move_uploaded_file($file_tmp, $file_destination)) {
            mysqli_query($conn, "INSERT INTO Foto (JudulFoto, Deskripsi, TanggalUnggah, Gambar, Album_ID, UserID) 
                                VALUES ('$judul', '$deskripsi', NOW(), '$file_name', '$album_id', '$user_id')");
            echo "<script>alert('Foto berhasil ditambahkan!');</script>";
        } else {
            echo "<script>alert('Gagal mengunggah file!');</script>";
        }
    } elseif ($action == 'edit') {
        $id = $_GET['id'];
        $judul = $_POST['judul'];
        $deskripsi = $_POST['deskripsi'];

        // Update with or without new image
        if (!empty($_FILES['gambar']['name'])) {
            $file_name = $_FILES['gambar']['name'];
            $file_tmp = $_FILES['gambar']['tmp_name'];
            $file_destination = "uploads/" . $file_name;
            move_uploaded_file($file_tmp, $file_destination);
            $query = "UPDATE Foto SET JudulFoto='$judul', Deskripsi='$deskripsi', Gambar='$file_name' WHERE FotoID='$id'";
        } else {
            $query = "UPDATE Foto SET JudulFoto='$judul', Deskripsi='$deskripsi' WHERE FotoID='$id'";
        }
        mysqli_query($conn, $query);
        echo "<script>alert('Foto berhasil diubah!');</script>";
    }
}

// Handle deletion
if (isset($_GET['action']) && $_GET['action'] == 'hapus') {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM Foto WHERE FotoID = '$id'");
    echo "<script>alert('Foto berhasil dihapus!');</script>";
}
?>

<h3>Data Foto</h3>
<!-- Button to trigger modal to add new photo -->
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addFotoModal">Tambah Foto</button>

<!-- Table for displaying photos -->
<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>#</th>
            <th>Judul Foto</th>
            <th>Deskripsi</th>
            <th>Album</th>
            <th>Gambar</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = mysqli_query($conn, "SELECT Foto.*, Album.NamaAlbum FROM Foto JOIN Album ON Foto.Album_ID = Album.AlbumID ORDER BY FotoID DESC");
        while ($row = mysqli_fetch_assoc($query)) {
            ?>
            <tr>
                <td><?= $row['FotoID'] ?></td>
                <td><?= $row['JudulFoto'] ?></td>
                <td><?= $row['Deskripsi'] ?></td>
                <td><?= $row['NamaAlbum'] ?></td>
                <td><img src="foto/<?= $row['Gambar'] ?>" width="100"></td>
                <td>
                    <!-- Button to edit photo -->
                    <a href="foto.php?action=edit&id=<?= $row['FotoID'] ?>" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editFotoModal-<?= $row['FotoID'] ?>">Edit</a>
                    <!-- Button to delete photo -->
                    <a href="foto.php?action=hapus&id=<?= $row['FotoID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Modal Tambah Foto -->
<div class="modal fade" id="addFotoModal" tabindex="-1" aria-labelledby="addFotoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="foto.php?action=tambah" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFotoModalLabel">Tambah Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Judul Foto</label>
                        <input type="text" class="form-control" name="judul" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Album</label>
                        <select class="form-control" name="album_id" required>
                            <option value="">Pilih Album</option>
                            <?php
                            $albums = mysqli_query($conn, "SELECT * FROM Album");
                            while ($album = mysqli_fetch_assoc($albums)) {
                                echo "<option value='{$album['AlbumID']}'>{$album['NamaAlbum']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Upload Gambar</label>
                        <input type="file" class="form-control" name="gambar" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Foto -->
<?php
$query = mysqli_query($conn, "SELECT * FROM Foto ORDER BY FotoID DESC");
while ($row = mysqli_fetch_assoc($query)) {
    ?>
    <div class="modal fade" id="editFotoModal-<?= $row['FotoID'] ?>" tabindex="-1" aria-labelledby="editFotoModalLabel-<?= $row['FotoID'] ?>" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="foto.php?action=edit&id=<?= $row['FotoID'] ?>" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFotoModalLabel-<?= $row['FotoID'] ?>">Edit Foto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Judul Foto</label>
                            <input type="text" class="form-control" name="judul" value="<?= $row['JudulFoto'] ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Deskripsi</label>
                            <textarea class="form-control" name="deskripsi" required><?= $row['Deskripsi'] ?></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Album</label>
                            <select class="form-control" name="album_id" required>
                                <option value="">Pilih Album</option>
                                <?php
                                $albums = mysqli_query($conn, "SELECT * FROM Album");
                                while ($album = mysqli_fetch_assoc($albums)) {
                                    echo "<option value='{$album['AlbumID']}' " . ($album['AlbumID'] == $row['Album_ID'] ? 'selected' : '') . ">{$album['NamaAlbum']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>Upload Gambar (Kosongkan jika tidak ingin mengganti)</label>
                            <input type="file" class="form-control" name="gambar">
                        </div>
                        <div class="form-group mb-3">
                            <img src="uploads/<?= $row['Gambar'] ?>" width="100" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php } ?>
