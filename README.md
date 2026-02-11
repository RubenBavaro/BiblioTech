
# BiblioTech - Digital Library System

<a name="readme-top"></a>

<br />
<div align="center">
  <a href="#">
    <img src="https://cdn-icons-png.flaticon.com/512/5442/5442126.png" alt="BiblioTech Logo" width="120" height="120">
  </a>
  <br>
  <strong>A secure, containerized digital library management system with TOTP authentication</strong>
  <br><br>

  [![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
  [![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
  [![Docker](https://img.shields.io/badge/Docker-Containerized-2496ED?logo=docker&logoColor=white)](https://www.docker.com/)
  [![TOTP](https://img.shields.io/badge/TOTP-RFC6238-blue)](https://datatracker.ietf.org/doc/html/rfc6238)
</div>

---

## 📋 Table of Contents

- [Overview](#overview)
- [Core Features](#core-features)
- [Technology Stack](#technology-stack)
- [Architecture](#architecture)
- [Project Structure](#project-structure)
- [Docker Infrastructure](#docker-infrastructure)
- [Getting Started](#getting-started)
- [Authentication System](#authentication-system)
- [User Roles](#user-roles)
- [Security Measures](#security-measures)
- [Documentation](#documentation)
- [Future Developments](#future-developments)
- [License](#license)

---

## 🎯 Overview

**BiblioTech** is a web-based digital library management system designed to replace traditional paper-based lending registries in a school environment.

The application centralizes book catalog management, inventory tracking, and loan lifecycle control in a secure and scalable infrastructure powered by Docker.

The system supports:

- Structured catalog management
- Real-time copy availability tracking
- Role-based access control
- Transactional loan operations
- Two-factor authentication using TOTP (RFC 6238 compliant)

---

## ✨ Core Features

- 📚 Digital book catalog with structured metadata
- 📦 Real-time tracking of total and available copies
- 🔄 Complete loan lifecycle management (checkout & return)
- 👥 Role-based access control (Student & Librarian)
- 🔐 Secure password hashing
- 🔑 Time-based One-Time Password (TOTP) authentication
- 🐳 Fully containerized environment
- 🛡️ SQL Injection prevention via PDO prepared statements
- 🔄 Transactional database operations for consistency

---

## 🛠️ Technology Stack

| Technology | Purpose |
|------------|----------|
| **PHP 8+** | Backend logic |
| **MySQL 8.0** | Relational database |
| **PDO** | Secure database abstraction layer |
| **HTML/CSS** | Frontend interface |
| **Docker & Docker Compose** | Containerized environment |
| **2FAuth** | Self-hosted TOTP key management service |
| **spomky-labs/otphp** | TOTP generation and verification library |

---

## 🏗️ Architecture

BiblioTech runs in a containerized architecture composed of:

- `web` → PHP + Apache application server
- `db` → MySQL 8 database
- `phpmyadmin` → database administration interface
- `2fauth` → TOTP management web service

All services communicate through a dedicated Docker bridge network.

The 2FAuth container is used for TOTP secret management and code generation.  
Authentication validation is performed entirely inside the BiblioTech backend.

---

## 📁 Project Structure

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

## 🐳 Docker Infrastructure

The entire system is deployed using Docker Compose.

### Services

| Service | Description | Port |
|----------|------------|------|
| Application | PHP + Apache server | 9000 |
| Database | MySQL 8.0 | Internal |
| phpMyAdmin | Database management | 9001 |
| 2FAuth | TOTP management interface | 9002 |

The 2FAuth service uses a persistent Docker volume for `/srv/2FAuth/storage` to ensure data durability.

---

## 🚀 Getting Started

### Prerequisites

- Docker
- Docker Compose
- Windows, Linux, or macOS

---

### 1️⃣ Navigate to the project root

```bash
cd path/to/BiblioTech/root
````

⚠ All Docker commands must be executed from the `root/` directory.

---

### 2️⃣ Start the containers

```bash
docker compose up -d
```

This will:

* Build and start the PHP web server
* Start the MySQL database
* Launch phpMyAdmin
* Launch 2FAuth
* Create the internal Docker network

---

### 3️⃣ Verify container status

```bash
docker compose ps
```

---

### 4️⃣ Access services

| Service     | URL                                            |
| ----------- | ---------------------------------------------- |
| Application | [http://localhost:9000](http://localhost:9000) |
| phpMyAdmin  | [http://localhost:9001](http://localhost:9001) |
| 2FAuth      | [http://localhost:9002](http://localhost:9002) |

---

### 5️⃣ Stop the environment

```bash
docker compose down
```

To remove database volumes:

```bash
docker compose down -v
```

---

## 🔐 Authentication System

BiblioTech implements a dual-layer authentication mechanism.

### Password-Based Authentication

* Passwords are hashed securely before storage
* Plaintext passwords are never stored
* Verification is performed server-side
* Session ID is regenerated upon successful login

### TOTP Authentication (RFC 6238)

The system integrates **2FAuth ([https://2fauth.app/](https://2fauth.app/))** as a self-hosted TOTP management application.

2FAuth is an open-source Laravel-based web application that manages TOTP secrets and generates time-based one-time passwords.

Authentication flow:

1. A unique `totp_secret` is generated for each user during registration.
2. The secret is registered in 2FAuth (or any RFC 6238 compatible authenticator).
3. The authenticator generates time-based codes.
4. The BiblioTech backend verifies the TOTP code using the `spomky-labs/otphp` library.
5. A ±1 time-window tolerance is applied to prevent clock drift issues.
6. Upon successful validation, a secure session is established.

⚠ Important:
2FAuth does NOT authenticate users directly.
It only generates TOTP codes.
All verification logic is implemented inside the BiblioTech backend.

---

## 👥 User Roles

### 🎓 Student

* Browse book catalog
* Request loans (if copies available)
* View personal active loans only

### 📖 Librarian

* View all active loans
* Process book returns
* Monitor inventory
* Manage lending operations

The librarian role cannot be self-assigned and must be created by an administrator or preloaded in the database.

---

## 🛡️ Security Measures

| Feature                     | Implementation                   |
| --------------------------- | -------------------------------- |
| Password Storage            | Secure hashing algorithms        |
| SQL Injection Protection    | PDO prepared statements          |
| Transaction Safety          | Database transactions            |
| Session Fixation Protection | Session ID regeneration          |
| Role Enforcement            | Server-side authorization checks |
| TOTP Verification           | RFC 6238 compliant validation    |
| Container Isolation         | Docker bridge network            |

---

## 📖 Documentation

The `docs/` directory contains:

* **BibliotechAnalisi.docx** – Full system analysis and design documentation

It includes:

* Functional requirements
* Entity-Relationship modeling
* UML diagrams
* Authentication architecture
* Security specifications

---

## 🔮 Future Developments

* Email notifications for loan expiration
* Advanced catalog search filters
* Administrative analytics dashboard
* REST API integration
* Brute-force protection with rate limiting
* Multi-library scalability
* Barcode/QR code scanning support

---

## 📜 License

BiblioTech is developed exclusively for educational and academic purposes.
Commercial use is not permitted.

---

<div align="center">

**⬆ Back to Top**

Secure • Containerized • Educational

</div>
