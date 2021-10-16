$(document).ready(function () {
    $('body').addClass('inner-pg logged my-account');
    $('.participant-count').hide();

    $('.example').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     false,
        "searching": false,
        "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
        } ]
    });
    $('#all-members-tbl-dtls').click(function(){
        $(this).addClass('btn-blue').siblings().removeClass('btn-blue');
        $('.members-dtls').show();
        $('.participant-dtls').hide();
    })
    $('#participant-tbl-dtls').click(function(){
        $(this).addClass('btn-blue').siblings().removeClass('btn-blue');
        $('.members-dtls').hide();
        $('.participant-dtls').show();
    })



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

    $( ".checkBoxNameParticipant" ).change(function (){
        $('.participant-count').show();
        var countCheckedCheckboxes = $( ".checkBoxNameParticipant" ).filter(':checked').length;
        $('#countParticipant').text(countCheckedCheckboxes);
    });

    $( ".checkBoxNameMember" ).change(function (){
        $('.participant-count').show();
        var countCheckedCheckboxes = $( ".checkBoxNameMember" ).filter(':checked').length;
        $('#countMember').text(countCheckedCheckboxes);
    });

    $("#checkall").click(function(){
        $('.participant-count').show();
        var oThis = this;
        $( ".checkBoxNameParticipant" ).each(function( index ) {
            $(this).prop('checked', $(oThis).prop('checked'));
        });
        var countCheckedCheckboxes = $( ".checkBoxNameParticipant" ).filter(':checked').length;
        $('#countParticipant').text(countCheckedCheckboxes);
    });

    $("#checkBoxMember").click(function(){
        $('.participant-count').show();
        var oThis = this;
        $( ".checkBoxNameMember" ).each(function( index ) {
            $(this).prop('checked', $(oThis).prop('checked'));
        });
        var countCheckedCheckboxes = $( ".checkBoxNameMember" ).filter(':checked').length;
        $('#countMember').text(countCheckedCheckboxes);
    });

    $("#sendReminderToMember").click(function (){
        var countCheckedCheckboxes = $( ".checkBoxNameMember" ).filter(':checked').length;
        if(countCheckedCheckboxes == 0) {
            alert('Please select at least one member to send reminder to')
            return false;
        }
        $("#frmMemberReminder").submit();
    });

    $("#sendReminderToParticipant").click(function (){
        var countCheckedCheckboxes = $( ".checkBoxNameParticipant" ).filter(':checked').length;
        if(countCheckedCheckboxes == 0) {
            alert('Please select at least one participant to send reminder to')
            return false;
        }
        $("#frmParticipantReminder").submit();
    });

});