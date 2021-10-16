$(document).ready(function () {
    $('body').addClass('inner-pg details-page event');
    $('.owl-carousel').owlCarousel({
        loop: false,
        margin: 0,
        nav: true,
        dots: false,
        navText: [
            "<i class='fas fa-arrow-left'></i>",
            "<i class='fas fa-arrow-right'></i>"
        ],
        autoplay: false,
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 1
            },
            1000: {
                items: 2
            }
        }
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
        $(this).siblings('input').focus();
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
    $('.form-group select').change(function () {
        if (this.value.length !== 0) {
            $(this).parent().parent().find('label').addClass('clicked');
        } else {
            $(this).parent().parent().find('label').removeClass('clicked');
        }
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

    $(function () {
        var eventDates = {};
        eventDates[new Date('01/02/2021')] = new Date('01/02/2021');
        eventDates[new Date('01/09/2021')] = new Date('01/09/2021');
        eventDates[new Date('01/08/2021')] = new Date('01/08/2021');
        eventDates[new Date('01/16/2021')] = new Date('01/16/2021');
        eventDates[new Date('01/15/2021')] = new Date('01/15/2021');
        eventDates[new Date('01/23/2021')] = new Date('01/23/2021');
        eventDates[new Date('01/22/2021')] = new Date('01/22/2021');
        eventDates[new Date('01/30/2021')] = new Date('01/30/2021');
        eventDates[new Date('01/29/2021')] = new Date('01/29/2021');


        $('#datepicker').multiDatesPicker(
            {
                beforeShowDay: function (date) {
                    var highlight = eventDates[date];
                    if (highlight) {
                        return [true, "eventHighlight", 'Tooltip text'];
                    } else {
                        return [true, '', ''];
                    }
                },
                dayNamesMin: ["S", "M", "T", "W", "T", "F", "S"]
            }
        );
        $(".btn-contribute").click(function () {
            // closeAllOtherContributeButtons();

            // if close project event is there, these conditions will work
            if($(this).attr('sequence') == 'ReqPending') {
                alert('Your project join request is under process');
            } else if($(this).attr('sequence') == 'NJoined') {
                window.location.href = pathToParticipateBeforeLogin;
            } else if($(this).attr('sequence') == 'NLoggedIn') {
                window.location.href = pathToParticipateBeforeLogin;
            } else {
                $(this).toggleClass('disabled')
                $(this).siblings('.contributeBox').slideToggle();
            }
        });
        if(nowJoined == 'true') {
            $(".btn-contribute").click();
        }
        $(".contributeBox .enter-amount .btn-close").click(function () {
            $(this).parents('.contributeBox').slideToggle();
            $('.btn-contribute').toggleClass('disabled');
        });
        $(".btn-contribute.disabled").click(function () {
            closeAllOtherContributeButtons();
        });
        $(".btn-enter").click(function () {
            $(this).parents('.contributeBox').slideToggle();
            $(this).parents('.actionBlock').find('.btn-contribute').removeClass('disabled')
        });
        // $(document).on('click', '.eventHighlight a', function (e) {
        //     alert("hihih")
        //     $(this).toggleClass('active')
        // })

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
});

function contributeFunds(nTrnCircleEventsId) {

    var amount =  $.trim($("#enterAmount").val());
    if (amount == "") {
        alert("Please enter amount for contribution");
        return  false;
    }
    if (isNaN(amount)) {
        alert("Please enter valid amount for contribution");
        return false;
    }
    amount = parseFloat(amount);
    /*
    var min_amount = parseFloat($("#enterAmount_"+nTrnFundRaiserCircleEventSubEventId).attr('min-amount'));
    if (amount < min_amount ){
        $("#enterAmount_"+nTrnFundRaiserCircleEventSubEventId).val('');
        alert("Please enter amount greater then equal to Amount Required.");
        return false;
    }
    */
    $("#amountToContribute").val(amount);
    // $("#subEventId").val(nTrnFundRaiserCircleEventSubEventId);
    $("#eventId").val(nTrnCircleEventsId);
    $("#frmContribute").submit();
}

function closeAllOtherContributeButtons(){
    $(".btn-contribute").removeClass('disabled')
    $(".contributeBox").hide();

}