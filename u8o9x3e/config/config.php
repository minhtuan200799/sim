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
$config["image_max_num"]        = "5";
$config["image_max_size"]       = "500";
$config["post_maxlength"]       = "1500";
$config["site_copy_right"]      = "© 2015 Nhanh Gọn. All rights reserved.";
$config["site_description"]     = "Với 30.000.000+ tổng lượt truy cập và 200.000+ lượt truy cập mỗi ngày, nhanhgon.vn là website tìm kiếm, mua bán rao vặt hàng đầu đầu Việt Nam, đa dạng các loại mặt hàng, dịch vụ .";
$config["site_email"]   = "info@nhanhgon.vn";
$config["site_keyword"] = "Với 30.000.000+ tổng lượt truy cập và 200.000+ lượt truy cập mỗi ngày, nhanhgon.vn là website tìm kiếm, mua bán rao vặt hàng đầu đầu Việt Nam, đa dạng các loại mặt hàng, dịch vụ .";
$config["site_title"]   = "Mua nhanh bán gọn";
$config["sys_cache_SoGiay"]     = "0";
$config["sys_extensions"]       = ".html";
$config["sys_home_page"]        = "1";
$config["page_cate"]        = "danh-muc";
$config["page_detail"]        = "rao-vat";
$config["page_server"]        = "1";
$config["sys_paging"]   = "100";
$config["sys_run_file"] = "";
$config["sys_skin"]     = "nhanhgon02";
$config["sys_table"]    = "10000";
$config["timer_refresh"]        = "10";
$config["timer_register"]       = "900";
$config["title_maxlength"]      = "100";
$config["base_url"]             = "http://". str_replace("//","/", $_SERVER["HTTP_HOST"]."/".WEB_SUBDOMAIN."/");

?>