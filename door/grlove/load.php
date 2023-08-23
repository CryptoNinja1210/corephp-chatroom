<?php if(!defined('s7V9pz')) {die();}?><?php
function gr_love() {
    $uid = $GLOBALS["user"]['id'];
    $arg = vc(func_get_args());
    if ($arg[0]["type"] == "lovedit") {
        if (gr_role('access', 'groups', '10') || gr_role('access', 'groups', '7')) {
            $list[0] = new stdClass();
            $list[0]->id = $arg[0]["id"];
            $list[0]->do = 'remove';
            $r = db('Grupo', 's', 'msgs', 'id,cat', $arg[0]["id"], 'group');
            if (count($r) > 0) {
                $cu = gr_group('user', $r[0]['gid'], $uid);
                if ($cu[0] && $cu['role'] != 3) {
                    $lv = db('Grupo', 's', 'msgs', 'gid,uid,msg,type', $r[0]['gid'], $uid, $arg[0]["id"], 'like');
                    if (count($lv) > 0) {
                        $list[0]->do = 'unlike';
                        db('Grupo', 'd', 'msgs', 'id,uid,type', $lv[0]["id"], $uid, 'like');
                    } else {
                        $list[0]->do = 'like';
                        db('Grupo', 'i', 'msgs', 'gid,uid,msg,type,xtra', $r[0]['gid'], $uid, $arg[0]["id"], 'like', $r[0]['uid']);
                        gr_alerts('new', 'liked', $r[0]['uid'], $r[0]["gid"], $arg[0]["id"], $uid);
                    }
                    $list[0]->count = db('Grupo', 's,count(id)', 'msgs', 'gid,msg,type', $r[0]['gid'], $arg[0]["id"], 'like')[0][0];
                    $list[0]->count = str_pad($list[0]->count, 2, "0", STR_PAD_LEFT);
                }
            }
            $r = json_encode($list);
            gr_prnt($r);
        }
    }
}
?>