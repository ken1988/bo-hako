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
//
// $Id: Interfaces.php,v 1.7 2003/07/08 08:20:59 haruki Exp $
/**
* props, MVC style web application framework
*
* @author Haruki Setoyama  <props@planewave.org>
* @package props
* @version 0.2
**/
/**
* propsAction
*
* @access interface
**/
class propsGate extends props
{
    /**
    * execute action
    * @access public
    * @return bool
    **/
    function check($control)
    {
        return true;
    }
}

/**
* propsAction
*
* @access interface
**/
class propsOrder extends props
{
    /**
    * process
    * @access public
    * @return mixed
    **/
    function entry(&$request, $control)
    {
    }

    function validate()
    {
        return true;
    }

    function finished()
    {

    }

    function getValue($name)
    {
        return false;
    }

    function getValueNames()
    {
        return array();
    }

    function getError($name)
    {
        return false;
    }

    function getErrorNames()
    {
        return array();
    }
}

/**
* propsAction
*
* @access interface
**/
class propsAction extends props
{
    /**
    * execute action
    * @access public
    * @return string
    **/
    function execute(&$order, &$response, $control)
    {
    }
}

/**
* propsUnitView
*
* @access interface
**/
class propsView extends props
{
    /**
    * make view form $input , namely propsAction output.
    * @access public
    * @return bool
    **/
    function display(&$order, $response, $control, $state)
    {
    }
}
/**
* propsRequest
*
* @access public
**/
class propsRequest extends props
{
    /**
    * get a user request value
    * @access public
    * @param string $name
    * @param string $type
    * @return mixed
    */
    function getParameter($name, $type='')
    {
    }

    function getParameterNames($type='')
    {
        return array();
    }
}
?>