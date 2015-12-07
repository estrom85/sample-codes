<h2>Actors Management</h2>
<hr>
<div>
	<ul id='home_selector' class="button-group radius">
		<?php 
		if(!empty($DATA['homes'])){
			foreach($DATA['homes'] as $home){
				printf("<li><a id='home_%d' class='button' onclick='ACTORS.setHome(%d)'>%s</a></li>",$home['id'],$home['id'],$home['name']);
			}
		}
		?>
	</ul>
</div>
<table class="selectable_table">
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Room</th>
  </tr>
</table>
<div>
	<ul class="button-group round">
		<li><a onclick='ACTORS.addActorDialog();' class='small button'>Add</a></li>
		<li><a onclick='ACTORS.editActorDialog();' class='small button'>Edit</a></li>
		<li><a onclick='ACTORS.remove();' class='small button'>Remove</a></li>
	</ul>
</div>

<div id='add_actor_dialog' class='reveal-modal' data-reveal>
	<h2>Add Actor</h2>
	Room:
	<select id='select_room' class='room_selector'>
		<option value=''>None</option>
	</select>
	Name: <input type="text" id="add_name_input"><br>
	<a onclick='ACTORS.add();' class='button'>Add Actor</a>
</div>

<div id='edit_actor_dialog' class='reveal-modal' data-reveal>
	<h2>Edit Actor</h2>
	Name: <input type="text" id="edit_name_input"><br>
	<a onclick='ACTORS.edit();' class='button'>Edit Actor</a>
</div>


