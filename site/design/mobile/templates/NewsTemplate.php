<?php

require('BaseTemplate.php');

class NewsTemplate extends BaseTemplate
{
	var $template = 'news';
	
	protected function build()
	{
		switch ($this->node->nodeType)
		{
			case NodeType::SECTION:
				$this->template = 'news_detail';
				break;
		}
		
		parent::build();
	}
}