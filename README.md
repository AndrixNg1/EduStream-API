# EduStream API

Plateforme backend d’apprentissage vidéo optimisée pour le streaming adaptatif, la gestion des cours, la progression des apprenants et un système d’authentification robuste basé sur Laravel.

> **Important — API‑Only** : EduStream‑API est **une API uniquement** (pas de frontend). Elle est conçue pour être intégrée par des applications clientes (web, mobile, desktop) qui veulent ajouter des fonctionnalités de formation (upload de vidéos, audios, fichiers, streaming sécurisé, suivi de progression). Le dépôt fournit les endpoints, la logique serveur, la gestion des jobs et les webhooks éventuels — **la couche UI/UX est laissée au client**.

> Ce README contient exemples et guides pour les développeurs d’applications clientes (exemples curl/Postman, flux d’authentification via tokens Sanctum, bonnes pratiques pour le player, et recommandations pour gérer les URLs signées et la rotation des tokens).

## 🚀 Objectifs

EduStream-API sert de socle backend pour une plateforme e-learning moderne offrant :

* Gestion complète des cours, chapitres et leçons.
* Upload, traitement et streaming vidéo (HLS/Adaptive Bitrate).
* Suivi de progression en temps réel.
* Authentification JWT/Sanctum.
* Rôles & permissions (admin / instructeur / étudiant).
* Système de jobs pour le traitement vidéo.

---

## 📁 Architecture du projet

Le backend adopte une architecture modulaire et scalable, avec une séparation claire entre **Controllers**, **Repositories**, **Services** et **Jobs**.

### **Dossiers clés**

* `app/Http/Controllers` — Endpoints API.
* `app/Models` — Modèles Eloquent.
* `app/Services/Video` — Pipeline de traitement vidéo.
* `app/Jobs` — File de traitement (encodage, thumbnails...).
* `routes/api.php` — Points d’entrée de l’API.
* `database/migrations` — Structure de la base.

---

## 🛠️ Stack technique

* **Laravel 11**
* **Sanctum** pour l’authentification API
* **FFmpeg** pour l’encodage vidéo + thumbnails
* **Queues (Redis)** pour les jobs
* **MySQL / PostgreSQL**
* **Spatie Permission** pour la gestion des rôles

---

## ⚙️ Installation

### 1. Cloner le projet

```bash
 git clone https://github.com/AndrixNg1/EduStream-API.git
 cd EduStream-API
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurer la base de données et migrations

```bash
php artisan migrate --seed
```

### 5. Lancer le serveur

```bash
php artisan serve
```

---

## 🎬 Traitement vidéo & Streaming

Le système s’appuie sur un pipeline dédié :

* **ProcessVideoJob** → Encodage HLS via FFmpeg
* **ThumbnailGenerator** → Miniatures automatiques
* **VideoStreamService** → Gestion des manifest `.m3u8`

Sortie :

* `/storage/videos/hls/{lesson_id}/master.m3u8`

---

## 🔐 Authentification

L’API utilise **Laravel Sanctum** pour une authentification simple et sécurisée.

### Endpoints principaux

* `POST /auth/login`
* `POST /auth/register`
* `POST /auth/logout`
* `GET /auth/me`

---

## 🎓 Gestion du contenu

### Courses

* CRUD complet + pagination
* Relation : Course → Chapters → Lessons

### Lessons

* Upload vidéo
* Traitement via queue
* Streaming HLS

### Progress

* Avancement des étudiants en temps réel
* Calcul automatique du pourcentage

---

## 📡 Endpoints API (overview)

```
/auth/*
/courses/*
/chapters/*
/lessons/*
/lessons/{id}/stream
/progress/*
```

(Documentation complète en préparation)

---

## 🧪 Tests

```bash
php artisan test
```

---

## 📄 Licence

MIT License.

---

## 📘 Auteur

Développé par **-Andrix**.

---
