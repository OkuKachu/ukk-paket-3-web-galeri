<?php
include 'db.php';

// Handle like addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['action']) && $_GET['action'] == 'tambah') {
    $foto_id = $_POST['foto_id'];
    $user_id = $_SESSION['id'];

    // Fix the query by escaping the table name
    mysqli_query($conn, "INSERT INTO `Like` (FotoID, UserID, TanggalLike) VALUES ('$foto_id', '$user_id', NOW())");
    echo "<script>alert('Like berhasil ditambahkan!');</script>";
}

// Handle unlike (delete like)
if (isset($_GET['action']) && $_GET['action'] == 'hapus') {
    $id = $_GET['id'];
    // Fix the query by escaping the table name
    mysqli_query($conn, "DELETE FROM `Like` WHERE LikeID = '$id'");
    echo "<script>alert('Like berhasil dihapus!');</script>";
}

// Fetch like data
$query = mysqli_query($conn, "SELECT `Like`.*, User.Username, Foto.JudulFoto FROM `Like`
                              JOIN Foto ON `Like`.FotoID = Foto.FotoID 
                              JOIN User ON `Like`.UserID = User.UserID 
                              ORDER BY LikeID DESC");
$likes = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>

<h3>Data Like</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Foto</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($likes as $row): ?>
            <tr>
                <td><?= $row['LikeID']; ?></td>
                <td><?= $row['Username']; ?></td>
                <td><?= $row['JudulFoto']; ?></td>
                <td>
                <a href="dashboard.php?tab=like&action=hapus&id=<?= $row['LikeID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Tambah Like Modal -->
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLikeModal">Tambah Like</button>

<!-- Add Like Modal -->
<div class="modal fade" id="addLikeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="dashboard.php?tab=like&action=tambah">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Like</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="foto_id" class="form-label">Foto</label>
                        <select id="foto_id" name="foto_id" class="form-control" required>
                            <option value="">Pilih Foto</option>
                            <?php
                            $fotos = mysqli_query($conn, "SELECT * FROM Foto");
                            while ($foto = mysqli_fetch_assoc($fotos)) {
                                echo "<option value='{$foto['FotoID']}'>{$foto['JudulFoto']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
