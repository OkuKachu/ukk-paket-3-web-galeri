<?php
    session_start();
    include 'db.php';

    // Pastikan hanya user yang sudah login yang bisa mengakses halaman ini
    if ($_SESSION['status_login'] != true) {
        echo '<script>window.location="login.php"</script>';
    }

    // Ambil data user berdasarkan UserID di session
    $query = mysqli_query($conn, "SELECT * FROM User WHERE UserID = '".$_SESSION['id']."'");
    if (!$query) {
        echo 'Query gagal: ' . mysqli_error($conn);
        exit;
    }
    $d = mysqli_fetch_object($query);
    if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true) {
        $userID = $_SESSION['id'];
        $query = mysqli_query($conn, "SELECT Role FROM User WHERE UserID = '$userID'");
        $user = mysqli_fetch_object($query);
        $isAdmin = ($user->Role == 'admin'); 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #fff;
            border-bottom: 1px solid #ddd;
        }
        .navbar-brand {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">GALERI NANDO</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="galeri.php">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Profil</a></li>
                    <?php if (isset($isAdmin) && $isAdmin): ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true): ?>
                    <li class="nav-item"><a class="nav-link" href="keluar.php">Logout</a></li>
                    <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="registrasi.php">Registrasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="section">
        <div class="container">
            <h3>Profil Anda</h3>
            <div class="box">
                <form action="" method="POST">
                    <input type="text" name="nama" placeholder="Nama Lengkap" class="input-control" value="<?php echo htmlspecialchars($d->NamaLengkap); ?>" required>
                    <input type="text" name="user" placeholder="Username" class="input-control" value="<?php echo htmlspecialchars($d->Username); ?>" required>
                    <input type="email" name="email" placeholder="Email" class="input-control" value="<?php echo htmlspecialchars($d->Email); ?>" required>
                    <textarea name="alamat" placeholder="Alamat" class="input-control" required><?php echo htmlspecialchars($d->Alamat); ?></textarea>
                    <input type="submit" name="submit" value="Ubah Profil" class="btn">
                </form>
                <?php
                    // Proses update profil
                    if (isset($_POST['submit'])) {
                        $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
                        $user   = mysqli_real_escape_string($conn, $_POST['user']);
                        $email  = mysqli_real_escape_string($conn, $_POST['email']);
                        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

                        $update = mysqli_query($conn, "UPDATE User SET 
                            NamaLengkap = '".$nama."',
                            Username = '".$user."',
                            Email = '".$email."',
                            Alamat = '".$alamat."'
                            WHERE UserID = '".$d->UserID."'");

                        if ($update) {
                            echo '<script>alert("Ubah data berhasil")</script>';
                            echo '<script>window.location="profil.php"</script>';
                        } else {
                            echo 'Gagal: '.mysqli_error($conn);
                        }
                    }
                ?>
            </div>
            
            <h3>Ubah Password</h3>
            <div class="box">
                <form action="" method="POST">
                    <input type="password" name="pass1" placeholder="Password Baru" class="input-control" required>
                    <input type="password" name="pass2" placeholder="Konfirmasi Password Baru" class="input-control" required>
                    <input type="submit" name="ubah_password" value="Ubah Password" class="btn">
                </form>
                <?php
                    // Proses ubah password
                    if (isset($_POST['ubah_password'])) {
                        $pass1 = $_POST['pass1'];
                        $pass2 = $_POST['pass2'];

                        if ($pass2 != $pass1) {
                            echo '<script>alert("Konfirmasi Password Baru tidak sesuai")</script>';
                        } else {
                            $hashed_password = password_hash($pass1, PASSWORD_DEFAULT);
                            $u_pass = mysqli_query($conn, "UPDATE User SET 
                                Password = '".$hashed_password."' 
                                WHERE UserID = '".$d->UserID."'");

                            if ($u_pass) {
                                echo '<script>alert("Ubah password berhasil")</script>';
                                echo '<script>window.location="profil.php"</script>';
                            } else {
                                echo 'Gagal: '.mysqli_error($conn);
                            }
                        }
                    }
                ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <small>Copyright &copy; 2024 - Web Galeri Foto.</small>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
