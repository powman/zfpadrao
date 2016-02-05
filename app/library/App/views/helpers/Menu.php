<?php
class Zend_View_Helper_Menu extends Zend_View_Helper_Abstract
{
    public function menu()
    {
        $view = Zend_Layout::getMvcInstance()->getView();
        $registy = Zend_Auth::getInstance()->getIdentity();
        $modulo = $this->_getModulo();
        $params = array("grupo_id" => $registy->id_grupo,'is_root' => $registy->is_root);
        $menu = $this->_getMenu($params);
        
        $i = 0;
        $html = '';
        foreach ($modulo as $val) {
            if(count($menu)){
                $html  .= '<li>';
                $html  .= '    <a href="javascript:;" class="black" >';
                $html  .= '        <i class="fa fa-'.$val['icone'].'"></i>';
                $html  .= '            '.$val['nome'].'';
                $html  .= '        <span class="caret black"></span>';
                $html  .= '    </a>';
                $html  .= '    <ul class="nav nav-second-level">';
                foreach ($menu as $value){
                    $html  .= '    <li>';
                    $html  .= '        <a class="black '.(strtolower(Zend_Controller_Front::getInstance()->getRequest()->getActionName()) == $value['action'] && strtolower(Zend_Controller_Front::getInstance()->getRequest()->getControllerName()) == $value['controller']  ? "active" : "").'" href="'.$view->baseUrl().'/'.$value['controller'].'/'.$value['action'].'">';
                    $html  .= '            '.$value['nome'].'';
                    $html  .= '        </a>';
                    $html  .= '    </li>';
                }
                $html  .= '    </ul>';
                $html  .= '</li>';
            }
            $i++;
        }
        return $html;
    }
    
    private static function _getModulo() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql  = 'select ';
		$sql .= '	m.id, ';
		$sql .= '	m.nome, ';
		$sql .= '	m.icone ';
		$sql .= 'from ';
		$sql .= '	modulo m ';
		$sql .= 'where status = 1 ';
		$sql .= 'order by ';
		$sql .= '	m.ordem asc ';
        
        $result = $db->fetchAll($sql);
        
        return $result;
    }
    
    private static function _getMenu($condicao = array()) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql  = 'select ';
        $sql .= '	m.modulo_id, ';
        $sql .= '	m.acl_id, ';
        $sql .= '	m.nome, ';
        $sql .= '	a.controller, ';
        $sql .= '	a.action, ';
        $sql .= '	m.ordem, ';
        $sql .= '	m.status ';
        $sql .= 'from ';
        $sql .= '	menu m ';
        $sql .= '	left join acl a on m.acl_id = a.id ';
        $sql .= '	left join acl_to_grupo ag on a.id = ag.acl_id ';
		$sql .= 'where m.status = 1 ';
		if(isset($condicao["modulo_id"]) && $condicao["modulo_id"])
		    $sql .= 'and m.modulo_id = '.$condicao["modulo_id"].' ';
		if(isset($condicao["grupo_id"]) && $condicao["grupo_id"] && !$condicao['is_root'])
		    $sql .= 'and ag.grupo_id = '.$condicao["grupo_id"].' ';
        
        $result = $db->fetchAll($sql);
    
        return $result;
    }
    
    private static function _getSubMenu($modulo_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql  = 'select ';
		$sql .= '	ms.id, ';
		$sql .= '	ms.nome, ';
		$sql .= '	ms.ctrl, ';
		$sql .= '	ms.action, ';
		$sql .= '	ms.modulo_id ';
		$sql .= 'from ';
		$sql .= '	 modulo_menu_sub ms ';
		$sql .= 'where ';
		$sql .= '    ms.modulo_id = '.$modulo_id.' ' ;
		$sql .= '    and ms.status = 1 ' ;
		$sql .= 'order by ';
		$sql .= '	ms.ordem asc ';
		
        $result = $db->fetchAll($sql);
        
        return $result;
    }
}