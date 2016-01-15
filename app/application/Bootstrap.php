<?php
/**
 * 
 * @author Paulo Henrique
 * @see http://www.pauloph.com.br
 *
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	
	public function _initAutoload()
	{
	    $modules = array(
	            'Site',
		        'Painel',
	            'Webservice',
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
        
        
        
    }
    
    
}
