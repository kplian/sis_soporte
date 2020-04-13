<?php
if (!defined('ENVIRONMENT')) {
    $domain = strtolower($_SERVER['HTTP_HOST']);

    switch ($domain) {
        case '172.18.79.207':
            define('ENVIRONMENT', 'development');
            define('HOSTNAME', '{correo.endetransmision.bo:993/imap/ssl}INBOX');
            define('USERNAME', 'xxxx');
            define('PASSWORD', 'xxxx');
            break;
        case '172.18.79.248':
            define('ENVIRONMENT', 'test');
            define('HOSTNAME', '{correo.endetransmision.bo:993/imap/ssl}INBOX');
            define('USERNAME', 'xxxx');
            define('PASSWORD', 'xxxx');
            break;
        default :
            define('ENVIRONMENT', 'production');
            define('HOSTNAME', '{correo.endetransmision.bo:993/imap/ssl}INBOX');
            define('USERNAME', 'xxxx');
            define('PASSWORD', 'xxxx');
            break;
    }
}
