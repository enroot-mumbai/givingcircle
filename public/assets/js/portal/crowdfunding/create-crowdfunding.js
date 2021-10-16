$(document).ready(function () {
    var pdfjsLib = window['pdfjs-dist/build/pdf'];
// The workerSrc property shall be specified.
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://mozilla.github.io/pdf.js/build/pdf.worker.js';
    $('body').addClass('inner-pg create-element event logged');
    $('[name="trn_circle_events_crowd_fund_event_portal"]').attr('enctype', "multipart/form-data");

    $('#planicon').parents('li').addClass('active');

    changeInputVal($('#urgent'));

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
        if(isEditEvent == true && $(this).parents().hasClass('distributeEvent') == false) {
            return false; // cannot change crowdfunding to normal event in edit mode
        }

        $(this).addClass('active').siblings('label').removeClass('active');

        if($(this).parents().hasClass('distributeEvent')) {

            // When no clicked for distributeEvent

            if(isEditEvent == true) {
                var isDistributedEventFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents_0_isDistributedEvent");
            } else {
                var isDistributedEventFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents___name___isDistributedEvent");
            }

            $(this).parents('.main-form-group').find('.distributeCampaign').hide();
            isDistributedEventFld.prop('checked',false);
            $("#memberName").val('');
            $("#selectContributor").val('');
            $("#memberMobileNumber").val('');
            $("#memberEmailId").val('');
            $("#distributeAmount").val('');
        }
    });
    $('.crowdfunding-radio label.distributeYes').on('click', function () {
        $(this).parents('.main-form-group').find('.distributeCampaign').show();

        if(isEditEvent == true) {
            var isDistributedEventFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents_0_isDistributedEvent");
        } else {
            var isDistributedEventFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents___name___isDistributedEvent");
        }

        isDistributedEventFld.prop('checked',true);

        if ($.trim($('#trn_circle_events_crowd_fund_event_portal_trnCircle').val()) != '') {
            var data = {};
            data['nProjectId'] = $('#trn_circle_events_crowd_fund_event_portal_trnCircle').val();
            jQuery.ajax({
                url: getProjectMemberListURl,
                data: data,
                type: "POST",
                dataType: "HTML",
                complete: function (data) {
                    $("[name='selectContributor']").html(data.responseText);
                }
            });
        } else {
            alert('Please select a project');
            $('#trn_circle_events_crowd_fund_event_portal_trnCircle').focus();
        }
    });
    if(isEditEvent == true) {

        var isDistributedEventFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents_0_isDistributedEvent");
        if(isDistributedEventFld.prop('checked') == true) {
            $('.distributeYes').click();
            loadCrowdFundingSubEventFromSession();
        } else {
            $('.distributeNo').click();
        }
    } else {
        $('.distributeNo').click(); // set default value to No
    }


    $(".delete-image").on('click', function (){

        var imgFldNme = $(this).siblings('input').attr('name');
        var imgSrc = $(this).siblings('input').val();

        if(imgFldNme == 'preloadedGalImg[]') {
            // remove gallery image

            var data = {};
            data['imagetype'] = 'galleryimage';
            data['imgsrc'] = imgSrc;

            jQuery.ajax({
                url: removeCrowdfundingGalleryImageUrl,
                data: data,
                type: "GET",
                dataType: "JSON",
                success: function (data) {
                    console.log(data);
                }
            });
        }
    });

    $(document).on('change', "[name='selectContributor']", function (e) {
        if($.trim($(this).val()) != '') {
            $(this).parent().parent().siblings().find("[name='memberMobileNumber']").val( $(this).find('option:selected').attr('mobilenumber'));
            $(this).parent().parent().siblings().find("[name='memberEmailId']").val( $(this).find('option:selected').attr('email'));
        } else {
            $(this).parent().parent().siblings().find("[name='memberMobileNumber']").val('');
            $(this).parent().parent().siblings().find("[name='memberEmailId']").val('');
        }
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

    $(document).on('click', '.project-status-input', function (e) {
        e.preventDefault();
        $(this).addClass('clicked');
        $(this).siblings('li').removeClass('clicked');
    });
    $('.edit-link').click(function () {
        $(this).siblings('input').prop("readonly", false).focus();
        // $(this).focus();

    });


    // $('.event-resources li').click(function () {
    //     $(this).toggleClass('active');
    //     $('.steps').removeClass('event-ul');
    //     var resourceSelected = $(this).children('span').html().toLowerCase();
    //     console.log(resourceSelected)
    //     $(".steps").find('li.' + resourceSelected).show();
    // });
    $('#selectProject').on('change', function () {
        $('.interest-selection').show();
        $('.btn-sec').show();
    });

    $('.primary-area-pre > li').on('click', function () {
        $('.primary-area-pre > li').removeClass('active');
        $(this).addClass('active');
        // $(this).toggleClass('active');
        $(this).parents('.main-form-group').siblings('.second-selection').css('opacity', 1);
        $(this).parents('.main-form-group').siblings('.second-selection').find('select').removeAttr('disabled');
    });

    $("#selectSecondArea").change(function () {
        var selectSecondOption = $(this).children("option:selected").val();
        $('.selected-options').append('<li>' + selectSecondOption + '<i class="fc-close"> </i> </li>');
    });

    $(document).on('click', '.selected-options .fc-close', function (e) {
        e.preventDefault();
        $(this).parent('li').remove();
    });


    checkWordLen = function () {
        var wordLen = 200,
            len; // Maximum word length
        $('#deployFunds').keydown(function (event) {
            len = $('#deployFunds').val().split(/[\s]+/);
            if (len.length > wordLen) {
                if (event.keyCode == 46 || event.keyCode == 8) {// Allow backspace and delete buttons
                } else if (event.keyCode < 48 || event.keyCode > 57) {//all other buttons
                    event.preventDefault();
                }
            }
            //console.log(len.length + " words are typed out of an available " + wordLen);
            wordsLeft = (wordLen) - len.length;
            $(this).siblings('.textarea-instru').find('.words-left').html(wordsLeft);
        });
    };
    checkWordLenBig = function () {
        var wordLen = 1000,
            len; // Maximum word length
        $('#theAppeal').keydown(function (event) {
            len = $('#theAppeal').val().split(/[\s]+/);
            if (len.length > wordLen) {
                if (event.keyCode == 46 || event.keyCode == 8) {// Allow backspace and delete buttons
                } else if (event.keyCode < 48 || event.keyCode > 57) {//all other buttons
                    event.preventDefault();
                }
            }
            //console.log(len.length + " words are typed out of an available " + wordLen);
            wordsLeft = (wordLen) - len.length;
            $(this).siblings('.textarea-instru').find('.words-left').html(wordsLeft);
        });
    };

    $($('ul.steps > li').get().reverse()).each(function (index) {
        $(this).css('z-index', index + 10);
    });

    $("#trn_circle_events_crowd_fund_event_portal_fromDate").datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: "yy-mm-dd",
        maxPicks: 1,
        minDate: new Date(),
        onSelect: function () {
            $("#trn_circle_events_crowd_fund_event_portal_toDate").removeAttr('disabled');
            $( "#trn_circle_events_crowd_fund_event_portal_toDate" ).datepicker( "option", "disabled", false );
            $( "#trn_circle_events_crowd_fund_event_portal_toDate" ).datepicker( "option", "minDate", $("#trn_circle_events_crowd_fund_event_portal_fromDate").val() );
        }
    });

    $("#trn_circle_events_crowd_fund_event_portal_toDate").datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: "yy-mm-dd",
        maxPicks: 1,
        disabled: true,
        minDate: 0,
    });

    if(isEditEvent == true) {

        $("#trn_circle_events_crowd_fund_event_portal_toDate").removeAttr('disabled');
        $( "#trn_circle_events_crowd_fund_event_portal_toDate" ).datepicker( "option", "disabled", false );
        $( "#trn_circle_events_crowd_fund_event_portal_toDate" ).datepicker( "option", "minDate", $("#trn_circle_events_crowd_fund_event_portal_fromDate").val() );
        $("#trn_circle_events_crowd_fund_event_portal_toDate").val(toDate);
    }

    $('.addLinkSubEvent a').click(function () {
        addCrowFundingSubEventToSession();
        $(this).parent('.AddLink').siblings('.DistributeBlock').show();
    });

    $(document).on('click', '.deleteSkill', function (e) {

        $(this).parents('ul').parent('li').remove();
        removeCrowdFundingSubEventToSession($(this).attr('crowdfundkey'));
    });

    $(document).on('click', '.AddDocumentLink a', function (e) {
        e.preventDefault();
        var newinput = $('.uploadedDocument > div:last').clone();
        $('.uploadedDocument > div:last').addClass('old');
        $(newinput).find('input').val('');
        $(newinput).find('input').each(function( index ) {
            if ($( this ).hasClass('custom-file-input')) {
                $(this).closest('.cloneSection').find('.previewDocEdit').remove();
                $(this).closest('.cloneSection').find('.divPreviewImg').hide();
                $(this).closest('.cloneSection').find('.divPreviewImg').find(".previewImg").attr("src", '');
                $(this).siblings().html('');
                $( this ).text('');
            } else {
                $( this ).val(null);
            }
        });
        $(newinput).find('.custom-file-label').html('Choose File');
        $(newinput).insertAfter(".uploadedDocument > div:last");
    });

    $(document).on('change', ".custom-file-input", function (e) {
        var fileExtension = ['jpeg', 'jpg', 'png', 'pdf'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only formats are allowed : "+fileExtension.join(', '));
            return false;
        }
        var fileExt = $(this).val().split('.').pop().toLowerCase();

        var fileName = $(this).val().split("\\").pop();
        console.log(fileName);
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        var oThis = this;
        var file = $(this).get(0).files[0];
        console.log(file);
        if(file){
            var reader = new FileReader();
            $(oThis).closest('.cloneSection').find('.divPreviewImg').show();
            $(oThis).closest('.cloneSection').find('.divPreviewImg').find(".previewImg").hide();
            $(oThis).closest('.cloneSection').find('.divPreviewImg').find(".previewPDF").hide();
            if (fileExt == 'pdf'){
                reader.onload = function(){
                    $(oThis).closest('.cloneSection').find('.divPreviewImg').find(".previewPDF").show();
                    console.log(reader);
                    $(oThis).closest('.cloneSection').find('.divPreviewImg').find(".previewPDF").attr("src", URL.createObjectURL(e.target.files[0]));
                    var pdfData = new Uint8Array(reader.result);
                    // Using DocumentInitParameters object to load binary data.
                    var loadingTask = pdfjsLib.getDocument({data: pdfData});
                    loadingTask.promise.then(function(pdf) {
                        console.log('PDF loaded');

                        // Fetch the first page
                        var pageNumber = 1;
                        pdf.getPage(pageNumber).then(function(page) {
                            console.log('Page loaded');

                            var scale = 1.5;
                            var viewport = page.getViewport({scale: scale});

                            // Prepare canvas using PDF page dimensions
                            var canvas = $(oThis).closest('.cloneSection').find('.divPreviewImg').find(".previewPDF");
                            var context = canvas.get(0).getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;

                            // Render PDF page into canvas context
                            var renderContext = {
                                canvasContext: context,
                                viewport: viewport
                            };
                            var renderTask = page.render(renderContext);
                            renderTask.promise.then(function () {
                                console.log('Page rendered');
                            });
                        });
                    }, function (reason) {
                        // PDF loading error
                        console.error(reason);
                    });

                }
                reader.readAsArrayBuffer(file);
            } else {
                reader.onload = function(){
                    $(oThis).closest('.cloneSection').find('.divPreviewImg').find(".previewImg").show();
                    $(oThis).closest('.cloneSection').find('.divPreviewImg').find(".previewImg").attr("src", reader.result);
                }
                reader.readAsDataURL(file);
            }



        }
    });

    $('#trn_circle_events_crowd_fund_event_portal_trnCircle').on('change', function () {
        console.log($(this).val());
        if ($.trim($(this).val()) != '') {
            getProjectsAreaOfInterest($.trim($(this).val()));
            //getProjectBankDetails($.trim($(this).val()));
            $('.interest-selection').show();
        } else {
            $('.btn-sec').hide();
            $('.interest-selection').hide();
            window.location.reload();
        }
    });

    if(isEditEvent == true) {
        $('#trn_circle_events_crowd_fund_event_portal_trnCircle').change();
    }

    $(document).on('click', '.primary-area-pre > li', function (e) {
        $('.primary-area-pre > li').removeClass('active');
        $(this).addClass('active');
        // $(this).toggleClass('active');
        $(this).parents('.main-form-group').siblings('.second-selection').css('opacity', 1);
        $(this).parents('.main-form-group').siblings('.second-selection').find('select').removeAttr('disabled');
        $('#primaryAreaOfInterest').val($(this).attr('areaInterest'));
        getSecondaryAreaOfInterest( $('#trn_circle_events_crowd_fund_event_portal_trnCircle').val(), $(this).attr
        ('areaInterest'));
        // remove previously selected secondary AI
        // $(this).parents('.main-form-group').siblings('.second-selection').find('.selected-options').remove();
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
                $('.selected-options').append('<li secAI = "' + $(this).children("option:selected").val() + '" >' + selectSecondOption + '<i class="fc-close"> </i> ' +
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
        var wordLen = 200,len; // Maximum word length
        len = $(this).val().split(/[\s]+/);
        if (len.length > wordLen) {
            if (event.keyCode == 46 || event.keyCode == 8 ||
                event.which == 46 || event.which == 8) {// Allow backspace and delete buttons
            } else {
                // if (event.keyCode < 48 || event.keyCode > 57) {//all other buttons
                event.preventDefault();
            }
        }
        wordsLeft = (wordLen) - len.length;
        $(this).siblings('.textarea-instru').find('.words-left').html(wordsLeft);
    });
    $(".textarea").keydown(function (){
        var wordLen = 1000,len; // Maximum word length
        len = $(this).val().split(/[\s]+/);
        if (len.length > wordLen) {
            if (event.keyCode == 46 || event.keyCode == 8 ||
                event.which == 46 || event.which == 8) {// Allow backspace and delete buttons
            } else {
                // if (event.keyCode < 48 || event.keyCode > 57) {//all other buttons
                event.preventDefault();
            }
        }
        wordsLeft = (wordLen) - len.length;
        $(this).siblings('.textarea-instru').find('.words-left').html(wordsLeft);
    });
    $(document).on('click', '#addMore', function (e) {
        e.preventDefault();
        var newinput = $('.youTube-form div:last').clone();
        $('.youTube-form div:last').addClass('old');

        $(newinput).find('input').each(function( index ) {
            $( this ).val('');
        });

        $(newinput).insertAfter(".youTube-form div:last");
    });
    $(document).on('click', '.removeInput.document', function (e) {

        if ($('.removeInput.document').length == 1) {
            $('.AddDocumentLink a').click();  // add empty block to have placeholder in place
            $(this).closest('div').remove();
            return false;
        }
        e.preventDefault();
        $(this).closest('div').remove();
    });
    $(document).on('click', '.removeInput.youtube', function (e) {

        if ($('.removeInput.youtube').length == 1) {
            $("#addMore").click(); // add empty block to have placeholder in place
            $(this).closest('div').remove();
            return false;
        }
        e.preventDefault();
        $(this).closest('div').remove();
    });
});
$('.image-gallery').click(function () {
    $(this).parents('.project-status').siblings('.image-gallery-div').show();
    $(this).parents('.project-status').siblings('.video-gallery-div').hide();
    $(this).addClass('clicked');
    $(this).siblings().removeClass('clicked');
    $("#reporting_Image").attr('checked', 'checked');
    $("#reporting_Video").removeAttr('checked');
});
$('.video-gallery').click(function () {
    $(this).parents('.project-status').siblings('.video-gallery-div').show();
    $(this).parents('.project-status').siblings('.image-gallery-div').hide();
    $(this).addClass('clicked');
    $(this).siblings().removeClass('clicked');
    $("#reporting_Image").removeAttr('checked');
    $("#reporting_Video").attr('checked', 'checked');
});

