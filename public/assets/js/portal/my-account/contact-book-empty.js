$(document).ready(function () {
    $('.import-contacts').hide();
    $('#import-contacts').click(function(){
        $('.import-contacts').show();
    })
    $('.close').click(function(){
        $('.import-contacts').hide();
    })

    $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonImage: "/resources/images/common/icons/icon_calender.png",
        buttonImageOnly: true,
        buttonText: "Select date"
    });

    $(".custom-file-input").on("change", function () {
        var fileName = $(this).val().split("\\").pop();
        var fileExtension = ['csv'];
        if ($.inArray(fileName.split('.').pop().toLowerCase(), fileExtension) == -1) {
            alert("Only formats are allowed : "+fileExtension.join(', '));
            return false;
        }
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        if ($('.custom-file-label').hasClass('selected')){
            //alert('ok');
            $('.btn-primary').removeClass('disabled');
        }
    });

    $("#btnImportContact").click(function (){
        if ($.trim($('#customFile').val()) == '') {
            alert('Please upload a csv file');
            return false;
        }
        $("#frmImportContact").submit();
    });

});