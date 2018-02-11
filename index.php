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

if($_SERVER['HTTP_HOST'] == 'localhost')
{
    define('BASE_URL'		, "http://localhost/ecart/");
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}else
{
    define('BASE_URL'		, "http://ecart.tv/");
    error_reporting(0);
    ini_set('display_errors', 'Off');
}
define('HASH_KEY'		, "H@dOx4h0sz237X83@jdw");
define('WEB_SUBDOMAIN'	, "ecart");
define('WEB_PREFIX'	    , "u8o9x3e");
define('CACHE_DIR'	    , WEB_PREFIX . "/cache");
define('WEB_OPK'	    , 122);

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