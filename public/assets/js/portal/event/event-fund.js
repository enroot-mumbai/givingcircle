$(document).ready(function () {
    $('body').addClass('inner-pg create-element event logged');
    $("[name='trn_circle_events_crowd_fund_event_portal']").attr('enctype', "multipart/form-data");

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
        $(this).siblings('input').focus();
        $(this).siblings('.cust-select').find('select').mouseenter();
    });
    $('.form-group .cust-select label').click(function () {
        $(this).stopPropagation()

    })

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
    $('.form-group select').change(function () {
        if (this.value.length !== 0) {
            $(this).parent().parent().find('label').addClass('clicked');
        } else {
            $(this).parent().parent().find('label').removeClass('clicked');
        }
    });
    $('.materialRequired .SkillAddLink').click(function () {
        var newRow = $(this).parents('.materialRequired').children('.row:last').clone();
        $('.materialRequired .row:last').addClass('old');
        $(newRow).insertAfter(".materialRequired .row:last");
        // $(this).focus();
    });
    $(document).on('click', '.removeInput', function (e) {
        e.preventDefault();
        $(this).parent('.old').remove();
    });
    $($('ul.steps > li').get().reverse()).each(function (index) {
        $(this).css('z-index', index + 10);
    });
    $('.collection-center li').click(function () {
        $(this).addClass('clicked');
        $(this).siblings('li').removeClass('clicked');

        if ($('.collection-center li.FromMaster').hasClass('clicked')) {
            $('.FromMasterBlock').show();
            $('.CreateNewBlock').hide();

        } else if ($('.collection-center li.CreateNew').hasClass('clicked')) {
            $('.FromMasterBlock').hide();
            $('.CreateNewBlock').show();
        }
    });
    if ($('.collection-center li.FromMaster').hasClass('clicked')) {
        $('.FromMasterBlock').show();
        $('.CreateNewBlock').hide();

    } else if ($('.collection-center li.CreateNew').hasClass('clicked')) {
        $('.FromMasterBlock').hide();
        $('.CreateNewBlock').show();
    }

    $('.multiCheckbtn a').click(function () {
        $(this).toggleClass('active');
    })
    $('.removeButton').click(function () {
        $(this).parents('.main-form-group').hide();
    })

    $('.edit-address').click(function () {
        $(this).parent('.savedAddress').find('input').prop("readonly", false).focus().select();
    });
    $('.btn-continue').click(function () {
        if(validate()) {
            // set submission_type based of clicked button: review or submit
            $("#submission_type").val($(this).attr('id'));
            $("[name='trn_fund_raiser_circle_event_details_portal']").submit();
        }

    });

    $('.SupportAddLink a').click(function () {
        $(this).parent('.SupportAddLink').siblings('.supportBlock').show();
        addFundRaiserSubEventsToSession();
        // $(this).focus();
    });

    $(document).on('click', '.deleteSkill', function (e) {
        $(this).parents('ul').parent('li').hide();
        removeFundRaiserSubEventsFromSession($(this).attr('key'));
    });

    $('.crowdfunding-radio label').on('click', function () {
        $(this).addClass('active').siblings('label').removeClass('active')
        if ($('.eventOccurrence label.oneTime').hasClass('active')) {
            $('#trn_fund_raiser_circle_event_details_portal_fromDate').val('');
            $('#trn_fund_raiser_circle_event_details_portal_toDate').val('');
            $('#trn_fund_raiser_circle_event_details_portal_toDate').attr('disabled', 'disabled');
            $('.eventDateBlock').show();
            $('#trn_fund_raiser_circle_event_details_portal_mstEventOccurrence').val(1);
        } else {
            var d = new Date();
            var dtval = ("0" + d.getDate()).slice(-2);
            var monthval = ("0" + parseInt(d.getMonth()+1)).slice(-2);
            var yearval = d.getFullYear();
            var nextyearval = yearval + 1;
            var fromdate = yearval+'-'+monthval+'-'+dtval;
            var todate = nextyearval+'-'+monthval+'-'+dtval;

            $('#trn_fund_raiser_circle_event_details_portal_fromDate').val(fromdate);
            // var oneYearFromNow = new Date();
            // oneYearFromNow.setFullYear(oneYearFromNow.getFullYear() + 1);
            $('#trn_fund_raiser_circle_event_details_portal_toDate').val(todate);
            $('#trn_fund_raiser_circle_event_details_portal_toDate').removeAttr('disabled');
            $("#trn_fund_raiser_circle_event_details_portal_mstEventOccurrence").val(2);
            $('.eventDateBlock').hide();
        }
    });

    $(".longtextarea").keyup(function (){
        charCount($(this), 300);
    });

    if (bHasSubEvents) {
        setTimeout(function () {
            $('.SupportAddLink').siblings('.supportBlock').show();
            jQuery.ajax({
                url: showFundRaiserSubEventsURL,
                data: {},
                type: "POST",
                dataType: "JSON",
                complete: function (data) {
                    var product = $(".supportAdded");
                    product.html(data.responseText);
                    $("#subEventTimePeriodSupported").val('');
                    $("#subEventNoOfBeneficiaries").val('');
                    $("#subEventAmount").val('');
                    $("#subEventRemarks").val('');
                    // $('#subEventCurrency').val(0); // not needed as only INR is supported
                }
            });
        }, 1000);
    }

    $("#trn_fund_raiser_circle_event_details_portal_fromDate").datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: "yy-mm-dd",
        maxPicks: 1,
        minDate: new Date(),
        onSelect: function () {
            $("#trn_fund_raiser_circle_event_details_portal_toDate").removeAttr('disabled');
            $( "#trn_fund_raiser_circle_event_details_portal_toDate" ).datepicker( "option", "disabled", false );
            $( "#trn_fund_raiser_circle_event_details_portal_toDate" ).datepicker( "option", "minDate", $("#trn_fund_raiser_circle_event_details_portal_fromDate").val() );
        }
    });
    $("#trn_fund_raiser_circle_event_details_portal_toDate").datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: "yy-mm-dd",
        maxPicks: 1,
        disabled: true,
        minDate: 0
    });



});

