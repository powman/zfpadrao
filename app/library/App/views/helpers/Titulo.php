<?php
class Zend_View_Helper_Titulo extends Zend_View_Helper_Abstract
{
    public function Titulo()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$chAction     = strtolower(Zend_Controller_Front::getInstance()->getRequest()->getActionName());
		$chController = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		
        $sql  = 'select ';
		$sql .= '	m.nome, ';
		$sql .= '	mo.nome as modulo, ';
		$sql .= '	mo.icone ';
		$sql .= 'from ';
		$sql .= '	menu m ';
		$sql .= '	left join acl a on a.id = m.acl_id ';
		$sql .= '	left join modulo mo on mo.id = m.modulo_id ';
		$sql .= 'where m.status = 1 ';
		$sql .= 'and a.controller = "'.$chController.'" ';
		$sql .= 'and a.action = "'.$chAction.'" ';
		
		$result = $db->fetchRow($sql);
		
		return $result;
		
	}
}