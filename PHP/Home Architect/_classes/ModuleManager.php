<?php


/**
 * Class ModuleManager
 * 
 * This class acts as class loader. Only module that is registered in this class
 * can be called from outside of application. every module has unique id by which
 * it is identified inside of class.
 *  
 *
 * @author Martin Macaj
 * 
 * Change log:
 * 
 * 2013-5-14 Martin Macaj:  class basic behaviour implemented
 * 2013-5-22 Martin Macaj:  - login check and user rights check moved to register module
 *                            method new vararg parameter added to register module method.
 *                          - module loader simplified, login check removed,
 *                          - no user right check and login check in init module. 
 *                          - $currentModule parameter introduced, to hold reference
 *                            to active module
 *                          - display Menu changed - displaying depends on active module id not 
 *                            $_GET['id'] variable
 */
class ModuleManager {
    
    /********************************************************************/
    /******************* Static parameters ******************************/
    /********************************************************************/
    
    private static $module_list;    //container of module indentificators
    private static $homedir;        //holds home directory of the application  
    private static $currentModule;
    private static $defaultModuleID;
    
    /********************************************************************/
    /********************* Public interface *****************************/
    /********************************************************************/
    
    /**
     *Initializes the class. This method must be called before any other methods
     * of the class
     * 
     * Sets all class variables and registers all modules
     * @param type $home 
     */
    public static function init($home){
        ModuleManager::$homedir=$home;
        
        /*
         * Here is place for module registration
         * 
         * Example:
         * ModuleManager::registerModule(12546,"Order management", "OMClass", "OM/OMClass.php");
         */
    }
    
    public static function getHomeDir(){
    	return ModuleManager::$homedir;
    }
    
    /**
     *Loads module and creates module class, default module is Login. It is loaded if
     * id does not exists or user is not logged in
     * 
     * @param type $id id of module
     * @return Module module instance 
     */
    public static function getModule(Application $app, $id){
		
    	if(empty($id) && !empty(self::$defaultModuleID)){
    		$id = self::$defaultModuleID;
    	}
    	
    	if(empty(self::$module_list[$id])){
    		return null;
    	}

        $module=  ModuleManager::$module_list[$id];
        $class=$module['class'];
        require_once ModuleManager::$homedir."/_classes/modules/".$module['path'];
        ModuleManager::$currentModule=new $class($app, $id);
		ModuleManager::$currentModule->init();
        return ModuleManager::$currentModule;
    }
    
    /**
     *Displays main menu of program if user is logged in
     * 
     * @return void 
     */
    public static function displayMainMenu(){
        if(empty($_SESSION['user']))return;
        
        echo "<ul class='menu nav nav-tabs'>";
        echo "<li class='menu_item";
        if(ModuleManager::$currentModule->getId()<0) echo " active";
        echo "'><a href='./'><i class='icon-home'></i> Home</a></li>";
        if(!empty(ModuleManager::$module_list)){
            foreach(ModuleManager::$module_list as $id=>$module){
                echo "<li class='menu_item";
                if(ModuleManager::$currentModule->getId()==$id) echo " active";
                echo "'><a href='./?id=$id'>".$module['label']."</a></li>";
                }
        }
        echo "<li class='menu_item'><a href='./?func=logout'>Logout</a></li>";
        echo "</ul>";
    }
    
    public static function getListOfModules(){
    	$modules = null;
    	foreach(self::$module_list as $key => $value){
    		$modules[] = array("id"=>$key,"label"=>$value['label']);
    	}
    	
    	return $modules;
    }
    
    /**
     * Returns module id by class name
     * 
     * @param string $class - class name
     * @return int module id. if module does not exists, returns -1
     */
    public static function findIdByClassName($class){
        if(empty(ModuleManager::$module_list)) return -1;
        foreach(ModuleManager::$module_list as $id=>$module)
            if(!strcmp($module['class'],$class)) return $id;
        return -1;
    }
    /******************************************************************/
    /************************Private methods **************************/
    /******************************************************************/
    
    /**
     * Registers module in the module manager
     * 
     * @param int $id id of module
     * @param string $label text that will be displayed in main menu
     * @param string $class_name class name
     * @param string $path include path 
     */
    public static function registerModule($id,$label,$class_name,$path){
        if(func_num_args() > 4 && !UserRights::checkRights(array_slice(func_get_args(), 4))) return;
        
        
        ModuleManager::$module_list[$id]['class']=$class_name;
        ModuleManager::$module_list[$id]['path']=$path;
        ModuleManager::$module_list[$id]['label']=$label;
    }
    
    public static function setDefaultModule($id){
    	if(empty(self::$module_list[$id])) return;
    	
    	self::$defaultModuleID = $id;
    }
    /**
     *Class constructor - constructor is set as private to disable instatiation
     * of the class. Class consists only of the static methods so instantiation
     * is meaningless.
     */
    private function __construct() {}
}

?>
