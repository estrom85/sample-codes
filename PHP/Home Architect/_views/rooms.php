<h2>Rooms Management</h2>

<hr>
<div>
	<ul id='home_selector' class="button-group radius">
		<?php 
		if(!empty($DATA['homes'])){
			foreach($DATA['homes'] as $home){
				printf("<li><a id='home_%d' class='button' onclick='ROOMS.setHome(%d)'>%s</a></li>",$home['id'],$home['id'],$home['name']);
			}
		}
		?>
	</ul>
</div>
<h3 id='home_label' class=''></h3>
Select Zone: 
<select id='zone_filter' class='zone_selector'>
	<option value=''>All</option>
</select>
<table id='rooms_list' class='selectable_table'>
	<tr>
		<th>id</th>
		<th>room</th>
	</tr>
</table>

<div>
	<ul class="button-group round">
		<li><a onclick='ROOMS.addDialog()' class='small button'>Add</a></li>
		<li><a onclick='ROOMS.editNameDialog()' class='small button'>Edit Name</a></li>
		<!-- <li><a onclick='ROOMS.changeZoneDialog()' class='small button'>Change Zone</a></li>-->
		<li><a onclick='ROOMS.remove()' class='small button'>Remove</a></li>
	</ul>
</div>

<div id='add_room_dialog' class='reveal-modal' data-reveal>
	<h2>Add Room</h2>
	Zone:
	<select id='select_zone' class='zone_selector'>
		<option value=''>None</option>
	</select>
	Name: <input type="text" id="add_name_input"><br>
	<a onclick='ROOMS.add()' class='button'>Add Room</a>
</div>

<div id='edit_room_dialog' class='reveal-modal' data-reveal>
	<h2>Change room name</h2>
	Name: <input type="text" id="edit_name_input"><br>
	<a onclick='ROOMS.editName()' class='button'>Edit Name</a>
</div>

<div id='change_zone_dialog' class='reveal-modal' data-reveal>
	<h2>Change Zone</h2>
	Zone: 
	<select size="5" id='change_zone_selector' class='zone_selector' multiple>
		<option value =''>None</option>
	</select>
	<a onclick='ROOMS.changeZone()' class='button'>Change Zone</a>
</div>

