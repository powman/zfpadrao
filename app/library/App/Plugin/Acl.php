<?php
// http://www.developerfiles.com/creating-acl-with-database-in-zend-framework/

class App_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{

   public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        
        $auth = Zend_Auth::getInstance();
        $authModel=new Painel_Model_CaAuth();
        if (!$auth->hasIdentity()){
            $controller = $request->getControllerName();
            $action = $request->getActionName();
            //Se o usuário site não existir, pega o usuario do banco com o id=1
            //$authModel->authenticate(array('email'=>'demo@site.com','senha'=>'123'));
            // se for diferente de logar e error redireciona para o login
            if($action != "logar" && $action != "error"){
                $request->setControllerName('index');
                $request->setActionName('login');
            }
            return;
        }
 
        $request=$this->getRequest();
        $aclResource=new Painel_Model_Acl();
        //verifica se o controle e a action existe, senao manda para o erro 404
        if( !$aclResource->resourceValid($request)){
            $request->setControllerName('error');
            $request->setActionName('error');
            return;
        }
 
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        //Verifica se existe o resource no banco de dados, senão cria ele.
        if( !$aclResource->resourceExists($controller, $action)){
            $aclResource->createResource($controller,$action);
        }
        //Get role_id
        $grupo_id=$auth->getIdentity()->grupo_id;
		$tblGrupo = new Painel_Model_CaGrupo();
        // Instancia a Acl
        $acl = new Zend_Acl();
        // adciona o grupo
        $acl->addRole(new Zend_Acl_Role($grupo_id));
        

        if($grupo_id==3){//If grupo_id=3 "Admin" não cria os resources
            //Mostra todos os controllers
            $resources=$aclResource->getAllResources();
            // Add the existing resources to ACL
            foreach($resources as $resource){
                if(isset($resource["controller"]))
                    $acl->add(new Zend_Acl_Resource($resource["controller"]));
            }
            $acl->allow($grupo_id);
            Zend_Registry::set('acl', $acl);
        }else{
            //Mostra todos os controllers
            $resources=$aclResource->getAllResources();  
            // Add the existing resources to ACL
            foreach($resources as $resource){
            	if(isset($resource["controller"]))
                	$acl->add(new Zend_Acl_Resource($resource["controller"]));
            }  
			
			//Pega as permissao deste grupo
            $userAllowedResources=$aclResource->getCurrentRoleAllowedResources($grupo_id);
			
			// Adciona as permissão no ACL
            $acl->allow($grupo_id, "index",array("login","logar"));
            $acl->allow($grupo_id, "error",array("error"));
            foreach($userAllowedResources as $controllerName =>$allowedActions){
                $arrayAllowedActions=array();
                foreach($allowedActions as $allowedAction){
                    $arrayAllowedActions[]=$allowedAction;
                }
                $acl->allow($grupo_id, $controllerName,$arrayAllowedActions);
            }
			
			//Salva a Acl no Registro
	        Zend_Registry::set('acl', $acl);
	        
	        //Verifica se você tem acesso, senão manda para o acesso negado.
	        if(!$acl->isAllowed($grupo_id,$controller,$action)){
	            $request->setControllerName('error');
	            $request->setActionName('acesso-negado');
	            return;
	        }

		}     
           
    }
}