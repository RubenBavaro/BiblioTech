<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}


function requireRole($role) {
    if (!isset($_SESSION['ruolo']) || $_SESSION['ruolo'] !== $role) {
        http_response_code(403);
        die("Accesso non autorizzato. Questa pagina è riservata agli utenti con ruolo: " . htmlspecialchars($role));
    }
}


function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}


function getUserRole() {
    return $_SESSION['ruolo'] ?? null;
}


function isBibliotecario() {
    return isset($_SESSION['ruolo']) && $_SESSION['ruolo'] === 'bibliotecario';
}


function isStudente() {
    return isset($_SESSION['ruolo']) && $_SESSION['ruolo'] === 'studente';
}
