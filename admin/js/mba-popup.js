jQuery(document).ready(function($) {
    var delay = typeof window.mbaPopupDelay !== 'undefined' ? window.mbaPopupDelay : 2;
    setTimeout(function() {
        $('#mba-popup-banner').attr('aria-hidden', 'false').fadeIn();
    }, delay * 1000);

    $(document).on('click', '.mba-popup-close', function() {
        $('#mba-popup-banner').attr('aria-hidden', 'true').fadeOut();
    });
    // Fermer en cliquant en dehors du popup
    $(document).on('click', '#mba-popup-banner', function(e) {
        if ($(e.target).is('#mba-popup-banner')) {
            $('#mba-popup-banner').attr('aria-hidden', 'true').fadeOut();
        }
    });
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('#mba-popup-banner').attr('aria-hidden', 'true').fadeOut();
        }
    });
});
