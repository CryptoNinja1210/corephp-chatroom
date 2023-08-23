var grversion = '2.6.1';
var alertitle;
var gruploader;
var grlzyload;
var gremojiz;
var grprofanity;
var actcustomscroll = 0;
var webthumbnail = null;
var animopen = $(".dumb .gdefaults").find(".pagetransstart").text();
var animclose = $(".dumb .gdefaults").find(".pagetransend").text();
var radioenabled = $(".dumb .gdefaults").find(".autoplayradio").text();
$("body").on('click', '.swr-grupo .aside > .tabs > ul > li,.loadside', function(e) {
    if (!$(e.target).parent().hasClass('subtab')) {
        $('.swr-grupo .aside > .tabs > ul > li > .subtab > li').removeClass('active');
        loadlist($(this), e);
    }
});
$("body").on('click', '.swr-grupo .aside > .tabs > ul > li > .subtab > li', function(e) {
    if ($(this).attr('filtr') != 'all') {
        $(this).parent().parent().attr('filtr', $(this).attr('filtr'));
    } else {
        $(this).parent().parent().removeAttr('filtr');
    }
    $(this).parent().parent().trigger('click');
    $(this).parent().parent().removeAttr('filtr');
});
$("body").on('click', '.swr-grupo .aside > .tabs > ul > li > .subtab > li', function(e) {});

function loadlist(el, e) {
    el.attr('type', 'json');
    el.attr('spin', 'off');
    el.attr('process', '1');
    el.find('i').html('');
    $('.tooltip').remove();
    if (ajxvar['grlive'] != undefined) {
        ajxvar['grlive'].abort();
    }
    if (ajxclrtm['grlive'] != undefined) {
        clearTimeout(ajxclrtm['grlive']);
    }
    $('.swr-grupo .'+el.attr('side')+' .listloader').removeClass('error').fadeIn();
    $proc = $(".swr-grupo ."+el.attr('side')+" > .content > .list").parent().find('.grproceed');
    if (!el.hasClass("grproceed")) {
        $proc.addClass("loadside");
        $(".swr-grupo ."+el.attr('side')+" > .content > .list").scrollTop(0);
        $('.swr-grupo .'+el.attr('side')+' > .tabs > ul > li').removeClass("active");
        if (el.hasClass('loadside')) {
            var tabtitle = el.text();
            if (el.attr('tabtitle') != undefined) {
                tabtitle = el.attr('tabtitle');
            }
            $('.swr-grupo .'+el.attr('side')+' > .tabs > ul > li.xtra').html('<span>'+tabtitle+'</span>');
            $('.swr-grupo .'+el.attr('side')+' > .tabs > ul > li.xtra').addClass('active');
            $('.swr-grupo .'+el.attr('side')+' > .tabs > ul > li.xtra').attr('side', el.attr('side'));
            $('.swr-grupo .'+el.attr('side')+' > .tabs > ul > li.xtra').attr('act', el.attr('act'));
            $('.swr-grupo .'+el.attr('side')+' > .tabs > ul > li.xtra').attr('xtra', el.attr('xtra'));
            $('.swr-grupo .'+el.attr('side')+' > .tabs > ul > li.xtra').attr('gmid', el.attr('gmid'));
            if (el.attr('srch') != undefined) {
                $('.swr-grupo .'+el.attr('side')+' > .tabs > ul > li.xtra').attr('srch', el.attr('srch'));
            }
            $('.swr-grupo .'+el.attr('side')+' > .tabs > ul > li.xtra').attr('zero', el.attr('zero'));
            $('.swr-grupo .'+el.attr('side')+' > .tabs > ul > li.xtra').attr('zval', el.attr('zval'));
        } else {
            el.addClass("active");
        }
    }
    $proc.attr('side', el.attr('side'));
    $proc.attr('act', el.attr('act'));
    $proc.attr('xtra', el.attr('xtra'));
    $proc.attr('filtr', el.attr('filtr'));
    $proc.attr('gmid', el.attr('gmid'));
    $proc.text(el.text());
    var ofs = sofs = 0;
    if (el.hasClass("grproceed")) {
        ofs = $proc.attr('offset');
        sofs = $proc.attr('soffset');
    }
    var data = {
        act: 1,
        do: "list",
        type: el.attr('act'),
        gid: $('.swr-grupo .panel').attr('no'),
        ldt: $('.swr-grupo .panel').attr('ldt'),
        offset: ofs,
        soffset: sofs,
        search: el.attr('srch'),
        gmid: el.attr('gmid'),
        xtra: el.attr('xtra'),
        filtr: el.attr('filtr'),
        ex: el.data(),
    };
    if (!el.hasClass("grproceed") && !el.hasClass("searching")) {
        $(".swr-grupo ."+el.attr('side')+" > .search > input").val("");
    }
    if (!el.hasClass("grproceed")) {
        $proc.removeAttr('srch');
        $proc.removeAttr('filtr');
    }
    var s = 'var soffst=offst="off";$(".swr-grupo .aside > .content > .list").removeClass();$(".swr-grupo .aside > .content > ul").addClass("list fh '+el.attr('act')+'");';
    s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .list").hide();';
    s = s+'offst=data[0].offset;';
    s = s+'soffst=data[0].soffset;';
    s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .addmore").removeClass("shw");';
    s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .addmore").addClass(data[0].shw);';
    s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .addmore > span").attr("mnu",data[0].mnu);';
    s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .addmore > span").attr("act",data[0].act);';
    s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .addmore > span").html("<i class="+data[0].icn+"></i>");';
    s = s+'var list="";$.each(data, function(k, v) {if (k !== 0) {if (data[k].name != undefined) {';
    s = s+'list=list+"<li "+data[k].id+"> <div><span class='+"'"+'left lrmbg'+"'"+'><img class=lazyimg data-src="+data[k].img+">';
    s = s+'</span><span class=center><b><span data-toggle=tooltip title='+"'"+'"+htmlDecode(data[k].name)+"'+"'"+'>"+htmlDecode(data[k].name)+"</span></b><i class="+data[k].icon+"></i>";';
    s = s+'list=list+"<u class=cnts>";';
    s = s+'if(data[k].count!="0"){if(data[k].countag!="0"){list=list+"<u cnt="+data[k].count+">"+data[k].count+" "+htmlDecode(data[k].countag)+"</u>";}}';
    s = s+'list=list+"</u><span>"+htmlDecode(data[k].sub)+"</span></span><span class=right>';
    s = s+'<span class=opt "+data[k].rtag+"><i class=gi-dot-3></i><ul>";';
    s = s+'if(data[k].oa!==0){list=list+"<li "+data[k].oat+">"+htmlDecode(data[k].oa)+"</li>";}';
    s = s+'if(data[k].ob!==0){list=list+"<li "+data[k].obt+">"+htmlDecode(data[k].ob)+"</li>";}';
    s = s+'if(data[k].oc!==0){list=list+"<li "+data[k].oct+">"+htmlDecode(data[k].oc)+"</li>";}';
    s = s+'if(data[k].od!==undefined){if(data[k].od!==0){list=list+"<li "+data[k].odt+">"+htmlDecode(data[k].od)+"</li>";}}';
    s = s+'list=list+"</ul></span></span></div></li>";';
    s = s+'}}});if(data===null || data.length===1){';
    s = s+'list="<div class=zeroelem> <div> <span>'+el.attr('zero')+'<span>'+el.attr('zval')+'</span> </span> </div> </div>";';
    s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .grproceed").removeClass("loadside");';
    s = s+'}$(".swr-grupo .'+el.attr('side')+' > .tabs > ul > li.active > i").html("");';
    if (el.hasClass('appnd')) {
        s = s+'if(data!=null && data.length!=1){';
        s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .list").append(list);';
        s = s+'}';
    } else {
        s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .list").html(list);';
    }
    s = s+'grscroll($(".swr-grupo .'+el.attr('side')+' > .content > .list"),"resize");';
    s = s+'$("[data-toggle=tooltip]").tooltip();';
    s = s+'var sdr="'+el.attr('side')+'";if (sdr=="rside") {';
    s = s+'$(".swr-grupo .rside > .content .profile").hide();}';

    if (el.hasClass('dofirst')) {
        s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .list > li:first-child").trigger("click");';
        el.removeClass('dofirst');
    }

    if (el.hasClass('loaditem')) {
        s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .list > li[no='+el.attr('itemid')+'] > div > .right > .opt > ul > li").eq('+el.attr('itemopt')+').trigger("click");';
        el.removeClass('loaditem');
    }

    if (el.attr('list') !== undefined) {
        s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .list > li[no='+el.attr('list')+']").trigger("click");';
        el.removeAttr('list');
    }

    if (el.attr('act') === 'groups' || el.attr('act') === 'pm') {
        s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .list > li[no="+$(".swr-grupo .panel").attr("no")+"]").addClass("active");';
        if (el.attr('openid') !== undefined) {
            s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .list > li[no='+el.attr('openid')+']").trigger("click");';
            el.removeAttr('openid');
        }
        if (el.attr('openfirstz') !== undefined && $(".dumb .gdefaults > .defload").attr('no') == undefined) {
            if ($(window).width() > 991) {
                s = s+'setTimeout(function() {$(".swr-grupo .'+el.attr('side')+' > .content > .list > li.paj:first-child").trigger("click");}, 500);';
            }
            el.removeAttr('openfirst');
        }
    }
    s = s+'if(offst!="off"){$(".swr-grupo .'+el.attr('side')+' > .content > .grproceed").attr("offset",offst);}';
    s = s+'if(soffst!="off"){$(".swr-grupo .'+el.attr('side')+' > .content > .grproceed").attr("soffset",soffst);}';
    s = s+'$(".swr-grupo .'+el.attr('side')+' .listloader").fadeOut();';
    s = s+'$(".swr-grupo .'+el.attr('side')+' > .content > .list").fadeIn();';
    var f = '$(".swr-grupo .'+el.attr('side')+' .listloader").addClass("error");';
    var ajv = 'loadlist'+el.attr('side');
    var grlv = 'ajxclrtm["grlivesetin"]=setTimeout(function() {gr_live();}, 2000);';
    s = s+grlv;
    f = f+grlv;
    ajxx(el, data, s, e, f, ajv);
}

$("body").on('mouseenter', '.swr-grupo .aside > .content .profile > .top > span.edit > span', function(e) {
    $(".swr-grupo .aside > .content .profile > .top > span.dp").fadeOut();
    $(".swr-grupo .aside > .content .profile > .top > span.name").fadeOut();
    $(".swr-grupo .aside > .content .profile > .top > span.roleimg").fadeOut();
    $(".swr-grupo .aside > .content .profile > .top > span.role").fadeOut();
    $(".swr-grupo .aside > .content .profile > .top > span.coverpic > span").fadeOut();
});

$("body").on('click', '.swr-grupo .panel > .textbox > .box > .icon > .gr-moreico', function(e) {
    if ($(this).hasClass('active')) {
        $(this).removeClass('active');
    } else {
        if ($(this).hasClass('qrcode')) {
            $(this).removeClass('qrcode');
            $('.gr-qrcode.active').trigger('click');
        } else if ($(this).hasClass('recording')) {
            stopRecording();
        } else {
            $(this).addClass('active');
        }
    }
});

$("body").on('mouseleave', '.swr-grupo .aside > .content .profile > .top > span.edit > span', function(e) {
    $(".swr-grupo .aside > .content .profile > .top > span.dp").fadeIn();
    $(".swr-grupo .aside > .content .profile > .top > span.roleimg").fadeIn();
    $(".swr-grupo .aside > .content .profile > .top > span.name").fadeIn();
    $(".swr-grupo .aside > .content .profile > .top > span.role").fadeIn();
    $(".swr-grupo .aside > .content .profile > .top > span.coverpic > span").fadeIn();
});

$("body").on('click', '.swr-grupo .panel > .room > .msgs > li > div i.info > i.tick.recieved', function(e) {
    if ($('.swr-grupo .panel').attr('ldt') != 'user') {
        if ($(window).width() <= 767.98) {
            $('[data-toggle="tooltip"]').tooltip('hide');
            $(".swr-grupo .lside .opt > ul").hide();
            $('.swr-grupo .lside,.swr-grupo .panel').removeClass('abmob');
            $(".swr-grupo .lside,.swr-grupo .panel").addClass("bwmob");
            $('.swr-grupo .rside > .top > .left > .icon').attr('data-block', 'alerts');
            if (!$('.rside').hasClass('abmob')) {
                $(".swr-grupo .rside").removeClass("animate__animated "+animclose+" animate__fast");
                $(".swr-grupo .rside").removeClass("nomob");
                $(".swr-grupo .rside").addClass("abmob");
                $(".swr-grupo .rside").addClass("animate__animated "+animopen+" animate__fast");
            }
        }
        if ($(window).width() >= 768 && $(window).width() <= 991) {
            grtabfold();
        }
        $('.dumb .lastseenz').attr('gmid', $(this).parents('li').attr('no'));
        $('.dumb .lastseenz').trigger('click');
    }
});

$("body").on('mouseenter', '.swr-grupo .panel > .room > .msgs > li', function(e) {
    $('.swr-grupo .msgopt > ul').hide();
    $(this).find('.msgopt i').hide();
    $(this).find('.msgopt ul').css('display', 'inline');
});

$("body").on('mouseleave', '.swr-grupo .panel > .room > .msgs > li', function(e) {
    $('.swr-grupo .msgopt > ul').hide();
});

$("body").on('mouseenter', '.swr-grupo .aside > .content > .list > li', function(e) {
    if ($(window).width() > 991) {
        $('.swr-grupo .opt > ul').hide();
        $('.swr-grupo .opt > i').show();
        $(this).find('.opt > i').hide();
        $(this).find('.opt > ul').css('display', 'table-cell');
    }
});

$("body").on('mouseleave', '.swr-grupo .aside > .content > .list > li', function(e) {
    if ($(window).width() > 991) {
        $('.swr-grupo .opt > i').show();
        $('.swr-grupo .opt > ul').hide();
    }
});
$('.swr-grupo').on('hover blur focus tap touchstart', function(e) {
    if (alertitle != undefined) {
        clearTimeout(alertitle);
        $(".dumb .newmsgalert").attr('alert', 'off');
        document.title = $(".dumb .webtitle").text();
    }
});

$('.grupo-preview > div .cntrls > .gi-plus').on('click tap touchstart', function(e) {});

$('.swr-grupo').on('click', function(e) {
    if (!$(e.target).parent().parent().hasClass('swr-menu') && !$(e.target).hasClass('subnav')) {
        if (!$(e.target).parent().hasClass('langswitch')) {
            $('.swr-menu').hide();

        }
    }
    if (!$(e.target).parents('.switchuser').hasClass('switchuser')) {
        $('.swr-grupo .panel > .textbox > .box > .switchuser > .uslist').hide();
    }
    if (!$(e.target).hasClass('gr-gif') && !$(e.target).parents('.grgif').hasClass('grgif')) {
        $(".swr-grupo .panel > .room").css("padding-bottom", "80px");
        $(".grgif").hide();
        $(".emojionearea > .emojionearea-editor").css("height", "auto");
    }
});

$('body').on('click', '.swr-grupo .aside > .content > .addmore > span', function(e) {
    var c = $(this).attr('mnu');
    var i = $(this).attr('act');
    if (i == 'uploadfile') {
        $('.swr-grupo .aside > .head > .icons > i.udolist .uploadfiles > input[type=file]').trigger('click');
    } else {
        menuclick(c, i);
    }
});

$('body').on('click', '.swr-grupo .panel > .room > .msgs > li .usrment', function(e) {
    if ($(this).attr("mention") != 0 && $('.swr-grupo .panel').attr('ldt') != 'user') {
        var ta = $(".swr-grupo .panel > .textbox > .box > textarea").data("emojioneArea");
        if ($('.emojionearea > .emojionearea-editor:contains("'+$(this).attr("mention")+'")').length == 0) {
            ta.setText('@'+$(this).attr("mention")+ta.getText());
            $('.emojionearea > .emojionearea-editor').focus();
            placeCaretAtEnd($(".emojionearea > .emojionearea-editor").data("emojioneArea").editor[0]);
        }
    }
});

$('body').on('click', '.swr-grupo .panel > .textbox > .box >.switchuser > .usrimg', function() {
    if ($('.swr-grupo .panel > .textbox > .box > .switchuser > .uslist').is(":hidden")) {
        $('.swr-grupo .panel > .textbox > .box > .switchuser > .uslist').fadeIn();
        if (!$(this).hasClass('loaded')) {
            var e = $.Event("keypress", {
                which: 13
            });
            $('.swr-grupo .panel > .textbox > .box > .switchuser > .uslist > span > input').trigger(e);
        }
    } else {
        $('.swr-grupo .panel > .textbox > .box > .switchuser > .uslist').hide();
    }
});

$('body').on('click', '.swr-grupo .panel > .textbox > .box > .switchuser > .uslist > ul > li', function() {
    $('.swr-grupo .panel > .textbox .userid').val($(this).attr('no'));
    $('.swr-grupo .panel > .textbox > .box >.switchuser > .usrimg > img').attr('src', $(this).find('img').attr('src'));
    $('.swr-grupo .panel > .textbox > .box > .switchuser > .uslist').hide();
});

$('.swr-grupo .panel > .textbox > .box > .switchuser > .uslist > span > input').on('keypress', function(e) {
    if (e.which == 13) {
        var data = {
            act: 1,
            do: "list",
            type: 'memsearch',
            ser: $(this).val(),
            ulist: 1,
            gid: $(".swr-grupo .panel").attr("no")
        };
        ajxvar['userlist'] = $.ajax({
            type: 'POST',
            url: '',
            data: data,
            async: true,
            dataType: 'json',
            beforeSend: function() {
                if (ajxvar['userlist'] !== null && ajxvar['userlist'] !== undefined) {
                    ajxvar['userlist'].abort();
                }
            },
            success: function(data) {}
        }).done(function(data) {
            $('.swr-grupo .panel > .textbox > .box > .switchuser > .uslist > ul').html('');
            $('.swr-grupo .panel > .textbox > .box >.switchuser > .usrimg').addClass('loaded');
            var defaultuser = {
                id: $('.swr-grupo .rside > .top > .left > span.vwp').attr('no'),
                img: $('.swr-grupo .rside > .top > .left > span.vwp > img').attr('src'),
                name: $('.swr-grupo .rside > .top > .left > span.vwp > span').html().split('<span>')[0],
            };
            $('.swr-grupo .panel > .textbox > .box > .switchuser > .uslist > ul').append("<li no='"+defaultuser.id+"'><img src='"+defaultuser.img+"'><span>"+defaultuser.name+"</span></li>");
            $.each(data, function(k, v) {
                $('.swr-grupo .panel > .textbox > .box > .switchuser > .uslist > ul').append("<li no='"+data[k].id+"'><img src='"+data[k].img+"'><span>"+data[k].name+"</span></li>");
            });
        }) .fail(function(qXHR, textStatus, errorThrown) {
            $(".swr-grupo .panel").attr('deactiv', 0);
        });
    }
});

$("body").on('mouseenter', '.swr-grupo .aside > .tabs > ul > li', function(e) {
    $(this).find(".subtab").show();
});

$("body").on('mouseleave', '.swr-grupo .aside > .tabs > ul > li', function(e) {
    $(".swr-grupo .aside > .tabs > ul > li > .subtab").hide();
});

