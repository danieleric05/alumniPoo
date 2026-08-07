# CNDA Alumni

# 01-ARCHITECTURE.md

Version : 1.0

---

# 1. Objectif

Ce document décrit l'architecture officielle de la plateforme CNDA Alumni.

Toute évolution devra respecter cette architecture.

Aucun nouveau module ne devra être créé sans justification.

Toute modification de cette architecture devra faire l'objet d'une ADR (Architecture Decision Record).

---

# 2. Principes d'architecture

La plateforme repose sur les principes suivants :

* séparation claire des responsabilités ;
* modularité ;
* faible couplage ;
* forte cohésion ;
* réutilisation maximale des composants ;
* simplicité.

---

# 3. Architecture globale

La plateforme est organisée autour de modules métier indépendants.

```
CNDA Alumni

│

├── Authentification

├── Utilisateurs

├── Alumni

├── Réseau

├── Publications

├── Evènements

├── Projets

├── Notifications

├── Administration

├── Paramètres

└── Audit
```

Chaque module possède son propre cycle de vie.

Les modules communiquent uniquement via leurs Services.

---

# 4. Organisation du code

Chaque module suit la même organisation.

```
Module

Controllers/

Services/

Repositories/

Policies/

Validators/

Views/

Assets/

Tests/
```

Aucun accès direct à la base de données depuis les Controllers.

---

# 5. Rôle des couches

## Controller

Responsable de :

* recevoir la requête HTTP ;
* appeler le Service ;
* retourner la réponse.

Le Controller ne contient jamais de logique métier.

---

## Service

Contient toute la logique métier.

Les règles métier doivent être centralisées ici.

Le Service peut appeler plusieurs Repository.

---

## Repository

Responsable uniquement de l'accès aux données.

Il ne contient aucune règle métier.

---

## Policy

Détermine les autorisations.

Toute décision d'accès passe obligatoirement par une Policy.

---

## Validator

Valide les données d'entrée.

Les validations ne doivent jamais être dupliquées.

---

## View

Responsable uniquement de l'affichage.

Aucune logique métier.

---

# 6. Dépendances

Les dépendances autorisées sont :

Controller

↓

Service

↓

Repository

↓

Base de données

Les dépendances inverses sont interdites.

---

# 7. Modules métier

## Authentification

Responsabilités :

* connexion
* déconnexion
* récupération de mot de passe
* gestion des sessions
* MFA

---

## Alumni

Responsabilités :

* fiche membre
* recherche
* annuaire
* promotion
* compétences

---

## Réseau

Responsabilités :

* contacts
* relations
* mentorat
* recommandations

---

## Publications

Responsabilités :

* actualités
* articles
* commentaires
* médias

---

## Evènements

Responsabilités :

* calendrier
* inscriptions
* présences
* rappels

---

## Projets

Responsabilités :

* création
* équipe
* partenaires
* financement
* documents

---

## Notifications

Responsabilités :

* notifications internes
* email
* SMS
* Push

---

## Administration

Responsabilités :

* utilisateurs
* permissions
* rôles
* audit
* statistiques

---

# 8. Base de données

Les principes suivants sont obligatoires.

* aucune duplication de données ;
* clés étrangères systématiques ;
* contraintes d'intégrité ;
* index sur les recherches fréquentes ;
* migrations obligatoires.

Toute modification du schéma doit passer par une migration versionnée.

---

# 9. Sécurité

Aucun module ne peut contourner :

* l'authentification ;
* les permissions ;
* les validations ;
* les protections CSRF.

Les Services ne doivent jamais supposer qu'une donnée est valide.

---

# 10. Journalisation

Les actions suivantes doivent être historisées :

* connexion ;
* déconnexion ;
* création ;
* modification ;
* suppression ;
* changement de permissions ;
* validation des profils.

---

# 11. Performance

Toute nouvelle fonctionnalité devra :

* éviter les requêtes N+1 ;
* paginer les listes ;
* limiter le nombre de requêtes SQL ;
* utiliser les index existants.

---

# 12. Evolution

Une évolution doit respecter la règle suivante.

Avant de créer un nouveau module, vérifier si le besoin peut être couvert par un module existant.

Les nouveaux modules sont exceptionnels.

L'enrichissement des modules existants est privilégié.

---

# 13. Principe de compatibilité

Les évolutions doivent préserver :

* les données existantes ;
* les API publiques ;
* les liens permanents ;
* les droits des utilisateurs.

Une rupture de compatibilité doit être documentée et validée avant son implémentation.

---

# 14. Principe de qualité

Le projet privilégie :

* un code simple ;
* un code lisible ;
* un code documenté ;
* un code testable.

Une fonctionnalité complexe doit être découpée en plusieurs composants plutôt que concentrée dans un seul fichier.

---

# 15. Règle d'or

Avant toute implémentation, le développeur doit se poser les quatre questions suivantes :

1. Existe-t-il déjà un composant qui répond à ce besoin ?
2. Cette évolution respecte-t-elle l'architecture existante ?
3. Cette évolution simplifie-t-elle ou complique-t-elle le projet ?
4. Le prochain développeur comprendra-t-il ce code dans deux ans sans explication ?
