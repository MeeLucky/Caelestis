<?php 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

    // $mysqli = new mysqli("localhost", "root", "", "Caelestis");
    require_once("php/dbconnect.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caelestis</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/jQuery.js"></script>
    <script src="js/script.js"></script>
</head>
<body>
    <div class="input-file">
        <label for="file-select">Загрузить таблицу Excel</label>
        <br>
        <input id="file-select" type="file" accept=".xls">
        <br>
        <input type="button" value="Загрузить" onclick="upload();">
    </div>
    <div class="settings">
        <div class="category-selector-wrapper">
            <label for="category-selector">Категория </label>
            <br>
            <select id="category-selector">
                <option value="0">Все категории</option>
                <option value="-1">Картриджи и испарители</option>
                <?php 
                    $result = $mysqli->query("SELECT id, name FROM general_categories WHERE on_suggest = 1");
                    foreach($result as $item) 
                        echo "<option value = ".$item["id"].">".$item["name"]."</option>";
                ?>  
            </select>

            <br>
            <br>

            <label for="storage-selector">Cклад </label>
            <select id="storage-selector">
                <option value="0"></option>
                <?php 
                    $result = $mysqli->query("SELECT id, name FROM storage");
                    foreach($result as $item) 
                        echo "<option value = ".$item["id"].">".$item["name"]."</option>";
                ?>  
            </select>

            <br>
            <br>

            <label for="sort-selector">Сортирвоать по </label>
            <select id="sort-selector">
                <option value="suggest">Рекомендациям</option>
                <option value="name">Названию</option>
                <option value="cost">Расходу</option>
                <option value="end">Остатку</option>
                
            </select>
        </div>
        <br>
        <input class="btn-show" type="button" value="Показать" onclick="getTurnover()">
    </div>

    <div class="result"></div>

</body>
</html>

<?php
// Подключаем класс для работы с excel
// require_once('phpex/Classes/PHPExcel.php');
// Подключаем класс для вывода данных в формате excel
// require_once('phpex/Classes/PHPExcel/Writer/Excel5.php');

//подключаемся к бд
// $mysqli = new mysqli("localhost", "root", "", "Caelestis");

//анализ экселя
// $id_report =  parseXLS($mysqli, 'report.xls');
//анализ оборотов в БД
// analyseTurnover($mysqli, $id_report);
//создание таблицы
// echo makeTable(getItemsByCategory($mysqli, 45));


//получение всей таблицы БД
// echo getTableAllItems($mysqli);

// //формирует предложение к заказу для всех оборотов
// function analyseTurnover($mysqli, $id_report) {
//     $result = $mysqli->query("SELECT * FROM item_turnover WHERE id_reports = " . $id_report);
//     echo "SELECT * FROM item_turnover WHERE id_reports = " . $id_report;
//     // exit;
//     foreach($result as $item) {
//         $start = $item["start"];
//         $arrival = $item["arrival"];
//         $cost = $item["cost"];
//         $end = $item["end"];
//         $rec = 0;
//         //1) если остаток 0, то расходы * 1.5
//         //2) если расход >= остатка, то (расход - остатк) * 1.2
//         //3) остаток > расохода 
//         //примеры:
//         //1) остаток 0, расход 21, 21 * 1.5 = 31.5, 32 к заказу
//         //2) остаток 5, расход 7, (7 - 5) * 1.2 = 2.4, 3 к заказу

//         if ($end != 0){
//             if ($cost >= $end) {
//                 $rec = ceil(($cost - $end) * 1.2);
//             }
//         } else {
//             $rec = ceil($cost * 1.5);
//         }

//         if($item["suggest"] != $rec){
//             $mysqli->query("UPDATE item_turnover SET suggest = " . $rec ." WHERE id_reports = ".$id_report." AND id_items = " . $item["id_items"]);
//             if (mysqli_error($mysqli) != "")
//                 echo "<br>". mysqli_error($mysqli);
//         }

//     }
// }

// function getItemsByCategory($mysqli, $id_category) {
//     $result = $mysqli->query("SELECT items.name as name, start, arrival, cost, end, id_general_categories, suggest
//     FROM items 
//     LEFT JOIN item_turnover ON items.id = item_turnover.id_items
//     LEFT JOIN categories ON items.id_categories = categories.id 
//     WHERE id_general_categories = ". $id_category . "
//     ORDER BY suggest DESC");
//     if (mysqli_error($mysqli) != "")
//         echo mysqli_error($mysqli);
//     return $result;
// } 

// function makeTable($result) {
//     $str = "<table border=1 cellpadding='3' cellspacing='0'>";
//     $str .= "<tr>";
//     $str .= "<th>Название</th>";
//     $str .= "<th>Начало<br>периода</th>";
//     $str .= "<th>Приход</th>";
//     $str .= "<th>Расход</th>";
//     $str .= "<th>Конец<br>периода</th>";
//     $str .= "<th>Рекомендация</th>";
//     $str .= "</tr>";
//     foreach($result as $item) {
//         $str .= "<tr>";
//         $str .= "<td>".$item["name"]."</td>";
//         $str .= "<td>".$item["start"]."</td>";
//         $str .= "<td>".$item["arrival"]."</td>";
//         $str .= "<td>".$item["cost"]."</td>";
//         $str .= "<td>".$item["end"]."</td>";
//         $str .= "<td>".$item["suggest"]."</td>";
//         $str .= "</tr>";
//     }
//     $str .= "</table>";

//     return $str;
// }


// function getTableAllItems($mysqli) {
//     $result = $mysqli->query("SELECT items.id as id, items.name as name, start, arrival, cost, end, general_name
//     FROM items 
//     LEFT JOIN item_turnover ON items.id = item_turnover.id_items
//     LEFT JOIN categories ON items.id_categories = categories.id;");
//     $str .= "";
//     echo mysqli_error($mysqli);
//     $str = "<table border=1 cellpadding='3' cellspacing='0'>";
//     $str .= "<tr>";
//     $str .= "<th>id</th>";
//     $str .= "<th>name</th>";
//     $str .= "<th>start</th>";
//     $str .= "<th>arrival</th>";
//     $str .= "<th>cost</th>";
//     $str .= "<th>end</th>";
//     $str .= "<th>category</th>";
//     $str .= "</tr>";
//     foreach($result as $item) {
//         $str .= "<tr>";
//         $str .= "<td align='center'>". $item["id"] ."</td>";
//         $str .= "<td>".  $item["name"] . "</td>";
//         $str .= "<td align='center'>".  $item["start"] . "</td>";
//         $str .= "<td align='center'>".  $item["arrival"] . "</td>";
//         $str .= "<td align='center'>".  $item["cost"] . "</td>";
//         $str .= "<td align='center'>".  $item["end"] . "</td>";
//         $str .= "<td align='center'>".  $item["general_name"] . "</td>";
//         $str .= "</tr>";
//     }
//     $str .= "</table>";
//     return $str;
// }


// function cashe_categories($mysqli) {
//     $result = $mysqli->query("SELECT MAX(id) as maxid FROM categories;");
//     $row = $result->fetch_assoc();
//     $maxid = $row["maxid"];
   
//     $result = $mysqli->query("SELECT id, name FROM categories;");
//     $row = $result->fetch_assoc();
//     $ret = array_fill(0, $maxid, null);
//     if ($row == null)
//         return [];
//     else {
//         foreach($result as $item) {
//             $ret[$item["id"]] = $item["name"];
//         }
//     }
//     return $ret;
// }


// //code
// //0 name
// //1 id
// //2 id_categories
// function cashe_items($mysqli) {
//     $result = $mysqli->query("SELECT code, id, name, id_categories FROM items;");
//     $row = $result->fetch_assoc();
//     $ret = [];
//     foreach($result as $item) {
//         $ret[$item["code"]] = [$item["name"], $item["id"], $item["id_categories"]];
//     }
//     return $ret; 
// }


// function formatToSQL($str) {
//     $str = str_replace("'", "", $str);
//     $str = str_replace("\\", "", $str);
//     return $str;
// }


// function parseXLS($mysqli, $inputFileName) {

//     $xls = PHPExcel_IOFactory::load($inputFileName);
//     $xls->setActiveSheetIndex(0);
//     $sheet = $xls->getActiveSheet();
//     $category = "";
//     $no_category = "Без категории";
//     $items = [];

//     //собираем товары из экселя в 1 массив
//     $row = 13; //с этой срочки начинается табличная часть
//     $end = false;
//     while ($end == false) {
//         $row++;
//         //если первый столб пустой (это дожен быть код или категория)
//         if($sheet->getCellByColumnAndRow(1,$row)->getValue() == null) {
//             //то проверям следующее поле (категория "без категории" основывается на пустой категории)
//             //то есть если после пустой категории нет товара, то это конец таблицы
//             if($sheet->getCellByColumnAndRow(1,$row+1)->getValue() == null)
//                 $end = true;
//                 continue;
//         }

//         if ($category == "")  $category = $no_category;

//         $name = $sheet->getCellByColumnAndRow(3,$row)->getValue();
//         if ($name == null || $name == "") {
//             $category = $sheet->getCellByColumnAndRow(1,$row)->getValue();
//         } else {
//             $code = $sheet->getCellByColumnAndRow(1,$row)->getValue();
//             // $article = $sheet->getCellByColumnAndRow(2,$row)->getValue(); //есть повторяющиеся
//             $start_period = $sheet->getCellByColumnAndRow(5,$row)->getValue();
//             $coming = $sheet->getCellByColumnAndRow(7,$row)->getValue();
//             $cost = $sheet->getCellByColumnAndRow(9,$row)->getValue();
//             $end_period = $sheet->getCellByColumnAndRow(11,$row)->getValue();

//             array_push($items, [formatToSQL($code), formatToSQL($name), formatToSQL($category), $start_period, $coming, $cost, $end_period]);
//         }
//     }

//     //получаем не табличные данные из экселя
//     $dateFrom = toDate(dateConventer($sheet->getCellByColumnAndRow(3,7)->getValue()));
//     $dateTo = toDate(dateConventer($sheet->getCellByColumnAndRow(3,8)->getValue()));
//     $storage = str_replace("По умолчанию содержит ", "", $sheet->getCellByColumnAndRow(3,10)->getValue());

//     //получаем ид склада
//     $query = "SELECT id FROM storage WHERE name = '".$storage."'";
//     $result = $mysqli->query($query);
//     if ($result->num_rows > 1){
//         throw new Exception("<b>Из базы полученно некорректное кол-во складов</b>");

//     } elseif ($result->num_rows == 0) {
//         $mysqli->query("INSERT INTO storage SET name = '" . $storage ."'");
//         $result = $mysqli->query($query);
//     }
//     $row = $result->fetch_assoc();
//     $id_storage = $row["id"];

//     //добавляем репорт и получаем его ид
//     $mysqli->query("INSERT INTO reports SET id_storage = ".$id_storage.", date_from = '".$dateFrom."', date_to = '".$dateTo."'");
//     $result = $mysqli->query("SELECT MAX(id) AS id FROM reports");
//     $row = $result->fetch_assoc();
//     $id_report = $row["id"];

//     //удаляем обороты по складу
//     $mysqli->query("DELETE FROM item_turnover WHERE id_reports IN (SELECT id FROM reports WHERE id_storage = ".$id_storage.")");
//     //кешируем таблицу категорий
//     $categories = cashe_categories($mysqli);

//     //кешируем таблицу товаров
//     $db_items = cashe_items($mysqli);

//     //перебираем все тоавары из таблицы эксель, 
//     //ищем категорию товара в БД, если её там нет, то добавляем в БД
//     //ищем товар в БД, если его там нет, то добавляем в БД
//     //добавляем обороты для товара
//     $debug = false;
    
//     foreach ($items as $item) {
//         //$item:
//         //  0 code
//         //  1 name
//         //  2 category
//         //  3 start_period
//         //  4 arrival
//         //  5 cost
//         //  6 end period

//         if ($debug) echo "<br><b>" . $item[1] . "</b><br>";
//         if ($debug) var_dump($item);

//         //получаем ид категории
//         $id_categories = array_search($item[2], $categories);
//         //если нет ид категории
//         if($id_categories == false) {
//             //находим название общей категории
//             $generalCategory = "";
//             $split = explode("/", $item[2]);
//             if     ($split[1] == "Немаркированные жидкости для POD-систем") $generalCategory = "Жидкости для POD-систем";
//             elseif ($split[1] == "Немаркированные Одноразовые POD-системы") $generalCategory = "Одноразовые POD-системы";
//             elseif ($split[1] == "Немаркированный табак, смеси для кальяна") $generalCategory = "Табаки и смеси для кальяна";
//             elseif ($split[2] == "Эксклюзив") $generalCategory = "Эксклюзив";
//             elseif ($split[2] == "СНС") $generalCategory = "СНС";
//             elseif ($split[2] == "Мосинком и Премиум Табак") $generalCategory = "Мосинком и Премиум Табак";
//             elseif ($split[2] == "Континент") $generalCategory = "Континент";
//             elseif ($split[2] == "ВЛК") $generalCategory = "ВЛК";
//             elseif ($split[2] == "АВРОРА") $generalCategory = "АВРОРА";
//             elseif ($split[0] == "т") $generalCategory = $split[1];
//             else $generalCategory = $split[0];
            
//             //находим или добавляем общую категорию в базу
//             $query = "SELECT id FROM general_categories WHERE name = '".$generalCategory."'";
//             $result = $mysqli->query($query);
//             if (mysqli_error($mysqli) != "") throw new Exception(mysqli_error($mysqli));
//             if ($result->num_rows == 1) {
//                 $row = $result->fetch_assoc();
//                 $id_general_category = $row["id"];
//             } elseif($result->num_rows == 0) {
//                 $mysqli->query("INSERT INTO general_categories  SET name = '".$generalCategory."'");
//                 $result = $mysqli->query($query);
//                 $row = $result->fetch_assoc();
//                 $id_general_category = $row["id"];
//             } else {
//                 throw new Exception("<b>Новая ошибка с категориями :)</b>");
//             }
            
//             //добавляем новую категорию
//             $mysqli->query("INSERT INTO categories  SET name = '".$item[2]."', id_general_categories = " . $id_general_category);
//             if (mysqli_error($mysqli) != "")
//                 echo "<br".mysqli_error($mysqli);
            
//             //перекешируем категории
//             $categories = cashe_categories($mysqli);
//             //пробуем ещё раз получить ид категории
//             $id_categories = array_search($item[2], $categories);
//         }
//         //проверям наличие ид категории
//         if($id_categories == false) throw new Exception("Не удалось получить ид категории");
//         if ($debug) echo "<br>Получили ид категории: " . $id_categories . " - " . $item[2];

//         //получаем ид товара
//         $id_item = null;
//         if (isset($db_items[$item[0]]) == true)
//             $id_item = $db_items[$item[0]][1];
//         else {
//             $mysqli->query("INSERT INTO items (name, id_categories, code) VALUES ('".$item[1]."', ". $id_categories .", '".$item[0]."');");
//             $db_items = cashe_items($mysqli);

//             if (isset($db_items[$item[0]]) == true)
//                 $id_item = $db_items[$item[0]][1];
//         }
//         if($id_item == null) {
//         echo "INSERT INTO items (name, id_categories, code) VALUES ('".$item[1]."', ". $id_categories .", '".$item[0]."');";
//             throw new Exception("Не удалось получить ид товара");
//         }
//         if ($debug) echo "<br>Получили ид товара: " . $id_item . " - " . $item[0];

//         //сверяем корректность тоавра и категории
//         //name
//         if ($db_items[$item[0]][0] != $item[1]) {
//             echo $item[0];
//             echo "<br><b>". $db_items[$item[0]][0] . " != " . $item[1] . "</b><br>";
//             var_dump($db_items[$item[0]]);

//             throw new Exception("Некореектное название товара");
//         }

//         //id_categories
//         if ($db_items[$item[0]][2] != $id_categories) {
//             $mysqli->query("UPDATE items SET id_categories = ".$id_categories." WHERE id = " . $id_item);
//             if (mysqli_error($mysqli) != "")
//             {
//                 echo "<br>" . mysqli_error($mysqli) . "<br>";
//                 var_dump($db_items[$item[0]]);
//                 echo "<br><b>". $db_items[$item[0]][2] . " != " . $id_categories . "</b><br>";
//                 throw new Exception("Некорректный ид категории");
//             }
//         }
        
//         // добавляем обороты для товара
//         $mysqli->query("INSERT INTO item_turnover   (id_items, id_reports, start, arrival, cost, end) 
//             VALUES  (".$id_item.", ". $id_report." ,".$item[3].", ".$item[4].", ".$item[5].", ".$item[6].")");
//         if ($debug) echo "<br><b>" . mysqli_error($mysqli) ."</b>";
//         if ($debug) echo "<br>Добавили обороты товара";
//         if ($debug) echo "<hr>";
//     }

//     return $id_report;
// }

// function toDate($date) {
//     return gmdate("Y-m-d H:i:s", $date);
// }

// function dateConventer($date) {
//     return ($date - 25569) * 86400;
// }