<?php

class Painel_Model_CaGrupo extends Zend_Db_Table_Abstract
{
    
    public $id = null;
    protected $_name = 'grupo';
    
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
	
	public function getById($id)
    {
        $sql = $this->getAdapter()->select()->from('grupo')->where('id = ?',$id);
        $res = $this->getAdapter()->fetchRow($sql);
        if (isset($res['id']) && $res['id']) {
            return $res;
        } else {
            return false;
        }
    }
    
    private function _dataExists($id)
    {
        $sql = $this->getAdapter()->select()->from(array('r' => 'resource'), 'count(id) as qtd');
        $res = $this->getAdapter()->fetchRow($sql);
        if (isset($res['qtd']) && $res['qtd'] > 0) {
            return true;
        } else {
            return false;
        }
    }
}