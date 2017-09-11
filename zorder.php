<?php
error_reporting('E_ALL');

/**
 * пробно без контроллера ЧПУ через GET
 */
$id_order = 0;
$mode='';
if(isset($_GET['id'])) $id_order = (int) $_GET['id'];
if(isset($_GET['mode'])) $mode = trim($_GET['mode']);

//print_r($_GET);print('<br>');

if(!$id_order OR empty($mode)) die('ERROR ORDER PARAMETR!');

// подключение path
require_once $_SERVER['DOCUMENT_ROOT'].'/administrator/zinit.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/administrator/zhead.tpl';

$torder = NEW TZOrder();

$torder->getOrder($id_order);

$table_order = $torder->getTableOrder();

//print('<pre>');print_r($table_order);print('</pre>');

$out = '<div id="wrapper">';

$out .='<a href="/administrator/">НА ГЛАВНУЮ СТРАНИЦУ</a>';

$out .= '<h3>Заказ №<span id="id_order">'.$torder->id.'</span> от '.$torder->orderdate.'</h3>';
// данные шапки заказа
$out .='<p>Покупатель:'.$torder->getSelectCustomer($mode,$torder->id_customer).'</p>';
// данные покупателя
$out .='<p id="id_user">Ответственный:'.$torder->username.'</p>';

// таблица оплат по заказу
$payarr = $torder->getPayList($id_order);

if(count($payarr)>0) {
    $out .='<p>Оплаты по заказу</p>';
    $out .='<table class="w100 border1">';

    foreach($payarr AS $row) {
        $out .='<tr>';

        $out .='<td>'.$row['description'].'</td>';
        $out .='<td>'.$row['paydate'].'</td>';


        $out .='<td>'.$row['amount'].'</td>';
        $out .='<td>'.$row['fullname'].'</td>';

        $out .='</tr>';
    }
    $out .='</table>';
}

$out .='<div class="message">
Редактируется фактическая цена на работы
    </div>';

// табличная часть
$out .='<table id="table_order" class="w100">';
// шапка
$out .='<tr><th class="w40px">№</th><th>Работа</th><th class="w120px">План</th><th class="w120px">Факт</th><th class="w40px">X</th></tr>';


foreach($table_order AS $row) {

    $out .='<tr>';

    $out .='<td class="w40px">'.$row['numline'].'</td>';
    $out .='<td data-id="'.$row['id_service'].'">'.$row['fullname'].'</td>';
    $out .='<td class="w120px">'.$row['priceplan'].'</td>';
    $out .='<td class="w120px"><input type="number" class="price" data-id="'.$row['numline'].'" value="'.$row['price'].'"/></td>';
    $out .='<td class="deleteline w40px" data-id="'.$row['numline'].'" title="Удалить">X</td>';

    $out .='</tr>';
}

$out .='</table>';

$out .='<div id="orderprice" class="floatright w100 talignright"><b>'.$torder->orderprice.'</b></div>';
// запись БЕЗ сериализации AJAX
$out .='<button id="saveorder" class="margintop10 floatright">Записать</button>';

$out .='</div>'; // wrapper
// подключаем
$out .='<script type="text/javascript" src="/js/jquery-1.11.2.min.js"></script>';
$out .='<script type="text/javascript" src="/administrator/z/zorder.js"></script>';
// вывод
echo $out;
?>