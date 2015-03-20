<?php 

class TemplateBuilder 
{
	public $class;

	public $extends = array();

	public $interfaces = array(); 

	public $properties = array(); 
	
	public $methods = array(); 

	public $code = array(); 

	public $params = array();

	public $method_comments = '';
	
	public $template_content = ''; 

	public function compileTemplate()
	{	
		$this->template .= "<?php"; 

		$this->template .= $this->builClass($this->template);
		$this->template .= $this->interfaces($this->template);
		$this->template .= $this->buildExtends($this->template);
		$this->template .= $this->buildProperties($this->template); 
		$this->template .= $this->buildMethods($this->template);

		$this->template .= "?>";
	}

	public function buildClass($template) 
	{	
		$this->class = str_replace(' ', '_', $this->class);

		$class = 'class '.$class;
		if (count($this->extends) != 0) { 
			$class .= 'extends ';

		}			 
		return $template; 
	}

	public function buildProperties($template)
	{
		foreach ($this->properties as $property) {
			$template_content .= $property;
		}
		return $template; 
	}

	public function buildMethods($template)
	{
		foreach ($this->methods as $method) {
			
		}
		return $template;
	}

	public function buildExtends($template)
	{
		foreach ($this->interfaces as $interface) {

		}
		return $template;
	}

	/**
	 * Take a TemplateGrammer object an compiles a model.
	 *
	 * @param  object $grammer
	 * @return string 
	 */
	public function compileModel() 
	{

	}

	/**
	 * Take a TemplateGrammer object an compiles a repository.
	 *
	 * @param  object $grammer
	 * @return string 
	 */
	public function compileRepository() 
	{

	}

	/**
	 * Take a TemplateGrammer object an compiles a view.
	 *
	 * @param  object $grammer
	 * @return string 
	 */
	public function compilerView() 
	{

	}

	/**
	 * Take a TemplateGrammer object an compiles a controller.
	 *
	 * @param  object $grammer
	 * @return string 
	 */
	public function compileController()
	{

	}

	public function addClassName($class_name)
	{
		
	}

	public function addFunction($func_name) 
	{

	}
}

?>