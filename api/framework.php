<?php
	header('Content-Type: application/json; charset=utf-8');
	//header('Content-Type: text/html; charset=utf-8');
	
	$postdata = Array();
	
	$apiKeys = array(
		"TOP_SECRET" => 1
	);
	
	function Modules(...$module) {
		if($module != null) {
			foreach ($module as $m) {
				include "modules/$m.php";
			}
		}
	}
	
	function bti($bool) {
		if($bool) {
			return 1;
		} else {
			return 0;
		}
	}
	
	function CheckApiKey($apiKey) {
		global $apiKeys;
	
		if(array_key_exists($apiKey, $apiKeys)) {
			if($apiKeys[$apiKey] > 0) {
				return true;
			} else {
				LogToFile("apiKeys", "Cтарый ключ: ".$apiKey."\nHost: ".$_SERVER[REQUEST_URI]."Origin: ".$_SERVER['HTTP_ORIGIN']."\nIP: ".$_SERVER['HTTP_X_FORWARDED_FOR']."\n");
				return false;
			}
		}
		LogToFile("apiKeys", "Несуществующий ключ".$apiKey."\nHost: ".$_SERVER[REQUEST_URI]."Origin: ".$_SERVER['HTTP_ORIGIN']."\nIP: ".$_SERVER['HTTP_X_FORWARDED_FOR']."\n");
		return false;
	}
	
	function JsonPost() {
		global $postdata;
		
		$postdata = json_decode(file_get_contents('php://input'), JSON_UNESCAPED_UNICODE);
	}

	function GetPhpInput()
	{
		return json_decode(file_get_contents('php://input'), JSON_UNESCAPED_UNICODE);
	}
	
	function LogToFile($file, $text) {
		if(!is_dir($_SERVER['DOCUMENT_ROOT']."/logs/".$file."/")) {
			mkdir($_SERVER['DOCUMENT_ROOT']."/logs/".$file."/");
		}
		file_put_contents($_SERVER['DOCUMENT_ROOT']."/logs/".$file."/".date('Y-m-d').".txt", "[".date("H:i:s")."] ".$text."\n", FILE_APPEND);
	}
	
	function JsonResponse($data, $type = "response", $encode = true) {
		$res = Array(
			$type => $data
		);
		if($encode) {
			return json_encode($res, JSON_UNESCAPED_UNICODE);
		} else {
			return $res;
		}
	}
	
	function JsonSuccess($data, $encode = true)
	{
		$res = Array(
			"status" => "success",
			"data" => $data
		);
		if($encode) {
			return json_encode($res, JSON_UNESCAPED_UNICODE);
		} else {
			return $res;
		}
	}
	
	function JsonError($message, $encode = true)
	{
		$res = Array(
			"status" => "error",
			"message" => $message
		);
		if($encode) {
			return json_encode($res, JSON_UNESCAPED_UNICODE);
		} else {
			return $res;
		}
	}
	
	function Curl( $url, $data ) {
		$post = curl_init($url);
		curl_setopt($post, CURLOPT_POSTFIELDS, $data);
		curl_setopt($post, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($post);
		curl_close($post);
		return $response;
	}
	
	/*function GetPost( $arg ) {
		return $_GET[$arg].$_POST[$arg];
	}*/
	
	function FormatTime( $t, $a ) {
		$name1 = "еденицу времени";
		$name2 = "еденицы времени";
		$name3 = "едениц времени";
		if($a == "s") {
			$name1 = "секунду";
			$name2 = "секунды";
			$name3 = "секунд";
		} elseif($a == "m") {
			$name1 = "минуту";
			$name2 = "минуты";
			$name3 = "минут";
		} elseif($a == "h") {
			$name1 = "час";
			$name2 = "часа";
			$name3 = "часов";
		} elseif($a == "d") {
			$name1 = "день";
			$name2 = "дня";
			$name3 = "дней";
		}

		if ( $t < 0 ) {
			return "*";
		} else {
			$t1 = substr($t, strlen($t)-1);
			//$t2 = substr($t, strlen($t)-2);
			//$t1 = tonumber( string.sub(t, string.len(t)) )
			//$t2 = tonumber( string.sub(t, string.len(t)-1) )
			if ( $t > 4 and $t < 21 ) { return $name3; }
			elseif ( $t1 == 1 ) { return $name1; }
			elseif ( $t1 >= 2 and $t1 <= 4 ) { return $name2; }
			elseif ( $t1 >= 5 and $t1 <= 9 or $t1 == 0 ) { return $name3; }
		}
	}
	
	function NiceFormatTime( $t ) {
		if($t < 60) {
			return $t." ".FormatTime( $t, "s" );
		} else if($t < 3600) {
			return floor($t/60)." ".FormatTime( floor($t/60), "m" );
		}  else {
			return floor($t/60/60)." ".FormatTime( floor($t/60/60), "h" );
		} 
	}

	function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}
	
	function GetMonth($m) {
		if($m == 1) {return "Января";}
		elseif($m == 2) {return "Февраля";}
		elseif($m == 3) {return "Марта";}
		elseif($m == 4) {return "Апреля";}
		elseif($m == 5) {return "Мая";}
		elseif($m == 6) {return "Июня";}
		elseif($m == 7) {return "Июля";}
		elseif($m == 8) {return "Августа";}
		elseif($m == 9) {return "Сентября";}
		elseif($m == 10) {return "Октября";}
		elseif($m == 11) {return "Ноября";}
		elseif($m == 12) {return "Декабря";}
	}
	
	function FormatLastJoin($n) {
		$t = time() + 10800 - $n;
		if($t < 60) {
			return $t." ".FormatTime( $t, "s" )." назад";
		} elseif($t < 3600) {
			$t = floor($t/60);
			return $t." ".FormatTime( $t, "m" )." назад";
		} elseif($t < 14400) {
			$t = floor($t/60/60);
			return $t." ".FormatTime( $t, "h" )." назад";
		} else {
			if(gmdate("d", $n) == gmdate("d", time()+10800) and gmdate("n", $n) == gmdate("n", time()+10800)) {
				return "Сегодня в ".gmdate("H:i", $n);
			} elseif(gmdate("d", $n)+1 == gmdate("d", time()+10800) and gmdate("n", $n) == gmdate("n", time()+10800)) {
				return "Вчера в ".gmdate("H:i", $n);
			} else {
				$month = GetMonth(gmdate("n", $n)+0);
				if(gmdate("y", $n) == gmdate("y", time())){
					return gmdate("j ".$month." в H:i", $n);
				} else {
					return gmdate("j ".$month." 20y года в H:i", $n);
				}
			}
		}
	}
	
	function ParseCommand($str)
	{
		$flag = false;
		$args = array();
		$str1 = "";
		$num = 0;
		for($i = 0; $i < strlen($str); ++$i) {
			$chr = $str[$i];

			if($chr == '"'){
				$flag = !$flag;
			} elseif ($chr == " ") {
				if(!$flag){
					//echo $str1."<br>";
					$args[$num] = $str1."";
					$num += 1;
					$str1 = "";
				} else {
					$str1 = $str1.$chr;
				}
			} else {
				$str1 = $str1.$chr;
			}
		}
		$args[$num] = $str1."";
		return $args;
	}
	
	function generate_password($number)
	{
		$arr = array('A','B','C','D','E','F',
					 'G','H','I','J','K','L',
					 'M','N','O','P','R','S',
					 'T','U','V','X','Y','Z',
					 '1','2','3','4','5','6',
					 '7','8','9','0');
		$pass = "";
		for($i = 0; $i < $number; $i++)
		{
		  $index = rand(0, count($arr) - 1);
		  $pass .= $arr[$index];
		}
		return $pass;
	}
	
	function generate_password2($number)
	{
		$arr = array('A','B','C','D','E','F',
					 'G','H','I','J','K','L',
					 'M','N','O','P','R','S',
					 'T','U','V','X','Y','Z',
					 'a','b','c','d','e','f',
					 'g','h','i','j','k','l',
					 'm','n','o','p','r','s',
					 't','u','v','x','y','z',
					 '1','2','3','4','5','6',
					 '7','8','9','0');
		$pass = "";
		for($i = 0; $i < $number; $i++)
		{
		  $index = rand(0, count($arr) - 1);
		  $pass .= $arr[$index];
		}
		return $pass;
	}


?>