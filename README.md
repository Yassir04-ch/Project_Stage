# Employee Management System

## 📌 Description

Employee Management System huwa application web développée dans le cadre d'un stage en entreprise IT. L'application permet de gérer les employés, les projets, les affectations et les notifications en temps réel.

## 🚀 Fonctionnalités

### Gestion des employés

* Ajouter un employé.
* Modifier les informations d'un employé.
* Supprimer un employé.
* Consulter la liste des employés.

### Gestion des projets

* Créer un projet.
* Modifier les informations d'un projet.
* Supprimer un projet.
* Consulter la liste des projets.

### Gestion des affectations

* Affecter un employé à un projet.
* Modifier une affectation.
* Supprimer une affectation.
* Consulter les affectations.

### Notifications en temps réel

* Notification lors de l'ajout d'une affectation.
* Notification lors de la suppression d'une affectation.
* Mise à jour instantanée grâce à WebSocket.

---

## 🛠️ Technologies utilisées

### Backend

* Laravel (PHP)
* REST API
* MySQL

### Frontend

* Vue.js
* Axios
* Vue Router

### Temps réel

* WebSocket
* Laravel Broadcasting

### Conteneurisation

* Docker
* Docker Compose

---

## 📂 Structure du projet

```text
Project_Stage/
│
├── Employe/                 # Backend Laravel
├── employee-frontend/       # Frontend Vue.js
├── docker-compose.yml
├── dockerfile
└── README.md
```

---

## ⚙️ Installation

### 1. Cloner le projet

```bash
git clone https://github.com/Yassir04-ch/Project_Stage.git
cd Project_Stage
```

### 2. Lancer avec Docker

```bash
docker-compose up -d --build
```

### 3. Backend Laravel

```bash
cd Employe
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### 4. Frontend Vue.js

```bash
cd employee-frontend
npm install
npm run dev
```

---

## 🗄️ Base de données

Créer une base de données MySQL puis configurer le fichier `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=employee_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## 🔔 Notifications en temps réel

L'application utilise les WebSockets pour envoyer des notifications instantanées lors des modifications des affectations et des projets.

---

## 📸 Captures d'écran

Ajoutez ici des captures d'écran de l'application.

---

## 👨‍💻 Auteur

**Yassir Ch.**

Projet réalisé dans le cadre d'un stage de développement web en entreprise IT.
