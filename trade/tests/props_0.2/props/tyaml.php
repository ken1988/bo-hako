<?php
class tyaml
{
    function load($str)
    {
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("\r", "\n", $str);

        $lines = explode("\n", $str);

        return $this->_perse($lines);
    }

    function loadFile($path)
    {
        $data = file($path);
        if($data === false)
        {
            trigger_error('tyaml::Could not open file ['.$path.']', E_USER_WARNING);
            return false;
        }
        return $this->_perse($lines);
    }

    function _perse($lines)
    {
        $line_num = count($lines);
        $prev = 0;
        $element = array();
        $sequence[0] = -1;
        $mapkey = array();

        $indent_stack = array(0);

        for($i=0; $i<$line_num ; $i++)
        {
            // get indent level and comment
            if(!preg_match('/^((?:\040|-\040)*)([^#]+)?(#.*)?$/', $lines[$i].' ', $part))
            {
                trigger_error('tyaml::invalid line. ('.$lines[$i].')', E_USER_WARNING);
                continue;
            }

            $indent = strlen($part[1]);
            $body = rtrim($part[2]);

            // empty line
            if($body == '') continue;

            if($indent > $prev)
            {   // nest
                array_push($indent_stack, $indent);
                $sequence[$indent] = -1;
                $prev = $indent;
            }
            elseif($indent < $prev)
            {
                $deep = array_pop($indent_stack);
                while($deep > $indent)
                {
                    $shallow = array_pop($indent_stack);
                    if($shallow < $indent)
                    {
                        trigger_error('tyaml::invalid indent. ('.$lines[$i].')',E_USER_NOTICE);
                        array_push($indent_stack, $shallow);
                        $indent = $deep;
                        break;
                    }

                    if(isset($mapkey[$shallow]))
                    {
                        $element[$shallow][$mapkey[$shallow]] = $element[$deep];
                        $mapkey[$shallow] = null;
                    }
                    elseif($sequence[$shallow] >= 0)
                    {
                        $element[$shallow][$sequence[$shallow]] = $element[$deep];
                    }
                    else
                    {
                        $element[$shallow] = $element[$deep];
                    }
                    unset($element[$deep]);
                    $deep = $shallow;
                }

                array_push($indent_stack, $indent);
                $prev = $indent;
            }


            // seq number
            if(strrpos($part[1], '-') !== false)
                $sequence[$indent]++;
            elseif($part[2] == '')
                continue;

            // set value
            if(preg_match('/^([^:]+):(?:\040+(.*))?$/', $body, $match))
            {   // mapping
                $value = $this->_type_convert($match[2]);

                if($sequence[$indent] >= 0)
                    $element[$indent][$sequence[$indent]][$match[1]] = $value;
                else
                    $element[$indent][$match[1]] = $value;

                if($value == '')
                    $mapkey[$indent] = $match[1];

            }
            else
            {   // scalar
                if($sequence[$indent] >= 0)
                {
                    $element[$indent][$sequence[$indent]]
                        = $this->_type_convert($body);
                }
                else
                {
                    $element[$indent]
                        = $this->_type_convert($body);
                }
            }
        }

        $deep = array_pop($indent_stack);
        while($deep > 0)
        {
            $shallow = array_pop($indent_stack);

            if(isset($mapkey[$shallow]))
            {
                $element[$shallow][$mapkey[$shallow]] = $element[$deep];
            }
            elseif($sequence[$shallow] >= 0)
            {
                $element[$shallow][$sequence[$shallow]] = $element[$deep];
            }
            else
            {
                $element[$shallow] = $element[$deep];
            }
            $deep = $shallow;
        }
        return $element[0];
    }

    function _type_convert($value)
    {
        if($value == 'true') return true;
        if($value == 'false') return false;
        if($value == '~') return null;
        return $value;
    }
}

?>