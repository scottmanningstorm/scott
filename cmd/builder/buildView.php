<?php 
include ('ClassTemplatesComponents.php'); 

$file_name = $argv[1].'.php'; 
$folder_name = $argv[3].$argv[1]; 
$file_name = ucfirst($file_name);
$type = $argv[2];

$content ='<header class="">
	<p class="">Paragraph</p>
	<h1 class="">Header</h1>	
	<a class="" href="">Header</a>
	<div class="">  
		<?php echo "testing"; ?>
	</div>
</header>
';

die($folder_name);

if (file_exists($folder_name)) { 
	echo "File allready exists";
} else { 
	mkdir($folder_name);
	$file = fopen($folder_name, 'w') or die('Error creating file: '.$path); 
	fwrite($file, $content);
	puts("\nCreated view: '$folder_name'\n"); 
}

?>