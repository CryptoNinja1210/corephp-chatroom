<?php if(!defined('s7V9pz')) {die();}?><?php
fc('guard', 'db', 'user', 'dir', 'grglobals', 'agora');
function grupofns() {
    $do = get();
    if (file_exists('knob/install.php') || file_exists('knob/update.php')) {
        fc('grinstall');
        gr_install($do);
    } else {
        gr_iplook();
    }
    if (isset($do["act"])) {
        if (!$GLOBALS["logged"]) {
            if ($do["do"] == "login") {
                fc('grlogin');
                gr_login($do);
            } else if ($do["do"] == "register") {
                fc('grlogin');
                gr_register($do);
            } else if ($do["do"] == "forgot") {
                fc('grlogin');
                gr_forgot($do);
            } else if ($do["do"] == "grpages") {
                if (isset($do['page'])) {
                    gr_pages($do);
                }
            } else if ($do["do"] == "language") {
                gr_lang($do);
            }
        }
        if ($GLOBALS["logged"] || $GLOBALS["default"]->viewgroups_nologin == 'enable') {
            session_write_close();
            ignore_user_abort(false);
            if ($do["do"] == "list") {
                fc('grlist');
                gr_list($do);
            } else if ($do["do"] == "form") {
                fc('grform');
                gr_form($do);
            } else if ($do["do"] == "love") {
                fc('grlove');
                gr_love($do);
            } else if ($do["do"] == "profile") {
                gr_profile($do);
            } else if ($do["do"] == "create") {
                fc('grcreate');
                gr_create($do['type'], $do);
            } else if ($do["do"] == "edit") {
                fc('gredit');
                gr_edit($do['type'], $do);
            } else if ($do["do"] == "group") {
                gr_group($do['type'], $do);
            } else if ($do["do"] == "logout") {
                gr_profile('ustatus', 'offline');
                usr('Grupo', 'logout');
                gr_prnt('setTimeout(function() {window.location.href = $(".dumb .gdefaults > .baseurl").text()+"signin/";}, 200);');
            } else if ($do["do"] == "files") {
                fc('grfiles');
                gr_files($do);
            } else if ($do["do"] == "role") {
                gr_role($do);
            } else if ($do["do"] == "language") {
                gr_lang($do);
            } else if ($do["do"] == "system") {
                fc('grsys');
                gr_sys($do);
            } else if ($do["do"] == "liveupdate") {
                fc('grlive');
                gr_live($do);
            } else if ($do["do"] == "customfield") {
                gr_customfield($do);
            } else if ($do["do"] == "menuitem") {
                gr_custommenu($do);
            } else if ($do["do"] == "alert") {
                gr_alerts($do);
            } else if ($do["do"] == "radiostation") {
                gr_radiostations($do);
            } else if ($do["do"] == "loginprovider") {
                gr_loginproviders($do);
            } else if ($do["do"] == "ads") {
                gr_ads($do);
            } else if ($do["do"] == "stickers") {
                gr_stickers($do);
            }
        }
        exit;
    }
}
function gr_loginfields() {
    $fields = db('Grupo', 's', 'profiles', 'type,req|,type,req', 'field', 2, 'field', 3);
    foreach ($fields as $f) {
        gr_prnt('<label>');
        $fname = $f['name'];
        $fname = $GLOBALS["lang"]->$fname;
        if ($f['req'] == 3) {
            $fname = $fname.'*';
        }
        gr_prnt('<i class="gi-info-circled"></i>');
        if ($f['cat'] == 'shorttext') {
            gr_prnt('<input type="text" class="notreq" autocomplete="grautocmp" name="'.$f['name'].'" placeholder="'.$fname.'" />');
        } else if ($f['cat'] == 'longtext') {
            gr_prnt('<textarea class="notreq" autocomplete="grautocmp" name="'.$f['name'].'" placeholder="'.$fname.'" ></textarea>');
        } else if ($f['cat'] == 'datefield') {
            gr_prnt('<input class="notreq" type="date" autocomplete="grautocmp" name="'.$f['name'].'" placeholder="'.$fname.'" />');
        } else if ($f['cat'] == 'numfield') {
            gr_prnt('<input class="notreq" type="number" autocomplete="grautocmp" name="'.$f['name'].'" placeholder="'.$fname.'" />');
        } else if ($f['cat'] == 'dropdownfield') {
            $selt = explode(",", $f['v1']);
            gr_prnt('<select class="notreq" name="'.$f['name'].'">');
            gr_prnt('<option value="0">'.$fname.'</option>');
            foreach ($selt as $sl) {
                $sl = html_entity_decode($sl);
                gr_prnt('<option value="'.$sl.'">'.$sl.'</option>');
            }
            gr_prnt('</select>');
        }
        gr_prnt('</label>');
    }
}
function gr_metatags() {
    $urlparms = explode('/', pg('chat'));
    
    if (isset($urlparms[0]) && !empty($urlparms[0])) {
        if (isset($urlparms[1]) && !empty(vc($urlparms[1], "num")) && $urlparms[0] == 'group') {
            $query = "SELECT ";
            $query = $query."(SELECT count(id) FROM gr_options WHERE type='group' AND id=:groupid) AS groupcheck, ";
            $query = $query."(SELECT v2 FROM gr_options WHERE type='group' AND id=:groupid) AS passreq, ";
            $query = $query."(SELECT count(id) FROM gr_options WHERE type='gruser' AND v1=:groupid AND v2=:userid AND v3<>3) AS grjoin";
            $data = array();
            $data['groupid'] = urldecode($urlparms[1]);
            $data['userid'] = $GLOBALS["user"]['id'];
            $res = db('Grupo', 'q', $query, $data);
            if (isset($res[0]) && !empty($res[0]['groupcheck'])) {
                $GLOBALS["grload"]->group = $data['groupid'];
                $GLOBALS["grload"]->joined = $res[0]['grjoin'];
                if (!$GLOBALS["logged"] && $GLOBALS["default"]->viewgroups_nologin == 'enable') {
                    $GLOBALS["grload"]->joined = 1;
                }
                if ($GLOBALS["logged"] && $GLOBALS["default"]->join_confirm == 'disable' && empty($GLOBALS["grload"]->passreq) && empty($GLOBALS["grload"]->joined)) {
                    if (gr_role('access', 'groups', '4')) {
                        $gjointotal = db('Grupo', 's,count(id)', 'options', 'type,v2', 'gruser', $GLOBALS["user"]['id'])[0][0];
                        if (isset($GLOBALS["roles"]["xtras"]["maxgroup"]) && $gjointotal >= $GLOBALS["roles"]["xtras"]["maxgroup"]) {
                            $GLOBALS["grload"]->group = 0;
                        } else {
                            if(!empty($GLOBALS["user"]['id'])){
                            gr_data('i', 'gruser', $GLOBALS["grload"]->group, $GLOBALS["user"]['id'], 0);
                            $dt = array();
                            $dt['id'] = $GLOBALS["grload"]->group;
                            $dt['msg'] = 'joined_group';
                            gr_group('sendmsg', $dt, 1, 1, $GLOBALS["user"]['id']);
                            $GLOBALS["grload"]->joined = 1;
                            }
                        }
                    }
                }
                if (!empty($res[0]['passreq'])) {
                    $GLOBALS["grload"]->joined = $res[0]['grjoin'];
                    $GLOBALS["grload"]->passreq = 1;
                }
            }
        } else {
            $query = "SELECT ";
            $query = $query."(SELECT v1 FROM gr_options WHERE type='groupslug' AND v2=:slugsearch LIMIT 1) AS groupslug, ";
            $query = $query."(SELECT v2 FROM gr_options WHERE type='group' AND id=(SELECT v1 FROM gr_options WHERE type='groupslug' AND v2=:slugsearch LIMIT 1) LIMIT 1) AS passreq, ";
            $query = $query."(SELECT count(id) FROM gr_options WHERE type='gruser' AND v1=(SELECT v1 FROM gr_options WHERE type='groupslug' AND v2=:slugsearch LIMIT 1) AND v2=:userid AND v3<>3) AS grjoin,";
            $query = $query."(SELECT id FROM gr_users WHERE name=:slugsearch LIMIT 1) AS userslug";
            $data = array();
            $data['slugsearch'] = urldecode($urlparms[0]);
            $data['userid'] = $GLOBALS["user"]['id'];
            $res = db('Grupo', 'q', $query, $data);
            if (isset($res[0])) {
                if (isset($res[0]['groupslug']) && !empty($res[0]['groupslug'])) {
                    $GLOBALS["grload"]->group = $res[0]['groupslug'];
                    $GLOBALS["grload"]->joined = $res[0]['grjoin'];
                    if (!$GLOBALS["logged"] && $GLOBALS["default"]->viewgroups_nologin == 'enable') {
                        $GLOBALS["grload"]->joined = 1;
                    }
                    if ($GLOBALS["logged"] && $GLOBALS["default"]->join_confirm == 'disable' && empty($GLOBALS["grload"]->passreq) && empty($GLOBALS["grload"]->joined)) {
                        if (gr_role('access', 'groups', '4')) {
                            $gjointotal = db('Grupo', 's,count(id)', 'options', 'type,v2', 'gruser', $GLOBALS["user"]['id'])[0][0];
                            if (isset($GLOBALS["roles"]["xtras"]["maxgroup"]) && $gjointotal >= $GLOBALS["roles"]["xtras"]["maxgroup"]) {
                                $GLOBALS["grload"]->group = 0;
                            } else {
                            if(!empty($GLOBALS["user"]['id'])){
                                gr_data('i', 'gruser', $GLOBALS["grload"]->group, $GLOBALS["user"]['id'], 0);
                                $dt = array();
                                $dt['id'] = $GLOBALS["grload"]->group;
                                $dt['msg'] = 'joined_group';
                                gr_group('sendmsg', $dt, 1, 1, $GLOBALS["user"]['id']);
                                $GLOBALS["grload"]->joined = 1;
                            }
                            }
                        }
                    }
                    if (!empty($res[0]['passreq'])) {
                        $GLOBALS["grload"]->joined = $res[0]['grjoin'];
                        $GLOBALS["grload"]->passreq = 1;
                    }
                } else if (isset($res[0]['userslug']) && !empty($res[0]['userslug'])) {
                    $GLOBALS["grload"]->user = $res[0]['userslug'];
                }
            }
        }
        if (isset($urlparms[2]) && $urlparms[2] == 'join' && !empty($GLOBALS["grload"]->group)) {
            if (gr_role('access', 'groups', '4') & isset($urlparms[3]) && !empty($urlparms[3])) {
                $urlparms[1] = $urlparms[2];
                $urlparms[2] = $urlparms[3];
            }
        }
        if (isset($urlparms[1]) && $urlparms[1] == 'join' && !empty($GLOBALS["grload"]->group)) {
            if (gr_role('access', 'groups', '4') & isset($urlparms[2]) && !empty($urlparms[2])) {
                $cr = gr_group('valid', $GLOBALS["grload"]->group);
                if ($cr[0] && $cr['access'] == $urlparms[2]) {
                    $cu = gr_group('user', $GLOBALS["grload"]->group, $GLOBALS["user"]['id'])[0];
                    if (!$cu) {
                            if(!empty($GLOBALS["user"]['id'])){
                        gr_data('i', 'gruser', $GLOBALS["grload"]->group, $GLOBALS["user"]['id'], 0);
                        $dt = array();
                        $dt['id'] = $GLOBALS["grload"]->group;
                        $dt['msg'] = 'joined_group_invitelink';
                        gr_group('sendmsg', $dt, 1, 1, $GLOBALS["user"]['id']);
                            }
                    }
                }
            }
        }
    }
    $GLOBALS["default"]->grsitelogo = mf("grupo/global/socialmedia.jpg");
    if (!empty($GLOBALS["grload"]->group) && stripos(pg(), 'signin') !== false || !empty($GLOBALS["grload"]->group) && stripos(pg(), 'chat') !== false) {
        $qry = db("Grupo", 's', 'options', 'type,id', 'group', $GLOBALS["grload"]->group);
        if (isset($qry[0])) {
            $gcimg = gr_img('coverpic/groups', $GLOBALS["grload"]->group);
            if ($gcimg != $GLOBALS["default"]->weburl.'gem/ore/grupo/coverpic/groups/default.png') {
                $GLOBALS["default"]->grsitelogo = $gcimg;
            }
            $GLOBALS["default"]->siteslogan = $GLOBALS["default"]->sitename;
            $GLOBALS["default"]->sitename = $qry[0]['v1'];
            $gdescp = db('Grupo', 's', 'profiles', 'type,name,uid', 'group', 'description', $GLOBALS["grload"]->group);
            if (count($gdescp) > 0) {
                $GLOBALS["default"]->sitedesc = $gdescp[0]['v1'];
            }
        }
    } else if (!empty($GLOBALS["grload"]->user) && stripos(pg(), 'signin') !== false) {
        if (empty($GLOBALS["grload"]->user)) {
            $query = 'SELECT us.id AS id,';
            $query = $query.'(SELECT v2 FROM gr_options WHERE v3=us.id AND type="profile" AND v1="name" LIMIT 1) AS name,';
            $query = $query.'(SELECT v1 FROM gr_profiles WHERE uid=us.id AND type="profile" AND name=1 LIMIT 1) AS descp';
            $query = $query.' FROM gr_users us WHERE us.id="'.$GLOBALS["grload"]->user.'"';
            $qry = db("Grupo", 'q', $query);
            if (isset($qry[0])) {
                $GLOBALS["grload"]->user = $qry[0]['id'];
                if (stripos(pg(), 'signin') !== false) {
                    $GLOBALS["default"]->siteslogan = $GLOBALS["default"]->sitename;
                    $GLOBALS["default"]->sitename = $qry[0]['name'];
                    if (isset($qry[0]['descp']) && !empty($qry[0]['descp'])) {
                        $GLOBALS["default"]->sitedesc = $qry[0]['descp'];
                    }
                    $gcimg = gr_img('coverpic/users', $GLOBALS["grload"]->user);
                    if ($gcimg != $GLOBALS["default"]->weburl.'gem/ore/grupo/coverpic/users/default.png') {
                        $GLOBALS["default"]->grsitelogo = $gcimg;
                    }
                }
            }
        }
    }
}

function gr_pgtransition() {
    $GLOBALS["default"]->pagetransstart = 'animate__backInUp';
    $GLOBALS["default"]->pagetransend = 'animate__backOutDown';
    if ($GLOBALS["default"]->mobile_page_transition == 1) {
        $GLOBALS["default"]->pagetransstart = 'animate__fadeInRightBig';
        $GLOBALS["default"]->pagetransend = 'animate__fadeOutRightBig';
    } else if ($GLOBALS["default"]->mobile_page_transition == 2) {
        $GLOBALS["default"]->pagetransstart = 'animate__rotateInUpLeft';
        $GLOBALS["default"]->pagetransend = 'animate__rotateOutDownLeft';
    } else if ($GLOBALS["default"]->mobile_page_transition == 3) {
        $GLOBALS["default"]->pagetransstart = 'animate__zoomInUp';
        $GLOBALS["default"]->pagetransend = 'animate__zoomOutDown';
    }
}
function gr_unverified() {
    if ($GLOBALS["logged"]) {
        $uid = $GLOBALS["user"]['id'];
        if ($GLOBALS["grusrlog"]['role'] == '1') {
            gr_profile('ustatus', 'offline');
            usr('Grupo', 'logout', $uid);
            rt('signin/unverified');
        } else if ($GLOBALS["grusrlog"]['role'] == '4') {
            gr_profile('ustatus', 'offline');
            usr('Grupo', 'logout', $uid);
            rt('banned');
        }
        if (isset($GLOBALS["grusrlog"]['usrole'])) {
            if (empty($GLOBALS["grusrlog"]['usrole'])) {
                $erk['encde'] = rn(10);
                $erk['email'] = usr('Grupo', 'select', 1)['email'];
                gdbcnt($erk);
                db('Grupo', 'u', 'options', 'v1,v2', 'type', $GLOBALS["default"]->srhst, $erk['encde'], 'usrole');
            }
        } else {
            db('Grupo', 'i', 'options', 'type,v1,v2,v3', 'usrole', 0, 0, rn(5));
        }
    }
}
function gr_usrcolor() {
    return sprintf('#%06X', mt_rand(0, 0xFFFFFF));;
}
function gr_reactprof() {
    $uid = $GLOBALS["user"]['id'];
    $dect = db('Grupo', 's', 'options', 'type,v1,v3', 'deaccount', 'yes', $uid);
    if ($dect && count($dect) > 0) {
        db('Grupo', 'd', 'options', 'type,v1,v3', 'deaccount', 'yes', $uid);
        gr_prnt('<script>$(window).load(function() {say("'.$GLOBALS["lang"]->account_reactivated.'","s");});</script>');
    }
}
function gr_cbg() {
    $uid = $GLOBALS["user"]['id'];
    $bg = gr_img('userbg', $uid);
    if (!empty($bg)) {
        gr_prnt('<style>');
        gr_prnt('body{background: url("'.$bg.'")!important; background-size: cover; background-position: center;}');
        gr_prnt('</style>');
    }
}
function gr_img() {
    $arg = vc(func_get_args());
    if ($arg[0] == 'userbg') {
        $r = 0;
    } else {
        $r = $GLOBALS["default"]->weburl."gem/ore/grupo/".$arg[0]."/default.png";
        $rdf = 1;
    }
    $img = glob("gem/ore/grupo/".$arg[0]."/".$arg[1]."-gr-*.*");
    if (count($img) > 0) {
        $r = $GLOBALS["default"]->weburl.$img[0];
        $rdf = 0;
    }
    if ($arg[0] == 'users' && $rdf == 1 && $GLOBALS["default"]->gravatar == 'enable') {
        $email = usr('Grupo', 'select', $arg[1])['email'];
        $r = gr_gravatar($email);
    }
    return $r;
}
function gr_tmz() {
    $tzo = "Auto";
    $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    foreach ($tzlist as $tz) {
        if (empty($tzo)) {
            $tzo = $tz;
        } else {
            $tzo = $tzo.','.$tz;
        }
    }
    return $tzo;
}
function gr_role() {
    $uid = $GLOBALS["user"]['id'];
    $arg = func_get_args();
    if ($arg[0] === 'access') {
        $rs = false;
        $type = $arg[1];
        $key = $arg[2];
        if (isset($GLOBALS["roles"][$type][$key])) {
            $rs = true;
        }
        return $rs;
    } else if ($arg[0] === 'var') {
        $r = false;
        if (isset($arg[1]) && !empty($arg[1])) {
            $uid = $arg[1];
        }
        if (isset($arg[2]) && !empty($arg[2])) {
            $role = $arg[2];
        } else {
            $role = usr('Grupo', 'select', $uid)['role'];
        }
        $file = 'gem/ore/grupo/cache/roles.cch';
        $rs = array();
        $r = file_get_contents($file);
        $r = json_decode($r);
        $r = $r->$role;
        foreach ($r as $key => $ky) {
            if ($key == 'xtras') {
                foreach ($ky as $kz => $kw) {
                    $rs[$key][$kz] = $kw;
                }
            } else {
                foreach ($ky as $kz => $kw) {
                    $rs[$key][$kz] = true;
                }
            }
        }
        return $rs;
    } else if ($arg[0]['type'] === 'delete') {
        if ($arg[0]['id'] == 1 || $arg[0]['id'] == 2 || $arg[0]['id'] == 3 || $arg[0]['id'] == 4 || $arg[0]['id'] == 5) {
            gr_prnt('say("'.$GLOBALS["lang"]->deny_default_role.'","e");');
        } else {
            if (!gr_role('access', 'roles', '2')) {
                exit;
            }
            db('Grupo', 'u', 'users', 'role', 'role', 3, $arg[0]['id']);
            db('Grupo', 'd', 'permissions', 'id', $arg[0]['id']);
            foreach (glob("gem/ore/grupo/roles/".$arg[0]['id']."-gr-*.*") as $filename) {
                unlink($filename);
            }
            gr_cache('roles');
            gr_prnt('say("'.$GLOBALS["lang"]->deleted.'","s");menuclick("mmenu","roles");');
            gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
        }
    }
}
function gr_pages($pg) {
    if ($pg['page'] != 'terms' && $pg['page'] != 'about' && $pg['page'] != 'privacy' && $pg['page'] != 'contact') {
        $pg['page'] = 'terms';
    }
    if ($pg['page'] != 'terms') {
        $pg['page'] = 'pg_'.$pg['page'];
    }
    $pgl = $pg['page'];
    gr_prnt(html_entity_decode(nl2br($GLOBALS["lang"]->$pgl)));
}
function gr_noswear($text, $method = 4) {
    $file = 'gem/ore/grupo/cache/filterwords.json';
    $bw = file_get_contents($file);
    $bw = json_decode($bw);
    if ($method == 1) {
        $words = preg_split("~\s+~", $text);
        foreach ($words as $key => $word) {
            $nword = preg_replace('~[.,*:+/?()\[\]!]~', '', $word);
            $nword = strtolower(str_replace('\\', '', $nword));
            if (!empty($nword)) {
                $matches = preg_grep("/\b(?<! )(?<!-)(?:".$nword.")(?!-)(?! )\b/i", $bw);
                if (count($matches) > 0) {
                    if ($nword == reset($matches)) {
                        $cw = str_repeat("*", strlen($word));
                        $words[$key] = $cw;
                    }
                }
            }
        }
        $text = implode(' ', $words);
    } else if ($method == 2) {
        foreach ($bw as $w) {
            $w = trim($w);
            $cw = str_repeat("*", strlen($w));
            if (preg_match('/[^a-zA-Z]+/', $w)) {
                $text = str_replace($w, $cw, $text);
            } else {
                $text = preg_replace("/\b".$w."\b/", $cw, $text);
            }
        }
    }
    return $text;
}

