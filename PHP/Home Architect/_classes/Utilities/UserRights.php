<?php
define("USR_ADMIN", 1);



/**
 * Class UserRights
 * 
 * Class acts as user right checker. used for check if current user has right
 * to access to modules or functions.
 *  
 *
 * @author Martin Macaj
 * 
 * Change log:
 * 
 * 2013-5-14 Martin Macaj:  class basic behaviour implemented
 * 
 */
class UserRights {
    
    public static function checkRights(){
    	
    	//print_r(func_get_args());
    	if(func_num_args() == 0) return true;
    	//print_r($_SESSION['user']);
        if(empty($_SESSION['user'])) return false;
        
        $right=$_SESSION['user']['type'];
        //echo $right;
        if($right==USR_ADMIN) return true;
        
        $right_groups=  func_get_args();
       // print_r($right_groups);
        if(empty($right_groups)) return false;
        /*
        foreach($right_groups as $group){
            if(is_array($group))
            if($group==$right)
                return true;
        }
        return false;     
         */
        return UserRights::checkRight($right_groups,$right);
    }
    
    private static function checkRight($array, $user){
        if(!is_array($array))
            return $array==$user;
        foreach($array as $right)
            if(UserRights::checkRight($right,$user)) 
                    return true;
        return false;
    }
}

?>
