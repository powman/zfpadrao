<?php
class Zend_View_Helper_LinkForm extends Zend_View_Helper_Abstract
{
    public function linkForm(array $params = array())
	{
		
		$chModulo     = strtolower(Zend_Controller_Front::getInstance()->getRequest()->getModuleName());
		$chController = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		
		$params = implode('/', $params);
		$params = $params? $params . '/': '';
		
		return $this->view->baseUrl($chModulo . '/' . $chController . '/form/' . $params);
		
	}
}