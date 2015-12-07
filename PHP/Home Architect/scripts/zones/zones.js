var ZONES = (function(){
	var base_url = "zones/data";
	var selected_home;
	var selected_zone;
	var table_rows;
	var zones;
	
	var init = function(){
		setHome($.cookie('home'));
		//setZone($.cookie('zone'));
	};
	
	var refreshList = function(){
		for(i in table_rows){
			table_rows[i].remove();
		}
		table_rows = [];
		zones = [];
		if(typeof(selected_home)==='undefined') return;
		$.ajax({
			url:base_url,
			data:{home:selected_home},
			success:function(data){
				data = JSON.parse(data);
				var $table = $("#zones_list");
				var first_index = null;
				for(d in data){
					var id = data[d].id;
					if(first_index == null) first_index = id;
					
					var name = data[d].name;
					var row = getTableRow(data[d]);
					zones[id] = name;
					table_rows[id] = row;
					$table.append(row);
				}
				
				var zone_id = $.cookie('zone');
				if(typeof(zones[zone_id]) === 'undefined'){
					
					zone_id = first_index;
				}
				setZone(zone_id);
				
			}
		});		
	}
	
	var getTableRow = function(rowData){
		$row = $("<tr />");
		$row.append("<td>"+rowData.id+"</td>")
			.append("<td>"+rowData.name+"</td>")
			.click(function(){setZone(rowData.id)});
		return $row;
	};
	
	var setHome = function(id){
		if(typeof(selected_home)!='undefined' && id == selected_home) return;
		home_button = $("#home_"+id);
		if(home_button.length == 0) return;
		selected_home = id;
		$.cookie('home',id);
		$("#home_selector a.secondary").removeClass('secondary');
		home_button.addClass('secondary');
		
		refreshList();
	};
	
	var setZone = function(id){
		//if(typeof(selected_zone!='undefined') && id == selected_zone) return;
		if(typeof(zones[id])==='undefined') return;
		$.cookie('zone',id);
		$(".selected").removeClass('selected');
		table_rows[id].addClass('selected');
		selected_zone = id;
	};
	
	var showAddDialog = function(){
		$('#add_zone_dialog').foundation('reveal','open');
	};
	
	var showEditDialog = function(){
		$("#edit_name_input").val(zones[selected_zone]);
		$('#edit_zone_dialog').foundation('reveal','open');
	};
	
	var showRemoveDialog = function(){
		
	};
	
	var addZone = function(){
		$.ajax({
			url:base_url,
			type:"POST",
			data:{name:$("#add_name_input").val(),home:selected_home},
			success:function(){
				refreshList();
			}
		});
		$("#add_zone_dialog").foundation('reveal','close');
	}
	
	var editZone = function(){
		
		$.ajax({
			url:base_url + "/" + selected_zone,
			type:"PUT",
			data:{name:$("#edit_name_input").val()},
			success:function(data){
				//alert(data);
				refreshList();
			}
		});
		$("#edit_zone_dialog").foundation('reveal','close');
	}
	
	var removeZone = function(){
		$.ajax({
			url:base_url + '/' + selected_zone,
			type:"DELETE",
			success:function(data){
				refreshList();
			}
		});
	}
	
	Loader.add(init);
	
	return{
		setHome:setHome,
		addDialog:showAddDialog,
		editDialog:showEditDialog,
		add:addZone,
		edit:editZone,
		remove:removeZone
	}
})();