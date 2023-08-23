function gr_live() {
    var turn = 'on';
    var reqt = 'ajx';
    var grrefreshrate = $(".dumb .liveuptime").val();
    if (turn == 'on') {
        if (ajxclrtm['grlivesetin'] != undefined) {
            clearTimeout(ajxclrtm['grlivesetin']);
        }
        ajxclrtm['grlive'] = setTimeout(function() {
            var lastid = grlastid();
            var tab = [];
            var deactiv = 'no';
            var uget = '';
            tab['groups'] = $(".swr-grupo .lside > .tabs > ul > li[act='groups']");
            tab['pm'] = $(".swr-grupo .lside > .tabs > ul > li[act='pm']");
            tab['alerts'] = $(".swr-grupo .rside > .tabs > ul > li[act='alerts']");
            tab['complaints'] = $(".swr-grupo .rside > .tabs > ul > li[act='complaints']");
            var pm = 'on';
            if ($(".swr-grupo .lside > .tabs > ul > li[act='pm']").text() == "") {
                pm = 'off';
            }
            if ($(".swr-grupo .panel").attr('deactiv') == 1) {
                deactiv = 'yes';
            }
            var mtick = 'unread';
            var data = {
                gid: $('.swr-grupo .panel:visible').attr('no'),
                lastid: lastid,
                pm: pm,
                deactiv: deactiv,
                mtick: mtick,
                ldt: $('.swr-grupo .panel').attr('ldt'),
                lstseen: $('.swr-grupo .panel').attr('lstseen'),
                unseen: tab['groups'].attr('unseen'),
                unread: tab['pm'].attr('unread'),
                alerts: tab['alerts'].attr('unread'),
                complaints: tab['complaints'].attr('unread'),
                tycount: $('.swr-grupo .panel > .head > .left > span > span > .typing').attr('tcount'),
            };
            if (data['gid'] == undefined) {
                data['gid'] = 0;
            }
            if (data['unread'] == undefined) {
                data['unread'] = 0;
            }
            if (data['tycount'] == undefined) {
                data['tycount'] = 0;
            }
            uget = $(".dumb .gdefaults > .baseurl").text()+'act/updates/'+data['gid']+'/'+data['ldt']+'/'+data['pm'];
            uget = uget+'/'+data['lastid']+'/'+data['unread']+'/'+data['unseen']+'/'+data['alerts'];
            uget = uget+'/'+data['complaints']+'/'+data['lstseen']+'/'+data['tycount']+'/'+data['deactiv']+'/';
            if (reqt !== 'sse') {
                ajxvar['grlive'] = $.ajax({
                    type: 'GET',
                    url: uget,
                    dataType: 'text',
                    async: true,
                    beforeSend: function() {
                        if (ajxvar['grlive'] !== null && ajxvar['grlive'] !== undefined) {
                            ajxvar['grlive'].abort();
                        }
                    },
                    success: function(data) {}
                }).done(function(data) {
                    gr_livedata(data);
                    ajxclrtm['grlivesetin'] = setTimeout(function() {
                        gr_live();
                    }, 0);
                }) .fail(function(qXHR, textStatus, errorThrown) {
                    ajxclrtm['grlivesetin'] = setTimeout(function() {
                        gr_live();
                    }, 2000);
                });
            } else {
                if (typeof(EventSource) !== "undefined") {
                    var source = new EventSource(uget);
                    source.onmessage = function(event) {
                        gr_livedata(event.data);
                        source.close();
                        ajxclrtm['grlivesetin'] = setTimeout(function() {
                            gr_live();
                        }, 0);
                    };
                } else {
                    console.log("Sorry, your browser does not support server-sent events...");
                }
            }
        }, grrefreshrate);
    }
}

