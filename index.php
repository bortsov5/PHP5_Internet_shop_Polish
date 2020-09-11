<?php

//Подключимся к базе данных
$DB_Host = "localhost"; // Адрес компьютера с MySQL
$local   = 0;
$to      = "********@gmail.com";
$subject = "Zamówienia ze strony";
$headers = "Content-type: text/html; charset=utf-8 \r\n";
$headers .= "From: zakaz <shop@******.pl>\r\n";


$sp_d = '<select type="text" name="sp_d" style="width: 97%;"><option value="Odbiór osobisty w sklepie">Odbiór osobisty w sklepie</option>
<option value="Dostawa do domu o">Dostawa do domu o</option></select>';


$sel_time = '<select type="text" name="sel_t" id="sel_tim" style="width: 97%;" onclick="resizeArea(comment_text);"><option value="7:00">7:00</option><option value="8:00">8:00</option><option value="9:00">9:00</option><option value="10:00">10:00</option><option value="11:00">11:00</option><option value="12:00">12:00</option><option value="13:00">13:00</option><option value="14:00">14:00</option><option value="15:00">15:00</option><option value="16:00">16:00</option><option value="17:00">17:00</option><option value="18:00">18:00</option><option value="19:00">19:00</option><option value="20:00">20:00</option><option value="21:00">21:00</option><option value="22:00">22:00</option></select>';

$kurszleur = 1;
$megikKat  = 217;
$glstr     = 301;
$dostavka  = 0;

if ($local == 1) {
    $DB_User = "root"; // Пользователь для доступа к базе
    $DB_Pass = ""; // Пароль для доступ
    $DB_Name = "ipad";
} else {
    $DB_User = "******"; // Пользователь для доступа к базе
    $DB_Pass = "******"; // Пароль для доступ
    $DB_Name = "******";
}

$valid_types   = array(
    "gif",
    "jpg",
    "png",
    "jpeg",
    "bmp"
); // допустимые расширения
$max_file_size = "921600"; // Максимально допустимый размер загружаемого фото
$maxwidth      = "2048"; // Ширина загружаемого изображения в пикселях не более
$maxheight     = "1536"; // Высота -||- 
$smwidth       = "150"; // Ширина миниизображения
$smheight      = "120"; // Высота миниизображения
$adrs          = "http://localhost";

// $adrs="http://localhost";

function e($str)
{
    return $result = $str; // iconv("windows-1251","UTF-8", $str);
}

function p($str)
{
    return $result = $str; //iconv("UTF-8", "windows-1250", $str);
}
function warn($str)
{
    return $result = "<br><div class='vidB'><img src='images/icon/Info.gif'>" . $str . "</div><br>";
}

function warn2($str)
{
    return $result = "<br><div class='vidB'><img src='images/icon/Info.gif'><FONT SIZE='3' COLOR='#339900'><b>" . $str . "</b></FONT></div><br>";
}

function warn3($str)
{
    return $result = "<br><div class='vidB'><img src='images/icon/Info.gif'><FONT SIZE='3' COLOR='#ff3300'><b>" . $str . "</b></FONT></div><br>";
}

function mysql_query_v($str)
{
    //echo warn($str);
    return $result = mysql_query($str); // or die("Запрос обломался: " . mysql_error());
}

function GP_v($str) //Функция защиты на внешние данные
{
    return $result = intval($str);
}

//подсветка синтаксиса
function svettext($svtext, $str)
{
    $svtext = str_replace($str, "<b><FONT COLOR='#000000'>" . $str . "</FONT></b>", $svtext);
    return $result = $svtext;
}

//Получаем размер файла
function fsize($path)
{
    return substr(filesize($path) / 1024, 0, 4);
}

//Имя предыдущей папки
function updir($path)
{
    $last = strrchr($path, "/");
    $n1   = strlen($last);
    $n2   = strlen($path);
    return substr($path, 0, $n2 - $n1);
}

