<?php

class Painel_MembroController extends Zend_Controller_Action
{
    
    public function init(){
        $this->model = new Painel_Model_Membro();
        $this->modelLog = new Painel_Model_Logs();
        $this->view->cssHelper = Painel_Plugin_CssHelper::CssHelper();
        $this->view->jsHelper = Painel_Plugin_JavascriptHelper::JsHelper();
        $this->view->mostra_head_footer = true;
        $this->uteis = new App_AbstractController();
        
        if ( !Zend_Auth::getInstance()->hasIdentity() ) {
            return $this->_helper->redirector->goToRoute( array('controller' => 'auth'), null, true);
        }
    }
	
	public function indexAction()
	{
		
	}
	
	public function listarAction()
	{
	
        $resposta = array();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        
        $sql = $this->model->getAdapter()->select()->from('membro');
        
        // Parametros para busca
        if($this->getRequest()->getParam('nome'))
            $sql->where('nome LIKE ?', "%".$this->getRequest()->getParam('nome')."%");
        
        if($this->getRequest()->getParam('status') === '0' || $this->getRequest()->getParam('status') === '1')
            $sql->where('status = ?', $this->getRequest()->getParam('status'));
        
        if($this->getRequest()->getParam('id'))
            $sql->where('id = ?', $this->getRequest()->getParam('id'));

        if($this->getRequest()->getParam('email'))
            $sql->where('email LIKE ?', '%' . $this->getRequest()->getParam('email') . '%');

        if($this->getRequest()->getParam('telefone'))
            $sql->where('telefone LIKE ?', '%' . $this->getRequest()->getParam('telefone') . '%');

        if($this->getRequest()->getParam('data_nascimento'))
            $sql->where('data_nascimento LIKE ?', '%' . $this->getRequest()->getParam('data_nascimento') . '%');

        if($this->getRequest()->getParam('cidade'))
            $sql->where('cidade LIKE ?', '%' . $this->getRequest()->getParam('cidade') . '%');

        if($this->getRequest()->getParam('endereco'))
            $sql->where('endereco LIKE ?', '%' . $this->getRequest()->getParam('endereco') . '%');

        if($this->getRequest()->getParam('bairro'))
            $sql->where('bairro LIKE ?', '%' . $this->getRequest()->getParam('bairro') . '%');
        // fim Parametros para busca
        
        // total com a pesquisa
        $totalTudo = $this->model->getAdapter()->fetchAll($sql);

        // Verifica a ordenacao
        if($this->getRequest()->getParam('sort') && $this->getRequest()->getParam('order')){
            $ordernar = $this->getRequest()->getParam('sort')." ".$this->getRequest()->getParam('order');
            $sql->order($ordernar);
        }
        
        // Verifica o limit e o offset
        if($this->getRequest()->getParam('limit') || $this->getRequest()->getParam('offset'))
            $sql->limit($this->getRequest()->getParam('limit'),$this->getRequest()->getParam('offset'));
        
        $logs = $this->model->getAdapter()->fetchAll($sql);
        $total = count($logs);
        $values['total'] = count($totalTudo);
        
        for($i=0;$i<$total;$i++){
            $values['rows'][$i]['id'] = $logs[$i]['id'];
            $values['rows'][$i]['nome'] = $logs[$i]['nome'];
            $values['rows'][$i]['email'] = $logs[$i]['email'];
            $values['rows'][$i]['telefone'] = $logs[$i]['telefone'];
            $values['rows'][$i]['data_nascimento'] = $this->uteis->converteData($logs[$i]['data_nascimento']);
            $values['rows'][$i]['cidade'] = $logs[$i]['cidade'];
            $values['rows'][$i]['endereco'] = $logs[$i]['endereco'];
            $values['rows'][$i]['bairro'] = $logs[$i]['bairro'];
            $values['rows'][$i]['status'] = $logs[$i]['status'] == 1 ? "Ativo" : "Inativo";
        }
        
        echo json_encode($values);
	}
	public function pesquisarAction()
	{
	    
	    $this->_helper->layout()->disableLayout();
	}
	public function excluirAction()
	{
	    
	    $resposta = array();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	
	    // total com a pesquisa
	    $result = $this->model->getAdapter()->delete("membro",'id = '.$this->getRequest()->getParam('id'));
	     
	    if($result){
	        $resposta['situacao'] = "sucess";
	        $resposta['msg'] = "Excluido com sucesso!";
	        
	        // Grava Logo
	        $data = array();
	        $data['modulo'] = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
	        $data['controller'] = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
	        $data['metodo'] = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
	        $data['ip'] = $_SERVER['REMOTE_ADDR'];
	        $data['data'] = time();
	        $data['usuario_id'] = Zend_Auth::getInstance()->getIdentity()->id;
	        $data['usuario_nome'] = Zend_Auth::getInstance()->getIdentity()->nome;
	        $data['descricao'] = "Exclusão de Membro";
	        $this->modelLog->save($data);
	        
	    }else{
	        $resposta['situacao'] = "error";
	        $resposta['msg'] = "Erro ao Excluir!";
	    }
	     
	    echo json_encode($resposta);
	     
	     
	}
	public function cadastroRapidoAction()
	{
	     
	    $this->_helper->layout()->disableLayout();
	}
	public function cadastroFormAction()
	{
	
	}
	
