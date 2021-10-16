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
    $('.social-share-btn').on('click', function (e) {
        e.preventDefault();
        $(this).siblings('.social-media').slideToggle();
    });

    $('.fa-close').on('click', function (e) {
        e.preventDefault();
        $(this).parent('.social-media').slideToggle();
        $(this).parent('.social-media').toggleClass('dblockImp');
        $(this).parents('.share-sec').find('.fa-share-alt').removeClass('toggle');
    });

    $("#anchOwnPrj").on('click', function (e){
        $('[name="projectList"]').hide(500);
        $('#myProjects').show(500);
        $(this).addClass('btn-blue');
        $(this).siblings().removeClass('btn-blue');
    });

    $("#anchOtherPrj").on('click', function (e){
        $('[name="projectList"]').hide(500);
        $('#otherProjects').show(500);
        $(this).addClass('btn-blue');
        $(this).siblings().removeClass('btn-blue');
    });

    $(document).on('click', "[name='deactivateProject']", function (e){
        var data = {};
        data['projectid'] = $(this).attr('projectid');
        url = getMyAccountProjectDeactivatePopup.replace('xyz', $.trim($(this).attr('projectid')));
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

    $(document).on('click', '#btnDeactivateProject', function (e){
        var data = {};
        data['projectid'] = $(this).attr('projectid');
        url = getMyAccountProjectDeactivateSave.replace('xyz', $.trim($(this).attr('projectid')));
        jQuery.ajax({
            url: url,
            data: data,
            type: "POST",
            dataType: "JSON",
            success: function (data) {
                alert(data.Message);
                $('.btn-close').click();
                url = getMyAccountAjaxOwnProjectList;
                jQuery.ajax({
                    url: url,
                    data: data,
                    type: "POST",
                    dataType: "html",
                    success: function (data) {
                        var result = $("#myProjects");
                        result.html('');
                        result.html(data);
                        AOS.init({
                            delay: 50,
                            duration: 800,
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', "[name='exitProject']", function (e){
        var data = {};
        data['projectid'] = $(this).attr('projectid');
        url = getMyAccountProjectExitPopup.replace('xyz', $.trim($(this).attr('projectid')));
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

    $(document).on('click', '#btnExitProject', function (e){
        var data = {};
        data['projectid'] = $(this).attr('projectid');
        url = getMyAccountProjectExitSave.replace('xyz', $.trim($(this).attr('projectid')));
        jQuery.ajax({
            url: url,
            data: data,
            type: "POST",
            dataType: "JSON",
            success: function (data) {
                alert(data.Message);
                $('.btn-close').click();
                url = getMyAccountAjaxOtherProjectList;
                jQuery.ajax({
                    url: url,
                    data: data,
                    type: "POST",
                    dataType: "html",
                    success: function (data) {
                        var result = $("#otherProjects");
                        result.html('');
                        result.html(data);
                        AOS.init({
                            delay: 50,
                            duration: 800,
                        });
                    }
                });
            }
        });
    });

    

});
function setClipboard(value) {
    var tempInput = document.createElement("input");
    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
    tempInput.value = value;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
}