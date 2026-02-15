<?php
require 'config.php';
require 'auth.php';
requireRole('bibliotecario');

$stmt = $pdo->query("
SELECT p.id, u.email, l.titolo, p.data_prestito
FROM prestiti p
JOIN utenti u ON p.id_utente = u.id
JOIN libri l ON p.id_libro = l.id
WHERE p.data_restituzione IS NULL
");

$prestiti = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📚 Gestione Restituzioni</h2>
        <a href="homepage.php" class="btn btn-outline-secondary">
            ← Torna alla Dashboard
        </a>
    </div>


<table class="table table-bordered">
<tr>
<th>Studente</th>
<th>Libro</th>
<th>Data Prestito</th>
<th>Azione</th>
</tr>

<?php foreach($prestiti as $p): ?>
<tr>
<td><?= htmlspecialchars($p['email']) ?></td>
<td><?= htmlspecialchars($p['titolo']) ?></td>
<td><?= $p['data_prestito'] ?></td>
<td>
<a href="restituisci.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm">
RESTITUISCI
</a>
</td>
</tr>
<?php endforeach; ?>

</table>
</div>
</body>
</html>
