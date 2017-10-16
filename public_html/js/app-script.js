$(document).ready(function(){

/////////   Settings   //////////////////////////////////////////////////////////////////

    if ($(document).width() < 992) {
        $('.confirm-delete-friend').css('marginTop', '10px');
    }

////////   For login form remember me   /////////////////////////////////////////////////

    $('#login-form label:eq(2)').removeClass('control-label');

/////////   For form asterisk   /////////////////////////////////////////////////////////

    var asterisk = function () {
        var obj = $("form label:contains('*')");

        if (obj) {
            var objLength = obj.length - 1;
            for (var i = objLength; i >= 0; i--) {
                var newString = obj.eq(i).html().replace("*", "<strong class='color-red'>*</strong>");
                obj.eq(i).html(newString);
            }
        }
    }();

/////////   For form-logout   ///////////////////////////////////////////////////////////

    if ($(document).width() < 768) {
        $('.form-logout').css({
            color: '#4582ec',
            padding: '5px 15px 5px 25px'
        })
        $('.form-logout').hover(
            function () {
                $(this).css('background', 'none');
            }
        );
    }

/////////   For confirm plugin   ////////////////////////////////////////////////////////

    $('.confirm-delete-friend').jConfirmAction({
        question: 'Are you sure?',
        noText: 'Cancel'
    });

    $('.confirm-delete-image').jConfirmAction({
        question: 'Do you want to delete image?',
        noText: 'Cancel'
    });

    $('.admin-delete-status').jConfirmAction({
        question: 'Are you sure?',
        noText: 'Cancel'
    });

/////////   For portfolio   /////////////////////////////////////////////////////////////

    if ($(document).width() < 768) {
        $('.portfolio-box').removeClass('pull-right');
    }

/////////   For scroll top   ////////////////////////////////////////////////////////////

    $(window).scroll(function () {
        if ($(this).scrollTop() > 1000) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });

    // scroll body to 0px on click
    $('#back-to-top').click(function () {
        $('#back-to-top').tooltip('hide');
        $('body,html').animate({
            scrollTop: 0
        }, 500);
        return false;
    });

    $('#back-to-top').tooltip('show');

/////////   Validation replay-form   ////////////////////////////////////////////////////

    $('form.replay-form').on('submit', function(){
        $(this).find('ul.formError li').remove();

        var id = $(this).attr('data-id');
        var content = $(this).find('textarea').val();
        var message = '';

        if (content.length == 0) {
            message = 'The input cannot be empty';
        } else if (content.length == 1) {
            message = 'The input is less than 2 characters long';
        } else if (content.length > 1000) {
            message = 'The input is more than 1000 characters long';
        }

        if (message) {
            $(this).find('ul.formError').append('<li class="color-red">' + message + '</li>');
            return false;
        }

        return true;
    });

/////////   Like button   ///////////////////////////////////////////////////////////////

    var likeMessage = function (message) {
        setTimeout(function() {
            (message).fadeOut(300);
        }, 3000);
    };

    var addLike = function () {
        $('a.like-button').on('click', function(){

            var dataId       = $(this).attr('data-id');
            var dataIdentity = $(this).attr('data-identity');

            //var likeCount = $(this).parent('li').siblings('li.likes-box').find('span.like-count');
            var likeCount = $(this).siblings('span.like-count');
            var liked     = $(this).parent('li').siblings('li.already-liked');
            liked.show();


            $.ajax({
                url: '/timeline/add-like',
                type: 'post',
                dataType: 'json',
                data: {id: dataId, identity: dataIdentity},
                success: function (data) {
                    if (data) {
                        if (data.statusCount) {
                            likeCount.html('<span class="badge">' + data.statusCount + '</span>');
                        }
                        if (data.alreadyLiked) {
                            liked.css('display', 'inline-block');
                            likeMessage(liked.html('<span class="color-red">' + data.alreadyLiked + '</span>'));
                        }
                    }
                }
            });

            return false;
        })
    };

    addLike();

/////////   Search user in admin area   /////////////////////////////////////////////////

    var searchUserAdminArea = function () {
        $('#admin-search-user-form').on('submit', function(){
            var search = $('#admin-search-user-form').serialize();


            $.ajax({
                url: '/admin/users/search',
                type: 'post',
                dataType: 'json',
                data: search,
                success: function(data){
                    if (data.results.length > 0) {
                        $('#admin-search-user-form-result li').remove();
                        for (key in data.results) {
                            var appObj = '<li><a href="/admin/users/edit/' + data.results[key]['id'] + '">' + data.results[key]['name'] + '</a></li>';
                            $('#admin-search-user-form-result').append(appObj);
                        }
                    } else {
                        $('#admin-search-user-form-result li').remove();
                        $('#admin-search-user-form-result').append('<li>No result found</li>');
                    }
                }
            });

            return false;
        })
    };

    searchUserAdminArea();

    $(document).on('click', function(event){
        $('#admin-search-user-form-result li').remove();
    });

/////////   For profile-box class   /////////////////////////////////////////////////////

    if ($(document).width() < 768) {
        $('.profile-box a.btn.btn-default').removeClass('pull-right').css('marginTop', '10px');
    }

/////////   For user delete modal   /////////////////////////////////////////////////////

    $('#delete-user').click('on', function(){
        $('.delete-user-form').submit();
    });

/////////   For button input file   /////////////////////////////////////////////////////

    if ($(document).width() < 460) {

        $(":file").jfilestyle({
            inputSize: "100%",
            buttonText: "Choose image"
        });

        $('.jfilestyle').removeClass('jfilestyle-corner');
        $('.jfilestyle input').css('marginBottom', '10px');
        $('.jfilestyle').css('display', 'block');
        $('.jfilestyle input').css('display', 'block');
    } else {
        $(":file").jfilestyle({
            inputSize: "300px",
            buttonBefore: true,
            buttonText: "Choose image"
        });
    }

    if ($(document).width() < 565) {
        $('.add_to_gallery').css({
            marginLeft: '0px',
            display: 'block'
        });
    }

/////////   In order for the pictures in the gallery to become in a row exactly 4   /////

    var userImageCount = $('.user-image-box-out').length;

    for (var i = 1; i <= userImageCount; i++) {
        console.log(i);
        if (i % 4 == 0) {
            if (i == 0) { continue; }
            $('.user-image-box-out').eq(i - 1).after('<div class="clearfix"></div>');
        }
    }

/////////   Show 'Profile deleted' message   ////////////////////////////////////////////

    var signInMessage = function (obj) {
        obj.fadeIn(300);
        setTimeout(function() {
            obj.fadeOut(300);
        }, 3000);
    };

    $('a.user-profile-img').on('click', function(event){
        var active = $(this).attr('data-active');

        if (active === '1') {
            return true;
        } else {
            signInMessage($(this).next('.media-body').find('.not-active').eq(0).text('Profile deleted'));
            return false;
        }
    });

    $('a.user-profile-name').on('click', function(){
        var active = $(this).attr('data-active');

        if (active === '1') {
            return true;
        } else {
            $(this).hover().css('textDecoration', 'none');
            signInMessage($(this).next('.not-active').text('Profile deleted'));
            return false;
        }
    });

/////////   Settings   //////////////////////////////////////////////////////////////////

    $('input.radio-block').parent('label').css('marginRight', '10px');

/////////   For tooltip   ///////////////////////////////////////////////////////////////

    $('[data-toggle="tooltip"]').tooltip();

/////////   For top menu  ///////////////////////////////////////////////////////////////

    if ($(document).width() > 768) {
        $('#change-language-form-with-flags').css({
            'marginLeft': '20px',
        })
    }

/////////   Empty captcha value after unsuccessful authorization  ///////////////////////

    // In order for the code to be executed only on a specific page
    if (location.pathname.indexOf('/register') === 0) {
        $('input#captcha').val('');
    }

/////////   END   ///////////////////////////////////////////////////////////////////////

});
