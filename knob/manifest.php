<?php if(!defined('s7V9pz')) {die();}?><?php
fc('grupo');
if (!headers_sent()) {
    header('Content-Type: application/json');
}
$path = parse_url(url(), PHP_URL_PATH);
?>
{
"short_name": "<?php gec($GLOBALS["default"]->sitename) ?>",
"name": "<?php gec($GLOBALS["default"]->sitename) ?>",
"description": "<?php gec($GLOBALS["default"]->sitedesc) ?>",
"icons": [
{
"src": "<?php gec($path) ?>gem/ore/grupo/global/icon192.png",
"type": "image/png",
"sizes": "192x192"
},
{
"src": "<?php gec($path) ?>gem/ore/grupo/global/icon512.png",
"type": "image/png",
"sizes": "512x512"
}
],
"start_url": "<?php gec($path) ?>",
"background_color": "#000000",
"display": "fullscreen",
"scope": "<?php gec($path) ?>",
"theme_color": "#fff"
}