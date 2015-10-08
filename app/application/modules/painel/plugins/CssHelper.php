<?php 
class Painel_Plugin_CssHelper extends Zend_Controller_Plugin_Abstract
{   
    public static function CssHelper() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $file_uri = $request->getModuleName().'/media/css/' . $request->getControllerName() . '/' . $request->getActionName() . '.css';

        if (file_exists($file_uri)) {
            return $file_uri;
        }
    }
}