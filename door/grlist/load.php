<?php if(!defined('s7V9pz')) {die();}?><?php
function gr_list($do) {
    $sofs = $ofs = 0;
    $lmt = $GLOBALS["default"]->aside_results_perload;
    $i = 1;
    $unq = 'YmFldm94';
    $uid = $GLOBALS["user"]['id'];
    $list = null;
    if (!isset($do["type"])) {
        $do["type"] = null;
    }
    if (isset($do["offset"])) {
        $ofs = vc($do["offset"], 'num');
    }
    if (isset($do["soffset"])) {
        $sofs = vc($do["soffset"], 'num');
    }
    if (!isset($do['search']) || isset($do['search']) && strlen($do['search']) < 2) {
        $do['search'] = null;
    } else {
        $do['search'] = vc($do['search']);
    }
    $query = 'UPDATE gr_options SET v2 = (case ';
    $query = $query.'when v2="invisible" then "invisible" ';
    $query = $query.'when tms < (CONVERT_TZ(NOW(),@@session.time_zone,"+05:30") - INTERVAL 30 MINUTE) then "offline" ';
    $query = $query.'when tms < (CONVERT_TZ(NOW(),@@session.time_zone,"+05:30") - INTERVAL 15 MINUTE) then "idle" ';
    $query = $query.'when tms > (CONVERT_TZ(NOW(),@@session.time_zone,"+05:30") - INTERVAL 15 MINUTE) then "online" ';
    $query = $query.'else "offline"';
    $query = $query.'end), tms = (case ';
    $query = $query.'when tms < (CONVERT_TZ(NOW(),@@session.time_zone,"+05:30") - INTERVAL 30 MINUTE) then "'.dt().'" ';
    $query = $query.'else tms';
    $query = $query.' end) WHERE type="profile" AND v1="status" AND v2="online" OR type="profile" AND v1="status"';
    $query = $query.' AND v2="idle" OR type="profile" AND v1="status" AND v2="" OR type="profile" AND v1="status" AND v2 IS NULL;';

    if ($GLOBALS["default"]->releaseguestuser == 'enable') {
        $query = $query.'UPDATE gr_users us,gr_options op SET us.name=SUBSTRING(MD5(RAND()) FROM 1 FOR 10),';
        $query = $query.'op.tms=(op.tms - INTERVAL 30 MINUTE) WHERE us.role=5 AND ';
        $query = $query.'op.type="profile" AND op.v1="status" AND op.v3=us.id AND op.v2="offline" AND ';
        $query = $query.' op.tms BETWEEN (DATE_SUB(CONVERT_TZ(NOW(),@@session.time_zone,"+05:30"), ';
        $query = $query.'INTERVAL 5 MINUTE)) AND CONVERT_TZ(NOW(),@@session.time_zone,"+05:30");';
    }

    $r = db('Grupo', 'q', $query);
    $list[0] = new stdClass();
    $list[0]->offset = $ofs+$lmt;
    $list[0]->soffset = $sofs;
    $list[0]->shw = 'hde';
    $unq = base64_decode($unq);
    $list[0]->icn = 'gi-plus';
    $list[0]->mnu = 0;
    $list[0]->act = 0;
    if ($do["type"] === "pm") {
        if (isset($GLOBALS["roles"]['privatemsg'][2])) {
            if (isset($GLOBALS["roles"]['users'][4])) {
                $list[0]->shw = 'shw';
                $list[0]->icn = 'gi-users';
                $list[0]->mnu = 'mmenu';
                $list[0]->act = 'users';
            } else if (isset($GLOBALS["roles"]['users'][5])) {
                $list[0]->shw = 'shw';
                $list[0]->icn = 'gi-users';
                $list[0]->mnu = 'mmenu';
                $list[0]->act = 'online';
            }
            $query = 'SELECT max(ms.id) as mesid,ms.gid,';
            $query = $query.'TRIM(LEADING :tra FROM (TRIM(TRAILING :trb FROM ms.gid))) AS cuser,';
            if (isset($GLOBALS["roles"]['users'][10])) {
                $query = $query.'(SELECT v2 FROM gr_options WHERE v3=cuser AND type="profile" AND v1="name") AS name,';
            } else {
                $query = $query.'(SELECT name FROM gr_users WHERE id=cuser LIMIT 1) AS name,';
            }
            $query = $query.'(SELECT v2 FROM gr_options WHERE v3=cuser AND type="profile" AND v1="status") AS status,';
            $query = $query.'(SELECT COUNT(id) FROM gr_msgs WHERE gid=ms.gid AND id>';
            $query = $query.'(SELECT v3 FROM gr_options WHERE type="lview" AND v1=ms.gid AND v2=:uid LIMIT 1)) AS lcount';
            $query = $query.' FROM gr_msgs ms WHERE ms.gid LIKE :sra AND ms.cat="user" ';
            if (!empty($do['search'])) {
                if (isset($GLOBALS["roles"]['users'][10])) {
                    $query = $query.'AND (SELECT v2 FROM gr_options WHERE v3=(TRIM(LEADING :tra FROM (TRIM(TRAILING :trb FROM ms.gid))))';
                    $query = $query.' AND type="profile" AND v1="name") LIKE :srch ';
                } else {
                    $query = $query.'AND (SELECT name FROM gr_users WHERE id=(TRIM(LEADING :tra FROM (TRIM(TRAILING :trb FROM ms.gid))))';
                    $query = $query.' LIMIT 1) LIKE :srch ';
                }
            }
            $query = $query.'AND ms.id > (SELECT IFNULL((SELECT MIN(CAST(v3 AS SIGNED)) FROM gr_options WHERE type="clearchat" AND v1=:uid AND v2=TRIM(LEADING :tra FROM (TRIM(TRAILING :trb FROM ms.gid))) LIMIT 1),0)) ';
            $query = $query.'OR ms.gid LIKE :srb AND ms.cat="user" ';
            if (!empty($do['search'])) {
                if (isset($GLOBALS["roles"]['users'][10])) {
                    $query = $query.'AND (SELECT v2 FROM gr_options WHERE v3=(TRIM(LEADING :tra FROM (TRIM(TRAILING :trb FROM ms.gid))))';
                    $query = $query.' AND type="profile" AND v1="name") LIKE :srch ';
                } else {
                    $query = $query.'AND (SELECT name FROM gr_users WHERE id=(TRIM(LEADING :tra FROM (TRIM(TRAILING :trb FROM ms.gid))))';
                    $query = $query.' LIMIT 1) LIKE :srch ';
                }
            }
            $query = $query.'GROUP BY ms.gid ORDER BY mesid DESC LIMIT '.$lmt.' OFFSET '.$ofs;

            $data = array();
            $data['tra'] = $uid.'-';
            $data['trb'] = '-'.$uid;
            $data['uid'] = $uid;
            $data['sra'] = $uid.'-%';
            $data['srb'] = '%-'.$uid;
            if (!empty($do['search'])) {
                $data['srch'] = '%'.$do['search'].'%';
            }

            $r = db('Grupo', 'q', $query, $data);
            foreach ($r as $v) {
                $status = $v['status'];
                if (empty($do["search"]) || !empty($do["search"]) && strpos($v['name'], $do["search"]) !== false) {}
                $list[$i] = new stdClass();
                $list[$i]->img = gr_img('users', $v['cuser']);
                $list[$i]->name = $v['name'];
                $list[$i]->count = 0;
                if ($v['lcount'] != 0) {
                    $list[$i]->count = $v['lcount'];
                    $list[$i]->countag = $GLOBALS["lang"]->new;
                }
                $list[$i]->sub = $GLOBALS["lang"]->offline;
                if (isset($GLOBALS["lang"]->$status)) {
                    $list[$i]->sub = $GLOBALS["lang"]->$status;
                }
                $list[$i]->rtag = 'type="profile" no="'.$v['cuser'].'"';
                $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
                $list[$i]->oa = $GLOBALS["lang"]->view;
                $list[$i]->oat = 'class="paj"';
                $list[$i]->icon = "'status ".$status."'";
                $list[$i]->id = 'class="loadgroup paj" ldt="user" no="'.$v['cuser'].'"';
                $i = $i+1;
            }
        }
    } else if ($do["type"] === "pmlist") {
        if (isset($GLOBALS["roles"]['users'][11]) && isset($GLOBALS["roles"]['privatemsg'][2])) {
            if (isset($GLOBALS["roles"]['users'][4])) {
                $list[0]->shw = 'shw';
                $list[0]->icn = 'gi-users';
                $list[0]->mnu = 'mmenu';
                $list[0]->act = 'users';
            } else if (isset($GLOBALS["roles"]['users'][5])) {
                $list[0]->shw = 'shw';
                $list[0]->icn = 'gi-users';
                $list[0]->mnu = 'mmenu';
                $list[0]->act = 'online';
            }
            $query = 'SELECT max(ms.id) as mesid,ms.gid,';
            $query = $query.'SUBSTRING_INDEX(ms.gid, "-", 1) AS userone,SUBSTRING_INDEX(ms.gid, "-", -1) AS usertwo,';
            $query = $query.'(SELECT v2 FROM gr_options WHERE v3=userone AND type="profile" AND v1="name") AS nameone,';
            $query = $query.'(SELECT v2 FROM gr_options WHERE v3=usertwo AND type="profile" AND v1="name") AS nametwo';
            $query = $query.' FROM gr_msgs ms WHERE ms.cat="user" AND ms.type <> "logs" ';
            if (!empty($do['search'])) {
                $query = $query.'AND (SELECT v2 FROM gr_options WHERE v3=SUBSTRING_INDEX(ms.gid, "-", 1) AND type="profile" AND v1="name") LIKE :srch ';
                $query = $query.'OR ms.cat="user" AND (SELECT v2 FROM gr_options WHERE v3=SUBSTRING_INDEX(ms.gid, "-", -1) AND type="profile" AND v1="name") LIKE :srch ';
            }
            $query = $query.'GROUP BY ms.gid ORDER BY mesid DESC LIMIT '.$lmt.' OFFSET '.$ofs;
            $data = array();
            if (!empty($do['search'])) {
                $data['srch'] = '%'.$do['search'].'%';
            }
            $r = db('Grupo', 'q', $query, $data);
            foreach ($r as $v) {
                $list[$i] = new stdClass();
                $list[$i]->img = gr_img('users', $v['userone']);
                $list[$i]->name = $v['nameone'].' - '.$v['nametwo'];
                $list[$i]->count = 0;
                $list[$i]->sub = $GLOBALS["lang"]->privatemsg;
                $list[$i]->rtag = 'type="profile" no="'.$v['nameone'].'"';
                $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
                $list[$i]->oa = $GLOBALS["lang"]->view;
                $list[$i]->oat = 'class="paj"';
                $list[$i]->icon = "";
                $list[$i]->id = 'class="loadgroup paj" ldt="user" no="'.$v['userone'].'-'.$v['usertwo'].'"';
                $i = $i+1;
            }
        }
    } else if ($do["type"] === "groups") {
        $GLOBALS["default"]->pingroup = vc($GLOBALS["default"]->pingroup, 'num');
        if (isset($do['filtr']) && !empty($do['filtr']) || !$GLOBALS["logged"]) {
            $GLOBALS["default"]->pingroup = 0;
        }
        if (isset($GLOBALS["roles"]['groups'][1])) {
            $list[0]->shw = 'shw';
            $list[0]->icn = 'gi-plus';
            $list[0]->mnu = 'udolist';
            $list[0]->act = 'group';
        }
        if (isset($do['filtr']) && $do['filtr'] == 'unjoined') {
            $r = array();
        } else if (!empty($GLOBALS["default"]->pingroup) && empty($do['search'])) {
            $query = 'SELECT gr.*,';
            $query = $query.'(SELECT COUNT(id) FROM gr_options WHERE type="gruser" AND v1=gr.id AND v2=:uid) AS grjoin,';
            $query = $query.'(SELECT CONCAT(v3,"|",v4,"|",tms) as grole FROM gr_options WHERE type="gruser" AND v1=gr.id AND v2=:uid LIMIT 1) AS grole,';
            $query = $query.'(SELECT COUNT(v1) FROM gr_options WHERE type="gruser" AND v1=gr.id) AS grtotal,';
            $query = $query.'(SELECT COUNT(id) FROM gr_msgs WHERE gid=gr.id AND type <> "like" AND type <> "logs" ';
            if ($GLOBALS["default"]->sysmessages == 'disable') {
                $query = $query.'AND type<>"system" ';
            }
            $query = $query.'AND id>(SELECT v3 FROM gr_options WHERE type="lview" AND v1=gr.id AND v2=:uid LIMIT 1)) AS lcount';
            $query = $query.' FROM gr_options gr WHERE gr.id=:pingroup AND gr.type="group"';
            $query = $query.' || gr.type="group" AND gr.id IN (SELECT gj.v1 FROM gr_options gj WHERE';
            $query = $query.' gj.type="gruser" AND gj.v2=:uid AND gj.v1<>:pingroup) ';
            $query = $query.'ORDER BY gr.id=:pingroup DESC, gr.tms DESC LIMIT '.$lmt.' OFFSET '.$ofs;
            $data = array();
            $data['uid'] = $uid;
            $data['pingroup'] = $GLOBALS["default"]->pingroup;
            $r = db('Grupo', 'q', $query, $data);
        } else if (!$GLOBALS["logged"]) {
            $query = 'SELECT gr.*,';
            $query = $query.'1 AS grjoin,';
            $query = $query.'(SELECT CONCAT("0","|","1","|","2") as grole) AS grole,';
            $query = $query.'(SELECT COUNT(v1) FROM gr_options WHERE type="gruser" AND v1=gr.id) AS grtotal,';
            $query = $query.'(SELECT COUNT(id) FROM gr_msgs WHERE gid=gr.id AND type <> "like" AND type <> "logs" ';
            if ($GLOBALS["default"]->sysmessages == 'disable') {
                $query = $query.'AND type<>"system" ';
            }
            $query = $query.'AND id>(SELECT v3 FROM gr_options WHERE type="lview" AND v1=gr.id AND v2=:uid LIMIT 1)) AS lcount';
            $query = $query.' FROM gr_options gr WHERE gr.type="group"';
            if (!empty($do['search'])) {
                $query = $query.' AND gr.v1 LIKE :srch';
            }
            $query = $query.' AND gr.v2="0" AND v3<>"secret"';
            $query = $query.' ORDER BY gr.tms DESC LIMIT '.$lmt.' OFFSET '.$ofs;
            $data = array();
            $data['uid'] = $uid;
            if (!empty($do['search'])) {
                $data['srch'] = '%'.$do['search'].'%';
            }
            $r = db('Grupo', 'q', $query, $data);
        } else {
            $query = 'SELECT gr.*,';
            $query = $query.'(SELECT COUNT(id) FROM gr_options WHERE type="gruser" AND v1=gr.id AND v2=:uid) AS grjoin,';
            $query = $query.'(SELECT CONCAT(v3,"|",v4,"|",tms) as grole FROM gr_options WHERE type="gruser" AND v1=gr.id AND v2=:uid LIMIT 1) AS grole,';
            $query = $query.'(SELECT COUNT(v1) FROM gr_options WHERE type="gruser" AND v1=gr.id) AS grtotal,';
            $query = $query.'(SELECT COUNT(id) FROM gr_msgs WHERE gid=gr.id AND type <> "like" AND type <> "logs" ';
            if ($GLOBALS["default"]->sysmessages == 'disable') {
                $query = $query.'AND type<>"system" ';
            }
            $query = $query.'AND id > (SELECT v3 FROM gr_options WHERE type="lview" AND v1=gr.id AND v2=:uid LIMIT 1)) AS lcount';
            $query = $query.' FROM gr_options gr WHERE gr.type="group"';
            if (!empty($do['search'])) {
                $query = $query.' AND gr.v1 LIKE :srch';
            }
            $query = $query.' AND gr.id IN (SELECT gj.v1 FROM gr_options gj WHERE gj.type="gruser" AND gj.v2=:uid) ';
            $query = $query.'ORDER BY gr.tms DESC LIMIT '.$lmt.' OFFSET '.$ofs;
            $data = array();
            $data['uid'] = $uid;
            if (!empty($do['search'])) {
                $data['srch'] = '%'.$do['search'].'%';
            }
            $r = db('Grupo', 'q', $query, $data);
        }
        $lk = $lmt-count($r);
        foreach ($r as $v) {
            $grole = explode('|', $v['grole']);
            $v['grole'] = $grole[0];
            $v['tempban'] = 0;
            $v['temptms'] = 0;
            if (isset($grole[2])) {
                $v['tempban'] = $grole[1];
                $v['temptms'] = $grole[2];
            }
            $list[$i] = new stdClass();
            $list[$i]->img = gr_img('groups', $v['id']);
            $list[$i]->name = $v['v1'];
            $list[$i]->countag = $list[$i]->count = 0;
            $list[$i]->rtag = '';
            $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
            $list[$i]->oa = $GLOBALS["lang"]->view;
            $list[$i]->oat = 'class="paj"';
            $list[$i]->icon = '';
            if ($v['grole'] == 3 && !empty($v['tempban'])) {
                $unbantms = date("M d, Y H:i:s", strtotime('+'.$v['tempban'].' minutes', strtotime($v['temptms'])));
                if (strtotime($unbantms) < strtotime('now')) {
                    $tempban = array();
                    $tempban['id'] = $v['id'];
                    $tempban['usid'] = $uid;
                    gr_group('unblock', $tempban, 1);
                    $v['grole'] = 0;
                }
            }
            if (!empty($v['v2'])) {
                $list[$i]->icon = '"gi-lock" data-toggle="tooltip" data-title="'.$GLOBALS["lang"]->protected_group.'"';
            }
            if ($v['v3'] == 'secret') {
                $list[$i]->icon = '"gi-eye-off" data-toggle="tooltip" data-title="'.$GLOBALS["lang"]->secret_group.'"';
            }



            if ($v['id'] == $GLOBALS["default"]->pingroup) {
                $list[$i]->icon = '"gi-check-1" data-toggle="tooltip" data-title="'.$GLOBALS["lang"]->pinned_group.'"';
            }
            if ($v['grjoin'] == 0) {
                $list[$i]->oa = $GLOBALS["lang"]->join;
                $list[$i]->id = 'class="formpop" title="'.$GLOBALS["lang"]->join_group.'" do="group" ldt="group" btn="'.$GLOBALS["lang"]->join.'" act="join" no="'.$v['id'].'"';
                if (!empty($v['v2'])) {
                    $list[$i]->sub = $GLOBALS["lang"]->protected_group;
                }
                if ($v['v3'] == 'secret') {
                    $list[$i]->sub = $GLOBALS["lang"]->secret_group;
                }
                if ($v['v3'] != 'secret' && empty($v['v2'])) {
                    $list[$i]->sub = gr_shnum($v['grtotal'])." ".$GLOBALS["lang"]->members;
                }
            } else if ($v['grole'] == 3 && !isset($GLOBALS["roles"]['groups'][7])) {
                $list[$i]->id = 'class="say" say="'.$GLOBALS["lang"]->banned.'" type="e" no="'.$v['id'].'" ldt="group"';
                $list[$i]->sub = $GLOBALS["lang"]->banned;
                if (!empty($v['tempban'])) {
                    $GLOBALS["lang"]->temp_banned_for = $GLOBALS["lang"]->temp_banned_for.' '.$v['tempban'].' '.$GLOBALS["lang"]->minutes;
                    $list[$i]->id = 'class="say" say="'.$GLOBALS["lang"]->temp_banned_for.'" type="e" no="'.$v['id'].'" ldt="group"';
                    $list[$i]->sub = $GLOBALS["lang"]->temp_banned;
                }
            } else {
                if ($v['lcount'] != 0) {
                    $list[$i]->count = $v['lcount'];
                    $list[$i]->countag = $GLOBALS["lang"]->new;
                }
                $list[$i]->sub = gr_shnum($v['grtotal'])." ".$GLOBALS["lang"]->members;
                $list[$i]->id = 'class="loadgroup paj" ldt="group" no="'.$v['id'].'"';
            }

            //paid channel edit
            if ($v['v3'] == 'paid') {
                $list[$i]->icon = '"gi-heart-empty" data-toggle="tooltip" data-title="Premium Channel"';
                $list[$i]->sub = 'Premium group';
            }
            //end 

            $i = $i+1;
        }
        if ($lk != 0) {
            $list[0]->soffset = $sofs+$lk;
            $rs = array();
            if (isset($do['filtr']) && $do['filtr'] == 'joined') {
                $rs = array();
            } else if (isset($GLOBALS["roles"]['groups'][6]) && isset($GLOBALS["roles"]['groups'][11])) {
                if (!empty($GLOBALS["default"]->pingroup) && empty($do['search'])) {
                    $query = 'SELECT gr.*,';
                    $query = $query.'(SELECT COUNT(id) FROM gr_options WHERE type="gruser" AND v1=gr.id) AS grtotal';
                    $query = $query.' FROM gr_options gr WHERE gr.id<>:pingroup AND gr.type="group"';
                    $query = $query.' AND gr.id NOT IN (SELECT gj.v1 FROM gr_options gj WHERE';
                    $query = $query.' gj.type="gruser" AND gj.v2=:uid AND gj.v1<>:pingroup) ';
                    $query = $query.'ORDER BY gr.tms DESC LIMIT '.$lk.' OFFSET '.$sofs;
                    $data = array();
                    $data['uid'] = $uid;
                    $data['pingroup'] = $GLOBALS["default"]->pingroup;
                    $rs = db('Grupo', 'q', $query, $data);
                } else {
                    $query = 'SELECT gr.*,';
                    $query = $query.'(SELECT COUNT(id) FROM gr_options WHERE type="gruser" AND v1=gr.id) AS grtotal';
                    $query = $query.' FROM gr_options gr WHERE gr.type="group"';
                    if (!empty($do['search'])) {
                        $query = $query.' AND gr.v1 LIKE :srch';
                    }
                    $query = $query.' AND gr.id NOT IN (SELECT gj.v1 FROM gr_options gj WHERE gj.type="gruser" AND gj.v2=:uid) ';
                    $query = $query.'ORDER BY gr.tms DESC LIMIT '.$lk.' OFFSET '.$sofs;
                    $data = array();
                    $data['uid'] = $uid;
                    if (!empty($do['search'])) {
                        $data['srch'] = '%'.$do['search'].'%';
                    }
                    $rs = db('Grupo', 'q', $query, $data);
                }
            } else if (isset($GLOBALS["roles"]['groups'][11])) {
                if (!empty($GLOBALS["default"]->pingroup) && empty($do['search'])) {
                    $query = 'SELECT gr.*,';
                    $query = $query.'(SELECT COUNT(id) FROM gr_options WHERE type="gruser" AND v1=gr.id) AS grtotal';
                    $query = $query.' FROM gr_options gr WHERE gr.id<>:pingroup AND gr.type="group"';
                    $query = $query.' AND gr.v3="secret" AND gr.id NOT IN (SELECT gj.v1 FROM gr_options gj WHERE';
                    $query = $query.' gj.type="gruser" AND gj.v2=:uid AND gj.v1<>:pingroup) ';
                    $query = $query.'ORDER BY gr.tms DESC LIMIT '.$lk.' OFFSET '.$sofs;
                    $data = array();
                    $data['uid'] = $uid;
                    $data['pingroup'] = $GLOBALS["default"]->pingroup;
                    $rs = db('Grupo', 'q', $query, $data);
                } else {
                    $query = 'SELECT gr.*,';
                    $query = $query.'(SELECT COUNT(id) FROM gr_options WHERE type="gruser" AND v1=gr.id) AS grtotal';
                    $query = $query.' FROM gr_options gr WHERE gr.type="group"';
                    if (!empty($do['search'])) {
                        $query = $query.' AND gr.v1 LIKE :srch';
                    }
                    $query = $query.' AND gr.v3="secret" AND gr.id NOT IN (SELECT gj.v1 FROM gr_options gj WHERE gj.type="gruser" AND gj.v2=:uid) ';
                    $query = $query.'ORDER BY gr.tms DESC LIMIT '.$lk.' OFFSET '.$sofs;
                    $data = array();
                    $data['uid'] = $uid;
                    if (!empty($do['search'])) {
                        $data['srch'] = '%'.$do['search'].'%';
                    }
                    $rs = db('Grupo', 'q', $query, $data);
                }
            } else if (isset($GLOBALS["roles"]['groups'][6])) {
                if (!empty($GLOBALS["default"]->pingroup) && empty($do['search'])) {
                    $query = 'SELECT gr.*,';
                    $query = $query.'(SELECT COUNT(id) FROM gr_options WHERE type="gruser" AND v1=gr.id) AS grtotal';
                    $query = $query.' FROM gr_options gr WHERE gr.id<>:pingroup AND gr.type="group"';
                    $query = $query.' AND gr.v3!="secret" AND gr.id NOT IN (SELECT gj.v1 FROM gr_options gj WHERE';
                    $query = $query.' gj.type="gruser" AND gj.v2=:uid AND gj.v1<>:pingroup) ';
                    $query = $query.'ORDER BY gr.tms DESC LIMIT '.$lk.' OFFSET '.$sofs;
                    $data = array();
                    $data['uid'] = $uid;
                    $data['pingroup'] = $GLOBALS["default"]->pingroup;
                    $rs = db('Grupo', 'q', $query, $data);
                } else {
                    $query = 'SELECT gr.*,';
                    $query = $query.'(SELECT COUNT(id) FROM gr_options WHERE type="gruser" AND v1=gr.id) AS grtotal';
                    $query = $query.' FROM gr_options gr WHERE gr.type="group"';
                    if (!empty($do['search'])) {
                        $query = $query.' AND gr.v1 LIKE :srch';
                    }
                    $query = $query.' AND gr.v3!="secret" AND gr.id NOT IN (SELECT gj.v1 FROM gr_options gj WHERE gj.type="gruser" AND gj.v2=:uid) ';
                    $query = $query.'ORDER BY gr.tms DESC LIMIT '.$lk.' OFFSET '.$sofs;
                    $data = array();
                    $data['uid'] = $uid;
                    if (!empty($do['search'])) {
                        $data['srch'] = '%'.$do['search'].'%';
                    }
                    $rs = db('Grupo', 'q', $query, $data);
                }

            }
            foreach ($rs as $v) {
                $chusers[0] = $v['id'];
                $list[$i] = new stdClass();
                $list[$i]->img = gr_img('groups', $v['id']);
                $list[$i]->name = $v['v1'];
                $list[$i]->countag = $list[$i]->count = 0;

                $list[$i]->rtag = 'type="profile" no="'.$chusers[0].'"';
                $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
                $list[$i]->oat = 'class="paj"';
                if (!isset($GLOBALS["roles"]['groups'][4]) && !isset($GLOBALS["roles"]['groups'][7])) {
                    $list[$i]->oa = $GLOBALS["lang"]->join;
                    $list[$i]->id = 'class="say" say="'.$GLOBALS["lang"]->denied.'" type="e" no="'.$v['id'].'" ldt="group"';
                } else {
                    $list[$i]->oa = $GLOBALS["lang"]->join;
                    if ($GLOBALS["default"]->join_confirm == 'enable' || !empty($v['v2'])) {
                        $list[$i]->id = 'class="formpop" title="'.$GLOBALS["lang"]->join_group.'" do="group" ldt="group" btn="'.$GLOBALS["lang"]->join.'" act="join" no="'.$v['id'].'"';
                    } else {
                        $list[$i]->rtag = '';
                        $list[$i]->oat = 'class="paj"';
                        $list[$i]->id = 'class="grrun" do="group" act="join" no="'.$chusers[0].'"';
                    }
                }
                $list[$i]->icon = '';
                if (!empty($v['v2'])) {
                    $list[$i]->icon = '"gi-lock" data-toggle="tooltip" data-title="'.$GLOBALS["lang"]->protected_group.'"';
                    $list[$i]->sub = $GLOBALS["lang"]->protected_group;
                }
                if ($v['v3'] == 'secret') {
                    $list[$i]->icon = '"gi-eye-off" data-toggle="tooltip" data-title="'.$GLOBALS["lang"]->secret_group.'"';
                    $list[$i]->sub = $GLOBALS["lang"]->secret_group;
                }
                if ($v['v3'] != 'secret' && empty($v['v2'])) {
                    $list[$i]->sub = gr_shnum($v['grtotal'])." ".$GLOBALS["lang"]->members;
                }
                if ($v['v6'] == 'unleavable') {
                    $list[$i]->icon = '"gi-minus-circled-1" data-toggle="tooltip" data-title="'.$GLOBALS["lang"]->unleavable_group.'"';
                }

                //paid channel edit
                if ($v['v3'] == 'paid') {
                    $list[$i]->icon = '"gi-heart-empty" data-toggle="tooltip" data-title="Premium Channel"';
                    $list[$i]->sub = 'Premium group';
                   
                    $list[$i]->oa = $GLOBALS["lang"]->join;
                    $list[$i]->id = 'class="formpop" title="'.$GLOBALS["lang"]->join_group.'" do="group" ldt="group" btn="'.$GLOBALS["lang"]->join.'" act="join" no="'.$v['id'].'"';
               
                } 
                //end
                $i = $i+1;
            }
        }
    } else if ($do["type"] === "crew") {
            if (isset($GLOBALS["roles"]['groups'][16])) {
                $do["gid"] = vc($do["gid"], 'num');if (!isset($do['filtr'])) {
                $do['filtr'] = 'browsing';
            }
            $query = 'SELECT us.id,un.subs,us.v1,us.v3,us.v2,us.v4,';
            if (isset($GLOBALS["roles"]['users'][10])) {
                $query = $query.'op.v2 AS name,';
            } else {
                $query = $query.'un.name AS name,';
            }
            $query = $query.'(SELECT COUNT(id) FROM gr_options WHERE type="gruser" AND v1=us.v1 AND v2=:uid) AS grjoin,';
            $query = $query.'(SELECT v3 FROM gr_options WHERE type="gruser" AND v1=us.v1 AND v2=:uid LIMIT 1) AS grole,';
            $query = $query.'(SELECT st.v2 FROM gr_options st WHERE st.v3=us.v2 AND st.type="profile" AND st.v1="status") AS status,';
            $query = $query."(SELECT pf.v2 FROM gr_profiles pf WHERE pf.uid = us.v2 AND pf.type = 'profile' AND pf.name = '6') AS flag";
            $query = $query.' FROM gr_users un INNER JOIN gr_options us ON un.id=us.v2,gr_options op';
            $query = $query.' WHERE us.v2=op.v3 AND op.type="profile" AND op.v1="name"';
            if (isset($GLOBALS["roles"]['groups'][18])) {
                if (isset($do['filtr']) && $do['filtr'] == 'browsing') {
                    $query = $query.' AND (SELECT v3 FROM gr_logs WHERE type="browsing" AND v1=us.v2 LIMIT 1)=:gid';
                    $query = $query.' AND (SELECT st.v2 FROM gr_options st WHERE st.v3=us.v2 AND st.type="profile" AND st.v1="status")="online"';
                }
            }
            $query = $query.' AND us.type="gruser"';
            if (!empty($do['search'])) {
                if (isset($GLOBALS["roles"]['users'][10])) {
                    $query = $query.' AND op.v2 LIKE :search';
                } else {
                    $query = $query.' AND un.name LIKE :search';
                }
            }
            $query = $query.' AND us.v1=:gid ORDER BY';
            if (isset($do['filtr']) && $do['filtr'] == 'recent') {
                $query = $query.' us.tms DESC,name ASC';
            } else {
                $query = $query.' us.v3 != 3 DESC,us.v3 DESC,name ASC';
            }
            $query = $query.' LIMIT '.$lmt.' OFFSET '.$ofs;

            $data = array();
            $data['uid'] = $uid;
            $data['gid'] = $do["gid"];

            if (!empty($do['search'])) {
                $data['search'] = "%".$do["search"]."%";
            }
            $rz = db('Grupo', 'q', $query, $data);
            foreach ($rz as $f) {
                if ($f['grjoin'] != 0 && $f['grole'] != 3 || isset($GLOBALS["roles"]['groups'][7])) {
                    $list[$i] = new stdClass();
                    $list[$i]->img = gr_img('users', $f['v2']);
                    $list[$i]->name = $f['name'];
                    $list[$i]->count = 0;
                    $list[$i]->sub = $GLOBALS["lang"]->member;
                    $sort = 1;
                    if ($f['v3'] == 2) {
                        $list[$i]->sub = $GLOBALS["lang"]->admin;
                        $sort = 3;
                    } else if ($f['v3'] == 1) {
                        $list[$i]->sub = $GLOBALS["lang"]->moderator;
                        $sort = 2;
                    } else if($f['v3'] == 10) {
                        $list[$i]->sub = $GLOBALS["lang"]->host;
                        $sort = 10;
                    } else if($f['v3'] == 11) {
                        $list[$i]->sub = $GLOBALS["lang"]->active;
                        $sort = 10;
                    } else if($f['v3'] == 12) {
                        $list[$i]->sub = $GLOBALS["lang"]->super;
                        $sort = 10;
                    } else if ($f['v3'] == 3) {
                        if (!empty($f['v4'])) {
                            $list[$i]->sub = $GLOBALS["lang"]->temp_banned;
                        } else {
                            $list[$i]->sub = $GLOBALS["lang"]->banned;
                        }
                        $sort = 0;
                    }

                    $list[$i]->rtag = 'type="group" no="'.$f['v1'].'"';
                    $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
                    if (isset($GLOBALS["roles"]['groups'][7]) || $f['grole'] == 2) {
                        $list[$i]->oa = $GLOBALS["lang"]->role;
                        $list[$i]->oat = 'class="formpop" title="'.$GLOBALS["lang"]->edit_grouprole.'" data-pname="'.$list[$i]->name.'" pn=1 do="group" btn="'.$GLOBALS["lang"]->update.'" act="role" data-usr="'.$f['v2'].'"';
                    }
                    if ($f['v2'] != $uid) {
                        if (isset($GLOBALS["roles"]['groups'][7]) || $f['grole'] == 2 || $f['grole'] == 1 && $f['v3'] != 2) {
                            $list[$i]->ob = $GLOBALS["lang"]->view;
                            $list[$i]->obt = 'class="vwp" no="'.$f['v2'].'"';
                        }
                        if (isset($GLOBALS["roles"]['groups'][7]) || $f['grole'] == 2 || $f['grole'] == 1 || $f['grole'] == 0) {
                            if (isset($GLOBALS["roles"]['privatemsg'][1])) {
                                $list[$i]->od = $GLOBALS["lang"]->chat;
                                $list[$i]->odt = 'class="loadgroup paj" ldt="user" no="'.$f['v2'].'"';
                            }
                        }
                    }
                    $norc = 0;
                    if ($f['v3'] == 2 && $f['grole'] == 1) {
                        $norc = 1;
                    }
                    if ($f['v2'] != $uid && $norc == 0) {
                        if (isset($GLOBALS["roles"]['groups'][7]) || $f['grole'] == 2 || $f['grole'] == 1) {
                            if ($f['v3'] == 3) {
                                $list[$i]->oc = $GLOBALS["lang"]->unban;
                                $list[$i]->oct = 'class="deval" act="unblock" data-usid="'.$f['v2'].'"';
                            } else {
                                $list[$i]->oc = $GLOBALS["lang"]->ban;
                                $list[$i]->oct = 'class="formpop" title="'.$GLOBALS["lang"]->ban_user.'" data-pname="'.$list[$i]->name.'" pn=1 do="group" btn="'.$GLOBALS["lang"]->take_action.'" act="block" data-usr="'.$f['v2'].'"';
                            }
                        } else {
                            $list[$i]->oc = $GLOBALS["lang"]->view;
                            $list[$i]->oct = 'class="vwp" no="'.$f['v2'].'"';
                        }
                    } else {
                        if (!isset($GLOBALS["roles"]['groups'][7])) {
                            $list[$i]->oc = $GLOBALS["lang"]->view;
                            $list[$i]->oct = 'class="vwp" no="'.$f['v2'].'"';
                        }
                    }


                    $list[$i]->og = 'Gift';
                    $list[$i]->subs = $f['subs'];
                    $list[$i]->ogt = 'class="vwgift" no="'.$f['v2'].'"';
                    
                    $list[$i]->icon = "'status ".$f['status']."'";
                    $list[$i]->icon1 = $f['v3'] == '10' ? "'status host-mic'" : ($f['v3'] == '11' ? "'status active-icon'" : ($f['v3'] == '12' ? "'status super-icon'" : "d-none"));
                    $list[$i]->flag = strlen($f['flag']) > 0 ? "'background-image: url(https://api.hostip.info/images/flags/" . strtolower($f['flag']) . ".gif)'" : "";
                    $list[$i]->flag1 = strlen($f['flag']) > 0 ? "'host-mic1'" : "d-none";
                    $list[$i]->id = 'class="crew"';
                    $i = $i+1;
                }
            }
        }
    } else if ($do["type"] === "alerts") {
        if (isset($GLOBALS["roles"]['groups'][1])) {
            $list[0]->shw = 'shw';
            $list[0]->icn = 'gi-trash';
            $list[0]->mnu = 'udolist';
            $list[0]->act = 'clearallalerts';
        }
        $query = 'SELECT al.v1,al.v2,al.v3,al.type,al.id,al.tms,al.seen,';
        if (isset($GLOBALS["roles"]['users'][10])) {
            $query = $query.'op.v2 AS name';
        } else {
            $query = $query.'(SELECT name FROM gr_users WHERE id=al.v3 LIMIT 1) AS name';
        }
        $query = $query.' FROM gr_alerts al,gr_options op WHERE al.v3=op.v3 AND op.type="profile" AND op.v1="name" ';
        if (!empty($do['search'])) {
            if (isset($GLOBALS["roles"]['users'][10])) {
                $query = $query.'AND op.v2 LIKE :search ';
            } else {
                $query = $query.'AND (SELECT name FROM gr_users WHERE id=al.v3 LIMIT 1) LIKE :search ';
            }
        }
        $query = $query.'AND al.uid=:uid ORDER BY al.id DESC,op.v2 LIMIT '.$lmt.' OFFSET '.$ofs.';';
        if ($ofs == 0) {
            $query = $query.'UPDATE gr_alerts SET seen=1 WHERE uid=:uid AND seen=0;';
        }
        $data = array();
        $data['uid'] = $uid;
        if (!empty($do['search'])) {
            $data['search'] = "%".$do["search"]."%";
        }
        $r = db('Grupo', 'q', $query, $data);
        foreach ($r as $f) {
            $list[$i] = new stdClass();
            $list[$i]->img = gr_img('users', $f['v3']);
            $list[$i]->name = $f['name'];
            $list[$i]->countag = $list[$i]->count = 0;
            $altype = 'alert_'.$f['type'];
            $list[$i]->sub = $GLOBALS["lang"]->$altype;

            $list[$i]->rtag = 'type="alert" no="'.$f['id'].'"';
            $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
            if ($f['type'] == 'invitation') {
                $list[$i]->oa = $GLOBALS["lang"]->join;
                $list[$i]->oat = 'class="formpop" title="'.$GLOBALS["lang"]->join_group.'" do="group" btn="'.$GLOBALS["lang"]->join.'" act="join" no="'.$f['v1'].'"';
            } else if ($f['type'] == 'mentioned' || $f['type'] == 'replied' || $f['type'] == 'liked') {
                $list[$i]->oa = $GLOBALS["lang"]->view;
                $list[$i]->oat = 'class="loadgroup paj goback" ldt="group" data-block="crew" msgload="'.$f['v2'].'" no="'.$f['v1'].'"';
            } else if ($f['type'] == 'newmsg') {
                $list[$i]->oa = $GLOBALS["lang"]->view;
                $list[$i]->oat = 'class="loadgroup paj" ldt="user" no="'.$f['v3'].'"';
            }
            $list[$i]->ob = $GLOBALS["lang"]->delete;
            $list[$i]->obt = 'class="deval" act="delete"';
            $list[$i]->icon = '';
            $list[$i]->id = '';
            if ($f['seen'] == 0) {
                $list[$i]->count = 1;
                $list[$i]->id = 'class="active"';
            }
            $i = $i+1;
        }
    } else if ($do["type"] === "blocklist") {
        $query = 'SELECT bl.v2,st.v2 AS status,';
        if (isset($GLOBALS["roles"]['users'][10])) {
            $query = $query.'op.v2 AS name';
        } else {
            $query = $query.'(SELECT name FROM gr_users WHERE id=bl.v2 LIMIT 1) AS name';
        }
        $query = $query.' FROM gr_options bl,gr_options op,gr_options st WHERE bl.v2=op.v3 AND op.type="profile" AND op.v1="name" AND ';
        $query = $query.'st.v3=op.v3 AND bl.type="pblock" AND st.type="profile" AND st.v1="status" ';
        if (!empty($do['search'])) {
            if (isset($GLOBALS["roles"]['users'][10])) {
                $query = $query.'AND op.v2 LIKE :search ';
            } else {
                $query = $query.'AND (SELECT name FROM gr_users WHERE id=bl.v2 LIMIT 1) LIKE :search ';
            }
        }
        $query = $query.'AND bl.v1=:uid ORDER BY bl.id DESC,op.v2 LIMIT '.$lmt.' OFFSET '.$ofs.';';
        $data = array();
        $data['uid'] = $uid;
        if (!empty($do['search'])) {
            $data['search'] = "%".$do["search"]."%";
        }
        $r = db('Grupo', 'q', $query, $data);
        foreach ($r as $f) {
            $list[$i] = new stdClass();
            $list[$i]->img = gr_img('users', $f['v2']);
            $list[$i]->name = $f['name'];
            $status = $f['status'];
            $list[$i]->countag = $list[$i]->count = 0;
            $list[$i]->sub = $GLOBALS["lang"]->$status;
            $list[$i]->rtag = 'type="blocklist" no="'.$f['v2'].'"';
            $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
            $list[$i]->oa = $GLOBALS["lang"]->view;
            $list[$i]->oat = 'class="vwp" no="'.$f['v2'].'"';
            $list[$i]->ob = $GLOBALS["lang"]->unblock;
            $list[$i]->obt = 'class="formpop" pn="2" title="'.$GLOBALS["lang"]->unblock_user.'" do="profile" btn="'.$GLOBALS["lang"]->unblock.'" act="block"';
            $list[$i]->icon = "'status ".$status."'";
            $list[$i]->id = '';
            $i = $i+1;
        }
    } else if ($do["type"] === "users" || $do["type"] === "addgroupuser" && $do["ldt"] != "user") {
        if (!isset($GLOBALS["roles"]['users'][4])) {
            exit;
        }
        if (isset($GLOBALS["roles"]['users'][1])) {
            $list[0]->shw = 'shw';
            $list[0]->icn = 'gi-plus';
            $list[0]->mnu = 'udolist';
            $list[0]->act = 'user';
        }
        if ($do["type"] === "addgroupuser") {
            $query = 'SELECT us.id,us.email,us.role,pr.name AS role,';
            if (isset($GLOBALS["roles"]['users'][10])) {
                $query = $query.'op.v2 AS name,';
            } else {
                $query = $query.'(SELECT name FROM gr_users WHERE id=us.id LIMIT 1) AS name,';
            }
            $query = $query.'(SELECT COUNT(id) FROM gr_options WHERE type="deaccount" AND v1="yes" AND v3=us.id) AS deaccount,';
            $query = $query.'(SELECT st.v2 FROM gr_options st WHERE st.v3=us.id AND st.type="profile" AND st.v1="status") AS status';
            $query = $query.' FROM gr_users us,gr_options op,gr_permissions pr WHERE us.id=op.v3';
            if (!empty($do['search'])) {
                if (isset($GLOBALS["roles"]['users'][10])) {
                    $query = $query.' AND op.v2 LIKE :search';
                } else {
                    $query = $query.' AND (SELECT name FROM gr_users WHERE id=us.id LIMIT 1) LIKE :search';
                }
            }
            $query = $query.' AND us.id NOT IN (SELECT v2 FROM gr_options WHERE type="gruser" AND v1=:gid)';
            $query = $query.' AND op.type="profile" AND op.v1="name" AND pr.id=us.role ORDER BY name ASC LIMIT '.$lmt.' OFFSET '.$ofs;
        } else {
            $query = 'SELECT us.id,us.email,us.role,pr.name AS role,';
            if (isset($GLOBALS["roles"]['users'][10])) {
                $query = $query.'op.v2 AS name,';
            } else {
                $query = $query.'(SELECT name FROM gr_users WHERE id=us.id LIMIT 1) AS name,';
            }
            $query = $query.'(SELECT COUNT(id) FROM gr_options WHERE type="deaccount" AND v1="yes" AND v3=us.id) AS deaccount,';
            $query = $query.'(SELECT st.v2 FROM gr_options st WHERE st.v3=us.id AND st.type="profile" AND st.v1="status") AS status';
            $query = $query.' FROM gr_users us,gr_options op,gr_permissions pr WHERE us.id=op.v3';
            if (!empty($do['search'])) {
                if (isset($GLOBALS["roles"]['users'][10])) {
                    $query = $query.' AND op.v2 LIKE :search';
                } else {
                    $query = $query.' AND (SELECT name FROM gr_users WHERE id=us.id LIMIT 1) LIKE :search';
                }
            }
            $query = $query.' AND op.type="profile" AND op.v1="name" AND pr.id=us.role ORDER BY name ASC LIMIT '.$lmt.' OFFSET '.$ofs;
        }
        $data = array();
        if ($do["type"] === "addgroupuser") {
            $data['gid'] = $do["gid"];
        }
        if (!empty($do['search'])) {
            $data['search'] = '%'.$do['search'].'%';
        }
        $lists = db('Grupo', 'q', $query, $data);
        foreach ($lists as $f) {
            $list[$i] = new stdClass();
            $list[$i]->img = gr_img('users', $f['id']);
            $list[$i]->name = $f['name'];
            $list[$i]->ltype = $do["type"];
            $list[$i]->count = 0;
            $list[$i]->sub = $f["role"];
            if ($f["deaccount"] == 1) {
                $list[$i]->sub = $GLOBALS["lang"]->deactivated;
            }

            $list[$i]->rtag = 'type="profile" no="'.$f['id'].'"';

            $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
            $list[$i]->oa = $GLOBALS["lang"]->view;
            $list[$i]->oat = 'class="vwp" no="'.$f['id'].'"';
            if (isset($GLOBALS["roles"]['groups'][12]) && $do["type"] === "addgroupuser") {
                $list[$i]->ob = $GLOBALS["lang"]->add;
                $list[$i]->obt = 'act="addgroupuser"';
                $list[$i]->sub = $f["role"];
            } else {
                if (isset($GLOBALS["roles"]['users'][6])) {
                    $list[$i]->ob = $GLOBALS["lang"]->login;
                    $list[$i]->obt = 'class="deval" act="login"';
                }

                if (isset($GLOBALS["roles"]['users'][2]) || isset($GLOBALS["roles"]['users'][3]) || isset($GLOBALS["roles"]['users'][8])) {
                    $list[$i]->oc = $GLOBALS["lang"]->edit;
                    $list[$i]->oct = 'class="formpop" data-no="'.$f['id'].'" pn=1 title="'.$GLOBALS["lang"]->edit_profile.'" do="edit" btn="'.$GLOBALS["lang"]->update.'" act="profile"';
                }
                if (isset($GLOBALS["roles"]['users'][9])) {
                    $list[$i]->od = $GLOBALS["lang"]->ip_logs;
                    $list[$i]->odt = 'class="mbopen loadside" xtra="'.$f['id'].'" data-block="lside" act="iplogs" side="lside" zero="0" zval="'.$GLOBALS["lang"]->zero_results.'"';
                }
            }
            $list[$i]->icon = "'status ".$f['status']."'";
            $list[$i]->id = 'class="user"';
            $i = $i+1;
        }
    } else if ($do["type"] === "languages") {
        if (!isset($GLOBALS["roles"]['languages'][4])) {
            exit;
        }
        if (isset($GLOBALS["roles"]['languages'][1])) {
            $list[0]->shw = 'shw';
            $list[0]->icn = 'gi-plus';
            $list[0]->mnu = 'udolist';
            $list[0]->act = 'language';
        }
        $deflang = $GLOBALS["default"]->language;
        $query = 'SELECT * FROM gr_phrases ';
        $query = $query.'WHERE type=:ptype ';
        if (!empty($do['search'])) {
            $query = $query.'AND short LIKE :search ';
        }
        $query = $query.'ORDER BY id DESC, `gr_phrases`.`short` LIMIT '.$lmt.' OFFSET '.$ofs;
        $data = array();
        $data['ptype'] = 'lang';
        if (!empty($do['search'])) {
            $data['search'] = '%'.$do['search'].'%';
        }
        $lists = db('Grupo', 'q', $query, $data);
        foreach ($lists as $f) {
            $list[$i] = new stdClass();
            $list[$i]->img = gr_img('languages', $f['id']);
            $list[$i]->name = $f['short'];
            $list[$i]->count = 0;
            $list[$i]->sub = $GLOBALS["lang"]->language;
            if ($deflang == $f['id']) {
                $list[$i]->sub = $GLOBALS["lang"]->default;
            }

            $list[$i]->rtag = 'type="language" no="'.$f['id'].'"';
            $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
            if (isset($GLOBALS["roles"]['languages'][2])) {
                $list[$i]->oa = $GLOBALS["lang"]->edit;
                $list[$i]->oat = 'class="formpop" title="'.$GLOBALS["lang"]->edit_language.'" do="edit" btn="'.$GLOBALS["lang"]->update.'" act="language" data-no="'.$f['id'].'"';
                if ($f['full'] != 'hide') {
                    $list[$i]->ob = $GLOBALS["lang"]->hide;
                    $list[$i]->obt = 'class="formpop" title="'.$GLOBALS["lang"]->hide_language.'" data-name="'.$f['short'].'" do="language" btn="'.$GLOBALS["lang"]->hide.'" act="hide" data-no="'.$f['id'].'"';
                } else {
                    $list[$i]->ob = $GLOBALS["lang"]->show;
                    $list[$i]->obt = 'class="formpop" title="'.$GLOBALS["lang"]->show_language.'" data-name="'.$f['short'].'" do="language" btn="'.$GLOBALS["lang"]->show.'" act="show" data-no="'.$f['id'].'"';
                }
            }
            if (isset($GLOBALS["roles"]['languages'][3]) && $f['id'] != 1) {
                $list[$i]->oc = $GLOBALS["lang"]->delete;
                $list[$i]->oct = 'class="formpop" pn=2 title="'.$GLOBALS["lang"]->confirm.'" data-name="'.$f['short'].'" data-no="'.$f['id'].'" do="language" btn="'.$GLOBALS["lang"]->delete.'" act="delete"';
            }
            $list[$i]->od = $GLOBALS["lang"]->export;
            $list[$i]->odt = 'class="deval" act="export"';
            $list[$i]->icon = "";
            $list[$i]->id = 'class="language" no="'.$f['id'].'"';
            $i = $i+1;
        }
    } else if ($do["type"] === "complaints") {
        if (!empty($do['search'])) {
            $do['search'] = str_replace('comp#', '', $do['search']);
        }
        $query = 'SELECT cp.*,rl.v3,';
        $query = $query.'(SELECT COUNT(id) FROM gr_options WHERE type="gruser" AND v1=cp.id AND v2=:uid) AS grjoin';
        $query = $query.' FROM gr_complaints cp,gr_options rl WHERE ';
        if (isset($GLOBALS["roles"]['groups'][7])) {
            $query = $query.'rl.type="gruser" AND rl.v1=cp.gid AND rl.v2=:uid ';
            $query = $query.'AND cp.gid=:gid ';
            if (!empty($do['search'])) {
                $query = $query.'AND cp.id LIKE "%'.$do['search'].'%" ';
            }
        } else {
            $query = $query.'rl.type="gruser" AND rl.v1=cp.gid AND rl.v2=:uid AND rl.v3=2 AND cp.msid<>0 ';
            $query = $query.'AND cp.gid=:gid ';
            if (!empty($do['search'])) {
                $query = $query.'AND cp.id LIKE "%'.$do['search'].'%" ';
            }
            $query = $query.'OR rl.type="gruser" AND rl.v1=cp.gid AND rl.v2=:uid AND rl.v3=1 AND cp.msid<>0 ';
            $query = $query.'AND cp.gid=:gid ';
            if (!empty($do['search'])) {
                $query = $query.'AND cp.id LIKE "%'.$do['search'].'%" ';
            }
            $query = $query.'OR rl.type="gruser" AND rl.v1=cp.gid AND rl.v2=:uid AND rl.v3=0 ';
            $query = $query.'AND cp.uid=:uid AND cp.gid=:gid ';
            if (!empty($do['search'])) {
                $query = $query.'AND cp.id LIKE "%'.$do['search'].'%" ';
            }
        }
        $query = $query.'ORDER BY cp.id DESC LIMIT '.$lmt.' OFFSET '.$ofs;
        $data = array();
        $data['uid'] = $uid;
        $data['gid'] = $do["gid"];
        if (!empty($do['search'])) {
            $data['search'] = '%'.$do['search'].'%';
        }
        $lists = db('Grupo', 'q', $query, $data);
        foreach ($lists as $f) {
            $list[$i] = new stdClass();
            $list[$i]->img = gr_img('users', $f['uid']);
            $list[$i]->name = "COMP#".$f['id'];
            $list[$i]->count = $list[$i]->countag = 0;
            $list[$i]->sub = $GLOBALS["lang"]->under_investigation;
            $list[$i]->count = 1;
            if ($f['status'] == 2) {
                $list[$i]->sub = $GLOBALS["lang"]->action_taken;
                $list[$i]->count = 0;
            } else if ($f['status'] == 3) {
                $list[$i]->sub = $GLOBALS["lang"]->rejected;
                $list[$i]->count = 0;
            }

            $list[$i]->rtag = 'type="group" no="'.$f['gid'].'"';
            $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;

            $list[$i]->ob = $GLOBALS["lang"]->view;
            $list[$i]->obt = 'class="formpop" title="'.$GLOBALS["lang"]->view_complaint.'" do="group" btn="'.$GLOBALS["lang"]->update.'" act="takeaction" data-no="'.$f['id'].'"';
            if (!empty($f['msid'])) {
                $list[$i]->oa = $GLOBALS["lang"]->proof;
                $list[$i]->oat = 'class="turnchat goback" data-block="crew" act="msgs" data-msid="'.$f['msid'].'"';
            }
            $list[$i]->icon = "";
            $list[$i]->id = '';
            $i = $i+1;
        }
    } else if ($do["type"] === "rusers") {
        if (!isset($GLOBALS["roles"]['roles'][3])) {
            exit;
        }
        $do["xtra"] = vc($do["xtra"], 'num');
        $query = 'SELECT us.id,us.email,us.role,';
        if (isset($GLOBALS["roles"]['users'][10])) {
            $query = $query.'op.v2 AS name,';
        } else {
            $query = $query.'us.name AS name,';
        }
        $query = $query.'(SELECT st.v2 FROM gr_options st WHERE st.v3=us.id AND st.type="profile" AND st.v1="status") AS status';
        $query = $query.' FROM gr_users us,gr_options op WHERE us.id=op.v3 AND us.role=:rid ';
        if (!empty($do['search'])) {
            if (isset($GLOBALS["roles"]['users'][10])) {
                $query = $query.'AND op.v2 LIKE :search ';
            } else {
                $query = $query.'AND us.name LIKE :search ';
            }
        }
        $query = $query.'AND op.type="profile" AND op.v1="name"';
        $query = $query.' ORDER BY us.id DESC,op.v2 LIMIT '.$lmt.' OFFSET '.$ofs;
        $data = array();
        $data['rid'] = $do["xtra"];
        if (!empty($do['search'])) {
            $data['search'] = '%'.$do['search'].'%';
        }
        $lists = db('Grupo', 'q', $query, $data);
        foreach ($lists as $f) {
            $list[$i] = new stdClass();
            $list[$i]->img = gr_img('users', $f['id']);
            $list[$i]->name = $f['name'];
            $list[$i]->count = 0;
            $list[$i]->sub = $f['email'];

            $list[$i]->rtag = 'type="profile" no="'.$f['id'].'"';
            $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
            $list[$i]->oa = $GLOBALS["lang"]->view;
            $list[$i]->oat = 'class="vwp" no="'.$f['id'].'"';
            if (isset($GLOBALS["roles"]['users'][6])) {
                $list[$i]->ob = $GLOBALS["lang"]->login;
                $list[$i]->obt = 'class="deval" act="login"';
            }
            if (isset($GLOBALS["roles"]['users'][2]) || isset($GLOBALS["roles"]['users'][3]) || isset($GLOBALS["roles"]['users'][8])) {
                $list[$i]->oc = $GLOBALS["lang"]->edit;
                $list[$i]->oct = 'class="formpop" data-no="'.$f['id'].'" pn=1 title="'.$GLOBALS["lang"]->edit_profile.'" do="edit" btn="'.$GLOBALS["lang"]->update.'" act="profile"';
            }
            $list[$i]->icon = "'status ".$f['status']."'";
            $list[$i]->id = 'class="user"';
            $i = $i+1;
        }
    } else if ($do["type"] === "iplogs") {
        if (!isset($GLOBALS["roles"]['users'][9])) {
            exit;
        }
        $do["xtra"] = vc($do["xtra"], 'num');
        $query = 'SELECT id,ip,dev,uid,tms,';
        $query = $query.'(SELECT IFNULL((SELECT CASE WHEN tz.v2="Auto" THEN ';
        $query = $query.'(SELECT am.v2 FROM gr_options am WHERE am.type="profile" AND am.v1="autotmz" AND am.v3=tz.v3)';
        $query = $query.' ELSE tz.v2 END AS timz FROM gr_options tz WHERE tz.type="profile" AND tz.v1="tmz" AND tz.v3=:usid),';
        $query = $query.':tmz)) AS timezone';
        $query = $query.' FROM gr_utrack WHERE uid=:uid';
        if (!empty($do['search'])) {
            $query = $query.' AND ip LIKE :search';
        }
        $query = $query.' ORDER BY gr_utrack.tms DESC LIMIT '.$lmt.' OFFSET '.$ofs;
        $data = array();
        $data['uid'] = $do["xtra"];
        $data['usid'] = $uid;
        $data['tmz'] = $GLOBALS["default"]->timezone;
        if (!empty($do['search'])) {
            $data['search'] = '%'.$do['search'].'%';
        }
        $lists = db('Grupo', 'q', $query, $data);
        foreach ($lists as $f) {
            $list[$i] = new stdClass();
            $list[$i]->img = $GLOBALS["default"]->weburl.'gem/ore/grupo/icons/iplog.svg';
            $list[$i]->name = $f['ip'];
            if ($list[$i]->name == '::1') {
                $list[$i]->name = '127.0.0.1';
            }
            $list[$i]->icon = "";
            $tms = new DateTime($f['tms']);
            $tmz = new DateTimeZone($f['timezone']);
            $tms->setTimezone($tmz);
            $tmst = strtotime($tms->format('Y-m-d H:i:s'));
            if ($GLOBALS["default"]->time_format == 24) {
                $tformat = 'H:i';
            } else {
                $tformat = 'h:i a';
            }
            if ($GLOBALS["default"]->dateformat == 'mdy') {
                $dformat = 'M-d-y';
            } else if ($GLOBALS["default"]->dateformat == 'ymd') {
                $dformat = 'y-M-d';
            } else {
                $dformat = 'd-M-y';
            }
            $list[$i]->name = $list[$i]->name.' - '.$tms->format($dformat.' '.$tformat);
            $list[$i]->count = 0;
            $ipxt = ipxtract($f['dev']);
            $list[$i]->sub = $ipxt['os'].' - '.$ipxt['browser'];
            $list[$i]->rtag = 'type="iplog" no="'.$f['id'].'"';
            $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
            $list[$i]->oa = $GLOBALS["lang"]->delete;
            $list[$i]->oat = 'class="formpop" pn=2 title="'.$GLOBALS["lang"]->confirm.'" data-no="'.$f['id'].'" do="profile" btn="'.$GLOBALS["lang"]->delete.'" act="iplogdelete"';
            $list[$i]->id = 'class="user"';
            $i = $i+1;
        }
    } else if ($do["type"] === "manageads") {
        if (!isset($GLOBALS["roles"]['sys'][7])) {
            exit;
        }
        $list[0]->shw = 'shw';
        $list[0]->icn = 'gi-plus';
        $list[0]->mnu = 'udolist';
        $list[0]->act = 'ads';
        $query = 'SELECT id,name,adslot';
        $query = $query.' FROM gr_ads';
        if (!empty($do['search'])) {
            $query = $query.' WHERE name LIKE :search';
        }
        $query = $query.' ORDER BY id DESC LIMIT '.$lmt.' OFFSET '.$ofs;
        $data = array();
        if (!empty($do['search'])) {
            $data['search'] = '%'.$do['search'].'%';
        }
        $lists = db('Grupo', 'q', $query, $data);
        foreach ($lists as $f) {
            $list[$i] = new stdClass();
            $list[$i]->img = $GLOBALS["default"]->weburl.'gem/ore/grupo/icons/ads.svg';
            $list[$i]->name = $f['name'];
            $list[$i]->icon = "";
            $list[$i]->count = 0;
            $adslot = $f['adslot'];
            $list[$i]->sub = $GLOBALS["lang"]->$adslot;
            $list[$i]->rtag = 'type="ads" no="'.$f['id'].'"';
            $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
            $list[$i]->ob = $GLOBALS["lang"]->edit;
            $list[$i]->obt = 'class="formpop" title="'.$GLOBALS["lang"]->edit_ad.'" do="edit" btn="'.$GLOBALS["lang"]->update.'" act="ads" data-no="'.$f['id'].'"';
            $list[$i]->oc = $GLOBALS["lang"]->delete;
            $list[$i]->oct = 'class="formpop" data-name="'.$f['name'].'" data-no="'.$f['id'].'" title="'.$GLOBALS["lang"]->confirm.'" do="ads" btn="'.$GLOBALS["lang"]->delete.'" act="delete"';
            $list[$i]->id = 'class="ads"';
            $i = $i+1;
        }
    } else if ($do["type"] === "stickerpacks") {
        if (!isset($GLOBALS["roles"]['features'][16])) {
            exit;
        }
        $list[0]->shw = 'shw';
        $list[0]->icn = 'gi-plus';
        $list[0]->mnu = 'udolist';
        $list[0]->act = 'stickerpack';
        if (!empty($do['search'])) {
            $do['search'] = stripslashes(str_replace('/', '', $do['search']));
            $dir = 'grupo/stickers/*'.$do['search'].'*';
        } else {
            $dir = 'grupo/stickers/';
        }
        $r = flr('list', $dir, 1);
        $r = array_slice($r, $ofs, $lmt);
        foreach ($r as $f) {
            $list[$i] = new stdClass();
            $n = basename($f);
            $list[$i]->img = $GLOBALS["default"]->weburl."gem/ore/grupo/icons/stickers.svg";
            $im = "gem/ore/grupo/stickers/".$n."/grstickericon.png";
            if (file_exists($im)) {
                $list[$i]->img = '"'.$GLOBALS["default"]->weburl.$im.'"';
            }
            $list[$i]->name = $n;
            $list[$i]->sub = $GLOBALS["lang"]->stickerpack;
            $list[$i]->count = '0';
            $list[$i]->rtag = 'type="stickers" no="'.$n.'"';
            $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
            $list[$i]->oa = $GLOBALS["lang"]->view;
            $list[$i]->oat = 'class="mbopen loadside" xtra="'.$n.'" tabtitle="'.$GLOBALS["lang"]->stickers.'" data-block="lside" act="stickers" side="lside" zero="0" zval="'.$GLOBALS["lang"]->zero_stickers.'"';
            $list[$i]->ob = $GLOBALS["lang"]->edit;
            $list[$i]->obt = 'class="formpop" data-no="'.$n.'" pn=1 title="'.$GLOBALS["lang"]->edit_stickerpack.'" do="edit" btn="'.$GLOBALS["lang"]->update.'" act="stickerpack"';
            $list[$i]->oc = $GLOBALS["lang"]->delete;
            $list[$i]->oct = 'class="formpop" data-no="'.$n.'" pn=1 title="'.$GLOBALS["lang"]->confirm.'" do="stickers"  btn="'.$GLOBALS["lang"]->delete.'" act="delete"';
            $list[$i]->icon = "";
            $list[$i]->id = 'class="sticker"';
            $i = $i+1;
        }
    } else if ($do["type"] === "stickers") {
        $do['xtra'] = preg_replace('/[^a-z0-9 ]/i', '', $_POST['xtra']);
        if (!isset($GLOBALS["roles"]['features'][16]) || empty($do['xtra'])) {
            exit;
        }
        $list[0]->shw = 'shw';
        $list[0]->icn = 'gi-plus';
        $list[0]->mnu = 'udolist';
        $list[0]->act = 'stickers';
        if (!empty($do['search'])) {
            $do['search'] = stripslashes(str_replace('/', '', $do['search']));
            $dir = 'grupo/stickers/'.$do['xtra'].'/*'.$do['search'].'*.{jpg,png,gif,bmp,jpeg}';
        } else {
            $dir = 'grupo/stickers/'.$do['xtra'].'/*.{jpg,png,gif,bmp,jpeg}';
        }
        $r = flr('list', $dir, 'brace');
        $r = array_slice($r, $ofs, $lmt);
        foreach ($r as $f) {
            $n = basename($f);
            if ($n != 'grstickericon.png') {
                $list[$i] = new stdClass();
                $list[$i]->img = '"'.$GLOBALS["default"]->weburl."gem/ore/grupo/stickers/".$do['xtra']."/".$n.'"';
                $sticker = explode('-gr-', $n, 2);
                if (isset($sticker[1])) {
                    $list[$i]->name = $sticker[1];
                } else {
                    $list[$i]->name = $n;
                }
                $list[$i]->sub = $do['xtra'];
                $list[$i]->count = '0';
                $list[$i]->rtag = 'type="stickers" no="'.$n.'"';
                $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
                $list[$i]->oc = $GLOBALS["lang"]->delete;
                $list[$i]->oct = 'class="formpop" data-name="'.$list[$i]->name.'" data-no="'.$do['xtra'].'/'.$n.'" title="'.$GLOBALS["lang"]->confirm.'" do="stickers"  btn="'.$GLOBALS["lang"]->delete.'" act="delete"';
                $list[$i]->icon = "";
                $list[$i]->id = 'class="sticker"';
                $i = $i+1;
            }
        }
    } else if ($do["type"] === "lastseen") {
        if (isset($do['gmid']) && !empty($do['gmid'])) {
            $do['gmid'] = vc($do['gmid'], 'num');
            $query = 'SELECT gr.v2,';
            if (isset($GLOBALS["roles"]['users'][10])) {
                $query = $query.'(SELECT op.v2 FROM gr_options op WHERE type="profile" AND op.v1="name" AND op.v3=gr.v2 LIMIT 1) AS name,';
            } else {
                $query = $query.'(SELECT name FROM gr_users WHERE id=gr.v2 LIMIT 1) AS name,';
            }
            $query = $query.'(SELECT st.v2 FROM gr_options st WHERE st.v3=gr.v2 AND st.type="profile" AND st.v1="status") AS status,';
            $query = $query.'(SELECT count(cm.id) FROM gr_msgs cm WHERE cm.id=:msid AND cm.uid=:uid) AS verfy';
            $query = $query.' FROM gr_options gr WHERE gr.type="lview" AND gr.v1=:gid';
            if (!empty($do['search'])) {
                if (isset($GLOBALS["roles"]['users'][10])) {
                    $query = $query.' AND (SELECT op.v2 FROM gr_options op WHERE type="profile" AND op.v1="name" AND op.v3=gr.v2 LIMIT 1) LIKE :search';
                } else {
                    $query = $query.' AND (SELECT name FROM gr_users WHERE id=gr.v2 LIMIT 1)  LIKE :search';
                }
            }
            $query = $query.' AND CAST(gr.v3 AS SIGNED)>=:msid AND gr.v2<>:uid ORDER BY name ASC LIMIT '.$lmt.' OFFSET '.$ofs;
            $data = array();
            $data['uid'] = $uid;
            $data['gid'] = $do["gid"];
            $data['msid'] = $do["gmid"];
            if (!empty($do['search'])) {
                $data['search'] = '%'.$do['search'].'%';
            }
            $lists = db('Grupo', 'q', $query, $data);
            foreach ($lists as $f) {
                if ($f['verfy'] != 0) {
                    $list[$i] = new stdClass();
                    $list[$i]->img = gr_img('users', $f['v2']);
                    $list[$i]->name = $f['name'];
                    $list[$i]->count = 0;
                    $varky = $f['status'];
                    $list[$i]->sub = $GLOBALS["lang"]->$varky;

                    $list[$i]->rtag = 'type="profile" no="'.$f['v2'].'"';
                    $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
                    $list[$i]->oa = $GLOBALS["lang"]->view;
                    $list[$i]->oat = 'class="vwp" no="'.$f['v2'].'"';
                    if (isset($GLOBALS["roles"]['privatemsg'][1]) && $f['v2'] != $uid) {
                        $list[$i]->ob = $GLOBALS["lang"]->chat;
                        $list[$i]->obt = 'class="loadgroup paj" ldt="user" no="'.$f['v2'].'"';
                    }
                    $list[$i]->icon = "'status ".$f['status']."'";
                    $list[$i]->id = 'class="user"';
                    $i = $i+1;
                }
            }
        }
    } else if ($do["type"] === "online") {
        if (!isset($GLOBALS["roles"]['users'][5])) {
            exit;
        }
        $query = 'SELECT us.name,st.v3,st.tms,st.v2,';
        if (isset($GLOBALS["roles"]['users'][10])) {
            $query = $query.'op.v2 AS fname, ';
        } else {
            $query = $query.'us.name AS fname, ';
        }
        $query = $query."(SELECT pf.v2 FROM gr_profiles pf WHERE pf.uid = op.v3 AND pf.type = 'profile' AND pf.name = '6') AS flag ";
        $query = $query.'FROM gr_options op, gr_options st, ';
        $query = $query.'gr_users us WHERE st.v3 = op.v3 AND st.v3 = us.id AND st.v3 <> :uid ';
        if (!empty($do['search'])) {
            if (isset($GLOBALS["roles"]['users'][10])) {
                $query = $query.'AND op.v2 LIKE :search ';
            } else {
                $query = $query.'AND us.name LIKE :search ';
            }
        }
        $query = $query.'AND st.v1="status" AND st.v2="online" AND st.type="profile" AND op.type="profile" ';
        $query = $query.'AND op.v1="name" OR st.v3 = op.v3 AND st.v3 = us.id AND st.v3 <> :uid ';
        if (!empty($do['search'])) {
            if (isset($GLOBALS["roles"]['users'][10])) {
                $query = $query.'AND op.v2 LIKE :search ';
            } else {
                $query = $query.'AND us.name LIKE :search ';
            }
        }
        $query = $query.'AND st.v1="status" AND st.v2="idle" AND st.type="profile" AND op.type="profile" ';
        $query = $query.'AND op.v1="name" ORDER BY st.v2 DESC LIMIT '.$lmt.' OFFSET '.$ofs;
        $data = array();
        $data['uid'] = $uid;
        if (!empty($do['search'])) {
            $data['search'] = '%'.$do['search'].'%';
        }
        $r = db('Grupo', 'q', $query, $data);
        foreach ($r as $f) {
            $list[$i] = new stdClass();
            $list[$i]->img = gr_img('users', $f['v3']);
            $list[$i]->name = $f['fname'];
            $list[$i]->count = 0;
            $list[$i]->sub = '';
            $list[$i]->user = '';
            $list[$i]->sub = '@'.$f['name'];

            $list[$i]->rtag = 'type="profile" no="'.$f['v3'].'"';
            $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
            $list[$i]->oa = $GLOBALS["lang"]->view;
            $list[$i]->oat = 'class="vwp" no="'.$f['v3'].'"';
            if (isset($GLOBALS["roles"]['privatemsg'][1])) {
                $list[$i]->ob = $GLOBALS["lang"]->chat;
                $list[$i]->obt = 'class="loadgroup paj" ldt="user" no="'.$f['v3'].'"';
            }
            if (isset($GLOBALS["roles"]['users'][2]) || isset($GLOBALS["roles"]['users'][3]) || isset($GLOBALS["roles"]['users'][8])) {
                $list[$i]->oc = $GLOBALS["lang"]->edit;
                $list[$i]->oct = 'class="formpop" data-no="'.$f['v3'].'" pn=1 title="'.$GLOBALS["lang"]->edit_profile.'" do="edit" btn="'.$GLOBALS["lang"]->update.'" act="profile"';
            }
            $list[$i]->icon = "'status ".$f['v2']."'";
            $list[$i]->icon1 = $f['v3'] == '10' ? "'status host-mic'" : ($f['v3'] == '11' ? "'status active-icon'" : ($f['v3'] == '12' ? "'status super-icon'" : "d-none"));
            $list[$i]->flag = strlen($f['flag']) > 0 ? "'background-image: url(https://api.hostip.info/images/flags/" . strtolower($f['flag']) . ".gif)'" : "";
                    $list[$i]->flag1 = strlen($f['flag']) > 0 ? "'host-mic1'" : "d-none";
            $list[$i]->id = 'class="online"';
            $i = $i+1;
        }
    } else if ($do["type"] === "roles") {
        if (!isset($GLOBALS["roles"]['roles'][3])) {
            exit;
        }
        if (isset($GLOBALS["roles"]['roles'][1])) {
            $list[0]->shw = 'shw';
            $list[0]->icn = 'gi-plus';
            $list[0]->mnu = 'udolist';
            $list[0]->act = 'role';
        }
        $query = 'SELECT rl.id,rl.name,(SELECT count(1) FROM gr_users us WHERE us.role=rl.id)';
        $query = $query.' AS rcount FROM gr_permissions rl';
        if (!empty($do['search'])) {
            $query = $query.' WHERE rl.name LIKE :search';
        }
        $query = $query.' ORDER BY name ASC LIMIT '.$lmt.' OFFSET '.$ofs;
        $data = array();
        if (!empty($do['search'])) {
            $data['search'] = '%'.$do['search'].'%';
        }
        $lists = db('Grupo', 'q', $query, $data);
        foreach ($lists as $f) {
            $list[$i] = new stdClass();
            $list[$i]->img = gr_img('roles', $f['id']);
            $list[$i]->name = $f['name'];
            $list[$i]->count = 0;
            $list[$i]->sub = $f['rcount'].' '.$GLOBALS["lang"]->users;

            $list[$i]->rtag = 'type="role" no="'.$f['id'].'"';
            $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
            if (isset($GLOBALS["roles"]['roles'][3])) {
                $list[$i]->oa = $GLOBALS["lang"]->users;
                $list[$i]->oat = 'class="mbopen loadside" xtra="'.$f['id'].'" data-block="rside" act="rusers" side="rside" zero="0" zval="'.$GLOBALS["lang"]->zero_users.'"';
            }
            if (isset($GLOBALS["roles"]['roles'][2])) {
                $list[$i]->ob = $GLOBALS["lang"]->edit;
                $list[$i]->obt = 'class="formpop" title="'.$GLOBALS["lang"]->edit_role.'" do="edit" btn="'.$GLOBALS["lang"]->update.'" act="role" data-name="'.$f['name'].'" data-no="'.$f['id'].'"';
            }
            if (isset($GLOBALS["roles"]['roles'][2])) {
                $list[$i]->oc = $GLOBALS["lang"]->delete;
                $list[$i]->oct = 'class="formpop" data-name="'.$f['name'].'" data-no="'.$f['id'].'" title="'.$GLOBALS["lang"]->confirm.'" do="role" btn="'.$GLOBALS["lang"]->delete.'" act="delete"';
            }
            $list[$i]->icon = '';
            $list[$i]->id = '';
            $i = $i+1;
        }
    } else if ($do["type"] === "files") {
        if (!isset($GLOBALS["roles"]['files']['5'])) {
            exit;
        }
        if (isset($GLOBALS["roles"]['files'][1])) {
            $list[0]->shw = 'shw';
            $list[0]->icn = 'gi-upload';
            $list[0]->mnu = 'udolist';
            $list[0]->act = 'uploadfile';
        }
        if (!empty($do['search'])) {
            $do['search'] = stripslashes(str_replace('/', '', $do['search']));
            $dir = 'grupo/files/'.$uid.'/*'.$do['search'].'*';
        } else {
            $dir = 'grupo/files/'.$uid.'/';
        }
        $r = flr('list', $dir);
        $r = array_slice($r, $ofs, $lmt);
        foreach ($r as $f) {
            $list[$i] = new stdClass();
            $list[$i]->img = $GLOBALS["default"]->weburl."gem/ore/grupo/ext/default.png";
            $im = "gem/ore/grupo/ext/".pathinfo($f, PATHINFO_EXTENSION).".png";
            $n = basename($f);
            if (file_exists($im)) {
                $list[$i]->img = $GLOBALS["default"]->weburl.$im;
            }
            $list[$i]->name = explode('-gr-', $n, 2)[1];
            $list[$i]->sub = flr('size', $dir.$n);
            $list[$i]->count = '0';

            $list[$i]->rtag = 'type="files" no="'.$n.'"';
            $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
            if (isset($GLOBALS["roles"]['files'][4])) {
                $list[$i]->oa = $GLOBALS["lang"]->share;
                $list[$i]->oat = 'class="mbopen" data-block="panel" act="share"';
            }
            if (isset($GLOBALS["roles"]['files'][2])) {
                $list[$i]->ob = $GLOBALS["lang"]->zip;
                $list[$i]->obt = 'class="deval" act="zip"';
            }
            if (isset($GLOBALS["roles"]['files'][3])) {
                $list[$i]->oc = $GLOBALS["lang"]->delete;
                $list[$i]->oct = 'class="formpop" pn=2 title="'.$GLOBALS["lang"]->confirm.'" do="files"  btn="'.$GLOBALS["lang"]->delete.'" act="delete"';
            }
            $ext = mime_content_type($f);
            if ($ext === 'image/jpeg' || $ext === 'image/png' || $ext === 'image/gif' || $ext === 'image/bmp' || $ext === 'image/x-ms-bmp') {
                $list[$i]->od = $GLOBALS["lang"]->view;
                $list[$i]->odt = 'class="grpreview paj" type="img" load="'.$GLOBALS["default"]->weburl.$f.'" mime="'.$ext.'"';
            } else if ($ext === 'video/mp4' || $ext === 'video/mpeg' || $ext === 'video/ogg' || $ext === 'video/webm') {
                $list[$i]->od = $GLOBALS["lang"]->view;
                $list[$i]->odt = 'class="grpreview paj" type="video" load="'.$GLOBALS["default"]->weburl.$f.'" mime="'.$ext.'"';
            }


            $list[$i]->icon = "";
            $list[$i]->id = 'class="file"';
            $i = $i+1;
        }
    } else if ($do["type"] === "ufields") {
        if (isset($GLOBALS["roles"]['fields'][4])) {
            if (isset($GLOBALS["roles"]['fields'][1])) {
                $list[0]->shw = 'shw';
                $list[0]->icn = 'gi-plus';
                $list[0]->mnu = 'udolist';
                $list[0]->act = 'customfield';
            }
            $query = 'SELECT cf.id,cf.cat,cf.type,ph.full,ph.type AS phtype,cf.name,';
            $query = $query.' ph.lid FROM gr_profiles cf ,gr_phrases ph WHERE cf.type="gfield"';
            if (!empty($do['search'])) {
                $query = $query.' AND ph.full LIKE :search';
            }
            $query = $query.' AND ph.type="phrase" AND ph.short=cf.name';
            $query = $query.' AND ph.lid=:uslang OR cf.type="field"';
            if (!empty($do['search'])) {
                $query = $query.' AND ph.full LIKE :search';
            }
            $query = $query.' AND ph.type="phrase" AND ph.short=cf.name AND ph.lid=:uslang';
            $query = $query.' ORDER BY cf.id DESC LIMIT '.$lmt.' OFFSET '.$ofs;
            $data = array();
            $data['uslang'] = $GLOBALS["lang"]->userlangid;
            if (!empty($do['search'])) {
                $data['search'] = '%'.$do['search'].'%';
            }
            $lists = db('Grupo', 'q', $query, $data);
            foreach ($lists as $f) {
                $list[$i] = new stdClass();
                $list[$i]->img = gr_img('fields', $f['cat']);
                $varky = $f['name'];
                $list[$i]->name = $GLOBALS["lang"]->$varky;
                $list[$i]->count = 0;
                $varky = $f['cat'];
                if ($f['type'] == 'field') {
                    $list[$i]->sub = $GLOBALS["lang"]->$varky.' - ('.$GLOBALS["lang"]->profile.')';
                } else {
                    $list[$i]->sub = $GLOBALS["lang"]->$varky.' - ('.$GLOBALS["lang"]->group.')';
                }

                $list[$i]->rtag = 'type="role" no="'.$f['id'].'"';
                $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
                if (isset($GLOBALS["roles"]['fields'][2])) {
                    $list[$i]->ob = $GLOBALS["lang"]->edit;
                    $list[$i]->obt = 'class="formpop" title="'.$GLOBALS["lang"]->edit_custom_field.'" do="edit" btn="'.$GLOBALS["lang"]->update.'" act="customfield" data-name="'.$f['name'].'" data-no="'.$f['id'].'"';
                }
                if (isset($GLOBALS["roles"]['fields'][3])) {
                    $list[$i]->oc = $GLOBALS["lang"]->delete;
                    $list[$i]->oct = 'class="formpop" data-name="'.$f['name'].'" data-no="'.$f['id'].'" title="'.$GLOBALS["lang"]->confirm.'" do="customfield" btn="'.$GLOBALS["lang"]->delete.'" act="delete"';
                }
                $list[$i]->icon = '';
                $list[$i]->id = '';
                $i = $i+1;
            }
        }
    } else if ($do["type"] === "cmenu") {
        if (isset($GLOBALS["roles"]['sys'][6])) {
            $list[0]->shw = 'shw';
            $list[0]->icn = 'gi-plus';
            $list[0]->mnu = 'udolist';
            $list[0]->act = 'menuitem';
            $query = 'SELECT cf.id,ph.type AS phtype,cf.v1,ph.lid FROM gr_options cf,';
            $query = $query.'gr_phrases ph WHERE cf.type="menuitem"';
            if (!empty($do['search'])) {
                $query = $query.' AND ph.full LIKE :search';
            }
            $query = $query.' AND ph.type="phrase" AND ph.short=cf.v1 AND ph.lid=:uslang';
            $query = $query.' ORDER BY v3 ASC LIMIT '.$lmt.' OFFSET '.$ofs;
            $data = array();
            $data['uslang'] = $GLOBALS["lang"]->userlangid;
            if (!empty($do['search'])) {
                $data['search'] = '%'.$do['search'].'%';
            }
            $lists = db('Grupo', 'q', $query, $data);
            foreach ($lists as $f) {
                $list[$i] = new stdClass();
                $list[$i]->img = gr_img('groups', 0);
                $varky = $f['v1'];
                $list[$i]->name = $GLOBALS["lang"]->$varky;
                $list[$i]->count = 0;
                $list[$i]->sub = $GLOBALS["lang"]->menu_item;
                $list[$i]->rtag = 'type="cmenu" no="'.$f['id'].'"';
                $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
                $list[$i]->ob = $GLOBALS["lang"]->edit;
                $list[$i]->obt = 'class="formpop" title="'.$GLOBALS["lang"]->edit_menu_item.'" do="edit" btn="'.$GLOBALS["lang"]->update.'" act="menuitem" data-name="'.$list[$i]->name.'" data-no="'.$f['id'].'"';
                $list[$i]->oc = $GLOBALS["lang"]->delete;
                $list[$i]->oct = 'class="formpop" data-name="'.$f['v1'].'" data-no="'.$f['id'].'" title="'.$GLOBALS["lang"]->confirm.'" do="menuitem" btn="'.$GLOBALS["lang"]->delete.'" act="delete"';
                $list[$i]->icon = '';
                $list[$i]->id = '';
                $i = $i+1;
            }
        }
    } else if ($do["type"] === "radiostations") {
        if (isset($GLOBALS["roles"]['features'][14])) {
            $list[0]->shw = 'shw';
            $list[0]->icn = 'gi-plus';
            $list[0]->mnu = 'udolist';
            $list[0]->act = 'radiostation';
            $query = 'SELECT id,v1,v3 FROM gr_options';
            $query = $query.' WHERE type="radiostation"';
            if (!empty($do['search'])) {
                $query = $query.' AND v1 LIKE :search';
            }
            $query = $query.' ORDER BY v1 ASC LIMIT '.$lmt.' OFFSET '.$ofs;
            $data = array();
            if (!empty($do['search'])) {
                $data['search'] = '%'.$do['search'].'%';
            }
            $lists = db('Grupo', 'q', $query, $data);
            foreach ($lists as $f) {
                $list[$i] = new stdClass();
                $list[$i]->img = gr_img('radiostations', $f['id']);
                $list[$i]->name = $f['v1'];
                $list[$i]->count = 0;
                $list[$i]->sub = $GLOBALS["lang"]->radiostations;
                $list[$i]->rtag = 'type="radiostation" no="'.$f['id'].'"';
                $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
                $list[$i]->ob = $GLOBALS["lang"]->edit;
                $list[$i]->obt = 'class="formpop" title="'.$GLOBALS["lang"]->edit_radiostation.'" do="edit" btn="'.$GLOBALS["lang"]->update.'" act="radiostation" data-no="'.$f['id'].'"';
                $list[$i]->oc = $GLOBALS["lang"]->delete;
                $list[$i]->oct = 'class="formpop" data-name="'.$f['v1'].'" data-no="'.$f['id'].'" title="'.$GLOBALS["lang"]->confirm.'" do="radiostation" btn="'.$GLOBALS["lang"]->delete.'" act="delete"';
                $list[$i]->icon = '';
                $list[$i]->id = '';
                $i = $i+1;
            }
        }
    } else if ($do["type"] === "loginproviders") {
        if (isset($GLOBALS["roles"]['sys'][8])) {
            $list[0]->shw = 'shw';
            $list[0]->icn = 'gi-plus';
            $list[0]->mnu = 'udolist';
            $list[0]->act = 'loginprovider';
            $query = 'SELECT id,v1,v3 FROM gr_options';
            $query = $query.' WHERE type="loginprovider"';
            if (!empty($do['search'])) {
                $query = $query.' AND v1 LIKE :search';
            }
            $query = $query.' ORDER BY v1 ASC LIMIT '.$lmt.' OFFSET '.$ofs;
            $data = array();
            if (!empty($do['search'])) {
                $data['search'] = '%'.$do['search'].'%';
            }
            $lists = db('Grupo', 'q', $query, $data);
            foreach ($lists as $f) {
                $list[$i] = new stdClass();
                $list[$i]->img = gr_img('loginprovider', $f['id']);
                $list[$i]->name = stripslashes($f['v1']);
                $list[$i]->count = 0;
                $list[$i]->sub = $GLOBALS["lang"]->identity_provider;
                $list[$i]->rtag = 'type="loginprovider" no="'.$f['id'].'"';
                $list[$i]->oa = $list[$i]->ob = $list[$i]->oc = 0;
                $list[$i]->ob = $GLOBALS["lang"]->edit;
                $list[$i]->obt = 'class="formpop" title="'.$GLOBALS["lang"]->edit_provider.'" do="edit" btn="'.$GLOBALS["lang"]->update.'" act="loginprovider" data-no="'.$f['id'].'"';
                $list[$i]->oc = $GLOBALS["lang"]->delete;
                $list[$i]->oct = 'class="formpop" data-name="'.$list[$i]->name.'" data-no="'.$f['id'].'" title="'.$GLOBALS["lang"]->confirm.'" do="loginprovider" btn="'.$GLOBALS["lang"]->delete.'" act="delete"';
                $list[$i]->icon = '';
                $list[$i]->id = '';
                $i = $i+1;
            }
        }
    } else if ($do["type"] === "getinfo") {


        if ($do["ldt"] == 'group') {
            if (!gr_role('access', 'groups', '14')) {
                $cr = db('Grupo', 's', 'options', 'type,id', 'group', $do["id"]);
                if (!empty($cr['0']['v3']) && !gr_role('access', 'groups', '7') && ($cr['0']['v3']=='paid' || $cr['0']['v3']=='secret') && floatval($cr['0']['v7'])>0) {
                    $chanId = $do["id"];
                    $timeCheckQuery = "SELECT v5 FROM `gr_options` WHERE `type` = 'gruser' and v1='$chanId' and v2 = '$uid'";
                    $check_time = db('Grupo', 'q', $timeCheckQuery);
                    if (count($check_time)>0) {
                        if (intval($check_time['v5']) < time()) {
                            $dt = array();
                            $dt['id'] = $do["id"];
                            $dt['msg'] = 'left_group';
                            gr_group('sendmsg', $dt, 1, 1, $uid);
                            gr_data('d', 'type,v1,v2', 'gruser', $do["id"], $uid);
                            gr_data('d', 'type,v1,v2', 'lview', $do["id"], $uid);
                            echo json_encode(array('eval'=>'window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/"; $(".swr-grupo .lside > .tabs > ul > li[act=groups]").trigger("click"); say("You not have access for this channel");'));
                            exit();
                        } 
                    }
                }
            }
        } 

        $i = 0;
        unset($list[0]);
        $list[$i] = new stdClass();
        $list[$i]->id = vc($do['id'], 'num');
        $list[$i]->edit = $list[$i]->icon = 0;
        $list[$i]->sharedmedia = 0;
        $list[$i]->btn = $GLOBALS["lang"]->message;
        $list[$i]->tbclass = 'loadgroup';
        $list[$i]->tbattr = 'no="'.$do['id'].'" ldt="user"';
        $list[$i]->msgoffmsg = $list[$i]->msgoff = 0;
        if (isset($do['ldt']) && $do['ldt'] == 'group') {
            $query = 'SELECT gr.v1,gr.v2,gr.v3,gr.v4,gr.v5,gr.v6,gr.tms,';
            $query = $query.'(SELECT count(1) FROM gr_msgs lk WHERE lk.type="like" AND lk.gid=:gid) AS likes,';
            $query = $query.'(SELECT GROUP_CONCAT(CONCAT(gr_msgs.msg) ORDER BY gr_msgs.id DESC SEPARATOR ";") AS sharedmedia ';
            $query = $query.'FROM gr_msgs WHERE gr_msgs.cat="group" AND gr_msgs.type="file" AND gr_msgs.gid=:gid AND gr_msgs.xtra LIKE "%.jpg" ';
            $query = $query.'OR gr_msgs.cat="group" AND gr_msgs.type="file" AND gr_msgs.gid=:gid AND gr_msgs.xtra LIKE "%.jpeg" ';
            $query = $query.'OR gr_msgs.cat="group" AND gr_msgs.type="file" AND gr_msgs.gid=:gid AND gr_msgs.xtra LIKE "%.png" ';
            $query = $query.'OR gr_msgs.cat="group" AND gr_msgs.type="file" AND gr_msgs.gid=:gid AND gr_msgs.xtra LIKE "%.gif" ';
            $query = $query.'OR gr_msgs.cat="group" AND gr_msgs.type="file" AND gr_msgs.gid=:gid AND gr_msgs.xtra LIKE "%.bmp" ';
            $query = $query.' LIMIT 9) AS sharedmedia,';
            $query = $query.'(SELECT v3 FROM gr_options WHERE type="gruser" AND v1=:gid AND v2=:uid LIMIT 1) AS grrole,';
            $query = $query.'(SELECT v2 FROM gr_options WHERE type="groupslug" AND v1=:gid LIMIT 1) AS groupslug,';
            $query = $query.'(SELECT IFNULL((SELECT CASE WHEN tz.v2="Auto" THEN ';
            $query = $query.'(SELECT am.v2 FROM gr_options am WHERE am.type="profile" AND am.v1="autotmz" AND am.v3=tz.v3)';
            $query = $query.' ELSE tz.v2 END AS timz FROM gr_options tz WHERE tz.type="profile" AND tz.v1="tmz" AND tz.v3=:uid),';
            $query = $query.':tmz)) AS timezone,';
            $query = $query.'(SELECT count(1) FROM gr_options mc WHERE mc.type="gruser" AND mc.v1=:gid) AS mcount';
            $query = $query.' FROM gr_options gr WHERE gr.type="group" AND gr.id=:gid';
            $query = $query.' LIMIT 1';
            $data = array();
            $data['gid'] = $do["id"];
            $data['uid'] = $uid;
            $data['tmz'] = $GLOBALS["default"]->timezone;
            $r = db('Grupo', 'q', $query, $data);
            if (isset($r[0])) {
                $list[$i]->img = gr_img('groups', $do['id']);
                $list[$i]->msgoff = 2;
                $list[$i]->uname = $GLOBALS["lang"]->public_group;
                $list[$i]->shares = gr_shnum($r[0]['mcount']);
                $list[$i]->loves = gr_shnum($r[0]['likes']);
                $list[$i]->mna = html_entity_decode($GLOBALS["lang"]->hearts);
                $list[$i]->mnb = html_entity_decode($GLOBALS["lang"]->members);
                $list[$i]->mnc = html_entity_decode($GLOBALS["lang"]->created_on);
                $list[$i]->cp = gr_img('coverpic/groups', $do['id']);
                $list[$i]->tbclass = 'formpop';
                if ($r[0]['grrole'] == 2 || isset($GLOBALS["roles"]['groups']['7'])) {
                    $list[$i]->icon = 1;
                    $list[$i]->iconattr = array();
                    $list[$i]->iconattr['pn'] = 4;
                    $list[$i]->iconattr['no'] = $do['id'];
                    $list[$i]->iconattr['do'] = 'edit';
                    $list[$i]->iconattr['act'] = 'group';
                    $list[$i]->icontitle = $GLOBALS["lang"]->edit_group;
                    $list[$i]->iconattr['title'] = $GLOBALS["lang"]->edit_group;
                    $list[$i]->iconattr['btn'] = $GLOBALS["lang"]->edit_group;
                    $list[$i]->iconclass = 'gi-pencil-1 formpop';
                } else {
                    $list[$i]->icon = 1;
                    $list[$i]->iconattr = array();
                    $list[$i]->iconattr['pn'] = 4;
                    $list[$i]->iconattr['no'] = $do['id'];
                    $list[$i]->iconattr['do'] = 'group';
                    $list[$i]->iconattr['act'] = 'reportmsg';
                    $list[$i]->icontitle = $GLOBALS["lang"]->report_group;
                    $list[$i]->iconattr['title'] = $GLOBALS["lang"]->report_group;
                    $list[$i]->iconattr['btn'] = $GLOBALS["lang"]->report_group;
                    $list[$i]->iconclass = 'gi-flag-1 formpop';
                }
                if ($r[0]['v6'] != 'unleavable') {
                    $list[$i]->btn = $GLOBALS["lang"]->leave_group;
                    $list[$i]->tbattr = 'pn="1" title="'.$GLOBALS["lang"]->leave_group.'" do="group" btn="'.$GLOBALS["lang"]->leave_group.'" act="leave"';
                } else {
                    $list[$i]->btn = $GLOBALS["lang"]->report_group;
                    $list[$i]->tbattr = 'class="formpop" pn="1" title="'.$GLOBALS["lang"]->report_group.'" do="group" btn="'.$GLOBALS["lang"]->report.'" act="reportmsg"';
                }
                if (isset($r[0])) {
                    $list[$i]->name = html_entity_decode($r[0]['v1']);
                    if (!empty($r[0]['v2'])) {
                        $list[$i]->uname = $GLOBALS["lang"]->protected_group;
                    }
                    if ($r[0]['v3'] == 'secret') {
                        $list[$i]->uname = $GLOBALS["lang"]->secret_group;
                    }
                    if (gr_role('access', 'features', '5')) {
                        if (!empty($r[0]['sharedmedia'])) {
                            $sharedmedia = explode(';', $r[0]['sharedmedia']);
                            $list[$i]->sharedmediatitle = $GLOBALS["lang"]->recent_images;
                            foreach ($sharedmedia as $key => $media) {
                                $fullimg = $GLOBALS["default"]->weburl.'gem/ore/grupo/files/dumb/'.$media;
                                $media = 'gem/ore/grupo/files/preview/'.$media;
                                if (!file_exists($media)) {
                                    unset($sharedmedia[$key]);
                                } else {
                                    $sharedmedia[$key] = $GLOBALS["default"]->weburl.$media.'||'.$fullimg;
                                }
                            }
                            $list[$i]->sharedmedia = implode(';', $sharedmedia);
                        }
                    }
                } else {
                    $list[$i]->name = $GLOBALS["lang"]->unknown;
                }
                $tms = new DateTime($r[0]['tms']);
            }
        } else {
            $query = 'SELECT us.subs,us.name AS uname,us.altered,us.role,';
            $query = $query.'(SELECT count(1) FROM gr_msgs lk WHERE lk.type="like" AND lk.xtra=:uid) AS likes,';
            $query = $query.'(SELECT name FROM gr_permissions rn WHERE rn.id=us.role) AS rolename,';
            $query = $query.'(SELECT count(id) FROM gr_options WHERE type="pblock" AND v1=:usid AND v2=:uid) AS blocked,';
            $query = $query.'(SELECT count(1) FROM gr_msgs sc WHERE sc.type="file" AND sc.uid=:uid) AS scount,';
            $query = $query.'(SELECT IFNULL((SELECT CASE WHEN tz.v2="Auto" THEN ';
            $query = $query.'(SELECT am.v2 FROM gr_options am WHERE am.type="profile" AND am.v1="autotmz" AND am.v3=tz.v3)';
            $query = $query.' ELSE tz.v2 END AS timz FROM gr_options tz WHERE tz.type="profile" AND tz.v1="tmz" AND tz.v3=:usid),';
            $query = $query.':tmz)) AS timezone,';
            $query = $query.'(SELECT tms FROM gr_utrack WHERE uid=:uid ORDER BY tms DESC LIMIT 1) AS lastlg,';
            $query = $query.'(SELECT v2 FROM gr_options WHERE v3=:uid AND type="profile" AND v1="name") AS name';
            $query = $query.' FROM gr_users us WHERE us.id=:uid LIMIT 1';
            $data = array();
            $data['uid'] = $do["id"];
            $data['usid'] = $uid;
            $data['tmz'] = $GLOBALS["default"]->timezone;
            $r = db('Grupo', 'q', $query, $data);
            if (isset($r[0])) {
                $list[$i]->shares = gr_shnum($r[0]['scount']);
                $list[$i]->loves = gr_shnum($r[0]['likes']);
                $list[$i]->mna = html_entity_decode($GLOBALS["lang"]->hearts);
                $list[$i]->mnb = html_entity_decode($GLOBALS["lang"]->shares);
                $list[$i]->mnc = html_entity_decode($GLOBALS["lang"]->last_login);
                $list[$i]->rolename = $r[0]['rolename'];
                $list[$i]->roleimg = gr_img('roles', $r[0]['role']);
                $list[$i]->img = gr_img('users', $do['id']);
                $list[$i]->cp = gr_img('coverpic/users', $do['id']);
                $list[$i]->subs = $r[0]['subs'];
                if (isset($GLOBALS["roles"]['users'][2]) && $do['id'] != $uid && $r[0]['uname'] != $unq) {
                    if (isset($r[0]['uname'])) {
                        $list[$i]->icon = 1;
                        $list[$i]->iconattr = array();
                        $list[$i]->iconattr['data-no'] = $list[$i]->iconattr['xtid'] = $do['id'];
                        $list[$i]->iconattr['do'] = 'edit';
                        $list[$i]->iconattr['act'] = 'profile';
                        $list[$i]->icontitle = $GLOBALS["lang"]->edit_profile;
                        $list[$i]->iconattr['title'] = $GLOBALS["lang"]->edit_profile;
                        $list[$i]->iconattr['btn'] = $GLOBALS["lang"]->edit_profile;
                        $list[$i]->iconclass = 'gi-pencil-1 formpop';
                    }
                } else if ($do['id'] != $uid) {
                    $list[$i]->icon = 1;
                    $list[$i]->iconattr = array();
                    $list[$i]->iconattr['pn'] = 4;
                    $list[$i]->iconattr['no'] = $do['id'];
                    $list[$i]->iconattr['do'] = 'profile';
                    $list[$i]->iconattr['act'] = 'block';
                    if ($r[0]['blocked'] > 0) {
                        $list[$i]->icontitle = $GLOBALS["lang"]->unblock_user;
                        $list[$i]->iconattr['title'] = $GLOBALS["lang"]->unblock_user;
                        $list[$i]->iconattr['btn'] = $GLOBALS["lang"]->unblock_user;
                        $list[$i]->iconclass = 'gi-lock-open-1 formpop';
                    } else {
                        $list[$i]->icontitle = $GLOBALS["lang"]->block_user;
                        $list[$i]->iconattr['title'] = $GLOBALS["lang"]->block_user;
                        $list[$i]->iconattr['btn'] = $GLOBALS["lang"]->block_user;
                        $list[$i]->iconclass = 'gi-lock-1 formpop';
                    }
                }
                if (!isset($GLOBALS["roles"]['privatemsg'][1]) || !isset($r[0]['uname'])) {
                    $list[$i]->msgoff = 2;
                    $list[$i]->msgoffmsg = $GLOBALS["lang"]->denied;
                    if (!isset($r[0]['uname'])) {
                        $list[$i]->msgoffmsg = $GLOBALS["lang"]->profile_noexists;
                    }
                    $list[$i]->tbclass = 'say';
                    $list[$i]->btn = $GLOBALS["lang"]->message;
                    $list[$i]->tbattr = 'type="e" say="'.$list[$i]->msgoffmsg.'"';

                }
                if ($do['id'] == $uid) {
                    $list[$i]->msgoff = 2;
                    $list[$i]->tbclass = 'editprf';
                    $list[$i]->tbattr = 'type="editprofile"';
                    $list[$i]->btn = $GLOBALS["lang"]->edit_profile;
                }
                if (isset($GLOBALS["roles"]['users'][10])) {
                    $list[$i]->name = $r[0]['name'];
                } else {
                    $list[$i]->name = $r[0]['uname'];
                }
                if (isset($r[0]['uname']) && !empty($r[0]['uname'])) {
                    $usrname = $r[0]['uname'];
                    $list[$i]->uname = '@'.$r[0]['uname'];
                } else {
                    $usrname = 0;
                    $list[$i]->uname = $GLOBALS["lang"]->unknown;
                }
                if (!empty($r[0]['lastlg'])) {
                    $tms = new DateTime($r[0]['lastlg']);
                } else if (isset($r[0]['uname'])) {
                    $tms = new DateTime($r[0]['altered']);
                } else {
                    $tms = new DateTime();
                }
            }
        }
        if (isset($r[0])) {
            $tmz = new DateTimeZone($r[0]['timezone']);
            $tms->setTimezone($tmz);
            $tmst = strtotime($tms->format('Y-m-d H:i:s'));
            if ($GLOBALS["default"]->dateformat == 'mdy') {
                $dformat = 'M-d-y';
            } else if ($GLOBALS["default"]->dateformat == 'ymd') {
                $dformat = 'y-M-d';
            } else {
                $dformat = 'd-M-y';
            }
            $list[$i]->lastlg = $tms->format($dformat);
            if ($GLOBALS["default"]->time_format == 24) {
                $list[$i]->lastlgtm = $tms->format('H:i:s');
            } else {
                $list[$i]->lastlgtm = $tms->format('h:i:s a');
            }
            $cfield = 'field';
            $fieldtyp = 'profile';
            if (isset($do['ldt']) && $do['ldt'] == 'group') {
                $cfield = 'gfield';
                $fieldtyp = 'group';
            }
            $query = 'SELECT ds.name as name,ds.v1 as val,ds.type as cat FROM gr_profiles ds WHERE ds.type="group" AND ds.uid=:fid AND ds.name="description"';
            $query = $query.' UNION SELECT pr.name,vl.v1,pr.cat';
            $query = $query.' FROM gr_profiles pr,gr_profiles vl WHERE vl.uid=:fid';
            $query = $query.' AND vl.type=:ftype AND vl.name=pr.id AND pr.type=:stype';
            $data = array();
            $data['fid'] = $do["id"];
            $data['ftype'] = $fieldtyp;
            $data['stype'] = $cfield;
            $data['tmz'] = $GLOBALS["default"]->timezone;
            $fields = db('Grupo', 'q', $query, $data);
            if (isset($do['ldt']) && $do['ldt'] == 'group') {
                if ($r[0]['v3'] != 'secret' || $r[0]['grrole'] == 1 || $r[0]['grrole'] == 2 || isset($GLOBALS["roles"]['groups']['7'])) {
                    $list['viewlink'] = new stdClass();
                    $list['viewlink']-> name = $GLOBALS["lang"]->group_link;
                    if (isset($r[0]['groupslug']) && !empty($r[0]['groupslug'])) {
                        $list['viewlink']-> cont = $GLOBALS["default"]->weburl.'chat/'.$r[0]['groupslug'].'/';
                    } else {
                        $list['viewlink']-> cont = $GLOBALS["default"]->weburl.'chat/group/'.$do['id'].'/';
                    }
                }
            } else if (!empty($usrname)) {
                $list['viewlink'] = new stdClass();
                $list['viewlink']-> name = $GLOBALS["lang"]->profile_link;
                $list['viewlink']-> cont = $GLOBALS["default"]->weburl.'chat/'.$usrname.'/';
            }
            foreach ($fields as $f) {
                $pf = $f['name'];
                $vpf = html_entity_decode($f['val']);
                $list[$pf] = new stdClass();
                $list[$pf]-> cont = $vpf;
                if ($f['name'] == 'description' && isset($do['ldt']) && $do['ldt'] == 'group') {
                    $list[$pf]-> name = $GLOBALS["lang"]->description;
                } else {
                    if ($f['name'] != 'description') {
                        $varky = $f['name'];
                        $list[$pf]-> name = $GLOBALS["lang"]->$varky;
                        if ($f['cat'] == 'datefield') {
                            if ($GLOBALS["default"]->dateformat == 'mdy') {
                                $dformat = 'M-d-y';
                            } else if ($GLOBALS["default"]->dateformat == 'ymd') {
                                $dformat = 'y-M-d';
                            } else {
                                $dformat = 'd-M-y';
                            }
                            $list[$pf]-> cont = date($dformat, strtotime($list[$pf]-> cont));
                        }
                    } else {
                        unset($list[$pf]);
                    }
                }
            }
            if (isset($do['ldt']) && $do['ldt'] == 'group') {
                if ($r[0]['grrole'] == 1 || $r[0]['grrole'] == 2 || isset($GLOBALS["roles"]['groups']['7'])) {
                    $list['embedcode'] = new stdClass();
                    $list['embedcode']-> name = $GLOBALS["lang"]->embed_code;
                    $list['embedcode']-> cont = $list['viewlink']-> cont;
                }
            } else if ($do['id'] == $uid || isset($GLOBALS["roles"]['users']['2'])) {
                $list['embedcode'] = new stdClass();
                $list['embedcode']-> name = $GLOBALS["lang"]->embed_code;
                $list['embedcode']-> cont = $list['viewlink']-> cont;
            }
        }
    } else if ($do["type"] === "memsearch") {
        $i = 0;
        unset($list[0]);
        if (!empty($do['ser']) || empty($do['ser']) && isset($do['ulist'])) {
            $query = 'SELECT us.id,us.v1,op.v2 AS name,us.v3,us.v2,';
            $query = $query.'un.name as uname';
            $query = $query.' FROM gr_users un INNER JOIN gr_options us ON un.id=us.v2,gr_options op WHERE ';
            $query = $query.'us.v2=op.v3 AND op.type="profile" AND us.v2<>:uid AND op.v1="name" AND ';
            $query = $query.'us.type="gruser" AND us.v1=:gid ';
            $query = $query.'AND op.v2 LIKE :search OR us.v2=op.v3 AND op.type="profile" AND us.v2<>:uid AND ';
            $query = $query.'op.v1="name" AND us.type="gruser" AND un.name LIKE :search AND us.v1=:gid ';
            $query = $query.'ORDER BY op.v2 ASC LIMIT 4';
            $data = array();
            $data['gid'] = $do["gid"];
            $data['uid'] = $uid;
            $data['search'] = '%'.$do['ser'].'%';
            $rs = db('Grupo', 'q', $query, $data);
            foreach ($rs as $f) {
                $list[$i] = new stdClass();
                $list[$i]->img = gr_img('users', $f['v2']);
                if (isset($GLOBALS["roles"]['users'][10])) {
                    $list[$i]->name = $f['name'];
                } else {
                    $list[$i]->name = $f['uname'];
                }
                $list[$i]->id = $f['v2'];
                $list[$i]->uname = $f['uname'];
                $i = $i+1;
            }
        }
    }
    else if ($do["type"] === "lastSenders") {
        if ($ofs>0) {
            $r = json_encode($list);
            gr_prnt($r);
            exit;
        }
        $i = 0;
        unset($list[0]);
            $query = 'SELECT uid,(-1*sum(gr_credit_used.credit)) as total FROM `gr_credit_used` WHERE gr_credit_used.credit < 0 and gr_credit_used.used_time > DATE_SUB(CURDATE(), INTERVAL 1 DAY) GROUP BY uid ORDER BY total DESC LIMIT 10';
            $data = array();
            $rs = db('Grupo', 'q', $query, $data);

            foreach ($rs as $f) {
                $list[$i] = new stdClass();


                $query = "SELECT gr_options.v2 as uname,(SELECT gr_users.name FROM gr_users WHERE gr_users.id = gr_options.v3) as name FROM gr_options WHERE gr_options.v3 = ".$f['uid']." AND gr_options.type = 'profile' AND gr_options.v1 = 'name'";
               
                $data = array();
                $u1 = db('Grupo', 'q', $query, $data);


                $list[$i]->img = gr_img('users', $f['uid']);
                $list[$i]->sub = '@'.$u1[0]['name'];
                $list[$i]->name = ($i).'. '.$u1[0]['uname'];
                $list[$i]->count = $f['total'];
                $list[$i]->countag = 'Credits Used';
                $list[$i]->id = $f['uid'];
                $list[$i]->uname = $u1[0]['uname'];
                $list[$i]->oa = 0;
                $list[$i]->ob = 0;
                $list[$i]->oc = $GLOBALS["lang"]->view;
                $list[$i]->oct = 'class="vwp" no="'.$f['uid'].'"';
                $list[$i]->od = $GLOBALS["lang"]->chat;
                $list[$i]->odt = 'class="loadgroup paj" ldt="user" no="'.$f['uid'].'"';


                $i = $i+1;
            }
    } else if ($do["type"] === "mostPopuler") {

        if ($ofs>0) {
            $r = json_encode($list);
            gr_prnt($r);
            exit;
        }
        $i = 0;
        unset($list[0]);
            $query = 'SELECT uid,(-1*sum(gr_credit_used.credit)) as total FROM `gr_credit_used` WHERE gr_credit_used.credit < 0 and gr_credit_used.used_time > DATE_SUB(CURDATE(), INTERVAL 180 DAY) GROUP BY uid ORDER BY total DESC LIMIT 10';
            $data = array();
            $rs = db('Grupo', 'q', $query, $data);

            foreach ($rs as $f) {
                $list[$i] = new stdClass();


                $query = "SELECT gr_options.v2 as uname,(SELECT gr_users.name FROM gr_users WHERE gr_users.id = gr_options.v3) as name FROM gr_options WHERE gr_options.v3 = ".$f['uid']." AND gr_options.type = 'profile' AND gr_options.v1 = 'name'";
               
                $data = array();
                $u1 = db('Grupo', 'q', $query, $data);


                $list[$i]->img = gr_img('users', $f['uid']);
                $list[$i]->sub = '@'.$u1[0]['name'];
                $list[$i]->name = ($i).'. '.$u1[0]['uname'];
                $list[$i]->count = $f['total'];
                $list[$i]->countag = 'Credits Used';
                $list[$i]->id = $f['uid'];
                $list[$i]->uname = $u1[0]['uname'];
                $list[$i]->oa = 0;
                $list[$i]->ob = 0;
                $list[$i]->oc = $GLOBALS["lang"]->view;
                $list[$i]->oct = 'class="vwp" no="'.$f['uid'].'"';
                $list[$i]->od = $GLOBALS["lang"]->chat;
                $list[$i]->odt = 'class="loadgroup paj" ldt="user" no="'.$f['uid'].'"';


                $i = $i+1;
            }
    }




    $r = json_encode($list);
    gr_prnt($r);
}
?>