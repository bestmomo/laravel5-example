<?php

class ExampleTest extends TestCase {

	/**
	 * A basic functional test should return a home page with french language.
	 *
	 * @return void
	 */
	public function testShouldReturnHomeWithFrenchLanguage()
	{	
		$this->visit('/language/fr')
             ->see('Connexion')
             ->dontSee('Conection');
	}	

	/**
	 * A basic functional test should return a home page with english language, because we are trying access an invalid language.
	 *
	 * @return void
	 */
	
	public function testShouldReturnHomeWithEnglishLanguage()
	{
		$this->visit('/language/foo')
             ->see('Conection')
             ->dontSee('404');
	}
}
