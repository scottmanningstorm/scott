<?php 
include ('ClassTemplatesomponents.php'); 

$file_name = $argv[1].'Controller'; 
$file_name = ucfirst($file_name);

$dir = 'app/controllers/';
$file = null; 

// Build up our class components 
$class_builder = new ClassTemplatesomponents(); 

$class_builder->addClassName($file_name); 
$class_builder->addClassExtends('AdminController'); 
$class_builder->addMethod('__construct', 'public', null, 'parent::__construct();');
$class_builder->addMethod('add', 'public', null, '');
$class_builder->addMethodComment('add', 'GET - /add');
$class_builder->addMethod('create', 'public', null, '');
$class_builder->addMethodComment('create', 'POST - /create');
$class_builder->addMethod('edit', 'public', null, '');
$class_builder->addMethodComment('edit', 'GET - /edit');
$class_builder->addMethod('update', 'public', null, '');
$class_builder->addMethodComment('update', 'POST - /update');
$content = $class_builder->compile($class_builder);

if (file_exists($dir.$file_name.'.php')) { 
	die("Controller file $dir$file_name.php allready exists!! Try with another file name\n"); 
} else { 
	$file = fopen($dir.$file_name.'.php', 'w') or die('Error creating file: '.$file_name); 
	fwrite($file, $content);
	echo ("Created Controller: 'app/controllers/$file_name.php' \n");  
}

?>