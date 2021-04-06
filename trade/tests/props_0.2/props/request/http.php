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
// $Id: http.php,v 1.2 2003/07/08 08:20:59 haruki Exp $
/**
* props, MVC style web application framework
*
* @author Haruki Setoyama  <props@planewave.org>
* @package props
**/
/**
* propsRequest_html
*
* @access public
**/
class propsRequest_http extends propsRequest
{

    var $_magic_quotes;

    function propsRequest()
    {
        $this->_magic_quotes = get_magic_quotes_gpc();
    }

    /**
    * getRawParameter
    * @access public
    * @param string $name
    * @param string $type   G,P,F,C for GET, POST, FILES, COOKIE
    * @return mixed
    **/
    function getParameter($name, $type='GP')
    {
        for($i=strlen($type)-1; $i>=0; $i--)
        {
            switch($type[$i])
            {
            case 'G':
                if(isset($_GET[$name]))
                    return $this->_stripslashes($_GET[$name]);
                break;
            case 'P':
                if(isset($_POST[$name]))
                    return $this->_stripslashes($_POST[$name]);
                break;
            case 'F':
                if(isset($_FILES[$name]))
                    return $this->_stripslashes($_FILES[$name]);
                break;
            case 'C':
                 if(isset($_COOKIE[$name]))
                    return $this->_stripslashes($_COOKIE[$name]);
                break;
            default:
                // do nothing
            }
        }

        return false;
    }

    /**
    * get names of the user request values
    * @access public
    * @param string $type   G,P,F,C for GET, POST, FILES, COOKIE
    * @return array
    */
    function getParameterNames($type='GP')
    {
        $ret = array();
        for($i=strlen($type)-1; $i>=0; $i--)
        {
            switch($type[$i]){
            case 'G':
                $ret = array_merge($ret, array_keys($_GET));
                break;
            case 'P':
                $ret = array_merge($ret, array_keys($_POST));
                break;
            case 'F':
                $ret = array_merge($ret, array_keys($_FILES));
                break;
            case 'C':
                $ret = array_merge($ret, array_keys($_COOKIE));
                break;
            default:
                // do nothing
            }
        }
        return $ret;
    }

    /////

    function getCookie($name)
    {
        if(isset($_COOKIE[$name]))
            return $this->_stripslashes($_COOKIE[$name]);
        else
            return false;
        break;
    }

    function getEnvVar($name)
    {
       if(isset($_ENV[$name]))
            return $_ENV[$name];
        else
            return false;
    }

    function getServerVar($name)
    {
       if(isset($_SERVER[$name]))
            return $_SERVER[$name];
        else
            return false;
    }

    ///////////////// TODO: below

    function getUri($site_root='')
    {
        if($site_root == ''){
            return $this->getEnvVar('HTTP_HOST').$this->getEnvVar('REQUEST_URI');
        }else{
            $uri = $this->getEnvVar('HTTP_HOST').$this->getEnvVar('REQUEST_URI');
            if(preg_match('/^'.$site_root.'/i', $uri)){
                return preg_replace('/^'.$site_root.'/i', '', $uri);
            }else{
                return false;
            }
        }
    }

    function isSecure()
    {
        return ($this->server_vars['HTTPS'] == 'on');
    }

    /////

    function _stripslashes($str)
    {
        if($this->_magic_quotes)
        {
            if (is_array($str)){
                $this->_stripslashes_array_($str);
                return $str;
            }else{
                return stripslashes($str);
            }
        }
        else
        {
            return $str;
        }
     }

     function _stripslashes_array_(&$array)
     {
        while (list($key) = each($array)) {
            if (is_array($array[$key])) {
                $this->_stripslashes_array_($array[$key]);
            } else {
                $array[$key] = stripslashes($array[$key]);
            }
        }
     }
}
?>