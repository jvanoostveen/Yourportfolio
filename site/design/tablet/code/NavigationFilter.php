<?php
class NavigationFilter
{
	public static function filter($node)
	{
		global $dataprovider;
		if (!$node)
		{
			$node = $dataprovider->nodes[0];
		}
		return $node;
	}
}