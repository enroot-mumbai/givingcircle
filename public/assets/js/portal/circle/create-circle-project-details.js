$(document).ready(function () {
    $('body').addClass('inner-pg');
    $('body').addClass('create-element');
    $('body').addClass('logged');
    $('html, body').animate(
        {
            scrollTop: $($('.next-section-arrow').attr('href')).offset().top - 95,
        },
        800,
        'linear'
    );
    $(".back-link").click(function (){

    });

    $(".btn-continue").click(function (){
        var trn_circle_portal_mstJoinBy = $.trim($("#trn_circle_portal_mstJoinBy").val());
        if (trn_circle_portal_mstJoinBy == '') {
            alert('Please select My Project Type');
            $("#trn_circle_portal_circle").focus();
            return false;
        }

        var len = $("#trn_circle_portal_circleInformation").val().split(/[\s]+/);
        if (len.length > 300) {
            alert('About Me: Maximum 300 words allowed');
            $("#trn_circle_portal_circleInformation").focus();
            return false;
        }

        var len2 = $("#trn_circle_portal_howGoalWillBeAchieved").val().split(/[\s]+/);
        if (len2.length > 300) {
            alert('My Goals & Plans: Maximum 300 words allowed');
            $("#trn_circle_portal_howGoalWillBeAchieved").focus();
            return false;
        }

        if($.trim(('#trn_circle_portal_impactStatement').val()) != '') {
            var len3 = $("#trn_circle_portal_impactStatement").val().split(/[\s]+/);
            if (len3.length > 300) {
                alert('Additional Info: Maximum 300 words allowed');
                $("#trn_circle_portal_impactStatement").focus();
                return false;
            }
        }

        if($.trim(('#trn_circle_portal_suggestedKeywords').val()) != '') {
            var len3 = $("#trn_circle_portal_suggestedKeywords").val().split(/[\s]+/);
            if (len3.length > 160) {
                alert('Suggested keywords: Maximum 160 words allowed');
                $("#trn_circle_portal_suggestedKeywords").focus();
                return false;
            }
        }

        $('#trn_circle_portal').submit();
    });
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

    $(document).on('click', '.project-status-input', function (e) {
        e.preventDefault();
        $("#trn_circle_portal_mstJoinBy").val($(this).attr('value'));
        $(this).addClass('clicked');
        $(this).siblings('li').removeClass('clicked');
    });
    $('.edit-link').click(function () {
        $(this).siblings('input').prop("readonly", false).focus();
        // $(this).focus();

    });
    $(".longtextarea").keyup(function (){
        wordCount($(this), 300);
    });
    $(".textarea").keyup(function (){
        wordCount($(this), 160);
    });

    $(".longtextarea").keydown(function (){
        wordCount($(this), 300);
    });
    $(".textarea").keydown(function (){
        wordCount($(this), 160);
    });

    $(".longtextarea").blur(function (){
        wordCount($(this), 300);
    });
    $(".textarea").blur(function (){
        wordCount($(this), 160);
    });

    jQuery("#trn_circle_portal_mstCountry").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/portal/location/state_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#trn_circle_portal_mstState");
                product.html('');
                product.append('<option value="" ></option>');
                // add options
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
    jQuery("#trn_circle_portal_mstState").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/portal/location/city_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#trn_circle_portal_mstCity");
                product.html('');
                product.append('<option value="" ></option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });

    if (projectType != '') {
        switch (projectType.toLowerCase()) {
            case 'open':{
                $("#project-status-input-Open").trigger('click');
                break
            }
            case 'closed':{
                $("#project-status-input-Closed").trigger('click');
                break;
            }
        }
    }

});