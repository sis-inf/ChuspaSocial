<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category('local_chuspasocial_category', new lang_string('pluginname', 'local_chuspasocial')));
    $settings = new admin_settingpage('local_chuspasocial', new lang_string('pluginname', 'local_chuspasocial'));

    if ($ADMIN->fulltree) {
        $settings->add(new admin_setting_configcheckbox('local_chuspasocial/enablemarketplace', 'Enable Marketplace', '', 1));
        $settings->add(new admin_setting_configtext('local_chuspasocial/maxpostlength', 'Max Post Length', '', '1000', PARAM_INT));
        $settings->add(new admin_setting_configtext('local_chuspasocial/maxpostimages', 'Max Post Images', '', '5', PARAM_INT));
        $settings->add(new admin_setting_configtext('local_chuspasocial/postsperpage', 'Posts Per Page', '', '15', PARAM_INT));
        $settings->add(new admin_setting_configtext('local_chuspasocial/currency', 'Currency', '', 'BOB', PARAM_TEXT));
    }

    $ADMIN->add('localplugins', $settings);
}