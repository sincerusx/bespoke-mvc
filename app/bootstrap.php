<?php

include DOC_ROOT . 'app/helpers/error_helper.php';
include DOC_ROOT . 'app/helpers/url_helper.php';
include DOC_ROOT . 'app/helpers/lang_helper.php';

include DOC_ROOT . 'configs/configs.php';

function __autoload($class)
{
	if(file_exists(DOC_ROOT . 'src/' . $class . EXT)){
		require_once DOC_ROOT . 'src/' . $class . EXT;
	}
	elseif(file_exists(DOC_ROOT . 'app/controllers/' . $class . EXT)){
		require_once DOC_ROOT . 'app/controllers/' . $class . EXT;
	}
	elseif(file_exists(DOC_ROOT . 'app/models/' . $class . EXT)){
		require_once DOC_ROOT . 'app/models/' . $class . EXT;
	}
	elseif(file_exists(DOC_ROOT . 'app/libraries/' . $class . EXT)){
		require_once DOC_ROOT . 'app/libraries/' . $class . EXT;
	}

}

session_start();

return new App();