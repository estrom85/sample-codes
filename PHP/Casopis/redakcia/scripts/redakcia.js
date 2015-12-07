/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var currentDisplaySettings;

function logout(){
    window.location.replace("./redakcia/login.php?func=logout");
}

function checkLogin(){
    
    if(!findCookie("user")) {
        logout();
        return false;
    }
    return true;
    
}
function findCookie(name){
    var cookies=document.cookie.split(";");
    var cookie;
    for(var i=0;i<cookies.length;i++){
        cookie=cookies[i].substr(0,cookies[i].indexOf("="));
        cookie=cookie.replace(/^\s+|\s+$/g,"");
        if(cookie==name)
            return true;
    }
    return false;
}

function incializujDialogoveOkno(){
    $("#dialog").dialog({autoOpen:false});
    $("#dialog").dialog("option","minWidth",450);
    $("#dialog").dialog("option","modal",true);
    $("#dialog").dialog("option","buttons",{
        "Zatvor":function(){
            $(this).dialog("close");
        }
    });
}

function nastavFormular(src,title,form_id,action){
    if(!checkLogin()) return;
    
    $("#dialog").load(src, function(){
        $("#"+form_id).validateForm(function(){
            odosliFormular(form_id,action);
        
        });   
    });
    
    
    $("#dialog").dialog("option","title",title);
    
    $("#dialog").dialog("option","buttons",{
        "Odošli":function(){
            //odosliFormular(form_id,action,verify);
            $("#"+form_id).trigger('submit');
            
    }, 
    "Zatvor":function(){
            $(this).dialog("close");
        }});
    $("#dialog").dialog("open");
    
}

function odosliFormular(form_id,action){
        var form_data=$("#"+form_id).serialize();
        $.post(action,form_data,function(data){
            $("#dialog").empty().append(data);
            nastavPracovnuPlochu(currentDisplaySettings);
            
            var form=$("#display-form");
            if(form.length!=0){
                
                $("#"+form_id).validateForm(function(){
                    odosliFormular(form_id,action,verify);
        
                });
                
                $("#dialog").dialog("option","buttons",{
                    "Odošli":function(){
                        //odosliFormular(form_id,action,verify);
                        $("#"+form_id).trigger('submit');
            
                     }, 
                    "Zatvor":function(){
                        $(this).dialog("close");
                    }});
                    
            }
            else{
                $("#dialog").dialog("option","buttons",{
                    "Zatvor":function(){
                        $(this).dialog("close");
                    }});
            }
        })
        
}

function nastavProgram(settings){
    if(!checkLogin()) return;
    nastavPracovnuPlochu(settings);
    nastavPanelNastrojov(settings);
}

function nastavPracovnuPlochu(settings){
    
    //reset_filters();
    settings=settings||{};
    //alert(settings.topic_key);
    currentDisplaySettings=settings;
    //pred načítaním programu vyčistí všetky skripty
    //scriptloader.clear();
    $.ajax({
        url: "./redakcia/request/main.php",
        data: settings,
        type: "GET",
        success: function(data){
            $("#main_window").html(data);
        }
        
    });
}

function nastavPanelNastrojov(settings){
    settings=settings||{};
    $.ajax({
       url: "./redakcia/request/toolbox.php",
       data: settings,
       type: "GET",
       success: function(data){
           $("#toolbox").html(data);
       }
    });
}


function spustiProgram(id,func,data){
   if(!checkLogin()) return;
    if(!(id||func))
        return;
    
    $.ajax({
        url: "./redakcia/request/action.php?id="+id+"&func="+func,
        data: data,
        type: "POST",
        success: function(data){
            var message=$(data).html();
            alert(message);
        }
        
    });
    nastavPracovnuPlochu(currentDisplaySettings);
}

function potvrdASpustiProgram(id,func,data,text){
    if(confirm(text)){
        spustiProgram(id,func,data);
    }
}

//dynamické nahrávanie skriptov a css
var scriptloader=(function (){
    var loaded_scripts="";
    var elements=[];
    var check_script=function(src){
        if(loaded_scripts.indexOf("["+src+"]")==-1)
            return true;
        return false;
    };
    
    var load_script=function(src,type,callback){
        callback=callback||function(){};
        if(!check_script(src))
            return;
        
        var fileref;
        if(type=="js"){
            fileref=document.createElement("script");
            fileref.setAttribute("type","text/javascript");
            fileref.setAttribute("src",src);
            
        }
        if(type=="css"){
            fileref=document.createElement("link");
            fileref.setAttribute("type","text/css");
            fileref.setAttribute("rel","stylesheet");
            fileref.setAttribute("href",src);
        }
        if(typeof fileref!="undefined"){
            fileref.onload=callback;
            fileref.onreadystatechange=function(){
                if(this.readyState=='complete')
                    callback();
            }
            document.getElementsByTagName("head")[0].appendChild(fileref);
            elements.push(fileref);
            loaded_scripts+="["+src+"]";
            
            
        }
            
    };
    
    var clear_scripts=function(){
        
       loaded_scripts="";
       while(elements.length){
           var element=elements.pop();
           element.parentNode.removeChild(element);
       }
    };
    
    return {
        load_script:load_script,
        clear:clear_scripts,
        empty:check_script
    };
    
})();