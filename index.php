<?php

if (($_SERVER['HTTP_REFERER'] == 'android-app://xyz.appmaker.bqbawq/' || (isset($_GET['android'])))  && !isset($_COOKIE['twp_check'])) {
	setcookie("twp_check", 'true');
}
//error_reporting(0);
require "key/open.php";
?>
