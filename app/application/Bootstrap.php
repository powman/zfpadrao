<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAutoloader()
	{
    	$autoloader = Zend_Loader_Autoloader::getInstance();
    	$autoloader->registerNamespace('App');
    	
	}
	
	public function _initAutoload()
	{
	    $modules = array(
	            'Painel',
		    'Site',
	    );
	
	    foreach ($modules as $module) {
	        $autoloader = new Zend_Application_Module_Autoloader(array(
	                'namespace' => ucfirst($module),
	                'basePath'  => APPLICATION_PATH . '/modules/' . strtolower($module),
	        ));
	    }
	
	    return $autoloader;
	}
	
    
    protected function _initPlugins()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $bootstrap = $this->getApplication();
        if ($bootstrap instanceof Zend_Application) {
            $bootstrap = $this;
        }
        $front = $bootstrap->getResource('FrontController');
        $bootstrap->bootstrap('FrontController');
        $front = $bootstrap->getResource('FrontController');
        $front->registerPlugin(new App_Plugin_Acl(Zend_Db_Table::getDefaultAdapter()));
        $front->registerPlugin(new App_Plugin_Layout());
        
        
        
    }
    
    
}
