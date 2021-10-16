$(document).ready(function() {

    $('#app_user_registration_edit_mstStatus').change(function (){

        if($(this).children("option:selected").text() == 'Rejected' || $(this).children("option:selected").text() == 'Deactivated') {
            $('label[for=app_user_registration_reasonToReject]').addClass('required');
            $("#app_user_registration_edit_reasonToReject").removeAttr('disabled');
            $("#app_user_registration_edit_reasonToReject").prop('required', true);
        } else {
            $('label[for=app_user_registration_edit_reasonToReject]').removeClass('required');
            $("#app_user_registration_edit_reasonToReject").val('');
            $("#app_user_registration_edit_reasonToReject").attr('disabled', 'disabled');
            $("#app_user_registration_edit_reasonToReject").prop('required', false);
        }
    });
    $('#app_user_registration_edit_mstStatus').change(); // change reason to reject box on load


    $('[name="app_user_registration"]').on('submit', function() {

        if(($('#app_user_registration_edit_mstStatus :selected').text() == 'Rejected'  || $('#app_user_registration_edit_mstStatus :selected').text() == 'Deactivated') &&
            $.trim($("#app_user_registration_edit_reasonToReject").val()) != '') {

            var wordLen = 10, len; // Maximum word length
            len = $("#app_user_registration_edit_reasonToReject").val().split(/[\s]+/);
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
                $("#app_user_registration_edit_reasonToReject").focus();
                return false;
            }
        }
        return true;
    });

    /*if (jQuery("#app_user_registration_appUserInfo_mstUserMemberType").find("option:selected").text().toLowerCase() == 'organization') {
        jQuery('.organizationDetails').show();
        jQuery('.individualDetailsOnly').hide();
    } else {
        jQuery('.organizationDetails').hide();
        jQuery('.individualDetailsOnly').show();
    }*/
});
/*

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
}*/
