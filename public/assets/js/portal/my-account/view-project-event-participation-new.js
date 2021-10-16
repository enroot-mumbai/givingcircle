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

    $('[name="exitEvent"]').click(function (){
        var url = exitEventPopUpURL.replace('xyz', $.trim($(this).attr('eventid')));
        var data = {};
        data['strproductname'] = $(this).attr('strproductname');
        data['joindataid'] = $(this).attr('joindataid');
        jQuery.ajax({
            url: url,
            type: "POST",
            data: data,
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

    $(document).on('click', "#btnExitEvent", function (e){
        var url = saveExitEventURL.replace('xyz', $.trim($(this).attr('eventid')));
        var data = {};
        data['productid'] = $(this).attr('productid');
        data['joindataid'] = $(this).attr('joindataid');
        jQuery.ajax({
            url: url,
            type: "POST",
            data: data,
            dataType: "JSON",
            success: function (data) {
                alert(data.Message);
                window.location.reload();
            }
        });
    });

    $(".button").click(function (){
        $(".trRow").hide();
        if ($(this).attr('data-filter') != 'All') {
            $($(this).attr('data-filter')).show(500);
        } else {
            $(".trRow").show(500);
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
    var totalEvents = $(".upcoming").length + $(".ongoing").length + $(".past").length;
    $("#totalEvents").html("Joined Event List ("+totalEvents+")");
    $("#btnUpComingShow").html("Upcoming ("+$(".upcoming").length+")");
    $("#btnOnGoingShow").html("Ongoing ("+$(".ongoing").length+")");
    $("#btnPastShow").html("Past ("+$(".past").length+")");
});
/*Filter*/

/*
// quick search regex
var qsRegex;
var buttonFilter;

// init Isotope
var $grid = $('.grid').isotope({
    itemSelector: '.element-item',
    layoutMode: 'fitRows',
    filter: function() {
        var $this = $(this);
        var searchResult = qsRegex ? $this.text().match( qsRegex ) : true;
        var buttonResult = buttonFilter ? $this.is( buttonFilter ) : true;
        return searchResult && buttonResult;
    }
});

$('#filters').on( 'click', 'button', function() {
    buttonFilter = $( this ).attr('data-filter');
    $grid.isotope();
});

// use value of search field to filter
var $quicksearch = $('#quicksearch').keyup( debounce( function() {
    qsRegex = new RegExp( $quicksearch.val(), 'gi' );
    $grid.isotope();
}) );


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
*/
function doSearch(data) {
    $.ajax({
        url: ajaxViewProjectEventParticipationURL,
        data: data,
        type: "POST",
        dataType: "HTML",
        success: function (data) {
            var product = $("#joinEventList");
            product.html(data);
            var totalEvents = $(".upcoming").length + $(".ongoing").length + $(".past").length;
            $("#totalEvents").html("Joined Event List ("+totalEvents+")");
            $("#btnUpComingShow").html("Upcoming ("+$(".upcoming").length+")");
            $("#btnOnGoingShow").html("Ongoing ("+$(".ongoing").length+")");
            $("#btnPastShow").html("Past ("+$(".past").length+")");
        }
    });
}