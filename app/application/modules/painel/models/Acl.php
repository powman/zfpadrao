<?php

class Painel_Model_Acl extends Zend_Db_Table_Abstract
{
    
   public static function resourceValid($request){
        // Verifica se o controller e valido
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
        if (!$dispatcher->isDispatchable($request)) {
            return false;
        }
        // Verifica se a action e valida
        $front      = Zend_Controller_Front::getInstance();
        $dispatcher = $front->getDispatcher();
        $controllerClass = $dispatcher->getControllerClass($request);
        $controllerclassName = $dispatcher->loadClass($controllerClass);
        $actionName = $dispatcher->getActionMethod($request);
        $controllerObject = new ReflectionClass($controllerclassName);      
        if(!$controllerObject->hasMethod($actionName)){
            return false;   
        }       
        return true;
    }
   
   public static function resourceExists($controller=null,$action=null){
        if(!$controller || !$action) throw new Exception("Error resourceExists(), the controller/action não existe");
        //$result=self::getBySQLCondition(array('controller=?'=>$controller,'action=?'=>$action));
        if(count($result)){
            return true;
        }
        return false;
    }
}