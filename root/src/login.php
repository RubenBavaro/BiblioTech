<?php
require 'config.php';
require 'vendor/autoload.php';
use OTPHP\TOTP;

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: homepage.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $totp_code = $_POST['totp'];

    if (empty($email) || empty($password) || empty($totp_code)) {
        $error = "Tutti i campi sono obbligatori.";
    } else {

        $stmt = $pdo->prepare("SELECT * FROM utenti WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $utente = $stmt->fetch();

        if ($utente) {

            if ($utente['failed_attempts'] >= 5 &&
                $utente['last_failed_login'] &&
                strtotime($utente['last_failed_login']) > strtotime("-15 minutes")) {

                $error = "Account temporaneamente bloccato per troppi tentativi falliti. Riprova tra 15 minuti.";
            } else {

                if (password_verify($password, $utente['password'])) {

                    $totp = TOTP::create($utente['totp_secret']);

                    if ($totp->verify($totp_code, null, 1)) {
                        session_start();
                        session_regenerate_id(true);

                        $_SESSION['user_id'] = $utente['id'];
                        $_SESSION['email'] = $utente['email'];
                        $_SESSION['ruolo'] = $utente['ruolo'];
                        $_SESSION['logged_in'] = true;
                        $_SESSION['login_time'] = date("Y-m-d H:i:s");

                        $stmt = $pdo->prepare("
                            INSERT INTO sessioni (id_utente, session_token, login_time, ip_address, user_agent)
                            VALUES (?, ?, ?, ?, ?)
                        ");

                        $stmt->execute([
                            $utente['id'],
                            session_id(),
                            date("Y-m-d H:i:s"),
                            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                        ]);

                        $pdo->prepare("UPDATE utenti SET failed_attempts = 0, last_failed_login = NULL WHERE id = ?")
                            ->execute([$utente['id']]);

                        setcookie(
                            "logged_in",
                            "1",
                            [
                                "expires"  => time() + 3600, 
                                "path"     => "/",
                                "secure"   => false,          
                                "httponly" => true,
                                "samesite" => "Strict"
                            ]
                        );

                        header("Location: homepage.php");
                        exit;

                    } else {
                        $error = "Codice TOTP non valido.";
                        
                        $pdo->prepare("UPDATE utenti SET failed_attempts = failed_attempts + 1, last_failed_login = NOW() WHERE id = ?")
                            ->execute([$utente['id']]);
                    }

                } else {
                    $error = "Password errata.";

                    $pdo->prepare("UPDATE utenti SET failed_attempts = failed_attempts + 1, last_failed_login = NOW() WHERE id = ?")
                        ->execute([$utente['id']]);
                }
            }

        } else {
            $error = "Credenziali non valide.";
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="container mt-5 col-md-4">
    <div class="card shadow">
        <div class="card-body">

            <h4 class="mb-4 text-center">🔐 Login BiblioTech</h4>

            <?php if($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           name="email" 
                           id="email"
                           class="form-control" 
                           placeholder="tua@email.com" 
                           required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="form-control" 
                           placeholder="••••••••" 
                           required>
                </div>

                <div class="mb-3">
                    <label for="totp" class="form-label">Codice TOTP</label>
                    <input type="text" 
                           name="totp" 
                           id="totp"
                           class="form-control" 
                           placeholder="123456" 
                           maxlength="6"
                           pattern="[0-9]{6}"
                           required>
                    <small class="form-text text-muted">Inserisci il codice a 6 cifre dalla tua app 2FA</small>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Accedi
                </button>
            </form>

            <div class="mt-3 text-center">
                <a href="forgot_password.php">Password dimenticata?</a>
            </div>

            <div class="mt-2 text-center">
                <small>Non hai un account? <a href="register.php">Registrati</a></small>
            </div>

        </div>
    </div>
</div>

</body>
</html>
