var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$(document).ready(function () {
    $('body').addClass('inner-pg create-element event logged');
    $('html, body').animate(
        {
            scrollTop: $($('.next-section-arrow').attr('href')).offset().top - 95,
        },
        800,
        'linear'
    );
    $('html, body').animate(
        {
            scrollTop: $($('.next-section-arrow').attr('href')).offset().top - 95,
        },
        800,
        'linear'
    );

    $('#planicon').parents('li').addClass('active');
    $('.createCCBtn').click(function () {
        var newRow = $(".createCCBlock:last").clone(true, true);
        //$('.createCCBlock:last').addClass('old');

        /// change ids of country, state and city
        var lastid = newRow.find('.countrylist').attr('id');
        var lastidArr = lastid.split('_');
        var lastelemKey = lastidArr[1];
        var newelemKey = parseInt(lastelemKey) + 1;

        newRow.find('.countrylist').attr('id', 'collectionCenterCountry_'+newelemKey);
        newRow.find('.statelist').attr('id', 'collectionCenterState_'+newelemKey);
        newRow.find('.citylist').attr('id', 'collectionCenterCity_'+newelemKey);
        newRow.find('.opneOnDaysCls').attr('id', 'collectionCenterOpenOnDays_'+newelemKey);
        newRow.find('.statelist').children().remove().end().append('<option value="">Select</option>');
        newRow.find('.citylist').children().remove().end().append('<option value="">Select</option>');
        newRow.find('.citylist').attr('id', 'collectionCenterCity_'+newelemKey);

        $(newRow).find('.multiCheckbtn a').each(function () {
            $(this).removeClass('active');
            $(this).removeClass(lastelemKey);
            $(this).attr('sequence',newelemKey);
        });

        $(newRow).find('.form-control').each(function (index) {
            $(this).val('');

            var inplastid = $(this).attr('id');
            var inplastidArr = inplastid.split('_');
            var inplastelemid = inplastidArr[0];
            $(this).attr('id', inplastelemid+'_'+newelemKey)
        });

        $(newRow).insertAfter(".createCCBlock:last");

        /* Change datepicker property after adding elements - start */
        $("#collectionCenterFromDate_"+newelemKey).removeClass('hasDatepicker');
        $("#collectionCenterToDate_"+newelemKey).removeClass('hasDatepicker');

        $("#collectionCenterFromDate_"+newelemKey).datepicker({
            showOn: "both",
            buttonImage: "/resources/images/common/icons/icon_calender.png",
            buttonImageOnly: true,
            buttonText: "Select date",
            dateFormat: "yy-mm-dd",
            maxPicks: 1,
            minDate: $("#trn_material_in_kind_circle_event_details_portal_fromDate").val(),
            maxDate: $("#trn_material_in_kind_circle_event_details_portal_toDate").val(),
            onSelect: function () {
                $("#collectionCenterToDate_"+newelemKey).datepicker( "option", "disabled", false );
                $("#collectionCenterToDate_"+newelemKey).datepicker( "option", "minDate", $(this).val() );
            }
        });

        $("#collectionCenterToDate_"+newelemKey).datepicker({
            showOn: "both",
            buttonImage: "/resources/images/common/icons/icon_calender.png",
            buttonImageOnly: true,
            buttonText: "Select date",
            dateFormat: "yy-mm-dd",
            disabled: true,
            minDate: 0,
            maxDate: $("#trn_material_in_kind_circle_event_details_portal_toDate").val(),
        });
        /* Change datepicker property after adding elements - end */
    });

    $(document).on('change', '.countrylist', function() {
        countryListChange($(this));
    });
    $(document).on('change', '.statelist', function() {
        stateListChange($(this));
    });

    /*
    $('select[name="collectionCenterCountry[]"]').each(function (){
        bindCountryEvent($(this));
    });

    $('select[name="collectionCenterState[]"]').each(function () {
        bindStateEvent($(this));
    });
    */

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

    $("#trn_material_in_kind_circle_event_details_portal_fromDate").datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: "yy-mm-dd",
        maxPicks: 1,
        minDate: new Date(),
        onSelect: function () {
            $("#trn_material_in_kind_circle_event_details_portal_toDate").removeAttr('disabled');
            $( "#trn_material_in_kind_circle_event_details_portal_toDate" ).datepicker( "option", "disabled", false );
            $( "#trn_material_in_kind_circle_event_details_portal_toDate" ).datepicker( "option", "minDate", $("#trn_material_in_kind_circle_event_details_portal_fromDate").val() );
        }
    });
    $("#trn_material_in_kind_circle_event_details_portal_toDate").datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: "yy-mm-dd",
        maxPicks: 1,
        disabled: true,
        minDate: 0,
        /*onSelect: function () {
            // check if any dates of collection center available, then value should be empty and min and max dates should be changed
        }*/
    });

    $('.materialRequired .SkillAddLink').click(function () {
        var newRow = $(this).parents('.materialRequired').children('.row:last').clone();
        $('.materialRequired .row:last').addClass('old');
        $(newRow).insertAfter(".materialRequired .row:last");
        $(newRow).find('.form-control').each(function (index) {
            $(this).val('');
        });
    });
    $(document).on('click', '.removeInput', function (e) {
        if ($('.removeInput').length == 1)
            return false;
        e.preventDefault();
        // $(this).parent().siblings().removeClass('old');
        $(this).parent().remove();
    });
    $(document).on('click', '.btn-close', function (e) {

        $('#collectionCenterModal').modal('hide');
    });
    $($('ul.steps > li').get().reverse()).each(function (index) {
        $(this).css('z-index', index + 10);
    });
    $('.collection-center li').click(function () {

        if(validateCreateNewCollectionCenter()) {

            $(this).addClass('clicked');
            $(this).siblings('li').removeClass('clicked');

            if ($('.collection-center li.FromMaster').hasClass('clicked')) {

                $("#collectionCenterType").val('FromMaster');

                // remove old create new blocks
                $('.createCCBlock').each(function (){

                    if($(this).index() == 0) {
                        // empty entered data
                        console.log('in if');
                        $(this).find('.countrylist').val('');
                        $(this).find('.statelist').children().remove().end().append('<option value="">Select</option>');
                        $(this).find('.citylist').children().remove().end().append('<option value="">Select</option>');
                        $(this).find('.multiCheckbtn a').each(function () {
                            $(this).attr('sequence','1'); // remove all old class and just add 1 to create first set
                        });
                        $(this).find('.form-control').each(function (index) {
                            $(this).val('');
                        });

                    } else {
                        $(this).remove();
                    }
                });

                    $('.FromMasterBlock').show();
                    $('.CreateNewBlock').hide();
                    getCollectionCenterFromMaster();

            } else if ($('.collection-center li.CreateNew').hasClass('clicked')) {
                $("#collectionCenterType").val('CreateNew');
                $('.FromMasterBlock').hide();
                $('.CreateNewBlock').show();
            }
        } else return false;
    });
    /*if ($('.collection-center li.FromMaster').hasClass('clicked')) {
        $('.FromMasterBlock').show();
        $('.CreateNewBlock').hide();
        getCollectionCenterFromMaster();
    } else if ($('.collection-center li.CreateNew').hasClass('clicked')) {
        $('.FromMasterBlock').hide();
        $('.CreateNewBlock').show();
    }*/

    $(document).on('click','.multiCheckbtn a', function (){

        var sequence = $(this).attr('sequence');
        $(this).toggleClass('active');

        var selectedDaysArr = new Array();
        $('.multiCheckbtn a.active').each(function (){
            console.log('selected days active -'+$(this).attr('title'))

            if(sequence == $(this).attr('sequence')) {
                selectedDaysArr.push($(this).attr('title'));
            }
        });
        $(this).siblings('.newOpenOnDaysCls').val(selectedDaysArr.join(','));
    });

    $(document).on('click', '.removeButton', function (e) {
        $(this).parents('.main-form-group').hide();
        removeCollectionCenterFromSession($(this).attr('trnCollectionCentreDetails'));
    });

    $('.edit-address').click(function () {
        $(this).parent('.savedAddress').find('input').prop("readonly", false).focus().select();
    });

    $(document).on('click', '#btnCollectionCenterAdd', function (e) {

        addSelectedCollectionCenterToUI();
    });
    if (bHasCollectionCentre) {
        $("#collectionCenterFromMaster").click();
    }

    if (eventSubEvents.length > 0) {
        for (i = 1; i < eventSubEvents.length; i++) {
            $(".SkillAddLink").click();
        }
        $("input[name^='itemRequired[']").each(function (index) {
            $(this).val(eventSubEvents[index].itemName);
        });
        $("input[name^='quantity[']").each(function (index) {
            $(this).val(eventSubEvents[index].itemQuantity);
        });
        $("input[name^='unit[']").each(function (index) {
            $(this).val(eventSubEvents[index].unit);
        });
        $("input[name^='remarks[']").each(function (index) {
            $(this).val(eventSubEvents[index].subEventName);
        });
    }
});

