<?php 
include ('ClassTemplatesComponents.php'); 



// Building classes - Template builder will take components class and compile into class... 
// Controller, Model, repo and view builder iwll build up the class components and pass over to template builder ready to compile. 

$file_name = $argv[1].'Controller'; 
$file_name = ucfirst($file_name);
//$is_resorce = $argv[2]; 

$dir = 'app/controllers/';
$file = null; 

// Build up our class components 
$class_builder = new ClassTemplatesomponents(); 

$class_builder->addClassName($file_name); 
$class_builder->addClassExtends('BaseController'); 
$class_builder->addMethod('__construct', 'public', null, 'parent::__construct();');
$class_builder->addMethod('index', 'public', null, '');
$class_builder->addMethodComment('index', 'GET - index');
$content = $class_builder->compile($class_builder);

if (file_exists($dir.$file_name.'.php')) { 
	die("Controller file $dir$file_name.php allready exists!! Try with another file name\n"); 
} else { 
	$file = fopen($dir.$file_name.'.php', 'w') or die('Error creating file: '.$file_name); 
	fwrite($file, $content);
	echo ("Created Controller: 'app/controllers/$file_name.php' \n");  
}

?>
