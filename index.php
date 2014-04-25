<?php
date_default_timezone_set('Australia/Brisbane');
session_start();

function o($v) {
	if (isset($_SESSION[$v])) {
		return $_SESSION[$v];
	}
	
	return '';
}

if (isset($_POST['content'])) {
	foreach ($_POST as $key => $value) {
		$_SESSION[$key] = $value;
	}
	
	// start generate
	$c = $_POST['content'];
	
	
	$_POST['contacts'] = str_replace("\n", '<br>', $_POST['contacts']);
	$_POST['attention'] = str_replace("\n", '<br>', $_POST['attention']);
	
	foreach ($_POST as $key => $value) {
		$c = str_replace('{'.$key.'}', $value, $c);
	}
	
	$name = trim(explode('<br>', $_POST['contacts'])[0]);
	$c = str_replace('{name}', $name, $c);
	$c = str_replace('{today}', date('j F, Y'), $c);
	
	$html = file_get_contents('html.html');
	$html = str_replace('{body}', $c, $html);
	
	file_put_contents('out/out.html', $html);
	@unlink('out/out.pdf');
	
	$out = dirname(__FILE__).'/out/out.html';
	$pdf = dirname(__FILE__).'/out/out.pdf';
	shell_exec("wkhtmltopdf \"{$out}\" \"{$pdf}\"");
	
	$filename = (empty($name)) ? 'Cover Letter' : $name.' - Cover Letter';
	$iframe = '<iframe width="0" height="0" src="output.php?filename='.$filename.'"></iframe>';
}

if (!isset($_SESSION['content'])) {
	$_SESSION['content'] = file_get_contents('template.html');
	$_SESSION['salutation'] = 'To whom it may concern';
}

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Cover letter generator</title>
	
	
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="css/summernote.css"/>
	<link rel="stylesheet" href="css/app.css"/>
	
	<script src="//code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<script src="js/summernote.min.js"></script>


</head>
<body>
	<div class="container">
		<form action="index.php" method="post">
		<div class="row">
			<div class="col-md-10 left">
				<div class="row">
					<div class="form-group pull-right">
						<label for="attention">Attention: Company name, Address etc (in multiple lines)</label>
						<textarea class="form-control attention" name="attention" placeholder="Attention"><?= o('attention') ?></textarea>
					</div>

					<div class="form-group pull-left">
						<label for="attention">Your Name, Address etc (in multiple lines)</label>
						<textarea class="form-control contacts" name="contacts" placeholder="Your Name, Address and Contacts"><?= o('contacts') ?></textarea>
					</div>
				</div>

				<div class="form-group">
					<label for="salutation">Salutation</label>
					<input type="text" class="form-control" name="salutation" placeholder="Salutation" value="<?= o('salutation') ?>"/>
				</div>

				<div class="form-group">
					<label for="title">Job title</label>
					<input type="text" class="form-control" name="title" placeholder="Job title" value="<?= o('title') ?>" />
				</div>

				<div class="form-group">
					<textarea id="Content" name="content"><?= o('content') ?></textarea>
				</div>
			</div>
			<div class="col-md-2 right">
				<button type="submit" class="btn btn-primary">Submit</button>
			</div>
		</div>
		</form>
	</div>
	
	<script src="js/app.js"></script>
<?php
	if (isset($iframe)) echo $iframe;
?>

</body>
</html>