function validate(){
    if ($.trim($("#trn_fund_raiser_circle_event_details_portal_targetAmount").val()) == '') {
        alert('Please enter Total Funds Required.');//Target Amount
        $("#trn_fund_raiser_circle_event_details_portal_targetAmount").focus();
        return false;
    }
    if ($.isNumeric($("#trn_fund_raiser_circle_event_details_portal_targetAmount").val()) == false){
        alert('Please enter numeric value for Total Funds Required.');
        $("#trn_fund_raiser_circle_event_details_portal_targetAmount").val('');
        $("#trn_fund_raiser_circle_event_details_portal_targetAmount").focus();
        return false;
    }

    if ($.trim($("#trn_fund_raiser_circle_event_details_portal_mstCurrencyTargetAmount").val()) == '') {
        alert('Please select Target Amount currency.');
        $("#trn_fund_raiser_circle_event_details_portal_mstCurrencyTargetAmount").focus();
        return false;
    }

    if ($.trim($("#trn_fund_raiser_circle_event_details_portal_purposeOfRaisingFunds").val()) == '') {
        alert('Please enter Key Responsibility.');
        $("#trn_fund_raiser_circle_event_details_portal_purposeOfRaisingFunds").focus();
        return false;
    }
    var wordLen = 300,len; // Maximum word length
    len = $("#trn_fund_raiser_circle_event_details_portal_purposeOfRaisingFunds").val().split(/[\s]+/);
    if (len.length > wordLen) {
        if (event.keyCode == 46 || event.keyCode == 8) {// Allow backspace and delete buttons
        } else if (event.keyCode < 48 || event.keyCode > 57) {//all other buttons
            event.preventDefault();
        }
    }
    wordsLeft = (wordLen) - len.length;
    if (wordsLeft < 0) {
        alert('Maximum 300 words accepted.');
        $("#trn_fund_raiser_circle_event_details_portal_purposeOfRaisingFunds").focus();
        return false;
    }

    /*
    if($.trim($('#trn_fund_raiser_circle_event_details_portal_minContributionAmount').val()) != '' ) {

        if(!$.isNumeric($('#trn_fund_raiser_circle_event_details_portal_minContributionAmount').val()) ) {
            alert('Please enter numeric value for Minimum Contribution.');
            $("#trn_fund_raiser_circle_event_details_portal_minContributionAmount").val('');
            $("#trn_fund_raiser_circle_event_details_portal_minContributionAmount").focus();
            return false;
        }
        if($('#trn_fund_raiser_circle_event_details_portal_minContributionAmount').val() >  $("#trn_fund_raiser_circle_event_details_portal_targetAmount").val()) {
            alert('Minimum Contribution cannot be more than Target Amount.');
            $("#trn_fund_raiser_circle_event_details_portal_minContributionAmount").focus();
            return false;
        }
    }
    */

    if ($.trim($("#trn_fund_raiser_circle_event_details_portal_mstEventOccurrence").val()) == '') {
        alert('Please select Event Occurrence.');
        $("#trn_fund_raiser_circle_event_details_portal_toDate").focus();
        return false;
    }

    if ($.trim($("#trn_fund_raiser_circle_event_details_portal_fromDate").val()) == '') {
        alert('Please enter valid From date.');
        $("#trn_fund_raiser_circle_event_details_portal_fromDate").focus();
        return false;
    }
    if ($.trim($("#trn_fund_raiser_circle_event_details_portal_toDate").val()) == '') {
        alert('Please enter valid To date.');
        $("#trn_fund_raiser_circle_event_details_portal_toDate").focus();
        return false;
    }

    if($.trim($("#subEventTimePeriodSupported").val()) != '' ||
        $.trim($("#subEventNoOfBeneficiaries").val()) != '' ||
        $.trim($("#subEventAmount").val()) != '') {

        // if any of the three entries are entered in text box but not yet added,
        // ask user to either empty the box or to add the details
        alert('Please add Beneficiary details or empty the boxes');
        $("#subEventTimePeriodSupported").focus();
        return false;
    }

    // Validate bank details
    if($("#bankName").length && $("#bankName").val() == '') {
        alert('Please enter Bank Name');
        $("#bankName").focus();
        return false;
    }
    if($("#accountHolderName").length && $("#accountHolderName").val() == '') {
        alert('Please enter Account Holder Name');
        $("#accountHolderName").focus();
        return false;
    }
    if($("#accountNumber").length && $("#accountNumber").val() == '') {
        alert('Please enter Account Number');
        $("#accountNumber").focus();
        return false;
    }
    if($("#ifscCode").length && $("#ifscCode").val() == '') {
        alert('Please enter IFSC Code');
        $("#ifscCode").focus();
        return false;
    }
    if($("#bankAccountType").length && $("#bankAccountType").val() == '') {
        alert('Please select Account Type');
        $("#bankAccountType").focus();
        return false;
    }

    return true;
}

