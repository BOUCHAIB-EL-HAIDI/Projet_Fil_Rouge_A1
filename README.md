# 🧾 ProcurePath - Système de Gestion des demandes d'achats

**ProcurePath** est une plateforme Full-Stack moderne conçue pour numériser, sécuriser et optimiser l'intégralité du cycle de vie des demandes d'achat au sein d'une organisation. 

Du simple collaborateur exprimant un besoin jusqu'à la livraison finale par le service achat, ProcurePath centralise les interactions, automatise les validations et assure une traçabilité totale via une interface premium.

---

## 🚀 Fonctionnalités Clés

- **Workflow d'Approbation Dynamique** : Circuit de validation intelligent passant par les Managers de département et la Direction Générale.
- **Gestion des Rôles Granulaire** : 
    - **Demandeur** : Expression des besoins.
    - **Manager** : Première validation métier.
    - **Directeur** : Validation budgétaire et stratégique.
    - **Acheteur** : Sourcing, gestion des devis et suivi de commande.
- **Messagerie en Temps Réel** : Un canal de discussion dédié par demande pour fluidifier les échanges entre les acteurs concernés (WebSockets).
- **Module Devis & Fournisseurs** : Centralisation des offres, comparaison assistée et sélection du meilleur prestataire.
- **Tableau de Bord Analytics** : Statistiques en temps réel sur les délais de livraison, la charge par département et les performances globales.
- **Notifications Instantanées** : Alertes en direct pour chaque changement d'état ou nouveau message.
- **Sécurité & Traçabilité** : Historique complet des changements de statut et gestion sécurisée des pièces jointes.

---

## 🛠️ Stack Technique

| Secteur | Technologies |
| :--- | :--- |
| **Backend** | Laravel 11 (PHP 8.2+), PostgreSQL, Sanctum (Auth) |
| **Frontend** | Vue 3 (Composition API), Pinia (State Management), Vite |
| **Styling** | TailwindCSS, Glassmorphism, Micro-animations |
| **Real-time** | Laravel Echo, Pusher / Reverb (WebSockets) |

---

## 📦 Installation & Lancement

### 1. Prérequis
- PHP 8.2+ & Composer
- Node.js & NPM
- Base de données (PostgreSQL recommandé)

### 2. Configuration du Backend
```bash
cd backend
composer install
cp .env.example .env # Configurez vos accès DB et Pusher ici
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### 3. Configuration du Frontend
```bash
cd frontend
npm install
npm run dev
```

L'application sera accessible sur `http://localhost:5173` et l'API sur `http://localhost:8000`.

---

## 👥 Circuit de Validation (Workflow)

1. **Création** : Un **Demandeur** soumet une nouvelle requête (Article, Quantité, Besoin).
2. **Review Manager** : Le **Manager** de son département reçoit une notification et approuve ou refuse la demande.
3. **Approbation Direction** : Le **Directeur** valide définitivement la demande budgétaire.
4. **Sourcing** : L'**Acheteur** prend le relais, consulte les fournisseurs et enregistre les différents devis reçus.
5. **Confirmation** : Le **Directeur** (ou Manager selon configuration) sélectionne le meilleur devis pour passer commande.
6. **Livraison** : L'**Acheteur** confirme la réception des produits pour clôturer le dossier.

---
*Développé pour transformer le chaos administratif en flux de travail élégant.*
