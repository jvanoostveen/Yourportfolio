<?PHP

class Queue
{
	function send()
	{
		ob_start();
		
		require_once(NL_CODE.'classes/NewsletterMailer.php');
		
		global $yourportfolio, $db, $system, $settings;
		
		$mailers = array();
		
		$queue = array();
		$query = sprintf("SELECT letter_id, addr_name, addr_email FROM `%s` WHERE status = 'unsent' ORDER BY addr_name LIMIT %d", $yourportfolio->_table['nl_queue'], $settings['batch_size']);
		$db->doQuery( $query, $queue, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		
		if (empty($queue))
		{
			return;
		}
		
		$sent_count = 0;
		$error_count = 0;
		
		if( $settings['debug'] == true )
		{
			trigger_error("Queue size is ".count($queue) );
		}
		
		// loop through users/emailaddresses
		foreach ($queue as $q)
		{
			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];
			$Tbegintime = $time;	
			
			// create NewsletterMailer when there is none yet.
			if (!in_array($q['letter_id'], array_keys($mailers)))
			{
				// initialize mailer
				$mailer = new NewsletterMailer();
				
				$newsletter = new Newsletter();
				$newsletter->id = $q['letter_id'];
				$newsletter->load();
				$newsletter->loadItems();
				
				if (!empty($newsletter->sender))
				{
					$mailer->FromName = $newsletter->sender;
				}
				
				$template = $newsletter->getTemplate();
				$template->loadDesign();
				
				$view = new NewsletterView();
				$view->newsletter = $newsletter;
				$view->template = $template;
				
				$view->build( true );
				$view->buildText( true );
		
				
				// embed cid names to <img src="..."
				$cid_map = array();
				$pattern = "/(src|background)=\"(.*?)\"/";
				preg_match_all($pattern, $view->html, $matches);
				
				$images = $matches[2];
				$i=0;
				foreach ($images as $image_path)
				{
					if( !in_array( $image_path, array_keys($cid_map) ) )
					{
						
						$image_name = substr($image_path, strrpos($image_path, '/') + 1);
						$cid = md5($image_name);
						
						$info = getimagesize($image_path);
						if( isset($info['mime']) && !empty($info['mime']) )
						{
							$mime = $info['mime'];
						} else {
							// fall back to extension method
							$ext = strtolower(NewsletterView::extension($image_path));
							switch($ext)
							{
								case 'jpg':
									$mime = 'image/jpeg';
									break;
								case 'gif':
									$mime = 'image/gif';
									break;
								case 'png':
									$mime = 'image/png';
									break;
								default:
									$mime = 'application/octet-stream';
									break;
							}
						}
						
						$mailer->AddEmbeddedImage($image_path, $cid, $image_name, 'base64', $mime);
						$cid_map[$image_path] = $cid;
					} else {
						$cid = $cid_map[$image_path];
					}
					
					$view->html = str_replace($matches[1][$i].'="'.$image_path, $matches[1][$i].'="cid:'.$cid, $view->html);
					$i++;
				}
				
				$mailer->Subject = $newsletter->subject;
				$mailer->Body = $view->html;
				$mailer->AltBody = $view->text;
				
				$mailers[$q['letter_id']] = $mailer;
			}
			
			$mailer = $mailers[$q['letter_id']];
			
			$mailer->AddAddress($q['addr_email'], stripslashes($q['addr_name']));
			
			if( $settings['debug'] == true)
			{
				trigger_error("- Sending message to ".$q['addr_email']." using ".$mailer->Mailer);
			}

			// replace special tags
			if (strpos($mailer->Body, '[[[ID]]]') !== false)
			{
				$mailer->Body = str_replace(array('[[[ID]]]'), array($q['addr_email']), $mailer->Body);
				
				unset($mailers[$q['letter_id']]);
			}
			
			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];
			$Qbegintime = $time;
			
			try
			{
				$mailer->Send();
				
				$time = microtime();
				$time = explode(' ', $time);
				$time = $time[1] + $time[0];
				$Qendtime = $time;
				if( $settings['debug'] == true)
				{
					trigger_error("- Message delivered to queue in ".($Qendtime - $Qbegintime));
				}
				
				$sent_count++;
				
				// message sent
				$result = null;
				$query = sprintf("UPDATE `%s` SET status='sent' WHERE letter_id='".$q['letter_id']."' AND addr_email='".$db->filter($q['addr_email'])."'", $yourportfolio->_table['nl_queue']);
				$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
			} catch (Exception $e)
			{
				// error while sending
				$error_count++;
				trigger_error('Failure! Message could not be send to: '.$q['addr_email'].', ErrorInfo: '.$mailer->ErrorInfo);
				
				if( $settings['debug'] == true)
				{
					if ($mailer->Mailer == 'smtp')
						trigger_error("SMTP settings: ".$mailer->Username.':*********@'.$mailer->Host);
				}
				
				$query = sprintf( "INSERT INTO `%s` SET `letter_id`=%d, `errors`=1 ON DUPLICATE KEY UPDATE `errors`=`errors`+1", $yourportfolio->_table['nl_letter_stats'], $q['letter_id']);
				$db->doQuery( $query, $void, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false );

				// depending on error
				// -> no connection to server
				//		abort the whole queue
				// -> emailaddress error
				//		set address status flag
				//		continue with queue
				switch ($mailer->ErrorInfo)
				{
					case ('SMTP Error: Could not connect to SMTP host.'):
						// set error flag
						// reset mailer
						$mailer->ClearAddresses();
						
						// close smtp
						$mailer->SmtpClose();
						
						// stop the next batch from starting by javascript
						$return = array('fatal' => 'connect' );
						return $return;
					default:
						$result = null;
						$query = sprintf("UPDATE `%s` SET status='error' WHERE letter_id='".$q['letter_id']."' AND addr_email='".$db->filter($q['addr_email'])."'", $yourportfolio->_table['nl_queue']);
						$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
						
						$query = sprintf("UPDATE `%s` SET status=%d WHERE address='%s' LIMIT 1", $yourportfolio->_table['nl_addresses'], AddressStatus::ERROR(), $db->filter($q['addr_email']));
						$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);				
				}
			}
			
