<?php

class Painel_Model_CaUsuario extends Zend_Db_Table_Abstract
{
    
    public $id = null;
    protected $_name = 'usuario';
	
	public function listarTodos($arraySearch = array(), $limit = null, $offset = null, &$msg = '')
	{
		// SQL para buscar os registros
		$sql = $this->getAdapter()->select()
		->from(array('u' => $this->_name), array('id_usuario', 'email','img','role_id'));
	
		if (isset($arraySearch['nome']) && $arraySearch['nome']) {
			$sql->where('u.nomea LIKE ?', "%{$arraySearch['nome']}%");
		}
		
		// SQL para buscar a quantidade de páginas existentes
		$sqlCount = $this->getAdapter()->query($sql->__toString());
	
		$sql->order('u.id_usuario')->limit($limit, $offset);
		$stmt = $sql->query();
 	
		$return['res']   = $stmt->fetchAll();
		$return['pages'] = $sqlCount->rowCount();
	
		return $return;
	}
    
    public function save(array $data)
    {
        $id = $data['id'];
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
        $sql = $this->getAdapter()->select()->from(array('u' => 'usuario'), 'count(id) as qtd');
        $res = $this->getAdapter()->fetchRow($sql);
        if (isset($res['qtd']) && $res['qtd'] > 0) {
            return true;
        } else {
            return false;
        }
    }
	
	public function excluir($id)
    {
        $result = $this->model->getAdapter()->delete("usuario",'id = '.$id);
		return result;
    }
}