$(document).ready(function () {
    $('body').addClass('inner-pg logged my-account');
    $('.cust-tabs').addClass('personal-info');

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
    $($('.select-interest > li').get().reverse()).each(function (index) {
        $(this).css('z-index', index + 10);
    });
    $('[name="areaOfInterest"]').click(function (){
        var data = {};
        data['interestId'] = $(this).attr('areaOfInterestId');
        data['fromMyAccount'] = 1;

        jQuery.ajax({
            url: getSecondaryAreaOfInterestURL,
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

    $(document).on('click', '#btnSaveSecondaryAreaOfInterest', function (e) {
        $('#selectAreaModal').modal('hide');
        jQuery.ajax({
            url: saveAreaOfInterestToSectionURL,
            data: $("#frmSaveSecondaryAreaOfInterest").serialize(),
            type: "POST",
            dataType: "json",
            success: function (data) {
                nSelectedPrimaryAI = data.count;
                if (data.addSecondary == true ) {
                    $("#liPrimaryAI"+data.primaryAreaOfInterest).addClass('active');
                    var strHtml = '<i class="fa fa-eye"></i>\n' +
                        '               <span class="tooltiptext">\n' +
                        '                   <ul>\n';
                    console.log(data.secAIList);
                    for ( AIName in data.secAIList) {
                        strHtml +='<li>'+data.secAIList[AIName]+'</li>';
                    }
                    strHtml +='</ul></span>';
                    console.log(strHtml);
                    $("#tooltip_"+data.primaryAreaOfInterest).html(strHtml);

                } else {
                    $("#liPrimaryAI"+data.primaryAreaOfInterest).removeClass('active');
                    $("#tooltip_"+data.primaryAreaOfInterest).html('');
                }
            }
        });
    });

    $(".btn-continue").click(function (){
        if(validate())
            $("#frmAreaOnInterest").submit();
    });
});

function validate() {
    //TODO Validation
    return true;
}