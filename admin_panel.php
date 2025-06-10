<?php
require_once "./php/config.php";
require_once "./php/users.php";

if(session_status()=== PHP_SESSION_NONE){
    session_start();
}

if(!isLoggedIn()|| $_SESSION["loggedInUser"]["role"] !== "admin"){
    header("Location: index.php");
    exit;
}

//POC REGIS
$stmt = $db->query("SELECT COUNT(*)AS count FROM users");
$row = $stmt->fetch_assoc();
$userCount = $row["count"] ?? 0;
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
        <a href="delete_products.php">Správa produktů</a>
        <a href="add_product.php">Přidat nový produkt</a>
        <a href="manage_categories.php">Správa kategorií</a>
        <a href="manage_users.php">Správa uživatelů</a>
        <a href="#">Správa objednávek</a>
        <a href="index.php">Zpět na web</a>
        <a href="logout.php">Odhlásit se</a>
    </div>
    <div class="admin-main">
        <div class="admin-header">
            <h1>Admin Panel</h1>
        </div>

        <div class="admin-metric">
            <h3>Registrovaných účtů</h3>
            <p><?php echo $userCount; ?></p>
        </div>
    </div>
</div>
</body>
</html>