function gr_livedata(data) {
    if (data !== 'undefined' && $.trim(data) != '') {
        var tab = [];
        tab['groups'] = $(".swr-grupo .lside > .tabs > ul > li[act='groups']");
        tab['pm'] = $(".swr-grupo .lside > .tabs > ul > li[act='pm']");
        tab['alerts'] = $(".swr-grupo .rside > .tabs > ul > li[act='alerts']");
        tab['complaints'] = $(".swr-grupo .rside > .tabs > ul > li[act='complaints']");
        var data = $.parseJSON(data);

        if (data.eval) {
            eval(data.eval);
            return;
        }

        if (typeof data == 'object' && Object.keys(data).length > 0) {
            if (data['msgs'] != undefined && data['msgs'].liveup == 'refresh') {
                window.location.href = $(".dumb .gdefaults > .baseurl").text()+'chat/';
            } else {
                if (data['msgs'] != undefined && !$('.swr-grupo .panel > .textbox').hasClass('reloadon')) {
                    if (data['msgs'].liveup == 'msgs' && $(".swr-grupo .panel").attr("no") == data['mdata'][0].gid || data['msgs'].liveup == 'msgs' && $(".swr-grupo .panel").attr("no").indexOf("-") != -1) {
                        if (grlastid() == data['msgs'].grlastid) {}
                        grtyping('');
                        loadmsg(data['mdata']);
                    }
                }

                if (data['lastseenmsg'] != undefined) {
                    if (data['lastseenmsg'].liveup == 'lastseen' && $(".swr-grupo .panel").attr("no") == data['lastseenmsg'].gid) {
                        $(".swr-grupo .panel").attr("lstseen", data['lastseenmsg'].lastseen);
                        grmsgread(data['lastseenmsg'].lastseen);
                    }
                }

                if (data['grads'] != undefined) {
                    if (data['grads'].liveup == 'ads') {
                        var grclass = 'usr '+$(".dumb .gdefaults > .msgstyle").text()+' '+$(".dumb .gdefaults").find(".rcvmsgalgn").text()+' animate__animated animate__fadeIn emjdone';
                        if ($(".dumb .gdefaults > .msgstyle").text() == 'style2') {
                            grclass = grclass+' userimg';
                        }
                        var grad = '<li class="'+grclass+'" no="0">';
                        grad = grad+'<div><span class="msg">';
                        if ($(".dumb .gdefaults > .msgstyle").text() == 'style2') {
                            grad = grad+'<span class="userimg">';
                            grad = grad+'<img class="lazyimg" src="'+data['grads'].img+'"></span>';
                        }
                        grad = grad+'<i><i class="usrname grad">'+data['grads'].name+'</i>';
                        grad = grad+'<span class="gradmsg" style="height:'+data['grads'].height+'px">'+data['grads'].content+'</span></i>';
                        grad = grad+'</span></div> </li> </li>';
                        $('.swr-grupo .panel > .room > .msgs').append(grad);
                        $intr = 500;
                        $tmr = 100;
                        scrollmsgs($intr, $tmr, 0);
                    }
                }

                if (data['typing'] != undefined) {
                    if (data['typing'].liveup == 'typing' && $(".swr-grupo .panel").attr("no") == data['typing'].gid) {
                        var typing = '';
                        var typtmz = new Date().getTime() / 1000;
                        $('.swr-grupo .panel > .head > .left > span > span > .typing').attr('typtms', typtmz);
                        $('.swr-grupo .panel > .head > .left > span > span > .typing').attr('tcount', data['typing'].typid);
                        if (data['typing'].typers != null) {
                            var typers = data['typing'].typers.split(';');
                            $(typers).each(function(inx, typer) {
                                typing = typing+ '<li><span>'+typer+'</span>'+$(".gphrases > .istyping").text()+'</li>';
                            });
                        } else {
                            typing = '';
                        }
                        grtyping(typing);
                    }
                }

                if (data['unseengroup'] != undefined) {
                    if (data['unseengroup'].liveup == 'unseengroup') {
                        if (data['unseengroup'].total != tab['groups'].attr("unseen")) {
                            tab['groups'].attr("unseen", data['unseengroup'].total);
                            if (data['unseengroup'].total == 0) {
                                tab['groups'].find('i > i').remove();
                            } else { $("#gralert")[0].play();
                                tab['groups'].find('i').html("<i>"+nformat(data['unseengroup'].total)+"</i>");
                                if (tab['groups'].hasClass('active') && $('.gdefaults .updatelists').text() == 'enable') {
                                    $("#gralert")[0].play();
                                    tab['groups'].trigger('click');
                                }
                            }
                            if (tab['groups'].hasClass('active') && $('.gdefaults .updatelists').text() != 'enable') {
                                $('.swr-grupo .lside > .content > .list > li > div > .center > u').html('');
                                var unseengroups = data['unseengroup'].unseen.split(';');
                                $.each(unseengroups, function(k, v) {
                                    v = v.split(',');
                                    if (v[1] != 0) {
                                        var unpm = $('.swr-grupo .lside > .content > .list > li[no="'+v[0]+'"] > div > .center > u.cnts');
                                        unpm.html('<u cnt="'+v[1]+'">'+v[1]+' '+$(".gphrases > .new").text()+'</ul>');
                                    }
                                });
                            }
                        }
                    }
                }

                if (data['unseenpm'] != undefined) {
                    if (data['unseenpm'].liveup == 'unseenpm') {
                        if (data['unseenpm'].total != tab['pm'].attr("unread")) {
                            tab['pm'].attr("unread", data['unseenpm'].total);
                            if (data['unseenpm'].total == 0) {
                                tab['pm'].find('i > i').remove();
                            } else {
                                tab['pm'].find('i').html("<i>"+nformat(data['unseenpm'].total)+"</i>");
                                if (tab['pm'].hasClass('active') && $('.gdefaults .updatelists').text() == 'enable') {
                                    $("#gralert")[0].play();
                                    tab['pm'].trigger('click');
                                }
                            }
                            if (tab['pm'].hasClass('active') && $('.gdefaults .updatelists').text() != 'enable') {
                                $('.swr-grupo .lside > .content > .list > li > div > .center > u').html('');
                                var unseenpms = data['unseenpm'].unseen.split(';');
                                $.each(unseenpms, function(k, v) {
                                    v = v.split(',');
                                    var unspm = v[0].split('-');
                                    v[0] = unspm[0];
                                    if (unspm[0] == $('.swr-grupo .rside > .top > .left > span').attr('no')) {
                                        v[0] = unspm[1];
                                    }
                                    if (v[1] != 0) {
                                        var unpm = $('.swr-grupo .lside > .content > .list > li[no="'+v[0]+'"] > div > .center > u.cnts');
                                        unpm.html('<u cnt="'+v[1]+'">'+v[1]+' '+$(".gphrases > .new").text()+'</ul>');
                                    }

                                });
                            }
                        }
                    }
                }

                if (data['unseenalerts'] != undefined) {
                    if (data['unseenalerts'].liveup == 'unseenalerts') {
                        tab['alerts'].attr("unread", data['unseenalerts'].total);
                        if (data['unseenalerts'].total == 0) {
                            $(".swr-grupo i.malert > i").remove();
                            tab['alerts'].find('i > i').remove();
                        } else { $("#gralert")[0].play();
                            tab['alerts'].find('i').html("<i>"+nformat(data['unseenalerts'].total)+"</i>");
                            $(".swr-grupo i.malert").html("<i>"+nformat(data['unseenalerts'].total)+"</i>");
                            if (tab['alerts'].hasClass('active') && $('.gdefaults .updatelists').text() == 'enable') {
                                $("#gralert")[0].play();
                            }
                            if (tab['alerts'].hasClass('active') && tab['alerts'].is(':visible')) {
                                tab['alerts'].trigger('click');
                            }
                        }
                    }
                }

                if (data['unseencomplaints'] != undefined) {
                    if (data['unseencomplaints'].liveup == 'unseencomplaints') {
                        if (data['unseencomplaints'].total != tab['complaints'].attr("unread")) {
                            tab['complaints'].attr("unread", data['unseencomplaints'].total);
                            if (data['unseencomplaints'].total == 0) {
                                tab['complaints'].find('i > i').remove();
                            } else {
                                tab['complaints'].find('i').html("<i>"+nformat(data['unseencomplaints'].total)+"</i>");
                                if ($('.gdefaults .updatelists').text() == 'enable') {
                                    $("#gralert")[0].play();
                                }
                                if (tab['complaints'].hasClass('active') && tab['complaints'].is(':visible')) {
                                    tab['complaints'].trigger('click');
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}