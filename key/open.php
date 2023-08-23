<?php define('s7V9pz', TRUE); ?>
<?php
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 1);
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 1);
if (!session_id()) {
    session_start();
}
include "bit.php";
date_default_timezone_set(cnf()["region"]);
include cnf()["door"]."/core/load.php";
load_knob();
?>