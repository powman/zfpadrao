<?php

class Painel_Model_ScaGrupo extends App_Model_Default
{
    
    protected $_name = 'sca_grupo';
    public $id = null;
    protected $primarykey = "id_grupo";
	
    /**
     * 
     * @param unknown $arraySearch
     * @param string $limit
     * @param string $offset
     * @param string $order
     * @param string $msg
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
	public function listarTodos($arraySearch = array(), $limit = null, $offset = null, $order = 'g.id_grupo', &$msg = '')
	{
		// SQL para buscar os registros
		$sql = $this->getAdapter()->select()
		->from(array('g' => $this->_name));
		if (isset($arraySearch['valor']) && $arraySearch['valor'] && is_int($arraySearch['valor'])) {
		    $sql->where('g.id_grupo = ?', $arraySearch['valor']);
		}
	
		if (isset($arraySearch['valor']) && $arraySearch['valor'] && !is_int($arraySearch['valor'])) {
			$sql->where('g.nm_grupo LIKE ?', "%{$arraySearch['valor']}");
		}
		
		// SQL para buscar a quantidade de páginas existentes
		$sqlCount = $this->getAdapter()->query($sql->__toString());

		$sql->order($order)->limit($limit, $offset);
		$stmt = $sql->query();
 	
		$return['res']   = $stmt->fetchAll();
		$return['total'] = $sqlCount->rowCount();
	
		return $return;
	}
	
	/**
	 * 
	 * @param int $id
	 * @param string $msg
	 * @return mixed
	 */
	public function fetchByKey($id, &$msg = null)
	{
	    $sql = $this->getAdapter()->select()
		->from(array('g' => $this->_name));
		$sql->where('g.id_grupo = ?', $id);
		
		$res = $this->getAdapter()->fetchRow($sql);
	
	    if (!$res) {
	        $msg = $this->msg['select']['not-found'];
	    }
		
	    return $res;
	}
}