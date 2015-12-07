(function($){
    $.fn.validateForm=function(postFunction){
        //([a-zA-Z0-9_\\u00A1-\\uFFFF])+$
        var $form=this;
        var $inputs=$form.find('input:text, input:password');
        
        var filters={
            nazov:{
                regex:/^[A-ZÁČĎÉÍĹĽŇÓŔŠŤÚŽÝ0-9].{4,50}$/,
                error: "Názov musí začínať veľkým písmenom alebo číslom (5-50 znakov)."
            },
            meno:{
                regex:/^[A-ZÁČĎÉÍĹĽŇÓŔŠŤÚŽÝ][a-záäčďéíľĺňóŕšťúžý]{2,20}$/,
                error: "Môže obsahovať len malé písmená. Prvé písmeno musí byť veľké. (3-20 znakov)."
            },
            login:{
                regex:/^[A-Za-z0-9_]{5,}$/,
                error: "Musí obsahovať minimálne 5 alfanumerických znakov."
            },
            clanok:{
                regex:/^[A-ZÁČĎÉÍĹĽŇÓŔŠŤÚŽÝ0-9].{4,100}$/,
                error: "Musí začínať veľkým písmenom alebo číslom (5-100 znakov)."
            },
            trieda:{
                regex:/^(null|[0-9].[A-Z])$/,
                error: "Nesprávny tvar triedy. (0.X)."
            },
            cislo:{
                regex:/^[0-9]+$/,
                error: "Zadajte číslo"
            },
            psswd:{
                regex:/^.{5,}$/,
                error: "Heslo musí mať minimálne 5 znakov"
            }
              
        };
        
        var validate=function(trieda, hodnota){
            var isValid=true,
            error="";
            
            if(!hodnota&&/required/.test(trieda)){
                
                error="Táto položka je povinná";
                isValid=false;
                
            }
            
            else if(/equals{[A-Za-z0-9_-]+}/.test(trieda)){
                var equals=/equals{[A-Za-z0-9_-]+}/.exec(trieda);
                equals= new String(equals);
                var first=equals.indexOf("{");
                var last=equals.indexOf("}");
                var id=equals.substring(first+1,last);
            
                if(hodnota!=$('input[name="'+id+'"]').val()){
                    error="Hodnoty musia byť rovnaké";
                    isValid=false;
                }
            }
            else{
                for (var f in filters){
                    
                    var regex=new RegExp(f);
                    if(regex.test(trieda)){
                        if(hodnota&&!filters[f].regex.test(hodnota)){
                            error=filters[f].error;
                            isValid=false;
                        }
                        break;
                    }
                    
                }
            }
            
            return{
                isValid: isValid,
                error: error
                
            }
        }
        
        var printError=function($input){
            
            var trieda=$input.attr('class'),
            hodnota=$input.val(),
            test=validate(trieda,hodnota);
            var error=test.error;
            var valid=test.isValid;
            
            var $error=$('<span class="error">'+error+'</span>'),
            $icon=$('<i class="error-icon"></i>');
            
            $input.removeClass('invalid').siblings('.error,.error-icon').remove();
            if(!valid){
                
                $error.add($icon).insertAfter($input);
                $input.addClass('invalid').siblings('.error').hide();
                $icon.hover(function(){
                    $(this).siblings('.error').toggle();
                });
            }
            
            
        };
        
        $inputs.each(function(){
            if($(this).is('.required')){ 
                printError($(this));
            }
        });
        
        $inputs.keyup(function(){
            printError($(this));
        });
        
        $form.submit(function(e){
            e.preventDefault();
            
            if($form.find('input.invalid').length){
                alert('Nesprávne zadané údaje');
            }
            else{
                postFunction();
                
            }
        });
        
        return this;
    }
})(jQuery);
    



