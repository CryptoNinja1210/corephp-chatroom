<?php if(!defined('s7V9pz')) {die();}?><?php
fc('grupo');
if ($GLOBALS["logged"]) {
    if (isset($_POST["do"])) {
        gec('location.reload();');
    } else {
        if (!isset($_GET['goto'])) {
            $_GET['goto'] = 'chat/sabaya/';
        }
        rt($_GET['goto']);
    }
}
gr_loginproviders('connect');
grupofns();
gr_metatags();
$GLOBALS["grads"] = gr_ads('get', 'signin');
$pgload = str_replace('/', '', pg('signin/pg'));

if ($GLOBALS["logged"]) {
    if (isset($_POST["do"])) {
        gec('location.reload();');
    } else {
        if (!isset($_GET['goto'])) {
            $_GET['goto'] = 'chat/sabaya/';
        }
        rt($_GET['goto']);
    }
}


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
    <meta property="og:url" content="<?php gec(url()); ?>">
    <meta property="og:title" content="<?php gec($GLOBALS["default"]->sitename.' - '.$GLOBALS["default"]->siteslogan); ?>">
    <meta property="og:description" content="<?php gec($GLOBALS["default"]->sitedesc); ?>">
    <meta property="og:image" content="<?php gec($GLOBALS["default"]->grsitelogo); ?>">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php gec(url()); ?>">
    <meta property="twitter:title" content="<?php gec($GLOBALS["default"]->sitename.' - '.$GLOBALS["default"]->siteslogan); ?>">
    <meta property="twitter:description" content="<?php gec($GLOBALS["default"]->sitedesc); ?>">
    <meta property="twitter:image" content="<?php gec($GLOBALS["default"]->grsitelogo); ?>">
    <link rel="shortcut icon" type="image/png" href="<?php gec(mf("grupo/global/favicon.png")); ?>" />
    <link rel="apple-touch-icon" href="<?php gec(mf("grupo/global/icon192.png")); ?>" />
    <link rel='manifest' href='<?php gec($GLOBALS["default"]->weburl); ?>manifest/'>
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>riches/fonts/<?php gec($GLOBALS["default"]->default_font) ?>/font.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>riches/fonts/grupo/css/icons.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>gem/tone/ajx.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>gem/tone/gr-sign.css" rel="stylesheet">
    <link href="<?php gec($GLOBALS["default"]->weburl) ?>gem/tone/grscroll.css" rel="stylesheet">
    <?php gr_core('hf', 'header'); ?>
    <style type="text/css">
        section {
            background: #fff;
        }
        .fullwrapper {
            background: rgb(73,98,253);
            background: -webkit-gradient(linear, left top, left bottom, color-stop(37%, rgba(73,98,253,1)), color-stop(96%, rgba(73,98,253,0.6167600829394257)));
            background: -o-linear-gradient(top, rgba(73,98,253,1) 37%, rgba(73,98,253,0.6167600829394257) 96%);
            background: linear-gradient(180deg, rgba(73,98,253,1) 37%, rgba(73,98,253,0.6167600829394257) 96%);
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
                -ms-flex-direction: row;
                    flex-direction: row;
        }
        @media all and (max-width: 991px){
            .fullwrapper {
                -webkit-box-orient: vertical !important;
                -webkit-box-direction: normal !important;
                -ms-flex-direction: column !important;
                flex-direction: column !important;
            }
        }
        .fullwrapper div .logo {
            background: transparent !important;
        }
        .fullwrapper div .logo img {
            width: 150px !important;
            animation: 5s logohanging linear infinite;
        }
        @keyframes logohanging {
            0% {transform: rotateZ(10deg)}
            10% {transform: rotateZ(-10deg)}
            20% {transform: rotateZ(10deg)}
            30% {transform: rotateZ(-10deg)}
            40% {transform: rotateZ(0deg)}
            50% {opacity: 1;}
            52% {opacity: 0;}
            54% {opacity: 1;}
            50% {opacity: 1;}
            85% {opacity: 1;}
            86% {opacity: 0;}
            88% {opacity: 1;}
            90% {opacity: 0;}
            92% {opacity: 1;}
        }
        @media all and (max-width: 767px){
            .fullwrapper div .logo img {
                width: 80px !important;
            }
        }
        .leftsidetexts h1 {
            font-size: 36px;
            font-weight: 800;
            color: #fff;
            text-transform: capitalize;
        }
        .leftsidetexts p {
            font-size: 18px;
            font-weight: 600;
            color: #fff;
            text-transform: capitalize;
        }
        .downlaodbtn {
            display: inline-block;
            padding: 12px 20px;
            background: #fff;
            color: rgb(73,98,253);
            font-size: 17px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 40px;
            text-transform: capitalize;
        }
        .downlaodbtn img {
            width: 30px;
            margin-right: 15px;
        }
        .downlaodbtn:hover {
            text-decoration: none;
        }
        .two > section > div > div > .box {
            background: #fff !important;
            border-radius: 5px;
            overflow: hidden;
            padding-top: 25px !important;
        }
        .swithlogin ul {
            display: -webkit-box !important;
            display: -ms-flexbox !important;
            display: flex !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
        .two > section > div > div > .box .swithlogin {
            margin-bottom: 20px;
        }
        .two > section > div > div > .box .swithlogin > ul > li {
            background: #fff !important;
            color: #202020 !important;
            font-weight: 600 !important;
            -webkit-transition: 0.4s !important;
            -o-transition: 0.4s !important;
            transition: 0.4s !important;
            float: none !important;
            margin-left: 0.66% !important;
            margin-right: 0.66% !important;
            width: auto !important;
            padding-left: 10px !important;
            padding-right: 10px !important;
            -webkit-box-flex: 1;
                -ms-flex-positive: 1;
                    flex-grow: 1;
        }
        .two > section > div > div > .box .swithlogin > ul > li:hover,
        .two > section > div > div > .box .swithlogin > ul > li.active {
            background: rgb(73,98,253) !important;
            background: -webkit-gradient(linear, left top, left bottom, color-stop(37%, rgba(73,98,253,1)), color-stop(96%, rgba(73,98,253,0.6167600829394257))) !important;
            background: -o-linear-gradient(top, rgba(73,98,253,1) 37%, rgba(73,98,253,0.6167600829394257) 96%) !important;
            background: linear-gradient(180deg, rgba(73,98,253,1) 37%, rgba(73,98,253,0.6167600829394257) 96%) !important;
            color: #fff !important;
        }
        .two > section > div > div form label {
            border: 2px solid #4962fd !important;
            border-radius: 40px !important;
            background: #fff !important;
        }
        .two > section > div > div form label > input, .two > section > div > div form label > i {
            color: #202020 !important;
        }
        .two > section > div > div form label > input {
            font-size: 14px !important;
        }
        .two > section > div > div form .submit {
            background: rgb(73,98,253) !important;
            background: -webkit-gradient(linear, left top, left bottom, color-stop(37%, rgba(73,98,253,1)), color-stop(96%, rgba(73,98,253,0.6167600829394257))) !important;
            background: -o-linear-gradient(top, rgba(73,98,253,1) 37%, rgba(73,98,253,0.6167600829394257) 96%) !important;
            background: linear-gradient(180deg, rgba(73,98,253,1) 37%, rgba(73,98,253,0.6167600829394257) 96%) !important;
            border-radius: 40px !important;
        }
        .sign > section > div > div form > .sub > span > i {
            border: 2px solid #4962fd !important;
        }
        .sign > section > div > div form > .sub > span > i > b.active {
            background: #4962fd !important;
        }
        .sub > span {
            color: #202020 !important;
            font-weight: 600 !important;
            font-size: 12px !important;
        }
        .sign > section > div > div form > .switch {
            background: #fff !important;
        }
        .sign > section > div > div form > .switch {
            padding: 0 22px !important;
        }
        .switch > p {
            color: #202020;
            font-weight: 600;
        }
        .sign > section > div > div form > .switch span {
            background: rgb(73,98,253) !important;
            background: -webkit-gradient(linear, left top, left bottom, color-stop(37%, rgba(73,98,253,1)), color-stop(96%, rgba(73,98,253,0.6167600829394257))) !important;
            background: -o-linear-gradient(top, rgba(73,98,253,1) 37%, rgba(73,98,253,0.6167600829394257) 96%) !important;
            background: linear-gradient(180deg, rgba(73,98,253,1) 37%, rgba(73,98,253,0.6167600829394257) 96%) !important;
            color: #fff !important;
            border-radius: 5px;
        }
        .sign > section > div > div form > .switch {
            border-top: none;
        }
        .tablewrapper {
            height: auto !important;
            min-height: auto !important;
            padding: 0px 0;
        }
        .table-responsive-sm {
            background: #fff;
            border-radius: 7px;
            overflow: hidden;
            margin-right: 20px;
        }
        .table {
            margin-bottom: 0 !important;
        }
        .table-responsive-sm .table tr:nth-child(odd) {
            background: rgba(214, 48, 49,.3);
        }
        .table-responsive-sm .table tr:nth-child(even) {
            background: rgba(9, 132, 227,.3)
        }
        .table td, .table th {
            border-top: none;
        }
        .table-responsive-sm .table tr th {
            color: #fff;
            font-weight: 600;
        }
        .table-responsive-sm .table tr th:nth-child(1) {
            background: #d63031;
        }
        .table-responsive-sm .table tr th:nth-child(2) {
            background: #00b894;
        }
        .table-responsive-sm .table tr th:nth-child(3) {
            background: #6c5ce7;
        }
        .table-responsive-sm .table tr th:nth-child(4) {
            background: #636e72;
        }
        .leftsidetexts.mobileview .logo img {
            width: 80px !important;
        }
        .leftsidetexts.mobileview .downlaodbtn {
            padding: 6px 15px !important;
        }
        @media all and (max-width: 991px) {
            .table-responsive-sm {
                margin-top: 0px;
            }
        }
    </style>
</head>
<body class="sign two bgone">
    <?php gr_core('hf', 'bodyopen'); ?>
    <div class="gr-lselect">
        <?php pr(gr_lang('list', 2)) ?>
    </div>
    <section>
        <div class="fullwrapper">
            <div class="leftsidetexts">
               <div class="logo">
                    <img src="<?php gec(mf("grupo/global/logo.png")); ?>" />
                </div>
                <h1>تطبيق شات عربي و دردشة صوتية</h1>
                <p>دردشة و شات صوتي و المزيد... جربنا</p>
                <a href="https://play.google.com/store/apps/details?id=xyz.appmaker.bqbawq" class="downlaodbtn"><img src="https://cdn.icon-icons.com/icons2/2119/PNG/512/social_google_play_store_icon_131220.png" alt="playstore">حمل تطبيقنا الان</a>
           </div>
            <div>
                <?php gr_ads('place', 'siginpageheader'); ?>
                <div class='box<?php gec(' '.$GLOBALS["lang"]->core_align) ?>'>
                    
                    <div class="errormsg">
                        <span></span>
                    </div>
                    <div class="swithlogin">
                        <ul>
                            <li class="active"><?php gec($GLOBALS["lang"]->login); ?></li>
                            <?php if ($GLOBALS["default"]->guest_login == 'enable') {
                                ?>
                                <li class="lag"><?php gec($GLOBALS["lang"]->login_as_guest); ?></li>
                                <?php
                            } ?>
                        </ul>
                    </div>
                    <form autocomplete='off' class='gr_sign'>
                        <div class="elements">
                            <input type="hidden" name="act" value=1 />
                            <input type="hidden" name="do" class='doz' value='login' />
                            <div class='register d-none'>
                                <label><i class="gi-user"></i>
                                    <input type="text" autocomplete='grautocmp' name="fname" placeholder="<?php gec($GLOBALS["lang"]->full_name) ?>" />
                                </label>
                                <label><i class="gi-mail"></i>
                                    <input type="email" autocomplete='grautocmp' name="email" placeholder="<?php gec($GLOBALS["lang"]->email_address) ?>" />
                                </label>
                                <label><i class="gi-globe"></i>
                                    <input type="text" autocomplete='grautocmp' name="name" placeholder="<?php gec($GLOBALS["lang"]->username) ?>" />
                                </label>
                                <?php gr_loginfields(); ?>
                            </div>

                            <div class='loginasguest d-none'>
                                <label><i class="gi-user"></i>
                                    <input type="text" autocomplete='grnickname' class="nickname" name="nickname" placeholder="<?php gec($GLOBALS["lang"]->nickname) ?>" />
                                </label>
                            </div>
                            <div class='login'>
                                <label><i class="gi-user"></i>
                                    <input type="text" autocomplete='grautocmp' name="sign" placeholder="<?php gec($GLOBALS["lang"]->email_username) ?>" />
                                </label>
                            </div>
                            <div class='global'>
                                <label><i class="gi-lock"></i>
                                    <input type="password" class='gstdep' autocomplete='grautocmp' name="pass" placeholder="<?php gec($GLOBALS["lang"]->password) ?>" />
                                </label>
                            </div>
                        </div>
                        <div class="regsep d-none"></div>
                        <div class="sub">
                            <span class='rmbr'>
                                <i><b class="active"></b>
                                    <input type="hidden" name="rmbr" value=1 />
                                </i>
                                <?php gec($GLOBALS["lang"]->remember_me) ?></span>
                            <span class="doer" data-do="forgot"><?php gec($GLOBALS["lang"]->forgot_password) ?></span>
                        </div>
                        <?php if ($GLOBALS["default"]->recaptcha == 'enable') {
                            ?>
                            <div class='recaptcha'>
                                <div class="g-recaptcha" data-theme='light' data-sitekey="<?php gec($GLOBALS["default"]->rsitekey) ?>"></div>
                            </div>
                            <?php
                        } ?>
                        <div class="submitbtns">
                            <span class="submit global" form='.gr_sign' do='login' btn='<?php gec($GLOBALS["lang"]->register); ?>' em='<?php gec($GLOBALS["lang"]->invalid_value); ?>' gst=0>
                                <?php gec($GLOBALS["lang"]->login); ?>
                            </span>
                            <span class="submit ajx reset d-none" form='.gr_sign'><?php gec($GLOBALS["lang"]->reset); ?></span>
                        </div>
                        <?php if ($GLOBALS["default"]->userreg == 'enable') {
                            ?>
                            <div class="switch mt-1" qn='<?php gec($GLOBALS["lang"]->already_have_account); ?>' btn='<?php gec($GLOBALS["lang"]->login); ?>'>
                              <span><?php gec($GLOBALS["lang"]->dont_have_account); ?> <?php gec($GLOBALS["lang"]->create); ?></span>
                               <p class="mb-1 text-capitalize mt-2">or login with</p>
                                <?php gr_loginproviders('show'); ?>
                            </div>
                            <?php
                        } ?>
                        <div class="footer">
                            <ul>
                                <li class='grpgopen' pg='about'><?php gec($GLOBALS["lang"]->nav_about) ?></li>
                                <li class='grpgopen' pg='terms'><?php gec($GLOBALS["lang"]->nav_terms) ?></li>
                                <li class='grpgopen' pg='privacy'><?php gec($GLOBALS["lang"]->nav_privacy) ?></li>
                                <li class='grpgopen' pg='contact'><?php gec($GLOBALS["lang"]->nav_contact) ?></li>
                            </ul>
                        </div>
                    </form>
                    
                    <div class='tos'>
                        <h4><span><?php gec($GLOBALS["lang"]->tos) ?></span><i class="gi-cancel-circled"></i></h4>
                        <p></p>
                    </div>
                </div>
                <?php gr_ads('place', 'siginpagefooter'); ?>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive-sm">
                            <table class="table">
                              <thead>
                                <tr>
                                  <th scope="col">المضيف الصوتي</th>
                                  <th scope="col">الوقت</th>
                                  <th scope="col">اليوم</th>
                                  <th scope="col">الفقرة</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>سارة</td>
                                  <td>8-10 AM<p>6-8 PM</p></td>
                                  <td>كل ايام الاسبوع</td>
                                  <td>دردشة صباحية<p>الغرفة العامة</p></td>
                                </tr>
                                 <tr>
                                  <td>ميرا</td>
                                  <td>10-12 AM<p>10-12PM</p></td>
                                  <td>كل ايام الاسبوع</td>
                                  <td>فقزه ابراج و دردشة <p>الغرفة العامة</p></td>
                                </tr>
                                 <tr>
                                  <td>سارة</td>
                                  <td>2:00-4:00 PM</td>
                                  <td>كل ايام الاسبوع</td>
                                  <td>مواضيع اجتماعية</td>
                                </tr>
                                 <tr>
                                  <td>رهف</td>
                                  <td>4-6 PM - 8-10 PM</td>
                                  <td>كل ايام الاسبوع</td>
                                  <td>مسابقات و نكت<p>الغرفة العامة</p></td>
                                </tr>
                                 <tr>
                                  <td>سيمو</td>
                                  <td>12-3 AM</td>
                                  <td>كل ايام الاسبوع</td>
                                  <td>مواضيع مختلفة<p>الغرفة العامة</p></td>
                                </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class='gr-consent<?php gec(' '.$GLOBALS["lang"]->core_align) ?>'>
        <span>
            <span><?php gec($GLOBALS["lang"]->cookie_constent); ?> <i class='d-none'><?php gec($GLOBALS["lang"]->tos); ?></i></span>
            <i><?php gec($GLOBALS["lang"]->got_it); ?></i>
        </span>
    </div>
    <div class="dumb d-none">
        <span class='unsplash'><?php gec($GLOBALS["default"]->unsplash_enable); ?></span>
        <span class='guestloginonload'><?php gec($GLOBALS["default"]->first_load_guestlogin); ?></span>
        <span class='unsplashid'><?php gec($GLOBALS["default"]->unsplash_load); ?></span>
        <span class='loading'><?php gec($GLOBALS["lang"]->loading) ?></span>
        <span class='pleasewait'><?php gec($GLOBALS["lang"]->please_wait) ?></span>
        <span class='cookieconsent'><?php gec($GLOBALS["default"]->cookie_consent) ?></span>
        <span class='baseurl'><?php gec($GLOBALS["default"]->weburl) ?></span>
        <span class='pgload'><?php gec($pgload) ?></span>
    </div>
    <div class="signbg"></div>
    <?php gr_core('hf', 'bodyclose'); ?>

    <!-- Modal -->
    <div class="modal fade" id="loginTermsCondition" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="loginTermsConditionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginTermsConditionLabel">Terms&Conditions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>1. Terms (please read) <br>
                    By accessing this app/wesbite, you are agreeing to be bound by the Terms and Conditions of Use, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws. If you do not agree with any of these terms, you are prohibited from using or accessing this website. The content contained here are protected by applicable copyright and trade mark laws. Please take the time to review our privacy policy at https://sabayachat.com/privacy-policy/

<p>By downloading or using the app, these terms will automatically apply to you – you should make sure therefore that you read them carefully before using the app. You’re not allowed to copy, or modify the app, any part of the app, or our trademarks in any way. You’re not allowed to attempt to extract the source code of the app, and you also shouldn’t try to translate the app into other languages, or make derivative versions. The app itself, and all the trade marks, copyright, database rights and other intellectual property rights related to it, still belong to THS Development.</p>

<p>THS Development is committed to ensuring that the app is as useful and efficient as possible. For that reason, we reserve the right to make changes to the app or to charge for its services, at any time and for any reason. We will never charge you for the app or its services without making it very clear to you exactly what you’re paying for.</p>

<p>The SabayaChat app stores and processes personal data that you have provided to us, in order to provide my Service. It’s your responsibility to keep your phone and access to the app secure. We therefore recommend that you do not jailbreak or root your phone, which is the process of removing software restrictions and limitations imposed by the official operating system of your device. It could make your phone vulnerable to malware/viruses/malicious programs, compromise your phone’s security features and it could mean that the SabayaChat app won’t work properly or at all.
</p>
<p>The app does use third party services that declare their own Terms and Conditions.</p>

<p>Link to Terms and Conditions of third party service providers used by the app</p>

   <p>Google Play Services</p>
    <p>Google Analytics for Firebase</p>
    <p>Facebook</p>
    <p>One Signal</p> 

<p>You should be aware that there are certain things that THS Development will not take responsibility for. Certain functions of the app will require the app to have an active internet connection. The connection can be Wi-Fi, or provided by your mobile network provider, but THS Development cannot take responsibility for the app not working at full functionality if you don’t have access to Wi-Fi, and you don’t have any of your data allowance left.</p>

<p>If you’re using the app outside of an area with Wi-Fi, you should remember that your terms of the agreement with your mobile network provider will still apply. As a result, you may be charged by your mobile provider for the cost of data for the duration of the connection while accessing the app, or other third party charges. In using the app, you’re accepting responsibility for any such charges, including roaming data charges if you use the app outside of your home territory (i.e. region or country) without turning off data roaming. If you are not the bill payer for the device on which you’re using the app, please be aware that we assume that you have received permission from the bill payer for using the app.
</p>
<p>Along the same lines, THS Development cannot always take responsibility for the way you use the app i.e. You need to make sure that your device stays charged – if it runs out of battery and you can’t turn it on to avail the Service, THS Development cannot accept responsibility.</p>

<p>With respect to THS Development’s responsibility for your use of the app, when you’re using the app, it’s important to bear in mind that although we endeavour to ensure that it is updated and correct at all times, we do rely on third parties to provide information to us so that we can make it available to you. THS Development accepts no liability for any loss, direct or indirect, you experience as a result of relying wholly on this functionality of the app.
</p>
<p>At some point, we may wish to update the app. The app is currently available on Android – the requirements for system(and for any additional systems we decide to extend the availability of the app to) may change, and you’ll need to download the updates if you want to keep using the app. THS Development does not promise that it will always update the app so that it is relevant to you and/or works with the Android version that you have installed on your device. However, you promise to always accept updates to the application when offered to you, We may also wish to stop providing the app, and may terminate use of it at any time without giving notice of termination to you. Unless we tell you otherwise, upon any termination, (a) the rights and licenses granted to you in these terms will end; (b) you must stop using the app, and (if needed) delete it from your device.</p>

<p>Changes to This Terms and Conditions</p>

<p>We may update our Terms and Conditions from time to time. Thus, you are advised to review this page periodically for any changes. We will notify you of any changes by posting the new Terms and Conditions on this page.</p>

<p>These terms and conditions are effective as of 2021-10-01</p>

<p>If you have any questions or suggestions about my Terms and Conditions, do not hesitate to contact me at info@SabayaChat.Com.</p>

                    </p>
                    
                    </p>
                    <p>2. Disclaimer<br>
                    The content on the app/website are provided "as is". We makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties, including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights. Furthermore, We does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the content on its website or otherwise relating to such content or on any sites linked to this site.
                    </p>
                    <p>3. Limitations<br>
                    In no event shall we be liable for any damages (including, without limitation, damages for loss of data or profit, due to business interruption, or criminal charges filed against you) arising out of the use or inability to use the content on the app/website, even if we or a authorized representative has been notified orally or in writing of the possibility of such damage. This applies to the use of our chat rooms and filemanager. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.
                    </p>
                    <p>4. Revisions and Errata<br>
                    The materials appearing on the app/website could include technical, typos, or image errors. We does not warrant that any of the content on its website are accurate, complete, or current. We may make changes to the content on its website at any time without any noticeWe does not, however, make any commitment to update the content.
                    </p>
                    <p>5. Links<br>
                    We has not reviewed all of the sites links from its app/website and is not responsible for the contents contained within. The inclusion of any link does not imply endorsement by us. Use of any such linked web site is at the user's own risk.
                    </p>
                    <p>6. Age Limitations<br>
                    In general, the age minimum for this webs site is 16. This website will not be held responsible for users who do not comply with the given age range as this information is not verifiable.
                    </p>
                    <p>7. Hateful Content<br>
                    We does not tolerate any form of hateful or violent content in our chat rooms or on our forums. This includes threats, promotion of violence or direct attacks towards other users based upon ethnicity, race, religion, sexual orientation, religion affiliation, age, disability, serious diseases and gender. Users also are prohibited from using hateful images for their profile pictures/avatars. This includes usernames. All such comment will be removed when noticed or reported to our staff, which may result in Ban or permenant Ban.
                    </p>
                    <p>8. Illegal Content<br>
                    We does not tolerate any form of illegal content in our chat rooms or on our forums. Users also are prohibited from using or uploading illegal images including child pornography or other illegal content such as adult images, and anything that is banned by google play store and/or UGC terms. This includes, but not limited to, profile pictures/avatars and any image transfers or uploads. This includes usernames. If you do so, you will be subject to being kicked, banned and, in some cases, reported to law enforcement. We will, to its highest ability, remove all illegal content when it is discovered or reported to us. We will not be held responsible for such content unless it is noticed and reported to our staff.
                    </p>
                    <p>9. Terms of Use Changes<br>
                    Again, We may revise these terms of use for its web site at any time without notice. By using this web site you are agreeing to be bound by the then current version of these Terms and Conditions of Use. If you cannot agree to this, please do not use this website.and please remove our app from your device.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Accept</button>
                </div>
            </div>
        </div>
    </div>
</body>
<link href="<?php gec($GLOBALS["default"]->weburl) ?>gem/tone/custom.css" rel="stylesheet">
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/jquery/jquery-3.4.1.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/jquery/jquery-migrate-1.4.1.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/popper/umd/popper.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/bootstrap/bootstrap.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/nicescroll/jquery.nicescroll.min.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>riches/kit/jscookie/js.cookie.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>gem/mine/ajx.js"></script>
<script src="<?php gec($GLOBALS["default"]->weburl) ?>gem/mine/gr-sign.js"></script>
<?php
gr_google();
if (pg('signin') == 'unverified/') {
    gr_prnt("<script> alert('".$GLOBALS["lang"]->check_inbox."'); </script>");
}
gr_core('hf', 'footer');
?>
<script type="module">
    import 'https://cdn.jsdelivr.net/npm/@pwabuilder/pwainstall';
    const el = document.createElement('pwa-update');
    document.body.appendChild(el);
</script>
<script defer type="application/javascript">
    $(document).ready(function() {
        $('#loginTermsCondition').modal('show')
    })
</script>
</html>