$("body").on('click', '.swr-grupo .aside > .tabs > ul > li > .subtab > li', function(e) {
    $('.swr-grupo .aside > .tabs > ul > li > .subtab > li').removeClass('active');
    if ($(this).attr('filtr') != 'all') {
        $(this).addClass('active');
    }
    $(".swr-grupo .aside > .tabs > ul > li > .subtab").hide();
});

$('body').on('mouseenter', ".swr-grupo .panel > .room > .msgs > li", function(e) {
    $(".msg span.opts").hide();
    $(this).find(".msg span.opts").css('display', 'inline-flex');
});
$('body').on('mouseleave', ".swr-grupo .panel > .room > .msgs > li", function(e) {
    $(".msg span.opts").hide();
});
$('body').on('mouseenter', ".emojionearea .emojionearea-button", function(e) {
    $(".gr-emoji").css('opacity', 1);
});
$('body').on('mouseleave', ".emojionearea .emojionearea-button", function(e) {
    $(".gr-emoji").css('opacity', 0.3);
});
$("html").on("dragover", function(e) {
    e.preventDefault();
    e.stopPropagation();
});
$("html").on("click", function(e) {
    $(".swr-grupo .dragfile").hide();
    $('.tooltip').remove();
});
$("html").on("drop", function(e) {
    e.preventDefault(); e.stopPropagation();
    $(".swr-grupo .dragfile").hide();
});
$('.swr-grupo .panel').on('dragover', function (e) {
    if ($(".swr-grupo .panel").attr("no") != 0) {
        if (!$(".swr-grupo .panel > .textbox").hasClass('slideOutDown')) {
            e.stopPropagation();
            e.preventDefault();
            $(".swr-grupo .dragfile").hide();
            $(".swr-grupo .panel .dragfile").show();
        }
    }
});

$('.swr-grupo .panel').on('drop', function (e) {
    if ($(".swr-grupo .panel").attr("no") != 0) {
        if (!$(".swr-grupo .panel > .textbox").hasClass('slideOutDown')) {
            e.stopPropagation();
            e.preventDefault();
            $(".swr-grupo .panel .dragfile").hide();
            var file = e.originalEvent.dataTransfer.files;
            $('.swr-grupo .atchmsg .attachfile').prop('files', file);
            $('.swr-grupo .atchmsg .attachfile').trigger("change");
        }
    }
});

$('.swr-grupo .lside').on('dragover', function (e) {
    if ($('.uploadable').is(":visible")) {
        e.stopPropagation();
        e.preventDefault();
        $(".swr-grupo .dragfile").hide();
        $(".swr-grupo .lside .dragfile").show();
    }
});

$('.swr-grupo .lside').on('drop', function (e) {
    if ($('.uploadable').is(":visible")) {
        e.stopPropagation();
        e.preventDefault();
        $(".swr-grupo .lside .dragfile").hide();
        var file = e.originalEvent.dataTransfer.files;
        $('.swr-grupo .uploadfiles > input').prop('files', file);
        $('.swr-grupo .uploadfiles > input').trigger("change");
    }
});

$('.swr-grupo .gr-qrcode').on('click', function (e) {
    if ($(this).hasClass("active")) {
        $(this).removeClass("animate__animated animate__bounceIn animate__infinite active");
    } else {
        $('.swr-grupo .panel > .textbox > .box > .icon > .gr-moreico').addClass("qrcode");
        $(this).addClass("animate__animated animate__bounceIn animate__infinite active");
    }
    $('.emojionearea > .emojionearea-editor').focus();
    var el = $(".emojionearea > .emojionearea-editor")[0];
    var pos = $(".emojionearea > .emojionearea-editor").attr("inx");
    SetCaretPosition(el, pos);
});

$(document).on('paste', function(e) {
    grsharescreenshot(e);
});
function grsharescreenshot(e) {
    var sharescreenshot = $.trim($(".dumb .gdefaults > .sharescreenshot").text());
    if (sharescreenshot == 1) {
        if (!$('.swr-grupo .panel > .textbox').hasClass('slideOutDown')) {
            var items = (e.clipboardData || e.originalEvent.clipboardData).items;
            var blob = null;
            for (var i = 0; i < items.length; i++) {
                if (items[i].type.indexOf("image") === 0) {
                    blob = items[i].getAsFile();
                }
            }
            if (blob !== null) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    var data = {
                        act: 1,
                        do: 'files',
                        type: 'pastescreen',
                        gid: $('.swr-grupo .panel').attr('no'),
                        ldt: $('.swr-grupo .panel').attr('ldt'),
                        from: grlastid(),
                        shot: event.target.result,
                    };
                    $('.pastescreen').attr('type', 'json');
                    $('.pastescreen').attr('load', $(".gphrases > .uploading").text());
                    $('.pastescreen').attr('lsub', $(".gphrases > .pleasewait").text());
                    var s = 'loadmsg(data);';
                    var f = grlv = 'ajxclrtm["grlivesetin"]=setTimeout(function() {gr_live();}, 2000);';
                    s = s+grlv;
                    ajxx($('.pastescreen'), data, s, e, f, 'screenshotz', 'grlive');
                };
                reader.readAsDataURL(blob);
            }
        }
    }
}


$('body').on('click', '.swr-grupo .panel .fullview', function(e) {
    if (actcustomscroll == 1) {
        $(".swr-grupo .panel > .room > .msgs").getNiceScroll().remove();
        $(".swr-grupo .panel > .room > .msgs").css("overflow", "hidden");
    }
    if ($('.swr-grupo .aside').hasClass("fold")) {
        $('.swr-grupo .aside').removeClass("fold");
        $('.swr-grupo .panel').removeClass("full");
    } else {
        $('.swr-grupo .aside').addClass("fold");
        $('.swr-grupo .panel').addClass("full");
    }
    setTimeout(function() {
        grscroll($(".swr-grupo .panel > .room > .msgs"), 'scroll');
    }, 510);
});


function grscroll($el, $do, $xtra) {
    if (actcustomscroll == 1) {
        if ($do == undefined) {
            $do = 'scroll';
        }
        if ($xtra == undefined) {
            $xtra = '#d4d4d4';
            if ($("body").hasClass('dark')) {
                $xtra = '#565454';
            }
        }
        if ($do == 'scroll') {
            $($el).niceScroll({
                cursorwidth: "8px",
                cursoropacitymin: 0,
                cursoropacitymax: 0.7,
                cursorcolor: $xtra,
                cursorborder: 'none',
                cursorborderradius: 4,
                autohidemode: false,
                smoothscroll: true,
                horizrailenabled: false
            });
        } else if ($do == 'remove') {
            $($el).getNiceScroll().remove();
        } else if ($do == 'hide') {
            $($el).getNiceScroll().hide();
        } else if ($do == 'resize') {
            if ($xtra == '#d4d4d4') {
                $xtra = 200;
            }
            $($el).getNiceScroll().hide();
            setTimeout(function() {
                $($el).getNiceScroll().onResize();
                $($el).getNiceScroll().show();
            }, $xtra);
        }
    }
}

$('body').on('click', '.swr-grupo .loadgroup,.dumb .loadgroup', function(e) {
    $('.swr-grupo .aside > .content > .list > li').removeClass("active");
    $(this).addClass("active");
    $(this).find('div > .center > u').html('');
    loadgroup($(this).attr('no'), $(this));
});
$('body').on('click', '.swr-grupo .aside > .content > .list > li', function(e) {
    if ($(window).width() <= 991 && !$(this).hasClass('loadgroup')) {
        $('[data-toggle="tooltip"]').tooltip('hide');
        $('.swr-grupo .opt > i').show();
        $(this).find('.opt > i').hide();
        $('.swr-grupo .aside .opt > ul').hide();
        $(this).find('.opt > ul').css('display', 'table-cell');
    }
    $(this).parents('.aside').find('.tabs > ul > li.active > i').html('');
});
$('body').on('click', '.swr-grupo .panel > .room > .msgs > li', function(e) {
    if ($(window).width() <= 767.98) {
        $('[data-toggle="tooltip"]').tooltip('hide');
        $('.swr-grupo .msgopt > ul').hide();
        $(this).find('.msgopt > ul').css('display', 'inline');
    }
});

$('body').on('click', '.swr-grupo .mbopen', function(e) {
    if ($(window).width() <= 767.98 && !$(this).hasClass('loadgroup')) {
        $('[data-toggle="tooltip"]').tooltip('hide');
        if ($(this).attr('data-block') == 'panel' && $('.swr-grupo .panel').attr('no') != 0) {
            $('.swr-grupo .lside').addClass('bwmob');
            $('.swr-grupo .panel').removeClass('nomob');
            $('.swr-grupo .panel').addClass('abmob');
            $(".swr-grupo .panel").removeClass("animate__animated "+animclose+" animate__fast");
            $(".swr-grupo .panel").addClass("animate__animated "+animopen+" animate__fast");
        } else if ($(this).attr('data-block') == 'rside') {
            $('.swr-grupo .lside .opt > ul').hide();
            $('.swr-grupo .rside > .top > .left > .icon').attr('data-block', $(this).attr('data-block'));
            $('.swr-grupo .lside,.swr-grupo .panel').addClass('bwmob');
            $('.grtab').addClass('d-none');
            $('.swr-grupo .rside').removeClass('nomob');
            $('.swr-grupo .rside').addClass('abmob');
            $(".swr-grupo .rside").removeClass("animate__animated "+animclose+" animate__fast");
            $(".swr-grupo .rside").addClass("animate__animated "+animopen+" animate__fast");
        }
    }
    if ($(this).attr('data-block') == 'rside') {
        if ($(window).width() >= 768 && $(window).width() <= 991) {
            grtabfold();
        }
    }
});

$('body').on('click', '.swr-grupo .standby', function() {
    $.when($('.swr-grupo > .window').fadeOut())
    .then(function() {
        $('.grupo-standby').fadeIn();
    });

});
$('body').on('click', '.grupo-standby', function() {
    $.when($('.grupo-standby').fadeOut())
    .then(function() {
        $('.swr-grupo > .window').fadeIn();
    });

});
$('body').on('click', '.swr-grupo .goback', function(e) {
    if ($(window).width() <= 767.98) {
        $('[data-toggle="tooltip"]').tooltip('hide');
        $('.swr-grupo .lside .opt > ul').hide();

        var block = $(this).attr('data-block');
        if (block == 'alerts' || block == 'rside') {
            $('.swr-grupo .panel').removeClass('bwmob');
            $(".swr-grupo .rside").removeClass("animate__animated "+animopen+" animate__fast");
            $(".swr-grupo .rside").addClass("animate__animated "+animclose+" animate__fast");
            setTimeout(function() {
                $('.swr-grupo .rside').addClass('nomob');
                $('.swr-grupo .rside').removeClass('abmob');
                $('.swr-grupo .lside').removeClass('bwmob');
            }, 600);
        } else if (block == 'crew' || block == 'palert') {
            $(".swr-grupo .panel").removeClass("animate__animated "+animopen+" animate__fast");
            $(".swr-grupo .rside").removeClass(""+animopen+"");
            $(".swr-grupo .rside").addClass("animate__animated "+animclose+" animate__fast");
            setTimeout(function() {
                $('.swr-grupo .rside').addClass('nomob');
                $('.swr-grupo .rside').removeClass('abmob');
                $('.swr-grupo .panel').removeClass('bwmob');
                $('.swr-grupo .panel').addClass('abmob');
            }, 500);
        } else {
            $(".swr-grupo .panel > .textbox").addClass('disabled');
            $(".swr-grupo .panel").removeClass("animate__animated "+animopen+" animate__fast");
            $(".swr-grupo .panel").addClass("animate__animated "+animclose+" animate__fast");
            setTimeout(function() {
                $('.swr-grupo .panel').addClass('nomob');
                $('.swr-grupo .panel').removeClass('abmob');
                $('.swr-grupo .lside').removeClass('bwmob');
            }, 500);
            if (block == 'files') {
                $('.swr-grupo .rside').removeClass('abmob animate__animated '+animclose+' animate__fast');
                $('.swr-grupo .rside').removeClass(animopen);
                $('.swr-grupo .rside').addClass('nomob');
                $('.swr-grupo .lside > .tabs > ul > li[act=files]').trigger('click');
            }
        }
    }
});


$(".swr-grupo .panel > .textbox > .box > textarea").blur(function() {
    if ($(window).width() <= 767.98) {
        $('[data-toggle="tooltip"]').tooltip('hide');
        setTimeout(function() {
            $('.swr-grupo .panel > .room > .msgsdd').animate({
                height: $('.swr-grupo .panel > .room').height()-160
            }, 200);
        }, 200);
    }
});
$('body').on('click', '.swr-grupo .goright', function(e) {
    $('.swr-grupo .lside .opt > ul').hide();
    $('.swr-grupo .rside > .top > .left > .icon').attr('data-block', $(this).attr('data-block'));
    $('.swr-grupo .lside,.swr-grupo .panel').addClass('bwmob');
    $(".swr-grupo .rside").removeClass("animate__animated "+animclose+" animate__fast");
    if ($(this).attr('data-block') == 'crew') {
        $('.swr-grupo .lside,.swr-grupo .panel').removeClass('abmob');
        $('.swr-grupo .rside > .tabs > ul > li').eq(1).trigger('click');
        $('.grtab').removeClass('d-none');
    } else if ($(this).attr('data-block') == 'palert') {
        $('.grtab').addClass('d-none');
        $(".swr-grupo i.malert").html("");
        $('.swr-grupo .lside,.swr-grupo .panel').removeClass('abmob');
        $('.swr-grupo .rside > .tabs > ul > li').eq(0).trigger('click');
    } else {
        $('.grtab').addClass('d-none');
        $(".swr-grupo i.malert").html("");
        $('.swr-grupo .rside > .tabs > ul > li').eq(0).trigger('click');
    }
    $('.swr-grupo .rside').removeClass('nomob');
    $('.swr-grupo .rside').addClass('abmob');
    $(".swr-grupo .rside").addClass("animate__animated "+animopen+" animate__fast");
});

$('.swr-grupo .aside > .head > .logo').on('click', function() {
    window.location.href = $(".dumb .gdefaults > .baseurl").text()+'chat/';
});

jQuery(document).ready(function($) {

    if (window.history && window.history.pushState) {
        $(window).on('popstate', function() {
            var hashLocation = location.hash;
            var hashSplit = hashLocation.split("#!/");
            var hashName = hashSplit[1];
            if (hashName !== '') {
                var hash = window.location.hash;
                if (hash === '') {
                    if ($('.swr-grupo .panel > .room > .groupreload > i').is(':visible')) {
                        $('.swr-grupo .panel > .room > .groupreload > i').trigger('click');
                        window.history.pushState('forward', null, './#');
                    } else {
                        if ($('.swr-grupo .panel').is(':visible') && !$('.swr-grupo .panel').hasClass('bwmob')) {
                            $('.swr-grupo .panel > .head > .icon.goback').trigger('click');
                        } else if ($('.swr-grupo .rside').is(':visible')) {
                            $('.swr-grupo .rside > .top > .left > .icon.goback').trigger('click');
                        }
                    }
                    if ($('.swr-grupo .lside').is(':visible') && $('.swr-grupo .lside').hasClass('bwmob')) {
                        window.history.pushState('forward', null, './#');
                    }
                }
            }
        });
        window.history.pushState('forward', null, './#');
    }

});
$('body').on('click', '.swr-grupo .aside > .content > .list > li.grrun', function(e) {
    $(this).attr('type', 'html');
    $(this).attr('spin', 'on');
    $(this).attr('load', $(".gphrases > .loading").text());
    $(this).attr('lsub', $(".gphrases > .pleasewait").text());
    var data = {
        act: 1,
        do: $(this).attr('do'),
        type: $(this).attr('act'),
        gid: $('.swr-grupo .panel').attr('no'),
        id: $(this).attr('no'),
        ldt: $('.swr-grupo .panel').attr('ldt'),
    };
    data = $.extend(data, $(this).data());
    var s = f = '';
    s = 'eval(data);';
    var ajv = 'listoptz'+data['type'];
    var grlv = 'ajxclrtm["grlivesetin"]=setTimeout(function() {gr_live();}, 2000);';
    s = s+grlv;
    f = f+grlv;
    ajxx($(this), data, s, e, f, ajv, 'grlive');
});


$('body').on('click', '.swr-grupo .opt > ul > li', function(e) {
    if (!$(this).hasClass('formpop') && !$(this).hasClass('paj') && !$(this).hasClass('vwp')) {
        $(this).attr('type', 'html');
        $(this).attr('spin', 'on');
        $(this).attr('load', $(".gphrases > .loading").text());
        $(this).attr('lsub', $(".gphrases > .pleasewait").text());
        var data = {
            act: 1,
            do: $(this).parent().parent().attr('type'),
            type: $(this).attr('act'),
            gid: $('.swr-grupo .panel').attr('no'),
            id: $(this).parent().parent().attr('no'),
            ldt: $('.swr-grupo .panel').attr('ldt'),
        };
        data = $.extend(data, $(this).data());
        var s = f = '';
        if (data['type'] === 'addgroupuser' && $('.swr-grupo .panel').attr('no') != 0) {
            if ($('.swr-grupo .panel').attr('ldt') != 'user') {
                var usridgen = rand(8);
                $(this).attr('type', 'html');
                $(this).addClass(usridgen);
                $(this).attr('spin', 'off');
                data['do'] = 'group';
                s = '$(".swr-grupo .opt > ul > li.'+usridgen+'").parents(".user").remove();';
            } else {
                window.location.href = $(".dumb .gdefaults > .baseurl").text()+'chat/';
                return false;
            }
        }
        if (data['type'] === 'share') {
            if ($('.swr-grupo .panel').attr('no') != 0) {
                $(this).attr('type', 'json');
                $(this).attr('spin', 'off');
                var senid = rand(8);
                var moset = $(".dumb .gdefaults").find(".sndmsgalgn").text();
                $(".swr-grupo .panel > .room > .msgs").animate({
                    scrollTop: $(".swr-grupo .panel > .room > .msgs").prop("scrollHeight")
                }, 500);
                var senmsg = $(this).parent().parent().attr('no');
                senmsg = senmsg.split("-gr-")[1];
                var msg = '<li class="you animate__animated animate__fadeIn '+senid+' '+moset+'" no="0"> <div><span class="msg"><i>';
                msg = msg+'<span class="block" type="files"><span>'+(escapeHtml(senmsg))+'<span class="animate__animated animate__fadeInUp animate__infinite">';
                msg = msg+'<i class="gi-upload"></i></span></span></span></i>';
                msg = msg+'</span></div></li>';
                $('.swr-grupo .panel > .room > .msgs').append(msg);
                scrollmsgs();
                s = '$(".'+senid+'").remove();if($(".swr-grupo .panel").attr("no")==data[2].gid){loadmsg(data);}';
                f = '$(".swr-grupo .panel > .room > .msgs > li.'+senid+' > div > .msg > i > span.block > span > span > i").removeClass("gi-upload");';
                f = f+'$(".swr-grupo .panel > .room > .msgs > li.'+senid+' > div > .msg > i > span.block > span > span").removeClass("animate__animated");';
                f = f+'$(".swr-grupo .panel > .room > .msgs > li.'+senid+' > div > .msg > i > span.block > span > span > i").addClass("gi-minus-circled-1");';
                f = f+'setTimeout(function() {$(".'+senid+'").remove();}, 2000);';
            } else {
                s = 'eval(data);';
            }
        }
        if ($(this).hasClass('deval')) {
            s = 'eval(data);';
        }
        if (data['do'] === 'group' && data['type'] === 'msgs') {
            $(this).attr('type', 'json');
            s = 'loadmsg(data,1);';
        }
        data = $.extend(data, $(this).data());
        var ajv = 'listoptz'+data['type'];
        var grlv = 'ajxclrtm["grlivesetin"]=setTimeout(function() {gr_live();}, 2000);';
        s = s+grlv;
        f = f+grlv;
        ajxx($(this), data, s, e, f, ajv, 'grlive');
    }
});

