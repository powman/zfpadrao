<?php

class Painel_Model_Acl extends Zend_Db_Table_Abstract
{
	
	protected $_name = 'acl';
    public $id = null;
	
	public function save(array $data)
    {
        $id = isset($data['id']) ? $data['id'] : '';
        if ($this->_dataExists($id) && $id) {
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
        $sql = $this->getAdapter()->select()->from(array('p' => 'acl'), 'count(id) as qtd');
        $res = $this->getAdapter()->fetchRow($sql);
        if (isset($res['qtd']) && $res['qtd'] > 0) {
            return true;
        } else {
            return false;
        }
    }
	
	private function _dataExistsCtrlAction(array $data)
    {
    	$controller = isset($data['controller']) ? $data['controller'] : "";
		$action = isset($data['action']) ? $data['action'] : "";
        $sql = $this->getAdapter()->select()->from(array('a' => 'acl'), 'count(id) as qtd')->where("a.controller = '".$controller."' and a.action = '".$action."'");
        $res = $this->getAdapter()->fetchRow($sql);
        if (isset($res['qtd']) && $res['qtd'] > 0) {
            return true;
        } else {
            return false;
        }
    }
    
   public static function resourceValid($request){
        // Verifica se o controller e valido
        $dispatcher = Zend_Controller_Front::getInstance()->getDispatcher();
        if (!$dispatcher->isDispatchable($request)) {
            return false;
        }
        // Verifica se a action e valida
        $front      = Zend_Controller_Front::getInstance();
        $dispatcher = $front->getDispatcher();
        $controllerClass = $dispatcher->getControllerClass($request);
        $controllerclassName = $dispatcher->loadClass($controllerClass);
        $actionName = $dispatcher->getActionMethod($request);
        $controllerObject = new ReflectionClass($controllerclassName);      
        if(!$controllerObject->hasMethod($actionName)){
            return false;   
        }       
        return true;
   }
   
   public function getAllResources(){
        $sql = $this->getAdapter()->select("controller")->from("acl");
		$res = $this->getAdapter()->fetchAll($sql);
        return $res;
    }
   
   public function resourceExists($controller=null,$action=null){
        if(!$controller || !$action) throw new Exception("Error resourceExists(), the controller/action não existe");
        $result=$this->_dataExistsCtrlAction(array("controller" => $controller,"action" => $action));
        if($result){
            return true;
        }
        return false;
    }
   
   public function createResource($controller=null,$action=null){
        if(!$controller || !$action) throw new Exception("Error resourceExists(), the controller/action não existe");
        $data=array('controller'=>$controller,'action'=>$action);
        return $this->save($data);
    }
   
    public function getCurrentRoleAllowedResources($grupo_id=null){
        if(!$grupo_id) throw new Exception("Error getCurrentUserPermissions(), grupo id e vazio");
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql='SELECT A.controller,A.action  FROM acl_to_grupo ATR INNER JOIN acl A ON A.id=ATR.acl_id WHERE grupo_id=? ORDER BY A.controller';
        $stmt = $db->query($sql,$grupo_id);
        $out= $stmt->fetchAll();
        $controller='';
        $resources=array();
        foreach ($out as $value){
            if($value['controller']!=$controller){
                $controller=$value['controller'];
            }
            $resources[$controller][]=$value['action'];
        }
        return $resources;
    }
}