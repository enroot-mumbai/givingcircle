
function wordCount(elem,maxLen, leftcountelem = '') {

    var wordLen = maxLen,len; // Maximum word length
    len = $(elem).val().split(/[\s]+/);

    console.log(event.keyCode+"-"+event.which);

    if (len.length >= wordLen) {

        if (event.keyCode == 46 || event.keyCode == 8 || event.which == 46 || event.which == 8 ) {
            // Allow backspace and delete buttons
        } else {
            // if (event.keyCode < 48 || event.keyCode > 57 || event.which < 48 || event.which > 57) {
            // Disable all other buttons
            event.preventDefault();
        }

        /*
        // Remove extra words
        var delCount = len.length - wordLen;

        for (i=0; i<= delCount; i++) {
            len.pop();
        }
        $(elem).val(len.join(' '));
         */

    }

        if($(elem).val() == '') {
            wordsLeft = maxLen;
        } else {
            wordsLeft = (wordLen) - len.length;
        }

        if(leftcountelem != '') {
            $(leftcountelem).html(wordsLeft);
        } else {
            $(elem).siblings('.textarea-instru').find('.words-left').html(wordsLeft);
        }
}

function getCircleShareCount(circleId) {
    var data = {};
    data['id'] = circleId;

    jQuery.ajax({
        url: pathToCircleShare,
        data: data,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            // console.log(spanupdate);
            //spanupdate.text(data.count);
        }
    });
}

function getEventShareCount(eventId) {
    var data = {};
    data['id'] = eventId;

    jQuery.ajax({
        url: pathToEventShare,
        data: data,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            // console.log(spanupdate);
            //spanupdate.text(data.count);
        }
    });
}

function getArticleShareCount(circleId) {
    var data = {};
    data['id'] = circleId;

    jQuery.ajax({
        url: pathToArticleShare,
        data: data,
        type: "GET",
        dataType: "JSON",
        success: function (data) {
            // console.log(spanupdate);
            //spanupdate.text(data.count);
        }
    });
}

function headerSearch() {
    var elem = $("#headerSearchInp");
    var textVal = $.trim(elem.val());
    if(textVal == '') {
        alert("Please enter search text");
        elem.focus();
        return false;
    }
    $('#searchText').val(textVal);
    $("#headerSrchFrm").submit();
}

function isUrlValid(url) {
    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}