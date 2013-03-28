<?PHP

require_once(CODE.'vendor/PHPMailer/class.phpmailer.php');

class NewsletterMailer extends PHPMailer
{
	
	function NewsletterMailer()
	{
		$this->__construct();
	}
	
	function __construct()
	{
		$this->exceptions = true;
		
		try
		{
			$this->SetLanguage('en', CODE.'vendor/PHPMailer/language/');
			
			global $settings;
			
			// send method
			switch( $settings['mail_method'])
			{
				case 'mail':
					$this->IsMail();
					break;
				case 'sendmail':
					$this->IsSendmail();
					break;
				case 'smtp':
					$this->IsSMTP();
					$this->Host = $settings['smtp_host'];
					if( !empty($settings['smtp_username']) )
						$this->SMTPAuth = true;
					else
						$this->SMTPAuth = false;
					$this->SMTPKeepAlive = true;
					
					$this->Username = $settings['smtp_username'];
					$this->Password = $settings['smtp_password'];
					break;
				default:
					$this->IsMail();
					break;
			}
			
			$this->SetFrom($settings['mbox_address'], $settings['from_name']);
			$this->AddReplyTo($settings['mbox_address'], $settings['from_name']);
			
			$this->IsHTML(true);
		} catch (phpmailerException $e)
		{
			error_log($e->errorMessage());
		} catch (Exception $e)
		{
			error_log($e->getMessage());
		}
	}
	
	function Send()
	{
		try
		{
			parent::Send();
		} catch (phpmailerException $e)
		{
			trigger_error('Mail could not be sent: '.$e->errorMessage());
		} catch (Exception $e)
		{
			trigger_error('Mail could not be sent: '.$e->getMessage());
		}
	}
}

?>