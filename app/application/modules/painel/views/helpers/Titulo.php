<?php
class Zend_View_Helper_Awsome extends Zend_View_Helper_Abstract
{
	private $titulo = null;
	
	public function init() 
	{
		$this->titulo = new CgModuloSubmenu();
	}
	
    public function titulo()
    {
        
        $path = 'js/bower_components/components-font-awesome/css/font-awesome.css';
        
        if(!file_exists($path)){
			return false;//se o caminho nÃ£o existe retorna falso.
		}
		$css = file_get_contents($path);
		$pattern = '/\.('. $class_prefix .'(?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';
		preg_match_all($pattern, $css, $matches, PREG_SET_ORDER);
		
		$icons = array('' => '');
		foreach ($matches as $match){
			$icons[str_replace($class_prefix, '', $match[1])] = '&#x' . str_replace('\\', '', $match[2]) . '; ' . $match[1];
		}
		return $icons;
    }
}