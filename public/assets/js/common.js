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

    if ($('#reportTable').length) {
        var report = $("#reportTable").DataTable({
            "paging": false,
            "lengthChange": true,
            "order": [],
            "ordering": false,
            "info": true,
            "searching": false,
            "autoWidth": true,
            "dom": 'Bfrtip',
            /*"buttons": [
                'excel', 'pdf'
            ]*/
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: $("#filename").val()+ '-Report-GivingCircle',
                },
                {
                    extend: 'pdfHtml5',
                    title: $("#filename").val()+ '-Report-GivingCircle',
                    pageSize : $("#pdfFileSize").val(),
                }
            ]
        });
        /*report.buttons().container()
            .appendTo( $('.col-sm-6:eq(0)', report.table().container() ) );*/
    }

    $('.notification').click(function () {
        $('.notification_popup').show();
    });

    $('#close-notification').click(function () {
        //alert('ok');
        $('.notification_popup').hide();
    });
});