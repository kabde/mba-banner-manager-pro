jQuery(function($) {
    var selectedImageUrl = $('#mba_image_preview img').first().attr('src') || '';

    function escapeAttr(value) {
        return String(value || '').replace(/[&<>"']/g, function(character) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[character];
        });
    }

    function currentType() {
        return $('input[name="mba_type"]:checked').val() || 'image';
    }

    function safePreviewUrl(value) {
        if (!value) {
            return '';
        }

        try {
            var url = new URL(value, window.location.origin);
            return (url.protocol === 'http:' || url.protocol === 'https:') ? url.href : '';
        } catch (error) {
            return '';
        }
    }

    function updateEmptyStates() {
        $('#mba-image-empty').toggle(!$('#mba_image_id').val());
        $('#mba-link-empty').toggle(!$('#mba_image_link').val());
        $('#mba-positions-empty').toggle($('input[name="mba_positions[]"]:checked').length === 0);
    }

    function updateTypeFields() {
        if (currentType() === 'html') {
            $('#mba-image-fields').hide();
            $('#mba-html-fields').show();
            $('#mba-dimensions-section').hide();
        } else {
            $('#mba-image-fields').show();
            $('#mba-html-fields').hide();
            $('#mba-dimensions-section').show();
        }

        updatePreview();
    }

    function updateCustomDimensions() {
        $('#mba-custom-dimensions').toggle($('#mba_dimensions_select').val() === 'custom');
    }

    function imagePreviewHtml() {
        var imageId = $('#mba_image_id').val();
        var imageUrl = selectedImageUrl || $('#mba_image_preview img').first().attr('src') || '';
        var link = safePreviewUrl($('#mba_image_link').val());

        if (!imageId || !imageUrl) {
            return '<div class="mba-preview-empty">Aucune image sélectionnée.</div>';
        }

        var image = '<img class="mba-preview-image" src="' + escapeAttr(imageUrl) + '" alt="">';
        if (link) {
            return '<a href="' + escapeAttr(link) + '" target="_blank" rel="noopener noreferrer">' + image + '</a>';
        }

        return image;
    }

    function htmlPreviewHtml() {
        var html = $('#mba_html').val();

        if (!$.trim(html)) {
            return '<div class="mba-preview-empty">Aucun code HTML à prévisualiser.</div>';
        }

        return '<iframe class="mba-preview-iframe" sandbox="allow-scripts allow-popups allow-forms" srcdoc="' + escapeAttr(html) + '"></iframe>';
    }

    function updatePreview() {
        $('#mba-live-preview').html(currentType() === 'html' ? htmlPreviewHtml() : imagePreviewHtml());
        updateEmptyStates();
    }

    function removeImage() {
        selectedImageUrl = '';
        $('#mba_image_id').val('');
        $('#mba_image_preview').empty();
        updatePreview();
    }

    function renderSelectedImage(attachment) {
        var imageUrl = attachment.url;
        if (attachment.sizes && attachment.sizes.medium) {
            imageUrl = attachment.sizes.medium.url;
        } else if (attachment.sizes && attachment.sizes.thumbnail) {
            imageUrl = attachment.sizes.thumbnail.url;
        }

        selectedImageUrl = imageUrl;
        $('#mba_image_id').val(attachment.id);
        $('#mba_image_preview').html(
            '<img src="' + escapeAttr(imageUrl) + '" style="max-width:150px; height:auto; border:1px solid #ddd; margin:5px 0;" alt="">' +
            '<br><button type="button" class="button button-small" id="mba_remove_image">Supprimer</button>'
        );
        updatePreview();
    }

    $('input[name="mba_type"]').on('change', updateTypeFields);
    $('#mba_dimensions_select').on('change', updateCustomDimensions);
    $('#mba_image_link, #mba_html').on('input', updatePreview);
    $(document).on('change', 'input[name="mba_positions[]"]', updateEmptyStates);
    $(document).on('click', '#mba_remove_image', removeImage);

    $('.mba-preview-mode').on('click', function() {
        var mode = $(this).data('mode') === 'mobile' ? 'mobile' : 'desktop';

        $('.mba-preview-mode').removeClass('is-active');
        $(this).addClass('is-active');
        $('#mba-live-preview')
            .toggleClass('mba-preview-mobile', mode === 'mobile')
            .toggleClass('mba-preview-desktop', mode !== 'mobile');
    });

    $('#mba_pick_image').on('click', function(e) {
        e.preventDefault();

        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            window.alert('Erreur: La bibliothèque média WordPress n\'est pas disponible');
            return;
        }

        var frame = wp.media({
            title: 'Choisir une image',
            button: {
                text: 'Utiliser cette image'
            },
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            renderSelectedImage(attachment);
        });

        frame.open();
    });

    if ($('#mba_image_id').val() && !$('#mba_remove_image').length) {
        $('#mba_image_preview').append('<br><button type="button" class="button button-small" id="mba_remove_image">Supprimer</button>');
    }

    updateTypeFields();
    updateCustomDimensions();
    updateEmptyStates();
});
