<?php
class Zend_View_Helper_Titulo extends Zend_View_Helper_Abstract
{
	
    public function titulo(Painel_Model_CgModuloSubmenu $model)
    {
        
        $controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		$action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		$res = $model->getTituloByCtrlAction($controller,$action);
		if($res){
			return '<i class="fa fa-'.$res['awsome'].'"></i> '.$res['nome'];
		}
    }
} 