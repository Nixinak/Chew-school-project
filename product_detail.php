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
</head>
<body>
    <div class="header">
        <a href="index.php">
            <img src="pics/icon.png" alt="logo">
        </a>
        <div class="auth-controls">
            <?php if (isLoggedIn()) {
                $isAdmin = isset($_SESSION["loggedInUser"]["role"]) && $_SESSION["loggedInUser"]["role"] === "admin";
            ?>
                <span class="user-email <?php echo $isAdmin ? 'admin-user' : ''; ?>">
                    Uživatel: <?php echo htmlentities(getLoggedInUserEmail(), ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <?php if ($isAdmin) { ?>
                    <a href="admin_panel.php" class="button-style">Admin Panel</a>
                <?php } ?>
                <a href="products.php" class="button-style">Produkty</a>
                <a href="logout.php" class="button-style logout-button">Odhlásit se</a>
            <?php } else { ?>
                <a href="login_page.php" class="button-style">Přihlásit se</a>
                <a href="register_page.php" class="button-style">Registrovat</a>
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
