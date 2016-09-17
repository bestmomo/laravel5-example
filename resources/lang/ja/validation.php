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

	"accepted"             => "* :attributeを受け入れなければなりません。",
	"active_url"           => "* :attributeが有効なURLではありません",
	"after"                => "* 日付：:attributeは、後の日付でなければなりません。",
	"alpha"                => "* :attributeには、文字が含まれていてもよいです。",
	"alpha_dash"           => "* :attributeには、文字、数字、およびダッシュを含めることができます。",
	"alpha_num"            => "* :attributeには、文字と数字のみを含めることができます。",
	"array"                => "* :attributeは配列でなければなりません。",
	"before"               => "* 日付：:attributeは、前の日付でなければなりません。",
	"between"              => [
		"numeric" => "* 最小値と：最大:attributeが間にある必要があります",
		"file"    => "* 最小値と：最大キロバイト:attributeがの間でなければなりません。",
		"string"  => "* 最小値と：最大文字:attributeがの間でなければなりません。",
		"array"   => "* 最小値と：最大項目:attributeがの間を持っている必要があります。",
	],
	"boolean"              => "* :attributeフィールドは、trueまたはfalseでなければなりません。",
	"confirmed"            => "* :attribute確認が一致しません。",
	"date"                 => "* :attributeが有効な日付ではありません。",
	"date_format"          => "* :attributeは、フォーマットと一致していない：フォーマットを。",
	"different"            => "* :attributeと：他は異なっている必要があります。",
	"digits"               => "* :attributeがなければなりません：桁の数字。",
	"digits_between"       => "* :attributeはの間でなければなりません：最小と：最大桁。",
	"email"                => "* :attributeには、有効なメールアドレスでなければなりません。",
	"filled"               => "* :attributeフィールドは必須です。",
	"exists"               => "The 選択：:attributeが無効です。",
	"image"                => "* :attributeはイメージでなければなりません。",
	"in"                   => "The 選択：:attributeが無効です。",
	"integer"              => "* :attributeは整数でなければなりません。",
	"ip"                   => "* :attributeには、有効なIPアドレスでなければなりません。",
	"max"                  => [
		"numeric" => "* 最大：:attributeがより大きくできない場合があります。",
		"file"    => "* 最大キロバイト：:attributeがより大きくできない場合があります。",
		"string"  => "* 最大文字数：:attributeがより大きくできない場合があります。",
		"array"   => "* 最大項目：:attributeは、より多くを持っていない場合があります。",
	],
	"mimes"                => "* :attributeは、タイプのファイルである必要があります：値。",
	"min"                  => [
		"numeric" => "* 最小：:attributeは、少なくともでなければなりません。",
		"file"    => "* キロバイト分：:attributeは、少なくともでなければなりません",
		"string"  => "* 文字分：:attributeは、少なくともでなければなりません。",
		"array"   => "* アイテム分：:attributeが少なくとも持っている必要があります。",
	],
	"not_in"               => "The 選択：:attributeが無効です。",
	"numeric"              => "* :attributeは数値でなければなりません。",
	"regex"                => "* :attributeの形式が無効です。",
	"required"             => "* :attributeフィールドは必須です。",
	"required_if"          => "* 他は次のとおりです：フィールドをするときに必要な:attribute値を。",
	"required_with"        => "* :attributeフィールドをするときに必要な：値が存在しています。",
	"required_with_all"    => "* :attributeフィールドをするときに必要な：値が存在しています。",
	"required_without"     => "* 値が存在しない次の場合、:attributeフィールドは必須です。",
	"required_without_all" => "* 値存在していない：のいずれも時:attributeフィールドが必要です。",
	"same"                 => "* :attributeと：他には一致している必要があります。",
	"size"                 => [
		"numeric" => "* :attributeがある必要がありますサイズ。",
		"file"    => "* :attributeがある必要がありますサイズのキロバイト。",
		"string"  => "* :attributeがある必要がありますサイズの文字。",
		"array"   => "* :attributeが含まれている必要がありますサイズのアイテムを。",
	],
	"unique"               => "* :attributeがすでに使用されています。",
	"url"                  => "* :attributeの形式が無効です。",
    "tags"                 => "タグは、カンマ（スペースなし）で区切って、最大50文字にする必要があります",
	"timezone"             => "* :attributeには、有効なタイムゾーンにする必要があります。",

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
			'rule-name' => 'カスタムメッセージ',
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
		"log" => "メールアドレスまたはパスワード"
	],

];
