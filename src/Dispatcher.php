<?php

class Dispatcher
{

	/**
	 * @var string $controller
	 */
	private $controller;

	/**
	 * @var string $action
	 */
	private $action;

	/**
	 * @var array $params
	 */
	private $params = array();

	/**
	 * Define the  actions taken here
	 * default action is as follows
	 * uri 2 defines function to be called
	 * uri 3 and more are passed as params to the function
	 *
	 * @return void
	 */
	public function runtimeCall()
	{

		# $uri = new URI;
		# $function = $uri->URISegment( 2 );

		$controller = $this->make($this->controller);
		$function = $this->action;

		if(empty($function)){
			$controller->indexAction();
		}
		elseif(method_exists($controller, $function)){
			if(empty($this->params)){
				call_user_func(array(
								   &$controller,
								   $function,
							   ));
			}
			else{
				call_user_func_array(array(
										 &$controller,
										 $function,
									 ), $params);
			}
		}
		else{
			$this->nonexisting_method($function);
		}

	}

	public function __construct($route, $app = null)
	{

		// inject app for use of container to make controller

		$this->controller = $controller = $this->setController($route[ 'controller' ]);
		$this->action = $action = $this->setAction($route[ 'action' ]);
		// Note: we are not working with parameters being passed in to functions at the moment
		$this->params = array();

		// Note: This should be taken from the Router
		$notfound = array(
			'controller' => 'Notfound',
			'action'     => 'indexAction',
		);

		// Check class exists
		if(false === class_exists($controller)){
			# die(''.__LINE__.'');
			return $this->make404();
			// log some error because this class does not exist;
			# die(sprintf('This class "%s" does\'t exist!', $this->controller));
		}

		// Check class cannot be instantiated
		if(false === $this->isInstantiable($controller)){
			# die(''.__LINE__.'');
			return $this->make404();
		}

		// check class method can be called exists
		if(false === method_exists($controller, $action)){
			# die(''.__LINE__.'');
			return $this->make404();
		}
		// everything ok, load this controller
		# echo 'everything ok, load this controller';

		$controller = $this->make($controller);

		return $controller->{$action}();

		# return $controller->{$action}();

		# return $this->runtimeCall();
		# $controller = $this->make($controller);
		# $controller->{$action}();
	}

	private function setController($controller)
	{
		return ucfirst($controller) . 'Controller';
	}

	private function setAction($action)
	{
		return $action . 'Action';
	}

	private function isInstantiable($object)
	{
		$reflectionClass = new ReflectionClass($object);

		if(false === $reflectionClass->isInstantiable()){
			// throw some error because this class does not exist;
			die(sprintf('This class "%s" isn\'t instantiable!', $object));
		}

		return true;
	}

	private function hasMethod($object, $method)
	{

		$reflectionClass = new ReflectionClass($object);

		/* Note: This is not working how I would like it to
		if($reflectionClass->hasMethod($method)){
			// throw some error because this class does not exist;
			die(sprintf('This class "%s" doesn\'t have the method "%s"!', $object, $method));
		}
		*/

		if(method_exists($object, $method)){
			// throw some error because this class does not exist;
			die(sprintf('This class "%s" doesn\'t have the method "%s"!', $object, $method));
		}

		return true;
	}

	private function isPublic($object, $method)
	{

		$reflectionMethod = new ReflectionMethod($object, $method);

		if($reflectionMethod->isPublic()){
			// throw some error because this class does not exist;
			die(sprintf('This class "%s" method "%s" is NOT public!', $object, $method));
		}

		return true;
	}

	private function make404()
	{
		$controller = $this->make('NotfoundController');
		$controller->{'indexAction'}();
		exit;
	}

	private function build($object)
	{
		$reflectionClass = new ReflectionClass($object);

		return $reflectionClass->newInstance($object);
	}

	private function make($object)
	{

		$reflector = new ReflectionClass($object);

		$buildStack[] = $object;

		$constructor = $reflector->getConstructor();

		// If there are no constructors, that means there are no dependencies then
		// we can just resolve the instances of the objects right away, without
		// resolving any other types or dependencies out of these containers.
		if(is_null($constructor)){
			array_pop($buildStack);
			dd();

			return new $object;
		}


		$dependencies = $constructor->getParameters();

		// Once we have all the constructor's parameters we can create each of the
		// dependency instances and then use the reflection instances to make a
		// new instance of this class, injecting the created dependencies in.
		$instances = $this->resolveDependencies(
			$dependencies
		);

		array_pop($buildStack);

		return $reflector->newInstanceArgs($instances);
	}

	protected function resolveDependencies(array $dependencies)
	{
		$results = [];

		foreach($dependencies as $dependency){
			// If the class is null, it means the dependency is a string or some other
			// primitive type which we can not resolve since it is not a class and
			// we will just bomb out with an error since we have no-where to go.
			$results[] = is_null($class = $dependency->getClass())
				? $this->resolvePrimitive($dependency)
				: $this->resolveClass($dependency);
		}

		return $results;
	}
}