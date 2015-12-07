<h2>Homes Management</h2>
<hr>
<table id = "home_list" class='selectable_table'>
	<tr>
		<th>id</th>
		<th>name</th>
	</tr>
</table>
<ul class="button-group round">
	<li><a onclick='HOMES.addDialog()' class='button small'>Add</a></li>
	<li><a onclick='HOMES.editDialog()' class='button small'>Edit</a></li>
	<li><a onclick='HOMES.remove()' class='button small'>Remove</a></li>
</ul>

<div id="add_dialog" class="reveal-modal" data-reveal>
	<h2>Add Home</h2>
	Name: <input type="text" id="add_home_name">
	<button onclick='HOMES.add()'>Add Home</button>
</div>

<div id="edit_dialog" class="reveal-modal" data-reveal>
	<h2>Edit Home</h2>
	Name: <input type="text" id="edit_home_name">
	<button onclick='HOMES.edit()'>Edit Home</button>
</div>

<div id="remove_dialog" class="reveal-modal" data-reveal>
	<h2>Remove Home</h2>
	<p>There are records, that are dependent on this record. <br>
	If you remove this home, all other records (zones, rooms, scenarios) will be removed as well.<br>
	</p><p>
	Do you really want to remove home?
	</p>
	<button onclick='HOMES.forceRemove()'>Remove Home</button>
</div>