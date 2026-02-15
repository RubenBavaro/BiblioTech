<?php
require 'config.php';
require 'auth.php';
requireRole('bibliotecario');

$idPrestito = $_GET['id'];

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT id_libro FROM prestiti 
                           WHERE id=? AND data_restituzione IS NULL
                           FOR UPDATE");
    $stmt->execute([$idPrestito]);
    $prestito = $stmt->fetch();

    if (!$prestito) {
        throw new Exception("Prestito non valido.");
    }

    $pdo->prepare("UPDATE prestiti 
                   SET data_restituzione = NOW()
                   WHERE id=?")
        ->execute([$idPrestito]);

    $pdo->prepare("UPDATE libri 
                   SET copie_disponibili = copie_disponibili + 1
                   WHERE id=?")
        ->execute([$prestito['id_libro']]);

    $pdo->commit();

    header("Location: gestione_restituzioni.php");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Errore restituzione: " . $e->getMessage());
}
