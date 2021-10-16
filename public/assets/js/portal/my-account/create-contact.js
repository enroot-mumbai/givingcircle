$(document).ready(function () {
    $('body').addClass('inner-pg logged my-account');
    $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date"
    });
    $('.input-images-3').imageUploader({
        label: 'Upload Documentation',
        label2: 'Add More Images'
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
    $("#btnAddContact").click(function (){
        removeErrorDivs();
        if(validate())
            $("#frmCreateContact").submit();
    });

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
    $("#title").change(function (){
       if($(this).val() != '')
           $("#lblTitle").addClass('clicked');
       else
           $("#lblTitle").removeClass('clicked');
    });
});

function validate() {
    var bError = false;
    if ($.trim($("#title").val()) == '') {
        $("#title").focus();
        $('<div class="errorDiv" style="color: #f00;">Please select Salutation</div>').insertAfter($("#title"));
        bError = true;
    }
    if ($.trim($("#firstName").val()) == '') {
        $("#firstName").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter First Name for contact</div>').insertAfter($("#firstName"));
        bError = true;
    }
    mobileNo = $.trim($("#mobileNo").val());
    if ($.trim($("#mobileNo").val()) == '' || $.isNumeric(mobileNo) == false || mobileNo.length != 10) {
        $("#mobileNo").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter valid mobile number</div>').insertAfter($("#mobileNo"));
        bError = true;
    }

    if ($.trim($("#emailId").val()) == '' || !validateEmail($.trim($("#emailId").val())) ) {
        $("#emailId").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter valid email address</div>').insertAfter($("#emailId"));
        bError = true;
    }
    return !bError;
}
function removeErrorDivs() {
    $.each($(".errorDiv"), function(){
        $(this).remove();
    });
}
function validateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return emailReg.test( $email );
}