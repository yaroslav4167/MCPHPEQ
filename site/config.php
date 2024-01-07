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
$db_login = 'login'; 		//Логин БД
$db_pass  = 'password'; //Пароль БД
$db_base  = 'database'; //Сама БД
$db_pref  = 'eq'; 			//Префикс таблиц ЕЗ

/*Подключение к движку*/
$db_user_table = 'dle_users';	//Таблица с пользователями
$db_user_name = 'name'; 			//Колонка с именем пользователя
$db_user_pass = 'password';		//Колонка с зашифрованным (или нет) паролем
$db_user_id = 'user_id';			//Колонка с паролем пользователя (либо с тем значением, которое необходимо проверять)
$db_user_uuid = 'uuid';				//Колонка с универсальным идентификатором пользователя
$db_user_assoc = $_SESSION['dle_user_id']; //Параметр, по которому происходит сверка
$dle_session_fix = true; 			//Фикс сессий (ТОЛЬКО ДЛЯ DLE!)

/*Настройка аватаров*/
$skins_path = '../lc/MinecraftSkins/'; // Путь к папке со скинами. Слеш в конце обязателен.
$default_skin = 'default.png'; 	// Имя файла дефолтного скина
$variable_size = true; 					// Возвращать ли разные размеры аватарок, в зависимости от размера скина?

/*Основная конфигурация*/
$server_array = array(
    # Массив путей к папкам серверов с статистикой игроков (Слэш в конце - обязателен!)
	# второй параметр означает, версия сервера 0 - <= 1.7, 1 - >= 1.8, 2 - >= 1.18
    1 => array('/home/user/HiTech/world/stats/', 2),
    2 => array('/home/user/SkyTech/world/stats/', 1),
    3 => array('/home/user/MagicRPG/world/stats/', 0));

/*Интеграция с WebAPI*/
$enableWEB_API = true;
$server_array_WAPI = array(
    1 => array('apiurl' => 'http://localhost:8080', 'key' => 'ADMIN'),
    2 => array('apiurl' => 'http://mysite.ru:8080', 'key' => 'ADMIN') );//Массив адресов, паролей для WEB-API. serverID - номер сервера в списке путей к папкам с статистикой

