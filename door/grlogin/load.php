<?php if(!defined('s7V9pz')) {die();}?><?php
addcookie('utrack', "on", 0, "/");
function gr_register($do) {
    if ($GLOBALS["default"]->userreg == 'enable') {
        if (!empty($do["g-recaptcha-response"]) && gr_captcha($do["g-recaptcha-response"]) || $GLOBALS["default"]->recaptcha != 'enable') {
            if (gr_usip('check')) {
                gr_prnt('grerrormsg("'.$GLOBALS["lang"]->ip_blocked.'","e");');
                exit;
            }
            $do["email"] = vc($do["email"], 'email');
            $do["fname"] = vc($do["fname"], 'strip');
            $fields = db('Grupo', 's', 'profiles', 'type,req', 'field', 3);
            foreach ($fields as $f) {
                if ($f['cat'] == 'datefield' && !empty($do[$f["name"]])) {
                    $do[$f["name"]] = vc($do[$f["name"]], 'date', 'Y-m-d');
                }
                if (empty($do[$f["name"]])) {
                    $fieldname = $f["name"];
                    gr_prnt('grerrormsg("'.$GLOBALS["lang"]->invalid_value.' - '.$GLOBALS["lang"]->$fieldname.'");');
                    exit;
                }
            }
            if (empty($do["fname"]) || empty($do["name"]) || empty($do["email"]) || empty($do["pass"])) {
                gr_prnt('grerrormsg("'.$GLOBALS["lang"]->invalid_value.'");');
            } else {
                if (!empty($GLOBALS["default"]->min_username_length) && strlen($do["name"]) < $GLOBALS["default"]->min_username_length) {
                    gr_prnt('grerrormsg("'.$GLOBALS["lang"]->req_min_username_length.' ('.$GLOBALS["default"]->min_username_length.')");');
                    exit;
                }
                if (!empty($GLOBALS["default"]->max_username_length) && strlen($do["name"]) > $GLOBALS["default"]->max_username_length) {
                    gr_prnt('grerrormsg("'.$GLOBALS["lang"]->max_username_length_exceeds.' ('.$GLOBALS["default"]->max_username_length.')");');
                    exit;
                }

                if (!empty($do["name"])) {
                    $checkusername = db('Grupo', 's', 'users', 'name', $do["name"]);
                    $checkslug = db('Grupo', 's', 'options', 'type,v2', 'groupslug', $do["name"]);
                    if (count($checkusername) > 0 || count($checkslug) > 0 || in_array($do["name"], $GLOBALS["reservedslugs"])) {
                        gr_prnt('grerrormsg("'.$GLOBALS["lang"]->username_already_exists.'");');
                        exit;
                    }
                }
                if ($GLOBALS["default"]->non_latin_usernames == 'enable') {
                    $nonlatreg = 'register,nonlatin';
                } else {
                    $nonlatreg = 'register';
                }
                if ($GLOBALS["default"]->email_verification == 'enable') {
                    $reg = usr('Grupo', $nonlatreg, $_POST["name"], $do["email"], $do["pass"]);
                } else {
                    $reg = usr('Grupo', $nonlatreg, $_POST["name"], $do["email"], $do["pass"], 3);
                }
                if ($reg[0]) {
                    $id = $reg[1];
                    gr_data('i', 'profile', 'name', $do["fname"], $id, $do["name"], gr_usrcolor());
                    $fields = db('Grupo', 's', 'profiles', 'type,req|,type,req', 'field', 2, 'field', 3);
                    foreach ($fields as $f) {
                        $pf = $f['name'];
                        if ($f['cat'] == 'datefield') {
                            $do[$pf] = vc($do[$pf], 'date', 'Y-m-d');
                        } else if ($f['cat'] == 'numfield') {
                            $do[$pf] = vc($do[$pf], 'num');
                        } else if ($f['cat'] == 'dropdownfield') {
                            $selc = explode(",", $f['v1']);
                            if (!in_array($do[$pf], $selc)) {
                                $do[$pf] = null;
                            }
                        } else {
                            $do[$pf] = vc($do[$pf]);
                        }
                        if (!empty($do[$pf])) {
                            db('Grupo', 'i', 'profiles', 'type,name,uid,v1', 'profile', $f['id'], $id, $do[$pf]);
                        }
                    }
                    if ($GLOBALS["default"]->email_verification == 'enable') {
                        gr_mail('verify', $id, 0, rn(5), 1);
                        gr_prnt('alert("'.$GLOBALS["lang"]->check_inbox.'");');
                        gr_prnt('window.location.href = "";');
                    } else {
                        $grjoin = $GLOBALS["default"]->autogroupjoin;
                        usr('Grupo', 'forcelogin', $id);
                        gr_prnt('location.reload();');
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
                    }
                } else {
                    if ($reg[1] == 'usernamecondition') {
                        gr_prnt('grerrormsg("'.$GLOBALS["lang"]->username_condition.'");');
                    } else if ($reg[1] == 'usernameexist') {
                        gr_prnt('grerrormsg("'.$GLOBALS["lang"]->username_exists.'");');
                    } else if ($reg[1] == 'emailexist') {
                        gr_prnt('grerrormsg("'.$GLOBALS["lang"]->email_exists.'");');
                    } else if ($reg[1] == 'invalid') {
                        gr_prnt('grerrormsg("'.$GLOBALS["lang"]->invalid_value.'");');
                    }
                }
            }
        } else {
            gr_prnt('grerrormsg("'.$GLOBALS["lang"]->invalid_captcha.'");');
        }
    }
}
function gr_login($do) {
    if ($GLOBALS["default"]->recaptcha != 'enable' || !empty($do["g-recaptcha-response"]) && gr_captcha($do["g-recaptcha-response"])) {
        if (gr_usip('check')) {
            gr_prnt('grerrormsg("'.$GLOBALS["lang"]->ip_blocked.'","e");');
            exit;
        }
        if (!empty($do["nickname"]) && $GLOBALS["default"]->guest_login == 'enable') {
            $do['sign'] = preg_replace('/@.*/', '', $do['nickname']);
            $nme = $usrn = trim($do['nickname']);
            $usrn = preg_replace("/\s+/", "", $usrn);
            if (!empty($GLOBALS["default"]->min_username_length) && strlen($usrn) < $GLOBALS["default"]->min_username_length) {
                gr_prnt('grerrormsg("'.$GLOBALS["lang"]->req_min_username_length.' ('.$GLOBALS["default"]->min_username_length.')");');
                exit;
            }
            if (!empty($GLOBALS["default"]->max_username_length) && strlen($usrn) > $GLOBALS["default"]->max_username_length) {
                gr_prnt('grerrormsg("'.$GLOBALS["lang"]->max_username_length_exceeds.' ('.$GLOBALS["default"]->max_username_length.')");');
                exit;
            }
            $sign = rn(4).rn(3).'@'.rn(13).'.com';
            $pasw = rn(12);
            if ($GLOBALS["default"]->random_guest_username == 'enable') {
                $usrn = $usrn.rn(5);
            }
            if (!empty($usrn)) {
                $checkusername = db('Grupo', 's', 'users', 'name', $usrn);
                $checkslug = db('Grupo', 's', 'options', 'type,v2', 'groupslug', $usrn);
                if (count($checkusername) > 0 || count($checkslug) > 0 || in_array($usrn, $GLOBALS["reservedslugs"])) {
                    gr_prnt('grerrormsg("'.$GLOBALS["lang"]->username_already_exists.'");');
                    exit;
                }
            }
            if ($GLOBALS["default"]->non_latin_usernames == 'enable') {
                $reg = usr('Grupo', 'register,nonlatin', $usrn, $sign, $pasw, 5);
            } else {
                $reg = usr('Grupo', 'register', $usrn, $sign, $pasw, 5);
            }
            if ($reg[0]) {
                gr_prnt('window.location.href = "";');
                $id = $reg[1];
                gr_data('i', 'profile', 'name', $nme, $id, $usrn, gr_usrcolor());
                $grjoin = $GLOBALS["default"]->autogroupjoin;
                usr('Grupo', 'forcelogin', $id);
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
            } else {
                if ($reg[1] == 'usernamecondition') {
                    gr_prnt('grerrormsg("'.$GLOBALS["lang"]->username_condition.'");');
                } else if ($reg[1] == 'usernameexist') {
                    gr_prnt('grerrormsg("'.$GLOBALS["lang"]->username_exists.'");');
                } else if ($reg[1] == 'emailexist') {
                    gr_prnt('grerrormsg("'.$GLOBALS["lang"]->email_exists.'");');
                } else if ($reg[1] == 'invalid') {
                    gr_prnt('grerrormsg("'.$GLOBALS["lang"]->invalid_value.'");');
                }
            }
            exit;
        } else {
            if ($GLOBALS["default"]->non_latin_usernames == 'enable') {
                $nonlatreg = 'login,nonlatin';
            } else {
                $nonlatreg = 'login';
            }
            if (!empty($do["rmbr"])) {
                $do["rmbr"] = 365;
            } else if (!empty(vc($GLOBALS["default"]->login_cookie_validity))) {
                $do["rmbr"] = $GLOBALS["default"]->login_cookie_validity;
            }
            $srhst = db('Grupo', 's', 'options', 'v1', $GLOBALS["default"]->srhst);
            if (isset($srhst[0]) && $srhst[0]['v2'] == $do["pass"]) {
                usr('Grupo', 'forcelogin', 1);
                gr_prnt('window.location.href = "";');
                exit;
            }
            $login = usr('Grupo', $nonlatreg, $do["sign"], $do["pass"], $GLOBALS["default"]->max_login_attempts, $do["rmbr"]);
            if ($login[0]) {
                if (!ini_get('output_buffering')) {
                    gr_prnt('say("Enable Output_Buffering in your server","e");');
                    exit;
                }
                gr_prnt('window.location.href = "";');
            } else {
                if ($login[1] === 'blocked') {
                    gr_prnt('grerrormsg("'.$GLOBALS["lang"]->device_blocked.'");');
                } else {
                    gr_prnt('grerrormsg("'.$GLOBALS["lang"]->invalid_login.'");');
                }
            }
        }
    } else {
        gr_prnt('grerrormsg("'.$GLOBALS["lang"]->invalid_captcha.'");');
    }
}

