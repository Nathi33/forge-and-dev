# Forge & Dev – Site Professionnel

## Description

Forge & Dev est un site vitrine et un outil de gestion de contact. Il propose :
- Une interface publique présentant les prestations et réalisations.
- Un formulaire de contact sécurisé avec protection anti-spam (honeypot).
- Une administration sécurisée pour gérer les contenus et accéder à la zone admin (EasyAdmin).
- Authentification via email et mot de passe avec hashage sécurisé.

Le site est construit pour être responsive et accessible sur tous les écrans.

---

## Stack Technique

- Backend : Symfony 8
- Frontend : Twig, Bootstrap 5
- Base de données : MySQL
- Gestion des utilisateurs : Symfony Security, hashage des mots de passe
- Mailing : Symfony Mailer
- Autres : EasyAdmin pour l’administration

---

## Installation

### Prérequis
- PHP >= 8.4
- Composer
- MySQL
- Serveur web (Apache/Nginx) ou PHP built-in server

### Étapes
Cloner le projet :
```bash
git clone https://github.com/ton-utilisateur/forge-and-dev.git
cd forge-and-dev
```

Installer les dépendances :
```bash
composer install
```
Copier le fichier d’environnement et le configurer :
```bash
cp .env .env.local
```

Modifier les variables pour ta base de données et le mailer :
- DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"
- MAILER_DSN="smtp://contact@forge-and-dev.fr:password@smtp.example.com:port"

Créer la base de données et exécuter les migrations :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Créer un utilisateur administrateur :
```bash
php bin/console app:create-admin
```
⚠️ Le mot de passe de l’admin est demandé à la saisie, il n’est pas stocké en clair dans le code.

---

## Utilisation

- Accéder au site public : http://localhost:8000/
- Accéder à l’admin : http://localhost:8000/admin (nécessite un compte avec ROLE_ADMIN)
- Formulaire de contact accessible via /contact
- Gestion des mails :
   - L’expéditeur (from) du formulaire de contact est contact@forge-and-dev.fr.
   - Les réponses des clients peuvent être envoyées directement à cette même adresse (replyTo).
   - Les messages reçus arrivent dans la boîte contact@forge-and-dev.fr.

---

## Sécurité

- Les mots de passe sont hashés avec Symfony PasswordHasher.
- Le formulaire de contact inclut un honeypot anti-spam.
- Toutes les pages /admin sont protégées avec le rôle ROLE_ADMIN.
- Le formulaire de connexion est sécurisé avec un token CSRF.

---

## Personnalisation

- Charte graphique : noir (#121212), bronze (#c48a3a), argent (#b5b5b5)
- Sections et contenu facilement modifiables via EasyAdmin.
- Formulaire contact modifiable via le formulaire Symfony ContactType.

---

## Commandes utiles

- Nettoyer le cache : php bin/console cache:clear
- Lister les routes : php bin/console debug:router
- Créer un utilisateur : php bin/console app:create-admin
- Lancer le serveur Symfony : symfony server:start ou php -S 127.0.0.1:8000 -t public

---

## Notes

- Les identifiants de l’admin ne sont jamais stockés en clair dans le code.
- Veille à configurer correctement le mailer DSN pour recevoir les messages de contact.
- Le site est responsive et optimisé pour les écrans mobiles et desktops.