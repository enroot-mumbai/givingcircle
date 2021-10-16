$(document).ready(function () {
    $('body').addClass('inner-pg');

    $(".background-express").css('background-image', function () {
        var bg = ('url(' + $(this).data("image-src") + ')');
        return bg;
    });
    $(".express-donate-parallax").css('background-image', function () {
        var bg = ('url(' + $(this).data("image-src") + ')');
        return bg;
    });

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

    var windowWidth = $(document).width();
    if(windowWidth > 990){
        $($('.refine-search .categories li > ul.type > li .tooltip').get().reverse()).each(function (index) {
            $(this).css('z-index', index + 10);
        });

        $('.labelText').each(function(item){
            $(this).parent('label').siblings('.tooltip').css('left', $(this).width() + 35);
        });
    } 
    if(windowWidth < 450){
        $($('.refine-search .categories li > ul.type > li .tooltip').get().reverse()).each(function (index) {
            $(this).css('z-index', index + 10);
        });
    };

    $('input[name="daterange"]').daterangepicker({
        opens: 'right',
        autoClose: true,
        locale: {
            format: 'DD/MM/YYYY'
        }
    });

    $("[name='categoryChkBox']").change(function () {
        getFilter();
    });

    $("[name='eventTime']").change(function () {
        getFilter();
    });

    $("[name='joinBy']").change(function () {
        getFilter();
    });
    $("[name='areaOfInterestChkBox']").change(function () {
        getFilter();
    });
    $(".search-input-btn").click(function () {
        getFilter();
    });
    $('.clearAll').on('click', function () {
        $('input[type=checkbox]').each(function () {
            $(this).prop('checked', false);
        });
        getFilter();
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
                $("#eventLike_"+eventId).text(data.count);
            }
        });
    });

});

function getFilter() {
    var categoryChkBox = [];
    var joinBy = [];
    var areaOfInterestChkBox = [];
    var eventTime = [];
    $.each($("input[name='categoryChkBox']:checked"), function(){
        categoryChkBox.push($(this).val());
    });
    $.each($("input[name='eventTime']:checked"), function(){
        eventTime.push($(this).val());
    });
    $.each($("input[name='joinBy']:checked"), function(){
        joinBy.push($(this).val());
    });
    $.each($("input[name='areaOfInterestChkBox']:checked"), function(){
        areaOfInterestChkBox.push(parseInt($(this).val()));
    });
    var data = {};
    if(categoryChkBox.length > 0) {
        data['categoryChkBox'] = categoryChkBox;
    }
    if(eventTime.length > 0) {
        data['eventTime'] = eventTime;
    }
    if(joinBy.length > 0) {
        data['joinBy'] = joinBy;
    }
    if(areaOfInterestChkBox.length > 0) {
        data['areaOfInterestChkBox'] = areaOfInterestChkBox;
    }
    data['searchCity'] = $.trim($("#searchCity").val());
    data["searchText"] = $.trim($(".search-input").val());
    jQuery.ajax({
        url: ajaxEventListingURL,
        data: data,
        type: "POST",
        dataType: "json",
        success: function (data) {
            var result = $(".event-listing");
            result.html('');
            result.html(data.html);
            $(".eventCount").html(data.count);
            AOS.init({
                delay: 50,
                duration: 800,
            });
        }
    });
}