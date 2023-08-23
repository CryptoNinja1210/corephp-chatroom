<?php if(!defined('s7V9pz')) {die();}?><?php
fc('grupo');
grupofns();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Grupo V2.6 Installation - BaeVox Powered</title>
    <meta name="description" content="Grupo Chatrooms">
    <meta name="author" content="BaeVox">
    <meta name="generator" content="Grupo - Powered by BaeVox">
    <link rel="shortcut icon" type="image/png" href="<?php pr(mf("grupo/global/favicon.png")); ?>" />
    <link href="https://fonts.googleapis.com/css?family=Montserrat:500,600,700,700i,800" rel="stylesheet">
    <link href="<?php gec($GLOBALS["core"]->url) ?>riches/kit/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["core"]->url) ?>riches/fonts/montserrat/font.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["core"]->url) ?>riches/fonts/grupo/css/icons.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["core"]->url) ?>gem/tone/ajx.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["core"]->url) ?>gem/tone/gr-sign.css" rel="stylesheet">
</head>
<body class="sign two bgtwo install">
    <section>
        <div>
            <div>
                <div class='box'>
                    <div class="logo">
                        <img src="<?php pr(mf("grupo/global/grupo.png")); ?>" />
                    </div>
                    <form autocomplete='off' class='gr_install'>
                        <div class="elements">
                            <input type="hidden" name="act" value=1 />
                            <input type="hidden" name="do" class='doz' value='install' />
                            <div class='stepone'>
                                <label><i class="gi-mail"></i>
                                    <input type="text" autocomplete='off' class='semail' name="email" placeholder="Email Address" />
                                </label>
                                <label><i class="gi-doc-text"></i>
                                    <input type="text" autocomplete='off' class='license' name="encde" placeholder="CodeCanyon Purchase Code" />
                                </label>
                                <label><i class="gi-rss"></i>
                                    <input type="text" autocomplete='off' name="host" placeholder="Database Host" />
                                </label>
                                <label><i class="gi-database"></i>
                                    <input type="text" autocomplete='off' name="db" placeholder="Database Name" />
                                </label>
                                <label><i class="gi-user"></i>
                                    <input type="text" autocomplete='off' name="user" placeholder="Database Username" />
                                </label>
                                <label><i class="gi-lock"></i>
                                    <input type="text" autocomplete='off' name="pass" placeholder="Database Password" />
                                </label>
                            </div>
                            <div class='steptwo d-none'>
                                <label><i class="gi-link"></i>
                                    <input type="text" autocomplete='off' class='surl' name="url" placeholder="Site URL" />
                                </label>
                                <label><i class="gi-home"></i>
                                    <input type="text" autocomplete='off' name="site" placeholder="Site Name" />
                                </label>
                                <label><i class="gi-user"></i>
                                    <input type="text" autocomplete='off' name="username" placeholder="Username" />
                                </label>

                                <label><i class="gi-lock"></i>
                                    <input type="password" autocomplete='off' name="password" placeholder="Password" />
                                </label>
                            </div>
                        </div>
                        <div class="regsep"></div>
                        <div class="submitbtns">
                            <span class="submit global install" form='.gr_install' url='<?php pr(url()); ?>install/'>Next Step</span>
                        </div>
                        <div class="switch">
                            <i>Stuck at Somewhere?</i>
                            <span class='say' say='Please send an e-mail to hello@baevox.com' sec='8000'><a href="mailto:hello@baevox.com">Contact Us</a></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>
<script src="<?php gec($GLOBALS["core"]->url) ?>riches/kit/jquery/jquery-3.4.1.min.js"></script>
<script src="<?php gec($GLOBALS["core"]->url) ?>riches/kit/jquery/jquery-migrate-1.4.1.min.js"></script>
<script src="<?php gec($GLOBALS["core"]->url) ?>riches/kit/popper/umd/popper.min.js"></script>
<script src="<?php gec($GLOBALS["core"]->url) ?>riches/kit/bootstrap/bootstrap.min.js"></script>
<script src="<?php gec($GLOBALS["core"]->url) ?>riches/kit/nicescroll/jquery.nicescroll.min.js"></script>
<script src="<?php gec($GLOBALS["core"]->url) ?>riches/kit/jscookie/js.cookie.js"></script>
<script src="<?php gec($GLOBALS["core"]->url) ?>gem/mine/ajx.js"></script>
<script src="<?php gec($GLOBALS["core"]->url) ?>gem/mine/gr-install.js"></script>
</html>