<?php
session_start();
require 'config.php';


if (!isset($_COOKIE['logged_in']) || $_COOKIE['logged_in'] !== "1") {
    header("Location: login.php");
    exit;
}

$ruolo = $_SESSION['ruolo'];
$userId = $_SESSION['user_id'];

if ($ruolo === 'studente') {

    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM prestiti 
        WHERE id_utente = ? 
        AND data_restituzione IS NULL
    ");
    $stmt->execute([$userId]);
    $prestitiAttivi = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM libri");
    $totaleLibri = $stmt->fetchColumn();

}


if ($ruolo === 'bibliotecario') {

    $stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM prestiti 
        WHERE data_restituzione IS NULL
    ");
    $prestitiAttiviTotali = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM utenti WHERE ruolo='studente'");
    $totaleStudenti = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM libri");
    $totaleLibri = $stmt->fetchColumn();
}

?>

<?php include 'header.php'; ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <?php if ($ruolo === 'studente'): ?>
                🎓 Dashboard Studente
            <?php else: ?>
                📚 Dashboard Bibliotecario
            <?php endif; ?>
        </h2>

        <span class="badge bg-primary">
            <?= htmlspecialchars($_SESSION['email']) ?>
        </span>
    </div>

    <?php if ($ruolo === 'studente'): ?>

        <div class="row mb-4">

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <h5>📖 Prestiti Attivi</h5>
                        <h3><?= $prestitiAttivi ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <h5>📚 Libri Disponibili</h5>
                        <h3><?= $totaleLibri ?></h3>
                    </div>
                </div>
            </div>

        </div>

        <div class="row mb-5">

            <div class="col-md-6 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5>📚 Catalogo Libri</h5>
                        <p>Visualizza tutti i libri disponibili.</p>
                        <a href="libri.php" class="btn btn-primary">
                            Vai al Catalogo
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5>📖 I miei Prestiti</h5>
                        <p>Consulta i libri in tuo possesso.</p>
                        <a href="prestiti.php" class="btn btn-success">
                            Visualizza Prestiti
                        </a>
                    </div>
                </div>
            </div>

        </div>

    <?php endif; ?>

    <?php if ($ruolo === 'bibliotecario'): ?>

        <div class="row mb-4">

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <h5>📖 Prestiti Attivi</h5>
                        <h3><?= $prestitiAttiviTotali ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <h5>👥 Studenti</h5>
                        <h3><?= $totaleStudenti ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm text-center">
                    <div class="card-body">
                        <h5>📚 Libri Totali</h5>
                        <h3><?= $totaleLibri ?></h3>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-6 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5>🔁 Gestione Restituzioni</h5>
                        <p>Visualizza e registra restituzioni.</p>
                        <a href="gestione_restituzioni.php" class="btn btn-danger">
                            Gestisci Restituzioni
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <h5>📚 Visualizza Catalogo</h5>
                        <p>Controlla disponibilità libri.</p>
                        <a href="libri.php" class="btn btn-primary">
                            Vai al Catalogo
                        </a>
                    </div>
                </div>
            </div>

        </div>

    <?php endif; ?>

    <div class="mt-4">
        <a href="logout.php" class="btn btn-outline-danger">
            Logout
        </a>
    </div>

</div>

</body>
</html>
