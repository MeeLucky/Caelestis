<?php
// Подключаем класс для работы с excel
require_once('phpex/Classes/PHPExcel.php');
// Подключаем класс для вывода данных в формате excel
require_once('phpex/Classes/PHPExcel/Writer/Excel5.php');

$inputFileName = 'report.xls';
/** Load $inputFileName to a PHPExcel Object **/
$xls = PHPExcel_IOFactory::load($inputFileName);
//создаём объект
// $xls = new PHPExcel("report.xls");
// Устанавливаем индекс активного листа
$xls->setActiveSheetIndex(0);
$sheet = $xls->getActiveSheet();
// $str = $sheet->getCellByColumnAndRow(1,12)->getValue();
$category = "";
$no_category = "Без категории";

$items = [];

//собираем товары из экселя в 1 массив
for ($row = 14; $row < 30; $row++) {
    $name = $sheet->getCellByColumnAndRow(3,$row)->getValue();
    if ($name == null) {
        $category = $sheet->getCellByColumnAndRow(1,$row)->getValue();
        if ($category == null) 
            $category = $no_category;
    } else {
        // $code = $sheet->getCellByColumnAndRow(1,$row)->getValue();
        $article = $sheet->getCellByColumnAndRow(2,$row)->getValue();
        $start_period = $sheet->getCellByColumnAndRow(5,$row)->getValue();
        $coming = $sheet->getCellByColumnAndRow(7,$row)->getValue();
        $cost = $sheet->getCellByColumnAndRow(9,$row)->getValue();
        $end_period = $sheet->getCellByColumnAndRow(11,$row)->getValue();

        // array_push($items, [$code, $article, $name, $category, $start_period, $coming, $cost, $end_period]);
        array_push($items, [$article, $name, $category, $start_period, $coming, $cost, $end_period]);
    }
}
//подключаемся к бд
$mysqli = new mysqli("localhost", "root", "", "Caelestis");
//очищаем таблицу оборотов
$mysqli->query("TRUNCATE TABLE item_turnover");
//кешируем таблицу категорий
$categories = cashe_categories($mysqli);
//кешируем таблицу товаров
$db_items = cashe_items($mysqli);

//перебираем товары из экселя
foreach ($items as $item) {
    //получаем ид категории
    $id_categories = array_search($item[2], $categories);
    //если нет ид категории
    if($id_categories == false) {
        //то добавляем новую категорию
        $mysqli->query("INSERT INTO categories  SET name = '".$item[2]."'");
        //перекешируем категории
        $categories = cashe_categories($mysqli);
        //пробуем ещё раз получить ид категории
        $id_categories = array_search($item[2], $categories);
    }
    //проверям наличие ид категории
    if($id_categories == false) throw new Exception("Не удалось получить ид категории");

    //получаем ид товара
    $id_item = null;
    if (isset($db_items[$item[0]]) == true)
        $id_item = $db_items[$item[0]][1];
    else {
        $mysqli->query("INSERT INTO items (name, id_categories, article) VALUES ('".$item[1]."', ". $id_categories .", '".$item[0]."');");
        $db_items = cashe_items($mysqli);

        if (isset($db_items[$item[0]]) == true)
            $id_item = $db_items[$item[0]][1];
    }
    if($id_item == null) throw new Exception("Не удалось получить ид товара");
   
    //добавляем обороты для товара
    $mysqli->query("INSERT INTO item_turnover   (id_items, start, arrival, cost, end) 
        VALUES  (".$id_item.", ".$item[3].", ".$item[4].", ".$item[5].", ".$item[6].")");
}

function cashe_categories($mysqli) {
    $result = $mysqli->query("SELECT MAX(id) as maxid FROM categories;");
    $row = $result->fetch_assoc();
    $maxid = $row["maxid"];
   
    $result = $mysqli->query("SELECT id, name FROM categories;");
    $row = $result->fetch_assoc();
    $ret = array_fill(0, $maxid, null);
    if ($row == null)
        return [];
    else {
        foreach($result as $item) {
            $ret[$item["id"]] = $item["name"];
        }
    }
    return $ret;
}

function cashe_items($mysqli) {
    $result = $mysqli->query("SELECT article, id, id_categories, name FROM items;");
    $row = $result->fetch_assoc();
    $ret = [];
    foreach($result as $item) {
        $ret[$item["article"]] = [$item["name"], $item["id_categories"]];
    }
    return $ret; 
}

// var_dump("$item[0]");
//     if (empty($db_items[$item[0]]) == false) {
//         $id_item = $db_items[$item[0]][1];
//     } else {
//         echo "INSERT INTO items (id_categories, article, name)
//         VALUES (".$id_categories.", '".$item[0]."'), '".$item[1]."'";
        
//         $mysqli->query("INSERT INTO items (id_categories, article, name)
//           VALUES (".$id_categories.", '".$item[0]."'), '".$item[1]."'");
//         $db_items = cashe_items($mysqli);

//         if (empty($db_items[$item[0]]) == false) {
//             $id_item = $db_items[$item[0]][1];
//         }
//     }
//     //проверям наличие ид товара
//     if($id_item == null) throw new Exception("Не удалось получить ид товара");