<?php 
class Zend_View_Helper_JsHelper extends Zend_View_Helper_Abstract
{
    public static function JsHelper() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        
        $file_uri = $request->getModuleName().'/media/js/' . $request->getControllerName() . '/' . $request->getActionName() . '.js';
        if (file_exists($file_uri)) {
            return $file_uri;
        }
    }
}