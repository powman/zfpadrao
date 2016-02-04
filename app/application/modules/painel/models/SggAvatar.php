<?php

class Painel_Model_SggAvatar extends App_Model_Default
{
    
    protected $_name = 'sgg_avatar';
    public $id = null;
    protected $primarykey = "id_avatar";
	
    /**
     * 
     * @param unknown $arraySearch
     * @param string $limit
     * @param string $offset
     * @param string $order
     * @param string $msg
     * @return Ambigous <multitype:, multitype:mixed Ambigous <string, boolean, mixed> >
     */
	public function listarTodos($arraySearch = array(), $limit = null, $offset = null, $order = 'u.id_usuario', &$msg = '')
	{
		// SQL para buscar os registros
		$sql = $this->getAdapter()->select()
		->from(array('a' => $this->_name));
		if (isset($arraySearch['valor']) && $arraySearch['valor'] && is_int($arraySearch['valor'])) {
		    $sql->where('a.id_avatar = ?', $arraySearch['valor']);
		}
	
		if (isset($arraySearch['valor']) && $arraySearch['valor'] && !is_int($arraySearch['valor'])) {
			$sql->where('a.nm_avatar LIKE ?', "%{$arraySearch['valor']}");
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
		->from(array('a' => $this->_name));
		$sql->where('a.id_avatar = ?', $id);
		
		$res = $this->getAdapter()->fetchRow($sql);
	
	    if (!$res) {
	        $msg = $this->msg['select']['not-found'];
	    }
		
	    return $res;
	}
}