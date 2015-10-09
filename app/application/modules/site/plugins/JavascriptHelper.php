<?php 
class Site_Plugin_JavascriptHelper extends Zend_Controller_Plugin_Abstract
{
    public static function JsHelper() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $file_uri = $request->getModuleName().'/media/js/' . $request->getControllerName() . '/' . $request->getActionName() . '.js';

        if (file_exists($file_uri)) {
            return $file_uri;
        }
    }
}