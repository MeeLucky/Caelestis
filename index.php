<?php
echo "start: " . date('h:i:s a', time());

// Подключаем класс для работы с excel
require_once('phpex/Classes/PHPExcel.php');
// Подключаем класс для вывода данных в формате excel
require_once('phpex/Classes/PHPExcel/Writer/Excel5.php');

//подключаемся к бд
$mysqli = new mysqli("localhost", "root", "", "Caelestis");

parseXLS($mysqli, 'report.xls')

analyseTurnover($mysqli);
echo makeTable(getItemsByCategory($mysqli, 14));
// echo getTableAllItems($mysqli);

function analyseTurnover($mysqli) {
    $result = $mysqli->query("SELECT * FROM item_turnover");
    foreach($result as $item) {
        $start = $item["start"];
        $arrival = $item["arrival"];
        $cost = $item["cost"];
        $end = $item["end"];
        $rec = 0;
        if ($end != 0){
            if ($cost > $end) {
                $rec = $cost - $end;
                $rec += ceil($rec * 0.2) - 1;
            }
        } else {
            $rec = $cost + ceil($cost * 0.5);
        }

        if($item["suggest"] != $rec){
            $mysqli->query("UPDATE item_turnover SET suggest = " . $rec ." WHERE id_items = " . $item["id_items"]);
            if (mysqli_error($mysqli) != "")
                echo "<br>". mysqli_error($mysqli);
        }

    }
}

