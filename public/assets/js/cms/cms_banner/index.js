$(document).ready(function() {
    $('label[for=cms_banner_bannerDesktopImage]').remove();
    $('label[for=cms_banner_bannerTabletImage]').remove();
    $('label[for=cms_banner_bannerMobileImage]').remove();

    $("#cms_banner_bannerDesktopImage").fileinput({
        theme: "fa",
        overwriteInitial: true,
        maxFileSize: 1024,
        showClose: false,
        showCaption: false,
        showBrowse: false,
        browseOnZoneClick: true,
        removeLabel: '',
        defaultPreviewContent: '<h6 class="text-muted">Click to upload desktop banner</h6>',
        layoutTemplates: {main2: '{preview} {remove} {browse}'},
        allowedFileExtensions: ["jpg", "png", "jpeg"],
        maxImageWidth: $("#maxDesktopImageWidth").val(),
        maxImageHeight: $("#maxDesktopImageHeight").val(),
        retryErrorUploads: false,
        removeFromPreviewOnError: true,
    });
    $("#cms_banner_bannerTabletImage").fileinput({
        theme: "fa",
        overwriteInitial: true,
        maxFileSize: 1024,
        showClose: false,
        showCaption: false,
        showBrowse: false,
        browseOnZoneClick: true,
        removeLabel: '',
        defaultPreviewContent: '<h6 class="text-muted">Click to upload tablet banner</h6>',
        layoutTemplates: {main2: '{preview} {remove} {browse}'},
        allowedFileExtensions: ["jpg", "png", "jpeg"],
        maxImageWidth: $("#maxTabletImageWidth").val(),
        maxImageHeight: $("#maxTabletImageHeight").val(),
        retryErrorUploads: false,
        removeFromPreviewOnError: true,
    });
    $("#cms_banner_bannerMobileImage").fileinput({
        theme: "fa",
        overwriteInitial: true,
        maxFileSize: 1024,
        showClose: false,
        showCaption: false,
        showBrowse: false,
        browseOnZoneClick: true,
        removeLabel: '',
        defaultPreviewContent: '<h6 class="text-muted">Click to upload mobile banner</h6>',
        layoutTemplates: {main2: '{preview} {remove} {browse}'},
        allowedFileExtensions: ["jpg", "png", "jpeg"],
        maxImageWidth: $("#maxMobileImageWidth").val(),
        maxImageHeight: $("#maxMobileImageHeight").val(),
        retryErrorUploads: false,
        removeFromPreviewOnError: true,
    });

    // Copy the intro text to alt and title
    $("#cms_banner_bannerImageSetName").keyup(function(){
        var Text = $(this).val();
        $("#cms_banner_bannerImageAlt").val(Text);
        $("#cms_banner_bannerImageTitle").val(Text);
    });

    $('#cms_banner_bannerMediaType').on('change', function() {
        var mediatype = $(this).val();
        if (mediatype === 'image') {
            if(mode == 'add') {
                // no need to check it in edit mode as image is already uploaded and no way to delete the same
                $("#cms_banner_bannerDesktopImage").prop('required' ,true);
                $("#cms_banner_bannerTabletImage").prop('required' ,true);
                $("#cms_banner_bannerMobileImage").prop('required' ,true);
            }

            $("#cms_banner_bannerImageSetName").prop('required', true);
            $('label[for=cms_banner_bannerImageSetName]').addClass('required');
            $("#cms_banner_bannerImageTitle").prop('required', true);
            $('label[for=cms_banner_bannerImageTitle]').addClass('required');
            $("#cms_banner_bannerImageAlt").prop('required', true);
            $('label[for=cms_banner_bannerImageAlt]').addClass('required');

            $("#cms_banner_bannerVideo").prop('required', false);
            $('label[for=cms_banner_bannerVideo]').removeClass('required');
            $("#cms_banner_bannerVideoPath").prop('required', false);
            $('label[for=cms_banner_bannerVideoPath]').removeClass('required');
        }
        if (mediatype === 'video') {
            if(mode == 'add') {
                // no need to check it in edit mode as image is already uploaded and no way to delete the same
                $("#cms_banner_bannerDesktopImage").prop('required' ,false);
                $("#cms_banner_bannerTabletImage").prop('required' ,false);
                $("#cms_banner_bannerMobileImage").prop('required' ,false);
            }

            $("#cms_banner_bannerImageSetName").prop('required', false);
            $('label[for=cms_banner_bannerImageSetName]').removeClass('required');
            $("#cms_banner_bannerImageTitle").prop('required', false);
            $('label[for=cms_banner_bannerImageTitle]').removeClass('required');
            $("#cms_banner_bannerImageAlt").prop('required', false);
            $('label[for=cms_banner_bannerImageAlt]').removeClass('required');

            $("#cms_banner_bannerVideo").prop('required', true);
            $('label[for=cms_banner_bannerVideo]').addClass('required');
            $("#cms_banner_bannerVideoPath").prop('required', true);
            $('label[for=cms_banner_bannerVideoPath]').addClass('required');
        }
    });
    $('#cms_banner_bannerMediaType').change(); // for default load


});