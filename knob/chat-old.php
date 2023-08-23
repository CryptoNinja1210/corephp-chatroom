<?php if(!defined('s7V9pz')) {die();}?><?php
fc('grupo');
if (!$GLOBALS["logged"] && $GLOBALS["default"]->viewgroups_nologin == 'disable') {
    if (isset($_POST['act'])) {
        $data[0] = new stdClass();
        $data[0]->liveup = 'refresh';
        gr_prnt(json_encode($data));
        exit;
    } else {
        $page = pg('chat');
        if (!empty($page)) {
            rt('signin/?goto=chat/'.$page);
        } else {
            rt('signin');
        }
    }
}
grupofns();
gr_unverified();
gr_profile('ustatus', 'online');
gr_usip('add');
gr_acton();
gr_metatags();
$GLOBALS["grads"] = gr_ads('get');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-status-bar-style" content="black" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no shrink-to-fit=no">
    <title><?php gec($GLOBALS["default"]->sitename.' - '.$GLOBALS["default"]->siteslogan); ?></title>
    <meta name="description" content="<?php gec($GLOBALS["default"]->sitedesc); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php gec($GLOBALS["default"]->weburl); ?>">
    <meta property="og:title" content="<?php gec($GLOBALS["default"]->sitename.' - '.$GLOBALS["default"]->siteslogan); ?>">
    <meta property="og:description" content="<?php gec($GLOBALS["default"]->sitedesc); ?>">
    <meta property="og:image" content="<?php gec($GLOBALS["default"]->grsitelogo); ?>">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php gec($GLOBALS["default"]->weburl); ?>">
    <meta property="twitter:title" content="<?php gec($GLOBALS["default"]->sitename.' - '.$GLOBALS["default"]->siteslogan); ?>">
    <meta property="twitter:description" content="<?php gec($GLOBALS["default"]->sitedesc); ?>">
    <meta property="twitter:image" content="<?php gec($GLOBALS["default"]->grsitelogo); ?>">
    <link rel="shortcut icon" type="image/png" href="<?php gec(mf("grupo/global/favicon.png")); ?>" />
    <link rel="apple-touch-icon" href="<?php gec(mf("grupo/global/icon192.png")); ?>" />
    <link rel='manifest' href='<?php gec($GLOBALS["default"]->weburl); ?>manifest/'>
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>riches/fonts/<?php gec($GLOBALS["default"]->default_font) ?>/font.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/emojionearea/dist/emojionearea.min.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/animate/animate.min.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/colorpicker/dist/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/viewerjs/viewer.min.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>riches/fonts/grupo/css/icons.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>gem/tone/ajx.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>gem/tone/grupo.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>gem/tone/grscroll.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>gem/tone/grupo-dark.css" rel="stylesheet">
    <?php gr_core('hf', 'header'); ?>
