<?PHP
class Browser
{
	const MOBILE = 'mobile';
	const TABLET = 'tablet';
	
	const IPHONE = 'iphone';
	const IPAD = 'ipad';
	
	private static $_isMobile;
	private static $_isTablet;
	
	/**
	 * Upgrade to mobile_detect?
	 * http://detectmobilebrowsers.mobi/
	 */
	public static function isPlatform($platform)
	{
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		
		switch ($platform)
		{
			case self::MOBILE: // general mobile
				if (self::isPlatform(self::IPHONE))
					return true;
				
				if (strpos(strtolower($useragent), 'googlebot-mobile') > 0)
					return true;
				
				if (strpos(strtolower($useragent), 'windows') > 0)
					return false;
				
				if (preg_match('/(android|up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($useragent)))
					return true;
				
				if (isset($_SERVER['HTTP_ACCEPT']) && (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']))))
					return true;
				
				$mobile_ua = strtolower(substr($useragent, 0, 4));
				$mobile_agents = array(	'acs-','alav','alca','amoi','andr','audi','avan',
										'benq','bird','blac','blaz','brew',
										'cell','cldc','cmd-',
										'dang','doco',
										'eric',
										'hipt',
										'inno','ipaq',
										'java','jigs',
										'kddi','keji',
										'leno','lg-c','lg-d','lg-g','lge-',
										'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp',
										'nec-','newt','noki',
										'oper',
										'palm','pana','pant','phil','play','port','prox',
										'qwap',
										'sage','sams','sany','sch-','sec-','send','seri','sgh-','shar','sie-','siem','smal','smar','sony','sph-','symb',
										't-mo','teli','tim-','tosh','tsm-',
										'upg1','upsi',
										'vk-v','voda',
										'w3c ','wap-','wapa','wapi','wapp','wapr','webc','winw','winw',
										'xda','xda-'
									);
				
				if (in_array($mobile_ua, $mobile_agents))
					return true;
				
				if (isset($_SERVER['ALL_HTTP']) && strpos(strtolower($_SERVER['ALL_HTTP']), 'OperaMini') > 0)
					return true;
				
				break;
			case self::IPHONE: // iPhone & iPod Touch
				if (preg_match('/iphone/', strtolower($useragent)) || preg_match('/ipod/', strtolower($useragent)))
					return true;
				
				break;
			case self::TABLET: // general tablet
				// TODO: detect tablets
				if (self::isPlatform(self::IPAD))
					return true;
				
				break;
			case self::IPAD: // iPad
				if (preg_match('/ipad/', strtolower($useragent)))
					return true;
				
				break;
		}
		
		return false;
	}
	
	public static function isMobile()
	{
		if (!isset(self::$_isMobile))
			self::$_isMobile = self::isPlatform(self::MOBILE);
		
		return self::$_isMobile;
	}
	
	public static function setMobile($value)
	{
		self::$_isMobile = $value;
	}
	
	public static function isTablet()
	{
		if (!isset(self::$_isTablet))
			self::$_isTablet = self::isPlatform(self::TABLET);
		
		return self::$_isTablet;
	}
	
	public static function setTablet($value)
	{
		self::$_isTablet = $value;
	}
}