<?php
session_start();
require_once "./php/config.php";
require_once "./php/users.php";

if (!isLoggedIn()) {
    header("Location: login_page.php");
    exit;
}

$userId = $_SESSION['loggedInUser']['id'];
$totalPrice = 0;

$sql = "SELECT
            p.name, p.price, pv.size, ci.quantity
        FROM cart_items ci
        JOIN product_variants pv ON ci.product_variant_id = pv.id
        JOIN products p ON pv.product_id = p.id
        WHERE ci.user_id = ?";

$stmt = $db->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}
$stmt->close();

if (empty($cartItems)) {
    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <title>Dokončení objednávky – ChewForever</title>
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

    <div class="cart-container">
        <h1>Rekapitulace a dokončení objednávky</h1>
        <p>Prosím, zkontrolujte položky ve své objednávce. Kliknutím na tlačítko níže objednávku závazně potvrdíte.</p>

        <table class="cart-table">
            <thead>
                <tr>
                    <th>Produkt</th>
                    <th>Cena za kus</th>
                    <th>Množství</th>
                    <th>Celkem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item) {
                    $subtotal = $item['price'] * $item['quantity'];
                    $totalPrice += $subtotal;
                ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlentities($item['name']); ?></strong><br>
                            <small>Velikost: <?php echo htmlentities($item['size']); ?></small>
                        </td>
                        <td><?php echo number_format($item['price'], 2, ',', ' '); ?> Kč</td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo number_format($subtotal, 2, ',', ' '); ?> Kč</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <h2>Celkem k úhradě: <?php echo number_format($totalPrice, 2, ',', ' '); ?> Kč</h2>
            <form action="checkout_logic.php" method="post">
                <button type="submit" name="confirm_order" class="checkout-button">Závazně objednat</button>
            </form>
        </div>
    </div>
</body>
</html>