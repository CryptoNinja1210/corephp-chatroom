<?php if(!defined('s7V9pz')) {die();}?><?php
error_reporting(0);
session_write_close();
ignore_user_abort(false);
if (empty(vc($GLOBALS["default"]->request_timeout, 'num'))) {
    $GLOBALS["default"]->request_timeout = 10;
}
set_time_limit($GLOBALS["default"]->request_timeout+5);
function gr_live() {
    $timeout_in_seconds = $GLOBALS["default"]->request_timeout;
    $start_time = time();
    $inp = explode('/', pg('act/updates'));
    $gid = $ldt = $lastid = $poll = 0;
    $timeout = false;
    $uid = $GLOBALS["user"]['id'];
    $src = '"'.$uid.'-%"';
    $srck = '"%-'.$uid.'"';
    $arg = vc(func_get_args());
    $list = array();
    if ($inp[1] == 'user' && strpos($inp[0], '-') == false) {
        $inp[0] = vc($inp[0], 'num');
    }
    if (empty($poll) && !empty($inp[0]) && !empty($inp[1])) {
        $gid = $inp[0];
        $ldt = $inp[1];
        $lastid = $inp[3];
        if ($inp[1] == 'user' && strpos($inp[0], '-') !== false) {
            $gid = $inp[0];
        } else if ($inp[1] == 'user') {
            $tmpido = $inp[0].'-'.$uid;
            if ($inp[0] > $uid) {
                $tmpido = $uid.'-'.$inp[0];
            }
            $gid = $tmpido;
        }
    }
    if (!isset($_COOKIE['graddelay'])) {
        addcookie('graddelay', 0, 0, "/");
    }


    $timeCheckQuery = "SELECT * FROM `gr_options` WHERE `type` = 'gruser' and v2 = '$uid'";

    $check_time = db('Grupo', 'q', $timeCheckQuery);

    foreach ($check_time as $k1 => $v22) {

         if (intval($v22['v5'])==0)
            continue;
         if (intval($v22['v5']) > time()) 
            continue;

        $dt = array();
        $dt['id'] = $v22["v1"];
        $dt['msg'] = 'left_group';
        gr_group('sendmsg', $dt, 1, 1, $uid);

        gr_data('d', 'type,v1,v2', 'gruser', $dt['id'], $uid);
        gr_data('d', 'type,v1,v2', 'lview', $dt['id'], $uid);
        echo json_encode(array('eval'=>'window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/"; $(".swr-grupo .lside > .tabs > ul > li[act=groups]").trigger("click"); say("You leave channel");'));
        exit();
    }



    while (empty($poll) && !$timeout) {
        $poll = 0;
        $lastseen = 0;
        $timeout = (time() - $start_time) > $timeout_in_seconds;
        $data = array();
        $data['cat'] = $ldt;
        $data['gid'] = $gid;
        $data['lastid'] = $lastid;
        $data['uid'] = $uid;
        $data['pmsa'] = $uid.'-%';
        $data['pmsb'] = '%-'.$uid;
        $data['orgid'] = $inp[0];
        $query = 'SELECT ';
        $ad_divider = '[gradslot'.rn(4).']';
        if (!empty($inp[0]) && !empty($inp[1])) {
            $query = $query.'(SELECT COUNT(gr_msgs.id) AS unseenmsgs ';
            $query = $query.'FROM gr_msgs WHERE gr_msgs.cat = :cat ';
            if (!empty($inp[10]) && $inp[10] == 'yes') {
                $query = $query.'AND gr_msgs.id=0 ';
            }
            if ($GLOBALS["default"]->sysmessages == 'disable') {
                $query = $query.'AND gr_msgs.type<>"system" ';
            }
            if ($ldt == 'user') {
                $query = $query.'AND id > (SELECT IFNULL((SELECT MIN(CAST(v3 AS SIGNED)) FROM gr_options WHERE type="clearchat" AND v1=:uid AND v2=:orgid LIMIT 1),0)) ';
            }
            $query = $query.'AND gr_msgs.gid = :gid AND gr_msgs.id > :lastid) AS unseenmsgs,';

            if (!empty($inp[0]) && !empty($inp[1])) {
                if (empty($_COOKIE['graddelay']) || (time() - $_COOKIE['graddelay']) > $GLOBALS["default"]->ad_delay) {
                    $query = $query.'(SELECT CONCAT(name,"'.$ad_divider.'",content,"'.$ad_divider.'",adheight) AS ads ';
                    $query = $query.'FROM gr_ads WHERE gr_ads.adslot = "chatmessage" ORDER BY rand() LIMIT 1) AS grads,';
                    addcookie('graddelay', time(), 0, "/");
                }
            }

            $query = $query.'(SELECT IFNULL(MIN(CAST(gr_options.v3 AS SIGNED)), 0) AS lastseen ';
            $query = $query.'FROM gr_options WHERE gr_options.type = "lview" ';
            $query = $query.'AND gr_options.v1 = :gid) AS lastseenmsg,';

            $query = $query.'(SELECT GROUP_CONCAT(CONCAT(gr_options.v2) SEPARATOR ";") AS typing ';
            $query = $query.'FROM gr_options INNER JOIN gr_logs ';
            $query = $query.'ON gr_options.v3 = gr_logs.v2 WHERE gr_logs.type = "typing" ';
            $query = $query.'AND gr_options.type = "profile" AND gr_logs.v1 = :gid ';
            $query = $query.'AND gr_options.v1 = "name" AND gr_logs.v3 <> 0 ';
            $query = $query.'AND gr_logs.v2 <> :uid ';
            $query = $query.'LIMIT 3) AS typing,';

            $query = $query.'(SELECT gr_logs.id FROM gr_logs WHERE gr_logs.type = "typing" ';
            $query = $query.'AND gr_logs.v1 = :gid AND gr_logs.v3 <> 0 AND gr_logs.v2 <> :uid ';
            $query = $query.'ORDER BY gr_logs.tms DESC LIMIT 1) AS typid,';
        }
        if (!empty($inp[2]) && $inp[2] == 'on') {
            $query = $query.'(SELECT GROUP_CONCAT(CONCAT(usp.gid, ",", usp.tunseen) SEPARATOR ";") AS tunseen ';
            $query = $query.'FROM (SELECT COUNT(gr_msgs.id) AS tunseen,gr_msgs.gid AS gid ';
            $query = $query.'FROM gr_msgs,gr_options WHERE gr_options.v1 = gr_msgs.gid ';
            $query = $query.'AND gr_options.type = "lview" AND gr_msgs.gid <> :gid ';
            $query = $query.'AND gr_msgs.id > gr_options.v3 AND gr_options.v2 = :uid ';
            $query = $query.'AND gr_msgs.gid LIKE :pmsa AND gr_msgs.cat = "user" ';
            $query = $query.'OR gr_options.v1 = gr_msgs.gid AND gr_options.type = "lview" ';
            $query = $query.'AND gr_msgs.gid <> :gid AND gr_msgs.id > gr_options.v3 ';
            $query = $query.'AND gr_options.v2 = :uid AND gr_msgs.gid LIKE :pmsb AND gr_msgs.cat = "user" ';
            $query = $query.'GROUP BY gr_msgs.gid) usp) AS unseenpm,';

            $query = $query.'(SELECT SUM(usp.tunseen) AS expr1 FROM (SELECT COUNT(gr_msgs.id) AS tunseen ';
            $query = $query.'FROM gr_msgs,gr_options WHERE gr_options.v1 = gr_msgs.gid ';
            $query = $query.'AND gr_options.type = "lview" AND gr_msgs.gid <> :gid ';
            $query = $query.'AND gr_msgs.id > gr_options.v3 AND gr_options.v2 = :uid ';
            $query = $query.'AND gr_msgs.gid LIKE :pmsa AND gr_msgs.cat = "user" ';
            $query = $query.'OR gr_options.v1 = gr_msgs.gid AND gr_options.type = "lview" ';
            $query = $query.'AND gr_msgs.gid <> :gid AND gr_msgs.id > gr_options.v3 ';
            $query = $query.'AND gr_options.v2 = :uid AND gr_msgs.gid LIKE :pmsb ';
            $query = $query.'AND gr_msgs.cat = "user" GROUP BY gr_msgs.gid ';
            $query = $query.') usp) AS totunseenpm, ';
        }
        $query = $query.'(SELECT GROUP_CONCAT(CONCAT(groupid, ",", tunseen) SEPARATOR ";") ';
        $query = $query.'FROM (SELECT gp.v1 AS groupid,COUNT(ms.id) AS tunseen ';
        $query = $query.'FROM gr_msgs ms INNER JOIN gr_options op ';
        $query = $query.'ON ms.gid = op.v1 AND ms.type <> "like" AND ms.type <> "logs" ';
        if ($GLOBALS["default"]->sysmessages == 'disable') {
            $query = $query.'AND ms.type<>"system" ';
        }
        $query = $query.'AND ms.id > op.v3,gr_options gp WHERE gp.type = "gruser" ';
        $query = $query.'AND op.v1 = gp.v1 AND gp.v2 = :uid AND gp.v1 <> :gid ';
        $query = $query.'AND op.type = "lview" AND op.v2 = :uid AND gp.v3 <> 3 ';
        $query = $query.'GROUP BY gp.v1,op.v3 HAVING COUNT(ms.id) <> 0) usg) AS unseengroup, ';

        $query = $query.'(SELECT SUM(tunseen) FROM (SELECT gp.v1 AS groupid,COUNT(ms.id) AS tunseen ';
        $query = $query.'FROM gr_msgs ms INNER JOIN gr_options op ON ms.gid = op.v1 ';
        if ($GLOBALS["default"]->sysmessages == 'disable') {
            $query = $query.'AND ms.type<>"system" ';
        }
        $query = $query.'AND ms.type <> "like" AND ms.type <> "logs" AND CAST(ms.id AS SIGNED) > CAST(op.v3 AS SIGNED), ';
        $query = $query.'gr_options gp WHERE gp.type = "gruser" AND op.v1 = gp.v1 ';
        $query = $query.'AND gp.v2 = :uid AND gp.v1 <> :gid AND op.type = "lview" AND op.v2 = :uid ';
        $query = $query.'AND gp.v3 <> 3 GROUP BY gp.v1,op.v3 HAVING COUNT(ms.id) <> 0) usg) AS totunseengroup, ';

        $query = $query.'(SELECT COUNT(id) FROM gr_alerts WHERE uid = :uid AND seen = 0) AS unseenalerts';

        if (!empty(vc($inp[0], 'num')) && !empty($inp[1])) {
            $query = $query.',(SELECT count(cp.id)';
            $query = $query.' FROM gr_complaints cp,gr_options rl WHERE ';
            if (isset($GLOBALS["roles"]['groups'][7])) {
                $query = $query.'cp.status=1 AND rl.type="gruser" AND rl.v1=cp.gid AND rl.v2=:uid ';
                $query = $query.'AND cp.gid=:gid ';
            } else {
                $query = $query.'cp.status=1 AND rl.type="gruser" AND rl.v1=cp.gid AND rl.v2=:uid AND rl.v3=2 AND cp.msid<>0 ';
                $query = $query.'AND cp.gid=:gid ';
                $query = $query.'OR cp.status=1 AND rl.type="gruser" AND rl.v1=cp.gid AND rl.v2=:uid AND rl.v3=1 AND cp.msid<>0 ';
                $query = $query.'AND cp.gid=:gid ';
                $query = $query.'OR cp.status=1 AND rl.type="gruser" AND rl.v1=cp.gid AND rl.v2=:uid AND rl.v3=0 ';
                $query = $query.'AND cp.uid=:uid AND cp.gid=:gid';
            }
            $query = $query.') as unseencomplaints';
        }
        $query = $query.';DELETE FROM gr_msgs WHERE type = "logs" AND tms < (CONVERT_TZ(NOW(),';
        $query = $query.'@@session.time_zone,"+05:30") - INTERVAL 60 SECOND);';
        $query = $query.'UPDATE gr_logs SET v3=0 WHERE type = "typing" AND tms < (CONVERT_TZ(NOW(),';
        $query = $query.'@@session.time_zone,"+05:30") - INTERVAL 15 SECOND);';

        $query = $query.'UPDATE gr_options SET v2 = (CASE ';
        $query = $query.'WHEN v2="invisible" THEN "invisible"';
        $query = $query.'ELSE "online"';
        $query = $query.' END),tms="'.dt().'" WHERE type="profile" AND v1="status" AND v3="'.$uid.'"';
        $query = $query.' AND tms < (CONVERT_TZ(NOW(),@@session.time_zone,"+05:30") - INTERVAL 300 SECOND);';
        $r = db('Grupo', 'q', $query, $data);
        if (isset($r[0]['unseenmsgs']) && $r[0]['unseenmsgs'] > 0) {
            $gr = array();
            $gr['id'] = $inp[0];
            $gr['from'] = $inp[3];
            $gr['ldt'] = $inp[1];
            $list['msgs'] = new stdClass();
            $list['mdata'] = gr_group('msgs', $gr, 'array');
            if (!empty($list)) {
                if (isset($list['mdata'][0]->nomem) && $list['mdata'][0]->nomem == 1) {
                    $list['msgs']->liveup = 'refresh';
                    $poll = 1;
                } else if (count($list['mdata']) > 2) {
                    $list['msgs']->liveup = 'msgs';
                    $list['msgs']->grlastid = $inp[3];
                    $poll = 1;
                }
            }
        }
        if (!isset($uid) || empty($uid) && $GLOBALS["default"]->viewgroups_nologin != 'enable' || isset($GLOBALS["grusrlog"]['role']) && $GLOBALS["grusrlog"]['role'] == 4) {
            $list['msgs']->liveup = 'refresh';
            $poll = 1;
        }
        if (isset($r[0]['lastseenmsg']) && !empty($r[0]['lastseenmsg']) && $r[0]['lastseenmsg'] != $inp[8]) {
            $list['lastseenmsg'] = new stdClass();
            $list['lastseenmsg']->liveup = 'lastseen';
            $list['lastseenmsg']->lastseen = $r[0]['lastseenmsg'];
            $list['lastseenmsg']->gid = $inp[0];
            $poll = 1;
        }
        if (isset($r[0]['typing']) && !empty($r[0]['typing']) && $r[0]['typid'] != $inp[9] || empty($r[0]['typing']) && $inp[9] != 0 && empty($r[0]['typid'])) {
            $list['typing'] = new stdClass();
            $list['typing']->liveup = 'typing';
            $list['typing']->typers = $r[0]['typing'];
            $list['typing']->typid = $r[0]['typid'];
            $list['typing']->gid = $inp[0];
            $poll = 1;
        }

        if (isset($r[0]['unseengroup']) && !empty($r[0]['unseengroup']) && $r[0]['totunseengroup'] != $inp[5]) {
            $list['unseengroup'] = new stdClass();
            $list['unseengroup']->liveup = 'unseengroup';
            $list['unseengroup']->total = $r[0]['totunseengroup'];
            $list['unseengroup']->unseen = $r[0]['unseengroup'];
            $poll = 1;
        }

        if (isset($r[0]['unseenpm']) && !empty($r[0]['unseenpm']) && $r[0]['totunseenpm'] != $inp[4]) {
            $list['unseenpm'] = new stdClass();
            $list['unseenpm']->liveup = 'unseenpm';
            $list['unseenpm']->total = $r[0]['totunseenpm'];
            $list['unseenpm']->unseen = $r[0]['unseenpm'];
            $poll = 1;
        }

        if (isset($r[0]['unseenalerts']) && !empty($r[0]['unseenalerts']) && $r[0]['unseenalerts'] != $inp[6]) {
            $list['unseenalerts'] = new stdClass();
            $list['unseenalerts']->liveup = 'unseenalerts';
            $list['unseenalerts']->total = $r[0]['unseenalerts'];
            $poll = 1;
        }

        if (isset($r[0]['grads']) && !empty($r[0]['grads'])) {
            $list['grads'] = new stdClass();
            $adcampaign = explode($ad_divider, $r[0]['grads']);
            $list['grads']->liveup = 'ads';
            $list['grads']->name = $adcampaign[0];
            $list['grads']->content = $adcampaign[1];
            $list['grads']->height = $adcampaign[2];
            $list['grads']->img = $GLOBALS["default"]->weburl.'gem/ore/grupo/global/icon192.png';
            $poll = 1;
        }

        if (isset($r[0]['unseencomplaints']) && !empty($r[0]['unseencomplaints']) && $r[0]['unseencomplaints'] != $inp[7]) {
            $list['unseencomplaints'] = new stdClass();
            $list['unseencomplaints']->liveup = 'unseencomplaints';
            $list['unseencomplaints']->total = $r[0]['unseencomplaints'];
            $poll = 1;
        }
        if (!empty($poll) || $timeout) {
            break;
        }
        sleep(2);
    }
    header('Content-type: application/json');
    gr_prnt(json_encode($list));
}
?>