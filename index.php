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

.container {
    display: flex;
    flex-grow: 1;
}

.left {
    flex: 1.2;
    padding: 20px;
    background-color: #000000;
}

.right {
    flex: 0.8;
    overflow: hidden;
    display: flex;
    align-items: stretch;
}

.slider-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0;
    transition: opacity 1s ease-in-out;
}

.slide.active {
    opacity: 1;
}

.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999;
    display: none;
}

.popup {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #181818;
    color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    width: 900px;
    height: 450px;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

.popup.active {
    display: flex;
    opacity: 1;
}

.popup-overlay.active {
    display: block;
}

.popup-content {
    display: flex;
    width: 100%;
    height: 100%;
    border-radius: 8px;
    overflow: hidden;
}

.left-side {
    flex: 1;
    padding: 30px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}

.logo {
    width: 80px;
    margin-bottom: 20px;
}

h2 {
    font-size: 1.5em;
    margin-bottom: 20px;
}

.input-wrapper {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    color: #ccc;
    font-size: 0.9em;
}

input[type="email"] {
    width: calc(100% - 12px);
    padding: 10px;
    border: 1px solid #333;
    border-radius: 4px;
    background-color: #222;
    color: #fff;
    box-sizing: border-box;
}

.submit-button {
    background-color: #fff;
    color: #000;
    border: none;
    padding: 12px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 15px;
    transition: background-color 0.3s ease;
    margin-bottom: 10px;
}

.submit-button:hover {
    background-color: #eee;
}

.no-thanks-button {
    background: none;
    color: #777;
    border: 1px solid #777;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 15px;
    transition: color 0.3s ease, border-color 0.3s ease;
}

.no-thanks-button:hover {
    color: #fff;
    border-color: #fff;
}

.right-side {
    flex: 1;
    background-color: #333;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}

.man-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
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


<div class="container">
    <div class="left">
    </div>
    <div class="right">
        <div class="slider-container">
            <img src="pics/Obr1.jpg" alt="Obrázek 1" class="slide active">
            <img src="pics/Obr2.jpg" alt="Obrázek 2" class="slide">
            <img src="pics/Obr3.jpg" alt="Obrázek 3" class="slide">
            <img src="pics/Obr4.jpg" alt="Obrázek 3" class="slide">
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