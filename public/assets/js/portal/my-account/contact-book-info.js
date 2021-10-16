$(document).ready(function () {
    $('body').addClass('inner-pg logged my-account');
    $('.import-contacts').hide();
    $('#import-contacts').click(function(){
        $('.import-contacts').show();
    })
    $('.close').click(function(){
        $('.import-contacts').hide();
    })

    $(".custom-file-input").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        var fileExtension = ['csv'];
        if ($.inArray(fileName.split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only formats are allowed : "+fileExtension.join(', '));
            return false;
        }
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        if ($('.custom-file-label').hasClass('selected')){
            //alert('ok');
            $('.btn-primary').removeClass('disabled');
        }
    });

    $("#btnImportContact").click(function (){
        if ($.trim($('#customFile').val()) == '') {
            alert('Please upload a csv file');
            return false;
        }
        $("#frmImportContact").submit();
    });

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

    $(document).on('click', "#btnDeleteContact", function (e) {
        var data = {}
        data['contactid'] = $(this).attr('contactid');
        $.ajax({
            url: deleteContactURL,
            type: 'POST',
            data: data,
            dataType: "JSON",
            success: (function (formdata) {
                alert(formdata.Message);
                $('.btn-close').click();
                $.ajax({
                    url: getContactsAjaxURL,
                    data: data,
                    type: "POST",
                    dataType: "HTML",
                    success: function (data) {
                        var product = $("#divContactList");
                        product.html(data);
                        $(".py-3").html('Total Contacts ( ' + $("#example tbody tr").length + ' )');
                    }
                });
            }),
        });
    });

    $(document).on('click', ".search-input-btn", function (e) {
        var data = {}
        data['searchContact'] = $("#searchContact").val();
        $.ajax({
            url: getContactsAjaxURL,
            data: data,
            type: "POST",
            dataType: "HTML",
            success: function (data) {
                var product = $("#divContactList");
                product.html(data);
                $(".py-3").html('Total Contacts ( ' + $("#example tbody tr").length + ' )');
            }
        });
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

});