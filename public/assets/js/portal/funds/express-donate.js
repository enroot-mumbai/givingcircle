$(document).ready(function () {
    $('body').addClass('inner-pg express-donate');
    function formcontrol() {
        $(".form-control").filter(function () {
            if (this.value.length !== 0) {
                $(this).siblings('label').addClass('clicked');
            }
        });
    }

    $(".background-express").css('background-image', function () {
        var bg = ('url(' + $(this).data("image-src") + ')');
        return bg;
    });
    $(".express-donate-parallax").css('background-image', function () {
        var bg = ('url(' + $(this).data("image-src") + ')');
        return bg;
    });

    formcontrol();
    $('.form-group label').click(function () {
        $(this).addClass('clicked');
        $(this).siblings('input').focus();
        $(this).siblings('.cust-select').find('select').mouseenter();
    });
    $('.form-group .cust-select label').click(function () {
        $(this).stopPropagation()
    })

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
    // $('.social-share-btn').on('click', function (e) {
    $(document).on('click', '.social-share-btn', function (e) {
        e.preventDefault();
        $(this).children('.fa-share-alt').addClass('toggle');
        $(this).siblings('.social-media').slideToggle();
        $(this).parents('ul.share-view').siblings('.share-sec').find('.social-media').slideToggle();
    });
    // $('.share-link').on('click', function (e) {
    $(document).on('click', '.share-link', function (e) {
        e.preventDefault();
        $(this).siblings('.share-sec').find('.social-media').slideToggle();
    });
    // $('.fa-close').on('click', function (e) {
    $(document).on('click', '.fa-close', function (e) {
        e.preventDefault();
        $(this).parent('.social-media').slideToggle();
        $(this).parents('.share-sec').find('.fa-share-alt').removeClass('toggle');
    });
    /*$('.social-share-btn').on('click', function (e) {
        e.preventDefault();
        $(this).parents('ul.share-view').siblings('.share-sec').find('.social-media').slideToggle();
    });*/

    $($('.refine-search .categories li > ul.type > li .tooltip').get().reverse()).each(function (index) {
        $(this).css('z-index', index + 10);
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
    $("[name='categoryChkBox']").change(function () {
        getFilter();
    });

    $("[name='statusChkBox']").change(function () {
        getFilter();
    });
    $("[name='areaOfInterestChkBox']").change(function () {
        getFilter();
    });
    $("[name='searchCity']").change(function () {
        getFilter();
    });
    /*$(".search-input").change(function () {
        getFilter();
    });*/
    $(".search-input-btn").click(function () {
        getFilter();
    });

    $("#event-search-by").click(function () {
        getFilter();
    });

    $('.clearAll').on('click', function () {
        $('input[type=checkbox]').each(function () {
            $(this).prop('checked', false);
        });
        getFilter();
    });

});

function getFilter() {
    var categoryChkBox = [];
    var statusChkBox = [];
    var areaOfInterestChkBox = [];
    $.each($("input[name='categoryChkBox']:checked"), function(){
        categoryChkBox.push($(this).val());
    });
    $.each($("input[name='statusChkBox']:checked"), function(){
        statusChkBox.push($(this).val());
    });
    $.each($("input[name='areaOfInterestChkBox']:checked"), function(){
        areaOfInterestChkBox.push(parseInt($(this).val()));
    });
    var data = {};
    if(categoryChkBox.length > 0) {
        data['categoryChkBox'] = categoryChkBox;
    }
    if(statusChkBox.length > 0) {
        data['statusChkBox'] = statusChkBox;
    }
    if(areaOfInterestChkBox.length > 0) {
        data['areaOfInterestChkBox'] = areaOfInterestChkBox;
    }
    data['searchCity'] = $.trim($("#searchCity").val());
    data["searchText"] = $.trim($(".search-input").val());
    jQuery.ajax({
        url: ajaxExpressDonateListingURL,
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