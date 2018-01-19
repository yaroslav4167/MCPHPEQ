<?php
session_start();//Стартуем сессию
include('config.php');
/*Подключение базы данных*/
$mysqli = mysqli_connect($db_host, $db_login, $db_pass, $db_base);
mysqli_set_charset($mysqli, "utf8");
	if (!file_exists(__DIR__ . "/cache/db.txt")) {
		mysqli_multi_query($mysqli, $MyQuery) or die("Ошибка MySQL: " . mysql_error());
		$file = fopen(__DIR__ . "/cache/db.txt", 'w');
		fclose($file);
	}
/*=======================*/

/*Кэш MySQL
if($db_user_assoc != ''){
	if(@$usercache == ''){
		$q = "SELECT * FROM `".$db_user_table."` WHERE `".$db_user_id."`=".$db_user_assoc."";
		$query = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);
		$res = mysqli_fetch_array($query);
		$usercache = $res;
	}
}
=========*/

if(@$_GET['pickq'] != '') {getBonuces($_GET['pickq']);}

function userRule() {//Проверка прав доступа
	global $dle_session_fix, $db_user_table, $db_user_name, $db_user_id, $db_user_assoc, $mysqli;
	if($db_user_assoc != ''){
		return true;
	}
	if($dle_session_fix == true) {
		if(dleFixCookie()){
			return true;
		}

	}
	return false;
}
function dleFixCookie() {//Фикс недействительной сессии при длительном нахождении на странице.
	global $mysqli, $db_user_pass, $db_user_table, $db_user_id;
	if($_COOKIE['dle_user_id'] != '' && $_COOKIE['dle_password'] != ''){
		$id = mysqli_real_escape_string($mysqli, $_COOKIE['dle_user_id']);
		$pass = mysqli_real_escape_string($mysqli, $_COOKIE['dle_password']);
		$q = "SELECT `".$db_user_pass."` FROM `".$db_user_table."` WHERE `".$db_user_id."`=".$id."";
		$query = mysqli_query($mysqli, $q);
		$res = mysqli_fetch_array($query);
		if($res[$db_user_pass] == md5($pass)){
			$_SESSION['dle_user_id'] = $id;//DLE FIX AUTHORIZATION
			mysqli_free_result($query);
			return true;
		} else {
			error_log("Замечена попытка подмены данных! id:" . $_COOKIE['dle_user_id'] . " pass:" . $_COOKIE['dle_password'], 0);
			return false;
		}
	}
	return false;
}
function getUUID($username){//Получение UUID пользователя
	global $mysqli, $db_user_table, $db_user_name, $db_user_uuid;
	$username = mysqli_real_escape_string($mysqli, htmlspecialchars($username));
	$q = "SELECT `".$db_user_uuid."` FROM `".$db_user_table."` WHERE `".$db_user_name."` = '".$username."'";
	$query = mysqli_query($mysqli, $q);
	$res = mysqli_fetch_array($query);
	//$result = $res[$db_user_uuid];
	mysqli_free_result($query);
	return $res[$db_user_uuid];
}
function getUserID(){
	global $dle_session_fix, $db_user_assoc;
	if($db_user_assoc != ''){
		return $db_user_assoc;
	} else {
		if($dle_session_fix && dleFixCookie()){
			if($_COOKIE['dle_user_id'] != '')	
				return (int)$_COOKIE['dle_user_id'];
			
			return;
		} else {
			return;
		}
	}
}
function MoneyAmount() { //Реальная валюта пользователя
	global $mysqli, $db_bonuce_table, $db_real_column, $db_user_id;
	$id = getUserID();
	$query = mysqli_query($mysqli, "SELECT * FROM `".$db_bonuce_table."` WHERE `".$db_user_id."`='".$id."' LIMIT 1");
	$result = mysqli_fetch_array($query);
	mysqli_free_result($query);
	return round($result[$db_real_column], 2);
}
function BonusMoneyAmount() { //Бонус-валюта пользователя
	global $mysqli, $db_bonuce_table, $db_bonuce_column, $db_user_id;
	$id = getUserID();
	$query = mysqli_query($mysqli, "SELECT * FROM `".$db_bonuce_table."` WHERE `".$db_user_id."`='".$id."' LIMIT 1");
	$result = mysqli_fetch_array($query);
	mysqli_free_result($query);
	return round($result[$db_bonuce_column], 2);
}
function getUsername(){//Получение никнейма пользователя
	global $db_user_table, $db_user_name, $db_user_id, $db_user_assoc, $mysqli, $dle_session_fix;
	$assoc = getUserID();
	$q = "SELECT * FROM ".$db_user_table." WHERE ".$db_user_id." = '".$assoc."' LIMIT 1";
	$query = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);
	$res = mysqli_fetch_array($query);
	mysqli_free_result($query);
	return $res[$db_user_name];
}
function getStat($stat, $username){//Получение одного параметра со всех серверов (сервер1 + сервер2 ...)
	global $server_array;
	$uuid = getUUID($username);
	$result = 0;
	foreach ($server_array as $dirStat) {
		$f = $dirStat . $uuid . '.json';
		if(file_exists($f)){
			$fileContent = file_get_contents($f);
			$js = json_decode($fileContent, true);
			if(isset($js[$stat])) {
				if(strpos($stat, 'mineBlock') !== false && isset($js[str_replace('mineBlock', 'useItem', $stat)])) {
					$result += $js[$stat] - $js[str_replace('mineBlock', 'useItem', $stat)];//Разница между поставленными и добытыми блоками (AntiCheat)
				} else {
					$result += $js[$stat];
				}
			}
		}
	}
	return $result;
}
function questsDisplay() {
	global $quests, $mysqli, $db_pref;
	$username = getUsername();
	$result = '';
	$i = 0;
	$q = "SELECT * FROM `".$db_pref."_everydayQuests` WHERE `user` = '".$username."'";
	$query = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);
	while($res = mysqli_fetch_array($query)) {
		$js = unserialize($res['quest']);
		$statV = getStat($js['jsName'], $username);
		$ammount = $statV - $res['value'];
		if($ammount >= $res['ammount']){
			$ammount = $res['ammount'];
			$off = 'enable';
		} else {
			$off = 'disabled';
		}
		$i++;
		$result .= '<div class="questBlock" id="quest-'.$i.'"><table>' .
		'<tr><td><center><b>' . $js['name'] . '</b></center></td></tr>' .
		'<tr><td>' . $js['description'] . '</td></tr>' .
		'<tr><td>Награда: <b>' . $js['bonuceAmmount'] . '<span class="fa fa-btc"></span></b></td></tr>' .
		'<tr><td><center>' . $ammount . ' / ' . $res['ammount'] . '</center></td></tr>' .
		//'<tr><td><center><a href="index.php?pickq='.$res['id'].'"><button '.$off.'>Забрать награду</button></a></center></td></tr>' .
		'</table>' .
    '<a href="index.php?pickq='.$res['id'].'"><button '.$off.' class="qButton q'.$off.'"></button></a></div>';
	}
	mysqli_free_result($query);
	return $result;
}
function getTop(){//Получение массива топ-игроков
	global $mysqli, $db_pref;
	$q = "SELECT * FROM `".$db_pref."_top` ORDER BY `num_q` DESC LIMIT 0,10";
	$query = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q . '<br>' . mysqli_error());
	while($res = mysqli_fetch_array($query)) {//Добавляем всё к одному массиву
		$resS[] = $res;
	}
	return $resS;
}
function getBonuces($id) {//Сдача квеста/получение бонусов
	if(!userRule())
		return;
	
	global $mysqli, $db_pref, $db_bonuce_column, $db_bonuce_table, $db_assoc_bonuce;
	
	$id = mysqli_real_escape_string($mysqli, htmlspecialchars($id));

	$username = getUsername();
	$userid = getUserID();
	$q = "SELECT * FROM `".$db_pref."_everydayQuests` WHERE `id` = '".$id."'";
	$query = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);
	$res = mysqli_fetch_array($query);
	$today = date("d.m.Y");
	$js = unserialize($res['quest']);
	$statV = getStat($js['jsName'], $username);
	$ammount = $statV - $res['value'];
	if($ammount >= $res['ammount'] && isset($res['ammount'])){
		$q = "INSERT INTO `".$db_pref."_everydayQuests_history`(`user`, `info`, `date`) VALUES ('".$username."','".$res['quest']."','".$today."')";
		mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);

		$q = "DELETE FROM `".$db_pref."_everydayQuests` WHERE `id`=".$id.";";
		mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q . '<br>' . mysql_error());
		$q = "UPDATE `".$db_bonuce_table."` SET `".$db_bonuce_column."`=`".$db_bonuce_column."`+".$js['bonuceAmmount']." WHERE `".$db_assoc_bonuce."` = ".$userid;
		mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q . '<br>' . mysql_error());

		$q = "SELECT * FROM `".$db_pref."_top` WHERE `name` = '".$username."'";
		$sql = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q . '<br>' . mysql_error());
		$val = mysqli_num_rows($sql);
		if($val == 0) {//Запись в ТОП!
			$q = "INSERT INTO `".$db_pref."_top`(`name`, `num_q`, `num_money`) VALUES ('".$username."', 1, ".$js['bonuceAmmount'].")";
		} else {
			$q = "UPDATE `".$db_pref."_top` SET `num_q`=`num_q`+1, `num_money`=`num_money`+".$js['bonuceAmmount']." WHERE `name` = '".$username."'";
		}
		mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q . '<br>' . mysql_error());
	}
	mysqli_free_result($query);
}
function questsUpdate() {
	global $quests, $dle_session_fix, $mysqli, $questsLimit, $db_pref;
	$today = date("d.m.Y");
	shuffle($quests);//Перемешиваем массив
	$t = 0;
	$id = getUserID();
	$username = getUsername();
	$q = "SELECT * FROM `".$db_pref."_everydayQuests_history` WHERE `user` = '".$username."' AND `date` = '".$today."'";
	$qL = "SELECT * FROM `".$db_pref."_everydayQuests` WHERE `user` = '".$username."'";
	$query = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);
	$queryL = mysqli_query($mysqli, $qL)or DIE('Ашипка MySQL<br>' . $qL);
	$numQ = mysqli_num_rows($query);
	$numL = mysqli_num_rows($queryL);
	$numR = $numQ + $numL;
	mysqli_free_result($query);
	mysqli_free_result($queryL);
	if($numR < $questsLimit) {
		$q = "SELECT * FROM `".$db_pref."_everydayQuests` WHERE `user_id` = '".$id."'";
		$query = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);
		$resS = array();
		while($res = mysqli_fetch_array($query)) {//Кешируем вывод БД (Для исключения повторений)
			$resS[] = $res;
		}
		for ($i=$numR; $i <= $questsLimit; $i++) { 
			$t++;
			foreach ($quests as $questsvalue) {	
				$t++;
				$coincidence = false;
				foreach($resS as $resvalue) {
					$t++;
					if(stristr($resvalue['quest'], $questsvalue['jsName']) != ''){
						$coincidence = true;
						break;
					}
				}
				if(!$coincidence && $numR < $questsLimit){
					$numR++;
					$q = "INSERT INTO `".$db_pref."_everydayQuests`(`user`, `user_id`, `uuid`, `quest`, `ammount`, `value`) VALUES ('".$username."','".$id."','".getUUID($username)."','".serialize($questsvalue)."',".$questsvalue['ammount'].",".getStat($questsvalue['jsName'], $username).")";
					mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);
				}
			}
		}
	}
}
?>
