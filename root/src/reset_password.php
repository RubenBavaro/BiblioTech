<?php
require 'config.php';

$error = "";
$success = "";
$validToken = false;
$tokenData = null;

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
    
    $stmt = $pdo->prepare("
        SELECT prt.id, prt.id_utente, prt.token, prt.expires_at, 
               u.id as user_id, u.email 
        FROM password_reset_tokens prt
        JOIN utenti u ON prt.id_utente = u.id
        WHERE prt.token = ? 
        AND prt.expires_at > NOW()
        AND prt.used = FALSE
        AND u.is_active = 1
    ");
    $stmt->execute([$token]);
    $tokenData = $stmt->fetch();
    
    if ($tokenData) {
        $validToken = true;
    } else {
        $error = "Link non valido o scaduto. Richiedi un nuovo reset password.";
    }
} else {
    $error = "Token mancante.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && $validToken) {
    
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($password)) {
        $error = "La password non può essere vuota.";
    } elseif (strlen($password) < 8) {
        $error = "La password deve essere di almeno 8 caratteri.";
    } elseif ($password !== $confirmPassword) {
        $error = "Le password non coincidono.";
    } else {
        
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("
                UPDATE utenti 
                SET password = ? 
                WHERE id = ?
            ");
            $stmt->execute([
                password_hash($password, PASSWORD_DEFAULT),
                $tokenData['user_id']
            ]);
            
            $stmt = $pdo->prepare("
                UPDATE password_reset_tokens 
                SET used = TRUE 
                WHERE token = ?
            ");
            $stmt->execute([$token]);
            
            $pdo->commit();
            
            $success = "Password aggiornata con successo! Verrai reindirizzato al login...";
            
            header("refresh:3;url=login.php");
            
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Errore reset password: " . $e->getMessage());
            $error = "Errore durante l'aggiornamento della password. Riprova.";
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="container mt-5 col-md-4">
    <div class="card shadow">
        <div class="card-body">

            <h4 class="mb-4">Reimposta Password</h4>

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

            <?php if ($validToken && !$success): ?>
                
                <p class="text-muted mb-4">
                    Inserisci la nuova password per l'account:<br>
                    <strong><?= htmlspecialchars($tokenData['email']) ?></strong>
                </p>

                <form method="POST">
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Nuova Password</label>
                        <input type="password" 
                               class="form-control" 
                               id="password"
                               name="password" 
                               minlength="8"
                               placeholder="Minimo 8 caratteri"
                               required>
                        <small class="form-text text-muted">Minimo 8 caratteri</small>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Conferma Password</label>
                        <input type="password" 
                               class="form-control" 
                               id="confirm_password"
                               name="confirm_password" 
                               minlength="8"
                               placeholder="Ripeti la password"
                               required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Cambia Password
                    </button>
                    
                </form>

            <?php elseif (!$validToken): ?>
                
                <div class="text-center mt-3">
                    <a href="forgot_password.php" class="btn btn-outline-primary">
                        Richiedi nuovo link
                    </a>
                </div>

            <?php endif; ?>

            <div class="mt-3 text-center">
                <a href="login.php">Torna al Login</a>
            </div>

        </div>
    </div>
</div>

</body>
</html>