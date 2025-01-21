<?php
session_start();
include 'db.php';

$foto_id = $_GET['id']; 
$query = mysqli_query($conn, "SELECT * FROM Foto WHERE FotoID = '$foto_id'");
$foto = mysqli_fetch_object($query);
$image_path = 'foto/' . $foto->Gambar;


// Proses Hapus Foto
if (isset($_POST['delete_photo'])) {
    // Check if user is the owner or admin
    if ($foto->UserID == $_SESSION['id'] || (isset($isAdmin) && $isAdmin)) {
        // Delete the photo file from the server
        $image_path = 'foto/' . $foto->Gambar;
        if (file_exists($image_path)) {
            unlink($image_path); // Delete the image file
        }

        // Delete photo record from the database
        $delete_query = mysqli_query($conn, "DELETE FROM Foto WHERE FotoID = '$foto_id'");
        if ($delete_query) {
            header("Location: galeri.php"); // Redirect to gallery page after deletion
            exit;
        } else {
            echo "Error: Unable to delete photo.";
        }
    } else {
        echo "You don't have permission to delete this photo.";
    }
}

// Proses komentar
if (isset($_POST['submit_comment'])) {
    $isi_komentar = $_POST['isi_komentar'];
    $tanggal_komentar = date('Y-m-d');
    $query = "INSERT INTO Komentar (FotoID, UserID, IsiKomentar, TanggalKomentar)
              VALUES ('$foto_id', '".$_SESSION['id']."', '$isi_komentar', '$tanggal_komentar')";
    mysqli_query($conn, $query);
}

// First, modify your comment query to include timestamps and comment IDs
$comments = mysqli_query($conn, "SELECT 
    Komentar.IsiKomentar, 
    Komentar.KomentarID,
    Komentar.UserID as KomentarUserID,
    Komentar.TanggalKomentar,
    User.Username 
    FROM Komentar 
    JOIN User ON Komentar.UserID = User.UserID 
    WHERE Komentar.FotoID = '$foto_id'
    ORDER BY Komentar.TanggalKomentar DESC");

// Add handler for comment deletion
if(isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];
    // Check if user is admin or comment owner
    $delete_check = mysqli_query($conn, "SELECT UserID FROM Komentar WHERE KomentarID = '$comment_id'");
    $comment_data = mysqli_fetch_object($delete_check);

    if ($comment_data) {
        // Check if the comment owner is the logged-in user or if the user is an admin
        if ($comment_data->UserID == $_SESSION['id'] || (isset($isAdmin) && $isAdmin)) {
            mysqli_query($conn, "DELETE FROM Komentar WHERE KomentarID = '$comment_id'");
            echo "<script>window.location.reload();</script>";
        }
    } else {
        // If no comment was found, you can log an error or handle it as needed
        echo "<script>alert('Komentar tidak ditemukan!');</script>";
    }
}

// Handle "Like" and "Unlike" actions
if (isset($_GET['like'])) {
    $user_id = $_SESSION['id'];
    $tanggal_like = date('Y-m-d');
    
    // Check if user already liked the photo
    $check_like = mysqli_query($conn, "SELECT * FROM `Like` WHERE FotoID = '$foto_id' AND UserID = '$user_id'");
    
    if (mysqli_num_rows($check_like) > 0) {
        // If already liked, then "Unlike" (Delete the like)
        mysqli_query($conn, "DELETE FROM `Like` WHERE FotoID = '$foto_id' AND UserID = '$user_id'");
    } else {
        // If not liked, then "Like" (Insert the like)
        mysqli_query($conn, "INSERT INTO `Like` (FotoID, UserID, TanggalLike) VALUES ('$foto_id', '$user_id', '$tanggal_like')");
    }
    
    // After toggling, reload the page to update the button state
    header("Location: detail-image.php?id=$foto_id");
    exit;
}

// Check if the user has liked the photo
$liked = false;
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $check_like = mysqli_query($conn, "SELECT * FROM `Like` WHERE FotoID = '$foto_id' AND UserID = '$user_id'");
    if (mysqli_num_rows($check_like) > 0) {
        $liked = true;
    }
}

