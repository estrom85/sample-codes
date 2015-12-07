/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
(function(){
    $.fn.post_edit=function(program_id,article_id,post_id,categories){
        if(!(program_id&&article_id&&post_id))
            return;
        categories=categories||{};
        var cat=this.find(".post_cat").text();
        var name=this.find(".post_name").text();
        var post=this.find(".post_content").text();
        var $this=this;
        
        var catChanger=function(){
            var $select=$("<select name='cat_id'/>");
            for(var option in categories){
                var $option=$("<option value='"+categories[option].id+"'>"+categories[option].name+"</option>");
                if(categories[option].name==cat)
                    $option.attr("selected","selected");
                $select.append($option);
            }
            return $select;
        };
        
        var editForm=function(){
            var $form=$("<form id='edit_post'/>");
            
            $form.append("<span class='edit_label'>Nazov:<br/></span>");
            $form.append($("<input type='text' size='80' name='name' value='"+name+"'>"));
            $form.append("<span class='edit_label'><br/>Kategoria:</span><br/>")
            $form.append(catChanger());
            $form.append("<span class='edit_label'><br/>Príspevok:<br/></span>");
            $form.append("<textarea name='post' cols='60' rows='8'>"+post+"</textarea><br/>");
            return $form;
        };
        
        var reset=function(){
            $(".editing").show().removeClass("editing");
            $this.addClass("editing");
            $("#edit_post").remove();
        };
        
        reset();
        $form=editForm();
        var $cancel_bttn=$("<input type='button' id='cancel_button' value='Zrušiť'>");
        $cancel_bttn.click(function(){
            reset();
        });
        $form.append($("<input type='submit'>"));
        $form.append($cancel_bttn);
        
        $form.submit(function(e){
            e.preventDefault(); 
            var data=$form.serializeArray();
            var action="redakcia/request/action.php?id="+program_id+"&article_id="+article_id+"&post_id="+post_id+"&func=edit_post";
            var src="redakcia/request/main.php?id="+program_id+"&article_id="+article_id+"&post_id="+post_id+"&mode=post";
            $.ajax({
                url:action,
                type:"POST",
                data:data,
                success:function(data){
                    alert($(data).text());
                    $this.load(src);
                }
            });
            reset();
        });
        
        
        this.hide();
        
        $form.insertAfter(this);
        
        return this;
    }
})(jQuery);

var post_filter=(function(){
    var cat_filter=-1;
    var release_filter=-1;
    var content;
    var article;
    var program;
    
    var set_filters=function(prog,art,cont){
        content=$("#"+cont);
        article=art;
        program=prog;
    }
    
    var update=function(){
        var data={};
        data['id']=program;
        data['article_id']=article;
        data['mode']="display";
        if(cat_filter>-1)
            data['cat']=cat_filter;
        if(release_filter>-1)
            data['released']=release_filter;

        $.ajax({
            url:"redakcia/request/main.php",
            type:"GET",
            data:data,
            success:function(data){
                content.html(data);

            }
        });
        
    }
    
    var set_category=function(element){
        cat_filter=$(element).val();
        update();
    }
    
    var set_release=function(element){
        release_filter=$(element).val();
        update();
    }
    
    return {
        set_category:set_category,
        set_release:set_release,
        set_filters:set_filters
    }
    
})();

(function(){
    $.fn.pageDevide=function(size){
        size=size||10;
        var $posts=[];
        
        this.find("div.display_post").each(function(){
            $posts.push($(this));
        });
        
        if($posts.length<size)
            return;
        
        var num_of_pages=Math.ceil($posts.length/size);
        for(var i=0;i<num_of_pages;i++){
        }
    
    };
})(jQuery);