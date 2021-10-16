$(document).ready(function () {
    $('body').addClass('inner-pg logged my-account');
    $(".custom-file-input").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
    checkWordLen = function () {
        var wordLen = 50,
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
    $("#selectContributor").change(function (){
        if($.trim($(this).val()) != '') {
            $("#lblContributorMobile").addClass('clicked');
            $("#lblContributorEmail").addClass('clicked');
            $("#contributorMobile").val($("#selectContributor option:selected").attr('mobilenumber'));
            $("#contributorEmail").val($("#selectContributor option:selected").attr('email'));
        } else {
            $("#contributorMobile").val('');
            $("#contributorEmail").val('');
            $("#lblContributorMobile").removeClass('clicked');
            $("#lblContributorEmail").removeClass('clicked');
        }
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

    $("#addToDistributeCamp").click(function (){
        removeErrorDivs();
        if (validate()){
            $("#frmContributeForDisCamp").submit();
        }
    });

    $('#targetAmount').keyup(function(){
        var val = $(this).val();
        if(isNaN(val)){
            val = val.replace(/[^0-9\.]/g,'');
            if(val.split('.').length>2)
                val =val.replace(/\.+$/,"");
        }
        $(this).val(val);
    });

    $(document).on('click', "[name='deActDistEvent']", function (e){
        var data = {};
        data['eventid'] = $(this).attr('eventid');
        url = getMyAccountCampaignDeactivatePopup.replace('xyz', $.trim($(this).attr('eventid')));
        jQuery.ajax({
            url: url,
            data: data,
            type: "POST",
            dataType: "html",
            success: function (data) {
                var result = $("#selectAreaModalBody");
                result.html('');
                result.html(data);
                AOS.init({
                    delay: 50,
                    duration: 800,
                });
            }
        });
    });

    $(document).on('click', "#btnDeactivateEvent", function (e){
        $("#frmDeactivateEventReason").submit();
    });

});

function validate() {
    var bError = false;
    if ($.trim($("#selectContributor").val()) == '') {
        $("#selectContributor").focus();
        $('<div class="errorDiv" style="color: #f00;">Please select.</div>').insertAfter($("#selectContributor"));
        bError = true;
    }
    if ($.trim($("#targetAmount").val()) == '') {
        $("#targetAmount").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter contribution amount.</div>').insertAfter($("#targetAmount"));
        bError = true;
    }

    if ($("#targetAmount").val() > nTargetAmount) {
        $("#targetAmount").focus();
        $('<div class="errorDiv" style="color: #f00;">Please enter contribution amount less then target ' +
            'amount' + '.</div>').insertAfter($("#targetAmount"));
        bError = true;
    }
    return (!bError);
}
function removeErrorDivs() {
    $.each($(".errorDiv"), function(){
        $(this).remove();
    });
}