<?php if(!defined('s7V9pz')) {die();}?><?php
fc('grupo');
$exp = pg('export');
$exp = explode('/', $exp);
$gid = $tusid = $exp[0];
$ldt = $exp[1];
$uid = $GLOBALS["user"]['id'];
if ($ldt == 'language') {
    if (isset($GLOBALS["roles"]['languages'][4])) {
        $fname = 'grupo/cache/phrases/lang-'.$gid.'.cch';
        flr('download', $fname, 'language.json');
    }
    exit;
}
$cu = gr_group('user', $gid, $uid, $ldt);
if (!$cu[0] || $cu['role'] == '3') {
    rt('404');
}
if ($ldt == 'user') {
    if (!gr_role('access', 'privatemsg', '3', $uid)) {
        rt('404');
        exit;
    }
    if (isset($GLOBALS["roles"]['users'][11]) && strpos($gid, '-') !== false) {
        $gid = $gid;
    } else {
        $tmpido = $gid.'-'.$uid;
        if ($gid > $uid) {
            $tmpido = $uid.'-'.$gid;
        }
        $gid = $tmpido;
    }
} else {
    if (!gr_role('access', 'groups', '8', $uid) & !gr_role('access', 'groups', '7', $uid)) {
        rt('404');
        exit;
    }
}
if ($GLOBALS["default"]->sysmessages == 'disable') {
    $r = db('Grupo', 's', 'msgs', 'gid,type<>', $gid, 'system');
} else {
    $r = db('Grupo', 's', 'msgs', 'gid', $gid);
}
if ($ldt == 'user') {
    $n = $GLOBALS["lang"]->conversation_with.' '.gr_profile('get', $tusid, 'name');
} else {
    $n = db('Grupo', 's', 'options', 'type,id', 'group', $gid)[0]['v1'];
}
?>
<?php
$lndate = htmlspecialchars_decode($GLOBALS["lang"]->datetime);
$lnsender = htmlspecialchars_decode($GLOBALS["lang"]->sender);
$lnmsg = htmlspecialchars_decode($GLOBALS["lang"]->message);
$cont = '';
$cont .= '<html lang="en">';
$cont .= '<head>';
$cont .= '<meta charset="utf-8">';
$cont .= '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
$cont .= '<meta name="description" content="'.$GLOBALS["default"]->sitedesc.'">';
$cont .= '<meta name="author" content="Silwr">';
$cont .= '<meta name="generator" content="Grupo">';
$cont .= '<title>'.$GLOBALS["default"]->sitename.'</title>';
$cont .= '<link href="https://fonts.googleapis.com/css?family=Montserrat:500,600,700,700i,800" rel="stylesheet">';
$cont .= '<link href="'.$GLOBALS["default"]->weburl.'gem/tone/gr-backup.css" rel="stylesheet">';
$cont .= '<link href="'.$GLOBALS["default"]->weburl.'gem/tone/custom.css" rel="stylesheet">';
$cont .= '</head>';
$cont .= '<body>';
$cont .= '<div class="limiter">';
$cont .= '<div class="container-table100">';
$cont .= '<div class="wrap-table100">';
$cont .= '<div class="table100">';
$cont .= '<table>';
$cont .= '<thead>';
$cont .= '<tr class="table100-head">';
$cont .= '<th class="column1">'.$lndate;
$cont .= '</th>';
$cont .= '<th class="column2">'.$lnsender;
$cont .= '</th>';
$cont .= '<th class="column3">'.$lnmsg;
$cont .= '</th>';
$cont .= '</tr>';
$cont .= '</thead>';
$cont .= '<tbody>';
foreach ($r as $v) {
    if ($v['type'] === 'system') {
        $varky = $v['msg'];
        $v['msg'] = $GLOBALS["lang"]->$varky;
    } else if ($v['type'] === 'file') {
        $v['msg'] = $GLOBALS["lang"]->shared_file;
    } else if ($v['type'] === 'audio') {
        $v['msg'] = 'Audio Message';
    } else {
        preg_match_all('/(^|\s)@(?P<mention>\w+)/', $v['msg'], $mentions);
        if (count($mentions[2]) > 0) {
            $mentions = implode('", "', $mentions[2]);
            $qmen = 'SELECT v2,v3 FROM gr_options WHERE type="profile" AND v1="name" AND v3 IN ("'.$mentions.'") ORDER BY v3 DESC';
            $rqm = db('Grupo', 'q', $qmen);
            foreach ($rqm as $ment) {
                $v['msg'] = str_replace('@'.$ment['v3'], '<b>'.$ment['v2'].'</b> ', $v['msg']);
            }
        }
    }
    if ($v['uid'] == $uid) {
        $name = $GLOBALS["lang"]->you;
    } else {
        if (isset($GLOBALS["roles"]['users'][10])) {
            $name = gr_profile('get', $v['uid'], 'name');
        } else {
            $name = usr('Grupo', 'select', $v['uid'])['name'];
        }
    }
    $tms = new DateTime($v['tms']);
    $tmz = new DateTimeZone(gr_profile('get', $uid, 'tmz'));
    $tms->setTimezone($tmz);
    $tmst = strtotime($tms->format('Y-m-d H:i:s'));
    $cont .= '<tr><td class="column1" data-title="'.$lndate.'">';
    $cont .= $tms->format('d-M-y').' '.$tms->format('h:i A').'</td>';
    $cont .= '<td class="column2" data-title="'.$GLOBALS["lang"]->sender.'">'.$name.'</td>';
    $cont .= '<td class="column3" data-title='.$GLOBALS["lang"]->message.'">'.htmlspecialchars_decode($v['msg']);
    $cont .= '</td></tr>';
}
$cont .= '</tbody></table><div class="emojionez dumb"><textarea></textarea><div class="gdefaults"><span class="asciismileys">'.$GLOBALS["default"]->ascii_smileys.'</span></div></div></div></div></div></div>';
$cont .= '<link href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.css" rel="stylesheet" type="text/css">';
$cont .= '<script src="'.$GLOBALS["default"]->weburl.'riches/kit/jquery/jquery-3.4.1.min.js"> </script>';
$cont .= '<script src="'.$GLOBALS["default"]->weburl.'riches/kit/jquery/jquery-migrate-1.4.1.min.js"> </script>';
$cont .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.js"> </script>';
$cont .= '<script src="'.$GLOBALS["default"]->weburl.'riches/kit/emojionearea/dist/asciiemoji.js"> </script>';
$cont .= '<script src="'.$GLOBALS["default"]->weburl.'riches/kit/emojionearea/dist/exportemjois.js"> </script>';
$cont .= '</body></html>';
$fname = 'grupo/files/dumb/transcript-'.$gid.'.html';
$bkdir = 'grupo/files/dumb/';
flr('new', $bkdir);
file_put_contents('gem/ore/'.$fname, $cont);
flr('download', $fname);
?>