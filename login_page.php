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
</head>
<body>
    <div class="header">
        <a href="index.php">
            <img src="pics/icon.png" alt="logo">
        </a>
        <div class="header-auth-links">
            <a href="register_page.php">Registrovat</a>
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
