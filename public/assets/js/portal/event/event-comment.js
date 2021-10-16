$(document).ready(function () {

    $(".express-donate-parallax").css('background-image', function () {
        var bg = ('url(' + $(this).data("image-src") + ')');
        return bg;
    });

    $("#trn_user_comments_comment").removeClass('form-control');
    $(".replyBox").removeClass('form-control');
    $(".replyBox").addClass('search-input');

    $(".forward-list").hide();
    $(".btn-forward-post").click(function (e) {
        e.preventDefault();
        $(this).siblings('.forward-list').fadeToggle();
    });
    $(".btn-reply").click(function (e) {
        e.preventDefault();
        $(this).toggleClass('disabled');
        $(this).parents('.comment-box').siblings('#reply-popup').toggle();
        $(this).parents('.comment-list').siblings('.reply-box').toggle();
    });
    $(".btn-close").click(function (e) {
        e.preventDefault();
        $(this).parents('#reply-popup').hide();
        $(this).parents('#reply-popup').siblings('.comment-box').find('.btn-reply').removeClass('disabled');
        $(this).parents('.comment-list').siblings('.reply-box').show();
    });
    $(".reply-btn").click(function (e) {
        e.preventDefault();
        $(this).parents('.like-reply-list').siblings('.reply-box').show();
    });
    $(".post-reply").click(function (e) {
        e.preventDefault();
        $(this).parents('.reply-area').hide();
        $(this).parents('.comment-box').find('.author-box').show();
        $(this).parents('.comment-box').find('.like-reply-list').show();
    });

    $('.no-form').removeClass('form-control no-form');

    $('.reply-box').css('display', 'block');
    $('.comment-box').find('.author-box').show();
    $('.comment-box').find('.like-reply-list').show();
    $('.replypostarea').hide();

    $("#commentForm").submit(function(event) {
        /* stop form from submitting normally */
        event.preventDefault();
        postComment('');
    });

    $(".replyFrm").submit(function(event) {
        /* stop form from submitting normally */
        event.preventDefault();

        var formid = $(this)[0].id;
        var commentid = formid.replace("replyForm-", "");
        postComment(commentid);
    });
});

function postComment(commentid) {
    var data = {};

    data['commentId'] = commentid;
    data['eventId'] = $('#commenteventId').val();
    //data['commentId'] = $("#commentId").val();
    //data['comment'] = $('#trn_user_comments_comment').val();

    if (commentid !== '') {
        var message = $('#replymessage'+commentid);
        message.text('Posting your reply to comment......');
        $('.replybutton').attr("disabled", "disabled");
        data['comment'] = $('#reply_box_'+commentid).val();
    } else {
        var message = $("#cmtmessage");
        message.text('Posting your comment......');
        $('.commentbutton').attr("disabled", "disabled");
        data['comment'] = $('#trn_user_comments_comment').val();
    }
    console.log(data);
    $.ajax({
        url: commentPath,
        type: 'POST',
        data: data,
        success: (function (formdata) {

            // empty the text boxes
            if(commentid != '') {
                // hide current open reply box if replying
                $("#replypostarea"+commentid).hide();
                $('#reply_box_'+commentid).val('');
            } else {
                $('#trn_user_comments_comment').val('');
            }
            message.text('');
            message.text(formdata['message']);
            message.slideDown(500, function(){
                setTimeout(function(){
                    message.text('');
                    message.slideUp(500);
                },3000);
            });

            //$("#submitcomment").show();

            //enable all buttons
            $('.commentbutton').removeAttr("disabled");
            $('.replybutton').removeAttr("disabled");

            //window.location.reload();
        })
    });
}

function showReplyBlock(commentid) {
    $("#replypostarea"+commentid).toggle();
    //$("#replyarea"+commentid).toggle();
}

function getLikeCount(commentId, spanupdateId) {
    var data = {};
    //var articleId = $("#article_id").val();
    data['id'] = commentId;

    var spanupdate = $("#"+spanupdateId);

    jQuery.ajax({
        url: eventCommentLike,
        data: data,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            console.log(spanupdate);
            spanupdate.text(data.count);
        }
    });
}

function getLoginMsg() {

    var message = $("#loginMsg");

    // message.text = "Please Login to post a comment";
    message.slideDown(500, function(){
        setTimeout(function(){
            message.slideUp(500);
        },3000);
    });
}