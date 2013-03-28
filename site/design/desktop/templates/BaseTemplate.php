<?PHP

class BaseTemplate extends Template
{
	protected $contentOnly = false;
	protected $registry;
	
	protected $template = 'no_template';
	
	protected $og_video_controls_height = 0;
	
	public function __construct()
	{
		parent::__construct();
		
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
		{
			$this->contentOnly = true;
		}
	}
	
	protected function prebuild()
	{
		if ($this->contentOnly)
			return;
		
		parent::prebuild();
		
		global $yourportfolio;
		global $dataprovider;
		global $canvas, $system;
		
		$node = $this->node;
		$title = '';
		if ($node)
			$title = $node->getTitle();
		
		if (!empty($title))
			$title = $yourportfolio->getTitle().': '.$title;
		else
			$title = $yourportfolio->getTitle();
		$home_url = $system->base_url;
		
		/**
		 * SEO metadata
		 */
		$seo_description = '';
		$seo_keywords = '';
		
		if ($node)
		{
			$seo_description = $node->getMetadata('seo_description');
			$seo_keywords = $node->getMetadata('seo_keywords');
		}
		
		if (empty($seo_description))
		{
			$seo_description = $yourportfolio->prefs['description'];
		}
		
		if (empty($seo_keywords))
		{
			$seo_keywords = $yourportfolio->prefs['keywords'];
		}
		
		$seo_description = $canvas->f($seo_description);
		$seo_keywords = $canvas->f($seo_keywords);
		
		/**
		 * Open Graph variables
		 */
		$og_site_url = $system->siteUrl();
		$og_site_name = $canvas->f($yourportfolio->getTitle());
		$og_description = '';
		$og_title = '';
		$og_url = '';
		
		/**
		 * fb:admins or fb:app_id - A comma-separated list of either Facebook user IDs or a Facebook Platform application ID 
		 * that administers this page. It is valid to include both fb:admins and fb:app_id on your page.
		 */
		$og_fb_admins = null;
		$og_fb_app_id = null;
		if (isset($yourportfolio->preferences['facebook_user_ids']))
			$og_fb_admins = $canvas->f($yourportfolio->preferences['facebook_user_ids']);
		if (isset($yourportfolio->preferences['facebook_app_id']))
			$og_fb_app_id = $canvas->f($yourportfolio->preferences['facebook_app_id']);
		
		$og_image = null;
		$og_image_url = null;
		
		$og_video = null;
		$og_video_url = null;
		
		if ($node)
		{
			$og_title = $node->getMetadata('og_title');
			if (empty($og_title))
			{
				$og_title = $node->getTitle();
			}
			
			$og_description = $node->getMetadata('og_description');
			if (empty($og_description))
			{
				$og_description = $node->getText();
			}
			$og_url = $node->url();
			
			$og_image = Files::get(Files::PREVIEW, $node);
			if ($og_image)
			{
				$og_image_url = $og_site_url.$og_image->getPath();
			}
			
			$og_video = Files::get(Files::MOVIE, $node);
			if ($og_video && Path::assetExists('facebookplayer.swf'))
			{
				$og_video_url = $og_site_url;
				$og_video_url .= Path::asset('facebookplayer.swf');
				$og_video_url .= '?u='.$og_site_url.$system->base_url.$og_video->syspath;
				$og_video_url .= '&w='.$og_video->width;
				$og_video_url .= '&h='.$og_video->height;
				
				$facebook_width = 398;
				
				$og_video_width = $facebook_width;
				$f = $facebook_width / $og_video->width;
				$og_video_height = floor(($og_video->height * $f)) + $this->og_video_controls_height;
				if (defined('OG_VIDEO_CONTROLS_HEIGHT'))
					$og_video_height += OG_VIDEO_CONTROLS_HEIGHT;
			}
		} else {
			$og_description = $yourportfolio->prefs['description'];
		}
		
		$og_title = $canvas->f($og_title);
		$og_description = $canvas->text_filter($canvas->f($og_description), 300);
		
		require('html_start.php');
	}
	
	protected function build()
	{
		parent::build();
		
		global $yourportfolio;
		global $dataprovider;
		global $canvas, $system;
		
		$node;
		$rootNode;
		
		if ($this->node)
		{
			$node = $this->node;
			$rootNode = $node->root;
		} else {
			$this->template = 'index';
		}
		
		if (!empty($this->template))
			require($this->template.'.php');
	}
	
	protected function postbuild()
	{
		if ($this->contentOnly)
			return;
		
		parent::postbuild();
		
		global $yourportfolio;
		global $dataprovider;
		global $canvas, $system;
		
		require('html_stop.php');
	}
}