<?php
error_reporting('E_ALL');
// подключение path
require_once $_SERVER['DOCUMENT_ROOT'].'/administrator/zinit.php';

$torder = NEW TZOrder();

    //$pt = json_decode($_POST['pt'],true);

    $pt = $_POST['pt'];

//print_r($pt);die('tut');

    $res = $torder->updateOrder($pt);
    if($res==-1) {
       echo 'Error save!';
    } else {
        echo 'Сумма заказа '.$res;
    }
    /*
    // первая строка массива - данные шапки
    $id_order = (int)$pt[0]['id_order'];
    $id_customer = (int)$pt[0]['id_customer'];

    unset($pt[0]);

    //
    */
?>