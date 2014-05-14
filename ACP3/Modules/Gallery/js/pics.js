jQuery(document).ready(function ($) {
    $(".thumbnails li a").fancybox({
        type: 'image',
        padding: 0,
        nextClick: true,
        arrows: true,
        loop: true
    });
});