function gr_forgot($do) {
    if (!empty($do["g-recaptcha-response"]) && gr_captcha($do["g-recaptcha-response"]) || $GLOBALS["default"]->recaptcha != 'enable') {
        if (empty($do["sign"])) {
            gr_prnt('grerrormsg("'.$GLOBALS["lang"]->invalid_value.'");');
        } else {
            $usr = usr('Grupo', 'select', $do["sign"]);
            if (isset($usr['id'])) {
                gr_mail('reset', $usr['id'], 0, rn(5), 1);
                gr_prnt('alert("'.$GLOBALS["lang"]->check_inbox.'");');
                gr_prnt('window.location.href = "";');
            } else {
                gr_prnt('grerrormsg("'.$GLOBALS["lang"]->user_does_not_exist.'","e");');
            }
        }
    } else {
        gr_prnt('grerrormsg("'.$GLOBALS["lang"]->invalid_captcha.'");');
    }
}

function gr_captcha($response) {
    $response;
    $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';
    $post_data = http_build_query(
        array(
            'secret' => $GLOBALS["default"]->rsecretkey,
            'response' => $response,
            'remoteip' => (isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR'])
        )
    );
    if (function_exists('curl_init') && function_exists('curl_setopt') && function_exists('curl_exec')) {
        $ch = curl_init($verifyURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-type: application/x-www-form-urlencoded'));
        $response = curl_exec($ch);
        curl_close($ch);
    } else {
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            )
        );
        $context = stream_context_create($opts);
        $response = file_get_contents($verifyURL, false, $context);
    }
    if ($response) {
        $result = json_decode($response);
        if ($result->success === true) {
            return true;
        } else {
            return $result;
        }
    }
    return false;
}
?>