			$mailer->ClearAddresses();
			
			if ($mailer->Mailer == 'smtp')
				$mailer->SmtpClose();

			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];
			$Tendtime = $time;
			
			if( $settings['debug'] == true)
			{
				trigger_error("Roundtrip time is ".($Tendtime - $Tbegintime) );
			}
		}
		
		// all messages sent (or tried)
		// clean up queue and letter status
		$query = sprintf("SELECT DISTINCT letter_id FROM `%s` q1 WHERE NOT EXISTS (SELECT * FROM `%s` q2 WHERE q1.letter_id=q2.letter_id AND q2.status = 'unsent')", $yourportfolio->_table['nl_queue'], $yourportfolio->_table['nl_queue']);
		$list = array();
		$db->doQuery($query, $list, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array', false);
		
		if (!empty($list))
		{
			$list = implode($list, "','");
			
			$query = sprintf("UPDATE `%s` SET status='sent' WHERE letter_id IN ('".$list."')", $yourportfolio->_table['nl_letters']);
			$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
			
			$query = sprintf("DELETE FROM `%s` WHERE letter_id IN ('".$list."')", $yourportfolio->_table['nl_queue']);
			$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		}
		
		$query = sprintf("SELECT COUNT(*) FROM `%s` WHERE status = 'unsent'", $yourportfolio->_table['nl_queue']);
		$unsent_count = 0;
		$db->doQuery($query, $unsent_count, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		
		$ob = ob_get_clean();
		if (!empty($ob))
		{
			error_log('queue premature output: '.$ob);
		}
		
		$return = array('sent' => $sent_count, 'unsent' => $unsent_count, 'errors' => $error_count);
		
		return $return;
	}
}
?>
