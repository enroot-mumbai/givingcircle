$("document").ready(function() {

    $('label[for=cms_article_blog_ogImage]').remove();
    $("#cms_article_blog_ogImage").fileinput({
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
        allowedFileExtensions: ["jpg", "png"]
    });

    // Select2 for selectbox
    $('select').select2({
        theme: 'bootstrap4'
    })

    $(".textarea").summernote({
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
    $('label[for^=cms_article_blog_articleIntroDesktopImage]').remove();
    $("#cms_article_blog_articleIntroDesktopImage").fileinput({
        theme: "fa",
        overwriteInitial: true,
        maxFileSize: 800,
        showClose: false,
        showCaption: false,
        showBrowse: false,
        browseOnZoneClick: true,
        removeLabel: '',
        defaultPreviewContent: '<h6 class="text-muted">Click to upload intro image</h6>',
        layoutTemplates: {main2: '{preview} {remove} {browse}'},
        allowedFileExtensions: ["jpg","jpeg", "png"]
    });
    // $('label[for^=cms_article_blog_articleIntroTabletImage]').remove();
    // $("#cms_article_blog_articleIntroTabletImage").fileinput({
    //     theme: "fa",
    //     overwriteInitial: true,
    //     maxFileSize: 800,
    //     showClose: false,
    //     showCaption: false,
    //     showBrowse: false,
    //     browseOnZoneClick: true,
    //     removeLabel: '',
    //     defaultPreviewContent: '<h6 class="text-muted">Click to upload intro tablet image</h6>',
    //     layoutTemplates: {main2: '{preview} {remove} {browse}'},
    //     allowedFileExtensions: ["jpg","jpeg", "png"]
    // });
    // $('label[for^=cms_article_blog_articleIntroMobileImage]').remove();
    // $("#cms_article_blog_articleIntroMobileImage").fileinput({
    //     theme: "fa",
    //     overwriteInitial: true,
    //     maxFileSize: 800,
    //     showClose: false,
    //     showCaption: false,
    //     showBrowse: false,
    //     browseOnZoneClick: true,
    //     removeLabel: '',
    //     defaultPreviewContent: '<h6 class="text-muted">Click to upload intro mobile image</h6>',
    //     layoutTemplates: {main2: '{preview} {remove} {browse}'},
    //     allowedFileExtensions: ["jpg","jpeg", "png"]
    // });

    var $collectionHolder;
    var $pageURL = $(location).attr("href");
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
        jQuery("#articleContent").append(newForm);


        $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentDesktopImage").fileinput({
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
        // $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentTabletImage").fileinput({
        //     theme: "fa",
        //     overwriteInitial: true,
        //     maxFileSize: 800,
        //     showClose: false,
        //     showCaption: false,
        //     showBrowse: false,
        //     browseOnZoneClick: true,
        //     removeLabel: '',
        //     defaultPreviewContent: '<h6 class="text-muted">Click to upload content block tablet image</h6>',
        //     layoutTemplates: {main2: '{preview} {remove} {browse}'},
        //     allowedFileExtensions: ["jpg","jpeg", "png"]
        // });
        // $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentMobileImage").fileinput({
        //     theme: "fa",
        //     overwriteInitial: true,
        //     maxFileSize: 800,
        //     showClose: false,
        //     showCaption: false,
        //     showBrowse: false,
        //     browseOnZoneClick: true,
        //     removeLabel: '',
        //     defaultPreviewContent: '<h6 class="text-muted">Click to upload content block mobile image</h6>',
        //     layoutTemplates: {main2: '{preview} {remove} {browse}'},
        //     allowedFileExtensions: ["jpg","jpeg", "png"]
        // });
        // Content Image Upload
        $('label[for=cms_article_blog_cmsArticleContent_'+index+'_articleContentDesktopImage]').remove();
        // $('label[for=cms_article_blog_cmsArticleContent_'+index+'_articleContentTabletImage]').remove();
        // $('label[for=cms_article_blog_cmsArticleContent_'+index+'_articleContentMobileImage]').remove();



        // First Content block mandatory
        $("#cms_article_blog_cmsArticleContent_0_articleContent").prop('required', true);
        $('label[for=cms_article_blog_cmsArticleContent_0_articleContent]').addClass('required').show();


        $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentDesktopImage").on('fileselect', function(event, numFiles, label) {
            $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentImageSetName").prop('required', true);
            $('label[for=cms_article_blog_cmsArticleContent_'+index+'_articleContentImageSetName]').addClass('required');
            $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentImageAlt").prop('required', true);
            $('label[for=cms_article_blog_cmsArticleContent_'+index+'_articleContentImageAlt]').addClass('required');
            $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentImageTitle").prop('required', true);
            $('label[for=cms_article_blog_cmsArticleContent_'+index+'_articleContentImageTitle]').addClass('required');
        });

        $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentDesktopImage").on('fileclear', function(event) {
            $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentImageSetName").prop('required', false);
            $('label[for=cms_article_blog_cmsArticleContent_'+index+'_articleContentImageSetName]').removeClass('required');
            $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentImageAlt").prop('required', false);
            $('label[for=cms_article_blog_cmsArticleContent_'+index+'_articleContentImageAlt]').removeClass('required');
            $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentImageTitle").prop('required', false);
            $('label[for=cms_article_blog_cmsArticleContent_'+index+'_articleContentImageTitle]').removeClass('required');
        });

        // Copy the intro text to alt and title
        $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentImageSetName").keyup(function(){
            var Text = $(this).val();
            $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentImageAlt").val(Text);
            $("#cms_article_blog_cmsArticleContent_"+index+"_articleContentImageTitle").val(Text);
        });
        $("#cms_article_blog_cmsArticleContent_"+index+"_articleContent").summernote({
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

        $("#cms_article_blog_cmsArticleContent_"+index+"_mediaType").select2({theme: 'bootstrap4'});
    }


    // Intro Media Section
    $('label[for=cms_article_blog_articleIntroDesktopImage]').remove();

    // The main content bloc is required
    $('label[for=cms_article_blog_cmsArticleContent_0_articleContent]').addClass('required');
    $("#cms_article_blog_cmsArticleContent_0_articleContent").prop('required', true);

    $('#cms_article_blog_introMediaType').on('change', function() {
        var mediatype = $(this).val();
        if (mediatype === 'image') {
            $("#cms_article_blog_articleIntroDesktopImage").prop('required', true);
            $('label[for=cms_article_blog_articleIntroDesktopImage]').addClass('required');
            $("#cms_article_blog_articleIntroImageSetName").prop('required', true);
            $('label[for=cms_article_blog_articleIntroImageSetName]').addClass('required');
            $("#cms_article_blog_articleIntroImageAlt").prop('required', true);
            $('label[for=cms_article_blog_articleIntroImageAlt]').addClass('required');
            $("#cms_article_blog_articleIntroImageTitle").prop('required', true);
            $('label[for=cms_article_blog_articleIntroImageTitle]').addClass('required');
            // $(".introimage").show();
            $("#cms_article_blog_articleIntroVideo").prop('required', false);
            $('label[for=cms_article_blog_articleIntroVideo]').removeClass('required');
            $("#cms_article_blog_articleIntroVideoPath").prop('required', false);
            $('label[for=cms_article_blog_articleIntroVideoPath]').removeClass('required');
            // $(".introvideo").hide();

        }
        if (mediatype === 'video') {
            $("#cms_article_blog_articleIntroDesktopImage").prop('required', false);
            $('label[for=cms_articleIntroDesktopImage]').removeClass('required');
            $("#cms_article_blog_articleIntroImageSetName").prop('required', false);
            $('label[for=cms_article_blog_articleIntroImageSetName]').removeClass('required');
            $("#cms_article_blog_articleIntroImageAlt").prop('required', false);
            $('label[for=cms_article_blog_articleIntroImageAlt]').removeClass('required');
            $("#cms_article_blog_articleIntroImageTitle").prop('required', false);
            $('label[for=cms_article_blog_articleIntroImageTitle]').removeClass('required');
            // $(".introimage").hide();
            $("#cms_article_blog_articleIntroVideo").prop('required', true);
            $('label[for=cms_article_blog_articleIntroVideo]').addClass('required');
            $("#cms_article_blog_articleIntroVideoPath").prop('required', true);
            $('label[for=cms_article_blog_articleIntroVideoPath]').addClass('required');
            // $(".introvideo").show();

        }
    });

    // Copy the intro text to alt and title
    $("#cms_article_blog_articleIntroImageSetName").keyup(function(){
        var Text = $(this).val();
        $("#cms_article_blog_articleIntroImageAlt").val(Text);
        $("#cms_article_blog_articleIntroImageTitle").val(Text);
    });

    if ($pageURL.search(/add/i) > 0) {
        jQuery("#addContent").trigger("click");
        $('.removebutton').hide();
        $("#removeIntroImage").hide();
    }
    for(i=0;i<jQuery('#articleContent').find('.cmsArticleContent').length;i++){
        $('label[for=cms_article_blog_cmsArticleContent_'+i+'_articleContentDesktopImage]').remove();
        // $('label[for=cms_article_blog_cmsArticleContent_'+i+'_articleContentTabletImage]').remove();
        // $('label[for=cms_article_blog_cmsArticleContent_'+i+'_articleContentMobileImage]').remove();
        $("#cms_article_blog_cmsArticleContent_"+i+"_articleContentDesktopImage").fileinput({
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
        // $("#cms_article_blog_cmsArticleContent_"+i+"_articleContentTabletImage").fileinput({
        //     theme: "fa",
        //     overwriteInitial: true,
        //     maxFileSize: 800,
        //     showClose: false,
        //     showCaption: false,
        //     showBrowse: false,
        //     browseOnZoneClick: true,
        //     removeLabel: '',
        //     defaultPreviewContent: '<h6 class="text-muted">Click to upload content block tablet image</h6>',
        //     layoutTemplates: {main2: '{preview} {remove} {browse}'},
        //     allowedFileExtensions: ["jpg","jpeg", "png"]
        // });
        // $("#cms_article_blog_cmsArticleContent_"+i+"_articleContentMobileImage").fileinput({
        //     theme: "fa",
        //     overwriteInitial: true,
        //     maxFileSize: 800,
        //     showClose: false,
        //     showCaption: false,
        //     showBrowse: false,
        //     browseOnZoneClick: true,
        //     removeLabel: '',
        //     defaultPreviewContent: '<h6 class="text-muted">Click to upload content block mobile image</h6>',
        //     layoutTemplates: {main2: '{preview} {remove} {browse}'},
        //     allowedFileExtensions: ["jpg","jpeg", "png"]
        // });
    }
});