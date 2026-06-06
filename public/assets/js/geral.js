$(document).ready(function() {
    $('.main-content').hide().fadeIn(400);

    $('a[href]').on('click', function(event) {
        const href = $(this).attr('href');

        if (!href || href.startsWith('#') || href.startsWith('mailto:') || $(this).attr('target') === '_blank') {
            return;
        }

        event.preventDefault();
        $('body').fadeOut(200, function() {
            window.location.href = href;
        });
    });
});
