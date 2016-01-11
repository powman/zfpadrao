<?php

class CaUsuarioController extends App_Controller_BaseController
{
	public $models = array('CaUsuarioGrupo');
	public $modelAtual = 'CaUsuario';
	public $msg = null;
	
	public function indexAction()
	{
	    // verifica se tem acao para remover
	    $this->view->remover = Zend_Registry::get('acl')->isAllowed($this->view->sessao->grupo_id, $this->controle, "remover");
	    $this->view->form_cadastro = Zend_Registry::get('acl')->isAllowed($this->view->sessao->grupo_id, $this->controle, "form");
	    
	    if ($this->getRequest()->isXmlHttpRequest()) {
	        $this->_helper->layout()->disableLayout();
	        $this->_helper->viewRenderer->setNoRender(true);

	        $offset        		= $this->_getParam('offset',0);
	        $page          		= $this->_getParam('page',1);
	        $registroPagina     = $this->_getParam('count',10);
	        
	        $aPesquisa = array();
	        $order = "";
	        $offset = ($registroPagina*$page)-$registroPagina;
	        if($this->_getParam("filter")){
	            
    	        // pega todos os dados do filtro de pesquisa
    	        foreach ($this->_getParam("filter") as $key => $value){
    	            if(!is_numeric($value)){
    	               $aPesquisa[$key] = urldecode($value);
    	            }else{
    	                $aPesquisa[$key] = intval($value);
    	            }
    	        }
	        }
	        
	        // pega os dados de ordenacao
	        if($this->_getParam("sorting"))
	           $order = key($this->_getParam("sorting"))." ".$this->_getParam("sorting")[key($this->_getParam("sorting"))];
	        $res = $this->model->listarTodos($aPesquisa,$registroPagina,$offset,$order);
	        
	        foreach ($res["res"] as $key => $value)
	        {
	            $res["res"][$key]["del"] = "true";
	            if($this->view->sessao->id_usuario == $res["res"][$key]["id_usuario"] || !$this->view->remover){
	               $res["res"][$key]["selected"] = "false";
	               $res["res"][$key]["del"] = "false";
	            }
	        }

	        echo json_encode(array("msg"=>"Dados carregado","status" => "sucesso","dados" => $res));
	    }
		
	}
	
	public function formAction()
	{
	    
	}
	
	public function incluirAction()
	{
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	
	
	    echo json_encode(array("msg"=>"Abas carregada","status" => "sucesso","dados" => ''));
	}
	
	public function getUsuarioAction()
	{
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $id = $this->_getParam("id");
	    
	    $res = $this->model->fetchByKey($id,$this->msg);

	    echo json_encode(array("msg"=>$this->msg,"status" => "sucesso","dados" => $res));
	}
	
	public function getAbasAction()
	{
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $request = Zend_Controller_Front::getInstance()->getRequest();
	    $res = array();
	    
	    if(Zend_Registry::get('acl')->isAllowed($this->view->sessao->grupo_id, $this->controle, "aba-usuario"))
	       $res[] = array('title' => "Usuário",'url' => $this->_helper->url("aba-usuario",$this->controle),'disabled' => false);
	    
	    if(Zend_Registry::get('acl')->isAllowed($this->view->sessao->grupo_id, $this->controle, "aba-avatar"))
	       $res[] = array('title' => "Avatar",'url' => $this->_helper->url("aba-avatar",$this->controle),'disabled' => false);
	    
	    echo json_encode(array("msg"=>"Abas carregada","status" => "sucesso","dados" => $res));
	}
	
	public function modalAction()
	{
	    $this->_helper->layout()->disableLayout();

	    $params = array("valor" => $this->_getParam("search"));
	    $this->view->dados = $this->model->listarTodos($params);
	}
	
	public function abaUsuarioAction()
	{
	    $this->_helper->layout()->disableLayout();
	}
	
	public function abaAvatarAction()
	{
	    $this->_helper->layout()->disableLayout();
	}
	
