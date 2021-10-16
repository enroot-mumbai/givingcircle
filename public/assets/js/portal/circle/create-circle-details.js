
$(document).ready(function () {
    $('body').addClass('inner-pg');
    $('body').addClass('create-element');
    $('body').addClass('logged');
    $('html, body').animate(
        {
            scrollTop: $($('.next-section-arrow').attr('href')).offset().top - 95,
        },
        800,
        'linear'
    );
    $('.btn-continue').click(function (){
        if (nSelectedPrimaryAI == 0) {
            alert("Please select primary area of interest.");
            return false;
        }
        $("#frmSelectAreaOfInterest").submit();
    });
    $('[name="areaOfInterest"]').click(function (){
        var data = {};
        data['interestId'] = $(this).attr('areaOfInterestId');

        jQuery.ajax({
            url: getSecondaryAreaOfInterestURL,
            data: data,
            type: "POST",
            dataType: "html",
            success: function (data) {
                var result = $("#selectAreaModalBody");
                result.html('');
                result.html(data);
                AOS.init({
                    delay: 50,
                    duration: 800,
                });
            }
        });
    });

    $(document).on('click', '.btn-close', function (e) {
        $('#selectAreaModal').modal('hide');
    });

    $(window).scroll(function () {
        if ($(this).scrollTop() > 150) {
            $('.bullets .first').removeClass('active');
            $('.bullets .second').addClass('active');
        } else {
            $('.banner-sec').removeClass('fixed');
            $('.wrapper').removeClass('fixed');
            $('.bullets .first').addClass('active');
            $('.bullets .second').removeClass('active');
        }
    });
    $('.next-section-arrow').on('click', function (e) {
        e.preventDefault()
        $('html, body').animate(
            {
                scrollTop: $($(this).attr('href')).offset().top - 95,
            },
            800,
            'linear'
        );
    });
    $('.bullets li.second > a').on('click', function (e) {
        e.preventDefault()
        $('html, body').animate(
            {
                scrollTop: $($(this).attr('href')).offset().top - 95,
            },
            800,
            'linear'
        );
        $(this).parent('li').addClass('active');
        $(this).parent('li').siblings('li').removeClass('active');
    });
    $(document).on('click', '.checkbox-label input[type=\'checkbox\']', function (e) {
        $(this).parent().toggleClass("checked");
    });

    $(document).on('click', '#btnSaveSecondaryAreaOfInterest', function (e) {
        $('#selectAreaModal').modal('hide');
        jQuery.ajax({
            url: saveAreaOfInterestToSectionURL,
            data: $("#frmSaveSecondaryAreaOfInterest").serialize(),
            type: "POST",
            dataType: "json",
            success: function (data) {
                nSelectedPrimaryAI = data.count;
                if (data.addSecondary == true ) {
                    $("#liPrimaryAI"+data.primaryAreaOfInterest).addClass('active');
                    var strHtml = '<i class="fa fa-eye"></i>\n' +
                        '               <span class="tooltiptext">\n' +
                        '                   <ul>\n';
                    console.log(data.secAIList);
                    for ( AIName in data.secAIList) {
                        strHtml +='<li>'+data.secAIList[AIName]+'</li>';
                    }

                    strHtml +='</ul></span>';
                    console.log(strHtml);
                    $("#tooltip_"+data.primaryAreaOfInterest).html(strHtml);

                } else {
                    $("#liPrimaryAI"+data.primaryAreaOfInterest).removeClass('active');
                    $("#tooltip_"+data.primaryAreaOfInterest).html('');
                }
            }
        });
    });
    $($('.select-interest > li').get().reverse()).each(function (index) {
        $(this).css('z-index', index + 10);
    });

    var width = $(window).width();
    if(width > 930 && width < 989){
        $(".create-element .start-project .interest-area .select-interest > li:nth-child(5n)").addClass("tooltip-auto");
    }
    if(width > 1265 || width > 760 && width < 929 || width > 525 && width < 576){
        $(".create-element .start-project .interest-area .select-interest > li:nth-child(4n)").addClass("tooltip-auto");
    }
    if(width > 990 && width < 1264 || width > 590 && width < 759 || width > 395 && width < 524){
        $(".create-element .start-project .interest-area .select-interest > li:nth-child(3n)").addClass("tooltip-auto");
    }
    if(width > 375 && width < 394 || width > 577 && width < 589){
        $(".create-element .start-project .interest-area .select-interest > li:nth-child(2n)").addClass("tooltip-auto");
    }
});