if(mediaType == 'video') {
    $('.video-gallery').click();
} else if(mediaType == 'image') {
    $('.image-gallery').click();
}

$('.input-images-2').imageUploader({
    label: 'Upload Main Image',
    imagesInputName: 'profileImage',
    extensions: ['.jpg', '.jpeg', '.png'],
    mimes: ['image/jpeg', 'image/png'],
    maxSize: 5248000, // 5.1MB
    preloaded: preloadedProfile,
    preloadedInputName: 'preloadedProfileImg'
});
$('.input-images-3').imageUploader({
    label: 'Upload Other Images',
    label2: 'Add More Images',
    imagesInputName: 'imageGallery[]',
    extensions: ['.jpg', '.jpeg', '.png'],
    mimes: ['image/jpeg', 'image/png'],
    maxSize: 5248000, // 5.1MB
    preloaded: preloadedGallery,
    preloadedInputName: 'preloadedGalImg'
});

function submit(elem){
    $("#submission_type").val($(elem).attr('id'));
    if(validate()) {
        $('[name="trn_circle_events_crowd_fund_event_portal"]').submit();
    }
    return false;
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
            if(isEditEvent == true) {
                selectPreloadedAI(primaryAISelected);
            }
        }
    });
}

function selectPreloadedAI(primaryArea) {

    $.each( primaryArea, function( key, primaryId ) {
        $("#liPrimaryAI"+primaryId).click();
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
            if(isEditEvent == true) {
                selectPreloadedSecondaryAI(secondaryAISelected);
                secondaryAISelected = []; // empty secondary area list after first load
            }
        }
    });
}

