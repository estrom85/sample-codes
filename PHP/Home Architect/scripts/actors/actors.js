var ACTORS = (function(){
	var selected_home;
	
	var init = function(){
		setHome($.cookie('home'));
	}
	
	var setHome = function(id){
		if(typeof(selected_home)!='undefined' && id == selected_home) return;
		home_button = $("#home_"+id);
		if(home_button.length == 0) return;
		selected_home = id;
		$.cookie('home',id);
		$("#home_selector a.secondary").removeClass('secondary');
		home_button.addClass('secondary');
	}
	
	removeActor = function(){
		alert("remove");
	}
	
	addActor = function(){
		alert("add");
	}
	
	editActor = function(){
		alert("edit");
	}
	
	Loader.add(init);
	
	return {
		setHome: setHome,
		addActorDialog: function(){$("#add_actor_dialog").foundation('reveal','open')},
		editActorDialog:function(){$("#edit_actor_dialog").foundation('reveal','open')},
		add: addActor,
		edit: editActor,
		remove:removeActor
	}
})();