function getItemsByCategory($mysqli, $id_category) {
    $result = $mysqli->query("SELECT items.name as name, start, arrival, cost, end, id_general_categories, suggest
    FROM items 
    LEFT JOIN item_turnover ON items.id = item_turnover.id_items
    LEFT JOIN categories ON items.id_categories = categories.id 
    WHERE id_general_categories = ". $id_category . "
    ORDER BY suggest DESC");
    if (mysqli_error($mysqli) != "")
        echo mysqli_error($mysqli);
    return $result;
} 

function makeTable($result) {
    $str = "<table border=1 cellpadding='3' cellspacing='0'>";
    $str .= "<tr>";
    $str .= "<th>Название</th>";
    $str .= "<th>Начало<br>периода</th>";
    $str .= "<th>Приход</th>";
    $str .= "<th>Расход</th>";
    $str .= "<th>Конец<br>периода</th>";
    $str .= "<th>Рекомендация</th>";
    $str .= "</tr>";
    foreach($result as $item) {
        $str .= "<tr>";
        $str .= "<td>".$item["name"]."</td>";
        $str .= "<td>".$item["start"]."</td>";
        $str .= "<td>".$item["arrival"]."</td>";
        $str .= "<td>".$item["cost"]."</td>";
        $str .= "<td>".$item["end"]."</td>";
        $str .= "<td>".$item["suggest"]."</td>";
        $str .= "</tr>";
    }
    $str .= "</table>";

    return $str;
}


function getTableAllItems($mysqli) {
    $result = $mysqli->query("SELECT items.id as id, items.name as name, start, arrival, cost, end, general_name
    FROM items 
    LEFT JOIN item_turnover ON items.id = item_turnover.id_items
    LEFT JOIN categories ON items.id_categories = categories.id;");
    $str .= "";
    echo mysqli_error($mysqli);
    $str = "<table border=1 cellpadding='3' cellspacing='0'>";
    $str .= "<tr>";
    $str .= "<th>id</th>";
    $str .= "<th>name</th>";
    $str .= "<th>start</th>";
    $str .= "<th>arrival</th>";
    $str .= "<th>cost</th>";
    $str .= "<th>end</th>";
    $str .= "<th>category</th>";
    $str .= "</tr>";
    foreach($result as $item) {
        $str .= "<tr>";
        $str .= "<td align='center'>". $item["id"] ."</td>";
        $str .= "<td>".  $item["name"] . "</td>";
        $str .= "<td align='center'>".  $item["start"] . "</td>";
        $str .= "<td align='center'>".  $item["arrival"] . "</td>";
        $str .= "<td align='center'>".  $item["cost"] . "</td>";
        $str .= "<td align='center'>".  $item["end"] . "</td>";
        $str .= "<td align='center'>".  $item["general_name"] . "</td>";
        $str .= "</tr>";
    }
    $str .= "</table>";
    return $str;
}


echo "End: " . date('h:i:s a', time());


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
//code
//0 name
//1 id
//2 id_categories
function cashe_items($mysqli) {
    $result = $mysqli->query("SELECT code, id, name, id_categories FROM items;");
    $row = $result->fetch_assoc();
    $ret = [];
    foreach($result as $item) {
        $ret[$item["code"]] = [$item["name"], $item["id"], $item["id_categories"]];
    }
    return $ret; 
}

function formatToSQL($str) {
    $str = str_replace("'", "", $str);
    $str = str_replace("\\", "", $str);
    return $str;
}

function parseXLS($mysqli, $inputFileName) {
    
CONST USEXLS = true;
    if (USEXLS) {
        
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
                $code = $sheet->getCellByColumnAndRow(1,$row)->getValue();
                // $article = $sheet->getCellByColumnAndRow(2,$row)->getValue();
                $start_period = $sheet->getCellByColumnAndRow(5,$row)->getValue();
                $coming = $sheet->getCellByColumnAndRow(7,$row)->getValue();
                $cost = $sheet->getCellByColumnAndRow(9,$row)->getValue();
                $end_period = $sheet->getCellByColumnAndRow(11,$row)->getValue();

                // array_push($items, [$code, $article, $name, $category, $start_period, $coming, $cost, $end_period]);
                array_push($items, [formatToSQL($code), formatToSQL($name), formatToSQL($category), $start_period, $coming, $cost, $end_period]);
            }
        }

        //очищаем таблицу оборотов
        $mysqli->query("TRUNCATE TABLE item_turnover");
        //кешируем таблицу категорий
        $categories = cashe_categories($mysqli);
        //кешируем таблицу товаров
        $db_items = cashe_items($mysqli);
        //перебираем все тоавары из таблицы эксель, 
        //ищем категорию товара в БД, если её там нет, то добавляем в БД
        //ищем товар в БД, если его там нет, то добавляем в БД
        //добавляем обороты для товара
        $debug = false;
        foreach ($items as $item) {
            if ($debug) echo "<br><b>" . $item[1] . "</b><br>";
            if ($debug) var_dump($item);
            //получаем ид категории
            $id_categories = array_search($item[2], $categories);
            //если нет ид категории
            if($id_categories == false) {
                //то добавляем новую категорию
                $mysqli->query("INSERT INTO categories  SET name = '".$item[2]."'");
                echo "<br> ". "INSERT INTO categories  SET name = '".$item[2]."'" . " <br>";
                echo mysqli_error($mysqli);
                //перекешируем категории
                $categories = cashe_categories($mysqli);
                //пробуем ещё раз получить ид категории
                $id_categories = array_search($item[2], $categories);
            }
            //проверям наличие ид категории
            if($id_categories == false) throw new Exception("Не удалось получить ид категории");
            if ($debug) echo "<br>Получили ид категории: " . $id_categories . " - " . $item[2];

            //получаем ид товара
            $id_item = null;
            if (isset($db_items[$item[0]]) == true)
                $id_item = $db_items[$item[0]][1];
            else {
                $mysqli->query("INSERT INTO items (name, id_categories, code) VALUES ('".$item[1]."', ". $id_categories .", '".$item[0]."');");
                $db_items = cashe_items($mysqli);

                if (isset($db_items[$item[0]]) == true)
                    $id_item = $db_items[$item[0]][1];
            }
            if($id_item == null) {
        echo        "INSERT INTO items (name, id_categories, code) VALUES ('".$item[1]."', ". $id_categories .", '".$item[0]."');";
                throw new Exception("Не удалось получить ид товара");
            }
                if ($debug) echo "<br>Получили ид товара: " . $id_item . " - " . $item[0];

            //сверяем корректность тоавра и категории
            //name
            if ($db_items[$item[0]][0] != $item[1]) {
                echo $item[0];
                echo "<br><b>". $db_items[$item[0]][0] . " != " . $item[1] . "</b><br>";
                var_dump($db_items[$item[0]]);

                throw new Exception("Некореектное название товара");
            }
            //id_categories
            if ($db_items[$item[0]][2] != $id_categories) {
                $mysqli->query("UPDATE items SET id_categories = ".$id_categories." WHERE id = " . $id_item);
                if (mysqli_error($mysqli) != "")
                {
                    echo "<br>" . mysqli_error($mysqli) . "<br>";
                    var_dump($db_items[$item[0]]);
                    echo "<br><b>". $db_items[$item[0]][2] . " != " . $id_categories . "</b><br>";
                    throw new Exception("Некорректный ид категории");
                }
            }
            
        
            // добавляем обороты для товара
            $mysqli->query("INSERT INTO item_turnover   (id_items, start, arrival, cost, end) 
                VALUES  (".$id_item.", ".$item[3].", ".$item[4].", ".$item[5].", ".$item[6].")");
            echo mysqli_error($mysqli);
            if ($debug) echo "<br>Добавили обороты товара";
            if ($debug) echo "<hr>";
        }
    }
}