//Удаление файла
function removefile($path)
{
    if (file_exists($path)) {
        if (unlink($path)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

//Удаление каталога с файлами
function removedir($directory)
{
    $dir = opendir($directory);
    while (($file = readdir($dir))) {
        if (is_file($directory . "/" . $file)) {
            unlink($directory . "/" . $file);
        } else if (is_dir($directory . "/" . $file) || ($file != ".") || ($file != "..")) {
            removedir($directory . "/" . $file);
        }
    }
    closedir($dir);
    rmdir($directory);
    return TRUE;
}

//Листинг папок
function listing($url, $mode)
{
    //Проверяем, является ли директорией
    if (is_dir($url)) {
        //Проверяем, была ли открыта директория
        if ($dir = opendir($url)) {
            //Сканируем директорию
            while (false !== ($file = readdir($dir))) {
                //Убираем лишние элементы
                if ($file != "." && $file != "..") {
                    //Если папка, то записываем в массив $folders
                    if (is_dir($url . "/" . $file)) {
                        $folders[] = $file;
                    }
                    //Если файл, то пишем в массив $files
                    else {
                        $files[] = $file;
                    }
                }
            }
        }
        //Закрываем директорию
        closedir($dir);
    }
    //Если режим =1 то возвращаем массив с папками
    if ($mode == 1) {
        return $folders;
    }
    //Если режим =0 то возвращаем массив с файлами
    if ($mode == 0) {
        return $files;
    }
}

//Функция создания папки
function makedir($url)
{
    //Вырезаем пробелы и хтмл-тэги
    $url = trim(htmlspecialchars($url));
    //Если папка создается возвращаем TRUE
    if (@mkdir($url)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

//Функция переименования
function frename($url, $oldname, $nname)
{
    $nname   = trim(htmlspecialchars($nname));
    $oldname = trim(htmlspecialchars($oldname));
    $url     = trim(htmlspecialchars($url));
    if (@rename($url . "/" . $oldname, $url . "/" . $nname)) {
        return TRUE;
    } else {
        return FALSE;
    }
}


//Выбирает из строки только цифры
function digitt($str)
{
    $idC    = $str;
    //$idC=str_replace('86elita', '', $idC);
    $l      = strlen($idC);
    $stroka = '';
    for ($i = 0; $i <= $l; $i++) {
        if ($idC[$i] == '1' || $idC[$i] == '2' || $idC[$i] == '3' || $idC[$i] == '4' || $idC[$i] == '5' || $idC[$i] == '6' || $idC[$i] == '7' || $idC[$i] == '8' || $idC[$i] == '9' || $idC[$i] == '0') {
            $stroka = $stroka . "" . $idC[$i];
        }
    }
    return $stroka;
}

function invert($str)
{
    $stroka = $str;
    $stroka = str_replace('<b>', '[b]', $stroka);
    $stroka = str_replace('</b>', '[/b]', $stroka);
    $stroka = str_replace('<i>', '[i]', $stroka);
    $stroka = str_replace('</i>', '[/i]', $stroka);
    $stroka = str_replace('<u>', '[u]', $stroka);
    $stroka = str_replace('</u>', '[/u]', $stroka);
    $stroka = str_replace('<a href=', '[url=', $stroka);
    $stroka = str_replace('</a>', '[/url]', $stroka);
    
    $stroka = str_replace('<FONT SIZE=3>', '[size=2]', $stroka);
    $stroka = str_replace('<FONT SIZE=2>', '[size=1]', $stroka);
    $stroka = str_replace('<FONT SIZE=1>', '[size=0.5]', $stroka);
    $stroka = str_replace('</FONT>', '[/size]', $stroka);
    $stroka = str_replace('<UL><LI>', '[list]', $stroka);
    $stroka = str_replace('</UL>', '[/list]', $stroka);
    $stroka = str_replace('<OL><LI>', '[list=1]', $stroka);
    
    $stroka = str_replace('<IMG SRC=', '[img src=', $stroka);
    $stroka = str_replace('<FONT SIZE=2 COLOR=#000099>', '[quote]', $stroka);
    $stroka = str_replace('</FONT>', '[/quote]', $stroka);
    $stroka = str_replace('<LI>', '[*]', $stroka);
    $stroka = str_replace('<br>', '[br]', $stroka);
    $stroka = str_replace('<?php', '[php]', $stroka);
    $stroka = str_replace('< ?php', '[php]', $stroka);
    $stroka = str_replace('?>', '[/php]', $stroka);
    
    $stroka = str_replace('</LI>', '[/*]', $stroka);
    $stroka = str_replace('<FONT SIZE=2 COLOR=#990000>', '[code]', $stroka);
    $stroka = str_replace('</FONT>', '[/code]', $stroka);
    
    $stroka = str_replace('>', ']', $stroka);
    return $stroka;
}

function normal($str)
{
    $idC    = $str;
    $l      = strlen($idC);
    $stroka = '';
    for ($i = 0; $i <= $l; $i++) {
        //echo $idC[$i];
        if ($idC[$i] == '1' || $idC[$i] == '2' || $idC[$i] == '3' || $idC[$i] == '4' || $idC[$i] == '5' || $idC[$i] == '6' || $idC[$i] == '7' || $idC[$i] == '8' || $idC[$i] == '9' || $idC[$i] == 'Q' || $idC[$i] == 'W' || $idC[$i] == 'E' || $idC[$i] == 'R' || $idC[$i] == 'T' || $idC[$i] == 'Y' || $idC[$i] == '/' || $idC[$i] == 'U' || $idC[$i] == 'I' || $idC[$i] == 'O' || $idC[$i] == 'P' || $idC[$i] == '[' || $idC[$i] == ']' || $idC[$i] == 'A' || $idC[$i] == 'S' || $idC[$i] == 'D' || $idC[$i] == 'F' || $idC[$i] == 'G' || $idC[$i] == 'H' || $idC[$i] == 'J' || $idC[$i] == 'K' || $idC[$i] == 'L' || $idC[$i] == ';' || $idC[$i] == ':' || $idC[$i] == 'Z' || $idC[$i] == 'X' || $idC[$i] == 'C' || $idC[$i] == 'V' || $idC[$i] == 'B' || $idC[$i] == 'N' || $idC[$i] == 'M' || $idC[$i] == ',' || $idC[$i] == '.' || $idC[$i] == '?' || $idC[$i] == '@' || $idC[$i] == '!' || $idC[$i] == '#' || $idC[$i] == '$' || $idC[$i] == '*' || $idC[$i] == '(' || $idC[$i] == ')' || $idC[$i] == '-' || $idC[$i] == '+' || $idC[$i] == 'q' || $idC[$i] == 'w' || $idC[$i] == 'r' || $idC[$i] == 't' || $idC[$i] == 'y' || $idC[$i] == 'u' || $idC[$i] == 'i' || $idC[$i] == 'o' || $idC[$i] == 'p' || $idC[$i] == 'a' || $idC[$i] == 's' || $idC[$i] == 'd' || $idC[$i] == 'f' || $idC[$i] == 'g' || $idC[$i] == 'h' || $idC[$i] == 'j' || $idC[$i] == 'k' || $idC[$i] == 'l' || $idC[$i] == 'z' || $idC[$i] == 'x' || $idC[$i] == 'c' || $idC[$i] == 'v' || $idC[$i] == 'b' || $idC[$i] == 'n' || $idC[$i] == 'm' || $idC[$i] == '=' || $idC[$i] == ' ' || $idC[$i] == 'e' || $idC[$i] == 'Й' || $idC[$i] == 'Ц' || $idC[$i] == 'У' || $idC[$i] == 'К' || $idC[$i] == 'Е' || $idC[$i] == 'Н' || $idC[$i] == 'Г' || $idC[$i] == 'Ш' || $idC[$i] == 'Щ' || $idC[$i] == 'З' || $idC[$i] == 'Х' || $idC[$i] == 'Ъ' || $idC[$i] == 'Ф' || $idC[$i] == 'Ы' || $idC[$i] == 'В' || $idC[$i] == 'А' || $idC[$i] == 'П' || $idC[$i] == 'Р' || $idC[$i] == 'О' || $idC[$i] == 'Л' || $idC[$i] == 'Д' || $idC[$i] == 'Ж' || $idC[$i] == 'Э' || $idC[$i] == 'Я' || $idC[$i] == 'Ч' || $idC[$i] == 'С' || $idC[$i] == 'М' || $idC[$i] == 'И' || $idC[$i] == 'Т' || $idC[$i] == 'Ь' || $idC[$i] == 'Б' || $idC[$i] == 'Ю' || $idC[$i] == 'й' || $idC[$i] == 'ц' || $idC[$i] == 'у' || $idC[$i] == 'к' || $idC[$i] == 'е' || $idC[$i] == 'н' || $idC[$i] == 'г' || $idC[$i] == 'ш' || $idC[$i] == 'щ' || $idC[$i] == 'з' || $idC[$i] == 'х' || $idC[$i] == 'ъ' || $idC[$i] == 'ф' || $idC[$i] == 'ы' || $idC[$i] == 'в' || $idC[$i] == 'а' || $idC[$i] == 'п' || $idC[$i] == 'р' || $idC[$i] == 'о' || $idC[$i] == 'л' || $idC[$i] == 'д' || $idC[$i] == 'ж' || $idC[$i] == 'э' || $idC[$i] == 'я' || $idC[$i] == 'ч' || $idC[$i] == 'с' || $idC[$i] == 'м' || $idC[$i] == 'и' || $idC[$i] == 'т' || $idC[$i] == 'ь' || $idC[$i] == 'б' || $idC[$i] == 'ю' || $idC[$i] == '"' || $idC[$i] == '0') {
            $stroka = $stroka . "" . $idC[$i];
        }
    }
    
    $stroka = $str;
    
    $stroka = str_replace('[b]', '<b>', $stroka);
    $stroka = str_replace('[/b]', '</b>', $stroka);
    $stroka = str_replace('[i]', '<i>', $stroka);
    $stroka = str_replace('[/i]', '</i>', $stroka);
    $stroka = str_replace('[u]', '<u>', $stroka);
    $stroka = str_replace('[/u]', '</u>', $stroka);
    $stroka = str_replace('[url=', '<a href=', $stroka);
    $stroka = str_replace('[img src=', '<IMG SRC=', $stroka);
    $stroka = str_replace('[/url]', '</a>', $stroka);
    $stroka = str_replace('[/img]', '>', $stroka);
    $stroka = str_replace('[size=2]', '<FONT SIZE=3>', $stroka);
    $stroka = str_replace('[size=1]', '<FONT SIZE=2>', $stroka);
    $stroka = str_replace('[size=0.5]', '<FONT SIZE=1>', $stroka);
    $stroka = str_replace('[/size]', '</FONT>', $stroka);
    $stroka = str_replace('[list]', '<UL><LI>', $stroka);
    $stroka = str_replace('[/list]', '</UL>', $stroka);
    $stroka = str_replace('[list=1]', '<OL><LI>', $stroka);
    
    $stroka = str_replace('[quote]', '<FONT SIZE=2 COLOR=#000099>', $stroka);
    $stroka = str_replace('[php]', '< ?php', $stroka);
    $stroka = str_replace('[/php]', '?>', $stroka);
    
    $stroka = str_replace('[/quote]', '</FONT>', $stroka);
    $stroka = str_replace('[*]', '<LI>', $stroka);
    $stroka = str_replace('[br]', '<br>', $stroka);
    $stroka = str_replace('[/*]', '</LI>', $stroka);
    $stroka = str_replace('[code]', '<FONT SIZE=2 COLOR=#990000>', $stroka);
    $stroka = str_replace('[/code]', '</FONT>', $stroka);
    
    
    $stroka = str_replace(']', '>', $stroka);
    return $stroka;
}

function plust($strp, $k)
{
    $strpt = normal($strp);
    if ($strpt <> '') {
        $r = "&fil" . $k . "=" . $strpt;
    }
    return $r;
}

function plusti($strp, $k)
{
    $strpt = digitt($strp);
    if ($strpt <> '') {
        $r = "&fili" . $k . "=" . $strpt;
    }
    return $r;
}

function plustm($strp, $k)
{
    $strpt = normal($strp);
    if ($strpt <> '' or $strpt <> '0') {
        $r = $strpt;
    }
    return $r;
}


///////////////

function img_resize($src, $dest, $width, $height, $rgb = 0xFFFFFF, $quality = 95)
{
    if (!file_exists($src))
        return false;
    $size = getimagesize($src);
    if ($size === false)
        return false;
    
    // Определяем исходный формат по MIME-информации, предоставленной
    // функцией getimagesize, и выбираем соответствующую формату
    // imagecreatefrom-функцию.
    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
    $icfunc = "imagecreatefrom" . $format;
    if (!function_exists($icfunc))
        return false;
    
    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];
    
    $ratio       = min($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);
    
    $new_width  = $use_x_ratio ? $width : floor($size[0] * $ratio);
    $new_height = !$use_x_ratio ? $height : floor($size[1] * $ratio);
    $new_left   = $use_x_ratio ? 0 : floor(($width - $new_width) / 2);
    $new_top    = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);
    
    $isrc  = $icfunc($src);
    $idest = imagecreatetruecolor($width, $height);
    
    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);
    
    imagejpeg($idest, $dest, $quality);
    
    imagedestroy($isrc);
    imagedestroy($idest);
    
    return true;
    
}


$link = @mysql_connect($DB_Host, $DB_User, $DB_Pass) or die("Несмог подсоединиться к базе!");
mysql_select_db($DB_Name);

mysql_query_v('set NAMES utf8');
//mysql_query_v("SET CHARACTER SET 'utf8'");

//mysql_query_v('set NAMES  cp1251');
//mysql_query_v("SET CHARACTER SET ' cp1251'");

//АВТОРИЗАЦИЯ
// Начинаем сессию  
session_start();

$loginA = $_POST['login'];
$pasA   = $_POST['passw'];


if ($_GET['newsales'] == '1') {
    
   // $zapros = mysql_query_v("SELECT max(ID) as mid from ipad_userpass");
   // while ($linenA = mysql_fetch_array($zapros, MYSQL_ASSOC)) {
   //     $idMax = $linenA['mid'];
   // }
   // $idMax = $idMax + 1;
   // $dop   = date("ssmm");
   // //Добавим новую учетку в базу
   // $res   = mysql_query_v("INSERT INTO ipad_userpass (login, pass, prava) VALUES 
   // (\"" . $idMax . "\",\"" . $dop . "\",\"1\")");
    
    //Присвоим новые перемееные сессии
    $_POST['login'] = '';
    $_POST['passw'] = '';
    $_POST['enter'] = 'yes';
    
    $loginA = '';
    $pasA   = '';
    
    //echo "Авторегистрация".$idMax.$dop;
}

// Проверям были ли посланы данные  
if (!empty($_POST['enter'])) {
    $_SESSION['login'] = $loginA;
    $_SESSION['passw'] = $pasA;
}

$id   = 0;
$con  = 0;
$dost = 3;  
$sklepy = 0; //Магазин
// Проверям были ли посланы данные  
if (!empty($_SESSION['login']) and !empty($_SESSION['passw'])) {
    //Запрос на проверку логина и пароля если таковые есть
    $zapros = mysql_query_v("SELECT ID, prava, nameu, shopn from ipad_userpass WHERE (login='" . $_SESSION['login'] . "' and pass='" . $_SESSION['passw'] . "') or (REPLACE(Numer, '+', '')='" . str_replace('+', '', $_SESSION['login']) . "' and REPLACE(datar, '/', '')='" . $_SESSION['passw'] . "')");
    while ($linenA = mysql_fetch_array($zapros, MYSQL_ASSOC)) {
        $id   = $linenA['ID'];
        $dost = $linenA['prava'];
        $con  = $con + 1;
	$nameuq = $linenA['nameu'];
        $sklepy = $linenA['shopn'];
    }
}

if (!empty($_POST['enter'])) {
    if ($con == 1) {
        $_SESSION['login'] = $loginA;
        $_SESSION['passw'] = $pasA;
        $_SESSION['theme'] = rand(1, 8);
        
    }
}

if (!empty($_SESSION['login']) and $con == 1) {
    $loginA = $_SESSION['login'];
    $pasA   = $_SESSION['passw'];
    $aut    = 1; //Успешная авторизация
} else {
    $aut  = 0; //Не авторизован
    $dost = 3; //Права доступа зверь!
}


//Страницы
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = "1";
}
if ($page == "0" or $page == "") {
    $page = "1";
} //else {$page=abs($page);}
if (!ctype_digit($page)) {
    print "Próbując przełamać!Strona może być tylko liczba!";
    return 0;
}


//Текущий выбор
if (GP_v($_GET['KeyKat']) <> '') {
    $tekKey = GP_v($_GET['KeyKat']);
} else {
    if (GP_v($_POST['KeyKat']) <> '') {
        $tekKey = GP_v($_POST['KeyKat']);
    } else {
        $tekKey = 0;
    }
}


$uriven = "";
$lastKN = 0;

if ($_GET['reg'] == '1') {
    $registr = 1;
} else {
    $registr = 0;
}

if ($_GET['badpass'] == '1') {
    $badpass = 1;
} else {
    $badpass = 0;
}



function salesDcalc($w, $kurszleur) //вес
{
    if ($w <= 0.5) {
        return $result = round(130 * $kurszleur, 2);
    }
    if ($w <= 1) {
        return $result = round(134 * $kurszleur, 2);
    }
    if ($w <= 2) {
        return $result = round(140 * $kurszleur, 2);
    }
    if ($w <= 3) {
        return $result = round(145 * $kurszleur, 2);
    }
    if ($w <= 4) {
        return $result = round(150 * $kurszleur, 2);
    }
    if ($w <= 5) {
        return $result = round(156 * $kurszleur, 2);
    }
    if ($w <= 6) {
        return $result = round(162 * $kurszleur, 2);
    }
    if ($w <= 7) {
        return $result = round(170 * $kurszleur, 2);
    }
    if ($w <= 8) {
        return $result = round(178 * $kurszleur, 2);
    }
    if ($w <= 9) {
        return $result = round(186 * $kurszleur, 2);
    }
    if ($w <= 10) {
        return $result = round(194 * $kurszleur, 2);
    }
    if ($w <= 11) {
        return $result = round(202 * $kurszleur, 2);
    }
    if ($w <= 12) {
        return $result = round(212 * $kurszleur, 2);
    }
    if ($w <= 13) {
        return $result = round(222 * $kurszleur, 2);
    }
    if ($w <= 14) {
        return $result = round(232 * $kurszleur, 2);
    }
    if ($w <= 15) {
        return $result = round(245 * $kurszleur, 2);
    }
    if ($w <= 16) {
        return $result = round(258 * $kurszleur, 2);
    }
    if ($w <= 17) {
        return $result = round(272 * $kurszleur, 2);
    }
    if ($w <= 18) {
        return $result = round(286 * $kurszleur, 2);
    }
    if ($w <= 19) {
        return $result = round(300 * $kurszleur, 2);
    }
    if ($w <= 20) {
        return $result = round(320 * $kurszleur, 2);
    }
}



?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>rynek</title>
<link rel="SHORTCUT ICON" href="favicon.ico" type="image/x-icon">

<meta name=keywords CONTENT="Przechowuj swoje zakupy.">
<meta name=description CONTENT="Rynek z gwarancją dostawy towarów">
<meta name = "format-detection" content = "telephone=no" />

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="/css/stylepl.css">
<link rel="stylesheet" type="text/css" href="/css/addkont.css" />
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/swfobject.js"></script>
<script type="text/javascript" src="/js/jquery.markitup.pack.js"></script>
<script type="text/javascript" src="/js/set.js"></script> 
<script type="text/javascript" src="/js/markitup.js"></script>
<script type="text/javascript" src="/js/ieloader.js"></script>

<link rel="stylesheet" href="/css/fancy.css" type="text/css" media="screen" />
<link rel="stylesheet" href="/js/droppy.css" type="text/css" media="screen">
<script type="text/javascript" src="/js/block.js"></script>
<script type="text/javascript" src="/js/fancybox.js"></script>
<script type="text/javascript" src="/js/droppy.js"></script>

<script type="text/javascript"
          src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.3/mootools.js">
</script>
        
<script>
var i = 1;
function addElement(_this) {
var tr = document.getElementById('myFile'+i);
if (tr.value=='')
{ abort;
}
var uf = document.getElementById('upform');

// Создаём новое поле для выбора файла
var newFile = document.createElement('input');
var newbr = document.createElement('br');

i=i+1;
var fileIdName = 'myFile'+i; //Случайное имя

newFile.setAttribute('id',fileIdName);
newFile.setAttribute('name',fileIdName);
newFile.setAttribute('type','file');
newFile.setAttribute('size','50');

// при выборе файла данное поле тоже должно добавлять еще одно поле
newFile.setAttribute('onChange','addElement(this)');
newFile.onchange = addElement;

uf.appendChild(newFile); // добавляем поле к форме, поле добавится в конец формы, потому мы ранее
uf.appendChild(newbr); // добавляем поле к форме, поле добавится в конец формы, потому мы ранее
 

}
</script>


<script>
function resizeArea(text_id)
{
var text=document.getElementById('comment_text').value; //А дальше не ясно
var text_time=document.form.sel_tim.value; //А дальше не ясно
if (document.form.sp_cd[0].checked==true) {
var text2='<b>Odbiór osobisty w sklepie</b>';} else{
var text2='<b>Dostawa do domu o</b> '+text_time;
}

document.getElementById('new_url1').href = 'http://polskiezakupy.pl/index.php?vievcart=1&confirmz=1&msg='+text+'<br>'+text2;
document.getElementById('new_url2').href = 'http://polskiezakupy.pl/index.php?vievcart=1&confirmz=1&msg='+text+'<br>'+text2;
document.getElementById('new_url3').href = 'http://polskiezakupy.pl/index.php?vievcart=1&confirmz=1&msg='+text+'<br>'+text2;
document.getElementById('new_url4').href = 'http://polskiezakupy.pl/index.php?vievcart=1&confirmz=1&msg='+text+'<br>'+text2;
}


// back to top script
window.addEvent('domready', function() {
	// hide #jm-back-top first
	document.id('jm-back-top').fade('hide');
	// fade in #jm-back-top
	window.addEvent('scroll', function() {
		if (window.getScroll().y > 100) {
			document.id('jm-back-top').fade('in');
		} else {
			document.id('jm-back-top').fade('out');
		}
	});
	// scroll body to 0px on click
	document.id('jm-back-top').addEvent('click', function() {
		scroll = new Fx.Scroll(window);
		scroll.toTop();
	});
});

</script>





</head>

<?php
if ($_SESSION['theme'] == '') {
    $theme = rand(1, 8);
} else
    $theme = $_SESSION['theme'];

?>

<body bgcolor="#ffffff" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">




<script src="jquery.js" type="text/javascript" language="javascript"></script>
      <script language="javascript">
      
      $(document).ready(function()
      {
$("#login_new").blur(function()
{
$("#msgbox").removeClass().addClass('messagebox').text('sprawdzenie...').fadeIn("slow");
//Проверить существует ли имя
$.post("user_availability.php",{ user_name:$(this).val() } ,function(data)
{
if(data=='no') //если имя не доступно
{
$("#msgbox").fadeTo(200,0.1,function() //начнет появляться сообщение
{ 
$(this).html('Ta nazwa jest już zajęta').addClass('messageboxerror').fadeTo(900,1);
}); 
}
else
{
$("#msgbox").fadeTo(200,0.1,function() 
{ 
//тут прописывается сообщение о доступности имени
$(this).html('Nazwa jest dostępna do rejestracji').addClass('messageboxok').fadeTo(900,1); 
});
}

});

});
});
</script>
<center>

<?php

if (($tekKey < 10) AND (!isset($_GET['vievcart'])) AND (!isset($_GET['konto'])))
 {$dp='max-height: 769px;';} else
{$dp='';}

?>
<div id="abb" style="padding: 0px 0px 0px 0px; max-width:1020px; <?php echo $dp; ?>">
<?php

//if (($aut == 0) and ($_GET['autus'] <> '1')) {
//    echo "<div id='hgreen'><center><A class='link_small_black' HREF=index.php?autus=1>Wejście</a></center></div>";
//}


//Курс
$kursd = mysql_query_v("SELECT kurs FROM ipad_kurs");
while ($kursp = mysql_fetch_array($kursd, MYSQL_ASSOC)) {
    $kurs = $kursp['kurs'];
}


//Удаление объявления
//Если редактируем то наверно нужно сюда зайти!
if (isset($_GET['deadlink'])) {
    $iddead = digitt($_GET['deadlink']);
    if ($iddead > 0) {
        //Проверим кто создал объявление
        $deadpra = mysql_query_v("SELECT userid from ipad_tovar where id=" . $iddead);
        while ($deadspra = mysql_fetch_array($deadpra, MYSQL_ASSOC)) {
            $desid = $deadspra['userid'];
        }
        
        
        if ($aut == 1) {
            if ($desid == $id) {
                //Можно начинать
                // снесем фотки прикрепленные к объявлению
                $dfoto = mysql_query_v("SELECT link, linksm from ipad_fototovar where idtovar=" . $iddead);
                while ($linedeadfoto = mysql_fetch_array($dfoto, MYSQL_ASSOC)) {
                    // echo "<tr><td align='right'>";
                    $adrfboolde  = "" . str_replace('C:/wamp/www/', '', $linedeadfoto['link']);
                    $adrfboolde  = "" . str_replace('home/aladyha/domains/polskiezakupy.pl/public_html/', '', $linedeadfoto['link']);
                    $adrfsmallde = "" . str_replace('C:/wamp/www/', '', $linedeadfoto['linksm']);
                    $adrfsmallde = "" . str_replace('home/aladyha/domains/polskiezakupy.pl/public_html/', '', $linedeadfoto['linksm']);
                    
                    //Физически постираем фотки
                    removefile($adrfboolde);
                    removefile($adrfsmallde);
                }
                
                //Поудаляем в таблицах фоток
                mysql_query_v("DELETE from ipad_fototovar where idtovar=" . $iddead);
                //Поудаляем в таблицах объявлений
                mysql_query_v("DELETE from ipad_tovar where id=" . $iddead);
                
                echo warn("Operacja usuwania jest zakończony pomyślnie!");
                $tov = $tov - 1;
            } else {
                echo warn("Usuwanie anulowane! Stanowisko to nie zostało utworzone przez Ciebie.<br>");
            }
        } else {
            echo warn("Usuwanie anulowane!problem zezwolenia.<br>");
        }
    }
}




//Добавление товара в корзину
if (isset($_POST['addcart'])) {
    if ($_POST['addcart'] == '1') {
        if (isset($_GET['addcviev'])) {
            $idtv = GP_v($_GET['addcviev']);
            if ($idtv > 0) {
                
                $adding = digitt($_POST['kolvo']);
                if ($adding <= 0) {
                    $adding = 1;
                }
                
                //Узнаем сколько товара у нас уже есть
                $sales_tov_cnt_res = mysql_query_v("SELECT sum(amount) as amount from ipad_usersales where userid=" . $id . " and state=0 and totalpay=0 and id_tovar=" . $idtv);
                while ($sales_tcnt_r = mysql_fetch_array($sales_tov_cnt_res, MYSQL_ASSOC)) {
                    $sales_tov_cnt_res_amount = $sales_tcnt_r[amount];
                }
                
                if ($sales_tov_cnt_res_amount == 0) {
                    $res        = mysql_query_v("INSERT INTO ipad_usersales (userid, state, id_tovar, amount, totalpay, date_add) VALUES (\"" . $id . "\",0,\"" . $idtv . "\"," . $adding . ",0, NOW())");
                    $messaddtov = 'Dodano do koszyka ' . $adding . ' pozycja(e). Można wyświetlić listę towarów<br>klikając "<A class="link_small_black" HREF=index.php?vievcart=1>Koszyk</a>"';
                } else {
                    $sales_tov_cnt_res_amount = $sales_tov_cnt_res_amount + $adding;
                   // if ($sales_tov_cnt_res_amount > 3) {
                   //     $sales_tov_cnt_res_amount = 3;
                   // }
                    //echo $sales_tov_cnt_res_amount;
                    $res = mysql_query_v("Update ipad_usersales set amount=" . $sales_tov_cnt_res_amount . " where userid=" . $id . " and state=0 and totalpay=0 and id_tovar=" . $idtv);
                    
                    $messaddtov = "Zmieniono ilość towarów w koszyku (+" . $adding . ")";
                }
                
                
                if ($messaddtov <> '') {
                    echo warn2($messaddtov);
                }
                
                
                
            }
        }
    }
}



//Добавление товара в корзину через ссылку
if (isset($_GET['addcart'])) {
    if ($_GET['addcart'] == '1') {
        if (isset($_GET['addcviev'])) {
            $idtv = GP_v($_GET['addcviev']);
            if ($idtv > 0) {
                
                $adding = digitt($_GET['kolvo']);
                if ($adding < 0) {
                    $adding = 0;
                }
                
                //Узнаем сколько товара у нас уже есть
                $sales_tov_cnt_res = mysql_query_v("SELECT sum(amount) as amount from ipad_usersales where userid=" . $id . " and state=0 and totalpay=0 and id_tovar=" . $idtv);
                while ($sales_tcnt_r = mysql_fetch_array($sales_tov_cnt_res, MYSQL_ASSOC)) {
                    $sales_tov_cnt_res_amount = $sales_tcnt_r[amount];
                }
                
                if (($adding == 0) and ($sales_tov_cnt_res_amount>0)) {
                    $res = mysql_query_v("DELETE from ipad_usersales where userid=" . $id . " and state=0 and id_tovar=".$idtv."");
                    $messaddtov = 'Pozycja usunięta';
                } 

				if ($sales_tov_cnt_res_amount == 0) {
                    $res        = mysql_query_v("INSERT INTO ipad_usersales (userid, state, id_tovar, amount, totalpay, date_add) VALUES (\"" . $id . "\",0,\"" . $idtv . "\"," . $adding . ",0, NOW())");
                    $messaddtov = 'Dodano do koszyka ' . $adding . ' pozycja(e). Można wyświetlić listę towarów<br>klikając "<A class="link_small_black" HREF=index.php?vievcart=1>Koszyk</a>"';
                }

                if (($adding>0) and ($sales_tov_cnt_res_amount <> 0))
                 {
					if ($adding<$sales_tov_cnt_res_amount) {
					$sales_tov_cnt_res_amount = $sales_tov_cnt_res_amount - 1; 
					} else {
                    $sales_tov_cnt_res_amount = $sales_tov_cnt_res_amount + 1; }
                    //if ($sales_tov_cnt_res_amount > 3) {
                    //    $sales_tov_cnt_res_amount = 3;
                    // }
                    //echo $sales_tov_cnt_res_amount;
                    $res = mysql_query_v("Update ipad_usersales set amount=" . $sales_tov_cnt_res_amount . " where userid=" . $id . " and state=0 and totalpay=0 and id_tovar=" . $idtv);
                    
                    $messaddtov = "Zmieniono Cena towarów w koszyku (" . $sales_tov_cnt_res_amount . ")";
                } 


                
                
                if ($messaddtov <> '') {
                    echo warn2($messaddtov);
                }
                
                
                
            }
        }
    }
}



//Добавление категорий
if ($_GET['kat'] == '1' and $aut == 1) {
    if ($dost <> 0) {
        exit;
    } //Никто кроме админа
    if ($_POST['Name'] <> "") {
        //Загрузим картинку (после)
        
        if ($_POST['Id'] == "") {
            if ($tekKey == 0) {
                $link = "|0|";
            } else { //Небольшой запрос чтоб узнать историю
                $rlin = mysql_query_v("SELECT link FROM ipad_menu WHERE Id=" . $tekKey);
                while ($rlink = mysql_fetch_array($rlin, MYSQL_ASSOC)) {
                    $link = $rlink[link];
                }
                $link = $link . "" . $tekKey . "|";
            }
            if ($dost == '0') {
                $tads = '1';
            } else { {
                    $tads = '1';
                }
            }
            $res = mysql_query_v("INSERT INTO ipad_menu (Name, KeyKat, idP, link, testacsess, imagelink, priority, sklepy) VALUES (\"" . normal(e($_POST['Name'])) . "\",\"" . $tekKey . "\",\"" . $id . "\",\"" . $link . "\",\"" . $tads . "\",\"" . $tekKey . "." . $ext . "\",\"" . normal($_POST['priority']) . "\",".$sklepy.")");
            
            
            $rlin2 = mysql_query_v("SELECT max(Id) as k FROM ipad_menu");
            while ($rlink2 = mysql_fetch_array($rlin2, MYSQL_ASSOC)) {
                $tekKeyAdd = $rlink2[k];
            }
            
        } else {
            $tekKeyAdd = $tekKey;
        }
        
        
        
        
        $tmpn = 0;
        foreach ($_FILES as $file) {
            
            if (strlen($file["name"]) <= 0)
                continue;
            $tmpn     = $tmpn + 1;
            $new      = $new . '' . $tmpn;
            // Процедура добавления ->
            $fotoname = $file["name"]; // определяем имя файла
            $fotosize = $file["size"]; // Запоминаем размер файла
            
            if ($fotoname <> "") {
                $ext = strtolower(substr($fotoname, 1 + strrpos($fotoname, ".")));
                if (!in_array($ext, $valid_types)) {
                    echo warn('<B>Plik nie ZAŁADOWANE!</b> <br>Zezwalaj na pobieranie tylko pliki z rozszerzeniami: <b>gif, jpg, jpeg, png</b><br><br><A HREF="index.php?KeyKat=' . $tekKey . '">Kliknij tutaj, aby powrócić</A>');
                    exit;
                }
                $dir = '.'; //Если директория не указана возмем за корневую текущую
                //Директория
                if (is_dir($dir)) {
                    chdir($dir . '/images/kategory');
                    $basedir = getcwd();
                    $basedir = str_replace('\\', '/', $basedir);
                    //       echo "<br>".$basedir;
                }
                
                
                copy($file["tmp_name"], $basedir . "/" . $tekKeyAdd . "." . $ext);
            }
            
        }
        
        
        //<Карт
        
        
        //Проверка первичное добавление или после обновления
        {
            if ($dost == 0) {
                $ta = 2;
            } else {
                $ta = 1;
            }
            
            
            
            if ($ext <> '') {       
  
                
             if ($sklepy==1) {
$sqlUpdT = "UPDATE ipad_menu SET Name=\"" . e($_POST['Name']) . "\", testacses=\"" . $ta . "\", imagelink=\"" . $tekKeyAdd . "." . $ext . "\",  priority=\"" . $_POST['priority'] . "\",  sklepy=\"" . $_POST['sklepy'] . "\" WHERE id=" . $tekKeyAdd;   
             } else {   
             
$sqlUpdT = "UPDATE ipad_menu SET Name=\"" . e($_POST['Name']) . "\", testacses=\"" . $ta . "\", imagelink=\"" . $tekKeyAdd . "." . $ext . "\",  priority=\"" . $_POST['priority'] . "\" WHERE id=" . $tekKeyAdd;
}

                
                //echo $sqlUpdT;
                
                $res = @mysql_query_v($sqlUpdT);
                
                
            } else {

   if ($sklepy==1) {
                $res = @mysql_query_v("UPDATE ipad_menu SET Name=\"" . e($_POST['Name']) . "\", testacses=\"" . $ta . "\", priority=\"" . $_POST['priority'] . "\", sklepy=\"" . $_POST['sklepy'] . "\" WHERE id=" . $tekKeyAdd); } else {
$res = @mysql_query_v("UPDATE ipad_menu SET Name=\"" . e($_POST['Name']) . "\", testacses=\"" . $ta . "\", priority=\"" . $_POST['priority'] . "\" WHERE id=" . $tekKeyAdd);
             }
            }
        }
        
    }
    
    //****** Добавление фильтров в категорию
    if (isset($_GET['addfilter'])) {
        if ($_GET['addfilter'] == '1') {
            // Режим добавления
            $idMenuf = normal($_POST['Idmenu']);
            $namef1  = normal($_POST['Name1']);
            $pole1   = normal($_POST['pole1']);
            $namef2  = normal($_POST['Name2']);
            $pole2   = normal($_POST['pole2']);
            $namef3  = normal($_POST['Name3']);
            $pole3   = normal($_POST['pole3']);
            $namef4  = normal($_POST['Name4']);
            $pole4   = normal($_POST['pole4']);
            $namef5  = normal($_POST['Name5']);
            $pole5   = normal($_POST['pole5']);
            $namef6  = normal($_POST['Name6']);
            $pole6   = normal($_POST['pole6']);
            $namef7  = normal($_POST['Name7']);
            $pole7   = normal($_POST['pole7']);
            $namef8  = normal($_POST['Name8']);
            $pole8   = normal($_POST['pole8']);
            $namef9  = normal($_POST['Name9']);
            $pole9   = normal($_POST['pole9']);
            $namef10 = normal($_POST['Name10']);
            $pole10  = normal($_POST['pole10']);
            
            
            //Если ключик найдем то редактирование
            $idf      = '';
            $idfilter = mysql_query_v("select Id from ipad_filter where menuid=" . $idMenuf . " limit 1");
            while ($filterid = mysql_fetch_array($idfilter, MYSQL_ASSOC)) {
                $idf = $filterid['Id'];
            }
            if ($idf <> '') {
                echo "Обновление информации";
                $res = @mysql_query_v("UPDATE ipad_filter SET name1=\"" . $namef1 . "\", name2=\"" . $namef2 . "\", name3=\"" . $namef3 . "\", name4=\"" . $namef4 . "\", name5=\"" . $namef5 . "\", name6=\"" . $namef6 . "\", name7=\"" . $namef7 . "\", name8=\"" . $namef8 . "\", name9=\"" . $namef9 . "\", name10=\"" . $namef10 . "\", poletip1=\"" . $pole1 . "\", poletip2=\"" . $pole2 . "\", poletip3=\"" . $pole3 . "\", poletip4=\"" . $pole4 . "\", poletip5=\"" . $pole5 . "\", poletip6=\"" . $pole6 . "\", poletip7=\"" . $pole7 . "\", poletip8=\"" . $pole8 . "\", poletip9=\"" . $pole9 . "\", poletip10=\"" . $pole10 . "\" WHERE id=" . $idf);
            } else {
                echo "Добавление информации";
                $res = mysql_query_v("INSERT INTO ipad_filter (menuid, name1, name2, name3, name4, name5, name6, name7, name8, name9, name10, poletip1, poletip2, poletip3, poletip4, poletip5, poletip6, poletip7, poletip8, poletip9, poletip10) VALUES (\"" . $idMenuf . "\",\"" . $namef1 . "\",\"" . $namef2 . "\",\"" . $namef3 . "\",\"" . $namef4 . "\",\"" . $namef5 . "\",\"" . $namef6 . "\",\"" . $namef7 . "\",\"" . $namef8 . "\",\"" . $namef9 . "\",\"" . $namef10 . "\",\"" . $pole1 . "\",\"" . $pole2 . "\",\"" . $pole3 . "\",\"" . $pole4 . "\",\"" . $pole5 . "\",\"" . $pole6 . "\",\"" . $pole7 . "\",\"" . $pole8 . "\",\"" . $pole9 . "\",\"" . $pole10 . "\")");
            }
            
            
            //            echo "1";
            
        }
    }
    
    
}










//Тут Мое конто
if (($tekKey == 0) and ((isset($_GET['usid'])) or (isset($_GET['konto'])))) {
    
    
    echo "<table width='100%' border='0'>";
    echo "<tr height='40px' bgcolor='#FFFFFF'>";

    echo '<td width="7%"><center><a href="#" onclick="history.back();return false;"><img src="images/back.jpg" height="33px" border=0></a></center></td><td width="86%"><center><font size="4">Moje Konto: '.$nameuq.'</font>';
    echo '</center><a name="up">';
    echo "</td><td width='7%'></td>";
    echo "</tr>";
    echo "<tr height='30px'  bgcolor='#FF0000'>";
    echo '<td colspan="3">';

	
   $rlin = mysql_query_v("SELECT Id, Name from `ipad_menu` order by Id limit 1");
    while ($rlink = mysql_fetch_array($rlin, MYSQL_ASSOC)) {
        $linkid    = $rlink[Id];
        $linksName = $rlink[Name];
    }
    
    if ($aut <> 0) {
        $addns = '';
    } else {
    }


    echo '<div style="opacity: 0.9; display: none;">';
    echo '		<div id="inline1" style="width:400px;height:100px;overflow:auto;">';
    echo '<center><strong>Pomoc</strong></center><br><br>Aby uzyskać pomoc, skontaktuj się z obsługą sklepu lub zadzwoń: +48 537 756 984<br><br>';
    echo 'Ponadto, można znaleźć więcej informacji na dole strony.<br><br><center><a href="javascript:;" onclick="$.fancybox.close();">Zamknij okno</a></center>';
    echo '		</div>';
    echo '	</div>';


    echo '<nav id="globalheader" class="apple globalheader-js noinset svg globalheader-loaded">';
    echo '<ul id="globalnav" role="navigation">';
    echo '<li id="gn-apple"><a href="#" class=""><span><center>Moje Dane</center></span></a></li>';
    echo '<li id="gn-apple"><a href="?KeyKat=5" class=""><span><center>Historia zamówień</center></span></a></li>';
    echo '<li id="gn-ipod" ><a id="various1" href="#inline1"><span><center>Pomoc</center></span></a></li>';
    echo '<li id="gn-mac"><a href="?KeyKat=0&amp;newsales=1" class=""><span><center>Wyjście</center></span></a></li>';//
   
	echo '</ul>';
    echo '</nav>';	
	
	echo "</td>";
    echo "</tr>";
    echo "</table>";
    
    
    echo "<table height='100%' width='100%' border='0'>";
    echo "<tr>";
    
    //Левая навигация
    echo "<td width='130px' valign='top'>";
    

    echo "<table class='data-table2' width='100%'>";
echo "<tr>";
    echo "<td><a href='/?KeyKat=" . $glstr . "'><img src='images/new.jpg' border=0><br><center><b>Strona główna</b></a></center>";
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'>";
    
    if ($_GET['questn'] == "1") {
        if ($_GET['konfirm'] == "1") {
            if ($id > 0) {
                $sqldrey = "DELETE from ipad_usersales where userid=" . $id . " and state=0";
                mysql_query_v($sqldrey);
            }
        }
        if ($_GET['konfirm'] <> "1") {
            echo "<br><br><br><center><b>Opróżić koszyk?</b><br><br>";
            
            
            
            echo "<a href='index.php?KeyKat=0&newsales=1'><FONT SIZE='4'><b>Tak</b></FONT></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?vievcart=1'><FONT SIZE='4'><b>Nie</b></FONT></a>";
            
            
            echo "<br><br></center><br>";
        } else {
            echo "<br><br><center><b>Twój koszyk jest wyczyszczone</b><br><br></center><br><br><br>";
        }
    } else {
        echo "<a href='index.php?vievcart=1&questn=1'><center><img src='images/new_f.jpg' border=1></center><br><center><b>Nowe zamówienie</b></a></center>";
    }
    echo "</td>";
    echo "</tr>";
    
    echo "</table>";
    echo "<table>";
    
    echo "<tr>";
    echo "<td>";
    
    echo "<table width='100%' class='data-table2'>";
    echo "<tr>";
    echo "<td><center><a href='/?KeyKat=" . $glstr . "&find'><img src='images/find.jpg' border=0><br><b>Szukaj...</b></a></center>";
    echo "</td>";
echo "</tr>";

    echo "</table>";
    
    echo "</td>";
    echo "</tr>";
    
    
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'>";
    
    
    if ($id > 0) {
        $ressoob2 = mysql_query_v("SELECT count(*) as cnt FROM ipad_usersales WHERE userid = " . $id . " ");
        while ($ressoobr2 = mysql_fetch_array($ressoob2, MYSQL_ASSOC)) {
            $ressoobk2 = $ressoobr2['cnt'];
        }
        //Сумма заказов из корзины		
        $ressumr = mysql_query_v("SELECT sum(round(ifnull(it.price,0)*ifnull(iu.amount,0),2)) as summf FROM ipad_usersales iu, ipad_tovar it WHERE iu.userid = " . $id . " AND iu.id_tovar = it.Id ");
        
        while ($ressum = mysql_fetch_array($ressumr, MYSQL_ASSOC)) {
            $ressumf = $ressum['summf'];
        }
        
        if ($ressumf == null) {
            $ressumf = 0;
        }
        
        
        //Вес		
        $weigthtr = mysql_query_v("SELECT count(*) as cnt, sum(round(ifnull(iu.amount,0),2)) as weigthtf, round(sum(ifnull(it.price,0)*ifnull(iu.amount,0)),2) as sumt, max(iu.ID) as ID FROM ipad_usersales iu, ipad_tovar it WHERE iu.userid = " . $id . " AND iu.id_tovar = it.Id and (iu.state=0)");
        while ($weigthtm = mysql_fetch_array($weigthtr, MYSQL_ASSOC)) {
            $weigthtf = $weigthtm['cnt'];
			$idfg = $weigthtm['ID'];
			$ressumf = $weigthtm['sumt'];
        }
        if ($weigthtf == null) {
            $weigthtf = 0;
			$idfg = '';
			$ressumf = 0;

        }
        
    } else {
        $ressoobk2 = 0;
        $ressumf   = 0;
        $weigthtf  = 0;
		$idfg = 0;
    }
    
    
    
    
    echo "<table width='100%' class='data-table2'>";
    echo "<tr>";
    echo "<td><center>Koszyk:</center>";
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Zam. nr: <b>" . $idfg . "</b>";
	echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>w złotych: <b>" .$ressumf . "</b>";
        echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Ilość: <b>" . round($weigthtf) . "</b>";
    echo "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td>";
    
    echo "<center>";
    echo "";
    echo "<a id='new_url4' href='index.php?vievcart=1'><img src='images/pen.jpg' border='0'></a>";
//
    echo "";
    echo "";
    echo "";
   // echo "<td bgcolor='#9ccb12'><center><a id='new_url1' href='index.php?vievcart=1&confirmz=1'></a></center>";

 echo "<a id='new_url1' href='index.php?vievcart=1&confirmz=1'><center><b>Koszyk</b></center></a>";
    echo "";
    echo "";
    echo "</center>";
    
    echo "</td>";
    echo "</tr>";
    
    echo "</table>";
    
    
    
    echo "</td>";
    echo "</tr>";
    
    echo "</table>";
    
    echo "</td>";
    
    //Центральная
    echo "<td valign='top'>";
    
   
    
    if ($_GET['confirmz'] == "1") {
        
        $sqlTz2 = "SELECT * FROM ipad_usersales iu, ipad_tovar it WHERE iu.id_tovar = it.Id order by iu.id desc limit 20";
        
        // echo $sqlTz;
        
        
        $message = "<B>Zamówienie Nr: " . $loginA . "</B><BR>" . $cntmsg . "<BR>".$cntmsg2."<br><center><table border='1'>";
        $message = $message . "<tr><td><b>№</b></td><td><b>Zdjęcie</b></td><td><b>Shop</b></td><td><b>Nazwa produktu</b></td><td><b>Liczba</b></td><td width='120px'><b>Cena</b></td></tr>";
        
        $kntS   = 1;
        $SelTz2 = mysql_query_v($sqlTz2);
        $massa  = 0;
        $allpr  = 0;
        if ($SelTz2 <> null) {
            while ($SelTzp2 = mysql_fetch_array($SelTz2, MYSQL_ASSOC)) {
                $massa   = $massa + digitt($SelTzp2['weigtht']);
                $allpr   = $allpr + $SelTzp2['summf'];
				$id_tovar_m = $SelTzp2['id'];
                $message = $message . "<tr><td>" . $kntS . "</td><td>" . $SelTzp2['barcode'] . "</td><td>" . $SelTzp2['Shopn'] . "</td><td>" . $SelTzp2['Name'] . " (" . $SelTzp2['hr'] . ")</td><td>" . $SelTzp2['amount'] . "</td><td>" . $SelTzp2['summf'] . " Zł</td></tr>";
                
                $kntS = $kntS + 1;
            }
            //$allpr = $allpr + salesDcalc($massa, $kurszleur);
        }
        $message = $message . "</table></center>";
        
        if ($kntS <> 1) {
            //Обновим статус 
            $sqlupst = "update ipad_usersales set state=1, date_doc=now() where userid=" . $id . " and state=0";
           mysql_query_v($sqlupst);
            
            //отправим емайл
           // echo $message;
           mail($to, $subject, $message, $headers);
            
            
            echo "<table width='98%' border='0'>";
            echo "<tr>";
            echo "<td width='50%'>";
            echo "</td>";
            
            echo "<td>";
            echo "<table border='0'>";
            echo "<tr>";
            echo "<td>";
            echo "</td>";
            echo "</tr>";
            
            echo "<tr>";
            echo "<td bgcolor='#9ccb12'><b></b>";
            echo "</td>";
            echo "</tr>";
            
            
            
            echo "</table>";
            echo "</td>";
            echo "</tr>";
            
            echo "</table>";
            
            echo "<br><br><br>";
            
            echo "<table width='98%' border='0'>";
            echo "<tr>";
            echo "<td>";
            echo "</td>";
            echo "</tr>";
            echo "</table>";
            
            echo "<br><br><br>";
            
            echo "<table width='98%' border='0'>";
            echo "<tr>";
            echo "<td>";
            echo "</td>";
            echo "</tr>";
            echo "</table>";
            
            echo "<br><br><br>";
            
            echo "<table width='98%' border='0'>";
            echo "<tr>";
            echo "<td>";
            echo "</td>";
            echo "</tr>";
            echo "</table>";
            
            echo "<br><br><br>";
            echo "<center><FONT SIZE='5' color='#009900'><b>Dziękujemy za zamówienie!</b></FONT></center>";
            
       } else {
            echo "<br><br><center><b>Koszyk jest niepotwierdzone zlecenia! Zrobić coś!</b></center>";
        }
        
    } else {
        
        echo "<br>&nbsp;&nbsp;<b>Najczęściej zamawiane produkty:</b><br><br>";
        
        
        //Удаление товара из корзины echo "---".salesDcalc(20);
        if ((GP_v($_GET['tdel']) > 0) and ($aut == 1) and ($id > 0)) {
            
            $sqldre = "DELETE from ipad_usersales where ID=" . GP_v($_GET['tdel'] . " and userid=" . $id . " and state=0");
            mysql_query_v($sqldre);
            
            echo Warn3("Produkt usunięty");
        }
        
        
        
        echo "<div id=allspec><p id='jm-back-top' style='display: block;'><a href='#top'><span></span>&nbsp;</a></p>";
        echo "<center><table width='98%' class='data-table'>";
        echo "<tr><td><b><center>Nr</center></b></td><td><b><center>Zdjęcie </center></b></td><td><b><center>Nazwa produktu</center></b></td><td><b><center>Liczba</center></b></td><td width='120px'><b><center>Cena</center></b></td><td><b><center>Akcja</center></b></td></tr>";
        

        $sqlTz = "SELECT iu.headerid, it.Id as Itdtv, ipf.linksm, ipf.link, iu.date_sales, iu.date_pay, iu.date_doc, iu.date_add, iu.totalpay, iu.amount, iu.id_tovar, iu.state, iu.id, it.Name, it.hr, round(ifnull(it.price,0),2) as summf, it.barcode, it.weigtht, iu.ID FROM ipad_usersales iu, ipad_tovar it, ipad_fototovar ipf WHERE iu.userid=" . $id . " and iu.id_tovar = it.Id and it.id=ipf.idtovar order by iu.ID desc limit 20";
        $SelTz = mysql_query_v($sqlTz);
        $kntS  = 1;



	   if ($SelTz == null) {


        ////////********
        $sqlTz = "SELECT iu.headerid, it.Id as Itdtv, ipf.linksm, ipf.link, iu.date_sales, iu.date_pay, iu.date_doc, iu.date_add, iu.totalpay, iu.amount, iu.id_tovar, iu.state, iu.id, it.Name, it.hr, round(ifnull(it.price,0),2) as summf, it.barcode, it.weigtht, iu.ID FROM ipad_usersales iu, ipad_tovar it, ipad_fototovar ipf WHERE  iu.id_tovar = it.Id and it.id=ipf.idtovar order by iu.ID desc limit 20";
        
       //  echo $sqlTz;

        $SelTz = mysql_query_v($sqlTz);
	   }

     //  echo $sqlTz;

        $massa = 0;
        $allpr = 0;
        if ($SelTz <> null) {
            while ($SelTzp = mysql_fetch_array($SelTz, MYSQL_ASSOC)) {


if ($local == 1) {
                        $adrfbool = str_replace('C:/wamp/www', '', $SelTzp['link']);
                    } else {
                        $adrfbool = str_replace('home/aladyha/domains/polskiezakupy.pl/public_html', '', $SelTzp['link']);
                    }
                    
                    if ($local == 1) {
                        $adrfsmall = str_replace('C:/wamp/www', '', $SelTzp['linksm']);
                    } else {
                        $adrfsmall = str_replace('home/aladyha/domains/polskiezakupy.pl/public_html', '', $SelTzp['linksm']);
                    }
                    $widhy = $smwidth / 2;
                    $hedhy = $smheight / 2;
                    
                    $adrfbool  = str_replace('/katalog', 'katalog', $adrfbool);
                    $adrfsmall = str_replace('/katalog', 'katalog', $adrfsmall);


$adrfsmall="<a class='gallery' rel='group' HREF='" . $adrfbool . "'><img class='photoramka' src='" . $adrfsmall . "' alt='' width='" . $widhy . "' height='" . $hedhy . "' border='0'></a>";



				$amn=$SelTzp['amount'];
                $amnp=$amn+1;
				$amnm=$amn-1;
				$id_tovar_m=$SelTzp['ID'];
                $massa = $massa + digitt($SelTzp['weigtht']);
                echo "<tr><td><center>" . $kntS . "</center></td><td>" . $adrfsmall . "</td><td>" . $SelTzp['Name'] . " (" . $SelTzp['hr'] . ")</td><td><center><table border='0'><tr><td>1</td></tr></table></center></td><td><center>" . $SelTzp['summf'] . " Zł</center></td><td><center>";
                
                if ($SelTzp['state'] == 0 or $dost == 0) {
                    echo "<a href='?addcart=1&addcviev=" . $SelTzp['Itdtv'] . "&kolvo=1&konto=1'><img src='images/add_cart3.jpg' border='0'><br>Dodaj</a>";
                } else {
                    echo "Zamówienie zostanie potwierdzone";
                }
                
                echo "</center></td></tr>";
                $allpr = $allpr + $SelTzp['summf'];
                $kntS  = $kntS + 1;
            }
            //$allpr = $allpr + salesDcalc($massa, $kurszleur);
            
            if (($kntS <> 1) and ($dostavka == 1)) {
                echo "<tr><td>" . $kntS . "</td><td>Koszt dostawy</td><td>" . $massa . " kg</td><td>" . salesDcalc($massa, $kurszleur) . "  Zł<br>" . salesDcalc($massa, $kurszleur) * $kurs . " Bel. trzeć.</td><td></td><td></td></tr>";
                
            }
        }
        
        
        echo "</table></center>";
        echo "</div>";
        
  
        
        echo "</td>";
        
        echo "</tr><TR><td colspan='3' height='140px'>";

echo '<footer class="main-footer"><div><ul class="main-footer-shortcuts">';
echo '<li><a href="?KeyKat=2">O nas</a></li>';
echo '<li><a href="?KeyKat=2">Praca</a></li>';
echo '<li><a href="?KeyKat=2">Twój Sklep</a></li>';
echo '<li><a href="?KeyKat=2">FAQ</a></li>';
echo '</ul></div></footer>';

        echo "</td></TR>";
        echo "</table>";
        echo "</center>";
        
        
    }



    echo "</td>";
    echo "</tr>";
    echo "</table>";
    

    
}












//Тут просмотр корзины
if (($tekKey == 0) and ((isset($_GET['usid'])) or (isset($_GET['vievcart'])))) {
    
    
    echo "<table width='100%' border='0'>";
    echo "<tr height='40px' bgcolor='#FFFFFF'>";

    echo '<td width="7%"><center><a href="#" onclick="history.back();return false;"><img src="images/back.jpg" height="33px" border=0></a></center></td><td width="86%"><center><font size="4">Koszyk</font>';
    echo '</center><a name="up">';
    echo "</td><td width='7%'></td>";
    echo "</tr>";
    echo "<tr height='30px'  bgcolor='#FF0000'>";
    echo '<td colspan="3">';

	
   $rlin = mysql_query_v("SELECT Id, Name from `ipad_menu` order by Id limit 1");
    while ($rlink = mysql_fetch_array($rlin, MYSQL_ASSOC)) {
        $linkid    = $rlink[Id];
        $linksName = $rlink[Name];
    }
    
    if ($aut <> 0) {
        $addns = '';
    } else {
    }


    echo '<div style="opacity: 0.9; display: none;">';
    echo '		<div id="inline1" style="width:400px;height:100px;overflow:auto;">';
    echo '<center><strong>Pomoc</strong></center><br><br>Aby uzyskać pomoc, skontaktuj się z obsługą sklepu lub zadzwoń: +48 537 756 984<br><br>';
    echo 'Ponadto, można znaleźć więcej informacji na dole strony.<br><br><center><a href="javascript:;" onclick="$.fancybox.close();">Zamknij okno</a></center>';
    echo '		</div>';
    echo '	</div>';


    echo '<nav id="globalheader" class="apple globalheader-js noinset svg globalheader-loaded">';
    echo '<ul id="globalnav" role="navigation">';
    echo '<li id="gn-apple"><a href="#" class=""><span><center>Moje Dane</center></span></a></li>';
    echo '<li id="gn-apple"><a href="?KeyKat=5" class=""><span><center>Historia zamówień</center></span></a></li>';
    echo '<li id="gn-ipod" ><a id="various1" href="#inline1"><span><center>Pomoc</center></span></a></li>';
    echo '<li id="gn-mac"><a href="?KeyKat=0&amp;newsales=1" class=""><span><center>Wyjście</center></span></a></li>';//
   
	echo '</ul>';
    echo '</nav>';	
	
	echo "</td>";
    echo "</tr>";
    echo "</table>";
    
    
    echo "<table height='100%' width='100%' border='0'>";
    echo "<tr>";
    
    //Левая навигация
    echo "<td width='130px' valign='top'>";
    

    echo "<table class='data-table2' width='100%'>";
echo "<tr>";
    echo "<td><a href='/?KeyKat=" . $glstr . "'><img src='images/new.jpg' border=0><br><center><b>Strona główna</b></a></center>";
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'>";
    
    if ($_GET['questn'] == "1") {
        if ($_GET['konfirm'] == "1") {
            if ($id > 0) {
                $sqldrey = "DELETE from ipad_usersales where userid=" . $id . " and state=0";
                mysql_query_v($sqldrey);
            }
        }
        if ($_GET['konfirm'] <> "1") {
            echo "<br><br><br><center><b>Opróżić koszyk?</b><br><br>";
            
            
            
            echo "<a href='index.php?KeyKat=0&newsales=1'><FONT SIZE='4'><b>Tak</b></FONT></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?vievcart=1'><FONT SIZE='4'><b>Nie</b></FONT></a>";
            
            
            echo "<br><br></center><br>";
        } else {
            echo "<br><br><center><b>Twój koszyk jest wyczyszczone</b><br><br></center><br><br><br>";
        }
    } else {
        echo "<a href='index.php?vievcart=1&questn=1'><center><img src='images/new_f.jpg' border=1></center><br><center><b>Nowe zamówienie</b></a></center>";
    }
    echo "</td>";
    echo "</tr>";
    
    echo "</table>";
    echo "<table>";
    
    echo "<tr>";
    echo "<td>";
    
    echo "<table width='100%' class='data-table2'>";
    echo "<tr>";
    echo "<td><center><a href='/?KeyKat=" . $glstr . "&find'><img src='images/find.jpg' border=0><br><b>Szukaj...</b></a></center>";
    echo "</td>";
echo "</tr>";

    echo "</table>";
    
    echo "</td>";
    echo "</tr>";
    
    
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'>";
    
    
    if ($id > 0) {
        $ressoob2 = mysql_query_v("SELECT count(*) as cnt FROM ipad_usersales WHERE userid = " . $id . " ");
        while ($ressoobr2 = mysql_fetch_array($ressoob2, MYSQL_ASSOC)) {
            $ressoobk2 = $ressoobr2['cnt'];
        }
        //Сумма заказов из корзины		
        $ressumr = mysql_query_v("SELECT sum(round(ifnull(it.price,0)*ifnull(iu.amount,0),2)) as summf FROM ipad_usersales iu, ipad_tovar it WHERE iu.userid = " . $id . " AND iu.id_tovar = it.Id ");
        
        while ($ressum = mysql_fetch_array($ressumr, MYSQL_ASSOC)) {
            $ressumf = $ressum['summf'];
        }
        
        if ($ressumf == null) {
            $ressumf = 0;
        }
        
        
        //Вес		
        $weigthtr = mysql_query_v("SELECT count(*) as cnt, sum(round(ifnull(iu.amount,0),2)) as weigthtf, round(sum(ifnull(it.price,0)*ifnull(iu.amount,0)),2) as sumt, max(iu.ID) as ID FROM ipad_usersales iu, ipad_tovar it WHERE iu.userid = " . $id . " AND iu.id_tovar = it.Id and (iu.state=0)");
        while ($weigthtm = mysql_fetch_array($weigthtr, MYSQL_ASSOC)) {
            $weigthtf = $weigthtm['cnt'];
			$idfg = $weigthtm['ID'];
			$ressumf = $weigthtm['sumt'];
        }
        if ($weigthtf == null) {
            $weigthtf = 0;
			$idfg = '';
			$ressumf = 0;

        }
        
    } else {
        $ressoobk2 = 0;
        $ressumf   = 0;
        $weigthtf  = 0;
		$idfg = 0;
    }
    
    
    
    
    echo "<table width='100%' class='data-table2'>";
    echo "<tr>";
    echo "<td><center>Koszyk:</center>";
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Zam. nr: <b>" . $idfg . "</b>";
	echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>w złotych: <b>" .$ressumf . "</b>";
        echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Ilość: <b>" . round($weigthtf) . "</b>";
    echo "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td>";
    
    echo "<center>";
    echo "";
    echo "<a id='new_url4' href='index.php?vievcart=1'><img src='images/pen.jpg' border='0'></a>";
//
    echo "";
    echo "";
    echo "";
   // echo "<td bgcolor='#9ccb12'><center><a id='new_url1' href='index.php?vievcart=1&confirmz=1'></a></center>";

 echo "<a id='new_url1' href='index.php?vievcart=1&confirmz=1'><center><b>Koszyk</b></center></a>";
    echo "";
    echo "";
    echo "</center>";
    
    echo "</td>";
    echo "</tr>";
    
    echo "</table>";
    
    
    
    echo "</td>";
    echo "</tr>";
    
    echo "</table>";
    
    echo "</td>";
    
    //Центральная
    echo "<td valign='top'>";
    
    if ($dost == 0) {
        //Обновление курса
        if (isset($_POST['kurs'])) {
            $res  = mysql_query_v("Update ipad_kurs set kurs=" . $_POST['kurs']);
            $kurs = $_POST['kurs'];
        }
        
        
        
        
        
       // echo "<FORM ACTION='index.php?usid=" . $_GET['usid'] . "' METHOD='POST'>";
       // echo "Złoty kolor na biały. trzeć.:<INPUT NAME='kurs' size='10' maxlength='10' VALUE='" . $kurs . "'>";
       // echo "<INPUT TYPE=SUBMIT style='width:90' VALUE='Odświeżać'>";
       // echo "</FORM><br>";
    }
    
    
    if ($_GET['confirmz'] == "1") {
        
        $cntmsg = $_POST['comment'];
        if ($_POST['sp_cd']==1) {$cntmsg=$cntmsg."<br>Odbiór osobisty w sklepie";} else
        {$cntmsg=$cntmsg."<br>Dostawa do domu o<br>".$_POST['sel_t'];}


        //$cntmsg2 = normal(e($_GET['msg']));

        //print_r($_POST);
        
        $sqlTz2 = "SELECT (SELECT ipad_site.Name FROM ipad_site where ipad_site.Id=(SELECT ipad_userpass.shopn FROM ipad_userpass where ID=it.userid)) as Shopn, iu.headerid, iu.date_sales, iu.date_pay, iu.date_doc, iu.date_add, iu.totalpay, iu.amount, iu.id_tovar, iu.state, iu.id, it.Name, it.hr, round(ifnull(it.price,0)*ifnull(iu.amount,0),2) as summf, it.barcode, it.weigtht FROM ipad_usersales iu, ipad_tovar it WHERE iu.userid =" . $id . " AND iu.id_tovar = it.Id and state=0";
        
        // echo $sqlTz;
        
        
        $message = "<B>Zamówienie Nr: " . $loginA . "</B><BR>" . $cntmsg . "<BR>".$cntmsg2."<br><center><table border='1'>";
        $message = $message . "<tr><td><b>№</b></td><td><b>Zdjęcie</b></td><td><b>Shop</b></td><td><b>Nazwa produktu</b></td><td><b>Liczba</b></td><td width='120px'><b>Cena</b></td></tr>";
        
        $kntS   = 1;
        $SelTz2 = mysql_query_v($sqlTz2);
        $massa  = 0;
        $allpr  = 0;
        if ($SelTz2 <> null) {
            while ($SelTzp2 = mysql_fetch_array($SelTz2, MYSQL_ASSOC)) {
                $massa   = $massa + digitt($SelTzp2['weigtht']);
                $allpr   = $allpr + $SelTzp2['summf'];
				$id_tovar_m = $SelTzp2['id'];
                $message = $message . "<tr><td>" . $kntS . "</td><td>" . $SelTzp2['barcode'] . "</td><td>" . $SelTzp2['Shopn'] . "</td><td>" . $SelTzp2['Name'] . " (" . $SelTzp2['hr'] . ")</td><td>" . $SelTzp2['amount'] . "</td><td>" . $SelTzp2['summf'] . " Zł</td></tr>";
                
                $kntS = $kntS + 1;
            }
            //$allpr = $allpr + salesDcalc($massa, $kurszleur);
        }
        $message = $message . "</table></center>";
        
        if ($kntS <> 1) {
            //Обновим статус 
            $sqlupst = "update ipad_usersales set state=1, date_doc=now() where userid=" . $id . " and state=0";
           mysql_query_v($sqlupst);
            
            //отправим емайл
           // echo $message;
           mail($to, $subject, $message, $headers);
            
            
            echo "<table width='98%' border='0'>";
            echo "<tr>";
            echo "<td width='50%'>";
            echo "</td>";
            
            echo "<td>";
            echo "<table border='0'>";
            echo "<tr>";
            echo "<td>";
            echo "</td>";
            echo "</tr>";
            
            echo "<tr>";
            echo "<td bgcolor='#9ccb12'><b></b>";
            echo "</td>";
            echo "</tr>";
            
            
            
            echo "</table>";
            echo "</td>";
            echo "</tr>";
            
            echo "</table>";
            
            echo "<br><br><br>";
            
            echo "<table width='98%' border='0'>";
            echo "<tr>";
            echo "<td>";
            echo "</td>";
            echo "</tr>";
            echo "</table>";
            
            echo "<br><br><br>";
            
            echo "<table width='98%' border='0'>";
            echo "<tr>";
            echo "<td>";
            echo "</td>";
            echo "</tr>";
            echo "</table>";
            
            echo "<br><br><br>";
            
            echo "<table width='98%' border='0'>";
            echo "<tr>";
            echo "<td>";
            echo "</td>";
            echo "</tr>";
            echo "</table>";
            
            echo "<br><br><br>";
            echo "<center><FONT SIZE='5' color='#009900'><b>Dziękujemy za zamówienie!</b></FONT></center>";
            
       } else {
            echo "<br><br><center><b>Koszyk jest niepotwierdzone zlecenia! Zrobić coś!</b></center>";
        }
        
    } else {
        
        echo "<br>&nbsp;&nbsp;<b>Najczęściej zamawiane produkty:</b><br><br>";
        
        
        //Удаление товара из корзины echo "---".salesDcalc(20);
        if ((GP_v($_GET['tdel']) > 0) and ($aut == 1) and ($id > 0)) {
            
            $sqldre = "DELETE from ipad_usersales where ID=" . GP_v($_GET['tdel'] . " and userid=" . $id . " and state=0");
            mysql_query_v($sqldre);
            
            echo Warn3("Produkt usunięty");
        }
        
        
        
        echo "<div id=allspec>";
        echo "<center><table width='98%' class='data-table'>";
        echo "<tr><td><b><center>Nr</center></b></td><td><b><center>Zdjęcie </center></b></td><td><b><center>Nazwa produktu</center></b></td><td><b><center>Liczba</center></b></td><td width='120px'><b><center>Cena</center></b></td><td><b><center>Akcja</center></b></td></tr>";
        
        ////////********
        $sqlTz = "SELECT iu.headerid, ipf.linksm, ipf.link, iu.date_sales, iu.date_pay, iu.date_doc, iu.date_add, iu.totalpay, iu.amount, iu.id_tovar, iu.state, iu.id, it.Name, it.hr, round(ifnull(it.price,0)*ifnull(iu.amount,0),2) as summf, it.barcode, it.weigtht, iu.ID FROM ipad_usersales iu, ipad_tovar it, ipad_fototovar ipf WHERE iu.userid =" . $id . " AND iu.id_tovar = it.Id and it.id=ipf.idtovar and iu.state=0";
        
        // echo $sqlTz;
        $kntS  = 1;
        $SelTz = mysql_query_v($sqlTz);
        $massa = 0;
        $allpr = 0;
        if ($SelTz <> null) {
            while ($SelTzp = mysql_fetch_array($SelTz, MYSQL_ASSOC)) {


if ($local == 1) {
                        $adrfbool = str_replace('C:/wamp/www', '', $SelTzp['link']);
                    } else {
                        $adrfbool = str_replace('home/aladyha/domains/polskiezakupy.pl/public_html', '', $SelTzp['link']);
                    }
                    
                    if ($local == 1) {
                        $adrfsmall = str_replace('C:/wamp/www', '', $SelTzp['linksm']);
                    } else {
                        $adrfsmall = str_replace('home/aladyha/domains/polskiezakupy.pl/public_html', '', $SelTzp['linksm']);
                    }
                    $widhy = $smwidth / 2;
                    $hedhy = $smheight / 2;
                    
                    $adrfbool  = str_replace('/katalog', 'katalog', $adrfbool);
                    $adrfsmall = str_replace('/katalog', 'katalog', $adrfsmall);


$adrfsmall="<a class='gallery' rel='group' HREF='" . $adrfbool . "'><img class='photoramka' src='" . $adrfsmall . "' alt='' width='" . $widhy . "' height='" . $hedhy . "' border='0'></a>";



				$amn=$SelTzp['amount'];
                $amnp=$amn+1;
				$amnm=$amn-1;
				$id_tovar_m=$SelTzp['ID'];
                $massa = $massa + digitt($SelTzp['weigtht']);
                echo "<tr><td><center>" . $kntS . "</center></td><td>" . $adrfsmall . "</td><td>" . $SelTzp['Name'] . " (" . $SelTzp['hr'] . ")</td><td><center><table border='0'><tr><td><a href='index.php?vievcart=1&addcart=1&addcviev=".$SelTzp['id_tovar']."&kolvo=" . $amnm . "'><img src='/images/minus.jpg' width='33px'></td><td>" . $SelTzp['amount'] . "</td><td><a href='index.php?vievcart=1&addcart=1&addcviev=".$SelTzp['id_tovar']."&kolvo=" . $amnp . "'><img src='/images/plus.jpg' width='33px'></a></td></tr></table></center></td><td><center>" . $SelTzp['summf'] . " Zł</center></td><td><center>";
                
                if ($SelTzp['state'] == 0 or $dost == 0) {
                    echo "<a href='?vievcart=1&tdel=" . $SelTzp['id'] . "'><img src='images/del_cart.jpg' border='0'><br>Dodaj</a>";
                } else {
                    echo "Zamówienie zostanie potwierdzone";
                }
                
                echo "</center></td></tr>";
                $allpr = $allpr + $SelTzp['summf'];
                $kntS  = $kntS + 1;
            }
            //$allpr = $allpr + salesDcalc($massa, $kurszleur);
            
            if (($kntS <> 1) and ($dostavka == 1)) {
                echo "<tr><td>" . $kntS . "</td><td>Koszt dostawy</td><td>" . $massa . " kg</td><td>" . salesDcalc($massa, $kurszleur) . "  Zł<br>" . salesDcalc($massa, $kurszleur) * $kurs . " Bel. trzeć.</td><td></td><td></td></tr>";
                
            }
        }
        
        
        echo "</table></center>";
        echo "</div>";
        
         echo "<form id='form' name='form' method='post' action='index.php?vievcart=1&confirmz=1'>";
        echo "<br><br><table border='0' width='80%'>";
        echo "<tr>";
        echo "<td align='center'>";
        
        
         echo "<table width='300px' border='0'>";
         echo "<tr>";
         echo "<td colspan='2'><b>Sposób dostawy następnego dnia:</b></td>";
	
         echo "</tr>";
         echo "<tr>";
         echo "<td width='75%'><img src='images/home1.jpg'><input type='radio' checked='checked' name='sp_cd' value='1' onclick='resizeArea(comment_text);'>Odbiór osobisty w sklepie</td>";
         echo "<td></td>";
         echo "</tr>";
         echo "<tr>";
         echo "<td><img src='images/home.jpg'><input type='radio' name='sp_cd' value='2' onclick='resizeArea(comment_text);'>Dostawa do domu o</td>";
         echo "<td><br>".$sel_time."</td>";
         echo "</tr>";
        echo "</table>";
         

        echo "</td>";
        echo "</tr>";
        echo "</table>";

        echo "<br><br><br><br><center>";
        echo "<table border='0' width='98%'>";
        echo "<tr>";
        echo "<td width='10%'>";
        echo "</td>";
        echo "<td><center>";
        echo "<table border='0'>";
        echo "<tr>";
        echo "<td bgcolor='#9ccb12'>Cena zamówienia: <b>" . $allpr . "</b> Zł <br>Numer zamówienia: <b>".$id_tovar_m."</b> ";
        echo "</td>";
        
        
        echo "</tr>";
        echo "</table>";
        
        
        
        echo "</td></center>";
        echo "<td width='33%'>";
        echo "<center><table class='data-table'>";
        echo "<tr>";
        echo "<td rowspan='2'>Uwagi do zamówienia:<br><textarea rows='7' cols='25' name='comment' id='comment_text'"; ?> onkeyup="resizeArea('comment_text');" <?php echo "></textarea></td><td><img src='images/confirm.jpg' border='0'>";//<a id='new_url2' href='index.php?vievcart=1&confirmz=1'></a>
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor='#9ccb12'><center><input type='submit' value='Zamów teraz'></center>";
        echo "</td>"; //<a id='new_url3' href=''></a>
        echo "</tr>";
        echo "</table></center>";
        echo "</form>";
        
        echo "</td>";
        
        echo "</tr><TR><td colspan='3' height='140px'>";

echo '<footer class="main-footer"><div><ul class="main-footer-shortcuts">';
echo '<li><a href="?KeyKat=2">O nas</a></li>';
echo '<li><a href="?KeyKat=2">Praca</a></li>';
echo '<li><a href="?KeyKat=2">Twój Sklep</a></li>';
echo '<li><a href="?KeyKat=2">FAQ</a></li>';
echo '</ul></div></footer>';

        echo "</td></TR>";
        echo "</table>";
        echo "</center>";
        
        
    }



    echo "</td>";
    echo "</tr>";
    echo "</table>";
    

    
}

//Если $tekKey=0 то главная страница

if (($tekKey == 0) and (!isset($_GET['usid'])) and (!isset($_GET['vievcart'])) AND (!isset($_GET['konto']))) {
    
    echo "<table height='100%' width='100%' border='0'>";
    echo "<tr height='50px' bgcolor='#FFFFFF'>";
    echo '<td><center><a href="?KeyKat=1&autus=0"><FONT SIZE="4">Kliknij tutaj, jeśli jesteś pierwszy raz na naszej stronie.</FONT></a></center>';
    echo "</td>";
    echo "</tr>";
    echo "<tr height='50px' bgcolor='#FF0000'>";
    echo '<td>';

echo '<div id="fancy_overlay" style="opacity: 0.9; display: none;">';
echo '		<div id="inline1" style="width:400px;height:100px;overflow:auto;">';
echo '<center><strong>Pomoc</strong></center><br><br>Aby uzyskać pomoc, skontaktuj się z obsługą sklepu lub zadzwoń: +48 537 756 984<br><br>';
echo 'Ponadto, można znaleźć więcej informacji na dole strony.<br><br><center><a href="javascript:;" onclick="$.fancybox.close();">Zamknij okno</a></center>';
echo '		</div>';
echo '	</div>';


    $rlin = mysql_query_v("SELECT Id, Name from `ipad_menu` order by Id limit 1");
    while ($rlink = mysql_fetch_array($rlin, MYSQL_ASSOC)) {
        $linkid    = $rlink[Id];
        $linksName = $rlink[Name];
    }
    
    if ($aut <> 0) {
        $addns = '';
    } else {
        //$addns = '&newsales=1';
    }


    echo '<nav id="globalheader" class="apple globalheader-js noinset svg globalheader-loaded">';
    echo '<ul id="globalnav" role="navigation">';
    echo '<li><a href="?KeyKat=3" class=""><span><center>Rejestracja</center></span></a></li>';
    echo '<li><a href="?KeyKat=' . $linkid . '' . $addns . '" class=""><span><center>Rozpocznij Zamówienie</center></span></a></li>';

 if ($aut == 0) {
  $ffr='?KeyKat=' . $linkid . '' . $addns.'&redirect=konto';
 } else 
 {
   $ffr='?konto=1';
 }

    echo '<li><a href="'.$ffr.'" class=""><span><center>Moje Konto</center></span></a></li>';//
    echo '<li><a id="various1" href="#inline1"><span><center>Pomoc</center></span></a></li>';
    echo '</ul>';
    echo '</nav>';

    echo "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td valign='top'>";
    
    echo "<center>";//<a href="?KeyKat=1&autus=0"></a>
    echo '<div style="max-width:1024px; max-height:682px;"><img src="images/kategory/title.jpg" alt="Aby uruchomić ekran dotykowy." title="Aby uruchomić ekran dotykowy." width="100%" /></div>';
    echo "</center>";
    
    echo "</td>";
    echo "</tr>";

    echo "<tr height='100px'><td></td>";
    echo "</tr>";
    echo "</table>";
    
}


if (($tekKey == 3) and (!isset($_GET['usid'])) and (!isset($_GET['vievcart'])) AND (!isset($_GET['konto']))) {
    
    echo "<table style='".$dp."' height='768px'  width='100%' border='0'>";
    echo "<tr height='50px' bgcolor='#FFFFFF'>";
    echo '<td width="7%">';
    echo "</td><td width='86%'><center><FONT SIZE='4'>";
    if (isset($_GET['r'])) {
        echo "Rejestracja zakończona";
    } else {
        echo "Rejestracja";
    }
    
    echo "</FONT></center></td><td width='7%'></td>";
    echo "</tr>";
    echo "<tr height='50px' bgcolor='#FF0000'>";
    echo '<td colspan="3">';

	
   $rlin = mysql_query_v("SELECT Id, Name from `ipad_menu` order by Id limit 1");
    while ($rlink = mysql_fetch_array($rlin, MYSQL_ASSOC)) {
        $linkid    = $rlink[Id];
        $linksName = $rlink[Name];
    }
    
    if ($aut <> 0) {
        $addns = '';
    } else {
        //$addns = '&newsales=1';
    }


    echo '<div style="opacity: 0.9; display: none;">';
    echo '		<div id="inline1" style="width:400px;height:100px;overflow:auto;">';
    echo '<center><strong>Pomoc</strong></center><br><br>Aby uzyskać pomoc, skontaktuj się z obsługą sklepu lub zadzwoń: +48 537 756 984<br><br>';
    echo 'Ponadto, można znaleźć więcej informacji na dole strony.<br><br><center><a href="javascript:;" onclick="$.fancybox.close();">Zamknij okno</a></center>';
    echo '		</div>';
    echo '	</div>';


    echo '<nav id="globalheader" class="apple globalheader-js noinset svg globalheader-loaded">';
    echo '<ul id="globalnav" role="navigation">';
    echo '<li id="gn-apple"><a href="?KeyKat=3" class=""><span><center>Rejestracja</center></span></a></li>';
    echo '<li id="gn-store"><a href="?KeyKat=' . $linkid . '' . $addns . '" class=""><span><center>Rozpocznij Zamówienie</center></span></a></li>';
    echo '<li id="gn-ipod" ><a id="various1" href="#inline1"><span><center>Pomoc</center></span></a></li>';
    echo '<li id="gn-mac"><a href="?KeyKat=0&amp;newsales=1" class=""><span><center>Wyjście</center></span></a></li>';//
   
	echo '</ul>';
    echo '</nav>';	
	
	echo "</td>";
    echo "</tr>";
    echo "<tr bgcolor='#FFFFFF'>";
    echo '<td colspan="3" align="center" valign="top">';
    
    
    if (!isset($_GET['r'])) {
        
?>

<form action="?KeyKat=3&r=1" method="post" enctype="multipart/form-data" name="nbcust_subscribe" id="nbcust_subscribe">
<table width="700px" class="contenttable"><tbody><tr>
					      <td width="150">Numer telefonu*</td>
					      <td width="426"><input type="text" name="telephone" value=""  maxlength="80" style="width: 97%;"><br>
					      Numer telefonu komórkowego z numerem kierunkowym kraju. Nie masz telefonu komórkowego? Wystarczy też numer stacjonarny!</td>
					    </tr><tr>
					      <td width="30">Nr sklepu spożywczego</td>
					      <td><?php
 
$sklepy='<select type="text" name="sklepu" size="1" maxlength="80" style="width: 97%;">';
$zs = mysql_query("SELECT * from ipad_site where testacsess=1");
    while ($linenA = mysql_fetch_array($zs, MYSQL_ASSOC)) {
        $sklepy=$sklepy.'<option value="'.$linenA['Id'].'">'.$linenA['Name'].'</option>';
    }
    $sklepy=$sklepy.'</select>';
    echo $sklepy;       

?><br>Numer możesz teź uzyskać od lokalnogo sklepu</td>
					    </tr><tr>
					      <td width="150">Imię*</td>
					      <td><input type="text" name="forename" value="" maxlength="80" style="width: 97%;"></td>
					    </tr><tr>
					      <td width="150">Nazwisko*</td>
					      <td><input type="text" name="name" value="" maxlength="80" style="width: 97%;"></td>
					    </tr><tr>
					      <td width="150">Data urodzenia (d/m/r)*</td>
					      <td><div class="lfloat"><div><select name="birthday_day" id="day"><option value="00" selected="1">dzień</option><option value="01">1</option><option value="02">2</option><option value="03">3</option><option value="04">4</option><option value="05">5</option><option value="06">6</option><option value="07">7</option><option value="08">8</option><option value="09">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option></select><select name="birthday_month" id="month"><option value="0" selected="1">miesiąc</option><option value="01">styczeń</option><option value="02">luty</option><option value="03">marzec</option><option value="04">kwiecień</option><option value="05">maj</option><option value="06">czerwiec</option><option value="07">lipiec</option><option value="08">sierpień</option><option value="09">wrzesień</option><option value="10">październik</option><option value="11">listopad</option><option value="12">grudzień</option></select><select name="birthday_year" id="year"><option value="0" selected="1">rok</option><option value="2013">2013</option><option value="12">2012</option><option value="11">2011</option><option value="10">2010</option><option value="09">2009</option><option value="08">2008</option><option value="07">2007</option><option value="06">2006</option><option value="05">2005</option><option value="04">2004</option><option value="03">2003</option><option value="02">2002</option><option value="01">2001</option><option value="00">2000</option><option value="99">1999</option><option value="98">1998</option><option value="97">1997</option><option value="96">1996</option><option value="95">1995</option><option value="94">1994</option><option value="93">1993</option><option value="92">1992</option><option value="91">1991</option><option value="90">1990</option><option value="89">1989</option><option value="88">1988</option><option value="87">1987</option><option value="86">1986</option><option value="85">1985</option><option value="84">1984</option><option value="83">1983</option><option value="82">1982</option><option value="81">1981</option><option value="80">1980</option><option value="79">1979</option><option value="78">1978</option><option value="77">1977</option><option value="76">1976</option><option value="75">1975</option><option value="74">1974</option><option value="73">1973</option><option value="72">1972</option><option value="71">1971</option><option value="70">1970</option><option value="69">1969</option><option value="68">1968</option><option value="67">1967</option><option value="66">1966</option><option value="65">1965</option><option value="64">1964</option><option value="63">1963</option><option value="62">1962</option><option value="61">1961</option><option value="60">1960</option><option value="59">1959</option><option value="58">1958</option><option value="57">1957</option><option value="56">1956</option><option value="55">1955</option><option value="54">1954</option><option value="53">1953</option><option value="52">1952</option><option value="51">1951</option><option value="50">1950</option><option value="49">1949</option><option value="48">1948</option><option value="47">1947</option><option value="46">1946</option><option value="45">1945</option><option value="44">1944</option><option value="43">1943</option><option value="42">1942</option><option value="41">1941</option><option value="40">1940</option><option value="39">1939</option><option value="38">1938</option><option value="37">1937</option><option value="36">1936</option><option value="35">1935</option><option value="34">1934</option><option value="33">1933</option><option value="32">1932</option><option value="31">1931</option><option value="30">1930</option><option value="29">1929</option><option value="28">1928</option><option value="27">1927</option><option value="26">1926</option><option value="25">1925</option><option value="24">1924</option><option value="23">1923</option><option value="22">1922</option><option value="21">1921</option><option value="20">1920</option><option value="19">1919</option><option value="18">1918</option><option value="17">1917</option><option value="16">1916</option><option value="15">1915</option><option value="14">1914</option><option value="13">1913</option><option value="12">1912</option><option value="11">1911</option><option value="10">1910</option><option value="09">1909</option><option value="08">1908</option><option value="07">1907</option><option value="06">1906</option><option value="05">1905</option></select></div></div>					
</td>
					    </tr><tr>
					      <td width="150">Ulica, nr domu/nr mieszkania*</td>
					      <td><input type="text" name="address" value="" maxlength="80" style="width: 97%;"></td>
					    </tr><tr>
					      <td width="150">Kod pocztowy*</td>
					      <td><input type="text" name="zip" value="" maxlength="80" style="width: 97%;"></td>
					    </tr><tr>
					      <td width="150">Miejscowość*</td>
					      <td><input type="text" name="city" value="" maxlength="80" style="width: 97%;"></td>
					    </tr><tr>
					      <td width="150">Kraj</td>
					      <td><select type="text" name="country" size="1" maxlength="80" style="width: 97%;"><option value="AF">Afghanistan</option><option value="AX">Åland Islands</option><option value="AL">Albania</option><option value="DZ">Algeria</option><option value="AS">American Samoa</option><option value="AD">Andorra</option><option value="AO">Angola</option><option value="AI">Anguilla</option><option value="AQ">Antarctica</option><option value="AG">Antigua and Barbuda</option><option value="AR">Argentina</option><option value="AM">Armenia</option><option value="AW">Aruba</option><option value="AU">Australia</option><option value="AT">Austria</option><option value="AZ">Azerbaijan</option><option value="BH">Bahrain</option><option value="BD">Bangladesh</option><option value="BB">Barbados</option><option value="BY">Belarus</option><option value="BE">Belgium</option><option value="BZ">Belize</option><option value="BJ">Benin</option><option value="BM">Bermuda</option><option value="BT">Bhutan</option><option value="BO">Bolivia</option><option value="BA">Bosnia and Herzegovina</option><option value="BW">Botswana</option><option value="BV">Bouvet Island</option><option value="BR">Brazil</option><option value="IO">British Indian Ocean Territory</option><option value="VG">British Virgin Islands</option><option value="BN">Brunei</option><option value="BG">Bulgaria</option><option value="BF">Burkina Faso</option><option value="BI">Burundi</option><option value="KH">Cambodia</option><option value="CM">Cameroon</option><option value="CA">Canada</option><option value="CV">Cape Verde</option><option value="KY">Cayman Islands</option><option value="CF">Central African Republic</option><option value="TD">Chad</option><option value="CL">Chile</option><option value="CN">China</option><option value="CX">Christmas Island</option><option value="CC">Cocos (Keeling) Islands</option><option value="CO">Colombia</option><option value="KM">Comoros</option><option value="CD">Congo</option><option value="CG">Congo-Brazzaville</option><option value="CK">Cook Islands</option><option value="CR">Costa Rica</option><option value="CI">Côte d'Ivoire</option><option value="HR">Croatia</option><option value="CU">Cuba</option><option value="CY">Cyprus</option><option value="CZ">Czech Republic</option><option value="DK">Denmark</option><option value="DJ">Djibouti</option><option value="DM">Dominica</option><option value="DO">Dominican Republic</option><option value="EC">Ecuador</option><option value="EG">Egypt</option><option value="SV">El Salvador</option><option value="GQ">Equatorial Guinea</option><option value="ER">Eritrea</option><option value="EE">Estonia</option><option value="ET">Ethiopia</option><option value="FK">Falkland Islands</option><option value="FO">Faroes</option><option value="FJ">Fiji</option><option value="FI">Finland</option><option value="FR">France</option><option value="GF">French Guiana</option><option value="PF">French Polynesia</option><option value="TF">French Southern Territories</option><option value="GA">Gabon</option><option value="GM">Gambia</option><option value="GE">Georgia</option><option value="DE">Germany</option><option value="GH">Ghana</option><option value="GI">Gibraltar</option><option value="GR">Greece</option><option value="GL">Greenland</option><option value="GD">Grenada</option><option value="GP">Guadeloupe</option><option value="GU">Guam</option><option value="GT">Guatemala</option><option value="GN">Guinea</option><option value="GW">Guinea-Bissau</option><option value="GY">Guyana</option><option value="HT">Haiti</option><option value="HM">Heard Island and McDonald Islands</option><option value="HN">Honduras</option><option value="HK">Hong Kong SAR of China</option><option value="HU">Hungary</option><option value="IS">Iceland</option><option value="IN">India</option><option value="ID">Indonesia</option><option value="IR">Iran</option><option value="IQ">Iraq</option><option value="IE">Ireland</option><option value="IL">Israel</option><option value="IT">Italy</option><option value="JM">Jamaica</option><option value="JP">Japan</option><option value="JO">Jordan</option><option value="KZ">Kazakhstan</option><option value="KE">Kenya</option><option value="KI">Kiribati</option><option value="KW">Kuwait</option><option value="KG">Kyrgyzstan</option><option value="LA">Laos</option><option value="LV">Latvia</option><option value="LB">Lebanon</option><option value="LS">Lesotho</option><option value="LR">Liberia</option><option value="LY">Libya</option><option value="LI">Liechtenstein</option><option value="LT">Lithuania</option><option value="LU">Luxembourg</option><option value="MO">Macao SAR of China</option><option value="MK">Macedonia</option><option value="MG">Madagascar</option><option value="MW">Malawi</option><option value="MY">Malaysia</option><option value="MV">Maldives</option><option value="ML">Mali</option><option value="MT">Malta</option><option value="MH">Marshall Islands</option><option value="MQ">Martinique</option><option value="MR">Mauritania</option><option value="MU">Mauritius</option><option value="YT">Mayotte</option><option value="MX">Mexico</option><option value="FM">Micronesia</option><option value="MD">Moldova</option><option value="MC">Monaco</option><option value="MN">Mongolia</option><option value="MS">Montserrat</option><option value="MA">Morocco</option><option value="MZ">Mozambique</option><option value="MM">Myanmar</option><option value="NA">Namibia</option><option value="NR">Nauru</option><option value="NP">Nepal</option><option value="NL">Netherlands</option><option value="AN">Netherlands Antilles</option><option value="NC">New Caledonia</option><option value="NZ">New Zealand</option><option value="NI">Nicaragua</option><option value="NE">Niger</option><option value="NG">Nigeria</option><option value="NU">Niue</option><option value="NF">Norfolk Island</option><option value="KP">North Korea</option><option value="MP">Northern Marianas</option><option value="NO">Norway</option><option value="OM">Oman</option><option value="PK">Pakistan</option><option value="PW">Palau</option><option value="PS">Palestine</option><option value="PA">Panama</option><option value="PG">Papua New Guinea</option><option value="PY">Paraguay</option><option value="PE">Peru</option><option value="PH">Philippines</option><option value="PN">Pitcairn Islands</option><option value="PL" selected="selected">Poland</option><option value="PT">Portugal</option><option value="PR">Puerto Rico</option><option value="QA">Qatar</option><option value="RE">Reunion</option><option value="RO">Romania</option><option value="RU">Russia</option><option value="RW">Rwanda</option><option value="SH">Saint Helena</option><option value="KN">Saint Kitts and Nevis</option><option value="LC">Saint Lucia</option><option value="PM">Saint Pierre and Miquelon</option><option value="VC">Saint Vincent and the Grenadines</option><option value="WS">Samoa</option><option value="SM">San Marino</option><option value="ST">São Tomé und Príncipe</option><option value="SA">Saudi Arabia</option><option value="SN">Senegal</option><option value="CS">Serbia and Montenegro</option><option value="SC">Seychelles</option><option value="SL">Sierra Leone</option><option value="SG">Singapore</option><option value="SK">Slovakia</option><option value="SI">Slovenia</option><option value="SB">Solomon Islands</option><option value="SO">Somalia</option><option value="ZA">South Africa</option><option value="GS">South Georgia and the South Sandwich Islands</option><option value="KR">South Korea</option><option value="ES">Spain</option><option value="LK">Sri Lanka</option><option value="SD">Sudan</option><option value="SR">Suriname</option><option value="SJ">Svalbard</option><option value="SZ">Swaziland</option><option value="SE">Sweden</option><option value="CH">Switzerland</option><option value="SY">Syria</option><option value="TW">Taiwan</option><option value="TJ">Tajikistan</option><option value="TZ">Tanzania</option><option value="TH">Thailand</option><option value="BS">The Bahamas</option><option value="TL">Timor-Leste</option><option value="TG">Togo</option><option value="TK">Tokelau</option><option value="TO">Tonga</option><option value="TT">Trinidad and Tobago</option><option value="TN">Tunisia</option><option value="TR">Turkey</option><option value="TM">Turkmenistan</option><option value="TC">Turks and Caicos Islands</option><option value="TV">Tuvalu</option><option value="UG">Uganda</option><option value="UA">Ukraine</option><option value="AE">United Arab Emirates</option><option value="GB">United Kingdom</option><option value="US">United States</option><option value="UM">United States Minor Outlying Islands</option><option value="UY">Uruguay</option><option value="VI">US Virgin Islands</option><option value="UZ">Uzbekistan</option><option value="VU">Vanuatu</option><option value="VA">Vatican City</option><option value="VE">Venezuela</option><option value="VN">Vietnam</option><option value="WF">Wallis and Futuna</option><option value="EH">Western Sahara</option><option value="YE">Yemen</option><option value="ZM">Zambia</option><option value="ZW">Zimbabwe</option></select></td></tr><tr>
					      <td width="150">e-mail*<script>
function emailCheck( email_node ) {
	email = email_node.value.toLowerCase().split('@');
	if (email.length<2) return;
	switch (email[1]) {
		case 'rub.de':
		case 'ruhr-uni-bochum.de':
			if (!document.getElementById('partners_list')) break;
			document.getElementById('partners_list').value = 42;
			updatePartnerList();
			break;
	}
}
</script></td>
					      <td><input type="text" name="email" value="" maxlength="80" style="width: 97%;" onchange="emailCheck(this)"></td>
					    </tr>
						<tr>
							<td colspan="2">
								<br>Regulamin:<br>
1. Mam namiar zapłacić za zamówione produkty w momencie odbioru produktów.<br>				
2. Jeśli nie spodobają mi się produkty, to zwrócę je tego samego dnia.	<br>			
Przeczytałem/łam i akceptuję Regulamin sklepu Polskie Zakupy Aliaksandr Ladyha.<br>					
Wyrażam zgodę na przetwarzanie danych osobowych.<br>
							</td>
				    </tr></tbody></table><br>

<div align="center"><input name="next" type="submit" class="b1" value="Akceptuj" class="register_button" />
</form><br>

<?php
        
    }
    
    if (isset($_GET['r'])) {
        if ($_GET['r'] == 1) {
            
            //print_r($_POST);
            $telephone = mysql_real_escape_string(normal($_POST['telephone']));
            $sklepu    = mysql_real_escape_string($_POST['sklepu']);
            $forename  = mysql_real_escape_string($_POST['forename']);
            $name      = mysql_real_escape_string($_POST['name']);
            $data_r    = mysql_real_escape_string($_POST['birthday_day'] . "" . $_POST['birthday_month'] . "" . $_POST['birthday_year']);
            $address   = mysql_real_escape_string($_POST['address']);
            $zip       = mysql_real_escape_string($_POST['zip']);
            $city      = mysql_real_escape_string($_POST['city']);
            $country   = mysql_real_escape_string($_POST['country']);
            $email     = mysql_real_escape_string($_POST['email']);
            
            if (strlen($telephone) < 6) {
                echo warn("<br><br>Unknown telefon");
                exit;
            }
            
            if (strlen($data_r) < 5) {
                echo warn("<br><br>Unknown data urodzenia");
                exit;
            }
            
            $idf    = 0;
            $zapros = mysql_query_v("SELECT count(*) as cnt from ipad_userpass WHERE Numer='" . $telephone . "' and Numer<>''");
            while ($linenB = mysql_fetch_array($zapros, MYSQL_ASSOC)) {
                $idf = $linenB['cnt'];
            }
            
            if ($idf > 0) {
                echo warn("Błąd. Ten telefon został już zarejestrowany!");
                exit;
            } else {
                
                //Вставка
                $zapros = mysql_query_v("SELECT max(ID) as mid from ipad_userpass");
                while ($linenA = mysql_fetch_array($zapros, MYSQL_ASSOC)) {
                    $idMax = $linenA['mid'];
                }
                $idMax = $idMax + 1;
                $dop   = date("ssmm");
                $res   = mysql_query_v("INSERT INTO ipad_userpass (login,pass,prava,Numer,shopn,nameu,nazwisko,datar,adress,Kodp,Miej,country,mail) VALUES (\"" . $idMax . "\",\"" . $dop . "\",\"1\",\"" . $telephone . "\",\"" . $sklepu . "\",\"" . $forename . "\",\"" . $name . "\",\"" . $data_r . "\",\"" . $address . "\",\"" . $zip . "\",\"" . $city . "\",\"" . $country . "\",\"" . $email . "\")");
                
                //Присвоим новые перемееные сессии
               // $_POST['login'] = $idMax;
               // $_POST['passw'] = $dop;
               // $_POST['enter'] = 'yes';
                
               // $loginA            = $idMax;
               // $pasA              = $dop;
               // $_SESSION['login'] = $loginA;
               // $_SESSION['passw'] = $pasA;
                
               $_POST['login'] = '';
                $_POST['passw'] = '';
                $_POST['enter'] = '';
                
                $loginA            = '';
                $pasA              = '';
                $_SESSION['login'] = '';
                $_SESSION['passw'] = '';
                
                
                echo "<br><div align='left' style='margin:0px 0px 0px 10px;'>";
                echo "Rejestracja zakończona!<br>";
                echo "<br>";
                echo "Szanowny Pan/Pani <b>" . $name . "</b><br>";
                echo "<br>";
                echo "Numer telefonu dla logowania:<b>" . $telephone . "</b><br>";
                echo "<br>";
                echo "Twój PIN jest następujący:<b>" . $data_r . "</b><br>";
                echo "<br>";
                echo "Teraz kliknij 'Rozpocznij zamówienie' aby rozpocząć zakupy.<br></div>";
                
            }
            
        }
    }


	echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td colspan='3' align='center'>";




 echo '<footer class="main-footer"><div><ul class="main-footer-shortcuts">';
echo '<li><a href="?KeyKat=2">O nas</a></li>';
echo '<li><a href="?KeyKat=2">Praca</a></li>';
echo '<li><a href="?KeyKat=2">Twój Sklep</a></li>';
echo '<li><a href="?KeyKat=2">FAQ</a></li>';
echo '</ul></div></footer>';

    echo "</td>";
    echo "</tr>";
    echo "</table>";
    //регистрация
}

if (($tekKey == 2) and (!isset($_GET['usid'])) and (!isset($_GET['vievcart'])) AND (!isset($_GET['konto']))) {
    
    echo "<table height='100%' width='100%' border='0'>";
    echo "<tr height='50px' bgcolor='#FFFFFF'>";
    echo '<td width="7%">';
    echo "</td><td width='86%'><center><FONT SIZE='4'>O firmie</FONT></center></td><td width='7%'></td>";
    echo "</tr>";
    echo "<tr height='30px' bgcolor='#FF0000'>";
    echo '<td colspan="3">';


   $rlin = mysql_query_v("SELECT Id, Name from `ipad_menu` order by Id limit 1");
    while ($rlink = mysql_fetch_array($rlin, MYSQL_ASSOC)) {
        $linkid    = $rlink[Id];
        $linksName = $rlink[Name];
    }
    
    if ($aut <> 0) {
        $addns = '';
    } else {
        //$addns = '&newsales=1';
    }


    echo '<div style="opacity: 0.9; display: none;">';
    echo '		<div id="inline1" style="width:400px;height:100px;overflow:auto;">';
    echo '<center><strong>Pomoc</strong></center><br><br>Aby uzyskać pomoc, skontaktuj się z obsługą sklepu lub zadzwoń: +48 537 756 984<br><br>';
    echo 'Ponadto, można znaleźć więcej informacji na dole strony.<br><br><center><a href="javascript:;" onclick="$.fancybox.close();">Zamknij okno</a></center>';
    echo '		</div>';
    echo '	</div>';


    echo '<nav id="globalheader" class="apple globalheader-js noinset svg globalheader-loaded">';
    echo '<ul id="globalnav" role="navigation">';
    echo '<li id="gn-apple"><a href="?KeyKat=3" class=""><span><center>Rejestracja</center></span></a></li>';
    echo '<li id="gn-store"><a href="?KeyKat=' . $linkid . '' . $addns . '" class=""><span><center>Rozpocznij Zamówienie</center></span></a></li>';
    echo '<li id="gn-ipod" ><a id="various1" href="#inline1"><span><center>Pomoc</center></span></a></li>';
    echo '<li id="gn-mac"><a href="?KeyKat=0&amp;newsales=1" class=""><span><center>Wyjście</center></span></a></li>';//
   
	echo '</ul>';
    echo '</nav>';


    echo "</td>";
    echo "</tr>";
    echo "<tr height='350px' bgcolor='#FFFFFF'>";
    echo '<td colspan="3" align="left" valign="top">';
    
   
echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;Misja<br><br>';

echo '&nbsp;&nbsp;&nbsp;&nbsp;Uważamy, że każdy klient ma doczynienia z dwoma rodzajami produktów. Takie, które klient chce zobaczyć i dotknąć<br>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;przed zakupem (owoce, warzywa,...); oraz druga grupa produktów, których jakość klient już zna (ryż, płyny,...).<br>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;Pozwala to zaoszczędzić czas na lepsze rzeczy, niż chodzenie między regałami sklepowymi. Dlatego pierwszą kategorię<br>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;produktów, możesz kupić tutaj, w lokalnym sklepie spożywczym, podczas gdy inne produkty możesz zamówić<br>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;bezpośrednio do domu.<br><br>';

echo '&nbsp;&nbsp;&nbsp;&nbsp;Serdecznie pozdrawiamy,<br><br>';	

    echo "&nbsp;&nbsp;&nbsp;&nbsp;Polskie Zakupy Aliaksandr Ladyha<br>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;Tel: +48 537 756 984 (Polska)<br>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;Tel: +375 44 772 35 33 (Białoruś)<br>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='mailto:a.ladyha@gmail.com'>a.ladyha@gmail.com</a><br>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='polskiezakupy.pl'>www.polskiezakupy.pl</a><br>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;NIP 1132865878<br>";
    
    


    echo '</td>';
	echo '</tr>';
	echo '<tr>';
    echo '<td colspan="3" align="center">'; 

echo '<footer class="main-footer"><div><ul class="main-footer-shortcuts">';
echo '<li><a href="?KeyKat=2">O nas</a></li>';
echo '<li><a href="?KeyKat=2">Praca</a></li>';
echo '<li><a href="?KeyKat=2">Twój Sklep</a></li>';
echo '<li><a href="?KeyKat=2">FAQ</a></li>';
echo '</ul></div></footer>';
    
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    //О фирме
}


if (($tekKey == 7) and (!isset($_GET['usid'])) and (!isset($_GET['vievcart'])) AND (!isset($_GET['konto']))) {
    
    echo "<table height='100%' width='100%' border='0'>";
    echo "<tr height='50px' bgcolor='#FFFFFF'>";
    echo '<td width="7%">';
    echo "</td><td width='86%'><center><FONT SIZE='4'>Przypomnienie PIN</FONT></center></td><td width='7%'></td>";
    echo "</tr>";
    echo "<tr height='30px' bgcolor='#FF0000'>";
    echo '<td colspan="3">';

   $rlin = mysql_query_v("SELECT Id, Name from `ipad_menu` order by Id limit 1");
    while ($rlink = mysql_fetch_array($rlin, MYSQL_ASSOC)) {
        $linkid    = $rlink[Id];
        $linksName = $rlink[Name];
    }
    
    if ($aut <> 0) {
        $addns = '';
    } else {
        //$addns = '&newsales=1';
    }


    echo '<div style="opacity: 0.9; display: none;">';
    echo '		<div id="inline1" style="width:400px;height:100px;overflow:auto;">';
    echo '<center><strong>Pomoc</strong></center><br><br>Aby uzyskać pomoc, skontaktuj się z obsługą sklepu lub zadzwoń: +48 537 756 984<br><br>';
    echo 'Ponadto, można znaleźć więcej informacji na dole strony.<br><br><center><a href="javascript:;" onclick="$.fancybox.close();">Zamknij okno</a></center>';
    echo '		</div>';
    echo '	</div>';


    echo '<nav id="globalheader" class="apple globalheader-js noinset svg globalheader-loaded">';
    echo '<ul id="globalnav" role="navigation">';
    echo '<li id="gn-apple"><a href="?KeyKat=3" class=""><span><center>Rejestracja</center></span></a></li>';
    echo '<li id="gn-store"><a href="?KeyKat=' . $linkid . '' . $addns . '" class=""><span><center>Rozpocznij Zamówienie</center></span></a></li>';
    echo '<li id="gn-ipod" ><a id="various1" href="#inline1"><span><center>Pomoc</center></span></a></li>';
    echo '<li id="gn-mac"><a href="?KeyKat=0&amp;newsales=1" class=""><span><center>Wyjście</center></span></a></li>';//
   
	echo '</ul>';
    echo '</nav>';


    echo "</td>";
    echo "</tr>";
    echo "<tr height='658px' bgcolor='#FFFFFF'>";
    echo '<td colspan="3" valign="top" align="left">';
    
echo '<font size="4"><br>&nbsp;&nbsp;&nbsp;&nbsp;Zapomniałeś PIN?<br>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;Prosimy skontaktować się telefonicznie z naszą firmą w celu odzyskania numeru PIN.<br>';
echo '&nbsp;&nbsp;&nbsp;&nbsp;Numer firmy: +48 537 756 984 <br></font>';

    echo '</td>';
	echo '</tr>';
	echo '<tr>';
    echo '<td colspan="3" align="center">';
    

    //Напомнить пароль
}


if (($tekKey == 5) and (!isset($_GET['usid'])) and (!isset($_GET['vievcart'])) AND (!isset($_GET['konto']))) {
    
    
    if ($aut == 0) {

	echo "<table height='100%' width='100%' border='0'>";
    echo "<tr height='50px' bgcolor='#FFFFFF'>";
    echo '<td width="7%">';
    echo "</td><td width='86%'><center><FONT SIZE='4'>";
    echo "Logowanie";
    echo "</FONT></center></td><td width='7%'></td>";
    echo "</tr>";
    echo "<tr height='30px' bgcolor='#FF0000'>";
    echo '<td colspan="3">';


    echo '<div style="opacity: 0.9; display: none;">';
    echo '		<div id="inline1" style="width:400px;height:100px;overflow:auto;">';
    echo '<center><strong>Pomoc</strong></center><br><br>Aby uzyskać pomoc, skontaktuj się z obsługą sklepu lub zadzwoń: +48 537 756 984<br><br>';
    echo 'Ponadto, można znaleźć więcej informacji na dole strony.<br><br><center><a href="javascript:;" onclick="$.fancybox.close();">Zamknij okno</a></center>';
    echo '		</div>';
    echo '	</div>';



    echo '<nav id="globalheader" class="apple globalheader-js noinset svg globalheader-loaded">';
    echo '<ul id="globalnav2" role="navigation">';
    echo '<li id="gn-apple"><a href="?KeyKat=3" class=""><span><center>Rejestracja</center></span></a></li>';
    echo '<li id="gn-ipod" ><a id="various1" href="#inline1"><span><center>Pomoc</center></span></a></li>';
    echo '<li id="gn-mac"><a href="?KeyKat=0&amp;newsales=1" class=""><span><center>Wyjście</center></span></a></li>';//
   
	echo '</ul>';
    echo '</nav>';


	
	echo "</td>";
    echo "</tr>";
    echo "<tr height='100%' bgcolor='#FFFFFF'>";
    echo '<td colspan="3" align="center"  valign="top">';



        echo "<br><center>";
		
    if ($_POST['login']<>'')
		{	
	      echo warn('Nieprawidłowy numer telefonu lub PIN');
	     }


		echo "<FORM ACTION='' METHOD='POST'>";
        echo "<TABLE border='0' width='60%'>";
        echo "<TR>";
        echo "	<TD width='33%'>Numer telefonu:</TD>";
        echo "	<TD width='33%'><INPUT type='tel' NAME='login' id='login' size='30' maxlength='30' VALUE='' placeholder='W formie: 48 XXX XXX XXX' tabindex='1'></TD>";
        echo "	<TD width='33%' rowspan='3'><center><INPUT TYPE=SUBMIT class='b1' VALUE='Zaloguj' tabindex='3'></center></TD>";
        echo "</TR><TR><td colspan='2'></td></TR>";
       // echo "<TR>";
        echo "<TR>";
        echo "	<TD>Kod PIN:</TD>";
        echo "	<TD><INPUT NAME='passw' type='tel' size='30' maxlength='30' VALUE='' tabindex='2'><input type=hidden name=enter value=yes></TD>";
        echo "</TR>";
        echo "<TR>";
        echo "	<TD colspan='3'><center><a href='?KeyKat=7'>Zapomniałem PIN</a></center></TD>";
        echo "</TR>";
        echo "</TABLE>";
        echo "</FORM></center>";
       
	   	 
       echo "</td>";
       echo "</tr>";
       
	   echo "<tr>";
       echo "<td colspan='3' align='center'>";


 echo '<footer class="main-footer"><div><ul class="main-footer-shortcuts">';
echo '<li><a href="?KeyKat=2">O nas</a></li>';
echo '<li><a href="?KeyKat=2">Praca</a></li>';
echo '<li><a href="?KeyKat=2">Twój Sklep</a></li>';
echo '<li><a href="?KeyKat=2">FAQ</a></li>';
echo '</ul></div></footer>';
       
	   echo "</td>";
       echo "</tr>";
       
	   echo "</table>";       
 
		
		?>
<script type="text/javascript">
myFocus("login");

function myFocus(id){
 sleep(1000);
 try{  document.getElementById(id).focus();
 } catch(e) {
    // обработчик ошибки, можно оставить пустым
    // alert(e);
 }
}
</script>		
<?php
        
        exit;
    }
    
    
    echo "<table height='100%' width='100%' border='0'>";
    echo "<tr height='50px' bgcolor='#FFFFFF'>";
    echo '<td width="7%">';
    echo "</td><td width='86%'><center><FONT SIZE='4'>Historia zamówień</FONT></center></td><td width='7%'></td>";
    echo "</tr>";
    echo "<tr height='30px' bgcolor='#FF0000'>";
    echo '<td colspan="3">';

   $rlin = mysql_query_v("SELECT Id, Name from `ipad_menu` order by Id limit 1");
    while ($rlink = mysql_fetch_array($rlin, MYSQL_ASSOC)) {
        $linkid    = $rlink[Id];
        $linksName = $rlink[Name];
    }
    
    if ($aut <> 0) {
        $addns = '';
    } else {
        //$addns = '&newsales=1';
    }


    echo '<div style="opacity: 0.9; display: none;">';
    echo '		<div id="inline1" style="width:400px;height:100px;overflow:auto;">';
    echo '<center><strong>Pomoc</strong></center><br><br>Aby uzyskać pomoc, skontaktuj się z obsługą sklepu lub zadzwoń: +48 537 756 984<br><br>';
    echo 'Ponadto, można znaleźć więcej informacji na dole strony.<br><br><center><a href="javascript:;" onclick="$.fancybox.close();">Zamknij okno</a></center>';
    echo '		</div>';
    echo '	</div>';


    echo '<nav id="globalheader" class="apple globalheader-js noinset svg globalheader-loaded">';
    echo '<ul id="globalnav" role="navigation">';
    echo '<li id="gn-apple"><a href="?KeyKat=3" class=""><span><center>Rejestracja</center></span></a></li>';
    echo '<li id="gn-store"><a href="?KeyKat=' . $linkid . '' . $addns . '" class=""><span><center>Rozpocznij Zamówienie</center></span></a></li>';
    echo '<li id="gn-ipod" ><a id="various1" href="#inline1"><span><center>Pomoc</center></span></a></li>';
    echo '<li id="gn-mac"><a href="?KeyKat=0&amp;newsales=1" class=""><span><center>Wyjście</center></span></a></li>';//
   
	echo '</ul>';
    echo '</nav>';

    echo "</td>";
    echo "</tr>";
    echo "<tr height='70%' bgcolor='#FFFFFF'>";
    echo '<td colspan="3" valign="top" align="center">';
    
    if ($dost == 0) {
        $del_us   = intval($_GET['conf_del_us']);
        $conf_pay = intval($_GET['conf_pay']);
        $del_cnf  = intval($_GET['del_cnf']);
        
        if ($conf_pay > 0) {
            $sqlupst4 = "update ipad_usersales set state=2, date_pay=now() where userid=" . $conf_pay . "";
            mysql_query_v($sqlupst4);
            echo warn('Usunięty');
        }
        
        if ($del_cnf == 1) {
            mysql_query_v("delete FROM ipad_userpass WHERE Id=" . $del_us);
            echo warn('Usunięty');
        } else {
            if ($del_cnf <> 0) {
                echo warn('Usuń dane rejestracji od użytkownika ' . $del_us . '?<br><br><a href="?KeyKat=5&conf_del_us=' . $del_us . '&del_cnf=1">Tak</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?KeyKat=5">Raczej nie</a><br>');
            }
        }
        //     }
    }
    
    
    echo "<br><table class='data-table3' width='97%' border=0>";
    if ($dost == 0) {
        
        echo "<tr>";
        echo "<td width='10%'><center>data zamówienia</center></td>";
        echo "<td width='10%'><center>użytkownik</center></td>";
        echo "<td width='10%'><center>liczba pozycji</center></td>";
        echo "<td width='60%'><center>produkt</center></td>";
        echo "<td width='5%'><center>del user</center></td>";
        echo "<td width='5%'><center>płatny</center></td>";
        echo "</tr>";
        
        
        $t_us = intval($_GET['sel_us']);
        
        
        $rdd2 = mysql_query_v("SELECT iu.ID, CASE ifnull(iu.nameu,'') WHEN '' THEN iu.login else iu.nameu END as login, (select count(*) from ipad_usersales it where it.userid=iu.ID and it.state=1) as cntsales, (select max(it.date_add) from ipad_usersales it where it.userid=iu.ID and it.state=1) as sales_date, iu.pass, iu.Numer, iu.shopn, iu.datar, iu.adress, iu.mail FROM  ipad_userpass iu");
        while ($rldd2 = mysql_fetch_array($rdd2, MYSQL_ASSOC)) {
            
            if (($t_us == $rldd2[ID]) or ($del_us == $rldd2[ID])) {
                echo "<tr bgcolor='#8DB600'>";
            } else {
                echo "<tr>";
            }
            echo "<td><center>" . $rldd2[sales_date] . "</center></td>";
            echo "<td><center><a href='?KeyKat=5&sel_us=" . $rldd2[ID] . "'>" . $rldd2[login] . "</a></center></td>";
            echo "<td><center>" . $rldd2[cntsales] . "</center></td>";
            
            if (($t_us == $rldd2[ID]) or ($del_us == $rldd2[ID])) {
                echo "<td width='70%'>";

                 echo "Numer: <b>".$rldd2[Numer]."</b><br>";
                 echo "PIN: <b>".$rldd2[datar]."</b><br>";
                 echo "shopn: <b>".$rldd2[shopn]."</b><br>";
                 echo "mail: <b>".$rldd2[mail]."</b><br>";
                 echo "adress: <b>".$rldd2[adress]."</b><br>";

				echo "-------------<br>";
                $rdd3 = mysql_query_v("SELECT it.*, (select ip.Name from ipad_tovar ip where ip.id=it.id_tovar) as Name FROM `ipad_usersales` it where it.userid=" . $rldd2[ID] . " and it.state=1");
                while ($rldd3 = mysql_fetch_array($rdd3, MYSQL_ASSOC)) {
                    if ($rldd3[state] == 1) {
                        $nz = "nie zapłacił";
                    } else {
                        $nz = "";
                    }
                    echo $rldd3[Name] . " - <b>" . $rldd3[amount] . " rzecz</b> [" . $nz . "]<br>";
                    
                }
                
                echo "</td>";
            } else {
                echo "<td></td>";
            }
            
            
            echo "<td><center><a href='?KeyKat=5&conf_del_us=" . $rldd2[ID] . "'>usunąć</a></center></td>";
            
            
            echo "<td><center><a href='?KeyKat=5&conf_pay=" . $rldd2[ID] . "'>Yes</a></center></td>";
            
            echo "</tr>";
        }
        
    } else {
        echo "<tr height='30px'>";
        echo "<td width='20%'><center><b>Nr zamówienia</b></center>";
        echo "</td >";
        echo "<td width='20%'><center><b>Dzień zamówienia</b></center>";
        echo "</td>";
        echo "<td width='20%'><center><b>Dzień odbioru</b></center>";
        echo "</td>";
        echo "<td width='20%'><center><b>Kwota do zapłaty</b></center>";
        echo "</td>";
        echo "<td width='20%'><center><b>Wpłacona kwota</b></center>";
        echo "</td>";
        
        $rnm = 0;
        $rdd = mysql_query_v("SELECT state, SUM(totalpay) AS totalpay, date_add, date_pay FROM ipad_usersales WHERE userid=" . $id . " GROUP BY date_add, date_pay, state");
        while ($rldd = mysql_fetch_array($rdd, MYSQL_ASSOC)) {
            $rnm = $rnm + 1;
            echo "</tr>";
            echo "<tr valign='top'>";
            echo "<td><center>" . $rnm . "</center>";
            echo "</td>";
            echo "<td><center>" . $rldd[date_add] . "</center>";
            echo "</td>";
            echo "<td><center>" . $rldd[date_pay] . "</center>";
            echo "</td>";
            echo "<td><center>" . $rldd[totalpay] . "</center>";
            echo "</td>";
            echo "<td><center>" . $rldd[totalpay] . "</center>";
            echo "</td>";
            echo "</tr>";
        }
        



    }
    echo "</table>";


	        if ($rnm==0) 
		{
		  echo warn('Nie masz zadnych aktywnych zlecen');
		}
    
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td colspan='3' valign='top' align='center'>";

echo '<footer class="main-footer"><div><ul class="main-footer-shortcuts">';
echo '<li><a href="?KeyKat=2">O nas</a></li>';
echo '<li><a href="?KeyKat=2">Praca</a></li>';
echo '<li><a href="?KeyKat=2">Twój Sklep</a></li>';
echo '<li><a href="?KeyKat=2">FAQ</a></li>';
echo '</ul></div></footer>';

    
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    //Мои заказы
}

if (($tekKey == 6) and (!isset($_GET['usid'])) and (!isset($_GET['vievcart'])) AND (!isset($_GET['konto']))) {
    
    echo "<table height='100%' width='100%' border='0'>";
    echo "<tr height='50px' bgcolor='#FFFFFF'>";
    echo '<td width="7%">';
    echo "</td><td width='86%'><center><FONT SIZE='6'>Wpłata</FONT></center></td><td width='7%'></td>";
    echo "</tr>";
    echo "<tr height='30px' bgcolor='#FF0000'>";
    echo '<td colspan="3">';

   $rlin = mysql_query_v("SELECT Id, Name from `ipad_menu` order by Id limit 1");
    while ($rlink = mysql_fetch_array($rlin, MYSQL_ASSOC)) {
        $linkid    = $rlink[Id];
        $linksName = $rlink[Name];
    }
    
    if ($aut <> 0) {
        $addns = '';
    } else {
        //$addns = '&newsales=1';
    }


    echo '<div style="opacity: 0.9; display: none;">';
    echo '		<div id="inline1" style="width:400px;height:100px;overflow:auto;">';
    echo '<center><strong>Pomoc</strong></center><br><br>Aby uzyskać pomoc, skontaktuj się z obsługą sklepu lub zadzwoń: +48 537 756 984<br><br>';
    echo 'Ponadto, można znaleźć więcej informacji na dole strony.<br><br><center><a href="javascript:;" onclick="$.fancybox.close();">Zamknij okno</a></center>';
    echo '		</div>';
    echo '	</div>';


    echo '<nav id="globalheader" class="apple globalheader-js noinset svg globalheader-loaded">';
    echo '<ul id="globalnav" role="navigation">';
    echo '<li id="gn-apple"><a href="?KeyKat=3" class=""><span><center>Rejestracja</center></span></a></li>';
    echo '<li id="gn-store"><a href="?KeyKat=' . $linkid . '' . $addns . '" class=""><span><center>Rozpocznij Zamówienie</center></span></a></li>';
    echo '<li id="gn-ipod" ><a id="various1" href="#inline1"><span><center>Pomoc</center></span></a></li>';
    echo '<li id="gn-mac"><a href="?KeyKat=0&amp;newsales=1" class=""><span><center>Wyjście</center></span></a></li>';//
   
	echo '</ul>';
    echo '</nav>';
	
	
	echo "</td>";
    echo "</tr>";
    echo "<tr height='100%' bgcolor='#FFFFFF'>";
    echo '<td colspan="3" align="left" valign="top">';

    echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;Zapłać przy odbiorze:<br>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;1. Zapłać przy odbiorze<br>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;2. Przelew bankowy<br>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;Bank: <b>Citi handlowy</b><br>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;Firma: <b>Polskie Zakupy Aliaksandr Ladyha</b><br>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;Nr konta: <b>32 1030 0019 0109 8530 0048 7748</b><br>";


    echo '</td>';
	echo '</tr>';
	echo '<tr>';
    echo '<td colspan="3" align="center">';

    
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    //О фирме
}





if (($tekKey == 1) and (!isset($_GET['usid'])) and (!isset($_GET['vievcart'])) AND (!isset($_GET['konto']))) {
    echo "<table style='".$dp."' width='100%' border='0'>";
    echo "<tr height='50px' bgcolor='#FFFFFF'>";
    echo '<td width="7%"><center><a href="#" onclick="history.back();return false;"><img src="images/back.jpg" height="33px" border=0></a></center></td><td width="84%"><center><FONT SIZE="4">Proces Kupna</FONT></center>';
    echo "</td><td width='7%'> </td>";
    echo "</tr>";
    echo "<tr height='50px' bgcolor='#FF0000'>";
    echo '<td colspan="3">';


    $rlin = mysql_query_v("SELECT Id, Name from `ipad_menu` order by Id limit 1");
    while ($rlink = mysql_fetch_array($rlin, MYSQL_ASSOC)) {
        $linkid    = $rlink[Id];
        $linksName = $rlink[Name];
    }
    
    if ($aut <> 0) {
        $addns = '';
    } else {
        //$addns = '&newsales=1';
    }


    echo '<div style="opacity: 0.9; display: none;">';
    echo '		<div id="inline1" style="width:400px;height:100px;overflow:auto;">';
    echo '<center><strong>Pomoc</strong></center><br><br>Aby uzyskać pomoc, skontaktuj się z obsługą sklepu lub zadzwoń: +48 537 756 984<br><br>';
    echo 'Ponadto, można znaleźć więcej informacji na dole strony.<br><br><center><a href="javascript:;" onclick="$.fancybox.close();">Zamknij okno</a></center>';
    echo '		</div>';
    echo '	</div>';


    echo '<nav id="globalheader" class="apple globalheader-js noinset svg globalheader-loaded">';
    echo '<ul id="globalnav" role="navigation">';
    echo '<li id="gn-apple"><a href="?KeyKat=3" class=""><span><center>Rejestracja</center></span></a></li>';
    echo '<li id="gn-store"><a href="?KeyKat=' . $linkid . '' . $addns . '" class=""><span><center>Rozpocznij Zamówienie</center></span></a></li>';
    echo '<li id="gn-ipod" ><a id="various1" href="#inline1"><span><center>Pomoc</center></span></a></li>';
    echo '<li id="gn-mac"><a href="?KeyKat=0&amp;newsales=1" class=""><span><center>Wyjście</center></span></a></li>';//
   
	echo '</ul>';
    echo '</nav>';

    echo "</td>";
    echo "</tr>";
    echo "<tr height='21px'>";
    echo '<td colspan="3"><table width="100%" border="0"><tr><td width="50%">';
    if ($_GET['autus'] == '1') {
        if ($aut == 0) {
            echo "<FORM ACTION='" . $ins . "' METHOD='POST'>&nbsp;&nbsp;&nbsp;
 Numer zamówienia: <INPUT NAME='login' size='10' maxlength='10' VALUE=''>
 Hasło: <INPUT NAME='passw' type='password' size='10' maxlength='10' VALUE=''>
<input type=hidden name=enter value=yes>
<INPUT TYPE=SUBMIT style='width:60' VALUE='Wejście'>";
            echo "</FORM>";
        }
    }
    
    if ($aut <> 0) {
        $ressoob2 = mysql_query_v("SELECT count(*) as cnt FROM ipad_usersales WHERE userid = " . $id . " ");
        while ($ressoobr2 = mysql_fetch_array($ressoob2, MYSQL_ASSOC)) {
            $ressoobk2 = $ressoobr2['cnt'];
        }
        
    }
    
    echo "&emsp;<a href='?KeyKat=2'><FONT SIZE='4'>Sprawdź, dlaczego warto robić  u nas zakupy?</FONT></a></td><td align='right'><a href='?KeyKat=2'><FONT SIZE='4'>O firmie</FONT></a>&emsp;</td></tr></table></td>";
    echo "</tr>";
    echo "<tr valign='top' bgcolor='#FFFFFF' height='627px'>";
    echo "<td colspan='3'>";
    
    echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;<FONT SIZE="2"><b>Zobacz, jak wygląda proces kupna (kliknij na krok, który jest właściwy dla Ciebie):</b></FONT>';
    echo "<br><br>";
    
    echo "<center>";
    echo "<table class='data-table3' width='95%' border=0>";
    echo "<tr height='30px' bgcolor='#dbd7c3'>";
    echo "<td width='25%'><center>Krok 1</center>";
    echo "</td>";
    echo "<td width='25%'><center>Krok 2</center>";
    echo "</td>";
    echo "<td width='25%'><center>Krok 3</center>";
    echo "</td>";
    echo "<td width='25%'><center>Krok 4</center>";
    echo "</td>";
    echo "</tr>";
    //echo "SELECT Id, Name from `ipad_menu` order by Id limit 1";
    $rlin = mysql_query_v("SELECT Id, Name from `ipad_menu` order by Id limit 1");
    while ($rlink = mysql_fetch_array($rlin, MYSQL_ASSOC)) {
        $linkid    = $rlink[Id];
        $linksName = $rlink[Name];
    }
    if ($linkid=='') {$linkid='100';}   


    if ($aut <> 0) {
        $addns = '';
    } else {
        //$addns = '&newsales=1';
    }
    echo "<tr>";
    echo "<td><center>Zarejestruj się w sklepie spożywczym, który jest najbliżej Ciebie, lub na tej strone.</center>";
    echo "</td>";
    echo "<td><center>Prześlij listę produktów, które chcesz kupić.</center>";
    echo "</td>";
    echo "<td><center>Wszystko dostarczone do Twojego sklepu osiedlowego, lub domu, następnogo dnia.</center>";
    echo "</td>";
    echo "<td><center>Zapłać za produkty przy odbiorze.</center>";
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><center>Zaakceptuj regulamin i otrzymaj numer identyfikacyjny na zakupy. Użyj w tym celu przycisk Rejestracja<br><br><br><br><br><a href='?KeyKat=3'><div style='max-width:235px;'><img src='images/krok1.jpg' width='100%' border=0></div></a></center>";
    echo "</td>";
    echo "<td><center>Użyj przycisk Rozpocznij Zamówienie , lub  zadzwoń, wyślij SMS, e-maila,  albo  zostaw listę w sklepie.<br><br><br><br><br><br><a href='?KeyKat=" . $linkid . "" . $addns . "'><div style='max-width:235px;'><img src='images/krok2.jpg' width='100%' border=0></div></a></center>";
    echo "</td>";
    echo "<td><center>Dostarczanie do sklepu bezpłatnie, lub do domu za 5 zł.<br><br><br><br><br><a href='?KeyKat=5'><div style='max-width:235px;'><img src='images/krok3.jpg' width='100%' border=0></div></a></center>";
    echo "</td>";
    echo "<td><center>Stali klienci mogą płacić za swoje zakupy raz w tygodniu.<br><br><br><br><br><br><br><br><br><br><a href='?KeyKat=6'><div style='max-width:235px;'><img src='images/krok4.jpg' width='100%' border=0></div></a></center>";
    echo "</td>";
    echo "</tr>";

    echo "</table>";
    echo "</center>";
   
echo '<br><br><br><br><br><br><footer class="main-footer"><div><ul class="main-footer-shortcuts">';
echo '<li><a href="?KeyKat=2">O nas</a></li>';
echo '<li><a href="?KeyKat=2">Praca</a></li>';
echo '<li><a href="?KeyKat=2">Twój Sklep</a></li>';
echo '<li><a href="?KeyKat=2">FAQ</a></li>';
echo '</ul></div></footer>'; 
    
    echo "</table>";
}

if (($tekKey <> 0) and ($tekKey <> 1) and ($tekKey <> 2) and ($tekKey <> 3) and ($tekKey <> 4) and ($tekKey <> 5) and ($tekKey <> 6) and ($tekKey <> 7)) {
    
    //Если не вошли то форма входа
    
    if ($aut == 0) {
        
	echo "<table  style='".$dp."' width='100%' border='0'>";
    echo "<tr height='50px' bgcolor='#FFFFFF'>";
    echo '<td width="7%">';
    echo "</td><td width='86%'><center><FONT SIZE='4'>";
    echo "Logowanie";
    echo "</FONT></center></td><td width='7%'></td>";
    echo "</tr>";
    echo "<tr height='30px' bgcolor='#FF0000'>";
    echo '<td colspan="3">';

   $rlin = mysql_query_v("SELECT Id, Name from `ipad_menu` order by Id limit 1");
    while ($rlink = mysql_fetch_array($rlin, MYSQL_ASSOC)) {
        $linkid    = $rlink[Id];
        $linksName = $rlink[Name];
    }
    
    if ($aut <> 0) {
        $addns = '';
    } else {
        //$addns = '&newsales=1';
    }


    echo '<div style="opacity: 0.9; display: none;">';
    echo '		<div id="inline1" style="width:400px;height:100px;overflow:auto;">';
    echo '<center><strong>Pomoc</strong></center><br><br>Aby uzyskać pomoc, skontaktuj się z obsługą sklepu lub zadzwoń: +48 537 756 984<br><br>';
    echo 'Ponadto, można znaleźć więcej informacji na dole strony.<br><br><center><a href="javascript:;" onclick="$.fancybox.close();">Zamknij okno</a></center>';
    echo '		</div>';
    echo '	</div>';


    echo '<nav id="globalheader" class="apple globalheader-js noinset svg globalheader-loaded">';
    echo '<ul id="globalnav" role="navigation">';
    echo '<li id="gn-apple"><a href="?KeyKat=3" class=""><span><center>Rejestracja</center></span></a></li>';
    echo '<li id="gn-store"><a href="?KeyKat=' . $linkid . '' . $addns . '" class=""><span><center>Rozpocznij Zamówienie</center></span></a></li>';
    echo '<li id="gn-ipod" ><a id="various1" href="#inline1"><span><center>Pomoc</center></span></a></li>';
    echo '<li id="gn-mac"><a href="?KeyKat=0&amp;newsales=1" class=""><span><center>Wyjście</center></span></a></li>';//
   
	echo '</ul>';
    echo '</nav>';

    echo "</td>";
    echo "</tr>";
    echo "<tr bgcolor='#FFFFFF'>";
    echo '<td colspan="3" align="center" height="658px" valign="top">';



        echo "<br><center>";
		
    if ($_POST['login']<>'')
		{	
	      echo warn('Nieprawidłowy numer telefonu lub PIN');
	     }


    if ($_GET['redirect']=='konto') {$rrrr='?konto=1'; } else {$rrrr='';}


		echo "<FORM ACTION='".$rrrr."' METHOD='POST'>";
        echo "<TABLE border='0' width='60%'>";
        echo "<TR>";
        echo "	<TD width='33%'>Numer telefonu:</TD>";
        echo "	<TD width='33%'><INPUT type='tel' NAME='login' id='login' size='30' maxlength='30' placeholder='W formie: 48 XXX XXX XXX' VALUE='' tabindex='1'></TD>";
        echo "	<TD width='33%' rowspan='3'><center><INPUT TYPE=SUBMIT class='b1' VALUE='Zaloguj' tabindex='3'></center></TD>";
        echo "</TR><TR><td colspan='2'></td></TR>";
        echo "<TR>";
        echo "	<TD>Kod PIN:</TD>";
        echo "	<TD><INPUT NAME='passw' type='tel' size='30' maxlength='30' VALUE='' tabindex='2'><input type=hidden name=enter value=yes></TD>";
        echo "</TR>";
        echo "<TR>";
        echo "	<TD colspan='3'><center><a href='?KeyKat=7'>Zapomniałem PIN</a></center></TD>";
        echo "</TR>";
        echo "</TABLE>";
        echo "</FORM></center>";
       
	   	 
       echo "</td>";
       echo "</tr>";
       
	   echo "<tr>";
       echo "<td colspan='3' align='center'>";

 echo '<footer class="main-footer"><div><ul class="main-footer-shortcuts">';
echo '<li><a href="?KeyKat=2">O nas</a></li>';
echo '<li><a href="?KeyKat=2">Praca</a></li>';
echo '<li><a href="?KeyKat=2">Twój Sklep</a></li>';
echo '<li><a href="?KeyKat=2">FAQ</a></li>';
echo '</ul></div></footer>';
       
	   echo "</td>";
       echo "</tr>";
       
	   echo "</table>";       
 
		
		?>
<script type="text/javascript">
myFocus("login");

function myFocus(id){
 try{
    document.getElementById(id).focus();
 } catch(e) {
    // обработчик ошибки, можно оставить пустым
    // alert(e);
 }
}
</script>		
<?php
        
        
        
        exit;
    }
    
    
    
    
    
    
    
    
    ///======= Шапка
    
    $dedkat = intval($_GET['deadall']);
    if ($dedkat > 0) {
        if ($_GET['konfirm'] <> "1") {
            echo "<br><br><br><center><b>Удалить все товары в категории?</b><br><br>";
            echo "<a href='index.php?KeyKat=" . $tekKey . "&deadall=1&konfirm=1'>Tak</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?KeyKat=" . $tekKey . "'>Nie</a>";
            echo "<br><br></center><br>";
        } else {
            echo "<br><br><br><center><b>Удалено</b></center>";
            //echo "DELETE from ipad_fototovar where idtovar in (select id from ipad_tovar where menuid=".$tekKey.")";
            //Поудаляем в таблицах фоток
            mysql_query_v("DELETE from ipad_fototovar where idtovar in (select id from ipad_tovar where menuid=" . $tekKey . ")");
            //Поудаляем в таблицах объявлений
            mysql_query_v("DELETE from ipad_tovar where menuid=" . $tekKey);
            
        }
        
        
        
    }
 
$NameUR = "";
    //Название родительского уровня
    $resn   = mysql_query_v("SELECT Name, link, (SELECT KeyKat FROM ipad_menu WHERE Id=" . $tekKey . ") as lastK, idP FROM ipad_menu WHERE Id=" . $tekKey . " and sklepy like '%".$sklepy."%' and testacsess=1 order by priority");
    while ($linen = mysql_fetch_array($resn, MYSQL_ASSOC)) {
        $NameUR = $linen['Name']; //Категория (жирная)
        $uriven = "<a href='index.php?KeyKat=" . $tekKey . "' title='Obecna kategoria'><b>" . $linen['Name'] . "</b></a>";
        $link   = $linen['link'];
        $lastKN = $linen['lastK'];
        if ($linen['idP'] <> "")
            $dir = $linen['idP'];
    }   
    
    echo "<table width='100%' border='0'>";
    echo "<tr height='40px' bgcolor='#FFFFFF'>";
    echo '<td width="7%">';
     
if ($lastKN <> "0") {
echo '<center><a href="#" onclick="history.back();return false;"><img src="images/back.jpg" height="33px" border=0></a></center>'; }
    echo '</td><td width="86%"><center><FONT SIZE="4">';
    
    
    if ($lastKN <> "0") {
        //Кнопка назад уровня
        $resl = mysql_query_v("SELECT Name,Id FROM ipad_menu WHERE Id=" . $lastKN);
        while ($linel = mysql_fetch_array($resl, MYSQL_ASSOC)) {//" . $linel['Name'] . "</FONT></a>&nbsp;&nbsp;&nbsp;<&nbsp;&nbsp;&nbsp;
            echo "<FONT SIZE='4'>" . $NameUR . "";
			//<A HREF=index.php?KeyKat=" . $linel[Id] . " title='Powrót do kategorii'>
            // 
            if ($dost == 0) //можно редактировать
                {
                echo "<A HREF='index.php?KeyKat=" . $tekKey . "&deadall=1' title='Удалить все товары в категории'> <img src=images/icon/Bin.png></a>";
            }
            
            
        }
    } else {
        if ($tekKey <> "0") { //считаем что это корневая категория

		 if (isset($_GET['find'])) {
            echo "<FONT SIZE='4'> Wyszukiwarka</FONT>";
		 } else {
            echo "<FONT SIZE='4'> Strona Główna</FONT>";}
        }
    }
    
    
    echo '</FONT></center><a name="up"></a>';
    echo "</td><td width='7%'></td>";
    echo "</tr>";
    echo "<tr height='30px'>";
    echo '<td colspan="3">';


   $rlin = mysql_query_v("SELECT Id, Name from `ipad_menu` order by Id limit 1");
    while ($rlink = mysql_fetch_array($rlin, MYSQL_ASSOC)) {
        $linkid    = $rlink[Id];
        $linksName = $rlink[Name];
    }
    
    if ($aut <> 0) {
        $addns = '';
    } else {
    }


    echo '<div style="opacity: 0.9; display: none;">';
    echo '		<div id="inline1" style="width:400px;height:100px;overflow:auto;">';
    echo '<center><strong>Pomoc</strong></center><br><br>Aby uzyskać pomoc, skontaktuj się z obsługą sklepu lub zadzwoń: +48 537 756 984<br><br>';
    echo 'Ponadto, można znaleźć więcej informacji na dole strony.<br><br><center><a href="javascript:;" onclick="$.fancybox.close();">Zamknij okno</a></center>';
    echo '		</div>';
    echo '	</div>';


    echo '<nav id="globalheader" class="apple globalheader-js noinset svg globalheader-loaded">';
    echo '<ul id="globalnav2" role="navigation">';
    echo '<li id="gn-apple"><a href="?konto=1" class=""><span><center><b>Moje Konto: '.$nameuq.'</b></center></span></a></li>';
    echo '<li id="gn-ipod" ><a id="various1" href="#inline1"><span><center>Pomoc</center></span></a></li>';

    echo '<li id="gn-mac"><a href="?KeyKat=0&amp;newsales=1" class=""><span><center>Wyjście</center></span></a></li>';//
   
	echo '</ul>';
    echo '</nav>';

    echo "</td>";
    echo "</tr>";
    echo "</table>";
    
    
    echo "<table height='100%' width='100%' border='0'>";
    echo "<tr>";
    
    //Левая навигация
    echo "<td width='130px' valign='top' align='center'>";
    

    echo "<div style='margin-top: 4px; margin-left: 1px;'><table class='data-table2' width='145px'>";
echo "<tr>";   
 echo "<td><a href='/?KeyKat=" . $glstr . "'><img src='images/new.jpg' border=0><br><center><b>Wyszukiwarka</b></a></center>";
    echo "</td>";
    echo "</tr>";    
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'>";
    
    if ($_GET['questn'] == "1") {
        if ($_GET['konfirm'] == "1") {
            if ($id > 0) {
                $sqldrey = "DELETE from ipad_usersales where userid=" . $id . " and state=0";
                mysql_query_v($sqldrey);
            }
        }
        if ($_GET['konfirm'] <> "1") {
            echo "<br><br><br><center><b>Opróżić koszyk?</b><br><br><a href='index.php?KeyKat=0&newsales=1'><FONT SIZE='4'><b> Tak</b></FONT></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='index.php?KeyKat=" . $tekKey . "'><FONT SIZE='4'><b> Nie</b></FONT></a><br><br><br></center>";
        } else {
            echo "<br><br><center><b>Twój koszyk jest wyczyszczone</b><br><br><br></center><br>";
        }
    } else {
        echo "<a href='index.php?KeyKat=" . $tekKey . "&questn=1'><center><img src='images/new_f.jpg' border=1></center><br><center><b>Nowe zamówienie</b></a></center>";
    }
    echo "</td>";
    echo "</tr>";
    
    echo "</table></div>";
    echo "<table>";
    
    echo "<tr>";
    echo "<td>";
    echo "<table width='100%' class='data-table2'>";
    echo "<tr>";
    echo "<td><center><a href='/?KeyKat=" . $glstr . "&find'><img src='images/find.jpg' border=0><br><b>Szukaj...</b></a></center>";
    echo "</td>";
echo "</tr>";

    echo "</table>";
    
    echo "</td>";
    echo "</tr>";
    
    
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'>";
    
    
    if ($id > 0) {
        $ressoob2 = mysql_query_v("SELECT count(*) as cnt FROM ipad_usersales WHERE userid = " . $id . " ");
        while ($ressoobr2 = mysql_fetch_array($ressoob2, MYSQL_ASSOC)) {
            $ressoobk2 = $ressoobr2['cnt'];
        }
        //Сумма заказов из корзины		
        $ressumr = mysql_query_v("SELECT sum(round(ifnull(it.price,0)*ifnull(iu.amount,0),2)) as summf FROM ipad_usersales iu, ipad_tovar it WHERE iu.userid = " . $id . " AND iu.id_tovar = it.Id ");
        
        while ($ressum = mysql_fetch_array($ressumr, MYSQL_ASSOC)) {
            $ressumf = $ressum['summf'];
        }
        
        if ($ressumf == null) {
            $ressumf = 0;
        }
        
        
        //Вес		
        $weigthtr = mysql_query_v("SELECT count(*) as cnt, sum(round(ifnull(iu.amount,0),2)) as weigthtf, round(sum(ifnull(it.price,0)*ifnull(iu.amount,0)),2) as sumt, max(iu.ID) as ID FROM ipad_usersales iu, ipad_tovar it WHERE iu.userid = " . $id . " AND iu.id_tovar = it.Id and (iu.state=0)");
        while ($weigthtm = mysql_fetch_array($weigthtr, MYSQL_ASSOC)) {
            $weigthtf = $weigthtm['cnt'];
			$idfg = $weigthtm['ID'];
			$ressumf = $weigthtm['sumt'];
        }
        if ($weigthtf == null) {
            $weigthtf = 0;
			$idfg = '';
			$ressumf = 0;

        }
        
    } else {
        $ressoobk2 = 0;
        $ressumf   = 0;
        $weigthtf  = 0;
		$idfg = 0;
    }
    
    
    
    
    
    echo "<table width='100%' class='data-table2'>";
    echo "<tr>";
    echo "<td><center>Koszyk:</center>";
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Zam. nr: <b>" . $idfg . "</b>";
	echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>w złotych: <b>" .$ressumf . "</b>";
        echo "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td>Ilość: <b>" . round($weigthtf) . "</b>";
    echo "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td><a href='/index.php?vievcart=1'><img src='images/pen.jpg' border=0><br><center><b>Koszyk</b></a></center>";
    echo "</td>";
    echo "</tr>";
    
    echo "</table>";
    
    
    
    echo "</td>";
    echo "</tr>";
    
    echo "</table>";
    
    echo "</td>";
    
    //Центральная
    echo "<td valign='top'>";
    
    
    //Поиск



   if (isset($_GET['find'])) {

   if (isset($_POST['searchtext'])) {
$_SESSION['searchtext'] = $_POST['searchtext'];
$_SESSION['ComboBox'] = $_POST['ComboBox'];

   }

    echo "<form action='?KeyKat=".$glstr."&find' METHOD='POST'>";
   echo "<center><table border=0 width=97%>";
   echo "<tr>";
   echo "<td width=100%><div class='d_search_left'><input type='text' name='searchtext' class='d_search_input' x-webkit-speech  value='' placeholder='Jakiego produktu szukasz?'>";

 $zp = "SELECT Id, Name, imagelink from ipad_menu where testacsess>0 and KeyKat=" . $tekKey . " and sklepy like '%".$sklepy."%' order by priority ";
$resKon = mysql_query_v($zp);
    
echo "<select name='ComboBox' class='d_search_input2' style='width : 29%'><option value='0'>wszystkie działy</option>";
  
   while ($r = mysql_fetch_assoc($resKon)) //засунем выборку в массив
            {
  echo "<option value='".$r[Id]."'>".$r[Name]."</option>";
}    
 // 
 //       

echo "</select></div>
</td>";
   echo "<td width='69px'><input type='submit' class='d_button d_xl d_green' value='SZUKAJ'></td>";
   echo "</tr>";
   echo "</table><center></form>";
   }

    //Покажем результат

 
if (isset($_SESSION['searchtext']) and (isset($_GET['find']))) {


//====== получим все подпункты меню в массив
$resKonM = mysql_query("SELECT id, Name FROM ipad_menu");
$newmenu = array();
while ($rm = mysql_fetch_assoc($resKonM)) //засунем выборку в массив
    {
    $newmenu[$rm['id']] = $rm['Name'];
}





if ($_SESSION['ComboBox']=='0')
{
    $SQLZaprosSearch = "SELECT ipad_tovar.price, ipad_tovar.Name, ipad_tovar.id,  ipad_tovar.menuid, ipad_menu.link as l, ipad_fototovar.linksm, ipad_fototovar.link FROM ipad_tovar join ipad_fototovar on ipad_tovar.id=ipad_fototovar.idtovar 
join ipad_menu on ipad_tovar.menuid=ipad_menu.id where ipad_tovar.Name like '%".mysql_real_escape_string($_SESSION['searchtext'])."%'  order by ipad_tovar.menuid, ipad_tovar.Name";

} else {

 $SQLZaprosSearch = "SELECT ipad_tovar.price, ipad_tovar.Name, ipad_tovar.id,  ipad_tovar.menuid, ipad_menu.link as l, ipad_fototovar.linksm, ipad_fototovar.link FROM ipad_tovar join ipad_fototovar on ipad_tovar.id=ipad_fototovar.idtovar join ipad_menu on ipad_tovar.menuid=ipad_menu.id  where ipad_tovar.Name like '%".mysql_real_escape_string($_SESSION['searchtext'])."%' and ipad_tovar.menuid in (select Id from ipad_menu where link like '%|".mysql_real_escape_string($_SESSION['ComboBox'])."%') order by ipad_tovar.menuid, ipad_tovar.Name ";

//
}
//echo $SQLZaprosSearch;
 $resKoSearch = mysql_query_v($SQLZaprosSearch);

        echo "<p id='jm-back-top' style='display: block;'><a href='#top'><span></span>&nbsp;</a></p><table class='data-table3' width=96%>";
        echo "<tr bgcolor='#dbd7c3'><td width='81px'><center>Zdjęcie</center></td><td><center>Nazwa produktu</center></td><td width='150px'><center>Cena</center></td><td width='100px'><center>Akcja</center></td></tr>";


$cipm='';


        while ($rse = mysql_fetch_assoc($resKoSearch)) 
            {

if ($cipm<>$rse[l].''.$rse[menuid].'|') {

$cipm=$rse[l].''.$rse[menuid].'|';

$textL = explode("|", $cipm);



$linkb='';
//пройдемся по массиву и соберем цепочку 
        for ($i = 1; $i < count($textL) - 1; $i++) {
            if (($newmenu[$textL[$i]]) <> '') {
                              if ($linkb=='') {$linkb=$linkb.'<a href="?KeyKat='.$textL[$i].'">'.$newmenu[$textL[$i]].'</a>';} else {
                $linkb=$linkb.' > <a href="?KeyKat='.$textL[$i].'">'.$newmenu[$textL[$i]].'</a>';}
            }
            
        }



echo "<tr><td colspan='4' >".$linkb."</td></tr>";

}

  if ($local == 1) {
                        $adrfbool = str_replace('C:/wamp/www', '', $rse['link']);
                    } else {
                        $adrfbool = str_replace('home/aladyha/domains/polskiezakupy.pl/public_html', '', $rse['link']);
                    }
                    
                    if ($local == 1) {
                        $adrfsmall = str_replace('C:/wamp/www', '', $linefoto['linksm']);
                    } else {
                        $adrfsmall = str_replace('home/aladyha/domains/polskiezakupy.pl/public_html', '', $rse['linksm']);
                    }
                    $widhy = $smwidth / 2;
                    $hedhy = $smheight / 2;
                    
                    $adrfbool  = str_replace('/katalog', 'katalog', $adrfbool);
                    $adrfsmall = str_replace('/katalog', 'katalog', $adrfsmall);


    
$adrfsmall="<center><a class='gallery' rel='group' HREF='" . $adrfbool . "'><img class='photoramka' src='" . $adrfsmall . "' alt='' width='" . $widhy . "' height='" . $hedhy . "' border='0'></a></center>";
       echo "<tr>";
            echo "<td>".$adrfsmall."</td><td><a HREF='index.php?viev=".$rse[id]."&KeyKat=".$rse[menuid]."'>".$rse[Name]."</a></td><td><center>".$rse[price]." zł</center></td><td><center>";
			echo '<form action="?addcviev='.$rse[id].'&KeyKat=' . $glstr . '&find" method="POST">';
echo '<input type="hidden" value="1" name="kolvo" id="kolvo">';
echo '<input type="hidden" name="addcart" value="1">';
echo '<input type="SUBMIT" class="button3" value="Dodaj do koszyka"><br>Dodaj';
echo '</form>';
			echo "</center></td>";
       echo "</tr>";
        }
        echo "</table>";
   }
    //======== Кишки категории 
    
    if (!isset($_GET['find'])) {  //Если не поиск то нормальный режим
    
    $SQLZapros = "SELECT COUNT(*) FROM ipad_menu where testacses>0 and KeyKat=" . $tekKey . " and sklepy like '%".$sklepy."%' ";
    $itog      = mysql_query($SQLZapros);
    $pos       = mysql_fetch_row($itog);
    $maxi      = intval($pos[0]);
    if (($maxi) / 9 > round(($maxi) / 9)) {
        $maxpage = round(($maxi) / 9) + 1;
        if ($page > $maxpage) {
            $page = $maxpage;
        }
    } else {
        $maxpage = round(($maxi) / 9);
        if ($page > $maxpage) {
            $page = $maxpage;
        }
    }
    
    

   } //1



    //Удаление категории
    if (isset($_GET['Katdelete'])) {
        if ($dost <> 0) {
            exit;
        } //Никто кроме админа
        
        if ($aut == 1) {
            if ($_GET['confirmdk'] == 1) {
                mysql_query_v("delete FROM ipad_menu WHERE Id=" . GP_v($_GET['Katdelete']));
                mysql_query_v("delete from ipad_tovar where menuid=" . GP_v($_GET['Katdelete']));
                
                
                echo warn('Usunięty');
            } else {
                echo warn('Czy na pewno usunąć tę kategorię ' . GP_v($_GET['Katdelete']) . '?<br><br><a href="index.php?Katdelete=' . GP_v($_GET['Katdelete']) . '&KeyKat=' . $tekKey . '&confirmdk=1">Tak</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/">Raczej nie</a><br>');
            }
        }
        
    }
    
    
    
    //Форма добавления и редактирования
    if ($_GET['addKat'] == "1" and $aut == 1 and $dost == 0) {
        if ($_GET['UPKat'] == "1") {
            $res  = mysql_query_v("SELECT * FROM ipad_menu WHERE Id=" . $tekKey);
            $edit = mysql_fetch_array($res);
        }
        
        if ($edit['idP'] == $id or $dost == 0) { //Если форма твоя - правь
            //Форма 
            echo "<center><br><FORM METHOD=POST ACTION='index.php?KeyKat=" . $tekKey . "&kat=1' enctype=\"multipart/form-data\">";
?>
		<table width=190 cellspacing=0 cellpadding=0 border=0 class='boxTable'>
        <tr><td class="boxTitle">
		<INPUT TYPE=HIDDEN NAME="Id" VALUE="<?php
            echo $edit['Id'];
?>">
        <INPUT TYPE=HIDDEN NAME="KeyKat" VALUE="<?php
            echo $tekKey;
?>">
		<b>																
		<?php
            if ($edit['Id'] > 0) {
                echo "Исправление названия и иконки ";
                echo "<a href='/index.php?Katdelete=" . $edit['Id'] . "&KeyKat=" . $tekKey . "'><img title='Удалить категорию и все товары в ней' src='images/icon/Bin.png'></a> ";
                
            } else {
                echo "Название новой категории:";
            }
            echo "</td></tr><tr><td class='boxContent'>";
?>
		
		</b> <center><INPUT NAME="Name" maxlength='32' VALUE="<?php
            echo $edit['Name'];
?>"><br>Приоритет:<br><INPUT NAME="priority" maxlength='5' VALUE="<?php
            echo $edit['priority'];
?>"><?php if ($sklepy==1) { echo "<br>Коды магазинов:<br><INPUT NAME='sklepy' maxlength='25' VALUE='".$edit['sklepy']."'>"; } ?><BR>
    Картинка:<INPUT type="file" size="10" NAME="imagelink" id="myFile1"><BR>    
		<INPUT TYPE=SUBMIT class="button" VALUE="Отправить!"></center>
        <?php
            echo "</td></tr><tr><td class='boxTitlefun'></td></tr></table></FORM></div></center>";
        }
    }
    
    
    
    if (($maxi / 3) > intval($maxi / 3)) {
        $stf = intval($maxi / 3) + 1;
    } else {
        $stf = intval($maxi / 3);
    }
    
    
    
    $zp = "SELECT Id, Name, imagelink from ipad_menu where testacsess>0 and KeyKat=" . $tekKey . " and sklepy like '%".$sklepy."%' order by priority ";

  
    
    //echo $zp;
    //Массив 3x3
    
    $newArr = array();
    
    $resKon = mysql_query_v($zp);
    
    if ($resKon <> null) {
        
        while ($r = mysql_fetch_assoc($resKon)) //засунем выборку в массив
            {
            $newArr[] = $r;
        }
    }
    
    //Отображение категорий
    $kntcat = $newArr[0][Id];
    $cntr   = 0;
    
    if ($newArr[0][Id] <> '') //Если категории еще есть
        {
        echo "<table width=100% border='0'>";
        for ($i = 1; $i <= $stf; $i++) {
//echo "-".$i;
            echo "<tr height=200>";
            for ($j = 1; $j <= 3; $j++) {
                echo "<td width='33%' align=center>";
                
                echo "<table width='90%' height='175px' class='data-table3'>";
                echo "<tr>";
                echo "<td height='90%'>";
                
                if ($newArr[$cntr][imagelink] <> '') {
                    $imv = "<img src='images/kategory/" . $newArr[$cntr][imagelink] . "' border='0'>";
                } else {
                    $imv = "";
                }
                if ($newArr[$cntr][Name] <> '') {
                    $imn = $newArr[$cntr][Name];
                } else {
                    $imn = "";
                }
                
                
                
                if ($imn <> '') {
                    echo "<A HREF=index.php?KeyKat=" . $newArr[$cntr][Id] . "><center>" . $imv . "</center></a></td></tr><tr><td bgcolor='#dbd7c3'><center><A HREF=index.php?KeyKat=" . $newArr[$cntr][Id] . ">" . $imn . "</a>";
                    
                    if ($dost == 0) //можно редактировать
                        {
                        echo "<A HREF=index.php?KeyKat=" . $newArr[$cntr][Id] . "&UPKat=1&addKat=1 title='Edycja'> <img src=images/icon/edit.gif></a>";
                    }
                    
                    echo "</center>";
                    
                } else {
                    echo "<center>" . $imn . "</center>";
                }
                
                echo "</td>";
                echo "</tr>";
                echo "</table>";
                
                
                $cntr = $cntr + 1;
                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    //Покажем товар раз нет категорий
    else {
        
        if ($megikKat == $tekKey) {
            echo "<br><FONT SIZE='3'>Ta kategoria pozwala zamówić wszelkie towary z Polski.<br><br><br>
Aby to zrobić, należy zadzwonić pod numer telefonu znajdujący się na górze i wyjaśnić, <br>
czego pragniesz, nasz przedstawiciel. <br>
On z kolei będzie w stanie zaoferować wiele możliwości dostosowane do swojego Łódź
Opis, które są sprzedawane w Polsce. <br>
Po uzgodnić z przedstawicielem zakupie, otrzymasz numer produktu. <br>
Po dokonaniu płatności za zamówienie, towar zostanie wysłany pocztą.</FONT>";
        }
        
        
        //Сама процедура добавления
        if ($_POST['kat'] == '2') {
            //echo "Добавление тов.";
            
            $idins      = normal($_POST['id']);
            $product    = normal($_POST['product']);
            $hr         = normal($_POST['hr']);
            $price      = normal($_POST['price']);
            $weigth     = normal($_POST['weigth']);
            $full_story = normal($_POST['full_story']);
            $barcode    = normal($_POST['barcode']);
            $currI      = 'Zł';
            
            //Остановился тут
            if ($aut == 1 and $dost == 0) {
                
                $dir = '.'; //Если директория не указана возмем за корневую текущую
                //Директория
                if (is_dir($dir)) {
                    chdir($dir . '/katalog');
                    $basedir = getcwd();
                    $basedir = str_replace('\\', '/', $basedir);
                    //       echo "<br>".$basedir;
                }
                
                if (!is_dir($basedir . '/' . $loginA)) { //Если папки пользователя на сервере почему то нет - создадим
                    mkdir($basedir . '/' . $loginA);
                    chmod($basedir . '/' . $loginA, 0777); //Создаем папку с правами записи
                }
                
                
               
                if ($idins <> '') {
   
                    $inssql = "UPDATE ipad_tovar SET price=\"" . $price . "\", Name=\"" . $product . "\", obiavl=\"" . $full_story . "\", barcode=\"" . $barcode . "\" WHERE id=" . $idins . "";
                } else {
                    $inssql = "INSERT INTO ipad_tovar (menuid,price,curr,fotoid,userid,region,strana,Name,Kontact,obiavl, hr, barcode, weigtht, testacsess) VALUES (\"" . $tekKey . "\",\"" . $price . "\",\"" . $currI . "\",\"" . $fl1 . "\",\"" . $id . "\",\"" . $fl2 . "\",\"" . $fl3 . "\",\"" . $product . "\",\"" . $kontactI . "\",\"" . $full_story . "\",\"" . $hr . "\",\"" . $barcode . "\",\"" . $weigth . "\",2)";
                }
                $res = mysql_query_v($inssql);
                
                //echo "|".$inssql."|";
                
                
                $posttext = mysql_query_v("select max(ID) as Nb from ipad_tovar");
                while ($linf = mysql_fetch_array($posttext, MYSQL_ASSOC)) {
                    $nombFid = $linf[Nb];
                }
                
                
                //echo $nombFid;
                
                //Добавление картинок
                $tmpn = date("ddmmssmm");
                ;
                foreach ($_FILES as $file) {
                    if (strlen($file["name"]) <= 0)
                        continue;
                    $tmpn     = $tmpn + 1;
                    $new      = $new . '' . $tmpn;
                    // Процедура добавления ->
                    $fotoname = $file["name"]; // определяем имя файла
                    $fotosize = $file["size"]; // Запоминаем размер файла
                    if ($fotoname <> "") {
                        $ext = strtolower(substr($fotoname, 1 + strrpos($fotoname, ".")));
                        if (!in_array($ext, $valid_types)) {
                            echo warn('<B>ФАЙЛ НЕ ЗАГРУЖЕН!</b> <br>Zezwalaj na pobieranie tylko pliki z rozszerzeniami: <b>gif, jpg, jpeg, png</b><br><br><A HREF="index.php?updlink=' . $nombFid . '&KeyKat=' . $tekKey . '">Kliknij tutaj, aby powrócić</A>');
                            exit;
                        }
                        
                        // 1. считаем кол-во точек в выражении - если большей одной - СВОБОДЕН!
                        $findtchka = substr_count($fotoname, ".");
                        if ($findtchka > 1) {
                            echo "POINT znaleziono w nazwie pliku $findtchka раз(а). To nie jest! <BR>\r\n";
                        }
                        
                        // 2. если в имени есть .php, .html, .htm - свободен! 
                        $bago = "Przepraszam. W nazwie pliku <B> palenia </B>, aby użyć .php, .html, .htm";
                        if (preg_match("/\.php/i", $fotoname)) {
                            echo "Wejście <B>\".php\"</B> znaleziono. $bago";
                            exit;
                        }
                        if (preg_match("/\.html/i", $fotoname)) {
                            echo "Wejście <B>\".html\"</B> znaleziono. $bago";
                            exit;
                        }
                        if (preg_match("/\.htm/i", $fotoname)) {
                            echo "Wejście <B>\".htm\"</B> znaleziono. $bago";
                            exit;
                        }
                        
                        // 4. Проверяем, может быть файл с таким именем уже есть на сервере
                        
                        $fotomax = round($max_file_size / 10.24) / 100; // максимальный размер фото в Кб.
                        if ($fotoksize > $fotomax) {
                            print "Został przekroczony dopuszczalny zdjęcie rozmiar! <BR><B>maksymalna dopuszczalna</B> rozmiar zdjęcia: <B>$fotomax </B>Кб.<BR> <B>Próbujesz </B>, aby załadować obraz: <B>$fotoksize</B> Кб!";
                            exit;
                        }
                        
                        $size = getimagesize($file["tmp_name"]);
                        // $size=getimagesize($_FILES['file']['tmp_name']);
                        if ($size[0] > $maxwidth or $size[1] > $maxheight) {
                            print warn("$size[0] x $size[1] - nie dopuszczalne zdjęcia rozmiar. dopuszczalne tylko $maxwidth х $maxheight px!");
                            exit;
                        }
                        
                        //echo $fotosize;
                        
                        if ($fotosize > "0" and $fotosize < $max_file_size) {
                            copy($file["tmp_name"], $basedir . "/" . $loginA . "/" . $new . "." . $ext);
                            //	copy($_FILES['file']['tmp_name'], $basedir."/".$loginA."/".$new.".".$ext);
                            // print "<br><br>Фото УСПЕШНО загружено: $fotoname (Размер: $fotosize байт)";
                            //Вставим инфу о файле в базу!
                            $vievid = 1; //Поумолчанию фотка видна 
                            
                            $size = getimagesize($basedir . "/" . $loginA . "/" . $new . "." . $ext);
                            
                            // Проверяем размер фото. Если "габариты" меньше заданный в админке 150 х 120 - то ничего с ним не делаем
                            // блок делает мальное изображение исходной фотки - в качестве превьюшки
                            if ($size[0] > $smwidth or $size[1] > $smheight) {
                                $smallfoto = $basedir . "/" . $loginA . "/sm_" . $new . "." . $ext;
                                if (img_resize($basedir . "/" . $loginA . "/" . $new . "." . $ext, $basedir . "/" . $loginA . "/sm_" . $new . "." . $ext, $smwidth, $smheight)) {
                                    //echo 'Изображение масштабировано <B>успешно</B>.';
                                } else
                                    echo '<font color=red><B>Błąd zdjęć skalowalności! Poblemy z biblioteki GD!</B></font> Zapoznaj się z Administratorem';
                            } else {
                                $smallfoto = $basedir . "/" . $loginA . "/" . $new . "." . $ext;
                            }
                            
                            $res = mysql_query_v("INSERT INTO ipad_fototovar (userid,link,idtovar,linksm,viev) VALUES (\"" . $id . "\",\"" . $basedir . "/" . $loginA . "/" . $new . "." . $ext . "\",\"" . $nombFid . "\",\"" . $smallfoto . "\",\"" . $vievid . "\")");
                            
                            
                        } else {
                            print "<B>Plik nie jest ładowany - Server Error! Zapoznaj się z Administratorem!<B>";
                            exit;
                        }
                        
                    } //$fotoname <> ""
                    
                    
                }
                
            }
            
        }
        
        //=====Добавление контента==============
        if ((($_GET['addKont'] == "2") or digitt($_GET['updlink']) > 0) and ($aut == 1) and ($dost == 0)) {
            
            $tovare_r_id      = '';
            $tovare_r_Name    = '';
            $tovare_r_obiavl  = '';
            $tovare_r_barcode = '';
            $tovare_r_hr      = '';
            $tovare_r_weigtht = '';
            $tovare_r_price   = '';
            
            
            
            if (digitt($_GET['updlink']) > 0) {
                $tovare = mysql_query_v("SELECT * from ipad_tovar where id=" . digitt($_GET['updlink']) . " ");
                while ($tovare_r = mysql_fetch_array($tovare, MYSQL_ASSOC)) {
                    $tovare_r_id      = $tovare_r['Id'];
                    $tovare_r_Name    = $tovare_r['Name'];
                    $tovare_r_obiavl  = $tovare_r['obiavl'];
                    $tovare_r_barcode = $tovare_r['barcode'];
                    $tovare_r_hr      = $tovare_r['hr'];
                    $tovare_r_weigtht = $tovare_r['weigtht'];
                    $tovare_r_price   = $tovare_r['price'];
                }
            }
            
            
            
            echo '<br><form action="index.php?KeyKat=' . $tekKey . '" id="upform" name="upform" method="post" enctype="multipart/form-data">';
            echo '<table  border="0" cellpadding="0" cellspacing="0">';
            
            echo '<tr>
				<td width=30% height="25" nowrap="nowrap" style="padding: 3px; font-size: 12px;"><b>Продукт</b> (nazwa):</td>
				<td><input type="text" name="product" maxlength="150" size="54" class="f_input" value="' . $tovare_r_Name . '" /></td>
			</tr>';
            
            echo '<tr>
				<td width=30% height="25" nowrap="nowrap" style="padding: 3px; font-size: 12px;"><b>Funkcje</b> (szerokość / wysokość):</td>
				<td><input type="text" name="hr" maxlength="250" size="70" class="f_input" value="' . $tovare_r_hr . '" /></td>
			</tr>';
            
            echo '<tr>
				<td width=30% height="25" nowrap="nowrap" style="padding: 3px; font-size: 12px;"><b>Cena</b> (punkt dzielnik 0.12):</td>
				<td><input type="text" name="price" maxlength="40" size="20" class="f_input" value="' . $tovare_r_price . '" /></td>
			</tr>';
            
            
            echo '<tr>
				<td width=30% height="25" nowrap="nowrap" style="padding: 3px; font-size: 12px;"><b>Waga</b> (punkt dzielnik kg):</td>
				<td><input type="text" name="weigth" maxlength="40" size="20" class="f_input" value="' . $tovare_r_weigtht . '" /></td>
			</tr>';
            
            
            
            echo '<tr>
				<td style="padding: 3px; font-size: 12px;">Szczegółowy opis:</td>
				<td><textarea name="full_story" id="full_story" style="height: 300px; margin-left: 2px; margin-top: 2px;"  />' . $tovare_r_obiavl . '</textarea></td>
			</tr>';
            
            
            echo '<tr>
				<td width=30% height="25" nowrap="nowrap" style="padding: 3px; font-size: 12px;">Kod kreskowy:</td>
				<td><input type="text" name="barcode" maxlength="40" size="20" class="f_input" value="' . $tovare_r_barcode . '" /></td>
			</tr>';
            
            echo '<tr>
				<td width="80">&nbsp;</td>
				<td>
				
<input type="hidden" name="id" value="' . $tovare_r_id . '">
<input type="hidden" name="kat" value="2">
<input type="hidden" name="KeyKat" value="' . $tekKey . '">

              </td>
			</tr>';
            
            
            $upfoto = mysql_query_v("SELECT id, link, linksm from ipad_fototovar where idtovar=" . digitt($_GET['updlink']));
            if ($upfoto <> null) {
                while ($linefoto = mysql_fetch_array($upfoto, MYSQL_ASSOC)) {
                    echo "<tr><td align='right'>";
                    
                    
                    if ($local == 1) {
                        $adrfbool = str_replace('C:/wamp/www', '', $linefoto['link']);
                    } else {
                        $adrfbool = str_replace('home/aladyha/domains/polskiezakupy.pl/public_html', '', $linefoto['link']);
                    }
                    
                    if ($local == 1) {
                        $adrfsmall = str_replace('C:/wamp/www', '', $linefoto['linksm']);
                    } else {
                        $adrfsmall = str_replace('home/aladyha/domains/polskiezakupy.pl/public_html', '', $linefoto['linksm']);
                    }
                    $widhy = $smwidth / 2;
                    $hedhy = $smheight / 2;
                    
                    $adrfbool  = str_replace('/katalog', 'katalog', $adrfbool);
                    $adrfsmall = str_replace('/katalog', 'katalog', $adrfsmall);
                    
                    echo "<a class='gallery' rel='group' HREF='" . $adrfbool . "'><img class='photoramka' src='" . $adrfsmall . "' alt='' width='" . $widhy . "' height='" . $hedhy . "' border='0'></a>&nbsp;";
                    echo "</td><td>&nbsp;&nbsp;    
			  <A HREF='index.php?updlink=" . $rd . "&KeyKat=" . $tekKey . "&deadfoto=" . $linefoto['id'] . "'>Usunąć</a><br>";
                    echo "</td></tr>";
                }
            }
            
            
            
            
            
            echo '</table>';
            
            echo 'Dodawanie zdjęć:<br>
		<input type="file" size="50" onchange="addElement(this)" id="myFile1" name="file1"><br>
            
		</form>
		<input type="submit" name="add" value="Oszczędzać" class="button"  onclick="upform.submit()"/>';
            
            
            echo "<br><center>================================================</center><br><br><br>";
            
        }
        
    }
    
    //echo "Товар";
    $tovc = mysql_query_v("select count(*) as tv from ipad_tovar as k where k.menuid=" . $tekKey . " and k.testacsess>0 ");
    if ($tovc <> null) {
        while ($tovA = mysql_fetch_array($tovc, MYSQL_ASSOC)) {
            $tov = $tovA['tv'];
        }
    }
    
    
    
    if ($tov > 0) {
        //тов
        if (isset($_GET['viev'])) {
            if (GP_v($_GET['viev']) > 0) {
                // echo "Детальный просмотр";
                
                $vt = mysql_query_v("SELECT * from ipad_tovar where Id=" . GP_v($_GET['viev']) . " ");
                if ($vt <> null) {
                    while ($vtR = mysql_fetch_array($vt, MYSQL_ASSOC)) {
                        
                        //print_r($vtR);
                        echo "<br><center><table width='100%' border='0'>";
                        echo "<tr>";
                        
                        echo "<td width='100%' valign='top'>";
                        $upfoto = mysql_query_v("SELECT id, link, linksm from ipad_fototovar where idtovar=" . $vtR[Id] . " limit 1");
                        while ($linefoto = mysql_fetch_array($upfoto, MYSQL_ASSOC)) {
                            
                            if ($local == 1) {
                                $adrfbool = str_replace('C:/wamp/www', '', $linefoto['link']);
                            } else {
                                $adrfbool = str_replace('/home/aladyha/domains/polskiezakupy.pl/public_html/', '', $linefoto['link']);
                            }
                            
                            echo "<table border='0'><tr><td width='900'><center>";
                            echo "<a class='gallery' rel='group' HREF='" . $adrfbool . "'><img class='photoramka' src='" . $adrfbool . "' alt='' border='0'></a>&nbsp;";
                            
                            
                            echo "</center></td></tr></table>";
                        }
                        
                        
                        if ($vtR[obiavl] <> '') {
                            echo "<center><table width='100%' class='data-table2'>";
                            echo "<tr>";
                            echo "<td><b>Opis towarów:</b><br>" . p($vtR[obiavl]);
                            echo "</td>";
                            echo "</tr>";
                            echo "</table></center>";
                        }
                        
                        
                        echo "</td>";
                        //Параметры:<b>" . $vtR[hr] . "</b><br>
                        echo "<td valign='top'><FORM ACTION='?addcviev=" . $vtR[Id] . "&KeyKat=" . $glstr . "' METHOD='POST'>";
                        echo "<center><table border='0'>";
                        echo "<tr>";
                        echo "<td>Produkt:<b>" . $vtR[Name] . "</b><br>";
                        echo "</td>";
                        echo "</tr>";
                        
                        echo "<tr>";
                        //echo "<td><a HREF='" . $adrfbool . "'><img src='images/icon/b_firstpage.png' border='0'></a><INPUT NAME='adding'  size='3' maxlength='3' VALUE='1'><a HREF='" . $adrfbool . "'><img src='images/icon/b_lastpage.png' border='0'></a>";
                        
                        echo "<td><br>";
                        
                        echo '<table width="256px"  class="data-table3" border="0"><tr><td colspan="3"><b><center>Liczba</center></b></td><td><b><center>Cena</center></b></td></tr><tr>
			<td class="knopka"><img src="/images/minus.jpg" align="absbottom" alt="-1" onclick="javascript:minus(' . $vtR[Id] . ',' . $vtR[price] . ');return false;" class="cur" width="50" height="50"></td>
			<td class="sht"><input type="text" value="1" name="kolvo" id="kolvo" onBlur="javascript:pereschet(' . $vtR[Id] . ',' . $vtR[price] . ');return false;"></td>
			<td class="knopka"><img src="/images/plus.jpg" align="absbottom" alt="+1" onclick="javascript:plus(' . $vtR[Id] . ',' . $vtR[price] . ');return false;" width="50" height="50" class="cur"></td>
			<td><b>'.$vtR[price].'</b> zł<br><font size="2" ><div class="pri" id="price' . $vtR[Id] . '"></div></font></td></tr></table>';
                        
                        echo "</td>";
                        echo "</tr>";
                        
                        
                        
                        echo "<tr>";
                        echo "<td><input type=hidden name='addcart' value='1'>";
                        
                        echo "<table width='256px' class='data-table3'>";
                        echo "<tr><td>";
                        echo "<center><INPUT TYPE=SUBMIT class='button2' VALUE='Dodaj do koszyka'></center>";
                        echo "</td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td bgcolor='#dbd7c3'><center>Dodaj do listy / Koszyk</center>";
                        
                        echo "</td>";
                        echo "</tr>";
                        echo "</table>";
                        
                        echo "</td>";
                        echo "</tr>";
                        
                        
                        
                        
                        echo "</table></center>";
                        echo "</FORM></td>";
                        
                        echo "</tr>";
                        echo "</table></center>";
                        
                        
                        
                        
                    }
                }
            }
        } else {  
            $lims = ($page * 3 * 3) - 9;
            $limf = 9;
            
            if ($messaddtov <> '') {
                echo warn($messaddtov);
            }
            //echo $SQLZapros2;
            $SQLZapros2 = "SELECT COUNT(*) from ipad_tovar as k where k.menuid=" . $tekKey . " and k.testacsess>0";
            $itog2      = mysql_query($SQLZapros2);
            $pos2       = mysql_fetch_row($itog2);
            $maxi2      = intval($pos2[0]);
            if (($maxi2 / 3) > intval($maxi2 / 3)) {
                $stf2 = intval($maxi2 / 3) + 1;
            } else {
                $stf2 = intval($maxi2 / 3);
            }
            
               if ($sklepy<>1) {
$tovT    = mysql_query_v("select * from ipad_tovar as k where k.menuid=" . $tekKey . " and k.testacsess>0 order by testacsess desc, Id asc"); 

 } else {
                
            $tovT    = mysql_query_v("select * from ipad_tovar as k where k.menuid=" . $tekKey . " and k.testacsess>0 and  k.userid in (select ipad_userpass.ID from ipad_userpass where ipad_userpass.prava=0 and ipad_userpass.shopn=".$sklepy.") order by testacsess desc, Id asc"); }
            $newArrT = array();
            if ($tovT <> null) {
                while ($r = mysql_fetch_assoc($tovT)) //засунем выборку в массив
                    {
                    $newArrT[] = $r;
                }
                
                $cntrT = 0;
                $cntrT1 = 0;
                            $flT = 0;
                $cntLT=0;
                      $p=0;   
                if ($newArrT[0][Id] <> '') //Если товар еще есть
                    {



                    echo "<table width=100% border='0'>";
                    for ($i = 1; $i <= $stf2; $i++) {
if ($cntrT>=77777) {$cntrT=$cntrT1-$mk;}


if (($cntrT==0) and ($newArrT[0][testacsess]==2))
{ $flT = 1;
echo "<tr bgcolor='#c5d9f0'><td colspan='3'><center>Produkty dostępne w sklepie</center></td></tr>";
}





if (($cntrT<>0) and ($flT==1) and ($newArrT[$cntrT][testacsess]==1))
{echo "<tr bgcolor='#dbd7c3'><td colspan='3'><center>Produkty dostępne następnego dnia</center></td></tr>";$flT = 0;}


                        echo "<tr height=200>";




                        for ($j = 1; $j <= 3; $j++) {

if (($cntrT<>0) and ($flT==1) and ($newArrT[$cntrT][testacsess]==1) and $p==0)
{ $p=1;
//echo $cntLT;
if ((($cntLT+1)/3)==round((($cntLT+1)/3))) {$mk=1; $cntrT=77777;}
if ((($cntLT+2)/3)==round((($cntLT+2)/3))) {$mk=2;$cntrT=77777;}
//echo $cntLT;
//$j=4;
}




if ($flT==1) {
$cntLT=$cntLT+1;
}


                            echo "<td width='33%' align=center>";
                            
                            
                            echo "<table width='90%' height='175px' class='data-table2'>";
                            echo "<tr>";
                            echo "<td height='90%'>";
                            
                            if ($newArrT[$cntrT][Id] <> null) {
                                echo "<center><a href='index.php?viev=" . $newArrT[$cntrT][Id] . "&KeyKat=" . $tekKey . "'><b>" . $newArrT[$cntrT][Name] . "</b></a></center><br>";
                            }
                            
                            $upfoto = mysql_query_v("SELECT id, link, linksm from ipad_fototovar where idtovar=" . $newArrT[$cntrT][Id] . " limit 1");
                            if ($upfoto <> null) {
                                while ($linefoto = mysql_fetch_array($upfoto, MYSQL_ASSOC)) {
                                    
                                    $adrfbool = $adrs . "" . str_replace('C:/wamp/www', '/', $linefoto['link']);
                                    //  $adrfbool = $adrs . "" . str_replace('home/aladyha/domains/polskiezakupy.pl/public_html', '', $linefoto['link']);
                                    
                                    if ($local == 1) {
                                        $adrfsmall = str_replace('C:/wamp/www/', '', $linefoto['linksm']);
                                    } else {
                                        $adrfsmall = str_replace('home/aladyha/domains/polskiezakupy.pl/public_html/', '', $linefoto['linksm']);
                                    }
                                    $widhy = $smwidth / 2;
                                    $hedhy = $smheight / 2;
                                    
                                    $adrfbool = str_replace('/katalog', 'katalog', $adrfbool);
                                    
                                    
                                    echo "<center><a href='index.php?viev=" . $newArrT[$cntrT][Id] . "&KeyKat=" . $tekKey . "'><img class='photoramka' src='" . $adrfsmall . "' alt='' border='0'></a></center>";
                                    
                                }
                            }
                            
                            echo "<br></td>";
                            echo "</tr>";
                            echo "<tr>";
                            echo "<td>"; //" . $newArrT[$cntrT][curr] . "
                            if ($newArrT[$cntrT][Id] <> null) {
                                if ($newArrT[$cntrT][hr] == '') {
                                    $tty = '';
                                } else {
                                    $tty = "(" . $newArrT[$cntrT][hr] . ")";
                                }
                                echo "<a href='index.php?viev=" . $newArrT[$cntrT][Id] . "&KeyKat=" . $tekKey . "'><b>" . $newArrT[$cntrT][price] . "</b> zł " . $tty . "</a>";
                                echo $newArrT[$cntrT][sklepy];
                                if ($aut == 1 and $dost == 0 and $id==$newArrT[$cntrT][userid]) {
                                    echo "<a class='link_small_black' href='index.php?updlink=" . $newArrT[$cntrT][Id] . "&KeyKat=" . $tekKey . "'> <img src=images/icon/edit.gif></a>";
                                    echo "<a class='link_small_black' href='index.php?deadlink=" . $newArrT[$cntrT][Id] . "&KeyKat=" . $tekKey . "'> <img src=images/icon/delete.png></a>";
                                }
                            }
                            echo "</td>";
                            echo "</tr>";
                            
                            echo "</table>";
                            
            



                
                            $cntrT = $cntrT + 1;
                            $cntrT1 = $cntrT1 + 1;
                            echo "</td>";









                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                    
                    
                }
            }
        }
        //<<тов

      //if ($cntrT>=9)
       //   {
            //echo "<center><a href='#up'><img src='images/toup.jpg' border=0></a></center>";
//echo "<p id='jm-back-top' style='display: block;'><a href='#top'><span></span>&nbsp;</a></p>";
       //     }


    } //else { echo warn("Obecnie nie ma kategrii produkt");}
    
    //<<<
 
	echo "<p id='jm-back-top' style='display: block;'><a href='#top'><span></span>&nbsp;</a></p>";

    
    if ($aut == 1 and $tov == 0 and $dost == 0 and $_GET['confirmdk'] <> 1) {
        echo "<br><center><A class='link_small_black' HREF=index.php?addKat=1&KeyKat=" . $tekKey . "><img src='images/icon/Wand.png' border='0'> Dodawanie kategorii</a></center>";
    }
    
    if ($aut == 1 and $tekKey <> '0' and $dost == 0 and $kntcat == '' and $_GET['confirmdk'] <> 1) {
        
        //Если категорий нет - покажем
        echo "<br><center><a class='link_small_black' href='index.php?addKont=2&KeyKat=" . $tekKey . "'><img src='images/icon/Basket.png' border='0'><b> Dodaj ten przedmiot</b></a></center>";
        
    }
    
    
     echo '<footer class="main-footer"><div><ul class="main-footer-shortcuts">';
echo '<li><a href="?KeyKat=2">O nas</a></li>';
echo '<li><a href="?KeyKat=2">Praca</a></li>';
echo '<li><a href="?KeyKat=2">Twój Sklep</a></li>';
echo '<li><a href="?KeyKat=2">FAQ</a></li>';
echo '</ul></div></footer>';
    
    //<<<Центральная
    echo "</td>";
    echo "<td width='1px' valign='center'>";
    
    //Навигация вверх вниз
    
    
    echo "</td>";
    
    
    echo "</tr>";
    echo "</table><a name='down'></a>";
    
    //<Товар
}









?>
</div>

</center>

</body>
</html>