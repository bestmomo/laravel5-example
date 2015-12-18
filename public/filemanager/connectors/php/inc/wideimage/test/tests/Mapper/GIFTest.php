<?php
	/**
    This file is part of WideImage.
		
    WideImage is free software; you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation; either version 2.1 of the License, or
    (at your option) any later version.
		
    WideImage is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Lesser General Public License for more details.
		
    You should have received a copy of the GNU Lesser General Public License
    along with WideImage; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    
    * @package Tests
  **/
	
	include WideImage::path() . 'Mapper/GIF.php';

	/**
	 * @package Tests
	 *
	 * @group Mapper
	 * @group GIF
	 */
	class WideImage_Mapper_GIF_Test extends WideImage_TestCase
	{
		protected $mapper;
		
		function setup()
		{
			$this->mapper = WideImage_MapperFactory::selectMapper(null, 'gif');
		}
		
		function teardown()
		{
			$this->mapper = null;
			
			if (file_exists(IMG_PATH . 'temp/test.gif'))
				unlink(IMG_PATH . 'temp/test.gif');
		}
		
		function testLoad()
		{
			$handle = $this->mapper->load(IMG_PATH . '100x100-color-hole.gif');
			$this->assertTrue(is_resource($handle));
			$this->assertEquals(100, imagesx($handle));
			$this->assertEquals(100, imagesy($handle));
			imagedestroy($handle);
		}
		
		function testSaveToString()
		{
			$handle = imagecreatefromgif(IMG_PATH . '100x100-color-hole.gif');
			ob_start();
			$this->mapper->save($handle);
			$string = ob_get_clean();
			$this->assertTrue(strlen($string) > 0);
			imagedestroy($handle);
			
			// string contains valid image data
			$handle = imagecreatefromstring($string);
			$this->assertTrue(is_resource($handle));
			imagedestroy($handle);
		}
		
		function testSaveToFile()
		{
			$handle = imagecreatefromgif(IMG_PATH . '100x100-color-hole.gif');
			$this->mapper->save($handle, IMG_PATH . 'temp/test.gif');
			$this->assertTrue(filesize(IMG_PATH . 'temp/test.gif') > 0);
			imagedestroy($handle);
			
			// file is a valid image
			$handle = imagecreatefromgif(IMG_PATH . 'temp/test.gif');
			$this->assertTrue(is_resource($handle));
			imagedestroy($handle);
		}
	}
