<?php
include 'db.php';

// Handle CRUD actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Handle adding komentar
    if ($action == 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $fotoID = $_POST['fotoID'];
        $userID = $_SESSION['id']; // Ambil UserID dari session
        $isiKomentar = $_POST['isiKomentar'];
        
        $query = "INSERT INTO Komentar (FotoID, UserID, IsiKomentar, TanggalKomentar) 
                  VALUES ('$fotoID', '$userID', '$isiKomentar', NOW())";
        mysqli_query($conn, $query);
        echo '<script>window.location="dashboard.php?tab=komentar";</script>';
    }

    // Handle delete komentar
    elseif ($action == 'hapus' && $id > 0) {
        $query = "DELETE FROM Komentar WHERE KomentarID = $id";
        mysqli_query($conn, $query);
        echo '<script>window.location="dashboard.php?tab=komentar";</script>';
    }

    // Handle edit komentar
    elseif ($action == 'edit' && $id > 0 && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $isiKomentar = $_POST['isiKomentar'];

        $query = "UPDATE Komentar SET IsiKomentar = '$isiKomentar' WHERE KomentarID = $id";
        mysqli_query($conn, $query);
        echo '<script>window.location="dashboard.php?tab=komentar";</script>';
    }
}

// Fetch komentar data
$query = mysqli_query($conn, "SELECT Komentar.*, Foto.JudulFoto, User.Username FROM Komentar 
                              JOIN Foto ON Komentar.FotoID = Foto.FotoID 
                              JOIN User ON Komentar.UserID = User.UserID 
                              ORDER BY KomentarID DESC");
$komentar = mysqli_fetch_all($query, MYSQLI_ASSOC);

// Fetch single komentar for edit
$komentarToEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && $id > 0) {
    $query = mysqli_query($conn, "SELECT * FROM Komentar WHERE KomentarID = $id");
    $komentarToEdit = mysqli_fetch_assoc($query);
}
?>

<h3>Data Komentar</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Komentar</th>
            <th>Foto</th>
            <th>User</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($komentar as $row): ?>
            <tr>
                <td><?= $row['KomentarID']; ?></td>
                <td><?= $row['IsiKomentar']; ?></td>
                <td><?= $row['JudulFoto']; ?></td>
                <td><?= $row['Username']; ?></td>
                <td>
                    <a href="dashboard.php?tab=komentar&action=edit&id=<?= $row['KomentarID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="dashboard.php?tab=komentar&action=hapus&id=<?= $row['KomentarID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Tambah Komentar Modal -->
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKomentarModal">Tambah Komentar</button>

<!-- Edit Komentar Modal -->
<?php if ($komentarToEdit): ?>
    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editKomentarModal">Edit Komentar</button>
<?php endif; ?>

<!-- Add Komentar Modal -->
<div class="modal fade" id="addKomentarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="dashboard.php?tab=komentar&action=tambah">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Komentar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fotoID" class="form-label">Foto</label>
                        <select id="fotoID" name="fotoID" class="form-control" required>
                            <option value="">Pilih Foto</option>
                            <?php
                            $fotos = mysqli_query($conn, "SELECT * FROM Foto");
                            while ($foto = mysqli_fetch_assoc($fotos)) {
                                echo "<option value='{$foto['FotoID']}'>{$foto['JudulFoto']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="isiKomentar" class="form-label">Isi Komentar</label>
                        <textarea id="isiKomentar" name="isiKomentar" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Komentar Modal -->
<?php if ($komentarToEdit): ?>
    <div class="modal fade" id="editKomentarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="dashboard.php?tab=komentar&action=edit&id=<?= $komentarToEdit['KomentarID']; ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Komentar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="isiKomentar" class="form-label">Isi Komentar</label>
                            <textarea id="isiKomentar" name="isiKomentar" class="form-control" required><?= $komentarToEdit['IsiKomentar']; ?></textarea>
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
