# Diagnostic Étape par Étape - MBA Banner Manager Pro

## 🚨 Problème : Rien ne s'affiche sur le site

### Étape 1 : Vérifier que le plugin est activé

1. **Allez dans l'admin WordPress** : `/wp-admin/plugins.php`
2. **Vérifiez que "MBA Banner Manager Pro" est activé** (bouton "Désactiver" visible)
3. **Si non activé** : Cliquez sur "Activer"

### Étape 2 : Vérifier que le plugin est chargé

1. **Visitez votre site** : `https://votresite.com/`
2. **Vous devriez voir** : Une boîte rouge avec "MBA BANNER MANAGER TEST"
3. **Si vous ne voyez rien** : Le plugin n'est pas chargé

### Étape 3 : Vérifier le code source

1. **Clic droit sur la page** → "Afficher le code source"
2. **Recherchez** : `<!-- MBA Banner Manager Plugin is loaded! -->`
3. **Si trouvé** : Le plugin est chargé
4. **Si non trouvé** : Problème de chargement

### Étape 4 : Vérifier les fichiers

1. **Vérifiez que ces fichiers existent** :
   - `mba-banner-manager.php`
   - `admin/class-mba-banners-cpt.php`
   - `admin/class-mba-banners-meta.php`
   - `admin/class-mba-banners-frontend.php`

2. **Vérifiez les permissions** : Les fichiers doivent être lisibles

### Étape 5 : Test avec debug

1. **Connectez-vous comme administrateur**, puis visitez : `https://votresite.com/?debug_banners=1`
2. **Vous devriez voir** : Un panneau en haut avec les détails des bannières
3. **Si rien** : Problème de permissions ou de chargement

### Étape 6 : Vérifier les erreurs PHP

1. **Activez le debug WordPress** dans `wp-config.php` :
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

2. **Vérifiez le fichier de log** : `/wp-content/debug.log`

### Étape 7 : Test simple

Ajoutez ce code temporairement dans votre `functions.php` :

```php
add_action('wp_head', function() {
    echo '<!-- TEST: functions.php is working -->';
});

add_action('wp_body_open', function() {
    echo '<div style="background: yellow; padding: 10px; margin: 10px;">TEST: wp_body_open works!</div>';
});
```

## 🔧 Solutions selon le problème

### Problème : Plugin non activé
**Solution** : Activez le plugin dans l'admin WordPress

### Problème : Fichiers manquants
**Solution** : Réinstallez le plugin

### Problème : Permissions insuffisantes
**Solution** : 
```bash
chmod 644 mba-banner-manager.php
chmod 644 admin/*.php
```

### Problème : Conflit avec le thème
**Solution** : Testez avec un thème par défaut (Twenty Twenty-Four)

### Problème : Conflit avec d'autres plugins
**Solution** : Désactivez temporairement tous les autres plugins

### Problème : Version PHP incompatible
**Solution** : Vérifiez que vous utilisez PHP 7.4+

## 📋 Checklist de diagnostic

- [ ] Plugin activé dans l'admin
- [ ] Boîte rouge de test visible sur le site
- [ ] Commentaire HTML présent dans le code source
- [ ] Tous les fichiers présents
- [ ] Permissions correctes
- [ ] Pas d'erreurs dans debug.log
- [ ] Thème compatible
- [ ] Pas de conflit avec d'autres plugins

## 🆘 Si rien ne fonctionne

1. **Désactivez le plugin**
2. **Supprimez le plugin**
3. **Réinstallez le plugin**
4. **Testez sur un site de développement**

## 📞 Informations à fournir

Si vous avez besoin d'aide, fournissez :
1. Version de WordPress
2. Version de PHP
3. Thème utilisé
4. Autres plugins activés
5. Contenu du debug.log
6. Capture d'écran de l'erreur 
