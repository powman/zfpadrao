<?php

class Painel_Model_CgModuloSubmenu extends Zend_Db_Table_Abstract
{
    
    public $id = null;
    protected $_name = 'modulo_menu_sub';
    
    public function save(array $data)
    {
        $id = isset($data['id']) ? $data['id'] : '';
        if ($this->_dataExists($id) && $id) {
            unset($data['id']);
            $result = $this->update($data, "id = {$id}");
            return  $result === 0 || $result === true || $result >= 1 ? true : false;
        } else {
            $this->insert($data);
            return $this->getAdapter()->lastInsertId();
        }
    }
    
    private function _dataExists($id)
    {
        $sql = $this->getAdapter()->select()->from(array('m' => 'modulo_menu_sub'), 'count(id) as qtd');
        $res = $this->getAdapter()->fetchRow($sql);
        if (isset($res['qtd']) && $res['qtd'] > 0) {
            return true;
        } else {
            return false;
        }
    }
	
	public function getTituloByCtrlAction($ctrl,$action)
    {
        $sql = $this->getAdapter()->select()->from(array('m' => 'modulo_menu_sub'));
		$sql->where('m.ctrl = ? ',$ctrl);
		$sql->where('m.action = ? ',$action);
		$sql->joinLeft('modulo as mo','m.modulo_id = mo.id',array('mo.awsome as awsome'));
        $res = $this->getAdapter()->fetchRow($sql);
        //if (isset($res['qtd']) && $res['qtd'] > 0) {
            return $res;
        //} else {
            //return false;
        //}
    }
}