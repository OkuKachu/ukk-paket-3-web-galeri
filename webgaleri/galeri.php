<?php
session_start();
error_reporting(0);
include 'db.php';

// Fetch all photos from the database
$foto = mysqli_query($conn, "SELECT Foto.*, User.Username FROM Foto JOIN User ON Foto.UserID = User.UserID ORDER BY Foto.FotoID DESC");
// Check login and admin role
$isAdmin = false;
if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true) {
    $userID = $_SESSION['id'];
    $query = mysqli_query($conn, "SELECT Role FROM User WHERE UserID = '$userID'");
    $user = mysqli_fetch_object($query);
    $isAdmin = ($user->Role == 'admin');
} else {
    header("Location: login.php");
    exit();
}

// Add album
if (isset($_POST['submit_album'])) {
    $nama_album = mysqli_real_escape_string($conn, $_POST['nama_album']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tanggal_dibuat = date('Y-m-d');

    $query = "INSERT INTO Album (NamaAlbum, Deskripsi, TanggalDibuat, UserID) 
              VALUES ('$nama_album', '$deskripsi', '$tanggal_dibuat', '$userID')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Album berhasil ditambahkan!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan album.');</script>";
    }
}

// Upload photo
if (isset($_POST['submit_foto'])) {
    $judul_foto = mysqli_real_escape_string($conn, $_POST['judul_foto']);
    $deskripsi_foto = mysqli_real_escape_string($conn, $_POST['deskripsi_foto']);
    $album_id = mysqli_real_escape_string($conn, $_POST['album_id']);
    $tanggal_unggah = date('Y-m-d');
    
    // File upload handling
    $filename = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];
    $type = $_FILES['gambar']['type'];
    $size = $_FILES['gambar']['size'];

    // Allowed image types
    $allowed_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');
    
    if (in_array($type, $allowed_types)) {
        // Generate unique filename
        $new_filename = date('YmdHis') . '_' . $filename;
        
        if (move_uploaded_file($tmp_name, "foto/" . $new_filename)) {
            $query = "INSERT INTO Foto (JudulFoto, Deskripsi, TanggalUnggah, Gambar, Album_ID, UserID) 
                      VALUES ('$judul_foto', '$deskripsi_foto', '$tanggal_unggah', '$new_filename', '$album_id', '$userID')";
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Foto berhasil diunggah!'); window.location='index.php';</script>";
            } else {
                echo "<script>alert('Gagal mengunggah foto.');</script>";
            }
        } else {
            echo "<script>alert('Gagal mengupload file.');</script>";
        }
    } else {
        echo "<script>alert('Tipe file tidak didukung.');</script>";
    }
}

// Fetch albums for display
$albums_query = mysqli_query($conn, "SELECT Album.*, User.Username 
    FROM Album 
    JOIN User ON Album.UserID = User.UserID 
    ORDER BY Album.TanggalDibuat DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        /* Navigation Bar */
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

        /* Floating Action Button */
        .floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: transform 0.3s;
            z-index: 1000;
        }

        .floating-btn:hover {
            transform: scale(1.1);
        }

        /* Masonry Grid */
        .masonry-grid {
            columns: 5;
            column-gap: 16px;
            padding: 16px;
        }

        @media (max-width: 1200px) {
            .masonry-grid {
                columns: 4;
            }
        }

        @media (max-width: 992px) {
            .masonry-grid {
                columns: 3;
            }
        }

        .masonry-item {
            break-inside: avoid;
            margin-bottom: 16px;
            position: relative;
        }

        /* Card Styling */
        .pin-card {
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .pin-card:hover {
            transform: scale(1.02);
        }

        .pin-card img {
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .pin-card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
        }

        .pin-card:hover .pin-card-overlay {
            opacity: 1;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 16px;
            border: none;
        }

        .upload-form {
            padding: 24px;
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #ddd;
            padding: 12px;
            margin-bottom: 16px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 24px;
            padding: 12px 24px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #ad081b;
        }

        /* Image Preview */
        #imagePreview {
            max-height: 300px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
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

    <!-- Floating Action Button -->
    <div class="floating-btn" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="fas fa-plus fa-lg"></i>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Konten Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#photoUpload">Upload Foto</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#albumCreate">Buat Album</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="photoUpload">
                            <form method="POST" enctype="multipart/form-data" class="upload-form">
                                <input type="text" name="judul_foto" class="form-control" placeholder="Judul Foto" required>
                                <textarea name="deskripsi_foto" class="form-control" placeholder="Deskripsi Foto" required></textarea>
                                <select name="album_id" class="form-control" required>
                                    <option value="">Pilih Album</option>
                                    <?php 
                                    // Reset pointer for album query
                                    mysqli_data_seek($albums_query, 0);
                                    while ($album = mysqli_fetch_array($albums_query)): 
                                    ?>
                                    <option value="<?php echo $album['AlbumID']; ?>"><?php echo $album['NamaAlbum']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                                <input type="file" name="gambar" class="form-control" id="imageInput" accept="image/*" required>
                                <img id="imagePreview" src="" alt="" style="display: none;">
                                <button type="submit" name="submit_foto" class="btn btn-primary w-100">Unggah Foto</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="albumCreate">
                            <form method="POST" class="upload-form">
                                <input type="text" name="nama_album" class="form-control" placeholder="Nama Album" required>
                                <textarea name="deskripsi" class="form-control" placeholder="Deskripsi Album" required></textarea>
                                <button type="submit" name="submit_album" class="btn btn-primary w-100">Buat Album</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Masonry Grid Gallery -->
    <div class="container-fluid">
        <div class="masonry-grid">
            <?php
            // Reset pointer for album query (jika sebelumnya sudah digunakan)
            mysqli_data_seek($albums_query, 0);

            // Cek apakah terdapat data album
            if (mysqli_num_rows($albums_query) > 0): 
                while ($album = mysqli_fetch_array($albums_query)):
                    // Query untuk mengambil foto cover album
                    $cover_query = mysqli_query($conn, "SELECT * FROM Foto WHERE Album_ID = {$album['AlbumID']} ORDER BY TanggalUnggah DESC LIMIT 1");
                    $cover = mysqli_fetch_array($cover_query);
            ?>
                <div class="masonry-item">
                    <div class="pin-card">
                        <a href="album.php?id=<?php echo $album['AlbumID']; ?>" class="text-decoration-none">
                            <?php if ($cover): ?>
                                <img src="foto/<?php echo $cover['Gambar']; ?>" alt="<?php echo htmlspecialchars($album['NamaAlbum']); ?>" class="img-fluid">
                            <?php else: ?>
                                <div class="placeholder-img d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                    <i class="fas fa-images fa-3x text-secondary"></i>
                                </div>
                            <?php endif; ?>
                            <div class="pin-card-overlay">
                                <h5 class="text-white"><?php echo htmlspecialchars($album['NamaAlbum']); ?></h5>
                                <p class="text-white-50"><?php echo htmlspecialchars($album['Deskripsi']); ?></p>
                                <small class="text-white-50">Dibuat oleh: <?php echo htmlspecialchars($album['Username']); ?></small>
                            </div>
                        </a>
                    </div>
                </div>
            <?php 
                endwhile; 
            else: 
            ?>
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-secondary mb-3"></i>
                    <h5>Belum ada album</h5>
                    <p class="text-muted">Klik tombol + untuk membuat album baru</p>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview functionality
        document.getElementById('imageInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body>
</html>