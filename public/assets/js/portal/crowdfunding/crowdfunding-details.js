$(document).ready(function () {
    $('body').addClass('inner-pg details-page crowdFunding');
    $('#demoTab').easyResponsiveTabs({
        type: 'default', //Types: default, vertical, accordion
    });

    $('#trn_crowd_fund_event_offline_transfer').captcha({
        'idCaptchaInput': 'captcha',
        'idCaptchaText': 'captchaCode'
    });

    $(".owl-carousel").owlCarousel({
        loop: false,
        items: 1,
        nav: true,
        navText: [
            "<i class='fc-arrow-left'></i>",
            "<i class='fc-arrow-right'></i>"
        ],
        margin: 0,
        stagePadding: 0,
        autoplay: false
    });

    dotcount = 1;

    jQuery('.owl-dot').each(function () {
        jQuery(this).addClass('dotnumber' + dotcount);
        jQuery(this).attr('data-info', dotcount);
        dotcount = dotcount + 1;
    });

    slidecount = 1;

    jQuery('.owl-item').not('.cloned').each(function () {
        jQuery(this).addClass('slidenumber' + slidecount);
        slidecount = slidecount + 1;
    });

    jQuery('.owl-dot').each(function () {
        grab = jQuery(this).data('info');

        if(jQuery('.slidenumber' + grab + ' img').length > 0) {
            slidegrab = jQuery('.slidenumber' + grab + ' img').attr('src');
        } else {
            // slidegrab = jQuery('.slidenumber' + grab + ' embed').attr('src');
            slidegrab = jQuery('.slidenumber' + grab + ' embed').attr('buttonImg');
            jQuery(this).css("background-color", "#fff");
            // jQuery(this).css("background-image", 'repeating-radial-gradient(blue, yellow 10%, green 20%)');
        }
        jQuery(this).css("background-image", "url(" + slidegrab + ")");

    });

    amount = $('.owl-dot').length;
    gotowidth = 100 / amount;
    jQuery('.owl-dot').css("height", gotowidth + "%");

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
    $(".price-block li a").click(function (e) {
        e.preventDefault();
        $(".price-block li a").removeClass('active');
        $(this).addClass('active');
    });
    $(".donateShareBlock .btn-primary").click(function (e) {
        $(this).addClass('disabled');
        $(this).parents('.donateShareBlock').find('.contributeBox').slideToggle();
    });
    $(".readPreUpdate").click(function (e) {
        $(this).hide();
        $(this).parents('.content-block').find('.remaining').show();
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
    $('.form-control:-webkit-autofill').each(function () {
        $(this).siblings('label').addClass('clicked');
    });
    $('.form-group select').change(function () {
        if (this.value.length !== 0) {
            $(this).parent().parent().find('label').addClass('clicked');
        } else {
            $(this).parent().parent().find('label').removeClass('clicked');
        }
    });
    $(".form-group select").change();

    $(".btn-contribute").click(function (){
        if ($.trim($(this).parent().siblings().val()) == '' || isNaN($.trim($(this).parent().siblings().val()))) {
            alert('Please enter valid amount.');
            return false;
        }
        if (minimumContributionAmount > parseFloat($(this).parent().siblings().val()) ) {
            alert("Please enter amount greater then equal to Minimum Contribution Amount " +
                "("+minimumContributionAmount+") Rupees ");
            $(this).parent().siblings().val('');
            $(this).parent().siblings().focus();
            return false;
        }
        $("#amountToContribute").val(parseFloat($(this).parent().siblings().val()));
        $("#frmContribute").submit();
    });

    $("#allSupportersAnch").click(function (){
        $('.loading').show();
        $.ajax({
            url: crowdFundingGetAllSupporters,
            type: 'POST',
            dataType: "html",
            success: (function (formdata) {
                $('.loading').hide();
                var product = $(".topDonors");
                product.html(formdata);

            }),
        });
    });

    $(document).on('click','.circleLike',function(){
        var data = {};
        var circleId = data['id'] = $(this).attr("circleId");
        jQuery.ajax({
            url: updateCircleLikeCountUrl,
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $(".circleLike_"+circleId).each(function () {
                    $(this).text(data.count);
                });
            }
        });
    });
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


    $("#trn_crowd_fund_event_offline_transfer").submit(function(event) {
        /* stop form from submitting normally */
        event.preventDefault();
        saveOfflineTransfer();
    });

});

function saveOfflineTransfer() {



    if($.trim($("#trn_crowd_fund_event_offline_transfer_bankTransactionId").val()) == '') {
        alert("Please enter bank transaction Id");
        $("#trn_crowd_fund_event_offline_transfer_bankTransactionId").focus();
        return false;
    }
    if($.trim($("#trn_crowd_fund_event_offline_transfer_amountDonated").val()) == '') {
        alert("Please enter Amount Donated");
        $("#trn_crowd_fund_event_offline_transfer_amountDonated").focus();
        return false;
    }
    if($.isNumeric($("#trn_crowd_fund_event_offline_transfer_amountDonated").val()) === false) {
        alert("Please enter Numeric valud for Amount Donated");
        $("#trn_crowd_fund_event_offline_transfer_amountDonated").focus();
        return false;
    }
    if($.trim($("#trn_crowd_fund_event_offline_transfer_firstName").val()) == '') {
        alert("Please enter First Name");
        $("#trn_crowd_fund_event_offline_transfer_firstName").focus();
        return false;
    }
    if($.trim($("#trn_crowd_fund_event_offline_transfer_lastName").val()) == '') {
        alert("Please enter Last Name");
        $("#trn_crowd_fund_event_offline_transfer_lastName").focus();
        return false;
    }
    if($.trim($("#trn_crowd_fund_event_offline_transfer_email").val()) == '') {
        alert("Please enter Email");
        $("#trn_crowd_fund_event_offline_transfer_email").focus();
        return false;
    }

    var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(!regex.test($.trim($("#trn_crowd_fund_event_offline_transfer_email").val()))) {
        alert('Please enter valid email format');
        $("#trn_crowd_fund_event_offline_transfer_email").focus();
        return false;
    }
    var mobileNo = $.trim($("#trn_crowd_fund_event_offline_transfer_mobileNumber").val());
    if(mobileNo == '') {
        alert("Please enter Mobile Number");
        $("#trn_crowd_fund_event_offline_transfer_mobileNumber").focus();
        return false;
    }
    if($.isNumeric(mobileNo) === false || mobileNo.length != 10) {
        alert("Please enter Valid Mobile Number");
        $("#trn_crowd_fund_event_offline_transfer_mobileNumber").focus();
        return false;
    }

    var data = $("#trn_crowd_fund_event_offline_transfer").serialize();

    $.ajax({
        url: addOfflineTransferDetails,
        data: data,
        type: "POST",
        dataType: "JSON",
        complete: function (data) {

            alert('Thank you for the acknowledgement.');
            $("#trn_crowd_fund_event_offline_transfer").trigger("reset");
            $('.btn-close').click();
        }
    });

}