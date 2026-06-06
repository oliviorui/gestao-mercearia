$(document).ready(function() {
    $(".main-content").css("display", "none");

    $(".main-content").fadeIn(900);

    $("a").click(function(event) {
        $("body").fadeOut(900);
    });
});