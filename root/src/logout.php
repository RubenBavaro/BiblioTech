<?php
require 'config.php';

session_start();

if (isset($_SESSION['user_id'])) {

    $currentSession = session_id(); 

    try {
        $stmt = $pdo->prepare("
            UPDATE sessioni
            SET logout_time = NOW()
            WHERE session_token = ?
        ");

        $stmt->execute([$currentSession]);

        if ($stmt->rowCount() === 0) {
            error_log("Nessuna sessione trovata per token: " . $currentSession);
        }

    } catch (PDOException $e) {
        error_log("Errore durante il logout: " . $e->getMessage());
    }
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

setcookie("logged_in", "", time() - 3600, "/");

header("Location: login.php");
exit;
