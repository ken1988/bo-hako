<?
define('HTMLTEMPLATE', true);
define('HTMLTEMPLATE_HEADER', '<?php if(!defined("HTMLTEMPLATE")) exit(); ?>'."\n"); // <?
define('HTMLTEMPLATE_EXT', '.tmpl.php');
define('HTMLTEMPLATE_MKDIRS', false);
define('HTMLTEMPLATE_TIMESTAMP', false);
/**
* HTML template
* (C)Hiroshi Ayukawa.All rights reserved.
* License:BSD
* 2002.03.28   Ver. 1.3.1
* Revised by Haruki Setoyama <pwaf@planewave.org>
* @version $Id: htmltemplate.php,v 1.1 2003/07/08 08:20:28 haruki Exp $
* @access public
**/
class HtmlTemplate
{
    /**
    * Interprit a file on memory and output the result.
    * @access public
    * @param String $file Filename
    * @param Array $data a tree-like array
    * @return void
    */
	function t_Include($template_file, $val)
	{
	    HtmlTemplate::_parse_display($template_file, $val);
	}

    /**
    * Interprit a file on memory and require the result as a string.
    * @access public
    * @param String $file Filename
    * @param Array $data a tree-like array
    * @return void
    */
	function t_Buffer($template_file, $data)
	{
		ob_start();
		HtmlTemplate::_parse_display($template_file, $data);
		$ans = ob_get_contents();
		ob_end_clean();
		return $ans;
	}

    /**
    * Includes HTML file .
    * @access public
    * @param String $file filename
    * @param Array $data tree-like array
    * @param Array $dirname directoryname for .tmp file
    * @return void
    */
	function t_Include_file($template_file, $data, $compile_dir ='./')
	{
		HtmlTemplate::_compile_display($template_file, $data, $compile_dir);
	}

    /**
    * Require HTML file as a string.
    * @access public
    * @param String $file filename
    * @param Array $data tree-like array
    * @param Array $dirname directoryname for .tmp file
    * @return String
    */
	function t_Buffer_file($template_file, $data, $compile_dir ='./')
	{
		flush();
		ob_start();
		HtmlTemplate::_compile_display($template_file, $data, $compile_dir);
		$ans = ob_get_contents();
		ob_end_clean();
		return $ans;
	}

    /**
    * Compare the timestamp between .tmp & .html
    * bug fixed by STam on 04/29/2002.thanks.
    * @access private
    * @param String $file filename
    * @param Array $data tree-like array
    * @param Array $dirname directoryname for .tmp file
    * @return void
    */
	function _compile_display($template_file, $val, $compile_dir ='')
    {
        if($compile_dir == '') $compile_dir = dirname($template_file);
        $compiled_file = $compile_dir.'/'.basename($template_file).HTMLTEMPLATE_EXT;
		if(! file_exists($compiled_file))
		{
		    if(! file_exists($compile_dir) && HTMLTEMPLATE_MKDIRS)
            {
                if(! htmltemplate::_mkdirs($compile_dir))
                {
                    trigger_error('No directories for compiled files.', E_USER_WARNING);
                    htmltemplate::_parse_display($template_file);
                    return;
                }
            }
			if(htmltemplate::_compile_template_file($template_file, $compiled_file) === false) return;
		}
		elseif(HTMLTEMPLATE_TIMESTAMP && filemtime($template_file) > filemtime($compiled_file))
		{
            htmltemplate::_compile_template_file($template_file, $compiled_file);
		}
		include($compiled_file);
	}

    /**
    * Create directories for .tmp files
    * @access private
    * @param String $path path name
    * @param Array $mode mode of the dir.
    * @return void
    */
	function _mkdirs($path, $mode=0777)
	{
		if (strlen($path) == 0) return false;
		if (is_dir($path)) return true;
		if (is_file($path)) return false;
		if (! HtmlTemplate::_mkdirs(dirname($path), $mode)) return false;
		return mkdir($path, $mode);
	}

