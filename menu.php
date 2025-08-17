<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$jurusan = $_SESSION['user']['jurusan'];
$username = $_SESSION['user']['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $nama = $conn->real_escape_string($_POST['nama_makanan']);
        $harga = $conn->real_escape_string($_POST['harga']);
        $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
        
        $gambar = '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExt = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetPath)) {
                $gambar = $targetPath;
            }
        }
        
        $conn->query("INSERT INTO menu (nama_makanan, harga, deskripsi, jurusan, gambar) 
                     VALUES ('$nama', '$harga', '$deskripsi', '$jurusan', '$gambar')");
    } 
    elseif (isset($_POST['update'])) {
        $id = $conn->real_escape_string($_POST['id']);
        $nama = $conn->real_escape_string($_POST['nama_makanan']);
        $harga = $conn->real_escape_string($_POST['harga']);
        $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
        
        $gambar = '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExt = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetPath)) {
                $gambar = $targetPath;
                $oldImage = $conn->query("SELECT gambar FROM menu WHERE id = '$id'")->fetch_assoc()['gambar'];
                if ($oldImage && file_exists($oldImage)) {
                    unlink($oldImage);
                }
                $conn->query("UPDATE menu SET gambar = '$gambar' WHERE id = '$id'");
            }
        }
        
        $conn->query("UPDATE menu SET 
                     nama_makanan = '$nama', 
                     harga = '$harga', 
                     deskripsi = '$deskripsi' 
                     WHERE id = '$id' AND jurusan = '$jurusan'");
    } 
    elseif (isset($_POST['delete'])) {
        $id = $conn->real_escape_string($_POST['id']);
        $image = $conn->query("SELECT gambar FROM menu WHERE id = '$id'")->fetch_assoc()['gambar'];
        if ($image && file_exists($image)) {
            unlink($image);
        }
        $conn->query("DELETE FROM menu WHERE id = '$id' AND jurusan = '$jurusan'");
    }
    
    header("Location: menu.php");
    exit;
}

$searchQuery = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$query = $conn->query("SELECT * FROM menu WHERE jurusan = '$jurusan' 
                       AND nama_makanan LIKE '%$searchQuery%'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Makanan - <?= $jurusan ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    
    /* Hero Section */
    .hero {
        position: relative;
        width: 100%;
        min-height: 30vh;
        margin-top: 0px;
        overflow: hidden;
    }

    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .hero-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: blur(5px);
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }

    .hero-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        z-index: 2;
    }

    .hero-text h1 {
        font-weight: bold;
        color: white;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        position: relative;
        overflow: hidden;
        margin-bottom: 20px;
        height: 100%;
        background-color: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .card-img-top {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }

    .price-tag {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: var(--primary-color);
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
    }

    .menu-title {
        text-align: center;
        margin: 30px 0;
        color: var(--primary-color);
        font-weight: 700;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
    }

    .action-buttons .btn {
        flex: 1;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-primary:hover {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }
    
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
    }
    
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .btn-danger {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
    }

    /* Search */
    .search-create-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 30px auto;
        max-width: 100%;
        gap: 20px;
    }
    
    .search-box {
        flex-grow: 1;
    }
    
    .create-btn-container {
        flex-shrink: 0;
    }
    
    .navbar {
        background-color: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .navbar-brand h4 {
        color: var(--primary-color) !important;
    }
    
    .modal-content {
        border-radius: 15px;
        border: none;
    }

    .modal-header {
        border-bottom: none;
        padding-bottom: 0;
        background-color: var(--light-color);
    }
    
    .modal-title {
        color: var(--primary-color);
        font-weight: 600;
    }

    .modal-footer {
        border-top: none;
        background-color: var(--light-color);
    }
    
    .image-preview {
        max-width: 100%;
        height: 150px;
        object-fit: cover;
        margin-top: 10px;
        display: none;
        border-radius: 5px;
        border: 1px solid #ddd;
    }
    
    .upload-btn {
        position: relative;
        overflow: hidden;
        display: inline-block;
        background-color: white;
        border: 1px solid #ddd;
    }
    
    .upload-btn:hover {
        background-color: var(--light-color);
    }
    
    .upload-btn input[type="file"] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }
    
    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
