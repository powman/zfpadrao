<?php

class IndexController extends App_Controller_BaseController 
{
    public $models = array();
    public $modelAtual = '';
    public $msg = null;

	public function indexAction()
	{
	 $tblModulo = new Painel_Model_CgModulo();
	 //$params = array("nome" => "teste");
	 $tblModulo->listarTodos();

	}
	
	public function loginAction()
	{
	
	}
	
	public function logarAction()
	{
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	    $resposta = array();
	    
	    $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
	    $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
	    
	    $authAdapter->setTableName('usuario')
	    ->setIdentityColumn('email')
	    ->setCredentialColumn('senha');
	    
	    $authAdapter->setIdentity($this->getRequest()->getParam('email'))
	    ->setCredential($this->getRequest()->getParam('senha'))
	    ->setCredentialTreatment('MD5(?) and status = 1');
	    
	    //Realiza autenticacao
	    $result = $authAdapter->authenticate();
	    //Verifica se a autenticacao foi validada
	    if($result->isValid()){
	        //obtem os dados do usuario
	        $usuario = $authAdapter->getResultRowObject();
	        //Armazena seus dados na sessao
	        $storage = Zend_Auth::getInstance()->getStorage();
	        $storage->write($usuario);
	        // se não for para lembrar os dados expira a sessao em 30 minutos
	        if(!$this->getRequest()->getParam('lembrar')){
    	        $session = new Zend_Session_Namespace( 'Zend_Auth' );
    	        $session->setExpirationSeconds( 1800 );
	        }
	        //Redireciona para o Index
	        $resposta['situacao'] = "success";
	        $resposta['msg'] = "Logando aguarde...";
	    }else{
	        $resposta['situacao'] = "error";
	        $resposta['msg'] = "Usuário inativo ou senha incorreta.";
	    }
	    
	    echo json_encode($resposta);
	}

}