<?PHP

class TemplateRegistry
{
	const IS_DEFAULT = -1;
	
	public static function register()
	{
		global $templateController;
		
		$templateController->register('TextTemplate', NodeTemplate::TEXT, self::IS_DEFAULT);
		$templateController->register('GalleryTemplate', NodeTemplate::ALBUM, self::IS_DEFAULT);
		$templateController->register('NewsTemplate', NodeTemplate::NEWS, self::IS_DEFAULT);
		$templateController->register('ContactTemplate', NodeTemplate::CONTACT, self::IS_DEFAULT);
		$templateController->registerDefault('BaseTemplate');
	}
}