function addFundRaiserSubEventsToSession(){
    var data = {};

    /*if($.trim($("#subEventCurrency").val()) == '') {
        alert('Please Select Curreny from drop down.');
        $("#subEventCurrency").focus();
        return false;
    }*/
    data['subEventCurrency'] = $("#subEventCurrency").val();
    if($.trim($("#subEventTimePeriodSupported").val()) == '') {
        alert('Please Enter Time Period.');
        $("#subEventTimePeriodSupported").focus();
        return false;
    }
    data['subEventTimePeriodSupported'] = $("#subEventTimePeriodSupported").val();

    if($.trim($("#subEventNoOfBeneficiaries").val()) == '') {
        alert('Please Enter No of Beneficiaries.');
        $("#subEventNoOfBeneficiaries").focus();
        return false;
    }
    data['subEventNoOfBeneficiaries'] = $("#subEventNoOfBeneficiaries").val();

    if($.trim($("#subEventAmount").val()) == '') {
        alert('Please Enter Amount.');
        $("#subEventAmount").focus();
        return false;
    }
    data['subEventAmount'] =  $("#subEventAmount").val();
    data['subEventRemarks'] =  $("#subEventRemarks").val();
    jQuery.ajax({
        url: addFundRaiserSubEventsToSessionURL,
        data: data,
        type: "POST",
        dataType: "JSON",
        complete: function (data) {
            var product = $(".supportAdded");
            product.html(data.responseText);
            $("#subEventTimePeriodSupported").val('');
            $("#subEventNoOfBeneficiaries").val('');
            $("#subEventAmount").val('');
            $("#subEventRemarks").val('');
            // $('#subEventCurrency').val(0); // not needed as only INR is in use
        }
    });
}

function removeFundRaiserSubEventsFromSession(key) {
    var data = {};
    data['key'] = key;
    jQuery.ajax({
        url: removeFundRaiserSubEventsFromSessionURL,
        data: data,
        type: "POST",
        dataType: "JSON",
        complete: function (data) {
        }
    });
}

Date.prototype.toShortFormat = function() {

    let day = this.getDate();

    let monthIndex = this.getMonth();
    let month = monthIndex+1;

    let year = this.getFullYear();

    return `${year}-${month.pad()}-${day}`;
}

Number.prototype.pad = function(size) {
    var s = String(this);
    while (s.length < (size || 2)) {s = "0" + s;}
    return s;
}

function charCount(elem, wordLen) {
    //var wordLen = 300,len; // Maximum word length
    len = $(elem).val().split(/[\s]+/);
    if (len.length > wordLen) {

        if (event.keyCode == 46 || event.keyCode == 8 || event.which == 46 || event.which == 8 ) {
            // Allow backspace and delete buttons
        } else if (event.keyCode < 48 || event.keyCode > 57 || event.which < 48 || event.which > 57) {
            // Disable all other buttons
            event.preventDefault();
        }
        // Remove extra words
        var delCount = len.length - wordLen;
        var newArr = len.slice(0, -delCount);

        $(elem).val(newArr.join(' '));
    } else {
        wordsLeft = (wordLen) - len.length;
        $(elem).siblings('.textarea-instru').find('.words-left').html(wordsLeft);
    }
}

function changeInputVal(elem) {

    if($(elem).val() == 'on') {
        $(elem).attr('checked', 'checked');
        $('#trn_fund_raiser_circle_event_details_portal_isUrgent').val(1);
        $('#trn_fund_raiser_circle_event_details_portal_isUrgent').attr('checked', 'checked');
        $(elem).val('off');
    } else {
        $(elem).removeAttr('checked');
        $('#trn_fund_raiser_circle_event_details_portal_isUrgent').val(0);
        $('#trn_fund_raiser_circle_event_details_portal_isUrgent').removeAttr('checked');
        $(elem).val('on');
    }
}