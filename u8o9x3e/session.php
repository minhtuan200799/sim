<?php if (!defined('ROOT')) exit('No direct script access allowed');
/***********************************************************************/
/*                                                                     */
/*   Copyright (C) COI Corporation. All rights reserved                */
/*   Author     : Roca Chien, rocachien@usa.com                        */

/*                                                                     */
/*   Created    : 20-08-2010 13:20:05.                                 */
/*   Modified   : 28-08-2010 16:20:05.                                 */
/*                                                                     */  
/***********************************************************************/
//
class Session {
	var $sid;
	var $sname;
	var $svalue;
	var $session_limit;
	var $online_limit;
	var $online_live;
	var $session_by;
	var $online;
	var $db;
	function  Session($db) {
		$this->initialize();
		$this->db		= $db;
		$this->LoadVisitor();
	}
	function __destruct() {	
		if(isset($this->sid)) {
			unset($this->sid);
		}
		if(isset($this->CI)) {
			unset($this->CI);
		}
		if(isset($this->sname)) {
			unset($this->sname);
		}
		if(isset($this->svalue)) {
			unset($this->svalue);
		}
		if(isset($this->session_limit)) {
			unset($this->session_limit);
		}
		if(isset($this->online_limit)) {
			unset($this->online_limit);
		}
		if(isset($this->online_live)) {
			unset($this->online_live);
		}
		if(isset($this->session_by)) {
			unset($this->session_by);
		}
		if(isset($this->online)) {
			unset($this->online);
		}		
		if(isset($this->db))
		{
			$this->db->close();
			unset($this->db);
		}
	}
	function __toString() {
		return get_class(__CLASS__) ;
	}	
	function __wakeup(){
  		$class = get_class($this);
  		new $class;
	}
		
	function initialize($config = array()){
		$this->sname		= md5("randomid");
        $this->svalue	= session_id();

        $defaults = array(
							'sid'			=> $this->svalue,
							'sname'			=> "",
							'svalue'		=> "",
							'session_limit'	=> 18000,		/*1800(30m)*/
							'online_limit'	=> 60,			/*180(3m)*/
							'online_live'	=> 6,			/*18s*/
							'session_by'	=> "cookie",	/*auto|cookie|session|data*/
							'online'		=> 0
						);	
	
		foreach ($defaults as $key => $val)
		{
			if (isset($config[$key]))
			{
				$method = 'set_'.$key;
				if (method_exists($this, $method))
				{
					$this->$method($config[$key]);
				}
				else
				{
					$this->$key = $config[$key];
				}			
			}
			else
			{
				$this->$key = $val;
			}
		}
	}
	function set_sname($input){
		$this->sname	= trim($input);
	}
	function set_svalue($input){
		$this->svalue	= trim($input);
	}
	function set_session_limit($input){
		$this->session_limit	= ( ! eregi("^[[:digit:]]+$", $input)) ? 0 : $input;
	}
	function set_online_limit($input){
		$this->online_limit	= ( ! eregi("^[[:digit:]]+$", $input)) ? 0 : $input;
	}
	function set_online_live($input){
		$this->online_live	= ( ! eregi("^[[:digit:]]+$", $input)) ? 0 : $input;
	}
	function set_session_by($input)
	{
		$input	= trim($input);
		switch($input){
			case "cookie"	: $input	= "cookie"; break;
			case "data"		: $input	= "data"; break;
			case "session"	: $input	= "session"; break;
			default 		: $input	= "auto"; break;
		}
		$this->session_by	= $input;
	}
	function getKeyFromSession()
	{//	Get session key from session
		$value = "";
		if(isset($_SESSION[$this->sname]))
			$value = $_SESSION[$this->sname];
		return $value;
	}
	
	function setKeyToSession ()
	{//	Set session key to session
		$_SESSION[$this->sname]=$this->svalue;
		return $_SESSION[$this->sname];
	}
	
	function delKeySession()
	{//	delete key from session
		unset($_SESSION[$this->sname]);
	}
	
