$(document).ready(function () {

    $('#planicon').parents('li').addClass('active');
    $('body').addClass('inner-pg create-element event logged');
    $("[name='trn_circle_events_portal']").attr('enctype', "multipart/form-data");
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

    if(isEditEvent == false) {
        $('.crowdfunding-radio label').on('click', function () {
            $(this).addClass('active').siblings('label').removeClass('active')
        });
    }

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

    $('.edit-link').click(function () {
        $(this).siblings('input').prop("readonly", false).focus().select();
        // $(this).focus();

    });

    $('#selectProject').on('change', function () {
        $('.interest-selection').show();
    });
    $('.eventOption > li.createNew').on('click', function () {
        return false; // added to stop default behavior, as not to create new if copying or editing event
        $(this).parents('.col-sm-12').siblings('.new-event').show();
    });
    $('.primary-area-pre > li').on('click', function () {
        $('.primary-area-pre > li').removeClass('active');
        $(this).addClass('active');
        // $(this).toggleClass('active');
        $(this).parents('.main-form-group').siblings('.second-selection').css('opacity', 1);
        $(this).parents('.main-form-group').siblings('.second-selection').find('select').removeAttr('disabled');
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

    $($('ul.steps > li').get().reverse()).each(function (index) {
        $(this).css('z-index', index + 10);
    });
    console.log(bHasProfileImage);
    if (bHasProfileImage == true) {
        $(".my-gallery").click();
    }
    $('.btn-continue').click(function (){
        if(validate())
            $("[name='trn_circle_events_portal']").submit();
    });
    $(".longtextarea").keydown(function (){
        var wordLen = 300,len; // Maximum word length
        len = $(this).val().split(/[\s]+/);
        if (len.length > wordLen) {
            if (event.keyCode == 46 || event.keyCode == 8) {// Allow backspace and delete buttons
            } else if (event.keyCode < 48 || event.keyCode > 57) {//all other buttons
                event.preventDefault();
            }
        }
        wordsLeft = (wordLen) - len.length;
        $(this).siblings('.textarea-instru').find('.words-left').html(wordsLeft);
    });
    $(".textarea").keyup(function (){
        var wordLen = 160,len; // Maximum word length
        len = $(this).val().split(/[\s]+/);
        if (len.length > wordLen) {
            if (event.keyCode == 46 || event.keyCode == 8) {// Allow backspace and delete buttons
            } else if (event.keyCode < 48 || event.keyCode > 57) {//all other buttons
                event.preventDefault();
            }
        }
        wordsLeft = (wordLen) - len.length;
        $(this).siblings('.textarea-instru').find('.words-left').html(wordsLeft);
    });
    $.each( items, function( key, value ) {
        $("#trn_circle_events_portal_mstEventProductType option[value='"+value+"']").attr("selected", "selected");
    });
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

$('.input-images-1').imageUploader({
    label: 'Upload Profile Image',
    imagesInputName: 'profileImage',
    preloaded: preloadedProfile
});
$('.input-images-2').imageUploader({
    label: 'Upload Background Image',
    imagesInputName: 'backGroundImage',
    preloaded: preloadedBackGround
});
/*$('.input-images-3').imageUploader({
    label: 'Upload Gallery Images',
    label2: 'Add More Images',
    imagesInputName: 'imageGallery[]',
    preloaded: preloadedGallery,
    preloadedInputName: 'preloadedGalImg'
});*/

$('.input-images-3').imageUploader({
    label: 'Upload Gallery Images',
    label2: 'Add More Images',
    imagesInputName: 'imageGallery[]',
    extensions: ['.jpg', '.jpeg', '.png'],
    mimes: ['image/jpeg', 'image/png'],
    maxSize: 5248000, // 5 MB //61440 - 60KB,
    preloaded: preloadedGallery,
    preloadedInputName: 'preloadedGalImg'
});

$(document).ready(function () {
    $('.interest-selection').show();
    //$('.primary-area-pre > li:first-child').addClass('active');
    $('.primary-area-pre > li:first-child').parents('.main-form-group').siblings('.second-selection').css('opacity', 1);
    $('.primary-area-pre > li:first-child').parents('.main-form-group').siblings('.second-selection').find('select').removeAttr('disabled');
    $('.new-event').show();
    setTimeout(function(){
        $('#trn_circle_events_portal_trnCircle').trigger('change');
    }, 1000);


    // this will be used in edit case, not to use in copy case
    if(isEditEvent == true) {

        $(".delete-image").on('click', function (){

            var imgFldNme = $(this).siblings('input').attr('name');
            var imgSrc = $(this).siblings('input').val();

            if(imgFldNme == 'preloadedGalImg[]') {
                // remove gallery image

                var data = {};
                data['imagetype'] = 'galleryimage';
                data['imgsrc'] = imgSrc;

                jQuery.ajax({
                    url: pathToRemoveImage,
                    data: data,
                    type: "GET",
                    dataType: "JSON",
                    success: function (data) {
                        console.log(data);
                    }
                });
            }
        });
    }

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

        if($(this).children("option:selected").val() == undefined) {
            $(this).val('');
            $(this).change();
        }
        if($(this).children("option:selected").val() != '') {

            var selectSecondOption = $(this).children("option:selected").text();
            var currentData = $("#secondaryAreaOfInterest").val();

            if ($.trim(currentData) != '')
                var arrData = currentData.split(',');
            else
                var arrData = new Array();

            if ($.inArray($(this).children("option:selected").val(), arrData) > -1) {
                // don't add to list as it is coming second time.
            } else {
                $('.selected-options').append('<li secAI = "' + $(this).children("option:selected").val() + '" >' + selectSecondOption + '<i class="fc-close"> </i> ' +
                    '</li>');
                arrData.push($(this).children("option:selected").val());
            }

            $("#secondaryAreaOfInterest").val(arrData.join(','));
        }

        /*var selectSecondOption = $(this).children("option:selected").text();
        var currentData = $("#secondaryAreaOfInterest").val();
        $('.selected-options').append('<li secAI = "'+$(this).children("option:selected").val()+'" >' + selectSecondOption + '<i class="fc-close"> </i> ' +
            '</li>');
        if ($.trim(currentData) != '')
            var arrData = currentData.split(',');
        else
            var arrData = new Array();
        arrData.push($(this).children("option:selected").val());
        $("#secondaryAreaOfInterest").val(arrData.join(','));*/
    });

    $('.event-resources li').click(function () {

        if(isEditEvent == true) {
            return false;
        }

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
    if(isEditEvent == false) {
        $(document).on('click', '.project-status-input', function (e) {
            e.preventDefault();
            $(this).addClass('clicked');
            $(this).siblings('li').removeClass('clicked');
            var name = $(this).attr('name');
            if (name == 'project-join-by') {
                $("#trn_circle_events_portal_mstJoinBy").val($(this).attr('value'));
            }
        });
    }
})
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
function validate() {
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
    if ($.trim($("#trn_circle_events_portal_name").val()) == '') {
        alert('Please Mention Event Name.');
        $("#trn_circle_events_portal_name").focus();
        return false;
    }
    if ($.trim($("#trn_circle_events_portal_eventPurpose").val()) == '') {
        alert('Please Mention Event Purpose.');
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
        } else if (event.keyCode < 48 || event.keyCode > 57 || event.which < 48 || event.which > 57) {//all other buttons
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
                errormsg += "Gallery image: Size more than 5 MB\n";
                error = true;
            }
        }
    });

    if(error === true) {

        var atleastOnePreloadedImg = false;

        $('input[name="preloadedGalImg[]"]').each(function () {

            if($(this).val() != '') {
                atleastOnePreloadedImg = true;
                error = false;
            }
        });

        if(atleastOnePreloadedImg == false) {
            alert(errormsg);
            return false;
        }
    }

    if(error === false) {
        // other validations passed

        if(isEditEvent === false) {
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
        } else {
            return true;
        }
    }
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