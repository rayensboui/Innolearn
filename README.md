# 🚀 Innolearn - Plateforme Éducative

Plateforme de gestion éducative avec Symfony et architecture MVC, comprenant 6 modules principaux.

## 📦 Modules
- 👥 **Utilisateurs** - Gestion multi-rôles (étudiants, formateurs, admin)
- 📅 **Événements** - Calendrier, réservations, webinaires
- 💳 **Abonnements** - Plans, paiements, facturation
- 💼 **Opportunités** - Pipeline commercial, leads, conversion
- 📚 **Cours** - Catalogue, contenu, progression, certifications
- 🏢 **Projets** - Collaboration, tâches, délais, équipes

## 🚀 Installation
```bash
git clone https://github.com/votre-username/innolearn.git
cd innolearn
composer install
npm install
cp .env .env.local
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony serve -d
🏗️ Structure Symfony
text
src/Controller/     # 6 contrôleurs (User, Event, Subscription, etc.)
src/Entity/         # Modèles de données
templates/          # Vues Twig par module
public/             # Assets CSS/JS
📊 Base de données
MySQL/PostgreSQL avec Doctrine ORM

6 tables principales liées aux modules

Migrations pour versionnement du schéma

🛠️ Technologies
Symfony 6.x, PHP 8.1+

Twig, Bootstrap 5, JavaScript

Stripe/PayPal pour les paiements

🤝 Contribution
Fork le projet

Crée une branche

Commit tes changements

Push et ouvre une PR

📄 Licence
MIT

text

### Pourquoi cette version est mieux :

✅ **Court et clair** - 30 secondes de lecture max  
✅ **Essentiel uniquement** - Pas de détails superflus  
✅ **Actionnable** - Instructions d'installation directes  
✅ **Vue d'ensemble rapide** - Compréhension immédiate du projet  
✅ **Facile à maintenir** - Moins de texte à mettre à jour  

### Quand ajouter plus de détails :

1. **Si c'est un projet open-source** → Ajouter guide de contribution détaillé  
2. **Si besoin d'installation complexe** → Ajouter section configuration avancée  
3. **Si API publique** → Ajouter documentation API  
4. **Si déploiement complexe** → Ajouter section déploiement  

**Conseil** : Commencez avec cette version simple, et ajoutez des sections seulement si nécessaire !
