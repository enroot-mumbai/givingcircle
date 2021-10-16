$(document).ready(function () {
    $('body').addClass('inner-pg logged create-element details-page funds-pax-details');
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

    $('.btn-donate').click(function () {
        removeErrorDivs();
        if(validate()){
            $("#frmMakePayment").submit();
        }
    });


});
function removeErrorDivs() {
    $.each($(".errorDiv"), function(){
       $(this).remove();
    });
}

function validate() {

    mobileNo = $.trim($("#mobile-no").val());
    var bReturn = true;
    if (mobileNo != '') {
        if ($.isNumeric(mobileNo) == false || mobileNo.length != 10) {
            alert("Please enter valid mobile Number.");
            $("#mobile-no").val('');
            $("#mobile-no").focus();
            bReturn = false;
            $('<div class="errorDiv" style="color: #ff0000;">Please enter valid mobile Number.</div>').insertAfter($("#mobile-no"));
        }
    }
    emailId = $.trim($("#email-id").val());
    if (emailId != '' ) {
        if( !validateEmail(emailId)) {
            $("#email-id").val('');
            $("#email-id").focus();
            bReturn = false;
            $('<div class="errorDiv" style="color: #f00;">Please enter a valid email address.</div>').insertAfter($("#email-id"));
        }
    }
    panNum = $.trim($("#panNum").val());
    if (panNum != '' ) {
        if( !validatePAN(panNum)) {
            $("#panNum").val('');
            $("#panNum").focus();
            bReturn = false;
            $('<div class="errorDiv" style="color: #f00;">Please enter a valid Pan Card Number.</div>').insertAfter($("#panNum"));
        }
    }
    $.each($(".required"), function(){
        if($.trim($(this).val()) == "") {
            bReturn = false;
            $('<div class="errorDiv" style="color: #f00;">This is required field</div>').insertAfter($(this));
            $(this).focus();
        }
    });
    if ($("#tnc").is(':checked') == false) {
        alert("Please confirm do you agree to Term and Conditions. ")
        bReturn = false;
        return bReturn;
    }
    return bReturn;
}
function validateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return emailReg.test( $email );
}
function validatePAN(pan){
    var regex = /[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
    return regex.test(pan);
}

function blockSpecialChar(e){
    var k;
    document.all ? k = e.keyCode : k = e.which;
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
}