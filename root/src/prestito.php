<?php
session_start();
require 'config.php';
require 'auth.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['ruolo'] !== 'studente') {
    http_response_code(403);
    die("Accesso non autorizzato.");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Libro non valido.");
}

$idLibro = (int) $_GET['id'];
$userId = $_SESSION['user_id'];

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT copie_disponibili 
        FROM libri 
        WHERE id = ? 
        FOR UPDATE
    ");
    $stmt->execute([$idLibro]);
    $libro = $stmt->fetch();

    if (!$libro) {
        throw new Exception("Libro inesistente.");
    }

    if ($libro['copie_disponibili'] <= 0) {
        throw new Exception("Nessuna copia disponibile.");
    }

    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM prestiti 
        WHERE id_utente = ? 
        AND id_libro = ? 
        AND data_restituzione IS NULL
    ");
    $stmt->execute([$userId, $idLibro]);

    if ($stmt->fetchColumn() > 0) {
        throw new Exception("Hai già questo libro in prestito.");
    }

    $stmt = $pdo->prepare("
        INSERT INTO prestiti (id_utente, id_libro, data_prestito)
        VALUES (?, ?, NOW())
    ");
    $stmt->execute([$userId, $idLibro]);

    $stmt = $pdo->prepare("
        UPDATE libri
        SET copie_disponibili = copie_disponibili - 1
        WHERE id = ?
    ");
    $stmt->execute([$idLibro]);

    $pdo->commit();

    header("Location: libri.php?success=1");
    exit;

} catch (Exception $e) {

    $pdo->rollBack();

    header("Location: libri.php?error=" . urlencode($e->getMessage()));
    exit;
}

