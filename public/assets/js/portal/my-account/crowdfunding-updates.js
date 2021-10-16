$(document).ready(function () {
    $('body').addClass('inner-pg logged my-account');
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
    $(".custom-file-input").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        var oThis = this;
        var file = $(this).get(0).files[0];
        if(file) {
            var reader = new FileReader();
            reader.onload = function(){
                $('.divPreviewImg').show();
                $('.divPreviewImg').find(".previewImg").attr("src", reader.result);
            }
            reader.readAsDataURL(file);
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

    $("#btnPublishOnWeb").click(function (){
        if($.trim($("#textBroadCastMessage").val()) == ''){
            alert('Please enter valid message to Publish');
            return ;
        }
        $("#hdnSentTo").val('website');

        $("#frmBroadCastUpdate").submit();
    });

    $("#btnSentToDonor").click(function (){
        if($.trim($("#textBroadCastMessage").val()) == ''){
            alert('Please enter valid message to send');
            return ;
        }
        $("#hdnSentTo").val('donors');
        $("#frmBroadCastUpdate").submit();
    });

});