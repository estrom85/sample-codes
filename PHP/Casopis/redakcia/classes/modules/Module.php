<?php
/**
 * Základná trieda pre jednotlivé moduly. Predstavuje základnú funkcionalitu,
 * je to prístupový bod k jednotlivým modulom. Zabezpečuje zobrazovanie požadovaných 
 * formulárov spustenie manipulačných funkcií, zobrazenie hlavnej obrazovky a aj panela
 * nástrojov.
 * 
 * @author Ing. Martin Mačaj
 */
abstract class Module {
    //zoznam všetkých spustiteľných funkcií daného programu
    private $functions;
    private $msg;
    private $enabled;
    
    //k tejto metóde sa zvyčajne pristupuje z konstruktora modulu nastavuje
    //spusitieľné funkcie
    protected function setFunction($id, $executeFunc){
        $this->functions[$id]['id']=$id;
        $this->functions[$id]['execute']=$executeFunc;
        
    }
    //nastaví formulár pre danú akciu
    protected function setForm($id, $formTitle, $formID, $formFunction){
        if(empty($this->functions[$id]))
            return;
        $this->functions[$id]['form']['name']=$formTitle;
        $this->functions[$id]['form']['function']=$formFunction;
        $this->functions[$id]['form']['id']=$formID;
        
    }
    //zakazat beh modulu
    protected function disable(){
        $this->enabled=false;
    }
    //povolit beh modulu
    protected function enable(){
        $this->enabled=true;
    }

    //ziska id pozadovaneho formulara
    protected function getFormID($id){
        return $this->functions[$id]['form']['id'];
    }
    
    //nastavi spravu
    protected function setMsg($success,$msg){
        $this->msg['success']=$success;
        $this->msg['msg']=$msg;
    }
    //zobrazí požadovaný formulár
    final public function form(){
        if(empty($_GET['func']))
            return;
        
        if(empty($this->functions[$_GET['func']]['form']))
            return;
        $form=  $this->functions[$_GET['func']]['form']['function'];
        $this->$form();
        
    }
    //spustí požadovanú funkciu programu
    final public function execute(){
        /*
         * kontroly funkcii
         */
        
        //prednastavena hodnota premnnej enabled je false
        if(empty($this->enabled))
            $this->enabled=false;
        
        //zisti ci bola zadana funkcia
        if(empty($_GET['func'])){
            $this->setMsg(false, "Nebola zadaná žiadna funkcia");
            return;
        }
        //zisti, ci zadana funkcia existuje
        if(empty($this->functions[$_GET['func']])){
            $this->setMsg(false, "Zadaná funkcia neexistuje");
            return;
        }
        //kontrola identifikatora formulara
        if((!empty($_POST['nonce']))&&(!Nonce::checkNonce($_POST['nonce']))){
            $this->setMsg(false, "Neplatný formulár.");
            return;
        }
            
        //pripojenie na databazu  
        $db=CDatabaza::getInstance();
        //zisti, ci sa uskutocnilo spojenie s databazou
        if(empty($db)){
            $this->setMsg(false, "Spojenie s databázou zlyhalo");
            return;
        }
        //skontoluje, ci je mozne spustit modul
        if(!$this->enabled){
            $this->setMsg(false, "Nemáte oprávnenie na zmenu záznamov");
            return;
        }
        //spusti vybranu funkciu zvoleneho modulu
        $call=$this->functions[$_GET['func']]['execute'];
        $this->$call();
    }

    //zobrazi spravu po vykonani akcie, ak bola akcia neuspesna zobrazi formular
    //na opatovne zadanie udajov
    final public function displayMsg(){ 
        $class='msg';
        if($this->msg['success'])
            $class=$class."-pass";
        else 
            $class=$class."-fail";
        echo "<div id='display-msg' class='$class'>".$this->msg['msg']."</div>";
        if(!$this->msg['success']){
            echo "<div id='display-form'>";
            $this->form ();
            echo "</div>";
        }
    }
    
    //zobrazi panel nastrojov
    public function toolbox(){
        echo "<ul class='toolbox'>";
        //skotroluje, ci su v module zadane nejake funkcie ak nie, uzavrie toolbox a opusti funkciu
        if(empty($this->functions)){
            echo "</ul>";
            return;
        }
        $id=  $this->getProgramID();
        $form_src_base="./redakcia/request/form.php?id=$id";
        $action_base="./redakcia/request/action.php?id=$id";
        //prechadza vsetkymi funkciami a zobrazi tlacidlo pre zobrazenie formulara ak formular k danej funcii existuje
        foreach ($this->functions as $function){
            //ak formular k danej funkcii neexistuje, prejde na dalsiu funkciu
            if(empty($function['form']))
                continue;
            //nastavenie parametrov pre funkciu zobrazujucu formular
            $func="&func=".$function['id'];
            $src=$form_src_base.$func;
            $title=  $function['form']['name'];
            $form_id=$function['form']['id'];
            $action=$action_base.$func;
            //skonstruuje javascript funkciu pre zobrazenie formulara, ktory danu funkciu spusta
            $function_desc="nastavFormular('$src','$title','$form_id','$action')";
            //zobrazi tlacidlo na paneli nastrojov
            echo "<li onclick=\"".$function_desc."\">$title</li>";
        }
        echo "</ul>";
    }
    
    //funkcia, ktora vracia ID aktivneho modulu
    abstract protected function getProgramID();
    //funcia zodpovedna za zobrazovanie jednotlivych modulov
    abstract public function display();

}

?>
