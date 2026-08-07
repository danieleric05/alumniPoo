# Audit de l'application alumniPoo

Date : 2026-08-07
Périmètre : sécurité, qualité de code / dette technique, conformité au cahier des charges (CDC).

## Priorités immédiates

| # | Problème | Fichier | Gravité |
|---|---|---|---|
| 1 | Identifiants admin réels (`admin`/`admin123`) affichés en clair sur la page de login publique | `src/views/auth/login.php:57` | 🔴 Critique |
| 2 | `AlumniService::updateUserProfile()` ne vérifie pas que l'utilisateur existe — peut créer une ligne `users` orpheline silencieusement au lieu d'échouer | `AlumniService.php:18-31` | 🟠 Élevé |
| 3 | `/logout` en GET, hors périmètre de protection CSRF — un lien/image malveillant peut déconnecter un utilisateur à son insu | `web/index.php:21` | 🟠 Élevé |
| 4 | N+1 requêtes : jusqu'à ~250 jointures SQL sur une seule page `/admin/users` (50 lignes × 5 vérifications de permission, recalculées à chaque appel) | `PermissionService.php:51-63`, `admin/users.php` | 🟠 Élevé |

---

## 1. Sécurité

### Points forts confirmés (pas de faille)

- **CSRF** : vérifié de façon centralisée dans `Router::dispatch()` pour tout POST/PUT/DELETE/PATCH, aucune route de contournement (sauf `/logout`, voir ci-dessous) ; tous les formulaires POST des vues incluent `Csrf::field()`.
- **SQL** : 100% des appels RedBean utilisent des requêtes paramétrées (`?` + tableau de paramètres), y compris dans `PermissionService.php` — aucune concaténation de donnée utilisateur trouvée.
- **XSS** : `htmlspecialchars()` appliqué systématiquement sur les données utilisateur dans les 16 vues (titres, options, attributs, textarea).
- **IDOR** : `AlumniService` scope systématiquement les requêtes par `i_user = ?` (work experience, contact info) ; `AdminController` exige les permissions granulaires adéquates.
- **Mots de passe** : `password_hash`/`password_verify` avec `PASSWORD_BCRYPT`, régénération de session (`session_regenerate_id(true)`) après login/register, rate limiting sur `/login`, logout via `session_destroy()`.

### Failles confirmées, par sévérité

1. 🔴 **Critique** — page de login publique affichant en clair les identifiants admin réels (`admin`/`admin123`). `src/views/auth/login.php:57`.
2. 🟠 **Élevé** — `/logout` en GET, hors périmètre CSRF (logout forcé cross-site). `web/index.php:21`.
3. 🟡 **Moyen** — `X-Forwarded-For` accepté sans validation, affaiblit le rate-limiting par IP. `BaseController.php:77-82`.
4. 🟡 **Moyen** — aucun rate limiting sur `/register` (création de comptes en masse).
5. 🟡 **Moyen** — énumération de comptes via le message "Username already exists" à l'inscription. `AuthController.php:120`.
6. 🟡 **Moyen** — aucun paramétrage explicite HttpOnly/Secure/SameSite sur le cookie de session.
7. 🟡 **Moyen** — absence totale d'en-têtes de sécurité HTTP (CSP/X-Frame-Options/HSTS) + Tailwind CDN sans SRI.
8. 🟢 **Faible** — fixture morte `UsersFixtures.php` avec mot de passe en clair (non utilisée en pratique).

---

## 2. Qualité de code et dette technique

### Impact élevé

**1. Bug de perte de données : `AlumniService::updateUserProfile()` ne vérifie jamais que l'utilisateur existe**

`src/Formulair/Service/AlumniService.php:18-31`
```php
public function updateUserProfile(int $userId, array $data): OODBBean
{
    $user = R::load('users', $userId);           // pas de vérif !
    foreach ([...] as $field) { ... }
    R::store($user);                              // insère une NOUVELLE ligne si $user->id === 0
    return $user;
}
```
`R::load()` ne renvoie jamais `null` : si `$userId` ne correspond à aucun utilisateur, il renvoie un bean vide (`id = 0`), et `R::store()` sur ce bean insère une nouvelle ligne au lieu d'échouer. Incohérent avec `updateMembershipExpiry()` (même fichier, lignes 33-46) qui fait bien `if (!$user->id) return null;`, tout comme `AuthService::updateUserStatus()`, `AuthService::deleteUser()` et `PermissionService::assignTemplateToUser()`.

