$(function() {
    $('.js-example-basic-single').select2();
    //$('#trn_volunter_circle_event_details_portal_mstSkillSet').multipleSelect()
})
$(document).ready(function () {

    $('#planicon').parents('li').addClass('active');

    $("#eventOnSiteCountry").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        $.ajax({
            url: "/portal/location/state_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#eventOnSiteState");
                product.html('');
                product.append('<option value="" ></option>');
                // add options
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });

    $("#eventOnSiteState").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        $.ajax({
            url: "/portal/location/city_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#eventOnSiteCity");
                product.html('');
                product.append('<option value="" ></option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
    $("#eventOnSiteCity").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        $.ajax({
            url: "/portal/location/area_in_city_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#eventOnSiteAreaInCity");
                product.html('');
                product.append('<option value="" ></option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });


    $('body').addClass('inner-pg create-element event logged');
    if($.trim($("[name='recurringNosOfHours']").val()) != '') {
        $("[name='recurringNosOfHours']").change();
    }

    $('.btn-continue').click(function (){
        if(validate()) {
            // set submission_type based of clicked button: review or submit or continue
            $("#submission_type").val($(this).attr('id'));
            $("[name='trn_volunter_circle_event_details_portal']").submit();
        }
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

    $("#trn_volunter_circle_event_details_portal_fromDate").datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: "yy-mm-dd",
        maxPicks: 1,
        minDate: new Date(),
        defaultDate: eventStartDate,
        onSelect: function () {
            $("#trn_volunter_circle_event_details_portal_toDate").removeAttr('disabled');
            $( "#trn_volunter_circle_event_details_portal_toDate" ).datepicker( "option", "disabled", false );
            $( "#trn_volunter_circle_event_details_portal_toDate" ).datepicker( "option", "minDate", $("#trn_volunter_circle_event_details_portal_fromDate").val() );
        }
    });

    $("#trn_volunter_circle_event_details_portal_toDate").datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: "yy-mm-dd",
        maxPicks: 1,
        disabled: true,
        minDate: 0,
        defaultDate: eventEndDate,
        onSelect: function () {
            //eventDates
            var fromdate = new Date($("#trn_volunter_circle_event_details_portal_fromDate").val());
            var todate = new Date($("#trn_volunter_circle_event_details_portal_toDate").val());

            var eventDates = {};
            eventDates[fromdate] = fromdate;
            eventDates[todate] = todate;

            $('#datepickerMonthly').multiDatesPicker({
                minDate: fromdate,
                maxDate: todate,
                beforeShowDay: function (date) {
                    var highlight = eventDates[date];
                    if (highlight) {
                        return [true, "eventHighlight", 'Tooltip text'];
                    } else {
                        return [true, '', ''];
                    }
                },
                onSelect: function (){

                    $("#selectedDatesMonthly").val($(this).val());

                    var dtArr = $(this).val().split(',');
                    dtArr.forEach(function (dtval){

                        var highlight = dtval;
                        if (highlight) {
                            return [true, "eventHighlight", 'Tooltip text'];
                        } else {
                            return [true, '', ''];
                        }

                    });
                },
                dayNamesMin: ["S", "M", "T", "W", "T", "F", "S"]
            });
        }
    });
    if(eventStartDate != '') {
        $fromcalendar = $("#trn_volunter_circle_event_details_portal_fromDate");
        $.datepicker._getInst($fromcalendar[0]).settings.onSelect();
    }

    if(eventEndDate != '') {
        $tocalendar = $("#trn_volunter_circle_event_details_portal_toDate");
        $.datepicker._getInst($tocalendar[0]).settings.onSelect();
    }

    $("#trn_volunter_circle_event_details_portal_fromTime").change(function (){

        if($.trim($("#trn_volunter_circle_event_details_portal_fromTime").val()) != '') {
            $("#trn_volunter_circle_event_details_portal_toTime").attr('disabled', false);
            $("#trn_volunter_circle_event_details_portal_toTime").val($(this).val());
            $("#trn_volunter_circle_event_details_portal_toTime").attr('min', $(this).val());
        } else {
            $("#trn_volunter_circle_event_details_portal_toTime").val('');
            $("#trn_volunter_circle_event_details_portal_toTime").attr('disabled', true);
        }
    });
    if($("#trn_volunter_circle_event_details_portal_fromTime").val() != ''){
        $("#trn_volunter_circle_event_details_portal_fromTime").change();
    }

    $(document).on('click', '.project-status-input', function (e) {
        e.preventDefault();
        $(this).addClass('clicked');
        $(this).siblings('li').removeClass('clicked');
    });
    $('.SkillAddLink').click(function () {
        /*if($("#trn_volunter_circle_event_details_portal_mstSkillSet").val() == '') {
            alert("Please select atleast one Specific Skill");
            return false;
        }*/

        if($.trim($("#skillset").val()) == '') {
            alert("Please add Specific Skill");
            return false;
        }
        getSkillAddedUI();
        // $(this).focus();
    });

    $(document).on('click', '.deleteSkill', function (e) {
        skillid = $(this).attr('skillid');
        //$('#trn_volunter_circle_event_details_portal_mstSkillSet option[value="'+skillid+'"]').remove();
        $(this).parents('ul.skillSet').parent('li').remove();
    });

    $('.event-option li.daily').on('click', function () {
        $("#recurringBy").val($.trim($(this).find('span').html()));
        $(this).addClass('active').siblings('li').removeClass('active');
        $(this).parents('.row').find('.eventRecurringOption .multiCheckbtn').hide();
        $(this).parents('.row').find('.eventRecurringOption .datePickerBox').hide();
    })
    $('.event-option li.weekly').on('click', function () {
        $("#recurringBy").val($.trim($(this).find('span').html()));
        $(this).addClass('active').siblings('li').removeClass('active');
        $(this).parents('.row').find('.eventRecurringOption .multiCheckbtn').css('display', 'flex')
        $(this).parents('.row').find('.eventRecurringOption .datePickerBox').hide();
    })
    $('.event-option li.monthly').on('click', function () {

        if($('#trn_volunter_circle_event_details_portal_fromDate').val() == '' ||
            $('#trn_volunter_circle_event_details_portal_toDate').val() == '') {
            alert('Please select Event start date and end date first');

            $("#recurringBy").val('');
            $(this).removeClass('active').siblings('li').removeClass('active');
            $(this).parents('.row').find('.eventRecurringOption .multiCheckbtn').hide();
            $(this).parents('.row').find('.eventRecurringOption .datePickerBox').hide();

            return false;
        }

        $("#recurringBy").val($.trim($(this).find('span').html()));
        $(this).addClass('active').siblings('li').removeClass('active');
        $(this).parents('.row').find('.eventRecurringOption .multiCheckbtn').hide();
        $(this).parents('.row').find('.eventRecurringOption .datePickerBox').show();
    })

    if(recurringBy == 'monthly') {
        $('.event-option li.monthly').click();
    } else if(recurringBy == 'weekly') {
        $('.event-option li.weekly').click();
    } else if(recurringBy == 'daily') {
        $('.event-option li.daily').click();
    }

    $('.multiCheckbtn a').click(function () {
        $(this).toggleClass('active');

        var selectedDaysArr = new Array();
        $('.multiCheckbtn a.active').each(function (){
            console.log($(this).attr('title'));
            selectedDaysArr.push($(this).attr('title'));
        });
        $("#selectedDatesWeekly").val(selectedDaysArr.join(','));
    })

    $('.eventOccurrence label').on('click', function (event) {
        $(this).addClass('active').siblings('label').removeClass('active');
        console.log($(this).attr('value'));
        $("#trn_volunter_circle_event_details_portal_mstEventOccurrence").val($(this).attr('value'));

        if ($('.eventOccurrence label.recurring').hasClass('active')) {
            $('.optionsBlock').show();
        } else {
            $('.optionsBlock').hide();
        }
        event.preventDefault();
    });

    $('.crowdfunding-radio label').on('click', function () {
        if($(this).parent().hasClass('eventOccurrence')){
            return ;
        }
        $(this).addClass('active').siblings('label').removeClass('active');
        if ($('.eventOccurrence label.recurring').hasClass('active')) {
            $('.optionsBlock').show();
        } else {
            $('.optionsBlock').hide();
        }
        if ($('.placeOfWOrk label.OnSite').hasClass('active')) {
            $("#trn_volunter_circle_event_details_portal_mstPlaceOfWork").val(1);
            $('.virtualBlock').hide();
            $('.OnSiteBlock').show();
            $('.OnSiteBlock-inner').show();
            $("#lblstartTime").html('Start Time');
            $("#lblendTime").html('End Time');
            $("#trn_volunter_circle_event_details_portal_fromTime").val('');
            $("#trn_volunter_circle_event_details_portal_fromTime").change(); // called to set toTime automatically
        } else {
            $("#trn_volunter_circle_event_details_portal_mstPlaceOfWork").val(2);
            $('.OnSiteBlock').hide();
            $('.OnSiteBlock-inner').each(function( index ) {
                $( this ).hide();
            });
            $("#lblstartTime").html('Check in');
            $("#lblendTime").html('Check out');
            $('.virtualBlock').show();
            $("#trn_volunter_circle_event_details_portal_fromTime").val('');
            $("#trn_volunter_circle_event_details_portal_fromTime").change(); // called to set toTime automatically
        }
    });

    $('.edit-address').click(function () {
        $(this).parent('.savedAddress').find('input').prop("readonly", false).focus().select();
    });

    $(".textarea").keydown(function (){
        wordCount($(this), 300);
    });

    $(".textarea").keyup(function (){
        wordCount($(this), 300);
    });

    $($('ul.steps > li').get().reverse()).each(function (index) {
        $(this).css('z-index', index + 10);
    });

    $(".datepicker").datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: "dd / mm / yy"

    });
    if (bHasSkills ) {
        setTimeout(function () {
            getSkillAddedUI();
        }, 1000);
    }
    if(eventOccurence != "" ) {
        if (eventOccurence == 'One Time')
            $('.eventOccurrence label[for="oneTime"]').click();
        else
            $('.eventOccurrence label[for="recurring"]').click();
    }
    if(placeOfWork != "" ) {
        if (placeOfWork == 'Virtual')
            $('.Virtual').click();
        else
            $('.OnSite').click();
    }
    if(eventCheckinTime != '') {
        console.log(eventCheckinTime);
        $("#trn_volunter_circle_event_details_portal_fromTime").val(eventCheckinTime);
        $("#trn_volunter_circle_event_details_portal_fromTime").change();
    }
    if(eventCheckoutTime != '') {
        $("#trn_volunter_circle_event_details_portal_toTime").val(eventCheckoutTime);
    }
});

