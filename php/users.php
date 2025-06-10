<?php
function registerUser($db, $email, $password) {
    $stmt_check = mysqli_prepare($db, "SELECT id FROM users WHERE email = ?");
    if ($stmt_check === false) {
        error_log("chyba mail:" . mysqli_error($db));
        return "chyba";
    }

    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        mysqli_stmt_close($stmt_check);
        return "Uživatel s tímto emailem již existuje.";
    }

    mysqli_stmt_close($stmt_check);

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    if ($hashedPassword === false) {
        error_log("Chyba heslo");
        return "chyba2";
    }

    $stmt = mysqli_prepare($db, "INSERT INTO users (email, password_hash) VALUES (?, ?)");
    if ($stmt === false) {
        error_log("chyba dotaz " . mysqli_error($db));
        return "chyba uz";
    }

    mysqli_stmt_bind_param($stmt, "ss", $email, $hashedPassword);
    $result = mysqli_execute($stmt);

    if ($result === false) {
        error_log("chyba dotaz2" . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return "chyba uz2";
    }

    mysqli_stmt_close($stmt);
    return true;
}

function getUserByEmail($db, $email) {
    $stmt = mysqli_prepare($db, "SELECT id, email, password_hash, role FROM users WHERE email = ?");
    if ($stmt === false) {
        error_log("chyba uz3 " . mysqli_error($db));
        return null;
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    if (!mysqli_execute($stmt)) {
        error_log("chyba uz4" . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return null;
    }

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $user;
}

function loginUser($db, $email, $password) {
    $user = getUserByEmail($db, $email);

    if ($user === null) {
        return "Uživatel s tímto emailem neexistuje.";
    }

    if (!password_verify($password, $user["password_hash"])) {
        return "Neplatné heslo.";
    }

    $_SESSION["loggedInUser"] = [
        "id" => $user["id"],
        "email" => $user["email"],
        "role" => $user["role"]
    ];
    session_regenerate_id(true);
    return true;
}

function logoutUser() {
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

function isLoggedIn() {
    return isset($_SESSION["loggedInUser"]);
}

function getLoggedInUserEmail() {
    if (isLoggedIn()) {
        return $_SESSION["loggedInUser"]["email"];
    }
    return null;
}
