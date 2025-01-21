<?php
    session_start();
    include 'db.php';
    if($_SESSION['status_login'] != true || $_SESSION['a_global']->admin_role != 'admin'){
        echo '<script>window.location="login.php"</script>';
    }
    
    $produk = mysqli_query($conn, "SELECT * FROM tb_foto WHERE foto_id = '".$_GET['id']."' ");
    $p = mysqli_fetch_object($produk);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Image - WEB Galeri Foto</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <!-- header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">GALERI NANDO</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Tautan Umum -->
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="galeri.php">Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profil.php">Profil</a>
                    </li>
                    
                    <!-- Tampilkan link Dashboard hanya untuk admin -->
                    <?php if (isset($isAdmin) && $isAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Dashboard</a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Tautan Login / Logout -->
                    <?php if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="keluar.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">    
                            <a class="nav-link" href="registrasi.php">Registrasi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- content -->
    <div class="section">
        <div class="container">
            <h3>Edit Image</h3>
            <div class="box">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="text" name="nama" placeholder="Nama Foto" class="input-control" value="<?php echo $p->foto_nama ?>" required>
                    <input type="file" name="gambar" class="input-control">
                    <input type="submit" name="submit" value="Submit" class="btn">
                </form>
                <?php
                    if(isset($_POST['submit'])){
                        $nama   = $_POST['nama'];
                        $gambar = $_FILES['gambar']['name'];
                        $tmp    = $_FILES['gambar']['tmp_name'];
                        $type   = $_FILES['gambar']['type'];
                        
                        if($gambar != ''){
                            if($type == 'image/jpeg' || $type == 'image/png'){
                                unlink('foto/'.$p->foto_gambar);
                                move_uploaded_file($tmp, 'foto/'.$gambar);
                                $update = mysqli_query($conn, "UPDATE tb_foto SET 
                                    foto_nama = '".$nama."',
                                    foto_gambar = '".$gambar."'
                                    WHERE foto_id = '".$p->foto_id."'");
                            }else{
                                echo '<script>alert("Format file tidak didukung")</script>';
                            }
                        }else{
                            $update = mysqli_query($conn, "UPDATE tb_foto SET 
                                foto_nama = '".$nama."'
                                WHERE foto_id = '".$p->foto_id."'");
                        }
                        
                        if($update){
                            echo '<script>alert("Ubah data berhasil")</script>';
                            echo '<script>window.location="data-image.php"</script>';
                        }else{
                            echo 'gagal '.mysqli_error($conn);
                        }
                    }
                ?>
            </div>
        </div>
    </div>
    
    <!-- footer -->
    <footer>
        <div class="container">
            <small>Copyright &copy; 2024 - Web Galeri Foto.</small>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
