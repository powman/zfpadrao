<?php

class Painel_Model_CaUsuario extends App_Model_Default
{
    
    protected $_name = 'usuario';
    public $id = null;
    protected $primarykey = "id";
	
	public function listarTodos($arraySearch = array(), $limit = null, $offset = null, &$msg = '')
	{
		// SQL para buscar os registros
		$sql = $this->getAdapter()->select()
		->from(array('u' => $this->_name), array('id', 'nome','senha','email','img','status','grupo_id'));
	
		if (isset($arraySearch['nome']) && $arraySearch['nome']) {
			$sql->where('u.nome LIKE ?', "%{$arraySearch['nome']}%");
		}
		
		// SQL para buscar a quantidade de páginas existentes
		$sqlCount = $this->getAdapter()->query($sql->__toString());
	
		$sql->order('u.id')->limit($limit, $offset);
		$stmt = $sql->query();
 	
		$return['res']   = $stmt->fetchAll();
		$return['pages'] = $sqlCount->rowCount();
	
		return $return;
	}
}