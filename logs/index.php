<?
	include "../api/mysql.php";
	
	$auth = false;
	
	if(isset($_COOKIE["token"])) {
		sql_select("users_auth");
		sql_db("u0542462_diplom");
		sql_where("token", $_COOKIE["token"]);
		sql_where("ip", $_SERVER['HTTP_X_FORWARDED_FOR']);
		$res = sql_execute();
		
		if(count($res) == 0) {
			header("Location: https://gbdiplom.chernooh.ru/");
			die();
		} else {
			$auth = true;
			include "index2.php";
		}
	} else {
		header("Location: https://gbdiplom.chernooh.ru/");
		die();
	}
?>