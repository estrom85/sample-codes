var HOMES = (function(){
	var base_url = "home/data";
	var table;
	var homes = [];
	var table_rows = [];
	
	var selected_id = -1;
	
	var refresh_table = function(){
		table = $("#home_list");
		for(row in table_rows){
			table_rows[row].remove();
		}
		table_rows = [];
		homes = [];
		
		selected_id = -1;
		
		$.ajax({
			url:base_url,
			success:function(data){
				//alert (table);
				var data = JSON.parse(data);
				var first_id = null;
				for(d in data){
					if(first_id == null) first_id = data[d].id;
					homes[data[d].id] = data[d].name;
					var row = getTableRow(data[d].id,data[d].name);
					table_rows[data[d].id] = row;
					table.append(row);
					
					
					
				}
				
				var home_id = $.cookie('home');
				if(typeof(home_id) === 'undefined' || typeof(homes[home_id]) === 'undefined') selectHome(first_id);
				else selectHome($.cookie('home'));
			}
		});
	}
	
	var getTableRow = function(id, name){
		var row = $("<tr />").click(function(){selectHome(id)});
		row.append("<td>"+id+"</td>");
		row.append("<td>"+name+"</td>");
		
		return row;
	}
	
	var selectHome  = function(id){
		if(typeof(homes[id]) === 'undefined') return;
		$(".selected").removeClass('selected');
		table_rows[id].addClass('selected');
		selected_id = id;
		$.cookie('home',id);
	}
	
	var addHome = function(){
		$.ajax({
			url:base_url,
			type:"POST",
			data:{name:$("#add_home_name").val()},
			success: function($data){
				//alert($data);
				refresh_table();
			}
		});
		$("#add_dialog").foundation('reveal','close');
	}
	
	var editHome = function(){
		if(selected_id < 0) return;
		$.ajax({
			url:base_url+"/"+selected_id,
			type:"PUT",
			data:{name:$("#edit_home_name").val()},
			success: function(data){
				//alert(data);
				refresh_table();
			}
		});
		$("#edit_dialog").foundation('reveal','close');
	}
	
	var removeHome = function(){
		if(selected_id < 0) return;
		$.ajax({
			url:base_url+"/"+selected_id,
			type:"DELETE",
			success: function($data){
				//alert($data);
				$response = JSON.parse($data);
				if($response.status === "REF_ERR"){
					$("#remove_dialog").foundation('reveal','open');
				}
				refresh_table();
			}
		});
	}
	
	var forceRemoveHome = function(){
		if(selected_id < 0) return;
		$.ajax({
			url:base_url+"/"+selected_id,
			type:"DELETE",
			data: {force : "true"},
			success: function($data){
				
				refresh_table();
			}
		});
		$("#remove_dialog").foundation('reveal','close');
	}
	
	Loader.add(refresh_table);
	
	return{
		addDialog: function(){$("#add_dialog").foundation('reveal','open');},
		editDialog: function(){
			if(selected_id<0){
				alert("no home selected");
				return;
			}
			$("#edit_home_name").val(homes[selected_id]);
			$("#edit_dialog").foundation('reveal','open');
			},
		add:addHome,
		edit:editHome,
		remove:removeHome,
		forceRemove:forceRemoveHome
	}
})();