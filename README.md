# MBA Banner Manager Pro

Plugin WordPress pour la gestion de bannières publicitaires avec ciblage par emplacement et appareil.

## Fonctionnalités

### Types de bannières supportés
- **Images** : Upload via la bibliothèque média WordPress avec lien ciblé
- **HTML/JS** : Code personnalisé pour bannières publicitaires (AdSense, etc.)

### Gestion des bannières
- ✅ **Statut Actif/Inactif** : Activer ou désactiver les bannières
- 🎯 **Ciblage par emplacement** : Header, Footer, Sidebars, Dans les articles, Entre les articles
- 📱 **Ciblage par appareil** : Desktop, Mobile, ou les deux
- 📐 **Formats prédéfinis** : Formats standards (728x90, 300x250, etc.) et personnalisés
- 👤 **Gestion des auteurs** : Suivi de qui a créé chaque bannière

### Interface d'administration
- **Listing amélioré** avec colonnes :
  - Statut (Actif/Inactif)
  - Type (Image/HTML avec aperçu)
  - Emplacements ciblés
  - Auteur
  - Date de création
- **Interface dynamique** : Champs qui s'adaptent selon le type de bannière
- **Suggestions d'emplacements** basées sur le format choisi
- **Aperçu des images** dans le listing

### Affichage frontend
- **Affichage automatique** selon les emplacements configurés
- **Ciblage intelligent** par appareil (desktop/mobile)
- **Shortcode manuel** : `[mba_banner location="header"]`
- **Responsive design** adapté à tous les écrans

## Installation

1. Téléchargez le plugin dans le dossier `/wp-content/plugins/`
2. Activez le plugin dans l'administration WordPress
3. Accédez à "Bannières" dans le menu d'administration
4. Créez vos bannières et configurez leurs emplacements
5. Les bannières s'affichent automatiquement sur votre site !

## Utilisation

### Créer une bannière
1. Allez dans **Bannières > Ajouter une bannière**
2. Choisissez le **statut** (Actif/Inactif)
3. Sélectionnez le **type** (Image ou HTML/JS)
4. Configurez les détails selon le type choisi
5. Sélectionnez les **emplacements** et **appareils** ciblés
6. Publiez la bannière

### Types de bannières

#### Bannière Image
- Choisissez une image via la bibliothèque média
- Définissez un lien ciblé (optionnel)
- Sélectionnez le format ou définissez des dimensions personnalisées

#### Bannière HTML/JS
- Insérez votre code HTML, JavaScript ou bannière publicitaire
- Le code sera inséré tel quel dans votre site
- Compatible avec Google AdSense, bannières publicitaires, etc.

### Emplacements disponibles
- **En-tête du site** : Affichage en haut de page
- **Pied de page** : Affichage en bas de page
- **Barre latérale principale** : Sidebar principale
- **Barre latérale secondaire** : Sidebar secondaire
- **Dans les articles** : Intégration dans le contenu des articles
- **Entre les articles** : Affichage entre les articles dans les listes

### Formats prédéfinis
- **Bannières horizontales** : 728x90, 970x90, 970x250, 468x60, 320x50
- **Bannières verticales** : 300x250, 300x600, 160x600, 120x600, 250x250, 200x200
- **Bannières intégrées** : 336x280, 580x400, 180x150
- **Bannières spéciales** : 1200x630 (réseaux sociaux), 1080x1080 (Instagram), 600x200 (email)
- **Format personnalisé** : Dimensions libres

## Compatibilité

- ✅ WordPress 5.0+
- ✅ WordPress Multisite (MU)
- ✅ Compatible avec la plupart des thèmes
- ✅ Support des bibliothèques média WordPress

## Support

Pour toute question ou problème, consultez la documentation ou contactez le support.

## Documentation

- **Guide d'utilisation frontend** : `FRONTEND-USAGE.md`
- **Classes principales** : 
  - `admin/class-mba-banners-cpt.php` - Gestion du CPT
  - `admin/class-mba-banners-meta.php` - Métaboxes admin
  - `admin/class-mba-banners-frontend.php` - Affichage frontend

## Version

Version actuelle : 1.0 