/**
 * This function does and Ajax and gets the Skill UI
 */
function getSkillAddedUI() {
    var strSkillAdded = $("#skillset").val();
    var data = {};
    data['strSkillAdded'] = strSkillAdded;

    jQuery.ajax({
        url: getEventSkillAddedUIURL,
        data: data,
        type: "POST",
        dataType: "JSON",
        complete: function (data) {
            /*var product = $("#ulSkillAdded");
            product.html(data.responseText);*/
            $("#ulSkillAdded").append(data.responseText);
            $('.SkillAddLink').parents('.main-form-group').siblings('.skiilAdded').show();
            $('.SkillAddLink').parents('.main-form-group').siblings('.skiilAdded').find('li').show();
            $("#skillset").val('');
        }
    });
}

function validate() {

    if ($.trim($("#trn_volunter_circle_event_details_portal_workDescription").val()) == '') {
        alert('Please enter Broad Skills Required.');
        $("#trn_volunter_circle_event_details_portal_workDescription").focus();
        return false;
    }
    var wordLen = 300,len; // Maximum word length
    len = $("#trn_volunter_circle_event_details_portal_workDescription").val().split(/[\s]+/);
    if (len.length > wordLen) {
        if (event.keyCode == 46 || event.keyCode == 8 || event.which == 46 || event.which == 8) {// Allow backspace and delete buttons
        } else if (event.keyCode < 48 || event.keyCode > 57 || event.which < 48 || event.which > 57) {//all other buttons
            event.preventDefault();
        }
    }
    wordsLeft = (wordLen) - len.length;
    if (wordsLeft < 0) {
        alert('Maximum 300 words accepted.');
        $("#trn_volunter_circle_event_details_portal_workDescription").focus();
        return false;
    }

    /*if ($.trim($("#trn_volunter_circle_event_details_portal_mstSkillSet").val()) == '') {
        alert('Please select Specific Skills Required.');
        $("#trn_volunter_circle_event_details_portal_mstSkillSet").focus();
        return false;
    }*/
    /*var arrSelectedSkill = $("#trn_volunter_circle_event_details_portal_mstSkillSet").val();
    if (arrSelectedSkill.length != $('#ulSkillAdded>li').length){
        alert('Please click on Add button next to Skill.');
        $("#trn_volunter_circle_event_details_portal_mstSkillSet").focus();
        return false
    }*/

    var berror = false;
    var errormsg = '';
    if($('[name="hdnSubEventType[]').length == 0) {
        alert("Please Add atleast one Skill.");
        return false;
    }
    $('[name="hdnSubEventType[]').each(function (){

        var skillname = $(this).val();
        var skillnameSlug = $(this).val().toLowerCase().replace(' ','_');

        if(berror == false) {
            //  if already error found, move forward

            if($('#noHours_'+skillnameSlug).val() == '') {
                berror = true;
                errormsg = errormsg+skillname+" : Please select hours.\n";
            }
            if($('[name="skillSet['+skillnameSlug+']"]:checked').val() == undefined) {
                berror = true;
                errormsg = errormsg + skillname + " : Please select option.\n";
            }
        }
    });
    if(berror == true) {
        alert(errormsg);
        return false;
    }

    if ($.trim($("#trn_volunter_circle_event_details_portal_keyResponsibility").val()) == '') {
        alert('Please enter Key Responsibility.');
        $("#trn_volunter_circle_event_details_portal_keyResponsibility").focus();
        return false;
    }
    var wordLen = 300,len; // Maximum word length
    len = $("#trn_volunter_circle_event_details_portal_keyResponsibility").val().split(/[\s]+/);
    if (len.length > wordLen) {
        if (event.keyCode == 46 || event.keyCode == 8 || event.which == 46 || event.which == 8) {// Allow backspace and delete buttons
        } else if (event.keyCode < 48 || event.keyCode > 57 || event.which < 48 || event.which > 57) {//all other buttons
            event.preventDefault();
        }
    }
    wordsLeft = (wordLen) - len.length;
    if (wordsLeft < 0) {
        alert('Maximum 300 words accepted.');
        $("#trn_volunter_circle_event_details_portal_keyResponsibility").focus();
        return false;
    }

    if ($.trim($("#trn_volunter_circle_event_details_portal_fromDate").val()) == '') {
        alert('Please enter valid From date.');
        $("#trn_volunter_circle_event_details_portal_fromDate").focus();
        return false;
    }
    if ($.trim($("#trn_volunter_circle_event_details_portal_toDate").val()) == '') {
        alert('Please enter valid To date.');
        $("#trn_volunter_circle_event_details_portal_toDate").focus();
        return false;
    }

    if ($.trim($("#trn_volunter_circle_event_details_portal_mstEventOccurrence").val()) == '') {
        alert('Please select Event Occurrence.');
        $("#trn_volunter_circle_event_details_portal_toDate").focus();
        return false;
    }

    if ($.trim($("#trn_volunter_circle_event_details_portal_mstEventOccurrence").find(":selected").text()) == 'Recurring') {
        if($.trim($("#recurringBy").val()) == '') {
            alert('Please select Recurring Event Option as Dialy or Weekly or Monthly .');
            $("#recurringBy").focus();
            return false;
        }
        if($.trim($("[name='recurringNosOfHours']").val()) == '') {
            alert('Please select Recurring Event Number of Hours .');
            $("#recurringBy").focus();
            return false;
        }
    }

    if($("#trn_volunter_circle_event_details_portal_mstPlaceOfWork").val() == '') {
        alert('Please select Place of Work.');
        $("#trn_volunter_circle_event_details_portal_mstPlaceOfWork").focus();
        return false;
    }

    if($.trim($("#trn_volunter_circle_event_details_portal_mstPlaceOfWork :selected").text()) == 'On Site' ) {
        if($.trim($("#eventOnSiteAddress1").val()) == '') {
            alert("Please enter Address line 1");
            $("#eventOnSiteAddress1").focus();
            return false;
        }

        /*if($.trim($("#eventOnSitePinCode").val()) == '') {
            alert("Please enter Pincode");
            $("#eventOnSitePinCode").focus();
            return false;
        }

        if($.trim($("#eventOnSiteCountry").val()) == '') {
            alert("Please Select Country");
            $("#eventOnSiteCountry").focus();
            return false;
        }
        if($.trim($("#eventOnSiteState").val()) == '') {
            alert("Please Select State");
            $("#eventOnSiteState").focus();
            return false;
        }
        if($.trim($("#eventOnSiteState").val()) == '') {
            alert("Please Select State");
            $("#eventOnSiteState").focus();
            return false;
        }
        if($.trim($("#eventOnSiteCity").val()) == '') {
            alert("Please Select City");
            $("#eventOnSiteCity").focus();
            return false;
        }
        if($.trim($("#eventOnSiteAreaInCity").val()) == '') {
            alert("Please Select Area In City");
            $("#eventOnSiteAreaInCity").focus();
            return false;
        }*/
    }

    if($("#trn_volunter_circle_event_details_portal_fromTime") != undefined &&
        $("#trn_volunter_circle_event_details_portal_toTime") != undefined) {

        var start_time = $("#trn_volunter_circle_event_details_portal_fromTime").val();
        var end_time = $("#trn_volunter_circle_event_details_portal_toTime").val();

        //convert both time into timestamp
        var sttime = new Date("January 21, 2013 " + start_time + ":00:00");
        sttime = sttime.getTime();

        var endtime = new Date("January 21, 2013 " + end_time + ":00:00");
        endtime = endtime.getTime();

        var dateStartLabel = 'Start Time';
        var dateEndLabel = 'End Time';
        if($.trim($("#trn_volunter_circle_event_details_portal_mstPlaceOfWork :selected").text()) == 'Virtual') {
            dateStartLabel = 'Check in';
            dateEndLabel = 'Check out';
        }
        if((start_time == '00:00' || start_time == null) && (end_time == '00:00' || end_time == null)) {
            alert("Please enter valid time");
            $("#trn_volunter_circle_event_details_portal_fromTime").focus();
            return false;
        } else if(start_time != '00:00' && end_time == '00:00') {
            alert("Please enter valid "+dateEndLabel+" time");
            $("#trn_volunter_circle_event_details_portal_toTime").focus();
            return false;
        } else if(sttime == endtime) {
            alert(dateStartLabel+" time and "+dateEndLabel+" time cannot be same");
            $("#trn_volunter_circle_event_details_portal_fromTime").focus();
            return false;

        } else if(sttime > endtime) {

            alert(dateStartLabel+" time cannot be less than "+dateEndLabel+" time");
            $("#trn_volunter_circle_event_details_portal_fromTime").focus();
            return false;
        }
    }

    return true;
}