Appelée sans re-vérification par `AdminController::handleEditUser()` une fois la validation passée. Scénario concret : un admin supprime un compte pendant qu'il a une session active ; si ce compte soumet `/profile`, une ligne `users` orpheline est créée silencieusement.

**2. Piège de nommage RedBean confirmé : `_first_name`, `_last_name`, `_year_would_graduate_in`**

`src/Formulair/Core/Database.php:51` désactive `DispenseHelper::setEnforceNamingPolicy(false)`. Le convertisseur camelCase→snake_case de RedBean (`AQueryWriter::camelsSnake()`) insère un `_` devant toute majuscule suivie d'une minuscule — y compris le tout premier caractère :
- `FirstName` → `_first_name`
- `LastName` → `_last_name`
- `YearWouldGraduateIn` → `_year_would_graduate_in`

Ces propriétés sont utilisées partout (contrôleurs, services, ~15 vues) via l'accès bean (`$user->FirstName`), traduit automatiquement sans risque. Mais la seule ligne de SQL brut du projet qui touche ces colonnes (`AlumniService.php:50`, `ORDER BY _last_name, _first_name`) doit respecter ce préfixe. Toute future requête SQL brute, migration ou rapport utilisant l'orthographe « naturelle » (sans underscore) échouera silencieusement à matcher la colonne réelle.

**3. N+1 requêtes sur les vérifications de permission**

`src/Formulair/Service/PermissionService.php:51-63` — `userHasPermission()` fait une jointure SQL à chaque appel, sans cache. `AuthMiddleware::hasPermission()` instancie un `new PermissionService()` et relance cette jointure à chaque appel.

`src/views/admin/users.php` appelle `hasPermission()` jusqu'à 5 fois par ligne du tableau (dans le `array_map` qui boucle sur `$users`), portant toutes sur le même admin connecté mais recalculées à chaque itération. Avec `Paginator::DEFAULT_PER_PAGE = 50`, une seule page peut déclencher jusqu'à 250 requêtes JOIN, plus 4 de plus dans `layouts/base.php` (nav desktop + mobile) rendues sur *chaque* page du site.

Recommandation : calculer le jeu de permissions de l'utilisateur courant une fois par requête HTTP (cache statique ou session) plutôt que de le recalculer à chaque `hasPermission()`.

> **Note importante** : un point initialement signalé par l'audit automatisé — `iYearStart`/`iYearEnd` plafonnés à `min:1990|max:2000` — **n'est pas un bug**. C'est une règle métier posée explicitement par le porteur du projet (seules les promotions entrées entre 1990 et 2000 sont dans le périmètre de l'association, hors sorties anticipées). L'agent d'audit n'avait pas ce contexte métier.

### Impact moyen

**4. Incohérence de validation : `FirstName`/`LastName` — `max:100` vs `max:191` selon le contrôleur**

`AuthController.php:107-108` (inscription) utilise `max:100`, tandis que `AlumniController.php:51-52` et `AdminController.php:111-112` (édition de profil) utilisent `max:191`. Même donnée, même colonne, deux limites différentes selon le point d'entrée.

**5. Logger : le niveau de log n'est jamais réellement appliqué, pas de rotation**

`src/Formulair/Core/Logger.php:8,21` calcule `$this->logLevel` selon `APP_DEBUG`, mais cette propriété n'est lue nulle part ailleurs — `log()` écrit inconditionnellement tous les niveaux. `Router::dispatch()` logue un `debug()` à chaque requête HTTP, toujours actif en production malgré l'intention de filtrage. Pas de rotation de fichier (`logs/app.log` accumule indéfiniment).

**6. Duplication de code — logique de service et contrôleurs**

- Règles de validation profil dupliquées mot pour mot entre `AlumniController::handleUpdateProfile` et `AdminController::handleEditUser`.
- Règles de validation d'expérience professionnelle dupliquées entre `handleAddWorkExperience` et `handleEditWorkExperience`.
- Hydratation de bean dupliquée entre `AlumniService::addWorkExperience` et `updateWorkExperience` (8 mêmes affectations copiées-collées).

**7. Duplication de code — vues**

