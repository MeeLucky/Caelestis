<?php
$storage = $_GET["storage"];
$category = $_GET["category"];

$mysqli = new mysqli("localhost", "root", "", "Caelestis");

$result = $mysqli->query("SELECT MAX(id) as maxid FROM reports WHERE id_storage = " . $storage);
$row = $result->fetch_assoc();
$id_report = $row["maxid"];

//генерация рекомендаций
analyseTurnover($mysqli, $id_report);
//создание таблицы
echo makeTable(getItemsByCategory($mysqli, $category));


//формирует предложение к заказу для всех оборотов
function analyseTurnover($mysqli, $id_report) {
    $result = $mysqli->query("SELECT id, cost, end, suggest FROM item_turnover WHERE id_reports = " . $id_report);
    foreach($result as $item) {
        // $start = $item["start"];
        // $arrival = $item["arrival"];
        $cost = $item["cost"];
        $end = $item["end"];
        $rec = 0;
        //1) если остаток 0, то расходы * 1.5
        //2) если расход >= остатка, то (расход - остатк) * 1.2
        //3) остаток > расохода 
        //примеры:
        //1) остаток 0, расход 21, 21 * 1.5 = 31.5, 32 к заказу
        //2) остаток 5, расход 7, (7 - 5) * 1.2 = 2.4, 3 к заказу

        if ($end != 0){
            if ($cost >= $end) {
                $rec = ceil(($cost - $end) * 1.2);
            }
        } else {
            $rec = ceil($cost * 1.5);
        }

        if($item["suggest"] != $rec){
            $mysqli->query("UPDATE item_turnover SET suggest = " . $rec ." WHERE id = " . $item["id"]);
            // $mysqli->query("UPDATE item_turnover SET suggest = " . $rec ." WHERE id_reports = ".$id_report." AND id_items = " . $item["id_items"]);
            if (mysqli_error($mysqli) != "")
                echo "<br>". mysqli_error($mysqli);
        }

    }
}

function getItemsByCategory($mysqli, $id_category) {
    $result = $mysqli->query("SELECT items.name as name, cost, end, id_general_categories, suggest
    FROM items 
    LEFT JOIN item_turnover ON items.id = item_turnover.id_items
    LEFT JOIN categories ON items.id_categories = categories.id 
    WHERE id_general_categories = ". $id_category . "
    ORDER BY suggest DESC, cost DESC;");
    if (mysqli_error($mysqli) != "")
        echo mysqli_error($mysqli);
    return $result;
} 

function makeTable($result) {
    $str = "<table border=1 cellpadding='3' cellspacing='0'>";
    $str .= "<tr>";
    $str .= "<th>Название</th>";
    // $str .= "<th>Начало<br>периода</th>";
    // $str .= "<th>Приход</th>";
    $str .= "<th>Расход</th>";
    $str .= "<th>Конец<br>периода</th>";
    $str .= "<th>Рекомендация</th>";
    $str .= "</tr>";
    foreach($result as $item) {
        $str .= "<tr>";
        $str .= "<td>".$item["name"]."</td>";
        // $str .= "<td>".$item["start"]."</td>";
        // $str .= "<td>".$item["arrival"]."</td>";
        $str .= "<td>".$item["cost"]."</td>";
        $str .= "<td>".$item["end"]."</td>";
        $str .= "<td>".$item["suggest"]."</td>";
        $str .= "</tr>";
    }
    $str .= "</table>";

    return $str;
}

