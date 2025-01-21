<?php
include 'db.php';

// Handle CRUD actions for users
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Handle adding user
    if ($action == 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $email = $_POST['email'];
        $nama_lengkap = $_POST['nama_lengkap'];
        $alamat = $_POST['alamat'];

        $query = "INSERT INTO User (Username, Password, Email, NamaLengkap, Alamat) 
                  VALUES ('$username', '$password', '$email', '$nama_lengkap', '$alamat')";
        mysqli_query($conn, $query);
        echo '<script>window.location="dashboard.php?tab=user";</script>';
    }

    // Handle deleting user
    elseif ($action == 'hapus' && $id > 0) {
        $query = "DELETE FROM User WHERE UserID = $id";
        mysqli_query($conn, $query);
        echo '<script>window.location="dashboard.php?tab=user";</script>';
    }

    // Handle editing user
    elseif ($action == 'edit' && $id > 0 && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $nama_lengkap = $_POST['nama_lengkap'];
        $alamat = $_POST['alamat'];

        $query = "UPDATE User SET Email='$email', NamaLengkap='$nama_lengkap', Alamat='$alamat' WHERE UserID = $id";
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $query .= ", Password='$password'";
        }
        mysqli_query($conn, $query);
        echo '<script>window.location="dashboard.php?tab=user";</script>';
    }
}

// Fetch user data
$query = mysqli_query($conn, "SELECT * FROM User ORDER BY UserID DESC");
$users = mysqli_fetch_all($query, MYSQLI_ASSOC);

// Fetch single user for edit
$userToEdit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && $id > 0) {
    $query = mysqli_query($conn, "SELECT * FROM User WHERE UserID = $id");
    $userToEdit = mysqli_fetch_assoc($query);
}
?>

<h3>Data User</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Username</th>
            <th>Email</th>
            <th>Nama Lengkap</th>
            <th>Alamat</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $row): ?>
            <tr>
                <td><?= $row['UserID']; ?></td>
                <td><?= $row['Username']; ?></td>
                <td><?= $row['Email']; ?></td>
                <td><?= $row['NamaLengkap']; ?></td>
                <td><?= $row['Alamat']; ?></td>
                <td>
                    <a href="dashboard.php?tab=user&action=edit&id=<?= $row['UserID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="dashboard.php?tab=user&action=hapus&id=<?= $row['UserID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal Tambah User -->
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Tambah User</button>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="dashboard.php?tab=user&action=tambah">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea id="alamat" name="alamat" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<?php if ($userToEdit): ?>
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="dashboard.php?tab=user&action=edit&id=<?= $userToEdit['UserID']; ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username" class="form-control" value="<?= $userToEdit['Username']; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?= $userToEdit['Email']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" value="<?= $userToEdit['NamaLengkap']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea id="alamat" name="alamat" class="form-control" required><?= $userToEdit['Alamat']; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password (Kosongkan jika tidak ingin mengganti)</label>
                            <input type="password" id="password" name="password" class="form-control">
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

<script>
    // Open the edit modal when the page loads
    window.onload = function() {
        var modal = new bootstrap.Modal(document.getElementById('editUserModal'));
        modal.show();
    }
</script>

