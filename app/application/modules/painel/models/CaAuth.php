<?php

class Painel_Model_CaAuth extends Zend_Db_Table_Abstract
{
    protected $_name = 'usuario';
    
    public function save(array $data)
    {
        $id = $data['id'];
        if ($this->_dataExists($id)) {
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
    
    
    public static function login($email, $senha)
    {
        
        $resposta = array();
        
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        
        $authAdapter->setTableName('usuario')
        ->setIdentityColumn('email')
        ->setCredentialColumn('senha');
        
        $authAdapter->setIdentity($email)
        ->setCredential($senha)
        ->setCredentialTreatment('MD5(?)');
        
        $select = $authAdapter->getDbSelect();
        $select->join( array('r' => 'role'), 'r.id = usuario.role_id', array('role' => 'role') );
        
        //Realiza autenticação
        $result = $authAdapter->authenticate();
        //Verifica se a autenticação foi válida
        if($result->isValid()){
            //Obtém dados do usuário
            $usuario = $authAdapter->getResultRowObject();
            //Armazena seus dados na sessão
            $storage = Zend_Auth::getInstance()->getStorage();
            $storage->write($usuario);
            //Redireciona para o Index
            return true;
        }
        throw new Exception('Nome de usuário ou senha inválida');
    }
    
    public static function logout()
    {  
      //Limpa dados da Sessão
      Zend_Auth::getInstance()->clearIdentity();
      //Redireciona a requisição para a tela de Autenticacao novamente
      return $this->_helper->redirector->goToRoute( array('module' => 'painel','controller' => 'auth'), null, true);
      
    }
}