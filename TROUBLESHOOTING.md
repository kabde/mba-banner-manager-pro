# Guide de Dépannage - MBA Banner Manager Pro

## 🔍 Diagnostic des problèmes d'affichage

### Étape 1 : Vérifier la configuration de la bannière

1. **Statut** : La bannière doit être "Actif"
2. **Type** : Vérifiez que c'est bien "Image" ou "HTML/JS"
3. **Emplacements** : Au moins un emplacement doit être sélectionné
4. **Appareil** : Vérifiez le ciblage (Desktop/Mobile/Les 2)

### Étape 2 : Vérifier les données de la bannière

#### Pour une bannière image :
- L'image doit être uploadée et visible dans la bibliothèque média
- L'ID de l'image doit être sauvegardé

#### Pour une bannière HTML/JS :
- Le code HTML/JS doit être présent dans le champ

### Étape 3 : Debug en temps réel

Connectez-vous avec un compte administrateur, puis ajoutez `?debug_banners=1` à l'URL de votre site pour activer le debug :

```
https://votresite.com/?debug_banners=1
```

Cela affichera un panneau de diagnostic visible uniquement pour les administrateurs connectés.

### Étape 4 : Test avec shortcode

Utilisez le shortcode pour tester manuellement :

```
[mba_banner location="header"]
```

## 🐛 Problèmes courants et solutions

### Problème : Bannière ne s'affiche pas

**Causes possibles :**
1. Bannière inactive
2. Mauvais emplacement configuré
3. Ciblage appareil incorrect
4. Cache obsolète
5. Problème de requête SQL

**Solutions :**
1. Vérifiez le statut dans l'admin
2. Vérifiez les emplacements sélectionnés
3. Testez sur mobile et desktop
4. Activez le debug admin avec `?debug_banners=1`

### Problème : Image ne s'affiche pas

**Causes possibles :**
1. Image supprimée de la bibliothèque média
2. ID d'image incorrect
3. Problème de permissions

**Solutions :**
1. Re-uploader l'image
2. Vérifier que l'image existe dans la bibliothèque
3. Vérifier les permissions du dossier uploads

### Problème : Code HTML/JS ne fonctionne pas

**Causes possibles :**
1. Code mal formaté
2. Conflit avec le thème
3. Filtres WordPress trop restrictifs

**Solutions :**
1. Vérifier la syntaxe du code
2. Tester dans un thème par défaut
3. Vérifier les filtres `wp_kses_post`

## 🔧 Outils de diagnostic

### Vérifier les métadonnées d'une bannière

Ajoutez ce code temporairement dans votre `functions.php` :

```php
// Debug des métadonnées d'une bannière (remplacez 123 par l'ID de votre bannière)
add_action('wp_head', function() {
    if (isset($_GET['debug_banner_meta'])) {
        $banner_id = intval($_GET['debug_banner_meta']);
        $meta = get_post_meta($banner_id);
        echo '<!-- Banner Meta Debug: ' . print_r($meta, true) . ' -->';
    }
});
```

Puis visitez : `https://votresite.com/?debug_banner_meta=123`

### Vérifier toutes les bannières

```php
// Lister toutes les bannières
add_action('wp_head', function() {
    if (isset($_GET['list_banners'])) {
        $banners = get_posts([
            'post_type' => 'mbabanners',
            'posts_per_page' => -1
        ]);
        
        echo '<!-- All Banners: -->';
        foreach ($banners as $banner) {
            $status = get_post_meta($banner->ID, '_mba_status', true);
            $type = get_post_meta($banner->ID, '_mba_type', true);
            $positions = get_post_meta($banner->ID, '_mba_positions', true);
            echo '<!-- Banner ' . $banner->ID . ': ' . $banner->post_title . ' (Status: ' . $status . ', Type: ' . $type . ', Positions: ' . print_r($positions, true) . ') -->';
        }
    }
});
```

Puis visitez : `https://votresite.com/?list_banners=1`

## 📱 Test sur différents appareils

### Test mobile
- Utilisez les outils de développement de votre navigateur
- Activez le mode responsive
- Testez sur un vrai appareil mobile

### Test desktop
- Vérifiez sur différents navigateurs
- Testez avec différentes tailles d'écran

## 🎯 Vérifications spécifiques par emplacement

### Header
- Vérifiez que le thème appelle `wp_head()`
- Testez avec le shortcode `[mba_banner location="header"]`

### Footer
- Vérifiez que le thème appelle `wp_footer()`
- Testez avec le shortcode `[mba_banner location="footer"]`

### Sidebars
- Vérifiez que les sidebars sont actives
- Testez avec le shortcode `[mba_banner location="sidebar1"]`

### Dans les articles
- Vérifiez que c'est bien un article (pas une page)
- Testez avec le shortcode `[mba_banner location="in_article"]`

### Entre les articles
- Vérifiez que vous êtes sur une page de liste (accueil, catégorie, etc.)
- Testez avec le shortcode `[mba_banner location="in_listing"]`

## 🚨 Problèmes critiques

### Bannière s'affiche partout
- Vérifiez les emplacements sélectionnés
- Vérifiez la logique de ciblage

### Performance dégradée
- Videz le cache
- Vérifiez le nombre de bannières
- Optimisez les images

### Conflit avec d'autres plugins
- Désactivez temporairement les autres plugins
- Testez avec un thème par défaut

## 📞 Support

Si le problème persiste :
1. Activez le debug avec `?debug_banners=1`
2. Notez les messages d'erreur
3. Vérifiez les logs WordPress
4. Contactez le support avec ces informations 