</style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
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
            <img src="img/image.png" alt="Dashboard Kantin" class="hero-img w-100 h-100 object-fit-cover" style="filter: blur(8px);">
            <div class="overlay position-absolute w-100 h-100" style="background: rgba(0, 0, 0, 0.5);"></div>
        </div>
        <div class="hero-text text-center">
            <h1 class="fw-bold">Dashboard Kantin <?= htmlspecialchars($jurusan) ?></h1>
        </div>
    </section>
    
    <!-- Main Content -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="search-box">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" 
                               placeholder="Cari menu..." value="<?= htmlspecialchars($searchQuery) ?>">
                        <?php if(!empty($searchQuery)): ?>
                            <button id="resetSearch" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Create Button -->
                <div class="create-btn-container">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="fas fa-plus me-2"></i>Tambah Menu
                    </button>
                </div>
            </div>

            <h2 class="menu-title">Daftar Menu <?= $jurusan ?></h2>

             <!-- Menu Cards -->
            <div class="row" id="menuContainer">
                <?php if ($query->num_rows > 0): ?>
                    <?php while ($row = $query->fetch_assoc()): ?>
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="card shadow-sm">
                                <img src="<?= $row['gambar'] ?? 'img/default-food.jpg' ?>" 
                                     class="card-img-top" alt="<?= htmlspecialchars($row['nama_makanan']) ?>">
                                <div class="price-tag">Rp<?= number_format($row['harga'], 0, ',', '.') ?></div>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($row['nama_makanan']) ?></h5>
                                    <p class="card-text text-muted"><?= htmlspecialchars($row['deskripsi'] ?? 'Menu kantin '.$jurusan) ?></p>
                                    <div class="action-buttons">
                                        <button class="btn btn-warning edit-btn" 
                                                data-id="<?= $row['id'] ?>"
                                                data-nama="<?= htmlspecialchars($row['nama_makanan']) ?>"
                                                data-harga="<?= $row['harga'] ?>"
                                                data-deskripsi="<?= htmlspecialchars($row['deskripsi']) ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <?= empty($searchQuery) ? 'Belum ada menu tersedia' : 'Menu tidak ditemukan' ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        </div>
    </section>

    <!-- Tambah Menu Baru -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Menu Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_makanan" class="form-label">Nama Makanan</label>
                            <input type="text" class="form-control" id="nama_makanan" name="nama_makanan" required>
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar Makanan</label>
                            <div class="upload-btn btn btn-outline-secondary w-100">
                                <i class="fas fa-upload me-2"></i>Pilih Gambar
                                <input type="file" id="gambarInput" name="gambar" accept="image/*">
                            </div>
                            <img id="imagePreview" class="image-preview" alt="Preview Gambar">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="create" class="btn btn-success">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Menu -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editNama" class="form-label">Nama Makanan</label>
                            <input type="text" class="form-control" id="editNama" name="nama_makanan" required>
                        </div>
                        <div class="mb-3">
                            <label for="editHarga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="editHarga" name="harga" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDeskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar Makanan</label>
                            <div class="upload-btn btn btn-outline-secondary w-100">
                                <i class="fas fa-upload me-2"></i>Ubah Gambar
                                <input type="file" id="editGambarInput" name="gambar" accept="image/*">
                            </div>
                            <img id="editImagePreview" class="image-preview" alt="Preview Gambar">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- Ganti button delete dengan ini -->
<button type="button" class="btn btn-danger me-auto" id="deleteButton">
    <i class="fas fa-trash me-1"></i> Hapus
</button>

<!-- Tambahkan hidden button untuk submit delete -->
<button type="submit" name="delete" id="realDeleteButton" style="display: none;"></button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // SweetAlert for delete button
document.getElementById('deleteButton').addEventListener('click', function(e) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Trigger click pada hidden delete button
            document.getElementById('realDeleteButton').click();
        }
    });
});

// Pastikan fungsi edit tetap berjalan
$('.edit-btn').click(function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    const harga = $(this).data('harga');
    const deskripsi = $(this).data('deskripsi');
    const gambar = $(this).closest('.card').find('.card-img-top').attr('src');
    
    $('#editId').val(id);
    $('#editNama').val(nama);
    $('#editHarga').val(harga);
    $('#editDeskripsi').val(deskripsi);
    
    if (gambar && !gambar.includes('default-food.jpg')) {
        $('#editImagePreview').attr('src', gambar).show();
    } else {
        $('#editImagePreview').hide();
    }
    
    $('#editModal').modal('show');
});

        // Automatic search
        $(document).ready(function() {
            $('#searchInput').on('input', function() {
                const searchQuery = $(this).val();
                if (searchQuery.length > 0) {
                    window.location.href = `menu.php?search=${encodeURIComponent(searchQuery)}`;
                } else {
                    window.location.href = 'menu.php';
                }
            });

            // Reset search
            $('#resetSearch').click(function() {
                window.location.href = 'menu.php';
            });
            
            $('#gambarInput').change(function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#imagePreview').attr('src', event.target.result).show();
                    }
                    reader.readAsDataURL(file);
                }
            });
            
            $('#editGambarInput').change(function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#editImagePreview').attr('src', event.target.result).show();
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</body>
</html>