	function getKeyFromData()
	{//	get session key from data base
		$value	= "";
		$begin	= 0;
		$time	= time();
		$sql	= "SELECT ss_value, ss_begin FROM sys_session WHERE ss_id='".$this->sid."' AND ss_name='"
				.$this->sname."' LIMIT 1";
		$res 	= $this->db->query($sql);
		if($this->db->numrows($res)) 
		{
			while($row	= $this->db->fetchrow($res)){
				$value	= $row['ss_value'];
				$begin	= $row['ss_begin'];
			}
		}
		if($begin + $this->session_limit < $time)
		{
			$sql	= "DELETE FROM sys_session WHERE (ss_begin + ".$this->session_limit.") < ".$time;
			$this->db->query($sql);
		}elseif($begin + $this->online_limit < $time)
		{
			$sql 	=  "UPDATE sys_session SET ss_begin= '". $time ."' WHERE ss_id='".$this->sid
					."' AND ss_name='".$this->sname."'";
			$this->db->query($sql);
		}
		return $value ;
	}
	function setKeyToData ()
	{//	Set session key to data base
		$time=time();
		$sql="SELECT ss_begin FROM sys_session WHERE ss_id='".$this->sid."' AND ss_name='".$this->sname."'";
		$rs = $this->db->query($sql);
		if($this->db->numrows($rs)) 
		{
			while($row = $this->db->fetchrow($rs)){
				$begin = $row['ss_begin'];
			}
		}
		if (empty($begin)) {
			$sql  =  "INSERT INTO sys_session (ss_id, ss_name, ss_value, ss_begin) "
					."VALUES ('".$this->sid."', '".$this->sname."', '".$this->svalue."', '".$time."')";		
		}else{
			$sql  =  "UPDATE sys_session SET ss_begin= '". $time
					."', ss_value='".$this->svalue."' WHERE ss_id='".$this->sid."' AND ss_name='".$this->sname."'";	
		}
		$err=($this->db->query($sql))? true : false;
		return $err;
	}
	function delKeyData()
	{//	delete key from Data
		$sql="DELETE FROM sys_session WHERE ss_id='".$this->sid."' AND ss_name='".$this->sname."'";
		$err=($this->db->query($sql))? true : false;
		return $err;
	}
	function getKeyFromCookie()
	{//	Get session key from Cookies
		return get_cookie($this->sname);
	}
	function setKeyToCookie()
	{//	Set session key to cookies
		//remove old cookie
		$this->delKeyCookie();		
		//set a new cookie		
		return set_cookie($this->sname, $this->svalue);
	}
	function delKeyCookie()
	{//	delete key from session
		return del_cookie($this->sname);
	}
    function decode($st)
    {
        return base64_decode($st);
    }
    function encode($st)
    {
        return base64_encode($st);
    }
	function getKey($name, $ssby ="")
	{//	get session key
		if(!empty($ssby)) $this->session_by	= $ssby;
		$this->set_sname(md5($name));
		switch($this->session_by){
			case "cookie":return $this->decode($this->getKeyFromCookie()); break;
			case "data": return $this->decode($this->getKeyFromData()); break;
			case "session": return $this->decode($this->getKeyFromSession()); break;
			default : 
				$sess	= @$this->decode($this->getKeyFromCookie());
				if(!empty($sess))
					return 	$sess; break;
				$sess	= @$this->decode($this->getKeyFromData());
				if(!empty($sess))
					return 	$sess; break;
				$sess	= @$this->decode($this->getKeyFromSession());
				if(!empty($sess))
					return 	$sess;
				break;
		}
	}
	function setKey($name, $value, $ssby ="")
	{//	set session key
		if(!empty($ssby)) $this->session_by	= $ssby;
		$this->set_sname(md5($name));
		$this->set_svalue($this->encode($value));
		switch($this->session_by){
			case "cookie":{return @$this->setKeyToCookie(); break;}
			case "data": {return @$this->setKeyToData(); break;}
			case "session": {return @$this->setKeyToSession(); break;}
			default : {
				if(@$this->setKeyToCookie())
					return 	TRUE;
				elseif(@$this->setKeyToData())
					return 	TRUE;
				elseif(@$this->setKeyToSession())
					return 	TRUE;
				else return FALSE;
			}
		}
	}
	function delKey($name, $ssby ="")
	{//	del session
		if(!empty($ssby)) $this->session_by	= $ssby;
		$this->set_sname(md5($name));
		switch($this->session_by){
			case "cookie"	: return @$this->delKeyCookie();	break;
			case "data"		: return @$this->delKeyData();		break;
			case "session"	: return @$this->delKeySession();	break;
			default :
				@$this->delKeyCookie();
				@$this->delKeyData();
				@$this->delKeySession();
				return true;
				break;
		}
	}
	function getGlobal($name)
	{//	get session key from data base
		$value = "";
		$sql="SELECT glb_value FROM sys_global WHERE glb_name='".WEB_OPK."_".$name."'";
		$rs = $this->db->query($sql);

		if($this->db->numrows($rs)) 
		{
			while($row = $this->db->fetchrow($rs)){
				$value = $row['glb_value'];
			}
		}
		return $value ;
	}
	function updateGlobal($name, $value="")
	{//	
		$name	= WEB_OPK."_".$name;
		$sql  =  "UPDATE sys_global SET glb_value= '". $value."' WHERE glb_name='".$name."'";	
		
		$err=($this->db->query($sql))? true : false;
		return $err;
	}
	function setGlobal($name, $value="")
	{//	Set session key to data base
		$val = $this->getGlobal($name);
		$name	= WEB_OPK."_".$name;
		if (empty($val)) {
			$sql  =  "INSERT INTO sys_global (glb_name, glb_value) VALUES ('".$name."', '".$value."')";		
		}else{
			$sql  =  "UPDATE sys_global SET glb_value= '". $value."' WHERE glb_name='".$name."'";	
		}
		$err=($this->db->query($sql))? true : false;
		return $err;
	}
	function delGlobal($name)
	{//	delete key from Data
		$name	= WEB_OPK."_".$name;
		$sql="DELETE FROM sys_global WHERE glb_name='".$name."'";
		$err=($this->db->query($sql))? true : false;
		return $err;
	}
	function Online()
	{//	Set session online
		$name	= "".WEB_OPK."online";
		$time	= time();
		$this->online	= (int)$this->getKey($name, "data");
		if(empty($this->online))
		{//	Neu moi ghe tham
			$this->setKey($name, $time, "data");
		}else
		{//	Da phat sinh ra van de
			if($time - $this->online > $this->online_limit)
			{//	Neu qua gioi han thi giet no (delete)
				$this->delKey($name, "data");
			}elseif($time - $this->online > $this->online_live)
			{//	Neu qua thoi gian song ma van con thoi han thi cuu no (update)
				$this->setKey($name, $time, "data");
				$this->delOnline($name);
			}
		}
		$ol	= $this->getOnline($name);
		return empty($ol)?1:$ol;
	}
	function getOnline($name= "online")
	{//	get time online
		$name = md5($name);
		$name = substr($name,0,49);
		$sql	="SELECT COUNT(*) AS online_num FROM sys_session WHERE ss_name='".$name."'";
		$res	= $this->db->query($sql);
		if($this->db->numrows($res)) 
		{
			while($row = $this->db->fetchrow($res)){
				return $row['online_num'];
			}
		}
		else
			return 1;
	}
	function delOnline($name= "online")
	{//	get time online
		$name	= $this->encode($name);
		$name	= substr($name,0,-2);
		$time	= time();
		$sql="DELETE FROM sys_session WHERE (`ss_begin` + ".$this->online_limit." < $time) AND ss_name='".$name."'";
		$err=($this->db->query($sql))? true : false;
		return $err;
	}
	function Checktimes()
	{//	check time
		if(time() - $this->online > $this->online_live)
		{//	Neu nhu qua dat thi tiem them nhua song (update)
			return TRUE;
		}else
			return FALSE;
	}
	function LoadVisitor()
	{
		$name = 'LoadVisitor';
		$val = $this->getGlobal($name);
		if(empty($val))
		{
			$value	= "VS:1,D".date('d').":1,W".date('W').":1,M".date('m').":1,Y".date('Y').":1";
			$sql	=  "INSERT INTO sys_global (glb_name, glb_value) VALUES ('".WEB_OPK."_".$name."', '".$value."')";		
			$err=($this->db->query($sql))? true : false;
		}else{
			$val	= explode(",", $val);
			
			$vs		= explode(":", $val[0]);
			$dv		= explode(":", $val[1]);
			$wv		= explode(":", $val[2]);
			$mv		= explode(":", $val[3]);
			$yv		= explode(":", $val[4]);
			//cap nhat tang
			$tang	= $this->getViewcount();
			
			$aVt[0]	= $vs[1];
			$vs[1]	= $vs[1] + $tang;//kIEM TRA NEU NGAY KHAC VOI NGAY CHET TIET THI KHOI TAO LAI = 1
			$vs		= implode(":", $vs);
			
			$aVt[1]	= $dv[1];
			$dv[1]	= ($dv[0] == "D".date('d'))?$dv[1] + $tang:1;
			$dv[0]	= "D".date('d');
			$dv		= implode(":", $dv);
			
			$aVt[2]	= $wv[1];
			$wv[1]	= ($wv[0] == "W".date('W'))?$wv[1] + $tang:1;
			$wv[0]	= "W".date('W');
			$wv		= implode(":", $wv);
			
			$aVt[3]	= $mv[1];
			$mv[1]	= ($mv[0] == "M".date('m'))?$mv[1] + $tang:1;
			$mv[0]	= "M".date('m');
			$mv		= implode(":", $mv);
			
			$aVt[4]	= $yv[1];
			$yv[1]	= ($yv[0] == "Y".date('Y'))?$yv[1] + $tang:1;
			$yv[0]	= "Y".date('Y');
			$yv		= implode(":", $yv);
			
			$value	= $vs.",".$dv.",".$wv.",".$mv.",".$yv;
			$this->updateGlobal($name, $value);
			$this->arrVisitor	= $aVt;
			$this->LoadVisitorMost();
		}
	}
	function LoadVisitorMost()
	{
		$name	= 'LoadVisitorMost';
		$val	= $this->getGlobal($name);
		if(empty($val))
		{
			$value	= $this->arrVisitor[1].",".$this->arrVisitor[2].",".$this->arrVisitor[3].",".$this->arrVisitor[4];
			$sql	=  "INSERT INTO sys_global (glb_name, glb_value) VALUES ('".WEB_OPK."_".$name."', '".$value."')";		
			$this->db->query($sql);
		}else{
			$val	= explode(",", $val);
			
			
			$val[0]	= ($val[0]>$this->arrVisitor[1])?$val[0]:$this->arrVisitor[1];
			$val[1]	= ($val[1]>$this->arrVisitor[2])?$val[1]:$this->arrVisitor[2];
			$val[2]	= ($val[2]>$this->arrVisitor[3])?$val[2]:$this->arrVisitor[3];
			$val[3]	= ($val[3]>$this->arrVisitor[4])?$val[3]:$this->arrVisitor[4];
			
			$value	= implode(",", $val);
			$this->updateGlobal($name, $value);
			$this->arrVisitorMost	= $val;
		}
	}
	function Visitor()
	{//	get visitor
		return empty($this->arrVisitor[0])?0:$this->arrVisitor[0];
	}
	function Visitor_day()
	{//	get visitor
		return empty($this->arrVisitor[1])?0:$this->arrVisitor[1];
	}
	function Visitor_week()
	{//	get visitor
		return empty($this->arrVisitor[2])?0:$this->arrVisitor[2];
	}
	function Visitor_month()
	{//	get visitor
		return empty($this->arrVisitor[3])?0:$this->arrVisitor[3];
	}
	function Visitor_year()
	{//	get visitor
		return empty($this->arrVisitor[4])?0:$this->arrVisitor[4];
	}
	function Visitor_most_day()
	{//	get visitor
		return empty($this->arrVisitorMost[0])?0:$this->arrVisitorMost[0];		
	}
	function Visitor_most_week()
	{//	get visitor
		return empty($this->arrVisitorMost[1])?0:$this->arrVisitorMost[1];	
	}
	function Visitor_most_month()
	{//	get visitor
		return empty($this->arrVisitorMost[2])?0:$this->arrVisitorMost[2];
	}
	function Visitor_most_year()
	{//	get visitor
		return empty($this->arrVisitorMost[3])?0:$this->arrVisitorMost[3];
	}
	function getViewcount()
	{
		switch(date('G'))
		{
			case "0":	$viewcount	= 1; break;
			case "1":	$viewcount	= 1; break;
			case "2":	$viewcount	= 1; break;
			case "3":	$viewcount	= 1; break;
			case "4":	$viewcount	= 1; break;
			case "5":	$viewcount	= 1; break;
			case "6":	$viewcount	= 2; break;
			case "7":	$viewcount	= 2; break;
			case "8":	$viewcount	= 7; break;
			case "9":	$viewcount	= 9; break;
			case "10":	$viewcount	= 8; break;
			case "11":	$viewcount	= 9; break;
			case "12":	$viewcount	= 6; break;
			case "13":	$viewcount	= 5; break;
			case "14":	$viewcount	= 9; break;
			case "15":	$viewcount	= 8; break;
			case "16":	$viewcount	= 7; break;
			case "17":	$viewcount	= 5; break;
			case "18":	$viewcount	= 6; break;
			case "19":	$viewcount	= 4; break;
			case "20":	$viewcount	= 3; break;
			case "21":	$viewcount	= 2; break;
			case "22":	$viewcount	= 1; break;
			case "23":	$viewcount	= 1; break;
			case "24":	$viewcount	= 1; break;
			default	: $viewcount		= 1;break;
		}
		#$viewcount	= rand(1, $viewcount);
		$viewcount	= 1;
		return $viewcount;
	}
};
?>