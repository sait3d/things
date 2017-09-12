<?php

class TZOrder extends TPage {
/*
 * для задачи используем две таблицы СУБД в одном классе для упрощения
 */
    public $id=0;
    public $orderdate='';
    public $d_user=0;
    public $id_customer=0;
    public $orderprice=0;
    // данные шапки
    public $username = '';
    public $customername = '';

    public function __construct() {
        parent::__construct();
        parent::getPDO();
    }

    public function __destruct() {
    }

    /*
     * шапка заказа
     */
    public function getOrder($id) {

        if(!$id) return 0;

        $sql = "SELECT
zo.id,
zo.orderdate,
zo.id_user,
u.fullname AS username,
zo.id_customer,
c.fullname AS customername,
c.address,
c.contacts,
zo.orderprice
FROM z_order AS zo
INNER JOIN z_users AS u ON u.id=zo.id_user
INNER JOIN z_customer AS c ON c.id=zo.id_customer
WHERE zo.id = :id";

        $stm = $this->pdo->prepare($sql);
        $stm->bindParam(':id',$id,PDO::PARAM_INT);

        $rowcnt = 0;
        if($stm->execute()) {
            $rowcnt = $stm->rowCount();
            if($rowcnt) {
                $row = $stm->fetch(PDO::FETCH_ASSOC);
                // общие параметры страницы
                $this->id = $row['id'];
                $this->orderdate = $row['orderdate'];
                $this->id_user = $row['id_user'];
                $this->username = $row['username'];
                $this->id_customer = $row['id_customer'];
                $this->customername = $row['customername'];
                $this->orderprice = $row['orderprice'];

            }
        }
        return $rowcnt;
    }

    // табличная часть заказа
    public function getTableOrder() {

        $data = array();
        if(!$this->id) return $data;

        $sql = "SELECT
ot.id_order,
ot.numline,
ot.id_service,
s.fullname,
s.price AS priceplan,
ot.price
FROM z_order_table AS ot
INNER JOIN z_service AS s ON s.id=ot.id_service
WHERE ot.id_order = :id";

        $stm = $this->pdo->prepare($sql);
        $stm->bindParam(':id',$this->id,PDO::PARAM_INT);

        if($stm->execute()) {
            $rowcnt = $stm->rowCount();

            if($rowcnt) {
                $data = $stm->fetchALL(PDO::FETCH_ASSOC);
            }
        }

        unset($stm);

        return $data;
    }

    public function getListOrder() {

        $sql = "SELECT
zo.id,
zo.orderdate,
zo.id_user,
u.fullname AS username,
zo.id_customer,
c.fullname AS customername,
c.address,
c.contacts,
zo.orderprice
FROM z_order AS zo
INNER JOIN z_users AS u ON u.id=zo.id_user
INNER JOIN z_customer AS c ON c.id=zo.id_customer";

        $result = $this->pdo->query($sql);
        $data = $result->fetchALL(PDO::FETCH_ASSOC);

        unset($result);
        return $data;
    }

    /**
     * templates создавать надо время, поэтому делаем в
     */
    // список покупателей
    public function getSelectCustomer($mode,$id_customer) {

        $sql = "SELECT
id,
fullname,
address,
contacts
FROM z_customer";
        $result = $this->pdo->query($sql);
        $data = $result->fetchALL(PDO::FETCH_ASSOC);

        unset($result);

        $out = '<select name="id_customer" id="id_customer">';
        $out .= '<option value="0">Выберите покупателя</option>';

        foreach($data AS $row) {
            $out .= '<option '.($id_customer==$row['id']?'selected="selected" ':'').'value="'.$row['id'].'">'.$row['fullname'].'</option>';
        }

        $out .= '</select>';

        return $out;

    }

