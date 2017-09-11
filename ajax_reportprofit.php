<?php
error_reporting('E_ALL');
// подключение path
require_once $_SERVER['DOCUMENT_ROOT'].'/administrator/zinit.php';

$torder = NEW TZOrder();

//$pt = json_decode($_POST['pt'],true);

$id_customer = (int)$_POST['id_customer'];

$data = array();
if($id_customer) {
    $data['id_customer'] = $id_customer;
}

$parr = $torder->getProfitReport($data);

//print_r($parr);

$out = '';

$out .='<table id="table_order" class="w100">';

$out .='<tr><th>№</th><th>Дата</th><th>Покупатель</th><th>План</th><th>Факт</th><th>Наценка</th><th>Оплата</th><th>Долг</th><th>Ответственный</th></tr>';

foreach($parr AS $row) {

    $plan = $row['sumplanprice'];
    $fact = $row['sumprice'];
    $pay = $row['sumpay'];

    $out .='<tr>'
        .'<td title="Открыть заказ"><a href="/administrator/z/zorder.php?mode=edit&id='.$row['id'].'">'.$row['id'].'</a></td>'
        .'<td>'.$row['orderdate'].'</td>'
        .'<td>'.$row['customername'].'</td>'
        .'<td>'.$plan.'</td>'
        .'<td>'.$fact.'</td>'
        .'<td>'.($fact-$plan).'</td>'
        .'<td>'.$pay.'</td>'
        .'<td>'.($fact-$pay).'</td>'
        .'<td>'.$row['username'].'</td>
        </tr>';
}

$out .='</table>';

echo $out;
?>