<?php
session_start();
include 'db.php';

// Handle Login Form Submission
if (isset($_POST['login_submit'])) {
    $user = mysqli_real_escape_string($conn, $_POST['user']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);

    $cek = mysqli_query($conn, "SELECT * FROM user WHERE Username = '$user'");

    if (mysqli_num_rows($cek) > 0) {
        $d = mysqli_fetch_object($cek);

        if (password_verify($pass, $d->Password)) {
            $_SESSION['status_login'] = true;
            $_SESSION['a_global'] = $d;
            $_SESSION['id'] = $d->UserID;
            $_SESSION['role'] = $d->role;

            if ($d->role == 'admin') {
                header('Location: dashboard.php');
                exit;
            } else {
                header('Location: index.php');
                exit;
            }
        } else {
            echo '<script>alert("Username atau password salah")</script>';
        }
    } else {
        echo '<script>alert("Username atau password salah")</script>';
    }
}

// Handle Registration Form Submission
if (isset($_POST['register_submit'])) {
    $nama = ucwords($_POST['nama']);
    $username = $_POST['user'];
    $password = password_hash($_POST['pass'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $alamat = ucwords($_POST['almt']);

    $insert = mysqli_query($conn, "INSERT INTO user (NamaLengkap, Username, Password, Email, Alamat, role) VALUES ('$nama', '$username', '$password', '$email', '$alamat', 'user')");

    if ($insert) {
        echo '<script>alert("Registrasi berhasil")</script>';
        echo '<script>window.location="login.php"</script>';
    } else {
        echo 'Gagal ' . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login & Registrasi | Web Galeri Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>
    <div class="container" id="container">
        <!-- Sign Up Form -->
        <div class="form-container sign-up-container">
            <form method="POST" action="">
                <h1>Create Account</h1>
                <input type="text" name="nama" placeholder="Nama Lengkap" required />
                <input type="text" name="user" placeholder="Username" required />
                <input type="password" name="pass" placeholder="Password" required />
                <input type="email" name="email" placeholder="Email" required />
                <input type="text" name="almt" placeholder="Alamat" required />
                <button type="submit" name="register_submit">Sign Up</button>
            </form>
        </div>

        <!-- Sign In Form -->
        <div class="form-container sign-in-container">
            <form method="POST" action="">
                <h1>Sign in</h1>
                <input type="text" name="user" placeholder="Username" required />
                <input type="password" name="pass" placeholder="Password" required />
                <button type="submit" name="login_submit">Sign In</button>
            </form>
        </div>

        <!-- Overlay Section -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>Sudah punya akun?</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Kamu belum punya akun</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const signUpButton = document.getElementById('signUp');
        const signInButton = document.getElementById('signIn');
        const container = document.getElementById('container');

        signUpButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
        });
    </script>
</body>
</html>
