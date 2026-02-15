<?php
require 'config.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT id, email, totp_secret FROM utenti WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $utente = $stmt->fetch();

    if ($utente) {

        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        $stmt = $pdo->prepare("
            INSERT INTO password_reset_tokens (id_utente, token, expires_at)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $utente['id'],
            $token,
            $expires
        ]);

        $to = $utente['email'];
        $subject = "Reset Password - BiblioTech";

        $resetLink = "http://localhost:9000/reset_password.php?token=" . $token;

        $message = "Ciao,

        Hai richiesto il reset della password per il tuo account BiblioTech.

        Puoi reimpostare la password cliccando qui:
        $resetLink

        Il link scadrà tra 30 minuti.

        TOTP Secret associato al tuo account:
        {$utente['totp_secret']}

        Se non hai richiesto tu questa operazione, ignora questa email.

        BiblioTech
        ";

        $headers = "From: no-reply@bibliotech.local\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        $emailSent = mail($to, $subject, $message, $headers);

        if (!$emailSent) {
            error_log("Errore nell'invio dell'email a: " . $to);
        }

        $success = "Se l'email è registrata, riceverai un messaggio con le istruzioni.";
    } else {
        $success = "Se l'email è registrata, riceverai un messaggio con le istruzioni.";
    }
}
?>

<?php include 'header.php'; ?>

<div class="container mt-5 col-md-4">
    <div class="card shadow">
        <div class="card-body">

            <h4 class="mb-4">Reset Password</h4>

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

            <form method="POST">
                <input type="email"
                       name="email"
                       class="form-control mb-3"
                       placeholder="Inserisci la tua email"
                       required>

                <button type="submit" class="btn btn-primary w-100">
                    Invia Email di Reset
                </button>
            </form>

            <div class="mt-3 text-center">
                <a href="login.php">Torna al Login</a>
            </div>

        </div>
    </div>
</div>

</body>
</html>