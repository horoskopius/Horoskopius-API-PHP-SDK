<?php
if (!function_exists('curl_init')) {
  throw new HoroskopiusAPIException('Horoskopius SDK zahteva CURL PHP ekstenziju.');
}
if (!function_exists('json_decode')) {
  throw new HoroskopiusAPIException('Horoskopius SDK zahteva JSON PHP ekstenziju.');
}
define('HOR_PATH_BASE', dirname(__FILE__) );
define( 'DRS', DIRECTORY_SEPARATOR );


class HoroskopiusAPIException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}


class HoroskopiusSDK {
	
	private $public_key;
	private $private_key;
	private $horoscope;
	private $horoscope_type;
	private $category;
	private $cache;
	private $response_type;
	private $signature;
	/* Headlines */
	private $headline_horoscope;
	private $headline_category;
	private $headline_type;
	private $date_horoskop;
	private $speedup;
	private $latin;
	
	public function __construct() {
			 $this->response_type = 'xml';
			 $this->horoscope = 1;
			 $this->category = 1;
			 $this->horoscope_type = 1;
			 $this->cache = 1;
			 $this->speedup = 1;
			 $this->latin = 1;
	}
	
	public function setAlphabet($i) {
			$this->latin = ($i > 0 && $i <= 2) ? $i : $this->latin;
	}
	
	private function generateSignature($k) {
		$sig = base64_encode(hash_hmac('sha1', $k, true));	
		return $sig;
	}
	
	public function setResponseType($s) {
			if ($s == NULL) : 
			$this->response_type = 'xml';
			else: 
			$this->response_type = $s;
			endif;
	}
	
	public function setHoroscope($i) {
			$this->horoscope = ($i > 0 && $i <= 2) ? $i : $this->horoscope;
			if ($this->horoscope == 1) : 
			$this->headline_horoscope = 'Horoskop';
			else : 
			$this->headline_horoscope = 'Kineski horoskop';
			endif;
	}
	
	public function setCache($i) {
			$this->cache = ($i > 0 && $i <= 2) ? $i : $this->cache;
	}
	
	public function setSpeedUp($i) {
			$this->speedup = ($i > 0 && $i <= 2) ? $i : $this->speedup;
	}
	
	public function setCategory($i) {
			$this->category = ($i > 0 && $i <= 3) ? $i : $this->category;
			if ($this->category == 1 && $this->horoscope == 1) : 
			$this->headline_category = '';
			elseif($this->category == 2 && $this->horoscope == 1) : 
			$this->headline_category = 'Ljubavni ';
			elseif($this->category == 3 && $this->horoscope == 1) :
			$this->headline_category = 'Poslovni ';
			else : 
			$this->headline_category = null;
			endif;
	}
	
	public function setHoroscopeType($i) {
			$this->horoscope_type = ($i > 0 && $i <= 3) ? $i : $this->horoscope_type;
			if ($this->horoscope_type == 1) : 
			$this->headline_type = 'Dnevni ';
			elseif($this->horoscope_type == 2) : 
			$this->headline_type = 'Nedeljni ';
			elseif($this->horoscope_type == 3) :
			$this->headline_type = 'Mesečni ';
			else : 
			$this->headline_type = null;
			endif;
	}
	
	public function setPrivateKey($k) {
			$this->private_key = $k;
	}
	
	public function setPublicKey($k) {
			$this->public_key = $k;	
	}
	
	public function getResponse() {
		switch ($this->response_type) :
			case 'xml':
				$this->returnXML();
			break;
			
			case 'json':
				$this->returnJSON();
			break;
		endswitch;
	}
	
