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
</head>
<body>
    <div class="header">
        <a href="index.php">
            <img src="pics/icon.png" alt="logo">
        </a>
        <div class="header-auth-links">
            <a href="login_page.php">Přihlásit se</a>
        </div>
    </div>

    <div class="auth-form-container">
        <h1>Vytvořit účet</h1>

        <?php if($registration_message === "success"){ ?>
            <p class="success-message">
                Registrace byla úěšná! Nyní se můžete <a class="success-link" href="login_page.php">přihlásit</a>.
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
