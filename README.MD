# Projet Web - Moudeule.

![moudeule](screens/moudeule.png)

**Année:** 2024/2025 
**Auteurs:** FEKIH HASSEN Yassine, CALMANOVIC-PLESCOFF Auguste, BRIERRE Titouan, KADIC Anais

## Table des matières
1. [Introduction](#introduction)
2. [Architecture du projet](#architecture-du-projet)
3. [Configuration requise](#configuration-requise)
4. [Installation](#installation)
5. [Base de données](#base-de-données)
6. [Authentification](#authentification)
7. [Structure de l'application](#structure-de-lapplication)
8. [API](#api)
10. [Auteurs](#auteurs)

## Introduction

Cette plateforme pédagogique est un système complet de gestion pour établissements d'enseignement supérieur. Elle permet la gestion des cours, des étudiants, des enseignants, des évaluations et des interactions via un forum intégré.

Développée dans le cadre d'un projet universitaire du Master Informatique (Université Paris Cité), cette application exploite le framework Laravel pour offrir une expérience fluide et sécurisée aux utilisateurs.

> **Accès en ligne** : Le site a été hébergé sur [AlwaysData](https://www.alwaysdata.com/) afin de faciliter sa consultation.  
> Vous pouvez le visiter à l’adresse suivante : [https://moudeule.alwaysdata.net/](https://moudeule.alwaysdata.net/)


## Architecture du projet

### Architecture MVC

Le projet est basé sur l'architecture Model-View-Controller (MVC) fournie par Laravel:

- **Models**: Représentent les entités de la base de données avec leurs relations (`User`, `Cours`, `Controle`, `Note`, etc.)
- **Views**: Templates Blade qui définissent l'interface utilisateur
- **Controllers**: Gèrent la logique métier et les interactions entre modèles et vues

### Structure des dossiers

```
/app
  /Http
    /Controllers      # Contrôleurs de l'application
    /Middleware       # Middleware d'authentification et autorisations
  /Models            # Modèles de données
/config              # Fichiers de configuration
/database
  /migrations        # Migrations de base de données
  /seeds             # Seeders pour peupler la base de données
/public              # Ressources accessibles publiquement
  /css               # Feuilles de style CSS
  /js                # Scripts JavaScript
  /images            # Images
/resources
  /views             # Templates Blade
    /auth            # Vues pour l'authentification
    /cours           # Vues pour la gestion des cours
    /planning        # Vues pour le planning
    /forum           # Vues pour le forum
/routes              # Définition des routes
/storage             # Stockage des fichiers
  /app/public        # Stockage des documents uploadés
```

## Configuration requise

- PHP 8.1 ou supérieur
- Composer
- SQLite ou MySQL (le projet utilise par défaut SQLite)
- Extension PHP PDO-SQLite ou PDO-MySQL
- Extension PHP fileinfo


## Installation

1. Cloner le dépôt
```bash
git clone https://github.com/yassinefkh/moudeule2025.git
cd moudeule2025
```

2. Installer les dépendances PHP
```bash
composer install
```

3. Copier le fichier d'environnement et générer une clé d'application
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurer le fichier .env pour utiliser la base de données SQLite fournie
```
DB_CONNECTION=sqlite
DB_DATABASE=/chemin/absolu/vers/database/db.sqlite
```

5. Créer un lien symbolique pour le stockage des fichiers
```bash
php artisan storage:link
```

6. Démarrer le serveur de développement
```bash
php artisan serve
```

L'application sera accessible à l'adresse: http://localhost:8000

**Note importante**: Une base de données SQLite complète (`db.sqlite`) est déjà fournie avec le projet (dans `database/`) et contient toutes les données nécessaires pour tester l'application. Il n'est donc pas nécessaire de créer une nouvelle base de données ou d'exécuter les migrations.


## Base de données

Une base de données SQLite pré-configurée `db.sqlite` est fournie avec le projet. Elle contient déjà:

- Des utilisateurs pour chaque rôle (administrateur, enseignant, étudiant)
- Des formations
- Des cours avec documents et sections
- Des contrôles et des notes
- Des publications et commentaires sur le forum

### Connexion avec les comptes de test

| Type        | Login           | Mot de passe |
|-------------|-----------------|--------------|
| Admin       | admin           | admin     |
| Enseignant  | mantoine            | mantoine      |
| Étudiant    | yassinefekih        | yassinefekih  |


---

### Schéma de la base de données

La base de données comprend les tables principales suivantes :

* **users** : Stocke les informations des utilisateurs (administrateurs, enseignants, étudiants)
* **formations** : Représente les différentes formations disponibles
* **cours** : Contient les cours associés aux formations et aux enseignants
* **cours\_users** : Table pivot qui gère l'inscription des étudiants aux cours
* **sections** : Définit des sections dans les cours pour organiser les documents (ex : CM, TP, projets)
* **section\_documents** : Fait le lien entre les sections et les documents qu'elles contiennent
* **documents** : Regroupe les documents pédagogiques associés aux cours ou sections
* **plannings** : Planifie les séances de cours (dates, heures)
* **controles** : Évaluations associées aux cours
* **notes** : Stocke les notes obtenues par les étudiants aux contrôles
* **posts** : Publications ou sujets créés dans le forum
* **comments** : Commentaires associés aux publications du forum
* **reactions** : Réactions des utilisateurs sur les publications ou commentaires
* **annonces** : Annonces pédagogiques postées par les enseignants


## Authentification

Le système d'authentification comprend trois rôles principaux:

1. **Administrateur**: Gestion complète du système (validation des comptes étudiants, gestion des formations, etc.)
2. **Enseignant**: Création et gestion des cours, des séances, des documents et des évaluations
3. **Étudiant**: Inscription aux cours, consultation des ressources et participation au forum

L'authentification est gérée par des middlewares personnalisés dans `app/Http/Middleware`.

## Structure de l'application

### Contrôleurs

Les contrôleurs principaux et leurs responsabilités:

- **AuthenticatedSessionController**: Gestion de l'authentification
- **RegisterController**: Inscription des nouveaux utilisateurs
- **AdminController**: Fonctionnalités d'administration
- **CourseController**: Gestion des cours
- **SessionController**: Gestion des séances de cours
- **DocumentController**: Gestion des documents pédagogiques
- **SectionController**: Gestion des sections de cours
- **ControleController**: Gestion des évaluations
- **NoteController**: Gestion des notes
- **PostController**: Gestion des publications du forum
- **CommentController**: Gestion des commentaires
- **ReactionController**: Gestion des réactions aux publications

### Modèles

Les relations entre modèles sont définies avec précision pour faciliter les requêtes:

- Un utilisateur peut être inscrit à plusieurs cours (étudiant) ou enseigner plusieurs cours (enseignant)
- Un cours appartient à une formation et possède plusieurs sections, documents et contrôles
- Un contrôle contient plusieurs notes attribuées aux étudiants
- Un post possède des commentaires et des réactions

## API

Bien que l'application soit principalement basée sur des vues, certaines fonctionnalités utilisent AJAX pour les interactions asynchrones:

- Ajout de réactions aux publications
- Filtrage dynamique du planning des cours
- Recherche dans les cours disponibles

L'API est protégée par l'authentification et les tokens CSRF.

## Auteurs

- Yassine FEKIH HASSEN (M1 VMI)
- Auguste CALMANOVIC-PLESCOFF (M1 VMI)
- Titouan BRIERRE (M1 VMI)
- Anais KADIC (M1 VMI)

---

Projet encadré par M. Antoine MARTIN, Enseignant de l'UE Programmation Web.

---

