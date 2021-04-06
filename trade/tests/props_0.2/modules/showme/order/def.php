<?php
// $Id: def.php,v 1.1 2003/07/08 08:28:37 haruki Exp $

class propsOrder_def extends propsOrder
{
    var $val;
    var $err;

    function entry(&$request, $control)
    {
        $this->val['orig'] = $request->getParameter('orig');
        if($request->getParameter('str') !== false)
            $this->val['str'] = $request->getParameter('str');
    }

    function validate()
    {
        if(!isset($this->val['str']) || strlen($this->getValue('str')) < 3)
        {
            $this->err['str'] = 'You must at least input three letters!';
            return false;
        }
        return true;
    }

    function finished()
    {

    }

    function getValue($name)
    {
        return $this->val[$name];
    }

    function getValueNames()
    {
        return array_keys($this->val);
    }

    function getError($name)
    {
        return $this->err[$name];
    }

    function getErrorNames()
    {
        return array_keys($this->err);
    }



}

?>