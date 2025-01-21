<?php
session_start();
error_reporting(0);
include 'db.php';

// Check login and admin role
$isAdmin = false;
if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true) {
    $userID = $_SESSION['id'];
    $query = mysqli_query($conn, "SELECT Role FROM User WHERE UserID = '$userID'");
    $user = mysqli_fetch_object($query);
    $isAdmin = ($user->Role == 'admin');
}

// Fetch albums for the dropdown and display
$albums = mysqli_query($conn, "SELECT * FROM Album WHERE UserID = $userID");
    // Upload photo
if (isset($_POST['submit_foto'])) {
    $judul_foto = $_POST['judul_foto'];
    $deskripsi_foto = $_POST['deskripsi_foto'];
    $album_id = $_POST['album_id'];
    $tanggal_unggah = date('Y-m-d');
    $gambar = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];

    move_uploaded_file($tmp_name, "foto/" . $gambar);

    $query = "INSERT INTO Foto (JudulFoto, Deskripsi, TanggalUnggah, Gambar, Album_ID, UserID) 
              VALUES ('$judul_foto', '$deskripsi_foto', '$tanggal_unggah', '$gambar', '$album_id', '$userID')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Foto berhasil diunggah!');</script>";
        header("Refresh:0");
    } else {
        echo "<script>alert('Gagal mengunggah foto.');</script>";
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Tambah Image - WEB Galeri Foto</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    :root {
        --primary-color: #e60023;
        --secondary-color: #767676;
        --background-color: #fff;
        --hover-color: #f0f0f0;
    }

    body {
        background-color: var(--background-color);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Fira Sans", "Droid Sans", "Helvetica Neue", Helvetica, "ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro", メイリオ, Meiryo, "ＭＳ Ｐゴシック", Arial, sans-serif;
    }
        /* Navbar */
    .navbar {
            height: 80px;
            box-shadow: none;
            border-bottom: 1px solid #efefef;
            padding: 0.5rem 1rem;
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .nav-link {
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 24px;
            transition: background-color 0.3s;
        }

        .nav-link:hover {
            background-color: var(--hover-color);
        }
    </style>
<link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <!-- header -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
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

    <div class="container text-center mt-5">
        <h1>Upload Gambar</h1>
        <p>Ingin Kembali?</p>
        <button class="btn btn-primary btn-lg mt-3" onclick="history.back();">
            Kembali
        </button>
    </div>
    
    <!-- content -->
    <div class="card shadow-sm mx-auto" style="max-width: 400px; padding: 15px; border-radius: 10px; margin-top: 4rem">
                <h4 class="text-center mb-3">Unggah Foto</h4>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="text" name="judul_foto" class="form-control" placeholder="Judul Foto" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="deskripsi_foto" class="form-control" placeholder="Deskripsi Foto" required></textarea>
                    </div>
                    <div class="mb-3">
                        <select name="album_id" class="form-control" required>
                            <option value="">Pilih Album</option>
                            <?php while ($album = mysqli_fetch_array($albums)): ?>
                                <option value="<?php echo $album['AlbumID']; ?>"><?php echo $album['NamaAlbum']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="file" name="gambar" class="form-control" id="imageInput" required>
                    </div>
                    <!-- Image Preview -->
                    <div class="mb-3">
                        <img id="imagePreview" src="" alt="Image Preview" style="width: 100%; height: auto; display: none; border-radius: 10px; object-fit: cover;">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="submit_foto" class="btn btn-primary w-100">Unggah Foto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- footer -->
    <footer>
        <div class="container mx-auto">
            <small>Copyright &copy; 2024 - Web Galeri Foto.</small>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('imageInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            const imagePreview = document.getElementById('imagePreview');
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block'; // Show the image
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>
