<?php
    session_start();
    include 'db.php';
    if($_SESSION['status_login'] != true || $_SESSION['a_global']->admin_role != 'admin'){
        echo '<script>window.location="login.php"</script>';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>WEB Galeri Foto</title>
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
            <h3>Data Foto</h3>
            <div class="box">
                <p><a href="tambah-image.php">Tambah Data</a></p>
                <table border="1" cellspacing="0" class="table">
                    <thead>
                        <tr>
                            <th width="60px">No</th>
                            <th>Kategori</th>
                            <th>Nama Foto</th>
                            <th>Gambar</th>
                            <th>Status</th>
                            <th width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 1;
                            $foto = mysqli_query($conn, "SELECT * FROM tb_foto ORDER BY foto_id DESC");
                            if(mysqli_num_rows($foto) > 0){
                                while($row = mysqli_fetch_array($foto)){
                        ?>
                        <tr>
                            <td><?php echo $no++ ?></td>
                            <td><?php echo $row['kategori_id'] ?></td>
                            <td><?php echo $row['foto_nama'] ?></td>
                            <td><a href="foto/<?php echo $row['foto_gambar'] ?>" target="_blank"> <img src="foto/<?php echo $row['foto_gambar'] ?>" width="50px"> </a></td>
                            <td><?php echo ($row['foto_status'] == 1)? 'Aktif':'Tidak Aktif' ?></td>
                            <td>
                                <a href="edit-image.php?id=<?php echo $row['foto_id'] ?>">Edit</a> || <a href="proses-hapus.php?idp=<?php echo $row['foto_id'] ?>" onclick="return confirm('Yakin ingin hapus ?')">Hapus</a>
                            </td>
                        </tr>
                        <?php }}else{ ?>
                            <tr>
                                <td colspan="6">Tidak ada data</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
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
