<?php if (!defined('ROOT')) exit('No direct script access allowed');
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

class COI extends SYSTEM
{
    var $session;
    public function  __construct()
    {
        parent::__construct();

        if($_SERVER['HTTP_HOST'] == "localhost")
        {
            $this->cache_live = 0;
        }else
        {
            $this->cache_live = $this->conf['sys_cache_SoGiay'];
        }
//        $this->db	= new Database($this->conf['dbview']);

//        include_once(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . "session".EXT);
//        $this->session	= new Session($this->db);
        //$this->session->Online();
        $this->load_module();
    }
    public function load_module()
    {
        $this->site_title = $this->config("site_title");
        $this->site_description = $this->config("site_description");
        $this->site_keyword = $this->config("site_keyword");
        //$this->check_login();

        $page = segment(1);

        switch($page)
        {
            case $this->config("page_cate") : $this->cate(); break;
            case $this->config("page_detail") : $this->detail(); break;

            case "search" : $this->search(); break;
            case "udb" : $this->update_database(); break;

            case "get-phone" : $this->ajax_get_phone(); break;
            case "get-captcha" : $this->ajax_get_captcha(); break;

            case "trang-chu" : $this->home_page(); break;
            case "gioi-thieu"  : $this->news(2); break;
            case "quy-dinh"  : $this->news(3); break;
            case "lien-he"  : $this->news(1); break;

            case "dang-tin"  : $this->post(); break;
            case "xem-lai"  : $this->review(); break;
            case "chi-tiet-tin-dang"  : $this->post_2(); break;
            case "them-hinh-anh"  : $this->post_image(); break;

            case "update-cate-count"  : $this->refresh_table(); break;

            default: $this->home_page(); break;
        }

        $this->clear_data();
    }
    private function start($page)
    {
        if(empty($page)) return;

        $this->xtpl_start($page);

        foreach($this->language as $key => $val)
        {
            $this->xtpl->assign($key, $val);
        }
        foreach($this->conf as $key => $val)
        {
            $this->xtpl->assign($key, $val);
        }

        $this->general();

        // tracking by Google
        if($_SERVER["HTTP_HOST"] != 'localhost') $this->xtpl->assign('GOOGLE_ANALYTIC', "<script>var _gaq = _gaq || [];_gaq.push(['_setAccount', 'UA-27338332-1']);_gaq.push(['_trackPageview']);(function() {var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);})();</script>");
    }
    private function show()
    {
        $this->xtpl_end();
    }
    private function general()
    {
        // Menu links
        $this->xtpl->assign("MN_HOME", site_url());
        $this->xtpl->assign("MN_NEWS_BUY", site_url());
        $this->xtpl->assign("MN_NEWS_SALE", site_url());
        $this->xtpl->assign("MN_INTRO", site_url("gioi-thieu"));
        $this->xtpl->assign("MN_PRIVACY", site_url("quy-dinh"));
        $this->xtpl->assign("MN_CONTACT", site_url("lien-he"));
        $this->xtpl->assign("PAGE_SEARCH", site_url("search"));
        $this->xtpl->assign("PAGE_POST", site_url("dang-tin"));
        $this->xtpl->assign("PAGE_POST_2", site_url("chi-tiet-tin-dang"));

        // Get site analysis
        $this->site_analysis();

        // Show ads
        //$this->show_ads();

    }
    private function menu_left()
    {
        if($this->xtpl->is_cache("menu_left"	, $this->cache_live))
        {
            $this->xtpl->parse("main.menu_left", $this->cache_live, "menu_left");
        }else{

            $cat = $this->conf["raovat"];

            if(!$cat || count($cat) <= 0)
            {
                $this->update_cate_count();
            }

            foreach($cat as $key => $val)
            {
                $this->xtpl->assign("NAME", $val["name"]);
                $this->xtpl->assign("COUNT", $val["count"]);
                $this->xtpl->assign("LINK", site_url($this->config("page_cate") ."/". $val["key"],""));

                $this->xtpl->parse("main.menu_left.cate");
            }
            $this->xtpl->parse("main.menu_left", $this->cache_live, "menu_left");
        }
    }
    private function site_analysis()
    {
//        $this->xtpl->assign("Visitor_day", number_format($this->session->Visitor_day(), 0, ".", "."));
//        $this->xtpl->assign("Visitor_month", number_format($this->session->Visitor_month(), 0, ".", "."));
//        $this->xtpl->assign("Visitor", number_format($this->session->Visitor(), 0, ".", "."));

        $this->xtpl->parse("main.analysis");
        $this->xtpl->parse("main.google_analysis");
    }
    private function  show_ads()
    {
        $xtpl = $this->xtpl_start('ads', true);

        if($xtpl->is_cache("main"	, $this->cache_live))
        {
            $xtpl->parse("main", $this->cache_live, "main");
        }else{
            $sql = "SELECT * FROM page_slide_v02 WHERE slide_cid ='2' ORDER BY slide_index;";
            $result	= $this->db->query($sql);
            if(!empty($result) && $this->db->numrows($result) >0)
            {
                while($row = $this->db->fetchrow($result))
                {
                    $xtpl->assign("SLIDE_URL", BASE_URL.$row['slide_image']);
                    $xtpl->assign("SLIDE_LINK", empty($row['slide_link']) ? "javascript:void(0)" : $row['slide_link']);
                    $xtpl->assign("SLIDE_TITLE", $row['slide_title']);
                    $xtpl->parse("main.row_1");
                }
            }

            $sql = "SELECT * FROM page_slide_v02 WHERE slide_cid ='3' ORDER BY slide_index;";
            $result	= $this->db->query($sql);
            if(!empty($result) && $this->db->numrows($result) >0)
            {
                while($row = $this->db->fetchrow($result))
                {
                    $xtpl->assign("SLIDE_URL", BASE_URL.$row['slide_image']);
                    $xtpl->assign("SLIDE_LINK", empty($row['slide_link']) ? "javascript:void(0)" : $row['slide_link']);
                    $xtpl->assign("SLIDE_TITLE", $row['slide_title']);
                    $xtpl->parse("main.row_2");
                }
            }

            $xtpl->parse("main", $this->cache_live, "main");
        }

        $ads	= $xtpl->text("main");
        unset($xtpl);

        $this->xtpl->assign('ADS', $ads);
    }
    private function send_email()
    {
        $confirm = segment(2, false);
        if($confirm != 99) return false;
        $sub		= "Khách hàng liên hệ";
        $to			= $this->config("site_email");
        $from		= $_POST['email'];
        $user		= $_POST['name'];

        $content	= "Chào Admin,<br><br>

		Có khách hàng liên hệ với thông tin chi tiết như sau:<br>
		Họ tên là: <b>". $_POST['name'] .";</b><br>
		Điện thoại: <b>". $_POST['phone'] .";</b><br>
		Địa chỉ email: <b>". $_POST['email'] ."</b> <br><br>
		Nội dung: <br><b>". $_POST['message'] ."</b> <br><br>


		Vui lòng xử lý cho khách hàng liền nhé !
		";
        //
        require (ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . 'email'.EXT);
        echo send_email($user, $from, $to, $sub, $content);
    }
    public function ajax_get_captcha()
    {
        $captcha = create_captcha();

        $_SESSION["CAPTCHA_WORD"] = $captcha["word"];
        echo $captcha["image"];

        return true;
    }
    public function ajax_get_phone()
    {
        $s = segment(2);
        $d = segment(3);
        $img_default = BASE_URL . "public/skin/". $this->config("sys_skin") ."/images/btn-load-phone.png";

        $cats = $this->config("raovat");

        foreach($cats as $val)
        {
            if($val["key"] == $s) $cat = $val;
        }
        if(empty($cat)) {
            echo $img_default;
            return;
        }

        $table = "raovat_" . str_replace("-", "", $s);

        $sql = "SELECT * FROM $table WHERE id='$d' LIMIT 1";
        $result	= $this->db->query($sql);
        if(!empty($result) && $this->db->numrows($result) >0)
        {
            while($row = $this->db->fetchrow($result))
            {
                $phone = $row['user'];
                echo text_to_image($phone);
                return true;
            }
        }

        echo $img_default;
        return false;
    }
    public function refresh_table()
    {
        $this->update_cate_count();
        echo "done";
    }
    public function search()
    {
        $this->start('index');
        $this->menu_left();

        $p = $c = $q = $w = "";
        $current_page = segment(2);


        $search = @$_SESSION['SEARCHING'];

        if(!empty($search))
        {
            $search = explode("~", $search);
            $q = $search[0];
            $c = $search[1];
            $p = $search[2];
        }

        $q = empty($_GET['q']) ? $q : $_GET["q"];
        $search_cate = empty($_GET['cate']) ? $c : $_GET["cate"];
        $search_place = empty($_GET['place']) ? $p : $_GET["place"];

        $cate = $this->get_cate_id($search_cate);


        if(!empty($q))
        {
            $w = " WHERE ";
            $q = strip_tags($q);
            $q = str_replace(array("'", "--", "select", "delete", "union"), "", $q);
            $q = preg_replace('/\s+/', ' ', trim($q));

            $_SESSION['SEARCHING'] = "$q~$search_cate~$search_place";
        }

        if(strlen($q) < 1) {
            alert("Từ khóa cần tìm chưa đủ dài.");
            reload();
            return;
        }



        $this->xtpl->assign("SEARCH_PLACE_URL", site_url("search") ."?q=".$q . "&cate=$search_cate" . "&place=SELECTPLACE");
        $this->xtpl->assign("SEARCH_CATE_URL", site_url("search") ."?q=".$q . "&place=$search_place" . "&cate=SELECTCATE");

        $this->xtpl->assign("SEARCH_Q", $q);
        $this->xtpl->parse("main.logo_detail");


        $limit = intval($this->config("sys_paging")); $limit = $limit / 2;
        $from = empty($current_page) ? 0: $current_page - 1;
        $from = $from * $limit;

        $w .= "(title LIKE '%$q%' OR detail LIKE '%$q%' ) AND";
        $w .= (!empty($cate)) ? " cate = '$cate' AND" : "";
        $w .= (!empty($search_place)) ? " place = '$search_place' AND" : "";


        $w = rtrim($w, "AND");

        $sql = "SELECT DISTINCT md5, title, time, keywords, place, cate, detail FROM page_search_v01 $w ORDER BY time DESC LIMIT $from, $limit";
        //$sql = "SELECT title, time, keywords, place, cate, detail FROM page_search_v01 WHERE `md5` IN (SELECT DISTINCT `md5` FROM page_search_v01 $w)  ORDER BY time DESC LIMIT $from, $limit";
        $result	= $this->db->query($sql);
        if(!empty($result) && $this->db->numrows($result) >0)
        {
            $count = $from;
            while($row = $this->db->fetchrow($result))
            {
                $count ++;

                $title = $row['title'];
                $title = mb_strtolower($title, 'UTF-8');
                $title = mb_ucfirst($title);


                $description = $row['keywords'];
                $description = search_highlight($description, array($q));

                $this->xtpl->assign("STT", $count);
                $this->xtpl->assign("TITLE", $title);
                $this->xtpl->assign("DETAIL", $description);
                $this->xtpl->assign("PLACE", $this->get_place($row['place']));
                $this->xtpl->assign("TIME", date("H\hi\-d/m", $row['time']));
                $this->xtpl->assign("LINK", site_url($this->config("page_detail") ."/" . $this->get_cate_key($row['cate']) ."/". $row['detail'], $title));

                $this->xtpl->parse("main.search.row");
            }
        }else
        {
            $this->other_link();
            $this->xtpl->parse("main.not_found");
            $this->show();
            return;
        }

        $sql = "SELECT COUNT(*) AS num FROM page_search_v01 $w";
        $result	= $this->db->query($sql);
        if(!empty($result) && $this->db->numrows($result) >0)
        {
            while($row = $this->db->fetchrow($result))
            {
                $num_row = $row['num'];

                // Dishplay detail a cate
                $this->xtpl->assign("CATE_NAME", "Kết quả tìm kiếm");
                $this->xtpl->assign("CATE_COUNT", $num_row);

                // Show city filter
                $this->xtpl->assign("STUFF", $this->config("sys_extensions"));
                $this->xtpl->assign("LINK_FILTER", site_url("trang-chu"));

                $this->xtpl->assign("CATEGORY_SELECT", $this->get_cate_select($search_cate));
                $this->xtpl->assign("PLACE_SELECT", $this->get_place_select($search_place));

                $this->xtpl->parse("main.search.detail");

                $paging = new Paging(array("url"=> "search", "current" => $current_page, "total" => intval($num_row), "perpage" => $limit));
                $this->xtpl->assign("PAGING_CONTENT", $paging->show());
                $this->xtpl->parse("main.search.paging");
            }
        }




        $this->xtpl->parse("main.search");


        $this->site_title =  "Tìm kiếm";
        $this->site_keyword = "Tìm kiếm";
        $this->site_description = "Tìm kiếm";

        $this->show();
    }
    public  function home_page()
    {
        $this->start('index');


        $this->show();
    }
    public function news($id=1)
    {
        $this->start('index');
        $this->menu_left();

        $id = empty($id) ? segment(2) : $id;

        $sql = "SELECT * FROM page_content_v01 WHERE cont_id = '$id' LIMIT 1;";
        $result	= $this->db->query($sql);
        if(!empty($result) && $this->db->numrows($result) >0)
        {
            while($row = $this->db->fetchrow($result))
            {
                $this->xtpl->assign("DETAIL", $row['cont_detail']);
            }
        }

        $this->xtpl->parse("main.news");
        $this->show();
    }
    public  function cate()
    {
        $this->start('index');
        $this->menu_left();

        $this->xtpl->parse("main.logo_detail");

        $s = segment(2);
        $city = segment(3, false);
        $current_page = segment(4, false);

        $page_cate = $this->config("page_cate");
        $page_detail = $this->config("page_detail");

        $limit = intval($this->config("sys_paging"));
        $from = empty($current_page) ? 0: $current_page - 1;
        $from = $from * $limit;

        $cats = $this->config("raovat");

        foreach($cats as $val)
        {
            if($val["key"] == $s) $cat = $val;
        }
        if(empty($cat)) {
            $this->other_link();
            $this->xtpl->parse("main.not_found");
            $this->show();
            return;
        }

        $table = "raovat_" . str_replace("-", "", $s);

        $w = empty($city) ? "" : " WHERE place = '$city'";
        $sql = "SELECT * FROM $table $w ORDER BY datetime DESC LIMIT $from, $limit";
        $result	= $this->db->query($sql);
        if(!empty($result) && $this->db->numrows($result) >0)
        {
            $count = $from;
            while($row = $this->db->fetchrow($result))
            {
                $count ++;

                $title = $row['title'];
                $title = mb_strtolower($title, 'UTF-8');
                $title = mb_ucfirst($title);

                $this->xtpl->assign("STT", $count);
                $this->xtpl->assign("TITLE", $title);
                $this->xtpl->assign("PLACE", $this->get_place($row['place']));
                $this->xtpl->assign("TIME", date("H\hi\-d/m", $row['datetime']));
                $this->xtpl->assign("LINK", site_url("$page_detail/$s/". $row['id'], $title));

                $this->xtpl->parse("main.cate.row");
            }



        } else{
            $this->other_link();
            $this->xtpl->parse("main.not_found");
            $this->show();
            return;
        }


        $sql = "SELECT COUNT(*) AS num FROM $table $w";
        $result	= $this->db->query($sql);
        if(!empty($result) && $this->db->numrows($result) >0)
        {
            while($row = $this->db->fetchrow($result))
            {
                $cat["count"] = $row['num'];
            }
        }

        // Dishplay paging
        if(intval($cat["count"]) > 0)
        {
            $city = empty($city) ? "0" : $city;
            $paging = new Paging(array("url"=> "$page_cate/$s/$city", "current" => $current_page, "total" => intval($cat["count"]), "perpage" => $limit));
            $this->xtpl->assign("PAGING_CONTENT", $paging->show());
            $this->xtpl->parse("main.cate.paging");
        }

        // Dishplay detail a cate
        $this->xtpl->assign("CATE_NAME", $cat["name"]);
        $this->xtpl->assign("CATE_COUNT", $cat["count"]);
        $this->xtpl->parse("main.cate.detail");

        // Show city filter
        $this->xtpl->assign("LINK_FILTER", site_url("$page_cate/$s"));
        $this->xtpl->assign("STUFF", $this->config("sys_extensions"));
        $this->xtpl->assign("PLACE_SELECT", $this->get_place_select($city));

        $this->xtpl->parse("main.cate");

        $this->site_title =  $cat["name"];
        $this->site_keyword = $cat["name"];
        $this->site_description = $cat["name"];

        $this->show();
    }
    public function detail()
    {
        $this->start('detail');
        $this->menu_left();

        $this->xtpl->parse("main.logo_detail");
//echo "<pre>"; $phone = get_phone_number("Liên hệ: 0939.702 779 (SMS hay Zalo đều được) BÁN SIM - LIÊN HỆ ĐT - 090.8888.091 090.100.7179 1,7 Giá 300n/ Sim Giá 200n/ Sim Giá 550n/ Sim 090.100.68.79 3.5T 093.666.7039 093.662.1001 0939.875.039 090.100.68.39 1.5T 093.669.0139 093.660.1771 0939.874.079 090.108.3968 2,5 0902.082.139 090.77.6868.5 0936.930.279 090.108.5568 2,5 0936.656.139 0907.027.599 0936.590.279 090.108.3986 700 0939.874.239 093.669.7790 0939.458.279 090.108.3978 700 093.665.9239 093.92.13683 0939.463.379 091.777.9993 3,5 0939.874.339 099.6869.268 0907.245.379 094.233.0168 700 0939.86.1439 0993.125.179 0939.938.479 0971.857.168 400 0939.3264.39 0993.12.52.33 0939.874.679 .01234567.413 4,5 0939.876.439 09.235678.41 09393.14.779 .0123456.1692 900 093.990.6439 09.235678.16 090.100.3439 .0123452.7779 900 0901.227.239 0939.79.0136 090.108.55.39 .01666.366668 6tr 090.737.5639 093.668.90.80 0939.874.768 .01999999.089 8.9tr 0901.007.168 1,7"); echo end($phone);
        $s = segment(2);
        $d = segment(3);

        $cats = $this->config("raovat");

        foreach($cats as $val)
        {
            if($val["key"] == $s) $cat = $val;
        }
        if(empty($cat)) {
            $this->other_link();
            $this->xtpl->parse("main.not_found");
            $this->show();
            return;
        }

        $table = "raovat_" . str_replace("-", "", $s);

        $sql = "SELECT * FROM $table WHERE id='$d' LIMIT 1";
        $result	= $this->db->query($sql);
        if(!empty($result) && $this->db->numrows($result) >0)
        {
            while($row = $this->db->fetchrow($result))
            {
                $detail = $row['detail'];
                $detail = strip_tags($detail, '<br>');

                $this->xtpl->assign("DETAIL_PHONE", $row['user']);
                $this->xtpl->assign("DETAIL_TITLE", $row['title']);
                $this->xtpl->assign("DETAIL_CONTENT", $detail);
                $this->xtpl->assign("DETAIL_DATE", date("d/m/Y", $row['datetime']));
                $this->xtpl->assign("DETAIL_COUNT", $row['viewcount']);
                $this->xtpl->assign("DETAIL_PLACE", $this->get_place($row['place']));
                $this->xtpl->assign("LINK_LOAD_PHONE", site_url("get-phone/" . $s ."/". $d, $row['title']));

                $time_table = ltrim($table, "raovat_");
                $sql = "SELECT * FROM raovat_comment WHERE post='$d' AND `table` = '$time_table' LIMIT 1;";
                $result2	= $this->db->query($sql);

                if(!empty($result2) && $this->db->numrows($result2) >0)
                {
                    $row2 = $this->db->fetchrow($result2);
                    if(!empty($row2["comment"]))
                    {
                        $comments = explode("~", $row2["comment"]);
                    }

                }

                if(strlen($row['image']) > 10)
                {
                    $images = explode(",", $row['image']);
                    $upload = "public/uploads/Content/";
                    $img_url = BASE_URL . $upload;
                    $num_img = count($images);

                    if($num_img == 1)
                    {
                        $img = $images[0];
                        $medium = str_replace(".", "_m.", $img);

                        if(strpos($img, "://") === false)
                        {
                            if($this->check_image_exist($upload . $img))
                            {
                                $this->xtpl->assign("DETAIL_IMAGE", $img_url . $medium);
                                $this->xtpl->parse("main.detail.detail_info.image_one");
                            }
                        }else
                        {
                            if($this->check_image_exist($img))
                            {
                                $this->xtpl->assign("DETAIL_IMAGE", $medium);
                                $this->xtpl->parse("main.detail.detail_info.image_one");
                            }
                        }


                    } elseif($num_img > 1)
                    {
                        $num_show = 0;
                        foreach($images as $index => $img)
                        {
                            $medium = str_replace(".", "_m.", $img);
                            $small = str_replace(".", "_s.", $img);

                            if(strpos($img, "://") === false){
                                if($this->check_image_exist($upload . $img))
                                {
                                    $num_show++;
                                    $this->xtpl->assign("DETAIL_IMAGE", $img_url . $medium);
                                    $this->xtpl->assign("SMALL_IMAGE", $img_url . $small);

                                    if(isset($comments) && !empty($comments[$index]))
                                    {
                                        $this->xtpl->assign("IMG_COMMENT", $comments[$index]);
                                    }

                                    $this->xtpl->parse("main.detail.detail_info.image_slide.image");
                                }
                            }else
                            {
                                if($this->check_image_exist($img))
                                {
                                    $num_show++;
                                    $this->xtpl->assign("DETAIL_IMAGE", $medium);
                                    $this->xtpl->assign("SMALL_IMAGE", $small);

                                    if(isset($comments) && !empty($comments[$index]))
                                    {
                                        $this->xtpl->assign("IMG_COMMENT", $comments[$index]);
                                    }
                                    $this->xtpl->parse("main.detail.detail_info.image_slide.image");
                                }
                            }

                        }

                        if($num_show > 0) $this->xtpl->parse("main.detail.detail_info.image_slide");
                    }
                }



                $this->xtpl->assign("LINK_SHARE", site_url($this->config("page_detail") ."/" .$s ."/". $row['id'], $row['title']));

                $title = remove_bad($row['title']);
                $title = str_replace("-", " ", $title);

                $detail = str_replace("<br>", " ", $detail);
                $detail = remove_bad($detail);
                $detail = str_replace("-", " ", $detail);

                $this->site_title =  $title . " - NhanhGon.vn";//"NhanhGon.vn : " . $cat["name"] . " &raquo; " .$row['title'];
                $this->site_keyword = $detail;
                $this->site_description = $detail;
                $this->xtpl->parse("main.detail.detail_info");

                $sql = "UPDATE  $table SET  viewcount =  (viewcount + (FLOOR(RAND() * 10) + 2)) WHERE  id = '$d' LIMIT 1;";
                $this->db->query($sql);
            }
        }else{
            $this->other_link();
            $this->xtpl->parse("main.not_found");
        }



        $sql = "SELECT * FROM $table ORDER BY datetime DESC LIMIT 15";
        $result	= $this->db->query($sql);
        if(!empty($result) && $this->db->numrows($result) >0)
        {
            $count = 0;
            while($row = $this->db->fetchrow($result))
            {
                $count ++;
                $title = $row['title'];
                $title = mb_strtolower($title, 'UTF-8');
                $title = mb_ucfirst($title);

                $this->xtpl->assign("STT", $count);
                $this->xtpl->assign("TITLE", $title);
                $this->xtpl->assign("PLACE", $this->get_place($row['place']));
                $this->xtpl->assign("TIME", date("H\hi\-d/m", $row['datetime']));
                $this->xtpl->assign("LINK", site_url($this->config("page_detail") ."/" .$s ."/". $row['id'], $title));

                $this->xtpl->parse("main.detail.other.row");
            }
            $this->xtpl->assign("CATE_LINK", site_url($this->config("page_cate") ."/" .$s));
            $this->xtpl->assign("CATE_NAME", $cat["name"]);
            $this->xtpl->assign("CATE_COUNT", $cat["count"]);

            $this->xtpl->parse("main.detail.other");
        }

        if(!empty($_POST['btnReport']))
        {
            $language = empty($_POST['typeLanguage']) ? 0 : 1;
            $picture = empty($_POST['typePicture']) ? 0 : 1;
            $politic = empty($_POST['typePolitic']) ? 0 : 1;
            $law = empty($_POST['typeLaw']) ? 0 : 1;
            $content = empty($_POST['reportArea']) ? "" : mysql_real_escape_string(trim($_POST['reportArea']));
            $ip = getUserIP();

            $sql = "INSERT INTO `raovat_report` (`report_post`, `report_table`, `report_language`, `report_picture`, `report_politic`, `report_law`, `report_content`, `report_ip`) VALUES ('$d', '$table', '$language', '$picture', '$politic', '$law', '$content', '$ip');";
            if(!$this->db->query($sql))
            {
                write_log($sql);
            }
            reload($this->config("page_detail") . "/" . $s ."/" . $d, "report", 0);
        }

        $sql = "SELECT SUM(report_language) AS lang, SUM(report_picture) AS picture, SUM(report_politic) AS politic, SUM(report_law) AS law, SUM(if(report_content = '', 0, 1)) AS cont  FROM `raovat_report` WHERE report_post = '$d' AND report_table = '$table' LIMIT 1;";
        $result	= $this->db->query($sql);
        if(!empty($result) && $this->db->numrows($result) >0)
        {
            $row = $this->db->fetchrow($result);
            if(($row['lang'] >= 10) || ($row['picture'] >= 5) || ($row['politic'] >= 5) || ($row['law'] >= 5) || ($row['cont'] >= 10) )
            {
                $sql = "DELETE FROM $table WHERE `id` = '$d' LIMIT 1;";
                if($this->db->query($sql))
                {
                    $this->update_cate_count();
                }

            }
        }

        $ad_link = $this->config("AD_LINK");
        $ad_text = $this->config("AD_TEXT");
        if($ad_link && $ad_text && strlen($ad_link) > 5 && strlen($ad_text) > 5) $this->xtpl->parse("main.detail.detail_ad");

        $this->xtpl->parse("main.detail.report");

        $this->xtpl->parse("main.detail");
        $this->show();
    }
    private function get_place($play)
    {
        $config = array();
        require(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . "config".DIRECTORY_SEPARATOR."places".EXT);
        $places = $config["place"];

        foreach($places as $key => $val)
        {
            if($key == $play) return $val[0];
        }
        return "";
    }
    private function get_place_id($place)
    {
        $config = array();
        require(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . "config".DIRECTORY_SEPARATOR."places".EXT);
        $places = $config["place"];

        foreach($places as $key => $val)
        {
            if(in_array($place, $val)) return $key;
        }
        return 2;
    }
    private function get_place_select($select)
    {
        $config = array();
        require(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . "config".DIRECTORY_SEPARATOR."places".EXT);
        $places = $config["place"];

        $option = "";
        foreach($places as $key => $val)
        {
            $text = $val[0];
            $selected = ($key == $select) ? 'selected="selected"' : "";
            $option .= "<option value='$key' $selected>$text</option>";
        }
        return $option;
    }
    private function get_cate_select($select)
    {
        $config = array();
        require(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . "config".DIRECTORY_SEPARATOR."category".EXT);
        $cate = $config["raovat"];

        $option = "";
        foreach($cate as $key => $val)
        {
            $text = $val["name"];
            $value = $val['key'];
            $selected = ($value == $select) ? 'selected="selected"' : "";
            $option .= "<option value='$value' $selected>$text</option>";
        }
        return $option;
    }
    private function get_cate_id($cate)
    {
        $config = array();
        require(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . "config".DIRECTORY_SEPARATOR."category".EXT);
        $cates = $config["raovat"];

        foreach($cates as $key => $val)
        {
            if($cate == $val["key"]) return $key;
        }
        return 0;
    }
    private function get_cate_key($cate_id)
    {
        $config = array();
        require(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . "config".DIRECTORY_SEPARATOR."category".EXT);
        $cates = $config["raovat"];

        if($cate_id && $cates && @$cates[$cate_id])
        {
            $cate = $cates[$cate_id];
            if($cate && is_array($cate)) return $cate['key'];
        }

        return false;
    }
    private function check_register_again()
    {
        $res = $this->session->getKey("REGISTER_AGAIN", "data");

        if(!empty($res))
        {
            list($cate, $detail_id, $time) = explode("~", $res);

            $time = $this->config("timer_register") - (time() - $time);

            if($time > 0)
            {
                $this->xtpl->assign("timer_register", $time);
                $this->xtpl->assign("LINK_DETAIL", site_url($this->config("page_detail") ."/" . $this->get_cate_key($cate) ."/". $detail_id, "xem chi tiet"));
                $this->xtpl->assign("LINK_IMAGES", site_url("them-hinh-anh"));


                $cats = $this->config("raovat");
                $table = "raovat_" . str_replace("-", "", $cats[$cate]["key"]);

                $sql = "SELECT * FROM $table WHERE id='$detail_id' LIMIT 1";
                $result	= $this->db->query($sql);
                if(!empty($result) && $this->db->numrows($result) >0)
                {
                    while($row = $this->db->fetchrow($result))
                    {
                        $title = url_validate($row['title']);
                        $title = str_replace("-", " ", $title);

                        $detail = str_replace("<br>", " ", $row['detail']);
                        $detail = url_validate($detail);
                        $detail = str_replace("-", " ", $detail);

                        $this->site_title =  $title . " - NhanhGon.vn";
                        $this->site_keyword = $detail;
                        $this->site_description = $detail;

                        $images = explode(",", $row['image']);
                        if(count($images) == 1)
                        {
                            $img = $images[0];
                            $medium = str_replace(".", "_m.", $img);
                            if(!empty($medium) && is_file(ROOT . "/public/uploads/Content/" . $medium))
                            {
                                $this->xtpl->assign("DETAIL_IMAGE", BASE_URL . "public/uploads/Content/" . $medium);
                            }elseif(!empty($img) && is_file(ROOT . "/public/uploads/Content/" . $img))
                            {
                                $this->xtpl->assign("DETAIL_IMAGE", BASE_URL . "public/uploads/Content/" . $img);
                            }
                        }else
                        {
                            foreach($images as $index => $img)
                            {
                                $medium = str_replace(".", "_m.", $img);
                                if(!empty($medium) && is_file(ROOT . "/public/uploads/Content/" . $medium))
                                {
                                    $this->xtpl->assign("DETAIL_IMAGE", BASE_URL . "public/uploads/Content/" . $medium);
                                }elseif(!empty($img) && is_file(ROOT . "/public/uploads/Content/" . $img))
                                {
                                    $this->xtpl->assign("DETAIL_IMAGE", BASE_URL . "public/uploads/Content/" . $img);
                                }
                            }
                        }

                        $this->xtpl->assign("LINK_SHARE", site_url($this->config("page_detail") ."/" . $this->get_cate_key($cate) ."/". $row['id'], $title));
                    }
                }


                $this->xtpl->parse("main.post_completed");
                $this->show();
                die();
            }
        }
    }
    private function check_image_exist($img)
    {
        if(empty($img) || strlen($img) < 10) return false;
        if(strpos($img, "://") !== false) return file_exists($img);
        if(file_get_contents($img) === false) return false;

        return true;
    }
    public  function post()
    {
        $this->start('post');
        $this->xtpl->parse("main.logo_vertical");

        $this->menu_left();
        $this->check_register_again();

        $cate = @$_SESSION["CHOOSE_CATE"];
        $cate = isset($cate) ? $cate : get_cookie("CHOOSE_CATE");

        if(isset($_POST['post']))
        {
            if(empty($_POST['cate'])){
                $this->xtpl->assign("ERROR_TEXT", "Vui lòng chọn một danh mục cần đăng");
            } else {
                $_SESSION["CHOOSE_CATE"] = $_POST['cate'];
                set_cookie("CHOOSE_CATE", $_POST['cate']);
                reload("chi-tiet-tin-dang");return;
            }

        }else{
            $this->xtpl->assign("ERROR_TEXT", "<span>* Chọn chính xác mục cần đăng sẽ giúp bạn mua/bán nhanh hơn rất nhiều.</span>");
        }

        $sql = "SELECT * FROM page_content_cat_v02 ORDER BY ccont_id ASC;";
        $result	= $this->db->query($sql);
        if(!empty($result) && $this->db->numrows($result) >0)
        {
            while($row = $this->db->fetchrow($result))
            {
                $selected = ($cate == $row['ccont_id']) ? 'checked="checked"' : "";
                $this->xtpl->assign("SELECTED", $selected);
                $this->xtpl->assign("CATE_ID", $row['ccont_id']);
                $this->xtpl->assign("CATE_NAME", $row['ccont_name']);
                $this->xtpl->assign("CATE_DESCRIPTION", $row['ccont_description']);
                $this->xtpl->parse("main.post.cate");
            }
        }

        $this->xtpl->parse("main.post");
        $this->show();
    }
    public  function post_2()
    {
        $this->start('post');
        $this->xtpl->parse("main.logo_vertical");

        $this->menu_left();
        $this->check_register_again();

        $cate = @$_SESSION["CHOOSE_CATE"];
        $cate = isset($cate) ? $cate : get_cookie("CHOOSE_CATE");

        if(!empty($_POST['btnBuoc1']) || empty($cate))
        {
            reload("dang-tin");
        }

        $post_phone = "";
        $post_place = "";
        $post_title = "";
        $post_description = "";
        $post_maxlength = $this->config("post_maxlength"); $post_maxlength = empty($post_maxlength) ? 1500 : $post_maxlength;
        $title_maxlength = $this->config("title_maxlength"); $title_maxlength = empty($title_maxlength) ? 1500 : $title_maxlength;
        $image_max_num = $this->config("image_max_num"); $image_max_num = empty($image_max_num) ? 0 : $image_max_num;

        $user = @$_SESSION['USER_PHONE'];
        $user = isset($user) ? $user : get_cookie("USER_PHONE");

        $this->xtpl->assign("POST_HIDDEN_IMG", "");
        if(isset($user))
        {
            $sql = "SELECT * FROM raovat_xemlai WHERE user='$user' ORDER BY datetime DESC LIMIT 1";
            $result	= $this->db->query($sql);
            if(!empty($result) && $this->db->numrows($result) >0)
            {
                while($row = $this->db->fetchrow($result))
                {
                    $post_phone = $row['user'];
                    $post_place = $row['place'];
                    $post_title = $row['title'];
                    $post_description = $row['detail'];
                    $post_description = str_replace('<br />', "\n", $post_description );
                    $this->xtpl->assign("POST_HIDDEN_IMG", $row['image']);
                }
            }
        }

        $this->xtpl->assign("POST_MAX_LENGTH", $this->config('post_maxlength'));
        $this->xtpl->assign("TITLE_MAX_LENGTH", $this->config('title_maxlength'));

        $post_phone = empty($_POST['post_phone']) ? $post_phone : $_POST['post_phone'];
        $this->xtpl->assign("POST_PHONE", $post_phone);

        $post_place = empty($_POST['post_place']) ? $post_place : $_POST['post_place'];

        $post_title = empty($_POST['post_title']) ? $post_title : $_POST['post_title'];
        $this->xtpl->assign("POST_TITLE", $post_title);

        $post_description = empty($_POST['post_description']) ? $post_description : $_POST['post_description'];
        $this->xtpl->assign("POST_DESCRIPTION", $post_description);

        $post_place = intval($post_place);

        $post_phone = strip_tags($post_phone);
        $post_phone = str_replace("'", "\'", $post_phone);

        $post_title = filter_title($post_title);

        $post_description = filter_description($post_description);

        if(isset($_POST['btnHoanTat']))
        {
            $captcha_word = $_SESSION["CAPTCHA_WORD"];


            if(empty($post_phone))
            {
                $this->xtpl->assign("ERROR", "Vui lòng nhập số điện thoại.");
            }
            elseif(empty($post_place) || $this->get_place($post_place) == "")
            {
                $this->xtpl->assign("ERROR", "Vui lòng nhập nơi giao dịch.");
            }
            elseif(empty($post_title) || mb_strlen($post_title) > $title_maxlength)
            {
                $this->xtpl->assign("ERROR", "Vui lòng nhập tiêu đề.");
            }
            elseif(empty($post_description) || mb_strlen($post_description) > $post_maxlength)
            {
                $this->xtpl->assign("ERROR", "Vui lòng nhập nội dung.");
            }
            elseif(empty($_POST['captcha']))
            {
                $this->xtpl->assign("ERROR", "Vui lòng nhập mã xác thực.");
            }
            elseif(strtolower($captcha_word) != strtolower($_POST['captcha']))
            {
                $this->xtpl->assign("ERROR", "Mã xác thực chưa đúng.");
            }
            else
            {
                $cats = $this->config("raovat");
                if($cats && $cate)
                {
                    $cat = $cats[$cate];
                }
                if(empty($cat)) {
                    $this->xtpl->assign("ERROR", "Không tìm thấy danh mục trong hệ thống.");
                    $this->xtpl->parse("main.post02.error");
                } else
                {
                    $table = ($_POST['post_review'] == 'REVIEW')?  $table = "raovat_xemlai" : "raovat_" . str_replace("-", "", $cat["key"]);
                    $save = true;
                    $time = time();
                    $images = array();
                    $comments = array();

                    if(!empty($_POST['post_hidden_img']))
                    {
                        $images = $_POST['post_hidden_img'];
                    }

                    for($i = 1; $i <= $image_max_num; $i++)
                    {
                        if(!empty($_FILES["post_img_" . $i]['name']))
                        {
                            $image = upload_img("post_img_" . $i);

                            if(!empty($image))
                            {
                                $images[] = $image;

                                $comment = 'img_comment_' . $i;
                                $$comment = empty($$comment) ? "" : $$comment;
                                $comment = empty($_POST['img_comment_' . $i]) ? $$comment : $_POST['img_comment_' . $i];
                                $comment = strip_tags($comment);
                                $comment = str_replace("'", "\'", $comment);
                                $comment = str_replace("~", "", $comment);
                                $comments[] = $comment;
                            }
                        }
                    }

                    $images = implode(",", $images);
                    $comments = implode("~", $comments);

                    if($save)
                    {
                        $sql = "INSERT INTO $table (`user`, `mua`, `title`, `image`, `detail`, `place`, `datetime`, `viewcount`) VALUES ('$post_phone', '$cate', '$post_title', '$images', '$post_description', '$post_place', '$time', '1');";
                        $result	= $this->db->query($sql);
                        if(!empty($result))
                        {
                            if($_POST['post_review'] != 'REVIEW') {


                                //Get post ID and add to moinhat
                                $detail_id = 0;

                                $sql = "SELECT * FROM $table WHERE user='$post_phone' AND datetime='$time' LIMIT 1";
                                $result	= $this->db->query($sql);
                                if(!empty($result) && $this->db->numrows($result) >0)
                                {
                                    while($row = $this->db->fetchrow($result))
                                    {
                                        $detail_id = $row['id'];

                                        $sql = "INSERT INTO page_search_v01 (`cate`, `detail`, `title`, `keywords`, `place`, `time`) VALUES ('$cate', '$detail_id', '$post_title', '$post_description', '$post_place', '$time');";
                                        if(!$this->db->query($sql))
                                        {
                                            write_log($sql);
                                        }

                                        $sql = "INSERT INTO raovat_comment (`post`, `table`, `comment`) VALUES ('$detail_id', '$cate', '$comments');";
                                        if(!$this->db->query($sql))
                                        {
                                            write_log($sql);
                                        }

                                        $time_delete = $time - 10*24*60*60;

                                        $sql = "DELETE FROM raovat_xemlai WHERE `datetime` < $time_delete;";
                                        $this->db->query($sql);

                                        $this->update_cate_count();
                                    }
                                }


                                //$_SESSION["REGISTER_AGAIN"] = "$cate~$detail_id~$time";
                                //set_cookie('REGISTER_AGAIN', $_SESSION["REGISTER_AGAIN"]);

                                $this->session->setKey("REGISTER_AGAIN", "$cate~$detail_id~$time", "data");

                                $this->xtpl->assign("LINK_DETAIL", site_url($this->config("page_detail") ."/" . $this->get_cate_key($cate) ."/". $detail_id, "xem chi tiet"));
                                $this->xtpl->assign("LINK_IMAGES", site_url("them-hinh-anh"));

                                $this->xtpl->parse("main.post_completed");

                                $_SESSION["CHOOSE_CATE"] = "";
                                unset($_SESSION["CHOOSE_CATE"]);
                                del_cookie("CHOOSE_CATE");

                                $_SESSION["USER_PHONE"] = "";
                                unset($_SESSION["USER_PHONE"]);
                                del_cookie("USER_PHONE");
                            }else{
                                $_SESSION['USER_PHONE'] = $post_phone;
                                set_cookie('USER_PHONE', $post_phone);
                                reload("xem-lai", 0);
                            }
                        }else{
                            write_log($sql);
                            $this->xtpl->assign("ERROR", "Đăng tin chưa được, vui lòng thử lại.");
                        }
                    }
                }
            }

        }

        $this->xtpl->assign("PLACE_SELECT", $this->get_place_select($post_place));

        for($i = 1; $i <= $image_max_num; $i++)
        {
            $this->xtpl->assign("INDEX", $i);
            $this->xtpl->parse("main.post02.file_upload");
        }

        $this->xtpl->assign("CAPTCHA_REFRESH", site_url("get-captcha"));

        if(empty($detail_id))
        $this->xtpl->parse("main.post02");
        $this->show();
    }
    private  function post_3()
    {
        $output["success"] = false;
        $output["message"] = "";

        if(isset($_POST['token']) && md5($_POST['token']) == "1f9784bb24a0f9625e74b10185b72aec")
        {
            $post_type = "B";
            $post_cate = "20";
            $post_phone = "0909090909";
            $post_place = "2";
            $post_title = "";
            $post_image = "";
            $post_description = "";

            $post_cate = empty($_POST['post_cate']) ? $post_cate : $_POST['post_cate'];
            $post_type = empty($_POST['post_type']) ? $post_type : $_POST['post_type'];
            $post_phone = empty($_POST['post_phone']) ? $post_phone : $_POST['post_phone'];
            $post_place = empty($_POST['post_place']) ? $post_place : $_POST['post_place'];
            $post_title = empty($_POST['post_title']) ? $post_title : $_POST['post_title'];
            $post_image = empty($_POST['post_image']) ? $post_image : $_POST['post_image'];
            $post_description = empty($_POST['post_description']) ? $post_description : $_POST['post_description'];

            $post_place = strip_tags($post_place);
            $post_place = str_replace("'", "\'", $post_place);
            $post_place = str_replace(array("&nbsp;","_"), "", $post_place);
            $post_place = trim($post_place);
            $post_place = $this->get_place_id($post_place);


            $post_phone = strip_tags($post_phone);
            $post_phone = str_replace("'", "\'", $post_phone);
            $post_phone = str_replace(array("&nbsp;","_"), "", $post_phone);
            $post_phone = trim($post_phone);

            $post_title = strip_tags($post_title);
            $post_title = str_replace("'", "\'", $post_title);
            $post_title = str_replace(array("&nbsp;","_"), "", $post_title);
            $post_title = trim($post_title);

            $post_description = strip_tags($post_description, '<br><a>');
            $post_description = str_replace("'", "\'", $post_description);
            $post_description = str_replace("<a", '<a target="_blank"' , $post_description);
            $post_description = str_replace( array("\n","\r","\r\n"), '<br />', $post_description );
            $post_description = str_replace(array('&nbsp;', 'www.',"_"), '', $post_description);
            $post_description = preg_replace('/\s+/', ' ', $post_description);
            $post_description = preg_replace('#<br.*?>#i', '<br />', $post_description);
            $post_description = preg_replace('#<br />(\s*<br />)+#', '<br />', $post_description);

            if(empty($post_phone))
            {
                $output["message"] .= "Vui lòng nhập số điện thoại.<br>";
            }
            elseif(empty($post_place) || $this->get_place($post_place) == "")
            {
                $output["message"] .= "Vui lòng nhập nơi giao dịch.<br>";
            }
            elseif(empty($post_title))
            {
                $output["message"] .= "Vui lòng nhập tiêu đề.<br>";
            }
            elseif(empty($post_description))
            {
                $output["message"] .= "Vui lòng nhập nội dung.<br>";
            }
            else
            {
                if($post_phone == "0909090909")
                {
                    $phone = get_phone_number($post_title . $post_description);
                    $post_phone = empty($phone) ? $post_phone : end($phone);
                }
                $cats = $this->config("raovat");
                if($cats && $post_cate)
                {
                    $cat = $cats[$post_cate];
                }
                if(empty($cat)) {
                    $output["message"] .= "Không tìm thấy danh mục trong hệ thống.<br>";
                } else
                {
                    $save = true;
                    $time = time();
                    $images = array();

                    if(!empty($post_image))
                    {
                        $post_image = urldecode($post_image);
                        $post_image = explode(",", $post_image);

                        $image_max_num = $this->config("image_max_num"); $image_max_num = empty($image_max_num) ? 0 : $image_max_num;

                        $inc = 0;
                        foreach($post_image as $image)
                        {
                            $inc++;
                            if($inc > $image_max_num) break;
                            $image = get_img($image);

                            if(strlen($image) > 3)
                            {
                                $images[] = $image;
                            }
                        }
                    }


                    $images = implode(",", $images);

                    if($save)
                    {
                        $table = "raovat_" . str_replace("-", "", $cat["key"]);
                        $sql = "INSERT INTO $table (`user`, `mua`, `title`, `image`, `detail`, `place`, `datetime`, `viewcount`) VALUES ('$post_phone', '$post_type', '$post_title', '$images', '$post_description', '$post_place', '$time', '1');";
                        $result	= $this->db->query($sql);
                        if(!empty($result))
                        {
                            $sql = "SELECT * FROM $table WHERE user='$post_phone' AND datetime='$time' LIMIT 1";
                            $result	= $this->db->query($sql);
                            if(!empty($result) && $this->db->numrows($result) >0)
                            {
                                while($row = $this->db->fetchrow($result))
                                {
                                    $detail_id = $row['id'];
                                    $detail = $post_cate . "~" . $row['id'];

                                    $sql = "INSERT INTO raovat_moinhat (`user`, `mua`, `title`, `description`, `detail`, `place`, `datetime`) VALUES ('$post_phone', '$post_type', '$post_title', '$post_description', '$detail', '$post_place', '$time');";
                                    if(!$this->db->query($sql))
                                    {
                                        write_log($sql);
                                    }

                                    $time_table = ltrim($table, "raovat_");
                                    $sql = "INSERT INTO raovat_time (`post`, `table`, `time`) VALUES ('$detail_id', '$time_table', '$time');";
                                    if(!$this->db->query($sql))
                                    {
                                        write_log($sql);
                                    }

                                    $time_delete = $time - 10*24*60*60;

                                    $sql = "DELETE FROM raovat_xemlai WHERE `datetime` < $time_delete;";
                                    $this->db->query($sql);

                                    $sql = "DELETE FROM raovat_moinhat WHERE `datetime` < $time_delete;";
                                    $this->db->query($sql);

                                    $this->update_cate_count();
                                }
                            }
                        }else{
                            write_log($sql);
                            $output["message"] .= "Đăng tin chưa được, vui lòng thử lại.<br>";
                        }
                    }
                }
            }

        }else{
            $output["message"] = "Dữ liệu chưa đầy đủ";
        }

        echo json_encode($output);
    }
    public function post_image()
    {
        $this->start('post');
        $this->menu_left();

        $this->xtpl->parse("main.logo_detail");

        $res = $this->session->getKey("REGISTER_AGAIN", "data");

        if(!empty($res))
        {
            list($cate, $detail_id, $time) = explode("~", $res);


            $id = segment(2, false);

            $cats = $this->config("raovat");

            if($cate > 0 && $cats)
            {
                $cat = $cats[$cate];
            }


            if(empty($cat)) {
                $this->other_link();
                $this->xtpl->parse("main.not_found");
                return;
            }

            $table = "raovat_" . str_replace("-", "", $cat["key"]);

            $sql = "SELECT * FROM $table WHERE id='$detail_id' LIMIT 1";
            $result	= $this->db->query($sql);
            if(!empty($result) && $this->db->numrows($result) >0)
            {
                $row = $this->db->fetchrow($result);
                $images = $row['image'];


                $images = explode(",", $images);
                foreach($images as $index => $img)
                {
                    $medium = str_replace(".", "_m.", $img);
                    if(!empty($medium) && is_file(ROOT . "/public/uploads/Content/" . $medium))
                    {
                        $this->xtpl->assign("DETAIL_IMAGE", BASE_URL . "public/uploads/Content/" . $medium);
                        $this->xtpl->assign("LINK_DELETE_IMAGE", site_url("them-hinh-anh/". ($index +1), "Xoa hinh"));

                        $this->xtpl->parse("main.post_images.row");
                    }else
                    if(!empty($img) && is_file(ROOT . "/public/uploads/Content/" . $img))
                    {
                        $this->xtpl->assign("DETAIL_IMAGE", BASE_URL . "public/uploads/Content/" . $img);
                        $this->xtpl->assign("LINK_DELETE_IMAGE", site_url("them-hinh-anh/". ($index +1), "Xoa hinh"));

                        $this->xtpl->parse("main.post_images.row");
                    }
                }

                $image_max_num = $this->config("image_max_num");
                $image_max_num = empty($image_max_num) ? 0 : $image_max_num;

                if($id > 0)
                {
                    $image = @$images[$id - 1];
                    @unlink(ROOT . '/public/uploads/Content/' . $image);


                    unset($images[$id - 1]);
                    $images = implode(",", $images);

                    $sql = "UPDATE  $table SET  `image` =  '$images' WHERE  id = '$detail_id' LIMIT 1";
                    $this->db->query($sql);

                    reload("them-hinh-anh", 0);
                }

                if(count($images) >= $image_max_num)
                {
                    $this->xtpl->assign("ERROR", "Số lượng hình đã đạt mức tối đa: $image_max_num hình.");
                }
                elseif(!empty($_FILES["post_img"]['name']))
                {
                    $image = upload_img("post_img");
                    if(!empty($image))
                    {
                        $images = $row['image'] . "," . $image;
                        $images = trim($images, ",");

                        $sql = "UPDATE  $table SET  `image` =  '$images' WHERE  id = '$detail_id' LIMIT 1";
                        $this->db->query($sql);

                        reload("them-hinh-anh", 0);
                    }
                }

                $this->xtpl->parse("main.post_images");
            }else{
                $this->other_link();
                $this->xtpl->parse("main.not_found");
            }




        }else
        {
            $this->other_link();
            $this->xtpl->parse("main.not_found");
        }

        $this->show();
    }
    public  function review()
    {
        $this->start('detail');
        $this->menu_left();

        $this->xtpl->parse("main.logo_detail");

        $table = "raovat_xemlai";
        $user = @$_SESSION["USER_PHONE"];
        $user = isset($user) ? $user : get_cookie("USER_PHONE");

        $sql = "SELECT * FROM $table WHERE user='$user' ORDER BY datetime DESC LIMIT 1";
        $result	= $this->db->query($sql);
        if(!empty($result) && $this->db->numrows($result) >0)
        {
            while($row = $this->db->fetchrow($result))
            {
                $this->xtpl->assign("DETAIL_TITLE", $row['title']);
                $this->xtpl->assign("DETAIL_CONTENT", $row['detail']);
                $this->xtpl->assign("DETAIL_DATE", date("d/m/Y", $row['datetime']));
                $this->xtpl->assign("DETAIL_COUNT", $row['viewcount']);
                $this->xtpl->assign("DETAIL_PLACE", $this->get_place($row['place']));

                if(!empty($row['image']) && is_file(ROOT . "/public/uploads/Content/" . $row['image']))
                {
                    $this->xtpl->assign("DETAIL_IMAGE", BASE_URL . "public/uploads/Content/" . $row['image']);
                    $this->xtpl->parse("main.detail.detail_info.image");
                }

                $this->site_title =  "Xem lại &raquo; " .$row['title'];
                $this->site_keyword = $row['title'];
                $this->site_description = $row['title'];
                $this->xtpl->parse("main.detail.detail_info");
            }
        }



        $this->xtpl->parse("main.detail.back");
        $this->xtpl->parse("main.detail");
        $this->show();
    }