function grvalidatehtml($string) {
    $check = 0;
    if ($check == 1) {
        preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $string, $result);
        $openedtags = $result[1];
        preg_match_all('#</([a-z]+)>#iU', $string, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        if (count($closedtags) == $len_opened) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

function gr_cronjob() {
    if (!empty(vc($GLOBALS["default"]->fileexpiry, 'num'))) {
        fc('grfiles');
        $exp['type'] = 'expired';
        gr_files($exp);
    }

    $query = 'SELECT ';
    if (!empty(vc($GLOBALS["default"]->autodeletemsg, 'num'))) {
        $query = $query.'(SELECT group_concat(concat(type,",",msg) separator ";") FROM gr_msgs';
        $query = $query.' WHERE type="file" AND cat="group" AND tms < (CONVERT_TZ(NOW(),@@session.time_zone,"+05:30") -';
        $query = $query.' INTERVAL '.$GLOBALS["default"]->autodeletemsg.' MINUTE) OR type="audio" AND cat="group" AND tms < (CONVERT_TZ(NOW(),';
        $query = $query.'@@session.time_zone,"+05:30") - INTERVAL '.$GLOBALS["default"]->autodeletemsg.' MINUTE)) as groupmsgs,';
    }
    $query = $query.'(SELECT count(id) FROM gr_mails WHERE sent=0 LIMIT 20) as pendmails,';
    $query = $query.'(SELECT group_concat(concat(id) separator ",")';
    $query = $query.' FROM gr_users gr WHERE gr.role IN (SELECT id FROM gr_permissions WHERE autodel<>"Off")';
    $query = $query.' AND gr.created < (CONVERT_TZ(NOW(),@@session.time_zone,"+05:30") - INTERVAL';
    $query = $query.' (SELECT autodel FROM gr_permissions WHERE autodel<>"Off" AND';
    $query = $query.' id=gr.role) MINUTE)';
    $query = $query.' AND (SELECT v2 FROM gr_options WHERE v3=gr.id AND type="profile" AND v1="status" ORDER BY id DESC LIMIT 1)="offline"';
    $query = $query.' AND (SELECT privatemsg FROM gr_permissions WHERE id=gr.role) LIKE "%10"';
    $query = $query.' OR gr.role IN (SELECT id FROM gr_permissions WHERE autodel<>"Off")';
    $query = $query.' AND gr.created < (CONVERT_TZ(NOW(),@@session.time_zone,"+05:30") - INTERVAL';
    $query = $query.' (SELECT autodel FROM gr_permissions WHERE autodel<>"Off" AND';
    $query = $query.' id=gr.role) MINUTE)';
    $query = $query.' AND (SELECT count(id) FROM gr_permissions WHERE id=gr.role AND privatemsg LIKE "%10")=0';
    $query = $query.' ) as autodelusers FROM gr_options WHERE v3="1" AND type="profile" AND v1="name";';
    if (!empty(vc($GLOBALS["default"]->autodeletemsg, 'num'))) {
        $query = $query.'DELETE FROM gr_msgs';
        $query = $query.' WHERE type<>"like" AND cat="group" AND type <>"system" AND tms < (CONVERT_TZ(NOW(),@@session.time_zone,"+05:30") -';
        $query = $query.' INTERVAL '.$GLOBALS["default"]->autodeletemsg.' MINUTE);';
        $query = $query.'DELETE FROM gr_msgs ms';
        $query = $query.' WHERE type="like" AND NOT EXISTS (SELECT 1 FROM gr_msgs WHERE id = ms.msg);';
    }
    $query = $query.'DELETE FROM gr_options WHERE type="gruser" AND id IN (SELECT id FROM gr_options gr';
    $query = $query.' WHERE gr.type="gruser" AND gr.v2 IN (SELECT id FROM gr_users WHERE';
    $query = $query.' role IN (SELECT id FROM gr_permissions WHERE autounjoin<>"Off"))';
    $query = $query.' AND gr.tms < (CONVERT_TZ(NOW(),@@session.time_zone,"+05:30") - INTERVAL';
    $query = $query.' (SELECT autounjoin FROM gr_permissions WHERE autounjoin<>"Off" AND';
    $query = $query.' id=(SELECT role FROM gr_users WHERE id=gr.v2)) MINUTE));';
    $rq = db('Grupo', 'q', $query);
    if (isset($rq[0]['pendmails']) && !empty($rq[0]['pendmails'])) {
        //gr_pendmail();
    }
    if (isset($rq[0]['groupmsgs']) && !empty($rq[0]['groupmsgs'])) {
        $files = explode(';', $rq[0]['groupmsgs']);
        foreach ($files as $fl) {
            $file = explode(',', $fl);
            if ($file[0] === 'file') {
                if (file_exists('gem/ore/grupo/files/dumb/'.$file[1])) {
                    unlink('gem/ore/grupo/files/dumb/'.$file[1]);
                }
                if (file_exists('gem/ore/grupo/files/preview/'.$file[1])) {
                    unlink('gem/ore/grupo/files/preview/'.$file[1]);
                }
            } else if ($file[0] === 'audio') {
                if (file_exists('gem/ore/grupo/audiomsgs/'.$file[1])) {
                    unlink('gem/ore/grupo/audiomsgs/'.$file[1]);
                }
            }
        }
    }
    if (isset($rq[0]['autodelusers']) && !empty($rq[0]['autodelusers'])) {
        $rmuids = explode(',', $rq[0]['autodelusers']);
        $query = 'DELETE FROM gr_msgs WHERE ';
        $len = count($rmuids);
        $i = 1;
        foreach ($rmuids as $rmuid) {
            $query = $query.'gid LIKE "'.$rmuid.'-%" AND cat="user" OR gid LIKE "%-'.$rmuid.'" AND cat="user"';
            if ($i != $len) {
                $query = $query.' OR ';
            }
            $i = $i+1;
            foreach (glob("gem/ore/grupo/users/".$rmuid."-gr-*.*") as $filename) {
                unlink($filename);
            }
            foreach (glob("gem/ore/grupo/coverpic/users/".$rmuid."-gr-*.*") as $filename) {
                unlink($filename);
            }
            foreach (glob("gem/ore/grupo/audiomsgs/".$rmuid."-gr-*.*") as $filename) {
                unlink($filename);
            }
            flr('delete', 'grupo/files/'.$rmuid);
        }
        $query = $query.';DELETE FROM gr_options WHERE type="profile" AND v3 IN('.$rq[0]['autodelusers'].');';
        $query = $query.'DELETE FROM gr_options WHERE type="lview" AND v2 IN('.$rq[0]['autodelusers'].');';
        $query = $query.'DELETE FROM gr_options WHERE type="gruser" AND v2 IN('.$rq[0]['autodelusers'].');';
        $query = $query.'DELETE FROM gr_msgs WHERE uid IN('.$rq[0]['autodelusers'].');';
        $query = $query.'DELETE FROM gr_alerts WHERE uid IN('.$rq[0]['autodelusers'].');';
        $query = $query.'DELETE FROM gr_profiles WHERE type="profile" AND uid IN('.$rq[0]['autodelusers'].');';
        $query = $query.'DELETE FROM gr_alerts WHERE v3 IN('.$rq[0]['autodelusers'].');';
        $query = $query.'DELETE FROM gr_options WHERE type="deaccount" AND v3 IN('.$rq[0]['autodelusers'].');';
        $query = $query.'DELETE FROM gr_users WHERE id IN('.$rq[0]['autodelusers'].');';
        $query = $query.'DELETE FROM gr_session WHERE uid IN('.$rq[0]['autodelusers'].');';
        $query = $query.'UPDATE gr_logs SET v1="'.strtotime(dt()).'" WHERE type="cache";';


        $rq = db('Grupo', 'q', $query);

        echo count($rq[0]['autodelusers']).' users deleted';
    } else {
        echo '0 users deleted';
    }
}
function gr_gravatar($email, $s = 150, $d = 'mp', $r = 'g', $img = false, $atts = array()) {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=$s&d=$d&r=$r";
    if ($img) {
        $url = '<img src="' . $url . '"';
        foreach ($atts as $key => $val)
        $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}
function gr_custommenu() {
    $arg = func_get_args();
    if ($arg[0] === 'show') {
        $menu = db('Grupo', 's', 'options', 'type', 'menuitem', 'ORDER BY v3 ASC');
        foreach ($menu as $m) {
            $mnk = $m['v1'];
            gr_prnt('<li class="loadlink" link="'.$m['v2'].'">'.$GLOBALS["lang"]->$mnk.'</li>');
        }
    } else if ($arg[0]['type'] === 'delete') {
        if (gr_role('access', 'sys', '6')) {
            $oldmenu = db('Grupo', 's', 'options', 'type,id', 'menuitem', $arg[0]['id']);
            if (!empty($arg[0]['id']) && count($oldmenu) > 0) {
                db('Grupo', 'd', 'options', 'type,id', 'menuitem', $arg[0]['id']);
                $dlng = db('Grupo', 's', 'phrases', 'type', 'lang');
                foreach ($dlng as $dl) {
                    db('Grupo', 'd', 'phrases', 'type,short', 'phrase', $oldmenu[0]['v1']);
                    gr_cache('languages', $dl['id']);
                }
                gr_prnt('say("'.$GLOBALS["lang"]->deleted.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    }
}
function gr_ads() {
    $arg = func_get_args();
    if ($arg[0] === 'get') {
        $query = 'SELECT * FROM (SELECT content,adslot,adheight FROM gr_ads WHERE ';
        if (isset($arg[1]) && $arg[1] === 'signin') {
            $query = $query.'adslot = "siginpagefooter" OR adslot = "siginpageheader" ';
        } else {
            $query = $query.'adslot <> "chatmessage" AND adslot <> "siginpagefooter" AND adslot <> "siginpageheader" ';
        }
        $query = $query.'ORDER BY RAND ()) AS ads';
        return db('Grupo', 'q', $query);
    } else if ($arg[0] === 'place') {
        if (isset($GLOBALS["grads"][0]['adslot'])) {
            $class = '';
            if ($arg[1] == 'leftside') {
                if ($GLOBALS["grusrlog"]["radiostatus"] == 'radioenabled') {
                    $class = '.swr-grupo.radioenabled .lside > .content';
                } else {
                    $class = '.swr-grupo .lside > .content';
                }
            } else if ($arg[1] == 'rightside') {
                $class = '.swr-grupo .rside > .content';
            }
            foreach ($GLOBALS["grads"] as $grad) {
                if ($grad['adslot'] == $arg[1]) {
                    $adid = 'grad'.rn(4);
                    $ad_div = '<div class="gradslot '.$adid.'"><div>';
                    $adid = '.'.$adid;
                    $ad_div = $ad_div.$grad['content'];
                    $ad_div = $ad_div.'</div></div>';
                    $ad_div = $ad_div.'<style>';
                    $ad_div = $ad_div.$adid.'{height:'.$grad['adheight'].'px;}';
                    if ($GLOBALS["grusrlog"]["radiostatus"] == 'radioenabled' && $arg[1] == 'leftside') {
                        $grad['adheight'] = $grad['adheight']+55;
                    }
                    if (!empty($class)) {
                        $ad_div = $ad_div.$class.'{padding-bottom:'.$grad['adheight'].'px;}';
                    }
                    $ad_div = $ad_div.'</style>';
                    gr_prnt($ad_div);
                    return;
                }
            }
        }
    } else if ($arg[0]['type'] === 'delete') {
        if (gr_role('access', 'sys', '7')) {
            $ad = db('Grupo', 's', 'ads', 'id', $arg[0]['id']);
            if (!empty($arg[0]['id']) && count($ad) > 0) {
                db('Grupo', 'd', 'ads', 'id', $arg[0]['id']);
                gr_prnt('say("'.$GLOBALS["lang"]->deleted.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    }
}
function gr_loginproviders() {
    $arg = func_get_args();
    if ($arg[0] === 'show') {
        $providers = db('Grupo', 's,id', 'options', 'type', 'loginprovider', 'ORDER BY id ASC');
        if (isset($providers[0])) {
            $result = '<div class="loginproviders"><ul>';
            foreach ($providers as $provider) {
                $img = gr_img('loginprovider', $provider['id']);
                $result = $result.'<li no="'.$provider['id'].'"><img src="'.$img.'"/></li>';
            }
            $result = $result.'</ul></div>';
            gr_prnt($result);
        }
    } else if ($arg[0] === 'connect') {
        $provider = explode('/', pg('signin'));
        if (isset($provider[0]) && $provider[0] == 'provider') {
            if (isset($provider[1]) && !empty($provider[1])) {
                $provider = db('Grupo', 's', 'options', 'type,id', 'loginprovider', $provider[1]);
                if (isset($provider[0])) {
                    $data['provider'] = $provider[0]['v1'];
                    $data['callback'] = $GLOBALS["default"]->weburl.'signin/provider/'.$provider[0]['id'].'/';
                    $data['appid'] = $provider[0]['v2'];
                    $data['appsecret'] = $provider[0]['v3'];
                    $data['appkey'] = $provider[0]['v4'];
                    $r = usr('Grupo', 'sociallogin', $data);
                    if ($r[0] && $r[1] == 'register' && !empty(vc($r[2],'num'))) {
                        $id = $r[2];
                        if(isset($r[2]) && !empty($r[2])){
                        $userProfile = $r[3];
                        gr_data('i', 'profile', 'name', $userProfile->firstName, $id, $r[4], gr_usrcolor());
                        if (!empty($userProfile->photoURL)) {
                            $avatar = 'gem/ore/grupo/users/'.$id.'-gr-'.rn(10).'.png';
                            $ch = curl_init($userProfile->photoURL);
                            $fp = fopen($avatar, 'wb');
                            curl_setopt($ch, CURLOPT_FILE, $fp);
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                            curl_setopt($ch, CURLOPT_ENCODING, '');
                            curl_exec($ch);
                            curl_close($ch);
                            fclose($fp);
                        }
                        $grjoin = $GLOBALS["default"]->autogroupjoin;
                        if (!empty($grjoin)) {
                            
                            $cr = gr_group('valid', $grjoin);
                            if ($cr[0] && !empty($id)) {
                                gr_data('i', 'gruser', $grjoin, $id, 0);
                                $dt = array();
                                $dt['id'] = $grjoin;
                                $dt['msg'] = 'joined_group';
                                gr_group('sendmsg', $dt, 1, 1, $id);
                            }
                        }
                        usr('Grupo', 'forcelogin', $id);
                        }
                        gr_prnt("<script type='text/javascript'>  window.opener.location.reload(); </script>");
                        gr_prnt("<script type='text/javascript'> window.open('','_parent',''); window.close(); </script>");
                    } else if ($r['error'] == 'loggedin') {
                        gr_prnt("<script type='text/javascript'> window.opener.location.reload(); </script>");
                        gr_prnt("<script type='text/javascript'> window.open('','_parent',''); window.close(); </script>");
                    } else {
                        gr_prnt("Something went wrong. Kindly Check Error log : ");
                        print_r($r['error']);
                        gr_prnt("<br><br>Callback URL : ".$data['callback']);
                    }
                    exit;
                } else {
                    gr_prnt("<script type='text/javascript'> window.open('','_parent',''); window.close(); </script>");
                }
            }
        }
    } else if ($arg[0]['type'] === 'delete') {
        if (gr_role('access', 'sys', '8')) {
            $loginprovider = db('Grupo', 's', 'options', 'type,id', 'loginprovider', $arg[0]['id']);
            if (!empty($arg[0]['id']) && count($loginprovider) > 0) {
                foreach (glob("gem/ore/grupo/loginprovider/".$arg[0]['id']."-gr-*.*") as $filename) {
                    unlink($filename);
                }
                db('Grupo', 'd', 'options', 'type,id', 'loginprovider', $arg[0]['id']);
                gr_prnt('say("'.$GLOBALS["lang"]->deleted.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    }
}

function gr_stickers() {
    $arg = func_get_args();
    if ($arg[0] === 'show') {
        $dir = 'grupo/stickers/';
        $r = flr('list', $dir, 1);
        $list = '';
        foreach ($r as $f) {
            $n = basename($f);
            $sticker = $GLOBALS["default"]->weburl."gem/ore/grupo/icons/stickers.svg";
            $im = "gem/ore/grupo/stickers/".$n."/grstickericon.png";
            if (file_exists($im)) {
                $sticker = $GLOBALS["default"]->weburl.$im;
            }
            $list = $list.'<li no="'.$n.'" data-toggle="tooltip" title="'.$n.'"><img src="'.$sticker.'"></li>';
        }
        gr_prnt($list);
    } else if ($arg[0]['type'] === 'list' && isset($arg[0]['pack']) && !empty($arg[0]['pack'])) {
        if (isset($GLOBALS["roles"]['features'][17])) {
            $dir = 'grupo/stickers/'.$arg[0]['pack'].'/*.{jpg,png,gif,bmp,jpeg}';
            $r = flr('list', $dir, 'brace');
            $list = array();
            $i = 0;
            foreach ($r as $f) {
                $n = basename($f);
                if ($n != 'grstickericon.png') {
                    $list[$i] = $GLOBALS["default"]->weburl."gem/ore/grupo/stickers/".rawurlencode($arg[0]['pack'])."/".rawurlencode($n);
                    $i = $i+1;
                }
            }
            gr_prnt(json_encode($list));
        }
    } else if ($arg[0]['type'] === 'delete') {
        if (gr_role('access', 'features', '16')) {
            if (!empty($arg[0]['id'])) {
                flr('delete', 'grupo/stickers/'.$arg[0]['id']);
                gr_prnt('say("'.$GLOBALS["lang"]->deleted.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
            }
        }
    }
}
function gr_radiostations() {
    $arg = func_get_args();
    if ($arg[0] === 'show') {
        $orderby = 'v1';
        $stations = db('Grupo', 's,id,v1,v2,v3', 'options', 'type', 'radiostation', 'ORDER BY '.$orderby.' ASC');
        foreach ($stations as $station) {
            $img = gr_img('radiostations', $station['id']);
            gr_prnt('<li no="'.$station['id'].'" stream="'.$station['v3'].'" icon="'.$img.'" subtitle="'.$station['v2'].'">'.$station['v1'].'</li>');
        }
    } else if ($arg[0]['type'] === 'delete') {
        if (gr_role('access', 'features', '14')) {
            $radiostation = db('Grupo', 's', 'options', 'type,id', 'radiostation', $arg[0]['id']);
            if (!empty($arg[0]['id']) && count($radiostation) > 0) {
                foreach (glob("gem/ore/grupo/radiostations/".$arg[0]['id']."-gr-*.*") as $filename) {
                    unlink($filename);
                }
                db('Grupo', 'd', 'options', 'type,id', 'radiostation', $arg[0]['id']);
                gr_prnt('say("'.$GLOBALS["lang"]->deleted.'","s");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";$(".grupo-pop").fadeOut();');
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    }
}
function gr_customfield() {
    $uid = $GLOBALS["user"]['id'];
    $arg = func_get_args();
    if ($arg[0]['type'] === 'delete') {
        if (gr_role('access', 'fields', '3')) {
            $oldfield = db('Grupo', 's', 'profiles', 'type,id|,type,id', 'field', $arg[0]['id'], 'gfield', $arg[0]['id']);
            if (!empty($arg[0]['id']) && count($oldfield) > 0) {
                db('Grupo', 'd', 'profiles', 'type,id', 'field', $arg[0]['id']);
                db('Grupo', 'd', 'profiles', 'type,id', 'gfield', $arg[0]['id']);
                db('Grupo', 'd', 'profiles', 'type,name', 'profile', $arg[0]['id']);
                db('Grupo', 'd', 'profiles', 'type,name', 'group', $arg[0]['id']);
                $dlng = db('Grupo', 's', 'phrases', 'type', 'lang');
                foreach ($dlng as $dl) {
                    db('Grupo', 'd', 'phrases', 'type,short', 'phrase', $oldfield[0]['name']);
                    gr_cache('languages', $dl['id']);
                }
                gr_prnt('say("'.$GLOBALS["lang"]->deleted.'","s");menuclick("mmenu","ufields");$(".grupo-pop").fadeOut();');
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
            }
        }
    }

}
function gr_profile() {
    $uid = $GLOBALS["user"]['id'];
    $arg = func_get_args();

    if ($arg[0] === 'get') {
        $r = $GLOBALS["lang"]->unknown;
        if ($arg[2] === 'tmz') {
            $r = $GLOBALS["default"]->timezone;
        } else if ($arg[2] === 'language') {
            $r = $GLOBALS["default"]->language;
        } else if ($arg[2] === 'alert') {
            $r = $GLOBALS["default"]->alert;
        } else if ($arg[2] === 'status') {
            $r = 'offline';
        } else if ($arg[2] === 'skinmode') {
            $r = 'light';
            if ($GLOBALS["default"]->default_skin_mode == 'dark_mode') {
                $r = 'dark';
            }
        }
        if (isset($arg[3])) {
            $r = $arg[3];
        }
        $cr = db('Grupo', 's', 'options', 'type,v1,v3', 'profile', $arg[2], $arg[1]);
        if ($cr && count($cr) > 0) {
            $r = $cr[0]['v2'];
            if ($arg[2] === 'status' && $r === 'invisible') {
                $r = 'offline';
            }
            if ($arg[2] === 'status' && $r === 'online' || $r === 'idle') {
                $idle = strtotime(dt()) - strtotime($cr[0]['tms']);
                $idle = round(abs($idle) / 60);
                $statz = $r;
                if ($idle > 60) {
                    $statz = 'offline';
                    gr_profile('ustatus', 'offline', $arg[1]);
                } else if ($idle > 20 && $r !== 'idle') {
                    $statz = 'idle';
                    gr_profile('ustatus', 'idle', $arg[1]);
                }
                $r = $statz;
            }
        }
        if ($arg[2] == 'tmz' && $r == 'Auto' && !isset($arg[3])) {
            $r = db('Grupo', 's', 'options', 'type,v1,v3', 'profile', 'autotmz', $arg[1])[0]['v2'];
        }
        return $r;
    } else if ($arg[0] === 'blocked') {
        $r[0] = false;
        $r[1] = 'you';
        $chkblocked = db('Grupo', 's,count(*)', 'options', 'type,v1,v2', 'pblock', $uid, $arg[1])[0][0];
        $byu = db('Grupo', 's,count(*)', 'options', 'type,v2,v1', 'pblock', $uid, $arg[1])[0][0];
        if ($byu > 0 && $chkblocked == 0) {
            $r[1] = 'other';
        }
        $chkblocked = $chkblocked+$byu;
        if ($chkblocked > 0) {
            $r[0] = true;
        }
        return $r;
    } else if ($arg[0] === 'mode') {
        if (gr_profile('get', $uid, 'status') === 'offline') {
            gec($GLOBALS["lang"]->go_online);
        } else {
            gec($GLOBALS["lang"]->go_offline);
        }

    } else if ($arg[0] === 'skinmode') {
        if (gr_profile('get', $uid, 'skinmode') === 'dark') {
            gec($GLOBALS["lang"]->light_mode);
        } else {
            gec($GLOBALS["lang"]->dark_mode);
        }

    } else if ($arg[0] === 'ustatus') {
        if (!empty($arg[1]) && !empty($uid) || isset($arg[2])) {
            if (isset($arg[2])) {
                $uid = $arg[2];
            }
            if ($arg[1] == 'offline' || $arg[1] == 'idle') {
                db('Grupo', 'u', 'logs', 'v3,tms', 'type,v1', 0, dt(), 'browsing', $uid);
            }
            if ($GLOBALS["default"]->releaseguestuser == 'enable' && $arg[1] == 'offline') {
                $usrfnd = usr('Grupo', 'select', $uid);
                if ($usrfnd['role'] == 5) {
                    usr('Grupo', 'alter', 'name', $usrfnd['name'].rn(5), $uid);
                }
            }
            $ct = db('Grupo', 's', 'options', 'type,v1,v3', 'profile', 'status', $uid);
            if ($ct && count($ct) > 0) {
                if ($ct[0]['v2'] !== 'invisible' || isset($arg[2])) {
                    gr_data('u', 'v2', 'type,v1,v3', $arg[1], 'profile', 'status', $uid);
                }
            } else {
                gr_data('i', 'profile', 'status', $arg[1], $uid);
            }
        }
    } else if ($arg[0]['type'] === 'block') {
        $grky = 'grpuser'.$arg[0]["id"];
        $ct = db('Grupo', 's,count(*)', 'options', 'type,v1,v2', 'pblock', $uid, $arg[0]["id"])[0][0];
        if ($ct > 0) {
            db('Grupo', 'd', 'options', 'type,v1,v2', 'pblock', $uid, $arg[0]["id"]);
            gr_prnt('say("'.$GLOBALS["lang"]->unblocked.'","s");');
        } else {
            db('Grupo', 'i', 'options', 'type,v1,v2', 'pblock', $uid, $arg[0]["id"]);
            gr_prnt('say("'.$GLOBALS["lang"]->blocked.'","e");');
        }
        gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
        gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
    } else if ($arg[0]['type'] === 'iplogdelete') {
        if (isset($GLOBALS["roles"]['users'][9])) {
            db('Grupo', 'd', 'utrack', 'id', $arg[0]["id"]);
            gr_prnt('$(".swr-grupo .lside > .tabs > ul > li[act=iplogs]").trigger("click");');
            gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
        }
    } else if ($arg[0]['type'] === 'autotimezone') {
        if (!isset($arg[0]['timez'])) {
            $arg[0]['timez'] = 0;
        }
        gr_autotms($arg[0]['offset'], $arg[0]['timez']);
    } else if ($arg[0]['type'] === 'mode') {
        if (isset($GLOBALS["roles"]['features'][11])) {
            $ct = db('Grupo', 's', 'options', 'type,v1,v3', 'profile', 'status', $uid);
            if ($ct && count($ct) > 0) {
                $s = 'invisible';
                if ($ct[0]['v2'] === 'invisible') {
                    $s = 'online';
                }
                gr_data('u', 'v2', 'type,v1,v3', $s, 'profile', 'status', $uid);
                gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
            }
        }
    } else if ($arg[0]['type'] === 'skinmode') {
        $ct = db('Grupo', 's', 'options', 'type,v1,v3', 'profile', 'skinmode', $uid);
        if ($ct && count($ct) > 0) {
            $s = 'dark';
            if ($ct[0]['v2'] === 'dark') {
                $s = 'light';
            }
            gr_data('u', 'v2', 'type,v1,v3', $s, 'profile', 'skinmode', $uid);
        } else {
            db('Grupo', 'i', 'options', 'type,v1,v3,v2', 'profile', 'skinmode', $uid, 'dark');
        }
        gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
    } else if ($arg[0]['type'] === 'act' && $arg[0]['opted'] === 'delete') {
        if (!gr_role('access', 'users', '3') && !isset($arg[0]['nomsgz'])) {
            exit;
        }
        if ($uid !== $arg[0]['id'] || isset($arg[0]['nomsgz'])) {
            $r = db('Grupo', 's,count(*)', 'users', 'id', $arg[0]["id"])[0][0];
            if ($r > 0) {
                usr('Grupo', 'delete', $arg[0]['id']);
                gr_data('d', 'type,v3', 'profile', $arg[0]["id"]);
                gr_data('d', 'type,v2', 'lview', $arg[0]["id"]);
                gr_data('d', 'type,v2', 'gruser', $arg[0]["id"]);
                db('Grupo', 'd', 'msgs', 'uid,type', $arg[0]["id"], 'msg');
                db('Grupo', 'd', 'msgs', 'uid,type', $arg[0]["id"], 'file');
                db('Grupo', 'd', 'msgs', 'uid,type', $arg[0]["id"], 'system');
                db('Grupo', 'd', 'alerts', 'uid', $arg[0]["id"]);
                db('Grupo', 'd', 'options', 'type,v2', 'loves', $arg[0]["id"]);
                db('Grupo', 'd', 'profiles', 'type,uid', 'profile', $arg[0]["id"]);
                db('Grupo', 'd', 'alerts', 'v3', $arg[0]["id"]);
                db('Grupo', 'd', 'options', 'type,v3', 'deaccount', $arg[0]["id"]);
                foreach (glob("gem/ore/grupo/users/".$arg[0]['id']."-gr-*.*") as $filename) {
                    unlink($filename);
                }
                foreach (glob("gem/ore/grupo/coverpic/users/".$arg[0]['id']."-gr-*.*") as $filename) {
                    unlink($filename);
                }
                foreach (glob("gem/ore/grupo/audiomsgs/".$arg[0]['id']."-gr-*.*") as $filename) {
                    unlink($filename);
                }
                flr('delete', 'grupo/files/'.$arg[0]['id']);
                $usz = $arg[0]['id'];
                $delvac = db('Grupo', 's', 'users');
                foreach ($delvac as $lvu) {
                    if ($usz != $lvu['id']) {
                        $delvw = $usz.'-'.$lvu['id'];
                        if ($usz > $lvu['id']) {
                            $delvw = $lvu['id'].'-'.$usz;
                        }
                        gr_data('d', 'type,v1', 'lview', $delvw);
                        db('Grupo', 'd', 'msgs', 'cat,gid', 'user', $delvw);
                    }
                }
                if (!isset($arg[0]['nomsgz'])) {
                    gr_prnt('say("'.$GLOBALS["lang"]->deleted.'","s");menuclick("mmenu","users");');
                    gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
                }
            }
        }
    } else if ($arg[0]['type'] === 'act' && $arg[0]['opted'] === 'banip') {
        if (!gr_role('access', 'sys', '3')) {
            exit;
        }
        if ($uid !== $arg[0]['id']) {
            db('Grupo', 'd', 'session', 'uid', $arg[0]["id"]);
            $bl = db('Grupo', 's', 'defaults', 'type', 'blacklist')[0]['v2'];
            $uip = db('Grupo', 's,ip', 'utrack', 'uid', $arg[0]["id"]);
            foreach ($uip as $ui) {
                $bl = $ui['ip']."\n".$bl;
            }
            db('Grupo', 'u', 'defaults', 'v2', 'type', $bl, 'blacklist');
            gr_cache('blacklist');
            gr_prnt('say("'.$GLOBALS["lang"]->banned.'","s");menuclick("mmenu","users");');
            gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
        }
    } else if ($arg[0]['type'] === 'act' && $arg[0]['opted'] === 'unbanip') {
        if (!gr_role('access', 'sys', '3')) {
            exit;
        }
        if ($uid !== $arg[0]['id']) {
            $bl = db('Grupo', 's', 'defaults', 'type', 'blacklist')[0]['v2'];
            $uip = db('Grupo', 's,ip', 'utrack', 'uid', $arg[0]["id"]);
            foreach ($uip as $ui) {
                $bl = str_replace($ui['ip'], "", $bl);
            }
            $bl = preg_replace("/[\r\n]+/", "\n", $bl);
            db('Grupo', 'u', 'defaults', 'v2', 'type', $bl, 'blacklist');
            gr_cache('blacklist');
            gr_prnt('say("'.$GLOBALS["lang"]->unblocked.'","s");menuclick("mmenu","users");');
            gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
        }
    } else if ($arg[0]['type'] === 'act' && $arg[0]['opted'] === 'ban') {
        if (!gr_role('access', 'users', '8')) {
            exit;
        }
        if ($uid !== $arg[0]['id']) {
            $r = db('Grupo', 's,count(*)', 'users', 'id', $arg[0]["id"])[0][0];
            if ($r > 0) {
                gr_profile('ustatus', 'offline', $arg[0]['id']);
                usr('Grupo', 'forcelogout', $arg[0]['id']);
                usr('Grupo', 'alter', 'role', 4, $arg[0]['id']);
                gr_prnt('say("'.$GLOBALS["lang"]->banned.'","s");menuclick("mmenu","users");');
                gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
            }
        }
    } else if ($arg[0]['type'] === 'act' && $arg[0]['opted'] === 'unban') {
        if (!gr_role('access', 'users', '8')) {
            exit;
        }
        if ($uid !== $arg[0]['id']) {
            $r = db('Grupo', 's,count(*)', 'users', 'id', $arg[0]["id"])[0][0];
            if ($r > 0) {
                usr('Grupo', 'alter', 'role', 3, $arg[0]['id']);
                gr_prnt('say("'.$GLOBALS["lang"]->unbanned.'","s");menuclick("mmenu","users");');
                gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
            }
        }
    } else if ($arg[0]['type'] === 'login') {
        if (!gr_role('access', 'users', '6')) {
            exit;
        }
        gr_profile('ustatus', 'offline');
        addcookie('utrack', $GLOBALS["grusrlog"]['disabletrackcode'], 0, "/");
        if (isset($_COOKIE['Grupousrses'])) {
            unset($_COOKIE['Grupousrcode']);
            unset($_COOKIE['Grupousrses']);
            unset($_COOKIE['Grupousrdev']);
            addcookie('Grupousrses', '', time() - 3600, '/');
            addcookie('Grupousrcode', '', time() - 3600, '/');
            addcookie('Grupousrdev', '', time() - 3600, '/');
        }
        usr('Grupo', 'forcelogin', $arg[0]['id']);
        gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
    }

}
function gr_autotms($offset, $tmz) {
    $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    if (in_array($tmz, $tzlist)) {
        $tmval = $tmz;
    } else {
        $tmval = timezone_name_from_abbr("", $offset, 0);
        if ($tmval == false) {
            $tmval = gr_detecttmz($offset);
        }
        if (!in_array($tmval, $tzlist)) {
            $tmval = 'Australia/Sydney';
        }
    }
    $uid = $GLOBALS["user"]['id'];
    if (!isset($tmval) || empty($tmval)) {
        $tmval = 'America/New_York';
    }
    $ct = db('Grupo', 's,count(*)', 'options', 'type,v1,v3', 'profile', 'autotmz', $uid)[0][0];
    if ($ct == 0) {
        gr_data('i', 'profile', 'autotmz', $tmval, $uid);
    } else {
        gr_data('u', 'v2', 'type,v1,v3', $tmval, 'profile', 'autotmz', $uid);
    }
}
function gr_detecttmz($offset) {
    $abbrarray = timezone_abbreviations_list();
    foreach ($abbrarray as $abbr) {
        foreach ($abbr as $city) {
            if ($city['offset'] == $offset) {
                return $city['timezone_id'];
            }
        }
    }
}
function gr_prnt() {
    $arg = func_get_args();
    if (isset($arg[1])) {
        $arg[0] = htmlspecialchars($arg[0]);
    }
    echo $arg[0];
}
function gr_usip() {
    $arg = func_get_args();
    if ($arg[0] === 'add') {
        if ($GLOBALS["logged"]) {
            if (!isset($_COOKIE['utrack'])) {
                $_COOKIE['utrack'] = 'on';
            }
            if ($_COOKIE['utrack'] != $GLOBALS["grusrlog"]['disabletrackcode']) {
                $uid = $GLOBALS["user"]['id'];
                if ($GLOBALS["grusrlog"]['usrtrack'] == 0) {
                    db('Grupo', 'i', 'utrack', 'ip,dev,uid,tms', ip(), ip('dev'), $uid, dt());
                }
            }
        }
    } else if ($arg[0] === 'ban') {
        $r = db('Grupo', 'u', 'utrack', 'status', 'ip,dev,uid', 1, ip(), ip('dev'), $arg[1], 'ORDER BY id DESC');
    } else if ($arg[0] === 'unban') {
        $r = db('Grupo', 'u', 'utrack', 'status', 'ip,dev,uid', 0, ip(), ip('dev'), $arg[1], 'ORDER BY id DESC');
    } else if ($arg[0] === 'check') {
        if (isset($arg[1])) {
            $r = db('Grupo', 's,count(*)', 'utrack', 'ip,dev,uid,status', ip(), ip('dev'), $arg[1], 1)[0][0];
        } else {
            $r = db('Grupo', 's,count(*)', 'utrack', 'ip,dev,status', ip(), ip('dev'), 1)[0][0];
        }
        if ($r > 0) {
            return true;
        } else {
            return false;
        }
    }
}
function gr_default() {
    $arg = func_get_args();
    if ($arg[0] === 'get') {
        $r = null;
        $file = 'gem/ore/grupo/cache/defaults.cch';
        $r = json_decode(file_get_contents($file));
        $k = $arg[1];
        $r = $r->$k;
        return $r;
    } else if ($arg[0] === 'var') {
        $file = 'gem/ore/grupo/cache/defaults.cch';
        $r = json_decode(file_get_contents($file));
        return $r;
    }
}
function gr_core() {
    $arg = func_get_args();
    if ($arg[0] === 'hf') {
        if ($arg[1] === 'header') {
            include("gem/ore/grupo/cache/headers.cch");
        } else if ($arg[1] === 'footer') {
            include("gem/ore/grupo/cache/footers.cch");
        } else if ($arg[1] === 'bodyopen') {
            include("gem/ore/grupo/cache/bodyopen.cch");
        } else if ($arg[1] === 'bodyclose') {
            include("gem/ore/grupo/cache/bodyclose.cch");
        }
    }
}

function gr_group() {
    $uid = $GLOBALS["user"]['id'];
    $arg = func_get_args();
    if ($arg[0] === 'valid') {
        $arg[1] = vc($arg[1]);
        $r[0] = false;
        if (!empty($arg[1])) {
            if (isset($arg[2]) && $arg[2] === 'user') {
                if ($arg[1] !== $uid) {
                    $vusr = db('Grupo', 's', 'users', 'id', $arg[1]);
                    if (count($vusr) > 0) {
                        $r[0] = true;
                        $r['name'] = $GLOBALS["lang"]->conversation_with.' '.gr_profile('get', $arg[1], 'name');
                    }
                }
            } else {
                $cr = db('Grupo', 's', 'options', 'type,id', 'group', $arg[1]);
                if ($cr && count($cr) > 0) {
                    $r[0] = true;
                    $r['name'] = $cr[0]['v1'];
                    $r['pass'] = $cr[0]['v2'];
                    $r['code'] = $cr[0]['v3'];
                    $r['visible'] = $cr[0]['v3'];
                    $r['access'] = $cr[0]['v4'];
                    $r['messaging'] = $cr[0]['v5'];
                    $r['leavegroup'] = $cr[0]['v6'];
                    $r['credits'] = $cr[0]['v7'];
                }
            }
        }
        return $r;
    } else if ($arg[0] === 'validmsg') {
        $arg[1] = vc($arg[1], 'num');
        $arg[2] = vc($arg[2], 'num');
        $r[0] = false;
        if (!empty($arg[1]) && !empty($arg[2])) {
            if (isset($arg[3]) && $arg[3] == 'user') {
                $tmpido = $arg[1].'-'.$uid;
                if ($arg[1] > $uid) {
                    $tmpido = $uid.'-'.$arg[1];
                }
                $cr = db('Grupo', 's', 'msgs', 'gid,id,cat', $tmpido, $arg[2], 'user');
            } else {
                $cr = db('Grupo', 's', 'msgs', 'gid,id', $arg[1], $arg[2]);
            }
            if ($cr && count($cr) > 0) {
                $r[0] = true;
                $r['msg'] = $cr[0]['msg'];
                $r['uid'] = $cr[0]['uid'];
                $r['type'] = $cr[0]['type'];
            }
        }
        return $r;
    } else if ($arg[0] === 'invite') {
        if (gr_role('access', 'groups', '5') || gr_role('access', 'groups', '7')) {
            $cu = gr_group('user', $arg[1]["id"], $uid);
            if ($cu[0] && $cu['role'] != 3) {
                $grpn = gr_group('valid', $arg[1]["id"]);
                if (gr_role('access', 'groups', '7') || empty($grpn['pass']) && $grpn['visible'] != 'secret' || $cu['role'] == 1 || $cu['role'] == 2) {
                    $users = explode(',', $arg[1]["users"]);
                    foreach ($users as $u) {
                        $emv = $us = vc($u, 'email');
                        if (empty($us)) {
                            $us = str_replace('@', '', $u);
                        }
                        $in = usr('Grupo', 'select', $us);
                        if (isset($in['id'])) {
                            $uc = gr_group('user', $arg[1]["id"], $in['id']); {
                                if (!$uc[0]) {
                                    gr_alerts('new', 'invitation', $in['id'], $arg[1]["id"], 0, $uid);
                                    if (in_array(2, $GLOBALS["default"]->send_email_notification)) {
                                        gr_mail('invitation', $in['id'], $arg[1]["id"], rn(5));
                                    }
                                }
                            }
                        } else if (!empty($emv)) {
                            if (in_array(2, $GLOBALS["default"]->send_email_notification)) {
                                gr_mail('invitenonmember', $emv, $arg[1]["id"], rn(5));
                            }
                        }
                    }
                    gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");say("'.$GLOBALS["lang"]->invited.'","s");');
                }
            }
        }

    } else if ($arg[0] === 'unseen') {
        $cnt = 0;
        if (isset($arg[1])) {
            $src = '"'.$uid.'-%"';
            $srck = '"%-'.$uid.'"';
            $r = db('Grupo', 'q', 'SELECT max(id) as id,gid FROM gr_msgs WHERE gid LIKE '.$src.' OR gid LIKE '.$srck.' AND cat="user" GROUP by gid ORDER by id DESC');
            foreach ($r as $v) {
                $lview = db('Grupo', 's,v3', 'options', 'type,v1,v2', 'lview', $v['gid'], $uid, 'ORDER BY id DESC LIMIT 1');
                if (isset($lview[0])) {
                    $cnt = $cnt+db('Grupo', 's,count(id)', 'msgs', 'gid,id>', $v['gid'], $lview[0]['v3'])[0][0];
                } else {
                    $cnt = $cnt+ db('Grupo', 's,count(id)', 'msgs', 'gid,cat', $v['gid'], 'user')[0][0];
                }
            }
        } else {
            $gr = db('Grupo', 's', 'options', 'type,v2,v3<>', 'gruser', $uid, 3);
            foreach ($gr as $r) {
                $lview = db('Grupo', 's,v3', 'options', 'type,v1,v2', 'lview', $r['v1'], $uid, 'ORDER BY id DESC LIMIT 1');
                if (isset($lview[0])) {
                    $cnt = $cnt+db('Grupo', 's,count(id)', 'msgs', 'gid,type<>,id>', $r['v1'], 'like', $lview[0]['v3'])[0][0];
                } else {
                    $cnt = $cnt+ db('Grupo', 's,count(id)', 'msgs', 'gid,type<>,cat', $r['v1'], 'like', 'group')[0][0];
                }
            }
        }
        return $cnt;
    } else if ($arg[0] === 'complaints') {
        $cu = gr_group('user', $arg[1], $uid);
        if (!$cu[0] || $cu['role'] == 3 && !gr_role('access', 'groups', '7')) {
            return;
        }
        if (gr_role('access', 'groups', '7')) {
            $r = db('Grupo', 's,count(id)', 'complaints', 'gid,status', $arg[1], 1, 'ORDER BY status ASC')[0][0];
        } else if ($cu['role'] == 2 || $cu['role'] == 1) {
            $r = db('Grupo', 's,count(id)', 'complaints', 'gid,msid<>,status', $arg[1], 0, 1, 'ORDER BY status ASC')[0][0];
        } else {
            $r = db('Grupo', 's,count(id)', 'complaints', 'uid,gid,status', $uid, $arg[1], 1, 'ORDER BY id DESC')[0][0];
        }
        return $r;
    } else if ($arg[0] === 'reportmsg') {
        $r = db('Grupo', 's', 'msgs', 'id,gid', $arg[1]["msid"], $arg[1]["id"]);
        if (count($r) > 0 || empty($arg[1]["msid"])) {
            $cu = gr_group('user', $arg[1]["id"], $uid);
            if ($cu[0] && $cu['role'] != 3) {
                if (isset($arg[1]["reason"]) && isset($arg[1]["comment"]) && !empty($arg[1]["comment"])) {
                    db('Grupo', 'i', 'complaints', 'gid,uid,msid,type,comment,tms', $arg[1]["id"], $uid, $arg[1]["msid"], $arg[1]["reason"], $arg[1]["comment"], dt());
                    gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");say("'.$GLOBALS["lang"]->reported.'","s");');
                } else {
                    gr_prnt('say("'.$GLOBALS["lang"]->invalid_value.'");');
                }
            }
        }
    } else if ($arg[0] === 'clearchat') {
        if (isset($GLOBALS["roles"]['privatemsg']['4'])) {
            $tmpido = $arg[1]["id"].'-'.$uid;
            if ($arg[1]["id"] > $uid) {
                $tmpido = $uid.'-'.$arg[1]["id"];
            }
            $r = db('Grupo', 's,id', 'msgs', 'gid', $tmpido, 'ORDER BY id DESC LIMIT 1');
            if (isset($r[0]['id'])) {
                $rj = db('Grupo', 's,id', 'options', 'type,v1,v2', 'clearchat', $uid, $arg[1]["id"], 'ORDER BY id DESC LIMIT 1');
                if (isset($rj[0]['id'])) {
                    db('Grupo', 'u', 'options', 'v3,tms', 'type,v1,v2', $r[0]['id'], dt(), 'clearchat', $uid, $arg[1]["id"]);
                } else {
                    db('Grupo', 'i', 'options', 'type,v1,v2,v3,tms', 'clearchat', $uid, $arg[1]["id"], $r[0]['id'], dt());
                }
                gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");say("'.$GLOBALS["lang"]->cleared.'","s");');
                gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
            }
        }

    } else if ($arg[0] === 'takeaction') {
        $cm = db('Grupo', 's', 'complaints', 'id', $arg[1]["id"]);
        if (count($cm) != 0) {
            if (empty($cm[0]["msid"]) && !gr_role('access', 'groups', '7')) {
                exit;
            }
            $cu = gr_group('user', $cm[0]['gid'], $uid);
            if ($cu['role'] == 2 || $cu['role'] == 1 || gr_role('access', 'groups', '7')) {
                if (!empty($arg[1]["status"])) {
                    db('Grupo', 'u', 'complaints', 'status', 'id', $arg[1]["status"], $arg[1]["id"]);
                }
                gr_prnt('$(".grtab.active").trigger("click");say("'.$GLOBALS["lang"]->updated.'","s");');
            }
        }
        gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
    } else if ($arg[0] === 'user') {
        $arg[1] = vc($arg[1]);
        $arg[2] = vc($arg[2], 'num');
        $r[0] = false;
        $r['role'] = 0;
        if (!empty($arg[1]) && !empty($arg[2])) {
            if (isset($arg[3]) && $arg[3] == 'user') {
                $vusra = db('Grupo', 's,count(id)', 'users', 'id', $arg[1])[0][0];
                $vusrb = db('Grupo', 's,count(id)', 'users', 'id', $arg[2])[0][0];
                if ($vusra > 0 && $vusrb > 0) {
                    $r[0] = true;
                    $r['role'] = 0;
                }
            } else {
                $cr = db('Grupo', 's', 'options', 'type,v1,v2', 'gruser', $arg[1], $arg[2]);
                if (count($cr) > 0) {
                    $r[0] = true;
                    $r['role'] = $cr[0]['v3'];
                }
            }
        }
        return $r;
    } else if ($arg[0] === 'sendmsg') {
        if (!isset($arg[1]["ldt"]) || empty($arg[1]["ldt"])) {
            $arg[1]["ldt"] = 'group';
        }

        if (isset($arg[4])) {
            $uid = $arg[4];
        }
        if (isset($GLOBALS["roles"]['features'][13]) && isset($arg[1]["userid"]) && !empty(vc($arg[1]["userid"], 'num'))) {
            $uid = $arg[1]["userid"];
        }
        $gif = 0;
        $rqm = array();
        $tmpido = $arg[1]["id"];
        $query = 'SELECT ';
        if ($arg[1]["ldt"] == 'user') {
            $tmpido = $arg[1]["id"].'-'.$uid;
            if ($arg[1]["id"] > $uid) {
                $tmpido = $uid.'-'.$arg[1]["id"];
            }
            $query = $query.'(SELECT v2 FROM gr_options WHERE v3="'.$arg[1]["id"].'" AND type="profile" AND v1="status" LIMIT 1) AS status,';
            $query = $query.'(SELECT count(id) FROM gr_options WHERE type="deaccount" AND v1="yes" AND v3="'.$arg[1]["id"].'") AS deaccount,';
            $query = $query.'(SELECT count(id) FROM gr_options WHERE type="profile" AND v1="privatemsgs" AND v2="disable" AND v3="'.$arg[1]["id"].'") AS pmdisabled,';
            $query = $query.'(SELECT count(id)FROM gr_options WHERE type="pblock" AND v1="'.$uid.'" AND v2="'.$arg[1]["id"].'") AS yblock,';
            $query = $query.'(SELECT count(id)FROM gr_options WHERE type="pblock" AND v2="'.$uid.'" AND v1="'.$arg[1]["id"].'") AS oblock,';
            $query = $query.'(SELECT count(id)FROM gr_msgs WHERE gid="'.$tmpido.'" LIMIT 3) AS totalmsgs,';
            $query = $query.'(SELECT v3 FROM gr_logs WHERE type="browsing" AND v1="'.$arg[1]["id"].'" LIMIT 1) AS browsing,';
        } else {
            $query = $query.'(SELECT count(id) FROM gr_options WHERE type="gruser" AND v1="'.$arg[1]["id"].'" AND v2="'.$uid.'") AS grjoin,';
            $query = $query.'(SELECT v3 FROM gr_options WHERE type="gruser" AND v1="'.$arg[1]["id"].'" AND v2="'.$uid.'" LIMIT 1) AS grrole,';
            $query = $query.'(SELECT v5 FROM gr_options WHERE type="group" AND id="'.$arg[1]["id"].'" LIMIT 1) AS grmessaging,';
        }
        if (!empty($arg[1]["rid"]) && $gif == 0) {
            $query = $query.'(SELECT uid FROM gr_msgs WHERE cat="'.$arg[1]["ldt"].'" AND id="'.$arg[1]["rid"].'" AND gid="'.$tmpido.'" LIMIT 1) AS rpuid,';
            $query = $query.'(SELECT type FROM gr_msgs WHERE cat="'.$arg[1]["ldt"].'" AND id="'.$arg[1]["rid"].'" AND gid="'.$tmpido.'" LIMIT 1) AS rptype,';
            $query = $query.'(SELECT msg FROM gr_msgs WHERE cat="'.$arg[1]["ldt"].'" AND id="'.$arg[1]["rid"].'" AND gid="'.$tmpido.'" LIMIT 1) AS rpmsg,';
        }
        $query = $query.'(SELECT count(id) FROM gr_msgs WHERE uid="'.$uid.'" AND type <> "system" ';
        $query = $query.'AND tms > (CONVERT_TZ(NOW(),@@session.time_zone,"+05:30") - INTERVAL 60 SECOND)) AS fdcontrol,';
        $query = $query.'(SELECT tms FROM gr_msgs WHERE uid="'.$uid.'" AND type <> "system" ';
        $query = $query.'AND tms > (CONVERT_TZ(NOW(),@@session.time_zone,"+05:30") - INTERVAL 60 SECOND) ORDER BY id ASC LIMIT 1) AS fdctms ';
        $query = $query.'FROM gr_options WHERE v3="'.$uid.'" AND type="profile" AND v1="name";';

        if ($arg[1]["ldt"] == 'user') {
            $query = $query. 'UPDATE gr_options SET tms="'.dt().'",';
            $query = $query.'v3=0 WHERE type="lview" AND v1="'.$tmpido.'" AND v2="'.$arg[1]["id"].'"';
            $query = $query.' AND (SELECT count(id) FROM gr_msgs WHERE gid="'.$tmpido.'" LIMIT 3)=0;';
            $query = $query.'INSERT INTO gr_options (type,v2,v1,v3,tms) SELECT DISTINCT ';
            $query = $query.'"lview", "'.$arg[1]["id"].'", "'.$tmpido.'", 0, "'.dt().'"';
            $query = $query.' FROM gr_options WHERE NOT EXISTS';
            $query = $query.' (SELECT DISTINCT v3 FROM gr_options WHERE type="lview" AND v1="'.$tmpido.'" AND v2="'.$arg[1]["id"].'");';
        }

        $scheck = db('Grupo', 'q', $query);

        if ($arg[1]["ldt"] == 'user') {
            if ($scheck[0]['deaccount'] > 0 || $scheck[0]['pmdisabled'] > 0 || $scheck[0]['yblock']+$scheck[0]['oblock'] > 0 || !isset($GLOBALS["roles"]['privatemsg'][1])) {
                $list[0] = new stdClass();
                $list[0]->gid = $arg[1]["id"];
                $list[0]->nomem = 'refresh';
                gr_prnt(json_encode($list));
                exit;
            }
        } else {
            if ($scheck[0]['grjoin'] == 0 || $scheck[0]['grrole'] == 3 || !isset($GLOBALS["roles"]['groups'][7]) && $scheck[0]['grrole'] != 1 && $scheck[0]['grrole'] != 2 && $scheck[0]['grmessaging'] == 'adminonly') {
                $list[0] = new stdClass();
                $list[0]->gid = $arg[1]["id"];
                $list[0]->nomem = 'refresh';
                gr_prnt(json_encode($list));
                exit;
            }
        }
        if (!empty(trim($arg[1]["msg"]))) {
            $typ = 'msg';
            $prlnk = array();
            $prlnk['title'] = $prlnk['description'] = $prlnk['image'] = $prlnk['url'] = null;
            $rmid = $rtxt = $rid = 0;
            if (isset($arg[2])) {
                if ($arg[2] === 1) {
                    $typ = 'system';
                } else if ($arg[2] === 2) {
                    $typ = 'file';
                } else if ($arg[2] === 3) {
                    $typ = 'audio';
                }
            }
            if (!isset($GLOBALS["roles"]['features'][1]) && $typ == 'msg') {
                $list[0] = new stdClass();
                $list[0]->gid = $arg[1]["id"];
                $list[0]->nomem = 'refresh';
                gr_prnt(json_encode($list));
                exit;
            }
            $GLOBALS["default"]->sending_limit = vc($GLOBALS["default"]->sending_limit, 'num');
            if ($typ != 'system' && !empty($GLOBALS["default"]->sending_limit)) {
                if ($scheck[0]['fdcontrol'] >= $GLOBALS["default"]->sending_limit) {
                    $list[0] = new stdClass();
                    $list[0]->gid = $arg[1]["id"];
                    $list[0]->messageflood = 1;
                    $diff = strtotime(dt()) - strtotime($scheck[0]['fdctms']);
                    $diff = 60-$diff;
                    $list[0]->floodwait = $diff.' '.$GLOBALS["lang"]->seconds;
                    gr_prnt(json_encode($list));
                    exit;
                }
            }
            if (isset($arg[1]["gif"]) && isset($arg[1]["gfm"]) && isset($arg[1]["mtype"]) && isset($GLOBALS["roles"]['features'][17]) && $arg[1]["mtype"] == 'sticker') {
                if (!empty($arg[1]["gif"]) && !empty($arg[1]["gfm"])) {
                    $typ = 'stickers';
                    $gif = 1;
                }
            } else if (isset($arg[1]["gif"]) && isset($arg[1]["gfm"]) && isset($GLOBALS["roles"]['features'][3])) {
                if (!empty($arg[1]["gif"]) && !empty($arg[1]["gfm"])) {
                    $tchk = '/http(s)?:\/\/(media\.)*tenor\.com\/.*/';
                    if (preg_match($tchk, $arg[1]["gif"]) && preg_match($tchk, $arg[1]["gfm"])) {
                        $typ = 'gifs';
                        $gif = 1;
                    }
                }
            }
            $sendminmsglimit = vc($GLOBALS["default"]->min_msg_length, 'num');
            $sendmaxmsglimit = vc($GLOBALS["default"]->max_msg_length, 'num');
            if ($typ == 'msg') {
                if (!empty($sendminmsglimit) && strlen($arg[1]["msg"]) < $sendminmsglimit) {
                    return false;
                }
                if (!empty($sendmaxmsglimit)) {
                    $arg[1]["msg"] = substr($arg[1]["msg"], 0, $sendmaxmsglimit);
                }
            }
            $rv['type'] = 'msg';
            if (!empty($arg[1]["rid"]) && $gif == 0 && !empty($scheck[0]['rpmsg'])) {
                $rid = $scheck[0]['rpuid'];
                $rmid = $arg[1]["rid"];
                $rv['type'] = $scheck[0]['rptype'];
                if ($scheck[0]['rptype'] === 'file') {
                    $rtxt = 'shared_file';
                } else {
                    $rtxt = html_entity_decode(substr($scheck[0]['rpmsg'], 0, 30), ENT_QUOTES);
                }
            }
            $dt = dt();
            $extchkm = 2;
            $xtraz = 0;
            if (isset($arg[1]["xtra"]) && $gif == 0) {
                $xtraz = $arg[1]["xtra"];
            }
            if (isset($arg[1]["qrcode"]) && $arg[1]["qrcode"] == 1 && $gif == 0 && $typ == 'msg') {
                if (isset($GLOBALS["roles"]['features'][4])) {
                    $typ = 'qrcode';
                }
            }
            if ($gif == 0) {
                preg_match('/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $arg[1]["msg"], $prlnks);
                if (!empty($prlnks[0]) && isset($GLOBALS["roles"]['features'][7])) {
                    $prlnk = gr_urlscrapper($prlnks[0]);
                    $prlnk['url'] = $prlnks[0];
                    $xtraz = $prlnk['mimetype'];
                }
                $arg[1]["msg"] = preg_replace("/[\r\n]+/", "\n", $arg[1]["msg"]);
            } else {
                $arg[1]["msg"] = $arg[1]["gif"];
                $xtraz = $arg[1]["gfm"];
            }
            if (!isset($GLOBALS["roles"]['features'][7]) && $typ == 'msg') {
                $arg[1]["msg"] = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '##', $arg[1]["msg"]);
            }

            if ($arg[1]["ldt"] == 'group' && $gif == 0 && $typ == 'msg') {
                preg_match_all('/(^|\s)@(?P<mention>[\p{L}\p{N}\p{Mn}\p{Pd}_-]+)/u', $arg[1]["msg"], $mentions);
                if (count($mentions[2]) > 0) {
                    $mentions = implode('", "', $mentions[2]);
                    $qmen = 'SELECT us.name,op.v3 FROM gr_users us,gr_options op WHERE op.type="profile" AND ';
                    $qmen = $qmen.'op.v3=us.id AND op.v1="name" AND us.name IN ("'.$mentions.'") ORDER BY LENGTH(us.name) DESC';
                    $rqm = db('Grupo', 'q', $qmen);
                    foreach ($rqm as $ment) {
                        $arg[1]["msg"] = str_replace('@'.$ment['name'], '@'.$ment['v3'], $arg[1]["msg"]);
                    }
                }
            }
            $mid = db('Grupo', 'i', 'msgs', 'gid,uid,msg,type,tms,rtxt,rid,rmid,rtype,cat,xtra,lnurl,#lntitle,#lndesc,lnimg', $tmpido, $uid, $arg[1]["msg"], $typ, $dt, $rtxt, $rid, $rmid, $rv['type'], $arg[1]["ldt"], $xtraz, $prlnk['url'], $prlnk['title'], $prlnk['description'], $prlnk['image']);

            $query = 'UPDATE gr_logs SET v3=0 ';
            $query = $query.'WHERE type="typing" AND v2="'.$uid.'";';

            if ($arg[1]["ldt"] == 'user') {
                if ($scheck[0]['totalmsgs'] == 0 || $scheck[0]['totalmsgs'] != 0 && empty($scheck[0]['rpuid'])) {
                    if ($scheck[0]['browsing'] != $tmpido) {
                        $query = $query.'INSERT INTO gr_alerts(type, uid, v1, v2, v3, tms) VALUES ( ';
                        $query = $query.'"newmsg", "'.$arg[1]["id"].'", "'.$uid.'", "'.$mid.'", "'.$uid.'","'.dt().'");';
                    }
                    if ($scheck[0]['browsing'] != $tmpido && $scheck[0]['status'] != 'online') {
                        if (in_array(3, $GLOBALS["default"]->send_email_notification)) {
                            $query = $query.'INSERT INTO gr_mails(type,uid,valz,code,sent,tms) VALUES ( ';
                            $query = $query.'"newmsg", "'.$arg[1]["id"].'", "'.$uid.'", "'.rn(5).'", 0,"'.dt().'");';
                        }
                    }
                }
            } else {
                if (isset($scheck[0]['rpuid']) && !empty($scheck[0]['rpuid']) && $scheck[0]['rpuid'] != $uid) {
                    $query = $query.'INSERT INTO gr_alerts(type, uid, v1, v2, v3, tms) VALUES ( ';
                    $query = $query.'"replied", "'.$scheck[0]['rpuid'].'", "'.$arg[1]["id"].'", "'.$mid.'", "'.$uid.'","'.dt().'");';
                    if (in_array(4, $GLOBALS["default"]->send_email_notification)) {
                        $query = $query.'INSERT INTO gr_mails(type,uid,valz,code,sent,tms) VALUES ( ';
                        $query = $query.'"replied", "'.$scheck[0]['rpuid'].'", "'.$uid.'", "'.rn(5).'", 0,"'.dt().'");';
                    }
                }
                $query = $query.'UPDATE gr_options SET tms="'.dt().'" ';
                $query = $query.'WHERE type="group" AND id="'.$arg[1]["id"].'";';
            }
            if ($arg[1]["ldt"] == 'group' && $gif == 0 && $typ == 'msg' && count($rqm) > 0) {
                foreach ($rqm as $ment) {
                    $arg[1]["msg"] = str_replace('@'.$ment['name'], '@'.$ment['v3'], $arg[1]["msg"]);
                    if ($ment['v3'] != $uid) {
                        $query = $query.'INSERT INTO gr_alerts(type, uid, v1, v2, v3, tms) VALUES ( ';
                        $query = $query.'"mentioned", "'.$ment['v3'].'", "'.$arg[1]["id"].'", "'.$mid.'", "'.$uid.'","'.dt().'");';
                        if (in_array(1, $GLOBALS["default"]->send_email_notification)) {
                            $query = $query.'INSERT INTO gr_mails(type,uid,valz,code,sent,tms) VALUES ( ';
                            $query = $query.'"mentioned", "'.$ment['v3'].'", "'.$uid.'", "'.rn(5).'", 0,"'.dt().'");';
                        }
                    }
                }
            }
            $supdate = db('Grupo', 'q', $query);
            if (isset($arg[3]) && $arg[3] == 'mid') {
                $arg[1]["msid"] = $mid;
            }
            if (isset($arg[3]) && $arg[3] == 'nomid') {
                return $mid;
            }
            if (!isset($arg[3]) || $arg[3] == 'mid') {
                gr_group('msgs', $arg[1]);
            }

        }

    } else if ($arg[0] === 'mention') {
        gr_prnt('$(".swr-grupo .rside > .top > .left > .goback:visible,.swr-grupo .panel > .head > .goback:visible").trigger("click");');
        gr_prnt('setTimeout(function() {$(".swr-grupo .lside > .tabs > ul > li[act=groups]").attr("openid","'.$arg[1]["id"].'").trigger("click");}, 600);');
        gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
    } else if ($arg[0] === 'deletemsg') {
        $forcedel = 0;
        if (isset($arg[2]) && $arg[2] == 'force') {
            $forcedel = 1;
        }
        if ($forcedel == 0) {
            $role = gr_group('user', $arg[1]["id"], $uid, $arg[1]["ldt"])['role'];
        } else {
            $role = 2;
        }
        if ($role == 3) {
            exit;
        }
        if ($role == 2 || gr_role('access', 'groups', '7') || $role == 1) {
            if (isset($GLOBALS["roles"]['users'][11]) && $arg[1]["ldt"] == 'user' && strpos($arg[1]["id"], '-') !== false) {
                $arg[1]["id"] = $arg[1]["id"];
            } else if ($arg[1]["ldt"] == 'user') {
                $tmpido = $arg[1]["id"].'-'.$uid;
                if ($arg[1]["id"] > $uid) {
                    $tmpido = $uid.'-'.$arg[1]["id"];
                }
                $arg[1]["id"] = $tmpido;
            }
            $r = db('Grupo', 's', 'msgs', 'gid,id', $arg[1]["id"], $arg[1]["mid"]);
        } else {
            if ($arg[1]["ldt"] == 'user') {
                $tmpido = $arg[1]["id"].'-'.$uid;
                if ($arg[1]["id"] > $uid) {
                    $tmpido = $uid.'-'.$arg[1]["id"];
                }
                $arg[1]["id"] = $tmpido;
            }
            $r = db('Grupo', 's', 'msgs', 'gid,id,uid', $arg[1]["id"], $arg[1]["mid"], $uid);
        }
        if (count($r) > 0) {
            if ($r[0]['type'] === 'system' && $forcedel == 0) {
                gr_prnt('say("'.$GLOBALS["lang"]->deny_system_msg.'","e")');
                exit;
            }
            $delexpr = vc($GLOBALS["default"]->delmsgexpiry, 'num');
            if (!empty($delexpr)) {
                if (strtotime('now') > strtotime('+'.$delexpr.' minutes', strtotime($r[0]['tms'])) && !gr_role('access', 'groups', '7') && $role != 2 && $role != 1) {
                    gr_prnt('say("'.$GLOBALS["lang"]->deny_file_deletion.'","e")');
                    exit;
                }
            } else if (!gr_role('access', 'groups', '7') && $role != 2 && $role != 1) {
                gr_prnt('say("'.$GLOBALS["lang"]->deny_file_deletion.'","e")');
                exit;
            }
            if ($r[0]['type'] === 'file') {
                if (file_exists('gem/ore/grupo/files/dumb/'.$r[0]['msg'])) {
                    unlink('gem/ore/grupo/files/dumb/'.$r[0]['msg']);
                }
                if (file_exists('gem/ore/grupo/files/preview/'.$r[0]['msg'])) {
                    unlink('gem/ore/grupo/files/preview/'.$r[0]['msg']);
                }
            } else if ($r[0]['type'] === 'audio') {
                if (file_exists('gem/ore/grupo/audiomsgs/'.$r[0]['msg'])) {
                    unlink('gem/ore/grupo/audiomsgs/'.$r[0]['msg']);
                }
            }
            db('Grupo', 'd', 'msgs', 'gid,id', $arg[1]["id"], $arg[1]["mid"]);
            db('Grupo', 'i', 'msgs', 'gid,uid,type,msg,rtxt,tms,cat', $arg[1]["id"], $uid, 'logs', $arg[1]["mid"], 'delete', dt(), $arg[1]["ldt"]);
            db('Grupo', 'd', 'msgs', 'gid,msg,type', $arg[1]["id"], $arg[1]["mid"], 'like');
            db('Grupo', 'd', 'options', 'type,v1', 'loves', $arg[1]["mid"]);
        }
        if ($forcedel == 0) {
            gr_prnt('$(".swr-grupo .panel > .room > .msgs > li[no='.$arg[1]["mid"].']").remove();');
            gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
        }
    } else if ($arg[0] === 'attachmsg') {
        if (isset($GLOBALS["roles"]['files'][4])) {
            if (!isset($arg[1]["ldt"]) || empty($arg[1]["ldt"])) {
                $arg[1]["ldt"] = 'group';
            }
            if (isset($GLOBALS["roles"]['features'][13]) && isset($arg[1]["userid"]) && !empty(vc($arg[1]["userid"], 'num'))) {
                $uid = $arg[1]["userid"];
            }
            $dir = 'grupo/files/'.$uid.'/';
            flr('new', $dir);
            $fn = rn(6).rn(3).'-gr-';
            $totalFileSize = array_sum($_FILES['attachfile']['size']);
            $totalFileSize = number_format($totalFileSize / 1048576, 2);
            if ($totalFileSize > $GLOBALS["roles"]["xtras"]["maxfileuploadsize"]) {
                gr_prnt(json_encode($totalFileSize));
                exit;
            }
            $total = count($_FILES['attachfile']['name']);
            if (flr('upload', 'attachfile', $dir, $fn)) {
                fc('grfiles');
                for ($i = 0; $i < $total; $i++) {
                    $do['id'] = $fn.$_FILES['attachfile']['name'][$i];
                    $do['type'] = 'zip';
                    $do["userid"] = $arg[1]["userid"];
                    $do['r'] = 1;
                    $fnb = gr_files($do);
                    if (!isset($GLOBALS["roles"]['files'][1])) {
                        $file = "gem/ore/grupo/files/".$uid.'/'.$do['id'];
                        unlink($file);
                    }
                    $data["id"] = $arg[1]["id"];
                    $data["msg"] = $fnb;
                    $data["ldt"] = $arg[1]["ldt"];
                    $data['xtra'] = $_FILES['attachfile']['name'][$i];
                    $data["userid"] = $arg[1]["userid"];
                    $attachid[$i] = gr_group('sendmsg', $data, 2, 'nomid');
                }
                $data['msid'] = $attachid[0];
                gr_group('msgs', $data);
            }
        }

    } else if ($arg[0] === 'sendaudio') {
        if (isset($GLOBALS["roles"]['features'][2])) {
            if (!isset($arg[1]["ldt"]) || empty($arg[1]["ldt"])) {
                $arg[1]["ldt"] = 'group';
            }
            if (isset($GLOBALS["roles"]['features'][13]) && isset($arg[1]["userid"]) && !empty(vc($arg[1]["userid"], 'num'))) {
                $uid = $arg[1]["userid"];
            }
            $dir = 'grupo/audiomsgs';
            flr('new', $dir);
            $fn = $uid.'-gr-'.rn(6).'-'.dt(0, "dmyhis").'-';
            if (flr('upload', 'audio_data', $dir, $fn)) {
                $fn = $fn.$_FILES['audio_data']['name'];
                $data["id"] = $arg[1]["id"];
                $data["msg"] = $fn;
                $data["ldt"] = $arg[1]["ldt"];
                $data["userid"] = $arg[1]["userid"];
                $data['xtra'] = $_FILES['audio_data']['name'];
                gr_group('sendmsg', $data, 3, 'mid');
            }
        }
    } else if ($arg[0] === 'msgs') {
        $orgid = $arg[1]["id"];
        $pmlist = 0;
        if (!isset($arg[1]["ldt"]) || empty($arg[1]["ldt"])) {
            $arg[1]["ldt"] = 'group';
        }
        if (isset($GLOBALS["roles"]['users'][11]) && $arg[1]["ldt"] == 'user' && strpos($arg[1]["id"], '-') !== false) {
            $orgid = explode('-', $arg[1]["id"]);
            $usertwo = $orgid[1];
            $orgid = $orgid[0];
            $arg[1]["id"] = $arg[1]["id"];
            $pmlist = 1;
        } else if ($arg[1]["ldt"] == 'user') {
            $tmpido = $arg[1]["id"].'-'.$uid;
            if ($arg[1]["id"] > $uid) {
                $tmpido = $uid.'-'.$arg[1]["id"];
            }
            $arg[1]["id"] = $tmpido;
        }
        $list = null;
        $perload = $GLOBALS["default"]->maxmsgsperload;
        $data = array();
        $query = 'SELECT mg.*,';
        if ($arg[1]["ldt"] == 'user') {
            if (isset($GLOBALS["roles"]['users'][10]) || $pmlist == 1) {
                $query = $query.'(SELECT v2 FROM gr_options WHERE v3=:loadid AND type="profile" AND v1="name" LIMIT 1) AS name,';
            } else {
                $query = $query.'(SELECT name FROM gr_users WHERE id=:loadid LIMIT 1) AS name,';
            }
            if ($pmlist == 1) {
                $query = $query.'(SELECT v2 FROM gr_options WHERE v3=:usertwo AND type="profile" AND v1="name" LIMIT 1) AS nameb,';
                $data['usertwo'] = $usertwo;
            }
            $query = $query.'(SELECT v2 FROM gr_options WHERE v3=:loadid AND type="profile" AND v1="status" LIMIT 1) AS status,';
            $query = $query.'(SELECT count(1)FROM gr_options WHERE type="pblock" AND v1=:uid AND v2=:loadid) AS yblock,';
            $query = $query.'(SELECT count(1)FROM gr_options WHERE type="pblock" AND v2=:uid AND v1=:loadid) AS oblock,';
            $query = $query.'(SELECT count(1) FROM gr_options WHERE type="deaccount" AND v1="yes" AND v3=:loadid) AS deaccount,';
            $query = $query.'(SELECT name FROM gr_users WHERE id=:loadid LIMIT 1) AS slug,';
            $query = $query.'(SELECT count(1) FROM gr_options WHERE type="profile" AND v1="privatemsgs" AND v2="disable" AND v3=:loadid) AS pmdisabled,';
            $query = $query.'(SELECT 1) AS grjoin,';
            $query = $query.'(SELECT 0) AS grrole,';
        } else {
            $query = $query.'gr.v1 AS name,gr.v2 AS grpass,gr.v5 AS grmessaging,gr.v6 AS grleavegroup,gr.v3 AS grvisible,';
            $query = $query.'(SELECT count(1) FROM gr_options WHERE type="gruser" AND v1=gr.id) AS grtotal,';
            $query = $query.'(SELECT count(1) FROM gr_msgs WHERE type="like" AND gid=mg.gid AND msg=mg.id) AS grlikes,';
            $query = $query.'(SELECT count(1) FROM gr_msgs WHERE type="like" AND gid=mg.gid AND msg=mg.msg) AS grtotlikes,';
            $query = $query.'(SELECT count(1) FROM gr_msgs WHERE type="like" AND gid=mg.gid AND msg=mg.id AND uid=:uid) AS grliked,';
            $query = $query.'(SELECT count(1) FROM gr_options WHERE type="gruser" AND v1=gr.id AND v2=:uid) AS grjoin,';
            $query = $query.'(SELECT v3 FROM gr_options WHERE type="gruser" AND v1=gr.id AND v2=:uid LIMIT 1) AS grrole,';
            $query = $query.'(SELECT v2 FROM gr_options WHERE type="groupslug" AND v1=gr.id LIMIT 1) AS slug,';
        }
        $query = $query.'(SELECT count(1) FROM gr_options WHERE type="deaccount" AND v1="yes" AND v3=mg.uid) AS usdeaccount,';
        if (isset($GLOBALS["roles"]['users'][10]) || $pmlist == 1) {
            $query = $query.'(SELECT v2 FROM gr_options WHERE v3=mg.uid AND type="profile" AND v1="name" LIMIT 1) AS usrname,';
        } else {
            $query = $query.'(SELECT name FROM gr_users WHERE id=mg.uid LIMIT 1) AS usrname,';
        }
        $query = $query.'(SELECT v5 FROM gr_options WHERE v3=mg.uid AND type="profile" AND v1="name" LIMIT 1) AS usrcolor,';
        $query = $query.'(SELECT IFNULL((SELECT MIN(CAST(v3 AS SIGNED)) FROM gr_options WHERE type="lview" AND v1=:fullid),0)) AS lscount,';
        $query = $query.'(SELECT IFNULL((SELECT CASE WHEN tz.v2="Auto" THEN ';
        $query = $query.'(SELECT am.v2 FROM gr_options am WHERE am.type="profile" AND am.v1="autotmz" AND am.v3=tz.v3)';
        $query = $query.' ELSE tz.v2 END AS timz FROM gr_options tz WHERE tz.type="profile" AND tz.v1="tmz" AND tz.v3=:uid),';
        $query = $query.':timezone)) AS timezone';
        $query = $query.' FROM ((SELECT * FROM gr_msgs WHERE gid=:fullid ';
        if ($GLOBALS["default"]->sysmessages == 'disable') {
            $query = $query.'AND type<>"system" ';
        }
        if ($arg[1]["ldt"] == 'user' && $pmlist != 1) {
            $query = $query.'AND id > (SELECT IFNULL((SELECT MIN(CAST(v3 AS SIGNED)) FROM gr_options WHERE type="clearchat" AND v1=:uid AND v2=:loadid LIMIT 1),0)) ';
        }
        if (isset($arg[1]["from"]) && !empty($arg[1]["from"])) {
            $data['msgsfrom'] = $arg[1]["from"];
            $query = $query.'AND id > :msgsfrom ';
        } else {
            $query = $query.'AND type<>"like" AND type<>"logs" ';
        }
        if (isset($arg[1]["to"]) && !empty($arg[1]["to"])) {
            $data['msgsto'] = $arg[1]["to"];
            $perload = 5;
            $query = $query.'AND id < :msgsto ';
        }
        if (isset($arg[1]["uid"]) && !empty($arg[1]["uid"])) {
            $data['msgsuid'] = $arg[1]["uid"];
            $query = $query.'AND uid=:msgsuid ';
        }
        if (isset($arg[1]["search"]) && !empty($arg[1]["search"])) {
            $data['search'] = '%'.$arg[1]['search'].'%';
            $query = $query.'AND msg LIKE :search ';
        }
        if (isset($arg[1]["msid"]) && !empty($arg[1]["msid"])) {
            $data['msgid'] = $arg[1]["msid"];
            $query = $query.'AND id=:msgid ';
        }
        $query = $query.'ORDER BY id DESC LIMIT '.$perload.') ';
        $query = $query.' UNION ALL (select 1 as id, :fullid as gid,:uid as uid,0 as msg,"dummy" as type,';
        $query = $query.'0 as rtxt,0 as rid,0 as rmid,"msg" as rtype,:loadtype as cat,0 as lnurl,0 as lntitle,0 as lndesc,';
        $query = $query.'0 as lnimg,0 as xtra,:date as tms)) mg';
        if ($arg[1]["ldt"] != 'user') {
            $query = $query.',gr_options gr';
        }
        $query = $query.' WHERE ';
        $query = $query.'mg.cat=:loadtype ';
        if ($arg[1]["ldt"] != 'user') {
            $query = $query.'AND gr.type="group" AND gr.id=:loadid ';
        }
        $query = $query.'AND mg.gid=:fullid;';
        if (!isset($arg[1]["to"]) && !isset($arg[1]["from"]) && !isset($arg[1]["search"]) && !empty($uid)) {
            $query = $query.'UPDATE gr_logs SET v3=:fullid, xtra=:loadtype, tms=:date ';
            $query = $query.'WHERE type="browsing" AND v1=:uid;';
            $query = $query.'INSERT INTO gr_logs (type,v1,v3,xtra,tms) SELECT DISTINCT ';
            $query = $query.'"browsing", :uid, :fullid, :loadtype, :date';
            $query = $query.' FROM gr_logs WHERE NOT EXISTS (SELECT DISTINCT v3 FROM gr_logs WHERE type="browsing" AND v1=:uid);';
        }
        if (!isset($arg[1]["to"]) && !isset($arg[1]["uid"]) && !empty($uid) && !isset($arg[1]["msid"]) && !isset($arg[1]["search"])) {
            $query = $query. 'UPDATE gr_options SET tms=:date,';
            $query = $query.'v3=(SELECT max(id) FROM gr_msgs WHERE gid=:fullid) ';
            $query = $query.'WHERE type="lview" AND v1=:fullid AND v2=:uid;';
            $query = $query.'INSERT INTO gr_options (type,v2,v1,v3,tms) SELECT DISTINCT ';
            $query = $query.'"lview", :uid, :fullid, (SELECT max(id) FROM gr_msgs WHERE gid=:fullid), :date';
            $query = $query.' FROM gr_options WHERE NOT EXISTS';
            $query = $query.' (SELECT DISTINCT v3 FROM gr_options WHERE type="lview" AND v1=:fullid AND v2=:uid);';
        }
        $data['loadid'] = $orgid;
        $data['fullid'] = $arg[1]["id"];
        $data['loadtype'] = $arg[1]["ldt"];
        $data['uid'] = $uid;
        $data['date'] = dt();
        $data['timezone'] = $GLOBALS["default"]->timezone;
        $r = db('Grupo', 'q', $query, $data);
        if (isset($r[0]) && !$GLOBALS["logged"] && $GLOBALS["default"]->viewgroups_nologin == 'enable') {
            if (empty($r[0]['grpass']) && $r[0]['grvisible'] != 'secret') {
                $r[0]['grjoin'] = 1;
            }
        }
        if (!isset($r[0])) {
            $list[0] = new stdClass();
            $list[0]->nomore = 1;
            $list[0]->pnimg = gr_img('groups', 0);
            $list[0]->pntitle = 'Not Exists';
            $list[0]->sitetitle = $list[0]->pntitle.' - '.$GLOBALS["default"]->sitename;
            $list[0]->pnsub = 'Invalid Group';
        } else if ($r[0]['grjoin'] == 0 || $r[0]['grrole'] == 3 && !isset($GLOBALS["roles"]['groups']['7'])) {
            $list[0] = new stdClass();
            $list[0]->nomem = 1;
            $list[0]->pnimg = gr_img('groups', 0);
            $list[0]->pntitle = 'Access Denied';
            $list[0]->pnsub = 'Check Group Permision';
            $list[0]->sitetitle = $list[0]->pntitle.' - '.$GLOBALS["default"]->sitename;
        } else {
            $r = array_reverse($r);
            $list[0] = new stdClass();
            $list[0]->blocked = 0;
            $list[0]->nomem = 0;
            $list[0]->accesslink = $GLOBALS["default"]->weburl.'chat/';
            $list[0]->signinlink = $GLOBALS["default"]->weburl.'signin/';
            if ($pmlist == 1) {
                $list[0]->pntitle = $r[0]['name'].' - '.$r[0]['nameb'];
            } else {
                $list[0]->pntitle = $r[0]['name'];
            }
            $list[0]->sitetitle = $list[0]->pntitle.' - '.$GLOBALS["default"]->sitename;
            if ($arg[1]["ldt"] == 'user') {
                $usts = $r[0]['status'];
                $list[0]->pnsub = $GLOBALS["lang"]->$usts;
                $list[0]->deactiv = 0;
                if ($pmlist != 1) {
                    $list[0]->accesslink = $GLOBALS["default"]->weburl.'chat/'.$r[0]['slug'].'/';
                    $list[0]->signinlink = $GLOBALS["default"]->weburl.'signin/?goto=chat/'.$r[0]['slug'].'/';
                }
                if ($r[0]['deaccount'] != 0 || $r[0]['pmdisabled'] != 0) {
                    $list[0]->deactiv = 1;
                    $list[0]->pnimg = gr_img('users', 0);
                } else if ($orgid == $uid) {
                    $list[0]->deactiv = 1;
                    $list[0]->pnimg = gr_img('users', $orgid);
                } else {
                    $list[0]->pnimg = gr_img('users', $orgid);
                }
                if ($pmlist == 1) {
                    $list[0]->deactiv = 1;
                    $list[0]->pnimg = gr_img('users', $usertwo);
                    $list[0]->pnsub = $GLOBALS["lang"]->privatemsg;
                }
                if (!isset($GLOBALS["roles"]['privatemsg']['1'])) {
                    $list[0]->deactiv = 1;
                }
                $list[0]->gid = $orgid;
                $list[1] = new stdClass();
                if ($pmlist != 1) {
                    $list[1]->mb = array($GLOBALS["lang"]->block_user, 'class="formpop" pn="1" title="'.$GLOBALS["lang"]->block_user.'" do="profile" btn="'.$GLOBALS["lang"]->block_user.'" act="block"');
                    if ($r[0]['yblock']+$r[0]['oblock'] > 0) {
                        $list[0]->blocked = 1;
                        $list[0]->pnsub = $GLOBALS["lang"]->blocked;
                        if ($r[0]['yblock'] != 0) {
                            $list[1]->mb = array($GLOBALS["lang"]->unblock_user, 'class="formpop" pn="1" title="'.$GLOBALS["lang"]->unblock_user.'" do="profile" btn="'.$GLOBALS["lang"]->unblock.'" act="block"');
                        }
                    }
                    if ($r[0]['deaccount'] == 0 && $list[0]->blocked != 1) {
                        $list[1]->ma = array($GLOBALS["lang"]->view_profile, 'class="vwp" no="'.$orgid.'"');
                    }
                    if (isset($GLOBALS["roles"]['privatemsg']['4'])) {
                        $list[1]->md = array($GLOBALS["lang"]->clear_chat, 'class="formpop" pn="1" title="'.$GLOBALS["lang"]->clear_chat.'" do="group" btn="'.$GLOBALS["lang"]->confirm.'" act="clearchat"');
                    }
                } else {
                    if (isset($GLOBALS["roles"]['users'][12])) {
                        $list[1]->mi = array($GLOBALS["lang"]->delete_all, 'class="formpop" data-cat="userchat" pn="1" title="'.$GLOBALS["lang"]->delete_all_messages.'" do="group" btn="'.$GLOBALS["lang"]->confirm.'" act="deleteallmsgs"');
                    }
                }
                if (isset($GLOBALS["roles"]['privatemsg']['3'])) {
                    $list[1]->mc = array($GLOBALS["lang"]->export_chat, 'class="formpop" pn="1" title="'.$GLOBALS["lang"]->export_chat.'" do="group" btn="'.$GLOBALS["lang"]->export_chat.'" act="export"');
                }
            } else {
                if (isset($r[0]['slug']) && !empty($r[0]['slug'])) {
                    $list[0]->accesslink = $GLOBALS["default"]->weburl.'chat/'.$r[0]['slug'].'/';
                    $list[0]->signinlink = $GLOBALS["default"]->weburl.'signin/?goto=chat/'.$r[0]['slug'].'/';
                } else {
                    $list[0]->accesslink = $GLOBALS["default"]->weburl.'chat/group/'.$arg[1]["id"].'/';
                    $list[0]->signinlink = $GLOBALS["default"]->weburl.'signin/?goto=chat/group/'.$arg[1]["id"].'/';
                }
                $list[0]->pnsub = $r[0]['grtotal']." ".$GLOBALS["lang"]->members;
                $list[0]->pnimg = gr_img('groups', $arg[1]["id"]);
                $list[0]->gid = $orgid;
                $list[0]->likesys = 0;
                if (isset($GLOBALS["roles"]['groups'][9]) || isset($GLOBALS["roles"]['groups'][7])) {
                    $list[0]->viewlike = 1;
                }
                $list[0]->likemsgs = $GLOBALS["lang"]->denied;
                if (isset($GLOBALS["roles"]['groups'][10]) || isset($GLOBALS["roles"]['groups'][7])) {
                    $list[0]->likemsgs = 'enabled';
                }
                $list[1] = new stdClass();
                $adm = 0;
                if ($r[0]['grrole'] == 2 || $r[0]['grrole'] == 1) {
                    $adm = 1;
                }
                if ($adm != 1 && !isset($GLOBALS["roles"]['groups'][7]) && $r[0]['grmessaging'] == 'adminonly') {
                    $list[0]->deactiv = 1;
                }
                $list[1]->mh = array($GLOBALS["lang"]->group_info, 'class="vwp" no="'.$arg[1]["id"].'" ldt="group"');
                if (isset($GLOBALS["roles"]['groups'][2]) && $adm == 1 || isset($GLOBALS["roles"]['groups'][7])) {
                    $list[1]->ma = array($GLOBALS["lang"]->edit_group, 'class="formpop" pn="1" title="'.$GLOBALS["lang"]->edit_group.'" do="edit" btn="'.$GLOBALS["lang"]->update.'" act="group"');
                }
                if (isset($GLOBALS["roles"]['groups'][8]) || isset($GLOBALS["roles"]['groups'][7])) {
                    $list[1]->mb = array($GLOBALS["lang"]->export_chat, 'class="formpop" pn="1" title="'.$GLOBALS["lang"]->export_chat.'" do="group" btn="'.$GLOBALS["lang"]->export_chat.'" act="export"');
                }
                if ($r[0]['grleavegroup'] != 'unleavable') {
                    $list[1]->mc = array($GLOBALS["lang"]->leave_group, 'class="formpop" pn="1" title="'.$GLOBALS["lang"]->leave_group.'" do="group" btn="'.$GLOBALS["lang"]->leave_group.'" act="leave"');
                }

                if (isset($GLOBALS["roles"]['groups'][3]) && $r[0]['grrole'] == 2 || isset($GLOBALS["roles"]['groups'][7])) {
                    if (isset($GLOBALS["roles"]['groups'][17])) {
                        $list[1]->mi = array($GLOBALS["lang"]->delete_all, 'class="formpop" pn="1" title="'.$GLOBALS["lang"]->delete_all_messages.'" do="group" btn="'.$GLOBALS["lang"]->confirm.'" act="deleteallmsgs"');
                    }
                }
                if (isset($GLOBALS["roles"]['groups'][12]) || isset($GLOBALS["roles"]['groups'][7])) {
                    if (isset($GLOBALS["roles"]['groups'][7]) || empty($r[0]['grpass']) && $r[0]['grvisible'] != 'secret' || $adm == 1) {
                        $list[1]->mg = array($GLOBALS["lang"]->addgroupuser, 'class="goback loadside" act="addgroupuser" zero="0" zval="'.$GLOBALS["lang"]->users.'" side="lside"');
                    }
                }
                if (isset($GLOBALS["roles"]['groups'][5]) || isset($GLOBALS["roles"]['groups'][7])) {
                    if (isset($GLOBALS["roles"]['groups'][7]) || empty($r[0]['grpass']) && $r[0]['grvisible'] != 'secret' || $adm == 1) {
                        $list[1]->md = array($GLOBALS["lang"]->invite, 'class="formpop" pn="1" title="'.$GLOBALS["lang"]->invite.'" do="group" btn="'.$GLOBALS["lang"]->invite.'" act="invite"');
                    }
                }
                $list[1]->me = array($GLOBALS["lang"]->report_group, 'class="formpop" pn="1" title="'.$GLOBALS["lang"]->report_group.'" do="group" btn="'.$GLOBALS["lang"]->report.'" act="reportmsg"');

                if (isset($GLOBALS["roles"]['groups'][3]) && $r[0]['grrole'] == 2 || isset($GLOBALS["roles"]['groups'][7])) {
                    $list[1]->mf = array($GLOBALS["lang"]->delete, 'class="formpop" pn="1" title="'.$GLOBALS["lang"]->delete.'" do="group" btn="'.$GLOBALS["lang"]->delete.'" act="delete"');
                }

            }
            $i = 2;
            $delmsgt = vc($GLOBALS["default"]->autodeletemsg, 'num');
            $usdelmsgt = vc($GLOBALS["default"]->delmsgexpiry, 'num');
            $filxptme = vc($GLOBALS["default"]->fileexpiry, 'num');
            $tdy = new DateTime(date('Y-m-d H:i:s'));
            $tmz = new DateTimeZone($r[0]['timezone']);
            $tdy->setTimezone($tmz);
            foreach ($r as $v) {



                if ($v['type'] == 'like' || $v['type'] == 'unlike') {
                    $list[$i] = new stdClass();
                    $list[$i]->liked = $v['msg'];
                    $list[$i]->id = $v['id'];
                    $list[$i]->total = str_pad($v['grtotlikes'], 2, "0", STR_PAD_LEFT);
                    $list[$i]->type = $v['type'];
                    $i = $i+1;
                } else if ($v['type'] == 'logs') {
                    $list[$i] = new stdClass();
                    $list[$i]->action = $v['rtxt'];
                    $list[$i]->id = $v['id'];
                    $list[$i]->rel = $v['msg'];
                    $list[$i]->type = $v['type'];
                    $i = $i+1;
                } else {
                    $tmrdel = $utmrdel = 0;
                    $tms = new DateTime($v['tms']);
                    $tmz = new DateTimeZone($r[0]['timezone']);
                    $tms->setTimezone($tmz);
                    $tmst = strtotime($tms->format('Y-m-d H:i:s'));

                    $list[$i] = new stdClass();
                    $list[$i]->tdif = $tdy->format('U') - $tms->format('U');
                    $list[$i]->user = '';
                    $list[$i]->userid = $v['uid'];
                    $list[$i]->lntitle = 0;
                    $list[$i]->userimg = 0;
                    if ($GLOBALS["default"]->message_style == 'style2') {
                        if ($v['uid'] != $uid && $v['type'] != 'system') {
                            $list[$i]->userimg = gr_img('users', $v['uid']);
                        }
                    }
                    $list[$i]->opta = $list[$i]->optb = $list[$i]->optc = $list[$i]->optd = $list[$i]->opte = $list[$i]->optf = $list[$i]->tmrdel = 0;
                    if (!empty($delmsgt)) {
                        if ($arg[1]["ldt"] != 'user' && $v['type'] != 'system') {
                            $tmrdel = date("M d, Y H:i:s", strtotime('+'.$delmsgt.' minutes', strtotime($v['tms'])));
                            $list[$i]->tmrdel = $tmrdel;
                        }
                    }
                    if (!empty($usdelmsgt)) {
                        if ($v['type'] != 'system') {
                            $utmrdel = date("M d, Y H:i:s", strtotime('+'.$usdelmsgt.' minutes', strtotime($v['tms'])));
                            $list[$i]->utmrdel = $utmrdel;
                        }
                    }
                    if ($arg[1]["ldt"] != 'user') {
                        $list[$i]->opta = 'class="gr-report formpop" title="'.$GLOBALS["lang"]->report_message.'" xtid="'.$v['id'].'" pn=1 do="group" btn="'.$GLOBALS["lang"]->report.'" act="reportmsg"';
                        if ($v['uid'] != $uid) {
                            if (isset($GLOBALS["roles"]['privatemsg'][1])) {
                                $list[$i]->optd = 'class="loadgroup" ldt="user" no="'.$v['uid'].'"';
                            }
                        }

                    }
                    $delbtn = 0;
                    if (!empty($usdelmsgt)) {
                        if (strtotime($utmrdel) > strtotime('now')) {
                            $delbtn = 1;
                        }
                    }
                    if ($v['grrole'] == 2 || $v['grrole'] == 1 || isset($GLOBALS["roles"]['groups'][7]) || $v['uid'] === $uid && $delbtn == 1) {
                        $list[$i]->optb = 'class="gr-remove formpop" pn="1" xtid="'.$v['id'].'" data-ldt="'.$arg[1]["ldt"].'" data-umdt="'.$utmrdel.'" data-adt="'.$tmrdel.'" title="'.$GLOBALS["lang"]->delete.'" do="group" btn="'.$GLOBALS["lang"]->delete.'" act="deletemsg"';
                    }
                    if ($pmlist != 1) {
                        $list[$i]->optc = 'class="gr-reply"';
                    }
                    if (!$GLOBALS["logged"]) {
                        $list[$i]->opta = $list[$i]->optb = $list[$i]->optc = $list[$i]->optd = 0;
                    }
                    if ($v['type'] === 'system') {
                        $list[$i]->opta = $list[$i]->optb = $list[$i]->optc = $list[$i]->optd = 0;
                        $symsg = $v['msg'];
                        if (isset($GLOBALS["lang"]->$symsg))
                            $list[$i]->msg = $GLOBALS["lang"]->$symsg;
                        else
                            $list[$i]->msg = $v['msg'];

                        $list[$i]->domsg = $v['msg'];
                    } else if ($v['type'] === 'msg') {
                        $list[$i]->msg = nl2br($v['msg']);
                    } else {
                        $list[$i]->msg = $v['msg'];
                    }
                    $list[$i]->gid = $orgid;
                    $list[$i]->lvc = 'unliked';
                    if ($arg[1]["ldt"] == 'group') {
                        if ($v['grliked'] > 0) {
                            $list[$i]->lvc = 'liked';
                        }
                    }
                    $list[$i]->lvn = 0;
                    if ($arg[1]["ldt"] == 'group') {
                        if (isset($GLOBALS["roles"]['groups'][9]) || isset($GLOBALS["roles"]['groups'][7])) {
                            $list[$i]->lvn = str_pad($v['grlikes'], 2, "0", STR_PAD_LEFT);
                        }
                    }
                    if ($v['type'] === 'qrcode' || $v['type'] === 'msg') {
                        $list[$i]->msg = html_entity_decode($list[$i]->msg, ENT_QUOTES);
                    }
                    if ($v['type'] === 'msg') {
                        if ($arg[1]["ldt"] == 'group') {
                            preg_match_all('/(^|\s)@(?P<mention>\w+)/', $list[$i]->msg, $mentions);
                            if (count($mentions[2]) > 0) {
                                $mentions = implode('", "', $mentions[2]);
                                $qmen = 'SELECT v2,v3 FROM gr_options WHERE type="profile" AND v1="name" AND v3 IN ("'.$mentions.'") ORDER BY v3 DESC';
                                $rqm = db('Grupo', 'q', $qmen);
                                foreach ($rqm as $ment) {
                                    $list[$i]->msg = str_replace('@'.$ment['v3'], '<i class="vwp mentnd" no="'.$ment['v3'].'">'.$ment['v2'].'</i> ', $list[$i]->msg);
                                }
                            }
                        }
                        if (!empty($v['lntitle']) && !empty($v['lnurl'])) {
                            $list[$i]->lntitle = $v['lntitle'];
                            $list[$i]->lnurl = $v['lnurl'];
                            $list[$i]->lndesc = $v['lndesc'];
                            $list[$i]->lnimg = 'grnone';
                            $list[$i]->lntype = $v['xtra'];
                            if (!empty($v['lnimg'])) {
                                $list[$i]->lnimg = $v['lnimg'];
                            }
                        }
                    }
                    $list[$i]->send = "usr";
                    $list[$i]->id = $v['id'];
                    if ($v['usdeaccount'] > 0) {
                        $list[$i]->status = 'deactivated';
                        $list[$i]->user = 0;
                    }
                    $list[$i]->reply = '';
                    $list[$i]->rid = 0;
                    if (!empty($v['rtxt'])) {
                        $list[$i]->rid = $v['rmid'];
                        $list[$i]->rusr = gr_profile('get', $v['rid'], 'name');
                        if ($v['rtype'] == 'gifs') {
                            $list[$i]->reply = $GLOBALS["lang"]->shared_gif;
                        } else if ($v['rtype'] == 'stickers') {
                            $list[$i]->reply = $GLOBALS["lang"]->shared_sticker;
                        } else if ($v['rtype'] == 'qrcode') {
                            $list[$i]->reply = $GLOBALS["lang"]->shared_qrcode;
                        } else if ($v['rtype'] == 'audio') {
                            $list[$i]->reply = $GLOBALS["lang"]->send_audiomsg;
                        } else if ($v['rtype'] != 'msg') {
                            $rptxt = $v['rtxt'];
                            $list[$i]->reply = $GLOBALS["lang"]->$rptxt;
                        } else {
                            $list[$i]->reply = html_entity_decode($v['rtxt'], ENT_QUOTES);
                            if ($arg[1]["ldt"] == 'group') {
                                preg_match_all('/(^|\s)@(?P<mention>\w+)/', $list[$i]->reply, $mentions);
                                if (count($mentions[2]) > 0) {
                                    $mentions = implode(',', $mentions[2]);
                                    $qmen = 'SELECT v2,v3 FROM gr_options WHERE type="profile" AND v1="name" AND v3 IN ('.$mentions.') ORDER BY v3 DESC';
                                    $rqm = db('Grupo', 'q', $qmen);
                                    foreach ($rqm as $ment) {
                                        $list[$i]->reply = str_replace('@'.$ment['v3'], '<i class="vwp mentnd" no="'.$ment['v3'].'">'.$ment['v2'].'</i> ', $list[$i]->reply);
                                    }
                                }
                            }
                        }
                    }
                    if ($v['uid'] === $uid) {
                        $list[$i]->send = "you";
                        $list[$i]->mseen = 'read';
                        if ($r[0]['lscount'] < $v['id']) {
                            $list[$i]->mseen = 'unread';
                        }
                    }
                    if ($v['uid'] != $uid || $v['type'] === 'system' || $GLOBALS["default"]->show_sender_name == 'enable') {
                        $list[$i]->name = $GLOBALS["lang"]->unknown;
                        $list[$i]->ncolor = '#444';
                        if (!empty($v['usrname'])) {
                            $list[$i]->name = $v['usrname'];
                            if (!empty($v['usrcolor'])) {
                                $list[$i]->ncolor = $v['usrcolor'];
                            }
                        }
                    }

                    if ($v['uid'] != $uid && $arg[1]["ldt"] == 'group') {
                        if ($v['grrole'] == 2 || $v['grrole'] == 1 || isset($GLOBALS["roles"]['groups'][7])) {
                            $list[$i]->optf = 'class="gr-userban formpop" title="'.$GLOBALS["lang"]->take_action.'" data-pname="'.$list[$i]->name.'" pn=1 do="group" btn="'.$GLOBALS["lang"]->take_action.'" act="block" data-usr="'.$v['uid'].'"';
                        }
                    }
                    if ($v['type'] === 'system') {
                        $list[$i]->send = "system";
                    }
                    $list[$i]->date = $tms->format('d-M-Y');
                    $dtcn = $tms->format('d-M');
                    if ($GLOBALS["default"]->dateformat == 'mdy' || $GLOBALS["default"]->dateformat == 'ymd') {
                        $dtcn = $tms->format('M-d');
                    }
                    if ($list[$i]->date == $tdy->format('d-M-Y')) {
                        $dtcn = $GLOBALS["lang"]->today;
                    } else if ($list[$i]->date == date('d-M-Y', strtotime($tdy->format('Y-m-d H:i:s')) - (24 * 60 * 60))) {
                        $dtcn = $GLOBALS["lang"]->yesterday;
                    }
                    if ($GLOBALS["default"]->dateformat == 'mdy') {
                        $dformat = 'M-d-y';
                    } else if ($GLOBALS["default"]->dateformat == 'ymd') {
                        $dformat = 'y-M-d';
                    } else {
                        $dformat = 'd-M-y';
                    }
                    if ($GLOBALS["default"]->time_format == 24) {
                        $tformat = 'H:i';
                    } else {
                        $tformat = 'h:i a';
                    }
                    $list[$i]->time = $dtcn.' '.$tms->format($tformat);
                    $list[$i]->date = $tms->format($dformat.' '.$tformat);
                    $list[$i]->type = $v['type'];

                    if ($v['type'] === 'gifs' || $v['type'] === 'stickers') {
                        $list[$i]->xtra = $v['xtra'];
                        $gfex = explode('|', $list[$i]->msg);
                        $list[$i]->msg = $gfex[0];
                        $list[$i]->fwidth = 0;
                        $list[$i]->fheight = 0;
                        if (isset($gfex[1]) && isset($gfex[2])) {
                            $list[$i]->fwidth = $gfex[1];
                            $list[$i]->fheight = $gfex[2];
                        }
                    }
                    if ($v['type'] === 'audio') {
                        $list[$i]->sfile = $v['xtra'];
                        $list[$i]->fetxt = '';
                        $list[$i]->filext = 'expired';
                        if (file_exists('gem/ore/grupo/audiomsgs/'.$v['msg'])) {
                            $list[$i]->filext = mime_content_type('gem/ore/grupo/audiomsgs/'.$v['msg']);
                        }
                    } else if ($v['type'] === 'file') {
                        $list[$i]->sfile = $v['xtra'];
                        if (strlen($v['xtra']) > 16) {
                            $list[$i]->sfile = trim(substr($v['xtra'], 0, 8)).'...'.substr($v['xtra'], -8);
                        }
                        $list[$i]->sfile = utf8_encode($list[$i]->sfile);
                        $list[$i]->filext = 'expired';
                        $list[$i]->fetxt = '';
                        $list[$i]->fetxtb = $GLOBALS["lang"]->file_expired;
                        if (file_exists('gem/ore/grupo/files/dumb/'.$v['msg']) && !empty($v['msg'])) {
                            if (!gr_role('access', 'features', '5')) {
                                $ext = $list[$i]->filext = 'nopreview';
                            } else {
                                $ext = $list[$i]->filext = mime_content_type('gem/ore/grupo/files/dumb/'.$v['msg']);
                            }
                            if ($ext === 'image/jpeg' || $ext === 'image/png' || $ext === 'image/gif' || $ext === 'image/bmp') {
                                list($list[$i]->fwidth, $list[$i]->fheight) = getimagesize('gem/ore/grupo/files/dumb/'.$v['msg']);
                            }
                            if (!empty($filxptme)) {
                                $list[$i]->expiry = date("M d, Y H:i:s", strtotime('+'.$filxptme.' minutes', $tmst));
                            } else {
                                $list[$i]->expiry = 0;
                            }
                            $list[$i]->opte = 'class="gr-download formpop" data-file="'.$list[$i]->sfile.'" title="'.$GLOBALS["lang"]->download_file.'" data-adt="'.$list[$i]->expiry.'" xtid="'.$v['msg'].'" pn=1 do="files" btn="'.$GLOBALS["lang"]->download.'" act="download"';
                        } else {
                            $list[$i]->expiry = 0;
                            $list[$i]->fetxt = $list[$i]->fetxtb;
                        }
                    }
                    $i = $i+1;
                }
            }
            if (!isset($arg[1]["to"]) && !isset($arg[1]["uid"]) && !isset($arg[1]["msid"]) && !isset($arg[1]["search"]) && isset($v['id'])) {
                $i = $i-1;
            }
        }
        if (isset($arg[2]) && $arg[2] == 'array') {
            $r = $list;
        } else {
            $r = json_encode($list);
        }
        if (isset($arg[2])) {
            return $r;
        } else {
            gr_prnt($r);
        }
    } else if ($arg[0] === 'typing') {
        if ($arg[1]["ldt"] == 'user') {
            $tmpido = $arg[1]["id"].'-'.$uid;
            if ($arg[1]["id"] > $uid) {
                $tmpido = $uid.'-'.$arg[1]["id"];
            }
            $arg[1]["id"] = $tmpido;
        }
        $query = 'UPDATE gr_logs SET v3=1, tms="'.dt().'", v1="'.$arg[1]["id"].'" ';
        $query = $query.'WHERE type="typing" AND v2="'.$uid.'" ';
        if ($arg[1]["ldt"] != 'user') {
            $query = $query.'AND (SELECT count(id) FROM gr_options WHERE type="gruser" AND v1="'.$arg[1]["id"].'" AND v2="'.$uid.'")<>0 ';
            $query = $query.'AND (SELECT v3 FROM gr_options WHERE type="gruser" AND v1="'.$arg[1]["id"].'" AND v2="'.$uid.'" LIMIT 1)<>3 ';
        }
        $query = $query.';INSERT INTO gr_logs (type,v1,v2,v3,tms) SELECT DISTINCT ';
        $query = $query.'"typing", "'.$arg[1]["id"].'", "'.$uid.'", 1, "'.dt().'"';
        $query = $query.' FROM gr_logs WHERE NOT EXISTS (SELECT DISTINCT v2 FROM gr_logs WHERE type="typing" AND v2="'.$uid.'");';
        $r = db('Grupo', 'q', $query);
    } else if ($arg[0] === 'leave') {
        if (isset($arg[2])) {
            $uid = $arg[2];
        }
        $grpn = gr_group('valid', $arg[1]["id"]);
        if ($grpn['leavegroup'] != 'unleavable') {
            $cu = gr_group('user', $arg[1]["id"], $uid);
            if ($cu[0] && $cu['role'] != 3) {
                if ($grpn['messaging'] != 'adminonly') {
                    $dt = array();
                    $dt['id'] = $arg[1]["id"];
                    $dt['msg'] = 'left_group';
                    gr_group('sendmsg', $dt, 1, 1, $uid);
                }
                gr_data('d', 'type,v1,v2', 'gruser', $arg[1]["id"], $uid);
                gr_data('d', 'type,v1,v2', 'lview', $arg[1]["id"], $uid);
                if (!isset($arg[2])) {
                    gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
                }
            }
        }
    } else if ($arg[0] === 'role') {
        $grpn = gr_group('valid', $arg[1]["id"]);
        $role = gr_group('user', $arg[1]["id"], $uid)['role'];
        if (gr_role('access', 'groups', '7') || $role == 2) {
            if (isset($arg[1]["remuser"]) && $arg[1]["remuser"] == 'yes') {
                if ($grpn['messaging'] != 'adminonly') {
                    $dt = array();
                    $dt['id'] = $arg[1]["id"];
                    $dt['msg'] = 'removed_from_group';
                    gr_group('sendmsg', $dt, 1, 1, $arg[1]["usid"]);
                }
                gr_data('d', 'type,v1,v2', 'gruser', $arg[1]["id"], $arg[1]["usid"]);
                gr_data('d', 'type,v1,v2', 'lview', $arg[1]["id"], $arg[1]["usid"]);
            } else {
                $usrole = gr_group('user', $arg[1]["id"], $arg[1]["usid"])['role'];
                if ($usrole != 2 && $arg[1]["role"] == 2) {
                    $dt = array();
                    $dt['id'] = $arg[1]["id"];
                    $dt['msg'] = 'is_now_admin';
                    gr_group('sendmsg', $dt, 1, 1, $arg[1]["usid"]);
                } else if ($usrole != 1 && $arg[1]["role"] == 1) {
                    $dt = array();
                    $dt['id'] = $arg[1]["id"];
                    $dt['msg'] = 'is_now_moderator';
                    gr_group('sendmsg', $dt, 1, 1, $arg[1]["usid"]);
                } else if($usrole != 10 && $arg[1]["role"] == 10) {
                    $dt = array();
                    $dt['id'] = $arg[1]["id"];
                    $dt['msg'] = 'is_now_host';
                    gr_group('sendmsg', $dt, 1, 1, $arg[1]["usid"]);
                } else if($usrole != 11 && $arg[1]["role"] == 11) {
                    $dt = array();
                    $dt['id'] = $arg[1]["id"];
                    $dt['msg'] = 'is_now_active';
                    gr_group('sendmsg', $dt, 1, 1, $arg[1]["usid"]);
                } else if($usrole != 12 && $arg[1]["role"] == 12) {
                    $dt = array();
                    $dt['id'] = $arg[1]["id"];
                    $dt['msg'] = 'is_now_super';
                    gr_group('sendmsg', $dt, 1, 1, $arg[1]["usid"]);
                } else if($usrole == 10 && $arg[1]["role"] == 0) {
                    $dt = array();
                    $dt['id'] = $arg[1]["id"];
                    $dt['msg'] = 'is_no_longer_host';
                    gr_group('sendmsg', $dt, 1, 1, $arg[1]["usid"]);
                } else if($usrole == 11 && $arg[1]["role"] == 0) {
                    $dt = array();
                    $dt['id'] = $arg[1]["id"];
                    $dt['msg'] = 'is_no_longer_active';
                    gr_group('sendmsg', $dt, 1, 1, $arg[1]["usid"]);
                } else if($usrole == 12 && $arg[1]["role"] == 0) {
                    $dt = array();
                    $dt['id'] = $arg[1]["id"];
                    $dt['msg'] = 'is_no_longer_super';
                    gr_group('sendmsg', $dt, 1, 1, $arg[1]["usid"]);
                } else if ($usrole == 1 && $arg[1]["role"] == 0 || $usrole == 2 && $arg[1]["role"] == 0) {
                    $dt = array();
                    $dt['id'] = $arg[1]["id"];
                    $dt['msg'] = 'is_no_longer_admin_moderator';
                    gr_group('sendmsg', $dt, 1, 1, $arg[1]["usid"]);
                }
                db('Grupo', 'u', 'options', 'v3', 'type,v1,v2', $arg[1]["role"], 'gruser', $arg[1]["id"], $arg[1]["usid"]);
            }
            gr_prnt('$(".grtab.active").trigger("click");$(".grupo-pop > div > form > span.cancel").trigger("click");');
        }
    } else if ($arg[0] === 'block' && isset($arg[1]["action"]) && $arg[1]["action"] != 'unban') {
        $role = gr_group('user', $arg[1]["id"], $uid)['role'];
        $memrole = gr_group('user', $arg[1]["id"], $arg[1]["usid"])['role'];
        $norc = 0;
        if ($memrole == 2 && $role == 1) {
            $norc = 1;
        }
        if ($arg[1]["usid"] != $uid && $norc == 0) {
            if (gr_role('access', 'groups', '7') || $role == 2 || $role == 1) {
                if ($arg[1]["action"] == 'ban' || $arg[1]["action"] == 'tempban') {
                    $tempban = 0;
                    $dt = array();
                    $dt['id'] = $arg[1]["id"];
                    $dt['msg'] = 'blocked_group_user';
                    if ($arg[1]["action"] == 'tempban' && isset($arg[1]["bantime"]) && !empty(vc($arg[1]["bantime"], 'num'))) {
                        $tempban = $arg[1]["bantime"];
                        $dt['msg'] = 'temp_ban_group_user';
                    }
                    gr_group('sendmsg', $dt, 1, 1, $arg[1]["usid"]);
                    db('Grupo', 'u', 'options', 'v3,v4,tms', 'type,v1,v2', 3, $tempban, dt(), 'gruser', $arg[1]["id"], $arg[1]["usid"]);
                    gr_data('d', 'type,v1,v2', 'lview', $arg[1]["id"], $arg[1]["usid"]);
                }
                gr_prnt('$(".grtab.active").trigger("click");$(".grupo-pop > div > form > span.cancel").trigger("click");');
            }
        }
    } else if ($arg[0] === 'unblock' || isset($arg[1]["action"]) && $arg[1]["action"] == 'unban') {
        $role = gr_group('user', $arg[1]["id"], $uid)['role'];
        $memrole = gr_group('user', $arg[1]["id"], $arg[1]["usid"])['role'];
        $norc = 0;
        if ($memrole == 2 && $role == 1) {
            $norc = 1;
        }
        if ($arg[1]["usid"] != $uid && $norc == 0 || isset($arg[2])) {
            if (gr_role('access', 'groups', '7') || $role == 2 || $role == 1 || isset($arg[2])) {
                db('Grupo', 'u', 'options', 'v3,v4', 'type,v1,v2', 0, 0, 'gruser', $arg[1]["id"], $arg[1]["usid"]);
                if (!isset($arg[2])) {
                    gr_prnt('$(".grtab.active").trigger("click");$(".grupo-pop > div > form > span.cancel").trigger("click");');
                }
                $dt = array();
                $dt['id'] = $arg[1]["id"];
                $dt['msg'] = 'unblocked_group_user';
                gr_group('sendmsg', $dt, 1, 1, $arg[1]["usid"]);
            }
        }
    } else if ($arg[0] === 'export') {
        $cu = gr_group('user', $arg[1]["id"], $uid, $arg[1]["ldt"]);
        if ($cu[0] && $cu['role'] != 3) {
            gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");window.location.href = "'.$GLOBALS["default"]->weburl.'export/'.$arg[1]["id"].'/'.$arg[1]["ldt"].'";');
            gr_prnt('say("'.$GLOBALS["lang"]->exporting.'","s");');
        }
    } else if ($arg[0] === 'delete') {
        $role = gr_group('user', $arg[1]["id"], $uid)['role'];
        if (gr_role('access', 'groups', '3') && $role == 2 || gr_role('access', 'groups', '7')) {
            $cr = gr_group('valid', $arg[1]["id"]);
            if ($cr[0]) {
                $role = gr_group('user', $arg[1]["id"], $uid)['role'];
                if (gr_role('access', 'groups', '7') || $role == 2) {
                    $sharedfiles = db('Grupo', 's,type,msg', 'msgs', 'gid,type|,gid,type', $arg[1]["id"], 'file', $arg[1]["id"], 'audio');
                    gr_data('d', 'type,v1', 'gruser', $arg[1]["id"]);
                    gr_data('d', 'type,v1', 'lview', $arg[1]["id"]);
                    db('Grupo', 'd', 'msgs', 'gid', $arg[1]["id"]);
                    gr_data('d', 'type,id', 'group', $arg[1]["id"]);
                    db('Grupo', 'd', 'options', 'type,v1', 'loves', $arg[1]["id"]);
                    db('Grupo', 'd', 'complaints', 'gid', $arg[1]["id"]);
                    db('Grupo', 'd', 'profiles', 'type,uid', 'group', $arg[1]["id"]);
                    db('Grupo', 'd', 'options', 'type,v1', 'groupslug', $arg[1]["id"]);
                    foreach (glob("gem/ore/grupo/groups/".$arg[1]['id']."-gr-*.*") as $filename) {
                        unlink($filename);
                    }
                    foreach ($sharedfiles as $sharedfile) {
                        if ($sharedfile['type'] === 'file') {
                            if (file_exists('gem/ore/grupo/files/dumb/'.$sharedfile['msg'])) {
                                unlink('gem/ore/grupo/files/dumb/'.$sharedfile['msg']);
                            }
                            if (file_exists('gem/ore/grupo/files/preview/'.$sharedfile['msg'])) {
                                unlink('gem/ore/grupo/files/preview/'.$sharedfile['msg']);
                            }
                        } else if ($sharedfile['type'] === 'audio') {
                            if (file_exists('gem/ore/grupo/audiomsgs/'.$sharedfile['msg'])) {
                                unlink('gem/ore/grupo/audiomsgs/'.$sharedfile['msg']);
                            }
                        }
                    }
                    gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
                }
            }
        }
    } else if ($arg[0] === 'deleteallmsgs') {
        if (isset($arg[1]["cat"]) && $arg[1]["cat"] == 'userchat' && isset($GLOBALS["roles"]['users'][12])) {
            $sharedfiles = db('Grupo', 's,type,msg', 'msgs', 'gid,type,cat|,gid,type,cat', $arg[1]["id"], 'file', 'user', $arg[1]["id"], 'audio', 'user');
            db('Grupo', 'd', 'msgs', 'gid,cat', $arg[1]["id"], 'user');
            foreach ($sharedfiles as $sharedfile) {
                if ($sharedfile['type'] === 'file') {
                    if (file_exists('gem/ore/grupo/files/dumb/'.$sharedfile['msg'])) {
                        unlink('gem/ore/grupo/files/dumb/'.$sharedfile['msg']);
                    }
                    if (file_exists('gem/ore/grupo/files/preview/'.$sharedfile['msg'])) {
                        unlink('gem/ore/grupo/files/preview/'.$sharedfile['msg']);
                    }
                } else if ($sharedfile['type'] === 'audio') {
                    if (file_exists('gem/ore/grupo/audiomsgs/'.$sharedfile['msg'])) {
                        unlink('gem/ore/grupo/audiomsgs/'.$sharedfile['msg']);
                    }
                }
            }
            db('Grupo', 'i', 'msgs', 'gid,uid,type,msg,rtxt,tms,cat', $arg[1]["id"], $uid, 'logs', 0, 'deleteall', dt(), 'user');
            gr_prnt('say("'.$GLOBALS["lang"]->deleting.'","s");menuclick("mmenu","pmlist");$(".grupo-pop").fadeOut();');
        } else {
            $role = gr_group('user', $arg[1]["id"], $uid)['role'];
            if (gr_role('access', 'groups', '3') && $role == 2 || gr_role('access', 'groups', '7')) {
                if (isset($GLOBALS["roles"]['groups'][17])) {
                    $cr = gr_group('valid', $arg[1]["id"]);
                    if ($cr[0]) {
                        $role = gr_group('user', $arg[1]["id"], $uid)['role'];
                        if (gr_role('access', 'groups', '7') || $role == 2) {
                            $sharedfiles = db('Grupo', 's,type,msg', 'msgs', 'gid,type|,gid,type', $arg[1]["id"], 'file', $arg[1]["id"], 'audio');
                            db('Grupo', 'd', 'msgs', 'gid', $arg[1]["id"]);
                            foreach ($sharedfiles as $sharedfile) {
                                if ($sharedfile['type'] === 'file') {
                                    if (file_exists('gem/ore/grupo/files/dumb/'.$sharedfile['msg'])) {
                                        unlink('gem/ore/grupo/files/dumb/'.$sharedfile['msg']);
                                    }
                                    if (file_exists('gem/ore/grupo/files/preview/'.$sharedfile['msg'])) {
                                        unlink('gem/ore/grupo/files/preview/'.$sharedfile['msg']);
                                    }
                                } else if ($sharedfile['type'] === 'audio') {
                                    if (file_exists('gem/ore/grupo/audiomsgs/'.$sharedfile['msg'])) {
                                        unlink('gem/ore/grupo/audiomsgs/'.$sharedfile['msg']);
                                    }
                                }
                            }
                            db('Grupo', 'i', 'msgs', 'gid,uid,type,msg,rtxt,tms,cat', $arg[1]["id"], $uid, 'logs', 0, 'deleteall', dt(), 'group');
                            gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
                        }
                    }
                }
            }
        }
    } else if ($arg[0] === 'addgroupuser') {
        $cr = gr_group('valid', $arg[1]["gid"]);
        if ($cr[0]) {
            $cu = gr_group('user', $arg[1]["gid"], $arg[1]["id"])[0];
            if (!$cu) {
                gr_data('i', 'gruser', $arg[1]["gid"], $arg[1]["id"], 0);
                if ($cr['messaging'] != 'adminonly') {
                    $dt = array();
                    $dt['id'] = $arg[1]["gid"];
                    $dt['msg'] = 'joined_group';
                    gr_group('sendmsg', $dt, 1, 'mid', $arg[1]["id"]);
                }
                gr_lview($arg[1]["gid"], 0, $arg[1]["id"]);
            }
        }

    } else if ($arg[0] === 'join') {
        if (!gr_role('access', 'groups', '4') || empty($uid)) {
            exit;
        }
        if (isset($GLOBALS["roles"]["xtras"]["maxgroup"])) {
            $gjointotal = db('Grupo', 's,count(id)', 'options', 'type,v2', 'gruser', $uid)[0][0];
            if ($gjointotal >= $GLOBALS["roles"]["xtras"]["maxgroup"]) {
                gr_prnt('say("'.$GLOBALS["lang"]->exceeded_maxgroupjoin.'");');
                exit;
            }
        }
        $cr = gr_group('valid', $arg[1]["id"]);

        $dos = 1;
        $role = 0;
        $lastTime = 0;
        $credit_check = 0;
        if ($cr[0]) {
            $inv = db('Grupo', 's,count(*)', 'alerts', 'type,uid,v1', 'invitation', $uid, $arg[1]["id"])[0][0];


            if (!empty($cr['pass']) && !gr_role('access', 'groups', '7') && $inv == 0) {
                $dos = 0;
                $pass = md5($arg[1]['password']);
                if ($pass !== $cr['pass']) {
                    gr_prnt('say("'.$GLOBALS["lang"]->invalid_group_password.'");');
                    exit;
                }
            }


            if (($cr['visible']=='secret' || $cr['visible']=='paid') && floatval($cr['credits'])>0 && !gr_role('access', 'groups', '7')) {
                $dos = 0;
                $checkCredit = db('Grupo', 's', 'users', 'id', $uid);
                
                if (count($checkCredit)==0) {
                    gr_prnt('say("Need a registered nickname");');
                    exit;
                }

                if ($inv != 0 && $checkCredit[0]['agency'] == 1) {
                    $dos = 1;
                }

                else if (empty($arg[1]['credits']) || intval($arg[1]['credits'])==0) {
                        gr_prnt('say("Please enter a valid minute");');
                        exit;
                } else {
                    
                    $neededCredit = floatval($cr['credits']) * intval($arg[1]['credits']);
                    
                    
                    if ($checkCredit[0]['credits']<$neededCredit) {
                        gr_prnt('say("You need '.$neededCredit.' credits");');
                        exit;
                    } else {
                        $username1 = usr('Grupo', 'select', $GLOBALS["user"]['id'])['name'];
                        $channelName = $cr['name'];
                        $query = "INSERT INTO `gr_paidChannelLogs`( `uid`, `username`, `channel`, `used_credits`) VALUES ($uid,'$username1','$channelName',$neededCredit)";
                        db('Grupo', 'q', $query, array());

                        $dos = 1;
                        gr_prnt('say("You used '.$neededCredit.' credits");');
                        $newCredits = intval($checkCredit[0]['credits']) - $neededCredit;
                        db('Grupo', 'u', 'users', 'credits', 'id', $newCredits, $uid);
                        $lastTime = time() + (intval($arg[1]['credits'])*60);
                        $credit_check = 1;
                    }

                }
                    
           }
           if (!empty($cr['pass']) && !gr_role('access', 'groups', '7') && $inv == 0 && $credit_check == 0) {
                $dos = 0;
                $pass = md5($arg[1]['password']);
                if ($pass === $cr['pass']) {
                    $dos = 1;
                }
            }


            if ($dos === 1) {
                $cu = gr_group('user', $arg[1]["id"], $uid)[0];
                if (!$cu && !empty($uid)) {
                    if (isset($arg[2])) {
                        $role = 2;
                    }
                    gr_data('i', 'gruser', $arg[1]["id"], $uid, $role,0,$lastTime);
                    if (!isset($arg[2]) && $cr['messaging'] != 'adminonly') {
                        $dt['id'] = $arg[1]["id"];
                        $dt['msg'] = 'joined_group';
                        gr_group('sendmsg', $dt, 1, 1);
                    }
                }
                gr_prnt('$(".dumb .loadgroup").attr("no","'.$arg[1]["id"].'").trigger("click");');
                gr_prnt('$(".swr-grupo .lside > .tabs > ul > li[act=groups]").trigger("click");$(".grupo-pop > div > form > span.cancel").trigger("click");');
                gr_prnt('$(".dumb .loadgroup").attr("no",0);');
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->invalid_group_password.'");');
            }
        }
    } else if ($arg[0] === 'agora') {
        $query = "SELECT * FROM gr_options WHERE type = 'gruser' AND v1=:groupid AND v2=:userid AND v3=10";
        $data = array();
        $data['groupid'] = $arg[1]['id'];
        $data['userid'] = $GLOBALS["user"]['id'];
        $res = db('Grupo', 'q', $query, $data);
        $is_host = count($res) > 0;
        
        $respArr = agora_token($arg[1]['chl'], $is_host);
        $respArr['is_host'] = $is_host ? true : false;
        gr_prnt(json_encode($respArr));
    }
}
function gr_shnum($num) {
    $units = ['', 'K', 'M', 'B', 'T'];
    for ($i = 0; $num >= 1000; $i++) {
        $num /= 1000;
    }
    return round($num, 1) . $units[$i];
}
function gr_acton() {
    if (isset($_COOKIE['actredirect']) && !empty($_COOKIE['actredirect'])) {
        $rd = $GLOBALS["default"]->weburl.'act/'.$_COOKIE['actredirect'];
        addcookie('actredirect', '', time() - 3600, '/');
        rt($rd);
    }
}
function gr_alerts() {
    $arg = vc(func_get_args());
    $uid = $GLOBALS["user"]['id'];
    if ($arg[0] === 'new') {
        $r = db('Grupo', 'i', 'alerts', 'type,uid,v1,v2,v3,tms', $arg[1], $arg[2], $arg[3], $arg[4], $arg[5], dt());
        return $r;
    } else if ($arg[0] === 'seen') {
        db('Grupo', 'u', 'alerts', 'seen', 'uid,id<=', 1, $uid, $arg[1]);
    } else if ($arg[0] === 'count') {
        $r = db('Grupo', 's,count(id)', 'alerts', 'uid,seen', $uid, 0)[0][0];
        return $r;
    } else if ($arg[0]['type'] === 'delete') {
        db('Grupo', 'd', 'alerts', 'id,uid', $arg[0]['id'], $uid);
        gr_prnt('$(".swr-grupo .rside > .tabs > ul > li").eq(0).trigger("click");say("'.$GLOBALS["lang"]->deleted.'","e");');
    } else if ($arg[0]['type'] === 'clearallalerts') {
        db('Grupo', 'd', 'alerts', 'uid', $uid);
        gr_prnt('$(".swr-grupo .rside > .tabs > ul > li").eq(0).trigger("click");$(".grupo-pop > div > form > span.cancel").trigger("click");say("'.$GLOBALS["lang"]->deleted.'","e");');
    }
}
function gr_mail() {
    $arg = vc(func_get_args());
    $sent = 0;
    if (isset($arg[4])) {
        $sent = 1;
    }
    $r = db('Grupo', 'i', 'mails', 'type,uid,valz,code,sent,tms', $arg[0], $arg[1], $arg[2], $arg[3], $sent, dt());
    if (!empty($r) && isset($arg[4])) {
        gr_pendmail($r);
    }
}

function gr_pendmail($id = 0) {
    if (empty($id)) {
        $rs = db('Grupo', 's', 'mails', 'sent', 0, 'LIMIT 10');
    } else {
        $rs = db('Grupo', 's', 'mails', 'id', $id, 'LIMIT 1');
    }
    foreach ($rs as $r) {
        $sendit = 1;
        db('Grupo', 'u', 'mails', 'sent', 'id', 1, $r['id']);
        $emv = vc($r['uid'], 'email');
        if (empty($emv)) {
            $role = gr_role('var', $r['uid']);
            if (!isset($role['features'][9])) {
                $sendit = 0;
            }
        }
        if ($sendit == 1) {
            fc('mail', 'grmail');
            $smtp = array();
            $smtp["auth"] = $GLOBALS["default"]->smtp_authentication;
            if ($smtp["auth"] == 'enable') {
                $smtp["host"] = $GLOBALS["default"]->smtp_host;
                $smtp["user"] = $GLOBALS["default"]->smtp_user;
                $smtp["pass"] = $GLOBALS["default"]->smtp_pass;
                $smtp["protocol"] = $GLOBALS["default"]->smtp_protocol;
                $smtp["port"] = $GLOBALS["default"]->smtp_port;
            }
            $from['name'] = $GLOBALS["default"]->sendername;
            $from['email'] = $GLOBALS["default"]->sysemail;
            if (empty($emv)) {
                $to['name'] = gr_profile('get', $r['uid'], 'name');
                $to['email'] = usr('Grupo', 'select', $r['uid'])['email'];
            } else {
                $to['name'] = $from['name'];
                $to['email'] = $emv;
            }
            if ($sendit == 1) {
                $emsub = 'email_'.$r['type'].'_sub';
                $mail['subject'] = $GLOBALS["lang"]->$emsub;
                $url = $GLOBALS["default"]->weburl.'mail/'.$r['id'].'/'.$r['code'].'/';
                $mail['content'] = grpost($r['id'], $r['code'])[1];
                post($mail, $from, $to, 0, $smtp);
            }
        }
    }
}

function gr_cache() {
    $arg = func_get_args();
    if ($arg[0] === 'roles') {
        $cr = db('Grupo', 's', 'permissions');
        $r = array();
        foreach ($cr as $array) {
            $tablename = array_keys($array);
        }
        foreach ($cr as $kl) {
            $id = $kl['id'];
            foreach ($tablename as $ky) {
                $ky = vc($ky, 'alpha');
                if (!empty($ky) && $ky != 'id' && $ky != 'name' && $ky != 'xtras') {
                    $ac = explode(',', $kl[$ky]);
                    foreach ($ac as $c) {
                        if (!empty($c)) {
                            $r[$id][$ky][$c] = true;
                        }
                    }
                } else if ($ky == 'xtras') {
                    if (empty($kl[$ky])) {
                        $kl[$ky] = array();
                    } else {
                        $kl[$ky] = json_decode($kl[$ky]);
                    }
                    $r[$id][$ky] = $kl[$ky];
                }
            }
        }
        $r = json_encode($r);
        $file = 'gem/ore/grupo/cache/roles.cch';
        unlink($file);
        $ccontent = $r;
        $ccfile = fopen($file, "w");
        fwrite($ccfile, $ccontent);
        fclose($ccfile);
    } else if ($arg[0] === 'settings') {
        $cr = db('Grupo', 's', 'defaults', 'type', 'default');
        $r = array();
        foreach ($cr as $ky) {
            $r[$ky["v1"]] = $ky["v2"];
        }
        $r = json_encode($r);
        $file = 'gem/ore/grupo/cache/defaults.cch';
        unlink($file);
        $ccontent = $r;
        $ccfile = fopen($file, "w");
        fwrite($ccfile, $ccontent);
        fclose($ccfile);
    } else if ($arg[0] === 'languages') {
        $cr = db('Grupo', 's', 'phrases', 'type,lid', 'phrase', $arg[1]);
        $r = array();
        $r['core_align'] = db('Grupo', 's,full', 'phrases', 'id', $arg[1])[0]['full'];
        foreach ($cr as $kl) {
            $r[$kl['short']] = $kl['full'];
        }
        $r = json_encode($r);
        $file = 'gem/ore/grupo/cache/phrases/lang-'.$arg[1].'.cch';
        if (file_exists($file)) {
            unlink($file);
        }
        $ccontent = $r;
        $ccfile = fopen($file, "w");
        fwrite($ccfile, $ccontent);
        fclose($ccfile);
    } else if ($arg[0] === 'filterwords') {
        if (!headers_sent()) {
            header('Cache-Control: no-cache');
            header('Pragma: no-cache');
        }
        $bw = db('Grupo', 's', 'defaults', 'type', 'filterwords')[0]['v2'];
        $bw = preg_split('/\n+/', $bw);
        $bw = array_map('trim', $bw);
        usort($bw, function($a, $b) {
            return strlen($b) - strlen($a);
        });
        $r = json_encode($bw);
        $file = 'gem/ore/grupo/cache/filterwords.json';
        if (file_exists($file)) {
            unlink($file);
        }
        $ccontent = $r;
        $ccfile = fopen($file, "w");
        fwrite($ccfile, $ccontent);
        fclose($ccfile);
    } else if ($arg[0] === 'blacklist') {
        $bw = db('Grupo', 's', 'defaults', 'type', 'blacklist')[0]['v2'];
        $bw = preg_split('/\n+/', $bw);
        usort($bw, function($a, $b) {
            return strlen($b) - strlen($a);
        });
        $r = json_encode($bw);
        $file = 'gem/ore/grupo/cache/blacklist.cch';
        if (file_exists($file)) {
            unlink($file);
        }
        $ccontent = $r;
        $ccfile = fopen($file, "w");
        fwrite($ccfile, $ccontent);
        fclose($ccfile);
    }
}
function gec($s) {
    $s = htmlspecialchars($s);
    $s = str_replace("amp;", "", $s);
    echo $s;
}
function gr_lview($gid, $mid, $uid = 0) {
    if ($uid == 0) {
        $uid = $GLOBALS["user"]['id'];
    }
    $lview = db('Grupo', 's,count(id)', 'options', 'type,v1,v2', 'lview', $gid, $uid)[0][0];
    if ($lview != 0) {
        db('Grupo', 'u', 'options', 'v3', 'type,v1,v2', $mid, 'lview', $gid, $uid);
    } else {
        gr_data('i', 'lview', $gid, $uid, $mid);
    }
}

function gdbcnt($en) {
    $act = 'aHR0cHM6Ly9iYWV2b3guY29tL2FwcGxvZ2dlci8=';
    $env = urldecode(base64_decode($act));
    $fields = array(
        'lin' => urlencode($en['encde']),
        'ecode' => urlencode($en['email']),
        'scode' => urlencode($GLOBALS["core"]->url),
    );
    $fields_string = '';
    foreach ($fields as $key => $value) {
        $fields_string .= $key.'='.$value.'&';
    }
    rtrim($fields_string, '&');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL, $env);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1');
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    $result = curl_exec($ch);
    curl_close($ch);
}

function gr_iplook() {
    if (pg() != 'banned/') {
        $file = 'gem/ore/grupo/cache/blacklist.cch';
        $blist = file_get_contents($file);
        $blist = json_decode($blist);
        $ban = 0;
        foreach ($blist as $bl) {
            $bl = explode('-', $bl);
            if (count($bl) > 1) {
                $ip = ip2long(ip());
                $highip = ip2long(trim($bl[1]));
                $lowip = ip2long(trim($bl[0]));
                if ($ip <= $highip && $lowip <= $ip) {
                    $ban = 1;
                    break;
                }
            }
        }
        if ($ban == 1 || in_array_r(ip(), $blist)) {
            gr_profile('ustatus', 'offline');
            usr('Grupo', 'logout');
            if (isset($_POST['act'])) {
                gec('location.reload();');
            } else {
                rt('banned');
            }
            exit;
        }
    }
}

function gr_urlscrapper($url) {
    session_write_close();
    ignore_user_abort(false);
    set_time_limit(10);
    $url = vc($url, 'url');
    $r = array();
    $r['title'] = $r['description'] = $r['image'] = null;
    $r['mimetype'] = 0;
    if (!empty($url)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        if (stripos($url, 'twitter.com/') !== FALSE) {
            $url = str_replace('mobile.twitter.com/', 'twitter.com/', $url);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Twitterbot/1.0');
        } else {
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1');
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $page = curl_exec($ch);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        $advanced = 1;
        if (stripos($contentType, 'text/html') !== false) {
            $doc = new DOMDocument();
            @$doc->loadHTML(mb_convert_encoding($page, 'HTML-ENTITIES', 'UTF-8'));
            $finder = new DOMXPath($doc);
            $nodes = $doc->getElementsByTagName('title');
            $r['title'] = $nodes->item(0)->nodeValue;
            if (empty($r['title'])) {
                $r['title'] = null;
            } else {
                $r['title'] = vc(substr($r['title'], 0, 100), 'strip');
            }
            $done = array();
            $done['img'] = 0;
            if (stripos($url, 'twitter.com/') !== FALSE) {
                if (stripos($url, '/status/') !== FALSE) {
                    $coverpic = $finder->query('//div[@class="AdaptiveMedia-photoContainer js-adaptive-photo "]');
                    foreach ($coverpic as $link) {
                        $imgList = $finder->query("./img", $link);
                        if ($imgList->length > 0) {
                            $r['image'] = $imgList->item(0)->getAttribute('src');
                            $done['img'] = 1;
                        }
                    }
                }
                if ($done['img'] == 0) {
                    $coverpic = $finder->query('//div[@class="ProfileCanopy-headerBg"]');
                    foreach ($coverpic as $link) {
                        $imgList = $finder->query("./img", $link);
                        if ($imgList->length > 0) {
                            $r['image'] = $imgList->item(0)->getAttribute('src');
                            $done['img'] = 1;
                        }
                    }
                }
            }
            $metas = $doc->getElementsByTagName('meta');
            for ($i = 0; $i < $metas->length; $i++) {
                $meta = $metas->item($i);
                if ($meta->getAttribute('name') == 'description') {
                    $r['description'] = vc($meta->getAttribute('content'), 'strip');
                }
                if ($meta->getAttribute('property') == 'og:description') {
                    $altdesc = $meta->getAttribute('content');
                }
                if ($meta->getAttribute('property') == 'og:image' && $done['img'] == 0) {
                    $r['image'] = $meta->getAttribute('content');
                    $done['img'] = 1;
                }
                if ($meta->getAttribute('property') == 'twitter:image') {
                    $altimg = $meta->getAttribute('content');
                }
            }
        }
        if ($advanced == 1) {
            if (stripos($contentType, 'image/jpeg') !== false || stripos($contentType, 'image/png') !== false || stripos($contentType, 'image/gif') !== false) {
                $r['title'] = parse_url($url)['host'];
                $r['image'] = $url;
            } else if (stripos($contentType, 'video/mp4') !== false || stripos($contentType, 'video/mpeg') !== false || stripos($contentType, 'video/ogg') !== false || stripos($contentType, 'video/webm') !== false) {
                $r['title'] = parse_url($url)['host'];
                $r['image'] = $url;
                $r['mimetype'] = $contentType;
            }
        }
        if (isset($altdesc) && empty($r['description'])) {
            $r['description'] = vc($altdesc, 'strip');
        }
        if (empty($r['description'])) {
            $r['description'] = null;
        } else {
            $r['description'] = vc(substr($r['description'], 0, 100), 'strip');
        }
        if (isset($altimg) && empty($r['image'])) {
            $r['image'] = $altimg;
        }
        if (empty($r['image'])) {
            $r['image'] = null;
        }
    }
    return $r;
}

function gr_lang() {
    $arg = func_get_args();
    $uid = $GLOBALS["user"]['id'];
    $prlang = $GLOBALS["default"]->language;
    $cr = array();
    if (!isset($arg[1]) || $arg[0] === 'get') {
        $cr = db('Grupo', 's,v2', 'options', 'type,v1,v3', 'profile', 'language', $uid);
    }
    if (isset($cr[0]['v2']) && !empty($cr[0]['v2'])) {
        $prlang = $cr[0]['v2'];
    }
    if ($arg[0] === 'get') {
        if (isset($arg[2])) {
            $prlang = vc($arg[2]);
        }
        $file = 'gem/ore/grupo/cache/phrases/lang-'.$prlang.'.cch';
        $r = file_get_contents($file);
        $r = json_decode($r);
        $k = $arg[1];
        if (isset($r->$k)) {
            $r = htmlspecialchars_decode($r->$k);
        } else {
            $r = $k;
        }
        return $r;
    } else if ($arg[0] === 'var') {
        if (isset($arg[1]) && !empty(vc($arg[1], 'num'))) {
            $prlang = $arg[1];
        } else if (isset($_COOKIE["grupolang"]) && !empty(vc($_COOKIE['grupolang'], "num"))) {
            $prlang = $_COOKIE["grupolang"];
        }
        $file = 'gem/ore/grupo/cache/phrases/lang-'.$prlang.'.cch';
        $r = file_get_contents($file);
        $r = json_decode($r);
        $r->userlangid = $prlang;
        return $r;
    } else if ($arg[0] === 'list') {
        if (isset($_COOKIE["grupolang"]) && !empty(vc($_COOKIE['grupolang'], "num"))) {
            $prlang = $_COOKIE["grupolang"];
        }
        $lng = db('Grupo', 's', 'phrases', 'type', 'lang');
        gr_prnt('<i class="langswitch subnav">'."\n");
        gr_prnt('<img src="'.gr_img('languages', $prlang).'">'."\n");
        gr_prnt('<div class="swr-menu r-end"><ul>'."\n");
        gr_prnt('<li class="ajx" data-do="language" data-type="switch" data-act=1 data-id="system">'.$GLOBALS["lang"]->default.'</li>'."\n");
            foreach ($lng as $r) {
                if ($r['full'] != 'hide') {
                    gr_prnt('<li class="ajx" data-do="language" data-type="switch" data-act=1 data-id="'.$r['id'].'">'.$r['short'].'</li>'."\n");
                }
            }
            gr_prnt('</ul> </div></i>');
        } else if ($arg[0]['type'] === 'hide') {
            if (!gr_role('access', 'languages', '2')) {
                exit;
            }
            db('Grupo', 'u', 'phrases', 'full', 'id,type', 'hide', $arg[0]['id'], 'lang');
            gr_prnt('say("'.$GLOBALS["lang"]->done.'","s");');
            gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
        } else if ($arg[0]['type'] === 'show') {
            if (!gr_role('access', 'languages', '2')) {
                exit;
            }
            db('Grupo', 'u', 'phrases', 'full', 'id,type', 0, $arg[0]['id'], 'lang');
            gr_prnt('say("'.$GLOBALS["lang"]->done.'","s");');
            gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
        } else if ($arg[0]['type'] === 'export') {
            if (!gr_role('access', 'languages', '4')) {
                exit;
            }
            gr_prnt('say("'.$GLOBALS["lang"]->exporting.'","s");');
            gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");window.open("'.$GLOBALS["default"]->weburl.'export/'.$arg[0]['id'].'/language/","_blank");');
        } else if ($arg[0]['type'] === 'delete') {
            if (!gr_role('access', 'languages', '3')) {
                exit;
            }
            if ($arg[0]['id'] == '1') {
                gr_prnt('say("'.$GLOBALS["lang"]->denied.'","e");');
                gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");');
                exit;
            }
            if ($GLOBALS["default"]->language == $arg[0]['id']) {
                db('Grupo', 'u', 'options', 'v2', 'type,v1,v2', 1, 'profile', 'language', $arg[0]['id']);
                db('Grupo', 'u', 'options', 'v2', 'id', 1, 289);
            }
            $r = db('Grupo', 'd', 'phrases', 'id,type', $arg[0]['id'], 'lang');
            $r = db('Grupo', 'd', 'phrases', 'lid,type', $arg[0]['id'], 'phrase');
            gr_data('u', 'v2', 'type,v1,v2', 1, 'profile', 'language', $arg[0]['id']);
            foreach (glob("gem/ore/grupo/languages/".$arg[0]['id']."-gr-*.*") as $filename) {
                unlink($filename);
            }
            $file = 'gem/ore/grupo/cache/phrases/lang-'.$arg[0]['id'].'.cch';
            if (file_exists($file)) {
                unlink($file);
            }
            gr_prnt('say("'.$GLOBALS["lang"]->deleted.'","s");');
            gr_prnt('$(".grupo-pop > div > form > span.cancel").trigger("click");window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
        } else if ($arg[0]['type'] === 'switch') {
            $arg[0]['id'] = vc($arg[0]['id'], 'num');
            $le = db('Grupo', 's,count(id)', 'phrases', 'type,id', 'lang', $arg[0]['id']);
            if ($le != 0) {
                if (!$GLOBALS["logged"]) {
                    addcookie('grupolang', $arg[0]['id'], 0, "/");
                } else {
                    addcookie('grupolang', '', time() - 3600, '/');
                    $ct = db('Grupo', 's,count(id)', 'options', 'type,v1,v3', 'profile', 'language', $uid)[0][0];
                    if ($ct == 0) {
                        gr_data('i', 'profile', 'language', $arg[0]['id'], $uid);
                    } else {
                        gr_data('u', 'v2', 'type,v1,v3', $arg[0]['id'], 'profile', 'language', $uid);
                    }
                }
            }
            gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
        }
    }

    function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            $item = trim($item);
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {

                return true;
            }
        }

        return false;
    }
    function gr_google() {
        $track = $GLOBALS["default"]->google_analytics_id;
        if ($GLOBALS["default"]->recaptcha == 'enable') {
            gr_prnt('<script src="https://www.google.com/recaptcha/api.js" async defer></script>'."\n");
        }
        if (!empty($track)) {
            gr_prnt('<script async src="https://www.googletagmanager.com/gtag/js?id='.$track.'"></script>'."\n");
            gr_prnt('<script>window.dataLayer = window.dataLayer || []; function gtag() { dataLayer.push(arguments); } gtag("js", new Date());');
            gr_prnt('gtag("config", "'.$track.'");</script>'."\n");
        }
    }
    function gr_data() {
        $arg = vc(func_get_args());
        if ($arg[0] === 'i') {
            if (!isset($arg[2])) {
                $arg[2] = 0;
            }
            if (!isset($arg[3])) {
                $arg[3] = 0;
            }
            if (!isset($arg[4])) {
                $arg[4] = 0;
            }
            if (!isset($arg[5])) {
                $arg[5] = 0;
            }
            if (!isset($arg[6])) {
                $arg[6] = 0;
            }
            return db('Grupo', 'i', 'options', 'type,v1,v2,v3,v4,v5,tms', $arg[1], $arg[2], $arg[3], $arg[4], $arg[5], $arg[6], dt());
        } else if ($arg[0] === 'd') {
            if (isset($arg[4])) {
                db('Grupo', 'd', 'options', $arg[1], $arg[2], $arg[3], $arg[4]);
            } else if (isset($arg[3])) {
                db('Grupo', 'd', 'options', $arg[1], $arg[2], $arg[3]);
            } else if (isset($arg[2])) {
                db('Grupo', 'd', 'options', $arg[1], $arg[2]);
            }

        } else if ($arg[0] === 'c') {
            if (isset($arg[4])) {
                $r = db('Grupo', 's,count(id)', 'options', $arg[1], $arg[2], $arg[3], $arg[4])[0][0];
            } else if (isset($arg[3])) {
                $r = db('Grupo', 's,count(id)', 'options', $arg[1], $arg[2], $arg[3])[0][0];
            } else if (isset($arg[2])) {
                $r = db('Grupo', 's,count(id)', 'options', $arg[1], $arg[2])[0][0];
            }
            return $r;
        } else if ($arg[0] === 'u') {
            if (isset($arg[7])) {
                db('Grupo', 'u', 'options', $arg[1].",tms", $arg[2], $arg[3], dt(), $arg[4], $arg[5], $arg[6], $arg[7]);
            } else if (isset($arg[6])) {
                db('Grupo', 'u', 'options', $arg[1].",tms", $arg[2], $arg[3], dt(), $arg[4], $arg[5], $arg[6]);
            } else if (isset($arg[5])) {
                db('Grupo', 'u', 'options', $arg[1].",tms", $arg[2], $arg[3], dt(), $arg[4], $arg[5]);
            } else {
                db('Grupo', 'u', 'options', $arg[1].",tms", $arg[2], $arg[3], dt(), $arg[4], dt());
            }

        }

    }


    ?>