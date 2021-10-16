var $collectionHolder;
var $pageURL = $(location).attr("href");
$collectionHolder = jQuery('#mediaContent').parent();
$collectionHolder.data('index', jQuery('#mediaContent').find('.trnMediaContent').length);

jQuery(document).on('click', '.btn-danger', function (e) {
    jQuery(this).closest(".trnMediaContent").remove();
});
jQuery("#addContent").on('click', function (e) {
    addRowContentForm($collectionHolder);
});

function addRowContentForm($collectionHolder) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');
    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    jQuery("#mediaContent").append(newForm)

    // // First Content block mandatory
    // $("#prd_product_media_mediaCollection_0_mediaFileName").prop('required', true);
    // $('label[for=prd_product_media_mediaCollection_0_mediaFileName]').addClass('required').show();

    $("#trn_product_media_mediaCollection_"+index+"_mediaFileName").fileinput({
        theme: "fa",
        overwriteInitial: true,
        maxFileSize: 5000,
        showClose: false,
        showCaption: false,
        showBrowse: false,
        browseOnZoneClick: true,
        removeLabel: '',
        defaultPreviewContent: '<h6 class="text-muted">Click to upload image</h6>',
        layoutTemplates: {main2: '{preview} {remove} {browse}'},
        allowedFileExtensions: ["jpg","jpeg", "png"]
    });

    // Content Image Upload
    $('label[for=trn_product_media_mediaCollection_'+index+'_mediaFileName]').remove();

    $("#trn_product_media_mediaCollection_"+index+"_mediaName").keyup(function(){
        var Text = $(this).val();
        $("#trn_product_media_mediaCollection_"+index+"_mediaAltText").val(Text);
        $("#trn_product_media_mediaCollection_"+index+"_mediaTitle").val(Text);
    });

    $("#trn_product_media_mediaCollection_"+index+"_mediaFileName").on('fileselect', function(event, numFiles, label) {
        $("#trn_product_media_mediaCollection_"+index+"_mediaName").prop('required', true);
        $('label[for=trn_product_media_mediaCollection_'+index+'_mediaName]').addClass('required');
        $("#trn_product_media_mediaCollection_"+index+"_mediaAltText").prop('required', true);
        $('label[for=trn_product_media_mediaCollection_'+index+'_mediaAltText]').addClass('required');
        $("#trn_product_media_mediaCollection_"+index+"_mediaTitle").prop('required', true);
        $('label[for=trn_product_media_mediaCollection_'+index+'_mediaTitle]').addClass('required');
    });

    $("#trn_product_media_mediaCollection_"+index+"_mediaFileName").on('fileclear', function(event) {
        $("#trn_product_media_mediaCollection_"+index+"_mediaName").prop('required', false);
        $('label[for=trn_product_media_mediaCollection_'+index+'_mediaName]').removeClass('required');
        $("#trn_product_media_mediaCollection_"+index+"_mediaAltText").prop('required', false);
        $('label[for=trn_product_media_mediaCollection_'+index+'_mediaAltText]').removeClass('required');
        $("#trn_product_media_mediaCollection_"+index+"_mediaTitle").prop('required', false);
        $('label[for=trn_product_media_mediaCollection_'+index+'_mediaTitle]').removeClass('required');
    });

    // Select2 for selectbox
    $('select').select2({
        theme: 'bootstrap4'
    })

}

$(document).ready(function () {
    jQuery("#trn_product_media_trnCircle").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/circle/event_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var circlevent = $("#trn_product_media_trnCircleEvents");
                circlevent.html('');
                // add options
                circlevent.append('<option value="" >Select Event..</option>');
                $.each(data, function (id, name) {
                    circlevent.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
    jQuery("#trn_product_media_edit_trnCircle").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/circle/event_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var circlevent = $("#trn_product_media_edit_trnCircleEvents");
                circlevent.html('');
                // add options
                circlevent.append('<option value="" >Select Event..</option>');
                $.each(data, function (id, name) {
                    circlevent.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
    $('label[for=trn_product_media_edit_mediaFileName]').remove();
    $("#trn_product_media_edit_mediaFileName").fileinput({
        theme: "fa",
        overwriteInitial: true,
        maxFileSize: 5000,
        showClose: false,
        showCaption: false,
        showBrowse: false,
        browseOnZoneClick: true,
        removeLabel: '',
        defaultPreviewContent: '<h6 class="text-muted">Click to upload image</h6>',
        layoutTemplates: {main2: '{preview} {remove} {browse}'},
        allowedFileExtensions: ["jpg","jpeg", "png"]
    });


    if ($pageURL.search(/add/i) > 0) {
        jQuery("#addContent").trigger("click");
        $('.removebutton').hide();
    }



});