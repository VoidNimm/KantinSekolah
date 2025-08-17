<?php
session_start();
include 'config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    $jurusan = $conn->real_escape_string($_POST['jurusan']);
    $nama_lengkap = $conn->real_escape_string($_POST['nama_lengkap']);

    // Check if username exists
    $check = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($check->num_rows > 0) {
        $error = "Username sudah digunakan!";
    } else {
        // Insert new admin user
        $result = $conn->query("INSERT INTO users (username, password, jurusan, nama_lengkap) 
                              VALUES ('$username', '$password', '$jurusan', '$nama_lengkap')");
        
        if ($result) {
            $success = "Registrasi berhasil! Silakan login.";
        } else {
            $error = "Gagal melakukan registrasi: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin Kantin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #041562;
            --secondary-color: #11468F;
            --accent-color: #DA1212;
            --light-color: #EEEEEE;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .register-wrapper {
            display: flex;
            max-width: 1100px;
            width: 100%;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .register-image {
            flex: 1;
            background: url('img/gor-davtyan-0BIvjaQwvQQ-unsplash.jpg') center/cover no-repeat;
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .register-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(4, 21, 98, 0.7);
        }
        
        .register-image-content {
            position: relative;
            z-index: 1;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .register-image-content h2 {
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .register-container {
            flex: 1;
            padding: 40px;
            max-width: 500px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .register-subtitle {
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .form-control {
            height: 50px;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding-left: 15px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(4, 21, 98, 0.25);
        }
        
        .btn-register {
            background-color: var(--primary-color);
            color: white;
            border: none;
            height: 50px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-register:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
        }
        
        .login-link a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
        }
        
        .input-group-text {
            background-color: white;
            border-right: none;
        }
        
        .input-group .form-control {
            border-left: none;
        }
        
        select.form-control {
            height: 50px;
        }
        
        @media (max-width: 768px) {
            .register-wrapper {
                flex-direction: column;
            }
            
            .register-image {
                min-height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <div class="register-image">
            <div class="register-image-content">
                <h3>Register Admin</h3>
                <p>Selamat Datang</p>
            </div>
        </div>
        
        <div class="register-container">
            <div class="register-header">
                <h2 class="register-title">Registrasi Admin Kantin</h2>
                <p class="register-subtitle">Buat akun admin baru untuk mengelola kantin</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input name="nama_lengkap" type="text" class="form-control" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                        <input name="username" type="text" class="form-control" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input name="password" type="password" class="form-control" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Jurusan</label>
                    <select name="jurusan" class="form-control" required>
                        <option value="">Pilih Jurusan</option>
                        <option value="RPL">RPL</option>
                        <option value="AKL">AKL</option>
                        <option value="MP">MP</option>
                        <option value="Adnor">Adnor</option>
                    </select>
                </div>
                
                <button class="btn btn-register mt-3" type="submit">Daftar</button>
                
                <div class="login-link">
                    Sudah punya akun? <a href="login.php">Login disini</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>