function menuclick(c, i, l, o) {
    if (l == undefined) {
        l = 0;
    }
    if (o == undefined) {
        o = 0;
    }
    if (l != 0) {
        $('.'+c+' > .swr-menu > ul > li[act="'+i+'"]').addClass('loaditem');
        $('.'+c+' > .swr-menu > ul > li[act="'+i+'"]').attr('itemid', l);
        $('.'+c+' > .swr-menu > ul > li[act="'+i+'"]').attr('itemopt', o);
    }
    $('.'+c+' > .swr-menu > ul > li[act="'+i+'"]').trigger('click');
    $('.'+c+' > .swr-menu').hide();
}

function loadgroup($id, e, r) {
    if (r == undefined) {
        r = 0;
    }
    $('.swr-grupo .panel').attr('noscroll', 0);
    $('.swr-grupo .panel').attr('lstseen', 0);
    $('.swr-grupo .panel > .textbox .userid').val(0);
    if (e.attr('ldt') == 'user') {
        $('.swr-grupo .panel > .textbox > .box >.switchuser').hide();
    } else {
        $('.swr-grupo .panel > .textbox > .box >.switchuser').show();
    }
    $('.swr-grupo .panel > .textbox > .box >.switchuser > .usrimg').removeClass('loaded');
    $('.swr-grupo .panel > .textbox > .box >.switchuser > .usrimg > img').attr('src', $('.swr-grupo .rside > .top > .left > span.vwp > img').attr('src'));
    $('.swr-grupo li.xtra[act="lastseen"]').text("");
    $(".swr-grupo .rside > .tabs > ul > li[act='complaints']").attr("unread", 0);
    if ($(window).width() <= 767.98 && r != 1) {
        $('[data-toggle="tooltip"]').tooltip('hide');
        var block = $('.rside > .top > .left > .icon.goback').attr('data-block');
        if (e.parents('.aside').hasClass('rside') && block == 'crew' || e.parents('.aside').hasClass('rside') && block == 'palert') {
            $('.rside > .top > .left > .icon.goback').trigger('click');
        } else {
            $('.swr-grupo .panel').hide();
            $('.swr-grupo .lside').addClass('bwmob');
            if (e.parents('.aside').hasClass('rside')) {
                $('.swr-grupo .rsides').addClass('bwmob');
                $('.swr-grupo .rside').css('zIndex', 20);
                $('.swr-grupo .rside').removeClass('abmob');
                $(".swr-grupo .rside").removeClass("animate__animated "+animopen+" animate__fast");
                setTimeout(function() {
                    $('.swr-grupo .rside').addClass('nomob');
                    $('.swr-grupo .rside').css('zIndex', 1);
                }, 800);
            } else {
                $(".swr-grupo .panel").removeClass("animate__animated "+animclose+" animate__fast");
            }
            setTimeout(function() {
                $('.swr-grupo .panel').removeClass('nomob');
                $('.swr-grupo .panel').addClass('abmob');
                $(".swr-grupo .panel").removeClass("animate__animated "+animclose+" animate__fast");
                $(".swr-grupo .panel").addClass("noanim animate__animated "+animopen+" animate__fast").show();
            }, 200);
        }
    }
    if ($id != $('.swr-grupo .panel').attr('no') || $id == $('.swr-grupo .panel').attr('no') && e.attr('ldt') != $('.swr-grupo .panel').attr('ldt') || r == 1 || e.attr('msgload') != undefined) {
        $(".swr-grupo .panel > .room > .msgs").html('');
        $(".swr-grupo .aside > .content > .list > li > div > .center > u").show();
        if ($(window).width() > 767.98) {
            e.find('div > .center > u').html('').hide();
        }
        $('.swr-grupo .panel > .textbox').addClass('disabled');
        $('.swr-grupo .panel').attr('no', e.attr('no'));
        $(".swr-grupo .panel").attr('deactiv', 1);
        $('.swr-grupo .panel').attr('ldt', e.attr('ldt'));
        $('.swr-grupo .panel > .head > .left > span').addClass('vwp');
        $('.swr-grupo .panel > .head > .left > span').attr('no', e.attr('no'));
        if (e.attr('ldt') == 'user') {
            $('.swr-grupo .panel > .head > .left > span').attr('ldt', 'user');
            $('.swr-grupo .panel > .head > .right > .gi-users').hide();
        } else {
            $('.swr-grupo .panel > .head > .left > span').attr('ldt', 'group');
            $('.swr-grupo .panel > .head > .right > .gi-users').show();
        }
        $(".swr-grupo .rside > .tabs > ul > li").eq(2).find('i').html("");
        $(".swr-grupo .rside > .tabs > ul > li").eq(2).attr('comp', 0);
        $("#graudio")[0].pause();
        $("#graudio > source").attr("src", "");
        $(".grupo-preview > div .prclose").trigger("click");
        $(".swr-grupo .msgloader").removeClass('error scrolload').fadeIn(100);
        var ldt = e.attr('ldt');
        var data = {
            act: 1,
            do: "group",
            type: 'msgs',
            id: $id,
            ldt: ldt,
        };
        if (e.attr('msgload') != undefined) {
            data['msid'] = e.attr('msgload');
        }
        ajxvar['loadgroup'] = $.ajax({
            type: 'POST',
            url: '',
            data: data,
            async: true,
            dataType: 'json',
            beforeSend: function() {
                if (ajxvar['loadgroup'] !== null && ajxvar['loadgroup'] !== undefined) {
                    ajxvar['loadgroup'].abort();
                }
                if (ajxvar['grlive'] != undefined) {
                    ajxvar['grlive'].abort();
                }
                if (ajxclrtm['grlive'] != undefined) {
                    clearTimeout(ajxclrtm['grlive']);
                }
                if (ajxvar['searchmsgs'] != undefined) {
                    ajxvar['searchmsgs'].abort();
                }
                if (ajxclrtm['searchmsgs'] != undefined) {
                    clearTimeout(ajxclrtm['searchmsgs']);
                }
            },
            success: function(data) {}
        }).done(function(data) {
            loadgrdata(e, data);
            ajxclrtm["grlivesetin"] = setTimeout(function() {
                gr_live();
            }, 2000);
            $(".swr-grupo .panel").attr('deactiv', 0);
        }) .fail(function(qXHR, textStatus, errorThrown) {
            $(".swr-grupo .msgloader").addClass("error");
            ajxclrtm["grlivesetin"] = setTimeout(function() {
                gr_live();
            }, 2000);
            $(".swr-grupo .panel").attr('deactiv', 0);
        });
        if (e.attr('msgload') == undefined) {
            $('.groupreload').fadeOut();
        }
        $('.swr-grupo .panel > .textbox,.swr-grupo .groupnav').removeClass('d-none');
        $('.grtab').addClass('d-none');
        if ($('.swr-grupo .panel').attr('ldt') != 'user') {
            $('.grtab').removeClass('d-none');
        }
    }
}


function loadgrdata(e, data) {
    loadmsg(data, 1);
    $(".swr-grupo .groupnav > .left > span > img").remove();
    $(".swr-grupo .groupnav > .left > span").prepend("<img class=lazyimg>");
    $(".swr-grupo .groupnav > .left > span > img").attr("data-src", data[0].pnimg);
    document.title = htmlDecode(htmlDecode(data[0].sitetitle));
    $(".swr-grupo .groupnav > .left > span > span").html(htmlDecode(data[0].pntitle)+"<span>"+htmlDecode(data[0].pnsub)+"</span><ul class=typing tcount=0></ul>");
    history.pushState({}, null, data[0].accesslink);
    $(".swr-grupo .panel > .textbox > .logintxt > span.loadlink").attr("link", data[0].signinlink);
    if (data[0].blocked == 1 || data[0].deactiv == 1) {
        $(".swr-grupo .panel > .textbox").addClass("animate__animated animate__slideOutDown");
        $(".swr-grupo .panel > .head > .left > span").removeClass("vwp");
    } else {
        $(".swr-grupo .panel > .textbox").removeClass("animate__animated animate__slideOutDown");
    }
    $(".swr-grupo .panel > .textbox").removeClass("disabled");
    $(".swr-grupo .panel .swr-menu > ul").html("");
    $.each(data[1], function(k, v) {
        $(".swr-grupo .panel .swr-menu > ul").append("<li "+v[1]+">"+htmlDecode(v[0])+"</li>");
    });
    if (e.attr('msgload') == undefined) {
        if ($(window).width() > 991 && $(".swr-grupo .panel").attr("ldt") != "user") {
            $(".swr-grupo .panel > .head > .left > span.vwp").trigger("click");
        }
        if ($(window).width() > 991 && $(".swr-grupo .panel").attr("ldt") == "user") {
            $(".swr-grupo .panel > .head > .left > span.vwp").trigger("click");
        }
    }
    $(".emojionearea > .oldemojionearea-editor").focus();
    if (e.attr('msgload') != undefined) {
        data['msid'] = e.attr('msgload');
        turn_chat();
    }
}

function grtyping(cnt) {
    if (cnt != undefined) {
        $('.swr-grupo .panel > .head > .left > span > span > .typing').html(cnt);
    }
    var elm = $('.swr-grupo .panel > .head > .left > span > span > .typing > li');
    if (elm.length != 0) {
        $('.swr-grupo .panel > .head > .left > span > span > span').hide();
        elm.eq(0).css('display', 'flex');
        if (elm.length > 1) {
            var typstms = setTimeout(function() {
                elm.eq(0).hide().next().css('display', 'flex').end().appendTo(elm.parent());
                grtyping();
            }, 1200);
        }
    } else {
        if (typstms != undefined) {
            clearTimeout(typstms);
        }
        $('.swr-grupo .panel > .head > .left > span > span > span').show();
    }
}
$('body').on('click', '.swr-grupo .panel > .room > .msgs > li > div > .msg > i > i.rply > i', function() {
    var id = $(this).attr('no');
    var el = $(".swr-grupo .panel > .room > .msgs > li[no="+id+"]");
    var scr = $(".swr-grupo .panel > .room > .msgs > li:nth-child("+el.index()+")")[0].offsetTop - $(".swr-grupo .panel > .room > .msgs")[0].offsetTop;
    $(".swr-grupo .panel > .room > .msgs").animate({
        scrollTop: scr
    }, 1000);
    el.addClass('highlight');
    setTimeout(function() {
        el.removeClass('highlight');
    }, 3000);
});

function escapeHtml(str) {
    return $('<grescp>').text(str).html();
}
function htmlencode(str) {
    var div = document.createElement('div');
    var text = document.createTextNode(str);
    div.appendChild(text);
    return div.innerHTML;
}
function scrollmsgs($in, $tm, $mn) {
    if ($mn == undefined) {
        $mn = 1;
    }
    if ($(".swr-grupo .panel").attr('scrolldown') == 'on' || $mn != 0) {
        if ($in == undefined) {
            $in = 500;
        }
        if ($tm == undefined) {
            $tm = 0;
        }
        var elem = $(".swr-grupo .panel > .room > .msgs");
        var scrllmst = setTimeout(function() {
            if (scrllmst != undefined) {
                clearTimeout(scrllmst);
            }
            $(".swr-grupo .panel > .room > .msgs").stop().animate({
                scrollTop: $(".swr-grupo .panel > .room > .msgs").prop("scrollHeight")}, $in);
        }, $tm);
        var nooutscroll = setTimeout(function() {
            if (nooutscroll != undefined) {
                clearTimeout(nooutscroll);
            }
            $(".swr-grupo .panel > .room > .msgs").removeClass('nooutscroll');
        }, 500);
    }
}
$('.swr-grupo .sendbtn').on('click', function(e) {
    grsendmsg($(this), e);
});

function htmlspecialchars(str) {
    var map = {
        "&": "&[gr]amp;",
        "<": "&[gr]lt;",
        ">": "&[gr]gt;",
        "\"": "&[gr]quot;",
        "'": "&[gr]#39;"
    };
    return str.replace(/[&<>"']/g, function(m) {
        return map[m].replace('[gr]', '');
    });
}

