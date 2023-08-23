<?php if(!defined('s7V9pz')) {die();}?><?php
function usr() {
    $ars = func_get_args();
    $arg = vc($ars);
    $d = $arg[0];
    $ex = $t = 0;
    if (isset($arg[1])) {
        $t = $arg[1];
        if (strpos($t, ',') !== false) {
            $t = explode(",", $t);
            $ex = $t[1];
            $t = $t[0];
        }
    }
    $r = false;
    if ($t === 'register') {
        $rl = 1;
        $r[0] = false;
        if ($ex === 'nonlatin') {
            $ars[2] = preg_replace('/[^\pL\pM*+\pN._%+-]/u', '', $ars[2]);
            if (strpos($ars[2], ' ') !== false) {
                $i = 0;
            } else {
                $i = mb_strtolower($ars[2]);
            }
        } else {
            $i = strtolower(vc($arg[2], 'alphanum'));
        }
        $e = mb_strtolower(vc($arg[3], 'email'));
        $p = $arg[4];
        if (isset($arg[5])) {
            $rl = vc($arg[5], 'num');
        }
        if (!empty($d) && !empty($i) && !empty($e) && !empty($p)) {
            if (preg_match('/[A-Za-z]/', $i) || preg_match('/[^\\p{Common}\\p{Latin}]/u', $i)) {
                if (!usr($d, 'exist', $i)) {
                    if (!usr($d, 'exist', $e)) {
                        $p = en($p);
                        $r[1] = db($d, 'i', 'users', 'name,email,pass,mask,depict,role,created,altered', $i, $e, $p['pass'], $p['mask'], $p['type'], $rl, dt(), dt());
                        $r[0] = true;
                    } else {
                        $r[1] = 'emailexist';
                    }
                } else {
                    $r[1] = 'usernameexist';
                }
            } else {
                $r[1] = 'usernamecondition';
            }
        } else {
            $r[1] = 'invalid';
        }
        return $r;
    } else if ($t === 'sociallogin') {
        fc("socialconnect");
        $r[0] = false;
        $r['error'] = 'loggedin';
        $data = $arg[2];
        $config = [
            'callback' => $data['callback'],
            'keys' => [
                'id' => $data['appid'],
                'secret' => $data['appsecret']
            ],
        ];
        if (isset($data['appkey']) && !empty($data['appkey'])) {
            $config = [
                'callback' => $data['callback'],
                'keys' => [
                    'id' => $data['appid'],
                    'secret' => $data['appsecret'],
                    'key' => $data['appkey']
                ],
            ];
        }
        try {
            $provider = 'Hybridauth\Provider'.$data['provider'];
            $adapter = new $provider($config);
            $adapter->authenticate();
            $accessToken = $adapter->getAccessToken();
            $userProfile = $adapter->getUserProfile();
            if ($data['provider'] == '\Facebook') {
                $userProfile->photoURL .= '&access_token='.$accessToken['access_token'];
            }
            $r[4] = 'user'.$userProfile->identifier;
            if (usr($d, 'exist', $userProfile->email)) {
                usr($d, 'forcelogin', $userProfile->email);
                $r[1] = 'login';
            } else {
                $r[1] = 'register';
                $r[2] = usr('Grupo', 'register,nonlatin', $r[4], $userProfile->email, rn(6), 3)[1];
            }
            $r[0] = true;
            $r[3] = $userProfile;
        }
        catch (Hybridauth\Exception\HttpClientFailureException $e) {
            $r['error'] = $e;
            return $r;
        }
        catch (Hybridauth\Exception\HttpRequestFailedException $e) {
            $r['error'] = $e;
            return $r;
        }
        catch (\Exception $e) {
            $r['error'] = $e;
            return $r;
        }
        return $r;
    } else if ($t === 'suggest') {
        $ch = $rn = null;
        if (isset($arg[3])) {
            $rn = $arg[3];
        }
        $fl = rn($rn);
        if (isset($arg[2])) {
            $ch = strtolower(vc($arg[2], 'alphanum'));
            $s = $ch.$fl;
        } else {
            $s = $fl;
        }
        if (!usr($d, 'exist', $s)) {
            return $s;
        } else {
            usr($d, 'suggest', $ch, $rn);
        }
    } else if ($t === 'logout') {
        $v = usr($d);
        if (isset($v['id'])) {
            ses($d, 'del', $v['id']);
            return true;
        }
    } else if ($t === 'devicelogout') {
        $i = vc($arg[2], 'num');
        $dev = $arg[3];
        $r = db($d, 'd', 'session', 'uid, device, try', $i, $dev, 0);
        return $r;
    } else if ($t === 'forcelogout') {
        if (isset($arg[2])) {
            $i = vc($arg[2], 'num');
            if ($i != 0) {
                $r = db($d, 'd', 'session', 'uid, try', $i, 0);
            } else {
                $r = db($d, 'd', 'session', 'try', 0);
            }
        }
        return $r;
    } else if ($t === 'delete') {
        $i = vc($arg[2], 'num');
        $r = db($d, 'd', 'users', 'id', $i);
        $r = db($d, 'd', 'session', 'uid', $i);
        return $r;
    } else if ($t === 'unblock') {
        $id = vc($arg[2], 'num');
        $dev = 'bs.'.ip().$arg[3];
        $code = vc($arg[4]);
        $r = db($d, 'd', 'session', 'uid, device, code', $id, $dev, $code);
        return $r;
    } else if ($t === 'forgot') {
        $r[0] = false;
        $usr = usr($d, 'select', $arg[2]);
        if (!empty($usr['role'])) {
            $r[1] = rn(14);
            $r[0] = db($d, 'u', 'users', 'altered, extra', 'id', dt(), $r[1], $usr['id']);
        }
        return $r;
    } else if ($t === 'reset') {
        $r[0] = false;
        $code = vc($arg[3]);
        $usr = usr($d, 'select', $arg[2]);
        if (!empty($usr['role']) && $usr['extra'] === $code) {
            $r[1] = rn(7);
            $p = en($r[1]);
            $r[0] = db($d, 'u', 'users', 'pass, mask, depict, altered, extra', 'id', $p['pass'], $p['mask'], $p['type'], dt(), 0, $usr['id']);
        }
        return $r;
    } else if ($t === 'clear') {
        if (isset($arg[2])) {
            if ($arg[2] != 0) {
                $v = vc($arg[2], 'num');
                $r = db($d, 'd', 'session', 'uid', $v);
            } else {
                $r = db($d, 'd', 'session');
            }
        }
        return $r;
    } else if ($t === 'ban') {
        $usr = usr($d, 'select', $arg[2]);
        $o = $usr['role'];
        if (!empty($o)) {
            $r = db($d, 'u', 'users', 'role, altered, extra', 'id', 0, dt(), $o, $usr['id']);
        }
        return $r;
    } else if ($t === 'unban') {
        $usr = usr($d, 'select', $arg[2]);
        $o = $usr['extra'];
        $o = vc($o, 'num');
        if (empty($o)) {
            $o = 1;
        } if (empty($ol['role'])) {
            $r = db($d, 'u', 'users', 'role, altered', 'id', $o, dt(), $usr['id']);
        }
        return $r;
    } else if ($t === 'forcelogin') {
        $usr = usr($d, 'select', $arg[2]);
        if (isset($usr['id'])) {
            $i = $usr['id'];
            if (!empty($i) && !empty($usr['role'])) {
                $ctm = 0;
                if (isset($arg[3]) && !empty(vc($arg[3], 'num'))) {
                    $ctm = $arg[3];
                }
                $r = ses($d, 'add', $i, $ctm);
                $r['uid'] = $i;
                if (isset($_COOKIE[$d.'usrcode']) && isset($_COOKIE[$d.'usrses']) && !empty($_COOKIE[$d.'usrses'])) {
                    $r['code'] = $_COOKIE[$d.'usrcode'];
                    $r['dev'] = $_COOKIE[$d.'usrdev'];
                    $r['ses'] = $_COOKIE[$d.'usrses'];
                }
            }
        }
        return $r;
    } else if ($t === 'login') {
        $p = $arg[3];
        $u = mb_strtolower(vc($arg[2], 'email'));
        $f = 'email';
        $r[0] = false;
        $r[1] = 'invalid';
        if (empty($u)) {
            $f = 'name';
            if ($ex === 'nonlatin') {
                if (strpos($arg[2], ' ') !== false) {
                    $u = 0;
                } else {
                    $u = mb_strtolower($arg[2]);
                }
            } else {
                $u = mb_strtolower(vc($arg[2], 'alphanum'));
            }
        }
        if (isset($arg[4]) && !empty($arg[4]) && usr($d, 'exist', $u)) {
            $block = vc($arg[4], 'num');
            $uid = usr($d, 'select', $u)['id'];
            $bc = db($d, 's, try', 'session', 'uid, device', $uid, 'bs.'.ip().ip('dev'), 'ORDER BY id DESC LIMIT 1');
            if (count($bc) > 0) {
                $bc = $bc[0]['try'];
            } else {
                $bc = 0;
            }

            if ($bc < $block) {
                $bc = $bc+1;
                if ($bc === 1) {
                    db($d, 'i', 'session', 'uid, device, code, tms, try', $uid, 'bs.'.ip().ip('dev'), rn(20), dt(), $bc);
                } else {
                    db($d, 'u', 'session', 'try', 'uid, device', $bc, $uid, 'bs.'.ip().ip('dev'));
                }
            } else {
                $u = null;
                $r[1] = 'blocked';
            }
        }
        if (!empty($u)) {
            $kr = db($d, 's', 'users', $f, $u, 'ORDER BY id DESC LIMIT 1');
            if (count($kr) > 0) {
                $kr = $kr[0];
                $p = en($p, $kr['depict'], $kr['mask'])['pass'];
                if ($kr['pass'] === $p) {
                    db($d, 'd', 'session', 'uid, device', $kr['id'], 'bs.'.ip().ip('dev'));
                    if ($kr['role'] != '0') {
                        $ctm = 0;
                        if (isset($arg[5]) && !empty(vc($arg[5], 'num'))) {
                            $ctm = $arg[5];
                        }
                        ses($d, 'add', $kr['id'], $ctm);
                        $r['uid'] = $kr['id'];
                        if (isset($_COOKIE[$d.'usrcode']) && isset($_COOKIE[$d.'usrses']) && !empty($_COOKIE[$d.'usrses'])) {
                            $r['code'] = $_COOKIE[$d.'usrcode'];
                            $r['dev'] = $_COOKIE[$d.'usrdev'];
                            $r['ses'] = $_COOKIE[$d.'usrses'];
                        }
                        $r[0] = true;
                    } else {
                        $r[1] = 'banned';
                    }
                }}
        }
        return $r;

    } else if ($t === 'select') {
        $i = strtolower(vc($arg[2], 'num'));
        $f = 'id';
        if (empty($i)) {
            $f = 'email';
            $i = vc($arg[2], 'email');
        }
        if (empty($i)) {
            $f = 'name';
            $i = vc($arg[2]);
        }
        $r = db($d, 's', 'users', $f, $i, 'ORDER BY id DESC LIMIT 1');
        if (isset($r[0])) {
            $r = $r[0];
        }
        return $r;
    } else if ($t === 'exist') {
        $v = strtolower(vc($arg[2], 'num'));
        $sr = 'id';
        if (empty($v)) {
            $sr = 'email';
            $v = vc($arg[2], 'email');
        }
        if (empty($v)) {
            $sr = 'name';
            $v = vc($arg[2]);
        }
        $r = db($d, 's, count(*)', 'users', $sr, $v)[0][0];
        if ($r > 0) {
            return true;
        } else {
            return false;
        }
    } else if ($t === 'alter') {
        $tm = dt();
        $r = false;
        $ch = vc($arg[2]);
        $id = vc($arg[4], 'num');
        if (!empty($id)) {
            if ($ch === 'email') {
                $v = mb_strtolower(vc($arg[3], 'email'));
            } else if ($ch === 'name') {
                if ($ex === 'nonlatin') {
                    $ars[3] = preg_replace('/[^\pL\pM*+\pN._%+-]/u', '', $ars[3]);
                    if (strpos($ars[3], ' ') !== false) {
                        $v = 0;
                    } else {
                        $v = mb_strtolower($ars[3]);
                    }
                } else {
                    $v = strtolower(vc($arg[3], 'alphanum'));
                }
                if (!preg_match('/[A-Za-z]/', $v) && !preg_match('/[^\\p{Common}\\p{Latin}]/u', $v)) {
                    $v = 0;
                }
            } else if ($ch === 'role') {
                $v = strtolower(vc($arg[3], 'num'));
            }
            if ($ch === 'pass') {
                $v = $arg[3];
                if (!empty($v)) {
                    $p = en($v);
                    $r = db($d, 'u', 'users', 'pass, mask, depict, altered', 'id', $p['pass'], $p['mask'], $p['type'], $tm, $id);
                }
            } else if ($ch === 'email' || $ch === 'name' || $ch === 'role') {
                if (!empty($v)) {
                    if (!usr($d, 'exist', $v) || $ch === 'role') {
                        $r = db($d, 'u', 'users', $ch.', altered', 'id', $v, $tm, $id);
                    }
                }
            }
        }
        return $r;
    } else {
        return ses($d);
    }

}

