<?php
defined('APP_ROOT') || define('APP_ROOT', realpath(dirname((dirname(__FILE__)))) );
defined('APP_BASE') || define('APP_BASE', realpath(APP_ROOT ."/app"));
defined('APP_LIB') || define('APP_LIB', realpath(APP_ROOT ."/lib"));
defined('APP_CONF_DIR') || define('APP_CONF_DIR', realpath(APP_BASE ."/conf"));

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()
	->setFallbackAutoloader(TRUE);

class Initializer extends Zend_Controller_Plugin_Abstract{

	/**
	 * This function sets up the include path
	 * @return bool
	 */
	public static function setupIncPath(){

		if( !defined('APP_BASE') ){
			return false;
		}

		$models = explode(PATH_SEPARATOR, get_include_path());
		array_push($models, APP_LIB);

		/* @var $file DirectoryIterator */
		$dir = new DirectoryIterator(APP_BASE . "/modules");
		foreach($dir as $file){
			if( $file->isDir() && !$file->isDot() ){
				$mod = realpath($file->getPathname() . '/models');
				if( file_exists($mod)){
					array_push($models, $mod);
				}
			}
		}

		set_include_path(implode(PATH_SEPARATOR, $models));
		return true;
	}

	protected function initDB(){

		/* @var $cnf Zend_Config_Ini */
		$cnf = Zend_Registry::get('config');
		$db = Zend_Db::factory($cnf->database->adapter, $cnf->database->params->toArray());
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
		Zend_Registry::set('db_adapter', $db);

	}

	protected function initConfig(){

		$cnf = new Zend_Config_Ini(APP_CONF_DIR ."/app.ini", 'main');
		$cnf = new Zend_Config_Ini(APP_CONF_DIR ."/app.ini", $cnf->application->version);
		Zend_Registry::set('config', $cnf);
		unset($cnf);
	}

	protected function initMVC(){

		/* @var $cnf Zend_Config_Ini */
		$cnf = Zend_Registry::get('config');
		Zend_Layout::startMvc()
			->setLayoutPath(APP_BASE . "/{$cnf->layout->dir}")
			->setLayout($cnf->layout->script);
	}

	protected function initRoutes(Zend_Controller_Request_Abstract $req){

	}

	protected function initController(){
		$fc = Zend_Controller_Front::getInstance();
		$fc->addModuleDirectory(APP_BASE . '/modules');
	}

	public function routeStartup($req){

		$this->initMVC();
		$this->initDB();
		$this->initRoutes($req);
	}

	public function __construct(){
		$this->initConfig();
		$this->initController();
	}
}


try{

	Initializer::setupIncPath();
	Zend_Controller_Front::getInstance()
		->registerPlugin(new Initializer())
		->dispatch();
}catch (Exception $ex){
	echo "An unhandled Exception was caught: <b>" . $ex->getMessage() . "</b>";
	echo "<pre>" . $ex->getTraceAsString() . "</pre>";
}