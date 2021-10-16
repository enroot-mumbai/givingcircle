var $collectionHolder;
var $pageURL = $(location).attr("href");
jQuery(function () {
    $collectionHolder = jQuery('#formRowOption').parent();
    $collectionHolder.data('index', jQuery('#formRowOption').find('.collectionformfields').length);

});
jQuery(document).on('click', '.removebutton', function (e) {
    jQuery(this).closest(".collectionformfields").remove();
});
jQuery("#addContent").on('click', function (e) {
    addRowContentForm($collectionHolder);
    rowDataOption(($collectionHolder.data('index') - 1));

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
    jQuery("#formRowOption").append(newForm)
    // Select2 for selectbox
    $('select').select2({
        theme: 'bootstrap4'
    })

}

for(rowindex=0;rowindex<jQuery('#formRowOption').find('.collectionformfields').length;rowindex++){
    rowDataOption(rowindex);
}

function rowDataOption(counter)
{
    // Copy the intro text to alt and title
    $("#cms_page_cmsPageContent_"+counter+"_mediaName").keyup(function(){
        var Text = $(this).val();
        $("#cms_page_cmsPageContent_"+counter+"_mediaAlText").val(Text);
        $("#cms_page_cmsPageContent_"+counter+"_mediaTitle").val(Text);
    });

    $('label[for=cms_page_cmsPageContent_'+counter+'_mediaFileName]').remove();
    $("#cms_page_cmsPageContent_"+counter+"_mediaFileName").fileinput({

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
        allowedFileExtensions: ["jpg", "png", "jpeg"]
    });

    if($("#cms_page_cmsPageContent_"+counter+"_position").val() == '') {
        $("#cms_page_cmsPageContent_"+counter+"_position").val(counter);
    }

    $('#cms_page_cmsPageContent_'+counter+'_mediaType').on('change', function() {
        var mediatype = $(this).val();
        if (mediatype === 'image') {
            $("#cms_page_cmsPageContent_"+counter+"_mediaPosition").prop('required', true);
            $('label[for=cms_page_cmsPageContent_'+counter+'_mediaPosition]').addClass('required');
            $("#cms_page_cmsPageContent_"+counter+"_mediaName").prop('required', true);
            $('label[for=cms_page_cmsPageContent_'+counter+'_mediaName]').addClass('required');
            $("#cms_page_cmsPageContent_"+counter+"_mediaFileName").prop('required', true);
            $("#cms_page_cmsPageContent_"+counter+"_mediaPath").prop('required', false);
            $('label[for=cms_page_cmsPageContent_'+counter+'_mediaPath]').removeClass('required');

        }
        if (mediatype === 'video') {
            $("#cms_page_cmsPageContent_"+counter+"_mediaPosition").prop('required', true);
            $('label[for=cms_page_cmsPageContent_'+counter+'_mediaPosition]').addClass('required');
            $("#cms_page_cmsPageContent_"+counter+"_mediaName").prop('required', false);
            $('label[for=cms_page_cmsPageContent_'+counter+'_mediaName]').removeClass('required');
            $("#cms_page_cmsPageContent_"+counter+"_mediaFileName").prop('required', false);
            $("#cms_page_cmsPageContent_"+counter+"_mediaPath").prop('required', true);
            $('label[for=cms_page_cmsPageContent_'+counter+'_mediaPath]').addClass('required');

        }
        if (mediatype === '') {
            $("#cms_page_cmsPageContent_"+counter+"_mediaPosition").prop('required', false);
            $('label[for=cms_page_cmsPageContent_'+counter+'_mediaPosition]').removeClass('required');
            $("#cms_page_cmsPageContent_"+counter+"_mediaName").prop('required', false);
            $('label[for=cms_page_cmsPageContent_'+counter+'_mediaName]').removeClass('required');
            $("#cms_page_cmsPageContent_"+counter+"_mediaFileName").prop('required', false);
            $("#cms_page_cmsPageContent_"+counter+"_mediaPath").prop('required', false);
            $('label[for=cms_page_cmsPageContent_'+counter+'_mediaPath]').removeClass('required');

        }
    });

    $("#cms_page_cmsPageContent_"+counter+"_mediaPosition").prop('required', false);
    $('label[for=cms_page_cmsPageContent_'+counter+'_mediaPosition]').removeClass('required');
    $("#cms_page_cmsPageContent_"+counter+"_mediaName").prop('required', false);
    $('label[for=cms_page_cmsPageContent_'+counter+'_mediaName]').removeClass('required');
    $("#cms_page_cmsPageContent_"+counter+"_mediaFileName").prop('required', false);
    $("#cms_page_cmsPageContent_"+counter+"_mediaPath").prop('required', false);
    $('label[for=cms_page_cmsPageContent_'+counter+'_mediaPath]').removeClass('required');

    $(function () {
        // Summernote
        $('.textarea').summernote({
            height: 200,
            fontNames: [
                'poppins', 'sans-serif', 'Arial', 'Arial Black', 'Courier',
                'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande',
                'Sacramento'
            ]
        });
    });
}

$("document").ready(function() {

    $('label[for=cms_page_seoContent_ogImage]').remove();
    $("#cms_page_seoContent_ogImage").fileinput({
        theme: "fa",
        overwriteInitial: true,
        maxFileSize: 3000,
        showClose: false,
        showCaption: false,
        showBrowse: false,
        browseOnZoneClick: true,
        removeLabel: '',
        defaultPreviewContent: '<h6 class="text-muted">Click to upload social share image</h6>',
        layoutTemplates: {main2: '{preview} {remove} {browse}'},
        allowedFileExtensions: ["jpg", "png", "jpeg"]
    });

    function slugify(string) {
        return string
            .toString()
            .trim()
            .toLowerCase()
            .replace(/\s+/g, "-")
            .replace(/[^\w\-]+/g, "")
            .replace(/--+/g, "-")
            .replace(/^-+/, "")
            .replace(/-+$/, "");
    }

    if ($pageURL.search(/add/i) > 0) {
        jQuery("#addContent").trigger("click");
        $('.removebutton').hide();
    }
    $("#cms_page_pageName").keyup(function(){
        var Text = $(this).val();
        var slugtext = slugify(Text);
        $("#cms_page_slugName").val(slugtext);
    });

    /*
    $("document").ready(function() {
        jQuery("#cms_page_mstProductCategory").change(function () {
            var data = {};
            data['q'] = jQuery(this).val();
            jQuery.ajax({
                url: "/core/product/product_subcategory_list",
                data: data,
                type: "GET",
                dataType: "JSON",
                success: function (data) {
                    var product = $("#cms_page_mstProductSubCategory");
                    product.html('');
                    // add options
                    product.append('<option value="" >Select Product Sub Category..</option>');
                    $.each(data, function (id, name) {
                        product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                    });
                }
            });
        });
    });
    */

});