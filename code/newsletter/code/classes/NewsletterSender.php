<?PHP

require_once(NL_CODE.'classes/NewsletterMailer.php');
require_once(NL_CODE.'classes/NewsletterView.php');

class NewsletterSender
{
	function NewsletterSender()
	{
		$this->__construct();
	}
	
	function __construct()
	{
		
	}
	
	function sendOne($newsletter, $name, $email)
	{
		global $settings;
		
		// initialize mailer
		$mailer = new NewsletterMailer();
		
		$newsletter->load();
		$newsletter->loadItems();
		
		$template = $newsletter->getTemplate();
		$template->loadDesign();
		
		$view = new NewsletterView();
		$view->newsletter = $newsletter;
		$view->template = $template;
		
		$view->build();
		$view->buildText();
		
		if (strlen($view->html) == 0)
		{
			return;
		}
		
		// embed cid names to <img src="..."
		$pattern = "/(src|background)=\"(.*?)\"/";
		//$patterns[] = "/background=\"(.*)\"/";
		
		$cid_map = array();
		
		preg_match_all($pattern, $view->html, $matches, PREG_PATTERN_ORDER);
		
		$images = $matches[2];
		$i = 0;
		foreach ($images as $image_path)
		{
			if( !in_array( $image_path, array_keys($cid_map) ) )
			{
				$image_name = substr($image_path, strrpos($image_path, '/') + 1);
				$cid = md5($image_name);
				$mailer->AddEmbeddedImage($image_path, $cid, $image_name);
				$cid_map[$image_path] = $cid;
			} else {
				$cid = $cid_map[$image_path];
			}
			
			$view->html = str_replace($matches[1][$i].'="'.$image_path, $matches[1][$i].'="cid:'.$cid, $view->html);
			$i++;

		}
		
		$mailer->Subject = $newsletter->subject;   
		$mailer->Body = $view->html;
		$mailer->AltBody = $view->text; // preg_replace('/\s\s+/', ' ', strip_tags($view->html));
		$mailer->FromName = $newsletter->sender;
	
		// replace special tags
		if (strpos($mailer->Body, '[[[ID]]]') !== false)
			$mailer->Body = str_replace(array('[[[ID]]]'), array($email), $mailer->Body);
		
		$mailer->AddAddress($email, $name);
		
		$mailer->Send();
		
		$mailer->ClearAddresses();
		$mailer->ClearAttachments();
		
		// save name and email as last_name, last_email in settings
		global $yourportfolio, $db;
		
		$result = null;
		$query = "DELETE FROM `".$yourportfolio->_table['nl_settings']."` WHERE name IN ('last_name', 'last_email') LIMIT 2";
		$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		
		$query = "INSERT INTO `".$yourportfolio->_table['nl_settings']."` VALUES ('last_name', '".$db->filter($name)."', 'string'), ('last_email', '".$db->filter($email)."', 'email')";
		$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
	}
	
	/**
	 * Retrieves all addresses of the groups to which the Newsletter is send and places them in the Queue.
	 * 
	 * @return Void
	 */
	function prepareMailing($newsletter)
	{
		global $yourportfolio, $db;
		
		// get addresses
		$query = sprintf("SELECT a.name, LOWER(a.address) AS address FROM `%s` a WHERE a.status IN (".AddressStatus::OK().", ".AddressStatus::ERROR().") AND EXISTS ( 
							SELECT * FROM `%s` j WHERE a.address_id = j.address_id AND j.group_id IN ( 
							SELECT group_id FROM `%s` r WHERE r.letter_id='".$newsletter->id."') )",
						$yourportfolio->_table['nl_addresses'], $yourportfolio->_table['nl_bindings'], $yourportfolio->_table['nl_recipients']);
		$groups = array();
		$db->doQuery($query, $groups, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
		
		if (empty($groups))
		{
			return false;
		}
		
		// fill queue
		$cache = array();
		
		$query = sprintf("INSERT INTO `%s` VALUES ", $yourportfolio->_table['nl_queue']);
		foreach( $groups as $g )
		{
			if( !in_array($g['address'], $cache ) )
			{
				$query .= "('".$newsletter->id."', '".$db->filter($g['name'])."', '".$db->filter($g['address'])."', 'unsent'),";
				$cache[] = $g['address'];
			}
		}
		
		$query = substr($query, 0, -1);
		$db->doQuery($query, $groups, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
		
		$result = null;
		$query = "INSERT INTO `".$yourportfolio->_table['nl_letter_stats']."` SET letter_id='".$newsletter->id."', addressees='".count($cache)."' ON DUPLICATE KEY UPDATE addressees='".count($cache)."'";
		$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
		
		$newsletter->changeStatus('queued');
		return true;
	}
	
	function handle($data = array())
	{
		if (empty($data) || empty($data['action']))
		{
			return;
		}
		
		global $system;
		
		switch ($data['action'])
		{
			case ('send_to_me'):
				
				if (!empty($data['send']['email']))
				{
					$newsletter = new Newsletter();
					$newsletter->id = $data['send']['newsletter']['id'];
					
					NewsletterSender::sendOne($newsletter, $data['send']['name'], $data['send']['email']);
				}
				
				$redirect = $data['redirect'];
				break;
			case ('prepare_mailing'):
				
				$newsletter = new Newsletter();
				$newsletter->id = $data['send']['newsletter']['id'];
				$newsletter->loadAll();
				
				if(!NewsletterSender::prepareMailing($newsletter))
				{
					return false;
				}
				
				$system->relocate('newsletter_queue.php');
				break;
		}
		
		$system->relocate('newsletter_write.php?'.$redirect);
	}	
}
?>
