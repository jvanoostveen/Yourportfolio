<?php
class Files
{
	const THUMBNAIL = 'thumbnail';
	const PREVIEW = 'preview';
	const MOVIE = 'movie';
	
	private static $_aliases = array();
	
	public static function get($key, Node $node)
	{
		$file = $node->getFile($key);
		
		if (!$file && isset(self::$_aliases[$node->nodeType][$key]))
		{
			foreach (self::$_aliases[$node->nodeType][$key] as $realKey)
			{
				$file = $node->getFile($realKey);
				if ($file)
					break;
			}
		}
		
		return $file;
	}
	
	public static function parse()
	{
		$types = array(NodeType::ALBUM, NodeType::SECTION, NodeType::ITEM);
		
		foreach ($types as $type)
		{
			if (!isset(self::$_aliases[$type]))
				self::$_aliases[$type] = array();
			
			$file = SETTINGS.$type.'_files.ini';
			
			if (!file_exists($file))
				return;
			
			$settings = parse_ini_file($file, true);
			
			foreach ($settings as $key => $file)
			{
				if (isset($file['alias']))
				{
					$aliases = explode(',', $file['alias']);
					foreach ($aliases as $alias)
					{
						if (!isset(self::$_aliases[$type][$alias]))
							self::$_aliases[$type][$alias] = array();
						
						self::$_aliases[$type][$alias][] = $key;
					}
				}
			}
		}
	}
}