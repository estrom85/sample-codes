<div>
	<?php if (!empty($DATA['error'])){?>
	<div class=''><?php echo $DATA['error']?></div>
	<?php }?>
	<form action="login/login" method="post">
		Login: <input type="text" name="login"><br>
		Password: <input type="password" name="psswd"><br>
		<input type="submit" value="Log in">
	</form>
</div>