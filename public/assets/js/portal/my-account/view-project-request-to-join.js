$(document).ready(function () {
    $('.mob-filter').click(function(){
        $('.filter-sec').addClass('filter-fixed');
    });
    $('.fa-close').click(function(){
        $('.filter-sec').removeClass('filter-fixed');
    });
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

    $("[name='btnAcceptRequest']").on('click', function (){
        updateProjectParticipationEvent($(this).attr('requestid'), 'Activated');
    });
    $("[name='btnRejectRequest']").on('click', function (){
        updateProjectParticipationEvent($(this).attr('requestid'), 'Rejected');
    });
    $("[name='btnAcceptRequestProject']").on('click', function (){
        updateProjectParticipation($(this).attr('requestid'), 'Activated');
    });
    $("[name='btnRejectRequestProject']").on('click', function (){
        updateProjectParticipation($(this).attr('requestid'), 'Rejected');
    });

    $("#Events").change(function (){
        doSearch();
    });

    $("#Resources").change(function (){
        doSearch();
    });

    $("#Status").change(function (){
        doSearch();
    });

    $(".search-input-btn").click(function (){
        doSearch();
    });
});

function updateProjectParticipationEvent(requestid, status) {
    var data = {};
    data['requestid'] = requestid;
    data['status'] = status;
    jQuery.ajax({
        url: updateProjectEventParticipationRequestURL,
        data: data,
        type: "POST",
        dataType: "JSON",
        success: function (data) {
            alert(data.Message)
            jQuery.ajax({
                url: ajaxViewProjectRequestToJoinURL,
                data: data,
                type: "POST",
                dataType: "html",
                success: function (data) {
                    var result = $("#tbl-request-toJoin");
                    result.html('');
                    result.html(data);
                    AOS.init({
                        delay: 50,
                        duration: 800,
                    });
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
                    $("#countRequestToJoinMemberList").html("Request to Join List ("+$('[name="requestedMemberData"]').length+")");
                }
            });
        }
    });
}

function updateProjectParticipation(requestid, status) {
    var data = {};
    data['requestid'] = requestid;
    data['status'] = status;
    jQuery.ajax({
        url: updateProjectParticipationRequestURL,
        data: data,
        type: "POST",
        dataType: "JSON",
        success: function (data) {
            alert(data.Message)
            jQuery.ajax({
                url: ajaxViewProjectRequestToJoinURL,
                data: data,
                type: "POST",
                dataType: "html",
                success: function (data) {
                    var result = $("#tbl-request-toJoin");
                    result.html('');
                    result.html(data);
                    AOS.init({
                        delay: 50,
                        duration: 800,
                    });
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
                    $("#countRequestToJoinMemberList").html("Request to Join List ("+$('[name="requestedMemberData"]').length+")");
                }
            });
        }
    });
}

function doSearch() {
    var data = {};

    if($.trim($("#quicksearch").val()) != '') {
        data['quicksearch'] = $("#quicksearch").val();
    }

    if($("#Events").val()) {
        data['Events'] = $("#Events").val();
    }

    if($("#Resources").val()) {
        data['Resources'] = $("#Resources").val();
    }

    if($("#Status").val()) {
        data['Status'] = $("#Status").val();
    }

    jQuery.ajax({
        url: ajaxViewProjectRequestToJoinURL,
        data: data,
        type: "POST",
        dataType: "html",
        success: function (data) {
            var result = $("#tbl-request-toJoin");
            result.html('');
            result.html(data);
            AOS.init({
                delay: 50,
                duration: 800,
            });
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
            $("#countRequestToJoinMemberList").html("Request to Join List ("+$('[name="requestedMemberData"]').length+")");
        }
    });
}