function grsendmsg(el, e, gif, gfm, gfw, gfh, mtype) {
    if (gif == undefined) {
        gif = 0;
    }
    if (gfm == undefined) {
        gfm = 0;
    }
    if (gfw == undefined) {
        gfw = 0;
    }
    if (gfh == undefined) {
        gfh = 0;
    }
    if (mtype == undefined) {
        mtype = 0;
    }
    var msgd = $(".swr-grupo .panel > .textbox > .box > textarea").data("emojioneArea").getText();
    var lmt = parseInt($(".dumb .gdefaults").find(".minmsglen").text());
    if (gif != 0 && gfm != 0 && gfw != 0 && gfh != 0 && !el.hasClass('na') || $.trim(msgd) != '' && !el.hasClass('na')) {
        if (lmt !== "" && gif == 0 && gfm == 0) {
            if (msgd.length < lmt) {
                say($(".gphrases > .minlenreq").text());
                return false;
            }
        }
        el.attr('spin', 'off');
        el.attr('type', 'json');
        el.attr('turn', 'on');
        var moset = $(".dumb .gdefaults").find(".sndmsgalgn").text();
        var qrcode = 0;
        if ($(".gr-qrcode").hasClass('active')) {
            qrcode = 1;
        }
        if ($('.swr-grupo .panel > .textbox .userid').val() != 0 && $('.swr-grupo .panel > .textbox .userid').val() != $(".swr-grupo .rside > .top > .left > span.vwp").attr("no")) {
            moset = $(".dumb .gdefaults").find(".rcvmsgalgn").text();
        }
        moset = moset+' '+$(".dumb .gdefaults > .msgstyle").text();
        var senid = rand(8);
        var senmsg = $(".swr-grupo .panel > .textbox > .box > textarea").data("emojioneArea").getText();
        if (gif != 0 && gfm != 0) {
            prmsg = '<span class="preview image lrmbg"><span><img class="lazyimg tenor" gif="'+gfm+'" data-src="'+gif+'"/></span></span>';
            gif = senmsg = gif+"|"+gfw+"|"+gfh;
        } else {
            var nsenmsg = htmlspecialchars(senmsg);
            var prmsg = shwrdmre(nsenmsg, 0, 0, 1);
        }
        var msg = '<li class="you animate__animated animate__fadeIn '+senid+' '+moset+' '+mtype+'" no="0"> <div>';
        msg = msg+'<span class="msg"><i>'+prmsg;
        var sending = ' <i class="info">'+$(".gphrases > .sending").text()+'<i class="tick recieved sending"><i></i><i></i></i></i>';
        if ($(".dumb .gdefaults > .msgstyle").text() != 'style2') {
            msg = msg+sending;
        }
        msg = msg+'</i></span>';
        if ($(".dumb .gdefaults > .msgstyle").text() == 'style2') {
            msg = msg+sending;
        }
        msg = msg+'</div> </li>';
        scrollmsgs(200, 0);
        $('.swr-grupo .panel > .room > .msgs').append(msg);
        $(".gr-qrcode").removeClass("animate__animated animate__bounceIn animate__infinite active");
        $(".swr-grupo .panel > .room > .msgs").animate({
            scrollTop: $(".swr-grupo .panel > .room > .msgs").prop("scrollHeight")
        }, 500);
        var data = {
            act: 1,
            do: "group",
            type: 'sendmsg',
            gif: gif,
            gfm: gfm,
            mtype: mtype,
            msg: senmsg,
            qrcode: qrcode,
            rid: $('.swr-grupo .panel > .textbox .replyid').val(),
            userid: $('.swr-grupo .panel > .textbox .userid').val(),
            id: $('.swr-grupo .panel').attr('no'),
            ldt: $('.swr-grupo .panel').attr('ldt'),
            from: grlastid(),
        };
        if (gif == 0 && gfm == 0) {
            $(".swr-grupo .panel > .textbox > .box > textarea").data("emojioneArea").setText('');
        }
        $(".swr-grupo .panel > .textbox .replyid").val(0);
        $(".swr-grupo .panel > .room > .msgs > li").removeClass("selected");
        var s = '$(".'+senid+'").remove();';
        s = s+'if($(".swr-grupo .panel").attr("no")==data[0].gid){loadmsg(data);}';
        var f = '$(".swr-grupo .panel > .room > .msgs > li.'+senid+'> div i.info").text("'+$(".gphrases > .failed").text()+'");';
        f = f+'setTimeout(function() {$(".'+senid+'").remove();}, 2000);';
        var grlv = 'ajxclrtm["grlivesetin"]=setTimeout(function() {gr_live();}, 2000);';
        s = s+grlv;
        f = f+grlv;
        ajxx(el, data, s, e, f, 'grsendmsg', 'grlive');
    }
    $('.swr-grupo .panel > .textbox > .box > .icon > .gr-moreico').removeClass('qrcode');
    $(".swr-grupo .panel > .textbox > .box > textarea").data("emojioneArea").hidePicker();
    $('.emojionearea > .emojionearea-editor').trigger('click');
    $('.emojionearea > .emojionearea-editor').trigger('focus');
}
function grmsgexist(mid) {
    if (!$('.swr-grupo .panel > .room > .msgs > li[no='+mid+']').length) {
        return false;
    } else {
        return true;
    }
}
function loadmsg(d, n, fi) {
    var grkey = 'grp'+$(".swr-grupo .panel").attr("ldt")+d[0].gid;
    if (n == undefined) {
        n = 0;
    }
    if (fi == undefined) {
        fi = 0;
    }
    $xptxt = 0;
    if (n == 1) {
        $(".swr-grupo .panel > .room > .msgs").addClass('nooutscroll').html('');
    }
    var mntz = oldmsg = '';
    $.each(d, function(k, v) {
        if (k == 0 && d[0].messageflood == 1) {
            say($(".gphrases > .sendinglimitreached").text()+d[0].floodwait);
        } else if (k == 0 && d[0].nomem == 'refresh') {
            window.location.href = $(".dumb .gdefaults > .baseurl").text()+'chat/';
        } else if (d[k] == null) {} else if (d[k].id === undefined) {} else if (k !== 0 && k !== 1) {
            var m = d[k];
            if (m.id === undefined) {
                m = d;
            }
            var trn = 0;
            if (grmsgexist(m.id) == false) {
                trn = 1;
            }
            if (trn == 1) {
                var vtid = rand(6);
                if (m.type == 'like') {
                    var lkcnt = "";
                    if (m.total != 0) {
                        lkcnt = "<i>"+nformat(m.total)+"</i>";
                    }
                    $(".swr-grupo .panel > .room > .msgs > li[no="+m.liked+"]").find(".lcount").html(lkcnt);
                    var msg = '<li class="like" no="'+m.id+'"> </li>';
                    $('.swr-grupo .panel > .room > .msgs').append(msg);
                } else if (m.type == 'dummy') {
                    var msg = '<li class="logs you dummy" no="'+m.id+'"> </li>';
                    $('.swr-grupo .panel > .room > .msgs').append(msg);
                } else if (m.type == 'logs') {
                    if (m.action == 'delete') {
                        $(".swr-grupo .panel > .room > .msgs > li[no="+m.rel+"]").remove();
                    } else if (m.action == 'deleteall') {
                        $(".swr-grupo .panel > .room > .msgs > li").remove();
                    }
                    var msg = '<li class="logs you" no="'+m.id+'"> </li>';
                    $('.swr-grupo .panel > .room > .msgs').append(msg);
                } else {
                    var mntz = '';
                    var moset = $(".dumb .gdefaults").find(".rcvmsgalgn").text();
                    var bclass = m.send+' '+moset;
                    if (m.send == 'you') {
                        moset = $(".dumb .gdefaults").find(".sndmsgalgn").text();
                        bclass = m.send+' '+moset+' msgschk';
                    }
                    if (m.type == 'system') {
                        bclass = m.send;
                        if (m.domsg == 'created_group') {
                            bclass = m.send+' createdgroup';
                        }
                        if (n == 2) {
                            if (m.domsg == 'created_group') {
                                $('.swr-grupo .panel').attr('noscroll', 1);
                            }
                        } else if (n == 0) {
                            if (m.domsg == 'renamed_group') {
                                $(".swr-grupo .groupnav > .left > span > span").html(htmlDecode(d[0].pntitle)+"<span>"+d[0].pnsub+"</span>");
                            } else if (m.domsg == 'changed_group_icon') {
                                $(".swr-grupo .groupnav > .left > span > img").remove();
                                $(".swr-grupo .groupnav > .left > span").prepend("<img class=lazyimg>");
                                $(".swr-grupo .groupnav > .left > span > img").attr("data-src", d[0].pnimg);
                            } else if (m.domsg == 'changed_unleavable_group' || m.domsg == 'changed_leavable_group') {
                                $(".swr-grupo .panel > .room > .groupreload > i").trigger("click");
                            } else if (m.domsg == 'is_now_moderator' || m.domsg == 'is_now_admin' || m.domsg == 'is_no_longer_admin_moderator') {
                                if (m.userid == $(".swr-grupo .rside > .top > .left > span.vwp").attr("no")) {
                                    window.location.href = $(".dumb .gdefaults > .baseurl").text()+'chat/';
                                }
                            } else if (m.domsg == 'changed_message_settings') {
                                if (d[0].deactiv == 1) {
                                    $(".swr-grupo .panel > .textbox").addClass("animate__animated animate__slideOutDown");
                                } else {
                                    $(".swr-grupo .panel > .textbox").removeClass("animate__animated animate__slideOutDown");
                                }
                            }
                        }
                    }
                    if (m.type == 'qrcode') {
                        bclass = bclass+' qrcode';
                    }
                    if (m.type == 'stickers') {
                        bclass = bclass+' sticker';
                    }
                    var msg = '';
                    if (m.send != 'system' && m.userimg != '0') {
                        bclass = bclass+' userimg';
                    }
                    bclass = bclass+' '+$(".dumb .gdefaults > .msgstyle").text();
                    if (m.type == 'msg') {
                        if (m.lntitle != 0) {
                            var video = urlParser.parse(m.lnurl);
                            var lnxtrz = 'link="'+m.lnurl+'"';
                            var lnclass = ' openlink';
                            if (video && video['provider'] === 'youtube' || video && video['provider'] === 'dailymotion' || video && video['provider'] === 'vimeo') {
                                lnclass = lnclass+ ' embedvideo';
                                lnxtrz = lnxtrz+' vid="'+video['id']+'" provider="'+video['provider']+'"';
                            } else if (m.lntype === 'video/mp4' || m.lntype === 'video/mpeg' || m.lntype === 'video/ogg' || m.lntype === 'video/webm') {
                                lnclass = lnclass+ ' playvideo';
                                lnxtrz = lnxtrz+' mime="'+m.lntype+'"';
                                m.lnimg = $(".dumb .gdefaults > .baseurl").text()+'gem/ore/grupo/icons/playbutton.png';
                            }
                            msg = msg + '<li class="'+bclass+' animate__animated animate__fadeIn" no="'+m.id+'"> <div>';
                            msg = msg + '<span class="msg"><span class="urlpreview'+lnclass+'" '+lnxtrz+'>';
                            if (m.lnimg != 'grnone') {
                                msg = msg + '<span class="limg lrmbg"><img class="lazyimg" data-src="'+m.lnimg+'"></span>';
                            }
                            msg = msg + '<span class="lmeta"><b>'+m.lntitle+'</b>';
                            if ($.trim(m.lndesc) != '') {
                                msg = msg + '<span>'+m.lndesc+'</span>';
                            }
                            msg = msg + '</span></span></span></div> </li>';
                        }
                        bclass = bclass+' emjbuild';
                    }
                    msg = msg + '<li class="'+bclass+' animate__animated animate__fadeIn" no="'+m.id+'"> <div>';
                    if (m.name != undefined) {
                        if (m.status == 'deactivated') {
                            mntz = '<i class="usrname">'+m.name+'</i>';
                        } else {
                            mntz = '<i class="usrname vwp" style="color:'+m.ncolor+'" no="'+m.userid+'">'+m.name+'</i>';
                        }
                    }
                    var moptz = '<span class="opts"><span>';
                    if (m.optb != 0) {
                        moptz += '<i '+m.optb+'></i>';
                    } else if (m.opta != 0) {
                        moptz += '<i '+m.opta+'></i>';
                    }
                    if (m.optf != 0) {
                        moptz += '<i '+m.optf+'></i>';
                    }
                    if (m.optc != 0) {
                        moptz += '<i '+m.optc+'></i>';
                    }
                    if (m.opte != 0) {
                        moptz += '<i '+m.opte+'></i>';
                    }
                    if (d[0]['likemsgs'] == 'enabled' && m.send != 'you') {
                        moptz += '<i class="gr-like '+m.lvc+'"></i>';
                    }
                    moptz += '</span></span><span class="lcount">';
                    if (m.lvn != 0 && $('.swr-grupo .panel').attr('ldt') == 'group' && d[0]['viewlike'] == 1) {
                        moptz += '<i>'+nformat(m.lvn)+'</i>';
                    }
                    moptz += '</span>';

                    var userimg = '<span class="userimg vwp" no="'+m.userid+'"><img class="lazyimg" data-src="'+m.userimg+'"/></span>';

                    msg = msg+'<span class="msg">';
                    if (moset != 'right' && m.send != 'system' && m.userimg != '0') {
                        msg = msg+userimg;
                    }
                    if (moset === 'right' && m.send != 'system') {
                        msg = msg+moptz;
                    }
                    msg = msg+'<i>';
                    if (m.type === 'msg' || m.type === 'system' || m.type === 'qrcode') {
                        if (m.rid != 0) {
                            msg = msg+'<i class="rply"><i no="'+m.rid+'"><i>'+m.rusr+'</i>'+shwrdmre(m.reply, 30, 1)+'</i></i>';
                        }
                        if (m.type === 'qrcode') {
                            msg = msg+mntz+'<span class="codeqr"><span>'+m.msg+'</span></span>';
                        } else {
                            if (m.type === 'system') {
                                msg = msg+mntz+' '+htmlDecode(m.msg);
                            } else {
                                msg = msg+mntz+' '+shwrdmre(m.msg);
                            }
                        }
                    } else if (m.type === 'gifs' || m.type === 'stickers') {
                        msg = msg+mntz;
                        msg = msg+'<span class="preview image '+m.type+' lrmbg"><span><img class="lazyimg tenor" gif="'+m.xtra+'" data-src="'+m.msg+'" style="height:'+m.fheight+'px;"/></span></span>';
                    } else if (m.type === 'audio') {
                        msg = msg+mntz;
                        $ext = m.filext;
                        $xptxt = m.fetxtb;
                        if ($ext.toLowerCase().indexOf('audio/') != -1) {
                            msg = msg+'<span class="audioplay" mime="'+$ext+'" play="'+$(".dumb .gdefaults > .baseurl").text()+'gem/ore/grupo/audiomsgs/'+m.msg+'">';
                            msg = msg+'<span class="play"><i></i></span>';
                            msg = msg+'<span class="seek">';
                            msg = msg+'<input id="seekslider" type="range" min="0" max="1" value="0" step=".001">';
                            msg = msg+'<i class="bar"><i></i></i><i class="duration"><i>00:00</i><i class="tot">00:00</i></i></span>';
                            msg = msg+'<span class="icon gi-mic"></span></span>';
                        } else {
                            msg = msg+'<span class="block gi-block" type="files" act="download" no="'+m.msg+'">';
                            msg = msg+' <span><i class="noclick"></i> '+m.sfile+'</span></span>';
                        }
                    } else if (m.type === 'file') {
                        msg = msg+mntz;
                        $ext = m.filext;
                        $xptxt = m.fetxtb;
                        if ($ext === 'image/jpeg' || $ext === 'image/png' || $ext === 'image/gif' || $ext === 'image/bmp' || $ext === 'image/x-ms-bmp') {
                            var nominw = '';
                            if (m.fwidth < 50 && m.fheight < 30) {
                                nominw = 'nominwidth';
                            }
                            msg = msg+'<span class="preview image lrmbg '+nominw+'" style="width:'+m.fwidth+'px;height:'+m.fheight+'px;"><span><img class="lazyimg" data-src="'+$(".dumb .gdefaults > .baseurl").text()+'gem/ore/grupo/files/preview/'+m.msg+'"/></span></span>';
                        } else if ($ext === 'video/mp4' || $ext === 'video/mpeg' || $ext === 'video/ogg' || $ext === 'video/webm') {
                            msg = msg+'<span class="preview video lrmbg"><span mime="'+$ext+'" loadvideo="'+$(".dumb .gdefaults > .baseurl").text()+'gem/ore/grupo/files/dumb/'+m.msg+'">';
                            msg = msg+'</span></span>';
                        } else if ($ext === 'audio/mpeg' || $ext === 'audio/ogg' || $ext === 'audio/wav' || $ext === 'audio/x-wav') {
                            msg = msg+'<span class="audioplay" mime="'+$ext+'" play="'+$(".dumb .gdefaults > .baseurl").text()+'gem/ore/grupo/files/dumb/'+m.msg+'">';
                            msg = msg+'<span class="play"><i></i></span>';
                            msg = msg+'<span class="seek">';
                            msg = msg+'<input id="seekslider" type="range" min="0" max="1" value="0" step=".001">';
                            msg = msg+'<i class="bar"><i></i></i><i class="duration"><i>00:00</i><i class="tot">00:00</i></i></span>';
                            msg = msg+'<span class="icon gi-note-beamed"></span></span>';
                        } else {
                            var dwnb = 'gi-attach';
                            var dwnc = 'dwnldfile';
                            if ($ext === 'expired') {
                                dwnb = 'gi-block';
                                dwnc = 'noclick';
                            }
                            msg = msg+'<span class="block '+dwnc+'" type="files" act="download" no="'+m.msg+'">';
                            msg = msg+'<span><i class="'+dwnb+'"></i> '+m.sfile+'</span></span>';
                        }

                    }
                    var timestamp = ' <i class="info" title="'+m.date+'" data-toggle="tooltip">'+htmlDecode(m.time);
                    if (m.send == 'you') {
                        timestamp = timestamp+'<i class="tick recieved '+m.mseen+'"><i></i><i></i></i>';
                    }
                    timestamp = timestamp+'</i>';
                    if (m.send == 'system' || $(".dumb .gdefaults > .msgstyle").text() != 'style2') {
                        msg = msg+timestamp;
                    }
                    msg = msg+'</i>';
                    if (moset == 'right' && m.send != 'system' && m.userimg != '0') {
                        msg = msg+userimg;
                    }
                    if (moset != 'right' && m.send != 'system') {
                        msg = msg+moptz;
                    }
                    msg = msg+'</span>';
                    if (m.send != 'system' && $(".dumb .gdefaults > .msgstyle").text() == 'style2') {
                        msg = msg+timestamp;
                    }
                    msg = msg+'</div> </li>';
                    if (n == 2) {
                        oldmsg = oldmsg+msg;
                    } else {
                        $('.swr-grupo .panel > .room > .msgs').append(msg);
                    }
                    if (d.length === undefined) {
                        return false;
                    }
                }
            }
        }
    });
    if (n == 2) {
        $(".swr-grupo .panel > .room > .msgs").prepend(oldmsg);
        $('.swr-grupo .panel > .room > .msgs').animate({
            scrollTop: $(".swr-grupo .panel > .room > .msgs > li[no="+fi+"]").offset().top-180
        }, 5);
    } else {
        if (!$(".swr-grupo .panel > .room > .msgs").hasClass('noscroll')) {
            $intr = 500;
            $tmr = 100;
            if (n == 1) {
                $intr = 200;
                $tmr = 300;
            }
            scrollmsgs($intr, $tmr, n);
        }
        $(".swr-grupo .msgloader").fadeOut(100);
    }
    if (!$('.swr-grupo .panel > .room > .msgs > li').last().hasClass('you') && !$('.swr-grupo .panel > .room > .msgs > li').last().hasClass('like')) {
        if (!$('.swr-grupo .panel > .textbox').hasClass('disabled')) {
            if (n !== 1 && n !== 2) {
                grnewalert(1);
                $("#gralert")[0].play();
            }
        }
    }
    $('[data-toggle="tooltip"]').tooltip();
    qrload();
    gremojibuild();
    grprofanityFilter();
    grlzyload = setTimeout(function() {
        if (grlzyload != undefined) {
            clearTimeout(grlzyload);
        }
        lazyload();
    }, 300);
}

function grprofanityFilter() {
    $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i').profanityFilter({
        replaceWith: '*',
        externalSwears: $(".dumb .gdefaults > .baseurl").text()+'gem/ore/grupo/cache/filterwords.json',
    });
}

function grnewalert(n) {
    if (n != undefined) {
        $(".dumb .newmsgalert").attr('alert', 'on');
        if (alertitle != undefined) {
            clearTimeout(alertitle);
        }
    }
    var oldTitle = $(".dumb .webtitle").text();
    var newTitle = $(".dumb .newmsgalert").text();
    alertitle = setTimeout(function() {
        if ($(".dumb .newmsgalert").attr('titles') == 1) {
            document.title = newTitle;
            $(".dumb .newmsgalert").attr('titles', 0);
        } else {
            $(".dumb .newmsgalert").attr('titles', 1);
            document.title = oldTitle;
        }
        if ($(".dumb .newmsgalert").attr('alert') == 'on') {
            grnewalert();
        }
    }, 700);
}
function qrload() {
    $(".swr-grupo .panel > .room > .msgs > li.qrcode").each(function() {
        if (!$(this).hasClass('qrdone')) {
            var txt = $(this).find('.codeqr > span');
            $(this).find('.codeqr').qrcode({
                width: 100, height: 100, size: 100, text: txt.text()});
            txt.remove();
            $(this).addClass('qrdone');
        }
    });
}
function autotimez($elem, $txt) {
    if ($txt == undefined) {
        $txt = '0m 0s';
    }
    if ($elem == 'run') {
        $('.autotimering').each(function() {
            $(this).attr("timer", $(this).find("input").val());
            $(this).find("input").val("");
            autotimez($(this));
        });
    } else {
        if ($elem.attr("timer") != 0 && $elem.is(':visible')) {
            var countDownDate = new Date($elem.attr("timer")).getTime();
            var now = new Date().getTime();
            var distance = countDownDate - now;
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            var outp = '';
            if (days != 0) {
                outp = outp+days+"d ";
            }
            if (hours != 0) {
                outp = outp+hours+"h ";
            }
            if (minutes != 0) {
                outp = outp+minutes+"m ";
            }
            if (seconds != 0) {
                outp = outp+seconds+"s";
            }
            if (distance < 0) {
                $elem.find("input").val($txt);
                $elem.attr("timer", 0);
            } else {
                $elem.find("input").val(outp);
            }
            setTimeout(function() {
                if (distance > 0) {
                    autotimez($elem, $txt);
                }
            }, 1000);
        }
    }
}

$(document).on('DOMNodeInserted', '.swr-grupo', function() {
    lazyload();
});
function lazyload() {
    $(".lazyimg").Lazy({
        effect: "fadeIn",
        effectTime: 1000,
        bind: "event",
        afterLoad: function(element) {
            element.parents('.lrmbg').addClass('imgld');
        },
        onError: function(element) {
            element.parents('.lrmbg').addClass('errorld');
        },
        onFinishedAll: function(element) {}
    });
}
$('body').on('click', '.gr-prvlink > div > i.gi-cancel', function(e) {
    if (webthumbnail != null) {
        webthumbnail.abort();
    }
    $('.gr-prvlink').hide();
    $('.gr-prvlink > div > img').removeAttr("src");
});

$('body').on('click', '.gr-prvlink > div > i.submt', function(e) {
    if ($(this).hasClass("vidprev")) {
        var video = [];
        video['provider'] = $(this).attr('provider');
        video['id'] = $(this).attr('vid');
        var extra = 'allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen';
        if (video['provider'] === 'youtube') {
            link = 'https://www.youtube.com/embed/'+video['id']+'?autoplay=1';
        } else if (video['provider'] === 'dailymotion') {
            link = 'https://www.dailymotion.com/embed/video/'+video['id']+'?autoplay=1';
        } else if (video['provider'] === 'vimeo') {
            extra = 'webkitallowfullscreen mozallowfullscreen allowfullscreen';
            link = 'https://player.vimeo.com/video/'+video['id']+'?autoplay=1';
        }
        grpreview('embed', link, extra);
    } else {
        window.open($(this).attr("link"), '_blank');
    }
});

$('body').on('click', '.swr-grupo .panel > .room > .msgs > li > div > .msg > .urlpreview.openlink', function(e) {
    if ($(this).hasClass("embedvideo")) {
        var video = [];
        video['provider'] = $(this).attr('provider');
        video['id'] = $(this).attr('vid');
        var extra = 'allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen';
        if (video['provider'] === 'youtube') {
            link = 'https://www.youtube.com/embed/'+video['id']+'?autoplay=1';
        } else if (video['provider'] === 'dailymotion') {
            link = 'https://www.dailymotion.com/embed/video/'+video['id']+'?autoplay=1';
        } else if (video['provider'] === 'vimeo') {
            extra = 'webkitallowfullscreen mozallowfullscreen allowfullscreen';
            link = 'https://player.vimeo.com/video/'+video['id']+'?autoplay=1';
        }
        grpreview('embed', link, extra);
    } else if ($(this).hasClass("playvideo")) {
        grpreview('video', $(this).attr('link'), $(this).attr('mime'), $(this));
    } else {
        if ($(e.target).hasClass("lazyimg") && !$(e.target).parent().hasClass('errorld')) {
            grpreview('img', $(e.target).attr('src'));
        } else {
            window.open($(this).attr("link"), '_blank');
        }
    }
});

