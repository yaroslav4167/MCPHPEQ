<?php
/*
=================================
=Конфигурация ежедневных заданий=
======Copyright Yaroslavik=======
=========КостыльGaming===========
=================================
*/

/*Настройка подключения к базе данных*/
$db_host  = 'localhost'; //Сервер БД
$db_login = 'login'; //Логин БД
$db_pass  = 'password'; //Пароль БД
$db_base  = 'database'; //Сама БД
$db_pref  = 'eq'; //Префикс таблиц ЕЗ

/*Подключение к движку*/
$db_user_table = 'dle_users';	//Таблица с пользователями
$db_user_name = 'name'; 		//Колонка с именем пользователя
$db_user_pass = 'password';		//Колонка с зашированным (или нет) паролем
$db_user_id = 'user_id';		//Колонка с паролем пользователя (либо с тем значением, которое необходимо проверять)
$db_user_uuid = 'uuid';			//Колонка с универсальным индификатором пользователя
$db_user_assoc = $_SESSION['dle_user_id']; //Параметер, по которому происходит сверка
$dle_session_fix = true; 		//Фикс сессий (ТОЛЬКО ДЛЯ DLE!)

/*Настройка аватаров*/
$skins_path = '../lc/MinecraftSkins/'; // Путь к папке со скинами. Слеш в конце обязателен.
$default_skin = 'default.png'; // Имя файла дефолтного скина
$variable_size = true; // Возвращать ли разные размеры аватарок, в зависимости от размера скина?

/*Основная конфигурация*/
$server_array = array(
	1 => '/home/Public/HiTech/world/stats/', 
	2 => '/home/Public/SkyTech/world/stats/',
	3 => '/home/Public/MagicRPG/world/stats/' );//Массив путей к папкам серверов с статистикой игроков (Слэш в конце - обязателен!)
