<?php
include 'db.php';

// Handle CRUD actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Handle adding album
    if ($action == 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $namaAlbum = $_POST['namaAlbum'];
        $deskripsi = $_POST['deskripsi'];
        $userID = $_SESSION['id']; // Ambil UserID dari session
        
        $query = "INSERT INTO Album (NamaAlbum, Deskripsi, TanggalDibuat, UserID) VALUES ('$namaAlbum', '$deskripsi', NOW(), $userID)";
        mysqli_query($conn, $query);
        echo '<script>window.location="dashboard.php?tab=album";</script>';
    }

    // Handle delete album
    elseif ($action == 'hapus' && $id > 0) {
        $query = "DELETE FROM Album WHERE AlbumID = $id";
        mysqli_query($conn, $query);
        echo '<script>window.location="dashboard.php?tab=album";</script>';
    }

    // Handle edit album
    elseif ($action == 'edit' && $id > 0 && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $namaAlbum = $_POST['namaAlbum'];
        $deskripsi = $_POST['deskripsi'];

        $query = "UPDATE Album SET NamaAlbum = '$namaAlbum', Deskripsi = '$deskripsi' WHERE AlbumID = $id";
        mysqli_query($conn, $query);
        echo '<script>window.location="dashboard.php?tab=album";</script>';
    }
}

// Fetch album data
$query = mysqli_query($conn, "SELECT * FROM Album ORDER BY AlbumID DESC");
$albums = mysqli_fetch_all($query, MYSQLI_ASSOC);

// Fetch single album for edit
$albumToEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && $id > 0) {
    $query = mysqli_query($conn, "SELECT * FROM Album WHERE AlbumID = $id");
    $albumToEdit = mysqli_fetch_assoc($query);
}
?>

<h3>Data Album</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Nama Album</th>
            <th>Deskripsi</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($albums as $row): ?>
            <tr>
                <td><?= $row['AlbumID']; ?></td>
                <td><?= $row['NamaAlbum']; ?></td>
                <td><?= $row['Deskripsi']; ?></td>
                <td>
                    <a href="dashboard.php?tab=album&action=edit&id=<?= $row['AlbumID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="dashboard.php?tab=album&action=hapus&id=<?= $row['AlbumID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Tambah Album Modal -->
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAlbumModal">Tambah Album</button>

<!-- Edit Album Modal -->
<?php if ($albumToEdit): ?>
    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editAlbumModal">Edit Album</button>
<?php endif; ?>

<!-- Add Album Modal -->
<div class="modal fade" id="addAlbumModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="dashboard.php?tab=album&action=tambah">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Album</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="namaAlbum" class="form-label">Nama Album</label>
                        <input type="text" id="namaAlbum" name="namaAlbum" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Album Modal -->
<?php if ($albumToEdit): ?>
    <div class="modal fade" id="editAlbumModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="dashboard.php?tab=album&action=edit&id=<?= $albumToEdit['AlbumID']; ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Album</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="namaAlbum" class="form-label">Nama Album</label>
                            <input type="text" id="namaAlbum" name="namaAlbum" class="form-control" value="<?= $albumToEdit['NamaAlbum']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" class="form-control" required><?= $albumToEdit['Deskripsi']; ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
