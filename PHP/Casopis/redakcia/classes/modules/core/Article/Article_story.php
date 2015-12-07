<?php


/**
 * Description of Article_story
 *
 * @author mato
 */
ProgramManager::includeProgram("Article");
class Article_story extends Article {
    /*
     * Parametre triedy
     */
    /*
     * Konstruktor
     */
    public function __construct() {
        parent::__construct();
        $this->setFunction("save", "save");
    }
    /*
     * Implementovane funkcie triedy
     */
    protected function getProgramID() {
    
        return ProgramManager::getId("Article_story");
    }
    public function display(){
        parent::display(); 
        
        if($this->article['zobrazit'])
            $this->display_show ();
        else
            $this->display_edit ();   
    }
    public function toolbox(){
        echo "<ul class='toolbox'>";
        if($this->article['zobrazit']){
            if($this->accessRights->approved('DROP'))
                $this->displayActionButton('release', "Stiahni článok", "release:0"); 
        }
        else{
            
            $this->displayActionButton('save',"Ulož","art_content:CKEDITOR.instances.art_content.getData()",false);
            
            if($this->accessRights->approved('RELEASE')){
                $this->displayActionButton('release', "Zverejni článok", "release:1");
            }
            
            if($this->accessRights->approved('ADD')){
                echo "<hr/>";
                $this->displayFormButton('edit', "Zmeň názov článku");
                $this->displayFormButton('change_topic', "Zmeň rubriku");
            }
            if($this->accessRights->approved('ASSIGN')){
                echo "<hr/>";
                $this->displayFormButton('add_user', "Priraď redaktora");
                $this->displayFormButton('rem_user', "Odstráň redaktora");
            }
            
            
            
        }
        echo "<hr/>";
        $this->displayBackButton();
        echo "</ul>";
    }
    /*
     * Zakladne rozhranie triedy
     */
    protected function save(){
        if($this->article['zobrazit'])
            return;
        $path=  ProgramManager::getHomeDir()."/../articles/".$this->article['id']."/".$this->article['id'].".art";
        $article=fopen($path,'w');
        if($article){
            fwrite($article, $_POST['art_content']);
            fclose($article);
        }
        else{
            $this->setMsg(false, "nepodarilo sa otvorit subor");
        }
        
        
        $this->setTimeStamp();
        $this->setMsg(true, "Článok úspešne uloženy");
    }
    /*
     * Sukromne funkcie triedy
     */
    
    private function display_edit(){
       
        echo "";
        echo "<textarea id='art_content'>";
        $path=  ProgramManager::getHomeDir()."/../articles/".$this->article['id']."/".$this->article['id'].".art";
        
        
        
        if(!file_exists($path))
            echo "Prazdy clanok";
        else{
            $article=  fopen($path, "r");
            while(!feof($article))
                echo fgets($article);
            fclose($article);
        }
        echo "</textarea>";
        echo "<script type='text/javascript'>
            function load_editor(){
                var editor=CKEDITOR.instances['art_content'];
                if(editor) editor.destroy(true);
                CKEDITOR.replace('art_content',
                        {
                            customConfig: '../config/my_config.js',
                            toolbar: 'Article',   
                            filebrowserBrowseUrl: 'redakcia/utilities/browser/browser.php?article_id=".$this->article['id']."',
                            baseHref:'../'

                        });
            };
            var src='redakcia/utilities/ckeditor/ckeditor.js';
            if(!scriptloader.empty(src))
                load_editor();
            else
                scriptloader.load_script('redakcia/utilities/ckeditor/ckeditor.js','js',load_editor);

            </script>";
    }
    
    private function display_show(){
        
        echo "";
        $path=  ProgramManager::getHomeDir()."/../articles/".$this->article['id']."/".$this->article['id'].".art";
        $article=  fopen($path, "r");
        echo "<div style='text-align:left'>";
        if(!$article)
            echo "Prazdy clanok";
        else{
            while(!feof($article))
                echo fgets($article);
            fclose($article);
        }
        echo "</div>";
        
    }
    
    
}

?>
