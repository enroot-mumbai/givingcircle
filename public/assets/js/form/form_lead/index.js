$("document").ready(function() {

    jQuery("#form_lead_mstCountry").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/location/state_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var result = $("#form_lead_mstState");
                result.html('');
                // add options
                result.append('<option value="" >Select State..</option>');
                $.each(data, function (id, name) {
                    result.append('<option value="' + name.id + '">' + name.name + '</option>');
                });
            }
        });
    });

    jQuery("#form_lead_mstState").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/location/city_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var result = $("#form_lead_mstCity");
                result.html('');
                // add options
                result.append('<option value="" >Select City..</option>');
                $.each(data, function (id, name) {
                    result.append('<option value="' + name.id + '">' + name.name + '</option>');
                });
            }
        });
    });



});
