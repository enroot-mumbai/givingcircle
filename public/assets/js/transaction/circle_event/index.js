$(document).ready(function () {
    var citysearch = $('#citySearch');
    var countryid = $('.country');
    citysearch.attr('disabled', 'disabled');

    countryid.change(function () {
        console.log($(this).val());
        if ($(this).val() !== '') {
            citysearch.removeAttr("disabled");
        } else {
            citysearch.attr('disabled', 'disabled');
        }
    });

    citysearch.keyup(function(e) {
        if(e.keyCode === 13) {
            citylisting();
        }
    });
    $('.citysearchbutton').on('click', function() {
        citylisting();
    });
    function citylisting() {
        var data = {};
        data['countryId'] = countryid.val();
        data['citySearch'] = citysearch.val();
        var showcitylisting = $('.cityListing');
        $('.loading').show();
        $.ajax({
            url: '/master/place/city/search',
            type: 'GET',
            data: data,
            dataType: "html",
            success: (function (formdata) {
                $('.loading').hide();
                showcitylisting.html(formdata);
                showcitylisting.show();

            }),
        });
    }

    jQuery("#trn_circle_events_mstCountry").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/location/state_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#trn_circle_events_mstState");
                product.html('');
                // add options
                product.append('<option value="" >Select State..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
    jQuery("#trn_circle_events_mstState").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/location/city_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#trn_circle_events_mstCity");
                product.html('');
                // add options
                product.append('<option value="" >Select City..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });

    jQuery("#trn_circle_events_orgCompany").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/company/member_organization_list_ce",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#trn_circle_events_appUser");
                product.html('');
                // add options
                product.append('<option value="" >Select..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });

    jQuery("#trn_circle_events_appUser").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        var strAppUserName = $.trim($( "#trn_circle_events_appUser option:selected" ).text());
        var arrNameSplit = strAppUserName.split('-');
        if (arrNameSplit.length > 1 ){
            if($.trim(arrNameSplit[1]) == 'Org') {
                $("#trn_circle_events_isAffiliated").prop('checked', true);
            } else {
                $("#trn_circle_events_isAffiliated").prop('checked', false);
            }
        }
        jQuery.ajax({
            url: "/core/company/get_bank_details",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                if(data.country_id != ''){
                    $("#trn_circle_events_mstCountry").val(data.country_id);
                    $("#select2-trn_circle_events_mstCountry-container").text(data.country_name);
                }
                if(data.state_id != ''){
                    setStateId(data.country_id,data.state_id,data.state_name);
                }
                if(data.city_id != ''){
                    setCityId(data.state_id, data.city_id, data.city_name)
                }
            }
        });

        jQuery.ajax({
            url: "/core/circle/get_app_user_circles",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#trn_circle_events_trnCircle");
                product.html('');
                product.append('<option value="" >Select..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });

    jQuery("#trn_circle_events_trnCircle").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/circle/get_area_of_interest",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#divAreaOfInterest");
                product.html('');
                var strHtml = "";
                $.each(data, function (id, name) {
                    strSecondaryArea = name.secondaryAreaOfInterest.join(', ');
                    strHtml += '<div class="row">';
                    strHtml += '<div class="col-sm-3">' +
                        '<div class="form-group">' +
                        '<label for="primaryAreaOfInterest" class="required">Primary Area Of' +
                        ' Interest</label>' +
                        '<input type="text" id="primaryAreaOfInterest" name="projectCompany"' +
                        ' class="form-control" readonly value="'+ name.primaryAreaOfInterest +'"/>' +
                        '</div></div>'+
                        '<div class="col-sm-3"><div class="form-group"><label for="projectCompany" class="required">Secondary Area Of' +
                        ' Interest</label>' +
                        '<input type="text" id="secondarAreaOfInterest" name="projectCompany"' +
                        ' class="form-control" readonly value="'+ strSecondaryArea +'"/>' +
                        '</div></div>';
                    strHtml += '</div>';
                });
                product.html(strHtml);
            }
        });
    });

});


function setStateId(country_id, state_id, state_name) {
    var data = {};
    data['q'] = country_id;
    jQuery.ajax({
        url: "/core/location/state_list",
        data: data,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            var product = $("#trn_circle_events_mstState");
            product.html('');
            // add options
            product.append('<option value="" >Select State..</option>');
            $.each(data, function (id, name) {
                product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
            });
            $("#trn_circle_events_mstState").val(state_id);
            $("#select2-trn_circle_events_mstState-container").text(state_name);
        }
    });
}
function setCityId(state_id, city_id, city_name) {
    var data = {};
    data['q'] = state_id;
    jQuery.ajax({
        url: "/core/location/city_list",
        data: data,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            var product = $("#trn_circle_events_mstCity");

            product.html('');
            // add options
            product.append('<option value="" >Select City..</option>');
            $.each(data, function (id, name) {
                product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
            });
            $("#trn_circle_events_mstCity").val(city_id);
            $("#select2-trn_circle_events_mstCity-container").text(city_name);
        }
    });
}