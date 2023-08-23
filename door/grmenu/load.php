<?php if(!defined('s7V9pz')) {die();}?><?php if ($GLOBALS["logged"]) {
    ?>
    <i class="gi-menu mmenu subnav">
        <div class='swr-menu'>
            <ul>
                <?php gr_custommenu('show'); ?>
                <li class='formpop' title='<?php gec($GLOBALS["lang"]->edit_profile) ?>' do='edit' btn='<?php gec($GLOBALS["lang"]->update) ?>' act='profile'><?php gec($GLOBALS["lang"]->edit_profile) ?></li>
                <?php
                if (isset($GLOBALS["roles"]['fields'][4])) {
                    ?>
                    <li class='loadside' act='ufields' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_fields) ?>' side='lside'><?php gec($GLOBALS["lang"]->fields) ?></li>
                    <?php
                }
                if (isset($GLOBALS["roles"]['users'][4])) {
                    ?>
                    <li class='loadside' act='users' side='lside' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_users) ?>'><?php gec($GLOBALS["lang"]->users) ?></li>
                    <?php
                }
                if (isset($GLOBALS["roles"]['users'][11])) {
                    ?>
                    <li class='loadside' act='pmlist' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_pm) ?>' side='lside'><?php gec($GLOBALS["lang"]->users_chat) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['features'][14])) {
                    ?>
                    <li class='loadside' act='radiostations' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_stations) ?>' side='lside'><?php gec($GLOBALS["lang"]->radiostations) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['features'][16])) {
                    ?>
                    <li class='loadside' act='stickerpacks' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_stickers) ?>' side='lside'><?php gec($GLOBALS["lang"]->stickers) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['roles'][3])) {
                    ?>
                    <li class='loadside' act='roles' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_roles) ?>' side='lside'><?php gec($GLOBALS["lang"]->roles) ?></li>
                    <?php
                }
                if (isset($GLOBALS["roles"]['users'][5]) && $GLOBALS["default"]->show_online_tab != 'enable') {
                    ?>
                    <li class='loadside' act='online' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_online) ?>' side='lside'><?php gec($GLOBALS["lang"]->online) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['languages'][4])) {
                    ?>
                    <li class='loadside' act='languages' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_languages) ?>' side='lside'><?php gec($GLOBALS["lang"]->languages) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['sys'][7])) {
                    ?>
                    <li class='loadside' act='manageads' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_ads) ?>' side='lside'><?php gec($GLOBALS["lang"]->manage_ads) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['sys'][8])) {
                    ?>
                    <li class='loadside' act='loginproviders' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_providers) ?>' side='lside'><?php gec($GLOBALS["lang"]->providers) ?></li>
                    <?php
                }if (isset($GLOBALS["roles"]['sys'][2])) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->easycustomizer) ?>' do='system' btn='<?php gec($GLOBALS["lang"]->update) ?>' act='easycustomizer'><?php gec($GLOBALS["lang"]->easycustomizer) ?></li>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->appearance) ?>' do='system' btn='<?php gec($GLOBALS["lang"]->update) ?>' act='appearance'><?php gec($GLOBALS["lang"]->appearance) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['sys'][5])) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->header_footer) ?>' do='system' btn='<?php gec($GLOBALS["lang"]->update) ?>' act='hf'><?php gec($GLOBALS["lang"]->header_footer) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['sys'][3])) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->banip) ?>' do='system' btn='<?php gec($GLOBALS["lang"]->update) ?>' act='banip'><?php gec($GLOBALS["lang"]->banip) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['sys'][6])) {
                    ?>
                    <li class='loadside' act='cmenu' side='lside' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_items) ?>'><?php gec($GLOBALS["lang"]->custom_menu) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['sys'][4])) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->filterwords) ?>' do='system' btn='<?php gec($GLOBALS["lang"]->update) ?>' act='filterwords'><?php gec($GLOBALS["lang"]->filterwords) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['sys'][1])) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->settings) ?>' do='system' btn='<?php gec($GLOBALS["lang"]->update) ?>' act='settings'><?php gec($GLOBALS["lang"]->settings) ?></li>
                    <?php
                } if ($GLOBALS["grusrlog"]['role'] != 5 && isset($GLOBALS["roles"]['features'][11])) {
                    ?>
                    <li class='ajx switchmode' data-act=1 data-do='profile' data-type='mode'><?php gr_profile('mode'); ?></li>
                    <?php
                } ?>
                <li class='loadside' act='blocklist' side='lside' zero='0' zval='<?php gec($GLOBALS["lang"]->zero_users) ?>'><?php gec($GLOBALS["lang"]->blocklist) ?></li>
                <li class='ajx skinmode' data-act=1 data-do='profile' data-type='skinmode'><?php gr_profile('skinmode'); ?></li>
                <li class='standby'><?php gec($GLOBALS["lang"]->stand_by); ?></li>
                <li class='ajx' data-act=1 data-do='logout'><?php gec($GLOBALS["lang"]->logout) ?></li>
            </ul>
        </div>


    </i>
    <i class='gi-bell-1 malert goright d-md-none' data-block='alerts'></i>
    <?php
} ?>