</head>
<body class='<?php gec(gr_profile('get', $GLOBALS["user"]['id'], 'skinmode')); ?>'>
    <?php gr_core('hf', 'bodyopen'); ?>
    <div class='gr-preloader'>
        <div>
            <span class="animate__animated animate__infinite animate__fadeIn"></span>
        </div>
    </div>
    <section class="swr-grupo baevox-powered<?php gec(' '.$GLOBALS["lang"]->core_align.' '.$GLOBALS["grusrlog"]["radiostatus"]) ?>">
        <div class='window fh'>
            <div class="container-fluid fh">
                <div class="row fh">
                    <?php
                    if ($GLOBALS["logged"] || empty($GLOBALS["grload"]->group) || $GLOBALS["default"]->hide_grouptab == 'disable') {
                        ?>
                        <div class="col-md-5 col-lg-3 aside lside">
                            <div class='head'>
                                <span class='menu'>
                                    <?php fc('grmenu'); ?>
                                </span>
                                <span class='logo'>
                                    <img src="<?php gec(mf("grupo/global/sitelogo.png")); ?>" class="d-none d-md-block" />
                                    <img src="<?php gec(mf("grupo/global/mobilelogo.png")); ?>" class="d-block d-md-none" />
                                </span>
                                <span class='icons'>
                                    <?php fc('grdoable'); ?>
                                </span>
                            </div>
                            <div class="search">
                                <i class="gi-search"></i>
                                <input type="text" placeholder='<?php gec($GLOBALS["lang"]->search_here) ?>' />
                            </div>
                            <div class="tabs">
                                <ul>
                                    <?php
                                    if (empty($GLOBALS["grload"]->group) || $GLOBALS["default"]->hide_grouptab == 'disable') {
                                        ?>
                                        <li class='active' act='groups' side='lside' openfirst='1' zero='0' unseen='0' zval='<?php gec($GLOBALS["lang"]->zero_groups) ?>'><?php gec($GLOBALS["lang"]->groups) ?> <i></i>
                                            <?php if (gr_role('access', 'groups', '6')) {
                                                ?>
                                                <ul class='subtab'>
                                                    <li filtr='all'><?php gec($GLOBALS["lang"]->all) ?></li>
                                                    <li filtr='joined'><?php gec($GLOBALS["lang"]->joined) ?></li>
                                                    <li filtr='unjoined'><?php gec($GLOBALS["lang"]->unjoined) ?></li>
                                                </ul>
                                                <?php
                                            } ?>
                                        </li>
                                        <?php
                                    }
                                    if (gr_role('access', 'privatemsg', '2')) {
                                        ?>
                                        <li act='pm' side='lside' zero='0' unread='0' zval='<?php gec($GLOBALS["lang"]->zero_pm) ?>'><?php gec($GLOBALS["lang"]->pm) ?> <i></i></li>
                                        <?php
                                    } ?>
                                    <?php
                                    if (gr_role('access', 'files', '5')) {
                                        ?>
                                        <li act='files' <?php if (gr_role('access', 'files', '1')) { gec('class=uploadable'); } ?> side='lside' zero='0KB' zval='<?php gec($GLOBALS["lang"]->zero_files) ?>'><?php gec($GLOBALS["lang"]->files) ?></li>
                                        <?php
                                    } ?>
                                    <?php
                                    if (gr_role('access', 'users', '5') && $GLOBALS["default"]->show_online_tab == 'enable') {
                                        ?>
                                        <li act='online' side='lside' zero='0' unread='0' zval='<?php gec($GLOBALS["lang"]->zero_online) ?>'><?php gec($GLOBALS["lang"]->online) ?> <i></i></li>
                                        <?php
                                    } ?>

                                    <li side='lside' class='xtra'></li>
                                    <span class="gruploader">
                                        <i class="gi-up-open-1 animate__animated animate__rotateIn animate__infinite" data-toggle="tooltip" title="<?php gec($GLOBALS["lang"]->uploading) ?>"></i>
                                    </span>
                                </ul>
                            </div>
                            <div class="content">
                                <div class='grloader listloader'>
                                    <div>
                                        <div>
                                            <div class="spin">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class='dragfile dragupload'>
                                    <div>
                                        <div>
                                            <div class="icon"></div>
                                        </div>
                                    </div>
                                </div>
                                <span class="d-none grproceed loadside appnd" offset=0></span>
                                <ul class='list fh'>

                                </ul>
                                <span class="addmore">
                                    <span>
                                        <i class="gi-plus"></i>
                                    </span>
                                </span>
                                <?php gr_ads('place', 'leftside'); ?>
                            </div>
                            <?php if ($GLOBALS["grusrlog"]["radiostatus"] == 'radioenabled') {
                                ?>
                                <div class='grradioplayer onloaded'>
                                    <div>
                                        <span class='rinfo'>
                                            <img src="" />
                                            <span>
                                                <b><span></span><i class="gi-down-open"></i>
                                                    <ul class="radiolist">
                                                        <?php gr_radiostations('show'); ?>
                                                    </ul>
                                                </b>
                                                <span>
                                                    <span></span>
                                                </span>
                                            </span>
                                        </span>
                                        <span class='rcontrols'>
                                            <i class='rprev'></i>
                                            <i class='rplay'></i>
                                            <i class='rnext'></i>
                                        </span>
                                        <audio id='radioplayerstream' muted="muted" controls name="media">
                                            <source src="" type="audio/mpeg">
                                        </audio>
                                    </div>
                                </div>
                                <?php
                            } ?>
                        </div>
                        <?php
                    } else {
                        $GLOBALS["grusrlog"]['panelclass'] = 'col-md-12 col-lg-12';
                    } ?>
                    <div class="<?php gec($GLOBALS["grusrlog"]['panelclass'].' '); ?>nomob panel" no=0 ldt=0 lstseen=0 deactiv=0>
                        <div class='head groupnav d-none'>
                            <?php if ($GLOBALS["logged"]) {
                                ?>
                                <i class="icon gi-left-open tabclose d-none animate__animated animate__fadeInRight" side="right"></i>
                                <?php
                            } ?>
                            <i class='icon gi-left-open goback d-md-none'></i>
                            <span class='left'>
                                <span>
                                    <img class="lazyimg" data-src="<?php gec(mf("grupo/global/load.gif")); ?>">
                                    <span></span>
                                </span></span>
                            <span class='right'>
                                <?php if ($GLOBALS["logged"]) {
                                    ?>
                                    <i class='gi-bell-1 malert tabclose d-none d-md-block d-lg-none' data-block='palert' side="left"></i>
                                    <i class='gi-bell-1 malert goright d-md-none' data-block='palert'></i>
                                    <?php
                                    if (gr_role('access', 'files', '5')) {
                                        ?>
                                        <i class='icon gi-archive goback d-md-none' data-block="files"></i>
                                        <?php
                                    } if (isset($GLOBALS["roles"]['groups'][16])) {
                                        ?>
                                        <i class='gi-users goright d-md-none' data-block='crew'></i>
                                        <?php
                                    } ?>
                                    <i class="gi-search searchmsgs"></i>
                                    <i class="gi-switch d-none d-sm-inline-block fullview"></i>
                                    <i class="gi-dot-3 subnav">
                                        <div class='swr-menu r-end'>
                                            <ul></ul>
                                        </div>
                                    </i>
                                    <?php
                                } ?>
                            </span>
                        </div>
                        <div class="searchbar">
                            <span>
                                <i class="gi-search"></i>
                                <input type="text" placeholder='<?php gec($GLOBALS["lang"]->search_messages) ?>' />
                            </span>
                        </div>
                        <div class='room fh'>
                            <span class='groupreload'><i class='turnchat' do='on'><i class='gi-ccw'></i><?php gec($GLOBALS["lang"]->reload) ?></i></span>
                            <div class='grloader msgloader'>
                                <div>
                                    <div>
                                        <div class="spin"></div>
                                    </div>
                                </div>
                            </div>
                            <div class='dragfile dragattach'>
                                <div>
                                    <div>
                                        <div class="icon"></div>
                                    </div>
                                </div>
                            </div>
                            <ul class='msgs fh'>
                                <div class='zeroelem fh'>
                                    <div class="welcome">
                                        <span>
                                            <img src="<?php gec(mf("grupo/global/welcome.png")); ?>" />
                                            <i class="title"><?php gec($GLOBALS["lang"]->welcome_user) ?></i>
                                            <i class="desc"><?php gec($GLOBALS["lang"]->welcome_msg) ?></i>
                                            <i class="foot"><?php gec($GLOBALS["lang"]->welcome_footer) ?></i>
                                        </span>
                                        <?php gr_ads('place', 'welcome'); ?>
                                    </div>
                                </div>
                            </ul>
                        </div>
                        <div class='textbox d-none disabled'>
                            <?php if (!$GLOBALS["logged"]) {
                                ?>
                                <div class="logintxt">
                                    <span class="loadlink" link="./signin/"><?php gec($GLOBALS["lang"]->login_register_msg) ?></span>
                                </div>
                                <span class='box opaque'>
                                    <textarea placeholder="<?php gec($GLOBALS["lang"]->type_message) ?>"></textarea>
                                </span>
                                <?php
                            } else {
                                ?>
                                <div class="mentstore"></div>
                                <div class="mentions">
                                    <ul>
                                    </ul>
                                    <input type='hidden' />
                                </div>
                                <?php
                                if (isset($GLOBALS["roles"]['features'][17]) || $GLOBALS["default"]->tenor_enable == 'enable' && gr_role('access', 'features', '3')) {
                                    ?>
                                    <div class="grgif">
                                        <div class="wrap">
                                            <span class="switchtabs">
                                                <ul>
                                                    <?php if ($GLOBALS["default"]->tenor_enable == 'enable' && gr_role('access', 'features', '3')) {
                                                        ?>
                                                        <li load="gifs" class="active"><?php gec($GLOBALS["lang"]->gifs) ?></li>
                                                        <?php
                                                    } if (isset($GLOBALS["roles"]['features'][17])) {
                                                        ?>
                                                        <li load="stickers"><?php gec($GLOBALS["lang"]->stickers) ?></li>
                                                        <?php
                                                    } ?>
                                                </ul>
                                            </span>
                                            <span class="search"><input spellcheck="false" type="text" placeholder="<?php gec($GLOBALS["lang"]->search_gifs_tenor) ?>" /></span>
                                            <span class="stickerpacks">
                                                <span class="navg gi-left-open"></span>
                                                <span class="packs">
                                                    <ul>
                                                        <?php gr_stickers('show'); ?>
                                                    </ul>
                                                </span>
                                                <span class="navg gi-right-open"></span>
                                            </span>
                                            <div class="gifs">
                                                <span class="loading"></span>
                                                <ul class='grgifconts'>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                } ?>
                                <span class='box'>
                                    <span class='icon left'>
                                        <?php
                                        if (isset($GLOBALS["roles"]['features'][17]) || $GLOBALS["default"]->tenor_enable == 'enable' && gr_role('access', 'features', '3')) {
                                            ?>
                                            <i class='gr-gif' data-toggle="tooltip" title="<?php gec($GLOBALS["lang"]->gifs_stickers) ?>"></i>
                                            <?php
                                        } ?>
                                        <i class='gr-emoji' data-toggle="tooltip" title="<?php gec($GLOBALS["lang"]->emojis) ?>"></i>
                                    </span>
                                    <textarea placeholder="<?php gec($GLOBALS["lang"]->type_message) ?>"></textarea>
                                    <?php if (isset($GLOBALS["roles"]['features'][13])) {
                                        ?>
                                        <span class="switchuser">
                                            <span class="usrimg" data-toggle="tooltip" title="<?php gec($GLOBALS["lang"]->send_as_user) ?>"><img class="lazyimg" data-src="<?php gec(gr_img('users', $GLOBALS["user"]['id'])); ?>"></span>
                                            <span class="uslist">
                                                <span>
                                                    <i class="gi-search"></i>
                                                    <input type="text" spellcheck="false" placeholder='<?php gec($GLOBALS["lang"]->search_here) ?>' />
                                                </span>
                                                <ul></ul>
                                            </span>
                                        </span>
                                        <?php
                                    } ?>
                                    <span class='icon'>
                                        <?php
                                        if (gr_role('access', 'features', '2') || gr_role('access', 'features', '4') || gr_role('access', 'files', '4')) {
                                            ?>
                                            <span class='gr-moreico'>
                                                <i class='icon'></i>
                                                <ul>
                                                    <?php
                                                    if (gr_role('access', 'features', '4')) {
                                                        ?>
                                                        <li data-toggle="tooltip" title="<?php gec($GLOBALS["lang"]->qrcode) ?>">
                                                            <span><i class='gr-qrcode'></i></span>
                                                        </li>
                                                        <?php
                                                    } ?>
                                                    <?php
                                                    if (gr_role('access', 'features', '2')) {
                                                        ?>
                                                        <li class="grrecord">
                                                            <span><i class='gr-mrec record' data-toggle="tooltip" title="<?php gec($GLOBALS["lang"]->voice_message) ?>"></i></span>
                                                        </li>
                                                        <?php
                                                    } ?>
                                                    <?php
                                                    if (gr_role('access', 'files', '4')) {
                                                        ?>
                                                        <li data-toggle="tooltip" title="<?php gec($GLOBALS["lang"]->attach) ?>">
                                                            <span><i class='gr-attach'>
                                                                <form class='atchmsg' enctype="multipart/form-data">
                                                                    <input type="hidden" name="act" value="1">
                                                                    <input type="hidden" name="do" value="group">
                                                                    <input type="hidden" name="id" class='gid'>
                                                                    <input type="hidden" name="type" value="attachmsg">
                                                                    <input type='file' multiple name='attachfile' class='attachfile' />
                                                                </form>
                                                            </i></span>
                                                        </li>
                                                        <?php
                                                    } ?>
                                                </ul>
                                            </span>
                                            <?php
                                        } ?>
                                    </span>
                                    <input type='hidden' value=0 class='replyid' />
                                    <input type='hidden' value=0 class='userid' />
                                    <i class='sendbtn'><i></i></i>
                                </span>
                                <?php
                            } ?>
                        </div>
                    </div>
                    <?php if ($GLOBALS["logged"]) {
                        ?>
                        <div class="col-md-5 col-lg-3 nomob aside rside tabfold">
                            <div class='top'>
                                <span class='left'>
                                    <i class='icon gi-left-open goback d-md-none'></i>
                                    <span class="vwp" no="<?php gec($GLOBALS["user"]['id']); ?>">
                                        <img class="lazyimg" data-src="<?php gec(gr_img('users', $GLOBALS["user"]['id'])); ?>">
                                        <span><?php gec(gr_profile('get', $GLOBALS["user"]['id'], 'name')); ?>
                                            <span>@<?php gec(usr('Grupo', 'select', $GLOBALS["user"]['id'])['name']); ?></span>
                                        </span>
                                    </span></span>
                                <span class='right'>
                                    <?php gec(gr_lang('list')) ?>
                                </span>
                            </div>
                            <div class="search">
                                <i class="gi-search"></i>
                                <input type="text" spellcheck="false" placeholder='<?php gec($GLOBALS["lang"]->search_here) ?>' />
                            </div>
                            <div class="tabs">
                                <ul>
                                    <li act='alerts' unread=0 zero='0' zval='<?php gec($GLOBALS["lang"]->zero_alerts) ?>' side='rside'><?php gec($GLOBALS["lang"]->alerts) ?> <i></i></li>
                                    <?php if (isset($GLOBALS["roles"]['groups'][16])) {
                                        ?>
                                        <li act='crew' class='grtab d-none' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_crew) ?>' side='rside'><?php gec($GLOBALS["lang"]->crew) ?>
                                            <ul class='subtab'>
                                                <li filtr='all'><?php gec($GLOBALS["lang"]->all) ?></li>
                                                <li filtr='recent'><?php gec($GLOBALS["lang"]->newest) ?></li>
                                                <?php if (isset($GLOBALS["roles"]['groups'][18])) {
                                                    ?>
                                                    <li filtr='browsing'><?php gec($GLOBALS["lang"]->browsing) ?></li>
                                                    <?php
                                                } ?>
                                            </ul>
                                        </li>
                                        <?php
                                    } ?>
                                    <li act='complaints' comp=0 unread=0 class='grtab d-none' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_complaints) ?>' side='rside'><?php gec($GLOBALS["lang"]->complaints) ?> <i></i></li>
                                    <li side='rside' class='xtra'></li>
                                </ul>
                            </div>
                            <div class="content">
                                <div class='grloader listloader'>
                                    <div>
                                        <div>
                                            <div class="spin"></div>
                                        </div>
                                    </div>
                                </div>
                                <span class="d-none grproceed loadside appnd" offset=0></span>
                                <ul class='list fh groups'>

                                </ul>
                                <span class="addmore">
                                    <span>
                                        <i class="gi-plus"></i>
                                    </span>
                                </span>
                                <div class="profile">
                                    <div class="top">
                                        <span class="coverpic"><img class="lazyimg" data-src="" /><span></span></span>
                                        <span class="edit"><span><i class="gi-picture-1"></i></span><i class='formpop' title='<?php gec($GLOBALS["lang"]->edit_profile) ?>' data-side="profile" do='edit' btn='<?php gec($GLOBALS["lang"]->update) ?>' xtid="" act='profile'><?php gec($GLOBALS["lang"]->edit_profile) ?></i></span>
                                        <span class="dp"><img class="lazyimg" data-src="" /></span>
                                        <span class="roleimg"></span>
                                        <span class="name"></span>
                                        <span class="role"></span>
                                        <span class="refresh vwp d-none">refresh</span>
                                    </div>
                                    <div class="middle">
                                        <span class="pm loadgroup" ldt="user" no=""></span>
                                        <span class="stats">
                                            <span><span>0</span><i></i></span>
                                            <span><span>0</span><i></i></span>
                                            <span class="last"><span>0</span><i></i></span>
                                            <b><i class="gi-lock-1"></i></b>
                                        </span>
                                    </div>
                                    <div class="bottom">
                                        <div>
                                            <ul>
                                            </ul>
                                            <div>
                                                <div>
                                                    <span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php gr_ads('place', 'rightside'); ?>
                            </div>
                        </div>
                        <?php
                    } ?>

                </div>
            </div>
        </div>
    </section>
    <section class="grupo-standby">
        <div>
            <span><img class="lazyimg" data-src="<?php gec(mf("grupo/global/sitelogo.png")); ?>" /></span>
        </div>
    </section>

    <section class="grupo-pop<?php gec(' '.$GLOBALS["lang"]->core_align) ?>">
        <div>
            <form method='post' autocomplete="off" class='grform' spellcheck="false">
                <span class="grformspin">
                    <span></span>
                </span>
                <span class="head"></span>
                <span class="search">
                    <i class="gi-search"></i>
                    <input spellcheck="false" type="text" placeholder="<?php gec($GLOBALS["lang"]->search_here) ?>" />
                </span>
                <div class="fields">

                </div>

                <input type="hidden" name="act" value="1">
                <input type="hidden" name="do" class="grdo">
                <input type="hidden" name="type" class="grtype">
                <input type="submit" class='grsub notranslate' translate="no" form='.grform'>
                <span class="cancel"><?php gec($GLOBALS["lang"]->cancel) ?></span>
            </form>
        </div>
    </section>

    <section class="grupo-video">
        <div>
            <div>
                <span> <i class="gi-cancel"></i></span>
            </div>
        </div>
    </section>
    <section class="gr-prvlink">
        <div class="grdrag">
            <i class="gi-cancel"></i>
            <span>
                <span class="loading"></span>
            </span>
            <img alt="preview" />
            <i class="submt">open</i>
        </div>
    </section>
    <section class="grupo-preview">
        <div>
            <div class="loader grdrag">
                <div class="gr-ldone">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
            <div class="img grdrag">
                <div></div>
                <span class="cntrls">
                    <i class='gi-plus'></i>
                    <i class='gi-minus'></i>
                    <i class='gi-cancel prclose'></i>
                </span>
            </div>
            <div class="video grdrag">
                <span class="prmove"></span>
                <span class="prclose"></span>
                <div>
                    <video id="videprvw" controls muted="muted">
                        <source src="" type="video/mp4">
                    </video>
                </div>
            </div>
            <div class="embed grdrag">
                <span class="prmove"></span>
                <span class="prclose"></span>
                <div>
                </div>
            </div>
        </div>
    </section>
    <div class="out d-none"></div>
    <span class='autodelmsgz d-none'><?php gec(vc($GLOBALS["default"]->autodeletemsg, 'num')) ?></span>
    <span class='pastescreen d-none'></span>
    <div class="dumb d-none">
        <span class='loadside goback lastseenz' act='lastseen' zero='0' gmid=0 zval='<?php gec($GLOBALS["lang"]->zero_seen) ?>' side='rside'><?php gec($GLOBALS["lang"]->seen_by) ?></span>
        <span class='loadside srchbx' act='search' srch='' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_search) ?>' side='lside'><?php gec($GLOBALS["lang"]->search) ?></span>
        <span class='liveupdate'><?php gec($GLOBALS["lang"]->refresh) ?></span>
        <span class='webtitle'><?php gec($GLOBALS["default"]->sitename.' - '.$GLOBALS["default"]->siteslogan); ?></span>
        <span class='newmsgalert'><?php gec($GLOBALS["lang"]->newmsgalert) ?></span>
        <input type="hidden" class='liveuptime' value="<?php gec(vc($GLOBALS["default"]->refreshrate, 'num')) ?>" />
        <span class="loadgroup"></span>
        <audio id='gralert' muted="muted">
            <source src="<?php gec($GLOBALS["default"]->weburl.gr_profile('get', $GLOBALS["user"]['id'], 'alert')); ?>" />
        </audio>
        <input type='hidden' class='hidid' value=1/>
        <li class='loadside ruserz' act='rusers' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_users) ?>' side='rside'><?php gec($GLOBALS["lang"]->users) ?></li>
        <audio id="graudio" muted="muted">
            <source src="" type="audio/mp3">
        </audio>
        <div class="sendaudiomsg">
            Record Audio
        </div>
        <div class="gdefaults">
            <span class='baseurl'><?php gec($GLOBALS["default"]->weburl) ?></span>
            <span class='tenorapi'><?php gec($GLOBALS["default"]->tenor_api) ?></span>
            <span class='pagespeedapi'><?php gec($GLOBALS["default"]->pagespeed_api) ?></span>
            <span class='tenorlimit'><?php gec(vc($GLOBALS["default"]->tenor_limit, 'num')) ?></span>
            <span class="defload"></span>
            <span class="minmsglen"><?php gec(vc($GLOBALS["default"]->min_msg_length, 'num')) ?></span>
            <span class="maxmsglen"><?php gec(vc($GLOBALS["default"]->max_msg_length, 'num')) ?></span>
            <span class="enabletextarea"><?php gec (gr_role('access', 'features', '1')) ?></span>
            <span class="updatelists"><?php gec($GLOBALS["default"]->update_list_periodically) ?></span>
            <span class="sharescreenshot"><?php gec (gr_role('access', 'features', '6')) ?></span>
            <span class="rdmre"><?php gec(vc($GLOBALS["default"]->add_readmore_after, 'num')) ?></span>
            <span class="sndmsgalgn"><?php gec($GLOBALS["default"]->sent_msg_align) ?></span>
            <span class="rcvmsgalgn"><?php gec($GLOBALS["default"]->received_msg_align) ?></span>
            <span class="asciismileys"><?php gec($GLOBALS["default"]->ascii_smileys) ?></span>
            <span class="enterassend"><?php gec($GLOBALS["default"]->use_enter_as_send) ?></span>
            <span class="pagetransstart"><?php gec($GLOBALS["default"]->pagetransstart) ?></span>
            <span class="pagetransend"><?php gec($GLOBALS["default"]->pagetransend) ?></span>
            <span class="msgstyle"><?php gec($GLOBALS["default"]->message_style) ?></span>
            <span class="maxfilesize"><?php gec($GLOBALS["roles"]["xtras"]["maxfileuploadsize"]) ?></span>
            <span class="autoplayradio"><?php gec($GLOBALS["default"]->autoplay_radio) ?></span>
            <span id="pwadirectory">/grupo/</span>
        </div>
        <div class="gphrases">
            <span class='sending'><?php gec($GLOBALS["lang"]->sending) ?></span>
            <span class="sendinglimitreached"><?php gec($GLOBALS["lang"]->sending_limit_reached) ?></span>
            <span class='uploading'><?php gec($GLOBALS["lang"]->uploading) ?></span>
            <span class='loading'><?php gec($GLOBALS["lang"]->loading) ?></span>
            <span class='pleasewait'><?php gec($GLOBALS["lang"]->please_wait) ?></span>
            <span class='readmore'><?php gec($GLOBALS["lang"]->read_more) ?></span>
            <span class='failed'><?php gec($GLOBALS["lang"]->failed) ?></span>
            <span class='searchmin'><?php gec($GLOBALS["lang"]->search_min) ?></span>
            <span class='visit'><?php gec($GLOBALS["lang"]->visit) ?></span>
            <span class='play'><?php gec($GLOBALS["lang"]->play) ?></span>
            <span class='istyping'><?php gec($GLOBALS["lang"]->is_typing) ?></span>
            <span class='minlenreq'><?php gec($GLOBALS["lang"]->req_min_msg_length) ?> (<?php gec(vc($GLOBALS["default"]->min_msg_length, 'num')) ?>)</span>
            <span class='exceededmsg'><?php gec($GLOBALS["lang"]->exceeded_max_msg_length) ?></span>
            <span class='notxtmsg'><?php gec($GLOBALS["lang"]->notxtmsg) ?></span>
            <span class='new'><?php gec($GLOBALS["lang"]->new) ?></span>
            <span class='prfnoexists'><?php gec($GLOBALS["lang"]->profile_noexists) ?></span>
            <span class='maxfilesizelimit'><?php gec($GLOBALS["lang"]->exceeds_maxfilesizelimit) ?></span>
        </div>
        <div class="firstload">
            <?php
            if (!empty($GLOBALS["grload"]->group)) {
                if (!empty($GLOBALS["grload"]->joined)) {
                    gr_prnt('<span class="loadgroup" ldt="group" no="'.$GLOBALS["grload"]->group.'">loadgroup</span>');
                } else {
                    if ($GLOBALS["default"]->join_confirm == 'enable' || !empty($GLOBALS["grload"]->passreq)) {
                        gr_prnt('<span class="formpop" title="'.$GLOBALS["lang"]->join_group.'" do="group" ldt="group" btn="'.$GLOBALS["lang"]->join.'" act="join" no="'.$GLOBALS["grload"]->group.'">joingroup</span>');
                    } else {
                        gr_prnt('<span class="ajx" data-do="group" data-type="join" data-act="1" data-id="'.$GLOBALS["grload"]->group.'">joingroup</span>');
                    }
                }
            } else if (!empty($GLOBALS["grload"]->user)) {
                gr_prnt('<span class="loadgroup" ldt="user" no="'.$GLOBALS["grload"]->user.'">loaduser</span>');
            }
            ?>
        </div>
        <pre id="log"></pre>
    </div>
    <?php gr_core('hf', 'bodyclose'); ?>
