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

$message = "";

// Zpracování mazání
if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])){
    $id = intval($_POST["delete_id"]);
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        $message = "<p style='color: #66ff66;'>Produkt byl úspěšně smazán.</p>";
    }else{
        $message = "<p style='color: red;'>Chyba při mazání produktu.</p>";
    }
}

$products = $db->query("SELECT id, name, price FROM products");
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Správa produktů – Admin</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/style_admin_panel.css">
    <link rel="icon" type="image/png" href="pics/icon.png">
    <style>
        table {
            width: 100%;
            background-color: #1c1c1c;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #333;
            text-align: left;
            color: #eee;
        }
        th {
            background-color: #2a2a2a;
        }
        button.delete {
            background-color: red;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        button.delete:hover {
            background-color: darkred;
        }
    </style>
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
            <h1>Správa produktů</h1>
        </div>

        <?php echo $message; ?>

        <table>
            <thead>
                <tr>
                    <th>Název</th>
                    <th>Cena (Kč)</th>
                    <th>Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $products->fetch_assoc()){ ?>
                    <tr>
                        <td><?php echo htmlentities($product["name"], ENT_QUOTES, "UTF-8"); ?></td>
                        <td><?php echo number_format($product["price"], 2); ?> Kč</td>
                        <td>
                            <form method="post" onsubmit="return confirm('Opravdu smazat produkt?');">
                                <input type="hidden" name="delete_id" value="<?php echo $product["id"]; ?>">
                                <button type="submit" class="delete">Smazat</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
</div>
</body>
</html>
