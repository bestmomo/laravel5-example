<?php namespace app\Services;

use Illuminate\Validation\Validator;

class Validation extends Validator {

	public function validateTags($attribute, $value, $parameters)
	{
	    return preg_match("/^[A-Za-z0-9-éèàù]{1,50}?(,[A-Za-z0-9-éèàù]{1,50})*$/", $value);
	}

} 