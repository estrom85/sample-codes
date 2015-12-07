<h2>Zones Management</h2>

<hr>
<div>
	<ul id='home_selector' class="button-group radius">
		<?php 
		if(!empty($DATA['homes'])){
			foreach($DATA['homes'] as $home){
				printf("<li><a id='home_%d' class='button' onclick='ZONES.setHome(%d)'>%s</a></li>",$home['id'],$home['id'],$home['name']);
			}
		}
		?>
	</ul>
</div>
<h3 id='home_label' class=''></h3>
<table id='zones_list' class='selectable_table'>
	<tr>
		<th>id</th>
		<th>zone</th>
	</tr>
</table>

<div>
	<ul class="button-group round">
		<li><a onclick='ZONES.addDialog()' class='small button'>Add</a></li>
		<li><a onclick='ZONES.editDialog()' class='small button'>Edit</a></li>
		<li><a onclick='ZONES.remove()' class='small button'>Remove</a></li>
	</ul>
</div>

<div id='add_zone_dialog' class='reveal-modal' data-reveal>
	<h2>Add Zone</h2>
	Name: <input type="text" id="add_name_input"><br>
	<a onclick='ZONES.add()' class='button'>Add Zone</a>
</div>

<div id='edit_zone_dialog' class='reveal-modal' data-reveal>
	<h2>Edit Zone</h2>
	Name: <input type="text" id="edit_name_input"><br>
	<a onclick='ZONES.edit()' class='button'>Edit Zone</a>
</div>
