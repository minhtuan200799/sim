<?php 
/***********************************************************************/
/*                                                                     */
/*   Copyright (C) 2015. All rights reserved                           */
/*   Author     : Roca Chien, rocachien@yahoo.com                      */
/*   License    : http://nhanhgon.vn                                   */
/*                                                                     */
/*   Created    : 15-09-2015 15:13:05.                                 */
/*   Modified   : 15-09-2015 15:13:05.                                 */
/*                                                                     */
/***********************************************************************/
session_start();
date_default_timezone_set("Asia/Saigon");

// Config env
define('BASE_URL'		, $_SERVER['BASE_URL']);
define('HASH_KEY'		, $_SERVER['HASH_KEY']);
define('WEB_SUBDOMAIN'	, $_SERVER['WEB_SUBDOMAIN']);
define('WEB_PREFIX'	    , $_SERVER['WEB_PREFIX']);
define('CACHE_DIR'	    , WEB_PREFIX . "/". $_SERVER['CACHE_DIR']);
define('WEB_OPK'	    , $_SERVER['WEB_OPK']);

// Do not change any things
define('EXT'			, '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
define('ROOT'			, dirname(__FILE__));
define('SELF'			, pathinfo(__FILE__, PATHINFO_BASENAME));

//include systems
require_once(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . 'system'.EXT);

//load application
$app	= new SYSTEM();
if(empty($app) || !$app->strat())
{
    header("HTTP/1.0 404 Not Found");
}
?>