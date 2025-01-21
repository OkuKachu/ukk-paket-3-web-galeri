<?php
session_start();
error_reporting(0);
include 'db.php';

// Fetch all albums from the database
$kategori = mysqli_query($conn, "SELECT * FROM Album ORDER BY AlbumID DESC");

// Get the album ID from URL parameters
$albumID = $_GET['id'];
$album = mysqli_query($conn, "SELECT * FROM Album WHERE AlbumID = $albumID");
$albumData = mysqli_fetch_object($album);

// Get photos in the album by albumID
$fotos = mysqli_query($conn, "SELECT * FROM Foto WHERE Album_ID = $albumID");

// Check if the album exists
if (!$albumData) {
    echo "Album not found!";
    exit();
}

    // Cek apakah pengguna sudah login dan apakah role-nya admin
    if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true) {
        $userID = $_SESSION['id'];
        $query = mysqli_query($conn, "SELECT Role FROM User WHERE UserID = '$userID'");
        $user = mysqli_fetch_object($query);
        $isAdmin = ($user->Role == 'admin'); // Cek apakah role pengguna adalah admin
    }

// Check user permissions (Admin or Album Owner)
if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true) {
    $userID = $_SESSION['id'];

    // Check if user is owner of the album or an admin
    if ($albumData->UserID != $userID && (!isset($isAdmin) || !$isAdmin)) {
        echo "You do not have permission to delete this album.";
        exit();
    }
}

// Handle album deletion on form submit
if (isset($_POST['delete_album'])) {
    // Disable foreign key checks for safe deletion
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0;");

    // Delete photos in the album first
    $fotoQuery = mysqli_query($conn, "SELECT Gambar FROM Foto WHERE Album_ID = $albumID");
    while ($foto = mysqli_fetch_array($fotoQuery)) {
        // Delete image from the folder
        if (file_exists("foto/" . $foto['Gambar'])) {
            unlink("foto/" . $foto['Gambar']);
        }
    }

    // Delete photos from database
    $deleteFotos = mysqli_query($conn, "DELETE FROM Foto WHERE Album_ID = $albumID");

    if (!$deleteFotos) {
        echo "Failed to delete photos in the album.";
        exit();
    }

    // Delete album from database
    $deleteAlbum = mysqli_query($conn, "DELETE FROM Album WHERE AlbumID = $albumID");

    if (!$deleteAlbum) {
        echo "Failed to delete album.";
        exit();
    }

    // Re-enable foreign key checks
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1;");

    // Redirect to the gallery page after deletion
    header("Location: galeri.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album - <?php echo htmlspecialchars($albumData->NamaAlbum); ?></title>
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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Fira Sans", "Droid Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
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

        .album-header {
            padding: 20px 0;
            text-align: center;
            margin-bottom: 30px;
        }

        .album-title {
            font-size: 36px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .album-description {
            color: var(--secondary-color);
            font-size: 18px;
            margin-bottom: 5px;
        }

        .album-date {
            color: var(--secondary-color);
            font-size: 14px;
        }

        .masonry-grid {
            columns: 5;
            column-gap: 20px;
            padding: 0 20px;
        }

        @media (max-width: 1200px) {
            .masonry-grid { columns: 4; }
        }
        @media (max-width: 992px) {
            .masonry-grid { columns: 3; }
        }
        @media (max-width: 768px) {
            .masonry-grid { columns: 2; }
        }

        .pin-item {
            break-inside: avoid;
            margin-bottom: 20px;
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .pin-item:hover {
            transform: scale(1.02);
        }

        .pin-image {
            width: 100%;
            display: block;
            border-radius: 16px;
        }

        .pin-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 20px;
            color: white;
        }

        .pin-item:hover .pin-overlay {
            opacity: 1;
        }

        .pin-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .pin-date {
            font-size: 12px;
            opacity: 0.8;
        }

        .action-buttons {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 1000;
        }

        .floating-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: transform 0.3s;
        }

        .add-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .back-btn {
            background-color: #6c757d;
            color: white;
        }

        .floating-btn:hover {
            transform: scale(1.1);
        }

        .empty-state {
            text-align: center;
            padding: 100px 20px;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
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

     <!-- Album Header -->
     <div class="album-header">
        <h1 class="album-title"><?php echo htmlspecialchars($albumData->NamaAlbum); ?></h1>
        <p class="album-description"><?php echo htmlspecialchars($albumData->Deskripsi); ?></p>
        <p class="album-date">Dibuat pada <?php echo date('d F Y', strtotime($albumData->TanggalDibuat)); ?></p>
    </div>

    <!-- Floating Action Buttons -->
    <div class="action-buttons">
        <a href="tambah-image.php" class="floating-btn add-btn" title="Tambah Foto">
            <i class="fas fa-plus fa-lg"></i>
        </a>
        <?php if ($albumData->UserID == $_SESSION['id'] || (isset($isAdmin) && $isAdmin)): ?>
        <form method="POST" style="margin: 0;" onsubmit="return confirm('Yakin ingin menghapus album ini beserta seluruh foto di dalamnya?');">
            <button type="submit" name="delete_album" class="floating-btn delete-btn" title="Hapus Album">
                <i class="fas fa-trash fa-lg"></i>
            </button>
        </form>
        <?php endif; ?>
        <button onclick="window.location.href = document.referrer;" class="floating-btn back-btn" title="Kembali">
            <i class="fas fa-arrow-left fa-lg"></i>
        </button>
    </div>

    <!-- Photos Masonry Grid -->
    <?php if (mysqli_num_rows($fotos) > 0): ?>
    <div class="masonry-grid">
        <?php while ($foto = mysqli_fetch_array($fotos)): ?>
        <div class="pin-item">
            <a href="detail-image.php?id=<?php echo $foto['FotoID'] ?>">
                <img src="foto/<?php echo $foto['Gambar'] ?>" alt="<?php echo htmlspecialchars($foto['JudulFoto']) ?>" class="pin-image">
                <div class="pin-overlay">
                    <div class="pin-title"><?php echo htmlspecialchars(substr($foto['JudulFoto'], 0, 30)) ?></div>
                    <div class="pin-date"><?php echo date('d F Y', strtotime($foto['TanggalUnggah'])) ?></div>
                </div>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-images"></i>
        <h3>Album ini masih kosong</h3>
        <p>Klik tombol + untuk menambahkan foto pertama ke album ini</p>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