    /**
    * Create .tmp file
    * @access private
    * @param String $tmpfile filename
    * @return bool
    */
	function _compile_template_file($template_file, $compiled_file)
	{
	    $parsed = HtmlTemplate::_parse_template_file($template_file);
	    if($parsed === false) return false;
        if(! ($fp_w = fopen($compiled_file, 'w')))
        {
            trigger_error('Can not open a file for compiled data.', E_USER_WARNING);
            return false;
        }
        fwrite($fp_w, $parsed);
        ftruncate($fp_w, ftell($fp_w));
        fclose($fp_w);
        return true;
	}

	function _parse_template_file($template_file)
	{
        if(! ($fp_r = fopen($template_file, 'rb')))
        {
            trigger_error('Template file does not exists.', E_USER_WARNING);
            return false;
        }
        $source = fread($fp_r, filesize($template_file));
        fclose($fp_r);
        return HtmlTemplate::_parsesrc($source);
	}

	function _parse_display($template_file, $val)
	{
    	$code = htmltemplate::_parse_template_file($template_file);
        if($code === false) return;
        // $val is used in eval()
        echo eval('?>' .$code); // <?
    }

    /**
    * Parse HTML strings.
    * @access private
    * @param String $str HTML strings.
    * @return String
    */
	function _parsesrc($str)
	{
		// translate \r\n to \n
		$str = str_replace(array("\r\n", "\r"), array("\n", "\n"), $str);

		// interpretation of <!--{each }--><!--{/each}-->
		preg_match_all('/<!--\{each ([^\}]+)\}-->/i', $str, $k, PREG_PATTERN_ORDER);
		$kuri = $k[1];
		while(preg_match('/<!--\{each ([^\}]+)\}-->/i', $str, $match))
        {
			$ind = HtmlTemplate::_index_each($match[1], $kuri);
			$n = str_replace('/', '_', $match[1]);
			$str = str_replace($match[0],
    			'<?php for($cnt["'.$n.'"]=0;$cnt["'.$n.'"]<count($val'.$ind.');$cnt["'.$n.'"]++): ?>', // <?
    			$str);
		}
		$str = preg_replace('/<!--\{\/each\}-->/i'
		                    , '<?php endfor; ?>' // <?
		                    , $str);

		// interpretation of {val }
		while(preg_match('/\{val ([^\}]+)\}/i', $str, $match))
		{
			$ind = HtmlTemplate::_index_val($match[1], $kuri);
			$str = str_replace($match[0],
            			'<?php print nl2br(htmlspecialchars($val'.$ind.')); ?>', // <?
            			$str);
		}

		// interpretation of {rval }
		while(preg_match('/\{rval ([^\}]+)\}/', $str, $match))
		{
			$ind = HtmlTemplate::_index_val($match[1], $kuri);
			$str = str_replace($match[0],
            			'<?php print $val'.$ind.'; ?>',    // <?
            			$str);
		}

		// interpretation of <!--{def }--><!--{/def}-->
		while(preg_match('/<!--\{def ([^\}]+)\}-->/i', $str, $match))
		{
		    $ind = HtmlTemplate::_index_each($match[1], $kuri);
			$str = str_replace($match[0],
    			'<?php if((!is_array($val'.$ind.') && $val'.$ind.'!="") or (is_array($val'.$ind.') && count($val'.$ind.')>0)) : ?>', // <?
    			$str);
		}
		$str = preg_replace('/<!--\{\/def\}-->/i',
                        	'<?php endif; ?>', // <?
                        	$str);
		return HTMLTEMPLATE_HEADER.$str;
	}

    function _index($names, $each_vals)
    {
        $name_stack = array();
        $index = '';
        foreach($names as $name)
        {
            array_push($name_stack, $name);
            if(in_array(join('/', $name_stack), $each_vals))
            {
                $index .= '["'.$name.'"][$cnt["'.join('_', $name_stack).'"]]';
            }
            else
            {
                $index .= '["'.$name.'"]';
            }
        }
        return $index;
    }

    function _index_each($val_name, $each_vals)
    {
        $names = split('/', $val_name);
        $last = array_pop($names);
        return HtmlTemplate::_index($names, $each_vals).'["'.$last.'"]';
    }

    function _index_val($val_name, $each_vals)
    {
        $names = split('/', $val_name);
        return HtmlTemplate::_index($names, $each_vals);
    }
}
?>