</body>

<link href="<?php gec($GLOBALS["default"]->weburl) ?>gem/tone/custom.css" rel="stylesheet">
<?php
gr_cbg();
?>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/jquery/jquery-3.4.1.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/jquery/jquery-migrate-1.4.1.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/jqueryui/jquery-ui.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/jqueryui/jquery.ui.touch-punch.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/jquerylazy/jquery.lazy.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/jquerylazy/jquery.lazy.plugins.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/popper/umd/popper.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/bootstrap/bootstrap.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/agora/AgoraRTC_N-4.3.0.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/textcomplete/dist/jquery.textcomplete.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/jsvideourlparser/dist/jsVideoUrlParser.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/emojionearea/dist/emojionearea.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/emojionearea/dist/asciiemoji.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/audiorecorderjs/lib/WebAudioRecorder.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/qrcode/qrcode.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/nicescroll/jquery.nicescroll.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/jscookie/js.cookie.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/viewerjs/viewer.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/momentjs/moment.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/momentjs/moment-data.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/profanityfilter/jquery.profanityfilter.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/marqueejs/jquery.marquee.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>gem/mine/ajx.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>gem/mine/caret.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>gem/mine/gr-mic.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>gem/mine/grgifs.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>gem/mine/gr-live.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>gem/mine/grupo.js"></script>
<?php
gr_google();
gr_reactprof();
gr_core('hf', 'footer');
?>
<script type="module">
    import 'https://cdn.jsdelivr.net/npm/@pwabuilder/pwainstall';
    const el = document.createElement('pwa-update');
    document.body.appendChild(el);
</script>
</html>