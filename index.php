<?php

// Konfiguration
$ordner = 'bookmarks'; // Ordner für die Kommentardateien

// Inhalte
$title = 'Ressourcen - Bookmarks';
$question = 'Deine Boomarks';



$hasError = false;
$errorMsg = array();

// Löschen 
if( isset($_GET['delete']) ){
	$dpath = $ordner.'/'.$_GET['delete'];
	if( is_file($dpath) ){
		unlink($dpath);
	}
	header('Location: '.$_SERVER['PHP_SELF']);
}

// schreiben
if(  isset($_POST['entry_url']) && isset($_POST['entry_desc']) ){
	
	// Validieren auf leere Werte
	if(empty($_POST['entry_url']) || empty($_POST['entry_desc']) ){
		$hasError = true;
		$errorMsg[] = 'Bitte URL und Beschreibung ausfüllen...';
	}
	
	if($hasError == false){
		// ready für das Schreiben in Datei
		
		// keine HTML Tags zulassen:
		$url = strip_tags($_POST['entry_url']);
		$desc = strip_tags($_POST['entry_desc']);
		
		// Inhalt HTML erstellen
		$inhalt = '<a href="'.$url.'">'.$url.'</a>';
		$inhalt .= '<p>'.nl2br($desc).'</p>';
		
		
		// Dateinamen generieren - der Timestamp und der Name der Person ermöglichen
		$dateiname = time().'_entry-'.substr(md5(uniqid()), 0, 10).'.html';
		// echo 'dateiname: '.$dateiname; 
		
		$target_path = $ordner.'/'.$dateiname;
		$res = fopen($target_path, 'w'); // Dateistream öffnen
		
		if($res === false){
			echo 'Eintrag kann nicht gespeichert werden (Datei konnte nicht erstellt werden)';
		}else{
			fwrite($res, $inhalt); // schreiben in neu erstellte Datei
			header('Location: '.$_SERVER['PHP_SELF']); // Umleitung, damit beim Reload die Daten nicht noch einmal gesendet werden...
		}
	}
}


// Auslesen: verwende dafür wieder fopen(), aber diesmal mit fread(), kombiniere dies mit dem readdir oder scandir, um alle Dateien per Loop auszugeben.
$allowed = array('html'); 

$contents = array(); 
$files = array_reverse(scandir($ordner));

foreach($files as $filename){
	$filext = substr($filename, strrpos($filename, '.')+1); 
	
	if( strlen($filext)>0 && in_array($filext, $allowed) && !is_dir($filename)){
		$onefile = array();
		
		$path = $ordner."/".$filename;
		$strm = fopen($path, "r");
		
		$onefile['filename'] = $filename;
		$onefile['content']  = (filesize($path)>0) ? fread($strm, filesize($path)) : '';
		fclose($strm);
		
		$contents[] = $onefile;
	}
}



?>
<!DOCTYPE html>
<html lang="en-gb" dir="ltr">
<head>
	<title><?php echo $title; ?></title>
	<meta name="description" content="Bookmark Projekt">
	<style>
	html {
		font-family: arial;
		background: #F5F5DC;
	}
	
	a {
		text-decoration: none !important;
		font-size: 1rem;
		color: darkblue;
	 	text-align: center;
	}
	
	h4 {
		text-align: center;
		margin-bottom: 3rem;
	}
	
	h1 {
		text-align: center;
	}
	
	.alert {
		color: red;
	}
	
	.container {
	background: rgba( 255, 255, 255, 0.65 );
	box-shadow: 0 8px 32px 0 rgba( 31, 38, 135, 0.37 );
	backdrop-filter: blur( 8.0px );
	-webkit-backdrop-filter: blur( 8.0px );
	border-radius: 10px;
	border: 1px solid rgba( 255, 255, 255, 0.18 );
		margin: 2rem;
		padding:1rem;
		border: 1px solid black;
		border-radius: 10px
	}
	
	form {
		padding: 2rem;
		text-align: center;
		border-radius: 5px
	}
	
	input[type=text] {
	width: 100%;
	padding: 12px 20px;
	margin: 8px 0;
	box-sizing: border-box;
	font-size: 1rem;
	}
	
	textarea {
	width: 100%;
	padding: 12px 20px;
	margin: 8px 0;
	box-sizing: border-box;
	font-size: 1rem;
	}
	
	.button {
		background-color: #4CAF50; /* Green */
  		border: none;
  		color: white;
  		padding: 15px 32px;
  		text-align: center;
  		text-decoration: none;
  		display: inline-block;
  		font-size: 16px;
		border-radius: 2px;
		box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
	}
	
	.button:active {
		background-color: blue;
		box-shadow: 0 8px 16px 0;
	}

	
	.delete {
		margin-left: 95%;
	}

	
	.uk-card { border-radius:3px; }
	@media print {
		.uk-button, .uk-close, form {
			display:none;
			visibility:hidden;
		}
		.uk-card-body {
			border-bottom: 1px solid;
			border-radius: 0px;
			padding: 0 !important;
		}
	}
	</style>
</head>
<body>
<div>
	<h1><?php echo $title; ?> <br><a href="javascript:print();">PDF erstellen</a></h1>
	

	<h4><?php echo $question; ?></h4>
	<div class="container">
		<?php foreach($contents as $file){ ?>
		<div>
		<a class="delete" uk-close href="?delete=<?php echo $file['filename'];?>">&#10008;</a></p>
			<div class="uk-card uk-card-hover uk-card-body uk-card-small">
				<p><?php echo $file['content'] ?>
				<hr>
			</div>
		</div>
		<?php } ?>
	</div>
	
	<br>
	<form action="" method="POST">
	
		<?php
		// error handling
			if($hasError == true && count($errorMsg)>0){
				echo '<div class="alert">';
				echo implode('<br>', $errorMsg);
				echo '</div>';
			}
		?>
		
		<label>Neuer Bookmark hinzufügen</label>
		<br><input type="text" class="uk-input" name="entry_url" placeholder="URL" class="uk-margin"><br>
		<textarea name="entry_desc" placeholder="Beschreibung"></textarea>
		<br><input type="submit" class="button"><br>
	</form>
</div>
</body>
</html>