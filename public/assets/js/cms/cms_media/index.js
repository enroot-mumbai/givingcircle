$(document).ready(function() {

    jQuery("#cms_media_mediaCategory").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/master/general/media_subcategory/list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#cms_media_mediaSubCategory");
                product.html('');
                // add options
                product.append('<option value="" >Select ..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.mediaSubCategory + '</option>');
                });
            }
        });
    });

    $('label[for=cms_media_mediaImage]').remove();
    $("#cms_media_mediaImage").fileinput({
        theme: "fa",
        overwriteInitial: true,
        maxFileSize: 2000,
        showClose: false,
        showCaption: false,
        showBrowse: false,
        browseOnZoneClick: true,
        removeLabel: '',
        defaultPreviewContent: '<h6 class="text-muted">Click to upload image</h6>',
        layoutTemplates: {main2: '{preview} {remove} {browse}'},
        allowedFileExtensions: ["jpg", "png"]
    });

});


