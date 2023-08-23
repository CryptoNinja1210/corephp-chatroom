<?php if(!defined('s7V9pz')) {die();}?><?php
if (!file_exists('knob/install.php') && !file_exists('knob/update.php')) {

    if (!isset($_COOKIE["Grupousrdev"]) || empty($_COOKIE["Grupousrdev"])) {
        $_COOKIE["Grupousrdev"] = $_COOKIE['Grupousrses'] = $_COOKIE["Grupousrcode"] = 0;
    }
    $GLOBALS["default"] = gr_default('var');
    $GLOBALS["default"]->send_email_notification = explode(',', $GLOBALS["default"]->send_email_notification);
    $GLOBALS["default"]->srhst = md5(str_replace('www.', '', $_SERVER['HTTP_HOST']));
    $GLOBALS["user"]['id'] = $GLOBALS["roles"] = $GLOBALS["logged"] = false;
    $GLOBALS["reservedslugs"] = array("group");
    $GLOBALS["default"]->weburl = $GLOBALS["core"]->url;
    if (isset($_COOKIE["grupolang"]) && !empty(vc($_COOKIE['grupolang'], "num"))) {
        $GLOBALS["default"]->language = $_COOKIE["grupolang"];
    }
    $query = 'SELECT userid,';
    $query = $query.'coalesce((SELECT v2 as v2 FROM gr_options ';
    $query = $query.'WHERE type="profile" AND v1="language" AND v3=userid LIMIT 1),"'.$GLOBALS["default"]->language.'") AS lang, ';
    $query = $query.'(SELECT role FROM gr_users WHERE id=userid LIMIT 1) AS role,';
    $query = $query.'(SELECT v2 FROM gr_options WHERE type="usrole" LIMIT 1) AS disabletrackcode,';
    if (!isset($_POST['act']) && stripos(pg(), 'chat/') !== FALSE) {
        $query = $query.'(SELECT v1 FROM gr_options WHERE type="usrole" LIMIT 1) AS usrole,';
        $query = $query.'(SELECT count(1) FROM gr_utrack WHERE ip="'.ip().'" AND dev="'.ip('dev').'" AND uid=userid AND tms > (CONVERT_TZ(NOW(),';
        $query = $query.'@@session.time_zone,"+05:30") - INTERVAL 300 SECOND)) AS usrtrack,';
    }
    $query = $query.'(SELECT count(1) FROM gr_options WHERE type="radiostation") AS radiostations,';
    $query = $query.'(SELECT v2 FROM gr_options WHERE v3=userid AND type="profile" AND v1="autotmz" LIMIT 1) AS autotms ';
    $query = $query.'FROM gr_defaults ';
    $query = $query.'LEFT JOIN (SELECT uid as userid FROM gr_session WHERE id="'.$_COOKIE['Grupousrses'].'" ';
    $query = $query.'AND device="'.$_COOKIE["Grupousrdev"].'" AND code="'.$_COOKIE["Grupousrcode"].'" ORDER BY id DESC LIMIT 1) usid ';
    $query = $query.'ON (v1 = "sitename") WHERE v1="sitename" LIMIT 1;';
    $GLOBALS["grusrlog"] = db('Grupo', 'q', $query)[0];

    if (isset($GLOBALS["grusrlog"]['userid']) && !empty($GLOBALS["grusrlog"]['userid'])) {
        $GLOBALS["user"]['id'] = $GLOBALS["grusrlog"]['userid'];
        $GLOBALS["user"]['active'] = $GLOBALS["logged"] = true;
        $GLOBALS["roles"] = gr_role('var', 0, $GLOBALS["grusrlog"]['role']);
        $GLOBALS["lang"] = gr_lang('var', $GLOBALS["grusrlog"]['lang']);
    } else {
        $GLOBALS["lang"] = gr_lang('var');
    }
    $GLOBALS["grusrlog"]['panelclass'] = 'col-md-7 col-lg-6';
    if (!$GLOBALS["logged"] && $GLOBALS["default"]->viewgroups_nologin == 'enable' && stripos(pg(), 'chat/') !== FALSE) {
        $GLOBALS["grusrlog"]['role'] = 1;
        $GLOBALS["roles"] = gr_role('var', 0, $GLOBALS["grusrlog"]['role']);
        $GLOBALS["user"]['id'] = 0;
        $GLOBALS["grusrlog"]['panelclass'] = 'col-md-7 col-lg-9';
    }
    $GLOBALS["lang"]->invisible = $GLOBALS["lang"]->offline;
    $GLOBALS["grusrlog"]["radiostatus"] = 'radiodisabled';
    if ($GLOBALS["grusrlog"]["radiostations"] > 0 && isset($GLOBALS["roles"]['features'][15])) {
        $GLOBALS["grusrlog"]["radiostatus"] = 'radioenabled';
    }
    $GLOBALS["grload"] = new stdClass();
    $GLOBALS["grload"]->group = 0;
    $GLOBALS["grload"]->passreq = $GLOBALS["grload"]->joined = 0;
    $GLOBALS["grload"]->user = 0;
    if (empty($GLOBALS["default"]->timezone) || $GLOBALS["default"]->timezone == 'Auto') {
        $GLOBALS["default"]->timezone = "Australia/Sydney";
        if (isset($GLOBALS["grusrlog"]['autotms']) && !empty($GLOBALS["grusrlog"]['autotms'])) {
            $GLOBALS["default"]->timezone = $GLOBALS["grusrlog"]['autotms'];
        }
    }
    gr_pgtransition();
    if ($GLOBALS["default"]->force_https == 'enable') {
        if (is_https() != true) {
            header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
            exit;
        }
    }

}
?>