$('body').on('click', '.grpreview', function(e) {
    grpreview($(this).attr("type"), $(this).attr("load"), $(this).attr("mime"), $(this));
});

$('body').on('click', '.swr-grupo .loadlink', function(e) {
    window.location = $(this).attr("link");
    return false;
});
$('body').on('click', '.swr-grupo .panel > .room > .msgs > li > div > .msg > i > a.oldopenlink', function(e) {
    e.preventDefault();
    $('.gr-prvlink > div > span > span.loading').removeClass('error');
    var ptop = $(this).offset().top+25;
    var pleft = $(this).offset().left;
    if (ptop+144 > $('.swr-grupo .panel').height()) {
        ptop = ptop-130;
    }
    if (pleft+256 > $('.swr-grupo .panel').width()) {
        pleft = pleft-200;
    }
    $('.gr-prvlink > div').css("top", ptop+"px");
    $('.gr-prvlink > div > span > span.loading').css("display", "block");
    $('.gr-prvlink > div').css("left", pleft+"px");
    var key = $(".dumb .gdefaults").find(".pagespeedapi").text();
    if (key != '') {
        key = '&key='+key;
    }
    var url = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=' + $(this).attr("href") + '&screenshot=true'+key;
    var surl = '';
    $('.gr-prvlink > div > i.submt').attr("link", $(this).attr("href"));
    $('.gr-prvlink > div > i.submt').removeAttr('provider');
    $('.gr-prvlink > div > i.submt').removeAttr('vid');
    if ($(this).hasClass("embedvideo")) {
        $('.gr-prvlink > div > i.submt').addClass('vidprev');
        $('.gr-prvlink > div > i.submt').text($(".gphrases > .play").text());
        if (webthumbnail != null) {
            webthumbnail.abort();
        }
        var video = [];
        video['provider'] = $(this).find('i').attr('provider');
        video['id'] = $(this).find('i').attr('vid');
        $('.gr-prvlink > div > i.submt').attr('provider', video['provider']);
        $('.gr-prvlink > div > i.submt').attr('vid', video['id']);
        var link = '';
        if (video['provider'] === 'youtube') {
            link = 'https://img.youtube.com/vi/'+video['id']+'/mqdefault.jpg';
        } else if (video['provider'] === 'dailymotion') {
            link = 'https://www.dailymotion.com/thumbnail/video/'+video['id'];
        } else if (video['provider'] === 'vimeo') {
            $.getJSON('https://www.vimeo.com/api/v2/video/' + video['id'] + '.json?callback=?', {
                format: "json"
            }, function(data) {
                $('.gr-prvlink > div > img').attr('src', data[0].thumbnail_medium);
                $(".gr-prvlink > div > img").on('load', function() {
                    $('.gr-prvlink > div > span > span.loading').fadeOut();
                });
            }).fail(function() {
                $('.gr-prvlink > div > span > span.loading').addClass('error');
            });
        }
        if (video['provider'] != 'vimeo') {
            $('.gr-prvlink > div > img').attr('src', link);
            $(".gr-prvlink > div > img").on('load', function() {
                $('.gr-prvlink > div > span > span.loading').fadeOut();
            }).on('error', function() {
                $('.gr-prvlink > div > span > span.loading').addClass('error');
            });
        }
    } else {
        $('.gr-prvlink > div > i.submt').text($(".gphrases > .visit").text());
        $('.gr-prvlink > div > i.submt').removeClass('vidprev');
        webthumbnail = $.ajax({
            url: url,
            context: this,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                if (webthumbnail != null) {
                    webthumbnail.abort();
                }
            },
            success: function(data) {
                data = data.screenshot.data.replace(/_/g, '/').replace(/-/g, '+');
                $('.gr-prvlink > div > img').attr('src', 'data:image/jpeg;base64,' + data);
                $(".gr-prvlink > div > img").on('load', function() {
                    $('.gr-prvlink > div > span > span.loading').fadeOut();
                });
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('.gr-prvlink > div > span > span.loading').addClass('error');
            }
        });
    }
    $('.gr-prvlink').show();
});

$('body').on('mouseleave', '.swr-grupo .panel > .room > .msgs > li > div > .xmsg > i > a', function(e) {
    $('.gr-prvlink').hide();
    $('.gr-prvlink > div > img').removeAttr("src");
});

$('body').on('click', '.swr-grupo .msgopt > ul > li.run', function(e) {
    $(this).attr('spin', 'off');
    $(this).attr('turn', 'on');
    var data = {
        act: 1,
        do: "group",
        type: $(this).attr('do'),
        mid: $(this).parents('li').attr('no'),
        id: $('.swr-grupo .panel').attr('no'),
        ldt: $('.swr-grupo .panel').attr('ldt'),
    };
    ajxx($(this), data, '', e, '', 'msgoptoz');
});


$('.swr-grupo .uploadfiles > input').change(function(e) {
    if ($(this).prop('files').length > 0) {
        var data = new FormData($(".swr-grupo .uploadfiles")[0]);
        var files = $(".swr-grupo .uploadfiles > input").get(0).files;
        for (var i = 0; i < files.length; i++) {
            data.append("ufiles["+i+"]", files[i]);
        }
        var totalSize = 0;
        $(this).each(function() {
            for (var i = 0; i < this.files.length; i++) {
                totalSize += this.files[i].size;
            }
        });
        totalSize = (totalSize / (1024*1024)).toFixed(2);
        var valid = false;
        if (totalSize < parseInt($(".gdefaults > .maxfilesize").text())) {
            valid = true;
        }
        if (!valid) {
            say($(".gphrases > .maxfilesizelimit").text());
            return;
        }
        $('.gruploader').fadeIn();
        gruploader = $.ajax({
            url: '',
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            async: true,
            data: data,
            type: 'post',
            beforeSend: function() {
                if (gruploader != undefined) {
                    gruploader.abort();
                }
            },
            success: function(data) {
                $('.gruploader').hide();
            }
        }).done(function(data) {
            eval(data);
        }) .fail(function(qXHR, textStatus, errorThrown) {
            $('.gruploader').hide();
            say($(".gphrases > .failed").text(), 'e');
        });
    }
});

$('.swr-grupo .attachfile').change(function(e) {
    if ($(this).prop('files').length > 0) {
        $('.swr-grupo .atchmsg').find('.gid').val($('.swr-grupo .panel').attr('no'));
        $(".swr-grupo .panel > .textbox .replyid").val(0);
        $('.swr-grupo .panel > .room > .msgs > li').removeClass('selected');
        var data = new FormData($(".swr-grupo .atchmsg")[0]);
        var files = $(".swr-grupo .atchmsg > input.attachfile").get(0).files;
        for (var i = 0; i < files.length; i++) {
            data.append("attachfile["+i+"]", files[i]);
        }
        var totalSize = 0;
        $(this).each(function() {
            for (var i = 0; i < this.files.length; i++) {
                totalSize += this.files[i].size;
            }
        });
        totalSize = (totalSize / (1024*1024)).toFixed(2);
        var valid = false;
        if (totalSize < parseInt($(".gdefaults > .maxfilesize").text())) {
            valid = true;
        }
        if (!valid) {
            say($(".gphrases > .maxfilesizelimit").text());
            return;
        }
        var senid = rand(8);
        var senmsg = files[0].name;
        if (files.length > 1) {
            var totfil = parseInt(files.length)-1;
            senmsg = senmsg+' +('+totfil+')';
        }
        var moset = $(".dumb .gdefaults").find(".sndmsgalgn").text();
        var msg = '<li class="you animate__animated animate__fadeIn '+senid+' '+moset+'" no="0"> <div><span class="msg"><i>';
        msg = msg+'<span class="block" type="files"><span><span class="ptxt">'+escapeHtml(senmsg)+' (<span class="prog">0%</span>)';
        msg = msg+'</span><span class="animate__animated animate__fadeInUp animate__infinite">';
        msg = msg+'<i class="gi-upload"></i></span></span></span></i>';
        msg = msg+'</span></div></li>';
        $('.swr-grupo .panel > .room > .msgs').append(msg);
        $(".swr-grupo .panel > .room > .msgs").animate({
            scrollTop: $(".swr-grupo .panel > .room > .msgs").prop("scrollHeight")
        }, 500);
        data.append("attachfile", files);
        data.append("userid", $('.swr-grupo .panel > .textbox .userid').val());
        data.append("ldt", $('.swr-grupo .panel').attr('ldt'));
        data.append("from", grlastid());
        var f = '$(".swr-grupo .panel > .room > .msgs > li.'+senid+' > div > .msg > i > span.block > span > span > i").removeClass("gi-upload");';
        f = f+'$(".swr-grupo .panel > .room > .msgs > li.'+senid+' > div > .msg > i > span.block > span > span").removeClass("animate__animated");';
        f = f+'$(".swr-grupo .panel > .room > .msgs > li.'+senid+' > div > .msg > i > span.block > span > span > i").addClass("gi-minus-circled-1");';
        f = f+'setTimeout(function() {$(".'+senid+'").remove();}, 2000);';
        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = ((evt.loaded / evt.total) * 100);
                        $(".swr-grupo .panel > .room > .msgs > li."+senid+" > div > .msg > i > span.block > span > span > .prog").text(parseInt(percentComplete)+'%');
                    }
                }, false);
                return xhr;
            },
            url: '',
            dataType: 'text',
            cache: false,
            async: true,
            contentType: false,
            processData: false,
            data: data,
            type: 'post',
            success: function(data) {}
        }).done(function(data) {
            var data = $.parseJSON(data);
            $("."+senid).remove(); if ($(".swr-grupo .panel").attr("no") == data[0].gid) {
                loadmsg(data);
            }
        }) .fail(function(qXHR, textStatus, errorThrown) {
            eval(f);
            say($(".gphrases > .failed").text(), 'e');
        });
    }
});

function grmsgread(msid) {
    if (msid != undefined) {
        $('.swr-grupo .panel > .room > .msgs > li.you.msgschk').each(function() {
            if (parseInt($(this).attr('no')) <= parseInt(msid)) {
                $(this).find('i.info > i.tick.recieved').addClass('read');
                $(this).removeClass('msgschk');
            }
        });
    }
}

function grlastid() {
    var lastid = [];
    $('.swr-grupo .panel > .room > .msgs > li').each(function() {
        lastid.push($(this).attr('no'));
    });
    lastid.sort(function(a, b) {
        return b-a;
    });
    if (lastid[0] == undefined) {
        lastid[0] = 0;
    }
    return lastid[0];
}

function grlastseenid() {
    var lastid = [];
    $('.swr-grupo .panel > .room > .msgs > li').each(function() {
        if ($(this).find('.info > i.tick').hasClass('read')) {
            lastid.push($(this).attr('no'));
        }
    });
    lastid.sort(function(a, b) {
        return b-a;
    });
    if (lastid[0] == undefined || lastid[0] == 0) {
        lastid[0] = $(".swr-grupo .panel").attr("lstseen");
    }
    return lastid[0];
}

function grfirstid() {
    var firstid = [];
    $('.swr-grupo .panel > .room > .msgs > li').each(function() {
        if (!$(this).hasClass('dummy')) {
            firstid.push($(this).attr('no'));
        }
    });
    firstid.sort(function(a, b) {
        return a-b;
    });
    return firstid[0];
}

$('.swr-grupo .panel > .textbox > .box > textarea').on('keypress', function(e) {
    if (e.which == 13 && $(".dumb .gdefaults").find(".enterassend").text() == "enable") {
        if (!e.shiftKey) {
            e.preventDefault();
            $('.swr-grupo .sendbtn').trigger('click');
        }
    }
});

$('body').on('click keyup change', '.emojionearea-editor', function(e) {
    var scrollresize = 1;
    setTimeout(function() {
        $npd = parseInt($('.emojionearea > .emojionearea-editor').css("height"))+50;
        $(".swr-grupo .panel > .room").css("padding-bottom", $npd+"px");
        if (scrollresize == 1) {
            grscroll($(".swr-grupo .panel > .room > .msgs"), 'resize');
        }
    }, 200);
});

$('body').on('click', '.swr-grupo .panel > .textbox > .mentions > ul > li', function() {
    var a = $(".swr-grupo .panel > .textbox > .mentions > input").val();
    var c = $(this).find('span > i').text();
    var el = $(".emojionearea-editor").get(0);
    var $txt = jQuery(".emojionearea-editor");
    var caretPos = $('.emojionearea-editor').attr('inx');
    var textAreaTxt = $txt.html();
    var chars = parseInt(caretPos)+parseInt(c.length);
    var str = textAreaTxt.substring(0, caretPos);
    var k = str.substring(0, str.length - a.length);
    var cpos = str.length - a.length;
    $txt.html(k + c + textAreaTxt.substring(caretPos));
    $(".swr-grupo .panel > .textbox > .mentions").hide();
    setcrtpost(el, cpos+c.length);
});

function createRange(node, chars, range) {
    if (!range) {
        range = document.createRange();
        range.selectNode(node);
        range.setStart(node, 0);
    }

    if (chars.count === 0) {
        range.setEnd(node, chars.count);
    } else if (node && chars.count > 0) {
        if (node.nodeType === Node.TEXT_NODE) {
            if (node.textContent.length < chars.count) {
                chars.count -= node.textContent.length;
            } else {
                range.setEnd(node, chars.count);
                chars.count = 0;
            }
        } else {
            for (var lp = 0; lp < node.childNodes.length; lp++) {
                range = createRange(node.childNodes[lp], chars, range);

                if (chars.count === 0) {
                    break;
                }
            }
        }
    }

    return range;
};


$('body').on('keypress', '.emojionearea-editor', function(e) {
    if (e.which == 13 && $(".dumb .gdefaults").find(".enterassend").text() == "enable") {
        if (!e.shiftKey) {
            e.preventDefault();
            $('.swr-grupo .sendbtn').trigger('click');
        }
    }
});

$('.swr-grupo .subnav').on('click', function() {
    if ($(this).find(".swr-menu").is(':visible')) {
        $(this).find(".swr-menu").hide();
    } else {
        $('.swr-grupo .swr-menu').hide();
        $(this).addClass('active');
        $(this).find(".swr-menu").fadeIn();
    }
});

$('.grupo-pop > div > form > span.cancel').on('click', function(e) {
    $(".grupo-pop").fadeOut();
    if (ajxvar['subformpop'] != undefined) {
        ajxvar['subformpop'].abort();
    }
    ajxclrtm["grlivesetin"] = setTimeout(function() {
        gr_live();
    }, 2000);
});

$('body').on('click', '.grupo-pop > div > form > .fields > div > span.fileup > span', function() {
    $(this).parent().find('input').trigger('click');
});
$('body').on('change', '.grupo-pop > div > form > .fields > div > span.fileup > input[type=file]', function(e) {
    $(this).parent().find('span').text(e.target.files[0].name);
});

$('body').on('click', '.swr-grupo .panel > .room > .msgs > li.selected', function() {
    $('.swr-grupo .panel > .room > .msgs > li').removeClass('selected');
    $('.swr-grupo .panel > .textbox .replyid').val(0);
    $('.emojionearea > .emojionearea-editor').focus();
});

$('body').on('click', '.swr-grupo .msg span.opts > span > .gr-reply', function() {
    var id = $(this).parents('li').attr('no');
    $('.swr-grupo .panel > .room > .msgs > li').removeClass('selected');
    $(this).parents('li').addClass('selected');
    $('.swr-grupo .panel > .textbox .replyid').val(id);
    $('.emojionearea > .emojionearea-editor').focus();
});

$("body").on("contextmenu", "img", function(e) {
    return false;
});
$('body').on('click', '.grupo-pop > div > form > div > div > .imglist > li', function(e) {
    $('.grupo-pop > div > form > div > div > .imglist > li').removeClass('active');
    $(this).find('input').prop("checked", true);
    $(this).addClass('active');
});

$('body').on('change', '.grupo-pop .audselect > select', function(e) {
    $("#graudio > source").attr("src", $(this).val());
    $("#graudio > source").attr("type", "audio/mp3");
    $("#graudio")[0].pause();
    $("#graudio")[0].load();
    $("#graudio")[0].play();
});
$('body').on('click', '.grupo-pop > div > form > input[type="submit"]', function(e) {
    e.preventDefault();
    $(".grformspin").show();
    $('.grupo-pop > div > form .selectinp > input').val('');
    var data = new FormData($('.grform')[0]);
    if ($(".grupo-pop > div > form > .fields > div > span.fileup > .multifiles").length) {
        var multifiles = $(".grupo-pop > div > form > .fields > div > span.fileup > .multifiles").get(0).files;
        for (var i = 0; i < multifiles.length; i++) {
            data.append("multifiles["+i+"]", multifiles[i]);
        }
    }
    ajxvar['subformpop'] = $.ajax({
        url: '',
        dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
        async: true,
        data: data,
        type: 'post',
        beforeSend: function() {
            if (ajxvar['grlive'] !== null && ajxvar['grlive'] !== undefined) {
                ajxvar['grlive'].abort();
            }
            if (ajxvar['subformpop'] != undefined) {
                ajxvar['subformpop'].abort();
            }
        },
        success: function(data) {}
    }).done(function(data) {
        eval(data);
        $(".grformspin").fadeOut();
        ajxclrtm["grlivesetin"] = setTimeout(function() {
            gr_live();
        }, 2000);
    }) .fail(function(qXHR, textStatus, errorThrown) {
        say($(".gphrases > .failed").text(), 'e');
        $(".grformspin").fadeOut();
        ajxclrtm["grlivesetin"] = setTimeout(function() {
            gr_live();
        }, 2000);
    });
});

