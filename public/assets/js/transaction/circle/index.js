var mstAreaOfInterest = null;
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

    jQuery("#trn_circle_mstCountry").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/location/state_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#trn_circle_mstState");
                product.html('');
                // add options
                product.append('<option value="" >Select State..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
    jQuery("#trn_circle_mstState").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/location/city_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#trn_circle_mstCity");
                product.html('');
                // add options
                product.append('<option value="" >Select City..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
    jQuery("#trn_circle_orgCompany").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/company/member_organization_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#trn_circle_appUser");
                product.html('');
                // add options
                product.append('<option value="" >Select..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
                if(trn_circle_appUser != '' ){
                    jQuery("#trn_circle_appUser").val(trn_circle_appUser);
                    jQuery("#select2-trn_circle_appUser-container").text($( "#trn_circle_appUser option:selected" ).text());
                }
            }
        });
    });

    jQuery("#trn_circle_appUser").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        $(".divAreaOfInterest").hide();
        jQuery.ajax({
            url: "/core/company/get_bank_details",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {

                $("#trn_circle_beneficiaryBankName").val(data.bankName);
                $("#trn_circle_beneficiaryAccountHolderName").val(data.accountHolderName);
                $("#trn_circle_beneficiaryBankAccountNumber").val(data.accountNumber);
                $("#trn_circle_beneficiaryIfscCode").val(data.ifscCode);
                $("#trn_circle_mstBankAccountTypeBeneficiary").val(data.mstBankAccountType);
                $("#select2-trn_circle_mstBankAccountTypeBeneficiary-container").text(data.accountType);

                if ($.trim(data.bankName) != '') {
                    $("#trn_circle_beneficiaryBankName").attr('readonly',true);
                } else {
                    $("#trn_circle_beneficiaryBankName").removeAttr("readonly");
                }
                if ($.trim(data.accountHolderName) != '') {
                    $("#trn_circle_beneficiaryAccountHolderName").attr('readonly',true);
                } else {
                    $("#trn_circle_beneficiaryAccountHolderName").removeAttr("readonly");
                }
                if ($.trim(data.accountNumber) != '') {
                    $("#trn_circle_beneficiaryBankAccountNumber").attr('readonly',true);
                } else {
                    $("#trn_circle_beneficiaryBankAccountNumber").removeAttr("readonly");
                }
                if ($.trim(data.ifscCode) != '') {
                    $("#trn_circle_beneficiaryIfscCode").attr('readonly',true);
                } else {
                    $("#trn_circle_beneficiaryIfscCode").removeAttr("readonly");
                }
                if ($.trim(data.mstBankAccountType) != '') {
                    $("#trn_circle_mstBankAccountTypeBeneficiary").prop('disabled', true);
                } else {
                    $("#trn_circle_mstBankAccountTypeBeneficiary").prop('disabled', false);
                }
                if(data.country_id != ''){
                    $("#trn_circle_mstCountry").val(data.country_id);
                    $("#select2-trn_circle_mstCountry-container").text(data.country_name);
                }
                if(data.state_id != ''){
                    setStateId(data.country_id,data.state_id,data.state_name);
                }
                if(data.city_id != ''){
                    setCityId(data.state_id, data.city_id, data.city_name)
                }
                $(".divAreaOfInterest").show();
                mstAreaOfInterest = null;
                if(data.mstAreaOfInterest != ''){
                    mstAreaOfInterest = data.mstAreaOfInterest;
                }
            }
        });
    });

    jQuery('[name="trn_circle"]').submit(function(e) {
        $(':disabled').each(function(e) {
            $(this).removeAttr('disabled');
        })
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
            var product = $("#trn_circle_mstState");
            product.html('');
            // add options
            product.append('<option value="" >Select State..</option>');
            $.each(data, function (id, name) {
                product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
            });
            $("#trn_circle_mstState").val(state_id);
            $("#select2-trn_circle_mstState-container").text(state_name);
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
            var product = $("#trn_circle_mstCity");
            product.html('');
            // add options
            product.append('<option value="" >Select City..</option>');
            $.each(data, function (id, name) {
                product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
            });
            $("#trn_circle_mstCity").val(city_id);
            $("#select2-trn_circle_mstCity-container").text(city_name);
        }
    });
}

//Volunteer Circle Event Sub Events
$circleAreaOfInterest = jQuery('#circleAreaOfInterest').parent();
$circleAreaOfInterest.data('index', jQuery('#circleAreaOfInterest').find('.trnAreaOfInterest').length);

jQuery("#addcircleAreaOfInterest").on('click', function (e) {
    console.log(1);
    addcircleAreaOfInterest($circleAreaOfInterest);
});

function addcircleAreaOfInterest($circleAreaOfInterest) {
    // Get the data-prototype explained earlier
    var prototype = $circleAreaOfInterest.data('prototype');

    // get the new index
    var index = $circleAreaOfInterest.data('index');
    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    // increase the index with one for the next item
    $circleAreaOfInterest.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    jQuery("#circleAreaOfInterest").append(newForm);
    $('select').select2({
        theme: 'bootstrap4'
    });

    getAreaOfInterest(index);
}
//Volunteer Circle Event Sub Events Ends

function getAreaOfInterest(index) {
    if (mstAreaOfInterest != null) {
        var product = $("#trn_circle_trnAreaOfInterests_"+index+"_areaInterestPrimary");
        product.html('');
        // add options
        product.append('<option value="" >Select..</option>');
        $.each(mstAreaOfInterest, function (id, name) {
            product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
        });
        var product = $("#trn_circle_trnAreaOfInterests_"+index+"_areaInterestSecondary");
        product.html('');
        // add options
        product.append('<option value="" >Select..</option>');
        $.each(mstAreaOfInterest, function (id, name) {
            product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
        });

    }
}
