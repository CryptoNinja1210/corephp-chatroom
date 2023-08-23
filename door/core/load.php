<?php if(!defined('s7V9pz')) {die();}?><?php
$GLOBALS["core"] = new stdClass();
$GLOBALS["core"]->url = url();
function rn() {
    $arg = func_get_args();
    if (!isset($arg[0])) {
        $length = rand(8, 20);
    } else {
        $length = $arg[0];
    }
    if (empty($length)) {
        $length = rand(8, 20);
    }
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $size = strlen($chars);
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[rand(0, $size - 1)]; $str .= $chars[rand(0, $size - 1)];
    } $str = substr($str, 0, $length);
    if (isset($arg[2])) {
        $sym = $arg[2];
        if (!isset($arg[3])) {
            $str = $str.$sym;
            $str = str_shuffle($str);
        } else if ($arg[3] == 'left') {
            $str = $sym.$str;
        } else if ($arg[3] == 'right') {
            $str = $str.$sym;
        } else if ($arg[3] == 'mid') {
            $m = strlen($str)/2;
            $f = substr($str, 0, $m);
            $l = substr($str, $m);
            $str = $f.$sym.$l;
        }
        $str = vc($str);
    }
    return $str;
}
function is_https() {
    if (isset($_SERVER['HTTP_CF_VISITOR'])) {
        $cf_visitor = json_decode($_SERVER['HTTP_CF_VISITOR']);
        if (isset($cf_visitor->scheme) && $cf_visitor->scheme == 'https') {
            return true;
        }
    } else if (isset($_SERVER['HTTPS'])) {
        return true;
    }
    return false;
}
function addcookie($name, $value, $time = 0, $path = "/") {
    if (isset(cnf()["samesite"]) && strtolower(cnf()["samesite"]) == 'none') {
        if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 70300) {
            $cookie_options = array(
                'expires' => $time,
                'path' => $path,
                'secure' => true,
                'httponly' => false,
                'samesite' => 'None'
            );
            setcookie($name, $value, $cookie_options);
        } else {
            setcookie($name, $value, $time, "$path; SameSite=None; Secure");
        }
    } else {
        setcookie($name, $value, $time, $path);
    }
}
function rt($v) {
    $page = vc($v);
    $r = $GLOBALS["core"]->url.$v;
    if (substr($v, 0, 7) === 'http://' || substr($v, 0, 8) === 'https://') {
        $r = $v;
    }
    if (headers_sent()) {
        echo "<script type='text/javascript'> document.location = '".$r."'; </script>";
        exit;
    } else {
        exit(header("Location:".$r));
    }
}

function pr() {
    $arg = func_get_args();
    if (isset($arg[1])) {
        $d = 0;
        if (isset($arg[2])) {
            $d = 1;
        }
        $t = vc($arg[1]);
        $v = vc($arg[0], $t, $d);
    } else {
        $v = vc($arg[0]);
    }
    if (is_array($v)) {
        print_r($v);
    } else {
        echo $v;
    }}

function dt() {
    $arg = func_get_args();
    $f = 'Y-m-d H:i:s';
    $nw = null;
    if (isset($arg[1]) && !empty($arg[1])) {
        $f = $arg[1];
    }if (isset($arg[2]) && !empty($arg[2])) {
        $nw = $arg[2];
    }
    if (isset($arg[0]) && !empty($arg[0])) {
        $datetime = new DateTime($nw);
        $la_time = new DateTimeZone($arg[0]);
        $datetime->setTimezone($la_time);
        return $datetime->format($f);
    } else {
        if (isset($arg[2]) && !empty($arg[2])) {
            $time = strtotime($nw);
            return date($f, $time);
        } else {

            return date($f);
        }
    }
}

function get($d = 'post') {
    if ($d === 'get') {
        return vc($_GET);
    } if ($d === 'file') {
        return vc($_FILES);
    } else {
        return vc($_POST);
    }
}


