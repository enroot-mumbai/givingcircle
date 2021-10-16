$(document).ready(function() {

    $('label[for=prd_product_media_mediaFileName]').remove();
    $("#prd_product_media_mediaFileName").fileinput({
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


