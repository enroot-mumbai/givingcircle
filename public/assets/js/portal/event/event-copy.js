$(document).ready(function () {
    $('#planicon').parents('li').addClass('active');
    $('body').addClass('inner-pg create-element event logged');
    $('html, body').animate(
        {
            scrollTop: $($('.next-section-arrow').attr('href')).offset().top - 95,
        },
        800,
        'linear'
    );
    $(window).scroll(function () {
        if ($(this).scrollTop() > 150) {
            $('.bullets .first').removeClass('active');
            $('.bullets .second').addClass('active');
        } else {
            $('.banner-sec').removeClass('fixed');
            $('.wrapper').removeClass('fixed');
            $('.bullets .first').addClass('active');
            $('.bullets .second').removeClass('active');
        }
    });
    $('.next-section-arrow').on('click', function (e) {
        e.preventDefault()
        $('html, body').animate(
            {
                scrollTop: $($(this).attr('href')).offset().top - 95,
            },
            800,
            'linear'
        );
    });
    $('.bullets li.second > a').on('click', function (e) {
        e.preventDefault()
        $('html, body').animate(
            {
                scrollTop: $($(this).attr('href')).offset().top - 95,
            },
            800,
            'linear'
        );
        $(this).parent('li').addClass('active');
        $(this).parent('li').siblings('li').removeClass('active');
    });
    function formcontrol() {
        $(".form-control").filter(function () {
            if (this.value.length !== 0) {
                $(this).siblings('label').addClass('clicked');
            }
        });
    }
    formcontrol();
    $('.form-group label').click(function () {
        $(this).addClass('clicked');
    });
    $('.crowdfunding-radio label').on('click', function () {
        $(this).addClass('active').siblings('label').removeClass('active')
    });

    $('.form-control').click(function () {
        $(this).siblings('label').addClass('clicked');
    });
    $('.form-control').keyup(function () {
        $(this).siblings('label').addClass('clicked');
    });
    $('.form-control').blur(function () {
        if ($(this).val()) {
            $(this).siblings('label').addClass('clicked');
        } else if (!$(this).val()) {
            $(this).siblings('label').removeClass('clicked');
        }
    });
    // $('.form-control:-webkit-autofill').each(function () {
    //     $(this).siblings('label').addClass('clicked');
    // });
    $('.form-group select').change(function () {
        if (this.value.length !== 0) {
            $(this).parent().parent().find('label').addClass('clicked');
        } else {
            $(this).parent().parent().find('label').removeClass('clicked');
        }
    });

    $(".search-input-btn").click(function () {
        var searchVal = $.trim($("#searchEvent").val());

        /* // removed condition to clear search condition and load all
        if(searchVal == '') {
            alert("Please enter text to search");
            $("#searchEvent").focus();
            return false;
        }*/

        var data = {};
        data['searchText'] = searchVal;

        jQuery.ajax({
            url: ajaxSearchCopyEventURL,
            data: data,
            type: "POST",
            dataType: "JSON",
            success: function (data) {

                var result = $("#eventListCopy");
                result.html('');
                result.html(data.html);
                $("#totalEventCount").html(data.count);
                AOS.init({
                    delay: 50,
                    duration: 800,
                });
            }
        });

    });

    $(document).on('click', '.project-status-input', function (e) {
        e.preventDefault();
        $(this).addClass('clicked');
        $(this).siblings('li').removeClass('clicked');
    });
    $('.edit-link').click(function () {
        $(this).siblings('input').prop("readonly", false).focus();
        // $(this).focus();

    });


    $('.event-resources li').click(function () {
        $(this).toggleClass('active');
        $('.steps').removeClass('event-ul');
        var resourceSelected = $(this).children('span').html().toLowerCase();
        console.log(resourceSelected)
        $(".steps").find('li.' + resourceSelected).show();
    });
    $('#selectProject').on('change', function () {
        $('.interest-selection').show();
    });
    $('.eventOption > li.createNew').on('click', function () {
        $(this).parents('.col-sm-12').siblings('.new-event').show();
    });
    $('.primary-area-pre > li').on('click', function () {
        $('.primary-area-pre > li').removeClass('active');
        $(this).addClass('active');
        // $(this).toggleClass('active');
        $(this).parents('.main-form-group').siblings('.second-selection').css('opacity', 1);
        $(this).parents('.main-form-group').siblings('.second-selection').find('select').removeAttr('disabled');
    });

    $("#selectSecondArea").change(function () {
        var selectSecondOption = $(this).children("option:selected").val();
        $('.selected-options').append('<li>' + selectSecondOption + '<i class="fc-close"> </i> </li>');
    });

    $(document).on('click', '.selected-options .fc-close', function (e) {
        e.preventDefault();
        $(this).parent('li').remove();
    });


    checkWordLen = function () {
        var wordLen = 300,
            len; // Maximum word length
        $('textarea').keydown(function (event) {
            len = $('textarea').val().split(/[\s]+/);
            if (len.length > wordLen) {
                if (event.keyCode == 46 || event.keyCode == 8 || event.which == 46 || event.which == 8) {// Allow backspace and delete buttons
                } else if (event.keyCode < 48 || event.keyCode > 57 || event.which < 48 || event.which > 57) {//all other buttons
                    event.preventDefault();
                }
            }
            //console.log(len.length + " words are typed out of an available " + wordLen);
            wordsLeft = (wordLen) - len.length;
            $(this).siblings('.textarea-instru').find('.words-left').replaceWith(wordsLeft);
        });
    };
    checkWordLenSmall = function () {
        var wordLen = 160,
            len; // Maximum word length
        $('textarea').keydown(function (event) {
            len = $('textarea').val().split(/[\s]+/);
            if (len.length > wordLen) {
                if (event.keyCode == 46 || event.keyCode == 8 || event.which == 46 || event.which == 8) {// Allow backspace and delete buttons
                } else if (event.keyCode < 48 || event.keyCode > 57 || event.which < 48 || event.which > 57) {//all other buttons
                    event.preventDefault();
                }
            }
            //console.log(len.length + " words are typed out of an available " + wordLen);
            wordsLeft = (wordLen) - len.length;
            $(this).siblings('.textarea-instru').find('.words-left').html(wordsLeft);
        });
    };

    $($('ul.steps > li').get().reverse()).each(function (index) {
        $(this).css('z-index', index + 10);
    });

});
$('.gc-gallery').click(function () {
    $(this).parents('.project-status').siblings('.gc-gallery-div').show();
    $(this).parents('.project-status').siblings('.my-gallery-div').hide();
    $(this).addClass('clicked');
    $(this).siblings().removeClass('clicked');
});
$('.my-gallery').click(function () {
    $(this).parents('.project-status').siblings('.my-gallery-div').show();
    $(this).parents('.project-status').siblings('.gc-gallery-div').hide();
    $(this).addClass('clicked');
    $(this).siblings().removeClass('clicked');
});
$('.input-images-1').imageUploader({
    label: 'Upload Profile Image'
});
$('.input-images-2').imageUploader({
    label: 'Upload Background Image'
});
$('.input-images-3').imageUploader({
    label: 'Upload Gallery Images',
    label2: 'Add More Images'
});