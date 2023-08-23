<?php if(!defined('s7V9pz')) {die();}?><?php
function gr_files($do) {
    $uid = $GLOBALS["user"]['id'];
    if ($do['type'] === 'upload') {
        if (!gr_role('access', 'files', '1')) {
            gr_prnt('say("'.$GLOBALS["lang"]->denied.'","e")'); exit;
        }
        $totalFileSize = array_sum($_FILES['ufiles']['size']);
        $totalFileSize = number_format($totalFileSize / 1048576, 2);
        if ($totalFileSize > $GLOBALS["roles"]["xtras"]["maxfileuploadsize"]) {
            gr_prnt('say("'.$GLOBALS["lang"]->denied.'","e")'); exit;
        }
        $dir = 'grupo/files/'.$uid.'/';
        flr('new', $dir);
        if (flr('upload', 'ufiles', $dir, rn(6).rn(3).'-gr-')) {
            gr_prnt('$(".swr-grupo .lside > .tabs > ul > li[act=files]").trigger("click");say("'.$GLOBALS["lang"]->files_uploaded.'","s");');
        } else {
            gr_prnt('say("'.$GLOBALS["lang"]->error_uploading.'");');
        }
    } else if ($do['type'] === 'expired') {
        $expr = vc($GLOBALS["default"]->fileexpiry, 'num');
        if (!empty($expr)) {
            $dumb = glob('gem/ore/grupo/files/dumb/*');
            foreach ($dumb as $dm) {
                if (strtotime('now') > strtotime('+'.$expr.' minutes', filemtime($dm))) {
                    unlink($dm);
                }
            }
            $dumb = glob('gem/ore/grupo/files/preview/*');
            foreach ($dumb as $dm) {
                if (strtotime('now') > strtotime('+'.$expr.' minutes', filemtime($dm))) {
                    unlink($dm);
                }
            }
        }
    } else if ($do['type'] === 'download') {
        if (!gr_role('access', 'files', '2')) {
            gr_prnt('say("'.$GLOBALS["lang"]->denied.'","e")'); exit;
        }
        $cu = gr_group('user', $do["gid"], $uid, $do["ldt"])[0];
        if ($cu) {
            if ($do["ldt"] == 'user') {
                $tmpido = $do["gid"].'-'.$uid;
                if ($do["gid"] > $uid) {
                    $tmpido = $uid.'-'.$do["gid"];
                }
                $do["gid"] = $tmpido;
            }
            $ck = db('Grupo', 's,count(*)', 'msgs', 'gid,msg', $do["gid"], $do["id"])[0][0];
            if ($ck != 0) {
                $zn = "gem/ore/grupo/files/dumb/".$do["id"];
                if (file_exists($zn)) {
                    gr_prnt('window.open("'.$GLOBALS["default"]->weburl.'download/'.$do["id"].'/","_blank");$(".grupo-pop").fadeOut();');
                } else {
                    gr_prnt('say("'.$GLOBALS["lang"]->file_expired.'");$(".grupo-pop").fadeOut();');
                }
            }
        }
    } else if ($do['type'] === 'zip') {
        if (!empty($do['id'])) {
            if (gr_role('access', 'files', '2') || isset($do['r']) && $do['r'] = 1) {
                if (isset($GLOBALS["roles"]['features'][13]) && isset($do["userid"]) && !empty(vc($do["userid"], 'num'))) {
                    $uid = $do["userid"];
                }
                $mfile = "gem/ore/grupo/files/".$uid.'/'.$do['id'];
                if (file_exists($mfile)) {
                    $file = "grupo/files/".$uid.'/'.$do['id'];
                    $fid = $uid.rn(8).'.'.(new SplFileInfo($do['id']))->getExtension();
                    $zn = "grupo/files/dumb/".$fid;
                    flr('new', "grupo/files/dumb/");
                    flr('new', "grupo/files/preview/");
                    flr('delete', $zn);
                    if (flr('copy', $file, $zn)) {
                        if (isset($do['r'])) {
                            $ext = mime_content_type($mfile);
                            $pw = "grupo/files/preview/".$fid;
                            flr('delete', $pw);
                            if ($ext === 'image/jpeg' || $ext === 'image/png' || $ext === 'image/gif' || $ext === 'image/bmp' || $ext === 'image/x-ms-bmp') {
                                $resz = flr('resize', $zn, $pw, '180', '120', 1);
                                if ($resz == false) {
                                    flr('copy', $file, $pw);
                                }
                            }
                            return $fid;
                        } else {
                            gr_prnt('window.open("'.$GLOBALS["default"]->weburl.'download/'.$fid.'/","_blank");');
                        }
                    }
                }
            } else {
                gr_prnt('say("'.$GLOBALS["lang"]->denied.'","e")'); exit;
            }
        }
    } else if ($do['type'] === 'pastescreen' && gr_role('access', 'features', '6')) {
        $fid = 'grshot-'.rn(6).rn(3).'-gr-screenshot-'.dt(0, 'dmyhis').'.png';
        $dir = 'grupo/files/'.$uid.'/';
        flr('new', $dir);
        $imageName = $mfile = "gem/ore/grupo/files/".$uid.'/'.$fid;
        $data = $do['shot'];
        $data = str_replace('data:image/png;base64,', '', $data);
        $data = str_replace(' ', '+', $data);
        $data = base64_decode($data);
        $success = file_put_contents($imageName, $data);
        $do['id'] = $fid;
        $do['type'] = 'share';
        gr_files($do);
    } else if ($do['type'] === 'share') {
        if (!gr_role('access', 'files', '4')) {
            gr_prnt('say("'.$GLOBALS["lang"]->denied.'","e")'); exit;
        }
        if (!isset($do["ldt"]) || empty($do["ldt"])) {
            $do["ldt"] = 'group';
        }
        $cr = gr_group('valid', $do["gid"], $do["ldt"]);
        $cu = gr_group('user', $do["gid"], $uid, $do["ldt"]);
        if ($cr[0] && $cu[0]) {
            if ($do["ldt"] == 'group') {
                if ($cu['role'] != 1 && $cu['role'] != 2 && !gr_role('access', 'groups', '7') && $cr['messaging'] == 'adminonly') {
                    exit;
                }
            }
            $do['type'] = 'zip';
            $do['r'] = 1;
            $dt['msg'] = gr_files($do);
            $dt['xtra'] = explode('-gr-', $do['id'], 2)[1];
            $dt['id'] = $do['gid'];
            $dt['ldt'] = $do['ldt'];
            gr_group('sendmsg', $dt, 2, 'mid');
        } else {
            gr_prnt("say('".$GLOBALS["lang"]->select_group."');");
        }
    } else if ($do['type'] === 'delete') {
        if (!gr_role('access', 'files', '3')) {
            gr_prnt('say("'.$GLOBALS["lang"]->denied.'","e")'); exit;
        }
        $file = "gem/ore/grupo/files/".$uid.'/'.$do['id'];
        unlink($file);
        gr_prnt('$(".swr-grupo .lside > .tabs > ul > li[act=files]").trigger("click");say("'.$GLOBALS["lang"]->deleted.'","s");$(".grupo-pop").fadeOut();');
    }
}
?>