# Guide d'utilisation Frontend - MBA Banner Manager

## 🚀 Affichage automatique

Le plugin affiche automatiquement les bannières selon leurs emplacements configurés :

### Emplacements automatiques
- **Header** : Affiché au début du `<body>` via `wp_body_open`, avec fallback propre en footer pour les anciens thèmes
- **Footer** : Affiché dans `<footer>` ou fin du `<body>`
- **Sidebars** : Affiché dans les barres latérales
- **Dans les articles** : Inséré après le premier paragraphe des articles
- **Entre les articles** : Affiché entre les articles dans les listes

## 🎯 Ciblage intelligent

### Par appareil
- **Desktop** : Affiché uniquement sur ordinateur
- **Mobile** : Affiché uniquement sur mobile/tablette
- **Les 2** : Affiché sur tous les appareils

### Par statut
- Seules les bannières **actives** sont affichées
- Les bannières **inactives** sont ignorées

## 📝 Shortcode manuel

Utilisez le shortcode pour afficher des bannières manuellement :

### Syntaxe de base
```
[mba_banner]
```

### Avec paramètres
```
[mba_banner location="header" limit="2"]
```

### Paramètres disponibles
- `location` : Emplacement (header, footer, sidebar1, sidebar2, in_article, in_listing)
- `limit` : Nombre maximum de bannières à afficher (défaut: 1)

### Exemples d'utilisation

#### Dans un article ou page
```
Voici mon contenu...

[mba_banner location="in_article"]

Plus de contenu...
```

#### Dans un widget
```
[mba_banner location="sidebar1" limit="3"]
```

#### Dans un template PHP
```php
<?php echo do_shortcode('[mba_banner location="header"]'); ?>
```

## 🎨 Personnalisation CSS

### Classes CSS disponibles
- `.mba-banner-container` : Conteneur principal
- `.mba-banner` : Bannière individuelle
- `.mba-banner-image` : Bannière de type image
- `.mba-html-banner` : Bannière de type HTML/JS
- `.mba-header-banners` : Bannières d'en-tête
- `.mba-footer-banners` : Bannières de pied de page
- `.mba-sidebar-banners` : Bannières de sidebar
- `.mba-in-article-banners` : Bannières dans les articles
- `.mba-listing-banners` : Bannières entre les articles
- `.mba-shortcode-banners` : Bannières via shortcode

### Exemple de personnalisation
```css
/* Masquer les bannières sur mobile */
@media (max-width: 768px) {
    .mba-banner-container {
        display: none;
    }
}

/* Style personnalisé pour les bannières d'en-tête */
.mba-header-banners {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 8px;
}
```

## ⚡ Performance

### Optimisations
- Chargement conditionnel des assets
- Cache par requête pour éviter les requêtes répétées sur une même page
- Détection automatique du device

## 🔧 Dépannage

### Bannière ne s'affiche pas
1. Vérifiez que la bannière est **active**
2. Vérifiez l'**emplacement** configuré
3. Vérifiez le **ciblage appareil**
4. Testez avec le shortcode `[mba_banner location="header"]`

### Problème d'affichage
1. Vérifiez les dimensions de la bannière
2. Vérifiez que l'image existe (pour les bannières image)
3. Vérifiez le code HTML/JS (pour les bannières HTML)

### Debug
Connectez-vous comme administrateur et ajoutez `?debug_banners=1` à l'URL.

## 📱 Responsive

Le plugin est entièrement responsive :
- Adaptation automatique aux écrans mobiles
- Dimensions respectées sur tous les appareils
- CSS optimisé pour mobile

## 🎯 Bonnes pratiques

1. **Testez sur mobile** : Vérifiez l'affichage sur différents appareils
2. **Optimisez les images** : Utilisez des images optimisées pour le web
3. **Limitez le nombre** : Évitez trop de bannières sur une même page
4. **Respectez les formats** : Utilisez les formats standards pour de meilleurs résultats
5. **Testez les liens** : Vérifiez que les liens ciblés fonctionnent
