$(document).ready(function () {
    $('body').addClass('inner-pg logged my-account');
    $('.cust-tabs').addClass('personal-info');
    $("[name='app_user_my_account_about']").attr('enctype', "multipart/form-data");
    $('[name="app_user_my_account_about"]').addClass('form');
    $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: "-100:+0",
        onSelect: function(dateText, inst) {
            $(".datepicker").parent().siblings().addClass('clicked');
        }
    });
    $('.input-images-3').imageUploader({
        label: 'Upload Documentation',
        label2: 'Add More Images',
        imagesInputName: 'imageDocGallery[]',
        extensions: ['.jpg', '.jpeg', '.png'],
        mimes: ['image/jpeg', 'image/png'],
        maxSize: 5248000,
        preloaded: preloadedGallery
    });
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').css('background-image', 'url('+e.target.result +')');
                $('#imagePreview').hide();
                $('#imagePreview').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#imageUpload").change(function() {
        //alert('profile image');
        $(this).parents('.avatar-edit').find('.avatar-preview').addClass('imageadded');
        readURL(this);
        $("#frmUploadProfilePic").submit();
    });

    if(profilePic != '') {
        $('#imagePreview').css('background-image', 'url('+profilePic +')');
        $('#imagePreview').hide();
        $('#imagePreview').fadeIn(650);
        $(".fc-user-x2").hide();
    }

    if(backGroundPic != '') {
        $('#imagePreview2').css('background-image', 'url('+backGroundPic +')');
        $('#imagePreview2').hide();
        $('#imagePreview2').fadeIn(650);
        $(".profile-bg").hide()
    }

    function bannerImg(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview2').css('background-image', 'url('+e.target.result +')');
                $('#imagePreview2').hide();
                $('#imagePreview2').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#imageUpload2").change(function() {
        //alert('banner image');
        $(this).parents('.banner-image').find('.banner-preview').addClass('imageadded');
        $(this).parents().find('.profile-bg').hide();
        bannerImg(this);
        $("#frmUploadBackgroundPic").submit();
    });


    function formcontrol(){
        $(".form-control").filter(function() {
            if (this.value.length !==0){
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
    $(".btn-continue").click(function (){
        removeErrorDivs();
        if(validate()) {
            $("[name='app_user_my_account_about']").submit();
        }
    });

    $("[name='hasDisability']").change(function (){
        if ($(this).val() == 'Yes')
            $("#app_user_my_account_about_trnVolunterDetail_hasDisability").prop('checked', true);
        else
            $("#app_user_my_account_about_trnVolunterDetail_hasDisability").prop('checked', false);
    });

    $("[name='isWillingToHelpInDisaster']").change(function (){
        if ($(this).val() == 'Yes')
            $("#app_user_my_account_about_trnVolunterDetail_isWillingToHelpInDisaster").prop('checked', true);
        else
            $("#app_user_my_account_about_trnVolunterDetail_isWillingToHelpInDisaster").prop('checked', false);
    });

    $("[name='customCheckboxInline']").change(function (){
        $.each($("[name='customCheckboxInline']"), function(){
            val = $(this).val()
            $("#app_user_my_account_about_trnVolunterDetail_mstSourceOfInformation " +
                "option[value='"+val+"']").attr("selected", false);
        });
        $.each($("[name='customCheckboxInline']"), function(){
            if($(this).is(':checked')){
                val = $(this).val()
                $("#app_user_my_account_about_trnVolunterDetail_mstSourceOfInformation " +
                    "option[value='"+val+"']").attr("selected", "selected");
            }
        });
    });

    $("[name='morningTime']").click(function (){
        $(this).toggleClass('active');
        updateMorningSlots();
    });

    $("[name='afternoonTime']").click(function (){
        $(this).toggleClass('active');
        updateAfternoonSlots();
    });

    $("[name='eveningTime']").click(function (){
        $(this).toggleClass('active');
        updateEveningSlots();
    });

    $("#allDayMorning").change(function (){
        if ($(this).is(':checked')) {
            activateInActivateAllMorningSlots(true);
        } else {
            activateInActivateAllMorningSlots(false);
        }
        updateMorningSlots();
    });

    $("#allDayAfternoon").change(function (){
        if ($(this).is(':checked')) {
            activateInActivateAllAfternoonSlots(true);
        } else {
            activateInActivateAllAfternoonSlots(false);
        }
        updateAfternoonSlots();
    });

    $("#allDayEvening").change(function (){
        if ($(this).is(':checked')) {
            activateInActivateAllEveningSlots(true);
        } else {
            activateInActivateAllEveningSlots(false);
        }
        updateEveningSlots();
    });

    if(arrMorningAvailability.length >0 ) {
        $.each($("[name=\"morningTime\"]"), function(){
            if ($.inArray($(this).attr('value'), arrMorningAvailability) != -1) {
                $(this).addClass('active');
            }
        });
        updateMorningSlots();
    }
    if(arrAfternoonAvailability.length >0 ) {
        $.each($("[name=\"afternoonTime\"]"), function(){
            if ($.inArray($(this).attr('value'), arrAfternoonAvailability) != -1) {
                $(this).addClass('active');
            }
        });
        updateAfternoonSlots();
    }
    if(arrEveningAvailability.length >0 ) {
        $.each($("[name=\"eveningTime\"]"), function(){
            if ($.inArray($(this).attr('value'), arrEveningAvailability) != -1) {
                $(this).addClass('active');
            }
        });
        updateEveningSlots();
    }

    $("#tnc").change(function (){
        if($(this).is(':checked')) {
            $("#app_user_my_account_about_appUserInfo_isSubscribedToNewLetter").prop('checked', true);
        } else {
            $("#app_user_my_account_about_appUserInfo_isSubscribedToNewLetter").prop('checked', false);
        }
    });
    for(i=0; i< fieldForReadOnly.length; i++) {
        $("#"+fieldForReadOnly[i]).removeAttr('readonly');
    }
    for(i=0; i< fieldAddForReadOnly.length; i++) {
        $("#"+fieldAddForReadOnly[i]).attr('readonly','readonly');
    }

    $("#app_user_my_account_about_appUserInfo_mstCountry").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        $.ajax({
            url: "/portal/location/state_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#app_user_my_account_about_appUserInfo_mstState");
                product.html('');
                product.append('<option value="" ></option>');
                // add options
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
    $("#app_user_my_account_about_appUserInfo_mstState").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        $.ajax({
            url: "/portal/location/city_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#app_user_my_account_about_appUserInfo_mstCity");
                product.html('');
                product.append('<option value="" ></option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });

    $("#app_user_my_account_about_appUserInfo_userMobileNumber").change(function () {
        checkIsMobileNumberUnique();
    });

    $("#app_user_my_account_about_appUserInfo_pancardNumber").change(function ()    {
        var data = {};
        data['q'] = $(this).val();
        jQuery.ajax({
            url: "/core/pancard/check_unqiue",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                if(data.unique == true) {} else {
                    alert("Entered Pan Card already exists in the system");
                    $("#app_user_my_account_about_appUserInfo_pancardNumber").val('');
                }
            }
        });
    });
});

function validate() {
    var bError = false;
    if ($.trim($("#app_user_my_account_about_appUserInfo_mstSalutation").val()) == '') {
        $("#app_user_my_account_about_appUserInfo_mstSalutation").focus();
        $('<div class="errorDiv" style="color: #f00;">Please select.</div>').insertAfter($("#app_user_my_account_about_appUserInfo_mstSalutation"));
        bError = true;
    }
    if ($.trim($("#app_user_my_account_about_appUserInfo_address1").val()) == '') {
        $("#app_user_my_account_about_appUserInfo_address1").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter.</div>').insertAfter($("#app_user_my_account_about_appUserInfo_address1"));
        bError = true;
    }
    if ($.trim($("#app_user_my_account_about_appUserInfo_mstCountry").val()) == '') {
        $("#app_user_my_account_about_appUserInfo_mstCountry").focus();
        $('<div class="errorDiv" style="color: #f00;">Please select country.</div>').insertAfter($("#app_user_my_account_about_appUserInfo_mstCountry"));
        bError = true;
    }
    if ($.trim($("#app_user_my_account_about_appUserInfo_mstState").val()) == '') {
        $("#app_user_my_account_about_appUserInfo_mstState").focus();
        $('<div class="errorDiv" style="color: #f00;">Please select state.</div>').insertAfter($("#app_user_my_account_about_appUserInfo_mstState"));
        bError = true;
    }
    if ($.trim($("#app_user_my_account_about_appUserInfo_mstCity").val()) == '') {
        $("#app_user_my_account_about_appUserInfo_mstCity").focus();
        $('<div class="errorDiv" style="color: #f00;">Please select city.</div>').insertAfter($("#app_user_my_account_about_appUserInfo_mstCity"));
        bError = true;
    }
    if ($.trim($("#app_user_my_account_about_appUserInfo_pinCode").val()) == '') {
        $("#app_user_my_account_about_appUserInfo_pinCode").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter valid pin code.</div>').insertAfter($("#app_user_my_account_about_appUserInfo_pinCode"));
        bError = true;
    }
    /*if ($.trim($("#app_user_my_account_about_appUserInfo_pancardNumber").val()) == '') {
        $("#app_user_my_account_about_appUserInfo_pancardNumber").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter valid pan card number.</div>').insertAfter($("#app_user_my_account_about_appUserInfo_pancardNumber"));
        bError = true;
    }*/

    panNum = $.trim($("#app_user_my_account_about_appUserInfo_pancardNumber").val());
    if (panNum != '' ) {
        if( !validatePAN(panNum)) {
            $("#app_user_my_account_about_appUserInfo_pancardNumber").val('');
            $("#app_user_my_account_about_appUserInfo_pancardNumber").focus();
            $('<div class="errorDiv" style="color: #f00;">Please enter a valid Pan Card Number.</div>').insertAfter($("#app_user_my_account_about_appUserInfo_pancardNumber"));
            bError = true;
        }
    }

    return !bError;
}

function activateInActivateAllMorningSlots(activate) {
    if(activate == true) {
        $.each($("[name=\"morningTime\"]"), function(){
            $(this).addClass('active');
        });
    } else {
        $.each($("[name=\"morningTime\"]"), function(){
            $(this).removeClass('active');
        });
    }
}

function activateInActivateAllAfternoonSlots(activate) {
    if(activate == true) {
        $.each($("[name=\"afternoonTime\"]"), function(){
            $(this).addClass('active');
        });
    } else {
        $.each($("[name=\"afternoonTime\"]"), function(){
            $(this).removeClass('active');
        });
    }

}

function activateInActivateAllEveningSlots(activate) {
    if(activate == true) {
        $.each($("[name=\"eveningTime\"]"), function(){
            $(this).addClass('active');
        });
    } else {
        $.each($("[name=\"eveningTime\"]"), function(){
            $(this).removeClass('active');
        });
    }
}

function updateMorningSlots() {
    var days = new Array();
    $.each($("[name=\"morningTime\"]"), function(){
        if($(this).hasClass('active')) {
            days.push($(this).attr('value'));
        }
    });
    if (days.length == 7) {
        $("#allDayMorning").prop('checked', true);
    } else {
        $("#allDayMorning").prop('checked', false);
    }
    $("#hdnAvailabilityInMorning").val(days.join(','));
}

function updateAfternoonSlots() {
    var days = new Array();
    $.each($("[name=\"afternoonTime\"]"), function(){
        if($(this).hasClass('active')) {
            days.push($(this).attr('value'));
        }
    });
    if (days.length == 7) {
        $("#allDayAfternoon").prop('checked', true);
    } else {
        $("#allDayAfternoon").prop('checked', false);
    }
    $("#hdnAvailabilityInAfternoon").val(days.join(','));
}

function updateEveningSlots() {
    var days = new Array();
    $.each($("[name=\"eveningTime\"]"), function(){
        if($(this).hasClass('active')) {
            days.push($(this).attr('value'));
        }
    });
    if (days.length == 7) {
        $("#allDayEvening").prop('checked', true);
    } else {
        $("#allDayEvening").prop('checked', false);
    }
    $("#hdnAvailabilityInEvening").val(days.join(','));
}

function checkIsMobileNumberUnique(){
    var data = {};
    mobileNo = data['mobileNo'] = $.trim($("#app_user_my_account_about_appUserInfo_userMobileNumber").val
    ());
    if ($.isNumeric(mobileNo) == false) {
        alert("Please enter valid mobile Number.");
        $("#app_user_my_account_about_appUserInfo_userMobileNumber").val('');
        return false;
    }

    if (mobileNo.length != 10) {
        alert("Please enter valid mobile Number.");
        $("#app_user_my_account_about_appUserInfo_userMobileNumber").val('');
        return false;
    }

    countryCode = data['countryCode'] = '91'; //$.trim(jQuery
    //"#app_user_portal_registration_appUserInfo_mobileCountryCode").val());
    jQuery.ajax({
        url: "/portal/user_mob/check_unique",
        data: data,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            if(data.unique == false) {
                alert("User already exist with specified mobile number.");
                $("#app_user_my_account_about_appUserInfo_userMobileNumber").val('');
            }
        }
    });
}

function validatePAN(pan){
    var regex = /[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
    return regex.test(pan);
}
function removeErrorDivs() {
    $.each($(".errorDiv"), function(){
        $(this).remove();
    });
}