// Preloader
$(window).on('load', function () {
    if ($('.lds-ring').length) {
        $('.lds-ring').delay(100).fadeOut('slow', function () {
            $(this).remove();
        });
    }
});
$('.notification').click(function () {
    $('.notification_popup').show();
});

$('#close-notification').click(function () {
    //alert('ok');
    $('.notification_popup').hide();
});

$(document).ready(function () {
    var width = $(window).width();
    $(function () {
        adjustMenuHov();
    });

    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.back-to-top').fadeIn('slow');
            $('.fixed-buttons').css("bottom", 45);
        } else {
            $('.back-to-top').fadeOut('slow');
            $('.fixed-buttons').css("bottom", 0);
        }
    });
    $(".navbar-toggler").click(function () {
        $(this).toggleClass("on");
        $("#menu").slideToggle();
    });

    $('.back-to-top').click(function () {
        $('html, body').animate({
            scrollTop: 0
        }, 1500, 'easeInOutExpo');
        return false;
    });

    var path = window.location.href; // because the 'href' property of the DOM element is the absolute path

    $('.main-nav li a').each(function () {
        if (this.href === path) {
            $(this).parent().addClass('active');
            $(this).closest('.top-level-link').addClass('active');
        }
    });
    $('.standard-nav li a').each(function () {
        if (this.href === path) {
            $(this).parent().addClass('active');
        }
    });
    $('.sidebar-menu li a').each(function () {

        $("#report").removeClass('activated');
        if ($(this).data('parent') === 'report' && this.href === path) {

            $('.report-sub-nav').show();
            $("#report").addClass('activated');
            $(this).parent().addClass('active');

        } else if (this.href === path) {
            $(this).parent().addClass('active');
        }
    });

    $(".footer-toggler .btn-primary").click(function () {
        $(this).toggleClass("footer-hide");
        $(".footer-links").slideToggle();
    });

    if (width < 768) {
        $(".top-level-link a.mega-menu").click(function () {
            $(this).siblings(".subnav-arrow").toggleClass('open');
            $(this).siblings("ul.sub-menu-block").slideToggle();

        });
        $(".subnav-arrow").click(function () {
            $(this).toggleClass('open');
            $(this).siblings("ul.sub-menu-block").slideToggle();
        });
    };
    $(".menu").click(function () {
        $(this).toggleClass('open');
        $(this).siblings(".main-nav").toggleClass("menu-open");
    });

    /******Menu Hover******/
    var adjustMenuHov = function (e) {

        $('.navbar .dropdown').hover(function () {
            $(this).find('.megamenu').first().stop(true, true).delay().slideDown();
        }, function () {
            $(this).find('.megamenu').first().stop(true, true).delay().slideUp();
        });
        $('.navbar .dropdown > a').click(function () {
            location.href = this.href;
        });

    }

    $('.megamenu-li').mouseover(function () {
        $(this).find('.megamenu').show();
    })
    $('.megamenu-li').mouseout(function () {
        $(this).find('.megamenu').hide();
    });
    $('header').addClass('fixedHeader');
    // $(window).scroll(function () {
    //     if ($(this).scrollTop() > 150) {
    //         $('header').addClass('fixedHeader');
    //     } else {
    //         $('header').removeClass('fixedHeader');
    //     }
    // });

    $('a[href="#"]').click(function (event) {
        event.preventDefault();
    });
    var theme = function () {
        function handlePreventEmptyLinks() {
            $('a[href=#]').click(function (event) {
                event.preventDefault();
            });
        }
        return {
            onResize: function () {
                resizePage();
            },
            initAnimation: function () {
                var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                if (isMobile == false) {
                    $('*[data-animation]').addClass('animated');
                    $('*[data-animation]').addClass('animated');
                    $('.animated').waypoint(function (down) {
                        var elem = $(this);
                        var animation = elem.data('animation');
                        if (!elem.hasClass('visible')) {
                            var animationDelay = elem.data('animation-delay');
                            if (animationDelay) {
                                setTimeout(function () {
                                    elem.addClass(animation + ' visible');
                                }, animationDelay);
                            } else {
                                elem.addClass(animation + ' visible');
                            }
                        }
                    }, {
                        offset: $.waypoints('viewportHeight') - 60
                    });
                }
            }
        }
    }();
    $('.myBtn.less').hide();
    $('.myBtn').click(function () {
        $(this).parents('p').find('.more').show();
        $(this).parents('p').find('.lesstxt').hide();
        $(this).hide();
        $(this).siblings('.myBtn.less').show();
    });
    $('.myBtn.less').click(function () {
        $(this).parents('p').find('.more').hide();
        $(this).parents('p').find('.lesstxt').show();
        $(this).siblings('.myBtn.less').hide();
        $(this).siblings('.myBtn').show();
    });

    $('.cust-select').siblings('label').addClass('selectDDLabel');


    $('.clearAll').on('click', function () {
        $(this).parent('.refine-search').not
        $('.categories input[type=checkbox]').each(function () {
            $(this).prop('checked', false);
        });
    });

    $('.listing-page .refine-search label').on('click', function () {
        $(this).parent('.refine-search').toggleClass('open');
    });
    $('.clearAll').on('click', function (e) {
        e.stopPropagation();
    });

});


$(window).scroll(function () {
    var headerHeight = $('header').height();
    var staticBanner = $('.static-banner').height();

    if ($(this).scrollTop() > (headerHeight + staticBanner)) {
        $('.gc-details-fixed').slideDown();
    } else {
        $('.gc-details-fixed').slideUp();
    }
});

var pageURL = $(location).attr("href");
//$(".whatsapp-btn").attr("href", `https://api.whatsapp.com/send?text=${pageURL}`);



/**My Account Sidebar**/
$('#mobile-menu').click(function () {
    $('.sidebar-menu').toggleClass('menuFixed');
    $('body').toggleClass('mob-menu-added');
});
function DropdownFunction() {
    $('#myDropdown').toggleClass('show');
}

// $('.top-links .logged .user, .top-links .logged .user i, .top-links .logged .user span').click(function(e){
//     e.preventDefault();
//     $('#myDropdown').toggleClass('show');
// });
// Close the dropdown if the user clicks outside of it
window.onclick = function (event) {
    if (!event.target.matches('.user')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}


$('.mobileSearch').click(function(){
    $('.search-sec').toggleClass('show');
})