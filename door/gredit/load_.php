<?php if(!defined('s7V9pz')) {die();}?><?php
function gr_edit() {
    $arg = func_get_args();
    $uid = $GLOBALS["user"]['id'];
    if ($arg[0] === 'group') {
        $role = gr_group('user', $arg[1]["id"], $uid)['role'];
        $adm = 0;
        if ($role == 2 || $role == 1) {
            $adm = 1;
        }
        if (gr_role('access', 'groups', '2') && $adm == 1 || gr_role('access', 'groups', '7')) {
            if (!empty($arg[1]['name'])) {
                $cr = db('Grupo', 's,count(*)', 'options', 'type,v1,id<>', 'group', mb_strtolower($arg[1]['name']), $arg[1]['id'])[0][0];
                if ($cr == 0) {
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
                            $arg[1][$pf] = vc($arg[1][$pf]);
                        }
                        if (empty($arg[1][$pf]) && $f['req'] == 1 || empty($arg[1][$pf]) && $f['req'] == 3) {
                            gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");'); exit;
                        }
                    }
                    $ncode = $code = rn(6).rn(4);;
                    if (isset($_FILES['img']) && !empty($_FILES['img']['name'])) {
                        $ext = pathinfo($_FILES['img']['name'])['extension'];
                        $ncode = $code.'.'.$ext;
                    }
                    $nmchk = db('Grupo', 's', 'options', 'id', $arg[1]['id']);
                    if ($nmchk[0]['v1'] != vc($arg[1]['name'])) {
                        gr_data('u', '#v1,tms', 'type,id', $arg[1]['name'], dt(), 'group', $arg[1]['id']);
                        $dt = array();
                        $dt['id'] = $arg[1]["id"];
                        $dt['msg'] = 'renamed_group';
                        gr_group('sendmsg', $dt, 1, 1);
                    }
                    $pch = 1;
                    if (isset($arg[1]['delpass'])) {
                        if ($arg[1]['delpass'] == 1) {
                            $pch = 0;
                        }
                    }
                    if (!empty($arg[1]['description'])) {
                        $gdescp = db('Grupo', 's', 'profiles', 'type,name,uid', 'group', 'description', $arg[1]["id"]);
                        if (count($gdescp) > 0) {
                            db('Grupo', 'u', 'profiles', 'v1,tms', 'type,name,uid', $arg[1]['description'], dt(), 'group', 'description', $arg[1]["id"]);
                        } else {
                            db('Grupo', 'i', 'profiles', 'type,name,uid,v1,tms', 'group', 'description', $arg[1]["id"], $arg[1]['description'], dt());
                        }
                    }
                    if (!empty($arg[1]['slug'])) {
                        if (!preg_match('/[A-Za-z]/', $arg[1]['slug']) && !preg_match('/[^\\p{Common}\\p{Latin}]/u', $arg[1]['slug'])) {
                            gr_prnt('say("'.$GLOBALS["lang"]->slug_condition.'");'); exit;
                        }
                        $arg[1]['slug'] = preg_replace('/[^\pL\pM*+\pN._%+-]/u', '', $arg[1]['slug']);
                        $arg[1]['slug'] = mb_strtolower($arg[1]['slug']);
                    }
                    if (!empty($arg[1]['slug'])) {
                        $slug = db('Grupo', 's', 'options', 'type,v1', 'groupslug', $arg[1]["id"]);
                        $checkusername = db('Grupo', 's', 'users', 'name', $arg[1]["slug"]);
                        $checkslug = db('Grupo', 's', 'options', 'type,v2', 'groupslug', $arg[1]["slug"]);
                        if (count($slug) > 0) {
                            if (count($checkusername) > 0 || count($checkslug) > 0 && $slug[0]['v2'] != $arg[1]["slug"] || in_array($arg[1]['slug'], $GLOBALS["reservedslugs"])) {
                                gr_prnt('say("'.$GLOBALS["lang"]->slug_already_exists.'");'); exit;
                            } else if ($slug[0]['v2'] != $arg[1]["slug"]) {
                                db('Grupo', 'u', 'options', 'v2,tms', 'type,v1', $arg[1]['slug'], dt(), 'groupslug', $arg[1]["id"]);
                            }
                        } else {
                            if (count($checkusername) > 0 || count($checkslug) > 0 || in_array($arg[1]['slug'], $GLOBALS["reservedslugs"])) {
                                gr_prnt('say("'.$GLOBALS["lang"]->slug_already_exists.'");'); exit;
                            } else {
                                db('Grupo', 'i', 'options', 'type,v1,v2,tms', 'groupslug', $arg[1]["id"], $arg[1]['slug'], dt());
                            }
                        }
                    }
                    if (isset($GLOBALS["roles"]['groups'][15])) {
                        if (!empty($arg[1]['password']) && $pch == 1) {
                            $arg[1]['password'] = md5($arg[1]['password']);
                            gr_data('u', 'v2', 'type,id', $arg[1]['password'], 'group', $arg[1]['id']);
                            $dt = array();
                            $dt['id'] = $arg[1]["id"];
                            $dt['msg'] = 'changed_group_pass';
                            gr_group('sendmsg', $dt, 1, 1);
                        }
                    }

                    if (isset($GLOBALS["roles"]['groups'][14])) {
                        if (isset($arg[1]['visibility']) && $nmchk[0]['v3'] != $arg[1]['visibility']) {
                            if (!empty($arg[1]['visibility'])) {
                                $arg[1]['visibility'] = 'secret';
                            }
                            gr_data('u', 'v3', 'type,id', $arg[1]['visibility'], 'group', $arg[1]['id']);
                            $dt = array();
                            $dt['id'] = $arg[1]["id"];
                            $dt['msg'] = 'changed_group_visibility';
                            gr_group('sendmsg', $dt, 1, 1);
                        }
                    }

                    if (isset($GLOBALS["roles"]['groups'][13])) {
                        if (isset($arg[1]['unleavable']) && $nmchk[0]['v6'] != $arg[1]['unleavable']) {
                            $dt = array();
                            $dt['id'] = $arg[1]["id"];
                            $dt['msg'] = 'changed_leavable_group';
                            if (!empty($arg[1]['unleavable'])) {
                                $arg[1]['unleavable'] = 'unleavable';
                                $dt['msg'] = 'changed_unleavable_group';
                            }
                            gr_data('u', 'v6', 'type,id', $arg[1]['unleavable'], 'group', $arg[1]['id']);
                            gr_group('sendmsg', $dt, 1, 1);
                        }
                    }

                    if (isset($arg[1]['sendperm']) && $nmchk[0]['v5'] != $arg[1]['sendperm']) {
                        if (!empty($arg[1]['sendperm'])) {
                            $arg[1]['sendperm'] = 'adminonly';
                        }
                        gr_data('u', 'v5', 'type,id', $arg[1]['sendperm'], 'group', $arg[1]['id']);
                        $dt = array();
                        $dt['id'] = $arg[1]["id"];
                        $dt['msg'] = 'changed_message_settings';
                        gr_group('sendmsg', $dt, 1, 1);
                    }
                    if (isset($arg[1]['delpass']) && $arg[1]['delpass'] == 1) {
                        gr_data('u', 'v2', 'type,id', '', 'group', $arg[1]['id']);
                        $dt = array();
                        $dt['id'] = $arg[1]["id"];
                        $dt['msg'] = 'removed_group_pass';
                        gr_group('sendmsg', $dt, 1, 1);
                    }
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
                        } else if (empty($arg[1][$pf])) {
                            db('Grupo', 'd', 'profiles', 'type,name,uid', 'group', $f['id'], $arg[1]['id']);
                        } else {
                            $ct = db('Grupo', 's,count(*)', 'profiles', 'type,name,uid', 'group', $f['id'], $arg[1]['id'])[0][0];
                            if ($ct == 0) {
                                db('Grupo', 'i', 'profiles', 'type,name,uid,v1', 'group', $f['id'], $arg[1]['id'], $arg[1][$pf]);
                            } else {
                                db('Grupo', 'u', 'profiles', 'v1', 'type,name,uid', $arg[1][$pf], 'group', $f['id'], $arg[1]['id']);
                            }
                        }
                    }
                    if (isset($_FILES['img']) && !empty($_FILES['img']['name'])) {
                        $icon = $arg[1]['id'].'-gr-'.$code;
                        foreach (glob("gem/ore/grupo/groups/".$arg[1]['id']."-gr-*.*") as $filename) {
                            unlink($filename);
                        }
                        if (flr('upload', 'img', 'grupo/groups/', $icon, 'jpg,jpeg,png,gif', 0, 1)) {
                            flr('resize', 'grupo/groups/'.$icon.'.'.$ext, 0, 150, 150, 1);
                        }
                        $dt = array();
                        $dt['id'] = $arg[1]["id"];
                        $dt['msg'] = 'changed_group_icon';
                        gr_group('sendmsg', $dt, 1, 1);
                    }
                    if (!empty($_FILES['cpic']['name'])) {
                        $bg = $arg[1]['id'].'-gr-'.rn(10);
                        $ext = pathinfo($_FILES['cpic']['name'])['extension'];
                        foreach (glob("gem/ore/grupo/coverpic/groups/".$arg[1]['id']."-gr-*.*") as $filename) {
                            unlink($filename);
                        }
                        if (flr('upload', 'cpic', 'grupo/coverpic/groups/', $bg, 'jpg,jpeg,png,gif', 0, 1)) {
                            flr('resize', 'grupo/coverpic/groups/'.$bg.'.'.$ext, 0, 400, 220, 1);
                        }

                    }
                    gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");$(".swr-grupo .panel > .head > .left > span.vwp").trigger("click");$(".grupo-pop").fadeOut();');
                } else {
                    gr_prnt('say("'.$GLOBALS["lang"]->already_exists.'");');
                }
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'customfield') {
        if (gr_role('access', 'fields', '2')) {
            $oldfield = db('Grupo', 's', 'profiles', 'type,id|,type,id', 'field', $arg[1]['id'], 'gfield', $arg[1]['id']);
            $arg[1]['name'] = vc($arg[1]['name'], 'strip');
            $arg[1]['ftype'] = vc($arg[1]['ftype'], 'alpha');
            if (!empty($arg[1]['name']) && !empty($arg[1]['ftype']) && count($oldfield) > 0) {
                $shrt = trim(preg_replace('/\s+/', ' ', $arg[1]['name']));
                $opts = $req = 0;
                $shrt = "cf_".mb_strtolower(str_replace(" ", "_", $shrt));
                $chkcf = db('Grupo', 's', 'phrases', 'short', $shrt);
                if (count($chkcf) > 0 && $shrt != $oldfield[0]['name']) {
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
                    $catfield = $oldfield[0]['type'];
                    if (!empty($arg[1]['category']) && $arg[1]['category'] == 1) {
                        $catfield = 'gfield';
                    } else if (!empty($arg[1]['category']) && $arg[1]['category'] == 2) {
                        $catfield = 'field';
                    }
                    $r = db('Grupo', 'u', 'profiles', 'cat,v1,req,type', 'id', $arg[1]['ftype'], $opts, $req, $catfield, $arg[1]['id']);
                    $dlng = db('Grupo', 's', 'phrases', 'type', 'lang');
                    foreach ($dlng as $dl) {
                        db('Grupo', 'u', 'phrases', 'full', 'type,short', $arg[1]['name'], 'phrase', $oldfield[0]['name']);
                        gr_cache('languages', $dl['id']);
                    }
                    gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");menuclick("mmenu","ufields");$(".grupo-pop").fadeOut();');
                }
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'radiostation') {
        if (gr_role('access', 'features', '14')) {
            $arg[1]['description'] = vc($arg[1]['description'], 'strip');
            $arg[1]['streamlink'] = vc($arg[1]['streamlink'], 'url');
            $arg[1]['id'] = vc($arg[1]['id'], 'num');
            if (!empty($arg[1]['name']) && !empty($arg[1]['streamlink']) && !empty($arg[1]['id'])) {
                db('Grupo', 'u', 'options', '#v1,v2,v3', 'type,id', $arg[1]['name'], $arg[1]['description'], $arg[1]['streamlink'], 'radiostation', $arg[1]['id']);
                if (isset($_FILES['img']['name']) && !empty($_FILES['img']['name'])) {
                    $code = rn(6).rn(4);;
                    $ext = pathinfo($_FILES['img']['name'])['extension'];
                    $icon = $arg[1]['id'].'-gr-'.$code;
                    foreach (glob("gem/ore/grupo/radiostations/".$arg[1]['id']."-gr-*.*") as $filename) {
                        unlink($filename);
                    }
                    if (flr('upload', 'img', 'grupo/radiostations/', $icon, 'jpg,jpeg,png,gif', 1, 1)) {
                        flr('resize', 'grupo/radiostations/'.$icon.'.'.$ext, 0, 150, 150, 1);
                    }
                }
                gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'loginprovider') {
        if (gr_role('access', 'sys', '8')) {
            $arg[1]['provider'] = vc($arg[1]['provider'], 'strip');
            $arg[1]['id'] = vc($arg[1]['id'], 'num');
            if (!empty($arg[1]['provider'])) {
                db('Grupo', 'u', 'options', 'v1,#v2,#v3,#v4', 'type,id', $arg[1]['provider'], $_POST['appid'], $_POST['appsecretkey'], $_POST['appkey'], 'loginprovider', $arg[1]['id']);
                if (isset($_FILES['img']['name']) && !empty($_FILES['img']['name'])) {
                    $code = rn(6).rn(4);;
                    $ext = pathinfo($_FILES['img']['name'])['extension'];
                    $icon = $arg[1]['id'].'-gr-'.$code;
                    foreach (glob("gem/ore/grupo/loginprovider/".$arg[1]['id']."-gr-*.*") as $filename) {
                        unlink($filename);
                    }
                    if (flr('upload', 'img', 'grupo/loginprovider/', $icon, 'jpg,jpeg,png,gif', 1, 1)) {
                        flr('resize', 'grupo/loginprovider/'.$icon.'.'.$ext, 0, 150, 150, 1);
                    }
                }
                gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
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
                    $r = db('Grupo', 'u', 'ads', 'name,#content,adslot,adheight,tms', 'id', $arg[1]['name'], $arg[1]['adcontent'], $arg[1]['adslot'], $arg[1]['adheight'], dt(), $arg[1]['id']);
                    gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
                } else {
                    gr_prnt('say("'.$GLOBALS["lang"]->invalid_htmlcontent.'");');
                }
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'stickerpack') {
        if (gr_role('access', 'features', '16')) {
            $arg[1]['name'] = preg_replace('/[^a-z0-9 ]/i', '', $_POST['name']);
            if (!empty($arg[1]['name'])) {
                if (file_exists('gem/ore/grupo/stickers/'.$arg[1]['name']) && $arg[1]['name'] != $arg[1]['id']) {
                    gr_prnt('say("'.$GLOBALS["lang"]->already_exists.'");');
                } else {
                    rename('gem/ore/grupo/stickers/'.$arg[1]['id'], 'gem/ore/grupo/stickers/'.$arg[1]['name']);
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
                    gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
                }
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'menuitem') {
        if (gr_role('access', 'sys', '6')) {
            $arg[1]['ltext'] = vc($arg[1]['ltext'], 'strip');
            $arg[1]['morder'] = vc($arg[1]['morder'], 'num');
            $arg[1]['url'] = vc($arg[1]['url'], 'url');
            $arg[1]['id'] = vc($arg[1]['id'], 'num');
            if (!empty($arg[1]['ltext']) && !empty($arg[1]['url']) && !empty($arg[1]['id'])) {
                if (empty($arg[1]['morder'])) {
                    $arg[1]['morder'] = 0;
                }
                db('Grupo', 'u', 'options', 'v2,v3', 'type,id', $arg[1]['url'], $arg[1]['morder'], 'menuitem', $arg[1]['id']);
                $dlng = db('Grupo', 's', 'phrases', 'type', 'lang');
                $shrt = "mni_".$arg[1]['id'];
                foreach ($dlng as $dl) {
                    db('Grupo', 'u', 'phrases', 'full', 'type,short', $arg[1]['ltext'], 'phrase', $shrt);
                    gr_cache('languages', $dl['id']);
                }
                gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    } else if ($arg[0] === 'language') {
        if (!gr_role('access', 'languages', '2')) {
            exit;
        }
        if ($arg[1]['id'] == '0') {
            gr_prnt('say("'.$GLOBALS["lang"]->denied.'","e");');
            gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
            exit;
        }
        $r = db('Grupo', 's', 'phrases', 'type,id', 'lang', $arg[1]['id']);
        $arg[1]['name'] = vc($arg[1]['name'], 'strip');
        if (isset($r[0]) && !empty($arg[1]['name'])) {
            $ldir = 'ltr';
            if (isset($arg[1]["direction"]) && $arg[1]["direction"] == 'rtl') {
                $ldir = 'rtl';
            }
            db('Grupo', 'u', 'phrases', 'short,full', 'id', $arg[1]['name'], $ldir, $arg[1]['id']);

            if ($arg[1]["defaultlng"] == 1) {
                db('Grupo', 'u', 'defaults', 'v2', 'type,v1', $arg[1]['id'], 'default', 'language');
            }
            $ph = db('Grupo', 's', 'phrases', 'type,lid', 'phrase', 1);
            foreach ($ph as $p) {
                $pfull = db('Grupo', 's', 'phrases', 'type,lid,short', 'phrase', $arg[1]['id'], $p['short']);
                if (isset($pfull[0]['full'])) {
                    $key = 'z'.$pfull[0]['id'];
                    $p['full'] = $pfull[0]['full'];
                    if (!empty($arg[1][$key]) && $arg[1][$key] != $p['full']) {
                        $p['full'] = htmlspecialchars_decode($p['full']);
                        db('Grupo', 'u', 'phrases', 'full', 'lid,type,id', $arg[1][$key], $arg[1]['id'], 'phrase', $pfull[0]['id']);
                    }
                } else {
                    db('Grupo', 'i', 'phrases', 'short,type,full,lid', $p['short'], 'phrase', $p['full'], $arg[1]['id']);
                }
            }
        }
        if (isset($_FILES['img']['name']) && !empty($_FILES['img']['name'])) {
            $code = rn(6).rn(4);;
            $ext = pathinfo($_FILES['img']['name'])['extension'];
            $icon = $arg[1]['id'].'-gr-'.$code;
            foreach (glob("gem/ore/grupo/languages/".$arg[1]['id']."-gr-*.*") as $filename) {
                unlink($filename);
            }
            if (flr('upload', 'img', 'grupo/languages/', $icon, 'jpg,jpeg,png,gif', 1, 1)) {
                flr('resize', 'grupo/languages/'.$icon.'.'.$ext, 0, 150, 150, 1);
            }
        }
        gr_cache('languages', $arg[1]['id']);
        gr_cache('settings');
        gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");menuclick("mmenu","languages");');
        gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
    } else if ($arg[0] === 'role') {
        if (!gr_role('access', 'roles', '2')) {
            exit;
        }
        $arg[1]['name'] = vc($arg[1]['name'], 'strip');
        if (empty($arg[1]['name'])) {
            gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            exit;
        }
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
        if (!isset($arg[1]['fields'])) {
            $arg[1]['fields'] = null;
        } else {
            $arg[1]['fields'] = implode(',', $arg[1]['fields']);
        }
        if (!isset($arg[1]['features'])) {
            $arg[1]['features'] = null;
        } else {
            $arg[1]['features'] = implode(',', $arg[1]['features']);
        }
        if (!isset($arg[1]['privatemsg'])) {
            $arg[1]['privatemsg'] = null;
        } else {
            $arg[1]['privatemsg'] = implode(',', $arg[1]['privatemsg']);
        }
        if (!isset($arg[1]['autodel']) || empty(vc($arg[1]['autodel'], 'num'))) {
            $arg[1]['autodel'] = "Off";
        }
        if (!isset($arg[1]['autounjoin']) || empty(vc($arg[1]['autounjoin'], 'num'))) {
            $arg[1]['autounjoin'] = "Off";
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

        if (isset($_FILES['img']['name']) && !empty($_FILES['img']['name'])) {
            $code = rn(6).rn(4);;
            $ext = pathinfo($_FILES['img']['name'])['extension'];
            $icon = $arg[1]['rid'].'-gr-'.$code;
            foreach (glob("gem/ore/grupo/roles/".$arg[1]['rid']."-gr-*.*") as $filename) {
                unlink($filename);
            }
            if (flr('upload', 'img', 'grupo/roles/', $icon, 'jpg,jpeg,png,gif', 1, 1)) {
                flr('resize', 'grupo/roles/'.$icon.'.'.$ext, 0, 150, 150, 1);
            }
        }
        if (isset($arg[1]['delofflineuser']) && !empty($arg[1]['delofflineuser'])) {
            if ($arg[1]['privatemsg'] == null) {
                $arg[1]['privatemsg'] = 10;
            } else {
                $arg[1]['privatemsg'] = $arg[1]['privatemsg'].',10';
            }
        }
        db('Grupo', 'u', 'permissions', 'name,groups,files,users,features,languages,sys,roles,fields,privatemsg,autodel,autounjoin,#xtras', 'id', $arg[1]['name'], $arg[1]['group'], $arg[1]['files'], $arg[1]['users'], $arg[1]['features'], $arg[1]['languages'], $arg[1]['sys'], $arg[1]['roles'], $arg[1]['fields'], $arg[1]['privatemsg'], $arg[1]['autodel'], $arg[1]['autounjoin'], $xtras, $arg[1]['rid']);
        gr_cache('roles');
        gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
    } else if ($arg[0] === 'avatar') {
        if (!empty($_FILES['cavatar']['name'])) {
            $icon = $uid.'-gr-'.rn(10);
            $ext = pathinfo($_FILES['cavatar']['name'])['extension'];
            foreach (glob("gem/ore/grupo/users/".$uid."-gr-*.*") as $filename) {
                unlink($filename);
            }
            if (flr('upload', 'cavatar', 'grupo/users/', $icon, 'jpg,jpeg,png,gif', 0, 1)) {
                flr('resize', 'grupo/users/'.$icon.'.'.$ext, 0, 150, 150, 1);
            }
        } else if (isset($arg[1]['avatar'])) {
            if (file_exists('gem/ore/grupo/avatars/'.$arg[1]['avatar'])) {
                $icon = $uid.'-gr-'.rn(10);
                foreach (glob("gem/ore/grupo/users/".$uid."-gr-*.*") as $filename) {
                    unlink($filename);
                }
                flr('copy', 'grupo/avatars/'.$arg[1]['avatar'], 'grupo/users/'.$icon.'.png');
            }
        }
        gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
    } else if ($arg[0] === 'profile') {
        if (gr_role('access', 'users', '3') || gr_role('access', 'users', '8') || gr_role('access', 'sys', '3')) {
            if (isset($arg[1]['takeaction']) && !empty($arg[1]['takeaction'])) {
                $arg[1]['type'] = 'act';
                $arg[1]['opted'] = $arg[1]['takeaction'];
                gr_profile($arg[1]);
                exit;
            }
            if (!isset($GLOBALS["roles"]['users'][2])) {
                gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
            }
        }
        if (isset($GLOBALS["roles"]['users'][2]) || $arg[1]['id'] == $GLOBALS["user"]['id']) {
            if ($GLOBALS["default"]->non_latin_usernames == 'enable') {
                $nonlatreg = 'alter,nonlatin';
            } else {
                $nonlatreg = 'alter';
            }
            if (!empty($arg[1]['rmusbg']) && $arg[1]['rmusbg'] == 'yes') {
                foreach (glob("gem/ore/grupo/userbg/".$arg[1]['id']."-gr-*.*") as $filename) {
                    unlink($filename);
                }
            }
            if (!empty($_FILES['cavatar']['name'])) {
                $icon = $arg[1]['id'].'-gr-'.rn(10);
                $ext = pathinfo($_FILES['cavatar']['name'])['extension'];
                foreach (glob("gem/ore/grupo/users/".$arg[1]['id']."-gr-*.*") as $filename) {
                    unlink($filename);
                }
                if (flr('upload', 'cavatar', 'grupo/users/', $icon, 'jpg,jpeg,png,gif', 0, 1)) {
                    flr('resize', 'grupo/users/'.$icon.'.'.$ext, 0, 150, 150, 1);
                }
            } else if (isset($arg[1]['avatar'])) {
                if (file_exists('gem/ore/grupo/avatars/'.$arg[1]['avatar'])) {
                    $icon = $arg[1]['id'].'-gr-'.rn(10);
                    foreach (glob("gem/ore/grupo/users/".$arg[1]['id']."-gr-*.*") as $filename) {
                        unlink($filename);
                    }
                    flr('copy', 'grupo/avatars/'.$arg[1]['avatar'], 'grupo/users/'.$icon.'.png');
                }
            }
            if (!empty($arg[1]["user"])) {
                if (!preg_match('/[A-Za-z]/', $arg[1]['user']) && !preg_match('/[^\\p{Common}\\p{Latin}]/u', $arg[1]['user'])) {
                    gr_prnt('say("'.$GLOBALS["lang"]->username_condition.'");'); exit;
                }
                $checkusername = db('Grupo', 's', 'users', 'name,id<>', $arg[1]["user"], $arg[1]['id']);
                $checkslug = db('Grupo', 's', 'options', 'type,v2', 'groupslug', $arg[1]["user"]);
                if (count($checkusername) > 0 || count($checkslug) > 0 || in_array($arg[1]["user"], $GLOBALS["reservedslugs"])) {
                    gr_prnt('say("'.$GLOBALS["lang"]->username_already_exists.'");');
                    exit;
                }
            }
            if (usr('Grupo', 'alter', 'email', $_POST['email'], $arg[1]['id']) || usr('Grupo', 'select', $arg[1]['id'])['email'] == $arg[1]['email']) {
                if (usr('Grupo', $nonlatreg, 'name', $_POST['user'], $arg[1]['id']) || usr('Grupo', 'select', $arg[1]['id'])['name'] == $arg[1]['user']) {
                    if (!empty($arg[1]['password'])) {
                        usr('Grupo', 'alter', 'pass', $arg[1]['password'], $arg[1]['id']);
                    }
                    $arg[1]['name'] = vc($arg[1]['name'], 'strip');
                    if (!empty($arg[1]['name'])) {
                        if (isset($GLOBALS["roles"]['features'][10])) {
                            db('Grupo', 'u', 'options', 'v2,v4,v5', 'type,v1,v3', $arg[1]['name'], $arg[1]['user'], $arg[1]['ncolor'], 'profile', 'name', $arg[1]['id']);

                        } else {
                            db('Grupo', 'u', 'options', 'v2,v4', 'type,v1,v3', $arg[1]['name'], $arg[1]['user'], 'profile', 'name', $arg[1]['id']);
                        }
                    }
                    if (gr_role('access', 'roles', '2')) {
                        if (!empty($arg[1]['role'])) {
                            usr('Grupo', 'alter', 'role', $arg[1]['role'], $arg[1]['id']);
                        }
                    }
                    if (!empty($arg[1]['privatemsg'])) {
                        if ($arg[1]['privatemsg'] == 'enable' || $arg[1]['privatemsg'] == 'disable') {
                            $ct = db('Grupo', 's,count(*)', 'options', 'type,v1,v3', 'profile', 'privatemsgs', $arg[1]['id'])[0][0];
                            if ($ct == 0) {
                                db('Grupo', 'i', 'options', 'type,v1,v2,v3', 'profile', 'privatemsgs', $arg[1]['privatemsg'], $arg[1]['id']);
                            } else {
                                db('Grupo', 'u', 'options', 'v2', 'type,v1,v3', $arg[1]['privatemsg'], 'profile', 'privatemsgs', $arg[1]['id']);
                            }
                        }
                    }
                    $lists = db('Grupo', 's', 'profiles', 'type', 'field');
                    foreach ($lists as $f) {
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
                        } else if (empty($arg[1][$pf])) {
                            db('Grupo', 'd', 'profiles', 'type,name,uid', 'profile', $f['id'], $arg[1]['id']);
                        } else {
                            $ct = db('Grupo', 's,count(*)', 'profiles', 'type,name,uid', 'profile', $f['id'], $arg[1]['id'])[0][0];
                            if ($ct == 0) {
                                db('Grupo', 'i', 'profiles', 'type,name,uid,v1', 'profile', $f['id'], $arg[1]['id'], $arg[1][$pf]);
                            } else {
                                db('Grupo', 'u', 'profiles', 'v1', 'type,name,uid', $arg[1][$pf], 'profile', $f['id'], $arg[1]['id']);
                            }
                        }
                    }
                    if (!empty($arg[1]['tmz'])) {
                        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                        if (in_array($arg[1]['tmz'], $tzlist) || $arg[1]['tmz'] == 'Auto') {
                            $ct = db('Grupo', 's,count(*)', 'options', 'type,v1,v3', 'profile', 'tmz', $arg[1]['id'])[0][0];
                            if ($ct == 0) {
                                gr_data('i', 'profile', 'tmz', $arg[1]['tmz'], $arg[1]['id']);
                            } else {
                                gr_data('u', 'v2', 'type,v1,v3', $arg[1]['tmz'], 'profile', 'tmz', $arg[1]['id']);
                            }
                        }
                    }
                    if (!empty($arg[1]['delacc']) && $arg[1]['delacc'] == 'yes') {
                        if (gr_role('access', 'users', '7')) {
                            $ct = db('Grupo', 's', 'options', 'type,v1,v3', 'deaccount', 'yes', $arg[1]['id']);
                            if ($ct && count($ct) > 0) {
                                gr_prnt('say("'.$GLOBALS["lang"]->already_deactivated.'","e");');
                            } else {
                                $ct = db('Grupo', 'i', 'options', 'type,v1,v3', 'deaccount', 'yes', $arg[1]['id']);
                                gr_prnt('say("'.$GLOBALS["lang"]->deactivated.'","s");');
                                gr_profile('ustatus', 'offline', $arg[1]['id']);
                                usr('Grupo', 'forcelogout', $arg[1]['id']);
                            }
                        }
                    }

                    if (!empty($arg[1]['alert'])) {
                        $ct = db('Grupo', 's,count(*)', 'options', 'type,v1,v3', 'profile', 'alert', $arg[1]['id'])[0][0];
                        if ($ct == 0) {
                            gr_data('i', 'profile', 'alert', $arg[1]['alert'], $arg[1]['id']);
                        } else {
                            gr_data('u', 'v2', 'type,v1,v3', $arg[1]['alert'], 'profile', 'alert', $arg[1]['id']);
                        }
                    }
                    if (isset($GLOBALS["roles"]['features'][12])) {
                        if (!empty($_FILES['cbg']['name'])) {
                            $bg = $arg[1]['id'].'-gr-'.rn(10);
                            $ext = pathinfo($_FILES['cbg']['name'])['extension'];
                            foreach (glob("gem/ore/grupo/userbg/".$arg[1]['id']."-gr-*.*") as $filename) {
                                unlink($filename);
                            }
                            if (flr('upload', 'cbg', 'grupo/userbg/', $bg, 'jpg,jpeg,png,gif', 0, 1)) {
                                if (@is_array(getimagesize('gem/ore/grupo/userbg/'.$bg.'.'.$ext))) {
                                    flr('compress', 'grupo/userbg/'.$bg.'.'.$ext, 50);
                                } else {
                                    flr('delete', 'grupo/userbg/'.$bg.'.'.$ext);
                                }
                            }

                        }
                    }
                    if (!empty($_FILES['cpic']['name'])) {
                        $bg = $arg[1]['id'].'-gr-'.rn(10);
                        $ext = pathinfo($_FILES['cpic']['name'])['extension'];
                        foreach (glob("gem/ore/grupo/coverpic/users/".$arg[1]['id']."-gr-*.*") as $filename) {
                            unlink($filename);
                        }
                        if (flr('upload', 'cpic', 'grupo/coverpic/users/', $bg, 'jpg,jpeg,png,gif', 0, 1)) {
                            flr('resize', 'grupo/coverpic/users/'.$bg.'.'.$ext, 0, 400, 220, 1);
                        }

                    }
                    if ($arg[1]['id'] != $uid) {
                        if ($arg[1]['aside'] == 'profile') {
                            gr_prnt('$(".swr-grupo .aside > .content .profile > .top > span.refresh").trigger("click");');
                        } else if ($arg[1]['aside'] != 'right') {
                            gr_prnt('menuclick("mmenu","users");');
                        } else {
                            gr_prnt('$(".rside .xtra").trigger("click");');
                        }
                    } else {
                        if (!empty($arg[1]['password'])) {
                            usr('Grupo', 'clear', $arg[1]['id']);
                        }
                        gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
                    }
                    if (empty($arg[1]['delacc']) || $arg[1]['delacc'] != 'yes') {
                        gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");');
                    }
                    gr_prnt('$(".grupo-pop").fadeOut();');
                } else {
                    if ($GLOBALS["default"]->non_latin_usernames == 'enable') {
                        if (strpos($arg[1]['user'], ' ') !== false) {
                            $arg[1]['user'] = 0;
                        } else {
                            $arg[1]['user'] = mb_strtolower($arg[1]['user']);
                        }
                    } else {
                        $arg[1]['user'] = mb_strtolower(vc($arg[1]['user'], 'alphanum'));
                    }
                    if (empty($arg[1]['user'])) {
                        gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
                    } else {
                        gr_prnt('say("'.$GLOBALS["lang"]->username_exists.'");');
                    }
                }
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->email_exists.'");');
            }
        }
    }
}
?>