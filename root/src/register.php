<?php
require 'config.php';
require 'vendor/autoload.php';


$error = "";
$success = "";

function generateTOTPSecret() {
    $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';
    for ($i = 0; $i < 16; $i++) {
        $secret .= $base32Chars[random_int(0, 31)];
    }
    return $secret;
}

if (!isset($_SESSION['registration_totp_secret'])) {
    $_SESSION['registration_totp_secret'] = generateTOTPSecret();
}
$secret = $_SESSION['registration_totp_secret'];


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $secret_input = $_POST['secret'];

    if (empty($email) || empty($password)) {
        $error = "Tutti i campi sono obbligatori.";
    } elseif (strlen($password) < 8) {
        $error = "La password deve essere di almeno 8 caratteri.";
    } elseif ($password !== $confirm_password) {
        $error = "Le password non coincidono.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email non valida.";
    } else {

        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utenti WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetchColumn() > 0) {
                $error = "Email già registrata.";
            } else {

                $stmt = $pdo->prepare("
                    INSERT INTO utenti (email, password, totp_secret, ruolo, is_active, failed_attempts)
                    VALUES (?, ?, ?, 'studente', 1, 0)
                ");

                $stmt->execute([
                    $email,
                    password_hash($password, PASSWORD_DEFAULT),
                    $secret_input
                ]);

                $success = "Registrazione completata con successo! Ora puoi effettuare il login.";
                
                header("refresh:2;url=login.php");
            }

        } catch (PDOException $e) {
            error_log("Errore durante la registrazione: " . $e->getMessage());
            $error = "Errore durante la registrazione. Riprova più tardi.";
        }
    }
}


?>

<!DOCTYPE html>
<html lang="it" data-bs-theme="<?= htmlspecialchars($_COOKIE['tema'] ?? 'light') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - BiblioTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body">

<nav class="navbar navbar-expand-lg bg-body border-bottom">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">📚 BiblioTech</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">

                    <h3 class="mb-4 text-center">📝 Registrazione</h3>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!$success): ?>

                    <form method="POST">

                        <input type="hidden" name="secret" value="<?= htmlspecialchars($secret) ?>">

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   name="email" 
                                   id="email"
                                   class="form-control" 
                                   required
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   class="form-control" 
                                   minlength="8"
                                   required>
                            <small class="form-text text-muted">Minimo 8 caratteri</small>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Conferma Password</label>
                            <input type="password" 
                                   name="confirm_password" 
                                   id="confirm_password"
                                   class="form-control" 
                                   minlength="8"
                                   required>
                        </div>

                        <div class="alert alert-info">
                            <h6><strong>⚠️ Importante: Configurazione 2FA</strong></h6>
                            <p class="mb-2">Salva questa chiave TOTP nella tua app di autenticazione (es. 2FAuth, Google Authenticator):</p>
                            <div class="bg-white text-dark p-2 rounded mb-2">
                                <code style="font-size: 1.1rem; word-break: break-all;">
                                    <?= htmlspecialchars($secret) ?>
                                </code>
                            </div>
                            <small class="text-muted">
                                <strong>Nota:</strong> Questa chiave ti servirà per generare i codici TOTP al momento del login.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Completa Registrazione
                        </button>

                    </form>

                    <div class="mt-3 text-center">
                        <small>Hai già un account? <a href="login.php">Accedi</a></small>
                    </div>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>