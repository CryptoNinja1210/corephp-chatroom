<?php if(!defined('s7V9pz')) {die();}?><?php
function gr_sys() {
    $arg = func_get_args();
    $uid = $GLOBALS["user"]['id'];
    if ($arg[0]['type'] === 'appearance' && gr_role('access', 'sys', '2')) {
        $css = db('Grupo', 's', 'customize');
        if (!empty($arg[0]['boxed'])) {
            db('Grupo', 'u', 'defaults', 'v2', 'type,v1', $arg[0]['boxed'], 'default', 'boxed');
        }
        if (!empty($arg[0]['sentalign'])) {
            db('Grupo', 'u', 'defaults', 'v2', 'type,v1', $arg[0]['sentalign'], 'default', 'sent_msg_align');
        }
        if (!empty($arg[0]['recievedalign'])) {
            db('Grupo', 'u', 'defaults', 'v2', 'type,v1', $arg[0]['recievedalign'], 'default', 'received_msg_align');
        }
        if (!empty($arg[0]['defont'])) {
            db('Grupo', 'u', 'defaults', 'v2', 'type,v1', $arg[0]['defont'], 'default', 'default_font');
        }
        foreach ($css as $c) {
            $key = 'css'.$c['id'];
            if ($c['type'] == 'background') {
                $a = $key.'a';
                $b = $key.'b';
                if (!empty($arg[0][$a]) && !empty($arg[0][$b])) {
                    db('Grupo', 'u', 'customize', 'v1,v2', 'id', $arg[0][$a], $arg[0][$b], $c['id']);
                }
            } else if ($c['name'] == 'custom_css') {
                db('Grupo', 'u', 'customize', 'element', 'id', $_POST['customcss'], $c['id']);
            } else {
                if (!empty($arg[0][$key]) && $arg[0][$key] != $c['v1']) {
                    db('Grupo', 'u', 'customize', 'v1', 'id', $arg[0][$key], $c['id']);
                }
            }

        }
        gr_cache('settings');
        unlink('gem/tone/custom.css');
        $ccontent = gr_customcss();
        $ccfile = fopen("gem/tone/custom.css", "w");
        fwrite($ccfile, $ccontent);
        fclose($ccfile);
        gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");');
        gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
    } else if ($arg[0]['type'] === 'easycustomizer' && gr_role('access', 'sys', '2')) {
        $css = db('Grupo', 's', 'customize', 'xtra', 'easyedit');
        if (!empty($arg[0]['startcolor']) && !empty($arg[0]['endcolor'])) {
            foreach ($css as $c) {
                if ($c['type'] == 'background') {
                    db('Grupo', 'u', 'customize', 'v1,v2', 'id', $arg[0]['startcolor'], $arg[0]['endcolor'], $c['id']);
                } else {
                    db('Grupo', 'u', 'customize', 'v1', 'id', $arg[0]['startcolor'], $c['id']);
                }
            }
        }
        unlink('gem/tone/custom.css');
        $ccontent = gr_customcss();
        $ccfile = fopen("gem/tone/custom.css", "w");
        fwrite($ccfile, $ccontent);
        fclose($ccfile);
        gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");');
        gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
    } else if ($arg[0]['type'] === 'hf' && gr_role('access', 'sys', '5')) {
        $hfile = fopen("gem/ore/grupo/cache/headers.cch", "w");
        fwrite($hfile, $_POST['headers']);
        fclose($hfile);
        $ffile = fopen("gem/ore/grupo/cache/footers.cch", "w");
        fwrite($ffile, $_POST['footers']);
        fclose($ffile);
        $ffile = fopen("gem/ore/grupo/cache/bodyopen.cch", "w");
        fwrite($ffile, $_POST['bodyopen']);
        fclose($ffile);
        $ffile = fopen("gem/ore/grupo/cache/bodyclose.cch", "w");
        fwrite($ffile, $_POST['bodyclose']);
        fclose($ffile);
        gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");');
        gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
    } else if ($arg[0]['type'] === 'banip') {
        if (gr_role('access', 'sys', '3')) {
            if (!empty($arg[0]['blist'])) {
                db('Grupo', 'u', 'defaults', 'v2', 'type', $arg[0]['blist'], 'blacklist');
                gr_cache('blacklist');
                gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");$(".grupo-pop").fadeOut();');
            }
        }
        exit;
    } else if ($arg[0]['type'] === 'filterwords') {
        if (gr_role('access', 'sys', '4')) {
            if (!empty($arg[0]['blist'])) {
                db('Grupo', 'u', 'defaults', '#v2', 'type', $arg[0]['blist'], 'filterwords');
                gr_cache('filterwords');
                gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");$(".grupo-pop").fadeOut();');
                gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
            }
        }
        exit;
    } else if ($arg[0]['type'] === 'settings') {
        if (gr_role('access', 'sys', '1')) {
            if (!headers_sent()) {
                header('Cache-Control: no-cache');
                header('Pragma: no-cache');
            }
            $sys = db('Grupo', 's', 'defaults', 'type,v1<>,v1<>', 'default', 'sent_msg_align', 'received_msg_align');
            foreach ($sys as $s) {
                $key = $s['id'];
                if (!empty($arg[0][$key]) && $arg[0][$key] != $s['v2'] || $s['v1'] == 'autogroupjoin' || $s['v1'] == 'pingroup' || $s['v1'] == 'send_email_notification' || $s['v1'] == 'login_cookie_validity') {
                    if ($s['v1'] == 'fileexpiry' || $s['v1'] == 'delmsgexpiry' || $s['v1'] == 'autodeletemsg' || $s['v1'] == 'max_msg_length') {
                        $arg[0][$key] = vc($arg[0][$key], 'num');
                        if (empty($arg[0][$key])) {
                            $arg[0][$key] = 'Off';
                        }
                    } else if ($s['v1'] == 'unsplash_load') {
                        $arg[0][$key] = str_replace('collections', 'collection', $arg[0][$key]);
                    } else if ($s['v1'] == 'send_email_notification') {
                        if (!isset($arg[0][$key])) {
                            $arg[0][$key] = 0;
                        } else {
                            $arg[0][$key] = implode(',', $arg[0][$key]);
                        }
                    }
                    db('Grupo', 'u', 'defaults', 'v2', 'id', $arg[0][$key], $key);
                }
            }
            if (isset($_FILES['logo']['name']) && !empty($_FILES['logo']['name'])) {
                if (flr('upload', 'logo', 'grupo/global', 'logo', 'jpg,jpeg,png,gif', 1, 1, 'png', 1)) {
                    if (!is_array(getimagesize('gem/ore/grupo/global/logo.png'))) {
                        flr('delete', 'grupo/global/logo.png');
                    }
                }
            }
            if (isset($_FILES['sitelogo']['name']) && !empty($_FILES['sitelogo']['name'])) {
                if (flr('upload', 'sitelogo', 'grupo/global', 'sitelogo', 'jpg,jpeg,png,gif', 1, 1, 'png', 1)) {
                    if (!is_array(getimagesize('gem/ore/grupo/global/sitelogo.png'))) {
                        flr('delete', 'grupo/global/sitelogo.png');
                    }
                }
            }
            if (isset($_FILES['mobilelogo']['name']) && !empty($_FILES['mobilelogo']['name'])) {
                if (flr('upload', 'mobilelogo', 'grupo/global', 'mobilelogo', 'jpg,jpeg,png,gif', 1, 1, 'png', 1)) {
                    if (!is_array(getimagesize('gem/ore/grupo/global/mobilelogo.png'))) {
                        flr('delete', 'grupo/global/mobilelogo.png');
                    }
                }
            }
            if (isset($_FILES['welcome']['name']) && !empty($_FILES['welcome']['name'])) {
                if (flr('upload', 'welcome', 'grupo/global', 'welcome', 'jpg,jpeg,png,gif', 1, 1, 'png', 1)) {
                    if (!is_array(getimagesize('gem/ore/grupo/global/welcome.png'))) {
                        flr('delete', 'grupo/global/welcome.png');
                    }
                }
            }
            if (isset($_FILES['emaillogo']['name']) && !empty($_FILES['emaillogo']['name'])) {
                if (flr('upload', 'emaillogo', 'grupo/global', 'emaillogo', 'jpg,jpeg,png,gif', 1, 1, 'png', 1)) {
                    if (!is_array(getimagesize('gem/ore/grupo/global/emaillogo.png'))) {
                        flr('delete', 'grupo/global/emaillogo.png');
                    }
                }
            }
            if (isset($_FILES['pwaicon']['name']) && !empty($_FILES['pwaicon']['name'])) {
                if (flr('upload', 'pwaicon', 'grupo/global', 'pwaicon', 'jpg,jpeg,png,gif', 1, 1, 'png', 1)) {
                    if (!is_array(getimagesize('gem/ore/grupo/global/pwaicon.png'))) {
                        flr('delete', 'grupo/global/pwaicon.png');
                    } else {
                        flr('resize', 'grupo/global/pwaicon.png', 'grupo/global/icon512.png', 512, 512, 1);
                        flr('resize', 'grupo/global/pwaicon.png', 'grupo/global/icon192.png', 192, 192, 1);
                    }
                }
            }
            if (isset($_FILES['defaultbg']['name']) && !empty($_FILES['defaultbg']['name'])) {
                if (flr('upload', 'defaultbg', 'grupo/global', 'bg', 'jpg,jpeg,png,gif', 1, 1, 'jpg', 1)) {
                    if (@is_array(getimagesize('gem/ore/grupo/global/bg.jpg'))) {
                        flr('compress', 'grupo/global/bg.jpg', 50);
                    } else {
                        flr('delete', 'grupo/global/bg.jpg');
                    }
                }
            }
            if (isset($_FILES['defaultbgdark']['name']) && !empty($_FILES['defaultbgdark']['name'])) {
                if (flr('upload', 'defaultbgdark', 'grupo/global', 'bg-dark', 'jpg,jpeg,png,gif', 1, 1, 'jpg', 1)) {
                    if (@is_array(getimagesize('gem/ore/grupo/global/bg-dark.jpg'))) {
                        flr('compress', 'grupo/global/bg-dark.jpg', 50);
                    } else {
                        flr('delete', 'grupo/global/bg-dark.jpg');
                    }
                }
            }
            if (isset($_FILES['imgsocialmedia']['name']) && !empty($_FILES['imgsocialmedia']['name'])) {
                if (flr('upload', 'imgsocialmedia', 'grupo/global', 'socialmedia', 'jpg,jpeg,png,gif', 1, 1, 'jpg', 1)) {
                    if (@is_array(getimagesize('gem/ore/grupo/global/socialmedia.jpg'))) {
                        flr('compress', 'grupo/global/socialmedia.jpg', 50);
                    } else {
                        flr('delete', 'grupo/global/socialmedia.jpg');
                    }
                }
            }
            if (isset($_FILES['loginbg']['name']) && !empty($_FILES['loginbg']['name'])) {
                if (flr('upload', 'loginbg', 'grupo/global', 'login', 'jpg,jpeg,png,gif', 1, 1, 'jpg', 1)) {
                    if (@is_array(getimagesize('gem/ore/grupo/global/login.jpg'))) {
                        flr('compress', 'grupo/global/login.jpg', 50);
                    } else {
                        flr('delete', 'grupo/global/login.jpg');
                    }
                }
            }
            if (isset($_FILES['favicon']['name']) && !empty($_FILES['favicon']['name'])) {
                if (flr('upload', 'favicon', 'grupo/global', 'favicon', 'jpg,jpeg,png,gif', 1, 1, 'png', 1)) {
                    if (!is_array(getimagesize('gem/ore/grupo/global/favicon.png'))) {
                        flr('delete', 'grupo/global/favicon.png');
                    }
                }
            }
            gr_cache('settings');
            unlink('gem/tone/custom.css');
            $ccontent = gr_customcss();
            $ccfile = fopen("gem/tone/custom.css", "w");
            fwrite($ccfile, $ccontent);
            fclose($ccfile);
            gr_prnt('say("'.$GLOBALS["lang"]->updated.'","s");');
            if (strtolower(trim($arg[0]['rebuilder'])) == 'yes') {
                gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"act/reset";');
            } else {
                gr_prnt('window.location.href = $(".dumb .gdefaults > .baseurl").text()+"chat/";');
            }
        }
    }
}
function gr_customcss() {
    if (!headers_sent()) {
        header('Cache-Control: no-cache');
        header('Pragma: no-cache');
    }
    $css = db('Grupo', 's', 'customize', 'device,name<>', 'all', 'custom_css');
    $mobcss = db('Grupo', 's', 'customize', 'device,name<>', 'mobile', 'custom_css');
    $custm = db('Grupo', 's', 'customize', 'name', 'custom_css')[0]['element'];
    $sty = '';
    $GLOBALS["default"]->boxed = db('Grupo', 's,v2', 'defaults', 'type,v1', 'default', 'boxed')[0]['v2'];
    $GLOBALS["default"]->send_btn_visible = db('Grupo', 's,v2', 'defaults', 'type,v1', 'default', 'send_btn_visible')[0]['v2'];
    $GLOBALS["default"]->sysmessages = db('Grupo', 's,v2', 'defaults', 'type,v1', 'default', 'sysmessages')[0]['v2'];
    if ($GLOBALS["default"]->boxed == 'disable') {
        $sty .= '.swr-grupo > .window{ padding:0px;}.swr-grupo .aside{border-radius:0px;}body{background:none;}';
    }
    if ($GLOBALS["default"]->send_btn_visible == 'enable') {
        $sty .= '.swr-grupo .panel > .textbox > .box > i{display:block;}';
    }
    if ($GLOBALS["default"]->sysmessages == 'disable') {
        $sty .= '.swr-grupo .panel > .room > .msgs > li.system{display:none;}';
    }
    foreach ($css as $c) {
        $sty .= html_entity_decode($c['element'], ENT_QUOTES) . '{';
        if ($c['type'] == 'background') {
            $sty .= 'background: linear-gradient(to right,' . $c['v1'] . ',' . $c['v2'] . ');';
        } else if ($c['type'] == 'color' || $c['type'] == 'border-color' || $c['type'] == 'font-size') {
            if ($c['type'] == 'font-size') {
                $c['v1'] = $c['v1'].'px';
            }
            $sty .= $c['type'] . ':' . $c['v1'] . ';';
        }
        $sty .= '}';
    }

    $sty .= '@media (max-width: 767.98px){';
    foreach ($mobcss as $c) {
        $sty .= html_entity_decode($c['element'], ENT_QUOTES) . '{';
        if ($c['type'] == 'background') {
            $sty .= 'background: linear-gradient(to right,' . $c['v1'] . ',' . $c['v2'] . ');';
        } else if ($c['type'] == 'color' || $c['type'] == 'border-color' || $c['type'] == 'font-size') {
            if ($c['type'] == 'font-size') {
                $c['v1'] = $c['v1'].'px';
            }
            $sty .= $c['type'] . ':' . $c['v1'] . ';';
        }

        $sty .= '}';
    }
    $sty .= '}';
    $custm = html_entity_decode($custm, ENT_QUOTES);
    $custm = preg_replace('/<\\?.*(\\?>|$)/Us', '', $custm);
    $sty .= str_replace(">", ">", $custm);
    return $sty;
}
function gr_globalreset($act) {
    if (gr_role('access', 'sys', '1')) {
        gr_prnt('<style>div{font-family: sans-serif; font-size: 26px; color: darkgrey;height: 100%;display: flex; align-items: center; justify-content: center; text-align: center;}</style>');
        gr_prnt("<title>Grupo Rebuilder</title>");
        if (empty($act[1])) {
            gr_prnt("<meta http-equiv='refresh' content='3;url=".$GLOBALS["core"]->url."act/reset/step2/'>");
            gr_prnt('<body><div>Rebuilding Cache (Languages). Please Wait...</div></body>');
        } else if ($act[1] == 'step2') {
            $dlng = db('Grupo', 's', 'phrases', 'type', 'lang');
            $ph = db('Grupo', 's', 'phrases', 'type,lid', 'phrase', 1);
            foreach ($dlng as $dl) {
                foreach ($ph as $p) {
                    $pfull = db('Grupo', 's,count(id)', 'phrases', 'type,lid,short', 'phrase', $dl['id'], $p['short'])[0][0];
                    if ($pfull == 0) {
                        db('Grupo', 'i', 'phrases', 'short,type,full,lid', $p['short'], 'phrase', $p['full'], $dl['id']);
                    }
                }
                gr_cache('languages', $dl['id']);
            }
            gr_prnt("<meta http-equiv='refresh' content='5;url=".$GLOBALS["core"]->url."act/reset/step3/'>");
            gr_prnt('<body><div>Rebuilding Cache (User Roles). Please Wait...</div></body>');
        } else if ($act[1] == 'step3') {
            gr_cache('roles');
            gr_prnt("<meta http-equiv='refresh' content='5;url=".$GLOBALS["core"]->url."act/reset/step4/'>");
            gr_prnt('<body><div>Rebuilding Cache (Blacklist & Filter Words). Please Wait...</div></body>');
        } else if ($act[1] == 'step4') {
            gr_cache('blacklist');
            gr_cache('filterwords');
            gr_prnt("<meta http-equiv='refresh' content='5;url=".$GLOBALS["core"]->url."act/reset/step5/'>");
            gr_prnt('<body><div>Rebuilding Cache (Settings). Please Wait...</div></body>');
        } else if ($act[1] == 'step5') {
            gr_cache('settings');
            gr_prnt("<meta http-equiv='refresh' content='5;url=".$GLOBALS["core"]->url."act/reset/step6/'>");
            gr_prnt('<body><div>Rebuilding Appearances. Please Wait...</div></body>');
        } else if ($act[1] == 'step6') {
            unlink('gem/tone/custom.css');
            $ccontent = gr_customcss();
            $ccfile = fopen("gem/tone/custom.css", "w");
            fwrite($ccfile, $ccontent);
            fclose($ccfile);
            gr_prnt("<meta http-equiv='refresh' content='5;url=".$GLOBALS["core"]->url."act/reset/finished/'>");
            gr_prnt('<body><div>Removing Logs & Logging out users. Please Wait...</div></body>');
        } else if ($act[1] == 'finished') {
            db('Grupo', 'q', 'DELETE FROM gr_logs WHERE type <> "cache"');
            db('Grupo', 'q', 'ALTER TABLE gr_logs AUTO_INCREMENT = 0');
            db('Grupo', 'q', 'DELETE FROM gr_session');
            db('Grupo', 'q', 'ALTER TABLE gr_session AUTO_INCREMENT = 0');
            addcookie('grcreset', 1, 0, "/");
            gr_prnt("<meta http-equiv='refresh' content='2;url=".$GLOBALS["core"]->url."'>");
            gr_prnt('<body><div>Completed. Redirecting to homepage.</div></body>');
        }
    }
}
?>