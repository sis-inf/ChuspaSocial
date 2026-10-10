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

    $settings->add(new admin_setting_configtext(
        'local_chuspasocial/postsperpage',
        new lang_string('postsperpage', 'local_chuspasocial'),
        new lang_string('postsperpage_desc', 'local_chuspasocial'),
        20,
        PARAM_INT
    ));
}