function validateCreateNewCollectionCenter() {
    if($("#trn_material_in_kind_circle_event_details_portal_fromDate").val() == '') {
        alert("Please select event start date");
        $("#trn_material_in_kind_circle_event_details_portal_fromDate").focus();
        return false;
    }

    if($("#trn_material_in_kind_circle_event_details_portal_toDate").val() == '') {
        alert("Please select event end date");
        $("#trn_material_in_kind_circle_event_details_portal_toDate").focus();
        return false;
    }

    $("#collectionCenterFromDate_1").datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: "yy-mm-dd",
        maxPicks: 1,
        minDate: $("#trn_material_in_kind_circle_event_details_portal_fromDate").val(),
        maxDate: $("#trn_material_in_kind_circle_event_details_portal_toDate").val(),
        onSelect: function () {
            $("#collectionCenterToDate_1").datepicker( "option", "disabled", false );
            $("#collectionCenterToDate_1").datepicker( "option", "minDate", $(this).val() );
        }
    });

    $("#collectionCenterToDate_1").datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date",
        dateFormat: "yy-mm-dd",
        disabled: true,
        minDate: 0,
        maxDate: $("#trn_material_in_kind_circle_event_details_portal_toDate").val()
    });
    return true;
}

function submitData(elem) {

    if (validate() == true) {
        // set submission_type based of clicked button: review or submit or continue
        $("#submission_type").val($(elem).attr('id'));
        $("[name='trn_material_in_kind_circle_event_details_portal']").submit();
    }

}