// Get the total number of likes
$likes = mysqli_query($conn, "SELECT COUNT(*) AS total_likes FROM `Like` WHERE FotoID = '$foto_id'");
$total_likes = mysqli_fetch_object($likes)->total_likes;

if (isset($_SESSION['status_login']) && $_SESSION['status_login'] == true) {
    $userID = $_SESSION['id'];
    $query = mysqli_query($conn, "SELECT Role FROM User WHERE UserID = '$userID'");
    $user = mysqli_fetch_object($query);
    $isAdmin = ($user->Role == 'admin'); // Cek apakah role pengguna adalah admin
}

if (isset($_POST['update_photo'])) {
    // Collect new data from the form
    $judul_foto = $_POST['judul_foto'];
    $deskripsi = $_POST['deskripsi'];
    $foto_id = $_GET['id'];  // Assuming you are passing the 'id' of the photo in the URL

    // Fetch the existing photo details from the database
    $result = mysqli_query($conn, "SELECT Gambar FROM foto WHERE FotoID = '$foto_id'");
    $foto = mysqli_fetch_object($result);
    
    // Handle the file upload if there is a new image
    $new_image_name = $foto->Gambar; // Default to existing image name

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        // Tentukan direktori upload
        $upload_dir = 'foto/';
        
        // Dapatkan nama file asli dan ekstensi
        $file_name = basename($_FILES['gambar']['name']);
        
        // Tentukan path untuk file yang akan diupload
        $new_image_name = $file_name; // Only save the file name in the database
        
        // Tentukan path lengkap di folder upload
        $upload_path = $upload_dir . $new_image_name;

        // Cek apakah file sudah ada, jika sudah ganti nama file
        if (file_exists($upload_path)) {
            $file_name = pathinfo($file_name, PATHINFO_FILENAME) . '_' . time() . '.' . pathinfo($file_name, PATHINFO_EXTENSION);
            $new_image_name = $file_name;
            $upload_path = $upload_dir . $new_image_name;
        }

        // Validasi ekstensi file
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($upload_path, PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            echo "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
            exit;
        }

        // Periksa apakah file berhasil dipindahkan
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
            echo "File successfully uploaded: " . $new_image_name;
        } else {
            echo "Failed to upload file.";
            exit;
        }
    }

    // Update the photo in the database
    $update_sql = "UPDATE foto SET JudulFoto = ?, Deskripsi = ?, Gambar = ? WHERE FotoID = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('sssi', $judul_foto, $deskripsi, $new_image_name, $foto_id);
    if ($stmt->execute()) {
        echo "Photo successfully updated!";
    } else {
        echo "Error updating photo.";
    }
    $stmt->close();

    // Redirect to the same page to reflect the changes
    header("Location: detail-image.php?id=$foto_id");  // Redirect to the same page to reflect the changes
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
        .card {
            border: 1px solid #ddd;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card img {
            width: 100%;
            height: auto;
            border-bottom: 1px solid #ddd;
        }
        .comments-section {
            padding: 10px;
            border-top: 1px solid #ddd;
        }
        .comment {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #ccc;
            margin-right: 10px;
        }
        .comment-text {
            flex: 1;
            font-size: 14px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 10px;
        }
        button {
            background-color: #3897f0;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: #287bdc;
        }
    </style>
</head>
<body>

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

