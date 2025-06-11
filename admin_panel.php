<?php
require_once "./php/config.php";
require_once "./php/users.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn() || $_SESSION["loggedInUser"]["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

//celkem uziv
$stmt = $db->query("SELECT COUNT(*) AS count FROM users");
$userCount = ($stmt->fetch_assoc())["count"] ?? 0;

//poc admin
$stmt = $db->query("SELECT COUNT(*) AS count FROM users WHERE role = 'admin'");
$adminCount = ($stmt->fetch_assoc())["count"] ?? 0;

//pocet frantu uzivatelu
$stmt = $db->query("SELECT COUNT(*) AS count FROM users WHERE role = 'user'");
$userRoleCount = ($stmt->fetch_assoc())["count"] ?? 0;

//poc obj
$stmt = $db->query("SELECT COUNT(*) AS count FROM orders");
$orderCount = ($stmt->fetch_assoc())["count"] ?? 0;

//celkem
$stmt = $db->query("SELECT SUM(price_at_purchase * quantity) AS total FROM order_items");
$totalRevenue = number_format((float)($stmt->fetch_assoc())["total"] ?? 0, 2, ',', ' ');

//mesic
$currentMonth = date('Y-m');
$stmt = $db->query("
    SELECT SUM(price_at_purchase * quantity) AS month_total 
    FROM order_items 
    JOIN orders ON orders.id = order_items.order_id 
    WHERE DATE_FORMAT(orders.created_at, '%Y-%m') = '$currentMonth'
");
$monthlyRevenue = number_format((float)($stmt->fetch_assoc())["month_total"] ?? 0, 2, ',', ' ');
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel – ChewForever</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_admin_panel.css">
    <link rel="icon" type="image/png" href="pics/icon.png">
</head>
<body>
<div class="admin-container">
    <div class="admin-sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_panel.php">Admin Panel</a>
        <a href="manage_products.php">Správa produktů</a>
        <a href="add_product.php">Přidat nový produkt</a>
        <a href="manage_categories.php">Správa kategorií</a>
        <a href="manage_users.php">Správa uživatelů</a>
        <a href="#">Správa objednávek</a>
        <a href="index.php">Zpět na web</a>
        <a href="logout.php">Odhlásit se</a>
    </div>
    <div class="admin-main">
        <div class="admin-header">
            <h1>Přehled</h1>
        </div>

        <div class="admin-metric"><h3>Registrovaných účtů</h3><p><?php echo $userCount; ?></p></div>
        <div class="admin-metric"><h3>Admin účtů</h3><p><?php echo $adminCount; ?></p></div>
        <div class="admin-metric"><h3>Běžní uživatelé</h3><p><?php echo $userRoleCount; ?></p></div>
        <div class="admin-metric"><h3>Počet objednávek</h3><p><?php echo $orderCount; ?></p></div>
        <div class="admin-metric"><h3>Výdělek za měsíc</h3><p><?php echo $monthlyRevenue; ?> Kč</p></div>
        <div class="admin-metric"><h3>Výdělek celkem</h3><p><?php echo $totalRevenue; ?> Kč</p></div>
    </div>
</div>
</body>
</html>
