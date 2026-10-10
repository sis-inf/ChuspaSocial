<?php

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    
    $settings->add(new admin_setting_configtext(
        'local_chuspasocial/currency',           
        get_string('currency', 'local_chuspasocial'),      
        get_string('currency_desc', 'local_chuspasocial'), 
        'BOB',                                   
        PARAM_ALPHA                              
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_chuspasocial/enablemarketplace',
        new lang_string('enablemarketplace', 'local_chuspasocial'),
        new lang_string('enablemarketplace_desc', 'local_chuspasocial'),
        1
    ));
}