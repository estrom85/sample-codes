var ROOMS = (function(){
	var base_url = "rooms/data";
	var zone_url = "zones/data";
	var selected_home;
	var selected_room;
	var table_rows;
	var rooms = [];
	
	
	var init = function(){
		setHome($.cookie('home'));
		$("#zone_filter").change(refreshList);
	}
	
	var refreshZoneFilter = function(){
		$(".zone_selector_data").remove();
		$.ajax({
			url: zone_url,
			data:{home:selected_home},
			success: function(data){
				zones = JSON.parse(data);
				for(var zone in zones){
					$option = $("<option value = '" + zones[zone].id + "' class='zone_selector_data'>" + zones[zone].name + "</option>");	
					$(".zone_selector").append($option);
				}
			}
		});
	}
	
	var refreshList = function(){
		for(var i in table_rows){
			table_rows[i].remove();
		}
		table_rows = [];
		rooms = [];
		var data = {};
		data["home"] = selected_home;
		var zone = $("#zone_filter").val();
		if(zone!=''){
			data["zone"] = zone;
		}
		//alert(JSON.stringify(data));
		$.ajax({
			url:base_url,
			data:data,
			success:function(data){
				//alert(data);
				data = JSON.parse(data);
				var $table = $("#rooms_list");
				var first_index = null;
				for(d in data){
					var id = data[d].id;
					if(first_index == null) first_index = id;
					
					var name = data[d].name;
					var row = getTableRow(data[d]);
					rooms[id] = name;
					table_rows[id] = row;
					$table.append(row);
				}
				
				var room_id = $.cookie('room');
				if(typeof(rooms[room_id]) === 'undefined'){
					
					room_id = first_index;
				}
				setRoom(room_id);		
			}
		});
		
	}
	
	var getTableRow = function(rowData){
		$row = $("<tr />");
		$row.append("<td>"+rowData.id+"</td>")
			.append("<td>"+rowData.name+"</td>")
			.click(function(){setRoom(rowData.id)});
		return $row;
	};
	
	var setRoom = function(id){
		//if(typeof(selected_zone!='undefined') && id == selected_zone) return;
		if(typeof(rooms[id])==='undefined') return;
		$.cookie('room',id);
		$(".selected").removeClass('selected');
		table_rows[id].addClass('selected');
		selected_room = id;
	};
	
	
	
	var setHome = function(id){
		if(typeof(selected_home)!='undefined' && id == selected_home) return;
		home_button = $("#home_"+id);
		if(home_button.length == 0) return;
		selected_home = id;
		$.cookie('home',id);
		$("#home_selector a.secondary").removeClass('secondary');
		home_button.addClass('secondary');
		
		refreshZoneFilter();
		refreshList();
		
	}
	
	var addRoom = function(){
		var data = {};
		data['home'] = selected_home;
		data['name'] = $('#add_name_input').val();
		var zone = $('#select_zone').val();
		
		if(zone != ''){
			data['zone'] = zone;
		}
		
		//alert(JSON.stringify(data));
		
		$.ajax({
			url:base_url,
			data:data,
			type:'POST',
			success:function(){
				refreshList();
			}
		});
		
		$("#add_room_dialog").foundation('reveal','close');
	}
	
	var removeRoom = function(){
		$.ajax({
			url:base_url + "/"+selected_room,
			type:"DELETE",
			success:function(data){
				//alert(data);
				refreshList();
			}
		});
	}
	
	var editName = function(){
		$.ajax({
			url:base_url + "/" + selected_room,
			type:"PUT",
			data:{name:$("#edit_name_input").val()},
			success:function(data){
				//alert(data);
				refreshList();
			}
		});
		$("#edit_room_dialog").foundation('reveal','close');
	}
	
	var changeZone = function(){
		var zone = $("#change_zone_selector").val();
		if(zone === '') zone = 'NULL';
		//alert(zone);
		$.ajax({
			url:base_url + "/" + selected_room,
			type:"PUT",
			data:{zone:zone},
			success:function(data){
				//alert(data);
				refreshList();
			}
		});
		$("#edit_room_dialog").foundation('reveal','close');
	}
	
	Loader.add(init);
	
	return {
		setHome:setHome,
		addDialog:function(){$("#add_room_dialog").foundation('reveal','open')},
		add:addRoom,
		editNameDialog:function(){
			$("#edit_name_input").val(rooms[selected_room]);
			$("#edit_room_dialog").foundation('reveal','open');
			},
		changeZoneDialog:function(){$("#change_zone_dialog").foundation('reveal','open')},
		editName:editName,
		changeZone:changeZone,
		remove:removeRoom
	}
})();