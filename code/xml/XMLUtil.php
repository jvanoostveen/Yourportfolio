<?PHP
class XMLUtil
{
	function fileNode($file)
	{
		if ($file->width == 0)
		{
			global $canvas;
			return '<file key="'.$file->file_id.'" id="'.$file->id.'" path="'.$file->basepath.$file->sysname.'" name="'.$canvas->xml_filter(str_replace(array('&', '"'), '', $file->name)).'"><name><![CDATA['.$canvas->xml_filter($file->name).']]></name></file>'.PHP_EOL;
		}
		
		return '<file key="'.$file->file_id.'" id="'.$file->id.'" path="'.$file->basepath.$file->sysname.'" width="'.$file->width.'" height="'.$file->height.'"/>'.PHP_EOL;
	}
}