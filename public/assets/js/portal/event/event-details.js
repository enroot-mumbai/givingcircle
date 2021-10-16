$(document).ready(function () {
    $('body').addClass('inner-pg details-page event');

    if ($('#mainBanner .carousel-item').length < 2) {
        $('#mainBanner > a').hide();
    }

    /*$('.event-banner').owlCarousel({
        loop: false,
        margin:30,
        nav: true,
        dots: true,
        singleItem: true,
        items: 1,
        navText: [
            "<i class='fas fa-arrow-left'></i>",
            "<i class='fas fa-arrow-right'></i>"
        ],
        autoplay: false,
        autoplayHoverPause: true,
    });*/
    $(document).on('click','.eventLike',function(){
        var data = {};
        var eventId = data['id'] = $(this).attr("eventId");
        jQuery.ajax({
            url: ajaxEventLikeURL,
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $(".eventLike_"+eventId).each(function () {
                    $(this).text(data.count);
                });
            }
        });
    });
});
$(".testi-carousel").owlCarousel({
    loop: true,
    margin: 10,
    responsiveClass: true,
    responsive: {
        0: {
            items: 1,
            nav: true,
        },
        600: {
            items: 1,
            nav: true,
        },
        1000: {
            items: 1,
            nav: true,
            loop: true,
        },
    },
});

$('.social-share-btn').on('click', function (e) {
    e.preventDefault();
    $(this).children('.fa-share-alt').addClass('toggle');
    $(this).siblings('.social-media').slideToggle();
});

$('.social-share-btn').on('click', function (e) {
    e.preventDefault();
    $(this).children('.fa-share-alt').addClass('toggle');
    $(this).parents('.share-view').siblings('.share-sec').find('.social-media').slideToggle();
});
$('.share-link').on('click', function (e) {
    e.preventDefault();
    $(this).siblings('.share-sec').find('.social-media').slideToggle();
});
$('.fa-close').on('click', function (e) {
    e.preventDefault();
    $(this).parent('.social-media').slideToggle();
    $(this).parents('.share-sec').find('.fa-share-alt').removeClass('toggle');
});

$(".forward-list").hide();
$(".btn-forward-post").click(function (e) {
    e.preventDefault();
    $(this).siblings('.forward-list').fadeToggle();
});
$(".btn-reply").click(function (e) {
    e.preventDefault();
    $(this).toggleClass('disabled');
    $(this).parents('.comment-box').siblings('#reply-popup').toggle();
    $(this).parents('.comment-list').siblings('.reply-box').toggle();
});
$(".btn-close").click(function (e) {
    e.preventDefault();
    $(this).parents('#reply-popup').hide();
    $(this).parents('#reply-popup').siblings('.comment-box').find('.btn-reply').removeClass('disabled');
    $(this).parents('.comment-list').siblings('.reply-box').show();
});
$(".reply-btn").click(function (e) {
    e.preventDefault();
    $(this).parents('.like-reply-list').siblings('.reply-box').css('display', 'flex');
});
$(".post-reply").click(function (e) {
    e.preventDefault();
    $(this).parents('.reply-area').hide();
    $(this).parents('.comment-box').find('.author-box').show();
    $(this).parents('.comment-box').find('.like-reply-list').show();
});