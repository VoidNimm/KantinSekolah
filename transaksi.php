<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$jurusan = $_SESSION['user']['jurusan'];
$username = $_SESSION['user']['username'];
$success = '';
$error = '';

// Process order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['items']) && !empty($_POST['items'])) {
        $items = json_decode($_POST['items'], true);
        $successCount = 0;
        
        if (is_array($items) && count($items) > 0) {
            foreach ($items as $item) {
                // Pastikan key 'id_menu' dan 'jumlah' ada
                if (isset($item['id_menu']) && isset($item['jumlah'])) {
                    $id_menu = (int)$item['id_menu'];
                    $jumlah = (int)$item['jumlah'];
                    
                    if ($id_menu > 0 && $jumlah > 0) {
                        // Gunakan prepared statement untuk query SELECT juga
                        $stmt = $conn->prepare("SELECT * FROM menu WHERE id = ? AND jurusan = ?");
                        $stmt->bind_param("is", $id_menu, $jurusan);
                        $stmt->execute();
                        $menu = $stmt->get_result()->fetch_assoc();
                        
                        if ($menu) {
                            $nama_makanan = $menu['nama_makanan'];
                            $harga = $menu['harga'];
                            $total = $harga * $jumlah;
                            
                            $stmt = $conn->prepare("INSERT INTO transaksi (jurusan, nama_makanan, harga, jumlah, total) 
                                                  VALUES (?, ?, ?, ?, ?)");
                            $stmt->bind_param("ssiii", $jurusan, $nama_makanan, $harga, $jumlah, $total);
                            
                            if ($stmt->execute()) {
                                $successCount++;
                            }
                        }
                    }
                }
            }
            
            if ($successCount > 0) {
                $success = "Berhasil memproses $successCount pesanan!";
                // Reset selected items setelah berhasil
                echo '<script>selectedItems = []; updateSelectedItemsList(); updateTotalPrice();</script>';
            } else {
                $error = "Tidak ada pesanan yang valid.";
            }
        } else {
            $error = "Data pesanan tidak valid.";
        }
    } else {
        $error = "Silakan pilih menu terlebih dahulu.";
    }
}
// Get menu items
$menus = $conn->query("SELECT * FROM menu WHERE jurusan = '$jurusan'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Makanan - <?= $jurusan ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-color: #041562;
            --primary-color: #11468F;
            --accent-color: #DA1212;
            --light-color: #EEEEEE;
            --secondary-color: #28a745;
        }
        
        body {
            background-color: var(--light-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-color);
        }
        
        /* Hero Section */
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
        
        /* Menu Slider */
        .menu-slider {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            gap: 20px;
            padding: 20px 0;
            margin: 30px 0;
            -webkit-overflow-scrolling: touch;
        }
        
        .menu-slider::-webkit-scrollbar {
            height: 8px;
        }
        
        .menu-slider::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }
        
        .menu-card {
            scroll-snap-align: start;
            flex: 0 0 300px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .menu-card.selected {
            border-color: var(--primary-color);
        }
        
        .menu-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .menu-body {
            padding: 20px;
        }
        
        .menu-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .menu-price {
            font-weight: 700;
            color: var(--accent-color);
            margin: 10px 0;
        }
        
        .menu-desc {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Transaction Form */
        .transaction-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        
        .transaction-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .form-control {
            height: 50px;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding-left: 15px;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(142, 22, 22, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            height: 50px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .input-group-text {
            background-color: white;
            border-right: none;
        }
        
        .input-group .form-control {
            border-left: none;
        }
        
        /* Selected Items Section */
        .selected-items {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-top: 3px solid var(--primary-color);
        }
        
        .selected-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .selected-item:last-child {
            border-bottom: none;
        }
        
        .item-quantity {
            width: 60px;
            text-align: center;
        }
        
        .remove-item {
            color: var(--accent-color);
            cursor: pointer;
            font-size: 1.2rem;
        }
        
        /* Navigation arrows */
        .slider-nav {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }
        
        .slider-arrow {
            background: var(--primary-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .slider-arrow:hover {
            background: var(--accent-color);
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <h4 class="m-0">Kantin <?= htmlspecialchars($jurusan) ?></h4>
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
            <div class="transaction-container">
                <h2 class="transaction-title">Pilih Menu Makanan</h2>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php elseif ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <!-- Menu Slider -->
                <div class="menu-slider" id="menuSlider">
                    <?php while ($row = $menus->fetch_assoc()): ?>
                        <div class="menu-card" 
                             data-id="<?= $row['id'] ?>" 
                             data-nama="<?= htmlspecialchars($row['nama_makanan']) ?>"
                             data-harga="<?= $row['harga'] ?>">
                            <img src="<?= $row['gambar'] ?? 'img/default-food.jpg' ?>" class="menu-img" alt="<?= htmlspecialchars($row['nama_makanan']) ?>">
                            <div class="menu-body">
                                <h4 class="menu-title"><?= htmlspecialchars($row['nama_makanan']) ?></h4>
                                <div class="menu-price">Rp<?= number_format($row['harga'], 0, ',', '.') ?></div>
                                <p class="menu-desc"><?= htmlspecialchars($row['deskripsi'] ?? 'Menu kantin '.$jurusan) ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <!-- Slider Navigation -->
                <div class="slider-nav">
                    <div class="slider-arrow" id="slideLeft"><i class="fas fa-chevron-left"></i></div>
                    <div class="slider-arrow" id="slideRight"><i class="fas fa-chevron-right"></i></div>
                </div>
                
                <!-- Selected Items Section -->
                <div class="selected-items" id="selectedItems">
                    <h5 class="text-center mb-3" style="color: var(--primary-color);">Menu Dipilih</h5>
                    <div id="selectedItemsList">
                        <p class="text-center text-muted">Belum ada menu dipilih</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <h6 class="mb-0">Total Harga:</h6>
                        <h5 class="mb-0" id="totalPriceDisplay" style="color: var(--accent-color);">Rp0</h5>
                    </div>
                </div>
                
                <!-- Order Form -->
                <form method="post" id="orderForm" class="mt-4">
                    <input type="hidden" name="items" id="orderItems">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-cash-register me-2"></i> Proses Pesanan
                    </button>
                </form>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Store selected items
            let selectedItems = [];
            
            // Menu selection
            $('.menu-card').click(function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const harga = $(this).data('harga');
                
                // Toggle selection
                $(this).toggleClass('selected');
                
                // Check if item already selected
                const existingItem = selectedItems.find(item => item.id === id);
                
                if ($(this).hasClass('selected')) {
                    if (!existingItem) {
                        selectedItems.push({
                            id: id,
                            nama: nama,
                            harga: harga,
                            jumlah: 1
                        });
                    }
                } else {
                    if (existingItem) {
                        selectedItems = selectedItems.filter(item => item.id !== id);
                    }
                }
                
                updateSelectedItemsList();
                updateTotalPrice();
            });
            
            // Update selected items list
            function updateSelectedItemsList() {
                const $list = $('#selectedItemsList');
                
                if (selectedItems.length === 0) {
                    $list.html('<p class="text-center text-muted">Belum ada menu dipilih</p>');
                    return;
                }
                
                let html = '';
                
                selectedItems.forEach(item => {
                    html += `
                        <div class="selected-item" data-id="${item.id}">
                            <div>
                                <h6 class="mb-0">${item.nama}</h6>
                                <small class="text-muted">Rp${item.harga.toLocaleString('id-ID')}</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-outline-secondary decrement" data-id="${item.id}">-</button>
                                <input type="number" class="form-control form-control-sm item-quantity" 
                                       value="${item.jumlah}" min="1" data-id="${item.id}">
                                <button class="btn btn-sm btn-outline-secondary increment" data-id="${item.id}">+</button>
                                <i class="fas fa-times remove-item" data-id="${item.id}"></i>
                            </div>
                        </div>
                    `;
                });
                
                $list.html(html);
                
                // Add event listeners for quantity changes
                $('.decrement').click(function(e) {
                    e.stopPropagation();
                    const id = $(this).data('id');
                    const item = selectedItems.find(item => item.id === id);
                    if (item && item.jumlah > 1) {
                        item.jumlah--;
                        updateSelectedItemsList();
                        updateTotalPrice();
                    }
                });
                
                $('.increment').click(function(e) {
                    e.stopPropagation();
                    const id = $(this).data('id');
                    const item = selectedItems.find(item => item.id === id);
                    if (item) {
                        item.jumlah++;
                        updateSelectedItemsList();
                        updateTotalPrice();
                    }
                });
                
                $('.item-quantity').on('input', function() {
                    const id = $(this).data('id');
                    const value = parseInt($(this).val()) || 1;
                    const item = selectedItems.find(item => item.id === id);
                    if (item) {
                        item.jumlah = value;
                        updateTotalPrice();
                    }
                });
                
                $('.remove-item').click(function(e) {
                    e.stopPropagation();
                    const id = $(this).data('id');
                    selectedItems = selectedItems.filter(item => item.id !== id);
                    $(`.menu-card[data-id="${id}"]`).removeClass('selected');
                    updateSelectedItemsList();
                    updateTotalPrice();
                });
            }
            
            // Update total price
            function updateTotalPrice() {
                let total = 0;
                
                selectedItems.forEach(item => {
                    total += item.harga * item.jumlah;
                });
                
                $('#totalPriceDisplay').text('Rp' + total.toLocaleString('id-ID'));
            }
            
            // Prepare form submission
$('#orderForm').submit(function(e) {
    if (selectedItems.length === 0) {
        e.preventDefault();
        alert('Silakan pilih minimal 1 menu!');
        return;
    }
    
    // Convert selectedItems to format yang bisa diproses PHP
    let itemsData = selectedItems.map(item => ({
        'id_menu': item.id,
        'jumlah': item.jumlah
    }));
    
    // Debug: lihat data yang akan dikirim
    console.log('Data yang dikirim:', itemsData);
    
    // Simpan data dalam format yang benar
    $('#orderItems').val(JSON.stringify(itemsData));
    
    // Lanjutkan pengiriman form
    return true;
});
            
            // Slider navigation
            $('#slideLeft').click(function() {
                $('#menuSlider').animate({scrollLeft: '-=300'}, 300);
            });
            
            $('#slideRight').click(function() {
                $('#menuSlider').animate({scrollLeft: '+=300'}, 300);
            });
        });
    </script>
</body>
</html>