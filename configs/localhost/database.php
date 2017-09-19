<?php
/**
 * Created by PhpStorm.
 * User: liam.nelson2
 * Date: 15/03/2017
 * Time: 12:05
 */

define('DBHOST', 'localhost');
define('DBUSER', 'root');
define('DBPASS', 'grenada');
define('DBNAME', 'sincere_cms');
define('DBPORT', '3306');
define('DBPREFIX', 'cms_');

$gimoDB = array(
	'DBHOST' => 'localhost',
	'DBUSER' => 'root',
	'DBPASS' => 'grenada',
	'DBNAME' => 'cms_sincere',
	'DBPORT' => '3306',
	'PREFIX' => 'cms_',
	'DEBUG'  => true,
);

/**
 *    make sure you have change value on frontend/configs    ---> (database=>DBCHARSET,config=>WEB_CHARSET)
 *                                       cms_admin/configs    ---> (database=>DBCHARSET,config=>WEB_CHARSET)
 */
define('DBCHARSET', 'utf8');