# 📚 BiblioTech — Digital Library

<a name="readme-top"></a>

<br />
<div align="center">
  <a href="#">
    <img src="https://cdn-icons-png.flaticon.com/512/5442/5442126.png" alt="BiblioTech Logo" width="140" height="140">
  </a>
  <br><br>

  <strong>Sistema informativo digitale per la gestione dei prestiti librari scolastici</strong>
  <br><br>

  ![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php&logoColor=white)
  ![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
  ![Docker](https://img.shields.io/badge/Docker-Containerizzato-2496ED?logo=docker&logoColor=white)
  ![TOTP](https://img.shields.io/badge/TOTP-RFC6238-blue)
  ![Mailpit](https://img.shields.io/badge/Mailpit-Dev%20SMTP-orange)
</div>

---

## 📋 Indice

- [Panoramica](#panoramica)
- [Funzionalità Principali](#funzionalità-principali)
- [Stack Tecnologico](#stack-tecnologico)
- [Architettura Docker](#architettura-docker)
- [Struttura del Progetto](#struttura-del-progetto)
- [Avvio dell’Ambiente](#avvio-dellambiente)
- [Sistema di Autenticazione](#sistema-di-autenticazione)
- [Gestione Email (Mailpit)](#gestione-email-mailpit)
- [Ruoli Utente](#ruoli-utente)
- [Sicurezza](#sicurezza)
- [Documentazione](#documentazione)
- [Sviluppi Futuri](#sviluppi-futuri)
- [Licenza](#licenza)

---

# 🎯 Panoramica

**BiblioTech** è un sistema informativo web progettato per informatizzare la gestione dei prestiti librari in ambito scolastico.

Il sistema sostituisce il registro cartaceo con una soluzione digitale centralizzata che garantisce:

- Tracciabilità completa dei prestiti
- Aggiornamento in tempo reale delle copie disponibili
- Controllo accessi basato sui ruoli
- Autenticazione a due fattori (TOTP)
- Ambiente isolato e riproducibile tramite Docker

---

# ✨ Funzionalità Principali

- 📚 Catalogo libri digitale
- 📦 Gestione copie totali e disponibili
- 🔄 Ciclo completo di prestito e restituzione
- 👥 Controllo accessi basato su ruolo
- 🔐 Autenticazione con password + TOTP (RFC 6238)
- 📧 Reset password tramite email
- 🐳 Infrastruttura completamente containerizzata
- 🛡️ Protezione SQL Injection (PDO Prepared Statements)
- 🔄 Operazioni atomiche tramite transazioni MySQL

---

# 🛠 Stack Tecnologico

| Tecnologia | Utilizzo |
|------------|----------|
| **PHP 8+** | Backend applicativo |
| **MySQL 8.0** | Database relazionale |
| **PDO** | Accesso sicuro al database |
| **Docker & Docker Compose** | Orchestrazione container |
| **2FAuth** | Gestione chiavi TOTP self-hosted |
| **spomky-labs/otphp** | Verifica codici TOTP |
| **Mailpit** | Server SMTP di sviluppo |

---

# 🏗 Architettura Docker

L’applicazione è composta da 5 container:

| Servizio | Descrizione | Porta |
|-----------|------------|-------|
| `web` | PHP + Apache | 9000 |
| `db` | MySQL 8.0 | interna |
| `phpmyadmin` | Interfaccia DB | 9001 |
| `2fauth` | Gestione TOTP | 9002 |
| `mailpit` | SMTP di sviluppo | 8025 |

Tutti i servizi comunicano tramite la rete Docker:

```

bibliotech-network

```

---

# 📁 Struttura del Progetto

```

BiblioTech/
│
├── sql/
│   └── bibliotech_dump.sql
│
├── root/
│   ├── src/
│   │   ├── index.php
│   │   ├── login.php
│   │   ├── register.php
│   │   └── ...
│   │
│   ├── docker-compose.yaml
│   └── Dockerfile
│
├── docs/
│   └── BibliotechAnalisi.docx
│
└── README.md

````

---

# 🚀 Avvio dell’Ambiente

## 1️⃣ Posizionarsi nella directory root

```bash
cd path/to/BiblioTech/root
````

⚠️ I comandi Docker devono essere eseguiti nella cartella `root`.

---

## 2️⃣ Avviare i container

```bash
docker compose up --build -d
```

Questo comando:

* Costruisce l’immagine PHP
* Avvia MySQL
* Avvia phpMyAdmin
* Avvia 2FAuth
* Avvia Mailpit
* Crea la rete interna Docker

---

## 3️⃣ Verifica stato

```bash
docker compose ps
```

---

## 4️⃣ Accesso ai servizi

| Servizio     | URL                                            |
| ------------ | ---------------------------------------------- |
| Applicazione | [http://localhost:9000](http://localhost:9000) |
| phpMyAdmin   | [http://localhost:9001](http://localhost:9001) |
| 2FAuth       | [http://localhost:9002](http://localhost:9002) |
| Mailpit      | [http://localhost:8025](http://localhost:8025) |

---

## 5️⃣ Arrestare ambiente

```bash
docker compose down
```

Per rimuovere anche il database:

```bash
docker compose down -v
```

---

# 🔐 Sistema di Autenticazione

BiblioTech implementa autenticazione **a due fattori obbligatoria**.

## Primo fattore — Password

* Password hashata
* Mai salvata in chiaro
* Verifica server-side
* Rigenerazione ID sessione

## Secondo fattore — TOTP (RFC 6238)

Il sistema utilizza:

👉 [https://2fauth.app/](https://2fauth.app/)

2FAuth è un'applicazione Laravel self-hosted che:

* Gestisce chiavi TOTP
* Genera codici temporanei
* NON autentica direttamente l’utente

Flusso:

1. Generazione `totp_secret`
2. Registrazione in 2FAuth
3. Generazione codice TOTP
4. Verifica backend tramite `otphp`
5. Tolleranza ±1 intervallo temporale
6. Creazione sessione sicura

⚠️ L’accesso è consentito SOLO se entrambi i fattori sono validi.

---

# 📧 Gestione Email (Mailpit)

Mailpit è un server SMTP di sviluppo.

Funzionamento:

* PHP invia email tramite `msmtp`
* Email inoltrate a `mailpit:1025`
* Mailpit intercetta i messaggi
* Visualizzazione su [http://localhost:8025](http://localhost:8025)

Utilizzo principale:

* Reset password
* Test notifiche
* Verifica header email
* Debug contenuti HTML

⚠️ Nessuna email reale viene inviata.

---

# 👥 Ruoli Utente

## 🎓 Studente

* Visualizza catalogo
* Effettua prestiti
* Visualizza solo i propri prestiti

## 📖 Bibliotecario

* Visualizza tutti i prestiti
* Registra restituzioni
* Monitora disponibilità

Il ruolo bibliotecario:

* NON può essere auto-assegnato
* Deve essere creato manualmente o precaricato

---

# 🛡 Sicurezza

| Misura           | Implementazione     |
| ---------------- | ------------------- |
| Hash Password    | Algoritmo sicuro    |
| SQL Injection    | Prepared Statements |
| Session Fixation | Rigenerazione ID    |
| 2FA              | RFC 6238            |
| Reset Sicuro     | Token temporaneo    |
| Isolamento       | Docker network      |
| Consistenza      | Transazioni MySQL   |

---

# 📖 Documentazione

Cartella `docs/` contiene:

* Analisi completa
* Diagramma ER
* UML
* Specifiche sicurezza
* Architettura autenticazione

---

# 🔮 Sviluppi Futuri

* Notifiche scadenza prestiti
* Rate limiting login
* Dashboard amministrativa
* API REST
* Ricerca avanzata
* Multi-sede
* Integrazione QR / Barcode

---

# 📜 Licenza

Progetto sviluppato per finalità didattiche.

---

<div align="center">

**⬆ Torna all’inizio**

Sistema Sicuro • Containerizzato • Didattico

</div>

