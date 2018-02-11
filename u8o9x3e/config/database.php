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

if($_SERVER['HTTP_HOST'] == "localhost")
{
    $config['dbview']['username']		= "nhanhgon";
    $config['dbview']['database']		= "nhanhgon";
    $config['dbview']['password']		= "nhanhgon";
}else
{
    $config['dbview']['username']	= "raovat";
    $config['dbview']['database']	= "raovat";
    $config['dbview']['password']	= "raovat";
}

$config['dbview']['hostname']	= "localhost";
$config['dbview']['pconnect']	= TRUE;

?>