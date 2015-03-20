<?php 
include ('ClassTemplatesComponents.php'); 

$file_name = $argv[1].'Model'; // File Path 
$file = null; 
$path = "app/models/";
$type = $argv[2];

// Build up our class components 
$class_builder = new ClassTemplatesomponents(); 
$class_builder->addClassName($file_name); 
$class_builder->addClassExtends('Model'); 
$class_builder->addMethod('__construct', 'public', '', 'parent::__construct();');
$class_builder->builder->params['__construct']['params'] = "array()";
$content = $class_builder->compile($class_builder);

if (file_exists($path.$file_name.'.php')) { 
	die("Model file $path$file_name.php allready exists!! Try with another file name\n"); 
} else { 
	$file = fopen($path.$file_name.'.php', 'w') or die('Error creating file: '.$path.$file_name); 
	fwrite($file, $content);
	echo("Created model: '$path$file_name.php' \n"); 
}

?>