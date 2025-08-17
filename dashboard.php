<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$jurusan = $user['jurusan'];
$username = $user['username'];

$total_pemasukan = $conn->query("SELECT SUM(total) AS total FROM transaksi WHERE jurusan = '$jurusan'")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kantin - <?= htmlspecialchars($jurusan) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #041562;
            --secondary-color: #11468F;
            --accent-color: #DA1212;
            --light-color: #EEEEEE;
            --success-color: #28a745;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .hero {
            position: relative;
            min-height: 30vh;
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .hero-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            z-index: 2;
        }
        
        .stats-row {
            margin: 30px 0;
        }
        
        .stat-card {
            border-radius: 15px;
            padding: 25px;
            color: white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .income-card {
            background: linear-gradient(135deg, var(--success-color) 0%, #5cb85c 100%);
        }
        
        .admin-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }
        
        .stat-title {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            margin-bottom: 15px;
        }
        
        .admin-info {
            font-size: 1rem;
            line-height: 1.6;
            background: rgba(255,255,255,0.15);
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }
        
        .dashboard-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: 100%;
            margin-bottom: 25px;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .dashboard-card .card-body {
            padding: 30px;
            text-align: center;
        }
        
        .card-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .card-text {
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        .btn-dashboard {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-dashboard:hover {
            background-color: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .menu-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .transaksi-card {
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
        }
        
        .laporan-card {
            background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);
        }
        
        .welcome-section {
            background-color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .welcome-text {
            font-size: 1.2rem;
            color: #495057;
        }
        
        .username-highlight {
            color: var(--primary-color);
            font-weight: 600;
        }

        footer {
            border-top: 1px solid rgba(0,0,0,0.1);
            background-color: white !important;
        }

        footer a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s;
        }

        footer a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <h4 class="m-0 text-primary">Kantin <?= htmlspecialchars($jurusan) ?></h4>
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-2 fs-4"></i>
                            <span><?= htmlspecialchars($username) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Keluar</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-background position-absolute w-100 h-100">
            <img src="img/gor-davtyan-0BIvjaQwvQQ-unsplash.jpg" alt="Dashboard Kantin" class="hero-img w-100 h-100 object-fit-cover" style="filter: blur(8px);">
            <div class="overlay position-absolute w-100 h-100" style="background: rgba(0, 0, 0, 0.5);"></div>
        </div>
        <div class="hero-text text-center">
            <h1 class="fw-bold">Dashboard Kantin <?= htmlspecialchars($jurusan) ?></h1>
        </div>
    </section>

    <!-- Dashboard Content -->
    <div class="container py-4">
        <div class="welcome-section">
            <h3 class="dashboard-title">Selamat Datang, <span class="username-highlight"><?= htmlspecialchars($username) ?></span></h3>
            <p class="welcome-text">Anda login sebagai pengelola kantin <?= htmlspecialchars($jurusan) ?>. Silakan pilih menu di bawah untuk memulai.</p>
        </div>
        
        <div class="row g-4">
            <!-- Menu Card -->
            <div class="col-md-4">
                <div class="dashboard-card menu-card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3 class="card-title">Menu</h3>
                        <p class="card-text">Kelola daftar menu di kantin</p>
                        <a href="menu.php" class="btn btn-dashboard">Buka Menu</a>
                    </div>
                </div>
            </div>
            
            <!-- Transaksi Card -->
            <div class="col-md-4">
                <div class="dashboard-card transaksi-card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <h3 class="card-title">Transaksi</h3>
                        <p class="card-text">Kelola transaksi pembelian di kantin</p>
                        <a href="transaksi.php" class="btn btn-dashboard">Buka Transaksi</a>
                    </div>
                </div>
            </div>
            
            <!-- Laporan Card -->
            <div class="col-md-4">
                <div class="dashboard-card laporan-card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3 class="card-title">Laporan</h3>
                        <p class="card-text">Lihat laporan penjualan dan statistik</p>
                        <a href="laporan.php" class="btn btn-dashboard">Buka Laporan</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row stats-row g-4">
            <!-- Total Pemasukan Card -->
            <div class="col-md-6">
                <div class="stat-card income-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-title">Total Pemasukan</div>
                            <div class="stat-value">Rp<?= number_format($total_pemasukan, 0, ',', '.') ?></div>
                            <div class="stat-trend">
                                <i class="fas fa-sync-alt me-2"></i>Terhubung dengan laporan transaksi
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Admin Status Card -->
            <div class="col-md-6">
                <div class="stat-card admin-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-title">Status Admin</div>
                            <div class="stat-value">Kantin <?= htmlspecialchars($jurusan) ?></div>
                            <div class="admin-info">
                                <i class="fas fa-user-shield me-2"></i>
                                Anda sebagai admin kantin <?= htmlspecialchars($jurusan) ?> dengan akses penuh
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-user-cog"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-muted">
                    &copy; <?= date('Y') ?> Kantin <?= htmlspecialchars($jurusan) ?>. All rights reserved.
                </p>
            </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-muted">
                        Developed by <?= htmlspecialchars($username) ?>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>