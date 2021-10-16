$("document").ready(function() {
    $(".status").click(function() {
        var button = $(this);
        // console.log($(this).prev('input').val());
        // console.log($(this).closest('td').find($(".commentstatus")));
        // console.log($(this).next('span').attr('class'));
        var data = {};
        var commentstatus = $(this).closest('td').find($(".commentstatus"));
        data['comment_id'] = button.prev('input').val();
        data['status'] = button.next('span').attr('class');
        // $('.status').attr("disabled", "disabled");
        $.ajax({
            url: '/core/cms/article_comment/status',
            type: 'POST',
            data: data,
            dataType: "html",
            success: (function (formdata) {
                commentstatus.html('');
                commentstatus.html(formdata);
                commentstatus.show();
            })
        });

    });

});