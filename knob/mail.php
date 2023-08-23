<?php if(!defined('s7V9pz')) {die();}?><?php
fc('grupo');
$m = explode('/', pg('mail'));
if (isset($m[2])) {
    if ($m[0] === 'do') {
        $m[0] = $m[1];
        $m[1] = $m[2];
        $m[2] = 'do';
    }
    $r = db('Grupo', 's', 'mails', 'id,code', $m[0], $m[1]);
    if (isset($r[0]['id'])) {
        if ($m[2] === 'do') {
            $mtime = new DateTime($r[0]['tms']);
            $ntime = new DateTime(dt());
            $interval = $mtime->diff($ntime);
            if ($interval->format('%H') < 24) {
                if ($r[0]['type'] === 'reset' || $r[0]['type'] === 'verify' || $r[0]['type'] === 'signup') {
                    usr('Grupo', 'forcelogin', $r[0]['uid']);
                    addcookie('grcreset', 1, 0, "/");
                    if ($r[0]['type'] === 'verify') {
                        if (usr('Grupo', 'select', $r[0]['uid'])['role'] == 1) {
                            usr('Grupo', 'alter', 'role', 3, $r[0]['uid']);
                            $grjoin = $GLOBALS["default"]->autogroupjoin;
                            if (!empty($grjoin)) {
                                $cr = gr_group('valid', $grjoin);
                                if ($cr[0]) {
                                    gr_data('i', 'gruser', $grjoin, $r[0]['uid'], 0);
                                    $dt = array();
                                    $dt['id'] = $grjoin;
                                    $dt['msg'] = 'joined_group';
                                    gr_group('sendmsg', $dt, 1, 1, $r[0]['uid']);
                                }
                            }
                        }
                    }
                    if ($r[0]['type'] === 'reset') {
                        usr('Grupo', 'clear', $r[0]['uid']);
                        usr('Grupo', 'forcelogin', $r[0]['uid']);
                    }
                } else if ($r[0]['type'] === 'invitenonmember') {
                    $cr = gr_group('valid', $r[0]['valz']);
                    if ($cr[0]) {
                        $rd = url().'chat/group/'.$r[0]['valz'].'/join/'.$cr['access'].'/';
                        rt($rd);
                        exit;
                    }
                }
            }
            rt('');

        } else {
            fc("grmail");
            gr_prnt(grpost($m[0], $m[1])[1]);
        }
    } else {
        rt('404');
    }
} else {
    rt('404');
}
?>