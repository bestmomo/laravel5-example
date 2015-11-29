<?php

class LanguageTest extends TestCase {
	
	protected $baseUrl = 'http://localhost';

	/**
	 * A basic functional test should return a home page with french language.
	 *
	 * @return void
	 */
	public function testShouldReturnHomeWithFrenchLanguage()
	{	
		$this->visit('/language/fr')
             ->see('Connexion')
             ->dontSee('Connection');
	}	
	
	/**
	 * A basic functional test should return a home page with portuguese language.
	 *
	 * @return void
	 */
	public function testShouldReturnHomeWithPortugueseLanguage()
	{	
		$this->visit('/language/pt-BR')
             ->see('Contato')
             ->dontSee('Connexion')
			 ->dontSee('Connection');
	}
	
	/**
	 * A basic functional test should return a home page with english language, because we are trying access an invalid language url
	 * and at /config/app.php the fallback_locale array key is set to english (en) language.
	 *
	 * @return void
	*/	
	public function testShouldReturnHomeWithEnglishLanguageInstead404Page()
	{
		$this->visit('/language/foo')
             ->see('Connection')
             ->dontSee('404');
	}

	/**
	 * A basic functional test should return a 404 error page, because we are trying access an invalid language url
	 * made with a number language argument.
	 *
	 * @return void
	*/	
	public function testShouldReturn404ErrorPage()
	{
		$response = $this->call('GET', '/language/123');
		$this->assertEquals(404, $response->status());
	}
}