<?php if(!defined('s7V9pz')) {die();}?><?php
error_reporting(0);
fc('grupo');
$main = pg('connect');
$act = explode('/', $main);
$key = $GLOBALS["default"]->grconnect_secretkey;
$admin = 1;
$m = explode('/', pg('connect'));
$res = array();
$res['result'] = false;
$post = get();
if (isset($m[0]) && $m[0] == 'login') {
    $goto = '';
    if (isset($_GET['goto'])) {
        $goto = urldecode($_GET['goto']);
    }
    if (isset($m[1]) && isset($m[2])) {
        $login = db('Grupo', 's,device', 'session', 'id,code', $m[1], $m[2]);
        if (count($login) != 0) {
            $d = 'Grupo';
            $ctimer = 0;
            if (!empty($GLOBALS["default"]->login_cookie_validity)) {
                $ctimer = time() + (86400 * $GLOBALS["default"]->login_cookie_validity);
            }
            addcookie($d.'usrdev', $login[0]['device'], $ctimer, "/");
            addcookie($d.'usrcode', $m[2], $ctimer, "/");
            addcookie($d.'usrses', $m[1], $ctimer, "/");
        }
    }
    rt($goto);
} else if (isset($post['do'])) {
    if (isset($post['key']) && $post['key'] === $key) {
        if ($post['do'] === 'createuser') {
            $role = 3;
            if (isset($post["role"])) {
                $role = $post["role"];
            }
            $reg = usr('Grupo', 'register,nonlatin', $post["user"], $post["email"], $post["pass"], $role);
            if ($reg[0]) {
                $res['userid'] = $reg[1];
                if (!isset($post['name']) || empty($post['name'])) {
                    $post['name'] = $post["user"];
                }
                gr_data('i', 'profile', 'name', $post["name"], $reg[1], $post["user"], gr_usrcolor());
                $cfields = db('Grupo', 's', 'profiles', 'type', 'field');
                foreach ($cfields as $field) {
                    $pf = $field['name'];
                    if (isset($post[$pf]) && !empty($post[$pf])) {
                        if ($field['cat'] == 'datefield') {
                            $post[$pf] = vc($post[$pf], 'date', 'Y-m-d');
                        } else if ($field['cat'] == 'numfield') {
                            $post[$pf] = vc($post, 'num');
                        } else if ($field['cat'] == 'dropdownfield') {
                            $selc = explode(",", $field['v1']);
                            if (!in_array($post[$pf], $selc)) {
                                $post[$pf] = null;
                            }
                        } else {
                            $post[$pf] = $post[$pf];
                        }
                        if (!empty($post[$pf])) {
                            $ct = db('Grupo', 's,count(*)', 'profiles', 'type,name,uid', 'profile', $field['id'], $reg[1])[0][0];
                            if ($ct == 0) {
                                db('Grupo', 'i', 'profiles', 'type,name,uid,v1', 'profile', $field['id'], $reg[1], $post[$pf]);
                            } else {
                                db('Grupo', 'u', 'profiles', 'v1', 'type,name,uid', $post[$pf], 'profile', $field['id'], $reg[1]);
                            }
                        }
                    }
                }
                if (!empty($post["avatar"])) {
                    $avatar = 'gem/ore/grupo/users/'.$reg[1].'-gr-'.rn(10).'.png';
                    $ch = curl_init($_POST["avatar"]);
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
                    if ($cr[0]) {
                        gr_data('i', 'gruser', $grjoin, $res['userid'], 0);
                        $dt = array();
                        $dt['id'] = $grjoin;
                        $dt['msg'] = 'joined_group';
                        gr_group('sendmsg', $dt, 1, 1, $res['userid']);
                    }
                }
                $res['result'] = true;
            } else {
                $res['error'] = $reg[1];
            }
        } else if ($post['do'] === 'edituser') {
            $role = 3;
            if (isset($post["user"]) && !empty($post["user"])) {
                $uid = usr('Grupo', 'select', $post["user"]);
                if (isset($uid['id'])) {
                    if (isset($post['changename'])) {
                        $post['changename'] = vc($post['changename'], 'strip');
                        if (!empty($post['changename'])) {
                            db('Grupo', 'u', 'options', 'v2', 'type,v1,v3', $post['changename'], 'profile', 'name', $uid['id']);
                        }
                    }
                    if (isset($post['changeusername'])) {
                        if (!empty($post['changeusername'])) {
                            $post['changeuser'] = $post['changeusername'];
                        }
                    }
                    if (isset($post['changeuser'])) {
                        if (!empty($post['changeuser'])) {
                            if (preg_match('/[A-Za-z]/', $_POST['changeuser']) || preg_match('/[^\\p{Common}\\p{Latin}]/u', $_POST['changeuser'])) {
                                usr('Grupo', 'alter,nonlatin', 'name', $_POST['changeuser'], $uid['id']);
                            }
                        }
                    }
                    if (isset($post['changeemail'])) {
                        if (!empty($post['changeemail'])) {
                            usr('Grupo', 'alter', 'email', $_POST['changeemail'], $uid['id']);
                        }
                    }
                    if (isset($post['changepass'])) {
                        if (!empty($post['changepass'])) {
                            usr('Grupo', 'alter', 'pass', $_POST['changepass'], $uid['id']);
                        }
                    }
                    $cfields = db('Grupo', 's', 'profiles', 'type', 'field');
                    foreach ($cfields as $field) {
                        $pf = $field['name'];
                        if (isset($post[$pf]) && !empty($post[$pf])) {
                            if ($field['cat'] == 'datefield') {
                                $post[$pf] = vc($post[$pf], 'date', 'Y-m-d');
                            } else if ($field['cat'] == 'numfield') {
                                $post[$pf] = vc($post, 'num');
                            } else if ($field['cat'] == 'dropdownfield') {
                                $selc = explode(",", $field['v1']);
                                if (!in_array($post[$pf], $selc)) {
                                    $post[$pf] = null;
                                }
                            } else {
                                $post[$pf] = $post[$pf];
                            }
                            if (!empty($post[$pf])) {
                                $ct = db('Grupo', 's,count(*)', 'profiles', 'type,name,uid', 'profile', $field['id'], $uid['id'])[0][0];
                                if ($ct == 0) {
                                    db('Grupo', 'i', 'profiles', 'type,name,uid,v1', 'profile', $field['id'], $uid['id'], $post[$pf]);
                                } else {
                                    db('Grupo', 'u', 'profiles', 'v1', 'type,name,uid', $post[$pf], 'profile', $field['id'], $uid['id']);
                                }
                            }
                        }
                    }

                    if (isset($post['changeavatar'])) {
                        if (!empty($post['changeavatar'])) {
                            $avatar = 'gem/ore/grupo/users/'.$uid['id'].'-gr-'.rn(10).'.png';
                            $ch = curl_init($_POST["changeavatar"]);
                            $fp = fopen($avatar, 'wb');
                            curl_setopt($ch, CURLOPT_FILE, $fp);
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                            curl_setopt($ch, CURLOPT_ENCODING, '');
                            curl_exec($ch);
                            curl_close($ch);
                            fclose($fp);
                        }
                    }
                    if (isset($post['changerole'])) {
                        if (!empty($post['changerole'])) {
                            usr('Grupo', 'alter', 'role', $_POST['changerole'], $uid['id']);
                        }
                    }
                    $res['result'] = true;
                } else {
                    $res['error'] = 'invalid user';
                }
            } else {
                $res['error'] = 'invalid user';
            }
        } else if ($post['do'] === 'deleteuser') {
            if (isset($post["user"]) && !empty($post["user"])) {
                $uid = usr('Grupo', 'select', $post["user"]);
                if (isset($uid['id'])) {
                    $res['result'] = true;
                    $query = 'DELETE FROM gr_msgs WHERE ';
                    $query = $query.'gid LIKE "'.$uid['id'].'-%" AND cat="user" OR gid LIKE "%-'.$uid['id'].'" AND cat="user"';
                    foreach (glob("gem/ore/grupo/users/".$uid['id']."-gr-*.*") as $filename) {
                        unlink($filename);
                    }
                    foreach (glob("gem/ore/grupo/coverpic/users/".$uid['id']."-gr-*.*") as $filename) {
                        unlink($filename);
                    }
                    foreach (glob("gem/ore/grupo/audiomsgs/".$uid['id']."-gr-*.*") as $filename) {
                        unlink($filename);
                    }
                    flr('delete', 'grupo/files/'.$uid['id']);
                    $query = $query.';DELETE FROM gr_options WHERE type="profile" AND v3 IN('.$uid['id'].');';
                    $query = $query.'DELETE FROM gr_options WHERE type="lview" AND v2 IN('.$uid['id'].');';
                    $query = $query.'DELETE FROM gr_options WHERE type="gruser" AND v2 IN('.$uid['id'].');';
                    $query = $query.'DELETE FROM gr_msgs WHERE uid IN('.$uid['id'].');';
                    $query = $query.'DELETE FROM gr_alerts WHERE uid IN('.$uid['id'].');';
                    $query = $query.'DELETE FROM gr_profiles WHERE type="profile" AND uid IN('.$uid['id'].');';
                    $query = $query.'DELETE FROM gr_alerts WHERE v3 IN('.$uid['id'].');';
                    $query = $query.'DELETE FROM gr_options WHERE type="deaccount" AND v3 IN('.$uid['id'].');';
                    $query = $query.'DELETE FROM gr_users WHERE id IN('.$uid['id'].');';
                    $query = $query.'DELETE FROM gr_session WHERE uid IN('.$uid['id'].');';
                    $query = $query.'UPDATE gr_logs SET v1="'.strtotime(dt()).'" WHERE type="cache";';
                    $rq = db('Grupo', 'q', $query);
                } else {
                    $res['error'] = 'invalid user';
                }
            } else {
                $res['error'] = 'invalid user';
            }
        } else if ($post['do'] === 'login') {
            if (isset($post["user"]) && !empty($post["user"])) {
                $uid = usr('Grupo', 'select', $post["user"]);
                if (isset($uid['id'])) {
                    $login = usr('Grupo', 'forcelogin', $post["user"]);
                    $res['device'] = $login['sesdev'];
                    $res['sessioncode'] = $login['sescode'];
                    $res['sessionid'] = $login['sesid'];
                    $res['loginlink'] = url().'connect/login/'.$res['sessionid'].'/'.$res['sessioncode'].'/';
                    if (!empty($post['redirect'])) {
                        $res['loginlink'] = $res['loginlink'].'?goto='.urldecode($post['redirect']);
                    }
                    $res['result'] = true;
                } else {
                    $res['error'] = 'invalid user';
                }
            } else {
                $res['error'] = 'invalid user';
            }
        } else if ($post['do'] === 'logout') {
            if (isset($post["user"]) && !empty($post["user"])) {
                $uid = usr('Grupo', 'select', $post["user"]);
                if (isset($uid['id'])) {
                    gr_profile('ustatus', 'offline', $uid['id']);
                    usr('Grupo', 'forcelogout', $uid['id']);
                    $res['result'] = true;
                } else {
                    $res['error'] = 'invalid user';
                }
            } else {
                $res['error'] = 'invalid user';
            }
        } else if ($post['do'] === 'getinfo') {
            if (isset($post["user"])) {
                $select = usr('Grupo', 'select', $post["user"]);
                if (isset($select['name'])) {
                    $res['result'] = true;
                    $res['username'] = $select['name'];
                    $res['password'] = $select['pass'];
                    $res['email'] = $select['email'];
                    $res['roleid'] = $select['role'];
                    $res['url'] = url().'chat/'.$select['name'];
                    $query = 'SELECT us.name AS uname,us.altered,us.role,';
                    $query = $query.'(SELECT name FROM gr_permissions rn WHERE rn.id=us.role) AS rolename,';
                    $query = $query.'(SELECT tms FROM gr_utrack WHERE uid=:uid ORDER BY tms DESC LIMIT 1) AS lastlg,';
                    $query = $query.'(SELECT v2 FROM gr_options WHERE v3=:uid AND type="profile" AND v1="name") AS name';
                    $query = $query.' FROM gr_users us WHERE us.id=:uid LIMIT 1';
                    $data = array();
                    $data['uid'] = $select['id'];
                    $userinfo = db('Grupo', 'q', $query, $data);
                    if (isset($userinfo[0])) {
                        $res['name'] = $userinfo[0]['name'];
                        $res['role'] = $userinfo[0]['rolename'];
                        $res['lastlogin'] = $userinfo[0]['lastlg'];
                    }
                    $query = 'SELECT ds.name as name,ds.v1 as val,ds.type as cat FROM gr_profiles ds WHERE ds.type="group" AND ds.uid=:fid AND ds.name="description"';
                    $query = $query.' UNION SELECT pr.name,vl.v1,pr.cat';
                    $query = $query.' FROM gr_profiles pr,gr_profiles vl WHERE vl.uid=:fid';
                    $query = $query.' AND vl.type=:ftype AND vl.name=pr.id AND pr.type=:stype';
                    $data = array();
                    $data['fid'] = $select['id'];
                    $data['ftype'] = 'profile';
                    $data['stype'] = 'field';
                    $fields = db('Grupo', 'q', $query, $data);
                    foreach ($fields as $f) {
                        if ($f['name'] != 'description') {
                            $pf = $f['name'];
                            $vpf = html_entity_decode($f['val']);
                            $res[$pf] = $vpf;
                        }
                    }
                    $res['link'] = $GLOBALS["default"]->weburl.'chat/'.$select['name'].'/';
                    $res['avatar'] = gr_img('users', $select['id']);
                    $res['coverpic'] = gr_img('coverpic/users', $select['id']);
                } else {
                    $res['error'] = 'invalid user';
                }
            } else {
                $res['error'] = 'invalid user';
            }
        } else if ($post['do'] === 'creategroup') {
            if (isset($post["name"]) && !empty($post["name"])) {
                $group = db('Grupo', 's,count(*)', 'options', 'type,v1', 'group', strtolower($post['name']))[0][0];
                if ($group == 0) {
                    if (empty($post['password'])) {
                        $post['password'] = 0;
                    } else {
                        $post['password'] = md5($post['password']);
                    }
                    if ($post['visibility'] == FALSE) {
                        $post['visibility'] = 'secret';
                    } else {
                        $post['visibility'] = 0;
                    }
                    $res['groupid'] = db('Grupo', 'i', 'options', 'type,v1,v2,v3,v4,v5,v6,tms', 'group', $post['name'], $post['password'], $post['visibility'], rn(6), $post['sendpermission'], $post['unleavable'], dt());
                    $res['result'] = true;
                } else {
                    $res['error'] = 'group exists';
                }
            } else {
                $res['error'] = 'invalid group name';
            }
        } else if ($post['do'] === 'joingroup') {
            if (isset($post["groupid"]) && !empty($post["groupid"])) {
                $group = gr_group('valid', $post['groupid']);
                if ($group[0]) {
                    $uid = usr('Grupo', 'select', $post['userid']);
                    if (isset($uid['id'])) {
                        $post['userid'] = $uid['id'];
                        $usercheck = gr_group('user', $post['groupid'], $post['userid'])[0];
                        if (!$usercheck) {
                            $res['result'] = true;
                            gr_data('i', 'gruser', $post['groupid'], $post['userid'], $post['role']);
                            $dt = array();
                            $dt['id'] = $post['groupid'];
                            $dt['msg'] = 'joined_group';
                            gr_group('sendmsg', $dt, 1, 1, $post['userid']);
                        } else {
                            $res['error'] = 'already joined';
                        }
                    } else {
                        $res['error'] = 'invalid user';
                    }
                } else {
                    $res['error'] = 'group doesnt exists';
                }
            } else {
                $res['error'] = 'invalid group';
            }
        }
    } else {
        $res['error'] = 'invalid key';
    }
} else {
    $res['error'] = 'invalid request';
}
$r = json_encode($res);
gr_prnt($r);
?>