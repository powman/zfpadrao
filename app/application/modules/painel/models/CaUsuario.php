<?php

class Painel_Model_CaUsuario extends App_Model_Default
{
    
    protected $_name = 'usuario';
    public $id = null;
    protected $primarykey = "id";
	
	public function listarTodos($arraySearch = array(), $limit = null, $offset = null, $order = 'u.id', &$msg = '')
	{
		// SQL para buscar os registros
		$sql = $this->getAdapter()->select()
		->from(array('u' => $this->_name), array('id', 'nome','senha','email','img','status','grupo_id'));
		if (isset($arraySearch['valor']) && $arraySearch['valor'] && is_int($arraySearch['valor'])) {
		    $sql->where('u.id = ?', $arraySearch['valor']);
		}
	
		if (isset($arraySearch['valor']) && $arraySearch['valor'] && !is_int($arraySearch['valor'])) {
			$sql->where('u.nome LIKE ?', "%{$arraySearch['valor']}");
			$sql->orWhere('u.email LIKE ?', "%{$arraySearch['valor']}");
		}
		
		// SQL para buscar a quantidade de páginas existentes
		$sqlCount = $this->getAdapter()->query($sql->__toString());

		$sql->order($order)->limit($limit, $offset);
		$stmt = $sql->query();
 	
		$return['res']   = $stmt->fetchAll();
		$return['total'] = $sqlCount->rowCount();
	
		return $return;
	}
}