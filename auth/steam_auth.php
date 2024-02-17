<?php
	include "openid.php";
	
	//include "../framework.php";
	//include "../mysql.php";
	
	$_STEAMAPI = "TOP SECRET";

    $openid = new LightOpenID($_SERVER['SERVER_NAME']);
    if(!$openid->mode) 
    {
        if(isset($_GET['login'])) 
        {
            $openid->identity = 'https://steamcommunity.com/openid/?l=english'; 
            header('Location: ' . $openid->authUrl());
        }
    } 
    elseif($openid->mode == 'cancel') 
    {
        echo 'User has canceled authentication!';
    } 
    else 
    {
        if($openid->validate()) 
        {
			//$openid_json = json_decode(json_encode($openid));
			$openid_json = json_encode($openid);
			
			//echo $openid_json."<br><br>";
			//return;

			$openid_json = json_decode($openid_json);
			
			$id = $openid->identity;
			$sig = $openid_json->data->openid_sig;
			//echo $sig."<br><br>";
			//echo json_encode($openid)."<br>";
			// identity is something like: http://steamcommunity.com/openid/id/76561197960435530
			// we only care about the unique account ID at the end of the URL.
			$steamid = str_replace("https://steamcommunity.com/openid/id/", "", $id);
			//echo "User is logged in (steamID: $steamid)"."<br><br>";
			
			/*$params = array( 
			  'apiKey' => "NEPTrWHdtXFRpOmrRekvVvkfFneYLNwF", 
			  'steamid' => $steamid, 
			  'sig' => $sig
			); 
			if ($curl = curl_init()) {
				$post = curl_init('https://chernooh.ru/users/regUser.php');
				curl_setopt($post, CURLOPT_POSTFIELDS, $params);
				curl_setopt($post, CURLOPT_RETURNTRANSFER, true);
				curl_exec($post);
				curl_close($post);
			}*/
			
			sql_select("users");
			sql_db("u0542462_diplom");
			sql_where("steamid", $steamid);
			$res = sql_execute();
			
			if(count($res) == 0 || $res[0]["authlevel"] == 0) {
				return;
			}
			//if($res[0])
			
			//echo json_encode($res);
			
			$token = md5($openid_json->data->openid_sig);
			
			sql_insert("users_auth");
			sql_db("u0542462_diplom");
			sql_set("steamid", $steamid);
			sql_set("access", 1);
			sql_set("token", $token);
			sql_set("ip", $_SERVER['HTTP_X_FORWARDED_FOR']);
			sql_execute();
			
			setcookie("token", $token);
			
			header("Location: https://gbdiplom.chernooh.ru/logs");
			die();
        } 
        else 
        {
			echo "User is not logged in.\n";
        }
    }
?>