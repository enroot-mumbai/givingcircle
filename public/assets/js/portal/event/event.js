var items = [];
$(document).ready(function () {

    $('#planicon').parents('li').addClass('active');
    $("[name='trn_circle_events_portal']").attr('enctype', "multipart/form-data");
    $('body').addClass('inner-pg create-element event logged');

    $('.btn-continue').click(function (){
        if(validate())
            $("[name='trn_circle_events_portal']").submit();
    });

    $('html, body').animate(
        {
            scrollTop: $($('.next-section-arrow').attr('href')).offset().top - 95,
        },
        800,
        'linear'
    );
    $(window).scroll(function () {
        if ($(this).scrollTop() > 150) {
            $('.bullets .first').removeClass('active');
            $('.bullets .second').addClass('active');
        } else {
            $('.banner-sec').removeClass('fixed');
            $('.wrapper').removeClass('fixed');
            $('.bullets .first').addClass('active');
            $('.bullets .second').removeClass('active');
        }
    });
    $('.next-section-arrow').on('click', function (e) {
        e.preventDefault()
        $('html, body').animate(
            {
                scrollTop: $($(this).attr('href')).offset().top - 95,
            },
            800,
            'linear'
        );
    });
    $('.bullets li.second > a').on('click', function (e) {
        e.preventDefault()
        $('html, body').animate(
            {
                scrollTop: $($(this).attr('href')).offset().top - 95,
            },
            800,
            'linear'
        );
        $(this).parent('li').addClass('active');
        $(this).parent('li').siblings('li').removeClass('active');
    });
    function formcontrol() {
        $(".form-control").filter(function () {
            if (this.value.length !== 0) {
                $(this).siblings('label').addClass('clicked');
            }
        });
    }
    formcontrol();
    $('.form-group label').click(function () {
        $(this).addClass('clicked');
    });
    $('.crowdfunding-radio label').on('click', function () {
        $(this).find('input[name="crowdfunding"]').attr('checked', 'checked');
        $(this).addClass('active').siblings('label').removeClass('active')
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
    // $('.form-control:-webkit-autofill').each(function () {
    //     $(this).siblings('label').addClass('clicked');
    // });
    $('.form-group select').change(function () {
        if (this.value.length !== 0) {
            $(this).parent().parent().find('label').addClass('clicked');
        } else {
            $(this).parent().parent().find('label').removeClass('clicked');
        }
    });

    // deselect all and set default value as not in use now
    $("#trn_circle_events_portal_mstJoinBy option").attr('selected', false);
    $("#trn_circle_events_portal_mstJoinBy option").filter(function() {
        return this.text == 'Open';
    }).attr('selected', true);

    $(document).on('click', '.project-status-input', function (e) {
        e.preventDefault();
        $(this).addClass('clicked');
        $(this).siblings('li').removeClass('clicked');
        var name = $(this).attr('name');
        if (name == 'project-join-by') {
            $("#trn_circle_events_portal_mstJoinBy").val($(this).attr('value'));
        }
    });
    $('.edit-link').click(function () {
        $(this).siblings('input').prop("readonly", false).focus();
        // $(this).focus();

    });


    $('.event-resources li').click(function () {
        $(this).toggleClass('active');
        $('.steps').removeClass('event-ul');
        var resourceSelected = $(this).children('span').html().toLowerCase();
        if ($(this).attr('class') == 'active') {
            $(".steps").find('li.' + resourceSelected).show();
            console.log($(this).attr('value'));
            items.push($(this).attr('value'));
        } else {
            $(".steps").find('li.' + resourceSelected).hide();
            removeItem = $(this).attr('value');
            items = $.grep(items, function(value) {
                return value != removeItem;
            });
        }
        $(this).addClass('clicked');
        $(this).siblings('li').removeClass('clicked');
        console.log($(this).attr('class'));

        $("#trn_circle_events_portal_mstEventProductType option[value=1]").attr("selected", false);
        $("#trn_circle_events_portal_mstEventProductType option[value=2]").attr("selected", false);
        $("#trn_circle_events_portal_mstEventProductType option[value=3]").attr("selected", false);

        $.each( items, function( key, value ) {
            $("#trn_circle_events_portal_mstEventProductType option[value='"+value+"']").attr("selected", "selected");
        });

    });

    $('#trn_circle_events_portal_trnCircle').on('change', function () {
        console.log($(this).val());
        if ($.trim($(this).val()) != '') {
            getProjectsAreaOfInterest($.trim($(this).val()))
            $('.interest-selection').show();
        } else {
            $('.btn-sec').hide();
            $('.interest-selection').hide();
            window.location.reload();
        }
    });

    if($('#trn_circle_events_portal_trnCircle').val() != '') {
        $('#trn_circle_events_portal_trnCircle').change();
    }

    $('.eventOption > li.createNew').on('click', function () {

        if($('input[name="crowdfunding"]:checked').length == 0) {
            alert("Is this Event for Crowdfunding?");
            return false;
        }
        if ($.trim($("#trn_circle_events_portal_trnCircle").val()) == '') {
            alert('Please select project name.');
            return false;
        }
        if($("#primaryAreaOfInterest").val() == '') {
            alert('Please select primary area of interest.');
            return false;
        }

        if($("#selectSecondArea").val() == '') {
            alert('Please select secondary area of interest.');
            return false;
        }

        $(this).parents('.col-sm-12').siblings('.new-event').show();
        if ($.trim($('#trn_circle_events_portal_trnCircle').val()) != '') {
            $('.btn-sec').show();
        } else {
            $('.btn-sec').hide();
            $('.interest-selection').show();
        }
    });

    $(document).on('click', '.primary-area-pre > li', function (e) {
        $('.primary-area-pre > li').removeClass('active');
        $(this).addClass('active');
        // $(this).toggleClass('active');
        $(this).parents('.main-form-group').siblings('.second-selection').css('opacity', 1);
        $(this).parents('.main-form-group').siblings('.second-selection').find('select').removeAttr('disabled');
        $('#primaryAreaOfInterest').val($(this).attr('areaInterest'));
        getSecondaryAreaOfInterest( $('#trn_circle_events_portal_trnCircle').val(), $(this).attr
        ('areaInterest'));
    });

    $(document).on('change', "#selectSecondArea", function (e) {

        if($(this).children("option:selected").val() != '') {

            var selectSecondOption = $(this).children("option:selected").text();
            var currentData = $("#secondaryAreaOfInterest").val();

            if ($.trim(currentData) != '')
                var arrData = currentData.split(',');
            else
                var arrData = new Array();

            if($.inArray($(this).children("option:selected").val(), arrData) > -1) {
                // don't add to list as it is coming second time.
            } else {
                $('.selected-options').append('<li secAI = "'+$(this).children("option:selected").val()+'" >' + selectSecondOption + '<i class="fc-close"> </i> ' +
                    '</li>');
                arrData.push($(this).children("option:selected").val());
            }

            $("#secondaryAreaOfInterest").val(arrData.join(','));
        } 
    });

    $(document).on('click', '.selected-options .fc-close', function (e) {
        e.preventDefault();
        var currentData = $("#secondaryAreaOfInterest").val();
        var arrData = currentData.split(',');
        var removeItem = $(this).parent('li').attr('secAI');
        arrData = $.grep(arrData, function(value) {
            return value != removeItem;
        });
        $("#secondaryAreaOfInterest").val(arrData.join(','));
        $(this).parent('li').remove();
    });
    $(".longtextarea").keydown(function (){
        wordCount($(this), 300);
    });
    $(".textarea").keyup(function (){
        wordCount($(this), 300);
    });

    $($('ul.steps > li').get().reverse()).each(function (index) {
        $(this).css('z-index', index + 10);
    });

    if(primaryAISelected.length >0 ) {
        $('.eventOption > li.createNew').trigger('click');
    }
});
$('.gc-gallery').click(function () {
    $(this).parents('.project-status').siblings('.gc-gallery-div').show();
    $(this).parents('.project-status').siblings('.my-gallery-div').hide();
    $(this).addClass('clicked');
    $(this).siblings().removeClass('clicked');
});
$('.my-gallery').click(function () {
    $(this).parents('.project-status').siblings('.my-gallery-div').show();
    $(this).parents('.project-status').siblings('.gc-gallery-div').hide();
    $(this).addClass('clicked');
    $(this).siblings().removeClass('clicked');
});

/*
$('.input-images-1').imageUploader({
    label: 'Upload Profile Image',
    imagesInputName: 'profileImage',
    extensions: ['.jpg', '.jpeg', '.png'],
    mimes: ['image/jpeg', 'image/png'],
    maxSize: 20480, // 20KB
});
$('.input-images-2').imageUploader({
    label: 'Upload Background Image',
    imagesInputName: 'backGroundImage',
    extensions: ['.jpg', '.jpeg', '.png'],
    mimes: ['image/jpeg', 'image/png'],
    maxSize: 40960, // 40KB
});
*/

$('.input-images-3').imageUploader({
    label: 'Upload Gallery Images',
    label2: 'Add More Images',
    imagesInputName: 'imageGallery[]',
    extensions: ['.jpg', '.jpeg', '.png'],
    mimes: ['image/jpeg', 'image/png'],
    maxSize: 5248000, // 5 MB //61440 - 60KB,
    preloaded: preloadedGallery
});

function triggerClickOnSelectedPrimaryAI() {

    for (i =0 ; i < primaryAISelected.length; i++ ) {
        $("#liPrimaryAI"+primaryAISelected[i]).click();
    }
}

function triggerClickOnSelectedSecondaryAI() {

    for (i =0 ; i < secondaryAISelected.length; i++ ) {
        $("#selectSecondArea").val(secondaryAISelected[i]);
        $("#selectSecondArea").trigger('change');
    }
}

function getProjectsAreaOfInterest(nProjectId) {
    var data = {};
    data['nProjectId'] = nProjectId;
    jQuery.ajax({
        url: getProjectAreaOfInterestURL,
        data: data,
        type: "POST",
        dataType: "JSON",
        complete: function (data) {
            var product = $("#interest-selection-areainterest");
            product.html(data.responseText);
            triggerClickOnSelectedPrimaryAI();
        }
    });
}

function getSecondaryAreaOfInterest(nProjectId, nPrimaryAI) {
    var data = {};
    data['nPrimaryAI'] = nPrimaryAI;
    data['nProjectId'] = nProjectId;
    jQuery.ajax({
        url: getSecondaryAreaOfInterestURL,
        data: data,
        type: "POST",
        dataType: "JSON",
        complete: function (data) {
            var product = $("#selectSecondAreaDiv");
            product.html(data.responseText);
            triggerClickOnSelectedSecondaryAI();
        }
    });
}

function isUniqueEventName() {
    return new Promise(function(resolve, reject) {

        var data = {};
        data['eventname'] = $.trim($("#trn_circle_events_portal_name").val());

        $.ajax({
            url: checkUniqueEventName,
            data: data,
            success: function(result) {
                resolve(result) // Resolve promise and go to then()
            },
            error: function(err) {
                reject(err) // Reject the promise and go to catch()
            }
        });
    });
}

function validate() {
    /*if($("#trn_circle_events_portal_isCrowdFunding").is(":checked") == false){
        alert('Please specify whether event is Crowdfunding event or not.');
        $("#trn_circle_events_portal_trnCircle").focus();
        return false;
    }*/

    if ($.trim($("#trn_circle_events_portal_trnCircle").val()) == '') {
        alert('Please select project name.');
        return false;
    }
    if($("#primaryAreaOfInterest").val() == '') {
        alert('Please select primary area of interest.');
        return false;
    }

    if($("#secondaryAreaOfInterest").val() == '') {
        alert('Please select secondary area of interest.');
        return false;
    }
    if ($.trim($("#trn_circle_events_portal_name").val()) == '') {
        alert('Please select Event Name.');
        $("#trn_circle_events_portal_name").focus();
        return false;
    }

    if ($.trim($("#trn_circle_events_portal_eventPurpose").val()) == '') {
        alert('Please select Event Purpose.');
        $("#trn_circle_events_portal_eventPurpose").focus();
        return false;
    }

    var wordLen = 300,len; // Maximum word length
    len = $("#trn_circle_events_portal_eventPurpose").val().split(/[\s]+/);
    if (len.length > wordLen) {
        if (event.keyCode == 46 || event.keyCode == 8 || event.which == 46 || event.which == 8) {// Allow backspace and delete buttons
        } else if (event.keyCode < 48 || event.keyCode > 57 || event.which < 48 || event.which > 57) {//all other buttons
            event.preventDefault();
        }
    }
    wordsLeft = (wordLen) - len.length;
    if (wordsLeft < 0) {
        alert('Maximum 300 words accepted.');
        $("#trn_circle_events_portal_eventPurpose").focus();
        return false;
    }

    if ($.trim($("#trn_circle_events_portal_highlightsOfEvent").val()) == '') {
        alert('Please select Event Goal.');
        $("#trn_circle_events_portal_highlightsOfEvent").focus();
        return false;
    }

    var wordLen = 300,len; // Maximum word length
    len = $("#trn_circle_events_portal_highlightsOfEvent").val().split(/[\s]+/);
    if (len.length > wordLen) {
        if (event.keyCode == 46 || event.keyCode == 8 || event.which == 46 || event.which == 8) {// Allow backspace and delete buttons
        } else {
            //if (event.keyCode < 48 || event.keyCode > 57 || event.which < 48 || event.which > 57) {//all other buttons
            event.preventDefault();
        }
    }
    wordsLeft = (wordLen) - len.length;
    if (wordsLeft < 0) {
        alert('Maximum 300 words accepted.');
        $("#trn_circle_events_portal_highlightsOfEvent").focus();
        return false;
    }

    if ($.trim($("#trn_circle_events_portal_mstEventProductType").val()) == '') {
        alert('Please select Resources Required.');
        $("#trn_circle_events_portal_highlightsOfEvent").focus();
        return false;
    }

    /*if ($.trim($("#trn_circle_events_portal_mstJoinBy").val()) == '') {
        alert('Please select Event Type.');
        $("#trn_circle_events_portal_highlightsOfEvent").focus();
        return false;
    }*/

    // image validation
    var errormsg =  '';
    var error =  false;
    var validExtensions = ['jpg', 'jpeg','png']; //array of valid extensions
    var galleryImgFileSize = 5248000; // 5 MB // 60000 - 60KB

    $('input[name="imageGallery[][]"]').each(function() {

        var fileName = null;
        if($(this)[0].files[0] != undefined) {
            fileName = $(this)[0].files[0].name;
        }

        if($(this).val() == '' || fileName == null) {
            errormsg+="Please upload gallery image\n";
            error = true;
        } else if(fileName != null) {

            var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
            if ($.inArray(fileNameExt, validExtensions) == -1) {
                errormsg += "Gallery image: Invalid file type\n";
                error = true;
            }
            else if($(this)[0].files[0].size > galleryImgFileSize) {
                // file size error Gallery image 60KB
                errormsg += "Gallery image: Size more than 60KB\n";
                error = true;
            }
        }
    });

    if(error === true) {
        alert(errormsg);
        return false;
    } else {

        isUniqueEventName().then(function(result) {
            // Run this when your request was successful
            if(result === false) {
                alert('Event name is already in the system');
                $("#trn_circle_events_portal_name").focus();
                return false;
            } else {
                // submit form if no error
                //return true;
                $("[name='trn_circle_events_portal']").submit();
            }
        }).catch(function(err) {
            // Run this when promise was rejected via reject()
            console.log(err)
        })
    }
}