$(document).ready(function () {
    /*$('.new-leads').hide();
    $('.add-lead').click(function(){
        $('.new-leads').show();
        $('.new-leads .add-lead').addClass('lead-added');
    });
    $('.remove-lead').click(function(){
        $('.new-leads').hide();
    });*/
    $('.lead-added').click(function(){
        $('.participant-dtls').hide();
        $('.no-lead').hide();
        $('.tbl-memberlist').show();
    });
    $('#view-memberlist').click(function(){
        $(this).addClass('btn-blue');
        $('.participant-dtls').show();
    });
    $('#example, #example2').DataTable({
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
    $(document).on('click','.add-lead',function(){
        var data = {};
        data['projectid'] = projectId;
        data['appUserId'] = $(this).attr('appuserid');
        data['eventId'] = '';
        data['action'] = 'add';
        jQuery.ajax({
            url: addRemoveProjectLeadURL,
            data: data,
            type: "POST",
            dataType: "JSON",
            success: function (data) {
                alert(data.Message);
                window.location.reload();
            }
        });
    });
    
    $(document).on('click','.remove-lead',function(){
        var data = {};
        data['projectid'] = projectId;
        data['appUserId'] = $(this).attr('appuserid');
        data['eventId'] = '';
        data['action'] = 'remove';
        jQuery.ajax({
            url: addRemoveProjectLeadURL,
            data: data,
            type: "POST",
            dataType: "JSON",
            success: function (data) {
                alert(data.Message);
                window.location.reload();
            }
        });
    });
    
    $(".search-input-btn").click(function (){
        var data = {};
        data['quicksearch'] = $("#quicksearch").val();
        getProjectMemberListURL = getProjectMemberListURL.replace('xyz',projectId)
        jQuery.ajax({
            url: getProjectMemberListURL,
            data: data,
            type: "POST",
            dataType: "HTML",
            success: function (data) {
                $("#divProjectMemberList").html(data);
                $("#ttlRegisteredMember").html("Total Registered Members (" +$("[name='trContributor']").length + ")");
            }
        });
    });

});