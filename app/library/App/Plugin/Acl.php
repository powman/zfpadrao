<?php
// http://www.developerfiles.com/creating-acl-with-database-in-zend-framework/

class App_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{

   public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();
        $authModel=new Painel_Model_CaAuth();
        if (!$auth->hasIdentity()){
            //Se o usuário site não existir, pega o usuario do banco com o id=1
            //$authModel->authenticate(array('email'=>'demo@site.com','senha'=>'123'));
            $request->setControllerName('index');
            $request->setActionName('login');
            //$this->_helper->redirector->goToRoute( array('controller' => 'ca-auth'), null, true);
            //$baseUrl = new Zend_View_Helper_BaseUrl();
            //$this->getResponse()->setRedirect($baseUrl->baseUrl().'/index/login');
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
        $grupo=$tblGrupo->getById($grupo_id);
        $grupo=isset($role['nome']) ? $role['nome'] : "";
        // Instancia a Acl
        $acl = new Zend_Acl();
        // adciona o grupo
        $acl->addRole(new Zend_Acl_Role($grupo));
        

        if($grupo_id==3){//If grupo_id=3 "Admin" não cria os resources
            $acl->allow($grupo);
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
            $acl->allow($grupo, "index",array("login"));
            $acl->allow($grupo, "error",array("error"));
            foreach($userAllowedResources as $controllerName =>$allowedActions){
                $arrayAllowedActions=array();
                foreach($allowedActions as $allowedAction){
                    $arrayAllowedActions[]=$allowedAction;
                }
                $acl->allow($grupo, $controllerName,$arrayAllowedActions);
            }
			
			//Salva a Acl no Registro
	        Zend_Registry::set('acl', $acl);
	        //Verifica se você tem acesso, senão manda para o acesso negado.
	        if(!$acl->isAllowed($grupo,$controller,$action)){
	            $request->setControllerName('error');
	            $request->setActionName('acesso-negado');
	            return;
	        }

		}     
           
    }
}