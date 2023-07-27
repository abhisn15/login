<?php
session_start();
include 'db_connect.php';

// Cek apakah form sudah disubmit
if (!empty($_POST["captcha_input"]) && isset($_SESSION["captcha"])) {
    // Cek apakah email dan password tidak kosong
    if (!empty($_POST["email"]) && !empty($_POST["password"])) {
        $userMengisiCaptcha = strtoupper($_POST["captcha_input"]);
        $tampilanCaptcha = $_SESSION["captcha"];

        // Periksa apakah captcha yang diinput sesuai dengan captcha yang diharapkan
        if ($userMengisiCaptcha === $tampilanCaptcha) {
            // Proses form login di sini

            // Ambil input dari pengguna
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Validasi kredensial login di database
            $query = "SELECT * FROM login WHERE email = '$email' AND password = '$password'";
            $result = mysqli_query($koneksi, $query);

            if (!$result) {
                die("Query gagal: " . mysqli_error($koneksi));
            }

            $numRows = mysqli_num_rows($result);

            if (mysqli_num_rows($result) === 1) {

                $row = mysqli_fetch_assoc($result);
                if ($numRows > 0) {
                    // Login berhasil, set session variables
                    $_SESSION['email'] = $email;
                    $_SESSION['password'] = $password;
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['id'] = $row['id'];
                    // Redirect ke halaman yang diinginkan setelah login berhasil
                    header("Location: home.php");
                    exit();
                }
            } else {
                // Login gagal
                echo "Login gagal. Silakan periksa email dan password Anda.";
                exit;
            }
        } else {
            // Verifikasi captcha gagal
            // Tampilkan pesan kesalahan menggunakan "alert" setelah halaman selesai dimuat
            echo '<script>alert("Verifikasi captcha gagal. Silakan coba lagi."); window.location.href = ("./login.php")</script>';
            exit;
        }

        // Bersihkan nilai session captcha
        unset($_SESSION["captcha"]);
    } else {
        // Email atau password kosong
        echo "Silakan isi semua kolom yang diperlukan.";
        exit;
    }
}