    private function count_one_cate($table)
    {
        $sql = "SELECT COUNT(*) AS num FROM $table";
        $result	= $this->db->query($sql);
        if(!empty($result) && $this->db->numrows($result) >0)
        {
            while($row = $this->db->fetchrow($result))
            {
                return $row['num'];
            }
        }

        return 0;
    }

    private function update_cate_count()
    {

        $somecontent = '<?php if (!defined("ROOT")) exit("No direct script access allowed");
/***********************************************************************/
/*                                                                     */
/*   Copyright (C) 2015. All rights reserved                           */
/*   Author     : Roca Chien, rocachien@yahoo.com                      */
/*   License    : http://nhanhgon.vn                                   */
/*                                                                     */
/*   Created    : 09-11-2015 15:30:05.                                 */
/*   Modified   : '.date("d-m-Y H:i:s").'.                                 */
/*                                                                     */
/***********************************************************************/

//****************************** Category *****************************//';

        $sql	= "SELECT * FROM page_content_cat_v02 ORDER BY ccont_index ASC";
        $result	= $this->db->query($sql);
        if(!empty($result) && $this->db->numrows($result)>0)
        {
            while($row = $this->db->fetchrow($result))
            {
                $num = 0;
                $table = "raovat_" . str_replace(array("-", " "), "", $row['ccont_key']);

                $sql = "SELECT COUNT(*) AS num FROM $table";
                $result2	= $this->db->query($sql);
                if(!empty($result2) && $this->db->numrows($result2)>0)
                {
                    while($row2 = $this->db->fetchrow($result2))
                    {
                        $num = $row2["num"];
                    }
                }


                $somecontent .='
$config["raovat"]['.$row['ccont_id'].']	= array("key" => "'.$row['ccont_key'].'",    "name" => "'.$row['ccont_name'].'", "count" => '.$num.');';


            }
        }

        $somecontent .= '
?>';

        $filename = ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . "config".DIRECTORY_SEPARATOR."category".EXT;
        if (is_writable($filename))
        {
            if (!$handle = fopen($filename, 'w')) {
                echo "Cannot open file ($filename)";
                exit;
            }
            if (fwrite($handle, $somecontent) === FALSE) {
                echo "Cannot write to file ($filename)";
                exit;
            }

            fclose($handle);
            clear_cache();

        } else {
            echo "The file $filename is not writable";
        }
    }

