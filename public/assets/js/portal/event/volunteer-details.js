$(document).ready(function () {
    $('body').addClass('inner-pg details-page event');
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
        $(".btn-participate").click(function () {
            closeAllParticipateButtons();

            // if close project event is there, these conditions will work
            if($(this).attr('sequence') == 'ReqPending') {
                alert('Your project join request is under process');
            } else if($(this).attr('sequence') == 'NJoined') {
                window.location.href = pathToParticipateBeforeLogin;
            } else if($(this).attr('sequence') == 'NLoggedIn') {
                window.location.href = pathToParticipateBeforeLogin;
            } else {
                $(this).toggleClass('disabled')
                $(this).parents('tr').siblings('.participateCalender.'+$(this).attr('sequence')).slideToggle();
            }
        });
        if(nowJoined == 'true') {
            $(".btn-participate").click();
        }
        $(".participateCal.btn-close").click(function () {
            $(this).parents('.participateCalender.'+$(this).attr('sequence')).slideToggle();
            $('.btn-participate.'+$(this).attr('sequence')).toggleClass('disabled');
        });
        $(".btn-continue").click(function () {
            var loop_index = $(this).attr('loop-index');
            if(validateParticipateRequestData(loop_index) ) {
                var dates =  $('[name="datepickerParticipate_'+loop_index+'"]').multiDatesPicker('getDates');
                if (dates.length >0 ) {
                    $("#selectedDateRange_"+loop_index).val(dates.join(','));
                }
                $("#frmParticipate_"+loop_index).submit();
            }
            $(this).parents('.participateCalender.'+loop_index).slideToggle();
            $('.btn-participate.'+loop_index).removeClass('disabled');
        });
        $(".datepicker").multiDatesPicker(
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

function validateParticipateRequestData(loop_index) {
    var dates =  $('[name="datepickerParticipate_'+loop_index+'"]').multiDatesPicker('getDates');
    if (dates.length == 0 ) {
        alert('Please select dates to participate on')
        return false;
    }
    if ($.trim($("#numberOfHours_"+loop_index).val()) == '') {
        alert('Please select number of hours you want to participate')
        return false;
    }
    if ($.trim($("#eventTime_"+loop_index).val()) == '') {
        alert('Please select event timing you want to participate')
        return false;
    }
    return true;
}
function closeAllParticipateButtons(){
    $(".btn-participate").removeClass('disabled')
    $(".participateCalender").hide();

}