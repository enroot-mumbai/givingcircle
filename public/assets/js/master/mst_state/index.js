$(document).ready(function () {
    var countryid = $('.country');
    countryid.change(function () {
        if ($(this).val() !== '') {
            statelisting();
        } else {
            showstatelisting.html('');
        }
    });

    function statelisting() {
        var data = {};
        data['countryId'] = countryid.val();
        var showstatelisting = $('.stateListing');
        $('.loading').show();
        $.ajax({
            url: '/core/master/place/state/search',
            type: 'GET',
            data: data,
            dataType: "html",
            success: (function (formdata) {
                $('.loading').hide();
                showstatelisting.html(formdata);
                showstatelisting.show();

            }),
        });
    }
});