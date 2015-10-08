<?php
class App_Plugin_Controle extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        
        $front = Zend_Controller_Front::getInstance();
        $acl = array();
        foreach ($front->getControllerDirectory() as $module => $path) {
        
            foreach (scandir($path) as $file) {
        
                if (strstr($file, "Controller.php") !== false) {
        
                    include_once $path . DIRECTORY_SEPARATOR . $file;
        
                    foreach (get_declared_classes() as $class) {
        
                        if (is_subclass_of($class, 'Zend_Controller_Action')) {
        
                            $controller = strtolower(substr($class, 0, strpos($class, "Controller")));
                            $actions = array();
        
                            foreach (get_class_methods($class) as $action) {
        
                                if (strstr($action, "Action") !== false) {
                                    $actions[] = $action;
                                }
                            }
                        }
                    }
                    
                    $acl[$module][$controller] = $actions;
                }
            }
        }

        print_r($acl);
    }
}