<?php if (!defined("ROOT")) exit("No direct script access allowed");
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

//**********Định nghĩa các thành phần của web*****************//
$config["site_copy_right"]      = "© 2018 E-Cart. All rights reserved.";
$config["site_description"]     = "";
$config["site_email"]   = "info@ecart.tv";
$config["site_keyword"] = "";
$config["site_title"]   = "E-Cart";
$config["sys_cache_SoGiay"]     = "0";
$config["sys_extensions"]       = ".html";
$config["sys_home_page"]        = "1";
$config["sys_paging"]   = "100";
$config["sys_run_file"] = "";
$config["sys_skin"]     = "skn01";
$config["sys_table"]    = "10000";
$config["base_url"]             = "http://". str_replace("//","/", $_SERVER["HTTP_HOST"]."/".WEB_SUBDOMAIN."/");

?>