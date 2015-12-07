
<?php


/**
 * Login - trieda zodpovedna za prihlasovanie sa do redakcie
 *
 * @author mato
 */


class Login extends Module {
    /*
     * Parametre triedy
     */
    
    private $success=true;
    private $db=true;
    private $error_msg;
    
    /*
     * Konstruktor
     */
    public function __construct(){
        $this->setFunction("login", "login_func");
        $this->setFunction("logout", "logout_func");
        $this->enable();
    }
    
    /*
     * Main display function
     */
    public function display() {
        echo "<div id='main'>";
        $this->displayErrorMsg();
        $this->display_member_list();
        echo "<div id='door'><img src='images/door.jpg'></div>";
        echo "<div id='label'>Redakcia internetového časopisu</div>";
        //display login form
        echo "<div id='login' name='login' style='login'>";
        echo "<form method='post' action='./login.php?func=login'>";
        echo "<input type='hidden' name='id' value='".ProgramManager::getId("Login")."'/>";
        echo "<input type='hidden' name='func' value='login'/>";
        echo "<span style='font-size:25px'>Nepovolaným vstup zákazaný!!!</span></br></br>";
        echo "<i>Prihlasovacie meno:</i></br>";
        echo "<input name='usr_name' type='text'/></br>";
        echo "<i>Heslo:</i></br>";
        echo "<input name='psswd' type='password'/></br></br>";
        echo "<input style='font-family:DesyrelRegular' type='submit' value='Vstúpiť'/>";
        echo "</form></div></div>";


    }
    
    /*
     * main logic of program
     */
    
    protected function login_func(){
        //connect to database
       
       $data=  CDatabaza::getInstance();
       $data->connect();
       if(!$data->connected()){
           //if connection failed display error message
           $this->db=false;
           $this->success=false;
           $this->error_msg="Nemôžem sa pripojiť na databázu.";
           return;
       }
       
       //retrieve posted login information
       $login="";
       $psswd="";
       if(empty($_POST['usr_name'])||empty($_POST['psswd'])){
           $this->success=false;
           $this->error_msg="Zadaj prihlasovacie meno a heslo.";
           $data->close();
           return;
       }
       if (get_magic_quotes_gpc()){
            $_GET = array_map('stripslashes', $_GET);
            $_POST = array_map('stripslashes', $_POST);
       }
           
       
           //escape login name
            $login= addslashes($data->escape_string($_POST['usr_name']));
            $psswd= addslashes($data->escape_string($_POST['psswd']));
            $psswd= md5($psswd);
       
       
       //select table which contains user information
       $table="Uzivatel";
       
       //selects data from user table
       $sql="SELECT * FROM $table WHERE prihlasovacie_meno='$login' AND heslo='$psswd'";
       $result=$data->query($sql);
       
       
       //if exists one record, login is successfull else login failed
       if(mysqli_num_rows($result)!=1){
           $data->close();
           $this->success=false;
           $this->error_msg="Pokus o neoprávnený prístup do redakcie.";
           return;
       }
       
       //get user id from table and store it into session
       $user=  mysqli_fetch_array($result);
       $_SESSION['user']=$user['uzivatel_id'];
       setcookie('user',$user['uzivatel_id']);
       $data->close();
       
       //after succesfull login switch to intro program
       //header ("Location: ./?id=".ProgramManager::getId("Intro"));
       header("Location: ./");
       exit;
    }
    
    protected function logout_func(){
        session_destroy();
        setcookie('user','',time()-3600);
        header ("Location: ./");
        exit;
    }

    

    protected function getProgramID() {
        
    }
     
    private function displayErrorMsg(){
        
        ?>
        <div id='error_msg_wrapper'>
        <?php if(!$this->success){?>
        <div id='error_msg_warning'>Varovanie!!!</div>
        <span id='error_msg_content'><?php echo $this->error_msg;?></span>
        
        </div>
    <script type="text/javascript">
    (function warning(){
        
        var element=document.getElementById("error_msg_warning");
        
        var display=(element.style.visibility=="hidden");
        if(display)
            element.style.visibility="visible";
        else
            element.style.visibility="hidden";
        window.setTimeout(warning, 500);
    })();
    </script>
        <?php
    }
    else 
        echo "</div>";

    } 
    private function display_member_list(){
        ?>
        <div id='member_list'>
            Redakcná rada:
            <ul>
                <li>Alexandra Kužmová</li>
                <li>Sebastián Kvašňák</li>
                <li>Gabriela Sedláková</li>
                <li>Adam Humeňanský</li>
                <li>Richard Ondko</li>
                <li>Matúš Marton</li>
                <li>Slavomír Šmídl</li>
                <li>Diana Olšiaková</li>
                <li>Laura Uchalová</li>
                <li>Natália Miščíková</li>
                <li>Filip Mišenčík</li>
                <li>Filip Karniš</li>
            </ul>
        </div>"
        <?php
    }
}
?>
