<?php
require_once "./php/config.php";
require_once "./php/users.php";

if(isLoggedIn()){
    header("Location: index.php");
    exit;
}

$registration_message = "";

if(isset($_POST["registerForm"])){
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["passwordConfirm"];

    if($email === "" || $password === "" || $confirm === ""){
        $registration_message = "Všechna pole jsou povinná.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $registration_message = "Neplatný formát emailu.";
    } elseif(strlen($password)< 6){
        $registration_message = "Heslo musí mít alespoň 6 znaků.";
    } elseif($password !== $confirm){
        $registration_message = "Hesla se neshodují!";
    }else{
        $result = registerUser($db, $email, $password);
        if($result === true){
            $registration_message = "success";
        }else{
            $registration_message = htmlentities($result, ENT_QUOTES, 'UTF-8');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrace – ChewForever</title>
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
        <h1>Vytvořit účet</h1>

        <?php if($registration_message === "success"){ ?>
            <p class="success-message">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Registrace byla úěšná! Nyní se můžete <a class="success-link" href="login_page.php">přihlásit</a>.
            </p>
        <?php }else{ ?>

            <?php if($registration_message !== ""){
                echo "<p class='error-message'>" . $registration_message . "</p>";
            } ?>

            <form action="register_page.php" method="POST">
                <div>
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email'])? htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8'): ''; ?>">
                </div>
                <div>
                    <label for="password">Heslo:</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                <div>
                    <label for="passwordConfirm">Potvrzení hesla:</label>
                    <input type="password" id="passwordConfirm" name="passwordConfirm" required minlength="6">
                </div>
                <button type="submit" name="registerForm" class="submit-button">Registrovat</button>
            </form>
        <?php }?>
    </div>
</body>
</html>
