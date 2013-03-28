<?php
class Path
{
	private static $_css = array();
	private static $_assets = array();
	private static $_scripts = array();
	
	public static function setCSS($paths = array())
	{
		self::$_css = $paths;
	}
	
	public static function addCSS($path)
	{
		array_unshift(self::$_css, $path);
	}
	
	public static function css($file)
	{
		return self::find(self::$_css, $file);
	}
	
	public static function cssExists($file)
	{
		$path = self::css($file);
		return !empty($path);
	}

	public static function addScripts($path)
	{
		array_unshift(self::$_scripts, $path);
	}
	
	public static function script($file)
	{
		return self::find(self::$_scripts, $file);
	}
	
	public static function scriptExists($file)
	{
		$path = self::scripts($file);
		return !empty($path);
	}
	
	private static function find($paths, $file)
	{
		global $system;
		
		foreach ($paths as $path)
		{
			if (file_exists($path.$file))
			{
				return $system->base_url.$path.$file;
			}
		}
		
		return '';
	}
	
	public static function addAsset($path)
	{
		array_unshift(self::$_assets, $path);
	}
	
	public static function asset($file)
	{
		return self::find(self::$_assets, $file);
	}
	
	public static function assetExists($file)
	{
		$path = self::image($file);
		return !empty($path);
	}
	
	public static function addImage($path)
	{
		self::adAsset($path);
	}
	
	public static function image($file)
	{
		return self::asset($file);
	}
	
	public static function imageExists($file)
	{
		return self::assetExists($file);
	}
}