function getCollectionCenterFromMaster() {

    jQuery.ajax({
        url: getCollectionCenterFromMasterURL,
        data: {'collectionCenter': existingCollectionCenterIds, 'circleId': circleId },
        type: "POST",
        dataType: "JSON",
        complete: function (data) {
            var product = $("#collection-center-table");

            $.when(product.html(data.responseText)).done(function(){
                if (bHasCollectionCentre) {
                    addSelectedCollectionCenterToUI();
                }
            });

        }
    });
}

function addSelectedCollectionCenterToUI() {
    jQuery.ajax({
        url: addSelectedCollectionCenterToUIURL,
        data: getFormData($("#frmCollectionCenterFromMaster")),
        type: "POST",
        dataType: "JSON",
        complete: function (data) {
            $('.btn-close').click();
            var product = $("#addCollectionCenterFromMaster");
            product.html(data.responseText);

            // console.log($(".collectionCenterDP"));

            $(".collectionCenterDP").each(function (){

                // console.log("idval - "+$(this).attr('id'));

                var idval = $(this).attr('id');
                var idArr = idval.split('_');//$(this).id.split('_');
                if(idArr[0] == 'FromDate') {
                    $(this).datepicker({
                        showOn: "both",
                        buttonImage: "/resources/images/common/icons/icon_calender.png",
                        buttonImageOnly: true,
                        buttonText: "Select date",
                        dateFormat: "yy-mm-dd",
                        minDate: $("#trn_material_in_kind_circle_event_details_portal_fromDate").val(),
                        maxDate: $("#trn_material_in_kind_circle_event_details_portal_toDate").val(),
                        onSelect: function () {
                            // $("#ToDate_"+idArr[1]).removeAttr('disabled');
                            $("#ToDate_"+idArr[1]).datepicker( "option", "disabled", false );
                            $("#ToDate_"+idArr[1]).datepicker( "option", "minDate", $(this).val() );
                        }

                    });
                }
                if(idArr[0] == 'ToDate') {
                    $(this).datepicker({
                        showOn: "both",
                        buttonImage: "/resources/images/common/icons/icon_calender.png",
                        buttonImageOnly: true,
                        buttonText: "Select date",
                        dateFormat: "yy-mm-dd",
                        disabled: true,
                        minDate: 0,
                        maxDate: $("#trn_material_in_kind_circle_event_details_portal_toDate").val()
                    });
                }
            });

            if(existingCollectionCenters.length > 0) {

                $(existingCollectionCenters).each(function (){
                    $("#FromDate_"+$(this)[0].idkey).val($(this)[0].center_from_date);
                    $("#ToDate_"+$(this)[0].idkey).val($(this)[0].center_to_date);
                    $("#ToDate_"+$(this)[0].idkey).datepicker( "option", "disabled", false );
                });
            }

            //
            // $(".createCollectionCenterDP").datepicker({
            //     showOn: "both",
            //     buttonImage: "/resources/images/common/icons/icon_calender.png",
            //     buttonImageOnly: true,
            //     buttonText: "Select date",
            //     dateFormat: "dd / mm / yy"
            //
            // });
        }
    });
}

