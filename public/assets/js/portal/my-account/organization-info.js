$(document).ready(function () {
    $('body').addClass('inner-pg logged my-account');
    $('.cust-tabs').addClass('personal-info');
    $("[name='app_user_organization_my_account_about']").attr('enctype', "multipart/form-data");
    $('[name="app_user_organization_my_account_about"]').addClass('form');
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
    checkWordLen = function () {
        var wordLen = 300,
            len; // Maximum word length
        $('textarea').keydown(function (event) {
            len = $('textarea').val().split(/[\s]+/);
            if (len.length > wordLen) {
                if (event.keyCode == 46 || event.keyCode == 8) {// Allow backspace and delete buttons
                } else if (event.keyCode < 48 || event.keyCode > 57) {//all other buttons
                    event.preventDefault();
                }
            }
            //console.log(len.length + " words are typed out of an available " + wordLen);
            wordsLeft = (wordLen) - len.length;
            $(this).siblings('.textarea-instru').find('.words-left').replaceWith('<b class="words-left">' + wordsLeft + '</b>');
        });
    };
    $('.input-images-3').imageUploader({
        label: 'Upload Documentation',
        label2: 'Add More Images',
        imagesInputName: 'imageDocGallery[]',
        extensions: ['.jpg', '.jpeg', '.png'],
        mimes: ['image/jpeg', 'image/png'],
        maxSize: 5248000,
        preloaded: preloadedGallery
    });
    $('.input-images-1').imageUploader({
        label: 'Upload Company Logo',
        imagesInputName: 'organizationLogo',
        extensions: ['.jpg', '.jpeg', '.png'],
        mimes: ['image/jpeg', 'image/png'],
        maxSize: 5248000,
        preloaded: preloadedLogo
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
            $("[name='app_user_organization_my_account_about']").submit();
        }
    });


    $("#tnc").change(function (){
        if($(this).is(':checked')) {
            $("#app_user_organization_my_account_about_appUserInfo_isSubscribedToNewLetter").prop('checked', true);
        } else {
            $("#app_user_organization_my_account_about_appUserInfo_isSubscribedToNewLetter").prop('checked', false);
        }
    });
    for(i=0; i< fieldForReadOnly.length; i++) {
        $("#"+fieldForReadOnly[i]).removeAttr('readonly');
    }
    for(i=0; i< fieldAddForReadOnly.length; i++) {
        $("#"+fieldAddForReadOnly[i]).attr('readonly','readonly');
    }

    $("#app_user_organization_my_account_about_appUserInfo_mstCountry").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        $.ajax({
            url: "/portal/location/state_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#app_user_organization_my_account_about_appUserInfo_mstState");
                product.html('');
                product.append('<option value="" ></option>');
                // add options
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
    $("#app_user_organization_my_account_about_appUserInfo_mstState").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        $.ajax({
            url: "/portal/location/city_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#app_user_organization_my_account_about_appUserInfo_mstCity");
                product.html('');
                product.append('<option value="" ></option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });

    $("#app_user_organization_my_account_about_appUserInfo_userMobileNumber").change(function () {
        checkIsMobileNumberUnique();
    });

    $("#app_user_organization_my_account_about_appUserInfo_pancardNumber").change(function ()    {
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
                    $("#app_user_organization_my_account_about_appUserInfo_pancardNumber").val('');
                }
            }
        });
    });
});

