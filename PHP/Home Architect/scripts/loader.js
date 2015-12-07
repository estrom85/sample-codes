var Loader = (function(){
	var functions = new Array();
	
	var addFunction = function(func){
		functions.push(func);
	}
	
	var onLoad = function(){
		for(var f in functions){
			functions[f]();
		}
	}
	
	$(document).ready(onLoad);
	
	return {
		add:addFunction
	}
})();