function selectPreloadedSecondaryAI(secondaryArea) {

    $.each( secondaryArea, function( key, secondaryId ) {
        $("#selectSecondArea").val(secondaryId);
        $("#selectSecondArea").change();
    });
}

/*
function getProjectBankDetails(nProjectId) {
    var data = {};
    data['nProjectId'] = nProjectId;

    jQuery.ajax({
        url: getProjectBankDetailsURL,
        data: data,
        type: "POST",
        dataType: "JSON",
        complete: function (data) {
            var product = $("#bankDetails");
            product.html(data.responseText);
        }
    });
}
*/

function validate() {

    if ($.trim($("#trn_circle_events_crowd_fund_event_portal_trnCircle").val()) == '') {
        alert('Please select project name.');
        $("#trn_circle_events_crowd_fund_event_portal_trnCircle").focus();
        return false;
    }

    if ($("#secondaryAreaOfInterest").length == 0) {
        alert('Please select Primary Area of Interest.');
        $("#trn_circle_events_crowd_fund_event_portal_trnCircle").focus();
        return false;
    }
    if ($.trim($("#secondaryAreaOfInterest").val()) == '') {
        alert('Please Add one or more Secondary Area of Interest.');
        $("#selectSecondArea").focus();
        return false;
    }

    if ($.trim($("#trn_circle_events_crowd_fund_event_portal_name").val()) == '') {
        alert('Please enter Name of the Campaign.');
        $("#trn_circle_events_crowd_fund_event_portal_trnCircle").focus();
        return false;
    }

    if(isEditEvent == true) {
        var targetAmtFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents_0_targetAmount");
    } else {
        var targetAmtFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents___name___targetAmount");
    }

    if ($.trim(targetAmtFld.val()) == '') {
        alert('Please enter Target Amount.');
        targetAmtFld.focus();
        return false;
    }

    if ($.isNumeric(targetAmtFld.val()) == false){
        alert('Please enter valid Target Amount.');
        targetAmtFld.val('');
        targetAmtFld.focus();
        return false;
    }

    /*if ($.trim($("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents___name___mstTargetAmountCurrency").val()) == '') {
        alert('Please select Target Amount currency.');
        $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents___name___mstTargetAmountCurrency").focus();
        return false;
    }*/

    if ($.trim($("#trn_circle_events_crowd_fund_event_portal_eventPurpose").val()) == '') {
        alert('Please enter How do you plan to deploy the funds details.');
        $("#trn_circle_events_crowd_fund_event_portal_eventPurpose").focus();
        return false;
    }

    if ($.trim($("#trn_circle_events_crowd_fund_event_portal_highlightsOfEvent").val()) == '') {
        alert('Please enter The Appeal.');
        $("#trn_circle_events_crowd_fund_event_portal_highlightsOfEvent").focus();
        return false;
    }

    if ($.trim($("#trn_circle_events_crowd_fund_event_portal_fromDate").val()) == '') {
        alert('Please enter valid From date.');
        $("#trn_circle_events_crowd_fund_event_portal_fromDate").focus();
        return false;
    }
    if ($.trim($("#trn_circle_events_crowd_fund_event_portal_toDate").val()) == '') {
        alert('Please enter valid To date.');
        $("#trn_circle_events_crowd_fund_event_portal_toDate").focus();
        return false;
    }

    if(isEditEvent == true) {
        var minContributionAmtFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents_0_minimumContribution");
    } else {
        var minContributionAmtFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents___name___minimumContribution");
    }

    if($.trim(minContributionAmtFld.val()) != '') {

        if ($.isNumeric(minContributionAmtFld.val()) == false){
            alert('Please enter valid Minimum Contribution Amount.');
            minContributionAmtFld.val('');
            minContributionAmtFld.focus();
            return false;
        }

        if(parseFloat(minContributionAmtFld.val()) >
            parseFloat(minContributionAmtFld.val())) {

            alert('Minimum Contribution Should be less than or equal to Target Amount.');
            minContributionAmtFld.focus();
            return false;
        }
    }

    var bPreloadedProfileImg = true;
    var bPreloadedGalleryImg = true;

    if($('input[name="preloadedProfileImg[]"]') == undefined || $('input[name="preloadedProfileImg[]"]').val() == undefined) {
        bPreloadedProfileImg = false;
    }
    if($('input[name="preloadedGalImg[]"]') == undefined || $('input[name="preloadedGalImg[]"]').val() == undefined) {
        bPreloadedGalleryImg = false;
    }

    if($("#reporting_Image").attr('checked') == 'checked') {
        var errormsg = '';
        var error = false;
        $('input[name="profileImage[]"]').each(function() {

            if($(this)[0].files[0] == undefined && bPreloadedProfileImg === false) {
                errormsg+="Please upload main image\n";
                error = true;
            } else if($(this)[0].files[0] != undefined) {
                var fileName = $(this)[0].files[0].name;

                if ($(this).val() == '' || fileName == undefined) {
                    errormsg += "Please upload main image\n";
                    error = true;
                }
            }
        });
        if(error == true) {
            alert(errormsg);
            return false;
        }
        $('input[name="imageGallery[][]"]').each(function() {

            if($(this)[0].files[0] == undefined && bPreloadedGalleryImg === false) {
                errormsg+="Please upload atleast one other image\n";
                error = true;
            } else if($(this)[0].files[0] != undefined) {
                var fileName = $(this)[0].files[0].name;

                if ($(this).val() == '' || fileName == undefined) {
                    errormsg += "Please upload atleast one other image\n";
                    error = true;
                }
            }
        });
        if(error == true) {
            alert(errormsg);
            return false;
        }
    }

    if($("#reporting_Video").attr('checked') == 'checked') {
        var atleastOneVideo = false;
        $('input[name="youTube[]"]').each(function() {
            if($(this).val() != '') {
                atleastOneVideo = true;
            }
        });

        if (atleastOneVideo == false) {
            alert('Please enter atleast one video URL');
            return false;
        }

        var videoUrlArr = [];
        var duplicate = [];
        var duplicateURLError = false;
        // youtube url duplication check
        $('input[name="youTube[]"]').each(function( index ) {

            if($( this ).val() != '') {
                videoUrlArr[index]  = $( this ).val();
                var sortedArr = videoUrlArr.sort();

                for (var i = 0; i < sortedArr.length - 1; i++) {
                    if (sortedArr[i + 1] == sortedArr[i]) {
                        duplicate.push(sortedArr[i]);
                        duplicateURLError = true;
                    }
                }
            }
        });
        if(duplicateURLError === true) {
            alert("Duplicate URLs entered");
            return false;
        }
    }

    var error = false;
    var errorMsg = '';
    $('input[name="filename[]"]').each(function( index ) {

        if($( this ).val() != '') {
            $('input[name="documentDescription[]"]').each(function(docIndex) {
                if(index == docIndex) {
                    if($(this).val() == '') {
                        error = true;
                        errorMsg = 'Please enter document caption';
                        return false;
                    }
                }
            });

        }
    });

    if(error === true) {
        alert(errorMsg);
        return false;
    }

    var documentNameArr = [];
    var duplicate = [];
    var error = false;
    // document name duplication check
    $('input[name="documentDescription[]"]').each(function( index ) {

        if($( this ).val() != '') {
            documentNameArr[index]  = $( this ).val();
            var sortedArr = documentNameArr.sort();

            for (var i = 0; i < sortedArr.length - 1; i++) {
                if (sortedArr[i + 1] == sortedArr[i]) {
                    duplicate.push(sortedArr[i]);
                    error = true;
                }
            }
        }
    });

    if(error === true) {
        alert("Duplicate Document Names entered");
        return false;
    }

    if(isEditEvent == true) {
        var isDistributedEventFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents_0_isDistributedEvent");
    } else {
        var isDistributedEventFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents___name___isDistributedEvent");
    }

    if(isDistributedEventFld.prop('checked') == true
        && $('input[name="memberId[]"]').length < 1) {
        alert("Please select atleast one member to distribute the event");
        return false;
    }

    if ($('#tnc').is(':checked') == false) {
        alert('Please confirm do you agree to Terms & Conditions.');
        $('#tnc').focus();
        return false;
    }

    if(isEditEvent == false) {
        isUniqueEventName().then(function (result) {
            // Run this when your request was successful
            if (result === false) {
                alert('Campaign name is already in the system');
                $("#trn_circle_events_crowd_fund_event_portal_name").focus();
                return false;
            } else {
                // submit form if no error
                $('[name="trn_circle_events_crowd_fund_event_portal"]').submit();
                return true;
            }
        }).catch(function (err) {
            // Run this when promise was rejected via reject()
            console.log(err)
        })
    } else {
        return true;
    }

    return false;
}

