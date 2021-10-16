$(document).ready(function() {
    $('label[for=org_company_companyLogo]').remove();

});

var btnCust = '';
$("#org_company_companyLogo").fileinput({
    theme: "fa",
    overwriteInitial: true,
    maxFileSize: 300,
    showClose: false,
    showCaption: false,
    showBrowse: false,
    browseOnZoneClick: true,
    removeLabel: '',
    // removeIcon: '<i class="fa fa-remove"></i>',
    defaultPreviewContent: '<h6 class="text-muted">Click to upload company logo</h6>',
    layoutTemplates: {main2: '{preview} ' +  btnCust + ' {remove} {browse}'},
    allowedFileExtensions: ["jpg", "png", "gif"]
});