    // новый заказ
    public function insertOrder($data) {

        $id_order = 0;
        $id_customer = (int) $data[0]['id_customer'];

        unset($data[0]);
        // заполнение шапки заказа

        // запись шапки заказа
        $sql="INSERT INTO z_order
SET orderdate=DATE_FORMAT(NOW(),'%Y-%m-%d'),
id_user=:id_user,
id_customer=:$id_customer,
orderprice=:orderprice";

        $stm = $this->pdo->prepare($sql);

        $stm->bindParam(':id_user',$id_user,PDO::PARAM_INT);
        $stm->bindParam(':id_customer',$id_customer,PDO::PARAM_INT);
        $stm->bindParam(':orderprice',$orderprice,PDO::PARAM_INT);

        if($stm->execute()) {
            $id_order = $this->pdo->lastInsertId();
        } else {
            return -1;
        }
        // заполнение таблицы заказа
        $sql="INSERT INTO z_order_table
SET id_order=:id_order,
numline=:numline,
id_service=:id_service,
price=:price";

        $stm = $this->pdo->prepare($sql);

        $numline = 0;

        foreach($data AS $row) {

            $numline++;

            $stm->bindParam(':id_service',$row['id_service'],PDO::PARAM_INT);
            $stm->bindParam(':price',$row['price'],PDO::PARAM_INT);
            $stm->bindParam(':id_order',$id_order,PDO::PARAM_INT);
            $stm->bindParam(':numline',$numline,PDO::PARAM_INT);

            if($stm->execute()) {
                $orderprice +=(int)$row['price'];
            } else {
                return -1;
            }
        }

    }
    // изменение заказа
    public function updateOrder($data) {

        $id_order = (int) $data[0]['id_order'];
        $id_customer = (int) $data[0]['id_customer'];

        unset($data[0]);

        $orderprice = 0;
// id_service=:id_service,
        $sql="UPDATE z_order_table
SET price=:price
WHERE id_order=:id_order AND numline=:numline";

        $stm = $this->pdo->prepare($sql);

        foreach($data AS $row) {

            //$stm->bindParam(':id_service',$row['id_service'],PDO::PARAM_INT);
            $stm->bindParam(':price',$row['price'],PDO::PARAM_INT);
            $stm->bindParam(':id_order',$id_order,PDO::PARAM_INT);
            $stm->bindParam(':numline',$row['numline'],PDO::PARAM_INT);

            if($stm->execute()) {
                $orderprice +=(int)$row['price'];
            } else {
                return -1;
            }
        }

        // запись шапки заказа
        $sql="UPDATE z_order
SET id_customer=:id_customer,
orderprice=:orderprice
WHERE id=:id";

        $stm = $this->pdo->prepare($sql);

        $stm->bindParam(':id_customer',$id_customer,PDO::PARAM_INT);
        $stm->bindParam(':orderprice',$orderprice,PDO::PARAM_INT);
        $stm->bindParam(':id',$id_order,PDO::PARAM_INT);

        if($stm->execute()) {
            return $orderprice;
        } else {
            return -1;
        }
    }

    // получить список заказов с плановыми ценами
    public function getProfitReport($data) {

        $where = '';
        if(count($data)>0) {

            $where .=" WHERE o.id>0 ";
            if(isset($data['id_user'])) {
                $where.="AND o.id_user=".$data['id_user']." ";
            }
            if(isset($data['id_customer'])) {
                $where.="AND o.id_customer=".$data['id_customer']." ";
            }
        }

        $sql = "SELECT
o.id,
o.orderdate,
o.id_user,
c.fullname AS customername,
o.id_customer,
u.fullname AS username,
o.orderprice,
SUM(t.price) AS sumprice,
SUM(s.price) AS sumplanprice,
p.sumpay
FROM z_order AS o
INNER JOIN z_order_table AS t ON t.id_order=o.id
INNER JOIN z_service AS s ON s.id=t.id_service
INNER JOIN z_customer AS c ON c.id=o.id_customer
INNER JOIN z_users AS u ON u.id=o.id_user
LEFT JOIN (
SELECT id_order,SUM(amount) AS sumpay FROM z_payment GROUP BY id_order
) AS p ON p.id_order=o.id
".$where."
GROUP BY o.id";

        $stm = $this->pdo->prepare($sql);
        //$stm->bindParam(':id',$this->id,PDO::PARAM_INT);

        if($stm->execute()) {
            $rowcnt = $stm->rowCount();

            if($rowcnt) {
                $data = $stm->fetchALL(PDO::FETCH_ASSOC);
            }
        }

        unset($stm);

        return $data;
    }

    // список оплат по заказу
    public function getPayList($id_order) {
        $sql = "SELECT
p.id_order,
p.description,
p.paytape,
p.paydate,
p.amount,
p.id_user,
u.fullname
FROM z_payment p
INNER JOIN z_users AS u ON u.id=p.id_user
WHERE p.id_order=:id_order";

        $stm = $this->pdo->prepare($sql);
        $stm->bindParam(':id_order',$id_order,PDO::PARAM_INT);

        if($stm->execute()) {
            $rowcnt = $stm->rowCount();

            if($rowcnt) {
                $data = $stm->fetchALL(PDO::FETCH_ASSOC);
            }
        }

        unset($stm);

        return $data;

    }

} 