function validate() {
    var bError = false;
    if ($.trim($("#app_user_organization_my_account_about_appUserInfo_mstSalutation").val()) == '') {
        $("#app_user_organization_my_account_about_appUserInfo_mstSalutation").focus();
        $('<div class="errorDiv" style="color: #f00;">Please select.</div>').insertAfter($("#app_user_organization_my_account_about_appUserInfo_mstSalutation"));
        bError = true;
    }
    if ($.trim($("#app_user_organization_my_account_about_appUserInfo_address1").val()) == '') {
        $("#app_user_organization_my_account_about_appUserInfo_address1").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter.</div>').insertAfter($("#app_user_organization_my_account_about_appUserInfo_address1"));
        bError = true;
    }
    if ($.trim($("#app_user_organization_my_account_about_appUserInfo_mstCountry").val()) == '') {
        $("#app_user_organization_my_account_about_appUserInfo_mstCountry").focus();
        $('<div class="errorDiv" style="color: #f00;">Please select country.</div>').insertAfter($("#app_user_organization_my_account_about_appUserInfo_mstCountry"));
        bError = true;
    }
    if ($.trim($("#app_user_organization_my_account_about_appUserInfo_mstState").val()) == '') {
        $("#app_user_organization_my_account_about_appUserInfo_mstState").focus();
        $('<div class="errorDiv" style="color: #f00;">Please select state.</div>').insertAfter($("#app_user_organization_my_account_about_appUserInfo_mstState"));
        bError = true;
    }
    if ($.trim($("#app_user_organization_my_account_about_appUserInfo_mstCity").val()) == '') {
        $("#app_user_organization_my_account_about_appUserInfo_mstCity").focus();
        $('<div class="errorDiv" style="color: #f00;">Please select city.</div>').insertAfter($("#app_user_organization_my_account_about_appUserInfo_mstCity"));
        bError = true;
    }
    if ($.trim($("#app_user_organization_my_account_about_appUserInfo_pinCode").val()) == '') {
        $("#app_user_organization_my_account_about_appUserInfo_pinCode").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter valid pin code.</div>').insertAfter($("#app_user_organization_my_account_about_appUserInfo_pinCode"));
        bError = true;
    }
    if ($.trim($("#app_user_organization_my_account_about_trnOrganizationDetails_0_aboutOrganization").val()) == '') {
        $("#app_user_organization_my_account_about_trnOrganizationDetails_0_aboutOrganization").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter about your organization.</div>').insertAfter($("#app_user_organization_my_account_about_trnOrganizationDetails_0_aboutOrganization"));
        bError = true;
    }
    if ($.trim($("#app_user_organization_my_account_about_appUserInfo_pancardNumber").val()) == '') {
        $("#app_user_organization_my_account_about_appUserInfo_pancardNumber").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter valid pan card number.</div>').insertAfter($("#app_user_organization_my_account_about_appUserInfo_pancardNumber"));
        bError = true;
    }

    panNum = $.trim($("#app_user_organization_my_account_about_appUserInfo_pancardNumber").val());
    if (panNum != '' ) {
        if( !validatePAN(panNum)) {
            $("#app_user_organization_my_account_about_appUserInfo_pancardNumber").val('');
            $("#app_user_organization_my_account_about_appUserInfo_pancardNumber").focus();
            $('<div class="errorDiv" style="color: #f00;">Please enter a valid Pan Card Number.</div>').insertAfter($("#app_user_organization_my_account_about_appUserInfo_pancardNumber"));
            bError = true;
        }
    }
    if ($.trim($("#app_user_organization_my_account_about_trnOrganizationDetails_0_registrationCertificateTrustDeed").val()) == '') {
        $("#app_user_organization_my_account_about_trnOrganizationDetails_0_registrationCertificateTrustDeed").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter a valid Registration Certificate/ Trust Deed' +
            ' Number.</div>').insertAfter($("#app_user_organization_my_account_about_trnOrganizationDetails_0_registrationCertificateTrustDeed"));
        bError = true;
    }
    if ($.trim($("#app_user_organization_my_account_about_trnOrganizationDetails_0_incorporatedOnDate").val()) == '') {
        $("#app_user_organization_my_account_about_trnOrganizationDetails_0_incorporatedOnDate").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter a valid Incorporated On Date.</div>').insertAfter($("#app_user_organization_my_account_about_trnOrganizationDetails_0_incorporatedOnDate"));
        bError = true;
    }
    /*if ($.trim($("#app_user_organization_my_account_about_trnOrganizationDetails_0_registrationNo80G").val()) == '') {
        $("#app_user_organization_my_account_about_trnOrganizationDetails_0_registrationNo80G").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter a valid 80G Registration No.</div>').insertAfter($("#app_user_organization_my_account_about_trnOrganizationDetails_0_registrationNo80G"));
        bError = true;
    }*/
    /*if ($.trim($("#app_user_organization_my_account_about_trnOrganizationDetails_0_registrationDate80G").val()) == '') {
        $("#app_user_organization_my_account_about_trnOrganizationDetails_0_registrationDate80G").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter a valid 80G Registration Date</div>').insertAfter($("#app_user_organization_my_account_about_trnOrganizationDetails_0_registrationDate80G"));
        bError = true;
    }*/
    /*if ($.trim($("#app_user_organization_my_account_about_trnOrganizationDetails_0_website").val()) == '') {
        $("#app_user_organization_my_account_about_trnOrganizationDetails_0_website").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter a valid Organization Website</div>').insertAfter($("#app_user_organization_my_account_about_trnOrganizationDetails_0_website"));
        bError = true;
    }*/
    if ($.trim($("#app_user_organization_my_account_about_trnBankDetails_0_bankName").val()) == '') {
        $("#app_user_organization_my_account_about_trnBankDetails_0_bankName").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter a valid Beneficiary Bank Name</div>').insertAfter($("#app_user_organization_my_account_about_trnBankDetails_0_bankName"));
        bError = true;
    }
    if ($.trim($("#app_user_organization_my_account_about_trnBankDetails_0_accountHolderName").val()) == '') {
        $("#app_user_organization_my_account_about_trnBankDetails_0_accountHolderName").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter a valid Beneficiary Account Holder Name</div>').insertAfter($("#app_user_organization_my_account_about_trnBankDetails_0_accountHolderName"));
        bError = true;
    }
    if ($.trim($("#app_user_organization_my_account_about_trnBankDetails_0_accountNumber").val()) == '') {
        $("#app_user_organization_my_account_about_trnBankDetails_0_accountNumber").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter a valid Beneficiary Bank Account Number</div>').insertAfter($("#app_user_organization_my_account_about_trnBankDetails_0_accountNumber"));
        bError = true;
    }
    if ($.trim($("#app_user_organization_my_account_about_trnBankDetails_0_ifscCode").val()) == '') {
        $("#app_user_organization_my_account_about_trnBankDetails_0_ifscCode").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter a valid Beneficiary IFSC Code</div>').insertAfter($("#app_user_organization_my_account_about_trnBankDetails_0_ifscCode"));
        bError = true;
    }
    if ($.trim($("#app_user_organization_my_account_about_trnBankDetails_0_mstBankAccountType").val()) == '') {
        $("#app_user_organization_my_account_about_trnBankDetails_0_mstBankAccountType").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter a valid Beneficiary Account Type</div>').insertAfter($("#app_user_organization_my_account_about_trnBankDetails_0_mstBankAccountType"));
        bError = true;
    }
    return !bError;
}

function checkIsMobileNumberUnique(){
    var data = {};
    mobileNo = data['mobileNo'] = $.trim($("#app_user_organization_my_account_about_appUserInfo_userMobileNumber").val
    ());
    if ($.isNumeric(mobileNo) == false) {
        alert("Please enter valid mobile Number.");
        $("#app_user_organization_my_account_about_appUserInfo_userMobileNumber").val('');
        return false;
    }

    if (mobileNo.length != 10) {
        alert("Please enter valid mobile Number.");
        $("#app_user_organization_my_account_about_appUserInfo_userMobileNumber").val('');
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
                $("#app_user_organization_my_account_about_appUserInfo_userMobileNumber").val('');
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