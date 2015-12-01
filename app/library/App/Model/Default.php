<?php
/**
 * Model_Default
 * 
 */
class App_Model_Default extends Zend_Db_Table_Abstract {
	

	public $id = null;
	
	/**
	 * Mensagens a serem retornadas
	 *
	 * @var array
	 */
	protected $msg = array(
	        'select' => array(
	                'not-found' => 'Nenhum registro encontrado',
	                'success'   => ' %n registros foram encontrados'
	        ),
	        'insert' => array(
	                'success' => 'Registro inserido com sucesso',
	                'error'   => 'Erro ao inserir registro'
	        ),
	        'update' => array(
	                'success' => 'Registro alterado com sucesso',
	                'error'   => 'Erro ao alterar registro'
	        ),
	        'delete' => array(
	                'success' => 'Registro removido com sucesso',
	                'error'   => 'Erro ao remover o registro'
	        ),
	        'ambiguous' => array(
	                'success' => 'Registro modificado com sucesso',
	                'error'   => 'Erro ao modificar o registro'
	        )
	);
	
	/**
	 * Salva um registro no banco de dados
	 *
	 * @param array $data
	 * @param string $msg
	 * @return mixed
	 */
	public function save(array $data, &$msg = null)
	{
	    try{
    	    // limpar os arrays vazios
    	    $data = array_filter($data);
    	    // trim em todos os valores
    	    $data = array_map('trim', $data);
    	    $id = isset($data[$this->primarykey]) ? $data[$this->primarykey] : '';
    	    // se existir o id no banco faz um update
    	    if ($this->_dataExists($id) && $id) {
    	        // deleta o id para nao conflitar
    	        unset($data[$this->primarykey]);
    	        $result = $this->update($data, $this->primarykey." = {$id}");
    	        $result === 0 || $result === true || $result >= 1 ? true : false;
    	        if ($result) {
    	            $msg = $this->msg['update']['success'];
    	            return $result;
    	        } else {
    	            $msg = $this->msg['update']['error'];
    	            return false;
    	        }
    	    // faz o insert
    	    } else {
    	        $retorno = $this->insert($data);
    	        if($retorno){
    	           $msg = $this->msg['insert']['success'];
    	           $result = $this->find($retorno)->toArray();
    	           return $result[0];
    	        }else{
    	           $msg = $this->msg['insert']['error'];
    	           return false;
    	        }
    	        
    	    }
	    }catch (Zend_Exception $e) {
            $msg = $e->getMessage() . "\n";
            return false;
        }
	}
	
	/**
	 * Localizar item por id
	 *
	 * @param array $condition
	 * @param string $msg
	 * @return boolean
	 */
	private function _dataExists($id, &$msg = null)
	{
	    try {
    	    if($id){
    	        $sql = 'SELECT count(*) as qtd FROM '.$this->_schema.'.'.$this->_name.' WHERE '.$this->primarykey.' = ?';
        	    $res = $this->getAdapter()->fetchRow($sql,$id);
        	    if (isset($res['qtd']) && $res['qtd'] > 0) {
        	        $msg = str_replace('%n', $res['qtd'], $this->msg['select']['success']);
        	        return true;
        	    } else {
        	        $msg = $this->msg['select']['not-found'];
        	        return false;
        	    }
    	    }else{
    	        return false;
    	    }
	    }catch (Zend_Exception $e) {
	        $msg = $e->getMessage() . "\n";
	        return false;
	    }
	}
	
	/**
	 * Remove algum registro do banco de dados
	 *
	 * @param string $condition
	 * @param string $msg
	 * @return boolean
	 */
	public function remove($condition, &$msg = null)
	{
		try {
			$this->getAdapter()->delete($this->_name,$condition);
			$msg = $this->msg['delete']['success'];
			return true;
		} catch (Zend_Exception $e) {
	        $msg = $e->getMessage() . "\n";
	        return false;
	    }
	}
			
}
