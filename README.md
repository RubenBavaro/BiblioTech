# 📚 BiblioTech — Sistema Digitale di Gestione Prestiti Librari

<div align="center">

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Containerizzato-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)

**Sistema informativo web per la gestione digitale dei prestiti librari scolastici**

[Funzionalità](#-funzionalità) • [Architettura](#-architettura) • [Installazione](#-installazione-quick-start) • [Utilizzo](#-utilizzo) • [Documentazione](#-documentazione)

</div>

---

## 📋 Indice

- [Panoramica](#-panoramica)
- [Funzionalità](#-funzionalità)
- [Architettura](#-architettura)
- [Requisiti](#-requisiti)
- [Installazione Quick Start](#-installazione-quick-start)
- [Struttura del Progetto](#-struttura-del-progetto)
- [Utilizzo](#-utilizzo)
- [Autenticazione 2FA](#-autenticazione-2fa)
- [Email Testing](#-email-testing-mailpit)
- [Ruoli e Permessi](#-ruoli-e-permessi)
- [Sicurezza](#-sicurezza)
- [Documentazione](#-documentazione)
- [Risoluzione Problemi](#-risoluzione-problemi)
- [Sviluppi Futuri](#-sviluppi-futuri)

---

## 🎯 Panoramica

**BiblioTech** è un sistema informativo web sviluppato per sostituire il registro cartaceo dei prestiti librari scolastici con una soluzione digitale centralizzata.

Il sistema garantisce:

- ✅ **Tracciabilità completa** dei prestiti
- ✅ **Aggiornamento real-time** delle copie disponibili
- ✅ **Controllo accessi** basato su ruoli (Studente/Bibliotecario)
- ✅ **Autenticazione a due fattori** (2FA/TOTP RFC 6238)
- ✅ **Reset password** sicuro via email
- ✅ **Ambiente riproducibile** tramite Docker
- ✅ **Protezione SQL Injection** con Prepared Statements
- ✅ **Operazioni atomiche** tramite transazioni MySQL

### 🎓 Contesto

Progetto sviluppato per l'esame di Informatica come sistema di gestione biblioteca scolastica, implementando:
- Database relazionale MySQL con vincoli di integrità
- Autenticazione sicura a due fattori
- Interfaccia web responsive con Bootstrap 5.3
- Containerizzazione completa con Docker Compose
- Sistema di email per reset password

---

## ✨ Funzionalità

### Per Studenti 👨‍🎓
- 📖 Visualizzazione catalogo libri con disponibilità real-time
- 📦 Prestito libri con un click (se disponibili)
- 📋 Storico prestiti personali attivi
- ⏰ Monitoraggio scadenze con indicatori colorati
- 🔍 Ricerca libri per titolo e autore

### Per Bibliotecari 👨‍🏫
- 📊 Dashboard con statistiche globali
- 👥 Visualizzazione tutti i prestiti attivi con nome studente
- ✅ Registrazione restituzioni libri
- 📈 Monitoraggio copie disponibili in tempo reale
- 🔍 Gestione completa del sistema

### Funzionalità Trasversali 🔧
- 🔐 Autenticazione a due fattori (Password + TOTP)
- 📧 Reset password via email con token sicuro
- 🌓 Tema chiaro/scuro con persistenza
- 📱 Interfaccia responsive (mobile-friendly)
- 🔒 Blocco account dopo 5 tentativi falliti
- 📝 Logging completo delle sessioni

---

## 🏗 Architettura

### Stack Tecnologico

| Componente | Tecnologia | Versione |
|------------|------------|----------|
| **Backend** | PHP | 8.2 |
| **Database** | MySQL | 8.0 |
| **Web Server** | Apache | Latest |
| **Frontend** | Bootstrap | 5.3.3 |
| **Containerizzazione** | Docker Compose | 3.9 |
| **Email Testing** | Mailpit | Latest |
| **2FA Manager** | 2FAuth | Latest |
| **TOTP Library** | spomky-labs/otphp | Latest |

### Servizi Docker

| Servizio | Container | Porta | Descrizione |
|----------|-----------|-------|-------------|
| **web** | BiblioTech-SitoWebR | 9000 | Applicazione PHP + Apache |
| **db** | BiblioTech-DataBaseR | 3306 (interna) | Database MySQL |
| **phpmyadmin** | BiblioTech-PhpMyAdminR | 9001 | Gestione database GUI |
| **2fauth** | 2fauth-bibliotech | 9002 | Gestione chiavi TOTP |
| **mailpit** | BiblioTech-Mailpit | 8025/1025 | Server SMTP di sviluppo |

Tutti i servizi comunicano attraverso la rete Docker `bibliotech-network`.

---

## 💻 Requisiti

### Software Necessario

- **Docker** >= 20.10
- **Docker Compose** >= 2.0
- **Git** (per clonare il repository)
- **Browser moderno** (Chrome, Firefox, Safari, Edge)

### Porte Richieste

Assicurati che le seguenti porte siano libere:
- `9000` - Applicazione web
- `9001` - phpMyAdmin
- `9002` - 2FAuth
- `8025` - Mailpit UI
- `1025` - Mailpit SMTP

---

## 🚀 Installazione Quick Start

### 1. Clona il Repository

```bash
git clone <repository-url>
cd BiblioTech/root
```

⚠️ **IMPORTANTE**: Tutti i comandi Docker devono essere eseguiti dalla cartella `root/`

### 2. Avvia i Container

```bash
docker-compose up -d --build
```

Questo comando:
- ✅ Costruisce l'immagine PHP personalizzata
- ✅ Avvia MySQL con database `biblioTech`
- ✅ Configura phpMyAdmin
- ✅ Avvia 2FAuth per gestione TOTP
- ✅ Configura Mailpit per email testing
- ✅ Crea la rete Docker `bibliotech-network`

**Tempo stimato**: 2-3 minuti al primo avvio

### 3. Importa il Database

**Opzione A - Da riga di comando (consigliata):**

```bash
docker exec -i BiblioTech-DataBaseR mysql -uroot -prootpassword biblioTech < ../sql/bibliotech_dump.sql
```

**Opzione B - Via phpMyAdmin:**

1. Vai su http://localhost:9001
2. Login con:
   - **Utente**: `root`
   - **Password**: `rootpassword`
3. Seleziona database `biblioTech`
4. Vai su tab "Importa"
5. Carica file `sql/bibliotech_dump.sql`
6. Clicca "Esegui"

### 4. Verifica Installazione

```bash
docker-compose ps
```

Output atteso: Tutti i container con stato `Up`

### 5. Accedi all'Applicazione

Apri il browser e vai su:

🌐 **http://localhost:9000**

---

## 📁 Struttura del Progetto

```
BiblioTech/
│
├── 📄 README.md                    # Questo file
│
├── 📁 root/                        # Directory di lavoro Docker
│   ├── 🐳 docker-compose.yml      # Orchestrazione container
│   ├── 🐳 Dockerfile              # Immagine PHP personalizzata
│   │
│   └── 📁 src/                    # Codice sorgente applicazione
│       ├── auth.php               # Sistema autenticazione
│       ├── config.php             # Configurazione database
│       ├── header.php             # Header comune
│       │
│       ├── index.php              # Homepage pubblica
│       ├── login.php              # Pagina di login
│       ├── logout.php             # Logout e distruzione sessione
│       ├── register.php           # Registrazione utenti
│       │
│       ├── homepage.php           # Dashboard utente (role-based)
│       │
│       ├── forgot_password.php    # Richiesta reset password
│       ├── reset_password.php     # Impostazione nuova password
│       │
│       ├── libri.php              # Catalogo libri (studenti)
│       ├── libro.php              # Dettaglio libro
│       ├── prestiti.php           # Prestiti studente
│       ├── prestito.php           # Creazione prestito
│       │
│       ├── gestione_restituzioni.php  # Dashboard bibliotecari
│       ├── restituisci.php        # Registrazione restituzione
│       │
│       └── set_theme.php          # Toggle tema chiaro/scuro
│
├── 📁 sql/                         # Database
│   └── bibliotech.sql              # Dump completo con dati di test
│
└── 📁 docs/                        # Documentazione
    └── BibliotechAnalisi.docx     # Analisi completa del progetto
```

---

## 🎮 Utilizzo

### Accesso ai Servizi

| Servizio | URL | Credenziali |
|----------|-----|-------------|
| **Applicazione** | http://localhost:9000 | Vedi utenti di test sotto |
| **phpMyAdmin** | http://localhost:9001 | `root` / `rootpassword` |
| **2FAuth** | http://localhost:9002 | Registrazione libera |
| **Mailpit** | http://localhost:8025 | Nessuna (accesso diretto) |

### Utenti di Test

Il database include utenti pre-configurati con:
- **Password**: `password123` (per tutti)
- **TOTP Secret**: `JBSWY3DPEHPK3PXP` (per test 2FA)

#### 👨‍🎓 Studenti

**Mario Rossi**
- Email: `mario.rossi@student.bibliotech.it`
- Ruolo: Studente
- Password: `password123`
- TOTP: `JBSWY3DPEHPK3PXP`

**Luigi Verdi**
- Email: `luigi.verdi@student.bibliotech.it`
- Ruolo: Studente
- Password: `password123`
- TOTP: `JBSWY3DPEHPK3PXP`

#### 👨‍🏫 Bibliotecario

**Anna Bianchi**
- Email: `anna.bianchi@bibliotecario.bibliotech.it`
- Ruolo: Bibliotecario
- Password: `password123`
- TOTP: `JBSWY3DPEHPK3PXP`

### Dati di Test

Il database contiene 10 libri di esempio:
- Il Signore degli Anelli (5 copie)
- 1984 (3 copie)
- Il Nome della Rosa (4 copie)
- Harry Potter e la Pietra Filosofale (6 copie)
- Orgoglio e Pregiudizio (3 copie)
- Il Piccolo Principe (5 copie)
- La Divina Commedia (4 copie)
- I Promessi Sposi (5 copie)
- Il Codice da Vinci (3 copie)
- Cronache del Ghiaccio e del Fuoco (4 copie)

---

## 🔐 Autenticazione 2FA

BiblioTech implementa **autenticazione a due fattori obbligatoria** secondo lo standard RFC 6238.

### Primo Fattore - Password
- ✅ Hashata con `password_hash()` (bcrypt)
- ✅ Mai salvata in chiaro
- ✅ Verifica con `password_verify()`
- ✅ Blocco dopo 5 tentativi falliti (15 minuti)

### Secondo Fattore - TOTP
- ✅ Codici temporanei a 6 cifre
- ✅ Validità 30 secondi
- ✅ Finestra di tolleranza ±1 intervallo
- ✅ Verifica tramite libreria `spomky-labs/otphp`

### Configurazione TOTP

#### Opzione 1: Usa 2FAuth (Integrato)

1. Vai su http://localhost:9002
2. Crea account su 2FAuth
3. Clicca "+" per nuovo account
4. Inserisci manualmente:
   - **Nome**: BiblioTech Test
   - **Secret**: `JBSWY3DPEHPK3PXP`
5. Salva
6. Usa i codici generati per il login

#### Opzione 2: Google Authenticator / Authy

1. Apri l'app sul telefono
2. Tocca "+" per aggiungere account
3. Seleziona "Inserire chiave manualmente"
4. Inserisci:
   - **Nome account**: BiblioTech
   - **Chiave**: `JBSWY3DPEHPK3PXP`
   - **Tipo**: Basato sul tempo
5. Salva
6. Usa i codici a 6 cifre per il login

⚠️ **IMPORTANTE**: L'accesso è consentito **SOLO** se entrambi i fattori (password + TOTP) sono corretti.

---

## 📧 Email Testing (Mailpit)

Mailpit è un server SMTP di sviluppo che intercetta tutte le email inviate dall'applicazione.

### Utilizzo

1. **Richiedi reset password** su http://localhost:9000/forgot_password.php
2. **Apri Mailpit** su http://localhost:8025
3. **Visualizza email** ricevuta con link di reset
4. **Clicca link** per reimpostare password

⚠️ **IMPORTANTE**: Nessuna email reale viene inviata. Mailpit è solo per testing.

---

## 👥 Ruoli e Permessi

### 🎓 Ruolo Studente

**Funzionalità:**
- ✅ Visualizza catalogo completo
- ✅ Prende libri in prestito (se disponibili)
- ✅ Visualizza solo i propri prestiti attivi
- ✅ Monitora scadenze
- ❌ Non può vedere prestiti di altri
- ❌ Non può registrare restituzioni

### 📚 Ruolo Bibliotecario

**Funzionalità:**
- ✅ Visualizza TUTTI i prestiti attivi
- ✅ Vede nome studente per ogni prestito
- ✅ Registra restituzioni libri
- ✅ Monitora disponibilità globale

---

## 🛡 Sicurezza

### Misure Implementate

| Categoria | Implementazione |
|-----------|----------------|
| **Password** | Bcrypt hashing via `password_hash()` |
| **SQL Injection** | PDO Prepared Statements al 100% |
| **Session Fixation** | Rigenerazione ID dopo login |
| **XSS** | `htmlspecialchars()` su tutti gli output |
| **2FA** | TOTP RFC 6238 obbligatorio |
| **Rate Limiting** | Blocco account dopo 5 tentativi (15 min) |
| **Reset Password** | Token monouso con scadenza 30 minuti |

---

## 📚 Documentazione

La cartella `docs/` contiene la documentazione completa con:
- Analisi dettagliata del sistema
- Diagramma E-R
- Diagramma delle Classi UML
- Specifiche sicurezza
- Architettura 2FA

---

## 🔧 Risoluzione Problemi

### Container non si avviano

```bash
docker-compose logs
docker-compose restart
```

### Email non arrivano

```bash
docker logs BiblioTech-Mailpit
docker-compose restart mailpit
```

### Errore database

```bash
docker logs BiblioTech-DataBaseR
docker-compose restart db
```

### Comandi Utili

```bash
# Stato container
docker-compose ps

# Stop container
docker-compose stop

# Riavvia
docker-compose restart

# Backup database
docker exec BiblioTech-DataBaseR mysqldump -uroot -prootpassword biblioTech > backup.sql

# Ripristino database
docker exec -i BiblioTech-DataBaseR mysql -uroot -prootpassword biblioTech < backup.sql
```

---

## 🔮 Sviluppi Futuri

- [ ] Notifiche email scadenze
- [ ] Dashboard admin avanzata
- [ ] Sistema prenotazioni
- [ ] Ricerca avanzata
- [ ] API REST
- [ ] Multi-sede
- [ ] QR Code / Barcode
- [ ] App mobile

---

## 📄 Licenza

Progetto sviluppato per finalità didattiche.

---

<div align="center">

**[⬆ Torna all'inizio](#-bibliotech--sistema-digitale-di-gestione-prestiti-librari)**

---

Made with ❤️ using PHP, MySQL, Docker & Bootstrap

**Sistema Sicuro** • **Containerizzato** • **Production-Ready**

</div>
