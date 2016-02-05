<?php

class ScaUsuarioController extends App_Controller_BaseController
{
	public $models = array('SggAvatar');
	public $modelAtual = 'ScaUsuario';
	public $msg = null;
	/**
	 * Lista os dados na view
	 */
	public function indexAction()
	{
	    // verifica se tem acao para remover
	    $this->view->remover = Zend_Registry::get('acl')->isAllowed($this->view->sessao->id_grupo, $this->controle, "remover");
	    $this->view->form_cadastro = Zend_Registry::get('acl')->isAllowed($this->view->sessao->id_grupo, $this->controle, "form");
	    $this->view->ativar = Zend_Registry::get('acl')->isAllowed($this->view->sessao->id_grupo, $this->controle, "ativar");
	    $this->view->desativar = Zend_Registry::get('acl')->isAllowed($this->view->sessao->id_grupo, $this->controle, "desativar");
	    
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
	/**
	 * Formulario de incluir ou alterar
	 */
	public function formAction()
	{
	   
	}
	/**
	 * Incluir um usuario
	 */
	public function incluirAction()
	{
	    $resposta = array();
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    if ($this->getRequest()->isPost()){
	        $post = $this->getRequest()->getPost();
	        $validaLogin = $this->model->fetchRow($this->model->select()->where('login_usuario = ?', $post['login_usuario']));
	        if($validaLogin){
	            $resposta['status'] = "erro";
	            $resposta['msg'] = "Este login esta sendo usado.";
	            echo json_encode($resposta);
	            exit();
	        }
	        if(isset($post['password_usuario'])){
	            $post['password_usuario'] = md5($post['password_usuario']);
	        }
	        unset($post['id_usuario']);
	        unset($post['nm_grupo']);
	        $result = $this->model->save($post,$this->msg);
	        if($result){
	            $resposta['status'] = "sucesso";
	            $resposta['msg'] = $this->msg;
	            $resposta['dados'] = $result;
	        }else{
	            $resposta['status'] = "erro";
	            $resposta['msg'] = $this->msg;
	        }
	    }else{
	        $resposta['status'] = "erro";
	        $resposta['msg'] = "Um erro inesperado aconteceu.";
	    }
	    
	    echo json_encode($resposta);
	}
	/**
	 * Salvar o avatar
	 */
	public function salvarAvatarAction()
	{
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $resposta = array();
	    if ($this->getRequest()->isPost()){
    	    $form = array(
    	        "nm_avatar" => $_FILES["file"]["name"],
    	        "tp_avatar" => $_FILES["file"]["type"],
    	        "sz_avatar" => $_FILES["file"]["size"],
    	        "arquivo" =>   file_get_contents($_FILES["file"]["tmp_name"])
    	    );
    	    $result = $this->modelSggAvatar->save($form,$this->msg);
    	    if($result){
    	        $post = $this->getRequest()->getPost();
    	        $form = array();
    	        $form['id_avatar'] = $result['id_avatar'];
    	        $form['id_usuario'] = $post['id_usuario'];
    	        if($post['id_avatar'] != null && $post['id_avatar'] != 'null'){
    	           $this->modelSggAvatar->remove("id_avatar = ".$post['id_avatar']);
    	        }
    	        if($form['id_avatar'] && $form['id_usuario']){
    	            //print_r($form);
    	            //exit();
    	            $resultUsuario = $this->model->save($form,$this->msg);
        	        if($resultUsuario){
        	            $resposta['status'] = "sucesso";
        	            $resposta['msg'] = $this->msg;
        	            $resposta['dados'] = array('id_avatar' => $result['id_avatar']);
        	        }else{
        	            $resposta['status'] = "erro";
        	            $resposta['msg'] = "Um erro inesperado aconteceu.";
        	        }
    	        }else{
    	            $resposta['status'] = "sucesso";
        	        $resposta['msg'] = $this->msg;
        	        $resposta['dados'] = array('id_avatar' => $result['id_avatar']);
    	        }
    	        
    	    }
	    }else{
	        $resposta['status'] = "erro";
	        $resposta['msg'] = "Um erro inesperado aconteceu.";
	        
	    }
	    echo json_encode($resposta);
	    
	}
	/**
	 * Remover o avatar
	 */
	public function removerAvatarAction()
	{
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $resposta = array();
	    if ($this->getRequest()->isPost()){
	        $post = $this->getRequest()->getPost();
	        $form = array(
	            'id_avatar' => null,
	            'id_usuario' => $post['id_usuario']
	        );
	        $result = $this->model->save($form,$this->msg);
            if($result){
                $resultAvatar = $this->modelSggAvatar->remove("id_avatar = ".$post['id_avatar']);
                if($resultAvatar){
                    $resposta['status'] = "sucesso";
                    $resposta['msg'] = $this->msg;
                    $resposta['dados'] = array('id_avatar' => 0);
                }else{
                    $resposta['status'] = "erro";
                    $resposta['msg'] = "Um erro inesperado aconteceu.";
                }
            }
	
	    }else{
	        $resposta['status'] = "erro";
	        $resposta['msg'] = "Um erro inesperado aconteceu.";
	         
	    }
	    echo json_encode($resposta);
	     
	}
	
	/**
	 * Alterar um usuario
	 */
	public function alterarAction()
	{
	    $resposta = array();
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    if ($this->getRequest()->isPost()){
	        $post = $this->getRequest()->getPost();
	        $form = array();
	        if(isset($post['password_usuario']))
	            $form['password_usuario'] = md5($post['password_usuario']);
	        $form['id_usuario'] = $post['id_usuario'];
	        $form['id_pessoa'] = $post['id_pessoa'];
	        $form['nm_usuario'] = $post['nm_usuario'];
	        $form['login_usuario'] = $post['login_usuario'];
	        $form['st_usuario'] = $post['st_usuario'];
	        $form['id_grupo'] = $post['id_grupo'];
	       
	        $result = $this->model->save($form,$this->msg);
	        if($result){
	            $resposta['status'] = "sucesso";
	            $resposta['msg'] = $this->msg;
	        }else{
	            $resposta['status'] = "erro";
	            $resposta['msg'] = $this->msg;
	        }
	    }else{
	        $resposta['status'] = "erro";
	        $resposta['msg'] = "Um erro inesperado aconteceu.";
	    }
	    
	    echo json_encode($resposta);
	    
	}
	/**
	 * Pega o usuario por id
	 */
	public function getUsuarioAction()
	{
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $id = $this->_getParam("id");
	    
	    $res = $this->model->fetchByKey($id,$this->msg);

	    echo json_encode(array("msg"=>$this->msg,"status" => "sucesso","dados" => $res));
	}
	/**
	 * Pegas as abas e lista na view
	 */
	public function getAbasAction()
	{
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $request = Zend_Controller_Front::getInstance()->getRequest();
	    $res = array();
	    $usuario = $this->model->fetchByKey($this->view->sessao->id_usuario,$this->msg);
	    
	    if(Zend_Registry::get('acl')->isAllowed($this->view->sessao->id_grupo, $this->controle, "aba-usuario"))
	       $res[] = array('title' => "Usuário",'url' => $this->_helper->url("aba-usuario",$this->controle),'disabled' => false);
	    
	    if(Zend_Registry::get('acl')->isAllowed($this->view->sessao->id_grupo, $this->controle, "aba-avatar"))
	       $res[] = array('title' => "Avatar",'url' => $this->view->url(array('controller' => $this->controle,'action' => "aba-avatar")),'disabled' => true);
	    
	    echo json_encode(array("msg"=>"Abas carregada","status" => "sucesso","dados" => $res));
	}
	/**
	 * Abre o modal de pesquisa
	 */
	public function modalAction()
	{
	    $this->_helper->layout()->disableLayout();

	    $params = array("valor" => $this->_getParam("search"));
	    $this->view->dados = json_encode($this->model->listarTodos($params));
	}
	/**
	 * Lista a aba de usuário
	 */
	public function abaUsuarioAction()
	{
	    $this->_helper->layout()->disableLayout();

	}
	/**
	 * Aba de avatar
	 */
	public function abaAvatarAction()
	{
	    $this->_helper->layout()->disableLayout();
	    
	}
	/**
	 * Função para Ativar os cadastros
	 */
	public function ativarAction()
	{
	     
	    $resposta = array();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	     
	    $ids = $this->getRequest()->getParam('id');
	    if($ids){
            // chama a funcao excluir
            foreach ($ids as $value){
                $form = array('id_usuario'=>$value,"st_usuario" => 1);
                $result = $this->model->save($form,$this->msg);
            }
    
            if($result){
                $resposta['status'] = "sucesso";
                $resposta['msg'] = $this->msg;
            }else{
                $resposta['status'] = "erro";
                $resposta['msg'] = $this->msg;
            }
        }else{
            $resposta['status'] = "erro";
            $resposta['msg'] = "Nenhum dado recebido!";
        }

        echo json_encode($resposta);
	
	
	}
	
	/**
	 * Função para Desativar os cadastros
	 */
	public function desativarAction()
	{
	
	    $resposta = array();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	
	    $ids = $this->getRequest()->getParam('id');
	    if($ids){
    	    // chama a funcao excluir
    	    foreach ($ids as $value){
    	        $form = array('id_usuario'=>$value,"st_usuario" => 0);
    	        $result = $this->model->save($form,$this->msg);
    	    }
    	
    	    if($result){
    	        $resposta['status'] = "sucesso";
    	        $resposta['msg'] = $this->msg;
    	    }else{
    	        $resposta['status'] = "erro";
    	        $resposta['msg'] = $this->msg;
    	    }
	    }else{
	        $resposta['status'] = "erro";
	        $resposta['msg'] = "Nenhum dado recebido!";
	    }
	
	    echo json_encode($resposta);
	
	
	}
	/**
	 * 
	 */
	public function removerAction()
	{
	    
	    $resposta = array();
	    $array = false;
	    $condicao = true;
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	    
	    $ids = $this->getRequest()->getParam('id');
	    if($ids){
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
    	        // deleta os avatar destes usuários
    	        $usuarios = $this->model->fetchAll("id_usuario in(".$ids.")")->toArray();
    	        foreach ($usuarios as $usuario){
    	            $resultAvatar = $this->modelSggAvatar->remove($usuario['id_avatar']);
    	        }
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
	    }else{
	        $resposta['status'] = "erro";
	        $resposta['msg'] = "Nenhum dado recebido!";
	        echo json_encode($resposta);
	    }
	}
	/**
	 * (non-PHPdoc)
	 * @see App_Controller_BaseController::getBotaoAction()
	 * Pega o botao verificando as permissoes
	 */
	public function getBotaoAction()
	{
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $id        		= $this->getRequest()->getParam('id');
	    $dados = array();
	    $podeIncluir = Zend_Registry::get('acl')->isAllowed($this->view->sessao->id_grupo, $this->controle, "incluir");
	    $podeAlterar = Zend_Registry::get('acl')->isAllowed($this->view->sessao->id_grupo, $this->controle, "alterar");
	    $podeRemover = Zend_Registry::get('acl')->isAllowed($this->view->sessao->id_grupo, $this->controle, "remover");
	    if(!$id){
	
	        $dados[] = array('text' => "Incluir",'classe' => 'btn btn-success','model' => "btn.incluir",'btn' => "incluir","disabled" => $podeIncluir ? "" : "disabled");
	        $dados[] = array('text' => "Alterar",'classe' => 'btn btn-info','model' => "btn.alterar",'btn' => "alterar","disabled" => 'disabled');
	        if($this->view->sessao->id_usuario != $id)
	           $dados[] = array('text' => "Remover",'classe' => 'btn btn-danger','model' => "btn.remover",'btn' => "remover","disabled" => 'disabled');
	        $dados[] = array('text' => "Limpar",'classe' => 'btn btn-default','model' => "btn.limpar",'btn' => "limpar","disabled" => "");
	
	    }else if($id){
	        $dados[] = array('text' => "Incluir",'classe' => 'btn btn-success','model' => "btn.incluir",'btn' => "incluir","disabled" => "disabled");
	        $dados[] = array('text' => "Alterar",'classe' => 'btn btn-info','model' => "btn.alterar",'btn' => "alterar","disabled" => $podeAlterar ? "" : "disabled");
	        if($this->view->sessao->id_usuario != $id)
	           $dados[] = array('text' => "Remover",'classe' => 'btn btn-danger','model' => "btn.remover",'btn' => "remover","disabled" => $podeRemover ? "" : "disabled");
	        $dados[] = array('text' => "Limpar",'classe' => 'btn btn-default','model' => "btn.limpar",'btn' => "limpar","disabled" => "");
	    }
	
	    $html = "";
	    foreach ($dados as $key => $value){
	        $html .= '<button '.$value['disabled'].' style="'.($value['disabled'] ? "opacity:0.1;" : "").'" type="button" id="'.$value['btn'].'" ng-model="'.$value['model'].'" ng-click="btnAcao(\''.$value['btn'].'\')" class="'.$value['classe'].'">'.$value['text'].'</button>';
	    }
	
	    echo $html;
	}
	/**
	 * Metodo para abrir o modal da webcam
	 */
	public function webcamAction()
	{
	    $this->_helper->layout()->disableLayout();
	}
	/**
	 * metodo para recortar a imagem
	 */
	public function recortarImagemAction()
	{
	    $this->_helper->layout()->disableLayout();
	}


}