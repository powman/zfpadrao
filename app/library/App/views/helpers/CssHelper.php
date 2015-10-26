<?php 
class Zend_View_Helper_CssHelper extends Zend_View_Helper_Abstract
{   
    public function CssHelper() {
    	
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $file_uri = $request->getModuleName().'/media/css/' . $request->getControllerName() . '/' . $request->getActionName() . '.css';
        if (file_exists($file_uri)) {
            return $file_uri;
        }
    }
}