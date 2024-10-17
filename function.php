<?php

function getDatabaseConnection() {
    $host = 'localhost'; // Veritabanı sunucu adresi
    $db = 'dynamic-responsive-header'; // Veritabanı adı
    $user = 'root'; // Kullanıcı adı
    $pass = ''; // Şifre

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        return null;
    }
}

function createDynamicMenu($pdo) {
    // Ana menü öğelerini veritabanından çek
    $sql = "SELECT * FROM menu_items WHERE parent_id IS NULL"; // Ana menü öğeleri
    $stmt = $pdo->query($sql);

    if ($stmt->rowCount() > 0) {
        echo '<ul class="links">'; // Ana menü için "links" sınıfı

        // Ana menü öğelerini listele
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $menuName = htmlspecialchars($row['title']);
            $menuUrl = htmlspecialchars($row['link']);
            $menuId = (int)$row['id'];

            // Alt menü var mı kontrolü
            $subMenuSql = "SELECT * FROM menu_items WHERE parent_id = :menuId";
            $subStmt = $pdo->prepare($subMenuSql);
            $subStmt->bindParam(':menuId', $menuId, PDO::PARAM_INT);
            $subStmt->execute();

            if ($subStmt->rowCount() > 0) {
                // Eğer alt menü varsa
                echo "<li>
                        <a href='$menuUrl'>$menuName</a>
                        <i class='bx bxs-chevron-down htmlcss-arrow arrow '></i>"; // Ana menü ikonu
                echo '<ul class="htmlCss-sub-menu sub-menu">'; // Alt menü için "sub-menu" sınıfı

                // Alt menüleri listele
                while ($subRow = $subStmt->fetch(PDO::FETCH_ASSOC)) {
                    $subMenuName = htmlspecialchars($subRow['title']);
                    $subMenuUrl = htmlspecialchars($subRow['link']);
                    $subMenuId = (int)$subRow['id'];
                    $isMore = (int)$subRow['is_more']; // is_more sütunu

                    if ($isMore === 1) {
                        // is_more 1 ise, span kullanarak yazdır ve ok ikonunu ekle
                        echo "<li class='more'>
                                <span><a >$subMenuName</a>
                                <i class='bx bxs-chevron-right arrow more-arrow'></i></span>";

                        // Alt menünün alt menülerini al
                        $moreSubMenuSql = "SELECT * FROM menu_items WHERE parent_id = :subMenuId";
                        $moreSubStmt = $pdo->prepare($moreSubMenuSql);
                        $moreSubStmt->bindParam(':subMenuId', $subMenuId, PDO::PARAM_INT);
                        $moreSubStmt->execute();

                        if ($moreSubStmt->rowCount() > 0) {
                            echo '<ul class="more-sub-menu sub-menu">'; // Daha fazla alt menü için

                            while ($moreRow = $moreSubStmt->fetch(PDO::FETCH_ASSOC)) {
                                $moreMenuName = htmlspecialchars($moreRow['title']);
                                $moreMenuUrl = htmlspecialchars($moreRow['link']);
                                echo "<li><a href='$moreMenuUrl'>$moreMenuName</a></li>";
                            }

                            echo '</ul>'; // "more-sub-menu" kapanış
                        }

                        echo '</li>'; // li kapanış
                    } else {
                        // is_more 1 değilse, li kullanarak alt menüleri yazdır
                        echo "<li><a href='$subMenuUrl'>$subMenuName</a></li>";
                    }
                }

                echo '</ul></li>'; // Alt menü kapanış
            } else {
                // Alt menü yoksa, normal menü öğesi
                echo "<li><a href='$menuUrl'>$menuName</a></li>";
            }
        }

        echo '</ul>'; // Ana menü kapanış
    } else {
        echo "Menü bulunamadı."; // Menü öğesi yoksa
    }
}
?>