var nNotificationId = null;
var fromDate = null;
var toDate   = null;

$(document).ready(function () {
    $('body').addClass('inner-pg logged my-account static');
    $('.social-share-btn').on('click', function (e) {
        e.preventDefault();
        $(this).children('.fa-share-alt').addClass('toggle');
        $(this).siblings('.social-media').slideToggle();
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
    $('.social-share-btn').on('click', function (e) {
        e.preventDefault();
        $(this).parents('ul.share-view').siblings('.share-sec').find('.social-media').slideToggle();
    });

    $($('.refine-search .categories li > ul.type > li .tooltip').get().reverse()).each(function (index) {
        $(this).css('z-index', index + 10);
    });

    $('input[name="daterange"]').daterangepicker({
        opens: 'right',
        autoClose: true,
        locale: {
            format: 'DD/MM/YYYY'
        }
    }, function(start, end, label) {
        var data = {};
        fromDate = start.format('YYYY-MM-DD');
        toDate = end.format('YYYY-MM-DD');
        searchNotifications(data);
    });

    $(document).on('click', '[name="liNotification"]', function (e){
        var data = {};
        nNotificationId = data['nNotificationId'] = $(this).attr('notificationid');
        jQuery.ajax({
            url: markNotificationAsReadURL,
            data: data,
            type: "POST",
            dataType: "JSON",
            success: function (data) {
                var condition = {};
                searchNotifications(condition)
            }
        });
    });
    $("#spanUnreadNotification").html($(".selected").length);

    $("[name='mstNotificationStatus']").change(function (){
        var data = {};
        searchNotifications(data);
    });

    $(".search-input-btn").click(function (){
        var data = {};
        searchNotifications(data);
    });

    $("[name='projects_event']").change(function (){
        var data = {};
        searchNotifications(data);
    });

    $("[name='mstEventProductType']").change(function (){
        var data = {};
        searchNotifications(data);
    });

    $(".clearAll").click(function (){
        $('input[name="mstNotificationStatus"]:checked').prop('checked', false);
        $.each($("input[name='projects_event']:checked"), function(){
            $(this).prop('checked', false);
        });
        $.each($("input[name='mstEventProductType']:checked"), function(){
            $(this).prop('checked', false);
        });
        $("#quicksearch").val('');
        toDate = fromDate = null;
        var data = {};
        var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        $('input[name="daterange"]').data('daterangepicker').setStartDate(today);
        $('input[name="daterange"]').data('daterangepicker').setEndDate(today);
        searchNotifications(data);
    });

});

function searchNotifications(data) {

    if( fromDate != null)
        data['from'] = fromDate;
    if( fromDate != null)
        data['to'] = toDate;

    if($('input[name="mstNotificationStatus"]:checked').length > 0) {
        data['mstNotificationStatus'] =  $('input[name="mstNotificationStatus"]:checked').val();
    }
    var projectEvents = new Array();
    $.each($("input[name='projects_event']:checked"), function(){
        projectEvents.push($(this).val());
    });
    data['projects_event'] = projectEvents;

    var mstEventProductType = new Array();
    $.each($("input[name='mstEventProductType']:checked"), function(){
        mstEventProductType.push($(this).val());
    });
    data['mstEventProductType'] = mstEventProductType;

    if ($.trim($("#quicksearch").val()) != '') {
        data['quicksearch'] = $.trim($("#quicksearch").val());
    }

    jQuery.ajax({
        url: ajaxViewAllNotificationsURL,
        data: data,
        type: "POST",
        dataType: "html",
        success: function (data) {
            var result = $(".notification-listing");
            result.html('');
            result.html(data);
            AOS.init({
                delay: 50,
                duration: 800,
            });
            if(nNotificationId != null){
                $(".badge-primary").html($('[name="liNotification"]').length);
                $("#liNotification_h_"+nNotificationId).remove();
                $("#spanUnreadNotification").html($(".selected").length);
            }
            nNotificationId = null;
        }
    });
}