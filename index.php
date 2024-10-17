<?php
include './function.php';
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dinamik Menü Örneği</title>
    <link rel='stylesheet' href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css'>
    <link rel='stylesheet' href="./header.css">
</head>
<body>
<nav>
  <div class="navbar">
    <i class='bx bx-menu'></i>
    <div class="logo"><a href="#">Logo</a></div>
    <div class="nav-links">
      <div class="sidebar-logo">
        <span class="logo-name">Logo</span>
        <i class='bx bx-x' ></i>
      </div>
            <?php 
            try {
                // Veritabanı bağlantısı kontrolü
                $pdo = getDatabaseConnection(); 
                if ($pdo) {
                    // Eğer bağlantı başarılıysa menüyü oluştur
                    createDynamicMenu($pdo);
                } else {
                    echo "Veritabanına bağlanılamadı.";
                }
            } catch (Exception $e) {
                // Eğer bir hata oluşursa mesaj göster
                echo "Bağlantı hatası: " . $e->getMessage();
            }
            ?>
        </div>
        <div class="search-box">
            <i class='bx bx-search'></i>
            <div class="input-box">
                <input type="text" placeholder="Search...">
            </div>
        </div>
    </div>
</nav>
<script src="./header.js"></script>
</body>
</html>
