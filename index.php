<?
	include "api/mysql.php";
	include "auth/steam_auth.php";
	
	$auth = false;
	
	if(isset($_COOKIE["token"])) {
		sql_select("users_auth");
		sql_db("u0542462_diplom");
		sql_where("token", $_COOKIE["token"]);
		sql_where("ip", $_SERVER['HTTP_X_FORWARDED_FOR']);
		$res = sql_execute();
		
		if(count($res) == 0) {
			?>
			<form action="?login" method="post">
				<input type="image" src="http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_small.png" alt="Войти через Steam">
			</form>
			<?
			return;
		} else {
			$auth = true;
			include "servers.php";
		}
	} else {
		?>
		<form action="?login" method="post">
			<input type="image" src="http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_small.png" alt="Войти через Steam">
		</form>
		<?
	}
?>