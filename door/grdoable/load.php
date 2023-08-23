<?php if(!defined('s7V9pz')) {die();}?><?php if ($GLOBALS["logged"]) {
    ?>
    <i class="gi-list-add subnav udolist">
        <div class='swr-menu r-end'>
            <ul>
                <?php
                if (gr_role('access', 'files', '1')) {
                    ?>
                    <li act='files'><form class='uploadfiles' method='post' action='' enctype="multipart/form-data">
                        <input type='file' multiple name='file[]' />
                        <input type="hidden" name="act" value="1">
                        <input type="hidden" name="type" value="upload">
                        <input type="hidden" name="do" value="files">
                    </form>
                        <span><?php gec($GLOBALS["lang"]->upload_file) ?></span></li>
                    <?php
                } if (gr_role('access', 'groups', '1')) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->create_group) ?>' do='create' btn='<?php gec($GLOBALS["lang"]->create) ?>' act='group'><?php gec($GLOBALS["lang"]->create_group) ?></li>
                    <?php
                } if (gr_role('access', 'users', '1')) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->create_user) ?>' do='create' btn='<?php gec($GLOBALS["lang"]->create) ?>' act='user'><?php gec($GLOBALS["lang"]->create_user) ?></li>
                    <?php
                }if (gr_role('access', 'roles', '1')) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->create_role) ?>' do='create' btn='<?php gec($GLOBALS["lang"]->create) ?>' act='role'><?php gec($GLOBALS["lang"]->create_role) ?></li>
                    <?php
                } if (gr_role('access', 'languages', '1')) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->add_language) ?>' do='create' btn='<?php gec($GLOBALS["lang"]->add) ?>' act='language'><?php gec($GLOBALS["lang"]->add_language) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['features'][14])) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->add_radio_station) ?>' do='create' btn='<?php gec($GLOBALS["lang"]->add) ?>' act='radiostation'><?php gec($GLOBALS["lang"]->add_radio_station) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['features'][16])) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->create_stickerpack) ?>' do='create' btn='<?php gec($GLOBALS["lang"]->create) ?>' act='stickerpack'><?php gec($GLOBALS["lang"]->create_stickerpack) ?></li>
                    <?php
                } if (isset($GLOBALS["roles"]['features'][16])) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->upload_stickers) ?>' do='create' btn='<?php gec($GLOBALS["lang"]->create) ?>' act='stickers'><?php gec($GLOBALS["lang"]->upload_stickers) ?></li>
                    <?php
                } if (gr_role('access', 'sys', '6')) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->add_menu_item) ?>' do='create' btn='<?php gec($GLOBALS["lang"]->add) ?>' act='menuitem'><?php gec($GLOBALS["lang"]->add_menu_item) ?></li>
                    <?php
                } if (gr_role('access', 'sys', '7')) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->create_ad) ?>' do='create' btn='<?php gec($GLOBALS["lang"]->create) ?>' act='ads'><?php gec($GLOBALS["lang"]->create_ad) ?></li>
                    <?php
                } if (gr_role('access', 'sys', '8')) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->add_provider) ?>' do='create' btn='<?php gec($GLOBALS["lang"]->create) ?>' act='loginprovider'><?php gec($GLOBALS["lang"]->add_provider) ?></li>
                    <?php
                }if (gr_role('access', 'fields', '1')) {
                    ?>
                    <li class='formpop' title='<?php gec($GLOBALS["lang"]->add_custom_field) ?>' do='create' btn='<?php gec($GLOBALS["lang"]->add) ?>' act='customfield'><?php gec($GLOBALS["lang"]->add_custom_field) ?></li>
                    <?php
                } ?>
                <li class='formpop d-none' title='<?php gec($GLOBALS["lang"]->delete) ?>' do='alert' btn='<?php gec($GLOBALS["lang"]->delete) ?>' act='clearallalerts'>clearallalerts</li>
            </ul>
        </div>
    </i>
    <span class="vwp d-md-none mprf" no="<?php gec($GLOBALS["user"]['id']); ?>">
        <img class="lazyimg" data-src="<?php gec(gr_img('users', $GLOBALS["user"]['id'])); ?>">
    </span>
    <?php
} ?>