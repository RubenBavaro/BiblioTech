<?php

require 'auth.php';
require 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$ruolo = $_SESSION['ruolo'];

/* ============================
   SEZIONE INSERIMENTO LIBRO
   (solo bibliotecario)
============================ */

if ($ruolo === 'bibliotecario' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $titolo = trim($_POST['titolo']);
    $autore = trim($_POST['autore']);
    $copie = intval($_POST['copie']);

    if (!empty($titolo) && !empty($autore) && $copie > 0) {

        $stmt = $pdo->prepare("
            INSERT INTO libri (titolo, autore, copie_totali, copie_disponibili)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$titolo, $autore, $copie, $copie]);

        header("Location: libri.php?success=1");
        exit;
    } else {
        $errore = "Compila tutti i campi correttamente.";
    }
}

/* ============================
   RECUPERO LIBRI
============================ */

$stmt = $pdo->query("SELECT * FROM libri ORDER BY titolo ASC");
$libri = $stmt->fetchAll();

/* ============================
   PRESTITI ATTIVI STUDENTE
============================ */

$libriInPrestito = [];

if ($ruolo === 'studente') {

    $stmt = $pdo->prepare("
        SELECT id_libro 
        FROM prestiti 
        WHERE id_utente = ? 
        AND data_restituzione IS NULL
    ");
    $stmt->execute([$userId]);

    $libriInPrestito = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

?>

<?php include 'header.php'; ?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📚 Gestione Biblioteca</h2>
        <a href="homepage.php" class="btn btn-outline-secondary">
            ← Torna alla Dashboard
        </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            Operazione completata con successo.
        </div>
    <?php endif; ?>

    <?php if (isset($errore)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($errore) ?>
        </div>
    <?php endif; ?>

    <!-- ===============================
         SEZIONE BIBLIOTECARIO
    ================================ -->

    <?php if ($ruolo === 'bibliotecario'): ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                ➕ Aggiungi Nuovo Libro
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Titolo</label>
                            <input type="text" name="titolo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Autore</label>
                            <input type="text" name="autore" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Numero Copie</label>
                            <input type="number" name="copie" min="1" class="form-control" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">
                                Salva
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    <?php endif; ?>

    <!-- ===============================
         SEZIONE CATALOGO (STUDENTE + BIBLIOTECARIO)
    ================================ -->

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Titolo</th>
                        <th>Autore</th>
                        <th>Totali</th>
                        <th>Disponibili</th>

                        <?php if ($ruolo === 'studente'): ?>
                            <th>Azione</th>
                        <?php endif; ?>

                    </tr>
                </thead>
                <tbody>

                <?php foreach ($libri as $libro): ?>

                    <?php
                    $giaPreso = in_array($libro['id'], $libriInPrestito);
                    ?>

                    <tr>
                        <td><?= htmlspecialchars($libro['titolo']) ?></td>
                        <td><?= htmlspecialchars($libro['autore']) ?></td>
                        <td><?= $libro['copie_totali'] ?></td>
                        <td>
                            <?php if ($libro['copie_disponibili'] > 0): ?>
                                <span class="badge bg-success">
                                    <?= $libro['copie_disponibili'] ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger">0</span>
                            <?php endif; ?>
                        </td>

                        <?php if ($ruolo === 'studente'): ?>
                        <td>

                            <?php if ($giaPreso): ?>

                                <button class="btn btn-sm btn-warning" disabled>
                                    Già in tuo possesso
                                </button>

                            <?php elseif ($libro['copie_disponibili'] > 0): ?>

                                <a href="prestito.php?id=<?= $libro['id'] ?>"
                                   class="btn btn-sm btn-success">
                                    PRENDI IN PRESTITO
                                </a>

                            <?php else: ?>

                                <button class="btn btn-sm btn-secondary" disabled>
                                    Non disponibile
                                </button>

                            <?php endif; ?>

                        </td>
                        <?php endif; ?>

                    </tr>

                <?php endforeach; ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

</body>
</html>
