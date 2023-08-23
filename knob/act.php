<?php if(!defined('s7V9pz')) {die();}?><?php
fc('grupo');
$usr = usr('Grupo');
$main = pg('act');
$act = explode('/', $main);
if ($act[0] === 'cronjob') {
    gr_prnt('<style>div{font-family: sans-serif; font-size: 26px; color: darkgrey;height: 100%;display: flex; align-items: center; justify-content: center; text-align: center;}</style>');
    gr_prnt('<body><div>Cron Job Executed Successfully</div></body>');
    gr_cronjob();
    exit;
}
if ($act[0] === 'updates') {
    fc('grlive');
    gr_live();
    exit;
}
if ($GLOBALS["logged"]) {
    if ($act[0] === 'reset' && gr_role('access', 'sys', '1')) {
        fc('grsys');
        gr_globalreset($act);
        exit;
    }
    rt('');
} else {
    if ($act[0] != 'updates') {
        addcookie('actredirect', $main, 0, "/");
    }
    rt('signin');
}
?>