function addCrowFundingSubEventToSession() {

    if ($.trim($("#selectContributor").val()) == ''){
        alert('Please select Member Name');
        $("#selectContributor").focus();
        return false;
    }
    var berror = false;
    $('input[name="memberId[]"]').each(function() {
        if($(this).val() == $("#selectContributor").val()) {
            berror = true;
            return false;
        }
    });
    if(berror == true) {
        alert("Member already selected.\nPlease select another member");
        $("#memberName").val('');
        $("#selectContributor").val('');
        $("#memberMobileNumber").val('');
        $("#memberEmailId").val('');
        $("#distributeAmount").val('');
        return false;
    }
    if ($.trim($("#memberMobileNumber").val()) == ''){
        alert('Please enter Member Mobile Number');
        $("#memberMobileNumber").focus();
        return false;
    }
    if ($.isNumeric($("#memberMobileNumber").val()) == false || $("#memberMobileNumber").val().length != 10){
        alert('Please enter valid Member Mobile Number');
        $("#memberMobileNumber").val('');
        $("#memberMobileNumber").focus();
        return false;
    }
    if ($.trim($("#memberEmailId").val()) == ''){
        alert('Please enter Member Email Id');
        $("#memberEmailId").focus();
        return false;
    }
    if( !validateEmail($.trim($("#memberEmailId").val()))) {
        alert('Please enter valid Member Email Id');
        $("#memberEmailId").val('');
        $("#memberEmailId").focus();
        return false;
    }
    if ($.trim($("#distributeAmount").val()) == ''){
        alert('Please enter Distributing amount');
        $("#distributeAmount").focus();
        return false;
    }
    if ($.isNumeric($("#distributeAmount").val()) == false){
        alert('Please enter valid Distributing amount');
        $("#distributeAmount").val('');
        $("#distributeAmount").focus();
        return false;
    }

    if(isEditEvent == true) {
        var targetAmtFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents_0_targetAmount");
    } else {
        var targetAmtFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents___name___targetAmount");
    }

    if(targetAmtFld.val() == '') {
        alert('Please fill Target Amount first.');
        targetAmtFld.focus();
        return false;
    }

    if(parseFloat($("#distributeAmount").val()) >
        parseFloat(targetAmtFld.val())) {

        alert('Distribute Target Amount Should be less than or equal to Target Amount.');
        $("#distributeAmount").focus();
        return false;
    }

    if(isEditEvent == true) {
        var minContributionAmtFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents_0_minimumContribution");
    } else {
        var minContributionAmtFld = $("#trn_circle_events_crowd_fund_event_portal_trnCrowdFundEvents___name___minimumContribution");
    }

    if(minContributionAmtFld.val() !== '') {
        if ($.isNumeric(minContributionAmtFld.val()) === false){
            alert('Please enter valid Minimum Contribution Amount.');
            minContributionAmtFld.val('');
            minContributionAmtFld.focus();
            return false;
        }

        if(parseFloat($("#distributeAmount").val()) <
            parseFloat(minContributionAmtFld.val())) {

            alert('Distribute Target Amount Should be greater than or equal to Minimum Contribution Amount.');
            $("#distributeAmount").focus();
            return false;
        }
    }

    var data = {};
    data['memberName']         = $.trim($("#selectContributor option:selected").text());
    data['memberId']         = $.trim($("#selectContributor").val());
    data['memberMobileNumber'] = $.trim($("#memberMobileNumber").val());
    data['memberEmailId']      = $.trim($("#memberEmailId").val());
    data['distributeAmount']    = $.trim($("#distributeAmount").val());

    jQuery.ajax({
        url: addCrowFundingSubEventToSessionURL,
        data: data,
        type: "POST",
        dataType: "JSON",
        complete: function (data) {
            var product = $(".DistributeBlock");
            product.html(data.responseText);
            $("#memberName").val('');
            $("#selectContributor").val('');
            $("#memberMobileNumber").val('');
            $("#memberEmailId").val('');
            $("#distributeAmount").val('');
        }
    });
}

