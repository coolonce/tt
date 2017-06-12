<?php
require('connect.php');

$dateNow = date('Y-m-d');
$dataEarneds = $mysqli->query("SELECT * FROM users, earneds WHERE users.user_id = earneds.user_id ")->fetch_all(MYSQLI_ASSOC);  //Запрос на количество заработных денег, если не заработал, то не выведет
$userInfo = array();
foreach($dataEarneds as $val)
{	
	$userInfo[$val['user_id']]['hold'] = $val['hold_rule'];
	$userInfo[$val['user_id']]['allCash'] += $val['earned'];
	#$userInfo[$val['user_id']]['payDay']
	
	if($val['hold_rule'] == 1 ) 														// Считаем сколько должны выплатить к ближайшей дате оплаты
	{
		$datePayStart = date("Y-m-d",strtotime("-14 day", strtotime($dateNow)));
		$datePayEnd = date("Y-m-d",strtotime("-1 month", strtotime($dateNow)));
		
		if($val['date'] <=$datePayStart && $val['date'] > $datePayEnd)
		{
			$userInfo[$val['user_id']]['payDay'] += $val['earned'];
		}		
	}else if($val['hold_rule'] == 2){
		$datePayStart = date("Y-m-d",strtotime("-1 month", strtotime($dateNow)));
		$datePayEnd = date("Y-m-d",strtotime("-2 month", strtotime($dateNow)));
		
		if($val['date'] <= $datePayStart && $val['date'] > $datePayEnd)
		{
			$userInfo[$val['user_id']]['payDay'] += $val['earned'];
		}		
	}
	
}
$dataPayment = $mysqli->query("SELECT * FROM users, payments WHERE users.user_id = payments.user_id")->fetch_all(MYSQLI_ASSOC);
foreach($dataPayment as $val)
{	
	$userInfo[$val['user_id']]['allPayment'] += $val['paid_amount'];
}

for($i = 0; $i < 8; $i++) 																// Распределяем заработанные деньги на периоды 
{
	$dateStart = date("Y-m-d",strtotime((-14*$i)."day", strtotime($dateNow)));
	$dateEnd = date("Y-m-d",strtotime((-14 * ($i+1))." day", strtotime($dateNow)));
	#print_r($i." ".$dateStart." DateEnd: ". $dateEnd. "<br>");
	foreach($dataEarneds as $indx=> $val)
	{
		if( ($dateStart > $val['date']) && ($dateEnd <= $val['date']))
		{
			isset($val['earned']) ? $userInfo[$val['user_id']]['period'][$i] += $val['earned'] : $userInfo[$val['user_id']]['period'][$i] = 0;			
		}
	}
}
foreach($userInfo as $indx => $val)
{
	$userInfo[$indx]['balance'] = $userInfo[$indx]['allCash'] - $userInfo[$indx]['allPayment'];
}
$response = json_encode($userInfo);
print_r( $response);
return $response;

/*
$dateStart = date("Y-m-d",strtotime("-1 month", strtotime($dateNow)));
print_r("<br>".$dateStart);
print_r($userInfo);
*/



?>