$questsLimit = 5;					//Кол-во заданий на день
$duplicateQuests = false;			//Могут ли выдаваться одинаковые задания?
$db_bonuce_table = 'dle_users';		//Таблица с бонус-валютой
$db_bonuce_column = 'bonusMoney';	//Колонка с бонусами
$db_real_column = 'money';			//Колонка с  реальной валютой
$db_assoc_bonuce = 'user_id';		//Ассоциация бонусов по данному параметру
$db_assoc_value = @$_SESSION['dle_user_id'];//Параметер, по которому происходит сверка
$title = 'LogicQuests';//Что отображаеться в названии заголовка
$description = 'Ежедневыне квесты. Выполняй, и получай бонусы!'; //Описание для индексаторов
$quests = array(
	'q1' => array(
		'name' => 'Добыча леса',		 //Имя задания
		'description' => 'Добудь дерево',//Описание
		'ammount' => '30',				 //На сколько должно увеличиться значение
		'jsName' => 'stat.mineBlock.17', //Имя в файле файла-профиля
		'bonuceAmmount' => '10'),		 //Поощрение
	'q2' => array(
		'name' => 'Снова в шахту!',
		'description' => 'Добудь алмазы',
		'ammount' => '8',
		'jsName' => 'stat.mineBlock.56',
		'bonuceAmmount' => '15'),
	'q3' => array(
		'name' => 'Истребляем нечесть!',
		'description' => 'Убей ифритов!',
		'ammount' => '15',
		'jsName' => 'stat.killEntity.Blaze',
		'bonuceAmmount' => '18'),
	'q4' => array(
		'name' => 'Готовь оружие!',
		'description' => 'Убей нечесть!',
		'ammount' => '50',
		'jsName' => 'stat.mobKills',
		'bonuceAmmount' => '25'),
	'q5' => array(
		'name' => 'Рыбачим по-крупному',
		'description' => 'Вылови сокровища!',
		'ammount' => '1',
		'jsName' => 'stat.treasureFished',
		'bonuceAmmount' => '30'),
	'q6' => array(
		'name' => 'Мусорщик',
		'description' => 'Вылови мусор',
		'ammount' => '1',
		'jsName' => 'stat.junkFished',
		'bonuceAmmount' => '35'),
	'q7' => array(
		'name' => 'Пора на рыбалку!',
		'description' => 'Налови рыбы',
		'ammount' => '15',
		'jsName' => 'stat.fishCaught',
		'bonuceAmmount' => '25'),
	'q8' => array(
		'name' => 'Потомство :3',
		'description' => 'Размножь животных',
		'ammount' => '3',
		'jsName' => 'stat.animalsBred',
		'bonuceAmmount' => '15'),
	'q9' => array(
		'name' => 'С удовольствием',
		'description' => 'Проведи время в игре (сек.)',
		'ammount' => '900',
		'jsName' => 'stat.playOneMinute',
		'bonuceAmmount' => '18'),
	'q10' => array(
		'name' => 'Верхом',
		'description' => 'Прокатись на лошади (см)',
		'ammount' => '20000',
		'jsName' => 'stat.horseOneCm',
		'bonuceAmmount' => '25'),
	'q11' => array(
		'name' => 'Верхом?!?',
		'description' => 'Прокатись на свинье (см)',
		'ammount' => '20000',
		'jsName' => 'stat.pigOneCm',
		'bonuceAmmount' => '25'),
	'q12' => array(
		'name' => 'Твой хлеб',
		'description' => 'Испеки хлеб',
		'ammount' => '5',
		'jsName' => 'stat.craftItem.297',
		'bonuceAmmount' => '10'),
  'q13' => array(
		'name' => 'Синенький ;3',
		'description' => 'Добудь лазурит',
		'ammount' => '10',
		'jsName' => 'stat.mineBlock.21',
		'bonuceAmmount' => '18'),
  'q14' => array(
		'name' => 'Тяжелый металл',
		'description' => 'Добудь железо',
		'ammount' => '15',
		'jsName' => 'stat.mineBlock.15',
		'bonuceAmmount' => '12'),
  'q15' => array(
		'name' => 'Ghostbusters!',
		'description' => 'Уничтожь гаста',
		'ammount' => '1',
		'jsName' => 'stat.killEntity.Ghast',
		'bonuceAmmount' => '25'),
  'q16' => array(
		'name' => 'С Днём!...',
		'description' => 'Испеки торт',
		'ammount' => '1',
		'jsName' => 'stat.craftItem.354',
		'bonuceAmmount' => '10'),
  'q17' => array(
		'name' => 'Каждый день - праздник!',
		'description' => 'Испеки торты',
		'ammount' => '3',
		'jsName' => 'stat.craftItem.354',
		'bonuceAmmount' => '20'),
  'q18' => array(
		'name' => 'Бахча',
		'description' => 'Вырасти и собери арбузы',
		'ammount' => '10',
		'jsName' => 'stat.mineBlock.103',
		'bonuceAmmount' => '10'),
  'q19' => array(
		'name' => 'Контр-террорист',
		'description' => 'Убей крипперов',
		'ammount' => '10',
		'jsName' => 'stat.killEntity.Creeper',
		'bonuceAmmount' => '16'),
  'q20' => array(
		'name' => '!ызамлА',
		'description' => 'Добудь алмазы',
		'ammount' => '25',
		'jsName' => 'stat.mineBlock.56',
		'bonuceAmmount' => '25')
	/*'q' => array(
		'name' => '',
		'description' => '',
		'ammount' => '',
		'jsName' => '',
		'bonuceAmmount' => '')*/
	);
/*Дополнительно*/
$MyQuery = "
CREATE TABLE IF NOT EXISTS `".$db_pref."_everydayQuests` (
  `id` int(14) NOT NULL AUTO_INCREMENT,
  `user` VARCHAR(65) NOT NULL,
  `user_id` INT NOT NULL DEFAULT '0',
  `uuid` VARCHAR(65) NOT NULL,
  `quest` text NOT NULL,
  `ammount` INT NOT NULL DEFAULT '0',
  `value` INT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `".$db_pref."_everydayQuests_history` (
  `id` int(14) NOT NULL AUTO_INCREMENT,
  `user` VARCHAR(65) NOT NULL,
  `info` text NOT NULL,
  `date` VARCHAR(65) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `".$db_pref."_top` (
  `id` int(11) NOT NULL,
  `name` varchar(35) CHARACTER SET utf8 NOT NULL,
  `num_q` int(12) NOT NULL,
  `num_money` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;";//Стандартный запрос к БД (Выполняеться только один раз)
?>