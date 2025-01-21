<?php
    include 'db.php';
    session_start();

    // Fetch all photos from the database
    $foto = mysqli_query($conn, "SELECT Foto.*, User.Username FROM Foto JOIN User ON Foto.UserID = User.UserID ORDER BY Foto.FotoID DESC");

    if (!isset($_SESSION['id'])) {
        header('Location: login.php');
        exit();
    }
    
    
    // Cek apakah pengguna sudah login dan apakah role-nya admin
    if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true) {
        $userID = $_SESSION['id'];
        $query = mysqli_query($conn, "SELECT Role FROM User WHERE UserID = '$userID'");
        $user = mysqli_fetch_object($query);
        $isAdmin = ($user->Role == 'admin'); // Cek apakah role pengguna adalah admin
    }

    // Fetch all albums
    $albums_query = mysqli_query($conn, "SELECT Album.*, User.Username 
                                        FROM Album 
                                        JOIN User ON Album.UserID = User.UserID 
                                        ORDER BY Album.TanggalDibuat DESC");

    // Fetch photos based on album filter (optional)
    $album_filter = isset($_GET['album']) ? (int)$_GET['album'] : null;
    if ($album_filter) {
        // Query untuk foto dalam album tertentu
        $foto = mysqli_query($conn, "SELECT Foto.*, User.Username, Album.NamaAlbum 
                                    FROM Foto 
                                    JOIN User ON Foto.UserID = User.UserID 
                                    JOIN Album ON Foto.Album_ID = Album.AlbumID 
                                    WHERE Album_ID = $album_filter 
                                    ORDER BY Foto.FotoID DESC");

        // Get album name for display
        $album_query = mysqli_query($conn, "SELECT NamaAlbum FROM Album WHERE AlbumID = $album_filter");
        $album_name = mysqli_num_rows($album_query) > 0 ? mysqli_fetch_object($album_query)->NamaAlbum : "Album Tidak Ditemukan";
    } else {
        // Query untuk semua foto tanpa filter album
        $foto = mysqli_query($conn, "SELECT Foto.*, User.Username, Album.NamaAlbum 
                                    FROM Foto 
                                    JOIN User ON Foto.UserID = User.UserID 
                                    JOIN Album ON Foto.Album_ID = Album.AlbumID 
                                    ORDER BY Foto.FotoID DESC");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WEB Galeri Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
    .container-image {
        display: flex; /* Use flexbox for Masonry */
        flex-wrap: wrap; /* Allow items to wrap */
        justify-content: center;
        margin-top: 3rem; /* Center items horizontally */
        margin-left: 3rem;
    }

    .item {
        background: white; /* Card background */
        border-radius: 8px; /* Rounded corners */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Shadow effect */
        overflow: hidden;
        margin-bottom: 1rem;
        transition: transform 0.2s; 
        width: calc(20% - 15px); /* 4 items per row */
        display: flex; /* Use flexbox */
        flex-direction: column; /* Stack children vertically */
        align-items: center; /* Center items horizontally */
    }

    .item img {
        width: 100%; /* Full width */
        height: auto; /* Maintain aspect ratio */
        object-fit: cover; /* Cover the area */
        display: block; /* Ensure the image is treated as a block element */
    }

    .item:hover {
        transform: scale(1.05); /* Zoom effect on hover */
    }

    .text-center {
        padding: 10px;
    }

    .font-weight-bold {
        font-size: 18px; /* Larger title */
        margin: 0; /* Remove default margin */
    }

    .text-muted {
        font-size: 14px; /* Username styling */
        color: #6c757d; /* Muted color */
    }

    .album-filter {
            padding: 16px;
            background-color: var(--background-color);
            border-bottom: 1px solid #efefef;
        }

        .album-chips {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding: 8px 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .album-chips::-webkit-scrollbar {
            display: none;
        }

        .album-chip {
            padding: 8px 16px;
            background-color: #f0f0f0;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 500;
            color: var(--secondary-color);
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .album-chip:hover {
            background-color: #e0e0e0;
            color: var(--primary-color);
        }

        .album-chip.active {
            background-color: var(--primary-color);
            color: white;
        }

        .album-header {
            padding: 24px 16px;
            text-align: center;
        }

        .album-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }

</style>
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
    
    <?php if ($album_filter): ?>
    <!-- <div class="album-header">
        <h2><?php echo htmlspecialchars($album_name); ?></h2>
    </div> -->
    <?php endif; ?>

    <div class="album-filter">
        <div class="container-fluid">
            <div class="album-chips">
                <!-- Filter untuk semua foto -->
                <a href="index.php" class="album-chip <?php echo !$album_filter ? 'active' : ''; ?>">
                    Semua Foto
                </a>
                <?php
                // Query untuk mendapatkan semua album tanpa filter pengguna
                $albums_query = mysqli_query($conn, "SELECT * FROM Album ORDER BY TanggalDibuat DESC");
                while ($album = mysqli_fetch_array($albums_query)):
                ?>
                <!-- Filter untuk setiap album -->
                <a href="index.php?album=<?php echo $album['AlbumID']; ?>" 
                class="album-chip <?php echo $album_filter == $album['AlbumID'] ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($album['NamaAlbum']); ?>
                </a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>


    <div class="container-image">
        <?php
            // Assuming you have the query to fetch photos as shown above
            if (mysqli_num_rows($foto) > 0) {
                while ($p = mysqli_fetch_array($foto)) {
        ?>
        <div class="item">
            <a href="detail-image.php?id=<?php echo $p['FotoID'] ?>" class="d-block">
                <img src="foto/<?php echo $p['Gambar'] ?>" alt="<?php echo $p['JudulFoto']; ?>" />
            </a>
            <div class="text-center mt-2">
                <p class="font-weight-bold"><Strong><?php echo substr($p['JudulFoto'], 0, 30); ?></Strong></p>
                <p class="text-muted">Uploaded by: <?php echo $p['Username']; ?></p>
            </div>
        </div>
        <?php
                }
            } else {
                echo "<p>Foto tidak ada</p>";
            }
        ?>
    </div>

    <footer>
        <small>Copyright &copy; 2025 - Galeri Nando.</small>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js"></script>
    <script>
        // Initialize Masonry after all images have loaded
        document.addEventListener('DOMContentLoaded', function() {
            var grid = document.querySelector('.container-image');
            var msnry = new Masonry(grid, {
                itemSelector: '.item',
                columnWidth: '.item',
                percentPosition: true,
                gutter: 15
            });
        });
    </script>
</body>
</html>