function getFormData($form) {
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function (n, i) {
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

function validate() {
    var bError = false;
    $('input[name^="itemRequired"]').each(function () {
        if ($.trim($(this).val()) == '') {
            alert('Please Enter Item Required.')
            $(this).focus();
            bError = true;
            return false;
        }
    });

    if (bError)
        return false;


    var regEx = /^\d+(\.\d{1,2})?$/;

    $('input[name^="quantity"]').each(function () {
        var val = $.trim($(this).val());

        if (val == '' || val == 0) {
            alert('Please Enter Quantity.')
            $(this).focus();
            bError = true;
            return false;
        } else if (!$.isNumeric(val)) {
            $(this).focus();
            alert('Please Enter Numeric value for Quantity.')
            bError = true;
            return false;
        } else if (!(regEx.test(val))) {
            alert('Quantity can have number upto 2 decimal point.')
            $(this).focus();
            bError = true;
            return false;
        }
    });
    if (bError)
        return false;
    $('input[name^="unit"]').each(function () {

        var unitval = $.trim($(this).val());
        if (unitval == '') {
            alert('Please Enter Quantity Unit.')
            $(this).focus();
            bError = true;
            return false;
        } else if ($.isNumeric(unitval)) {
            alert('Numeric value not allowed for Unit.')
            $(this).focus();
            bError = true;
            return false;
        }
    });
    if (bError)
        return false;
    $('input[name^="remarks"]').each(function () {
        if ($.trim($(this).val()) == '') {
            alert('Please Enter remarks.')
            $(this).focus();
            bError = true;
            return false;
        }
    });
    if (bError)
        return false;

    if ($.trim($("#trn_material_in_kind_circle_event_details_portal_fromDate").val()) == '') {
        alert('Please enter valid From date.');
        $("#trn_material_in_kind_circle_event_details_portal_fromDate").focus();
        return false;
    }
    if ($.trim($("#trn_material_in_kind_circle_event_details_portal_toDate").val()) == '') {
        alert('Please enter valid To date.');
        $("#trn_material_in_kind_circle_event_details_portal_toDate").focus();
        return false;
    }

    var availabledatesArr = new Array();
    var eventAvailabledaysArr = new Array();
    var start = new Date($("#trn_material_in_kind_circle_event_details_portal_fromDate").val());
    var end = new Date($("#trn_material_in_kind_circle_event_details_portal_toDate").val());

    for (var d = start; d <= end; d.setDate(d.getDate() + 1)) {

        var tmpDt = new Date(d);
        availabledatesArr.push(tmpDt);
        var tmpVal = tmpDt.getDay();
        eventAvailabledaysArr.push(days[tmpVal]);
    }

    if($("#collectionCenterType").val() == 'CreateNew') {

        // validate new collection center fields

        $('input[name="collectionCenterFirstName[]"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Enter Firstname.');
                bError = true;
                $(this).focus();
                return false;
            }
        });
        if (bError)
            return false;
        $('input[name="collectionCenterLastName[]"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Enter Lastname.');
                bError = true;
                $(this).focus();
                return false;
            }
        });
        if (bError)
            return false;
        $('input[name="collectionCenterAddress1[]"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Enter Address Line 1.');
                bError = true;
                $(this).focus();
                return false;
            }
        });
        if (bError)
            return false;
        $('input[name="collectionCenterPinCode[]"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Enter Pincode.');
                bError = true;
                $(this).focus();
                return false;
            }
        });
        if (bError)
            return false;

        $('select[name="collectionCenterCountry[]"]').each(function () {

            if ($(this).val() == '') {
                alert('Please Select Country.');
                bError = true;
                $(this).focus();
                return false;
            }
        });
        if (bError)
            return false;
        $('select[name="collectionCenterState[]"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Select State.');
                bError = true;
                $(this).focus();
                return false;
            }
        });
        if (bError)
            return false;
        $('select[name="collectionCenterCity[]"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Select City.');
                bError = true;
                $(this).focus();
                return false;
            }
        });
        if (bError)
            return false;
        $('select[name="collectionCenterStartTime[]"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Select Collection Center Start Time.');
                bError = true;
                $(this).focus();
                return false;
            }
        });
        if (bError)
            return false;
        $('select[name="collectionCenterEndTime[]"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Select Collection Center End Time.');
                bError = true;
                $(this).focus();
                return false;
            }
        });
        if (bError)
            return false;

        // TODO: check for start time not greater than end time

        var selectedDaysArray = new Array();
        $('input[name="collectionCenterOpenOnDays[]"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Select Collection Center Open Days.');
                bError = true;
                $(this).focus();
                return false;
            } else {
                selectedDaysArray.push($(this).val());
            }
        });
        if (bError)
            return false;

        var fromDateArr = new Array();
        $('input[name="collectionCenterFromDate[]"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Select Collection Center: From Date.');
                bError = true;
                $(this).focus();
                return false;
            } else {
                fromDateArr.push($(this).val());
            }
        });
        if (bError)
            return false;

        var toDateArr = new Array();
        $('input[name="collectionCenterToDate[]"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Select Collection Center: To Date.');
                bError = true;
                $(this).focus();
                return false;
            } else {
                toDateArr.push($(this).val());
            }
        });
        if (bError)
            return false;

        // check if all the dates are selected in atleast one collection center
        var pendingDatesArr = checkAllDatesCovered(availabledatesArr, fromDateArr, toDateArr);
        if(pendingDatesArr.length > 0) {
            console.log(pendingDatesArr);
            alert("All Event dates are not covered");
            return false;
        }

        // check if CC days fall in CC selected dates range
        if(collectionCenterDaysRestriction(selectedDaysArray, fromDateArr, toDateArr ) == true) {
            alert("Collection center open days should fall in Collection Center dates range");
            return false;
        }
        // check if event days covered for all open on days
        var pendingDaysArr = checkValidDays(eventAvailabledaysArr, selectedDaysArray);

        if(pendingDaysArr.length > 0) {
            alert("All Event days should be covered");
            return false;
        }

    } else {
        // from master
        if ($('.addMoreBtn').length == 0) {
            alert('Please Enter Valid From Date for Collection Center');
            return false;
        }

        var i = 1;
        $('select[name^="startTime"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Select Start Time.');
                bError = true;
                $(this).focus();
                return false;
            }
        });
        if (bError)
            return false;

        $('select[name^="endTime"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Select End Time.');
                bError = true;
                $(this).focus();
                return false;
            }
        });
        if (bError)
            return false;

        var selectedDaysArray = new Array();
        $('input[name^="collectionCenterOpenOnDaysMst"]').each(function () {

            if ($(this).val() == '') {
                alert('Please Select Collection Center Open Days.');
                bError = true;
                $(this).focus();
                return false;
            } else {
                selectedDaysArray.push($(this).val());
            }
        });
        if (bError)
            return false;

        var fromDateArr = new Array();
        $('input[name^="FromDate"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Enter Valid From Date for Collection Center')
                $(this).focus();
                bError = true;
                return false;
            } else {
                fromDateArr.push($(this).val());
            }
        });
        if (bError)
            return false;

        var toDateArr = new Array();
        $('input[name^="ToDate"]').each(function () {
            if ($.trim($(this).val()) == '') {
                alert('Please Enter Valid To Date for Collection Center')
                $(this).focus();
                bError = true;
                return false;
            } else {
                toDateArr.push($(this).val());
            }
        });
        if (bError)
            return false;

        // check if all the dates are selected in atleast one collection center
        var pendingDatesArr = checkAllDatesCovered(availabledatesArr, fromDateArr, toDateArr);
        if(pendingDatesArr.length > 0) {
            console.log(pendingDatesArr);
            alert("All Event dates are not covered");
            return false;
        }

        // check if CC days fall in CC selected dates range
        if(collectionCenterDaysRestriction(selectedDaysArray, fromDateArr, toDateArr ) == true) {
            alert("Collection center open days should fall in Collection Center dates range");
            return false;
        }
        // check if event days covered for all open on days
        var pendingDaysArr = checkValidDays(eventAvailabledaysArr, selectedDaysArray);

        if(pendingDaysArr.length > 0) {
            alert("All Event days should be covered");
            return false;
        }
    }

    return true;
}

