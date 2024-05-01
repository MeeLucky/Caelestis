<?php

$mysqli = new mysqli("localhost", "root", "", "Caelestis");
// $result = $mysqli->query("SELECT id, name FROM categories");
// echo mysqli_error($mysqli);
// echo "<table border=1 cellpadding='3' cellspacing='0'>";
// echo "<tr>";
// echo "<th>ид</th>";
// echo "<th>имя</th>";
// echo "<th>общая категория</th>";
// echo "</tr>";

// foreach($result as $item) {
//     $generalCategory = "";
//     $split = explode("/", $item["name"]);
//     if ($split[0] == "Жидкости для POD-систем") $generalCategory = "Жидкости для POD-систем";
//     elseif ($split[1] == "Немаркированные жидкости для POD-систем") $generalCategory = "Жидкости для POD-систем";
//     elseif ($split[1] == "Немаркированные Одноразовые POD-системы") $generalCategory = "Одноразовые POD-системы";
//     elseif ($split[1] == "Немаркированный табак, смеси для кальяна") $generalCategory = "Табаки и смеси для кальяна";
//     elseif ($split[2] == "Эксклюзив") $generalCategory = "Эксклюзив";
//     elseif ($split[2] == "СНС") $generalCategory = "СНС";
//     elseif ($split[2] == "Мосинком и Премиум Табак") $generalCategory = "Мосинком и Премиум Табак";
//     elseif ($split[2] == "Континент") $generalCategory = "Континент";
//     elseif ($split[2] == "ВЛК") $generalCategory = "ВЛК";
//     elseif ($split[2] == "АВРОРА") $generalCategory = "АВРОРА";
//     elseif ($split[0] == "т") $generalCategory = $split[1];
//     else $generalCategory = $split[0];
    
//     $result = $mysqli->query("UPDATE categories SET general_name = '".$generalCategory."' WHERE id = ". $item["id"]);
//     echo mysqli_error($mysqli);
    
//     echo "<tr>";
//     echo "<td>".$item["id"]."</td>";
//     echo "<td>".$item["name"]."</td>";
//     echo "<td width='200px'>".$generalCategory."</td>";
//     echo "</tr>";
// }
// echo "</table>"; 

// $result = $mysqli->query("SELECT general_name FROM categories GROUP BY general_name");
// echo mysqli_error($mysqli);
// foreach($result as $item) {
//     echo "<br>";
//     echo $item["general_name"];
//     $mysqli->query("INSERT INTO general_categories (name) VALUES ('".$item["general_name"]."');");
//     if (mysqli_error($mysqli) != false) {
//         echo mysqli_error($mysqli);
//         echo "<br>";
//         echo "INSERT INTO general_categories name = '".$item["general_name"]."';";
//         break;
//     }
// }




$result = $mysqli->query("SELECT categories.id as id1, general_categories.id as id2, categories.name as name1, general_categories.name as name2 
FROM categories
LEFT JOIN general_categories ON categories.general_name = general_categories.name");
echo mysqli_error($mysqli);
foreach($result as $item) {
    $mysqli->query("UPDATE categories SET id_general_categories = " . $item["id2"] . " WHERE id = " . $item["id1"]);

    // echo "<br>" . $item["id1"] . $item["name1"] 
    // . " = " 
    // . $item["id2"] . $item["name2"] . "<br>";
    // $mysqli->query("INSERT INTO general_categories (name) VALUES ('".$item["general_name"]."');");
    // if (mysqli_error($mysqli) != false) {
        // echo mysqli_error($mysqli);
        // echo "<br>";
        // echo "INSERT INTO general_categories name = '".$item["general_name"]."';";
        // break;
    // }
}



