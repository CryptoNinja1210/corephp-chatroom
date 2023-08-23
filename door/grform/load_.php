<?php if(!defined('s7V9pz')) {die();}?><?php
function gr_form($do) {
    $uid = $GLOBALS["user"]['id'];
    $fields = new stdClass();
    if ($do["type"] == "creategroup") {
        if (!gr_role('access', 'groups', '1')) {
            exit;
        }
        $fields->name = array($GLOBALS["lang"]->group_name, 'input', 'text');
        $fields->slug = array($GLOBALS["lang"]->slug, 'input', 'text');
        $fields->description = array($GLOBALS["lang"]->description, 'textarea');
        $lists = db('Grupo', 's', 'profiles', 'type', 'gfield');
        foreach ($lists as $f) {
            $sel = null;
            $pf = $f['name'];
            $vpf = null;
            if ($f['req'] == 1 || $f['req'] == 3) {
                $GLOBALS["lang"]->$pf = $GLOBALS["lang"]->$pf.' *';
            }
            if ($f['cat'] == 'shorttext') {
                $fields-> $pf = array($GLOBALS["lang"]->$pf, 'input', 'text', '"'.$vpf.'"');
            } else if ($f['cat'] == 'longtext') {
                $fields-> $pf = array($GLOBALS["lang"]->$pf, 'textarea', 'text');
            } else if ($f['cat'] == 'datefield') {
                $fields-> $pf = array($GLOBALS["lang"]->$pf, 'input', 'date', '"'.$vpf.'"');
            } else if ($f['cat'] == 'numfield') {
                $fields-> $pf = array($GLOBALS["lang"]->$pf, 'input', 'number', '"'.$vpf.'"');
            } else if ($f['cat'] == 'dropdownfield') {
                $selt = explode(",", $f['v1']);
                foreach ($selt as $sl) {
                    $sel[$sl] = $sl;
                }
                $fields-> $pf = array($GLOBALS["lang"]->$pf, 'select', $sel, $vpf);
            }
        }
        if (isset($GLOBALS["roles"]['groups'][15])) {
            $fields->password = array($GLOBALS["lang"]->password, 'input', 'text');
        }
        $fields->img = array($GLOBALS["lang"]->icon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        $fields->cpic = array($GLOBALS["lang"]->cover_pic, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        if (isset($GLOBALS["roles"]['groups'][14])) {
            $fields->visibility = array($GLOBALS["lang"]->visibility, 'select', '0', '-----', '0', $GLOBALS["lang"]->visible, '1', $GLOBALS["lang"]->hidden);
        }
        if (isset($GLOBALS["roles"]['groups'][13])) {
            $fields->unleavable = array($GLOBALS["lang"]->unleavable, 'select', '0', '-----', 'unleavable', $GLOBALS["lang"]->yes, '0', $GLOBALS["lang"]->no);
        }
        $postpr[0] = $GLOBALS["lang"]->group_members;
        $postpr['adminonly'] = $GLOBALS["lang"]->admins_moderators;
        $fields->sendperm = array($GLOBALS["lang"]->send_messages, 'select', $postpr);
    } else if ($do["type"] == "createlanguage") {
        if (gr_role('access', 'languages', '1')) {
            $fields->name = array($GLOBALS["lang"]->language, 'input', 'text');
            $fields->img = array($GLOBALS["lang"]->icon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
            $fls = $GLOBALS["lang"]->create.','.$GLOBALS["lang"]->import;
            $fields->option = array($GLOBALS["lang"]->select_an_option, 'radio:shwopts:shw=opts mtch=import', $fls, 'create,import');
            $fields->import = array($GLOBALS["lang"]->import_json, 'input:hidopts opts', 'file', 'accept="application/JSON"');
        }

    } else if ($do["type"] == "createads") {
        if (gr_role('access', 'sys', '7')) {
            $fields->name = array($GLOBALS["lang"]->ad_name, 'input', 'text');
            $adslots = array();
            $adslots['leftside'] = $GLOBALS["lang"]->leftside;
            $adslots['rightside'] = $GLOBALS["lang"]->rightside;
            $adslots['welcome'] = $GLOBALS["lang"]->welcomewindow;
            $adslots['siginpageheader'] = $GLOBALS["lang"]->siginpageheader;
            $adslots['siginpagefooter'] = $GLOBALS["lang"]->siginpagefooter;
            $adslots['chatmessage'] = $GLOBALS["lang"]->chatmessage;
            $fields->adslot = array($GLOBALS["lang"]->adslot, 'select', $adslots);
            $fields->adheight = array($GLOBALS["lang"]->adheight, 'input', 'number', 100);
            $fields->adcontent = array($GLOBALS["lang"]->adcontent, 'textarea');
        }

    } else if ($do["type"] == "createstickerpack") {
        if (gr_role('access', 'features', '16')) {
            $fields->name = array($GLOBALS["lang"]->pack_name, 'input', 'text');
            $fields->img = array($GLOBALS["lang"]->icon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        }

    } else if ($do["type"] == "editstickerpack") {
        if (gr_role('access', 'features', '16')) {
            $fields->name = array($GLOBALS["lang"]->pack_name, 'input', 'text', '"'.$do["no"].'"');
            $fields->img = array($GLOBALS["lang"]->icon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
            $fields->id = array('hidden', 'input', 'hidden', '"'.$do["no"].'"');
        }

    } else if ($do["type"] == "stickersdelete") {
        if (gr_role('access', 'features', '16')) {
            if (!isset($do['name'])) {
                $do['name'] = $do['no'];
            }
            $fields->name = array($GLOBALS["lang"]->confirm_delete, 'input', 'disabled', '"'.$do['name'].'"');
            $fields->id = array('hidden', 'input', 'hidden', '"'.$do["no"].'"');
        }
    } else if ($do["type"] == "createstickers") {
        if (gr_role('access', 'features', '16')) {
            $packs = array();
            $spacks = glob('gem/ore/grupo/stickers' . '/*', GLOB_ONLYDIR);
            foreach ($spacks as $pack) {
                $pack = basename($pack);
                $packs[$pack] = $pack;
            }
            $fields->name = array($GLOBALS["lang"]->pack_name, 'select', $packs);
            $fields->img = array($GLOBALS["lang"]->icon, 'input', 'file', 'class="multifiles" multiple accept="image/x-png,image/gif,image/jpeg"');
        }

    } else if ($do["type"] == "createloginprovider") {
        if (gr_role('access', 'sys', '8')) {
            $providers = array();
            $providers['\Amazon'] = 'Amazon';
            $providers['\AOLOpenID'] = 'AOLOpenID';
            $providers['\Authentiq'] = 'Authentiq';
            $providers['\BitBucket'] = 'BitBucket';
            $providers['\Blizzard'] = 'Blizzard';
            $providers['\Discord'] = 'Discord';
            $providers['\Disqus'] = 'Disqus';
            $providers['\Dribbble'] = 'Dribbble';
            $providers['\Dropbox'] = 'Dropbox';
            $providers['\Facebook'] = 'Facebook';
            $providers['\Foursquare'] = 'Foursquare';
            $providers['\GitHub'] = 'GitHub';
            $providers['\GitLab'] = 'GitLab';
            $providers['\Google'] = 'Google';
            $providers['\Instagram'] = 'Instagram';
            $providers['\LinkedIn'] = 'LinkedIn';
            $providers['\Mailru'] = 'Mailru';
            $providers['\Medium'] = 'Medium';
            $providers['\MicrosoftGraph'] = 'MicrosoftGraph';
            $providers['\Odnoklassniki'] = 'Odnoklassniki';
            $providers['\OpenID'] = 'OpenID';
            $providers['\ORCID'] = 'ORCID';
            $providers['\Paypal'] = 'Paypal';
            $providers['\PaypalOpenID'] = 'PaypalOpenID';
            $providers['\Reddit	'] = 'Reddit';
            $providers['\Slack'] = 'Slack';
            $providers['\Spotify'] = 'Spotify';
            $providers['\StackExchange'] = 'StackExchange';
            $providers['\StackExchangeOpenID'] = 'StackExchangeOpenID';
            $providers['\Steam'] = 'Steam';
            $providers['\Strava'] = 'Strava';
            $providers['\SteemConnect'] = 'SteemConnect';
            $providers['\Telegram'] = 'Telegram';
            $providers['\Tumblr'] = 'Tumblr';
            $providers['\TwitchTV'] = 'TwitchTV';
            $providers['\Twitter'] = 'Twitter';
            $providers['\Vkontakte'] = 'Vkontakte';
            $providers['\WeChat'] = 'WeChat';
            $providers['\WindowsLive'] = 'WindowsLive';
            $providers['\WordPress'] = 'WordPress';
            $providers['\Yandex'] = 'Yandex';
            $providers['\Yahoo'] = 'Yahoo';
            $providers['\QQ'] = 'QQ';
            $fields->provider = array($GLOBALS["lang"]->identity_provider, 'select', $providers);
            $fields->img = array($GLOBALS["lang"]->icon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
            $fields->appid = array($GLOBALS["lang"]->appid, 'input', 'text');
            $fields->appkey = array($GLOBALS["lang"]->appkey, 'input', 'text');
            $fields->appsecretkey = array($GLOBALS["lang"]->appsecretkey, 'input', 'text');
        }

    } else if ($do["type"] == "editloginprovider") {
        if (gr_role('access', 'sys', '8')) {
            $providers = array();
            $providers['\Amazon'] = 'Amazon';
            $providers['\AOLOpenID'] = 'AOLOpenID';
            $providers['\Authentiq'] = 'Authentiq';
            $providers['\BitBucket'] = 'BitBucket';
            $providers['\Blizzard'] = 'Blizzard';
            $providers['\Discord'] = 'Discord';
            $providers['\Disqus'] = 'Disqus';
            $providers['\Dribbble'] = 'Dribbble';
            $providers['\Dropbox'] = 'Dropbox';
            $providers['\Facebook'] = 'Facebook';
            $providers['\Foursquare'] = 'Foursquare';
            $providers['\GitHub'] = 'GitHub';
            $providers['\GitLab'] = 'GitLab';
            $providers['\Google'] = 'Google';
            $providers['\Instagram'] = 'Instagram';
            $providers['\LinkedIn'] = 'LinkedIn';
            $providers['\Mailru'] = 'Mailru';
            $providers['\Medium'] = 'Medium';
            $providers['\MicrosoftGraph'] = 'MicrosoftGraph';
            $providers['\Odnoklassniki'] = 'Odnoklassniki';
            $providers['\OpenID'] = 'OpenID';
            $providers['\ORCID'] = 'ORCID';
            $providers['\Paypal'] = 'Paypal';
            $providers['\PaypalOpenID'] = 'PaypalOpenID';
            $providers['\Reddit	'] = 'Reddit';
            $providers['\Slack'] = 'Slack';
            $providers['\Spotify'] = 'Spotify';
            $providers['\StackExchange'] = 'StackExchange';
            $providers['\StackExchangeOpenID'] = 'StackExchangeOpenID';
            $providers['\Steam'] = 'Steam';
            $providers['\Strava'] = 'Strava';
            $providers['\SteemConnect'] = 'SteemConnect';
            $providers['\Telegram'] = 'Telegram';
            $providers['\Tumblr'] = 'Tumblr';
            $providers['\TwitchTV'] = 'TwitchTV';
            $providers['\Twitter'] = 'Twitter';
            $providers['\Vkontakte'] = 'Vkontakte';
            $providers['\WeChat'] = 'WeChat';
            $providers['\WindowsLive'] = 'WindowsLive';
            $providers['\WordPress'] = 'WordPress';
            $providers['\Yandex'] = 'Yandex';
            $providers['\Yahoo'] = 'Yahoo';
            $providers['\QQ'] = 'QQ';
            $cr = db('Grupo', 's', 'options', 'id,type', $do["no"], 'loginprovider');
            if ($cr && count($cr) > 0) {
                $callbackurl = $GLOBALS["default"]->weburl.'signin/provider/'.$do["no"].'/';
                $fields->callbackurl = array($GLOBALS["lang"]->callbackurl, 'input:selectinp:', 'text', '"'.$callbackurl.'"');
                $fields->provider = array($GLOBALS["lang"]->identity_provider, 'select', $providers, $cr[0]['v1']);
                $fields->img = array($GLOBALS["lang"]->icon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
                $fields->appid = array($GLOBALS["lang"]->appid, 'input', 'text', '"'.$cr[0]['v2'].'"');
                $fields->appkey = array($GLOBALS["lang"]->appkey, 'input', 'text', '"'.$cr[0]['v4'].'"');
                $fields->appsecretkey = array($GLOBALS["lang"]->appsecretkey, 'input', 'text', '"'.$cr[0]['v3'].'"');
                $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
            }
        }

    } else if ($do["type"] == "loginproviderdelete") {
        if (gr_role('access', 'sys', '8')) {
            $fields->name = array($GLOBALS["lang"]->confirm_delete, 'input', 'disabled', '"'.$do['name'].'"');
            $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
        }
    } else if ($do["type"] == "editads") {
        if (gr_role('access', 'sys', '7')) {
            $res = db('Grupo', 's', 'ads', 'id', $do["no"]);
            if ($res && count($res) > 0) {
                $fields->name = array($GLOBALS["lang"]->ad_name, 'input', 'text', '"'.$res[0]['name'].'"');
                $adslots = array();
                $adslots['leftside'] = $GLOBALS["lang"]->leftside;
                $adslots['rightside'] = $GLOBALS["lang"]->rightside;
                $adslots['welcome'] = $GLOBALS["lang"]->welcomewindow;
                $adslots['siginpageheader'] = $GLOBALS["lang"]->siginpageheader;
                $adslots['siginpagefooter'] = $GLOBALS["lang"]->siginpagefooter;
                $adslots['chatmessage'] = $GLOBALS["lang"]->chatmessage;
                $fields->adslot = array($GLOBALS["lang"]->adslot, 'select', $adslots, $res[0]['adslot']);
                $fields->adheight = array($GLOBALS["lang"]->adheight, 'input', 'number', $res[0]['adheight']);
                $res[0]['content'] = htmlspecialchars($res[0]['content']);
                $fields->adcontent = array($GLOBALS["lang"]->adcontent, 'textarea', 'text', $res[0]['content']);
                $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
            }
        }

    } else if ($do["type"] == "adsdelete") {
        if (gr_role('access', 'sys', '7')) {
            $fields->name = array($GLOBALS["lang"]->confirm_delete, 'input', 'disabled', '"'.$do['name'].'"');
            $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
        }
    } else if ($do["type"] == "createmenuitem") {
        if (gr_role('access', 'sys', '6')) {
            $fields->ltext = array($GLOBALS["lang"]->link_text, 'input', 'text');
            $fields->url = array($GLOBALS["lang"]->url, 'input', 'text');
            $fields->morder = array($GLOBALS["lang"]->order, 'input', 'number', 0);
        }

    } else if ($do["type"] == "createradiostation") {
        if (gr_role('access', 'features', '14')) {
            $fields->name = array($GLOBALS["lang"]->station_name, 'input', 'text');
            $fields->description = array($GLOBALS["lang"]->description, 'input', 'text');
            $fields->streamlink = array($GLOBALS["lang"]->stream_url, 'input', 'text');
            $fields->img = array($GLOBALS["lang"]->icon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        }

    } else if ($do["type"] == "editradiostation") {
        if (gr_role('access', 'features', '14')) {
            $cr = db('Grupo', 's', 'options', 'id,type', $do["no"], 'radiostation');
            if ($cr && count($cr) > 0) {
                $fields->name = array($GLOBALS["lang"]->station_name, 'input', 'text', '"'.$cr[0]['v1'].'"');
                $fields->description = array($GLOBALS["lang"]->description, 'input', 'text', '"'.$cr[0]['v2'].'"');
                $fields->streamlink = array($GLOBALS["lang"]->stream_url, 'input', 'text', '"'.$cr[0]['v3'].'"');
                $fields->img = array($GLOBALS["lang"]->icon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
                $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
            }
        }

    } else if ($do["type"] == "editmenuitem") {
        if (gr_role('access', 'sys', '6')) {
            $cr = db('Grupo', 's', 'options', 'id', $do["no"]);
            if ($cr && count($cr) > 0) {
                $vrky = $cr[0]['v1'];
                $fields->ltext = array($GLOBALS["lang"]->link_text, 'input', 'text', '"'.$GLOBALS["lang"]->$vrky.'"');
                $fields->url = array($GLOBALS["lang"]->url, 'input', 'text', '"'.$cr[0]['v2'].'"');
                $fields->morder = array($GLOBALS["lang"]->order, 'input', 'number', '"'.$cr[0]['v3'].'"');
                $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
            }
        }

    } else if ($do["type"] == "createcustomfield") {
        if (gr_role('access', 'fields', '1')) {
            $fields->name = array($GLOBALS["lang"]->fieldname, 'input', 'text');
            $fields->category = array($GLOBALS["lang"]->category, 'select:shwopts:shw=signup mtch=0', '0', '-----', '1', $GLOBALS["lang"]->group, '0', $GLOBALS["lang"]->profile);
            $fields->required = array($GLOBALS["lang"]->requiredfield, 'select', '0', '-----', '1', $GLOBALS["lang"]->yes, '0', $GLOBALS["lang"]->no);
            $fields->addtosignup = array($GLOBALS["lang"]->addtosignup, 'select:hidopts signup:', '0', '-----', '1', $GLOBALS["lang"]->yes, '0', $GLOBALS["lang"]->no);
            $fls = $GLOBALS["lang"]->shorttext.','.$GLOBALS["lang"]->longtext.','.$GLOBALS["lang"]->datefield.','.$GLOBALS["lang"]->numfield.','.$GLOBALS["lang"]->dropdownfield;
            $fields->ftype = array($GLOBALS["lang"]->fieldtype, 'radio:shwopts:shw=opts mtch=dropdownfield', $fls, 'shorttext,longtext,datefield,numfield,dropdownfield');
            $fields->options = array($GLOBALS["lang"]->fieldoptions, 'textarea:hidopts opts:', 'text', '', '"'.$GLOBALS["lang"]->separate_commas.'"');
        }
    } else if ($do["type"] == "groupdeletemsg") {
        $fields->fsearch = 'off';
        if (!empty($do["umdt"])) {
            $fields->name = array($GLOBALS["lang"]->confirm_msgdelete, 'input:autotimering:', 'text', "'".$do["umdt"]."'");
        } else {
            $fields->name = array($GLOBALS["lang"]->confirm_delete, 'input', 'disabled', "'".$GLOBALS["lang"]->message."'");
        }
        if (!empty($do["adt"]) && $do["ldt"] == 'group') {
            $fields->autodel = array($GLOBALS["lang"]->auto_deleted_after, 'input:autotimering:', 'text', "'".$do["adt"]."'");
        }
        $fields->id = array('hidden', 'input', 'hidden', $do["id"]);
        $fields->mid = array('hidden', 'input', 'hidden', $do["xtid"]);
        $fields->ldt = array('hidden', 'input', 'hidden', $do["ldt"]);
    } else if ($do["type"] == "groupclearchat") {
        $cr = gr_group('valid', $do["id"], 'user');
        if ($cr[0]) {
            $fields->name = array($GLOBALS["lang"]->confirm_clearchat, 'input', 'disabled', '"'.$cr['name'].'"');
            $fields->id = array('hidden', 'input', 'hidden', '"'.$do["id"].'"');
            $fields->ldt = array('hidden', 'input', 'hidden', '"'.$do["ldt"].'"');
        }
    } else if ($do["type"] == "filesdownload") {
        $fields->fsearch = 'off';
        $fields->name = array($GLOBALS["lang"]->confirm_download, 'input', 'disabled', "'".$do["file"]."'");
        if (!empty($do["adt"])) {
            $fields->autodel = array($GLOBALS["lang"]->auto_deleted_after, 'input:autotimering:', 'text', "'".$do["adt"]."'");
        }
        $fields->gid = array('hidden', 'input', 'hidden', $do["id"]);
        $fields->id = array('hidden', 'input', 'hidden', $do["xtid"]);
        $fields->ldt = array('hidden', 'input', 'hidden', $do["ldt"]);
    } else if ($do["type"] == "groupreportmsg") {
        if (!isset($do["xtid"]) || empty($do["xtid"])) {
            $do["xtid"] = 0;
        }
        $reasons = $GLOBALS["lang"]->spam.','.$GLOBALS["lang"]->abuse.','.$GLOBALS["lang"]->inappropriate.','.$GLOBALS["lang"]->other;
        $fields->reason = array($GLOBALS["lang"]->reason, 'radio', $reasons, 'spam,abuse,inappropriate,other');
        $fields->comment = array($GLOBALS["lang"]->comments, 'textarea', 'text');
        $fields->id = array('hidden', 'input', 'hidden', $do["id"]);
        $fields->msid = array('hidden', 'input', 'hidden', $do["xtid"]);
    } else if ($do["type"] == "grouptakeaction") {
        $cm = db('Grupo', 's', 'complaints', 'id', $do["no"]);
        if (count($cm) != 0) {
            $cu = gr_group('user', $cm[0]['gid'], $uid);
            if (!$cu[0] || $cu['role'] == 3 || $cm[0]['msid'] == 0 && $cm[0]['uid'] != $uid && !gr_role('access', 'groups', '7')) {
                exit;
            }
            $fields->name = array($GLOBALS["lang"]->full_name, 'input', 'disabled', '"'.gr_profile('get', $cm[0]['uid'], 'name').'"');
            if ($cm[0]['msid'] == 0) {
                $fields->type = array($GLOBALS["lang"]->category, 'input', 'disabled', $GLOBALS["lang"]->group);
            } else {
                $fields->type = array($GLOBALS["lang"]->category, 'input', 'disabled', $GLOBALS["lang"]->message);
            }
            $vrky = $cm[0]['type'];
            $fields->reason = array($GLOBALS["lang"]->reason, 'input', 'disabled', '"'.$GLOBALS["lang"]->$vrky.'"');
            $tms = new DateTime($cm[0]['tms']);
            $tmz = new DateTimeZone(gr_profile('get', $uid, 'tmz'));
            $tms->setTimezone($tmz);
            $tmst = strtotime($tms->format('Y-m-d H:i:s'));
            if ($GLOBALS["default"]->time_format == 24) {
                $tformat = 'H:i';
            } else {
                $tformat = 'h:i a';
            }
            if ($GLOBALS["default"]->dateformat == 'mdy') {
                $dformat = 'M-d-y';
            } else if ($GLOBALS["default"]->dateformat == 'ymd') {
                $dformat = 'y-M-d';
            } else {
                $dformat = 'd-M-y';
            }
            $fields->tms = array($GLOBALS["lang"]->timestamp, 'input', 'disabled', '"'.$tms->format($dformat.' '.$tformat).'"');
            $fields->comment = array($GLOBALS["lang"]->comments, 'span', 'text', $cm[0]['comment']);
            if ($cu['role'] == 2 || $cu['role'] == 1 || gr_role('access', 'groups', '7')) {
                $fields->status = array('Status', 'select', '0', '-----', '2', $GLOBALS["lang"]->action_taken, '3', $GLOBALS["lang"]->rejected, '1', $GLOBALS["lang"]->under_investigation);
            }
            $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
        }
    } else if ($do["type"] == "createrole") {
        if (!gr_role('access', 'roles', '1')) {
            exit;
        }
        $fields->name = array($GLOBALS["lang"]->role_name, 'input', 'text');
        $fields->img = array($GLOBALS["lang"]->icon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        $fields->maxgroup = array($GLOBALS["lang"]->maxgroupjoin, 'input', 'number', '"100"');
        $fields->maxfileuploadsize = array($GLOBALS["lang"]->maxfileuploadsize, 'input', 'number', '"1000"');
        $fields->autodel = array($GLOBALS["lang"]->autodelusrs, 'input', 'text', "Off");
        $dous['1'] = $GLOBALS["lang"]->yes;
        $dous['0'] = $GLOBALS["lang"]->no;
        $fields->delofflineuser = array($GLOBALS["lang"]->delete_only_offline_users, 'select', $dous, 0);
        $fields->autounjoin = array($GLOBALS["lang"]->autounjoin, 'input', 'text', "Off");
        $rl[1] = $GLOBALS["lang"]->create.','.$GLOBALS["lang"]->create_unleavable_group.','.$GLOBALS["lang"]->create_secret_group.','.$GLOBALS["lang"]->create_protected_group;
        $rl[1] = $rl[1].','.$GLOBALS["lang"]->edit.','.$GLOBALS["lang"]->delete.','.$GLOBALS["lang"]->join;
        $rl[1] = $rl[1].','.$GLOBALS["lang"]->invite.','.$GLOBALS["lang"]->adduser_noinvite.','.$GLOBALS["lang"]->view_visible.','.$GLOBALS["lang"]->view_hidden.','.$GLOBALS["lang"]->export_chat.','.$GLOBALS["lang"]->view_likes.','.$GLOBALS["lang"]->like_msgs.','.$GLOBALS["lang"]->admin_controls;
        $rl[1] = $rl[1].','.$GLOBALS["lang"]->viewmemberslist.','.$GLOBALS["lang"]->viewbrowsingmembers.','.$GLOBALS["lang"]->deleteallmsgs;
        $fields->group = array($GLOBALS["lang"]->group, 'checkbox', $rl[1], '1,13,14,15,2,3,4,5,12,6,11,8,9,10,7,16,18,17');

        $rl[2] = $GLOBALS["lang"]->upload.','.$GLOBALS["lang"]->download.','.$GLOBALS["lang"]->delete;
        $rl[2] = $rl[2].','.$GLOBALS["lang"]->attach.','.$GLOBALS["lang"]->view;
        $fields->files = array($GLOBALS["lang"]->files, 'checkbox', $rl[2], '1,2,3,4,5');

        $rl[9] = $GLOBALS["lang"]->sendtxtmsgs.','.$GLOBALS["lang"]->sendaudiomsgs.','.$GLOBALS["lang"]->sendgifs.','.$GLOBALS["lang"]->createqrcode;
        $rl[9] = $rl[9].','.$GLOBALS["lang"]->previewmsgs.','.$GLOBALS["lang"]->sharescreenshot.','.$GLOBALS["lang"]->sharelinks;
        $rl[9] = $rl[9].','.$GLOBALS["lang"]->whoistyping.','.$GLOBALS["lang"]->emailnotifications;
        $rl[9] = $rl[9].','.$GLOBALS["lang"]->selectnamecolor.','.$GLOBALS["lang"]->go_offline.','.$GLOBALS["lang"]->custom_bg.','.$GLOBALS["lang"]->send_as_user;
        $rl[9] = $rl[9].','.$GLOBALS["lang"]->manage_radiostations.','.$GLOBALS["lang"]->listen_radio;
        $rl[9] = $rl[9].','.$GLOBALS["lang"]->manage_stickers.','.$GLOBALS["lang"]->sendstickers;
        $fields->features = array($GLOBALS["lang"]->features, 'checkbox', $rl[9], '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17');

        $rl[3] = $GLOBALS["lang"]->create.','.$GLOBALS["lang"]->edit.','.$GLOBALS["lang"]->delete.','.$GLOBALS["lang"]->view;
        $rl[3] = $rl[3].','.$GLOBALS["lang"]->deactivate_account.','.$GLOBALS["lang"]->ip_logs;
        $rl[3] = $rl[3].','.$GLOBALS["lang"]->online.','.$GLOBALS["lang"]->login_as_user.','.$GLOBALS["lang"]->ban_user;
        $rl[3] = $rl[3].','.$GLOBALS["lang"]->view_full_name.','.$GLOBALS["lang"]->view_users_chat.','.$GLOBALS["lang"]->delete_users_chats;
        $fields->users = array($GLOBALS["lang"]->users, 'checkbox', $rl[3], '1,2,3,4,7,9,5,6,8,10,11,12');

        $rl[4] = $GLOBALS["lang"]->create.','.$GLOBALS["lang"]->edit.','.$GLOBALS["lang"]->delete.','.$GLOBALS["lang"]->view;
        $fields->languages = array($GLOBALS["lang"]->languages, 'checkbox', $rl[4], '1,2,3,4');

        $rl[5] = $GLOBALS["lang"]->settings.','.$GLOBALS["lang"]->appearance;
        $rl[5] = $rl[5].','.$GLOBALS["lang"]->banip.','.$GLOBALS["lang"]->filterwords;
        $rl[5] = $rl[5].','.$GLOBALS["lang"]->header_footer.','.$GLOBALS["lang"]->custom_menu.','.$GLOBALS["lang"]->manage_ads;
        $rl[5] = $rl[5].','.$GLOBALS["lang"]->manage_social_login;
        $fields->sys = array($GLOBALS["lang"]->system_variables, 'checkbox', $rl[5], '1,2,3,4,5,6,7,8');

        $rl[6] = $GLOBALS["lang"]->create.','.$GLOBALS["lang"]->edit.','.$GLOBALS["lang"]->view;
        $fields->roles = array($GLOBALS["lang"]->roles, 'checkbox', $rl[6], '1,2,3');

        $rl[7] = $GLOBALS["lang"]->create.','.$GLOBALS["lang"]->edit.','.$GLOBALS["lang"]->delete.','.$GLOBALS["lang"]->view;
        $fields->fields = array($GLOBALS["lang"]->fields, 'checkbox', $rl[7], '1,2,3,4');

        $rl[8] = $GLOBALS["lang"]->converse.','.$GLOBALS["lang"]->view.','.$GLOBALS["lang"]->export_chat.','.$GLOBALS["lang"]->clear_chat;
        $fields->privatemsg = array($GLOBALS["lang"]->privatemsg, 'checkbox', $rl[8], '1,2,3,4');


    } else if ($do["type"] == "editrole") {
        if (!gr_role('access', 'roles', '2')) {
            exit;
        }
        $cr = db('Grupo', 's', 'permissions', 'id', $do["no"], 'ORDER BY id DESC');
        if ($cr && count($cr) > 0) {
            $fields->name = array($GLOBALS["lang"]->role_name, 'input', 'text', '"'.$do["name"].'"');
            $fields->img = array($GLOBALS["lang"]->icon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
            $fields->rid = array('Role id', 'input', 'hidden', $do["no"]);
            if (empty($cr[0]['xtras'])) {
                $cr[0]['xtras'] = array();
                $cr[0]['xtras']['maxgroup'] = 100;
                $cr[0]['xtras']['maxfileuploadsize'] = 1000;
                $cr[0]['xtras'] = json_encode($cr[0]['xtras']);
            }
            $xtras = json_decode($cr[0]['xtras']);
            if (!isset($xtras->maxfileuploadsize)) {
                $xtras->maxfileuploadsize = 1000;
            }
            $fields->maxgroup = array($GLOBALS["lang"]->maxgroupjoin, 'input', 'number', '"'.$xtras->maxgroup.'"');
            $fields->maxfileuploadsize = array($GLOBALS["lang"]->maxfileuploadsize, 'input', 'number', '"'.$xtras->maxfileuploadsize.'"');
            $fields->autodel = array($GLOBALS["lang"]->autodelusrs, 'input', 'text', '"'.$cr[0]['autodel'].'"');
            $dous = array();
            $dous['1'] = $GLOBALS["lang"]->yes;
            $dous['0'] = $GLOBALS["lang"]->no;
            $dousr = 0;
            if (strpos($cr[0]['privatemsg'], '10') !== false) {
                $dousr = 1;
            }
            $fields->delofflineuser = array($GLOBALS["lang"]->delete_only_offline_users, 'select', $dous, $dousr);
            $fields->autounjoin = array($GLOBALS["lang"]->autounjoin, 'input', 'text', '"'.$cr[0]['autounjoin'].'"');
            $rl[1] = $GLOBALS["lang"]->create.','.$GLOBALS["lang"]->create_unleavable_group.','.$GLOBALS["lang"]->create_secret_group.','.$GLOBALS["lang"]->create_protected_group;
            $rl[1] = $rl[1].','.$GLOBALS["lang"]->edit.','.$GLOBALS["lang"]->delete.','.$GLOBALS["lang"]->join;
            $rl[1] = $rl[1].','.$GLOBALS["lang"]->invite.','.$GLOBALS["lang"]->adduser_noinvite.','.$GLOBALS["lang"]->view_visible.','.$GLOBALS["lang"]->view_hidden.','.$GLOBALS["lang"]->export_chat.','.$GLOBALS["lang"]->view_likes.','.$GLOBALS["lang"]->like_msgs.','.$GLOBALS["lang"]->admin_controls;
            $rl[1] = $rl[1].','.$GLOBALS["lang"]->viewmemberslist.','.$GLOBALS["lang"]->viewbrowsingmembers.','.$GLOBALS["lang"]->deleteallmsgs;
            $fields->group = array($GLOBALS["lang"]->group, 'checkbox', $rl[1], '1,13,14,15,2,3,4,5,12,6,11,8,9,10,7,16,18,17', $cr[0]['groups']);

            $rl[2] = $GLOBALS["lang"]->upload.','.$GLOBALS["lang"]->download.','.$GLOBALS["lang"]->delete;
            $rl[2] = $rl[2].','.$GLOBALS["lang"]->attach.','.$GLOBALS["lang"]->view;
            $fields->files = array($GLOBALS["lang"]->files, 'checkbox', $rl[2], '1,2,3,4,5', $cr[0]['files']);

            $rl[9] = $GLOBALS["lang"]->sendtxtmsgs.','.$GLOBALS["lang"]->sendaudiomsgs.','.$GLOBALS["lang"]->sendgifs.','.$GLOBALS["lang"]->createqrcode;
            $rl[9] = $rl[9].','.$GLOBALS["lang"]->previewmsgs.','.$GLOBALS["lang"]->sharescreenshot.','.$GLOBALS["lang"]->sharelinks;
            $rl[9] = $rl[9].','.$GLOBALS["lang"]->whoistyping.','.$GLOBALS["lang"]->emailnotifications;
            $rl[9] = $rl[9].','.$GLOBALS["lang"]->selectnamecolor.','.$GLOBALS["lang"]->go_offline.','.$GLOBALS["lang"]->custom_bg.','.$GLOBALS["lang"]->send_as_user;
            $rl[9] = $rl[9].','.$GLOBALS["lang"]->manage_radiostations.','.$GLOBALS["lang"]->listen_radio;
            $rl[9] = $rl[9].','.$GLOBALS["lang"]->manage_stickers.','.$GLOBALS["lang"]->sendstickers;
            $fields->features = array($GLOBALS["lang"]->features, 'checkbox', $rl[9], '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17', $cr[0]['features']);

            $rl[3] = $GLOBALS["lang"]->create.','.$GLOBALS["lang"]->edit.','.$GLOBALS["lang"]->delete.','.$GLOBALS["lang"]->view;
            $rl[3] = $rl[3].','.$GLOBALS["lang"]->deactivate_account.','.$GLOBALS["lang"]->ip_logs;
            $rl[3] = $rl[3].','.$GLOBALS["lang"]->online.','.$GLOBALS["lang"]->login_as_user.','.$GLOBALS["lang"]->ban_user;
            $rl[3] = $rl[3].','.$GLOBALS["lang"]->view_full_name.','.$GLOBALS["lang"]->view_users_chat.','.$GLOBALS["lang"]->delete_users_chats;
            $fields->users = array($GLOBALS["lang"]->users, 'checkbox', $rl[3], '1,2,3,4,7,9,5,6,8,10,11,12', $cr[0]['users']);

            $rl[4] = $GLOBALS["lang"]->create.','.$GLOBALS["lang"]->edit.','.$GLOBALS["lang"]->delete.','.$GLOBALS["lang"]->view;
            $fields->languages = array($GLOBALS["lang"]->languages, 'checkbox', $rl[4], '1,2,3,4', $cr[0]['languages']);

            $rl[5] = $GLOBALS["lang"]->settings.','.$GLOBALS["lang"]->appearance;
            $rl[5] = $rl[5].','.$GLOBALS["lang"]->banip.','.$GLOBALS["lang"]->filterwords;
            $rl[5] = $rl[5].','.$GLOBALS["lang"]->header_footer.','.$GLOBALS["lang"]->custom_menu.','.$GLOBALS["lang"]->manage_ads;
            $rl[5] = $rl[5].','.$GLOBALS["lang"]->manage_social_login;
            $fields->sys = array($GLOBALS["lang"]->system_variables, 'checkbox', $rl[5], '1,2,3,4,5,6,7,8', $cr[0]['sys']);

            $rl[6] = $GLOBALS["lang"]->create.','.$GLOBALS["lang"]->edit.','.$GLOBALS["lang"]->view;
            $fields->roles = array($GLOBALS["lang"]->roles, 'checkbox', $rl[6], '1,2,3', $cr[0]['roles']);

            $rl[7] = $GLOBALS["lang"]->create.','.$GLOBALS["lang"]->edit.','.$GLOBALS["lang"]->delete.','.$GLOBALS["lang"]->view;
            $fields->fields = array($GLOBALS["lang"]->fields, 'checkbox', $rl[7], '1,2,3,4', $cr[0]['fields']);

            $rl[8] = $GLOBALS["lang"]->converse.','.$GLOBALS["lang"]->view.','.$GLOBALS["lang"]->export_chat.','.$GLOBALS["lang"]->clear_chat;
            $fields->privatemsg = array($GLOBALS["lang"]->privatemsg, 'checkbox', $rl[8], '1,2,3,4', $cr[0]['privatemsg']);
        }

    } else if ($do["type"] == "customfielddelete") {
        if (gr_role('access', 'fields', '3')) {
            $vrky = $do['name'];
            $fields->name = array($GLOBALS["lang"]->confirm_delete, 'input', 'disabled', '"'.$GLOBALS["lang"]->$vrky.'"');
            $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
        }
    } else if ($do["type"] == "profileiplogdelete") {
        if (gr_role('access', 'users', '9')) {
            $fields->name = array($GLOBALS["lang"]->confirm_delete, 'input', 'disabled', '"'.$GLOBALS["lang"]->ip_log.'"');
            $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
        }
    } else if ($do["type"] == "alertclearallalerts") {
        $fields->name = array($GLOBALS["lang"]->confirm_delete, 'input', 'disabled', '"'.$GLOBALS["lang"]->alerts.'"');
    } else if ($do["type"] == "menuitemdelete") {
        if (gr_role('access', 'sys', '6')) {
            $vrky = $do['name'];
            $fields->name = array($GLOBALS["lang"]->confirm_delete, 'input', 'disabled', '"'.$GLOBALS["lang"]->$vrky.'"');
            $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
        }
    } else if ($do["type"] == "radiostationdelete") {
        if (gr_role('access', 'features', '14')) {
            $fields->name = array($GLOBALS["lang"]->confirm_delete, 'input', 'disabled', '"'.$do['name'].'"');
            $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
        }
    } else if ($do["type"] == "roledelete") {
        if (!gr_role('access', 'roles', '3')) {
            exit;
        }
        $fields->name = array($GLOBALS["lang"]->confirm_delete, 'input', 'disabled', '"'.$do['name'].'"');
        $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
    } else if ($do["type"] == "languagedelete") {
        if (!gr_role('access', 'languages', '3')) {
            exit;
        }
        $fields->name = array($GLOBALS["lang"]->confirm_delete, 'input', 'disabled', '"'.$do['name'].'"');
        $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
    } else if ($do["type"] == "languagehide") {
        if (!gr_role('access', 'languages', '2')) {
            exit;
        }
        $fields->name = array($GLOBALS["lang"]->confirm_hide, 'input', 'disabled', '"'.$do['name'].'"');
        $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
    } else if ($do["type"] == "languageshow") {
        if (!gr_role('access', 'languages', '2')) {
            exit;
        }
        $fields->name = array($GLOBALS["lang"]->confirm_show, 'input', 'disabled', '"'.$do['name'].'"');
        $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
    } else if ($do["type"] == "createuser") {
        if (!gr_role('access', 'users', '1')) {
            exit;
        }
        $fields->fname = array($GLOBALS["lang"]->full_name, 'input', 'text');
        $fields->email = array($GLOBALS["lang"]->email_address, 'input', 'text');
        $fields->name = array($GLOBALS["lang"]->username, 'input', 'text');
        $fields->pass = array($GLOBALS["lang"]->password, 'input', 'text', '"'.rn('7').'"');
        if (gr_role('access', 'users', '2')) {
            $role = db('Grupo', 's', 'permissions');
            $roles = array();
            foreach ($role as $r) {
                $roles[$r['id']] = $r['name'];
            }
            $fields->role = array($GLOBALS["lang"]->role, 'select', $roles);
        }
        $fields->sent = array($GLOBALS["lang"]->mail_login_info, 'select', '0', '-----', '1', $GLOBALS["lang"]->yes, '0', $GLOBALS["lang"]->no);

    } else if ($do["type"] == "profileact") {
        if (gr_role('access', 'users', '3')) {
            $optz['delete'] = $GLOBALS["lang"]->delete;
        }
        if (gr_role('access', 'users', '8')) {
            $optz['ban'] = $GLOBALS["lang"]->ban;
        }
        if (gr_role('access', 'sys', '3')) {
            $optz['banip'] = $GLOBALS["lang"]->banip;
            $optz['unbanip'] = $GLOBALS["lang"]->unbanip;
        }
        $fields->opted = array($GLOBALS["lang"]->select_option, 'select', $optz);
        $fields->id = array('hidden', 'input', 'hidden', '"'.$do["id"].'"');

    } else if ($do["type"] == "editlanguage") {
        if (!gr_role('access', 'languages', '2')) {
            exit;
        }
        $r = db('Grupo', 's', 'phrases', 'type,id', 'lang', $do['no']);
        if (isset($r[0])) {
            $fields->name = array($GLOBALS["lang"]->language, 'input', 'text', '"'.$r[0]['short'].'"');
            $fields->img = array($GLOBALS["lang"]->icon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
            $fields->defaultlng = array($GLOBALS["lang"]->set_default_language, 'select', '0', '-----', '1', $GLOBALS["lang"]->yes, '0', $GLOBALS["lang"]->no);
            $lalign['ltr'] = $GLOBALS["lang"]->ltr;
            $lalign['rtl'] = $GLOBALS["lang"]->rtl;
            $fields->direction = array($GLOBALS["lang"]->txt_direction, 'select', $lalign, $r[0]['full']);
            $fields->id = array('hidden', 'input', 'hidden', '"'.$do["no"].'"');
            $ph = db('Grupo', 's', 'phrases', 'type,lid', 'phrase', 1);
            foreach ($ph as $p) {
                $key = 'z'.rn(3).'noext';
                $pfull = db('Grupo', 's', 'phrases', 'type,lid,short', 'phrase', $do['no'], $p['short']);
                if (isset($pfull[0]['full'])) {
                    $key = 'z'.$pfull[0]['id'];
                    $p['full'] = $pfull[0]['full'];
                }
                if ($p['short'] == 'terms' || $p['short'] == 'pg_about' || $p['short'] == 'pg_privacy' || $p['short'] == 'pg_contact') {
                    $fields->$key = array(ucwords($p['short']), 'textarea', 'text', $p['full']);
                } else {
                    $fields->$key = array(ucwords($p['short']), 'input', 'text', '"'.$p['full'].'"');
                }
            }
        }

    } else if ($do["type"] == "editavatar") {
        $fields->cavatar = array($GLOBALS["lang"]->custom_avatar, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        $avatars = array();
        $directory = cnf()['gem'].'/ore/grupo/avatars';
        $images = glob($directory . "/*.png");

        foreach ($images as $image) {
            $key = basename($image);
            $avatars[$key] = '"'.$image.'"';
        }
        $fields->avatar = array($GLOBALS["lang"]->choose_avatar, 'imglist', $avatars);

    } else if ($do["type"] == "editcustomfield") {
        if (gr_role('access', 'fields', '2')) {
            $cr = db('Grupo', 's', 'profiles', 'type,id|,type,id', 'field', $do["no"], 'gfield', $do["no"]);
            if ($cr && count($cr) > 0) {
                $vrky = $cr['0']['name'];
                $fields->identifier = array($GLOBALS["lang"]->identifier, 'input', 'disabled', '"'.$vrky.'"');
                $fields->name = array($GLOBALS["lang"]->fieldname, 'input', 'text', '"'.$GLOBALS["lang"]->$vrky.'"');
                $fields->category = array($GLOBALS["lang"]->category, 'select:shwopts:shw=asgnp mtch=2', '0', '-----', '1', $GLOBALS["lang"]->group, '2', $GLOBALS["lang"]->profile);
                $req['1'] = $GLOBALS["lang"]->yes;
                $req['0'] = $GLOBALS["lang"]->no;
                $rqd = $adl = 0;
                if ($cr['0']['req'] == 1 || $cr['0']['req'] == 3) {
                    $rqd = 1;
                }
                $fields->required = array($GLOBALS["lang"]->requiredfield, 'select', $req, $rqd);
                if ($cr['0']['req'] == 2 || $cr['0']['req'] == 3) {
                    $adl = 1;
                }
                if ($cr['0']['type'] == 'field') {
                    $fields->addtosignup = array($GLOBALS["lang"]->addtosignup, 'select:hidopts asgnp shw:', $req, $adl);
                } else {
                    $fields->addtosignup = array($GLOBALS["lang"]->addtosignup, 'select:hidopts asgnp:', $req, $adl);
                }
                $fls['shorttext'] = $GLOBALS["lang"]->shorttext;
                $fls['longtext'] = $GLOBALS["lang"]->longtext;
                $fls['datefield'] = $GLOBALS["lang"]->datefield;
                $fls['numfield'] = $GLOBALS["lang"]->numfield;
                $fls['dropdownfield'] = $GLOBALS["lang"]->dropdownfield;
                $fields->ftype = array($GLOBALS["lang"]->fieldtype, 'select:shwopts:shw=opts mtch=dropdownfield', $fls, $cr['0']['cat']);

                if ($cr['0']['cat'] == 'dropdownfield') {
                    $fields->options = array($GLOBALS["lang"]->fieldoptions, 'textarea:hidopts opts shw:', 'text', $cr['0']['v1'], '"'.$GLOBALS["lang"]->separate_commas.'"');
                } else {
                    $fields->options = array($GLOBALS["lang"]->fieldoptions, 'textarea:hidopts opts:', 'text', '', '"'.$GLOBALS["lang"]->separate_commas.'"');
                }
                $fields->id = array('hidden', 'input', 'hidden', $do["no"]);
            }
        }
    } else if ($do["type"] == "editgroup") {
        $role = gr_group('user', $do["id"], $uid)['role'];
        $adm = 0;
        if ($role == 2 || $role == 1) {
            $adm = 1;
        }
        if (gr_role('access', 'groups', '2') && $adm == 1 || gr_role('access', 'groups', '7')) {
            $cr = db('Grupo', 's', 'options', 'type,id', 'group', $do["id"]);
            if ($cr && count($cr) > 0) {
                $fields->name = array($GLOBALS["lang"]->group_name, 'input', 'text', '"'.$cr['0']['v1'].'"');
                $gdescp = db('Grupo', 's,v1', 'profiles', 'type,name,uid', 'group', 'description', $do["id"]);
                if (count($gdescp) > 0) {
                    $gdescp = $gdescp[0]['v1'];
                } else {
                    $gdescp = '';
                }
                $gslug = db('Grupo', 's', 'options', 'type,v1', 'groupslug', $do["id"]);
                if (count($gslug) > 0) {
                    $gslug = $gslug[0]['v2'];
                } else {
                    $gslug = '';
                }
                $fields->slug = array($GLOBALS["lang"]->slug, 'input', 'text', '"'.$gslug.'"');
                $fields->description = array($GLOBALS["lang"]->description, 'textarea', '', $gdescp);
                $lists = db('Grupo', 's', 'profiles', 'type', 'gfield');
                foreach ($lists as $f) {
                    $sel = null;
                    $pf = $f['name'];
                    $vpf = null;
                    $ct = db('Grupo', 's', 'profiles', 'type,name,uid', 'group', $f['id'], $do["id"]);
                    if (count($ct) > 0) {
                        $vpf = html_entity_decode($ct[0]['v1']);
                    }
                    if ($vpf == null) {
                        $vpf = '';
                    }
                    if ($f['req'] == 1 || $f['req'] == 3) {
                        $GLOBALS["lang"]->$pf = $GLOBALS["lang"]->$pf.' *';
                    }
                    if ($f['cat'] == 'shorttext') {
                        $fields-> $pf = array($GLOBALS["lang"]->$pf, 'input', 'text', '"'.$vpf.'"');
                    } else if ($f['cat'] == 'longtext') {
                        $fields-> $pf = array($GLOBALS["lang"]->$pf, 'textarea', 'text', $vpf);
                    } else if ($f['cat'] == 'datefield') {
                        $fields-> $pf = array($GLOBALS["lang"]->$pf, 'input', 'date', '"'.$vpf.'"');
                    } else if ($f['cat'] == 'numfield') {
                        $fields-> $pf = array($GLOBALS["lang"]->$pf, 'input', 'number', '"'.$vpf.'"');
                    } else if ($f['cat'] == 'dropdownfield') {
                        $selt = explode(",", $f['v1']);
                        foreach ($selt as $sl) {
                            $sel[$sl] = $sl;
                        }
                        $fields-> $pf = array($GLOBALS["lang"]->$pf, 'select', $sel, $vpf);
                    }
                }
                if (isset($GLOBALS["roles"]['groups'][15])) {
                    $fields->password = array($GLOBALS["lang"]->password, 'input', 'text');
                }
                $fields->img = array($GLOBALS["lang"]->icon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
                $fields->cpic = array($GLOBALS["lang"]->cover_pic, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
                if (isset($GLOBALS["roles"]['groups'][14])) {
                    $fields->visibility = array($GLOBALS["lang"]->visibility, 'select', '0', '-----', '0', $GLOBALS["lang"]->visible, '1', $GLOBALS["lang"]->hidden);
                }
                $postpr[0] = $GLOBALS["lang"]->group_members;
                $postpr['adminonly'] = $GLOBALS["lang"]->admins_moderators;
                $fields->sendperm = array($GLOBALS["lang"]->send_messages, 'select', $postpr, $cr['0']['v5']);
                $unlvb[0] = $GLOBALS["lang"]->no;
                $unlvb['unleavable'] = $GLOBALS["lang"]->yes;
                if (isset($GLOBALS["roles"]['groups'][13])) {
                    $fields->unleavable = array($GLOBALS["lang"]->unleavable, 'select', $unlvb, $cr['0']['v6']);
                }
                $fields->id = array('hidden', 'input', 'hidden', $cr['0']['id']);
                if (!empty($cr['0']['v2'])) {
                    $fields->delpass = array($GLOBALS["lang"]->remove_password, 'select', '0', '-----', '1', $GLOBALS["lang"]->yes, '0', $GLOBALS["lang"]->no);
                }
            }
        }
    } else if ($do["type"] == "groupjoin") {
        if (!gr_role('access', 'groups', '4') && !gr_role('access', 'groups', '7')) {
            exit;
        }
        $cr = db('Grupo', 's', 'options', 'type,id', 'group', $do["id"]);
        if ($cr && count($cr) > 0) {
            $cu = gr_group('user', $do["id"], $uid)[0];
            if (!$cu) {
                $fields->name = array($GLOBALS["lang"]->confirm_join, 'input', 'disabled', '"'.$cr['0']['v1'].'"');
                $gdescp = db('Grupo', 's,v1', 'profiles', 'type,name,uid', 'group', 'description', $do["id"]);
                if (count($gdescp) > 0) {
                    $gdescp = $gdescp[0]['v1'];
                    $fields->description = array($GLOBALS["lang"]->description, 'textarea', 'disabled', $gdescp);
                }
                $inv = db('Grupo', 's,count(*)', 'alerts', 'type,uid,v1', 'invitation', $uid, $do["id"])[0][0];
                if (!empty($cr['0']['v2']) && !gr_role('access', 'groups', '7') && $inv == 0) {
                    $fields->password = array($GLOBALS["lang"]->password, 'input', 'text');
                }
                $fields->id = array('hidden', 'input', 'hidden', $cr['0']['id']);
            } else {
                pr(0);
            }
        }

    } else if ($do["type"] == "groupleave") {
        $cr = gr_group('valid', $do["id"]);
        if ($cr[0]) {
            $cu = gr_group('user', $do["id"], $uid)[0];
            if ($cu) {
                $fields->name = array($GLOBALS["lang"]->confirm_leave, 'input', 'disabled', '"'.$cr['name'].'"');
                $fields->id = array('hidden', 'input', 'hidden', '"'.$do["id"].'"');
            }
        }

    } else if ($do["type"] == "groupmention") {
        $gr = db('Grupo', 's', 'alerts', 'id,uid', $do["id"], $uid);
        if (isset($gr[0])) {
            $gr = $gr[0];
            if ($gr['type'] == 'mentioned' || $gr['type'] == 'replied' || $gr['type'] == 'liked') {
                $cu = gr_group('user', $gr['v1'], $uid)[0];
                if ($cu) {
                    $cr = gr_group('valid', $gr['v1']);
                    $fields->group = array($GLOBALS["lang"]->group_name, 'input', 'disabled', '"'.$cr['name'].'"');
                    $fields->user = array($GLOBALS["lang"]->full_name, 'input', 'disabled', '"'.gr_profile('get', $gr['v3'], 'name').'"');
                    $msg = db('Grupo', 's', 'msgs', 'id', $gr['v2'])[0];
                    $fields->msg = array($GLOBALS["lang"]->message, 'textarea', 'disabled', $msg['msg']);
                    $fields->id = array('hidden', 'input', 'hidden', '"'.$gr['v1'].'"');
                }
            }
        }
    } else if ($do["type"] == "grouprole") {
        $role = gr_group('user', $do["id"], $uid)['role'];
        if (!gr_role('access', 'groups', '7') && $role != 2) {
            exit;
        }
        $cr = gr_group('valid', $do["id"]);
        if ($cr[0]) {
            $fields->group = array($GLOBALS["lang"]->group_name, 'input', 'disabled', '"'.$cr['name'].'"');
            $fields->pname = array($GLOBALS["lang"]->full_name, 'input', 'disabled', '"'.$do["pname"].'"');
            $fields->usid = array('hidden', 'input', 'hidden', '"'.$do["usr"].'"');
            $fields->id = array('hidden', 'input', 'hidden', '"'.$do["id"].'"');
            $fields->role = array($GLOBALS["lang"]->role, 'select', '0', '-----', '2', $GLOBALS["lang"]->admin, '1', $GLOBALS["lang"]->moderator, '0', $GLOBALS["lang"]->member);
            $fields->remuser = array($GLOBALS["lang"]->remove_user, 'select', '0', '-----', 'yes', $GLOBALS["lang"]->yes, 'no', $GLOBALS["lang"]->no);
        }

    } else if ($do["type"] == "groupblock") {
        $role = gr_group('user', $do["id"], $uid)['role'];
        if (isset($GLOBALS["roles"]['groups'][7]) || $role == 2 || $role == 1) {
            $usrole = gr_group('user', $do["id"], $do["usr"])['role'];
            $fields->pname = array($GLOBALS["lang"]->full_name, 'input', 'disabled', '"'.$do["pname"].'"');
            $fields->usid = array('hidden', 'input', 'hidden', '"'.$do["usr"].'"');
            $fields->id = array('hidden', 'input', 'hidden', '"'.$do["id"].'"');
            $actions = array();
            if ($usrole == 3) {
                $actions['unban'] = $GLOBALS["lang"]->unban;
            } else {
                $actions['ban'] = $GLOBALS["lang"]->ban;
                $actions['tempban'] = $GLOBALS["lang"]->temporary_ban;
            }
            $fields->action = array($GLOBALS["lang"]->take_action, 'select:shwopts:shw=opts mtch=tempban', $actions);
            $fields->bantime = array($GLOBALS["lang"]->ban_till, 'input:hidopts opts', 'number', 10);
        }
    } else if ($do["type"] == "filesdelete") {
        if (!gr_role('access', 'files', '3')) {
            exit;
        }
        $fields->name = array($GLOBALS["lang"]->confirm_delete, 'input', 'disabled', '"'.explode('-gr-', $do["id"], 2)[1].'"');
        $fields->id = array('hidden', 'input', 'hidden', '"'.$do["id"].'"');

    } else if ($do["type"] == "groupexport") {
        if (gr_role('access', 'groups', '8') || gr_role('access', 'groups', '7') || gr_role('access', 'privatemsg', '3')) {
            $cr = gr_group('valid', $do["id"], $do["ldt"]);
            if ($cr[0]) {
                $cu = gr_group('user', $do["id"], $uid, $do["ldt"])[0];
                if ($cu) {
                    if ($do["ldt"] == 'user' && strpos($do["id"], '-') !== false) {
                        $cr['name'] = $GLOBALS["lang"]->privatemsg;
                    }
                    $fields->name = array($GLOBALS["lang"]->confirm_export, 'input', 'disabled', '"'.$cr['name'].'"');
                    $fields->id = array('hidden', 'input', 'hidden', '"'.$do["id"].'"');
                    $fields->ldt = array('hidden', 'input', 'hidden', '"'.$do["ldt"].'"');
                }
            }
        }
    } else if ($do["type"] == "groupdelete") {
        $role = gr_group('user', $do["id"], $uid)['role'];
        if (gr_role('access', 'groups', '3') && $role == 2 || gr_role('access', 'groups', '7')) {
            $cr = gr_group('valid', $do["id"]);
            if ($cr[0]) {
                $fields->name = array($GLOBALS["lang"]->confirm_delete, 'input', 'disabled', '"'.$cr['name'].'"');
                $fields->id = array('hidden', 'input', 'hidden', $do["id"]);
            }
        }

    } else if ($do["type"] == "groupdeleteallmsgs") {
        if (isset($do["cat"]) && $do["cat"] == 'userchat' && isset($GLOBALS["roles"]['users'][12])) {
            $fields->name = array($GLOBALS["lang"]->confirm_deletemsgs, 'input', 'disabled', '"'.$GLOBALS["lang"]->conversation.'"');
            $fields->cat = array('hidden', 'input', 'hidden', 'userchat');
            $fields->id = array('hidden', 'input', 'hidden', $do["id"]);
        } else {
            $role = gr_group('user', $do["id"], $uid)['role'];
            if (gr_role('access', 'groups', '3') && $role == 2 || gr_role('access', 'groups', '7')) {
                $cr = gr_group('valid', $do["id"]);
                if ($cr[0]) {
                    $fields->name = array($GLOBALS["lang"]->confirm_deletemsgs, 'input', 'disabled', '"'.$cr['name'].'"');
                    $fields->id = array('hidden', 'input', 'hidden', $do["id"]);
                }
            }
        }
    } else if ($do["type"] == "groupinvite") {
        if (gr_role('access', 'groups', '5') || gr_role('access', 'groups', '7')) {
            $cr = gr_group('valid', $do["id"]);
            $role = gr_group('user', $do["id"], $uid)['role'];
            if ($cr[0]) {
                if (gr_role('access', 'groups', '7') || empty($cr['pass']) && $cr['visible'] != 'secret' || $role == 1 || $role == 2) {
                    $invitelink = $GLOBALS["default"]->weburl.'chat/group/'.$do['id'].'/join/'.$cr['access'].'/';
                    $fields->invitelink = array($GLOBALS["lang"]->invite_link, 'input:selectinp:', 'text', '"'.$invitelink.'"');
                    $fields->users = array($GLOBALS["lang"]->email_username, 'input:inviter:', 'text', '', '"'.$GLOBALS["lang"]->separate_commas.'"');
                    $fields->id = array('hidden', 'input', 'hidden', $do["id"]);
                }
            }
        }

    } else if ($do["type"] == "profileblock") {
        $vusr = db('Grupo', 's,count(*)', 'users', 'id', $do["id"])[0][0];
        if ($vusr > 0) {
            if (gr_profile('blocked', $do["id"])[0]) {
                $fields->name = array($GLOBALS["lang"]->confirm_unblock, 'input', 'disabled', '"'.gr_profile('get', $do["id"], 'name').'"');
            } else {
                $fields->name = array($GLOBALS["lang"]->confirm_block, 'input', 'disabled', '"'.gr_profile('get', $do["id"], 'name').'"');
            }
            $fields->id = array('hidden', 'input', 'hidden', $do["id"]);
        }

    } else if ($do["type"] == "editprofile") {
        if (isset($do['no'])) {
            if (isset($GLOBALS["roles"]['users'][2]) || isset($GLOBALS["roles"]['users'][3]) || isset($GLOBALS["roles"]['users'][8])) {
                $uid = $do['no'];
                if (isset($do['xtid']) && !empty($do['xtid'])) {
                    $uid = $do['xtid'];
                }
            }
        }
        $usr = usr('Grupo', 'select', $uid);
        $prf = db('Grupo', 's', 'options', 'type,v1,v3', 'profile', 'name', $uid);
        $editable = 'disabled';
        if (isset($GLOBALS["roles"]['users'][2]) || $uid == $GLOBALS["user"]['id']) {
            $editable = 'text';
        }
        $fields->name = array($GLOBALS["lang"]->full_name, 'input', $editable, '"'.$prf[0]['v2'].'"');
        $fields->user = array($GLOBALS["lang"]->username, 'input', $editable, '"'.$usr['name'].'"');
        $fields->id = array('hidden', 'input', 'hidden', '"'.$uid.'"');
        if (gr_role('access', 'users', '3') || gr_role('access', 'users', '8') || gr_role('access', 'sys', '3')) {
            if (gr_role('access', 'users', '3')) {
                $optz['delete'] = $GLOBALS["lang"]->delete;
            }
            if (gr_role('access', 'users', '8')) {
                if ($usr['role'] == 4) {
                    $optz['unban'] = $GLOBALS["lang"]->unban;
                } else {
                    $optz['ban'] = $GLOBALS["lang"]->ban;
                }
            }
            if (gr_role('access', 'sys', '3')) {
                $optz['banip'] = $GLOBALS["lang"]->banip;
                $optz['unbanip'] = $GLOBALS["lang"]->unbanip;
            }
            $fields->takeaction = array($GLOBALS["lang"]->take_action, 'select', $optz);
        }
        if (isset($GLOBALS["roles"]['users'][2]) || $uid == $GLOBALS["user"]['id']) {
            $fields->email = array($GLOBALS["lang"]->email_address, 'input', 'text', '"'.$usr['email'].'"');
            $fields->password = array($GLOBALS["lang"]->password, 'input', 'password');
            if (isset($GLOBALS["roles"]['features'][10])) {
                $ncolor = $prf[0]['v5'];
                if (empty($ncolor)) {
                    $ncolor = gr_usrcolor();
                }
                $fields->ncolor = array($GLOBALS["lang"]->name_color, 'input', 'colorpick', $ncolor);
            }
            if (isset($do['no']) && gr_role('access', 'users', '2')) {
                $role = db('Grupo', 's', 'permissions');
                $roles = array();
                foreach ($role as $r) {
                    $roles[$r['id']] = $r['name'];
                }
                $fields->role = array($GLOBALS["lang"]->role, 'select', $roles, $usr['role']);
            }
            $fields->tmz = array($GLOBALS["lang"]->timezone, 'tmz', gr_tmz(), gr_profile('get', $uid, 'tmz', 1));
            $alerts = array();
            $alrt = glob('gem/ore/grupo/alerts/*');
            foreach ($alrt as $al) {
                $alerts[$al] = ucwords(basename($al, '.mp3'));
            }
            $fields->alert = array($GLOBALS["lang"]->notification_tone, 'select:alertonez audselect:', $alerts, gr_profile('get', $uid, 'alert'));
            if (isset($GLOBALS["roles"]['features'][12])) {
                $fields->cbg = array($GLOBALS["lang"]->custom_bg, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
            }
            if (!empty(gr_img('userbg', $uid))) {
                $fields->rmusbg = array($GLOBALS["lang"]->remove_custom_bg, 'select', '0', '-----', 'yes', $GLOBALS["lang"]->yes, 'no', $GLOBALS["lang"]->no);
            }
            $fields->cpic = array($GLOBALS["lang"]->cover_pic, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
            $fields->cavatar = array($GLOBALS["lang"]->custom_avatar, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
            $avatars = array();
            $directory = cnf()['gem'].'/ore/grupo/avatars';
            $images = glob($directory . "/*.png");

            foreach ($images as $image) {
                $key = basename($image);
                $avatars[$key] = '"'.$GLOBALS["default"]->weburl.$image.'"';
            }
            $fields->avatar = array($GLOBALS["lang"]->choose_avatar, 'imglist', $avatars);
            $pmfields = array();
            $pmfields['enable'] = $GLOBALS["lang"]->enable;
            $pmfields['disable'] = $GLOBALS["lang"]->disable;
            $pmsetting = db('Grupo', 's', 'options', 'type,v1,v3', 'profile', 'privatemsgs', $uid);
            if (!isset($pmsetting[0]['v2'])) {
                $pmsetting[0]['v2'] = 0;
            }
            $fields->privatemsg = array($GLOBALS["lang"]->privatemsg, 'select', $pmfields, $pmsetting[0]['v2']);
            $lists = db('Grupo', 's', 'profiles', 'type', 'field');
            foreach ($lists as $f) {
                $sel = null;
                $pf = $f['name'];
                $vpf = null;
                $ct = db('Grupo', 's', 'profiles', 'type,name,uid', 'profile', $f['id'], $uid);
                if (count($ct) > 0) {
                    $vpf = html_entity_decode($ct[0]['v1']);
                }
                if ($vpf == null) {
                    $vpf = '';
                }
                if ($f['req'] == 1 || $f['req'] == 3) {
                    $GLOBALS["lang"]->$pf = $GLOBALS["lang"]->$pf.' *';
                }
                if ($f['cat'] == 'shorttext') {
                    $fields-> $pf = array($GLOBALS["lang"]->$pf, 'input', 'text', '"'.$vpf.'"');
                } else if ($f['cat'] == 'longtext') {
                    $fields-> $pf = array($GLOBALS["lang"]->$pf, 'textarea', 'text', $vpf);
                } else if ($f['cat'] == 'datefield') {
                    $fields-> $pf = array($GLOBALS["lang"]->$pf, 'input', 'date', '"'.$vpf.'"');
                } else if ($f['cat'] == 'numfield') {
                    $fields-> $pf = array($GLOBALS["lang"]->$pf, 'input', 'number', '"'.$vpf.'"');
                } else if ($f['cat'] == 'dropdownfield') {
                    $selt = explode(",", $f['v1']);
                    foreach ($selt as $sl) {
                        $sel[$sl] = $sl;
                    }
                    $fields-> $pf = array($GLOBALS["lang"]->$pf, 'select', $sel, $vpf);
                }
            }
            if (gr_role('access', 'users', '7')) {
                $fields->delacc = array($GLOBALS["lang"]->deactivate_account, 'select', '0', '-----', 'yes', $GLOBALS["lang"]->yes, 'no', $GLOBALS["lang"]->no);
            }
        }
        if (!isset($do['side'])) {
            $do['side'] = 'left';
        }
        $fields->aside = array('hidden', 'input', 'hidden', '"'.$do['side'].'"');
    } else if ($do["type"] == "systemhf") {
        if (gr_role('access', 'sys', '5')) {
            $header = vc(file_get_contents('gem/ore/grupo/cache/headers.cch'));
            $footer = vc(file_get_contents('gem/ore/grupo/cache/footers.cch'));
            $bodyopen = vc(file_get_contents('gem/ore/grupo/cache/bodyopen.cch'));
            $bodyclose = vc(file_get_contents('gem/ore/grupo/cache/bodyclose.cch'));
            $fields->headers = array($GLOBALS["lang"]->header, 'textarea', 'text', $header);
            $fields->bodyopen = array($GLOBALS["lang"]->after_body_open_tag, 'textarea', 'text', $bodyopen);
            $fields->bodyclose = array($GLOBALS["lang"]->before_body_closing_tag, 'textarea', 'text', $bodyclose);
            $fields->footers = array($GLOBALS["lang"]->footer, 'textarea', 'text', $footer);
        }
    } else if ($do["type"] == "systembanip") {
        if (gr_role('access', 'sys', '3')) {
            $blist = db('Grupo', 's', 'defaults', 'type', 'blacklist')[0]['v2'];
            $fields->blist = array($GLOBALS["lang"]->blacklist, 'textarea', 'text', $blist);
        }
    } else if ($do["type"] == "systemfilterwords") {
        if (!gr_role('access', 'sys', '4')) {
            exit;
        } $blist = db('Grupo', 's', 'defaults', 'type', 'filterwords')[0]['v2'];
        $fields->blist = array($GLOBALS["lang"]->filterwords, 'textarea', 'text', $blist);

    } else if ($do["type"] == "systemeasycustomizer") {
        if (gr_role('access', 'sys', '2')) {
            $fields->startcolor = array($GLOBALS["lang"]->startcolor, 'input', 'colorpick', '#E91E63');
            $fields->endcolor = array($GLOBALS["lang"]->endcolor, 'input', 'colorpick', '#9C27B0');
        }
    } else if ($do["type"] == "systemappearance") {
        if (gr_role('access', 'sys', '2')) {
            $css = db('Grupo', 's', 'customize', 'device,name<>', 'all', 'custom_css');
            $mobcss = db('Grupo', 's', 'customize', 'device,name<>', 'mobile', 'custom_css');
            $box = $GLOBALS["default"]->boxed;
            $cus = db('Grupo', 's', 'customize', 'name', 'custom_css')[0]['element'];
            if (empty($cus)) {
                $cus = "";
            }
            $fields->boxed = array('Boxed Layout', 'select', $box, $GLOBALS["lang"]->$box, 'enable', $GLOBALS["lang"]->enable, 'disable', $GLOBALS["lang"]->disable);
            $fnt = array();
            $fnt = glob('riches/fonts/*', GLOB_ONLYDIR);
            foreach ($fnt as $al) {
                $al = basename($al);
                if ($al != "grupo") {
                    $fnts[$al] = ucwords($al);
                }
            }
            $fields->defont = array($GLOBALS["lang"]->default_font, 'select', $fnts, $GLOBALS["default"]->default_font);
            $fields->customcss = array('Custom CSS', 'textarea', '', $cus);
            $algn['left'] = $GLOBALS["lang"]->left;
            $algn['right'] = $GLOBALS["lang"]->right;
            $fields->sentalign = array($GLOBALS["lang"]->sent_msg_align, 'select', $algn, $GLOBALS["default"]->sent_msg_align);
            $fields->recievedalign = array($GLOBALS["lang"]->received_msg_align, 'select', $algn, $GLOBALS["default"]->received_msg_align);
            foreach ($css as $c) {
                $key = 'css'.$c['id'];
                $c['name'] = ucwords(str_replace('_', ' ', $c['name']));
                if ($c['type'] == 'background') {
                    $a = $key.'a';
                    $b = $key.'b';
                    $fields->$a = array($c['name'].' - Start Color', 'input', 'colorpick', '"'.$c['v1'].'"');
                    $fields->$b = array($c['name'].' - End Color', 'input', 'colorpick', '"'.$c['v2'].'"');
                } else if ($c['type'] == 'color' || $c['type'] == 'border-color') {
                    $fields->$key = array($c['name'], 'input', 'colorpick', '"'.$c['v1'].'"');
                } else if ($c['type'] == 'font-size') {
                    $c['name'] = $c['name'].' (px)';
                    $c['v1'] = vc($c['v1'], 'num', 1);
                    $fields->$key = array($c['name'], 'input', 'number', '"'.$c['v1'].'"');
                }
            }
            foreach ($mobcss as $c) {
                $key = 'css'.$c['id'];
                $c['name'] = ucwords(str_replace('_', ' ', $c['name']));
                if ($c['type'] == 'background') {
                    $a = $key.'a';
                    $b = $key.'b';
                    $fields->$a = array($c['name'].' - Start Color', 'input', 'colorpick', '"'.$c['v1'].'"');
                    $fields->$b = array($c['name'].' - End Color', 'input', 'colorpick', '"'.$c['v2'].'"');
                } else if ($c['type'] == 'color' || $c['type'] == 'border-color') {
                    $fields->$key = array($c['name'], 'input', 'colorpick', '"'.$c['v1'].'"');
                } else if ($c['type'] == 'font-size') {
                    $c['name'] = $c['name'].' (px)';
                    $c['v1'] = vc($c['v1'], 'num', 1);
                    $fields->$key = array($c['name'], 'input', 'number', '"'.$c['v1'].'"');
                }
            }
        }
    } else if ($do["type"] == "systemsettings") {
        if (!gr_role('access', 'sys', '1')) {
            exit;
        }
        $sys = db('Grupo', 's', 'defaults', 'type,v1<>,v1<>', 'default', 'sent_msg_align', 'received_msg_align');
        foreach ($sys as $s) {
            if ($s['v1'] != 'alert' && $s['v1'] != 'default_font') {
                $key = $s['id'];
                $inp = 'input';
                $type = 'text';
                $val = '"'.$s['v2'].'"';
                if ($s['v1'] === 'timezone') {
                    $inp = 'tmz';
                    $type = gr_tmz();
                    $val = $s['v2'];
                }
                $varkya = $s['v1'];
                $varkyb = $s['v2'];
                if ($s['v1'] === 'userreg' || $s['v1'] === 'join_confirm' || $s['v1'] === 'first_load_guestlogin' || $s['v1'] === 'hide_grouptab' || $s['v1'] === 'random_guest_username' || $s['v1'] === 'show_online_tab' || $s['v1'] === 'show_sender_name' || $s['v1'] === 'gravatar' || $s['v1'] === 'use_enter_as_send' || $s['v1'] === 'ascii_smileys' || $s['v1'] === 'non_latin_usernames' || $s['v1'] === 'send_btn_visible' || $s['v1'] === 'recaptcha' || $s['v1'] === 'releaseguestuser' || $s['v1'] === 'force_https' || $s['v1'] === 'update_list_periodically' || $s['v1'] === 'viewgroups_nologin' || $s['v1'] === 'unsplash_enable' || $s['v1'] === 'autoplay_radio' || $s['v1'] === 'cookie_consent' || $s['v1'] === 'sysmessages' || $s['v1'] === 'tenor_enable' || $s['v1'] === 'boxed' || $s['v1'] === 'smtp_authentication' || $s['v1'] === 'guest_login' || $s['v1'] === 'email_verification') {
                    $selectfields = array();
                    $selectfields['enable'] = $GLOBALS["lang"]->enable;
                    $selectfields['disable'] = $GLOBALS["lang"]->disable;
                    $fields->$key = array($GLOBALS["lang"]->$varkya, 'select', $selectfields, $varkyb);
                } else if ($s['v1'] === 'message_style') {
                    $selectfields = array();
                    $selectfields['style1'] = 'Style 1';
                    $selectfields['style2'] = 'Style 2';
                    $fields->$key = array($GLOBALS["lang"]->$varkya, 'select', $selectfields, $varkyb);
                } else if ($s['v1'] === 'dateformat') {
                    $selectfields = array();
                    $selectfields['dmy'] = 'Day Month Year';
                    $selectfields['mdy'] = 'Month Day Year';
                    $selectfields['ymd'] = 'Year Month Day';
                    $fields->$key = array($GLOBALS["lang"]->$varkya, 'select', $selectfields, $varkyb);
                } else if ($s['v1'] === 'send_email_notification') {
                    $snemls = $GLOBALS["lang"]->someone_mentions.','.$GLOBALS["lang"]->on_group_invitation.','.$GLOBALS["lang"]->receiving_new_message.','.$GLOBALS["lang"]->message_replies;
                    $fields->$key = array($GLOBALS["lang"]->$varkya, 'checkbox', $snemls, '1,2,3,4', $s['v2']);
                } else if ($s['v1'] === 'default_skin_mode') {
                    $fields->$key = array($GLOBALS["lang"]->$varkya, 'select', $s['v2'], $GLOBALS["lang"]->$varkyb, 'light_mode', $GLOBALS["lang"]->light_mode, 'dark_mode', $GLOBALS["lang"]->dark_mode);
                } else if ($s['v1'] === 'censor_bad_words') {
                    $fields->$key = array($GLOBALS["lang"]->$varkya, 'select', $s['v2'], $GLOBALS["lang"]->$varkyb, 'on_sending_message', $GLOBALS["lang"]->on_sending_message, 'on_loading_message', $GLOBALS["lang"]->on_loading_message);
                } else if ($s['v1'] === 'mobile_page_transition') {
                    $mpt[1] = 'Slide In';
                    $mpt[2] = 'Rotate In';
                    $mpt[3] = 'Zoom In';
                    $mpt[4] = 'Back In';
                    $fields->$key = array($GLOBALS["lang"]->$varkya, 'select', $mpt, $s['v2']);
                } else if ($s['v1'] === 'time_format') {
                    $fields->$key = array($GLOBALS["lang"]->$varkya, 'select', $s['v2'], $s['v2'], '12', 12, '24', 24);
                } else if ($s['v1'] === 'autogroupjoin' || $s['v1'] === 'pingroup') {
                    $group = db('Grupo', 's', 'options', 'type', 'group');
                    $groups = array();
                    foreach ($group as $r) {
                        $groups[$r['id']] = $r['v1'];
                    }
                    $fields->$key = array($GLOBALS["lang"]->$varkya, 'select', $groups, $s['v2']);
                } else if ($s['v1'] === 'language') {
                    $lang = db('Grupo', 's', 'phrases', 'type', 'lang');
                    $langs = array();
                    foreach ($lang as $r) {
                        $langs[$r['id']] = $r['short'];
                    }
                    $fields->$key = array($GLOBALS["lang"]->$varkya, 'select', $langs, $s['v2']);
                } else {
                    $fields->$key = array($GLOBALS["lang"]->$varkya, $inp, $type, $val);
                }
            } else if ($s['v1'] == 'alert') {
                $alid = $s['id'];
            } else if ($s['v1'] == 'default_font') {
                $fntid = $s['id'];
            }
        }
        $alerts = array();
        $alrt = glob('gem/ore/grupo/alerts/*');
        foreach ($alrt as $al) {
            $alerts[$al] = ucwords(basename($al, '.mp3'));
        }
        $fields->$alid = array($GLOBALS["lang"]->default_notification_tone, 'select:audselect:', $alerts, $GLOBALS["default"]->alert);
        $fnt = array();
        $fnt = glob('riches/fonts/*', GLOB_ONLYDIR);
        foreach ($fnt as $al) {
            $al = basename($al);
            if ($al != "grupo") {
                $fnts[$al] = ucwords($al);
            }
        }

        $cronjob = 'wget -q -O - '.$GLOBALS["default"]->weburl.'act/cronjob/ >/dev/null 2>&1';
        $fields->rebuilder = array('Rebuild Cache & Reset Logs', 'input', 'text', 0, '"Input yes to Activate"');
        $fields->cronjob = array($GLOBALS["lang"]->cronjob, 'input:selectinp:', 'text', '"'.$cronjob.'"');
        $fields->$fntid = array($GLOBALS["lang"]->default_font, 'select', $fnts, $GLOBALS["default"]->default_font);
        $fields->sitelogo = array($GLOBALS["lang"]->logo, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        $fields->mobilelogo = array($GLOBALS["lang"]->mobile_logo, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        $fields->logo = array($GLOBALS["lang"]->signin_logo, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        $fields->pwaicon = array($GLOBALS["lang"]->pwa_icon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        $fields->favicon = array($GLOBALS["lang"]->favicon, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        $fields->emaillogo = array($GLOBALS["lang"]->emaillogo, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        $fields->welcome = array($GLOBALS["lang"]->welcomeimg, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        $fields->defaultbg = array($GLOBALS["lang"]->defaultbg, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        $fields->defaultbgdark = array($GLOBALS["lang"]->defaultbgdark, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        $fields->imgsocialmedia = array($GLOBALS["lang"]->img_social_media, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');
        $fields->loginbg = array($GLOBALS["lang"]->loginbg, 'input', 'file', 'accept="image/x-png,image/gif,image/jpeg"');

    }
    $fields->choosefiletxt = array($GLOBALS["lang"]->choosefiletxt);
    $r = json_encode($fields);
    gr_prnt($r);
}
?>