    private function report()
    {
        $this->start('index');

        $this->xtpl->parse("main.detail");
        $this->show();
    }
    private function other_link($search=false)
    {
        $url = get_url();
        $url = trim($url, "/");
        $url = explode("/", $url);

        $keys = array();
        foreach($url as $val)
        {
            if(strlen($val) < 3) continue;
            $val = str_replace("-", " ", $val);
            $val = str_replace(array(".", "html", "raovat", "rao vat", "danh muc"), "", $val);
            if(strpos($val, " ") === false) continue;

            $keys[] = $val;
        }

        if(count($keys) > 0)
        {
            $links = "<li>Hoặc tham khảo link sau: </li><ul class='other_link'>";
            $burl = rtrim(BASE_URL, "/");

            foreach($keys as $key)
            {
                $kus = str_replace(" ", "+", $key);
                $links .= "<li><a href='$burl/search.html?q=$kus'>$key</a>";

                $key = explode(" ", $key);
                foreach($key as $word)
                {
                    $links .= " | <a href='$burl/search.html?q=$word'>$word</a>";

                    $_GET['q'] = $word;
                }
                $links .= "</li>";
            }


            $links .= "</ul>";

            if(!$search) $this->xtpl->assign("OTHER_LINK", $links);
            else $this->search();
        }
    }
}

?>