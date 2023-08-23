<?php if(!defined('s7V9pz')) {die();}?><?php
error_reporting(-1);
fc('grupo');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php gec($GLOBALS["lang"]->offline_page_title) ?>">
    <meta name="author" content="Baevox">
    <meta name="generator" content="Grupo">
    <title><?php gec($GLOBALS["lang"]->offline_page_title) ?></title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:500,600,700,700i,800" rel="stylesheet">
    <style>
        <?php
        include_once('gem/tone/404.css');
        ?>
    </style>
</head>
<body>
    <div id="notfound">
        <div class="notfound alas">
            <div class="notfound-404">
                <h1><?php gec($GLOBALS["lang"]->offline_page_alas) ?></h1>
            </div>
            <h2><?php gec($GLOBALS["lang"]->offline_page_heading) ?></h2>
            <p>
                <?php gec($GLOBALS["lang"]->offline_page_desc) ?>
            </p>
            <a href="<?php gec(url()); ?>"><?php gec($GLOBALS["lang"]->offline_page_go_to_btn) ?></a>
        </div>
    </div>

</body>
</html>