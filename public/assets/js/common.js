$(document).ready(function () {
    $('select').select2({
        theme: 'bootstrap4'
    })

    // Data Table
    if ($('#dataTable').length) {
        var data = $('#dataTable');
        data.DataTable({
            "paging": true,
            "lengthChange": true,
            "ordering": true,
            "order": [[ 1, "asc" ]],
            "info": true,
            "autoWidth": true,
            "oLanguage": {
                "sSearch": "Search:",
                "sInfo": "Total records(s): _TOTAL_"

            },
            "language": {
                "paginate": {
                    "previous": "&laquo;&nbsp;Previous",
                    "next": "Next&nbsp;&raquo;"
                }
            },
            initComplete: (settings, json) => {
                $('.dataTables_paginate').appendTo($('.dompage'));
                $('.dataTables_info').appendTo($('.domrecord'));
            },
        });
    }

    $('.notification').click(function () {
        $('.notification_popup').show();
    });

    $('#close-notification').click(function () {
        //alert('ok');
        $('.notification_popup').hide();
    });
});