function vc() {
    $arg = func_get_args();
    $r = null;
    if (is_array($arg[0])) {
        foreach ($arg[0] as $key => $v) {
            if (!isset($arg[1])) {
                $arg[1] = 0;
            }
            if (!isset($arg[2])) {
                $arg[2] = 0;
            }
            if (!isset($arg[3])) {
                $arg[3] = 0;
            }
            $arg[0][$key] = vc($v, $arg[1], $arg[2], $arg[3]);
        }
        $r = $arg[0];
    } else {
        if (isset($arg[1]) && !empty($arg[1])) {
            if ($arg[1] == 'alpha' || $arg[1] == 'alphanum' || $arg[1] == 'num' || $arg[1] == 'regex' || $arg[1] == 'email') {
                if (isset($arg[2]) && !empty($arg[2])) {
                    $do['alpha'] = '/[^a-zA-Z]/';
                    $do['alphanum'] = '/[^a-zA-Z0-9]/';
                    $do['num'] = '/[^0-9]/';
                    $do['email'] = '/[^\pL\pM*+\pN._%+-]+@[\pL\pM*+\pN.-]+\.[\pL\pM*+]{2,20}\z/u';
                } else {
                    $do['alpha'] = '/^[a-zA-Z]+$/';
                    $do['alphanum'] = '/^[a-zA-Z0-9]+$/';
                    $do['num'] = '/^[0-9]+$/';
                    $do['email'] = '/^[\pL\pM*+\pN._%+-]+@[\pL\pM*+\pN.-]+\.[\pL\pM*+]{2,20}\z/u';
                }
                if ($arg[1] == 'regex') {
                    if (!isset($arg[3]) && empty($arg[3])) {
                        $arg[3] = 'a-zA-Z0-9';
                    }
                    if (isset($arg[2]) && !empty($arg[2])) {
                        $do['regex'] = '/^['.$arg[3].']+$/';
                    } else {
                        $do['regex'] = '/[^'.$arg[3].']+$/';
                    }
                }
                if (preg_match($do[$arg[1]], $arg[0]) && !isset($arg[2]) || preg_match($do[$arg[1]], $arg[0]) && empty($arg[2])) {
                    $r = $arg[0];
                } else if (isset($arg[2]) && $arg[2] == 1) {
                    $r = preg_replace($do[$arg[1]], '', $arg[0]);
                }
            } else if ($arg[1] == 'ip' || $arg[1] == 'email2' || $arg[1] == 'url') {
                $do['ip'] = FILTER_VALIDATE_IP;
                $do['email2'] = FILTER_VALIDATE_EMAIL;
                $do['url'] = FILTER_VALIDATE_URL;
                if (filter_var($arg[0], $do[$arg[1]])) {
                    if ($arg[1] == 'email2') {
                        $arg[0] = filter_var($arg[0], FILTER_SANITIZE_EMAIL);
                    } else if ($arg[1] == 'url') {
                        $arg[0] = filter_var($arg[0], FILTER_SANITIZE_URL);
                    }
                    $r = $arg[0];
                }
            } else if ($arg[1] == 'strip') {
                $r = strip_tags(htmlspecialchars_decode($arg[0]));
                $r = str_replace('"', "", $r);
                $r = str_replace("'", "", $r);
                $r = str_replace("#039;", "", $r);
            } else if ($arg[1] == 'date') {
                if (!isset($arg[2])) {
                    $arg[2] = 'Y-m-d H:i:s';
                }
                $d = DateTime::createFromFormat($arg[2], $arg[0]);
                $d = $d && $d->format($arg[2]) == $arg[0];
                if ($d === true) {
                    $r = $arg[0];
                }
            }
        } else {
            $r = htmlspecialchars($arg[0], ENT_QUOTES, 'UTF-8');
        }
    }
    return $r;
}

function url($v = 0) {
    if (cnf()['global'] === 0) {
        return cnf()['url'];
    } else {
        $cloudflare = 0;
        if (isset($_SERVER['HTTP_CF_VISITOR'])) {
            $cf_visitor = json_decode($_SERVER['HTTP_CF_VISITOR']);
            if (isset($cf_visitor->scheme) && $cf_visitor->scheme == 'https') {
                $cloudflare = 1;
            }
        }
        $actuallink = ($cloudflare == 1 || isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if (!empty($v)) {
            if ($v == 'var') {
                parse_str(url('query'), $url);
            } else {
                if (isset(parse_url($actuallink)[$v])) {
                    $url = parse_url($actuallink)[$v];
                } else {
                    $url = '';
                }
            }
        } else {
            $url = vc($actuallink, 'url');
            if (!empty(pg())) {
                $url = substr($url, 0, -strlen(pg()));
            }
        }
        return $url;
    }
}
function pg($v = 0) {
    if ($v === 0) {
        $kn = str_replace('\\', '/', dirname($_SERVER['PHP_SELF']));
        if ($kn == '/') {
            $kn = '';
        }
        $kn = substr($_SERVER["REQUEST_URI"], strlen($kn)+1);
        $r = $kn;
    } else if ($v === 1) {
        $kn = explode('/', pg())[0];
        $r = $kn;
    } else {
        $r = explode($v."/", pg());
        if (count($r) > 1) {
            $r = $r[1];
        } else {
            $r = null;
        }
    }
    return $r;
}
function fc() {
    foreach (func_get_args() as $param) {
        include(cnf()["door"]."/".$param."/load.php");
    }
}
function sfn($sf, $fn) {
    require_once(cnf()["door"]."/".$fn."/".$sf);
}
function mf($r = "") {
    return $GLOBALS["core"]->url.cnf()["gem"]."/ore/".vc($r);
}

function css() {
    foreach (func_get_args() as $param) {
        $param = vc($param);
        if (substr($param, 0, 7) === 'http://' || substr($param, 0, 8) === 'https://') {
            echo "<link href='".$param."' rel='stylesheet' type='text/css'> \n";
        } else {
            echo '<link href="'.$GLOBALS["core"]->url.'gem/tone/'.$param.'.css" rel="stylesheet" type="text/css">'."\n";
        }
    }}

function cdn() {
    foreach (func_get_args() as $param) {
        if (substr($param, 0, 5) === 'libs/') {
            $cdn = 'https://cdnjs.cloudflare.com/ajax/';
        } else if (substr($param, 0, 4) === 'npm/') {
            $cdn = 'https://cdn.jsdelivr.net/';
        }
        if (strpos($param, '.css') !== false) {
            echo "<link href='".$cdn.$param."' rel='stylesheet' type='text/css'> \n";
        } else {
            echo "<script src='".$cdn.$param."'> </script> \n";
        }
    }}

function js() {
    foreach (func_get_args() as $param) {
        if (substr($param, 0, 7) === 'http://' || substr($param, 0, 8) === 'https://') {
            echo "<script src='".$param."'></script>\n";
        } else {
            echo '<script src="'.$GLOBALS["core"]->url.'gem/mine/'.$param.'.js"></script>'."\n";
        }
    }}
function error($code) {
    if ($code == "404") {
        header('HTTP/1.1 404 Not Found');
        include(cnf()["knob"]."/404.php");
        exit;
    }
}

function ns($k = 0) {
    $r = explode("/", pg());
    if ($k === 0) {
        if (count($r) > 2) {
            error("404");
        }
    } else {
        $aw = func_get_args();
        array_shift($r);
        $r = implode($r);
        if (!in_array($r, $aw)) {
            error("404");
        }
    }
}

function load_knob() {
    urlfix();
    $c = preg_split('~[^a-z0-9.\\_\\-]~i', pg())[0];
    $ext = substr($c, strrpos($c, '.') + 1);
    if (in_array($ext, explode(",", cnf()['ext']))) {
        $c = $ext;
    }
    if ($c === "index") {
        rt('');
    }
    if ($c == "") {
        $c = "index";
    }
    $knob = cnf()['knob']."/".$c.".php";
    if (!file_exists($knob)) {
        error("404");
    } else {
        include $knob;
    }
}

function minify() {
    function minified($buffer) {
        $search = array(
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            '/(\s)+/s',
            '/<!--(.|\s)*?-->/'
        );
        $replace = array(
            '>',
            '<',
            '\\1',
            ''
        );

        $buffer = preg_replace($search, $replace, $buffer);
        return $buffer;
    }
    ob_start("minified");
}

function jcmin($content) {
    $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
    $content = str_replace(': ', ':', $content);
    $content = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $content);
    return $content;
}

