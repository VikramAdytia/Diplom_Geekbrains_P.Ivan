<?
	include "../framework.php";	//Полезные функции
	include "../mysql.php";		//Подключение к базе и функции, работающие с базой
	
	if(isset($_GET["apiKey"])) {
		if(!CheckApiKey($_GET["apiKey"])) {
			echo JsonError("Неверный apiKey!");
			return;
		}
	} else {
		$us = sql_quick_query("SELECT `access` FROM `u0542462_diplom`.`users_auth` WHERE `token`=? AND `ip`=?", $_COOKIE["token"], $_SERVER['HTTP_X_FORWARDED_FOR']);
		if(count($us) == 0) {
			echo JsonError("Неверный apiKey!");
			return;
		}
	}

	if(!isset($_GET["log"])) {
		echo JsonError("Не указан тип логов!");
		return;
	}

	$args = array();
	$str = "";
	$res = null;
	
	switch($_GET["log"]) {
		case "connect":
			$str = "SELECT * FROM `u0542462_diplom`.`connections` WHERE `time` > ? AND `time` < ?";
			$args[] = $_GET["datefrom"];
			$args[] = $_GET["dateto"];

			if(isset($_GET["server"])) {
				$str = $str." AND `server` = ?";
				$args[] = $_GET["server"];
			}
			if(isset($_GET["steamid"])) {
				$str = $str." AND `steamid` = ?";
				$args[] = $_GET["steamid"];
			}
			$str = $str." ORDER BY `connections`.`id` DESC LIMIT 10000";

			$res = sql_quick_query($str, ...$args);
			for($i=0; $i<count($res); $i++) {
				$res[$i]["steamid"] = "".$res[$i]["steamid"]."";
			}

		break;
		case "chat":
			$str = "SELECT * FROM `u0542462_diplom`.`chat` WHERE `time` > ? AND `time` < ? AND `channel`=0";
			$args[] = $_GET["datefrom"];
			$args[] = $_GET["dateto"];

			if(isset($_GET["server"])) {
				$str = $str." AND `server` = ?";
				$args[] = $_GET["server"];
			}
			if(isset($_GET["steamid"])) {
				$str = $str." AND `steamid` = ?";
				$args[] = $_GET["steamid"];
			}
			$str = $str." ORDER BY `chat`.`id` DESC LIMIT 10000";
			
			$res = sql_quick_query($str, ...$args);
			for($i=0; $i<count($res); $i++) {
				$res[$i]["steamid"] = "".$res[$i]["steamid"]."";
			}
			
		break;
		case "teamchat":
			$str = "SELECT * FROM `u0542462_diplom`.`chat` WHERE `time` > ? AND `time` < ? AND `channel`=1";
			$args[] = $_GET["datefrom"];
			$args[] = $_GET["dateto"];

			if(isset($_GET["server"])) {
				$str = $str." AND `server` = ?";
				$args[] = $_GET["server"];
			}
			if(isset($_GET["steamid"])) {
				$str = $str." AND `steamid` = ?";
				$args[] = $_GET["steamid"];
			}
			if(isset($_GET["team"])) {
				$str = $str." AND `team` = ?";
				$args[] = $_GET["team"];
			}
			$str = $str." ORDER BY `chat`.`id` DESC LIMIT 10000";
			
			$res = sql_quick_query($str, ...$args);
			for($i=0; $i<count($res); $i++) {
				$res[$i]["steamid"] = "".$res[$i]["steamid"]."";
			}
			
		break;
		case "pm":
			$str = "SELECT * FROM `u0542462_diplom`.`pm` WHERE `time` > ? AND `time` < ?";
			$args[] = $_GET["datefrom"];
			$args[] = $_GET["dateto"];

			if(isset($_GET["server"])) {
				$str = $str." AND `server` = ?";
				$args[] = $_GET["server"];
			}
			if(isset($_GET["steamid"])) {
				if(isset($_GET["steamid2"])) {
					$str = $str." AND ((`steamid1` = ? AND `steamid2` = ?) OR (`steamid1` = ? AND `steamid2` = ?))";
					$args[] = $_GET["steamid"];
					$args[] = $_GET["steamid2"];
					$args[] = $_GET["steamid2"];
					$args[] = $_GET["steamid"];
				} else {
					$str = $str." AND (`steamid1` = ? OR `steamid2` = ?)";
					$args[] = $_GET["steamid"];
					$args[] = $_GET["steamid"];
				}
			}
			$str = $str." ORDER BY `pm`.`id` DESC LIMIT 10000";
			
			$res = sql_quick_query($str, ...$args);
			for($i=0; $i<count($res); $i++) {
				$res[$i]["steamid1"] = "".$res[$i]["steamid1"]."";
				$res[$i]["steamid2"] = "".$res[$i]["steamid2"]."";
			}
			
		break;
		case "tp":
			$str = "SELECT * FROM `u0542462_diplom`.`teleports` WHERE `time` > ? AND `time` < ?";
			$args[] = $_GET["datefrom"];
			$args[] = $_GET["dateto"];

			if(isset($_GET["server"])) {
				$str = $str." AND `server` = ?";
				$args[] = $_GET["server"];
			}
			if(isset($_GET["steamid"])) {
				if(isset($_GET["steamid2"])) {
					$str = $str." AND ((`steamid1` = ? AND `steamid2` = ?) OR (`steamid1` = ? AND `steamid2` = ?))";
					$args[] = $_GET["steamid"];
					$args[] = $_GET["steamid2"];
					$args[] = $_GET["steamid2"];
					$args[] = $_GET["steamid"];
				} else {
					$str = $str." AND (`steamid1` = ? OR `steamid2` = ?)";
					$args[] = $_GET["steamid"];
					$args[] = $_GET["steamid"];
				}
			}
			$str = $str." ORDER BY `teleports`.`id` DESC LIMIT 10000";
			
			$res = sql_quick_query($str, ...$args);
			for($i=0; $i<count($res); $i++) {
				$res[$i]["steamid1"] = "".$res[$i]["steamid1"]."";
				$res[$i]["steamid2"] = "".$res[$i]["steamid2"]."";
			}
			
		break;
		case "grant":
			$str = "SELECT * FROM `u0542462_diplom`.`grant` WHERE `time` > ? AND `time` < ?";
			$args[] = $_GET["datefrom"];
			$args[] = $_GET["dateto"];

			if(isset($_GET["server"])) {
				$str = $str." AND `server` = ?";
				$args[] = $_GET["server"];
			}
			if(isset($_GET["steamid"])) {
				$str = $str." AND `steamid` = ?";
				$args[] = $_GET["steamid"];
			}
			$str = $str." ORDER BY `grant`.`id` DESC LIMIT 10000";
			
			$res = sql_quick_query($str, ...$args);
			for($i=0; $i<count($res); $i++) {
				$res[$i]["steamid"] = "".$res[$i]["steamid"]."";
			}
			
		break;
		case "kit":
			$str = "SELECT * FROM `u0542462_diplom`.`kits` WHERE `time` > ? AND `time` < ?";
			$args[] = $_GET["datefrom"];
			$args[] = $_GET["dateto"];

			if(isset($_GET["server"])) {
				$str = $str." AND `server` = ?";
				$args[] = $_GET["server"];
			}
			if(isset($_GET["steamid"])) {
				$str = $str." AND `steamid` = ?";
				$args[] = $_GET["steamid"];
			}
			$str = $str." ORDER BY `kits`.`id` DESC LIMIT 10000";
			
			$res = sql_quick_query($str, ...$args);
			for($i=0; $i<count($res); $i++) {
				$res[$i]["steamid"] = "".$res[$i]["steamid"]."";
			}
			
		break;
	}

	//echo $str;
	//echo json_encode($args);
	
	//$res = sql_quick_query($str, ...$args);
	
	echo JsonSuccess($res);
?>