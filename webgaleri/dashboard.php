<?php
session_start();
include 'db.php';

// Ensure only admin can access this page
if ($_SESSION['status_login'] != true || $_SESSION['role'] != 'admin') {
    echo '<script>window.location="profil.php"</script>';
}

    // Cek apakah pengguna sudah login dan apakah role-nya admin
    if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true) {
        $userID = $_SESSION['id'];
        $query = mysqli_query($conn, "SELECT Role FROM User WHERE UserID = '$userID'");
        $user = mysqli_fetch_object($query);
        $isAdmin = ($user->Role == 'admin'); // Cek apakah role pengguna adalah admin
    }

// Fetch admin data
$query = mysqli_query($conn, "SELECT * FROM User WHERE UserID = '" . $_SESSION['id'] . "' AND role = 'admin'");
$d = mysqli_fetch_object($query);

$totalUsers = mysqli_fetch_object(mysqli_query($conn, "SELECT COUNT(*) as total FROM User"))->total;
$totalAlbums = mysqli_fetch_object(mysqli_query($conn, "SELECT COUNT(*) as total FROM Album"))->total;
$totalPhotos = mysqli_fetch_object(mysqli_query($conn, "SELECT COUNT(*) as total FROM Foto"))->total;
$totalComments = mysqli_fetch_object(mysqli_query($conn, "SELECT COUNT(*) as total FROM Komentar"))->total;
$totalLikes = mysqli_fetch_object(mysqli_query($conn, "SELECT COUNT(*) as total FROM `Like`"))->total;

// User management and album management code...

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            overflow-x: hidden;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            background-color: #343a40;
            position: fixed;
            top: 0;
            left: 0;
            color: #fff;
            padding: 20px 0;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
        }
        .sidebar a:hover {
            background-color: rgb(29, 25, 25);
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        footer {
        width: 100%;
        position: absolute;
        bottom: 0;
    }
    
    </style>
</head>
<body>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">GALERI NANDO</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="galeri.php">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="profil.php">Profil</a></li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true): ?>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="register.php">Registrasi</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="dashboard.php"><h3 class="text-center">Admin Panel</h3></a>
        <a href="dashboard.php?tab=user">User</a>
        <a href="dashboard.php?tab=album">Album</a>
        <a href="dashboard.php?tab=foto">Foto</a>
        <a href="dashboard.php?tab=komentar">Komentar</a>
        <a href="dashboard.php?tab=like">Like</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php
        if (isset($_GET['tab'])) {
            $tab = $_GET['tab'];
            switch ($tab) {
                case 'user':
                    include 'tabel/tabel-user.php';
                    break;
                case 'album':
                    include 'tabel/tabel-album.php';
                    break;
                case 'foto':
                    include 'tabel/tabel-foto.php';
                    break;
                case 'komentar':
                    include 'tabel/tabel-komentar.php';
                    break;
                case 'like':
                    include 'tabel/tabel-like.php';
                    break;
                default:
                    echo "<h3>Selamat datang di Dashboard Admin</h3>";
            }
        } else {
            ?>
            <div class="container py-4">
                <h3 class="mb-4">Selamat datang di Dashboard Admin</h3>
                
                <div class="row g-4">
                    <!-- Users Card -->
                    <div class="col-md-4">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Total Users</h6>
                                        <h2 class="mb-0 mt-2"><?php echo $totalUsers; ?></h2>
                                    </div>
                                    <i class="bi bi-people fs-1"></i>
                                </div>
                            </div>
                            <div class="card-footer py-3">
                                <a href="dashboard.php?tab=user" class="text-white text-decoration-none">View Details <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
        
                    <!-- Albums Card -->
                    <div class="col-md-4">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Total Albums</h6>
                                        <h2 class="mb-0 mt-2"><?php echo $totalAlbums; ?></h2>
                                    </div>
                                    <i class="bi bi-journal-album fs-1"></i>
                                </div>
                            </div>
                            <div class="card-footer py-3">
                                <a href="dashboard.php?tab=album" class="text-white text-decoration-none">View Details <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
        
                    <!-- Photos Card -->
                    <div class="col-md-4">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Total Photos</h6>
                                        <h2 class="mb-0 mt-2"><?php echo $totalPhotos; ?></h2>
                                    </div>
                                    <i class="bi bi-images fs-1"></i>
                                </div>
                            </div>
                            <div class="card-footer py-3">
                                <a href="dashboard.php?tab=foto" class="text-white text-decoration-none">View Details <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
        
                    <!-- Comments Card -->
                    <div class="col-md-6">
                        <div class="card bg-warning text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Total Comments</h6>
                                        <h2 class="mb-0 mt-2"><?php echo $totalComments; ?></h2>
                                    </div>
                                    <i class="bi bi-chat-dots fs-1"></i>
                                </div>
                            </div>
                            <div class="card-footer py-3">
                                <a href="dashboard.php?tab=komentar" class="text-white text-decoration-none">View Details <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
        
                    <!-- Likes Card -->
                    <div class="col-md-6">
                        <div class="card bg-danger text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Total Likes</h6>
                                        <h2 class="mb-0 mt-2"><?php echo $totalLikes; ?></h2>
                                    </div>
                                    <i class="bi bi-heart fs-1"></i>
                                </div>
                            </div>
                            <div class="card-footer py-3">
                                <a href="dashboard.php?tab=like" class="text-white text-decoration-none">View Details <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>

    <footer class="bg-dark text-white py-3">
        <div class="container text-center">
            <small>Copyright &copy; 2025 - Web Galeri Foto. All Rights Reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