<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="row g-0">
                    <!-- Image Column -->
                    <div class="col-md-8">
                        <img src="foto/<?php echo $foto->Gambar; ?>" class="img-fluid rounded-start h-100 w-100 object-fit-cover" alt="Foto" style="max-height: 600px;">
                    </div>
                    
                    <!-- Details Column -->
                    <div class="col-md-4">
                        <div class="card-body h-100 d-flex flex-column">
                            <!-- Header Section -->
                            <div>
                                <h4 class="card-title"><?php echo $foto->JudulFoto; ?></h4>
                                <p class="card-text text-muted"><?php echo $foto->Deskripsi; ?></p>
                            </div>

                            <!-- Likes Section -->
                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <span class="fw-bold">Likes: <?php echo $total_likes; ?></span>
                                    <a href="detail-image.php?id=<?php echo $foto_id; ?>&like=true" 
                                       class="btn btn-light btn-sm">
                                        <i class="fas fa-heart <?php echo $liked ? 'text-danger' : ''; ?>"></i>
                                        <?php echo $liked ? 'Liked' : 'Like'; ?>
                                    </a>
                                </div>
                            </div>

                            <!-- Updated Comments Section HTML -->
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Comments</h6>
                                    <button class="btn btn-link btn-sm text-decoration-none p-0" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#commentsDropdown">
                                        Show/Hide
                                    </button>
                                </div>
                                
                                <div class="collapse show" id="commentsDropdown">
                                    <div class="comments-container overflow-auto mb-3" style="max-height: 300px;">
                                        <?php
                                        if (mysqli_num_rows($comments) > 0) {
                                            while ($comment = mysqli_fetch_array($comments)) {
                                                $commentDate = new DateTime($comment['TanggalKomentar']);
                                                ?>
                                                <div class="comment border-bottom py-2">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($comment['Username']); ?></strong>
                                                            <p class="mb-1 small"><?php echo htmlspecialchars($comment['IsiKomentar']); ?></p>
                                                            <small class="text-muted">
                                                                <?php echo $commentDate->format('d M Y H:i'); ?>
                                                            </small>
                                                        </div>
                                                        <?php if ($comment['KomentarUserID'] == $_SESSION['id'] || (isset($isAdmin) && $isAdmin)): ?>
                                                            <form method="POST" class="ms-2" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                                                <input type="hidden" name="comment_id" value="<?php echo $comment['KomentarID']; ?>">
                                                                <button type="submit" name="delete_comment" class="btn btn-link text-danger btn-sm p-0">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        } else {
                                            echo "<p class='text-muted small'>No comments yet.</p>";
                                        }
                                        ?>
                                    </div>
                                </div>

                                <!-- Comment Form -->
                                <form method="POST" class="mt-auto">
                                    <textarea name="isi_komentar" 
                                            class="form-control form-control-sm mb-2" 
                                            placeholder="Add a comment..." 
                                            rows="2" 
                                            required></textarea>
                                    <button type="submit" 
                                            name="submit_comment" 
                                            class="btn btn-primary btn-sm w-100">
                                        Post Comment
                                    </button>
                                </form>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-3 d-flex gap-2">
                                <button type="button" 
                                        class="btn btn-warning btn-sm flex-grow-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editPhotoModal">
                                    Edit Photo
                                </button>
                                
                                <?php if ($foto->UserID == $_SESSION['id'] || (isset($isAdmin) && $isAdmin)): ?>
                                <form method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this photo?');" 
                                      class="flex-grow-1">
                                    <button type="submit" 
                                            name="delete_photo" 
                                            class="btn btn-danger btn-sm w-100">
                                        Delete
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                            
                            <button class="btn btn-secondary btn-sm mt-2" 
                                    onclick="window.history.back();">
                                Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<!-- Modal Edit Foto -->
<div class="modal fade" id="editPhotoModal" tabindex="-1" aria-labelledby="editPhotoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPhotoModalLabel">Edit Foto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="judul_foto" class="form-label">Judul Foto</label>
            <input type="text" class="form-control" id="judul_foto" name="judul_foto" value="<?php echo $foto->JudulFoto; ?>" required>
          </div>
          <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required><?php echo $foto->Deskripsi; ?></textarea>
          </div>
          <div class="mb-3">
            <label for="gambar" class="form-label">Gambar (optional)</label>
            <input type="file" class="form-control" id="gambar" name="gambar" onchange="previewImage(event)">
            <small class="form-text text-muted">Jika Anda tidak ingin mengubah gambar, biarkan kosong.</small>
            <!-- Image Preview -->
            <img id="imagePreview" src="#" alt="Image Preview" style="display: none; margin-top: 10px; width: 100%; max-height: 300px; object-fit: cover;">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" name="update_photo" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var image = document.getElementById('imagePreview');
        image.src = reader.result;
        image.style.display = 'block'; // Display the image preview
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>