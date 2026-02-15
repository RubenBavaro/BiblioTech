<?php
require 'config.php';
require 'auth.php';
requireRole('studente');

$stmt = $pdo->prepare("SELECT * FROM libri WHERE id=?");
$stmt->execute([$_GET['id']]);
$libro = $stmt->fetch();

if (!$libro) {
    die("Libro non trovato.");
}
?>

<?php include 'header.php'; ?>

<div class="container mt-4">
<h3><?= htmlspecialchars($libro['titolo']) ?></h3>
<p><strong>Autore:</strong> <?= htmlspecialchars($libro['autore']) ?></p>
<p><strong>Copie Totali:</strong> <?= $libro['copie_totali'] ?></p>
<p><strong>Disponibili:</strong> <?= $libro['copie_disponibili'] ?></p>

<?php if ($libro['copie_disponibili'] > 0): ?>
<a href="prestito.php?id=<?= $libro['id'] ?>" class="btn btn-success">
PRENDI IN PRESTITO
</a>
<?php else: ?>
<button class="btn btn-secondary" disabled>Non disponibile</button>
<?php endif; ?>
</div>

</body>
</html>
