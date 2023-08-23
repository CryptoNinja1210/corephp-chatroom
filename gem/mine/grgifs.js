var xmlHttp;
var stickerreq;
function httpGetAsync(theUrl, callback) {
    xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            callback(xmlHttp.responseText);
        }
    };
    xmlHttp.open("GET", theUrl, true);
    xmlHttp.send(null);

    return;
}
function tenorCallback_search(responsetext) {
    var response_objects = JSON.parse(responsetext);
    grgifs = response_objects["results"];
    var rs = '';
    if (grgifs.length === 0) {
        $(".grgif .loading").addClass('error');
    } else {
        $.each(grgifs, function(k, v) {
            var nano = grgifs[k]["media"][0]["tinygif"]["url"];
            var share = grgifs[k]["media"][0]["tinygif"]["url"];
            rs = rs+"<li gif='"+share+"'><img class='lazygif' data-src='"+nano+"'/></li>";
        });
        $(".grgif .grgifconts").html(rs);
        $(".lazygif").Lazy({
            bind: "event",
            onFinishedAll: function(element) {
                $(".grgif .loading").hide();
                grscroll($(".grgif > .wrap > div"), "resize");
            }
        });
    }
    return;

}


function grab_data(tab, term) {
    if (tab == undefined) {
        tab = "gifs";
    }
    if (xmlHttp != undefined) {
        xmlHttp.abort();
    }
    if (stickerreq != undefined) {
        stickerreq.abort();
    }
    $(".grgif > .wrap").removeClass('stickerlist');
    $(".grgif .loading").removeClass('error').show();
    $(".grgif .grgifconts").html('');
    if (tab == 'gifs') {
        $(".grgif .stickerpacks").hide();
        $(".grgif > .wrap > .search").css('display', 'block');
        var apikey = $(".dumb .gdefaults").find(".tenorapi").text();
        var lmt = $(".dumb .gdefaults").find(".tenorlimit").text();
        if (term == undefined) {
            var srch = "trending?";
        } else {
            var srch = "search?tag=" + term;
        }
        var search_url = "https://api.tenor.com/v1/"+srch+"&key="+apikey+"&limit=" +lmt;
        httpGetAsync(search_url, tenorCallback_search);
    } else if (tab == 'stickers') {
        $(".grgif > .wrap").addClass('stickerlist');
        $(".grgif > .wrap > .search").hide();
        $(".grgif .stickerpacks").css('display', 'block');
        if ($('.grgif .stickerpacks > span.packs > ul > li.active').length == 0) {
            $('.grgif .stickerpacks > span.packs > ul > li').eq(0).trigger('click');
        } else {
            $('.grgif .stickerpacks > span.packs > ul > li.active').trigger('click');
        }
    }
    return;
}
$("body").on('click', '.grgif .stickerpacks > span.packs > ul > li', function(e) {
    $('.grgif .stickerpacks > span.packs > ul > li').removeClass('active');
    $(this).addClass('active');
    var data = {
        act: 1,
        do: "stickers",
        type: 'list',
        pack: $(this).attr('no')
    };
    stickerreq = $.ajax({
        type: 'POST',
        url: '',
        data: data,
        async: true,
        dataType: 'json',
        success: function(data) {}
    }).done(function(data) {
        var rs = '';
        $.each(data, function(k, v) {
            rs = rs+"<li gif='"+v+"'><img class='lazygif' data-src='"+v+"'/></li>";
        });
        $(".grgif .grgifconts").html(rs);
        $(".lazygif").Lazy({
            bind: "event",
            onFinishedAll: function(element) {
                $(".grgif .loading").hide();
                grscroll($(".grgif > .wrap > div"), "resize");
            }
        });
    }).fail(function(qXHR, textStatus, errorThrown) {
        $(".grgif .loading").addClass('error');
    });

});

$("body").on('click', '.gr-gif', function(e) {
    if ($(".grgif").is(':visible')) {
        $(".swr-grupo .panel > .room").css("padding-bottom", "80px");
        $(".grgif").hide();
        $(".emojionearea > .emojionearea-editor").css("height", "auto");
        grscroll($(".swr-grupo .panel > .room > .msgs"), 'resize');
    } else {
        if (!$(this).hasClass('opnd')) {
            $(".grgif > .wrap > .switchtabs > ul > li").eq(0).trigger('click');
        }
        $(this).addClass('opnd');
        $(".swr-grupo .panel > .oldroom").css("padding-bottom", "334px");
        grscroll($(".swr-grupo .panel > .room > .msgs"), 'resize');
        scrollmsgs();
        $(".emojionearea > .emojionearea-editor").css("height", "20px");
        $(".grgif").addClass('animate__animated animate__fadeInUp animate__faster').show();
    }
});

$("body").on('click', '.grgif > .wrap > .switchtabs > ul > li', function(e) {
    $('.grgif > .wrap > .switchtabs > ul > li').removeClass('active');
    grab_data($(this).attr('load'));
    $(this).addClass('active');
});

$("body").on('click', '.grgif > .wrap > div > .grgifconts > li', function(e) {
    var gfm = $(this).attr('gif');
    var gif = $(this).find('img').attr('src');
    var gfw = $(this).find('img').get(0).naturalWidth;
    var gfh = $(this).find('img').get(0).naturalHeight;
    var mtype = 'gif';
    if ($('.grgif > .wrap > .switchtabs > ul > li.active').attr('load') == 'stickers') {
        mtype = 'sticker';
    }
    grsendmsg($(this), e, gif, gfm, gfw, gfh, mtype);
});
$('.grgif > div > .search > input').on('keypress', function(e) {
    if (e.which == 13) {
        grab_data('gifs', $(this).val());
    }
});