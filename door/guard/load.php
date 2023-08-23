<?php if(!defined('s7V9pz')) {die();}?><?php
function en($v, $t = 0, $m = 0) {
    if ($t == '0') {
        $t = rand(1, 10);
    }
    function depict($t, $v, $m) {
        $r["type"] = $t;
        $r["mask"] = $m;
        if ($r["mask"] == '0') {
            $r["mask"] = rn(rand(8, 15));
        }
        if ($r["type"] == '1') {
            $r["pass"] = md5(md5(sha1(sha1(md5($r["mask"] . $v)))));
        } else if ($r["type"] == '2') {
            $r["pass"] = hash('ripemd128', (md5(md5($r["mask"] . $v))));
        } else if ($r["type"] == '3') {
            $r["pass"] = hash('sha256', (crc32($r["mask"] . $v)));
        } else if ($r["type"] == '4') {
            $r["pass"] = hash('ripemd128', (crc32(crc32($r["mask"] . $v))));
        } else if ($r["type"] == '5') {
            $r["pass"] = hash('md4', (md5($r["mask"] . $v)));
        } else if ($r["type"] == '6') {
            $r["pass"] = md5(hash('sha256', (md5($r["mask"] . $v))));
        } else if ($r["type"] == '7') {
            $r["pass"] = hash('ripemd128', (sha1($r["mask"] . $v)));
        } else if ($r["type"] == '8') {
            $r["pass"] = hash('md2', (md5(sha1($r["mask"] . $v))));
        } else if ($r["type"] == '9') {
            $r["pass"] = sha1(crc32(sha1(crc32(md5($r["mask"] . $v)))));
        } else if ($r["type"] == '10') {
            $r["pass"] = md5(md5(sha1(sha1(crc32($r["mask"] . $v)))));
        }
        return $r;
    }
    return depict($t, $v, $m);
}

function guard($v = 0, $d = 0) {
    if ($d == 1) {
        $obf = 'key/shield/'.$_SESSION[$v].'.php';
        if (file_exists($obf)) {
            unlink($obf);
        }
        $s = $v.'[swr]'.date("dmyhms").'[swr]'.rn('14');
        $bf = 'key/shield/'.$s.'.php';
        $bit = "<?php if(!defined('".cnf()["bit"]."')) {die();}?>";
        file_put_contents($bf, $bit);
        $_SESSION[$v] = $s;
    }
}

function ip($t = 0) {
    if ($t === 'dev') {
        $dev = $_SERVER ['HTTP_USER_AGENT'];
        $dev = preg_replace("/[^A-Za-z0-9.]/", "", $dev);
        $dev = strtolower($dev);
        return $dev;
    } else {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];
        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        return $ip;
    }
}
function cr() {
    $arg = func_get_args();
    if (isset($arg[0]) && !empty($arg[0])) {
        $arg[0] = vc($arg[0]);
        $sk = rn();
        $sv = rn();
        $v = $arg[1];
        if (isset($arg[3]) && !empty($arg[3])) {
            $sk = $arg[3];
        }if (isset($arg[4]) && !empty($arg[4])) {
            $sv = $arg[4];
        }
        $ems = array(null, 'AES-128-CBC', 'AES-128-CFB', 'AES-128-CFB1', 'AES-128-CFB8', 'AES-128-OFB', 'AES-192-CBC', 'AES-192-CFB', 'AES-192-CFB1', 'AES-192-CFB8', 'AES-192-OFB', 'AES-256-CBC', 'AES-256-CFB', 'AES-256-CFB1', 'AES-256-CFB8', 'AES-256-OFB', 'BF-CBC', 'BF-CFB', 'BF-OFB', 'CAST5-CBC', 'CAST5-CFB', 'CAST5-OFB', 'IDEA-CBC', 'IDEA-CFB', 'IDEA-OFB');
        $mn = rand(1, 24);
        if (isset($arg[2]) && !empty($arg[2])) {
            $mn = $arg[2];
        }
        $em = $ems[$mn];
        $key = hash('sha256', $sk);
        $iv = substr(hash('sha256', $sv), 0, openssl_cipher_iv_length($em));
        if ($arg[0] == 'e') {
            $ev = null;
            if (isset($arg[5]) && !empty($arg[5])) {
                $ev = 1;
            }
            $r['str'] = base64_encode(openssl_encrypt($v, $em, $key, 0, $iv));
            $r['method'] = $mn;
            $r['key'] = $sk;
            $r['iv'] = $sv;
            $r['code'] = 'cr("d","'.$r['str'].'","'.$r['method'].'","'.$r['key'].'","'.$r['iv'].'",'.$ev.');';
        } else if ($arg[0] == 'd') {
            $r = openssl_decrypt(base64_decode($v), $em, $key, 0, $iv);
            if (isset($arg[5]) && !empty($arg[5])) {
                eval('?>'.$r);
                exit;
            }
        }

        return $r;
    }
}
function ipxtract($agent = 0) {
    $r['browser'] = "Unknown Browser";
    $found = false;
    $browser_array = array('/msie/i' => 'Internet Explorer',
        '/mobile/i' => 'Handheld Browser',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/opera/i' => 'Opera',
        '/edge/i' => 'Edge',
        '/opr/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror');

    foreach ($browser_array as $regex => $value) {
        if ($found) {
            break;
        } else if (preg_match($regex, $agent, $result)) {
            $r['browser'] = $value;
        }
    }
    $os_platform = "Unknown OS Platform";
    $os_array = array(
        '/windowsnt10/i' => 'Windows 10',
        '/windowsnt6.3/i' => 'Windows 8.1',
        '/windowsnt6.2/i' => 'Windows 8',
        '/windowsnt6.1/i' => 'Windows 7',
        '/windowsnt6.0/i' => 'Windows Vista',
        '/windowsnt5.2/i' => 'Windows Server 2003/XP x64',
        '/windowsnt5.1/i' => 'Windows XP',
        '/windowsxp/i' => 'Windows XP',
        '/windowsnt 5.0/i' => 'Windows 2000',
        '/windowsme/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|macosx/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );

    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $agent)) {
            $r['os'] = $value;
        }
    }
    return $r;
}
?>