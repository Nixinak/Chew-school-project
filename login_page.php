<?php
require_once "./php/config.php";
require_once "./php/users.php";

if(isLoggedIn()){
    header("Location: index.php");
    exit;
}

$login_message = "";

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["loginForm"])){
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if(empty($email)|| empty($password)){
        $login_message = "<p class='error-message'>Email i heslo jsou povinné.</p>";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $login_message = "<p class='error-message'>Neplatný formát emailu.</p>";
    }else{
        $result = loginUser($db, $email, $password);
        if($result === true){
            header("Location: index.php");
            exit;
        }else{
            $login_message = "<p class='error-message'>" . htmlentities($result, ENT_QUOTES, 'UTF-8'). "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihlášení-ChewForever</title>
    <link rel="stylesheet" href="style/stylereg.css">
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

    <div class="auth-form-container">
        <h1>Přihlásit se</h1>
        <?php echo $login_message; ?>
        <form action="login_page.php" method="POST">
            <div>
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email'])? htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8'): ''; ?>">
            </div>
            <div>
                <label for="password">Heslo:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="loginForm" class="submit-button">Přihlásit se</button>
        </form>
        <p class="auth-link">Nemáte ještě účet? <a href="register_page.php">Vytvořte si ho</a></p>
    </div>
</body>
</html>
