$(document).ready(function () {
    $('#example').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     false,
        "searching": false,
        "columnDefs": [ {
            "targets": 'no-sort',
            "orderable": false,
        } ]
    });
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

    $("#btnSentToAll").click(function (){
        $(".members-dtls").hide();
        clearAllSection();
        if ($.trim($("#txtMessgae").val()) == '') {
            $("#txtMessgae").focus();
            alert('Please enter valid message for broadcasting');
            return false;
        }
        $("#hdnSentTo").val('All');
        $("#frmBroadCast").submit();
    });

    $("#btnSendToIndividualMember").click(function () {
        $(".members-dtls").show();
    });

    $(document).on('click', "#checkBoxMember", function (e){
        $('.participant-count').show();
        var oThis = this;
        $( ".checkBoxNameMember" ).each(function( index ) {
            $(this).prop('checked', $(oThis).prop('checked'));
        });
        var countCheckedCheckboxes = $( ".checkBoxNameMember" ).filter(':checked').length;
        $('#countMember').text(countCheckedCheckboxes);
    });

    $("#sendReminderToParticipant").html('Send Broadcast Update');

    $(document).on('click', "#sendReminderToMember", function (e){
        var countCheckedCheckboxes = $( ".checkBoxNameMember" ).filter(':checked').length;
        if(countCheckedCheckboxes == 0) {
            alert('Please select at least one member to send broadcast message to')
            return false;
        }
        if ($.trim($("#txtMessgae").val()) == '') {
            $("#txtMessgae").focus();
            alert('Please enter valid message for broadcasting')
            return false;
        }
        var selected = new Array();
        $( ".checkBoxNameMember" ).each(function( index ) {
            if ($(this).is(':checked')) {
                selected.push($(this).val());
            }
        });
        $("#hdnBroadCastMembers").val(selected.join(','));
        if ($( ".checkBoxNameMember" ).length == selected.length) {
            $("#hdnSentTo").val('All');
        } else {
            $("#hdnSentTo").val('Individual');
        }
        $("#frmBroadCast").submit();
    });
});

function clearAllSection() {
    $( ".checkBoxNameMember" ).each(function( index ) {
        $(this).prop('checked', false);
    });
}