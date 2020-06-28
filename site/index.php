<?php
include('functions.php');
$res = '<head>
		<link rel="stylesheet" type="text/css" href="resources/styles.css?v=14" media="all">
		<link rel="stylesheet" type="text/css" href="resources/font.css?=1" media="all">
		<link rel="stylesheet" type="text/css" href="resources/font-awesome-4.6.3/css/font-awesome.min.css?=1" media="all">
		<meta name="description" content="'.$description.'">
		</head>';
$res .= '<title>'.$title.'</title>';
if (!userRule()) {
    $res .= '
    <head>
        <title>'.$title.'</title>
        <link rel="stylesheet" type="text/css" href="resources/styles.css?v=2.1" media="all">
    </head>
    <body>
		<center style="height:100%; display: flex; align-items: center;">
		 <div style="color: white; margin: auto; background: rgba(0,0,0,0.7); width: 100%; padding: 2%">
		  <h1>Пожалуйста, авторизируйтесь чтобы зайти на данную страницу.</h1>';
    if ($dle_session_fix) {
        $res .= '<br>
			  <form method="POST" action="/index.php">
				<input class="l_login" type="text" name="login_name" placeholder="Login"><input class="l_pass" type="password" name="login_password" placeholder="password"><button class="l_button" type="submit">Войти</button>
				<input name="login" type="hidden" id="login" value="submit">
				<input name="bpage" type="hidden" id="bpage" value="eq">

			  </form>';
    }
    $res .= '		</div>
				</center>
			</body>';
    echo $res;
    exit;
}
questsUpdate();
$res .= '<div class="topbar"><a href="/">'.$title.'</a>
		<table>
			<tbody>
				<tr>
					<td class="money">На Вашем счету: <b id="moneyCost">' . MoneyAmount() . '<span class="fa fa-ruble"></span> и ' . BonusMoneyAmount() . '<span class="fa fa-btc"></span></b></td>
				</tr>
			</tbody>
		</table>
</div>';
$q = getTop();
$i = 1;
$clC = true;
$res .= '<div class="qt"><div class="quests">
			<div class="centerQuests">
		' . questsDisplay() . '
			</div>
		<table width="100%" class="topTable">';
        $res .= '<tr class="tr2"><td>Позиция<td class="avatar"><td class="username">Ник игрока<td>Выполнено<br>квестов<td>Получено<br>бонусов</tr>';
        foreach ($q as $top) {
            if ($clC) {
                $cl = 'tr1';
                $clC = false;
            } else {
                $cl = 'tr2';
                $clC = true;
            }
            $res .= '<tr class="'.$cl.' position-'.$i.'"><td calss="position">'.$i.'<td class="avatar"><img src="avatar.php?size=25&user='.$top['name'].'"><td class="username">'.$top['name'].'<td>'.$top['num_q'].'<td>'.$top['num_money'].'<span class="fa fa-btc"></span></tr>';
            $i++;
        }
        $res .= '</table>
		</div></div>';

echo $res;
