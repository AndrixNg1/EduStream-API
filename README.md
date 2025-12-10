````markdown
# EduStream API

API de streaming vidéo/audio pour plateformes de formation.

## 🚀 Fonctionnalités

- Authentification avec Laravel Sanctum
- Gestion des cours, chapitres et leçons
- Upload, conversion et traitement vidéo/audio (FFmpeg)
- Génération de miniatures
- Streaming sécurisé via URL signées
- Suivi de progression des apprenants
- Gestion des rôles et permissions avec Spatie

## 🛠️ Installation

```bash
git clone <repo-url>
cd EduStream-API
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan install:api
````

## 🗂️ Structure du projet

```
edu-stream-api/
├── app/
│ ├── Http/
│ │ ├── Controllers/
│ │ │ ├── AuthController.php
│ │ │ ├── CourseController.php
│ │ │ ├── ChapterController.php
│ │ │ ├── LessonController.php
│ │ │ ├── StreamController.php
│ │ │ └── ProgressController.php
│ │ └── Requests/
│ ├── Models/
│ │ ├── Course.php
│ │ ├── Chapter.php
│ │ ├── Lesson.php
│ │ ├── LessonStream.php
│ │ └── Progress.php
│ ├── Services/
│ │ └── Video/
│ │ ├── VideoProcessor.php
│ │ ├── ThumbnailGenerator.php
│ │ └── VideoStreamService.php
│ ├── Repositories/
│ │ └── LessonRepository.php
│ └── Jobs/
│ └── ProcessVideoJob.php
├── database/
│ ├── migrations/
│ └── seeders/
├── routes/
│ └── api.php
├── docker-compose.yml
├── README.md
└── .env.example
```


---

