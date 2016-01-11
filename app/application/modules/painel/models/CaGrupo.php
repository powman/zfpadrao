<?php

class Painel_Model_CaGrupo extends App_Model_Default
{
  
    protected $_name = 'sca_grupo';
    public $id = null;
    protected $primarykey = "id_grupo";
	
	public function getById($id)
    {
        $sql = $this->getAdapter()->select()->from($this->_name)->where('id_grupo = ?',$id);
        $res = $this->getAdapter()->fetchRow($sql);
        if (isset($res['id_grupo']) && $res['id_grupo']) {
            return $res;
        } else {
            return false;
        }
    }
}