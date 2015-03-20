<?php

include ('cmd/builder/TemplateBuilder.php'); 

Class ClassTemplatesomponents
{
	protected $class_components = array(
		'opening_php_tags',
		'class',
		'extends',
		'implements',
		'public_properties', 
		'private_properties',
		'protected_properties',  
		'contrusctor',
		'methods',
		'closing_php_tags'
	);

	// Keeps a count on tab formating
	protected $tab_index = 1;

	protected $content = ''; 

	public $builder;

	public function __construct() 
	{
		$this->builder = new TemplateBuilder();
	}

	public function compile()
	{	
		$this->content = $this->compileOpeningTag(). 
		$this->compileClass($this->builder).
		$this->compileExtends($this->builder). 
		$this->compileImplements($this->builder).
		$this->openBracket(true). 
		$this->compileProperties($this->builder).
		$this->compileMethods($this->builder).
		$this->closeBracket().
		$this->compileClosingTag(); 
			 
		return $this->content; 		
	}

	public function compileComments(TemplateBuilder $temp)
	{
		foreach ($temp->varComments as $key => $comment) { 
			echo $key; 
		}
	}

	public function compileProperties(TemplateBuilder $temp)
	{	
		$properties = null;

		$properties .= $this->buildProperties('public', $temp->properties);
		$properties .= $this->buildProperties('protected', $temp->properties);
		$properties .= $this->buildProperties('private', $temp->properties);
		
		return $properties; 
	}

	public function buildProperties($access_level, $properties)
	{	
		$properties_string = null; 

		foreach ($properties[$access_level] as $key => $pub_vars) {
			$properties_string .= $this->tab()."$access_level $$key = '$pub_vars';\n\n"; 
		}
		
		return $properties_string;
	}	

	public function compileMethods(TemplateBuilder $temp)
	{
		$methods = null; 

		$methods .= $this->buildMethod('public', $temp->method, $temp->params);  
		$methods .= $this->buildMethod('protected', $temp->method, $temp->params);  
		$methods .= $this->buildMethod('private', $temp->method, $temp->params);   

		return $methods;
	}

	public function buildMethod($access_level, $methods, $params) 
	{
		$return = ''; 
		$parameters = null; 

		foreach ($methods[$access_level] as $key => $method) {	
			
			$return .= $this->buildMethodComments($key); 

			$parameters = $this->getMethodsParameters($key, $params);

			$return .= "\n".$this->tab()."$access_level function $key(".$parameters.") {";

			$this->tab_index++;  
			$return .= "\n\n".$this->tab().$this->builder->code[$key]; 
			$this->tab_index--; 
			
			$return .= "\n\n".$this->tab()."}\n\n"; 
		}

		return $return; 
	}

	public function buildMethodCode($method, $access_type)
	{
		return $this->builder->methods[$access_type][$method];
	}

	public function buildMethodComments($method)
	{
		$comment = $this->tab()."/** \n"; 

		if (!!$this->builder->method_comments[$method]) { 
			$comment .= $this->tab()." * ".$this->builder->method_comments[$method]. "\n";
	 	}
 		
 		foreach ($this->builder->params[$method] as $key => $param)  {
 			$comment .= $this->tab().' * @param '.gettype($param).' $'.$key."\n ";	
 		} 

 		$comment .= $this->commentReturnType();

	 	return $comment .= $this->tab()." */"; 
	}

	protected function commentReturnType($method)
	{
		if (!!$this->builder->method_comments[$method]['return_type']) {

		}
	}

	protected function commentParams($method)
	{
		if (!!$this->builder->method_comments[$method]['params']) {

		}
	}

	public function getMethodsParameters($function, $params)
	{
		$parameters = array(); 
		
	 	foreach($params[$function] as $key => $param) {
	 		if (!!$param) {
	 			$parameters[] = "$".$key.'='."'$param'";
			} else { 
				$parameters[] = "$".$key;
			}
		}

		$parameters = implode(', ', $parameters); 

		return $parameters;
	}

	public function buildConstructor()
	{
		return $this->buildMethod("public", "__construct");
	}

	public function compileOpeningTag()
	{
		$tags = "<?php\n";
		
		return $tags; 
	}

	public function compileClosingTag()
	{
		$tags = "\n\n?>";
		
		return $tags; 
	}

	public function compileClass(TemplateBuilder $temp)
	{	
		$temp->class = str_replace(' ', '_', $temp->class);
		
		$class = "\nClass $temp->class "; 

		return $class; 
	}

	public function compileExtends(TemplateBuilder $temp) 
	{	
		$extends = null; 

		if (!!$temp->extends) {
			$extends = "extends $temp->extends ";			
		}

		return $extends; 
	}

	public function compileImplements(TemplateBuilder $temp) 
	{	
		$implements = null; 

		if (!!$temp->interface) {
			$implements = "implements $temp->interface ";
		}

		return $implements;
	}

	public function openBracket($drop_line = false)
	{	
		$return = null; 

		if ($drop_line) {
			$return = "\n{\n";
		} else {
			$return = "{";
		}

		$this->tab_index++; 

		return $return; 
	}

	public function closeBracket()
	{
		return "}";
	}

	public function tab()
	{	
		$tabs = "";
		for ($i=1; $i < $this->tab_index; $i++) { 
			$tabs .= "\t";
		}

		return $tabs; 
	}


	// Adding template components
	public function addClassName($class_name)
	{
		$this->builder->class = $this->formatName($class_name);

		return $this;
	}

	public function addClassInterface($implements_name) 
	{
		$this->builder->interface = $this->formatName($implements_name); 
	}

	public function addClassExtends($extends_name) 
	{
		$this->builder->extends = $this->formatName($extends_name);
	}

	public function addProperty($name, $value, $access_level='public')
	{	
		$this->builder->properties[$access_level][$this->formatName($name)] = $value;

		return $this;
	}

	public function addMethod($func_name, $access_level = 'public', $return_type='void', $code='{}') 
	{		
		$this->builder->method[$access_level][$func_name][$return_type] = $code; 

		$this->builder->code[$func_name] = $code;

		return $this;
	}

	public function addParams($method, $params)
	{
		$this->builder->params[$method][$params];
	}

	public function formatName($name)
	{
		$name = str_replace(' ', '_', $name);
	
		return $name;
	}
	
	public function addMethodComment($method, $comment, $params='', $return_type='void')
	{
		$this->builder->method_comments[$method] = $comment;
	}

}

?>