<?php
require_once "./php/config.php";
require_once "./php/users.php";

$query = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id";
$result = $db->query($query);
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Produkty</title>
    <link rel="stylesheet" href="style/style.css">
    <style>
        body {
            background-color: #fff;
            color: #000;
            font-family: sans-serif;
            margin: 0;
            padding: 0;
        }

        .product-container {
            max-width: 1600px;
            margin: 40px auto;
            padding: 0 30px;
        }

        .product-container h1 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 28px;
            color: #000;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 60px 20px;
            justify-items: center;
        }

        .product-card {
            background: transparent;
            border: none;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .product-card:hover {
            transform: translateY(-4px);
        }

        .product-card img {
            width: 100%;
            height: auto;
            object-fit: contain;
            margin-bottom: 15px;
            background-color: #f7f7f7;
            padding: 10px;
            border-radius: 8px;
        }

        .product-card h2 {
            font-size: 16px;
            font-weight: normal;
            margin: 5px 0;
        }

        .product-card p {
            font-size: 14px;
            color: #555;
            margin: 0;
        }

        .product-card .category {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<div class="header">
    <a href="index.php">
        <img src="pics/icon.png" alt="logo">
    </a>
    <div class="auth-controls">
        <?php if(isLoggedIn()){
            $email = $_SESSION["loggedInUser"]["email"];
            $role = getUserByEmail($db, $email);
        ?>
            <span class="user-email">Uživatel: <?php echo htmlentities(getLoggedInUserEmail(), ENT_QUOTES, 'UTF-8'); ?></span>

            <?php if($_SESSION["loggedInUser"]["role"] === "admin"){ ?>
                <span class="user-role">Role: Admin</span>
                <a href="admin_panel.php" class="button-style">Admin Panel</a>
            <?php } ?>

            <a href="products.php" class="button-style">Produkty</a>
            <a href="logout.php" class="button-style logout-button">Odhlásit se</a>
        <?php }else{ ?>
            <a href="login_page.php" class="button-style">Přihlásit se</a>
            <a href="register_page.php" class="button-style">Registrovat</a>
        <?php } ?>
    </div>
</div>

<div class="product-container">
    <h1>Naše produkty</h1>
    <div class="product-grid">
        <?php while ($product = $result->fetch_assoc()) { ?>
            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="product-link">
                <div class="product-card">
                    <img src="/ch/<?php echo htmlentities($product['image_url']); ?>" alt="<?php echo htmlentities($product['name']); ?>">
                    <h2><?php echo htmlentities($product['name']); ?></h2>
                    <p><?php echo number_format($product['price'], 2); ?> Kč</p>
                    <p class="category">Kategorie: <?php echo htmlentities($product['category_name']); ?></p>
                </div>
            </a>
        <?php } ?>
    </div>
</div>

</body>
</html>
