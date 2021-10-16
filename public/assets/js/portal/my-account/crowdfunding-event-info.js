$(document).ready(function () {
    $('body').addClass('inner-pg logged my-account');
    $('input[name="daterange"]').daterangepicker({
        opens: 'right',
        autoClose: true,
        locale: {
            format: 'DD/MM/YYYY'
        }
    }, function(start, end, label) {
        var data = {};
        data['from'] = start.format('YYYY-MM-DD');
        data['to'] = end.format('YYYY-MM-DD');
        data['quicksearch'] = $("#quicksearch").val();
        doSearch(data);
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
    });
    $(document).on('click', '.social-share-btn', function (e){
        e.preventDefault();
        $(this).siblings('.social-media').slideToggle();
    });

    $(document).on('click', '.fa-close', function (e){
        e.preventDefault();
        $(this).parent('.social-media').slideToggle();
        $(this).parent('.social-media').toggleClass('dblockImp');
        $(this).parents('.share-sec').find('.fa-share-alt').removeClass('toggle');
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
    $(".search-input-btn").click(function (){
        var data = {};
        data['quicksearch'] = $("#quicksearch").val();
        doSearch(data);
    });
    $(".btn-clear-filter").click(function () {
        $('#frmSearch').trigger("reset");
        var data = {};
        doSearch(data);
        var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        $('input[name="daterange"]').data('daterangepicker').setStartDate(today);
        $('input[name="daterange"]').data('daterangepicker').setEndDate(today);
    });
});
/*Filter*/
// quick search regex
var qsRegex;
var buttonFilter;

// change is-checked class on buttons
$('.button-group').each( function( i, buttonGroup ) {
    var $buttonGroup = $( buttonGroup );
    $buttonGroup.on( 'click', 'button', function() {
        $buttonGroup.find('.is-checked').removeClass('is-checked');
        $( this ).addClass('is-checked');
    });
});


// debounce so filtering doesn't happen every millisecond
function debounce( fn, threshold ) {
    var timeout;
    threshold = threshold || 100;
    return function debounced() {
        clearTimeout( timeout );
        var args = arguments;
        var _this = this;
        function delayed() {
            fn.apply( _this, args );
        }
        timeout = setTimeout( delayed, threshold );
    };
}
function setClipboard(value) {
    var tempInput = document.createElement("input");
    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
    tempInput.value = value;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
}

function doSearch(data) {
    $.ajax({
        url: getCrowdFundingEventsUrl,
        data: data,
        type: "POST",
        dataType: "HTML",
        success: function (data) {
            var product = $("#crowdFundingEvents");
            product.html(data);
            $("#totalEvents").html("Total Events ("+$(".element-item").length+")");
        }
    });
}