	private function setCurlResponse() {	
		$this->signature = $this->generateSignature($this->private_key);
		$url = "http://dev.horoskopius.com/service/";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "type=$this->response_type&horoscope=$this->horoscope&category=$this->category&horoscope_type=$this->horoscope_type&sig=" . urlencode($this->signature) . "&auth=" . urlencode($this->public_key) . "&cache=" . $this->cache .""); 
		curl_setopt($ch, CURLOPT_USERAGENT, 'Horoskopius');
		$result = curl_exec($ch);
		return $result;
	}
	
	private function returnXML() {
		$cachefile = HOR_PATH_BASE . DRS . 'cachebase' . DRS . $this->response_type . $this->category . $this->horoscope . $this->horoscope_type . '.htm';
		$cachetime = 180*60;
		if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile)) && $this->speedup == 1) :
		require($cachefile);
		echo '<!-- speed up horoskopius -->';
		else:
		$response = $this->setCurlResponse();
		$xml = new SimpleXmlElement($response, LIBXML_NOCDATA);
		$cnt = count($xml->{"horoscope"});
		$content = '<div id="horoskopius">'.
			 '<h2>' . $this->latin2cyrillic($this->headline_type . $this->headline_category . $this->headline_horoscope) . '</h2>'.
			 '<span class="horoskopius-date"></span><ul>';
		for($i=0; $i<$cnt; $i++) :
			$content.= '<li><span class="sign-container hor-' . strtolower(str_replace("Š", "s", $xml->{"horoscope"}[$i]->{"sign"})) . '"></span><h3>' . $this->latin2cyrillic($xml->{"horoscope"}[$i]->{"sign"}) . '</h3> ' . $this->latin2cyrillic($xml->{"horoscope"}[$i]->{"horoscopetxt"}) . '</li>';
		endfor;
			$content .= '<li class="horoskopius-link">' . $this->latin2cyrillic('Horoskop obezbedio - Astro portal').' <a href="http://www.horoskopius.com">Horoskopius</a></li>';
			$content .= '</ul></div>';
			echo $content;
		$fp = fopen($cachefile, 'w');
		fwrite($fp, $content);
		fclose($fp);
		endif;
	}
	
	private function returnJSON() {
		
		$cachefile = HOR_PATH_BASE . DRS . 'cachebase' . DRS . $this->response_type . $this->category . $this->horoscope . $this->horoscope_type . '.htm';
		$cachetime = 180*60;
		if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile)) && $this->speedup == 1) :
		require($cachefile);
		echo '<!-- speed up horoskopius -->';
		else:
		$response = $this->setCurlResponse();
		$json = json_decode($response);
		$content = '<div id="horoskopius">'.
			 '<h2>' . $this->latin2cyrillic($this->headline_type . $this->headline_category . $this->headline_horoscope) . '</h2>'.
			 '<span class="horoskopius-date"></span><ul>';
			$cnt = count($json->{"horoscope"});
			for($i=0; $i<$cnt; $i++) :
			$content .= '<li><span class="sign-container hor-' . strtolower(str_replace("Š", "s", $json->{"horoscope"}[$i]->{"name_sign"})) . '"></span><h3>' . $this->latin2cyrillic($json->{"horoscope"}[$i]->{"name_sign"}) . '</h3> ' . $this->latin2cyrillic($json->{"horoscope"}[$i]->{"txt_hrs"}) . '</li>';
			endfor;
			$content .= '<li class="horoskopius-link"> ' . $this->latin2cyrillic('Horoskop obezbedio - Astro portal') . ' <a href="http://www.horoskopius.com">Horoskopius</a></li>';
			$content .= '</ul></div>';
			echo $content;
		$fp = fopen($cachefile, 'w');
		fwrite($fp, $content);
		fclose($fp);
		endif;
	}
	
	
	private function latin2cyrillic($text) {
		
		$tr = array(
					"A"=>"А",
					"B"=>"Б",
					"C"=>"Ц",
					"Č"=>"Ч",
					"D"=>"Д",
					"Đ"=>"Ђ",
					"E"=>"Е",
					"F"=>"Ф",
					"G"=>"Г",
					"H"=>"Х",
					"I"=>"И", 
					"J"=>"Ј",
					"K"=>"К",
					"L"=>"Л",
					"M"=>"М",
					"N"=>"Н", 
					"O"=>"О",
					"P"=>"П",
					"R"=>"Р",
					"S"=>"С",
					"Š"=>"Ш", 
					"T"=>"Т",
					"U"=>"У",
					"V"=>"В",
					"Z"=>"З",
					"Ž"=>"Ж", 
					"Ć"=>"Ћ",
					"a"=>"а",
					"b"=>"б",
					"c"=>"ц",
					"č"=>"ч", 
					"ć"=>"ћ",
					"d"=>"д",
					"đ"=>"ђ",
					"e"=>"е",
					"f"=>"ф",
					"g"=>"г", 
					"h"=>"х",
					"i"=>"и",
					"j"=>"ј",
					"k"=>"к",
					"l"=>"л", 
					"m"=>"м",
					"n"=>"н",
					"o"=>"о",
					"p"=>"п",
					"r"=>"р", 
					"s"=>"с",
					"š"=>"ш",
					"t"=>"т",
					"u"=>"у",
					"v"=>"в", 
					"z"=>"з",
					"ž"=>"ж",
					"Lj"=>"Љ",
					"Nj"=>"Њ",
					"Dž"=>"Џ",
					"lj"=>"љ",
					"nj"=>"њ",
					"dž"=>"џ"
					);
	if ($this->latin == 2) : 
	return strtr($text,$tr);	
	else : 
	return $text;
	endif;
	}
	
}
?>