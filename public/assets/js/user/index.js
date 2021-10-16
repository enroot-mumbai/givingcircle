$(document).ready(function() {
    $('label[for=user_profile_userAvatarImage]').remove();
});

$("#user_profile_userAvatarImage").fileinput({
    theme: "fa",
    overwriteInitial: true,
    maxFileSize: 800,
    showClose: false,
    showCaption: false,
    showBrowse: false,
    browseOnZoneClick: true,
    removeLabel: '',
    defaultPreviewContent: '<h6 class="text-muted">Click to upload your Avatar Image</h6>',
    layoutTemplates: {main2: '{preview} {remove} {browse}'},
    allowedFileExtensions: ["jpg", "png", "gif"]
});