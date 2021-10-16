$(document).ready(function() {

    $('#app_user_registration_mstStatus').change(function (){

        if($(this).children("option:selected").text() == 'Rejected' || $(this).children("option:selected").text() == 'Deactivated') {
            $('label[for=app_user_registration_reasonToReject]').addClass('required');
            $("#app_user_registration_reasonToReject").removeAttr('disabled');
            $("#app_user_registration_reasonToReject").prop('required', true);
        } else {
            $('label[for=app_user_registration_reasonToReject]').removeClass('required');
            $("#app_user_registration_reasonToReject").val('');
            $("#app_user_registration_reasonToReject").attr('disabled', 'disabled');
            $("#app_user_registration_reasonToReject").prop('required', false);
        }
    });
    $('#app_user_registration_mstStatus').change(); // change reason to reject box on load


    $('[name="app_user_registration"]').on('submit', function() {

        if(($('#app_user_registration_mstStatus :selected').text() == 'Rejected'  || $('#app_user_registration_mstStatus :selected').text() == 'Deactivated') &&
            $.trim($("#app_user_registration_reasonToReject").val()) != '') {

            var wordLen = 10, len; // Maximum word length
            len = $("#app_user_registration_reasonToReject").val().split(/[\s]+/);
            if (len.length > wordLen) {
                if (event.keyCode == 46 || event.keyCode == 8 || event.which == 46 || event.which == 8) {
                    // Allow backspace and delete buttons
                } else {
                    //if (event.keyCode < 48 || event.keyCode > 57) {//all other buttons
                    event.preventDefault();
                }
            }
            wordsLeft = (wordLen) - len.length;
            if (wordsLeft < 0) {
                alert('Maximum ' + wordLen + ' words accepted.\n'+len.length+' words entered');
                $("#app_user_registration_reasonToReject").focus();
                return false;
            }
        }
        return true;
    });


    $('label[for=app_user_registration_trnOrganizationDetails_0_organizationLogo]').remove();
    var btnCust = '';
    $("#app_user_registration_trnOrganizationDetails_0_organizationLogo").fileinput({
        theme: "fa",
        overwriteInitial: true,
        maxFileSize: 300,
        showClose: false,
        showCaption: false,
        showBrowse: false,
        browseOnZoneClick: true,
        removeLabel: '',
        // removeIcon: '<i class="fa fa-remove"></i>',
        defaultPreviewContent: '<h6 class="text-muted">Click to upload organization logo</h6>',
        layoutTemplates: {main2: '{preview} ' +  btnCust + ' {remove} {browse}'},
        allowedFileExtensions: ["jpg", "png", "gif"]
    });

    // Summernote
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

    jQuery("#app_user_registration_appUserInfo_mstCountry").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/location/state_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#app_user_registration_appUserInfo_mstState");
                product.html('');
                // add options
                product.append('<option value="" >Select State..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
    jQuery("#app_user_registration_appUserInfo_mstState").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/location/city_list",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                var product = $("#app_user_registration_appUserInfo_mstCity");
                product.html('');
                // add options
                product.append('<option value="" >Select City..</option>');
                $.each(data, function (id, name) {
                    product.append('<option value="'+ name.id +'">'+ name.name + '</option>');
                });
            }
        });
    });
    if (jQuery("#app_user_registration_appUserInfo_mstUserMemberType").find("option:selected").text().toLowerCase() == 'organization') {
        jQuery('.organizationDetails').show();
        jQuery('.individualDetailsOnly').hide();
    } else {
        jQuery('.organizationDetails').hide();
        jQuery('.individualDetailsOnly').show();
    }

    jQuery("#app_user_registration_appUserInfo_pancardNumber").change(function () {
        var data = {};
        data['q'] = jQuery(this).val();
        jQuery.ajax({
            url: "/core/pancard/check_unqiue",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                if(data.unique == true) {} else {
                    alert("Entered Pan Card already exists in the system");
                    $("#app_user_registration_appUserInfo_pancardNumber").val('');
                }
            }
        });
    });

    jQuery("#app_user_registration_appUserInfo_userEmail").change(function () {
        var data = {};
        emailID = data['q'] = $.trim(jQuery(this).val());
        jQuery.ajax({
            url: "/core/user_email/check_unqiue",
            data: data,
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                if(data.unique == true) {
                    $("#app_user_registration_userName").val(emailID);
                } else {
                    alert("User already exist with specified email address.");
                    $("#app_user_registration_appUserInfo_userEmail").val('');
                }
            }
        });
    });

});