$('body').on('click', '.formpop', function(e) {
    $(this).attr('type', 'json');
    $(".grformspin").fadeOut();
    $(this).attr('timeout', 0);
    if (ajxvar['grlive'] != undefined) {
        ajxvar['grlive'].abort();
    }
    if (ajxclrtm['grlive'] != undefined) {
        clearTimeout(ajxclrtm['grlive']);
    }
    $(this).attr('load', $(".gphrases > .loading").text());
    $(this).attr('lsub', $(".gphrases > .pleasewait").text());
    $(".grupo-pop > div > form > .search > input").val("");
    id = $(this).attr('no');
    if ($(this).attr('pn') == 1) {
        id = $('.swr-grupo .panel').attr('no');
    } else if ($(this).attr('pn') == 2) {
        id = $(this).parent().parent().attr('no');
    } else if ($(this).attr('pn') == 3) {
        id = $(this).parents('li').attr('no')
    } else if ($(this).attr('pn') == 4) {
        id = $(this).attr('no')
    }
    var data = {
        act: 1,
        do: "form",
        type: $(this).attr('do')+$(this).attr('act'),
        id: id,
        ldt: $('.swr-grupo .panel').attr('ldt'),
        xtid: $(this).attr('xtid'),
    };
    data = $.extend(data, $(this).data());
    $(".grupo-pop .head").text($(this).attr('title'));
    $(".grupo-pop .grsub").val($(this).attr('btn'));
    var s = '$(".grupo-pop").fadeIn();var fd="";';
    s = s+'if(Object.keys(data).length < 4 || data["fsearch"]=="off"){$(".grupo-pop > div > form > .search").hide();}';
    s = s+'else{$(".grupo-pop > div > form > .search").show();}';
    s = s+'$(".grupo-pop .grdo").val("'+$(this).attr('do')+'");';
    s = s+'$(".grupo-pop .grtype").val("'+$(this).attr('act')+'");';
    s = s+'$.each(data, function(k, v) {var ab=ac="";if(v[1]!=undefined && v[1].indexOf(":") != -1){ab=v[1].split(":")[1]; ac=v[1].split(":")[2]; v[1]=v[1].split(":")[0];}';
    s = s+'ab="'+"'"+'"+ab+"'+"'"+'";';
    s = s+'fd=fd+"<div class="+ab+ac+">";';
    s = s+'if(v[2]==="hidden"){fd=fd+"<input type="+v[2]+" value="+htmlDecode(v[3])+" name="+k+" autocomplete=dsb>"}';
    s = s+'else if(v[2]==="disabled" && v[1]==="textarea"){fd=fd+"<label>"+v[0]+"</label><textarea disabled name="+k+">"+htmlDecode(v[3])+"</textarea>"}';
    s = s+'else if(v[2]==="disabled"){fd=fd+"<label>"+v[0]+"</label><input type=text value="+htmlDecode(v[3])+" disabled name="+k+" autocomplete=dsb>"}';
    s = s+'else if(v[4]!==undefined && v[1]==="checkbox"){ fd=fd+"<label>"+htmlDecode(v[0])+"</label><div class=checkbox>"; var ov=v[4].split(",");var ch=v[2].split(",");var cv=v[3].split(",");$.each(ch, function(ke, va) { fd=fd+"<span><input type="+v[1];if(jQuery.inArray(cv[ke], ov) != -1) {fd=fd+" checked ";}fd=fd+" name="+k+"[] value="+htmlDecode(cv[ke])+">"+htmlDecode(va)+"</span>"; }); fd=fd+"</div>";}';
    s = s+'else if(v[1]==="checkbox"){ fd=fd+"<label>"+htmlDecode(v[0])+"</label><div class=checkbox>"; var ch=v[2].split(",");var cv=v[3].split(",");$.each(ch, function(ke, va) { fd=fd+"<span><input type="+v[1]+" name="+k+"[] value="+htmlDecode(cv[ke])+">"+htmlDecode(va)+"</span>"; }); fd=fd+"</div>";}';
    s = s+'else if(v[1]==="radio"){ fd=fd+"<label>"+htmlDecode(v[0])+"</label><div class=checkbox>"; var ch=v[2].split(",");var cv=v[3].split(",");$.each(ch, function(ke, va) { fd=fd+"<span><input type="+v[1]+" name="+k+" value="+htmlDecode(cv[ke])+" >"+htmlDecode(va)+"</span>"; }); fd=fd+"</div>";}';
    s = s+'else if(v[2]==="file"){fd=fd+"<label>"+htmlDecode(v[0])+"</label><span class=fileup><input type="+v[2]+" name="+k+" "+v[3]+" autocomplete=dsb><span>"+htmlDecode(data["choosefiletxt"][0])+"</span></span>"}';
    s = s+'else if(v[4]!==undefined && v[1]==="input"){fd=fd+"<label>"+htmlDecode(v[0])+"</label><input type="+v[2]+" placeholder="+v[4]+" name='+"'"+'"+k+"'+"'"+' autocomplete=dsb>"}';
    s = s+'else if(v[3]!==undefined && v[1]==="input" && v[2]==="colorpick"){fd=fd+"<label>"+htmlDecode(v[0])+"</label><input type=text class=colorpick value="+v[3]+" name="+k+" autocomplete=dsb>"}';
    s = s+'else if(v[3]!==undefined && v[1]==="input"){fd=fd+"<label>"+htmlDecode(v[0])+"</label><input type="+v[2]+" value="+htmlDecode(v[3])+" name="+k+" autocomplete=dsb>"}';
    s = s+'else if(v[3]!==undefined && v[1]==="textarea"){if(v[4]==undefined){v[4]="";}fd=fd+"<label>"+htmlDecode(v[0])+"</label><textarea name="+k+" placeholder="+v[4]+">"+htmlDecode(v[3])+"</textarea>"}';
    s = s+'else if(v[3]!==undefined && v[1]==="span"){fd=fd+"<label>"+htmlDecode(v[0])+"</label><span name="+k+" >"+htmlDecode(v[3])+"</span>"}';
    s = s+'else if(v[1]==="textarea"){if(v[4]==undefined){v[4]="";}fd=fd+"<label>"+htmlDecode(v[0])+"</label><textarea name="+k+" placeholder="+v[4]+"></textarea>"}';
    s = s+'else if(v[1]==="input"){fd=fd+"<label>"+htmlDecode(v[0])+"</label><input type="+v[2]+" name="+k+" autocomplete=dsb>"}';
    s = s+'else if(v[1]==="select"){fd=fd+"<label>"+htmlDecode(v[0])+"</label><select name="+k+" >";';
    s = s+'if(jQuery.type(v[2])=="object"){';
    s = s+'fd=fd+"<option value=0>------</option>";';
    s = s+'$.each(v[2] , function(index, val) {var sel="";if(index==v[3]){sel="selected";} fd=fd+"<option "+sel+" value='+"'"+'"+index+"'+"'"+'>"+htmlDecode(val)+"</option>";});';
    s = s+'}else{';
    s = s+'for(i=2;i<v.length;i++){fd=fd+"<option value="+v[i]+">"+htmlDecode(v[i+1])+"</option>";i=i+1;}}';
    s = s+'fd=fd+"</select>"}';
    s = s+'else if(v[1]==="tmz"){fd=fd+"<label>"+htmlDecode(v[0])+"</label><select name="+k+" ><option value=0>------</option>";';
    s = s+'var tm=v[2].split(",");for(i=0;i<tm.length;i++){var sel="";if(tm[i]==v[3]){sel="selected";}fd=fd+"<option "+sel+" value='+"'"+'"+tm[i]+"'+"'"+'>"+htmlDecode(tm[i])+"</option>";}';
    s = s+'fd=fd+"</select>"}';
    s = s+'else if(v[1]==="imglist"){fd=fd+"<label>"+htmlDecode(v[0])+"</label><ul class=imglist>";';
    s = s+'if(jQuery.type(v[2])=="object"){';
    s = s+'$.each(v[2] , function(index, val) { fd=fd+"<li><input type=radio name="+k+" value="+index+"><img class=lazyimg data-src="+val+"/></li>";});}';
    s = s+'fd=fd+"</ul>";}';
    s = s+'fd=fd+"</div>";';
    s = s+'});';
    s = s+'$(".grupo-pop .fields").html(fd);gralltxtareaajust();';
    s = s+'$(".grupo-pop > div > form > .fields > div > span.fileup > input").hide();$(".colorpick").colorpicker();';
    s = s+'$(".grupo-pop > div > form > .search > input").focus();';
    s = s+'$(".grupo-pop > div > form > div").scrollTop(0);lazyload();autotimez("run");';
    var f = grlv = 'ajxclrtm["grlivesetin"]=setTimeout(function() { gr_live(); }, 2000);';
    s = s+grlv;
    ajxx($(this), data, s, e, f, 'formpopup');

});

$(document).on('click', '.grupo-pop > div > form .selectinp > input', function() {
    this.setSelectionRange(0, this.value.length);
});
$(document).on('keypress', '.grupo-pop > div > form .selectinp > input', function(e) {
    e.preventDefault();
});
$("body").on("change", ".grupo-pop > div > form .shwopts input,.grupo-pop > div > form .shwopts select", function(e) {
    $pt = $(this).parents('.shwopts');
    if ($pt.attr("mtch") == $(this).val()) {
        $('.grupo-pop > div > form .hidopts.'+$pt.attr("shw")).show();
    } else {
        $('.grupo-pop > div > form .hidopts.'+$pt.attr("shw")).hide();
    }
    grscroll($(".grupo-pop > div > form > div"), "resize");
});

$(document).ready(function() {
    if ($('.lside .gi-list-add .swr-menu > ul > li').length === 0) {
        $('.lside .gi-list-add').hide();
    }
    $('[data-toggle="tooltip"]').tooltip();
    window.emojioneVersion = "4.5.0";
    $(".swr-grupo .panel > .textbox > .box > textarea").emojioneArea({
        pickerPosition: "top",
        tonesStyle: "radio",
        search: false,
        autocomplete: true,
        buttonTitle: "",
        hidePickerOnBlur: false,
        attributes: {
            spellcheck: true,
        },
        saveEmojisAs: "shortname",
        events: {
            keyup: function (editor, event) {
                var txtenable = $.trim($(".dumb .gdefaults > .enabletextarea").text());
                if (txtenable != 1) {
                    this.setText('');
                    say($(".gphrases > .notxtmsg").text());
                } else {
                    grtyprec();
                    var lmt = parseInt($(".dumb .gdefaults").find(".maxmsglen").text());
                    var text = this.getText();
                    if (lmt !== "") {
                        if (text.length > lmt) {
                            this.setText(text.substring(0, lmt));
                            placeCaretAtEnd($(".emojionearea > .emojionearea-editor").data("emojioneArea").editor[0]);
                            say($(".gphrases > .exceededmsg").text());
                        }
                    }
                }
            }
        },

    });
    var d = new Date();
    var offset = -d.getTimezoneOffset() * 60;
    $.post("", {
        act: 1,
        do: "profile",
        type: "autotimezone",
        offset: offset,
    });
});

function grtyprec(rst) {
    var el = $('.emojionearea-editor');
    var typrectms;
    if (rst != undefined) {
        el.attr('typing', 0);
        if (typrectms != undefined) {
            clearTimeout(typrectms);
        }
    } else if (el.attr('typing') != 1) {
        if (ajxvar['grlive'] != undefined) {
            ajxvar['grlive'].abort();
        }
        if (ajxclrtm['grlive'] != undefined) {
            clearTimeout(ajxclrtm['grlive']);
        }
        $.post("", {
            act: 1,
            do: "group",
            type: "typing",
            id: $('.swr-grupo .panel').attr('no'),
            ldt: $('.swr-grupo .panel').attr('ldt'),
        })  .done(function() {
            ajxclrtm["grlivesetin"] = setTimeout(function() {
                gr_live();
            }, 2000);
        })
        .fail(function() {
            ajxclrtm["grlivesetin"] = setTimeout(function() {
                gr_live();
            }, 2000);
        });
        el.attr('typing', 1);
        typrectms = setTimeout(function() {
            grtyprec(1);
        }, 8000);
    }
}

$('.gr-mic').on('click', function () {
    $(this).hide();
    var elem = $(this);
    if ($(this).hasClass('recrdng')) {
        $(this).removeClass('recrdng').fadeIn();
    } else {
        $(this).addClass('recrdng').fadeIn();
    }
});


$("body").on("keyup", ".swr-grupo .aside > .oldsearch > input", function(e) {
    var search = $(this).val().toLowerCase();
    var aside = 'lside';
    if ($(this).parents('.aside').hasClass('rside')) {
        aside = 'rside';
    }
    if (e.which == 13) {
        if (search.length > 2) {
            $(".dumb .srchbx").attr("srch", search);
            $(".dumb .srchbx").attr("side", aside);
            $(".dumb .srchbx").trigger('click');
        } else {
            say($(".gphrases > .searchmin").text());
        }
    }
});

$("body").on("keyup", ".swr-grupo .aside > .search > input", function(e) {
    var search = $(this).val().toLowerCase();
    if ($(this).parents('.aside').hasClass('lside') && $('.swr-grupo .lside > .tabs > ul > li.active').attr('act') == 'files') {
        var search = $(this).val();
    }
    var aside = 'lside';
    if ($(this).parents('.aside').hasClass('rside')) {
        aside = 'rside';
    }
    if (e.which == 13) {
        if (search.length > 2) {
            $(".swr-grupo ."+aside+" > .tabs > ul > li.active").attr("srch", search);
            $(".swr-grupo ."+aside+" > .tabs > ul > li.active").addClass('searching');
            $(".swr-grupo ."+aside+" > .tabs > ul > li.active").trigger('click');
            $(".swr-grupo ."+aside+" > .tabs > ul > li.active").removeAttr("srch");
            $(".swr-grupo ."+aside+" > .content > .list").parent().find('.grproceed').attr('srch', search);
            $(".swr-grupo ."+aside+" > .tabs > ul > li.active").removeClass('searching');
        } else if (search.length == 0) {
            $(".swr-grupo ."+aside+" > .tabs > ul > li.active").trigger('click');
        } else {
            say($(".gphrases > .searchmin").text());
        }
    }
});

$("body").on("click", ".swr-grupo .panel .searchmsgs", function(e) {
    if ($(".swr-grupo .panel > .searchbar").hasClass("animate__slideInDown")) {
        $(".swr-grupo .panel > .searchbar").removeClass("animate__animated animate__slideInDown");
        $(".swr-grupo .panel > .searchbar").addClass("animate__animated animate__slideOutUp animate__faster").show();
    } else {
        $(".swr-grupo .panel > .searchbar").removeClass("animate__animated animate__slideOutUp");
        $(".swr-grupo .panel > .searchbar").addClass("animate__animated animate__slideInDown animate__faster").show();
        $(".swr-grupo .panel > .searchbar input").focus();
    }
});
$("body").on("click", ".swr-grupo .panel > .room", function(e) {
    if ($(".swr-grupo .panel > .searchbar").hasClass("slideInDown")) {
        $(".swr-grupo .panel .searchmsgs").trigger('click');
    }
});
$("body").on("keyup", ".swr-grupo .panel > .searchbar input", function(e) {
    if (e.which == 13) {
        $(".swr-grupo .panel .searchmsgs").trigger('click');
        var search = $(this).val();
        if (search != "") {
            turn_chat();
            $(this).attr('type', 'json');
            $(this).attr('turn', 'on');
            $(this).attr('spin', 'off');
            $(".swr-grupo .msgloader").removeClass("error").fadeIn();
            var data = {
                act: 1,
                do: "group",
                type: 'msgs',
                id: $('.swr-grupo .panel').attr('no'),
                search: $(this).val(),
                ldt: $('.swr-grupo .panel').attr('ldt'),
            };
            var s = '$(".swr-grupo .msgloader").fadeOut(100);if($(".swr-grupo .panel").attr("no")==data[0].gid || $(".swr-grupo .panel").attr("no").indexOf("-") != -1){loadmsg(data,1);}';
            var f = '$(".swr-grupo .msgloader").addClass("error");';
            ajxx($(this), data, s, e, f, 'searchmsgs', 'grlive');
        }
    }
});
$("body").on("click", ".grupo-video > div > div > span", function(e) {
    $(".grupo-video").hide();
    $(".grupo-video > div > div > iframe").remove();
});
$("body").on("click", ".swr-grupo .panel > .room > .msgs > li a.olembedvideo", function(e) {
    e.preventDefault();
    var video = urlParser.parse($(this).attr('href')),
    link = '',
    extra = 'allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen';
    if (video['provider'] === 'youtube') {
        link = 'https://www.youtube.com/embed/'+video['id']+'?autoplay=1';
    } else if (video['provider'] === 'dailymotion') {
        link = 'https://www.dailymotion.com/embed/video/'+video['id']+'?autoplay=1';
    } else if (video['provider'] === 'vimeo') {
        extra = 'webkitallowfullscreen mozallowfullscreen allowfullscreen';
        link = 'https://player.vimeo.com/video/'+video['id']+'?autoplay=1';
    } else if (video['provider'] === 'twitch') {
        link = 'https://player.twitch.tv/?'+video['id']+'?autoplay=1';
    } else if (video['provider'] === 'youku') {
        link = 'http://player.youku.com/embed/'+video['id']+'?autoplay=1';
    }
    $(".grupo-video > div > div").append('<iframe src="'+link+'" '+extra+' frameborder="0" ></iframe>');
    $(".grupo-video").fadeIn();
});
$("body").on("click", ".turnchat", function(e) {
    var turn = $(this).attr('do');
    $(this).attr("ldt", $('.swr-grupo .panel').attr('ldt'));
    turn_chat(turn, $(this));
});
function turn_chat($d, el) {
    if ($d == undefined) {
        $d = 'off';
    }
    if ($d === 'on') {
        $('.groupreload').fadeOut();
        $('.swr-grupo .panel > .textbox').removeClass('animate__animated animate__slideOutDown disabled reloadon');
        $oldgid = $('.swr-grupo .panel').attr('no');
        loadgroup($oldgid, el, 1);
        $(".swr-grupo .panel").attr('deactiv', 0);
        $('.swr-grupo .panel > .textbox').addClass('animate__animated animate__slideInUp');
    } else {
        $('.groupreload').fadeIn();
        $('.swr-grupo .panel > .textbox').removeClass('animate__animated animate__slideInUp');
        $(".swr-grupo .panel").attr('deactiv', 1);
        $('.swr-grupo .panel > .textbox').addClass('animate__animated animate__slideOutDown disabled reloadon');
    }
}

$('.swr-grupo .aside > .content > .list').on('scroll', function(e) {
    if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight-20) {
        if ($(this).parent().find('.grproceed').hasClass('loadside')) {
            $(this).parent().find('.grproceed').trigger('click');
        }
    }
});

