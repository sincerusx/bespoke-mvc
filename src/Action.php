<?php

class Action
{

	public $params = array();

	public function run(Controller $config)
	{
		$actionName = $config->dispatcher[ "_action" ];
		$configName = $config->dispatcher[ "_config" ];
		$this->params = $config->dispatcher[ "_params" ];

		echo $actionName . '<br>';
		echo $configName . '<br>';
		echo '<pre>';
		print_r($this->params);
		echo '<pre>';


		if(method_exists($this, $configName)){
			$configure = new ReflectionMethod($this, $configName);

			if($configure->isPublic() && !$configure->isStatic()){
				$this->{$configName}();
			}
			else{
				$this->errors[] = 'The configuration function must be public and no static.';
			}
		}

		if(method_exists($this, $actionName)){
			$action = new ReflectionMethod($this, $actionName);

			if($action->isPublic() && !$action->isStatic()){
				$this->{$actionName}();
			}
			else{
				$this->errors[] = 'The action function must be public and no static.';
			}
		}
		else{
			$this->{$actionName}();
		}
	}

	public function getParam($key, $defaultValue = null)
	{
		if(array_key_exists($key, $this->params)){
			if(empty($this->params[ $key ]) AND $this->params[ $key ] != 0){
				return $defaultValue;
			}
			else return $this->params[ $key ];
		}
		else{
			return $defaultValue;
		}
	}
}