$questsLimit = 5;									//Кол-во заданий на день
$duplicateQuests = false;					//Могут ли выдаваться одинаковые задания?
$db_bonuce_table = 'dle_users';		//Таблица с бонус-валютой
$db_bonuce_column = 'bonusMoney';	//Колонка с бонусами
$db_real_column = 'money';				//Колонка с  реальной валютой
$db_assoc_bonuce = 'user_id';			//Ассоциация бонусов по данному параметру
$db_assoc_value = @$_SESSION['dle_user_id'];//Параметр, по которому происходит сверка
$title = 'LogicQuests';	//Что отображается в названии заголовка
$description = 'Ежедневыне квесты. Выполняй, и получай бонусы!'; //Описание для индексаторов
$quests = array(
	'q1' => array(
		'name' => 'Добыча леса',		 //Имя задания
		'description' => 'Добудь дерево',//Описание
		'ammount' => '30',				 //На сколько должно увеличиться значение
		'jsNameV1' => 'stat.mineBlock.17', //Имя в файле файла-профиля
		'jsNameV2' => 'stat.pickup.minecraft.log', //Имя в файле файла-профиля для версий >=1.8
		'jsNameV3' => 'stats.minecraft:mined.minecraft:oak_log', // Имя пути для версии >= 1.18 ([stats][minecraft:mined][minecraft:oak_log])
		'jsNameWAPI' => 'pickup.minecraft.log',
		'bonuceAmmount' => '10'),		 //Поощрение
	'q2' => array(
		'name' => 'Снова в шахту!',
		'description' => 'Добудь алмазы',
		'ammount' => '8',
		'jsNameV1' => 'stat.mineBlock.56',
		'jsNameV2' => 'stat.mineBlock.minecraft.diamond_ore',
		'jsNameV3' => 'stats.minecraft:mined.minecraft:diamond_ore',
		'jsNameWAPI' => 'mine_block.minecraft.diamond_ore',
		'bonuceAmmount' => '15'),
	'q3' => array(
		'name' => 'Истребляем нечисть!',
		'description' => 'Убей ифритов!',
		'ammount' => '15',
		'jsNameV1' => 'stat.killEntity.Blaze',
		'jsNameV2' => 'stat.killEntity.Blaze',
		'jsNameV3' => 'stats.minecraft:killed.minecraft:blaze',
		'jsNameWAPI' => 'kill_entity._blaze',
		'bonuceAmmount' => '18'),
	'q4' => array(
		'name' => 'Готовь оружие!',
		'description' => 'Убей нечисть!',
		'ammount' => '50',
		'jsNameV1' => 'stat.mobKills',
		'jsNameV2' => 'stat.mobKills',
		'jsNameV3' => 'stats.minecraft:custom.minecraft:mob_kills',
		'jsNameWAPI' => 'mob_kills',
		'bonuceAmmount' => '25'),
	/*'q5' => array(
		'name' => 'Рыбачим по-крупному',
		'description' => 'Вылови сокровища!',
		'ammount' => '1',
		'jsNameV1' => 'stat.treasureFished',
		'jsNameV2' => '',
		'bonuceAmmount' => '30'),
	'q6' => array(
		'name' => 'Мусорщик',
		'description' => 'Вылови мусор',
		'ammount' => '1',
		'jsNameV1' => 'stat.junkFished',
		'jsNameV2' => '',
		'bonuceAmmount' => '35'),*///Disable in >=1.11
	'q7' => array(
		'name' => 'Пора на рыбалку!',
		'description' => 'Налови рыбы',
		'ammount' => '15',
		'jsNameV1' => 'stat.fishCaught',
		'jsNameV2' => 'stat.fishCaught',
		'jsNameV3' => 'stats.minecraft:custom.minecraft:fish_caught',
		'jsNameWAPI' => 'fish_caught',
		'bonuceAmmount' => '25'),
	'q8' => array(
		'name' => 'Потомство :3',
		'description' => 'Размножь животных',
		'ammount' => '3',
		'jsNameV1' => 'stat.animalsBred',
		'jsNameV2' => 'stat.animalsBred',
		'jsNameV3' => 'stats.minecraft:custom.minecraft:animals_bred',
		'jsNameWAPI' => 'animals_bred',
		'bonuceAmmount' => '15'),
	'q9' => array(
		'name' => 'С удовольствием',
		'description' => 'Проведи время в игре (сек.)',
		'ammount' => '900',
		'jsNameV1' => 'stat.playOneMinute',
		'jsNameV2' => 'stat.playOneMinute',
		'jsNameV3' => 'stats.minecraft:custom.minecraft:play_time',
		'jsNameWAPI' => 'play_one_minute',
		'bonuceAmmount' => '18'),
	'q10' => array(
		'name' => 'Верхом',
		'description' => 'Прокатись на лошади (см)',
		'ammount' => '20000',
		'jsNameV1' => 'stat.horseOneCm',
		'jsNameV2' => 'stat.horseOneCm',
		'jsNameV3' => 'stats.minecraft:custom.minecraft:horse_one_cm',
		'jsNameWAPI' => 'horse_one_cm',
		'bonuceAmmount' => '25'),
	'q11' => array(
		'name' => 'Верхом?!?',
		'description' => 'Прокатись на свинье (см)',
		'ammount' => '20000',
		'jsNameV1' => 'stat.pigOneCm',
		'jsNameV2' => 'stat.pigOneCm',
		'jsNameV3' => 'stats.minecraft:custom.minecraft:pig_one_cm',
		'jsNameWAPI' => 'pig_one_cm',
		'bonuceAmmount' => '25'),
	'q12' => array(
		'name' => 'Твой хлеб',
		'description' => 'Испеки хлеб',
		'ammount' => '5',
		'jsNameV1' => 'stat.craftItem.297',
		'jsNameV2' => 'stat.craftItem.minecraft.bread',
		'jsNameV3' => 'stats.minecraft:crafted.minecraft:bread',
		'jsNameWAPI' => 'craft_item.minecraft.bread',
		'bonuceAmmount' => '10'),
	'q13' => array(
		'name' => 'Синенький ;3',
		'description' => 'Добудь лазурит',
		'ammount' => '10',
		'jsNameV1' => 'stat.mineBlock.21',
		'jsNameV2' => 'stat.mineBlock.minecraft.lapis_ore',
		'jsNameV3' => 'stats.minecraft:mined.minecraft:lapis_ore',
		'jsNameWAPI' => 'mine_block.minecraft.lapis_ore',
		'bonuceAmmount' => '18'),
	'q14' => array(
		'name' => 'Тяжелый металл',
		'description' => 'Добудь железо',
		'ammount' => '15',
		'jsNameV1' => 'stat.mineBlock.15',
		'jsNameV2' => 'stat.mineBlock.minecraft.iron_ore',
		'jsNameV3' => 'stats.minecraft:mined.minecraft:iron_ore',
		'jsNameWAPI' => 'mine_block.minecraft.iron_ore',
		'bonuceAmmount' => '12'),
	'q15' => array(
		'name' => 'Ghostbusters!',
		'description' => 'Уничтожь гаста',
		'ammount' => '1',
		'jsNameV1' => 'stat.killEntity.Ghast',
		'jsNameV2' => 'stat.killEntity.Ghast',
		'jsNameV3' => 'stats.minecraft:killed.minecraft:ghast',
		'jsNameWAPI' => 'kill_entity._ghast',
		'bonuceAmmount' => '25'),
	'q16' => array(
		'name' => 'С Днём!...',
		'description' => 'Испеки торт',
		'ammount' => '1',
		'jsNameV1' => 'stat.craftItem.354',
		'jsNameV2' => 'stat.craftItem.minecraft.cake',
		'jsNameV3' => 'stats.minecraft:crafted.minecraft:cake',
		'jsNameWAPI' => 'craft_item.minecraft.cake',
		'bonuceAmmount' => '10'),
	'q17' => array(
		'name' => 'Каждый день - праздник!',
		'description' => 'Испеки торты',
		'ammount' => '3',
		'jsNameV1' => 'stat.craftItem.354',
		'jsNameV2' => 'stat.craftItem.minecraft.cake',
		'jsNameV3' => 'stats.minecraft:crafted.minecraft:cake',
		'jsNameWAPI' => 'craft_item.minecraft.cake',
		'bonuceAmmount' => '20'),
	'q18' => array(
		'name' => 'Бахча',
		'description' => 'Вырасти и собери арбузы',
		'ammount' => '10',
		'jsNameV1' => 'stat.mineBlock.103',
		'jsNameV2' => 'stat.mineBlock.minecraft.melon_block',
		'jsNameV3' => 'stats.minecraft:mined.minecraft:melon',
		'jsNameWAPI' => 'mine_block.minecraft.melon_block',
		'bonuceAmmount' => '10'),
	'q19' => array(
		'name' => 'Контр террорист',
		'description' => 'Убей крипперов',
		'ammount' => '10',
		'jsNameV1' => 'stat.killEntity.Creeper',
		'jsNameV2' => 'stat.killEntity.Creeper',
		'jsNameV3' => 'stats.minecraft:killed.minecraft:creeper',
		'jsNameWAPI' => 'kill_entity._creeper',
		'bonuceAmmount' => '16'),
	'q20' => array(
		'name' => '!ызамлА',
		'description' => 'Добудь алмазы',
		'ammount' => '25',
		'jsNameV1' => 'stat.mineBlock.56',
		'jsNameV2' => 'stat.mineBlock.minecraft.diamond_ore',
		'jsNameV3' => 'stats.minecraft:mined.minecraft:diamond_ore',
		'jsNameWAPI' => 'mine_block.minecraft.diamond_ore',
		'bonuceAmmount' => '25')
	/*'q' => array(
		'name' => '',
		'description' => '',
		'ammount' => '',
		'jsNameV1' => '',
		'jsNameV2' => '',
		'jsNameV3' => '',
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;";//Стандартный запрос к БД (Выполняется только один раз)
