<?php
$tema = $_COOKIE['tema'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="it" data-bs-theme="<?= htmlspecialchars($tema) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BiblioTech</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body">

<nav class="navbar navbar-expand-lg bg-body border-bottom">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">📚 BiblioTech</a>

    <div class="d-flex">
      <form method="post" action="set_theme.php">
        <button type="submit" name="toggle" class="btn btn-outline-secondary">
            <?= $tema === 'dark' ? '☀️ Light' : '🌙 Dark' ?>
        </button>
      </form>
    </div>
  </div>
</nav>
