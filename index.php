<?php

/**
* Incluimos la clase Validator.php
*/
include 'Validator.php';

/**
* Hacemos uso de esta función para evitar usar $_GET[''] ó $_POST['']
*/
extract($_REQUEST);

$validations = [];
$warnings = '';
if ( isset($validate) ) {
	$validations['name'] = 'label:Nombre|value:' . $name. '|rules:required,min=3';
	$validations['email'] = 'label:Correo|value:' . $email. '|rules:required,email';
	if ( !empty($password) ) {
        if ( $confirm_password!==$password ) {
            $rulesPass = 'external_error';
        }
        $validations['password'] = 'label:Clave|value:' . $password. '|rules:required,min=8,max=15,'.$rulesPass.'|message:No se ha confirmado la clave';
    }
    if ( Validator::validate($validations) ) {
    	$messages = Validator::messages();
    	foreach ($messages as $key) {
    		$warnings .= $key.'<br>';
    	}
    }
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Validador con php</title>
	<meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
     <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>
<div class="col s12 m6">
	<h1>Probando el validador de datos</h1>
</div>
<?php if ( !empty($warnings) ): ?>
<div class="row valign-wrapper u-flexColumnCenter red accent-1 red-text text-darken4">
    <i class="material-icons prefix">error</i><br>
    <?=$warnings?>
</div>
<?php endif;?>
<div class="row valign-wrapper">
	<form action="" method="post">
		<div class="col s10">
			<label for="name">Nombre</label>
			<input type="text" name="name" class="input-field col s12">
		</div>
		<div class="col s10">
			<label for="email">Correo</label>
			<input type="text" name="email" class="input-field col s12">
		</div>
		<div class="col s10">
			<label for="password">Clave</label>
			<input type="text" name="password" class="input-field col s12">
		</div>
		<div class="col s10">
			<label for="confirm_password">Repite la Clave</label>
			<input type="text" name="confirm_password" class="input-field col s12">
		</div>
		<div class="col s10">
			<button type="submit" name="validate" class="btn-large ">Enviar</button>
		</div>
	</form>
</div>
</body>
</html>