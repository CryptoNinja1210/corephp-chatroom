<?php if(!defined('s7V9pz')) {die();}?><?php
error_reporting(0);
fc('grupo');
$file = explode('/', pg('download'))[0];
if (!isset($file) || empty($file)) {
    rt('404');
}
if (gr_role('access', 'files', '2')) {
    $zn = "grupo/files/dumb/".$file;
    flr('download', $zn);
} else {
    rt('404');
}
?>