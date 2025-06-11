<?php
session_start();
require_once "./php/config.php";
require_once "./php/users.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$productId = (int)$_GET['id'];

$stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<p style='color: red; text-align: center;'>Produkt nebyl nalezen.</p>";
    exit;
}

$stmt_variants = $db->prepare("SELECT id, size, stock FROM product_variants WHERE product_id = ? ORDER BY size");
$stmt_variants->bind_param("i", $productId);
$stmt_variants->execute();
$variants_result = $stmt_variants->get_result();
$variants = [];
while ($row = $variants_result->fetch_assoc()) {
    $variants[] = $row;
}

$stmt->close();
$stmt_variants->close();
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlentities($product['name']); ?> – ChewForever</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_product_detail.css">
    <link rel="icon" type="image/png" href="pics/icon.png">

    <link rel="stylesheet" href="style/style_product_detail.css">
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

    <div class="container product-detail">
        <div class="left">
            <img src="/ch/<?php echo htmlentities($product['image_url']); ?>" alt="<?php echo htmlentities($product['name']); ?>" class="product-image">
        </div>
        <div class="right">
            <h1 class="product-title"><?php echo htmlentities($product['name']); ?></h1>
            <p class="product-price"><?php echo number_format($product['price'], 2, ',', ' '); ?> Kč</p>

            <form action="cart_logic.php" method="post">
                <div class="size-selector">
                    <p>Velikost:</p>
                    <?php foreach ($variants as $variant) { ?>
                        <div class="size-option">
                            <input type="radio" name="variant_id" value="<?php echo $variant['id']; ?>" id="size_<?php echo $variant['id']; ?>" <?php echo $variant['stock'] == 0 ? 'disabled' : ''; ?>>
                            <label for="size_<?php echo $variant['id']; ?>" class="<?php echo $variant['stock'] == 0 ? 'out-of-stock' : ''; ?>">
                                <?php echo htmlentities($variant['size']); ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
                <input type="hidden" name="action" value="add">
                <button type="submit" class="add-to-cart-btn">Do košíku</button>
            </form>

            <div class="product-description">
                <p><?php echo nl2br(htmlentities($product['description'])); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
