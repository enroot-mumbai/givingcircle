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

    jQuery("#mst_areas_in_city_mstCountry").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/location/state_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#mst_areas_in_city_mstState");
                product.html('');
                // add options
                product.append('<option value="" >Select State..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
    jQuery("#mst_areas_in_city_mstState").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/location/city_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#mst_areas_in_city_mstCity");
                product.html('');
                // add options
                product.append('<option value="" >Select City..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });


});