<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$jurusan = $_SESSION['user']['jurusan'];
$username = $_SESSION['user']['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = $conn->real_escape_string($_POST['id']);
    
    $conn->query("DELETE FROM transaksi WHERE id = '$id' AND jurusan = '$jurusan'");
    
    if ($conn->affected_rows > 0) {
        $_SESSION['message'] = "Transaksi berhasil dihapus!";
    }
    
    header("Location: laporan.php");
    exit;
}

// Get transactions data
$transactions = $conn->query("SELECT * FROM transaksi WHERE jurusan = '$jurusan' ORDER BY tanggal DESC");
$total = $conn->query("SELECT SUM(total) AS pemasukan FROM transaksi WHERE jurusan = '$jurusan'")
              ->fetch_assoc()['pemasukan'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - <?= htmlspecialchars($jurusan) ?></title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
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
        }
        
        .hero {
            position: relative;
            min-height: 30vh;
            overflow: hidden;
        }
        
        .hero-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            z-index: 2;
        }
        
        .report-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin: 2rem auto;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: var(--primary-color);
            color: white;
        }
        
        .table tbody tr:hover {
            background-color: rgba(4, 21, 98, 0.05);
        }
        
        .summary-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 15px;
            padding: 1.25rem;
            margin: 2rem 0;
        }
        
        .delete-btn {
            background-color: var(--accent-color);
            border: none;
            transition: all 0.3s;
        }
        
        .delete-btn:hover {
            background-color: #b30e0e;
            transform: translateY(-1px);
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                border: 1px solid #ddd;
            }
            
            .table thead {
                display: none;
            }
            
            .table, .table tbody, .table tr, .table td {
                display: block;
                width: 100%;
            }
            
            .table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            
            .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: 45%;
                text-align: left;
                color: var(--primary-color);
                font-weight: 600;
            }
            
            .table td[data-label="Aksi"] {
                text-align: center;
                padding-left: 15px;
            }
            
            .table td[data-label="Aksi"]::before {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <h4 class="m-0">Kantin <?= htmlspecialchars($jurusan) ?></h4>
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2 fs-4"></i>
                        <span><?= htmlspecialchars($username) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i> Keluar
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-background position-absolute w-100 h-100">
            <img src="img/markus-spiske-XrIfY_4cK1w-unsplash.jpg" alt="Laporan Transaksi" class="hero-img w-100 h-100 object-fit-cover" style="filter: blur(8px);">
            <div class="overlay position-absolute w-100 h-100" style="background: rgba(0, 0, 0, 0.5);"></div>
        </div>
        <div class="hero-text text-center">
            <h1 class="fw-bold">Laporan Transaksi <?= htmlspecialchars($jurusan) ?></h1>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-5">
        <div class="container">
            <div class="report-container">
                <h2 class="text-center fw-bold mb-4" style="color: var(--primary-color);">Detail Transaksi</h2>
                
                <div class="summary-card">
                    <div class="summary-title fs-5 fw-semibold">Total Pemasukan</div>
                    <div class="summary-amount fs-2 fw-bold">Rp<?= number_format($total, 0, ',', '.') ?></div>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Makanan</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($transactions->num_rows > 0): ?>
                                <?php $no = 1; while ($row = $transactions->fetch_assoc()): ?>
                                    <tr>
                                        <td data-label="No"><?= $no++ ?></td>
                                        <td data-label="Nama Makanan"><?= htmlspecialchars($row['nama_makanan']) ?></td>
                                        <td data-label="Harga">Rp<?= number_format($row['harga'], 0, ',', '.') ?></td>
                                        <td data-label="Jumlah"><?= $row['jumlah'] ?></td>
                                        <td data-label="Total">Rp<?= number_format($row['total'], 0, ',', '.') ?></td>
                                        <td data-label="Tanggal"><?= $row['tanggal'] ?></td>
                                        <td data-label="Aksi">
                                            <button class="btn btn-danger me-auto delete-transaction" 
                                                    data-id="<?= $row['id'] ?>"
                                                    data-name="<?= htmlspecialchars($row['nama_makanan']) ?>"
                                                    data-amount="Rp<?= number_format($row['total'], 0, ',', '.') ?>">
                                                <i class="fas fa-trash me-1"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">Tidak ada data transaksi</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Hidden Delete Form -->
    <form id="deleteForm" method="POST" class="d-none">
        <input type="hidden" name="delete" value="1">
        <input type="hidden" name="id" id="transactionId">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-transaction').forEach(btn => {
                btn.addEventListener('click', function() {
                    const transactionId = this.dataset.id;
                    const transactionName = this.dataset.name;
                    const transactionAmount = this.dataset.amount;
                    
                    Swal.fire({
                        title: 'Hapus Transaksi?',
                        html: `Anda akan menghapus transaksi:<br><br>
                               <b>${transactionName}</b><br>
                               <b>${transactionAmount}</b><br><br>
                               Data tidak dapat dikembalikan.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('transactionId').value = transactionId;
                            document.getElementById('deleteForm').submit();
                        }
                    });
                });
            });
            
            <?php if(isset($_SESSION['message'])): ?>
                Swal.fire({
                    title: 'Sukses!',
                    text: '<?= $_SESSION['message'] ?>',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                });
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>