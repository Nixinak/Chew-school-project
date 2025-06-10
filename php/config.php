<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "chewforever";

try {
    $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    mysqli_set_charset($db, "utf8mb4");
    //echo "<p>Uspesne</p>";
} catch (mysqli_sql_exception $e) {
    error_log("Chyba připojení k databázi: " . $e->getMessage());
    echo "<p>chyva</p>";
    exit;
}