function loadCrowdFundingSubEventFromSession() {

    console.log('here to load');

    jQuery.ajax({
        url: loadCrowFundingSubEventFromSessionURL,
        data: {},
        type: "POST",
        dataType: "JSON",
        complete: function (data) {
            var product = $(".DistributeBlock");
            product.html(data.responseText);
            product.show();
            $("#memberName").val('');
            $("#selectContributor").val('');
            $("#memberMobileNumber").val('');
            $("#memberEmailId").val('');
            $("#distributeAmount").val('');
        }
    });
}

function removeCrowdFundingSubEventToSession(crowdfundkey){
    var data = {};
    data['key'] = crowdfundkey;
    jQuery.ajax({
        url: removeCrowdFundingSubEventToSessionURL,
        data: data,
        type: "POST",
        dataType: "JSON",
        complete: function (data) {
        }
    });
}

function validateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return emailReg.test( $email );
}

function isUniqueEventName() {
    return new Promise(function(resolve, reject) {

        var data = {};
        data['eventname'] = $.trim($("#trn_circle_events_crowd_fund_event_portal_name").val());

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

function changeInputVal(elem) {

    if($(elem).val() == 'on') {
        $(elem).attr('checked', 'checked');
        $('#trn_circle_events_crowd_fund_event_portal_isUrgentCrowdfund').val(1);
        $('#trn_circle_events_crowd_fund_event_portal_isUrgentCrowdfund').attr('checked', 'checked');
        $(elem).val('off');
    } else {
        $(elem).removeAttr('checked');
        $('#trn_circle_events_crowd_fund_event_portal_isUrgentCrowdfund').val(0);
        $('#trn_circle_events_crowd_fund_event_portal_isUrgentCrowdfund').removeAttr('checked');
        $(elem).val('on');
    }
}