<?php
return array( 

    /*
    |--------------------------------------------------------------------------
    | oAuth Config
    |--------------------------------------------------------------------------
    */

    /**
     * Storage
     */
    'storage' => 'Session', 

    /**
     * Consumers
     */
    'consumers' => array(

        /**
         * Dropbox
         */
        'Dropbox' => array(
            'client_id'     => 'ib2mwoce1fw11ds',
            'client_secret' => '15ag1877vr0pr0x',
            'scope'         => array(),
        ),
        'Google' => array(
            'client_id'     => '1080689220501-ikhkjc5m7o0jmocg6qo27qu4bgodmla9.apps.googleusercontent.com',
            'client_secret' => 'K4Q0iTLuDlOMuY4oBt_bqkAm',
            'scope'         => array('userinfo_email', 'userinfo_profile','https://www.googleapis.com/auth/drive',),
        ),
        'Box' => array(
            'client_id'     => '5jy9vikaqkb6yrutdt2nn6nbp7411fg1',
            'client_secret' => 'k48CSZoqO6DqND62Kpuj9iTNXvHg3NFM',
            'scope'         => array(),
        ),      

    )

);