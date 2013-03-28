<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007-2009 Furthermore
 * @copyright 2010 Axis fm
 * @author Joeri van Oostveen <joeri@axis.fm>
 */

/**
 * class: QuickTime
 * Detect file info of quicktime movies.
 * 
 * @package yourportfolio
 * @subpackage Toolkits
 */
class QuickTime
{
	/**
	 * Handles the 'moov' atom at the start as well as the end.
	 * http://developer.apple.com/library/mac/#documentation/QuickTime/QTFF/QTFFChap2/qtff2.html#//apple_ref/doc/uid/TP40000939-CH204-BBCEIDFA
	 * 
	 * Doesn't handle matrices, for docs see:
	 * - Apples Atom Inspector
	 * - http://developer.apple.com/library/mac/#documentation/QuickTime/RM/MovieBasics/MTEditing/K-Chapter/11MatrixFunctions.html
	 * 
	 * @param $file
	 * @return array
	 */
	function getFileInfo($file)
	{
		$info = array('width' => 0, 'height' => 0);
		
		$fp = fopen($file, 'r');
		$size = filesize($file);
		
		$offset = 0;
		$header_size = 0;
		$header_name = '';
		
		while ($offset < $size)
		{
			fseek($fp, $offset);
			
			$atom_header = fread($fp, 8);
			$header_size = QuickTime::BigEndian2Int(substr($atom_header, 0, 4));
			$header_name = substr($atom_header, 4, 4);
			
			if ($header_name == 'moov')
			{
				$atom_content = fread($fp, $header_size);
				
				$atom_offset = 8;
				while ($atom_offset < $header_size)
				{
					fseek($fp, $offset + $atom_offset);
					$atom = fread($fp, 8);
					$atom_size = QuickTime::BigEndian2Int(substr($atom, 0, 4));
					$atom_name = substr($atom, 4, 4);
					
					if ($atom_name == 'trak')
					{
						$subatom = fread($fp, 8);
						$subatom_size = QuickTime::BigEndian2Int(substr($subatom, 0, 4));
						$subatom_name = substr($subatom, 4, 4);
						
						if ($subatom_name == 'tkhd')
						{
							$data = fread($fp, $subatom_size);
							
							$width = QuickTime::calcFixed(substr($data, 76, 4));
							$height = QuickTime::calcFixed(substr($data, 80, 4));
							
							if ($width > 0 && $width > $info['width'])
								$info['width'] = $width;
							
							if ($height > 0 && $height > $info['height'])
								$info['height'] = $height;
						}
					}
					
					$atom_offset += $atom_size;
				}
			}
			
			$offset += $header_size;
		}
		
		return $info;
	}
	
	function calcFixed($str)
	{
		return QuickTime::BigEndian2Int(substr($str, 0, 2)) + (float) (QuickTime::BigEndian2Int(substr($str, 2, 2)) / pow(2, 16));
	}
	
	function BigEndian2Int($str)
	{
		while (strlen($str) < 4)
		{
			$str = chr(0).$str;
		}
		
		$v = unpack("N", $str);
		
		return $v[1];
	}
}
?>