function ses($d, $t = 0, $v = 0, $xt = 0) {
    $v = vc($v, 'num');
    if ($t === 'del') {
        if (!isset($_COOKIE[$d.'usrdev'])) {
            $_COOKIE[$d.'usrdev'] = $_COOKIE[$d.'usrcode'] = null;
        }
        $id = db($d, 's, id', 'session', 'uid, device, code', $v, $_COOKIE[$d.'usrdev'], $_COOKIE[$d.'usrcode']);
        if (isset($id[0]['id'])) {
            $id = $id[0]['id'];
        } else {
            $id = null;
        }
        db($d, 'd', 'session', 'uid, device, code', $v, $_COOKIE[$d.'usrdev'], $_COOKIE[$d.'usrcode']);
        if (isset($_COOKIE[$d.'usrses'])) {
            unset($_COOKIE[$d.'usrcode']);
            unset($_COOKIE[$d.'usrses']);
            unset($_COOKIE[$d.'usrdev']);
            addcookie($d.'usrses', '', time() - 3600, '/');
            addcookie($d.'usrcode', '', time() - 3600, '/');
            addcookie($d.'usrdev', '', time() - 3600, '/');
        }
        return $id;
    } else if ($t === 'add') {
        $xt = vc($xt, 'num');
        $ctimer = 0;
        $r[0] = true;
        $r['sesdev'] = $sesdev = ip().ip('dev');
        $r['sescode'] = $sescode = rn("5").rn("9");
        $r['sesid'] = db($d, 'i', 'session', 'uid, device, code, tms', $v, $sesdev, $sescode, dt());
        if (!empty($xt)) {
            $ctimer = time() + (86400 * $xt);
        }
        addcookie($d.'usrdev', $r['sesdev'], $ctimer, "/");
        addcookie($d.'usrcode', $r['sescode'], $ctimer, "/");
        addcookie($d.'usrses', $r['sesid'], $ctimer, "/");
        return $r;
    } else {
        $r['active'] = false;
        $r['id'] = 0;
        if (isset($_SESSION[$d.'usrses']) && isset($_SESSION[$d.'usrcode']) && isset($_SESSION[$d.'usrdev'])) {
            addcookie($d.'usrdev', $_SESSION[$d.'usrdev'], 0, "/");
            addcookie($d.'usrcode', $_SESSION[$d.'usrcode'], 0, "/");
            addcookie($d.'usrses', $_SESSION[$d.'usrses'], 0, "/");
            $_SESSION[$d.'usrses'] = $_SESSION[$d.'usrcode'] = $_SESSION[$d.'usrdev'] = null;
        }
        if (isset($_COOKIE[$d.'usrses']) && isset($_COOKIE[$d.'usrcode'])) {
            $c = db($d, 's, uid', 'session', 'id, device, code', $_COOKIE[$d.'usrses'], $_COOKIE[$d.'usrdev'], $_COOKIE[$d.'usrcode'], 'ORDER BY id DESC');
            if (count($c) > 0) {
                $r['active'] = true;
                $r['id'] = $c[0]['uid'];
            } else {
                if (isset($_COOKIE[$d.'usrses'])) {
                    unset($_COOKIE[$d.'usrcode']);
                    unset($_COOKIE[$d.'usrses']);
                    unset($_COOKIE[$d.'usrdev']);
                    addcookie($d.'usrses', '', time() - 3600, '/');
                    addcookie($d.'usrcode', '', time() - 3600, '/');
                    addcookie($d.'usrdev', '', time() - 3600, '/');
                }
            }
        }
        return $r;

    }
}

?>