<?php
abstract class NafuCodeClient
{
    protected $baseUri;

    /**
     *
     * Enter description here ...
     * @param unknown_type $name
     *
     * @return NafuCodeClient
     */
    public static function getClient($name)
    {
        $path = dirname(__FILE__).'/'.$name.'/'.$name.'.php';

        if( ! file_exists($path))
        {
            $msg = '';
            $msg .= 'Client not found';
            $msg .=(DBG_NAFUCODE) ? ': '.$name.' --- '.$path : '';

            throw new Exception($msg);
        }

        require_once $path;

        $className = 'NafuCode'.ucfirst($name).'Client';

        if( ! class_exists($className))
        throw new Exception(sprintf('Required class %s not found', $className));

        return new $className;
    }//function

    protected abstract function checkout($path, $projectData = null);//function
}//class