$('.swr-grupo .panel > .room > .msgs').on('scroll', function(e) {
    if (!$(".swr-grupo .panel > .room > .msgs").hasClass('nooutscroll')) {
        if ($(".swr-grupo .panel > .room > .msgs > li").eq(0).hasClass('createdgroup')) {
            $('.swr-grupo .panel').attr('noscroll', 1);
        }
        if (!$(".swr-grupo .panel > .textbox").hasClass('disabled') && $('.swr-grupo .panel').attr('noscroll') != 1) {
            var scrollTop = $(this).scrollTop();
            if (scrollTop <= 0) {
                $(".swr-grupo .panel").attr('scrolldown', 'off');
                $(this).attr('type', 'json');
                $(this).attr('turn', 'on');
                $(this).attr('spin', 'off');
                var firstid = grfirstid();
                $(".swr-grupo .msgloader").removeClass("error").addClass('scrolload').fadeIn(100);
                var data = {
                    act: 1,
                    do: "group",
                    type: 'msgs',
                    id: $('.swr-grupo .panel').attr('no'),
                    to: firstid,
                    ldt: $('.swr-grupo .panel').attr('ldt'),
                };
                var s = 'if($(".swr-grupo .panel").attr("no")==data[0].gid){loadmsg(data,2,'+firstid+');}$(".swr-grupo .msgloader").fadeOut(100);';
                var f = '$(".swr-grupo .msgloader").removeClass("scrolload").addClass("error");';
                f = f+'setTimeout(function() { $(".swr-grupo .msgloader").fadeOut(100); }, 2000);';
                var grlv = 'ajxclrtm["grlivesetin"]=setTimeout(function() { gr_live(); }, 2000);';
                s = s+grlv;
                f = f+grlv;
                ajxx($(this), data, s, e, f, 'scrollmsgs', 'grlive');
            }
        }
        if ($(this).scrollTop() + $(this).innerHeight()+150 >= $(this)[0].scrollHeight) {
            $(".swr-grupo .panel").attr('scrolldown', 'on');
        } else {
            lazyload();
            $(".swr-grupo .panel").attr('scrolldown', 'off');
        }
    }
});
function htmlDecode(input) {
    var e = document.createElement('div');
    e.innerHTML = input;
    if (e.childNodes[0] == undefined) {
        return input;
    } else {
        return e.childNodes[0].nodeValue;
    }
}
$(window).load(function() {
    var firstload = $('.dumb .firstload').find('span');
    $('.gr-preloader').fadeOut();
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register($(".dumb .gdefaults > .baseurl").text()+"pwabuilder-sw.js");
    }
    $('.swr-grupo').fadeIn();
    $('.swr-grupo .lside > .tabs > ul > li').eq(0).trigger('click');
    if ($(window).width() > 991) {
        $('.swr-grupo .rside > .tabs > ul > li').eq(0).trigger('click');
    }
    if (firstload.text() != '') {
        setTimeout(function() {
            firstload.trigger('click');
        }, 600);
    }
    var swearkey = 'localSwears'+$(".dumb .gdefaults > .baseurl").text()+'gem/ore/grupo/cache/filterwords.json';
    if (swearkey in localStorage) {
        localStorage.removeItem(swearkey);
    }
    grscroll($(".swr-grupo .lside > .content > .list"));
    grscroll($(".swr-grupo .panel > .room > .msgs"));
    if ($(window).width() <= 768 || $(window).width() >= 991) {
        grscroll($(".swr-grupo .rside > .content > .list"));
        grscroll($(".swr-grupo .aside > .content .profile > .bottom > div > ul"));
    }
    grscroll($(".grgif > div > div"));
    grscroll($(".grupo-pop > div > form > div"));
    grscroll($(".swr-menu"));
    var d = new Date();
    var offset = -d.getTimezoneOffset() * 60;
    if ($('.swr-grupo').hasClass('radioenabled')) {
        if (Cookies.get('grradioplayer') != undefined) {
            $(".swr-grupo .grradioplayer > div > .rinfo > span > b > ul.radiolist > li[no='"+Cookies.get('grradioplayer')+"']").trigger('click');
        } else {
            $(".swr-grupo .grradioplayer > div > .rinfo > span > b > ul.radiolist > li").eq(0).trigger('click');
        }
    }
    $.post("", {
        act: 1,
        do: "profile",
        type: "autotimezone",
        offset: offset,
        timez: moment.tz.guess(),
    });
});
$("body").on("click", ".swr-grupo .grradioplayer > div > .rcontrols > .rplay", function(e) {
    if ($("#radioplayerstream")[0].paused) {
        $("#radioplayerstream")[0].play();
    } else {
        $("#radioplayerstream")[0].pause();
    }
});

$("body").on("click", ".swr-grupo .grradioplayer > div > .rinfo > span > b", function(e) {
    if (!$(e.target).parent().hasClass('radiolist')) {
        $(".swr-grupo .grradioplayer > div > .rinfo > span > b > ul.radiolist").toggle();
    }
});