function collectionCenterDaysRestriction(selectedDaysArray, fromDateArr, toDateArr) {

    var outOfRestrictedList = false;
    fromDateArr.forEach(function (fromdate, index) {

        if (outOfRestrictedList == false) {
            let startDate = new Date(fromdate);
            let endDate = new Date(toDateArr[index]);
            let selectedDaysArr = selectedDaysArray[index].split(',');
            let daysArray = new Array();

            for (let d = startDate; d <= endDate; d.setDate(d.getDate() + 1)) {
                let dayno = new Date(d).getDay();

                if($.inArray(days[dayno], daysArray) == -1) {
                    daysArray.push(days[dayno]);
                }
            }

            selectedDaysArr.forEach(function (CCDay, dayindex) {

                if (outOfRestrictedList == false) {

                    if($.inArray(CCDay, daysArray) == -1) {

                        outOfRestrictedList = true;
                        return false;
                    }
                }
            });
        }
    });
    return outOfRestrictedList;
}

function checkValidDays(eventAvailabledaysArr, selectedDaysArray) {

    // var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    var tmpDaysArr = eventAvailabledaysArr;

    selectedDaysArray.forEach(function (selectedDay) {

        var selectedDayArr = selectedDay.split(',');
        eventAvailabledaysArr.forEach(function (availableDay) {

            if($.inArray(availableDay, selectedDayArr) != -1) {

                var remove_Item = availableDay;
                tmpDaysArr = $.grep(tmpDaysArr, function(value) {
                    return value != remove_Item;
                });
            }
        });
    });

    return tmpDaysArr;
}

