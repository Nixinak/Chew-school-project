<?php
session_start();
require_once "./php/config.php";
require_once "./php/users.php";

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Objednávka dokončena – ChewForever</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_cart.css">
    <link rel="icon" type="image/png" href="pics/icon.png">
    <style>
            body {
    font-family: sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    color: white;
    background-color: #000;
}

.header {
    background-color: #000;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 30px 40px;
    position: relative;
    border-bottom: 1px white solid
}

.header-left,
.header-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.header-center {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

.center-logo {
    max-width: 100px;
    max-height: 100px;
    filter: invert(100%);
}

.nav-link {
    position: relative;
    color: white;
    text-decoration: none;
    font-size: 18px;
    padding: 5px 0;
}

.nav-link::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: 0;
    height: 2px;
    width: 0%;
    background-color: white;
    transition: width 0.3s ease-in-out;
}

.nav-link:hover::after {
    width: 100%;
}

.nav-link:visited {
    color: white;
}

.icon {
    max-width: 26px;
    max-height: 26px;
    filter: invert(100%);
    cursor: pointer;
    vertical-align: middle;
}
    </style>
</head>
<body>
<div class="header">
    <div class="header-left">
        <a href="index.php" class="nav-link">HOME</a>
        <a href="products.php" class="nav-link">PRODUCTS</a>
    </div>

    <div class="header-center">
        <a href="index.php">
            <img src="pics/icon.png" alt="logo" class="center-logo">
        </a>
    </div>

    <div class="header-right">
        <?php if (isLoggedIn()) {
            $isAdmin = isset($_SESSION["loggedInUser"]["role"]) && $_SESSION["loggedInUser"]["role"] === "admin";
        ?>
            <?php if ($isAdmin) { ?>
                <a href="admin_panel.php" title="Admin Panel">
                    <img src="pics/terminal.svg" alt="Admin Panel" class="icon">
                </a>
            <?php } ?>
            <a href="cart.php" title="Košík">
                <img src="pics/shopping-cart.svg" alt="Košík" class="icon">
            </a>
            <a href="logout.php" class="nav-link">ODHLÁSIT SE</a>
        <?php } else { ?>
            <a href="login_page.php" class="nav-link">PŘIHLÁSIT SE</a>
            <a href="register_page.php" class="nav-link">REGISTRACE</a>
        <?php } ?>
    </div>
</div>

    <div class="cart-container" style="text-align: center;">
        <h1>Děkujeme za vaši objednávku!</h1>
        <p>Vaše objednávka byla úspěšně přijata a je zpracovávána.</p>
        <?php if ($orderId > 0) { ?>
            <p>Číslo vaší objednávky je: <strong><?php echo $orderId; ?></strong></p>
        <?php } ?>
        <a href="products.php" class="checkout-button" style="text-decoration: none; display: inline-block;">Zpět na produkty</a>
    </div>
</body>
</html>