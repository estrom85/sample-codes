/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var filter_manager=(function filter(){
    var filters=new Array();
    var content;
    var program;
    var page=1;
    var max_page=1;
    //alert("ok");
    var article=-1;
    
    var filter_exists=function(id){
        if(!filters.length)
            return false;
        for(var i=0;i<filters.length;i++){
            if(filters[i].attr('id')==id)
                return true;
        }
        return false;
    };
    
    var remove_filter=function(id){
        var temp=new Array();
        var element;
        while(filters.length){
            element=filters.pop();
            if(element.attr('id')==id)
                break;
            
            temp.push(element);
            element=null;
        }
        
        filters=filters.concat(temp);
        //display_filters();
        return element;
    };
    
    var display_filters=function(){
        var filt="";
        for(var i=0;i<filters.length;i++)
            filt+=filters[i].attr('id')+",";
        alert(filt);
    };
    
    var add_filter=function(id){
        var value;
        
        if(filter_exists(id)){
            
            value=remove_filter(id).val();
            
        }
        var element=$('#'+id);
        if(element.is("input"))
            element.keyup(apply_filter);
        else
            element.change(function(){apply_filter()});
        
        if(value)
            element.val(value);
        filters.push(element);
        
    };
    
    var apply_filter=function(){
        //alert(content);
        var src="redakcia/request/main.php?id="+program+"&mode=display";
        if(article>0)
            src+="$article_id="+article;
        var request="";
        
        for(i=0;i<filters.length;i++){   
            if(filters[i].val()){
                var value=""+filters[i].val();
                value=value.trim().replace(/[ ]+/g,"+");
                
                request=request+"&"+filters[i].attr('name')+"="+value;
            }
                
        }
       
        src+=request+"&page="+page;
        //alert(src);
        //alert(src);
        $("#"+content).load(src);
        
    };
    
    var next_page=function(){
        if(page+1>max_page)
            return;
        page++;
        apply_filter();
    };
    
    var prev_page=function(){
        if(page-1>0)
        page--;
        apply_filter();
    };
    
    var set_page=function (p){
        if(p<1||p>max_page)
            return;
        page=p;
        apply_filter();
    };
    var reset_filters=function (){
        filters=[];
    };
    
    var set_manager=function(cont,prog){
      content=cont;
      program=prog;
     
    };
    
    var set_article=function(art){
        article=art;
    };
    
    var set_max_page=function(max_pg){
        max_page=max_pg;
    }
    return{
        add:add_filter,
        apply:apply_filter,
        next:next_page,
        prev:prev_page,
        set_page:set_page,
        reset:reset_filters,
        set_manager:set_manager,
        set_max_page:set_max_page,
        set_article:set_article
    };
    
})();