function checkAllDatesCovered(availabledatesArr, fromDateArr, toDateArr) {

    var i = 1;
    var pendingDatesArray = availabledatesArr;

    fromDateArr.forEach(function (item, index) {

        var startDate = new Date(item);
        var endDate = new Date(toDateArr[index]);
        var dateArray = new Array();

        for (var d = startDate; d <= endDate; d.setDate(d.getDate() + 1)) {
            dateArray.push(new Date(d).getTime());
        }
        availabledatesArr.forEach(function (itemAvailableDt, index) {

            if($.inArray(itemAvailableDt.getTime(), dateArray) != -1) {
                var remove_Item = itemAvailableDt;

                pendingDatesArray = $.grep(pendingDatesArray, function(value) {
                    return value.getTime() != remove_Item.getTime();
                });
            }
        });
    });
    return pendingDatesArray;
}

function removeCollectionCenterFromSession(collectionCenterId) {
    jQuery.ajax({
        url: removeSelectedCollectionCenterFromUIURL,
        data: {'collectionCenterId': collectionCenterId},
        type: "POST",
        dataType: "JSON",
        complete: function (data) {
        }
    });

}

function removeNewCCBlock(elem){

    if($('a[name="removeCollectionCenterButton[]"]').length > 1) {
        $(elem).parents('.createCCBlock').remove();
        // $(elem).parents('.createCCBlock').hide();
    } else {
        alert('Add atleast one collection center');
    }
}