Le bloc HTML d'affichage des erreurs de validation est dupliqué à l'identique dans 7 vues (`auth/login.php`, `auth/register.php`, `alumni/edit_profile.php`, `alumni/add_work_experience.php`, `alumni/edit_work_experience.php`, `alumni/add_contact_info.php`, `admin/edit_user.php`). La classe Tailwind d'input est répétée 34 fois sur 8 fichiers. Le motif d'en-tête de tableau est répété dans 4 vues de liste.

### Impact faible

**8. Aucun test automatisé, malgré la documentation**

Pas de dossier `tests/`, pas de `phpunit.xml`. `composer.json` déclare pourtant `phpunit/phpunit` et un script `"test": "phpunit"` ; `README.md` documente même une section « Tests » comme si une suite existait. `composer test` échouerait immédiatement.

**9. Fichiers morts / obsolètes**

- `src/Formulair/DataFixtures/UsersFixtures.php` : mort (seule occurrence du nom de classe = sa propre déclaration), contient un mot de passe en clair, des noms de champs qui ne correspondent pas au modèle réel, et n'étend même pas `BaseFixtures.php` (stub vide).
- `COMPLETION_SUMMARY.md`, `QUICKSTART.md`, `INSTALL.md`, `test-setup.php` : jamais retouchés depuis le commit initial malgré 19 commits ultérieurs. `INSTALL.md` annonce encore « PHP 7.4+ » alors que `composer.json` fixe `"php": "8.2.*"`.
- `AuthService::getUserById()` : méthode jamais appelée.

---

## 3. Conformité au cahier des charges

| Section | Sous-point | État | Preuve |
|---|---|---|---|
| a) Bottin | a-1 formulaire inscription | ✅ Fait | `GET/POST /register` → `AuthController::register()`/`handleRegister()` |
| | a-2 recherche par nom | ❌ Absent | `AlumniController::directory()` ne lit que `page`, aucun paramètre de recherche |
| | a-3 recherche par secteur/domaine | ❌ Absent | Aucun filtre par `iDivision`/`job_division` dans l'annuaire |
| | a-4 statut sur adhérent | ✅ Fait | `iStatus` (actif/inactif, admin) + `dCotisationValidUntil` (conditionne l'accès au bottin via `isMembershipActive()`) |
| | confidentialité du bottin | ✅ Fait | `requireLogin()` + `isMembershipActive()` avant tout accès |
| b) Publications/Événements | b-1 calendrier | ❌ Absent | Table `tbl_events` orpheline, peuplée uniquement par les fixtures de démo, aucun contrôleur/vue |
| | b-2 recherche | ❌ Absent | — |
| | b-3 commentaires | ❌ Absent | Aucune table/modèle/contrôleur |
| c) Projets Collectifs | c-1 à c-5 | ❌ Absent intégralement | Aucune occurrence de "projet" dans le code applicatif |
| d) Profil utilisateur | page d'infos d'un adhérent | ✅ Fait | `GET /directory/{id}` → `directoryShow()` |
| | modifications approuvées | ❌ Absent | `handleUpdateProfile()` appelle `R::store()` directement, aucun état "pending", aucun workflow d'approbation |
| e) Droits hiérarchiques | e-1 droits + attribution | ✅ Fait | `requirePermission()` par route, permission méta `roles.manage` |
| | e-2 templates de profil | ✅ Fait | CRUD complet `/admin/roles/*`, protection des templates `admin`/`membre` |
| f) Notifications | f-1 émission | ❌ Absent | Aucune occurrence de "notif" dans le code |
| | f-2 lu/non-lu, sms/email | ❌ Absent | Aucune intégration SMS/email |

**Modules fonctionnels** : authentification, profil (sans approbation), bottin en lecture seule (liste paginée, sans recherche), expérience pro/contacts, administration des utilisateurs, système de permissions/templates.

**Écarts les plus notables** : le bottin n'a ni recherche par nom ni par secteur (juste une liste) ; le profil s'applique immédiatement sans validation admin, contrairement à l'exigence explicite de la CDC ; publications, projets collectifs et notifications sont des blocs entiers non démarrés.

---

## Recommandation

Avant d'attaquer les gros chantiers CDC manquants (b, c, f), traiter en priorité les 4 points de la table du haut — en particulier le #1 (identifiants admin exposés) et le #2 (perte de données silencieuse), qui sont des risques concrets en production dès maintenant.
