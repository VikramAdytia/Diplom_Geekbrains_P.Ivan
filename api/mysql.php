<?php
	$link = mysqli_connect('localhost','u0542462_diplom','TOP_SECRET');
	$link->set_charset("utf8mb4");
	//mysqli_query($link,'SET NAMES utf8');

	$sqlonlyvalues = false;
	$sql_array = array();

	function sql_select($table = null, ...$columns) {
		global $sql_array;
		$sql_array = array(
			"action" => "SELECT"
		);
		if($table != null) {
			$sql_array["table"] = $table;
		}
		if($columns != null) {
			if(!array_key_exists("columns", $sql_array)) {
				$sql_array["columns"] = array();
			}
			$sql_array["columns"] = array_merge($sql_array["columns"], $columns);
		}
	}

	function sql_update($table = null) {
		global $sql_array;
		$sql_array = array(
			"action" => "UPDATE"
		);
		if($table != null) {
			$sql_array["table"] = $table;
		}
	}

	function sql_insert($table = null) {
		global $sql_array;
		$sql_array = array(
			"action" => "INSERT"
		);
		if($table != null) {
			$sql_array["table"] = $table;
		}
	}
	
	function sql_delete($table = null) {
		global $sql_array;
		$sql_array = array(
			"action" => "DELETE"
		);
		if($table != null) {
			$sql_array["table"] = $table;
		}
	}
	
	function sql_table($table) {
		global $sql_array;
		$sql_array["table"] = $table;
	}
	
	function sql_db($db) {
		global $sql_array;
		$sql_array["database"] = $db;
	}
	
	function sql_columns(...$columns) {
		global $sql_array;
		if(!array_key_exists("columns", $sql_array)) {
			$sql_array["columns"] = array();
		}
		$sql_array["columns"] = array_merge($sql_array["columns"], $columns);
	}
	
	function sql_set($key = null, $value = null) {
		global $sql_array;
		
		if(!array_key_exists("set", $sql_array)) {
			$sql_array["set"] = array();
		}
		$sql_array["set"][] = array($key, $value);
	}

	function sql_where($key, $value, $eq = "=", $ao = null) {
		global $sql_array;
		
		if(!array_key_exists("where", $sql_array)) {
			$sql_array["where"] = array();
		}
		if($ao == null and count($sql_array["where"]) > 0) {
			$ao = "AND";
		}
		$sql_array["where"][] = array($key, $value, $eq, $ao);
	}
	function sql_or_where($key, $value) {
		sql_where($key, $value, "=", "OR");
	}
	function sql_where_like($key, $value) {
		sql_where($key, $value, "LIKE", null);
	}
	function sql_or_where_like($key, $value) {
		sql_where($key, $value, "LIKE", "OR");
	}
	function sql_where_not($key, $value) {
		sql_where($key, $value, "!=", null);
	}
	function sql_or_where_not($key, $value) {
		sql_where($key, $value, "!=", "OR");
	}
	function sql_where_complex($where) {
		global $sql_array;
		$sql_array["where"][] = $where;
	}
	function sql_where_full($where, $params = null) {
		global $sql_array;
		$sql_array["where_full"] = array($where, $params);
	}
	
	function sql_order($order, $da = "DESC") {
		global $sql_array;
		$sql_array["order"] = array($order, $da);
	}
	
	function sql_limit(...$limit) {
		global $sql_array;
		$sql_array["limit"] = $limit;
	}
	
	function sql_execute() {
		global $sql_array;
		
		$query = "";
		$binds = array();
		
		if($sql_array["action"] == "SELECT") {
			$query = $query."SELECT ";
			
			if(array_key_exists("columns", $sql_array)) {
				
				$columns = array();
				
				for ($i = 0; $i < count($sql_array["columns"]); $i++) {
					$columns[] = "`".$sql_array["columns"][$i]."`";
				}
				$query = $query.implode( ", " , $columns )." ";
			} else {
				$query = $query."* ";
			}
			if(array_key_exists("table", $sql_array)) {
				if( array_key_exists("database", $sql_array) ) {
					$query = $query."FROM `".$sql_array["database"]."`.`".$sql_array["table"]."` ";
				} else {
					$query = $query."FROM `".$sql_array["table"]."` ";
				}
			}
		} else if($sql_array["action"] == "UPDATE") {
			$query = $query."UPDATE ";
			
			if(array_key_exists("table", $sql_array)) {
				if(array_key_exists("database", $sql_array)) {
					$query = $query."`".$sql_array["database"]."`.`".$sql_array["table"]."` ";
				} else {
					$query = $query."`".$sql_array["table"]."` ";
				}
			}
			if(array_key_exists("set", $sql_array)) {
				$query = $query."SET ";
				
				$set = array();
				
				for ($i = 0; $i < count($sql_array["set"]); $i++) {
					$set[] = "`".$sql_array["set"][$i][0]."`=?";
					$binds[] = $sql_array["set"][$i][1];
				}
				$query = $query.implode( ", " , $set )." ";
			}
		} else if($sql_array["action"] == "INSERT") {
			$query = $query."INSERT INTO ";
			
			if(array_key_exists("table", $sql_array)) {
				if(array_key_exists("database", $sql_array)) {
					$query = $query."`".$sql_array["database"]."`.`".$sql_array["table"]."` ";
				} else {
					$query = $query."`".$sql_array["table"]."` ";
				}
			}
			if(array_key_exists("set", $sql_array)) {
				$set = array();
				$values = array();
				
				for ($i = 0; $i < count($sql_array["set"]); $i++) {
					$set[] = "`".$sql_array["set"][$i][0]."`";
					$values[] = "?";
					$binds[] = $sql_array["set"][$i][1];
				}
				$query = $query."( ".implode( ", " , $set )." ) ";
				
				$query = $query."VALUES ( ".implode( ", " , $values )." ) ";
			}
		} else if($sql_array["action"] == "DELETE") {
			$query = $query."DELETE FROM ";
			
			if(array_key_exists("table", $sql_array)) {
				if(array_key_exists("database", $sql_array)) {
					$query = $query."`".$sql_array["database"]."`.`".$sql_array["table"]."` ";
				} else {
					$query = $query."`".$sql_array["table"]."` ";
				}
			}
		} else {
			LogToFile("sql", "Неизвестный метод ".$sql_array["action"]."\n".json_encode($sql_array));
			return;
		}
		
		if(array_key_exists("where_full", $sql_array)) {
			$query = $query."WHERE ";
			
			for ($i = 0; $i < count($sql_array["where"]); $i++) {
				$query = $query.$sql_array["where"][$i]." ";
			}
		} else if(array_key_exists("where", $sql_array)) {
			$query = $query."WHERE ";
			
			for ($i = 0; $i < count($sql_array["where"]); $i++) {
				if(gettype($sql_array["where"][$i]) == "string") {
					$query = $query.$sql_array["where"][$i]." ";
				} else {
					if($sql_array["where"][$i][3] != null) {
						$query = $query.$sql_array["where"][$i][3]." ";
					}
					$query = $query."`".$sql_array["where"][$i][0]."` ";
					$query = $query.$sql_array["where"][$i][2]." ";
					$query = $query."? ";
					$binds[] = $sql_array["where"][$i][1];
				}
			}
			
		}
		
		if(array_key_exists("order", $sql_array)) {
			$query = $query."ORDER BY `".$sql_array["order"][0]."` ".$sql_array["order"][1]." ";
			
		}
		if(array_key_exists("limit", $sql_array)) {
			$query = $query."LIMIT ".implode( "," , $sql_array["limit"] )." ";
		}

		//echo $query."\n";
		//echo json_encode($binds)."\n\n";

		global $link;
		$stmt = $link->prepare( $query );
		
		if(count($binds) > 0) {
			$sss = "";
			for ($i = 0; $i < count($binds); $i++) {
				if(gettype($binds[$i]) == "integer") {
					$sss=$sss."i";
				} else if(gettype($binds[$i]) == "double") {
					$sss=$sss."d";
				} else {
					$sss=$sss."s";
				}
			}
			$stmt->bind_param( $sss, ...$binds );
		}
		
		$quickres = $stmt->execute();
		$result=$stmt->get_result();
		$num = $stmt->affected_rows;
		$insertid = mysqli_insert_id($link);
		$stmt->close();
		
		if($sql_array["action"] == "SELECT") {
			global $sqlonlyvalues;
			$array = array();
			if($sqlonlyvalues) {
				while ($row = mysqli_fetch_row($result))
				{
					$array[] = $row;
				}
			} else {
				while ($row = mysqli_fetch_assoc($result))
				{
					$array[] = $row;
				}
			}
			return $array;
		} else if($sql_array["action"] == "INSERT") {
			return $insertid;
		}
		return $quickres;
		
	}
	
	function sql_quick_query($query, ...$binds) {
		global $link;
		$stmt = $link->prepare( $query );

		if(count($binds) > 0) {
			$sss = "";
			for ($i = 0; $i < count($binds); $i++) {
				switch (gettype($binds[$i])) {
					case "integer":
					case "boolean":
						$sss=$sss."i";
						break;
					case "double":
						$sss=$sss."d";
						break;
					default:
						$sss=$sss."s";
						break;
				}
			}
			$stmt->bind_param( $sss, ...$binds );
		}
		
		$quickres = $stmt->execute();
		$result=$stmt->get_result();
		$num = $stmt->affected_rows;
		$insertid = mysqli_insert_id($link);
		$stmt->close();
		
		if(gettype($result) == "object") {
			global $sqlonlyvalues;
			$array = array();
			
			if($sqlonlyvalues) {
				while ($row = mysqli_fetch_row($result))
				{
					$array[] = $row;
				}
			} else {
				while ($row = mysqli_fetch_assoc($result))
				{
					$array[] = $row;
				}
			}
			$sqlonlyvalues = false;
			return $array;
		} else if($insertid != null) {
			return $insertid;
		}
		return $num;
	}
	
	function sql_only_values()
	{
		global $sqlonlyvalues;
		$sqlonlyvalues = true;
	}
?>