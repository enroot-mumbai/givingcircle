var $collectionHolder;
var $pageURL = $(location).attr("href");

$(document).ready(function() {

    // Select2 for selectbox
    $('select').select2({
        theme: 'bootstrap4'
    })

    // Summernote
    $('.textarea').summernote({
        height: 200,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview', 'help']],
        ]
    });

    $("#cms_article_articleIntroImage,.custom-file-input").fileinput({
        theme: "fa",
        overwriteInitial: true,
        maxFileSize: 800,
        showClose: false,
        showCaption: false,
        showBrowse: false,
        browseOnZoneClick: true,
        removeLabel: '',
        defaultPreviewContent: '<h6 class="text-muted">Click to upload content block image</h6>',
        layoutTemplates: {main2: '{preview} {remove} {browse}'},
        allowedFileExtensions: ["jpg","jpeg", "png"]
    });


    $('label[for^=cms_article_cmsArticleContent]').remove();


    $collectionHolder = jQuery('#articleContent').parent();
    $collectionHolder.data('index', jQuery('#articleContent').find('.cmsArticleContent').length);

    jQuery(document).on('click', '.btn-danger', function (e) {
        jQuery(this).closest(".cmsArticleContent").remove();
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
        jQuery("#articleContent").append(newForm)

        // First Content block mandatory
        $("#cms_article_cmsArticleContent_0_articleContent").prop('required', true);
        $('label[for=cms_article_cmsArticleContent_0_articleContent]').addClass('required').show();

        $("#cms_article_cmsArticleContent_"+index+"_articleContentImage").fileinput({
            theme: "fa",
            overwriteInitial: true,
            maxFileSize: 800,
            showClose: false,
            showCaption: false,
            showBrowse: false,
            browseOnZoneClick: true,
            removeLabel: '',
            defaultPreviewContent: '<h6 class="text-muted">Click to upload content block image</h6>',
            layoutTemplates: {main2: '{preview} {remove} {browse}'},
            allowedFileExtensions: ["jpg","jpeg", "png"]
        });

        // Content Image Upload
        $('label[for=cms_article_cmsArticleContent_'+index+'_articleContentImage]').remove();

        $("#cms_article_cmsArticleContent_"+index+"_articleContentImage").on('fileselect', function(event, numFiles, label) {
            $("#cms_article_cmsArticleContent_"+index+"_articleContentImageSetName").prop('required', true);
            $('label[for=cms_article_cmsArticleContent_'+index+'_articleContentImageSetName]').addClass('required');
            $("#cms_article_cmsArticleContent_"+index+"_articleContentImageAlt").prop('required', true);
            $('label[for=cms_article_cmsArticleContent_'+index+'_articleContentImageAlt]').addClass('required');
            $("#cms_article_cmsArticleContent_"+index+"_articleContentImageTitle").prop('required', true);
            $('label[for=cms_article_cmsArticleContent_'+index+'_articleContentImageTitle]').addClass('required');
        });

        $("#cms_article_cmsArticleContent_"+index+"_articleContentImage").on('fileclear', function(event) {
            $("#cms_article_cmsArticleContent_"+index+"_articleContentImageSetName").prop('required', false);
            $('label[for=cms_article_cmsArticleContent_'+index+'_articleContentImageSetName]').removeClass('required');
            $("#cms_article_cmsArticleContent_"+index+"_articleContentImageAlt").prop('required', false);
            $('label[for=cms_article_cmsArticleContent_'+index+'_articleContentImageAlt]').removeClass('required');
            $("#cms_article_cmsArticleContent_"+index+"_articleContentImageTitle").prop('required', false);
            $('label[for=cms_article_cmsArticleContent_'+index+'_articleContentImageTitle]').removeClass('required');
        });

        // Copy the intro text to alt and title
        $("#cms_article_cmsArticleContent_"+index+"_articleContentImageSetName").keyup(function(){
            var Text = $(this).val();
            $("#cms_article_cmsArticleContent_"+index+"_articleContentImageAlt").val(Text);
            $("#cms_article_cmsArticleContent_"+index+"_articleContentImageTitle").val(Text);
        });

    }
    // Intro Media Section
    $('label[for=cms_article_articleIntroImage]').remove();

    // The main content bloc is required
    $('label[for=cms_article_cmsArticleContent_0_articleContent]').addClass('required');
    $("#cms_article_cmsArticleContent_0_articleContent").prop('required', true);

    // Show hide functionality based on article type
    $('#cms_article_mstArticleCategory').on('change', function() {
        var category = $(this).val();
        if (category === '2') {
            $("#cms_article_mstAreaInterest").prop('required', true);
            $('label[for=cms_article_mstAreaInterest]').addClass('required').show();
            $("#mstAreaInterest").show();
            $("#cms_article_articleFor").prop('required', true);
            $('label[for=cms_article_articleFor]').addClass('required').show();
            $("#articleFor").show();
            $("#cms_article_mstCountry").prop('required', true);
            $('label[for=cms_article_mstCountry]').addClass('required').show();
            $("#mstCountry").show();
            $("#cms_article_mstState").prop('required', true);
            $('label[for=cms_article_mstState]').addClass('required').show();
            $("#mstState").show();
            $("#cms_article_mstCity").prop('required', true);
            $('label[for=cms_article_mstCity]').addClass('required').show();
            $("#mstCity").show();
            $("#cms_article_locationName").prop('required', true);
            $('label[for=cms_article_locationName]').addClass('required').show();
            $("#locationName").show();
            $("#cms_article_articleTitle").prop('required', false).val('');
            $('label[for=cms_article_articleTitle]').removeClass('required').hide();
            $("#articleTitle").hide();

        } else if (category === '1') {
            $('label[for=cms_article_mstAreaInterest]').removeClass('required').hide();
            $("#cms_article_mstAreaInterest").prop('required', false).val('');
            $("#mstAreaInterest").hide();
            $("#cms_article_articleFor").prop('required', false).val('');
            $('label[for=cms_article_articleFor]').removeClass('required').hide();
            $("#articleFor").hide();
            $("#cms_article_mstCountry").prop('required', false).val('');
            $('label[for=cms_article_mstCountry]').removeClass('required').hide();
            $("#mstCountry").hide();
            $("#cms_article_mstState").prop('required', false).val('');
            $('label[for=cms_article_mstState]').removeClass('required').hide();
            $("#mstState").hide();
            $("#cms_article_mstCity").prop('required', false).val('');
            $('label[for=cms_article_mstCity]').removeClass('required').hide();
            $("#mstCity").hide();
            $("#cms_article_locationName").prop('required', false).val('');
            $('label[for=cms_article_locationName]').removeClass('required').hide();
            $("#locationName").hide();
            $("#cms_article_articleTitle").prop('required', true);
            $('label[for=cms_article_articleTitle]').addClass('required').show();
            $("#articleTitle").show();
        }
    });

    $('#cms_article_introMediaType').on('change', function() {
        var mediatype = $(this).val();
        if (mediatype === 'image') {
            $("#cms_article_articleIntroImage").prop('required', true);
            $('label[for=cms_article_articleIntroImage]').addClass('required');
            $("#cms_article_articleIntroImageSetName").prop('required', true);
            $('label[for=cms_article_articleIntroImageSetName]').addClass('required');
            $("#cms_article_articleIntroImageAlt").prop('required', true);
            $('label[for=cms_article_articleIntroImageAlt]').addClass('required');
            $("#cms_article_articleIntroImageTitle").prop('required', true);
            $('label[for=cms_article_articleIntroImageTitle]').addClass('required');
            $(".introimage").show();
            $("#cms_article_articleIntroVideo").prop('required', false);
            $('label[for=cms_article_articleIntroVideo]').removeClass('required');
            $("#cms_article_articleIntroVideoPath").prop('required', false);
            $('label[for=cms_article_articleIntroVideoPath]').removeClass('required');
            $(".introvideo").hide();

        }
        if (mediatype === 'video') {
            $("#cms_article_articleIntroImage").prop('required', false);
            $('label[for=cms_articleIntroImage]').removeClass('required');
            $("#cms_article_articleIntroImageSetName").prop('required', false);
            $('label[for=cms_article_articleIntroImageSetName]').removeClass('required');
            $("#cms_article_articleIntroImageAlt").prop('required', false);
            $('label[for=cms_article_articleIntroImageAlt]').removeClass('required');
            $("#cms_article_articleIntroImageTitle").prop('required', false);
            $('label[for=cms_article_articleIntroImageTitle]').removeClass('required');
            $(".introimage").hide();
            $("#cms_article_articleIntroVideo").prop('required', true);
            $('label[for=cms_article_articleIntroVideo]').addClass('required');
            $("#cms_article_articleIntroVideoPath").prop('required', true);
            $('label[for=cms_article_articleIntroVideoPath]').addClass('required');
            $(".introvideo").show();

        }
    });


    jQuery("#cms_article_mstCountry").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/location/state_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#cms_article_mstState");
                product.html('');
                // add options
                product.append('<option value="" >Select State..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });

    jQuery("#cms_article_mstState").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/location/city_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#cms_article_mstCity");
                product.html('');
                // add options
                product.append('<option value="" >Select City..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });


    // Copy the intro text to alt and title
    $("#cms_article_articleIntroImageSetName").keyup(function(){
        var Text = $(this).val();
        $("#cms_article_articleIntroImageAlt").val(Text);
        $("#cms_article_articleIntroImageTitle").val(Text);
    });


    if ($pageURL.search(/add/i) > 0) {
        jQuery("#addContent").trigger("click");
        $('.removebutton').hide();
        $("#removeIntroImage").hide();
    }

});
