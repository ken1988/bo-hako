<?php
// props, MVC style web application framework
//
// Copyright (C) Haruki Setoyama <props@planewave.org>
//
// This library is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or any later version.
//
// This library is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public
// License along with this library; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

// include all the source of props lib.
// $Id: include.php,v 1.5 2003/07/08 08:20:59 haruki Exp $

define('PROPS_LIB_DIR', dirname(__FILE__));
// echo PROPS_LIB_DIR;

class props
{
    /**
    * _trigger_error
    * @access private
    **/
    function _trigger_error($msg, $type)
    {
        trigger_error(get_class($this).'::'.$msg, $type);
    }
}

include_once (PROPS_LIB_DIR.'/Controller.php');
include_once (PROPS_LIB_DIR.'/Interfaces.php');
include_once (PROPS_LIB_DIR.'/Config.php');

include_once (PROPS_LIB_DIR.'/tyaml.php');
?>