function bindCountryEvent(elem) {

    var idval = $(elem).attr('id');
    var idArr = idval.split('_');
    var elemKey = idArr[1];

    $("#collectionCenterCountry_"+elemKey).change(function () {
        var data = {};
        data['q'] = $(this).val();
        $.ajax({
            url: "/portal/location/state_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#collectionCenterState_"+elemKey);
                product.html('');
                product.append('<option value="" ></option>');
                // add options
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
}

function bindStateEvent(elem) {

    var idval = $(elem).attr('id');
    var idArr = idval.split('_');
    var elemKey = idArr[1];

    $("#collectionCenterState_"+elemKey).change(function () {
        var data = {};
        data['q'] = $(this).val();
        $.ajax({
            url: "/portal/location/city_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#collectionCenterCity_"+elemKey);
                product.html('');
                product.append('<option value="" ></option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
}

function countryListChange(elem) {

    var idval = $(elem).attr('id');
    var idArr = idval.split('_');
    var elemKey = idArr[1];

    var data = {};
    data['q'] = $(elem).val();
    $.ajax({
        url: "/portal/location/state_list",
        data: data,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            var product = $("#collectionCenterState_"+elemKey);
            product.html('');
            product.append('<option value="" ></option>');
            // add options
            $.each(data, function (id, name) {
                product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
            });

            // If country changed after selection, city dropdown should be empty
            var otherproduct = $("#collectionCenterCity_"+elemKey);
            otherproduct.html('');
            otherproduct.append('<option value="">Select</option>');
        }
    });
}

function stateListChange(elem) {

    var idval = $(elem).attr('id');
    var idArr = idval.split('_');
    var elemKey = idArr[1];

    var data = {};
    data['q'] = $(elem).val();

    $.ajax({
        url: "/portal/location/city_list",
        data: data,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            var product = $("#collectionCenterCity_"+elemKey);
            product.html('');
            product.append('<option value="" ></option>');
            $.each(data, function (id, name) {
                product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
            });
        }
    });
}