<?php

class Painel_Model_CsTemplateOpcao extends Zend_Db_Table_Abstract
{
    
    public $id = null;
    protected $_name = 'template_opcao';
    
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
        $sql = $this->getAdapter()->select()->from(array('t' => 'template_opcao'), 'count(id) as qtd');
        $res = $this->getAdapter()->fetchRow($sql);
        if (isset($res['qtd']) && $res['qtd'] > 0) {
            return true;
        } else {
            return false;
        }
    }
}