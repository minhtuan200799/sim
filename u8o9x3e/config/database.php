<?php if (!defined("ROOT")) exit("No direct script access allowed");
/***********************************************************************/
/*                                                                     */
/*   Copyright (C) 2015. All rights reserved                           */
/*   Author     : Roca Chien, rocachien@yahoo.com                      */
/*   License    : http://nhanhgon.vn                                   */
/*                                                                     */
/*   Created    : 28-12-2014 15:30:05.                                 */
/*   Modified   : 28-12-2014 15:30:05.                                 */
/*                                                                     */
/***********************************************************************/

//**********Định nghĩa các thành phần của web*****************//

$config['dbview']['database']	= $_SERVER["DBV_DATABASE"];
$config['dbview']['username']	= $_SERVER["DBV_USERNAME"];
$config['dbview']['password']	= $_SERVER["DBV_PASSWORD"];

$config['dbview']['hostname']	= "localhost";
$config['dbview']['pconnect']	= TRUE;

?>