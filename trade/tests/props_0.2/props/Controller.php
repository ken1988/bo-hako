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
// $Id: Controller.php,v 1.7 2003/07/08 08:20:58 haruki Exp $
/**
* props, MVC style web application framework
*
* @author Haruki Setoyama  <props@planewave.org>
* @package props
**/
/**
* propsController, main Controller object
*
* @access public
**/
class propsController extends props
{
    /**
    * constructor
    **/
    function propsController()
    {
    }

    var $_module;
    /**
    * setCurrentModule()
    * @access public
    **/
    function setCurrentModule($module_dir)
    {
        $this->_module = $module_dir;
    }

    var $_case = '';
    function setCaseTitle($title)
    {
        $this->_case = $title;
    }

    var $_request;
    function setRequestType($request)
    {
        include PROPS_REQUEST_DIR.'/'.$request.PROPS_PHP_EXT;
        $class = 'propsRequest_'.$request;
        $this->_request = new $class;
    }

    /**
    * main processer
    *
    * @access public
    **/
    function process($op, $practice, $flow)
    {
        $operation = $this->getOperation($op, $flow);
        if($operation === false)
        {
            header("HTTP/1.0 404 Not Found");
            return;
        }
        if(! isset($op[$operation]))
        {
            $this->_trigger_error(
                'process()::invalid operation.',
                E_USER_NOTICE
            );
            header("HTTP/1.0 404 Not Found");
            return;
        }

        $p = $this->processOperation($op[$operation]);
        if($p === false) return;

        $this->processPractice($practice[$p]);
    }

    function getOperation($op, $flow)
    {
        if(defined('PROPS_OPERATION'))
            return PROPS_OPERATION;
        if($this->_request->getParameter(PROPS_OPERATION_PARAMETER) !== false)
            return $this->_request->getParameter(PROPS_OPERATION_PARAMETER);

        if(defined('PROPS_STATE'))
            $state = PROPS_STATE;
        elseif($this->_request->getParameter(PROPS_STATE_PARAMETER) !== false)
            $state = $this->_request->getParameter(PROPS_STATE_PARAMETER);
        elseif(isset($_SESSION['props_state_'.$this->_module.'_'.$this->_case]))
            $state = $_SESSION['props_state_'.$this->_module.'_'.$this->_case];
        else
        {
            reset($op);
            return key($op);
        }

        if(defined('PROPS_EVENT'))
            $event = PROPS_EVENT;
        elseif($this->_request->getParameter(PROPS_EVENT_PARAMETER) !== false)
            $event = $this->_request->getParameter(PROPS_EVENT_PARAMETER);
        else
        {
            $this->_trigger_error(
                'getOperation()::event not specified.',
                E_USER_NOTICE
            );
            return false;
        }

        if(isset($flow[$state][$event]))
            return $flow[$state][$event];
        else
        {
            $this->_trigger_error(
                'getOperation()::invalid state or event',
                E_USER_NOTICE
            );
            return false;
        }

    }

    /**
    * operation processer
    * @param string $operation
    * @access public
    * @return bool
    **/
    function processOperation($operation)
    {
        // gate
        if(isset($operation['gate']['title']))
        {
            $ret = $this->_load($operation['gate']['title'], PROPS_GATE_DIRNAME);
            if(! $ret)
                return false;

            $class = 'propsGate_'.$operation['gate']['title'];
            $gate = new $class;
            $ret = $gate->check($operation['gate']['control']);

            if(! $ret)
            {
                return $operation['gate']['out'];
            }
        }

        // order
        if(isset($operation['order']['title']))
        {
            $ret = $this->_load($operation['order']['title'], PROPS_ORDER_DIRNAME);
            if(! $ret)
                return false;

            $class = 'propsOrder_'.$operation['order']['title'];
            $this->order = new $class;
            $this->order->entry($this->_request, $operation['order']['control']);

            if(isset($operation['order']['mistake']))
            {
                if(! $this->order->validate())
                {
                    return $operation['order']['mistake'];
                }
            }
        }

        // practice
        return $operation['practice']['title'];
    }


    function processPractice($practice)
    {
        // action
        if(isset($practice['action']['title']))
        {
            $ret = $this->_load($practice['action']['title'], PROPS_ACTION_DIRNAME);
            if(! $ret)
               return false;

            $class = 'propsAction_'.$practice['action']['title'];
            $action = new $class;
            $response = null;
            $ret = $action->execute($this->order, $response, $practice['action']['control']);
        }
        else
        {
            $response = null;
        }

        // view
        if(isset($practice['view']['title']))
        {
            $view_arr = $practice['view'];
        }
        elseif(isset($practice['views'][$ret]['title']))
        {
            $view_arr = $practice['view'][$ret];
        }
        else
        {
            $this->_trigger_error(
                'processPractice()::invalid view ('.$ret.')',
                E_USER_NOTICE
            );
            return false;
        }

        $ret = $this->_load($view_arr['title'], PROPS_VIEW_DIRNAME);
        if(! $ret)
            return false;

        $class = 'propsView_'.$view_arr['title'];
        $view = new $class;
        $view->display($this->order, $response,
                         $view_arr['control'],
                         $view_arr['state']);
        if(isset($view_arr['state']))
            $_SESSION['props_state_'.$this->_module.'_'.$this->_case] = $view_arr['state'];
        else
            unset($_SESSION['props_state_'.$this->_module.'_'.$this->_case]);

        return true;
    }

//////////////// private functions

    /**
    * _load
    * @access private
    * @return string    class name
    **/
    function _load($name, $dir)
    {
        $path = PROPS_MODULE_ROOT_DIR.
                '/'.$this->_module.
                '/'.$dir.
                '/'.$name.PROPS_PHP_EXT;

        if(!file_exists($path))
        {
            $this->_trigger_error(
                '_load_class()::file not exists. ('.$path.')',
                E_USER_WARNING
            );
            return false;
        }

        include_once $path;
        return true;
    }
}
?>