<?php 
	$module = $APP->getActiveModuleId();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<base href="<?php echo Location::getBaseUrl(); ?>" />
<title><?php echo $APP->getTitle(); ?></title>

<link rel="stylesheet" type="text/css" href="foundation/css/normalize.css"/>
<link rel="stylesheet" type="text/css" href="foundation/css/foundation.css"/>
<link rel="stylesheet" type="text/css" href="styles/styles.css">
<?php $APP->printStyleTags();?>


<script type="text/javascript" src="scripts/jquery-2.1.3.min.js"></script>
<script type="text/javascript" src="scripts/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="foundation/js/vendor/jquery.cookie.js"></script>
<script type="text/javascript" src="foundation/js/vendor/modernizr.js"></script>
<script type="text/javascript" src="foundation/js/foundation.min.js"></script>
<script type="text/javascript" src="foundation/js/vendor/fastclick.js"></script>
<script type="text/javascript" src="scripts/loader.js"></script>
<?php $APP->printScriptTags();?>

</head>
<body>
	<div id="main" class="container">
		<div class="row">
			<div class="col-md-12">
				<h1>Home Architect</h1>
			</div>
		</div>
		<div class="row">
			<div class="large-3 columns">
				<ul class="side-nav">
					<?php foreach($APP->getModuleList() as $m){
						if(strcmp($m['id'],"login") == 0) continue;
						printf("<li %s><a href='%s'>%s</a></li>\n",
							strcmp($module,$m['id'])==0?"class='active'":"",$m['id'],$m['label']);
					}
					if(!empty($_SESSION['user'])){
					?>
					<li><hr></li>
					<li><a href="login/logout">Logout</a></li>
					<?php 
					}
					?>
					
				</ul>
			</div>
			<div class="large-9 columns">
				<?php $APP->showView(); ?>
			</div>
		</div>
		<div class="row">
			
			</div>
		<div class="row">
			<div class="col-md-12">Bang</div>
		</div>
	</div>
</body>
</html>