$("body").on("click", ".swr-grupo .grradioplayer > div > .rcontrols > .rnext", function(e) {
    if ($('.swr-grupo .grradioplayer > div > .rinfo > span > b > ul.radiolist > li.active').is(":last-child")) {
        $(".swr-grupo .grradioplayer > div > .rinfo > span > b > ul.radiolist > li").eq(0).trigger('click');
    } else {
        $(".swr-grupo .grradioplayer > div > .rinfo > span > b > ul.radiolist > li.active").next('li').trigger('click');
    }
});
$("body").on("click", ".swr-grupo .grradioplayer > div > .rcontrols > .rprev", function(e) {
    if ($('.swr-grupo .grradioplayer > div > .rinfo > span > b > ul.radiolist > li.active').is(":first-child")) {
        $(".swr-grupo .grradioplayer > div > .rinfo > span > b > ul.radiolist > li:last-child").trigger('click');
    } else {
        $(".swr-grupo .grradioplayer > div > .rinfo > span > b > ul.radiolist > li.active").prev('li').trigger('click');
    }
});
$("body").on("click", ".swr-grupo .grradioplayer > div > .rinfo > span > b > ul.radiolist > li", function(e) {
    $(".swr-grupo .grradioplayer > div > .rinfo > span > b > ul.radiolist > li").removeClass('active');
    $("#radioplayerstream > source").attr("src", $(this).attr('stream'));
    $("#radioplayerstream")[0].pause();
    $("#radioplayerstream")[0].load();
    if (radioenabled == 'enable' || !$(".swr-grupo .grradioplayer").hasClass('onloaded')) {
        $("#radioplayerstream")[0].play();
    }
    $(".swr-grupo .grradioplayer").removeClass('onloaded');
    Cookies.set('grradioplayer', $(this).attr("no"), {
        expires: 1
    });
    $('.swr-grupo .grradioplayer > div > .rinfo > span > span').marquee('destroy');
    $(".swr-grupo .grradioplayer > div > .rinfo > span > b > span").text($(this).text());
    $(".swr-grupo .grradioplayer > div > .rinfo > span > span").html('<span>'+$(this).attr("subtitle")+'</span>');
    $(".swr-grupo .grradioplayer > div > .rinfo > img").attr("src", $(this).attr("icon"));
    $(".swr-grupo .grradioplayer > div > .rinfo > span > b > ul.radiolist").hide();
    $('.swr-grupo .grradioplayer > div > .rinfo > span > b').addClass('activated');
    $('.swr-grupo .grradioplayer > div > .rinfo > span > span').marquee({
        duration: 8000,
        pauseOnHover: true,
        duplicated: true,
        startVisible: true
    });
    $(this).addClass('active');
});
if ($('.swr-grupo').hasClass('radioenabled')) {
    $("#radioplayerstream")[0].addEventListener('play', function() {
        $('.swr-grupo .grradioplayer > div > .rcontrols > .rplay').addClass('rpause');
    });
    $("#radioplayerstream")[0].addEventListener('pause', function() {
        $('.swr-grupo .grradioplayer > div > .rcontrols > .rplay').removeClass('rpause');
    });
}
function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
};
function url2link(text, nl) {
    if (nl == undefined) {
        nl = 0;
    }
    if (nl == 0) {
        return (text || "").replace(
            /([^\S]|^)(((https?\:\/\/)|(www\.)|[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3}))(\S+))/gi,
            function(match, space, url) {
                url = url.replace(/[<>.`~!#$@%^&*()_|+\-=?;:'",<>\t]+$/gi, '');
                var email = hyperlink = url;
                var output;
                if (!hyperlink.match('^https?:\/\/')) {
                    hyperlink = 'http://' + hyperlink;
                }
                var notvalid = 0;
                var orh = hyperlink.split("<br");
                hyperlink = orh[0];
                var video = urlParser.parse(hyperlink);
                var em = 'openlink';
                if (video && video['provider'] === 'youtube' || video && video['provider'] === 'dailymotion' || video && video['provider'] === 'vimeo') {
                    var a = document.createElement('a');
                    a.href = hyperlink;
                    em = 'openlink embedvideo';
                    url = '<i class="gi-link" vid="'+video['id']+'" provider="'+video['provider']+'"></i>'+a.hostname;
                } else if (isValidEmailAddress(email)) {
                    em = 'email';
                    url = '<i class="gi-mail"></i>'+email;
                    hyperlink = "mailto:"+email;
                } else {
                    var a = document.createElement('a');
                    a.href = hyperlink;
                    if (a.hostname == '') {
                        notvalid = 1;
                    } else {
                        url = '<i class="gi-link"></i>'+a.hostname;
                    }
                }
                if (notvalid == 1) {
                    output = hyperlink;
                } else {
                    output = '<a class="'+em+'" href="' + hyperlink + '" target="_blank">' + url + '</a>';
                }
                if (orh[1] != undefined) {
                    output = output+'<br';
                }
                return space + output;
            }
        );
    } else {
        return text;
    }
};

function convertphonenum(text, nl) {
    if (nl == undefined) {
        nl = 0;
    }
    if (nl == 0) {
        return (text || "").replace(
            /(\b|[(+])[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}\b(?![\/=!@#$%^+"&*?<>])/g,
            function(match, space, phonenum) {
                url = '<i class="gi-phone-circled"></i>'+match;
                output = '<a class="phonenum" href="tel://' + match + '" target="_blank">' + url + '</a>';
                return output;
            }
        );
    } else {
        return text;
    }
};

function placeCaretAtEnd(el) {
    if (typeof window.getSelection != "undefined" && typeof document.createRange != "undefined") {
        var range = document.createRange();
        range.selectNodeContents(el);
        range.collapse(false);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    } else if (typeof document.body.createTextRange != "undefined") {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(el);
        textRange.collapse(false);
        textRange.select();
    }
}
$('body').on('click', '.swr-grupo .aside > .content .profile .editprf', function(e) {
    $('.swr-grupo .aside > .content .profile > .top > span.edit > i').trigger('click');
});
$('body').one('click', '.emojionearea-editor', function(e) {
    grscroll($(".emojionearea-editor"));
});

$('body').on('click', '.swr-grupo .panel > .textbox > .box > .icon > .gr-emoji.old', function(e) {
    if ($('.emojionearea-picker').hasClass('hidden')) {
        $('.emojionearea-button').trigger('click');
    } else {
        $(".swr-grupo .panel > .textbox > .box > textarea").data("emojioneArea").hidePicker();
    }
    $('.emojionearea > .emojionearea-editor').focus();
    var el = $(".emojionearea > .emojionearea-editor")[0];
    var pos = $(".emojionearea > .emojionearea-editor").attr("inx");
    SetCaretPosition(el, pos);
});

$('body').on('focus', '.emojionearea > .emojionearea-editor', function(e) {
    scrollmsgs(100, 100);
});

$('body').on('click tap touchstart', '.vwp', function(e) {
    var kr = 3;
    var ths = $(this);
    var et = e;
    if ($(window).width() <= 767.98) {
        $('[data-toggle="tooltip"]').tooltip('hide');
        $(".swr-grupo .lside .opt > ul").hide();
        $(".grtab").addClass("d-none");
        if ($(this).parents('div.lside').length || $(this).parents('div.firstload').length) {
            $('.swr-grupo .lside,.swr-grupo .panel').removeClass('abmob');
            $(".swr-grupo .lside,.swr-grupo .panel").addClass("bwmob");
            $('.swr-grupo .rside > .top > .left > .icon').attr('data-block', 'alerts');
            $('.rside').removeClass('abmob');
        } else if ($(this).parents('div.panel').length) {
            $('.swr-grupo .lside,.swr-grupo .panel').removeClass('abmob');
            $(".swr-grupo .lside,.swr-grupo .panel").addClass("bwmob");
            $('.swr-grupo .rside > .top > .left > .icon').attr('data-block', 'crew');
            $('.rside').removeClass('abmob');
        }
        $(".swr-grupo .rside").removeClass(""+animclose+"");
        if (!$('.rside').hasClass('abmob')) {
            $(".swr-grupo .rside").removeClass("nomob");
            $(".swr-grupo .rside").addClass("abmob animate__animated "+animopen+" animate__fast");
        }
    }
    if ($(window).width() >= 768 && $(window).width() <= 991) {
        grtabfold();
    }
    setTimeout(function() {
        $('.swr-grupo .rside > .content > .list').hide();
        $('.swr-grupo .rside > .tabs > ul > li').removeClass('active');
        $(".swr-grupo .rside > .content .profile").fadeOut();
        ths.attr('type', 'json');
        ths.attr('spin', 'off');
        $('.swr-grupo .rside .listloader').removeClass("error").fadeIn();
        var data = {
            act: 1,
            do: "list",
            type: 'getinfo',
            id: ths.attr('no'),
            ldt: ths.attr('ldt'),
        };

        ajxvar['loadinfo'] = $.ajax({
            type: 'POST',
            url: '',
            data: data,
            async: true,
            dataType: 'json',
            beforeSend: function() {
                if (ajxvar['loadinfo'] !== null && ajxvar['loadinfo'] !== undefined) {
                    ajxvar['loadinfo'].abort();
                }
                if (ajxvar['grlive'] != undefined) {
                    ajxvar['grlive'].abort();
                }
                if (ajxclrtm['grlive'] != undefined) {
                    clearTimeout(ajxclrtm['grlive']);
                }
            },
            success: function(data) {}
        }).done(function(data) {
            if (data[0].name == undefined) {
                say($(".gphrases > .prfnoexists").text());
                $(".swr-grupo .rside > .tabs > ul > li").eq(0).trigger("click");
            } else {
                $(".swr-grupo .aside > .content .profile > .top > span.name").text(htmlDecode(data[0].name));
                $(".swr-grupo .aside > .content .profile > .top > span.dp > img").remove();
                $(".swr-grupo .aside > .content .profile > .top > span.dp").prepend("<img class=lazyimg>");
                $(".swr-grupo .aside > .content .profile > .top > span.dp > img.lazyimg").attr("data-src", data[0].img);
                $(".swr-grupo .aside > .content .profile > .top > span.coverpic > img").remove();
                $(".swr-grupo .aside > .content .profile > .top > span.coverpic").prepend("<img class=lazyimg>");
                $(".swr-grupo .aside > .content .profile > .top > span.coverpic > img").attr("data-src", data[0].cp);
                $(".swr-grupo .aside > .content .profile > .top > span.coverpic").show();
                $(".swr-grupo .aside > .content .profile > .top > span.edit > span").show();
                if (data[0].cp == $(".dumb .gdefaults > .baseurl").text()+"gem/ore/grupo/coverpic/users/default.png" || data[0].cp == $(".dumb .gdefaults > .baseurl").text()+"gem/ore/grupo/coverpic/groups/default.png") {
                    $(".swr-grupo .aside > .content .profile > .top > span.edit > span").hide();
                    $(".swr-grupo .aside > .content .profile > .top > span.coverpic").hide();
                }
                $(".swr-grupo .aside > .content .profile > .top > span.role").text(htmlDecode(data[0].uname));
                $(".swr-grupo .aside > .content .profile > .top > span.refresh").attr("no", data[0].id);
                $(".swr-grupo .aside > .content .profile > .middle > span.pm").attr("no", data[0].id);
                $(".swr-grupo .aside > .content .profile > .top > span.edit > i").attr("xtid", data[0].id);
                $(".swr-grupo .aside > .content .profile > .top > span.edit > i").attr("data-no", data[0].id);
                $(".swr-grupo .aside > .content .profile > .middle > span.stats > span").eq(1).find("span").text(htmlDecode(data[0].shares));
                $(".swr-grupo .aside > .content .profile > .middle > span.stats > span").eq(0).find("span").text(htmlDecode(data[0].loves));
                $(".swr-grupo .aside > .content .profile > .middle > span.pm").text(htmlDecode(data[0].btn));
                $(".swr-grupo .aside > .content .profile > .middle > span.stats > span").eq(2).find("span").text(htmlDecode(data[0].lastlg));
                $(".swr-grupo .aside > .content .profile > .middle > span.stats > span").eq(0).find("i").text(htmlDecode(data[0].mna));
                $(".swr-grupo .aside > .content .profile > .middle > span.stats > span").eq(1).find("i").text(htmlDecode(data[0].mnb));
                $(".swr-grupo .aside > .content .profile > .middle > span.stats > span").eq(2).find("i").text(htmlDecode(data[0].mnc));
                $(".swr-grupo .aside > .content .profile > .middle > span.stats > span").eq(2).find("span").attr("data-toggle", "tooltip");
                $(".swr-grupo .aside > .content .profile > .middle > span.stats > span").eq(2).find("span").attr("title", data[0].lastlgtm);
                if (data[0].edit == 1) {
                    $(".swr-grupo .aside > .content .profile > .middle > span.stats > b > i").removeClass();
                    $(".swr-grupo .aside > .content .profile > .middle > span.stats > b > i").addClass('gi-pencil-1').show();
                } else if (data[0].icon == 1) {
                    $(".swr-grupo .aside > .content .profile > .middle > span.stats > b > i").removeAttr('pn');
                    $(".swr-grupo .aside > .content .profile > .middle > span.stats > b > i").removeAttr('no');
                    $(".swr-grupo .aside > .content .profile > .middle > span.stats > b > i").removeAttr('data-no');
                    $(".swr-grupo .aside > .content .profile > .middle > span.stats > b > i").removeAttr('xtid');
                    $(".swr-grupo .aside > .content .profile > .middle > span.stats > b > i").removeClass();
                    $(".swr-grupo .aside > .content .profile > .middle > span.stats > b > i").addClass(data[0].iconclass).show();
                    $(".swr-grupo .aside > .content .profile > .middle > span.stats > b > i").attr(data[0].iconattr);
                    $(".swr-grupo .aside > .content .profile > .middle > span.stats > b").attr("data-toggle", "tooltip");
                    $(".swr-grupo .aside > .content .profile > .middle > span.stats > b").attr('data-original-title', data[0].icontitle).tooltip('update');
                } else {
                    $(".swr-grupo .aside > .content .profile > .middle > span.stats > b > i").removeClass();
                    $(".swr-grupo .aside > .content .profile > .middle > span.stats > b > i").hide();
                }
                $(".swr-grupo .aside > .content .profile > .middle > span.pm").remove();
                $(".swr-grupo .aside > .content .profile > .middle").prepend("<span class="+'"pm"'+" "+data[0].tbattr+">"+htmlDecode(data[0].btn)+"</span>");

                $(".swr-grupo .aside > .content .profile > .top > span.roleimg > img").remove();
                if (ths.attr('ldt') != 'group') {
                    $(".swr-grupo .aside > .content .profile").removeClass("vgroup");
                    $(".swr-grupo .aside > .content .profile > .top > span.roleimg").prepend("<img class=roleimg>");
                    $(".swr-grupo .aside > .content .profile > .top > span.roleimg > img").attr("src", data[0].roleimg);
                    $(".swr-grupo .aside > .content .profile > .top > span.roleimg > img").attr("data-toggle", "tooltip");
                    $(".swr-grupo .aside > .content .profile > .top > span.roleimg > img").attr("title", data[0].rolename);
                } else {
                    $(".swr-grupo .aside > .content .profile").addClass("vgroup");
                }
                $(".swr-grupo .aside > .content .profile > .middle > span.pm").addClass(data[0].tbclass);
                $(".swr-grupo .aside > .content .profile > .bottom > div > ul").html("");
                var pbtm = $(".swr-grupo .aside > .content .profile > .bottom > div > ul");
                var pnent = $(".swr-grupo .aside > .content .profile > .bottom > div > div");
                if (data.length === 1) {
                    pbtm.hide(); pnent.show();
                } else {
                    pnent.hide(); pbtm.show();
                    $.each(data, function(k, v) {
                        if (k != 0) {
                            if (k == 'embedcode') {
                                data[k].cont = '<iframe width="411px" height="650px" allow="camera;microphone" src="'+data[k].cont+'" frameborder=0 allowfullscreen></iframe>';
                                pbtm.append("<li><b>"+htmlDecode(data[k].name)+"</b><span><span class='selectable'>"+htmlencode(data[k].cont)+"</span></span></li>");
                            } else if (k == 'viewlink') {
                                pbtm.append("<li><b>"+htmlDecode(data[k].name)+"</b><span><span class='selectable sluglink'>"+htmlencode(data[k].cont)+"</span></span></li>");
                            } else {
                                pbtm.append("<li><b>"+htmlDecode(data[k].name)+"</b><span>"+convertphonenum(url2link(htmlDecode(data[k].cont)))+"</span></li>");
                            }
                        }
                    });
                }
                if (ths.attr('ldt') == 'group' && data[0].sharedmedia != 0 && data[0].sharedmedia != '') {
                    var sharedmedia = data[0].sharedmedia.split(';');
                    var sharedmediacnt = '<li><b>'+htmlDecode(data[0].sharedmediatitle)+'</b><div class="groupimages" id="groupimages"> <ul>';
                    $.each(sharedmedia, function(shared, media) {
                        var mediaimg = media.split('||');
                        sharedmediacnt = sharedmediacnt+'<li><img class=lazyimg data-src="'+mediaimg[0]+'" data-original="'+mediaimg[1]+'"></li>';
                    });
                    sharedmediacnt = sharedmediacnt+'</ul></div></li>';
                    pbtm.append(sharedmediacnt);
                    grregisterrecentimages();
                }
                $(".swr-grupo .rside > .content .profile").fadeIn();
            }
            $(".swr-grupo .rside .listloader").fadeOut(); $("[data-toggle=tooltip]").data("bs.tooltip", false).tooltip();
            ajxclrtm["grlivesetin"] = setTimeout(function() {
                gr_live();
            }, 2000);
        }) .fail(function(qXHR, textStatus, errorThrown) {
            $(".swr-grupo .rside .listloader").addClass("error");
            ajxclrtm["grlivesetin"] = setTimeout(function() {
                gr_live();
            }, 2000);
        });
    }, 300);
});

function grtabfold(fold) {
    if (fold == undefined) {
        fold = 'left';
    }
    if (actcustomscroll == 1) {
        $(".swr-grupo .panel > .room > .msgs,.swr-grupo .aside > .content > .list,.swr-grupo .aside > .content .profile > .bottom > div > ul").getNiceScroll().remove();
        $(".swr-grupo .panel > .room > .msgs,.swr-grupo .aside > .content > .list,.swr-grupo .aside > .content .profile > .bottom > div > ul").css("overflow", "hidden");
    }
    if (fold == 'left') {
        if ($('.rside').hasClass('tabfold')) {
            $('.rside').removeClass('tabfold');
            $('.lside').addClass('tabfold');
            $('.swr-grupo .panel > .head > .icon.tabclose').removeClass('d-none');
        }
    } else {
        if (!$('.rside').hasClass('tabfold')) {
            $('.swr-grupo .panel > .head > .icon.tabclose').addClass('d-none');
            $('.lside').removeClass('tabfold');
            $('.rside').addClass('tabfold');
        }
    }
    setTimeout(function() {
        grscroll($(".swr-grupo .panel > .room > .msgs"), 'scroll');
        if (fold == 'left') {
            grscroll($(".swr-grupo .rside > .content > .list"), 'scroll');
            grscroll($(".swr-grupo .aside > .content .profile > .bottom > div > ul"));
        } else {
            grscroll($(".swr-grupo .lside > .content > .list"), 'scroll');
        }
    }, 510);
}
$('body').on('click', '.swr-grupo .tabclose', function(e) {
    grtabfold($(this).attr('side'));
});

function grregisterrecentimages() {
    var galley = document.getElementById('groupimages');
    var viewer = new Viewer(galley, {
        url: 'data-original',
    });
}
function linkify(inputText) {
    var replacedText, replacePattern1, replacePattern2, replacePattern3;
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank"><i class="gi-link"></i>$1</a>');
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');
    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');
    return replacedText;
}

$('body').on('keyup', '.grupo-pop > div > form > div > div > textarea', function(e) {
    textAreaAdjust($(this), 300, 100);
    grscroll($(".grupo-pop > div > form > div"), "resize");
});

function gralltxtareaajust() {
    $(".grupo-pop > div > form > div textarea").each(function() {
        textAreaAdjust($(this), 1300);
    });
}

function textAreaAdjust(o, m, k) {
    if (k == undefined) {
        o.css("height", "auto");
    }
    var hgh = o.prop('scrollHeight');
    o.css("height", (hgh)+"px");
}
$('body').on('click', '.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.block.dwnldfile', function(e) {
    $(this).parents('li').find('.gr-download').trigger("click");
});
$('body').on('click', '.swr-grupo .panel > .room > .msgs > li > div > .msg .gr-like', function(e) {
    var id = $(this).parents('li').attr('no');
    $(this).attr('type', 'json');
    $(this).attr('spin', 'off');
    var data = {
        act: 1,
        do: "love",
        type: 'lovedit',
        id: id,
    };
    var s = 'if(data[0].do=="remove"){$(".swr-grupo .panel > .room > .msgs > li[no="+data[0].id+"]").remove();}';
    s = s+'else if(data[0].do=="like"){$(".swr-grupo .panel > .room > .msgs > li[no="+data[0].id+"]").find(".gr-like").addClass("liked");}';
    s = s+'else{$(".swr-grupo .panel > .room > .msgs > li[no="+data[0].id+"]").find(".gr-like").removeClass("liked");}';
    s = s+'if(data[0].count==0){data[0].count="";}else{data[0].count="<i>"+data[0].count+"</i>";}';
    s = s+'$(".swr-grupo .panel > .room > .msgs > li[no="+data[0].id+"]").find(".lcount").html(data[0].count);';
    var f = grlv = 'ajxclrtm["grlivesetin"]=setTimeout(function() { gr_live(); }, 2000);';
    s = s+grlv;
    ajxx($(this), data, s, e, f, 'likemsgs', 'grlive');
});

$(".grdrag").draggable({
    containment: 'window',
    handle: ".prmove"
});

$('body').on('click', ".swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.preview.image > span > img", function(e) {
    if ($(this).hasClass('tenor')) {
        var url = $(this).attr("gif");
    } else {
        var url = $(this).attr("src");
        url = url.replace('/files/preview/', '/files/dumb/');
    }
    grpreview('img', url);
});
$('body').on('click', ".swr-grupo .aside > .content .profile.vgroup > .middle > span.stats > span:nth-child(2)", function(e) {
    $(".swr-grupo .rside > .tabs > ul > li[act='crew']").trigger('click');
});
$('body').on('click', ".swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.preview.video", function(e) {
    var url = $(this).find("span").attr("loadvideo");
    var mime = $(this).find("span").attr("mime");
    grpreview('video', url, mime, $(this));
});

function grpreview(ty, url, mime, vd) {
    if (mime == undefined) {
        mime = 0;
    }
    if (vd == undefined) {
        vd = 0;
    }
    $(".grupo-preview > div > div").hide();
    $('.gr-prvlink').hide();
    $('.gr-prvlink > div > img').removeAttr("src");
    var loader = $(".grupo-preview > div > .loader");
    var elm = $(".grupo-preview > div > .img");
    $(".grupo-preview > div > .img > div > img").remove();
    $(".grupo-preview > div > .video > div > video > source").remove();
    $(".grupo-preview > div > .embed > div > iframe").remove();
    if (ty == 'img') {
        var image = new Image();
        image.src = url;
        var viewer = new Viewer(image, {
            title: 0,
            navbar: 0,
            toolbar: {
                zoomIn: {
                    show: 1,
                    size: 'large',
                },
                zoomOut: {
                    show: 1,
                    size: 'large',
                },
                oneToOne: 0,
                play: 0,
                prev: 0,
                next: 0,
                rotateLeft: {
                    show: 1,
                    size: 'large',
                },
                reset: {
                    show: 1,
                    size: 'large',
                },
                rotateRight: {
                    show: 1,
                    size: 'large',
                },
                flipHorizontal: {
                    show: 1,
                    size: 'large',
                },
                flipVertical: {
                    show: 1,
                    size: 'large',
                },
            },
            hidden: function () {
                viewer.destroy();
            },
        });
        viewer.show();
    } else if (ty == 'video') {
        var vdo = $(".grupo-preview > div > .video");
        vdo.find('div > video').append("<source>");
        grcenter(loader);
        loader.fadeIn();
        if ($('.swr-grupo').hasClass('radioenabled')) {
            $("#radioplayerstream")[0].pause();
        }
        vdo.find('div > video > source').attr("src", url);
        vdo.find('div > video > source').attr("type", mime);
        vdo.find('div > video')[0].pause();
        vdo.find('div > video')[0].load();
        vdo.find('div > video').on('loadedmetadata', function() {
            if ($(window).width() <= 767.98) {
                $(".grdrag").draggable("destroy");
                vdo.find('div > video').css("max-width", $(window).width() - 20 + "px");
                vdo.find('div > video').css("max-height", $(window).height() - 20 + "px");
            } else {
                vdo.find('div > video').css("max-width", $(window).width() - 150 + "px");
                vdo.find('div > video').css("max-height", $(window).height() - 150 + "px");
            }
            grcenter(vdo);
            loader.hide();
            vdo.show();
            vdo.find('div > video')[0].play();
        });

    } else if (ty == 'embed') {
        if ($('.swr-grupo').hasClass('radioenabled')) {
            $("#radioplayerstream")[0].pause();
        }
        var vdo = $(".grupo-preview > div > .embed");
        vdo.find('div').append('<iframe src="'+url+'" '+mime+' frameborder="0" allowfullscreen></iframe>');
        grcenter(loader);
        loader.fadeIn();
        vdo.find('div > iframe').on('load', function() {
            if ($(window).width() <= 767.98) {
                vdo.find('div > iframe').css("width", "290px");
                vdo.find('div > iframe').css("height", "166px");
            } else {
                vdo.find('div > iframe').css("width", "520px");
                vdo.find('div > iframe').css("height", "300px");
            }
            grcenter(vdo);
            loader.hide();
            vdo.show();
        });

    }
}
function videothumb(vidtot) {
    var tms = 0;
    for (i = 0; i < vidtot.length; i++) {
        var el = vidtot[i];
        var elem = $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.preview.video.'+el+' > span');
        var link = elem.attr('loadvideo');
        var mime = elem.attr('mime');
        tms = tms+4000;
        var video = document.getElementById('videothumbgen');
        var nid = rand(10);
        var thecanvas = document.getElementById('videothumbgencanvas');
        $("#videothumbgen").html("<source></source>");
        $("#videothumbgen > source").attr("src", link);
        $("#videothumbgen > source").attr("type", mime);
        $("#videothumbgen").on("loadstart", function() {
            var context = thecanvas.getContext('2d');
            context.drawImage(video, 0, 0, thecanvas.width, thecanvas.height);
            var dataURL = thecanvas.toDataURL("image/png");
            $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.preview.video.'+el+' > span > img').attr('src', dataURL);

        });
    }
}
function grcenter(el) {
    el.css("top", Math.max(0, (($(window).height() - el.outerHeight()) / 2) + $(window).scrollTop()) + "px");
    el.css("left", Math.max(0, (($(window).width() - el.outerWidth()) / 2) + $(window).scrollLeft()) + "px");
}
$('body').on('click tap touchstart', '.grupo-preview > div .prclose,.goback,.goright', function(e) {
    $(".grupo-preview > div > div").hide();
    $(".grupo-preview > div > .img > div > img").remove();
    if (!$(".grupo-preview > div > .video > div > video")[0].paused) {
        $(".grupo-preview > div > .video > div > video")[0].pause();
    }
    $(".grupo-preview > div > .video > div > video").html("");
    $(".grupo-preview > div > .embed > div > iframe").remove();
    $('.gr-prvlink').hide();
    if (webthumbnail != null) {
        webthumbnail.abort();
    }
    $('.gr-prvlink > div > img').removeAttr("src");
});

$('body').on('click', '.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay > span.play', function(e) {
    var pr = $(this).parent();
    if ($(this).hasClass('pause')) {
        $(this).removeClass('pause');
        $(this).addClass('continue');
        $("#graudio")[0].pause();
    } else if ($(this).hasClass('continue')) {
        $(this).removeClass('continue');
        $(this).addClass('pause');
        if ($('.swr-grupo').hasClass('radioenabled')) {
            $("#radioplayerstream")[0].pause();
        }
        $("#graudio")[0].play();
    } else {
        $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay > span.play').removeClass('pause');
        $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay > span.seek > i.duration').css('opacity', 0);
        $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay > span.play').removeClass('continue');
        $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay').removeClass('current');
        $(this).parent().addClass('current');
        $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay > span.seek > i.bar').css('padding-left', '0px');
        var source = $(this).parent().attr("play");
        var mime = $(this).parent().attr("mime");
        $(this).addClass('pause');
        $("#graudio > source").attr("src", source);
        $("#graudio > source").attr("type", mime);
        if ($('.swr-grupo').hasClass('radioenabled')) {
            $("#radioplayerstream")[0].pause();
        }
        $("#graudio")[0].pause();
        $("#graudio")[0].load();
        $("#graudio")[0].play();
        $(this).parent().find('.duration').css('opacity', 1);
    }
});

var graudprv = $("#graudio").bind("timeupdate", function() {
    var elemt = $(".swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay.current");
    var widthOfProgressBar = Math.floor((100 / this.duration) * this.currentTime);
    var updtme = seconvert(this.duration);
    elemt.find('.duration > i.tot').text(updtme);
    elemt.find('.duration > i:first-child').text(seconvert(this.currentTime));
    $(".swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay.current #seekslider").val(graudprv.currentTime/graudprv.duration);
    $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay.current').find('span.seek > i.bar').stop(true, true).css('padding-left', widthOfProgressBar+'%');
})[0];
$("#graudio").bind("ended", function() {
    $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay > span.seek > i.duration').css('opacity', 0);
    $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay > span.play').removeClass('pause');
    $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay > span.play').removeClass('continue');
    $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay').removeClass('current');
    setTimeout(function() {
        $('.swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay > span.seek > i.bar').css('padding-left', '0%');
    }, 300);
});
$("body").on("input", ".swr-grupo .panel > .room > .msgs > li > div > .msg > i > span.audioplay #seekslider", function(e) {
    if ($(this).parent().parent().hasClass('current')) {
        graudprv.currentTime = this.value * graudprv.duration;
    } else {
        $olv = this.value;
        $(this).parent().parent().find('span.play').trigger('click');
        setTimeout(function() {
            graudprv.currentTime = $olv * graudprv.duration;
        }, 200);
    }
});
$("body").on("keyup", ".grupo-pop > div > form > .search > input", function() {

    var search = $(this).val();
    var elem = ".grupo-pop > div > form > div > div > label";
    var elemb = ".grupo-pop > div > form > div > div > input";
    var elemc = ".grupo-pop > div > form > .fields > div > div.checkbox > span";
    $(elem).parent().show();
    if (search != "") {
        search = search.toLowerCase();
        $(elem).parent().hide();
        $(elem).each(function() {
            var str = $(this).text();
            if (str.toLowerCase().indexOf(search) >= 0) {
                $(this).parent().show();
            }
        });
        $(elemb).each(function() {
            var str = $(this).val();
            if (str.toLowerCase().indexOf(search) >= 0) {
                $(this).parent().show();
            }
        });
        $(elemc).each(function() {
            var str = $(this).text();
            if (str.toLowerCase().indexOf(search) >= 0) {
                $(this).parent().parent().show();
            }
        });
        $(".grupo-pop > div > form > div").animate({
            scrollTop: 0
        }, 500);
    }
});

$('body').on('click', '.swr-grupo .panel > .room > .msgs > li > div > .msg > i > i.readmore', function(e) {
    $(this).hide();
    $(this).parent().find('.shortmsg').hide();
    $(this).parent().find('.moretext').fadeIn();
    grscroll($(".swr-grupo .panel > .room > .msgs"), 'resize');
});

$('.grupo-pop > div > form > div').on('scroll', function(e) {
    lazyload();
    if ($(this).scrollTop() > 200 && $(this).scrollTop() < 230) {
        grscroll($('.grupo-pop > div > form > div'), 'resize');
    }
});
function seconvert(s) {
    var h = Math.floor(s/3600);
    var tms = "";
    s -= h*3600;
    var m = Math.floor(s/60);
    s -= m*60;
    s = Math.floor(s);
    if (h != 0) {
        tms = h+":"+(m < 10 ? '0'+m: m)+":"+(s < 10 ? '0'+s: s);
    } else {
        tms = (m < 10 ? '0'+m: m)+":"+(s < 10 ? '0'+s: s);
    }
    if (tms == 'NaN:NaN:NaN') {
        tms = "00:00";
    }
    return tms;
}
function gradur(el) {
    setTimeout(function() {
        $(el).parent().find('.duration > i:last-child').text();
        $(el).parent().find('.duration > i:first-child').text();
        $(el).parent().find('.seek > i.bar > i').css('margin-left', '%');
        gradur(el);
    }, 1000);
}
function nformat(value) {
    var newValue = value;
    if (value >= 1000) {
        var suffixes = ["", "K", "M", "B", "T"];
        var suffixNum = Math.floor((""+value).length/3);
        var shortValue = '';
        for (var precision = 2; precision >= 1; precision--) {
            shortValue = parseFloat((suffixNum != 0 ? (value / Math.pow(1000, suffixNum)): value).toPrecision(precision));
            var dotLessShortValue = (shortValue + '').replace(/[^a-zA-Z 0-9]+/g, '');
            if (dotLessShortValue.length <= 2) {
                break;
            }
        }
        if (shortValue % 1 != 0)  shortValue = shortValue.toFixed(1);
        newValue = shortValue+suffixes[suffixNum];
    }
    return newValue;
}
function gremojibuild(d, nl) {
    if (nl == undefined) {
        nl = 0;
    }
    if (d == undefined) {
        if (emojione != undefined) {
            $(".swr-grupo .panel > .room > .msgs > li.emjbuild").each(function() {
                if (!$(this).hasClass('emjdone')) {
                    var txt = $(this).find(' div > .msg > i');
                    txt.html(emojione.shortnameToImage(asciiemoji(txt.html())));
                    $(this).addClass('emjdone').removeClass('emjbuild');
                }
            });
        }
    } else {
        if (nl == 0) {
            return d;
        } else {
            return emojione.shortnameToImage(asciiemoji(d));
        }
    }
}
function shwrdmre(str, len, nl, ej) {
    if (len == undefined) {
        len = 0;
    }
    if (nl == undefined) {
        nl = 0;
    }
    if (ej == undefined) {
        ej = 0;
    }
    $shrt = escapeHtml(str);
    var maxLength = $(".dumb .gdefaults").find(".rdmre").text();
    if (len != 0) {
        maxLength = len;
    }
    if (maxLength !== "") {
        if ($.trim(str).length > maxLength) {
            var div = document.createElement("div");
            div.innerHTML = str;
            $shrt = div.innerText;
            var newStr = $shrt.substring(0, maxLength);
            var removedStr = str.substring(maxLength, $.trim(str).length);
            $shrt = newStr;
            if (len == 0) {
                $shrt = $shrt.replace(/(?:\r\n|\r|\n)/g, '<br> ');
                $shrt = '<i class="shortmsg">'+gremojibuild($shrt, ej)+'</i><i class="readmore">'+$(".gphrases > .readmore").text()+'</i>';
                $shrt = $shrt+'<i class="moretext">' + gremojibuild(convertphonenum(url2link(str, nl), nl), ej) + '</i>';
            }
        } else {
            $shrt = gremojibuild(convertphonenum(url2link(str, nl), nl), ej);
        }
    } else {
        $shrt = gremojibuild(convertphonenum(url2link(str, nl), nl), ej);
    }
    return $shrt;
}