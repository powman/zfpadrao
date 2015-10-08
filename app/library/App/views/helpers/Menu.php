<?php
class Zend_View_Helper_Menu extends Zend_View_Helper_Abstract
{
    public function menu()
    {
        $view = Zend_Layout::getMvcInstance()->getView();
        $registy = Zend_Auth::getInstance()->getIdentity();
        $menu = $this->_getMenu();
        $modulos = $this->_getResource($registy->role_id,'modulo');
        $controles = $this->_getResource($registy->role_id,'resource');
        
        $array = $this->_montaArray($menu, $modulos, $controles);
        $i = 0;
        $html = '';
        foreach ($array as $val) {
            $html  .= '<li class="dropdown">';
            $html  .= '    <a tabindex="'.$i.'" data-toggle="dropdown">';
            $html  .= '        <i class="fa fa-'.$val['awsome'].'"></i>';
            $html  .= '            '.strtoupper($val['nome']).'';
            $html  .= '        <span class="caret"></span>';
            $html  .= '    </a>';
            $html  .= '    <ul class="dropdown-menu">';
            foreach ($val['submenu'] as $value){
                $html  .= '    <li>';
                $html  .= '        <a href="'.$view->baseUrl().'/'.$val['modulo'].'/'.$value['ctrl'].'/'.$value['action'].'">';
                $html  .= '            '.$value['nome'].'';
                $html  .= '        </a>';
                $html  .= '    </li>';
            }
            $html  .= '    </ul>';
            $html  .= '</li>';
            $i++;
        }
        return $html;
    }
    
    private function _montaArray($menu, $modulos, $controles)
    {
        $i = 0;
        $aMenu = array();
        foreach ($menu as $valor){
            if($this->in_array_r($valor['modulo'], $modulos)){
                $aMenu[$i]['id'] = $valor['id'];
                $aMenu[$i]['nome'] = $valor['nome'];
                $aMenu[$i]['awsome'] = $valor['awsome'];
                $aMenu[$i]['modulo'] = $valor['modulo'];
        
                $submenu = $this->_getSubMenu($valor['id']);
        
                $j = 0;
                foreach ($submenu as $valorSubmenu){
                    if($this->in_array_r($valorSubmenu['ctrl'], $controles)) {
                        $aMenu[$i]['submenu'][$j]['id'] = $valorSubmenu['id'];
                        $aMenu[$i]['submenu'][$j]['nome'] = $valorSubmenu['nome'];
                        $aMenu[$i]['submenu'][$j]['ctrl'] = $valorSubmenu['ctrl'];
                        $aMenu[$i]['submenu'][$j]['action'] = $valorSubmenu['action'];
                        $aMenu[$i]['submenu'][$j]['modulo_id'] = $valorSubmenu['modulo_id'];
                        $j++;
                    }
                }
                $i++;
            }
        }
        
        foreach ($aMenu as $key => $val) {
            if (!isset($aMenu[$key]['submenu'])) {
                unset($aMenu[$key]);
            }
        }
        
        return $aMenu;
    }
    
    private static function _getMenu() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql  = 'select ';
		$sql .= '	m.id, ';
		$sql .= '	m.nome, ';
		$sql .= '	m.awsome, ';
		$sql .= '	m.modulo ';
		$sql .= 'from ';
		$sql .= '	modulo m ';
		$sql .= 'where status = 1 ';
		$sql .= 'order by ';
		$sql .= '	m.ordem asc ';
        
        $result = $db->fetchAll($sql);
        
        return $result;
    }
    
    private static function _getResource($role_id, $retorno) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql  = 'select ';
        $sql .= '	r.'.$retorno.' ';
        $sql .= 'from ';
        $sql .= '	resource r ';
        $sql .= '	left join permissao p on p.resource_id = r.id ';
        $sql .= '	left join usuario u on u.role_id = p.role_id ';
        $sql .= 'where p.role_id = '.$role_id.' ';
        $sql .= 'group by r.'.$retorno.' ';
        
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
    
    function in_array_r($needle, $haystack) {
        $found = false;
        foreach ($haystack as $item) {
            if ($item === $needle) {
                $found = true;
                break;
            } elseif (is_array($item)) {
                $found = $this->in_array_r($needle, $item);
                if($found) {
                    break;
                }
            }
        }
        return $found;
    }
}