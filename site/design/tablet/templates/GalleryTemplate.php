<?php

require('BaseTemplate.php');

class GalleryTemplate extends BaseTemplate
{
	protected function build()
	{
		switch ($this->node->nodeType)
		{
			case NodeType::ALBUM:
				$this->template = 'gallery_list';
				break;
			case NodeType::SECTION:
				$this->template = 'gallery_thumbs';
				break;
			case NodeType::ITEM:
				$this->template = 'gallery_detail';
				break;
		}
		
		parent::build();
	}
}