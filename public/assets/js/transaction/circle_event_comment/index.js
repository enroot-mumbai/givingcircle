$("document").ready(function() {
    $(".status").click(function() {

        statusClick($(this));

    });
});

function statusClick(elem) {
    var button = $(elem);

    var data = {};
    var commentstatus = $(elem).closest('td').find($(".commentstatus"));
    data['comment_id'] = button.prev('input').val();
    data['status'] = button.next('span').attr('class');

    $.ajax({
        url: '/core/product/circle_comment/status',
        type: 'POST',
        data: data,
        dataType: "html",
        success: (function (formdata) {
            commentstatus.html('');
            commentstatus.html(formdata);
            commentstatus.show();
        })
    });
}