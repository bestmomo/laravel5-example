<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"             => "O :attribute foi aceito.",
	"active_url"           => "O :attribute não é uma URL válida.",
	"after"                => "O :attribute deve ser uma depois de :date.",
	"alpha"                => "O :attribute deve conter apenas letras.",
	"alpha_dash"           => "O :attribute deve conter apenas letras, números e traços.",
	"alpha_num"            => "O :attribute deve conter apenas letras e números.",
	"array"                => "O :attribute deve ser um array.",
	"before"               => "O :attribute deve ser uma data anterior a :date.",
	"between"              => [
		"numeric" => "O :attribute deve ser entre :min e :max.",
		"file"    => "O :attribute deve possuir entre :min e :max kilobytes.",
		"string"  => "O :attribute deve ter entre :min e :max caracteres.",
		"array"   => "O :attribute deve ter entre :min e :max itens.",
	],
	"boolean"              => "O :attribute campo deve ser verdadeiro ou falso.",
	"confirmed"            => "O :attribute confirmação não confere.",
	"date"                 => "O :attribute não é uma data válida.",
	"date_format"          => "O :attribute não confere com o formato :format.",
	"different"            => "O :attribute e :other devem ser diferentes.",
	"digits"               => "O :attribute deve ser :digits um dígito.",
	"digits_between"       => "O :attribute deve estar entre :min e :max dígitos.",
	"email"                => "O :attribute deve ser um endereço de e-mail válido.",
	"filled"               => "O :attribute campo é obrigatório.",
	"exists"               => "O selected :attribute é inválido.",
	"image"                => "O :attribute deve ser uma imagem.",
	"in"                   => "O selected :attribute é inválido.",
	"integer"              => "O :attribute deve ser um número inteiro.",
	"ip"                   => "O :attribute deve ser um IP válido.",
	"max"                  => [
		"numeric" => "O :attribute não deveria ser maior que :max.",
		"file"    => "O :attribute não deveria ser maior que :max kilobytes.",
		"string"  => "O :attribute não deveria ser maior que :max caracteres.",
		"array"   => "O :attribute não deveria ser maior que :max item=ns.",
	],
	"mimes"                => "O :attribute deve ser um arquivo do tipo: :values.",
	"min"                  => [
		"numeric" => "O :attribute deve ser no mínimo :min.",
		"file"    => "O :attribute deve ser no mínimo :min kilobytes.",
		"string"  => "O :attribute deve ser no mínimo :min caracteres.",
		"array"   => "O :attribute deve ser no mínimo :min itens.",
	],
	"not_in"               => "O item selecionado :attribute é inválido.",
	"numeric"              => "O :attribute deve se rum número.",
	"regex"                => "O :attribute formato é inválido.",
	"required"             => "O :attribute campo é origatório.",
	"required_if"          => "O :attribute campo é origatório quando :other é :value.",
	"required_with"        => "O :attribute campo é origatório quando :values é present.",
	"required_with_all"    => "O :attribute campo é origatório quando :values é present.",
	"required_without"     => "O :attribute campo é origatório quando :values é not present.",
	"required_without_all" => "O :attribute campo é origatório quando nenhum dos :values são presentes.",
	"same"                 => "O :attribute e :other devem ser iguais.",
	"size"                 => [
		"numeric" => "O :attribute deve ser :size.",
		"file"    => "O :attribute deve ser :size kilobytes.",
		"string"  => "O :attribute deve ser :size caracteres.",
		"array"   => "O :attribute deve conter :size itens.",
	],
	"unique"               => "O :attribute já foi informado.",
	"url"                  => "O :attribute formato é inválido.",
    "tags"                 => "tags, separadas com vírgula (SEM ESPAÇOS), devem ter no máximo 50 caracteres.",
	"timezone"             => "O :attribute deve ser uma zona válida.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => [
		'attribute-name' => [
			'rule-name' => 'custom-message',
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => [
		'log' => 'Email ou Senha'
	],

];
