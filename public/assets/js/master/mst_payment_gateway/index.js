var $collectionHolder;
var $pageURL = $(location).attr("href");

$collectionHolder = jQuery('#pgOption').parent();
$collectionHolder.data('index', jQuery('#pgOption').find('.attribute').length);

jQuery(document).on('click', '.btn-danger', function (e) {
    jQuery(this).closest(".attribute").remove();
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
    jQuery("#pgOption").append(newForm)

    $('select').select2({
        theme: 'bootstrap4',
    });

}
$(document).ready(function() {

    if ($pageURL.search(/add/i) > 0) {
        if ($('#addContent').find('.attribute').length == 0) {
            jQuery("#addContent").trigger("click");
            $('.removebutton').hide();
        }
    }
});
