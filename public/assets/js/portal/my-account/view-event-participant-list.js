$(document).ready(function () {

    $('body').addClass('inner-pg logged my-account');

    $('.participant-viewlist').DataTable({
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

    $('#Resources').change(function (){
        doSearch();
    });

    $('#Status').change(function (){
        doSearch();
    });

    $('.search-input-btn').change(function (){
        doSearch();
    });
});

function doSearch() {
    var data = {};
    if ($.trim($("#Resources").val()) != '') {
        data['mstProductType'] = $("#Resources").val();
    }
    if ($.trim($("#Status").val()) != '') {
        data['mstStatus'] = $("#Status").val();
    }
    if ($.trim($("#quicksearch").val()) != '') {
        data['quicksearch'] = $("#quicksearch").val();
    }
    jQuery.ajax({
        url: ajaxGetViewEventParticipant,
        data: data,
        type: "POST",
        dataType: "HTML",
        success: function (data) {
            $("#div-participant-viewlist").html(data);
            $("#ttlMembers").html("Total Members (" + $("[name='trParticipant']").length + ")");
            $("#spnVolunteerAccepted").html("Volunteer (" + $("#acceptedVolunteer").val() + " )");
            $("#spnMaterialAccepted").html("Material (" + $("#acceptedMaterial").val() + " )");
            $("#spnFundAccepted").html("Funds (" + $("#acceptedFundraiser").val() + " )");

            $("#spnVolunteerPending").html("Volunteer (" + $("#pendingVolunteer").val() + ")");
            $("#spnMaterialPending").html("Material (" + $("#pendingMaterial").val() + " )");
            $("#spnFundPending").html("Funds (" + $("#pendingFundraiser").val() + " )");
            $('.participant-viewlist').DataTable({
                "paging":   false,
                "ordering": true,
                "info":     false,
                "searching": false,
                "columnDefs": [ {
                    "targets": 'no-sort',
                    "orderable": false,
                } ]
            });
        }
    });
}