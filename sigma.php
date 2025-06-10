<div class="header">
    <a href="index.php">
        <img src="pics/icon.png" alt="logo">
    </a>
    <div class="auth-controls">
        <?php if(isLoggedIn()){
            $role = getUserByEmail($db, $email);
        ?>
            <span class="user-email">Uživatel: <?php echo htmlentities(getLoggedInUserEmail(), ENT_QUOTES, 'UTF-8'); ?></span>
            
            <?php if($role === 'admin'){ ?>
                <span class="user-role">Rola: Admin</span>
            <?php } ?>
            
            <a href="logout.php" class="button-style logout-button">Odhlásit se</a>
        <?php }else{ ?>
            <a href="login_page.php" class="button-style">Přihlásit se</a>
            <a href="register_page.php" class="button-style">Registrovat</a>
        <?php } ?>
    </div>
</div>
