jQuery(document).ready(function($) {
    // Gestion du type de bannière - doit être en premier
    $('input[name="mba_type"]').on('change', function() {
        var type = $(this).val();
        
        if (type === 'image') {
            $('#mba-image-fields').show();
            $('#mba-html-fields').hide();
            $('#mba-dimensions-section').show();
        } else if (type === 'html') {
            $('#mba-image-fields').hide();
            $('#mba-html-fields').show();
            $('#mba-dimensions-section').hide();
        }
    });
    
    // Gestion des dimensions personnalisées
    $('select[name="mba_dimensions"]').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#mba-custom-dimensions').show();
        } else {
            $('#mba-custom-dimensions').hide();
        }
    });
    
    // Attacher l'événement click au bouton
    $('#mba_pick_image').on('click', function(e) {
        e.preventDefault();
        
        // Vérifier que wp.media est disponible
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            alert('Erreur: La bibliothèque média WordPress n\'est pas disponible');
            return;
        }
        
        // Créer le frame média
        var frame = wp.media({
            title: 'Choisir une image',
            button: {
                text: 'Utiliser cette image'
            },
            multiple: false
        });
        
        // Gérer la sélection d'image
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            
            // Mettre à jour l'ID de l'image
            $('#mba_image_id').val(attachment.id);
            
            // Afficher la prévisualisation
            var imageUrl = attachment.url;
            if (attachment.sizes && attachment.sizes.thumbnail) {
                imageUrl = attachment.sizes.thumbnail.url;
            }
            
            var previewHtml = '<img src="' + imageUrl + '" style="max-width:150px; height:auto; border:1px solid #ddd; margin:5px 0;">';
            previewHtml += '<br><button type="button" class="button button-small" id="mba_remove_image">Supprimer</button>';
            
            $('#mba_image_preview').html(previewHtml);
            
            // Bouton supprimer
            $('#mba_remove_image').on('click', function() {
                $('#mba_image_id').val('');
                $('#mba_image_preview').html('');
            });
        });
        
        // Ouvrir le frame
        frame.open();
    });
    
    // Initialisation : ajouter le bouton supprimer si une image existe déjà
    if ($('#mba_image_id').val()) {
        var removeButton = '<br><button type="button" class="button button-small" id="mba_remove_image">Supprimer</button>';
        $('#mba_image_preview').append(removeButton);
        
        $('#mba_remove_image').on('click', function() {
            $('#mba_image_id').val('');
            $('#mba_image_preview').html('');
        });
    }
    
    // Initialisation : s'assurer que les bons champs sont affichés au chargement
    var currentType = $('input[name="mba_type"]:checked').val();
    if (currentType === 'html') {
        $('#mba-image-fields').hide();
        $('#mba-html-fields').show();
        $('#mba-dimensions-section').hide();
    } else {
        $('#mba-image-fields').show();
        $('#mba-html-fields').hide();
        $('#mba-dimensions-section').show();
    }
});