	public function cadastrarAcaoAction()
	{
	    $resposta = array();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	     
	    $data = $this->getRequest()->getPost();
	    unset($data['avisar']);
	     
	    if($this->getRequest()->getParam('senha')){
	        $data['senha'] = md5($this->getRequest()->getParam('senha'));
	    }else{
	        unset($data['senha']);
	    }
	
	    if($this->getRequest()->getParam('data_nascimento'))
	        $data['data_nascimento'] = $this->uteis->converteData($this->getRequest()->getParam('data_nascimento'));
	    
	    if($this->getRequest()->getParam('data_casamento'))
	        $data['data_casamento'] = $this->uteis->converteData($this->getRequest()->getParam('data_casamento'));
	
	     
	    $result = $this->model->save($data);
	     
	    if($result){
	        $resposta['situacao'] = "sucess";
	        $resposta['msg'] = "Cadastrado com sucesso!";
	         
	        if($this->getRequest()->getParam('senha') && $this->getRequest()->getParam('avisar')){
	            $aEmails = array($data['email']);
	            $aMsg = array();
	            $aMsg[] = array(
	                    'tipo' => 'Mensagem',
	                    'msg' => 'Caro '.$data['nome'].'!'.'<br/> Seja bem vindo.',
	            );
	            if($this->getRequest()->getParam('email')){
	                $aMsg[] = array(
	                        'tipo' => 'Login',
	                        'msg' => $this->getRequest()->getParam('email'),
	                );
	            }
	            if($this->getRequest()->getParam('senha')){
	                $aMsg[] = array(
	                        'tipo' => 'Senha',
	                        'msg' => $this->getRequest()->getParam('senha'),
	                );
	            }
	            $result = $this->uteis->enviaEmail($aEmails,$aMsg,null,'Seja bem vindo.');
	            if($result){
	                $resposta['msg'] = "Cadastrado com sucesso, dados enviado para o email.";
	            }
	        }
	        
	        // Grava Logo
	        $data = array();
	        $data['modulo'] = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
	        $data['controller'] = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
	        $data['metodo'] = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
	        $data['ip'] = $_SERVER['REMOTE_ADDR'];
	        $data['data'] = time();
	        $data['usuario_id'] = Zend_Auth::getInstance()->getIdentity()->id;
	        $data['usuario_nome'] = Zend_Auth::getInstance()->getIdentity()->nome;
	        $data['descricao'] = "Cadastro de Membro";
	        $this->modelLog->save($data);
	    }else{
	        $resposta['situacao'] = "error";
	        $resposta['msg'] = "Erro ao Cadastrar!";
	    }
	
	    echo json_encode($resposta);
	}
	public function editarRapidoAction()
	{
	    
	    $sql = $this->model->getAdapter()->select()->from('membro');
	     
	    // Parametros para busca
	    if($this->getRequest()->getParam('id'))
	        $sql->where('id = ?',$this->getRequest()->getParam('id'));
	    // fim Parametros para busca
	     
	    // total com a pesquisa
	    $data = $this->model->getAdapter()->fetchRow($sql);
	    $data['data_nascimento'] = $this->uteis->converteData($data['data_nascimento']);
	    $this->view->membro = $data;
	    
	    $this->_helper->layout()->disableLayout();
	}
	
	public function editarFormAction()
	{
	    $sql = $this->model->getAdapter()->select()->from('membro');
	    
	    // Parametros para busca
	    if($this->getRequest()->getParam('id'))
	        $sql->where('id = ?',$this->getRequest()->getParam('id'));
	    // fim Parametros para busca
	    
	    // total com a pesquisa
	    $data = $this->model->getAdapter()->fetchRow($sql);
	    $data['data_nascimento'] = $this->uteis->converteData($data['data_nascimento']);
	    $this->view->membro = $data;
	    
	}
	public function editarAcaoAction()
	{

	    $resposta = array();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	    $data = $this->getRequest()->getPost();
	    unset($data['avisar']);
	    if($this->getRequest()->getParam('senha')){
	        $data['senha'] = md5($this->getRequest()->getParam('senha'));
	    }else{
	        unset($data['senha']);
	    }
	    
	    if($this->getRequest()->getParam('data_nascimento'))
	        $data['data_nascimento'] = $this->uteis->converteData($this->getRequest()->getParam('data_nascimento'));
	    
	    if($this->getRequest()->getParam('data_casamento'))
	        $data['data_casamento'] = $this->uteis->converteData($this->getRequest()->getParam('data_casamento'));
	    
	    $result = $this->model->save($data);
	    
	    if($result){
	        $resposta['situacao'] = "sucess";
	        $resposta['msg'] = "Editado com sucesso!";
	        if($this->getRequest()->getParam('senha') && $this->getRequest()->getParam('avisar')){
    	        $aEmails = array($data['email']);
    	        $aMsg = array();
    	        $aMsg[] = array(
    	                'tipo' => 'Alteração',
    	                'msg' => 'Caro '.$data['nome'].'!'.'<br/> Seus dados foram alterados.',
    	        );
    	        if($this->getRequest()->getParam('senha')){
        	        $aMsg[] = array(
        	                'tipo' => 'Nova Senha',
        	                'msg' => $this->getRequest()->getParam('senha'),
        	        );
    	        }
    	        $result = $this->uteis->enviaEmail($aEmails,$aMsg,null,'Seu dados foram alterados.');
    	        if($result){
    	            $resposta['msg'] = "Editado com sucesso, dados enviado para o email.";
    	        }
	        }
	        // Grava Logo
	        $data = array();
	        $data['modulo'] = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
	        $data['controller'] = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
	        $data['metodo'] = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
	        $data['ip'] = $_SERVER['REMOTE_ADDR'];
	        $data['data'] = time();
	        $data['usuario_id'] = Zend_Auth::getInstance()->getIdentity()->id;
	        $data['usuario_nome'] = Zend_Auth::getInstance()->getIdentity()->nome;
	        $data['descricao'] = "Edição de Membro";
	        $this->modelLog->save($data);
	    }else{
	        $resposta['situacao'] = "error";
	        $resposta['msg'] = "Erro ao Editar!";
	    }
	     
	    echo json_encode($resposta);
	}


}