function load_tone() {
    error_reporting(0);
    $c = preg_split('~[^a-z0-9.\\_\\-]~i', pg())[0];
    $c = preg_replace('/(css(?!.*css))/', 'css', $c);
    $cssFile = cnf()['gem']."/tone/$c";
    if (!file_exists($cssFile)) {
        error("404");
    } else {
        header('Cache-Control: public');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
        header("Content-type: text/css");
        ob_start("jcmin");
        include $cssFile;
        ob_end_flush();
    }
}

function load_mine() {
    error_reporting(0);
    $c = preg_split('~[^a-z0-9.\\_\\-]~i', pg())[0];
    $c = preg_replace('/(js(?!.*js))/', 'js', $c);
    $jsFile = cnf()['gem']."/mine/$c";
    if (!file_exists($jsFile)) {
        error("404");
    } else {
        header('Cache-Control: public');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
        header('Content-Type: application/javascript');
        ob_start("jcmin");
        include $jsFile;
        ob_end_flush();
    }
}

function urlfix($url = 0) {
    if ($url === 0) {
        $cloudflare = 0;
        if (isset($_SERVER['HTTP_CF_VISITOR'])) {
            $cf_visitor = json_decode($_SERVER['HTTP_CF_VISITOR']);
            if (isset($cf_visitor->scheme) && $cf_visitor->scheme == 'https') {
                $cloudflare = 1;
            }
        }
        $url = ($cloudflare == 1 || isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
    $url = vc(vc($url, 'url'));
    $site = $GLOBALS["core"]->url.pg();
    $ext = 0;
    $aw = array('-', '_', '~', '!', '+', '*', ':', '@', '.', '$', '&', '?', '=');
    $sitepath = parse_url($site)["path"];
    if (isset(parse_url($site)["query"])) {
        $sitepath = $sitepath.'?'.parse_url($site)["query"];
    }
    foreach ($aw as $a) {
        if (stripos($sitepath, $a) !== false) {
            $ext = 1;
        }
    }
    if ($ext === 0) {
        if ($site !== $url || substr($site, -1) != '/') {
            if (substr($site, -1) != '/' && $ext === 0) {
                $site = $site.'/';
            }
            if (headers_sent()) {
                echo "<script type='text/javascript'> document.location = '".$site."'; </script>";
                exit;
            } else {
                exit(header("Location:".$site));
            }
        }
    }
}

function req() {
    $inc = get_included_files();
    $p = getcwd();
    foreach ($inc as $key => $value) {
        $inc[$key] = stripslashes(str_replace($p, "", $value));
    }
    foreach (func_get_args() as $f) {
        $fn = cnf()['door'].$f.'load.php';
        if (!in_array($fn, $inc)) {
            fc($f);
        }
    }
}




?>