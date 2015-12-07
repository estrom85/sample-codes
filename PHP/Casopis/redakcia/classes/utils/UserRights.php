<?php


/**
 * Trieda, ktora je zodpovedna za kontrolu uzivatelskych prav daneho uzivatela
 *
 * @author mato
 */
class UserRights {
    /*
     * Parametre triedy
     */
    private $rights;
    /*
     * konstruktor
     */
    public function __construct(CDatabaza $data, $user=null) {
        $user_id="";
        if($user==null){
            if(empty($_SESSION['user']))
                return;
            $user_id=$_SESSION['user'];
        }
        else{
            $user_id=$user;
        }
        //ziska informacie z databazy
        $connected=$data->connected();
        
        if(!$connected)
            $data->connect();
        if(!$data->connected())
            return;
        $sql="SELECT pristupove_prava FROM Uzivatel WHERE uzivatel_id='$user_id'";
        $query=$data->query($sql);
        $result=  mysqli_fetch_array($query);
        $user_rights=  explode(",", $result['pristupove_prava']);
        //nacita udaje z databazy a ulozi ich do premennej $rights
        for($i=0;isset($user_rights[$i]);$i++){
            $res=$user_rights[$i];
            $res=trim($res);
            $this->rights[$res]=true;
        }
        if(!$connected)
            $data->close();
    }
    //zisti, ci uzivatel ma pozadovane pristupove pravo
    public function approved($right){
        if(empty($this->rights[$right]))
            return false;
        return $this->rights[$right];
    }
}

?>
