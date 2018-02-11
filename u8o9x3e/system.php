<?php if (!defined('ROOT')) exit('No direct script access allowed');
/***********************************************************************/
/*                                                                     */
/*   Copyright (C) 2015. All rights reserved                           */
/*   Author     : Roca Chien, rocachien@yahoo.com                      */

/*                                                                     */
/*   Created    : 15-09-2015 15:13:05.                                 */
/*   Modified   : 15-09-2015 15:13:05.                                 */
/*                                                                     */
/***********************************************************************/

mb_internal_encoding('UTF-8');

function mang($str)
{
	echo "<pre>";
	print_r($str);
	echo "</pre>";
}
function &get_config()
{
	static $main_conf;
	if ( ! isset($main_conf))
	{
		require_once(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . "config".DIRECTORY_SEPARATOR."config".EXT);
		require_once(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . "config".DIRECTORY_SEPARATOR."database".EXT);
		require_once(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . "config".DIRECTORY_SEPARATOR."category".EXT);
		require_once(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . "config".DIRECTORY_SEPARATOR."mapping".EXT);
		if ( ! isset($config) OR ! is_array($config))
		{
			echo('Your config file does not appear to be formatted correctly.');
		}
		$main_conf[0] =& $config;
	}
	
	return $main_conf[0];
}
function get_current_lang()
{
	$lang = get_cookie("LANGUAGE");
	$lang = empty($lang)?"vn":$lang;
	return $lang;
}
function &get_lang()
{
	static $main_lang;
	if ( ! isset($main_lang))
	{
		$lang = get_current_lang();
		require(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . "config".DIRECTORY_SEPARATOR."lang_".$lang.EXT);
		if ( ! isset($language) OR ! is_array($language))
		{
			echo('Your config file does not appear to be formatted correctly.');
		}
		$main_lang[0] =& $language;
	}

	return $main_lang[0];
}
function cut_char($str, $numchar=0, $end="")
{
	if(strlen($str)<$numchar) return $str;
	$str    = substr($str,0,$numchar);
	$str    = substr($str,0,strrpos($str," "));
	return $str.$end;
}
function url_validate($str) 
{
	$search = array (
	'#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
	'#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
	'#(ì|í|ị|ỉ|ĩ)#',
	'#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
	'#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
	'#(ỳ|ý|ỵ|ỷ|ỹ)#',
	'#(đ)#',
	'#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
	'#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
	'#(Ì|Í|Ị|Ỉ|Ĩ)#',
	'#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
	'#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
	'#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
	'#(Đ)#' ,
	'#("|\'|\.|,|;|\&|/|\?|%|\\\|:|=|\!|_|\-|–|\[|\]|\+|\#)#' ,
	'/\s+/'
	);
	$replace = array ('a','e','i','o','u','y','d','A','E','I','O','U','Y','D'," ","-");
	$str = preg_replace($search, $replace, $str);
    $str = trim($str, "-");
    $str = mb_strtolower($str, 'UTF-8');
    $str = mb_ucfirst($str);
	return $str;
}
function remove_bad($str)
{
    $search = array (
        '#("|\'|\.|,|\&|/|\?|%|\\\|:|=|\!|_|\-|–|\[|\]|\+)#' ,
        '/\s+/'
    );
    $str = preg_replace($search, array (" ","-"), $str);
    $str = trim($str, "-");
    return $str;
}
function valid_email($address)
{
	return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? FALSE : TRUE;
}
function site_url($uri = '', $text='')
{
    $text	= url_validate($text);
    $config =& get_config();

    if ($uri == '')
	{
		return BASE_URL.$config['sys_run_file'];
	}
	else
	{
		$suffix = empty($config['sys_extensions']) ? '' : $config['sys_extensions'];
        $base_url = empty($config['sys_run_file']) ? BASE_URL : BASE_URL . $config['sys_run_file']  . "/";

		if(empty($text)) return $base_url . $uri.$suffix;
		return $base_url . $uri."/".$text.$suffix;
	}
}
function base_url()
{	
	return BASE_URL;
}
function get_url()
{
//	if (is_array($_GET) && count($_GET) == 1 && trim(key($_GET), '/') != '')
//	{
//		return key($_GET);
//	}
	$path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
	if (trim($path, '/') != '' && $path != "/".SELF)
	{
		return $path;
	}

	$path =  (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');
	if (trim($path, '/') != '')
	{
		return $path;
	}

	$path = (isset($_SERVER['ORIG_PATH_INFO'])) ? $_SERVER['ORIG_PATH_INFO'] : @getenv('ORIG_PATH_INFO');
	if (trim($path, '/') != '' && $path != "/".SELF)
	{
		return $path;
	}
	return '';
	
}
function segment($index=1, $string = true)
{
	$url = get_url();
    if(empty($url)) return false;

    if(strpos($url, "?") !== false) {
        $url = explode("?", $url);
        $url = $url[0];
    }

    $config =& get_config();

    if (strpos($url, $config['sys_extensions']) === false) {
        if(!file_exists($url)) {
            write_log("HTTP/1.0 404 Not Found --> " . $url);
            header("HTTP/1.0 404 Not Found");
            die ();
        }
    }

    $url = trim($url, ".");
    $url = trim($url, "/");
    $url = substr($url, 0, -strlen($config['sys_extensions']));
    $uri = strpos($url, ".") ? explode(".", $url) : explode("/", $url);

    // If not extension, return false
    if(count($uri) < 1) return false;

    $index = ($index <= 0) ? 1 : $index -1;
	if(empty($uri[$index])) return false;
	if($string)
		return $uri[$index];
	else
		return intval($uri[$index]);
}
function getUserIP()
{
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        }
        return $_SERVER["REMOTE_ADDR"];
    }
    if (getenv('HTTP_X_FORWARDED_FOR')) {
        return getenv('HTTP_X_FORWARDED_FOR');
    }
    if (getenv('HTTP_CLIENT_IP')) {
        return getenv('HTTP_CLIENT_IP');
    }
    return getenv('REMOTE_ADDR');
}
function get_cookie($name)
{
	$name = md5($name);
	if(isset($_COOKIE[$name])) {
		return decode($_COOKIE[$name]);	
	}else return "";
}
function get_phone_number($txt) {
    if(empty($txt)) return array ("0909090909");

    $txt = str_replace(array(".", "_", " "), "", $txt);
    $regexp = '/((\+*)((0[ -]+)*|(91)*)(\d{11}+|\d{10}+))|\d{5}([- ]*)\d{6}/';

    preg_match_all ($regexp, $txt, $m);

    return (count($m[0]) > 0)? $m[0]: array ("0909090909");
}
function set_cookie($name, $value)
{//	Set session key to cookies
	$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
	//set a new cookie
	$name = md5($name);
	$value = encode($value);
	if(setcookie($name, $value, 60*60*24*365 + time(), '/', $domain, 0))
	{
		return true ;
	}	
	return false ;
}
function del_cookie($name)
{
	$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
	$name = md5($name);
	if(setcookie($name, "", mktime(12, 0, 0, 1, 1, 1990), "/", $domain , 0))
		return TRUE;
	return FALSE;
}
function encode($str)
{
	return chr(rand(65,90)).base64_encode(HASH_KEY.$str).chr(rand(97,122));
}
function decode($str)
{
	$str = substr($str,0,-1);$str = substr($str,1);
	return substr(base64_decode($str), strlen(HASH_KEY));
}
function strip_html_tags( $text )
{
    $text = preg_replace(
        array(
            // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text );
    return strip_tags( $text );
}
function alert($msg, $title="C.O.I Corporation Alert")
{//
	echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><title>"
		.$title."</title><script>alert(\"".$msg."\");</script></head><body></body></html>";
}
function reload($uri = '', $text ='', $time = 0, $exit = false)
{
	$uri	= (substr($uri, 0, 4) == "http")?$uri:site_url($uri, $text);
	if (!headers_sent())
		header("Refresh: $time; url=$uri");
	else {
		echo '<script type="text/javascript">';
		echo 'window.location.href="'.$uri.'";';
		echo '</script>';
		echo '<noscript>';
		echo '<meta http-equiv="refresh" content="'.$time.';url='.$uri.'" />';
		echo '</noscript>';
	}
	if($exit)
			exit;
}
function referrer()
{
	return ( ! isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? '' : trim($_SERVER['HTTP_REFERER']);
}
function showIMG($url, $w=90, $h=90, $url2 ="", $title="", $auto="", $class="", $boder=0)
{
	$auto=($auto)?"true":"false";
	
	$img ="";
	$url=empty($url2)?$url:$url2;
	if(strpos($url, "ttp://") === false)
		$url	= BASE_URL .$url;
	$ex	= substr($url,-3,3);
	
	$extimg="jpg,jpe,peg,fif,png,gif,bmp,ico";
	if(stristr($extimg, $ex))
	{
		$h=empty($h) ? "" : ' height="'.$h.'"';
		$w=empty($w) ? "" : ' width="'.$w.'"';
		$img='<img src="'.$url.'" '.$w. $h .' border="'.$boder.'" title="'.$title.'" class="'.$class.'" />';
	}
	$extvideo="wav,mid,mp3";
	if(stristr($extvideo, $ex))
	{
		$img='<embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/" src="'.$url.'" ShowStatusBar="0" AutoStart="'.$auto.'" ShowControls="1" loop="false" width='.$w.' height='.$h.' style="width:'.$w.'px; height:'.$h.'px;"></embed>';
		return $img;
	}	
	$extvideo="wmv,dat";
	if(stristr($extvideo, $ex))
	{	
		if(user_browser() == "firefox"){
			$img='<object classid="clsid:166B1BCA-3F9C-11CF-8075-444553540000" '
			.'codebase="http://download.macromedia.com/pub/shockwave/cabs/director/sw.cab#'
			.'version=8,5,0,0" width="'.$w.'" height="'.$h.'" accesskey="a" tabindex="0" title="'
			.$title.'" z-index="0" wmode="transparent" >'
			.'<param name="src" value="'.$url.'" />'
			.'<PARAM NAME="AutoStart" VALUE="'.$auto.'">'
			.'<embed src="'.$url.'" pluginspage="http://www.macromedia.com/shockwave/download/" '
			.'width="'.$w.'" height="'.$h.'"  AutoStart="'.$auto.'" ></embed></object>';
		}else{
			$name = basename($url);
			$img='<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000 '
			.'codebase="http://download.macromedia.com/pub/shockwave/'
			.'cabs/flash/swflash.cab#version=6,0,40,0" z-index="0" wmode="transparent" '
			.'WIDTH="'.$w.'" HEIGHT="'.$h.'" id="myFlash">'
			.'<PARAM NAME=movie VALUE="'. $url.'">'
			.'<PARAM NAME=quality VALUE=high>'
			.'<PARAM NAME=bgcolor VALUE=#FFFFFF>'
			.'<PARAM NAME="AutoStart" VALUE="'.$auto.'">'
			.'<EMBED src="'. $url.'" quality=high bgcolor=#FFFFFF z-index="0" wmode="transparent" '
			.'WIDTH="'.$w.'" HEIGHT="'.$h.'" NAME="myFlash" ALIGN="" AutoStart="'.$auto.'" '
			.'TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/'
			.'go/getflashplayer"></EMBED></OBJECT>';
		}
	}
	if($ex=="swf")
	{
		$img='<embed src="'.$url.'" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="'.$w.'" height="'.$h.'"  wmode="transparent" z-index="0"></embed>';
	}
	//file flash movie
	if($ex=="flv")
	{
		$img='<embed src="'.base_url().'flvplayer.swf" type="application/x-shockwave-flash" flashvars="file='
		. $url.'&image=waitting.gif;location=flvplayer.swf&autostart='.$auto.'" '
		.'allowfullscreen="true" height="'.$h.'" width="'.$w.'" z-index="0" wmode="transparent" ></embed>';		
	}
	//file xml to silde show
	if($ex=="xml")
	{
		$img='<embed height="'.$h.'" width="'.$w.'" flashvars="file='.base_url().$url.'&amp;transition=fade&amp;shuffle=false&amp;shownavigation=true&amp;width='.$w.'&amp;height='.$h.'&amp;linkfromdisplay=true" wmode="transparent" quality="high" name="rotator" id="rotator" style="" src="'.base_url().'imagerotator.swf" type="application/x-shockwave-flash">';
	}
	return $img;
}
function show_num($num, $lang="vn")
{
	if(empty($num)) return "";
	switch($lang)
	{
		case "vn": return number_format($num, 0, ".", "."); break;
		case "en": return number_format($num, 2, ",", "."); break;
	}
}
function show_money($num, $lang='vn')
{
	if(empty($num)) return "";
	switch($lang)
	{
		case "vn": return show_num($num, $lang) . "<sup>đ</sup>"; break;
		case "en": return "\$" . show_num($num, $lang); break;
	}
}
function show_date($num, $lang="vn")
{
	if(empty($num)) return "";
	switch($lang)
	{
		case "vn": return date("d.m.Y", $num); break;
		case "en": return date("m.d.Y", $num); break;
	}
}
function upload_img($name, $folder='public/uploads/Content/')
{
    $folder = ROOT ."/" . $folder;
    if(empty($name)) return "";
    //
    if(!empty($_FILES[$name]['name'])){
        $path	= $_FILES[$name]['name'];
        $temp	= $_FILES[$name]['tmp_name'];
        //
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        $file_name	= md5_file($temp).".".$ext;
        $sub_folder = "";

        $new_folder = date("M") . "/";
        $sub_folder .= $new_folder;
        if(file_exists($folder . $sub_folder)==false){
            mkdir($folder . $sub_folder);
        }

        $new_folder = substr($file_name, 0, 2) . "/";
        $sub_folder .= $new_folder;
        if(file_exists($folder . $sub_folder)==false){
            mkdir($folder . $sub_folder);
        }

        $new_folder = substr($file_name, 2, 2) . "/";
        $sub_folder .= $new_folder;
        if(file_exists($folder . $sub_folder)==false){
            mkdir($folder . $sub_folder);
        }

        $new_folder = substr($file_name, 4, 2) . "/";
        $sub_folder .= $new_folder;
        if(file_exists($folder . $sub_folder)==false){
            mkdir($folder . $sub_folder);
        }

        $file_name = $sub_folder . substr($file_name, 6);
        $file = $folder  . $file_name;

        //
        if(substr($ext, -3, 3) == "php") return "";

        @chmod($folder . $sub_folder, 0777);
        if(substr($ext, -3, 3) == "gif")
        {
            $im = @imagecreatefromgif($temp);

            if(imagejpeg($im, $file)){
                @unlink($temp);

                if(resize_image($file))
                {
                    @chmod($folder . $sub_folder, 0755);
                    return $file_name;
                }
            }
        }else{
            if(move_uploaded_file($temp, $file)){
                @unlink($temp);

                if(resize_image($file))
                {
                    @chmod($folder . $sub_folder, 0755);
                    return $file_name;
                }
            }
        }
    }
    return "";
}
function get_img($url, $crop_top = 0, $crop_bottom = 0, $folder='public/uploads/Content/')
{
    if(empty($url)) return "";
    if(@getimagesize($url) == false) return "";
    //
    $folder = ROOT ."/" . $folder;
    $temp_folder = $folder . 'temp/';

    if(!file_exists($temp_folder)) @mkdir($temp_folder);


    $ext = explode("?", $url);
    $ext = $ext[0];
    $ext = explode(".", $ext);
    $ext = end($ext);
    $ext = strtolower($ext);


    $temp_file = $temp_folder . time() . rand(10000000000, 99999999999) ."." . $ext;
    $img = file_get_contents($url);

    if($img)
    {
        if(file_put_contents($temp_file, $img))
        {
            $file_name	= md5_file($temp_file).".".$ext;
            $sub_folder = "";

            $new_folder = date("M") . "/";
            $sub_folder .= $new_folder;
            if(file_exists($folder . $sub_folder)==false){
                mkdir($folder . $sub_folder);
            }

            $new_folder = substr($file_name, 0, 2) . "/";
            $sub_folder .= $new_folder;
            if(file_exists($folder . $sub_folder)==false){
                mkdir($folder . $sub_folder);
            }

            $new_folder = substr($file_name, 2, 2) . "/";
            $sub_folder .= $new_folder;
            if(file_exists($folder . $sub_folder)==false){
                mkdir($folder . $sub_folder);
            }

            $new_folder = substr($file_name, 4, 2) . "/";
            $sub_folder .= $new_folder;
            if(file_exists($folder . $sub_folder)==false){
                mkdir($folder . $sub_folder);
            }

            $file_name = $sub_folder . substr($file_name, 6);
            $file = $folder  . $file_name;

            //
            if(substr($ext, -3, 3) == "php") return "";

            @chmod($folder . $sub_folder, 0777);
            if(substr($ext, -3, 3) == "gif")
            {
                $im = @imagecreatefromgif($temp_file);

                if(imagejpeg($im, $file)){
                    @unlink($temp_file);

                    if(resize_image($file, $crop_top, $crop_bottom))
                    {
                        @chmod($folder . $sub_folder, 0755);
                        return $file_name;
                    }
                }
            }else{
                if(copy($temp_file, $file)){
                    @unlink($temp_file);

                    if(resize_image($file, $crop_top, $crop_bottom))
                    {
                        @chmod($folder . $sub_folder, 0755);
                        return $file_name;
                    }
                }
            }
        }
    }

    return "";
}
function str_replace_last($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}
function resize_image($file, $crop_top=0, $crop_bottom=0, $w=800, $h=500)
{


    $image = new SimpleImage();

    //Resize for small image
    $image->load($file);
    $current_tl = $w/$h;
    $width = $image->getWidth();
    $height = $image->getHeight();
    $ratio = $width/$height;
    $image->crop_top = $crop_top;
    $image->crop_bottom = $crop_bottom;

    if($width < 300 || $height < 200) return false;

    if($ratio > $current_tl)
        $image->resizeToWidth($w/4);
    else
        $image->resizeToHeight($h/4);

    $thumb_image = str_replace_last(".", "_s.", $file);
    $image->save($thumb_image);

    //Resize for medium image
    $image->load($file);
    $current_tl = $w/$h;
    $width = $image->getWidth();
    $height = $image->getHeight();
    $ratio = $width/$height;

    if($ratio > $current_tl)
        $image->resizeToWidth($w);
    else
        $image->resizeToHeight($h);

    $image->signature();
    $thumb_image = str_replace_last(".", "_m.", $file);
    $image->save($thumb_image);

    //Save origin image
    $image->load($file);
    $current_tl = $w/$h;
    $width = $image->getWidth();
    $height = $image->getHeight();
    $ratio = $width/$height;

    if($width > 1200 || $height > 800)
    {
        if($ratio > $current_tl)
            $image->resizeToWidth(1200);
        else
            $image->resizeToHeight(800);
    }

    $image->signature();
    $image->save($file);
    return true;
}
function mb_ucfirst($string, $encoding='UTF-8')
{
    $firstChar = mb_substr($string, 0, 1, $encoding);
    $then = mb_substr($string, 1, mb_strlen($string, $encoding)-1, $encoding);
    return mb_strtoupper($firstChar, $encoding) . $then;
}
function filter_title($post_title)
{
    $post_title = strip_tags($post_title);
    $post_title = str_replace("'", "\'", $post_title);
    $post_title = str_replace(array("&nbsp;","_","#","/","?"), "", $post_title);
    $post_title = trim($post_title);
    return $post_title;
}
function filter_description($post_description)
{
    $post_description = strip_tags($post_description, '<br><a>');

    $post_description = str_replace("'", "\'", $post_description);
    $post_description = str_replace("<a", '<a target="_blank"' , $post_description);
    $post_description = str_replace( array("\n","\r","\r\n"), '<br />', $post_description );
    $post_description = str_replace(array('&nbsp;', 'www.',"_"), '', $post_description);

    $post_description = preg_replace('/\s+/', ' ', $post_description);
    $post_description = preg_replace('#<br.*?>#i', '<br />', $post_description);
    $post_description = preg_replace('#<br />(\s*<br />)+#', '<br />', $post_description);

    return $post_description;
}
function create_captcha($data = '', $img_path = '', $img_url = '')
{
	$defaults = array(
        'word' => '',
        'img_path' => ROOT . DIRECTORY_SEPARATOR . 'public/captcha/',
        'img_url' => BASE_URL . 'public/captcha/',
        'img_width' => '330',
        'img_height' => '45',
        'font_path' => ROOT . DIRECTORY_SEPARATOR .WEB_PREFIX . DIRECTORY_SEPARATOR . 'config/Heineken.ttf',
        'expiration' => 7200);

	foreach ($defaults as $key => $val)
	{
		if ( ! is_array($data))
		{
			if ( ! isset($$key) OR $$key == '')
			{
				$$key = $val;
			}
		}
		else
		{
			$$key = ( ! isset($data[$key])) ? $val : $data[$key];
		}
	}

	if ($img_path == '' OR $img_url == '')
	{
		return FALSE;
	}

	if ( ! @is_dir($img_path))
	{
		return FALSE;
	}

	if ( ! is_writable($img_path))
	{
		return FALSE;
	}

	if ( ! extension_loaded('gd'))
	{
		return FALSE;
	}

	// -----------------------------------
	// Remove old images
	// -----------------------------------

	list($usec, $sec) = explode(" ", microtime());
	$now = ((float)$usec + (float)$sec);

	$current_dir = @opendir($img_path);

	while ($filename = @readdir($current_dir))
	{
		if ($filename != "." and $filename != ".." and $filename != "index.html")
		{
			$name = str_replace(".jpg", "", $filename);

			if (($name + $expiration) < $now)
			{
				@unlink($img_path.$filename);
			}
		}
	}

	@closedir($current_dir);

	// -----------------------------------
	// Do we have a "word" yet?
	// -----------------------------------
	$num_word = 4;
	if ($word == '')
	{
		$pool = 'ABCDEFGHIJKLMNOPQSTUVWXYZ';
		
		$str = '';
		for ($i = 0; $i < $num_word; $i++)
		{
			$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
		}
		
		$word = $str;
	}

	// -----------------------------------
	// Determine angle and position
	// -----------------------------------

	$length	= strlen($word);
	$angle	= ($length >= $num_word) ? rand(-($length-$num_word), ($length-$num_word)) : 0;
	$x_axis	= rand($num_word, (360/$length)-16);
	$y_axis = ($angle >= 0 ) ? rand($img_height, $img_width) : rand($num_word, $img_height);

	// -----------------------------------
	// Create image
	// -----------------------------------

	// PHP.net recommends imagecreatetruecolor(), but it isn't always available
	if (function_exists('imagecreatetruecolor'))
	{
		$im = imagecreatetruecolor($img_width, $img_height);
	}
	else
	{
		$im = imagecreate($img_width, $img_height);
	}

	// -----------------------------------
	//  Assign colors
	// -----------------------------------

	$bg_color		= imagecolorallocate ($im, 255, 255, 255);
	$border_color	= imagecolorallocate ($im, 52, 139, 138);
	$text_color		= imagecolorallocate ($im, 204, 153, 153);
	$grid_color		= imagecolorallocate($im, 255, 182, 182);
	$shadow_color	= imagecolorallocate($im, 255, 240, 240);

	// -----------------------------------
	//  Create the rectangle
	// -----------------------------------

	ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $bg_color);

	// -----------------------------------
	//  Create the spiral pattern
	// -----------------------------------

	$theta		= 1;
	$thetac		= 7;
	$radius		= 16;
	$circles	= 20;
	$points		= 32;

	for ($i = 0; $i < ($circles * $points) - 1; $i++)
	{
		$theta = $theta + $thetac;
		$rad = $radius * ($i / $points );
		$x = ($rad * cos($theta)) + $x_axis;
		$y = ($rad * sin($theta)) + $y_axis;
		$theta = $theta + $thetac;
		$rad1 = $radius * (($i + 1) / $points);
		$x1 = ($rad1 * cos($theta)) + $x_axis;
		$y1 = ($rad1 * sin($theta )) + $y_axis;
		imageline($im, $x, $y, $x1, $y1, $grid_color);
		$theta = $theta - $thetac;
	}

	// -----------------------------------
	//  Write the text
	// -----------------------------------

	$use_font = ($font_path != '' AND file_exists($font_path) AND function_exists('imagettftext')) ? TRUE : FALSE;

	if ($use_font == FALSE)
	{
		$font_size = 5;
		$x = rand(0, $img_width/($length/3));
		$y = 0;
	}
	else
	{
		$font_size	= 20;
		$x = rand(0, $img_width/($length/1.5));
		$y = $font_size+2;
	}

	for ($i = 0; $i < strlen($word); $i++)
	{
		if ($use_font == FALSE)
		{
			$y = rand(0 , $img_height/2);
			imagestring($im, $font_size, $x, $y, substr($word, $i, 1), $text_color);
			$x += ($font_size*2);
		}
		else
		{
			$y = rand($img_height/2, $img_height-3);
			imagettftext($im, $font_size, $angle, $x, $y, $text_color, $font_path, substr($word, $i, 1));
			$x += ($font_size*3);
		}
	}


	// -----------------------------------
	//  Create the border
	// -----------------------------------

	imagerectangle($im, 0, 0, $img_width-1, $img_height-1, $border_color);

	// -----------------------------------
	//  Generate the image
	// -----------------------------------

	$img_name = $now.'.jpg';

	ImageJPEG($im, $img_path.$img_name);

	$img = "<img align=\"absmiddle\" src=\"$img_url$img_name\" width=\"$img_width\" height=\"$img_height\" style=\"border:0;\" alt=\" \" />";

	ImageDestroy($im);

	return array('word' => $word, 'time' => $now, 'image' => $img);
}
function text_to_image($text = '')
{

    $font_size = 5;
    $img_width = 183;
    $img_height = 50;
    $x = $img_width / 2 - (strlen($text) *$font_size);
    $y = $img_height / 2 - $font_size - 1;
    $expiration = 2;//2d
    $word = empty($text) ? "NA" : $text;
    $img_path = ROOT . DIRECTORY_SEPARATOR . 'public/phones/';
    $img_url = BASE_URL . 'public/phones/';

    if ( ! @is_dir($img_path))
    {
        return FALSE;
    }

    if ( ! is_writable($img_path))
    {
        return FALSE;
    }

    if ( ! extension_loaded('gd'))
    {
        return FALSE;
    }

    // -----------------------------------
    // Remove old images
    // -----------------------------------

    $now = date("Ymd");//20151230

    $current_dir = @opendir($img_path);

    while ($filename = @readdir($current_dir))
    {
        if ($filename != "." and $filename != ".." and $filename != "index.html")
        {
            $name = str_replace(".jpg", "", $filename);
            $name = explode(".", $name);
            $time = intval($name[1]);

            if (($time + $expiration) < $now)
            {
                @unlink($img_path.$filename);
            }
        }
    }

    @closedir($current_dir);

    // PHP.net recommends imagecreatetruecolor(), but it isn't always available
    if (function_exists('imagecreatetruecolor'))
    {
        $im = imagecreatetruecolor($img_width, $img_height);
    }
    else
    {
        $im = imagecreate($img_width, $img_height);
    }

    // -----------------------------------
    //  Assign colors
    // -----------------------------------

    $bg_color		= imagecolorallocate ($im, 245, 169, 31);
    $border_color	= imagecolorallocate ($im, 245, 169, 31);
    $text_color		= imagecolorallocate ($im, 255, 255, 255);

    // -----------------------------------
    //  Create the rectangle
    // -----------------------------------

    ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $bg_color);

    // -----------------------------------
    //  Write the text
    // -----------------------------------
    for ($i = 0; $i < strlen($word); $i++)
    {
        imagestring($im, $font_size, $x, $y, substr($word, $i, 1), $text_color);
        $x += ($font_size*2);
    }



    // -----------------------------------
    //  Create the border
    // -----------------------------------

    imagerectangle($im, 0, 0, $img_width-1, $img_height-1, $border_color);

    // -----------------------------------
    //  Generate the image
    // -----------------------------------

    $img_name = $word.".". $now . '.jpg';

    ImageJPEG($im, $img_path.$img_name);
    ImageDestroy($im);

    return $img_url . $img_name;
}
function write_log($msg, $file="")
{
	if(empty($file))
		$filepath = ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . 'logs/log-'.date('Y-m-d').EXT;
	else
		$filepath = $file;
	$message  = '';

	if ( ! file_exists($filepath))
	{
		$message .= "<"."?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
	}

	@chmod($filepath, 0777);
	
	if ( ! $fp = @fopen($filepath, "ab"))
	{
		return FALSE;
	}

	$message .= date('Y-m-d; H:i:s'). ' --> '.$msg."\n";
	
	flock($fp, LOCK_EX);
	fwrite($fp, $message);
	flock($fp, LOCK_UN);
	fclose($fp);
	@chmod($filepath, 0666);
	return TRUE;
}
function clear_cache()
{
    $cache_dir = CACHE_DIR ."/";
    $handle		= @opendir($cache_dir);
    while ($file = @readdir($handle))
        if (strpos($file, ".") && $file != "index.html")
        {
            @unlink($cache_dir . $file);
        }
}
function save_log($post_data, $url = "admin/logs" )
{
    if(defined("DEBUG"))
    {
        echo "<br>----------------------------- ERROR ----------------------------------";
        mang($post_data);
        return false;
    }

    $url = BASE_URL . $url . EXT;
    $result = post_request($url, $post_data);
    if ($result['status'] == 'ok' && empty($result['error']))
    {
        //print_r($result);
        return true;
    }

    write_log($result['error']);

    return false;
}
function send_email($user, $from, $to, $sub, $content)
{
	/*
	$config['protocol'] 	= 'mail';
	$config['smtp_host']	= "mail.dinnermart.com.vn";
	$config['smtp_user']	= "info@dinnermart.com.vn";
	$config['smtp_pass']	= "adSd1huf7DihJ8d@asfk0jKasMdk2";
	$config['smtp_port']	= "25";
	$config['smtp_timeout']	= "5";
	*/
	
	$config['mailtype'] = 'html';
	$config['charset']	= 'utf-8';
	$config['priority'] = 2;
	
	$email = new Email($config);	
	//
	$email->clear();
	$email->to($to);//one@example.com, two@example.com, three@example.com
	$email->from($from, $user);
	$email->subject($sub);
	$email->message($content);
	$email->send();
	return $email->print_debugger();
}
if (!function_exists('mb_str_replace'))
{
    function mb_str_replace($search, $replace, $subject, &$count = 0)
    {
        if (!is_array($subject))
        {
            $searches = is_array($search) ? array_values($search) : array($search);
            $replacements = is_array($replace) ? array_values($replace) : array($replace);
            $replacements = array_pad($replacements, count($searches), '');
            foreach ($searches as $key => $search)
            {
                $parts = mb_split(preg_quote($search), $subject);
                $count += count($parts) - 1;
                $subject = implode($replacements[$key], $parts);
            }
        }
        else
        {
            foreach ($subject as $key => $value)
            {
                $subject[$key] = mb_str_replace($search, $replace, $value, $count);
            }
        }
        return $subject;
    }
}
function search_highlight($text, $keys, $num = 5)
{
    if(!is_array($keys) OR count($keys) < 1) return $text;

//    $text_temp = mb_strtolower($text);

    $out = "<div class='highlight'>";
//    $highlight = array();

    foreach($keys as $key)
    {
//        $key = mb_strtolower($key);
//        $len = mb_strlen($key);
//        $pos['position'] = search_key($text_temp, $key);
//        $pos['key'] = $key;
//        $pos['len'] = $len;
//        $highlight[] = $pos;

        $text = mb_str_replace($key, "<span>$key</span>", $text);
        $text = mb_str_replace(mb_strtoupper($key), "<span>".mb_strtoupper($key)."</span>", $text);
        $text = mb_str_replace(mb_ucfirst($key), "<span>".mb_ucfirst($key)."</span>", $text);
//        foreach($pos['position'] as $p)
//        {
//            $highlight_text .= mb_substr($text, 0, $p);
//            $highlight_text .= "<span>$key</span>";
//            $highlight_text .= mb_substr($text, $p + $len);
//            echo $highlight_text . "<br>==================";
//        }
//        $text = $highlight_text;
    }

//    echo "<pre>" . print_r($highlight) . "</pre>";
//    echo "<br>==================================";
    $out .= $text;
    $out .= "</div>";
    return $out;
}
function search_key($text, $key)
{
    $key = mb_strtolower($key);
//    echo $text . "<br>";
//    $key_pos = mb_strpos($text, $key); if($key_pos ===false) return;
//    $key_len = mb_strlen($key);
////
//    $pos[] = $key_pos;
//    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

    $lastPos = 0;
    $pos = array();
    while (($lastPos = mb_strpos($text, $key, $lastPos))!== false) {
        $pos[] = $lastPos;
        $lastPos = $lastPos + mb_strlen($key);
    }

    return $pos;
}
if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( ! isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( ! isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}
function url_exists($url){
    if (@fclose(@fopen( $url,  "r"))) return true;
    return false;
}
function post_request($url, $data, $referer='') {

    // Convert the data array into URL Parameters like a=b&foo=bar etc.
    $data = http_build_query($data);

    // parse the given URL
    $url = parse_url($url);

    if ($url['scheme'] != 'http') {
        die('Error: Only HTTP request are supported !');
    }

    // extract host and path:
    $host = $url['host'];
    $path = $url['path'];

    // open a socket connection on port 80 - timeout: 30 sec
    $fp = fsockopen($host, 80, $errno, $errstr, 30);

    if ($fp){

        // send the request headers:
        fputs($fp, "POST $path HTTP/1.1\r\n");
        fputs($fp, "Host: $host\r\n");

        if ($referer != '')
            fputs($fp, "Referer: $referer\r\n");

        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: ". strlen($data) ."\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $data);

        $result = '';
        while(!feof($fp)) {
            // receive the results of the request
            $result .= fgets($fp, 128);
        }
    }
    else {
        return array(
            'status' => 'err',
            'error' => "$errstr ($errno)"
        );
    }

    // close the socket connection:
    fclose($fp);

    // split the result header from the content
    $result = explode("\r\n\r\n", $result, 2);

    $header = isset($result[0]) ? $result[0] : '';
    $content = isset($result[1]) ? $result[1] : '';

    // return as structured array:
    return array(
        'status' => 'ok',
        'header' => $header,
        'content' => $content
    );
}
class SimpleImage {

    var $image;
    var $image_type;
    var $crop_top;
    var $crop_bottom;
    var $logo = "ng-logo-s.png";

    function load($filename) {

        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
        if( $this->image_type == IMAGETYPE_JPEG ) {

            $this->image = imagecreatefromjpeg($filename);
        } elseif( $this->image_type == IMAGETYPE_GIF ) {

            $this->image = imagecreatefromgif($filename);
        } //else

        if( $this->image_type == IMAGETYPE_PNG ) {

            $this->image = imagecreatefrompng($filename);
        }
    }
    function save($filename, $image_type=IMAGETYPE_JPEG, $compression=100, $permissions=null) {

        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image,$filename,$compression);
        } elseif( $image_type == IMAGETYPE_GIF ) {

            imagegif($this->image,$filename);
        } elseif( $image_type == IMAGETYPE_PNG ) {

            imagepng($this->image,$filename);
        }
        if( $permissions != null) {

            chmod($filename,$permissions);
        }
    }
    function output($image_type=IMAGETYPE_JPEG) {

        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image);
        } elseif( $image_type == IMAGETYPE_GIF ) {

            imagegif($this->image);
        } elseif( $image_type == IMAGETYPE_PNG ) {

            imagepng($this->image);
        }
    }
    function getWidth() {

        return imagesx($this->image);
    }
    function getHeight() {

        return imagesy($this->image);
    }
    function resizeToHeight($height) {

        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width,$height);
    }

    function resizeToWidth($width) {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width,$height);
    }

    function scale($scale) {
        $width = $this->getWidth() * $scale/100;
        $height = $this->getheight() * $scale/100;
        $this->resize($width,$height);
    }

    function resize($width,$height) {
        if($width == 800 || $height == 500)
        {
            $width_max = 800;
            $height_max = 500;
        }
        elseif($width == 200 || $height == 125)
        {
            $width_max = 200;
            $height_max = 125;
        }
        elseif($width == 1600 || $height == 1000)
        {
            $width_max = 1600;
            $height_max = 1000;
        }else{
            $width_max = $width;
            $height_max = $height;
        }
        $new_image = imagecreatetruecolor($width_max, $height_max);
        $white = imagecolorallocate($new_image,255,255,255);
        imagefill($new_image, 0, 0, $white);
        imagecopyresampled($new_image, $this->image, abs($width_max-$width)/2, abs($height_max-$height)/2, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());

//        if($this->crop_top > 0)
//        {
//            $to_crop_array = array('x' => $this->crop_top , 'y' => 0, 'width' => $width, 'height'=> $height - $this->crop_top);
//            $new_image = imagecrop($new_image, $to_crop_array);
//        }
//        if($this->crop_bottom > 0)
//        {
//            $to_crop_array = array('x' => 0 , 'y' => 0, 'width' => $width, 'height'=> $height - $this->crop_bottom);
//            $new_image = imagecrop($new_image, $to_crop_array);
//        }

        $this->image = $new_image;
    }
    function signature()
    {
        $logo = imagecreatefrompng(ROOT . DIRECTORY_SEPARATOR . $this->logo);
        list($width_logo, $height_logo) = getimagesize(ROOT . DIRECTORY_SEPARATOR . $this->logo);

        $x = $this->getWidth();
        $y = $this->getHeight();

        $x = $x/2 - $width_logo/2;
        $y = $y - $height_logo - 5;

        imagecopy($this->image, $logo, $x, $y ,0, 0,  $width_logo, $height_logo);
        imagedestroy($logo);
    }

}
class Database
{
	var $db_connect_id;
	var $query_result;
	var $row = array();
	var $rowset = array();
	var $num_queries = 0;
    var $query_ids = array();
	function Database($config) {
		
		$this->persistency 	= $config['pconnect'];
		$this->user 		= $config['username'];
		$this->password 	= $config['password'];
		$this->server 		= $config['hostname'];
		$this->dbname 		= $config['database'];

		if($this->persistency)
		{
			$this->db_connect_id = @mysql_pconnect($this->server, $this->user, $this->password);
		}
		else
		{
			$this->db_connect_id = @mysql_connect($this->server, $this->user, $this->password);
		}
		if($this->db_connect_id)
		{
			$dbselect = @mysql_select_db($this->dbname);
						
			if(!$dbselect)
			{
				@mysql_close($this->db_connect_id);
				$this->db_connect_id = $dbselect;
			}
			@mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'",$this->db_connect_id);
			return $this->db_connect_id;
		}
		else
		{
			return false;
		}
	}
	function close() {
        if($this->db_connect_id) {
            $numid = count($this->query_ids);
            for ($i=0; $i<$numid; $i++) {
                if (isset($this->query_ids[$i])) { @mysql_free_result($this->query_ids[$i]); }
            }
            if (!$this->persistency) {
                $result = @mysql_close($this->db_connect_id);
                $this->db_connect_id = NULL;
                return $result;
            }
            return false;
        }
        else
        {
            return false;
        }
    }
	function query($sql = "", $transaction = FALSE) {
		// Remove any pre-existing queries
		unset($this->query_result);
		if(!empty($sql)) {
			$sql = str_replace('union','UNI0N', $sql); // Forced Security issue fix by DJMaze
			if (preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $sql))
			{
				$sql = preg_replace("/^\s*DELETE\s+FROM\s+(\S+)\s*$/", "DELETE FROM \\1 WHERE 1=1", $sql);
			}
			$this->query_result = @mysql_query($sql, $this->db_connect_id);
			$this->num_queries++;
		}
		if($this->query_result) {
			unset($this->row[$this->query_result]);
			unset($this->rowset[$this->query_result]);
			$this->query_ids[] = $this->query_result;
			return $this->query_result;
		}
	}
	function numrows($query_id = 0) {
		if(!$query_id) {
			$query_id = $this->query_result;
		}
		if($query_id) {
			$result = @mysql_num_rows($query_id);
			return $result;
		}
		else {
			return false;
		}
	}
	function affectedrows() {
		if($this->db_connect_id) {
			$result = @mysql_affected_rows($this->db_connect_id);
			return $result;
		}
		else {
			return false;
		}
	}
	function numfields($query_id = 0) {
		if(!$query_id) {
			$query_id = $this->query_result;
		}
		if($query_id) {
			$result = @mysql_num_fields($query_id);
			return $result;
		}
		else {
			return false;
		}
	}
	function fieldname($offset, $query_id = 0) {
		if(!$query_id) {
			$query_id = $this->query_result;
		}
		if($query_id) {
			$result = @mysql_field_name($query_id, $offset);
			return $result;
		}
		else {
			return false;
		}
	}
	function fieldtype($offset, $query_id = 0) {
		if(!$query_id) {
			$query_id = $this->query_result;
		}
		if($query_id) {
			$result = @mysql_field_type($query_id, $offset);
			return $result;
		}
		else {
			return false;
		}
	}
	function fetchrow($query_id = 0) {
		if(!$query_id) {
			$query_id = $this->query_result;
		}

		if($query_id) {
            $qid = (int) $query_id;
			$this->row[$qid] = @mysql_fetch_array($query_id);
			return $this->row[$qid];
		}
		else {
			return false;
		}
	}
	function fetchrowset($query_id = 0) {
		if(!$query_id) {
			$query_id = $this->query_result;
		}
		if($query_id) {
				unset($this->rowset[$query_id]);
			unset($this->row[$query_id]);
			while($this->rowset[$query_id] = @mysql_fetch_array($query_id)) {
				$result[] = $this->rowset[$query_id];
			}
			return $result;
		} else 	{
			return false;
		}
	}
	function fetchfield($field, $rownum = -1, $query_id = 0) {
		if(!$query_id) {
			$query_id = $this->query_result;
		}
		if($query_id) {
			if($rownum > -1) {
				$result = @mysql_result($query_id, $rownum, $field);
			} else {
				if(empty($this->row[$query_id]) && empty($this->rowset[$query_id])) {
					if($this->fetchrow()) {
						$result = $this->row[$query_id][$field];
					}
				} else {
					if($this->rowset[$query_id]) {
						$result = $this->rowset[$query_id][$field];
					} else if($this->row[$query_id]) {
						$result = $this->row[$query_id][$field];
					}
				}
			}
			return $result;
		} else {
			return false;
		}
	}
	function rowseek($rownum, $query_id = 0) {
		if(!$query_id) {
			$query_id = $this->query_result;
		}
		if($query_id) {
			$result = @mysql_data_seek($query_id, $rownum);
			return $result;
		} else {
			return false;
		}
	}
	function nextid() {
		if($this->db_connect_id) {
			$result = @mysql_insert_id($this->db_connect_id);
			return $result;
		} else {
			return false;
		}
	}
	function freeresult($query_id = 0) {
		if(!$query_id) {
			$query_id = $this->query_result;
		}
		if ( $query_id ) {
			unset($this->row[$query_id]);
			unset($this->rowset[$query_id]);
			@mysql_free_result($query_id);
			$numid = count($this->query_ids);
			for ($i=0; $i < $numid; $i++) {
				if ($this->query_ids[$i] == $query_id) {
					unset($this->query_ids[$i]);
					return true;
				}
			}
			return true;
		} else {
			return false;
		}
	}
	function error($query_id = 0) {
		return @mysql_error($this->db_connect_id);
	}
}
class XTemplate
{
	var $filecontents = '';                               /* raw contents of template file */
	var $blocks = array();                                /* unparsed blocks */
	var $parsed_blocks = array();                 /* parsed blocks */
	var $preparsed_blocks = array();          /* preparsed blocks, for file includes */
	var $block_parse_order = array();         /* block parsing order for recursive parsing (sometimes reverse:) */
	var $sub_blocks = array();                        /* store sub-block names for fast resetting */
	var $vars = array();                                  /* variables array */
	var $filevars = array();                          /* file variables array */
	var $filevar_parent = array();                /* filevars' parent block */
	var $filecache = array();                         /* file caching */
	var $tpldir = '';                     /* location of template files */
	var $files = null;                    /* file names lookup table */
	var $filename = '';
	var $file_delim = '';//"/\{FILE\s*\"([^\"]+)\"\s*\}/m";  /* regexp for file includes */
	var $filevar_delim = '';//"/\{FILE\s*\{([A-Za-z0-9\._]+?)\}\s*\}/m";  /* regexp for file includes */
	var $filevar_delim_nl = '';//"/^\s*\{FILE\s*\{([A-Za-z0-9\._]+?)\}\s*\}\s*\n/m";  /* regexp for file includes w/ newlines */
	var $block_start_delim = '<!-- ';         /* block start delimiter */
	var $block_end_delim = '-->';                 /* block end delimiter */
	var $block_start_word = 'BEGIN:';         /* block start word */
	var $block_end_word = 'END:';                 /* block end word */
	var $tag_start_delim = '{';
	var $tag_end_delim = '}';
	var $mainblock = 'main';
	var $output_type = 'HTML';
	var $_null_string = array('' => '');             /* null string for unassigned vars */
	var $_null_block = array('' => '');  /* null string for unassigned blocks */
	var $_error = '';
	var $_autoreset = true;                                     /* auto-reset sub blocks */
	var $_ignore_missing_blocks = true ;          // NW 17 oct 2002 - Set to FALSE to
	var $_file_name_full_path = '';
	function XTemplate ($file,  $tpldir = '', $files = null, $mainblock = 'main', $autosetup = true) 
	{
		$this->filename = $file;
		$this->_file_name_full_path = realpath($file);
		$this->tpldir = $tpldir;
		if (is_array($files)) {
			$this->files = $files;
		}
		$this->mainblock = $mainblock;
		if ($autosetup) {
			$this->setup();
		}
	}
	function restart ($file, $tpldir = '',$files=null,$mainblock='main', $autosetup = true, $tag_start = '{', $tag_end = '}') 
	{
		$this->filename = $file;
		$this->_file_name_full_path = realpath($file);
		$this->tpldir = $tpldir;
		if (is_array($files)) {
			$this->files = $files;
		}
		$this->mainblock = $mainblock;
		$this->tag_start_delim = $tag_start;
		$this->tag_end_delim = $tag_end;
		$this->filecontents = '';
		$this->blocks = array();
		$this->parsed_blocks = array();
		$this->preparsed_blocks = array();
		$this->block_parse_order = array();
		$this->sub_blocks = array();
		$this->vars = array();
		$this->filevars = array();
		$this->filevar_parent = array();
		$this->filecache = array();
		if ($autosetup) {
			$this->setup();
		}
	}
	function setup ($add_outer = false) 
	{
		$this->tag_start_delim = preg_quote($this->tag_start_delim);
		$this->tag_end_delim = preg_quote($this->tag_end_delim);
		$this->file_delim = "/" . $this->tag_start_delim . "FILE\s*\"([^\"]+)\"\s*" . $this->tag_end_delim . "/m";
		$this->filevar_delim = "/" . $this->tag_start_delim . "FILE\s*" . $this->tag_start_delim . "([A-Za-z0-9\._]+?)" . $this->tag_end_delim . "\s*" . $this->tag_end_delim . "/m";
		$this->filevar_delim_nl = "/^\s*" . $this->tag_start_delim . "FILE\s*" . $this->tag_start_delim . "([A-Za-z0-9\._]+?)" . $this->tag_end_delim . "\s*" . $this->tag_end_delim . "\s*\n/m";
		if (empty($this->filecontents)) {
			$this->filecontents = $this->_r_getfile($this->filename);
		}
		if ($add_outer) {
			$this->_add_outer_block();
		}
		$this->blocks = $this->_maketree($this->filecontents, '');
		$this->filevar_parent = $this->_store_filevar_parents($this->blocks);
		$this->scan_globals();
	}
	function assign ($name, $val = '') 
	{
		if (is_array($name)) {
			foreach ($name as $k => $v) {
				$this->vars[$k] = $v;
			}
		} else {
			$this->vars[$name] = $val;
		}
	}
	function assign_file ($name, $val = '')
	{
		if (is_array($name)) {
			foreach ($name as $k => $v) {
				$this->_assign_file_sub($k, $v);
			}
		} else {
			$this->_assign_file_sub($name, $val);
		}
	}
	function parse ($bname) 
	{
		if (isset($this->preparsed_blocks[$bname])) {
			$copy = $this->preparsed_blocks[$bname];
		} elseif (isset($this->blocks[$bname])) {
			$copy = $this->blocks[$bname];
		} elseif ($this->_ignore_missing_blocks) {
			$this->_set_error("parse: blockname [$bname] does not exist");
			return;
		} else {
			$this->_set_error("parse: blockname [$bname] does not exist");
		}
		if (!isset($copy)) {
			die('Block: ' . $bname);
		}
		$copy = preg_replace($this->filevar_delim_nl, '', $copy);
		$var_array = array();
		preg_match_all("/" . $this->tag_start_delim . "([A-Za-z0-9\._]+? ?#?.*?)" . $this->tag_end_delim. "/", $copy, $var_array);
		$var_array = $var_array[1];
		foreach ($var_array as $k => $v) {
			$any_comments = explode('#', $v);
			$v = rtrim($any_comments[0]);
			if (sizeof($any_comments) > 1) {
				$comments = $any_comments[1];
			} else {
				$comments = '';
			}
			$sub = explode('.', $v);
			if ($sub[0] == '_BLOCK_') {
				unset($sub[0]);
				$bname2 = implode('.', $sub);
				$var = isset($this->parsed_blocks[$bname2]) ? $this->parsed_blocks[$bname2] : null;
				$nul = (!isset($this->_null_block[$bname2])) ? $this->_null_block[''] : $this->_null_block[$bname2];
				if ($var == '') {
					if ($nul == '') {
						$copy = preg_replace("/" . $this->tag_start_delim . $v . $this->tag_end_delim . "/m", '', $copy);
					} else {
						$copy = preg_replace("/" . $this->tag_start_delim . $v . $this->tag_end_delim . "/", "$nul", $copy);
					}
				} else {
					$var = trim($var);
					$var = str_replace('\\', '\\\\', $var);
					$var = str_replace('$', '\\$', $var);
					$var = str_replace('\\|', '|', $var);
					$copy = preg_replace("|" . $this->tag_start_delim . $v . $this->tag_end_delim . "|", "$var", $copy);
				}
			} else {
				$var = $this->vars;
				foreach ($sub as $v1) {
					if (!isset($var[$v1]) || (!is_array($var[$v1]) && strlen($var[$v1]) == 0)) {
						if (defined($v1)) {
							$var[$v1] = constant($v1);
						} else {
							$var[$v1] = null;
						}
					}
					$var = $var[$v1];
				}
				$nul = (!isset($this->_null_string[$v])) ? ($this->_null_string[""]) : ($this->_null_string[$v]);
				$var = (!isset($var)) ? $nul : $var;
				if ($var == '') {
					$copy=preg_replace("|\s*" . $this->tag_start_delim . $v . " ?#?" . $comments . $this->tag_end_delim . "\s*\n|m", '', $copy);
				}
				$var = trim($var);
				$var = str_replace('\\', '\\\\', $var);
				$var = str_replace('$', '\\$', $var);
				$var = str_replace('\\|', '|', $var);
				$copy=preg_replace("|" . $this->tag_start_delim . $v . " ?#?" . $comments . $this->tag_end_delim . "|", "$var", $copy);
			}
		}
		if (isset($this->parsed_blocks[$bname])) {
			$this->parsed_blocks[$bname] .= $copy;
		} else {
			$this->parsed_blocks[$bname] = $copy;
		}
		if ($this->_autoreset && (!empty($this->sub_blocks[$bname]))) {
			reset($this->sub_blocks[$bname]);
			foreach ($this->sub_blocks[$bname] as $k => $v) {
				$this->reset($v);
			}
		}
	}
	function rparse ($bname) 
	{
		if (!empty($this->sub_blocks[$bname])) {
			reset($this->sub_blocks[$bname]);
			foreach ($this->sub_blocks[$bname] as $k => $v) {
				if (!empty($v)) {
					$this->rparse($v);
				}
			}
		}
		$this->parse($bname);
	}
	function insert_loop ($bname, $var, $value = '') 
	{
		$this->assign($var, $value);
		$this->parse($bname);
	}
	function array_loop ($bname, $var, &$values) 
	{
		if (is_array($values)) {
			foreach($values as $v) {
				$this->assign($var, $v);
				$this->parse($bname);
			}
		}
	}
	function text ($bname = '') 
	{
		$text = '';
		$bname = !empty($bname) ? $bname : $this->mainblock;
		$text .= isset($this->parsed_blocks[$bname]) ? $this->parsed_blocks[$bname] : $this->get_error();
		return $text;
	}
	function out ($bname) 
	{
		$out = $this->text($bname);
		echo $this->strip_pre($out);
	}
	function out_file ($bname, $fname) 
	{
		if (!empty($bname) && !empty($fname) && is_writeable($fname)) {
			$fp = fopen($fname, 'w');
			fwrite($fp, $this->text($bname));
			fclose($fp);
		}
	}
	function reset ($bname) 
	{
		$this->parsed_blocks[$bname] = '';
	}
	function parsed ($bname) 
	{
		return (!empty($this->parsed_blocks[$bname]));
	}
	function SetNullString ($str, $varname = '') 
	{
		$this->_null_string[$varname] = $str;
	}
	function SetNullBlock ($str, $bname = '') 
	{
		$this->_null_block[$bname] = $str;
	}
	function set_autoreset () 
	{
		$this->_autoreset = true;
	}
	function clear_autoreset () 
	{
		$this->_autoreset = false;
	}
	function scan_globals () 
	{
		reset($GLOBALS);
		foreach ($GLOBALS as $k => $v) {
			$GLOB[$k] = $v;
		}
		$this->assign('PHP', $GLOB);
	}
	function get_error () 
	{
		$retval = false;
		if ($this->_error != '') {
			switch ($this->output_type) {
				case 'HTML':
				case 'html':
				$retval = '<b>[XTemplate]</b><ul>' . nl2br(str_replace('* ', '<li>', str_replace(" *\n", "</li>\n", $this->_error))) . '</ul>';
				break;
				default:
				$retval = '[XTemplate] ' . str_replace(' *\n', "\n", $this->_error);
				break;
			}
		}
		return $retval;
	}
	function _maketree ($con, $parentblock='') 
	{
		$blocks = array();
		$con2 = explode($this->block_start_delim, $con);
		if (!empty($parentblock)) {
			$block_names = explode('.', $parentblock);
			$level = sizeof($block_names);
		} else {
			$block_names = array();
			$level = 0;
		}
		foreach($con2 as $k => $v) {
			$patt = "($this->block_start_word|$this->block_end_word)\s*(\w+) ?#?.*?\s*$this->block_end_delim(.*)";
			$res = array();
			if (preg_match_all("/$patt/ims", $v, $res, PREG_SET_ORDER)) {
				$block_word	= $res[0][1];
				$block_name	= $res[0][2];
				$content	= $res[0][3];
				if (strtoupper($block_word) == $this->block_start_word) {
					$parent_name = implode('.', $block_names);
					$block_names[++$level] = $block_name;
					$cur_block_name=implode('.', $block_names);
					$this->block_parse_order[] = $cur_block_name;
					$blocks[$cur_block_name] = isset($blocks[$cur_block_name]) ? $blocks[$cur_block_name] . $content : $content;
					$blocks[$parent_name] .= str_replace('\\', '', $this->tag_start_delim) . '_BLOCK_.' . $cur_block_name . str_replace('\\', '', $this->tag_end_delim);
					$this->sub_blocks[$parent_name][] = $cur_block_name;
					$this->sub_blocks[$cur_block_name][] = '';
				} else if (strtoupper($block_word) == $this->block_end_word) {
					unset($block_names[$level--]);
					$parent_name = implode('.', $block_names);
					$blocks[$parent_name] .= $res[0][3];
				}
			} else {
				$tmp = implode('.', $block_names);
				if ($k) {
					$blocks[$tmp] .= $this->block_start_delim;
				}
				$blocks[$tmp] = isset($blocks[$tmp]) ? $blocks[$tmp] . $v : $v;
			}
		}
		return $blocks;
	}
	function _assign_file_sub ($name, $val) 
	{
		if (isset($this->filevar_parent[$name])) {
			if ($val != '') {
				$val = $this->_r_getfile($val);
				foreach($this->filevar_parent[$name] as $parent) {
					if (isset($this->preparsed_blocks[$parent]) && !isset($this->filevars[$name])) {
						$copy = $this->preparsed_blocks[$parent];
					} elseif (isset($this->blocks[$parent])) {
						$copy = $this->blocks[$parent];
					}
					$res = array();
					preg_match_all($this->filevar_delim, $copy, $res, PREG_SET_ORDER);
					if (is_array($res) && isset($res[0])) {
						foreach ($res[0] as $v) {
							$copy = preg_replace("/" . preg_quote($v) . "/", "$val", $copy);
							$this->preparsed_blocks = array_merge($this->preparsed_blocks, $this->_maketree($copy, $parent));
							$this->filevar_parent = array_merge($this->filevar_parent, $this->_store_filevar_parents($this->preparsed_blocks));
						}
					}
				}
			}
		}
		$this->filevars[$name] = $val;
	}
	function _store_filevar_parents ($blocks)
	{
		$parents = array();
		foreach ($blocks as $bname => $con) {
			$res = array();
			preg_match_all($this->filevar_delim, $con, $res);
			foreach ($res[1] as $k => $v) {
				$parents[$v][] = $bname;
			}
		}
		return $parents;
	}
	function _set_error ($str)    
	{
		$this->_error .= '* ' . $str . " *\n";
	}
	function _getfile ($file) 
	{
		if (!isset($file)) {
			$this->_set_error('!isset file name!' . $file);
			return '';
		}
		if (isset($this->files)) {
			if (isset($this->files[$file])) {
				$file = $this->files[$file];
			}
		}
		if (!empty($this->tpldir)) {
			$file = $this->tpldir. '/' . $file;
		}
		if (isset($this->filecache[$file])) {
			$file_text=$this->filecache[$file];
		} else {
			if (is_file($file)) {
				if (!($fh = fopen($file, 'r'))) {
					$this->_set_error('Cannot open file: ' . $file);
					return '';
				}
				$file_text = fread($fh,filesize($file));
				fclose($fh);
			} else {
				$this->_set_error("[" . realpath($file) . "] ($file) does not exist");
				$file_text = "<b>__XTemplate fatal error: file [$file] does not exist__</b>";
			}
			$this->filecache[$file] = $file_text;
		}
		return $file_text;
	}
	function _r_getfile ($file) 
	{
		$text = $this->_getfile($file);
		$res = array();
		while (preg_match($this->file_delim,$text,$res)) {
			$text2 = $this->_getfile($res[1]);
			$text = preg_replace("'".preg_quote($res[0])."'",$text2,$text);
		}
		return $text;
	}
	function _add_outer_block () 
	{
		$before = $this->block_start_delim . $this->block_start_word . ' ' . $this->mainblock . ' ' . $this->block_end_delim;
		$after = $this->block_start_delim . $this->block_end_word . ' ' . $this->mainblock . ' ' . $this->block_end_delim;
		$this->filecontents = $before . "\n" . $this->filecontents . "\n" . $after;
	}
	function _pre_var_dump () 
	{
		echo '<pre>';
		var_dump(func_get_args());
		echo '</pre>';
	}
	function strip_pre($text)
	{
		$forbid = array("<PRE>", "</PRE>");
		return str_replace($forbid, "", $text);
	}
}
class CachingXTemplate extends XTemplate
{
	public $cache_expiry	= 0;
	public $cache_unique	= '';
	public $cache_ext		= '.html';
	public $cache_dir		= './cache';
	private $_template_is_cached	= false;
	private $_cache_expiry			= 0;
	private $_cache_filemtime		= 0;
	public function __construct($file, $cache_expiry = 0, $tpldir = '', $files = null, $mainblock = 'main', $autosetup = true, $cache_unique = '', $cache_dir = '/cache', $cache_ext = '.html') {
		if(empty($file)) return false;
		$this->restart($file, $tpldir, $files, $mainblock, $autosetup, $this->tag_start_delim, $this->tag_end_delim, $cache_expiry, $cache_unique, $cache_dir, $cache_ext);
	}
	public function restart ($file, $tpldir = '', $files = null, $mainblock = 'main', $autosetup = true, $tag_start = '{', $tag_end = '}', $cache_expiry = 0, $cache_unique = '', $cache_dir = './xcache', $cache_ext = '.xcache') {
		if ($cache_expiry > 0) {
			$this->cache_expiry = $cache_expiry;
		}
		if (!empty($cache_unique)) {
			if (!preg_match('/^\./', $cache_unique)) {
				$cache_unique = '.' . $cache_unique;
			}
			$this->cache_unique = $cache_unique;
		}
		if (!empty($cache_dir)) {
			$this->cache_dir = WEB_PREFIX . $cache_dir;
		}
		if (!empty($cache_ext)) {
			if (!preg_match('/^\./', $cache_ext)) {
				$cache_ext = '.' . $cache_ext;
			}
			$this->cache_ext = $cache_ext;
		}
		parent::restart($file, $tpldir, $files, $mainblock, false, $tag_start, $tag_end);
		if (!$this->_template_is_cached && $autosetup) {
			$this->setup();
		}
	}
	public function assign ($name, $val = '', $magic_quotes = false) {
		if (!$this->_template_is_cached) {
			parent::assign($name, $val, $magic_quotes);
		}
	}
	public function assign_file ($name, $val = '') {
		if (!$this->_template_is_cached) {
			parent::assign_file($name, $val);
		}
	}
	public function parse ($bname, $cache_expiry = 0, $cachefile='') {
		if (!$this->_template_is_cached) {
			if (!$this->read_block_cache($bname, $cache_expiry, $cachefile)) {
				parent::parse($bname);
				$this->write_block_cache($bname, $cache_expiry, $cachefile);
			}
		}
	}
	public function text ($bname = '') {
		return parent::text($bname);
	}
	protected function read_block_cache ($bname, $cache_expiry = 0, $cachefile ='') {
		$retval = false;
		$file = $this->cache_dir  . DIRECTORY_SEPARATOR . $cachefile . $this->cache_unique . $this->cache_ext;
		if ($cache_expiry > 0 && file_exists($file)) {
			$filemtime = filemtime($file);
			$cache_expiry = time() - $cache_expiry;
			if ($filemtime >= $cache_expiry) {
				if ($block = file_get_contents($file)) {
					$block = unserialize($block);
					$this->parsed_blocks[$bname] = $block;
					$retval = true;
				}
			} else {
				if (file_exists($file)) {
					unlink($file);
				}
			}
		}
		return $retval;
	}
	function is_cache($bname, $cache_expiry = 0)
	{
		$file = $this->cache_dir  . DIRECTORY_SEPARATOR . $bname . $this->cache_unique . $this->cache_ext;
		if ($cache_expiry > 0 && file_exists($file)) {
			$filemtime = filemtime($file);
			$cache_expiry = time() - $cache_expiry;
			if ($filemtime >= $cache_expiry) return TRUE;
			else return FALSE;
		}
		else return FALSE;
	}
	protected function write_block_cache ($bname, $cache_expiry = 0, $cachefile ='') {
		if ($cache_expiry > 0 && file_exists($this->cache_dir)) {
			if (!file_exists($this->cache_dir )) {
				@mkdir($this->cache_dir );
			}
			@chmod($this->cache_dir, 0777);
			$file	= $this->cache_dir  . DIRECTORY_SEPARATOR . $cachefile . $this->cache_unique . $this->cache_ext;
			@file_put_contents($file, serialize($this->parsed_blocks[$bname]));
			@chmod($this->cache_dir, 0755);
			@chmod($file, 0755);
		}
	}
	private function _get_filename () {
		$filename = $this->filename;
		if (!empty($this->tpldir)) {
			$filename = str_replace(DIRECTORY_SEPARATOR, '_', $this->tpldir . DIRECTORY_SEPARATOR) . $this->filename;
		}
		return $filename;
	}
};
class Paging
{
	var $total	= 0;
	var $current		= 0;
	var $perpage	= 10;
	var $url		= "";
	//stype
    var $middle     = 5;
	function Paging($params = array())
	{
		if (count($params) > 0)
		{
			$this->initialize($params);		
		}
	}
	function initialize($params = array())
	{
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
                $this->$key = $val;
			}
		}
	}
	function show()
	{
		if($this->total == 0) return "";
		//
		$page_all	= ceil($this->total/$this->perpage);
		//
		if($page_all <=1) return "";
		$this->current	= empty($this->current)?1:intval($this->current);
		$this->current	= ($this->current > $page_all)? $page_all : $this->current;
		//
		$linkfirst	= site_url($this->url."/1","page");
		$linklast	= site_url($this->url."/".($page_all),"page");
		$str	= "";
        $start  = 1;
        $end    = $page_all;

        //Show
        if($this->current > 10){
            $str	.= '<a href="'.site_url($this->url."/" . ($this->current -10), "page").'" >&nbsp;<<&nbsp;</a>|';
        }
        if($this->current > $this->middle + 1)
        {
            $str	.= '<a href="'.site_url($this->url."/" . ($this->current -1), "page").'" >&nbsp;<&nbsp;</a>';
            $str	.= '<a href="'.site_url($this->url."/1","page").'" >&nbsp;1&nbsp;</a> - |';
            $start  = $this->current - $this->middle;
            $end    = $this->current + $this->middle;
            $end    = ($end > $page_all) ? $page_all : $end;
        }else{
            $end    = 2 * $this->middle + 1;
        }

        for($i = $start; $i<= $end; $i++)
        {
            $link	= $this->url."/". $i;

            if($i == $page_all)
            {
                $str	.= '<a href="'.site_url($link,"page").'" >&nbsp;'.$i.'&nbsp;</a>';
                break;
            }
            if($i == $this->current)
            {
                $str	.= '<a href="'.site_url($link,"page").'" current="Y">&nbsp;'.$i.'&nbsp;</a>|';
            }else
                $str	.= '<a href="'.site_url($link,"page").'" >&nbsp;'.$i.'&nbsp;</a>|';
        }
        if(($page_all > 2 * $this->middle + 1) && ($this->current < $page_all)){
            $str	.= ' - <a href="'.site_url($this->url."/" . ($page_all - 1), "page").'" >&nbsp;' . ($page_all - 1) .'&nbsp;</a>';
            $str	.= '<a href="'.site_url($this->url."/" . ($this->current + 1), "page").'" >&nbsp;>&nbsp;</a>|';
            $str	.= '<a href="'.site_url($this->url."/" . ($this->current + 10), "page").'" >&nbsp;>>&nbsp;</a>';
        }

		$this->current	= ($this->current-1)*$this->perpage;
		return $str;
	}
}
class SYSTEM
{
    var $xtpl;
    var $db;
    var $user;
    var $conf;
    var $language;
    var $current_lang;
    var $cache_live;
    public function  __construct()
    {
        $this->conf  =& get_config();
        $this->language  =& get_lang();
        $this->current_lang  = get_current_lang();
    }
    public function strat()
    {
        require_once(ROOT . DIRECTORY_SEPARATOR . WEB_PREFIX . DIRECTORY_SEPARATOR . $this->conf['sys_skin'] .EXT);

        $COI	= new COI();
        if(empty($COI))
        {
            reload(BASE_URL . "404.shtml");
            return false;
        }
        return true;
    }
    public function set_lang()
    {
        $lang = segment(2);
        $lang = ($lang == 1)?'vn':'en';
        set_cookie("LANGUAGE", $lang);
        reload(referrer());
    }
    public function goto_home()
    {
        reload();
    }
    public function config($name='')
    {
        if(empty($name))
            return false;
        else
            return @$this->conf[$name];
    }
    public function lang($name='')
    {
        if(empty($name))
            return false;
        else
            return $this->language[$name];
    }
    public function clear_data()
    {
        if(isset($this->db))
        {
            $this->db->close(); unset($this->db);
        }
    }
    public function check_login()
    {
        $login = get_cookie("LOGIN");
        if(empty($login)) return false;

        $this->user = json_decode($login);
        if($this->user->id > 0) return true;

        return false;
    }
    public function xtpl_start($file, $return = null)
    {
        if(!isset($file)) die('Template not found!');

        if($return)
        {
            return new CachingXTemplate(ROOT . DIRECTORY_SEPARATOR . "public/skin" . DIRECTORY_SEPARATOR . $this->config('sys_skin')."/".$file.'.html');
        }else {
            $this->xtpl = new CachingXTemplate(ROOT . DIRECTORY_SEPARATOR . "public/skin" . DIRECTORY_SEPARATOR . $this->config('sys_skin')."/".$file.'.html');
        }
    }
    public function xtpl_end($block='main')
    {
        $this->xtpl->assign('SITE_TITLE'			, strip_tags($this->site_title));
        $this->xtpl->assign('SITE_DESCRIPTION'		, strip_tags($this->site_description));
        $this->xtpl->assign('SITE_KEY'				, strip_tags($this->site_keyword));

        $dir	= BASE_URL . "public/skin/" .$this->config('sys_skin');
        $this->xtpl->assign('img', $dir."/images/");
        $this->xtpl->assign('uri', BASE_URL.$this->config('sys_run_file'));
        $this->xtpl->assign('url', BASE_URL);
        $this->xtpl->assign('skin', $this->config('sys_skin'));

        $this->xtpl->parse($block);
        $html	= $this->xtpl->text($block);

        $repl	= array(
            'src="images/',
            "src='images/",
            'href="images/',
            "href='images/",
            'url(images/',
            'background="images',
            "background='images",
            'css/',
            'js/'
        );
        $sear = array(
            'src="'.$dir."/images/",
            "src='".$dir."/images/",
            'href="'.$dir."/images/",
            "href='".$dir."/images/",
            'url('.$dir."/images/",
            'background="'.$dir."/images/",
            "background='".$dir."/images/",
            $dir."/css/",
            $dir."/js/"
        );
        if(isset($html))
        {
            $html = str_ireplace($repl, $sear, $html);
            //if($_SERVER['HTTP_HOST'] != "localhost") $html = str_ireplace(array("\n","\r"), array("",""), $html);
            echo $html;
        }
        else echo "";
    }
}
?>