jQuery(document).on('click', '.btn-danger', function (e) {
    jQuery(this).parent().parent().remove()
});

jQuery("#app_user_registration_appUserInfo_mstUserMemberType").change(function () {
   jQuery( ".removeBankDetails" ).each(function( index ) {
        jQuery(this).click();
   });
   jQuery("#addBankDetails").click();
   jQuery('.bankDetails').show();
   switch(jQuery(this).find("option:selected").text().toLowerCase()) {
       case 'organization':{
           jQuery('.organizationDetails').show();
           jQuery("#addOrganizationDetails").click();
           jQuery("[for='app_user_registration_appUserInfo_pancardNumber']").text("Organization Pan No.");
           jQuery("[for='app_user_registration_appUserInfo_address1']").text("Registered Office Address 1.");
           jQuery("[for='app_user_registration_appUserInfo_address2']").text("Registered Office Address 2.");
           break;
       }
       case 'individual':{
           jQuery("[for='app_user_registration_appUserInfo_pancardNumber']").text("Individual Pan No.");
           jQuery("[for='app_user_registration_appUserInfo_address1']").text("Address 1.");
           jQuery("[for='app_user_registration_appUserInfo_address2']").text("Address 2.");
           jQuery( ".removeOrganizationDetails" ).each(function( index ) {
               jQuery(this).click();
           });
           break;
       }
   }
});

$collectionHolderOrganizationDetails = jQuery('#organizationDetailsContent').parent();
$collectionHolderOrganizationDetails.data('index', jQuery('#organizationDetailsContent').find('.trnOrganizationDetails').length);

jQuery("#addOrganizationDetails").on('click', function (e) {
    addRowContentFormOrgDetails($collectionHolderOrganizationDetails);
});

function addRowContentFormOrgDetails($collectionHolderOrganizationDetails) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolderOrganizationDetails.data('prototype');

    // get the new index
    var index = $collectionHolderOrganizationDetails.data('index');
    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    // increase the index with one for the next item
    $collectionHolderOrganizationDetails.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    jQuery("#organizationDetailsContent").append(newForm);

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

    $("#app_user_registration_trnOrganizationDetails_"+index+"_organizationLogo").fileinput({
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
    $('label[for=app_user_registration_trnOrganizationDetails_'+index+'_organizationLogo]').remove();

    // Select2 for selectbox
    /*$('select').select2({
        theme: 'bootstrap4'
    })*/

}

var $collectionHolderBankDetails = null;
$collectionHolderBankDetails = jQuery('#bankDetailsContent').parent();
console.log($collectionHolderBankDetails);
$collectionHolderBankDetails.data('index', jQuery('#bankDetailsContent').find('.trnBankDetails').length);
jQuery(document).on('click',"#addBankDetails", function (e) {
    addRowContentFormBankDetails($collectionHolderBankDetails);
});
function addRowContentFormBankDetails($collectionHolderBankDetails) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolderBankDetails.data('prototype');

    // get the new index
    var index = $collectionHolderBankDetails.data('index');
    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    // increase the index with one for the next item
    $collectionHolderBankDetails.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    jQuery("#bankDetailsContent").append(newForm);
    // Select2 for selectbox
    /*$('select').select2({
        theme: 'bootstrap4'
    })*/
}

function validate() {

    var bAtleastOnePrimaryAISelected = false;
    var bAtleastOneSecondaryAISelected = false;
    var errorMsg = '';
    $("input[name^='primaryArea[]'").each(function (index) {
        // console.log($(this).attr('id') + " - "+$(this).is(':checked'));
        if($(this).is(':checked') == true) {
            bAtleastOnePrimaryAISelected = true;
            var primaryAIid = $(this).val();
            var primaryAIval = $(this).attr('paiName');

            if($("#secondaryArea_"+primaryAIid+" option:selected").length > 0) {
                bAtleastOneSecondaryAISelected = true;
            } else {
                errorMsg+="\n -> "+primaryAIval;
            }
        }
    });

    if(bAtleastOnePrimaryAISelected == false) {
        alert("Please select atleast one Primary Area of Interest");
    } else {
        if(errorMsg != '') {
            alert("Select Secondary Area of Interest for: \n" + errorMsg);
            return false;
        }
    }
    return true;
}