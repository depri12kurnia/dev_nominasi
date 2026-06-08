<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/userguide3/general/hooks.html
|
*/
$hook['post_controller'] = array(
    'class'    => 'Access_log',
    'function' => 'log_access',
    'filename' => 'Access_log.php',
    'filepath' => 'hooks'
);
