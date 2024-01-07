<?php
session_start();//Стартуем сессию
//die('Данный модуль временно отключён!');
include('config.php');
/*Подключение базы данных*/
$mysqli = mysqli_connect($db_host, $db_login, $db_pass, $db_base);
mysqli_set_charset($mysqli, "utf8");
	if (!file_exists(__DIR__ . "/cache/db.txt")) {
		mysqli_multi_query($mysqli, $MyQuery) or die("Ошибка MySQL: " . mysqli_error($mysqli));
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
$cached_stats = array();

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
function dleFixCookie() {//Фикс недействительной сессии при длительном нахождении на странице. (Не работает на новых версиях)
	if(@$_COOKIE['dle_user_id'] != '' && @$_COOKIE['dle_password'] != ''){
		
		if(@$_SESSION['dle_password'] == $_COOKIE['dle_password']){
			//$_SESSION['dle_user_id'] = $_COOKIE['dle_user_id'];//DLE FIX AUTHORIZATION
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
	$username = mysqli_real_escape_string($mysqli, $username);
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
			if(@$_SESSION['dle_user_id'] != '')	
				return (int)$_SESSION['dle_user_id'];
			
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
function getUsername($uuid = ''){//Получение никнейма пользователя
	global $db_user_table, $db_user_name, $db_user_id, $db_user_uuid, $db_user_assoc, $mysqli, $dle_session_fix;
	if($uuid == '') {
		$assoc = getUserID();
		$q = "SELECT * FROM ".$db_user_table." WHERE ".$db_user_id." = '".$assoc."' LIMIT 1";
		$query = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);
		$res = mysqli_fetch_array($query);
		mysqli_free_result($query);
		return $res[$db_user_name];
	} else {
		$q = "SELECT * FROM ".$db_user_table." WHERE ".$db_user_uuid." = '".$uuid."' LIMIT 1";
		$query = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);
		$res = mysqli_fetch_array($query);
		mysqli_free_result($query);
		return $res[$db_user_name];
	}
}
function getStat($statKEY, $username){//Получение одного параметра со всех серверов (сервер1 + сервер2 ...)
	global $server_array, $enableWEB_API, $server_array_WAPI, $quests;

    if(!isset($statKEY))
        return 0;

    $uuid = getUUID($username);
	$result = 0;

//     echo $statKEY;
//     if(!array_key_exists($statKEY, $quests)) {
//         $statKEY = array_keys($quests)[$statKEY];
//     }
//     echo " {$statKEY};";

	$userOnlineServers = array();
	if($enableWEB_API) {//Работаем с WEB-API	
		foreach($server_array_WAPI as $key => $serverWAPI) {
			$url = $serverWAPI['apiurl'] . 'player/' . $uuid . '?key=' . $serverWAPI['key'];
			$userStat = file_get_contents($url);
			if(!empty($userStat)){
				$userJsonStat = json_decode($userStat, true);
				if(array_key_exists('online', $userJsonStat) && ($userJsonStat['online'] == true)){
					if(array_key_exists('statistics', $userJsonStat)){
						$userOnlineServers[] = $key;
						$webAPIStat = $quests[$statKEY]['jsNameWAPI'];//Т.к. в WEB-API другие названия переменных, ищем в конфигурации правильные.
						//$result += $userJsonStat['statistics'][$webAPIStat];
						foreach($userJsonStat['statistics'] as $stat) {
							if($stat['stat'] == $webAPIStat) 
								$result += $stat['value'];
						}
					}
				}
			}
		}
	}

	//Кеширование всех файлов со статистикой из серверов
	global $cached_stats;
    if(!is_array($cached_stats))
        $cached_stats = array();

	if (count($cached_stats) == 0) {
		foreach ($server_array as $key => $dirStat) {
			if(!in_array($key, $userOnlineServers)){
				$f = $dirStat[0] . $uuid . '.json';
				if(file_exists($f) || UR_exists($f)){ //Путь это файл или URL? Существует ли нужный нам файл?
					$fileContent = file_get_contents($f);
					$js = json_decode($fileContent, true);
					$cached_stats[] = $js;
				}
			}
		}
	}
	
	foreach ($server_array as $key => $dirStat) {
        foreach ($cached_stats as $server_stat) {
			switch($dirStat[1]) {//Поддержка старых верий статистики
				case 0:
					$stat = $quests[$statKEY]['jsNameV1'];
					break;
				case 1:
					$stat = $quests[$statKEY]['jsNameV2'];
					break;
				case 2:
					$stat = $quests[$statKEY]['jsNameV3'];
					break;
			}
            if(isset($server_stat[$stat])) {
                $result += $server_stat[$stat];
            } else if ($dirStat[1] >= 2) {
				// Формируем адрес из a.b.c -> [a][b][c] (1.18+)
				$full_addr = explode(".", $stat);
				foreach ($full_addr as $segment_addr) {
					 if(isset($server_stat[$segment_addr])) {
						 $server_stat = $server_stat[$segment_addr];
					 } else {
						 break;
					 }
				}
				if (is_int($server_stat)) {
					$result += $server_stat;
				}
			}
        }
    }
	/*foreach ($server_array as $key => $dirStat) {
		if(!in_array($key, $userOnlineServers)){
			$f = $dirStat[0] . $uuid . '.json';
			if(file_exists($f) || UR_exists($f)){ //Путь это файл или URL? Существует ли нужный нам файл?
				$fileContent = file_get_contents($f);
				$js = json_decode($fileContent, true);
				$stat = $dirStat[1]?$quests[$statKEY]['jsNameV2']:$quests[$statKEY]['jsNameV1'];//Поддержка старых верий статистики
				if(isset($js[$stat])) {
					$result += $js[$stat];
				}
			}
		}
	}*/
	return $result;
}

function UR_exists($url){
   if (filter_var($url, FILTER_VALIDATE_URL)) {
      $headers=get_headers($url);
      return stripos($headers[0],"200 OK")?true:false;
   }
   return false;
}

function questsDisplay() {
	global $quests, $mysqli, $db_pref;
	$username = getUsername();
	$uuid = getUUID($username);
	$result = '';
	$i = 0;
	$q = "SELECT * FROM `".$db_pref."_everydayQuests` WHERE `uuid` = '".$uuid."'";
	$query = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);
	while($res = mysqli_fetch_array($query)) {
		$js = unserialize($res['quest']);
		$statV = getStat($js['qkey'], $username);
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
	$query = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q . '<br>' . mysqli_error($mysqli));
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
	$uuid = getUUID($username);
	$userid = getUserID();
	$q = "SELECT * FROM `".$db_pref."_everydayQuests` WHERE `id` = '".$id."'";
	$query = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);
	$res = mysqli_fetch_array($query);
    if(count((array)$res) <= 0)
        return;

	$today = date("d.m.Y");
	$js = unserialize($res['quest']);
	$statV = getStat($js['qkey'], $username);
	$ammount = $statV - $res['value'];
	if($ammount >= $res['ammount'] && isset($res['ammount'])){
		$q = "INSERT INTO `".$db_pref."_everydayQuests_history`(`uuid`, `info`, `date`) VALUES ('".$uuid."','".$res['quest']."','".$today."')";
		mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);

		$q = "DELETE FROM `".$db_pref."_everydayQuests` WHERE `id`=".$id.";";
		mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q . '<br>' . mysqli_error($mysqli));
		$q = "UPDATE `".$db_bonuce_table."` SET `".$db_bonuce_column."`=`".$db_bonuce_column."`+".$js['bonuceAmmount']." WHERE `".$db_assoc_bonuce."` = ".$userid;
		mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q . '<br>' . mysqli_error($mysqli));
		$q = "SELECT * FROM `".$db_pref."_top` WHERE `uuid` = '".$uuid."'";
		$sql = mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q . '<br>' . mysqli_error($mysqli));
		$val = mysqli_num_rows($sql);
		if($val == 0) {//Запись в ТОП!
			$q = "INSERT INTO `".$db_pref."_top`(`uuid`, `num_q`, `num_money`) VALUES ('".$uuid."', 1, ".$js['bonuceAmmount'].")";
		} else {
			$q = "UPDATE `".$db_pref."_top` SET `num_q`=`num_q`+1, `num_money`=`num_money`+".$js['bonuceAmmount']." WHERE `uuid` = '".$uuid."'";
		}
		mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q . '<br>' . mysqli_error($mysqli));
	}
	mysqli_free_result($query);
}
function questsUpdate() {
	global $quests, $dle_session_fix, $mysqli, $questsLimit, $db_pref;
	$today = date("d.m.Y");
	$arr_keys = array_keys($quests); //Получаем имена (Ключи) массивов в массиве
	shuffle($arr_keys);//Перемешиваем массив
	$t = 0;
	$id = getUserID();
	$username = getUsername();
	$uuid = getUUID($username);
	$q = "SELECT * FROM `".$db_pref."_everydayQuests_history` WHERE `uuid` = '".$uuid."' AND `date` = '".$today."'";
	$qL = "SELECT * FROM `".$db_pref."_everydayQuests` WHERE `uuid` = '".$uuid."'";
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
			foreach ($arr_keys as $key => $array_key) {
				$t++;
				$coincidence = false;
				foreach($resS as $resvalue) {
					$t++;
					if(stristr($resvalue['quest'], $key) != ''){
						$coincidence = true;
						break;
					}
				}
				if(!$coincidence && $numR < $questsLimit){
					$numR++;
					$quests[$array_key]['qkey'] = $array_key;
					$q = "INSERT INTO `".$db_pref."_everydayQuests`(`user`, `user_id`, `uuid`, `quest`, `ammount`, `value`) VALUES ('".$username."','".$id."','".$uuid."','".serialize($quests[$array_key])."',".$quests[$array_key]['ammount'].",".getStat($array_key, $username).")";
					mysqli_query($mysqli, $q)or DIE('Ашипка MySQL<br>' . $q);
				}
			}
		}
	}
}
?>