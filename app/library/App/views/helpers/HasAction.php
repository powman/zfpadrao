<?php

class Zend_View_Helper_HasAction extends Zend_View_Helper_Abstract
{
	public function hasAction($ch_action)
	{
		$ch_modulo     = strtolower(Zend_Controller_Front::getInstance()->getRequest()->getModuleName());
		$ch_controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		$ch_action     = $ch_action;
		
		$session    = SessionManager::getInstance();
		$id_usuario = $session->get('id_usuario');
		
		$usuario = new Usuario();
		
		if (!$this->config->inProduction() && $usuario->isRoot($id_usuario)) {
			$this->verificarAcao($ch_modulo, $ch_controller, $ch_action);
		}
		
		$action     = new Action();
		$permission = $action->getActionPermissao($ch_modulo, $ch_controller, $ch_action);
		
		return $permission || $usuario->isRoot($id_usuario);
	}
	
	private function verificarAcao($ch_modulo, $ch_controller, $ch_action)
	{
		$modulo     = new Modulo();
		$controller = new Controller();
		$action     = new Action();
	
		$id_modulo     = $modulo->getIdModuloByCh($ch_modulo);
		$id_controller = $controller->getIdControllerByCh($ch_controller);
	
		$action->verificarAcao($id_modulo, $id_controller, $ch_action);
	}
}