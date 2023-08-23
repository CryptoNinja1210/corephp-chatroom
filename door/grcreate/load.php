<?php if(!defined('s7V9pz')) {die();}?><?php
function gr_create() {
    $uid = $GLOBALS["user"]['id'];
    $arg = func_get_args();
    if ($arg[0] === 'group') {
        if (!gr_role('access', 'groups', '1')) {
            exit;
        }
        if (!empty($arg[1]['name'])) {
            $passw = 0;
            if (empty($arg[1]['password'])) {
                $passw = $arg[1]['password'] = 0;
            } else if (isset($GLOBALS["roles"]['groups'][15])) {
                $passw = md5($arg[1]['password']);
            }
            $cr = db('Grupo', 's,count(*)', 'options', 'type,v1', 'group', mb_strtolower($arg[1]['name']))[0][0];
            if ($cr == 0) {
                $ncode = $code = rn(6).rn(4);;
                if (isset($_FILES['img']['name']) && !empty($_FILES['img']['name'])) {
                    $ext = pathinfo($_FILES['img']['name'])['extension'];
                    $ncode = $code.'.'.$ext;
                }

                //edit for paid channel
                if (!empty($arg[1]['visibility']) && isset($GLOBALS["roles"]['groups'][14])) {
                    if ($arg[1]['visibility']==2)
                        $arg[1]['visibility'] = 'paid';
                    else
                        $arg[1]['visibility'] = 'secret';
                    
                }
                //end

                if (!empty($arg[1]['sendperm'])) {
                    $arg[1]['sendperm'] = 'adminonly';
                }
                if (!empty($arg[1]['unleavable']) && isset($GLOBALS["roles"]['groups'][13])) {
                    $arg[1]['unleavable'] = 'unleavable';
                }
                if (!empty($arg[1]['slug'])) {
                    $arg[1]['slug'] = preg_replace('/[^\pL\pM*+\pN._%+-]/u', '', $arg[1]['slug']);
                    $arg[1]['slug'] = mb_strtolower($arg[1]['slug']);
                }
                if (!empty($arg[1]['slug'])) {
                    if (!preg_match('/[A-Za-z]/', $arg[1]['slug']) && !preg_match('/[^\\p{Common}\\p{Latin}]/u', $arg[1]['slug'])) {
                        gr_prnt('say("'.$GLOBALS["lang"]->slug_condition.'");'); exit;
                    }
                    $checkusername = db('Grupo', 's', 'users', 'name', $arg[1]["slug"]);
                    $checkslug = db('Grupo', 's', 'options', 'type,v2', 'groupslug', $arg[1]["slug"]);
                    if (count($checkusername) > 0 || count($checkslug) > 0 && in_array($arg[1]['slug'], $GLOBALS["reservedslugs"])) {
                        gr_prnt('say("'.$GLOBALS["lang"]->slug_already_exists.'");'); exit;
                    }
                }
                $fields = db('Grupo', 's', 'profiles', 'type', 'gfield');
                foreach ($fields as $f) {
                    $pf = $f['name'];
                    if ($f['cat'] == 'datefield') {
                        $arg[1][$pf] = vc($arg[1][$pf], 'date', 'Y-m-d');
                    } else if ($f['cat'] == 'numfield') {
                        $arg[1][$pf] = vc($arg[1][$pf], 'num');
                    } else if ($f['cat'] == 'dropdownfield') {
                        $selc = explode(",", $f['v1']);
                        if (!in_array($arg[1][$pf], $selc)) {
                            $arg[1][$pf] = null;
                        }
                    } else {
                        $arg[1][$pf] = $arg[1][$pf];
                    }
                    if (empty($arg[1][$pf]) && $f['req'] == 1 || empty($arg[1][$pf]) && $f['req'] == 3) {
                        gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");'); exit;
                    }
                }
                $credit = 0;
                // edit for paid channel
                if ($arg[1]['visibility'] =='paid' || $arg[1]['visibility'] =='secret') {
                    if (floatval($arg[1]['credits'])>0)
                        $credit = floatval($arg[1]['credits']);
                    else if ($arg[1]['visibility'] =='paid') {
                        $credit = 1;
                    }
                } 
                $r = db('Grupo', 'i', 'options', 'type,v1,v2,v3,v4,v5,v6,tms,v7', 'group', $arg[1]['name'], $passw, $arg[1]['visibility'], rn(6), $arg[1]['sendperm'], $arg[1]['unleavable'], dt(), $credit);

                //end
                    
                if (!empty($arg[1]['slug'])) {
                    if (count($checkusername) == 0 && count($checkslug) == 0 && !in_array($arg[1]['slug'], $GLOBALS["reservedslugs"])) {
                        db('Grupo', 'i', 'options', 'type,v1,v2,tms', 'groupslug', $r, $arg[1]['slug'], dt());
                    }
                }


                if (!empty($arg[1]['description'])) {
                    db('Grupo', 'i', 'profiles', 'type,name,uid,v1,tms', 'group', 'description', $r, $arg[1]['description'], dt());
                }
                $dt = array();
                $dt['id'] = $r;
                $dt['msg'] = 'created_group';
                if (isset($GLOBALS["roles"]['groups'][15])) {
                    $dt['password'] = $arg[1]['password'];
                }
                gr_group('join', $dt, 1);
                gr_group('sendmsg', $dt, 1, 1);
                foreach ($fields as $f) {
                    $pf = $f['name'];
                    if ($f['cat'] == 'datefield') {
                        $arg[1][$pf] = vc($arg[1][$pf], 'date', 'Y-m-d');
                    } else if ($f['cat'] == 'numfield') {
                        $arg[1][$pf] = vc($arg[1][$pf], 'num');
                    } else if ($f['cat'] == 'dropdownfield') {
                        $selc = explode(",", $f['v1']);
                        if (!in_array($arg[1][$pf], $selc)) {
                            $arg[1][$pf] = null;
                        }
                    } else {
                        $arg[1][$pf] = $arg[1][$pf];
                    }
                    if (empty($arg[1][$pf]) && $f['req'] == 1 || empty($arg[1][$pf]) && $f['req'] == 3) {
                        gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");'); exit;
                    } else {
                        db('Grupo', 'i', 'profiles', 'type,name,uid,v1', 'group', $f['id'], $r, $arg[1][$pf]);
                    }
                }
                if (isset($_FILES['img']['name']) && !empty($_FILES['img']['name'])) {
                    $icon = $r.'-gr-'.$code;
                    if (flr('upload', 'img', 'grupo/groups/', $icon, 'jpg,jpeg,png,gif', 1, 1)) {
                        flr('resize', 'grupo/groups/'.$icon.'.'.$ext, 0, 150, 150, 1);
                    }
                }
                if (!empty($_FILES['cpic']['name'])) {
                    $bg = $r.'-gr-'.rn(10);
                    $ext = pathinfo($_FILES['cpic']['name'])['extension'];
                    foreach (glob("gem/ore/grupo/coverpic/groups/".$r."-gr-*.*") as $filename) {
                        unlink($filename);
                    }
                    if (flr('upload', 'cpic', 'grupo/coverpic/groups/', $bg, 'jpg,jpeg,png,gif', 0, 1)) {
                        flr('resize', 'grupo/coverpic/groups/'.$bg.'.'.$ext, 0, 400, 220, 1);
                    }

                }
                gr_prnt('$(".swr-grupo .lside > .tabs > ul > li[act=groups]").attr("list",'.$dt['id'].').trigger("click");say("'.$GLOBALS["lang"]->created.'","s");$(".grupo-pop").fadeOut();');
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->already_exists.'");');
            }
        } else {
            gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
        }
    } else if ($arg[0] === 'menuitem') {
        if (gr_role('access', 'sys', '6')) {
            $arg[1]['ltext'] = vc($arg[1]['ltext'], 'strip');
            $arg[1]['morder'] = vc($arg[1]['morder'], 'num');
            $arg[1]['url'] = vc($arg[1]['url'], 'url');
            if (!empty($arg[1]['ltext']) && !empty($arg[1]['url'])) {
                if (empty($arg[1]['morder'])) {
                    $arg[1]['morder'] = 0;
                }
                $r = db('Grupo', 'i', 'options', 'type,v2,v3,tms', 'menuitem', $arg[1]['url'], $arg[1]['morder'], dt());
                $shrt = "mni_".vc($r, 'num');
                db('Grupo', 'u', 'options', 'v1', 'type,id', $shrt, 'menuitem', $r);
                $dlng = db('Grupo', 's', 'phrases', 'type', 'lang');
                foreach ($dlng as $dl) {
                    db('Grupo', 'i', 'phrases', 'type,short,full,lid', 'phrase', $shrt, $arg[1]['ltext'], $dl['id']);
                    gr_cache('languages', $dl['id']);
                }
                gr_prnt('say("'.$GLOBALS["lang"]->created.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'radiostation') {
        if (gr_role('access', 'features', '14')) {
            $arg[1]['description'] = vc($arg[1]['description'], 'strip');
            $arg[1]['streamlink'] = vc($arg[1]['streamlink'], 'url');
            if (!empty($arg[1]['name']) && !empty($arg[1]['streamlink'])) {
                $r = db('Grupo', 'i', 'options', 'type,#v1,v2,v3,tms', 'radiostation', $arg[1]['name'], $arg[1]['description'], $arg[1]['streamlink'], dt());
                if (isset($_FILES['img']['name']) && !empty($_FILES['img']['name'])) {
                    $ncode = $code = rn(6).rn(4);;
                    $ext = pathinfo($_FILES['img']['name'])['extension'];
                    $ncode = $code.'.'.$ext;
                    $icon = $r.'-gr-'.$code;
                    flr('new', 'grupo/radiostations/');
                    if (flr('upload', 'img', 'grupo/radiostations/', $icon, 'jpg,jpeg,png,gif', 1, 1)) {
                        flr('resize', 'grupo/radiostations/'.$icon.'.'.$ext, 0, 150, 150, 1);
                    }
                }
                gr_prnt('say("'.$GLOBALS["lang"]->created.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'loginprovider') {
        if (gr_role('access', 'sys', '8')) {
            $arg[1]['provider'] = vc($arg[1]['provider'], 'strip');
            if (!empty($arg[1]['provider'])) {
                $r = db('Grupo', 'i', 'options', 'type,v1,#v2,#v3,#v4,tms', 'loginprovider', $arg[1]['provider'], $_POST['appid'], $_POST['appsecretkey'], $_POST['appkey'], dt());
                if (isset($_FILES['img']['name']) && !empty($_FILES['img']['name'])) {
                    $ncode = $code = rn(6).rn(4);
                    $ext = pathinfo($_FILES['img']['name'])['extension'];
                    $ncode = $code.'.'.$ext;
                    $icon = $r.'-gr-'.$code;
                    flr('new', 'grupo/loginprovider/');
                    if (flr('upload', 'img', 'grupo/loginprovider/', $icon, 'jpg,jpeg,png,gif', 1, 1)) {
                        flr('resize', 'grupo/loginprovider/'.$icon.'.'.$ext, 0, 150, 150, 1);
                    }
                }
                gr_prnt('say("'.$GLOBALS["lang"]->created.'","s");menuclick("mmenu","loginproviders","'.$r.'",0);$(".grupo-pop").fadeOut();');
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'stickerpack') {
        if (gr_role('access', 'features', '16')) {
            $arg[1]['name'] = preg_replace('/[^a-z0-9 ]/i', '', $_POST['name']);
            if (!empty($arg[1]['name'])) {
                if (file_exists('gem/ore/grupo/stickers/'.$arg[1]['name'])) {
                    gr_prnt('say("'.$GLOBALS["lang"]->already_exists.'");');
                } else {
                    flr('new', 'grupo/stickers/'.$arg[1]['name']);
                    if (isset($_FILES['img']['name']) && !empty($_FILES['img']['name'])) {
                        $dir = 'grupo/stickers/'.$arg[1]['name'].'/';
                        if (flr('upload', 'img', $dir, 'grstickericon', 'jpg,jpeg,png,gif', 1, 1, 'png', 1)) {
                            if (!is_array(getimagesize('gem/ore/'.$dir.'grstickericon.png'))) {
                                flr('delete', $dir.'grstickericon.png');
                            } else {
                                flr('resize', $dir.'grstickericon.png', 0, 60, 60, 1);
                            }
                        }
                    }
                    gr_prnt('say("'.$GLOBALS["lang"]->created.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
                }
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'stickers') {
        if (gr_role('access', 'features', '16')) {
            $arg[1]['name'] = preg_replace('/[^a-z0-9 ]/i', '', $_POST['name']);
            if (!empty($arg[1]['name'])) {
                if (file_exists('gem/ore/grupo/stickers/'.$arg[1]['name'])) {
                    if (isset($_FILES['img']['name']) && !empty($_FILES['img']['name'])) {
                        $dir = 'grupo/stickers/'.$arg[1]['name'].'/';
                        $fn = rn(6).rn(3).'-gr-';
                        flr('upload', 'multifiles', $dir, $fn);
                    }
                    gr_prnt('say("'.$GLOBALS["lang"]->files_uploaded.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
                }
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'ads') {
        if (gr_role('access', 'sys', '7')) {
            $arg[1]['name'] = vc($arg[1]['name'], 'strip');
            $arg[1]['adslot'] = vc($arg[1]['adslot'], 'strip');
            $arg[1]['adheight'] = vc($arg[1]['adheight'], 'num');
            $arg[1]['adcontent'] = preg_replace(array('/<(\?|\%)\=?(php)?/', '/(\%|\?)>/'), array('', ''), $_POST['adcontent']);
            if (!empty($arg[1]['name']) && !empty($arg[1]['adslot']) && !empty($arg[1]['adheight'])) {
                if (grvalidatehtml($arg[1]['adcontent'])) {
                    $r = db('Grupo', 'i', 'ads', 'name,#content,adslot,adheight,tms', $arg[1]['name'], $arg[1]['adcontent'], $arg[1]['adslot'], $arg[1]['adheight'], dt());
                    gr_prnt('say("'.$GLOBALS["lang"]->created.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
                } else {
                    gr_prnt('say("'.$GLOBALS["lang"]->invalid_htmlcontent.'");');
                }
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'language') {
        if (gr_role('access', 'languages', '1')) {
            $arg[1]['name'] = vc($arg[1]['name'], 'strip');
            if (!empty($arg[1]['name'])) {
                $cr = db('Grupo', 's,count(*)', 'phrases', 'type,short', 'lang', mb_strtolower($arg[1]['name']))[0][0];
                if ($cr == 0) {
                    $ncode = $code = rn(6).rn(4);;
                    if (isset($_FILES['img']['name']) && !empty($_FILES['img']['name'])) {
                        $ext = pathinfo($_FILES['img']['name'])['extension'];
                        $ncode = $code.'.'.$ext;
                    }
                    $lgid = $r = db('Grupo', 'i', 'phrases', 'type,short', 'lang', $arg[1]['name']);
                    if (isset($r) && !empty($r)) {
                        $query = 'INSERT INTO gr_phrases (`type`,`short`,`full`,`lid`)  ';
                        $query = $query.'SELECT `type`,`short`,`full`,'.$r.' ';
                        $query = $query.'FROM `gr_phrases`';
                        $query = $query.'WHERE `lid` = 1 AND `type`="phrase"';
                        db('Grupo', 'q', $query);
                        if (isset($_FILES['import']['name']) && !empty($_FILES['import']['name'])) {
                            $importlang = 'importedlang-'.$r;
                            if (flr('upload', 'import', 'grupo/cache/phrases/', $importlang, 'json', 1, 1)) {
                                $importlang = 'gem/ore/grupo/cache/phrases/'.$importlang.'.json';
                                $importlang = file_get_contents($importlang);
                                $importlang = json_decode($importlang);
                                if (isset($importlang->core_align)) {
                                    foreach ($importlang as $find => $replace) {
                                        if ($find == 'core_align') {
                                            $query = 'UPDATE gr_phrases SET `full`=:phreplace ';
                                            $query = $query.'WHERE `id`=:lid AND `type`="lang"';
                                            $data = array();
                                            $data['phreplace'] = $replace;
                                            $data['lid'] = $r;
                                        } else {
                                            $query = 'UPDATE gr_phrases SET `full`=:phreplace ';
                                            $query = $query.'WHERE `lid`=:lid AND `type`="phrase" AND `short`=:phfind';
                                            $data = array();
                                            $data['phreplace'] = $replace;
                                            $data['phfind'] = $find;
                                            $data['lid'] = $r;
                                        }
                                        db('Grupo', 'q', $query, $data);
                                    }
                                }
                                unlink('gem/ore/grupo/cache/phrases/importedlang-'.$r.'.json');
                            }
                        }
                    }
                    if (isset($_FILES['img']['name']) && !empty($_FILES['img']['name'])) {
                        $icon = $r.'-gr-'.$code;
                        if (flr('upload', 'img', 'grupo/languages/', $icon, 'jpg,jpeg,png,gif', 1, 1)) {
                            flr('resize', 'grupo/languages/'.$icon.'.'.$ext, 0, 150, 150, 1);
                        }
                    }
                    gr_cache('languages', $lgid);
                    gr_prnt('say("'.$GLOBALS["lang"]->created.'","s");menuclick("mmenu","languages","'.$lgid.'",0);$(".grupo-pop").fadeOut();');
                } else {
                    gr_prnt('say("'.$GLOBALS["lang"]->already_exists.'");');
                }
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'customfield') {
        if (gr_role('access', 'fields', '1')) {
            $arg[1]['name'] = vc($arg[1]['name'], 'strip');
            $arg[1]['ftype'] = vc($arg[1]['ftype'], 'alpha');
            if (!empty($arg[1]['name']) && !empty($arg[1]['ftype'])) {
                $opts = $req = 0;
                $shrt = trim(preg_replace('/\s+/', ' ', $arg[1]['name']));
                $shrt = "cf_".mb_strtolower(str_replace(" ", "_", $shrt));
                $chkcf = db('Grupo', 's', 'phrases', 'short', $shrt);
                if (count($chkcf) > 0) {
                    gr_prnt('say("'.$GLOBALS["lang"]->already_exists.'");');
                } else if ($arg[1]['ftype'] == 'dropdownfield' && empty($arg[1]['options'])) {
                    gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
                } else {
                    if (!empty($arg[1]['required']) && !empty($arg[1]['addtosignup'])) {
                        $req = 3;
                    } else if (!empty($arg[1]['addtosignup'])) {
                        $req = 2;
                    } else if (!empty($arg[1]['required'])) {
                        $req = 1;
                    }
                    if ($arg[1]['ftype'] == 'dropdownfield' && !empty($arg[1]['options'])) {
                        $opts = $arg[1]['options'];
                    }
                    $catfield = 'field';
                    if (!empty($arg[1]['category'])) {
                        $catfield = 'gfield';
                    }
                    $r = db('Grupo', 'i', 'profiles', 'type,name,cat,v1,req', $catfield, 'cf_', $arg[1]['ftype'], $opts, $req);
                    $shrt = "cf_".vc($r, 'num');
                    db('Grupo', 'u', 'profiles', 'name', 'type,cat,id', $shrt, $catfield, $arg[1]['ftype'], $r);
                    $dlng = db('Grupo', 's', 'phrases', 'type', 'lang');
                    foreach ($dlng as $dl) {
                        db('Grupo', 'i', 'phrases', 'type,short,full,lid', 'phrase', $shrt, $arg[1]['name'], $dl['id']);
                        gr_cache('languages', $dl['id']);
                    }
                    gr_prnt('say("'.$GLOBALS["lang"]->created.'","s");menuclick("mmenu","ufields");$(".grupo-pop").fadeOut();');
                }
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'role') {
        if (!gr_role('access', 'roles', '1')) {
            exit;
        }
        $arg[1]['name'] = vc($arg[1]['name'], 'strip');
        if (isset($arg[1]["name"]) && !empty($arg[1]["name"])) {
            if (!isset($arg[1]['group'])) {
                $arg[1]['group'] = null;
            } else {
                $arg[1]['group'] = implode(',', $arg[1]['group']);
            }
            if (!isset($arg[1]['files'])) {
                $arg[1]['files'] = null;
            } else {
                $arg[1]['files'] = implode(',', $arg[1]['files']);
            }
            if (!isset($arg[1]['users'])) {
                $arg[1]['users'] = null;
            } else {
                $arg[1]['users'] = implode(',', $arg[1]['users']);
            }
            if (!isset($arg[1]['languages'])) {
                $arg[1]['languages'] = null;
            } else {
                $arg[1]['languages'] = implode(',', $arg[1]['languages']);
            }
            if (!isset($arg[1]['sys'])) {
                $arg[1]['sys'] = null;
            } else {
                $arg[1]['sys'] = implode(',', $arg[1]['sys']);
            }
            if (!isset($arg[1]['roles'])) {
                $arg[1]['roles'] = null;
            } else {
                $arg[1]['roles'] = implode(',', $arg[1]['roles']);
            }
            if (!isset($arg[1]['features'])) {
                $arg[1]['features'] = null;
            } else {
                $arg[1]['features'] = implode(',', $arg[1]['features']);
            }
            if (!isset($arg[1]['fields'])) {
                $arg[1]['fields'] = null;
            } else {
                $arg[1]['fields'] = implode(',', $arg[1]['fields']);
            }
            if (!isset($arg[1]['autodel']) || empty(vc($arg[1]['autodel'], 'num'))) {
                $arg[1]['autodel'] = "Off";
            }
            if (!isset($arg[1]['autounjoin']) || empty(vc($arg[1]['autounjoin'], 'num'))) {
                $arg[1]['autounjoin'] = "Off";
            }
            if (!isset($arg[1]['privatemsg'])) {
                $arg[1]['privatemsg'] = null;
            } else {
                $arg[1]['privatemsg'] = implode(',', $arg[1]['privatemsg']);
            }
            if (isset($arg[1]['delofflineuser']) && !empty($arg[1]['delofflineuser'])) {
                if ($arg[1]['privatemsg'] == null) {
                    $arg[1]['privatemsg'] = 10;
                } else {
                    $arg[1]['privatemsg'] = $arg[1]['privatemsg'].',10';
                }
            }
            $xtras = array();
            if (!isset($arg[1]['maxgroup']) || empty(vc($arg[1]['maxgroup'], 'num'))) {
                $xtras['maxgroup'] = 0;
            } else {
                $xtras['maxgroup'] = $arg[1]['maxgroup'];
            }
            if (!isset($arg[1]['maxfileuploadsize']) || empty(vc($arg[1]['maxfileuploadsize'], 'num'))) {
                $xtras['maxfileuploadsize'] = 1000;
            } else {
                $xtras['maxfileuploadsize'] = $arg[1]['maxfileuploadsize'];
            }
            $xtras = json_encode($xtras);
            $r = db('Grupo', 'i', 'permissions', 'name,groups,files,users,features,languages,sys,roles,fields,privatemsg,autodel,autounjoin,#xtras', $arg[1]["name"], $arg[1]['group'], $arg[1]['files'], $arg[1]['users'], $arg[1]['features'], $arg[1]['languages'], $arg[1]['sys'], $arg[1]['roles'], $arg[1]['fields'], $arg[1]['privatemsg'], $arg[1]['autodel'], $arg[1]['autounjoin'], $xtras);
            if (isset($_FILES['img']['name']) && !empty($_FILES['img']['name'])) {
                $code = rn(6).rn(4);;
                $ext = pathinfo($_FILES['img']['name'])['extension'];
                $icon = $r.'-gr-'.$code;
                if (flr('upload', 'img', 'grupo/roles/', $icon, 'jpg,jpeg,png,gif', 1, 1)) {
                    flr('resize', 'grupo/roles/'.$icon.'.'.$ext, 0, 150, 150, 1);
                }
            }
            gr_cache('roles');
            gr_prnt('say("'.$GLOBALS["lang"]->created.'","s");menuclick("mmenu","roles");$(".grupo-pop").fadeOut();');
        } else {
            gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
        }
    } else if ($arg[0] === 'user') {
        if (!gr_role('access', 'users', '1')) {
            exit;
        }
        $arg[1]['name'] = vc($arg[1]['name'], 'strip');
        if (empty($arg[1]["fname"])) {
            $arg[1]["name"] = '';
        }
        $role = 3;
        if (isset($arg[1]["role"]) && !empty($arg[1]["role"])) {
            $role = $arg[1]["role"];
        }
        if (!empty($arg[1]["name"])) {
            $checkusername = db('Grupo', 's', 'users', 'name', $arg[1]["name"]);
            $checkslug = db('Grupo', 's', 'options', 'type,v2', 'groupslug', $arg[1]["name"]);
            if (count($checkusername) > 0 || count($checkslug) > 0 || in_array($arg[1]["name"], $GLOBALS["reservedslugs"])) {
                gr_prnt('say("'.$GLOBALS["lang"]->username_already_exists.'");');
                exit;
            }
        }
        if ($GLOBALS["default"]->non_latin_usernames == 'enable') {
            $reg = usr('Grupo', 'register,nonlatin', $_POST["name"], $arg[1]["email"], $arg[1]["pass"], $role);
        } else {
            $reg = usr('Grupo', 'register', $_POST["name"], $arg[1]["email"], $arg[1]["pass"], $role);
        }
        if ($reg[0]) {
            $id = $reg[1];
            gr_data('i', 'profile', 'name', $arg[1]["fname"], $id, $arg[1]["name"], gr_usrcolor());
            if ($role == 1) {
                gr_mail('verify', $id, 0, rn(5), 1);
            }
            gr_prnt('say("'.$GLOBALS["lang"]->created.'","s");menuclick("mmenu","users");$(".grupo-pop").fadeOut();');
            $grjoin = $GLOBALS["default"]->autogroupjoin;
            if (!empty($grjoin)) {
                $cr = gr_group('valid', $grjoin);
                if ($cr[0]) {
                    gr_data('i', 'gruser', $grjoin, $id, 0);
                    $dt = array();
                    $dt['id'] = $grjoin;
                    $dt['msg'] = 'joined_group';
                    gr_group('sendmsg', $dt, 1, 1, $id);
                }
            }
            if ($arg[1]["sent"] == 1) {
                gr_mail('signup', $id, 0, rn(5), 1);
            }
        } else {
            if ($reg[1] === 'invalid') {
                $reg[1] = $GLOBALS["lang"]->invalid_value;
            } else if ($reg[1] === 'usernamecondition') {
                $reg[1] = $GLOBALS["lang"]->username_condition;
            } else if ($reg[1] === 'exist') {
                $reg[1] = $GLOBALS["lang"]->already_exists;
            } else if ($reg[1] === 'emailexist') {
                $reg[1] = $GLOBALS["lang"]->email_exists;
            }
            gr_prnt('say("'.$reg[1].'");');
        }
    }
}
?>