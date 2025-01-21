<?php
include 'db.php';

// Handle photo deletion
if (isset($_GET['idp'])) {
    $foto = mysqli_query($conn, "SELECT Gambar FROM Foto WHERE FotoID = '".$_GET['idp']."' ");
    $p = mysqli_fetch_object($foto);
    if (file_exists('foto/'.$p->Gambar)) {
        unlink('foto/'.$p->Gambar); // Delete the image file
    }
    mysqli_query($conn, "DELETE FROM Foto WHERE FotoID = '".$_GET['idp']."'");
    header('Location: galeri.php');
}

// Handle comment deletion
if (isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];
    mysqli_query($conn, "DELETE FROM Komentar WHERE CommentID = '$comment_id'");
    header("Location: detail-image.php?id=".$_GET['foto_id']); // Redirect back to the photo detail page
}
?>
