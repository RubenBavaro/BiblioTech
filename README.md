# BiblioTech - Digital Library System

<!-- Improved compatibility of back to top link: See: https://github.com/othneildrew/Best-README-Template/pull/73 -->

<a name="readme-top"></a>

<!-- PROJECT LOGO-->
<br />
<div align="center">
    <a href="#">
    <img src="https://cdn-icons-png.flaticon.com/512/5442/5442126.png" alt="BiblioTech Logo" width="120" height="120">
    </a>
    <br>
  <strong>A comprehensive digital library management system for school book lending</strong>
    <br><br>

  [![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
  [![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
  [![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?logo=docker&logoColor=white)](https://www.docker.com/)
</div>

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Database Initialization](#database-initialization)
- [Usage](#usage)
  - [User Roles](#user-roles)
  - [Authentication](#authentication)
- [Security](#security)
- [Documentation](#documentation)
- [Future Developments](#future-developments)
- [License](#license)
- [Acknowledgments](#acknowledgments)

---

## 🎯 Overview

BiblioTech is a web application designed to digitize the management of book lending in a school library. The system replaces traditional paper-based registries with a centralized digital solution, enabling reliable, secure, and traceable management of books and loans.

### Key Capabilities

- **Catalog Management**: Comprehensive book catalog with metadata
- **Inventory Tracking**: Real-time monitoring of total and available copies
- **Loan Lifecycle Management**: Complete workflow from checkout to return
- **Role-Based Access Control**: Differentiated access for students and librarians
- **Secure Authentication**: Password-based and two-factor authentication (2FAS)

---

## ✨ Features

- 📚 Digital book catalog with search capabilities
- 📊 Real-time availability tracking
- 🔄 Complete loan management workflow
- 👥 Role-based access (Student & Librarian)
- 🔐 Secure authentication with 2FA support
- 🐳 Docker-based deployment for easy setup
- 🛡️ SQL injection protection via PDO prepared statements

---

## 🛠️ Technology Stack

| Technology | Purpose |
|------------|---------|
| **PHP 8+** | Backend logic and server-side processing |
| **MySQL/MariaDB** | Database management system |
| **HTML/CSS** | Frontend presentation |
| **PDO** | Secure database access layer |
| **2FAS** | Two-factor authentication |
| **Docker & Docker Compose** | Containerized development environment |

---

## 📁 Project Structure

```
BiblioTech/
│
├── sql/
│   └── bibliotech_dump.sql
│
├── root/ 
│   │  
│   ├── src/ 
│   │   └── index.php
│   │
│   ├── docker-compose.yaml
│   └── Dockerfile
│       
│
├── docs/
│   └── BibliotechAnalisi.docx
│
└── README.md
```

---

## 🚀 Getting Started

### Prerequisites

Ensure you have the following installed on your system:

- [Docker](https://www.docker.com/get-started) (20.10+)
- [Docker Compose](https://docs.docker.com/compose/install/) (1.29+)
- Compatible OS: Windows, Linux, or macOS

### Installation

1. **Clone or download the repository**

2. **Navigate to the root directory**

   ```bash
   cd path/to/BiblioTech/root
   ```

   ⚠️ **Important**: All Docker commands must be executed from the `root/` directory (where `docker-compose.yaml` is located).

3. **Start the containers**

   ```bash
   docker compose up -d
   ```

   This command will:
   - Create and start the PHP web server container
   - Create and start the MySQL/MariaDB database container
   - Expose the application on the configured port

4. **Verify container status**

   ```bash
   docker compose ps
   ```

   You should see both containers running.

5. **Access the application**

   Open your browser and navigate to:
   ```
   http://localhost:9000
   ```

### Database Initialization

The database is initialized using the SQL dump in the `sql/` directory.

To manually import the SQL file, navigate to:
```
http://localhost:9001
```

This will open phpMyAdmin where you can import the `bibliotech_dump.sql` file.

---

## 💡 Usage

### User Roles

BiblioTech supports two distinct user roles:

#### 🎓 Student

- Browse the book catalog
- Request loans (when copies are available)
- View personal active loans only

#### 📖 Librarian

- View all active loans across all users
- Process book returns
- Monitor copy availability and inventory
- Manage the lending workflow

> **Note**: The librarian role cannot be obtained through self-registration. Librarians must be created by an administrator or pre-loaded in the database.

### Authentication

The system supports multiple authentication methods:

- **Password-based authentication**: Traditional username and password
- **Passwordless authentication**: Two-factor authentication via 2FAS

---

## 🔒 Security

BiblioTech implements comprehensive security measures:

| Security Feature | Implementation |
|-----------------|----------------|
| **Password Storage** | Secure hashing algorithms |
| **SQL Injection Prevention** | PDO prepared statements |
| **Session Management** | Server-side session handling |
| **Access Control** | Role-based authorization |
| **Two-Factor Authentication** | 2FAS integration |

---

## 📖 Documentation

The `docs/` directory contains comprehensive project documentation:

- **BibliotechAnalisi.docx**: System analysis and requirements

This document provides detailed insights into the system architecture and design decisions.

---

## 🔮 Future Developments

Potential enhancements and features under consideration:

- ⏰ Automated loan expiration management
- 📧 Email notification system
- 🔍 Advanced catalog search with filters
- 🌐 RESTful API for third-party integrations
- 📊 Administrative dashboard with analytics
- 📱 Mobile-responsive interface improvements
- 📚 Multi-library support
- 🏷️ Barcode/QR code scanning for quick checkouts

---

## 📜 License

BiblioTech is developed exclusively for **educational and academic purposes**. Commercial use is not permitted.

---

## 🙏 Acknowledgments

This project acknowledges the following:

- To make this readme I was inspired by the ["Best README Template"](https://github.com/othneildrew/Best-README-Template) by Othneil Drew
- Official PHP and MySQL documentation
- [2FAS](https://2fas.com/) and [2FAS Auth](https://2fas.com/auth/) for two-factor authentication support

---

## 🛑 Stopping the Application

To stop the development environment:

```bash
docker compose down
```

To stop and remove all data (including database volumes):

```bash
docker compose down -v
```

⚠️ **Note**: Execute these commands from the `root/` directory.

---

<div align="center">
  
**[⬆ Back to Top](#readme-top)**

Made with ❤️ for educational purposes

</div>