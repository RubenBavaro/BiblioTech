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

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT 
        l.titolo,
        l.autore,
        p.data_prestito,
        DATE_ADD(p.data_prestito, INTERVAL 30 DAY) AS scadenza
    FROM prestiti p
    JOIN libri l ON p.id_libro = l.id
    WHERE p.id_utente = ?
    AND p.data_restituzione IS NULL
    ORDER BY scadenza ASC
");

$stmt->execute([$userId]);
$prestiti = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📖 I miei Prestiti</h2>
        <a href="homepage.php" class="btn btn-outline-secondary">
            ← Torna alla Dashboard
        </a>
    </div>

    <?php if (count($prestiti) > 0): ?>

        <div class="card shadow-sm">
            <div class="card-body p-0">

                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Titolo</th>
                            <th>Autore</th>
                            <th>Data Prestito</th>
                            <th class="text-end">Scadenza</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($prestiti as $p): ?>

                        <?php
                        $oggi = new DateTime();
                        $scadenza = new DateTime($p['scadenza']);
                        $diff = $oggi->diff($scadenza)->days;
                        $inRitardo = $oggi > $scadenza;

                        if ($inRitardo) {
                            $badge = "bg-danger";
                            $stato = "In ritardo";
                        } elseif ($diff <= 5) {
                            $badge = "bg-warning";
                            $stato = "In scadenza";
                        } else {
                            $badge = "bg-success";
                            $stato = "Regolare";
                        }
                        ?>

                        <tr>
                            <td><?= htmlspecialchars($p['titolo']) ?></td>
                            <td><?= htmlspecialchars($p['autore']) ?></td>
                            <td><?= date("d/m/Y", strtotime($p['data_prestito'])) ?></td>
                            <td class="text-end">
                                <span class="badge <?= $badge ?>">
                                    <?= date("d/m/Y", strtotime($p['scadenza'])) ?>
                                </span>
                                <small class="d-block text-muted">
                                    <?= $stato ?>
                                </small>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                    </tbody>
                </table>

            </div>
        </div>

    <?php else: ?>

        <div class="alert alert-info">
            Non hai prestiti attivi.
        </div>

    <?php endif; ?>

</div>

</body>
</html>
