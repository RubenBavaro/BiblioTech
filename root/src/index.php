<?php

if (isset($_COOKIE['logged_in']) && $_COOKIE['logged_in'] === "1") {
    header("Location: homepage.php");
    exit;
}

include 'header.php';
?>

<div class="container text-center mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <h1 class="display-4 mb-4">📚 Benvenuto su BiblioTech</h1>
            
            <p class="lead mb-4">
                Sistema digitale di gestione prestiti librari scolastici
            </p>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Funzionalità Principali</h5>
                    <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                            <div class="p-3">
                                <h6>📖 Catalogo Libri</h6>
                                <small class="text-muted">Consulta tutti i libri disponibili</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3">
                                <h6>🔄 Prestiti</h6>
                                <small class="text-muted">Gestisci i tuoi prestiti attivi</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3">
                                <h6>🔐 Autenticazione 2FA</h6>
                                <small class="text-muted">Sicurezza con TOTP</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="login.php" class="btn btn-primary btn-lg px-5">
                    Accedi
                </a>
                <a href="register.php" class="btn btn-outline-secondary btn-lg px-5">
                    Registrati
                </a>
            </div>

        </div>
    </div>
</div>

</body>
</html>