	public function listarAction()
	{
	
       /* $resposta = array();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        
        $sql = $this->model->getAdapter()->select()->from('usuario')->joinLeft(
                    'role',
                    'role.id = usuario.role_id',
                    array('role.role as grupo')
                    );
        
        // Parametros para busca
        if($this->getRequest()->getParam('pesquisa'))
            $sql->orWhere('role.role LIKE ?', "%".$this->getRequest()->getParam('pesquisa')."%");
        
        // Parametros para busca
        if($this->getRequest()->getParam('pesquisa'))
            $sql->orWhere('usuario.nome LIKE ?', "%".$this->getRequest()->getParam('pesquisa')."%");
        
        // Parametros para busca
        if($this->getRequest()->getParam('pesquisa'))
            $sql->orWhere('usuario.status = ?', "".$this->getRequest()->getParam('pesquisa')."");
        
        if($this->getRequest()->getParam('pesquisa'))
            $sql->orWhere('usuario.email LIKE ?', "%".$this->getRequest()->getParam('pesquisa')."%");
        
        if($this->getRequest()->getParam('pesquisa'))
            $sql->orWhere('usuario.id = ?', $this->getRequest()->getParam('pesquisa'));
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
            $values['rows'][$i]['grupo'] = $logs[$i]['grupo'];
            $values['rows'][$i]['status'] = $logs[$i]['status'] ? "Ativo" : "Inativo";
        }
        
        echo json_encode($values);*/
	}
	public function removerAction()
	{
	    
	    $resposta = array();
	    $array = false;
	    $condicao = true;
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	    
	    $ids = $this->getRequest()->getParam('id');
	    if(is_array($ids)){
	       $array = true;
	       $ids = implode(",", $this->getRequest()->getParam('id'));
	       $integerIDs = array_map('intval', $this->getRequest()->getParam('id')); 
	    }
	    if($array){
	        if(in_array((int)$this->view->sessao->id_usuario, $integerIDs)){
	            $condicao = false;
	        } 
	    }
	    if($condicao){
    	    // chama a funcao excluir
    	    $result = $this->model->remove("id_usuario in(".$ids.")",$this->msg);
    	     
    	    if($result){
    	        $resposta['status'] = "sucesso";
    	        $resposta['msg'] = $this->msg;
    	    }else{
    	        $resposta['status'] = "erro";
    	        $resposta['msg'] = $this->msg;
    	    }
    	     
    	    echo json_encode($resposta);
	    }else{
	        $resposta['status'] = "error";
	        $resposta['msg'] = "Erro ao Excluir, você não pode excluir seu usuário!";
	        
	        echo json_encode($resposta);
	    }
	     
	     
	}
	
	public function cadastrarAcaoAction()
	{
	    $resposta = array();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	     
	    $data = $this->getRequest()->getPost();
            $data['senha'] = md5($data['senha']);
	    unset($data['avisar']);
	     
	    $result = $this->model->save($data);
	     
	    if($result){
	        $resposta['situacao'] = "sucess";
	        $resposta['msg'] = "Cadastrado com sucesso!";
	        
	        if($this->getRequest()->getParam('senha') && $this->getRequest()->getParam('avisar')){
	            $aEmails = array($data['email']);
	            $aMsg = array();
	            $aMsg[] = array(
	                    'tipo' => 'Cadastro',
	                    'msg' => 'Caro '.$data['nome'].'!'.'<br/> Seja bem vindo ao painel administrativo.',
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
	        $data['descricao'] = "Cadastro de Usuário";
	        $this->modelLog->save($data);
	    }else{
	        $resposta['situacao'] = "error";
	        $resposta['msg'] = "Erro ao Cadastrar!";
	    }
	
	    echo json_encode($resposta);
	}
	public function editarRapidoAction()
	{
	    
	    $sql = $this->model->getAdapter()->select()->from('usuario');
	     
	    // Parametros para busca
	    if($this->getRequest()->getParam('id'))
	        $sql->where('id = ?',$this->getRequest()->getParam('id'));
	    // fim Parametros para busca
	     
	    // total com a pesquisa
	    $data = $this->model->getAdapter()->fetchRow($sql);
	    $this->view->usuario = $data;
	    
	    $sql = $this->modelUsuarioGrupo->getAdapter()->select()->from('role');
	    
	    $data = $this->model->getAdapter()->fetchAll($sql);
	    $this->view->grupos = $data;
	    
	    $this->_helper->layout()->disableLayout();
	}
	public function editarAcaoAction()
	{

	    $resposta = array();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	    $data = $this->getRequest()->getPost();
	    if($data['senha']){
            $data['senha'] = md5($data['senha']);
	    }else{
	        unset($data['senha']);
	    }
	    unset($data['avisar']);
	    
	    $result = $this->model->save($data);
	    
	    if($result){
	        $resposta['situacao'] = "sucess";
	        $resposta['msg'] = "Editado com sucesso!";
	        
	        
	        if($this->getRequest()->getParam('avisar')){
	            $aEmails = array($data['email']);
	            $aMsg = array();
	            $aMsg[] = array(
	                    'tipo' => 'Alteração',
	                    'msg' => 'Caro '.$data['nome'].'!'.'<br/> Seus dados foram alterados.',
	            );
	            if($this->getRequest()->getParam('email')){
	                $aMsg[] = array(
	                        'tipo' => 'Login',
	                        'msg' => $this->getRequest()->getParam('email'),
	                );
	            }
	            $result = $this->uteis->enviaEmail($aEmails,$aMsg,null,'Seus Dados foram alterados.');
	            if($result){
	                $resposta['msg'] = "Alterado com sucesso, dados enviado para o email.";
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
	        $data['descricao'] = "Edição de Usuário";
	        $this->modelLog->save($data);
	        
	    }else{
	        $resposta['situacao'] = "error";
	        $resposta['msg'] = "Erro ao Editar!";
	    }
	     
	    echo json_encode($resposta);
	}


}