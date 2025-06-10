<?php
require_once "./php/config.php";
require_once "./php/users.php";
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage-ChewForever</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="icon" type="image/png" href="pics/icon.png">
</head>
<body>

<div class="header">
    <a href="index.php">
        <img src="pics/icon.png" alt="logo">
    </a>
    <div class="auth-controls">
        <?php
        if(isLoggedIn()){
            $email = $_SESSION["loggedInUser"]["email"];
            $role = getUserByEmail($db, $email);
        ?>
            <span class="user-email">Uživatel: <?php echo htmlentities(getLoggedInUserEmail(), ENT_QUOTES, 'UTF-8'); ?></span>

            <?php
            if($_SESSION["loggedInUser"]["role"] === "admin"){ ?>
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

<div class="container">
    <div class="left">
        <!-- Obsah vlevo -->
    </div>
    <div class="right">
        <div class="slider-container">
            <img src="pics/Obr1.jpg" alt="Obrázek 1" class="slide active">
            <img src="pics/Obr2.jpg" alt="Obrázek 2" class="slide">
            <img src="pics/Obr3.jpg" alt="Obrázek 3" class="slide">
        </div>
    </div>
</div>

<?php
if(!isLoggedIn()){ ?>
    <div class="popup-overlay"></div>
    <div id="emailPopup" class="popup">
        <div class="popup-content">
            <div class="left-side">
                <img src="pics/icon.png" alt="Logo Chew" class="logo">
                <h2>NIKDY NEZMEŠKEJ NOVINKY</h2>
                <form action="" method="post">
                    <div class="input-wrapper">
                        <label for="popup-email">E-mail</label>
                        <input type="email" id="popup-email" name="email" required>
                    </div>
                    <button type="submit" class="submit-button">Odeslat</button>
                </form>
                <button class="no-thanks-button">Ne, děkuji</button>
            </div>
            <div class="right-side">
                <img src="pics/Obr1.jpg" alt="Obrázek muže" class="man-image">
            </div>
        </div>
    </div>
<?php } ?>


<script src="js/img.js"></script>
<script src="js/popout.js"></script>
</body>
</html>
