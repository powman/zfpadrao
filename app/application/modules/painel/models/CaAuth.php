<?php

class Painel_Model_CaAuth extends App_Model_Default
{
    protected $_name = 'usuario';
    protected $primarykey = "id";
	
	public static function authenticate(array $values)
    {
    	$email=isset($values['email']) ? $values['email'] : null;
		$senha=isset($values['senha']) ? $values['senha'] : null;

        if(!count($values)) throw new Exception('Não foi passado valores para autenticar');
        // Pegar os dados da autenticacao e checa
       
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		
		$authAdapter->setTableName('usuario')
        ->setIdentityColumn('email')
        ->setCredentialColumn('senha');
        
        $authAdapter->setIdentity($email)
        ->setCredential($senha)
        ->setCredentialTreatment('MD5(?)');
        
        $select = $authAdapter->getDbSelect();
        $select->join( array('g' => 'grupo'), 'g.id = usuario.grupo_id', array('grupo' => 'nome'));
		
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
		return false;
    }
    
    public static function logout()
    {  
      //Limpa dados da Sessão
      Zend_Auth::getInstance()->clearIdentity();
      //Redireciona a requisição para a tela de Autenticacao novamente
      return $this->_helper->redirector->goToRoute( array('module' => 'painel','controller' => 'auth'), null, true);
      
    }
}