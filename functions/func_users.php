<?php

/**
 * Получение данных работника: Фамилия, имя, отчество, дата рождения, стаж работы, должность, ИНН, СНИЛС, пол.
 * @global type $conn 
 * @return если сотрудник найден - данные работника: Фамилия, имя, отчество, стаж, дата рождения;
 *          иначе - ошибка "Не найдены данные о работниках"
 */
function GetUzvers() {
    global $conn;

    $query_uzvers = "
                    SELECT u.`id`, u.`surname`, u.`name`, u.`secondname`, u.`birthday`, 
                    Expa(u.`id`) AS expa, getPosition(u.`id`) AS position, u.`inn`, u.`snils`, u.`sex`, ac.`status`                         
                    FROM Uzvers u 
                    LEFT JOIN Access ac ON ac.`id_uzvers`=u.`id` 
                    WHERE ac.`status`=1
                    ";

    $rez_uzvers = $conn->Execute($query_uzvers);

    $uzversData = array();

    if ($rez_uzvers && $rez_uzvers->numrows() > 0) {
        while ($uzver = $rez_uzvers->FetchRow()) {
            array_push($uzversData, $uzver);
        }
    } else {
        $uzversData = "Не найдены данные о работника!!! " . $conn->ErrorMsg();
    }
    return $uzversData;
}

/**
 * Получение периодов работы работника
 * @param type $id - идентификатор работника
 */
function getUzversExpa($id) {
    global $conn;
    $rez = array();
    $query_uzvers = "
                    SELECT e.`id`, e.`date_start`, e.`date_end`        
                    FROM Experience e
                    WHERE e.`id_uzvers`='{$id}'
                    ";
//    $rez_expa = $conn->Execute($query_uzvers);
//    if ($rez_expa && $rez_expa->numrows() > 0) {
//        while (list($id, $date_start, $date_end) = $rez_expa->FetchRow()) {
//            array_push($rez, array(
//                "id" => $id,
//                "date_start" => $date_start,
//                "date_end" => $date_end
//            ));
//        }
//    }

    $rez = executeQuery($query_uzvers);
    if (count($rez) < 1) {
        $rez = "Не найдены данные о периодах работника работника!!!";
    }
    return $rez;
}

/**
 * Занесение данных работника в БД
 * @global type $conn
 * @param type $name имя
 * @param type $surname фамилия
 * @param type $secondname отчество
 * @param type $birthday дата рождения
 * @param type $position должность
 * @param type $date_start даты начал работы
 * @param type $date_end даты завершения работ
 */
function createUzvers($name, $surname, $secondname, $birthday, $position, $date_start, $date_end, $inn, $snils, $sex) {
    global $conn;

    $query_add_uzver = "INSERT INTO Uzvers (`surname`, `name`, `secondname`, `birthday`, `inn`, `snils`, `sex`)  
        VALUES ('{$surname}','{$name}','{$secondname}','{$birthday}','{$inn}','{$snils}','{$sex}')";

    $rez_add_uzver = $conn->Execute($query_add_uzver);
    $uzver_id = $conn->insert_Id();

    $rez = createUzverExpa($uzver_id, $date_start, $date_end);

    createUzverPosition($uzver_id, $position);
    createUzverStatus($uzver_id);

    return $rez;
}

/**
 * Занесение в БД периодов работы
 * @global type $conn
 * @param type $id идентификатор работника
 * @param type $start даты начала работ
 * @param type $end даты завершения работ
 * @return type
 */
function createUzverExpa($id, $start, $end) {
    global $conn;

    $query_add_expa = "INSERT INTO Experience(`id_uzvers`, `date_start`, `date_end`) 
            VALUES ('{$id}','{$start}', '{$end}')";
    $rez_add_expa = $conn->Execute($query_add_expa);

    $rez = ($rez_add_expa == FALSE) ? $conn->ErrorMsg() : $rez_add_expa;

    return $rez;
}

function createUzverPosition($id, $position) {
    global $conn;

    $query_uzver_position = "INSERT INTO uzvers_position(`id_uzver`,`id_position`) 
        VALUES ('{$id}','{$position}')";
    $rez_position = $conn->Execute($query_uzver_position);

    return $rez_position;
}

function createUzverStatus($id) {
    global $conn;

    $query_uzver_position = "INSERT INTO access(`id_uzvers`,`status`) 
        VALUES ('{$id}','1')";
    $rez_position = $conn->Execute($query_uzver_position);

    return $rez_position;
}

/**
 * Проверка аутентификации.
 * Если пользователь найден, то возвращается его идентификатор.
 * @global type $conn
 * @param type $login логин
 * @param type $password пароль
 * @return type $id - идентификатор работника
 */
function CheckUserLogin($login, $password) {
    $login = trim($login);
    $password = trim($password);

    $query = "SELECT id_uzvers, status
              FROM Access
              WHERE login = '{$login}' AND password = '{$password}'
            ";

//    $rez = $conn->Execute($query);
//    if ($rez && $rez->numrows() > 0) {
//        while (list($id_uzvers, $status) = $rez->FetchRow()) {
//            array_push($access, array(
//                "id_uzvers" => $id_uzvers,
//                "status" => $status
//            ));
//        }
//    }

    $access = executeQuery($query);

    if (count($access) != 1) {
        $access = "Проблема с аутентификацией ";
    }
    return $access;
}

/**
 * Получение общей информации о пользователе
 * @global type $conn
 * @param type $id
 * @return string
 */
function UzverCommonInfo($login) {

    $query = "SELECT ac.`level_access`, ac.`status`, uz.`surname`, uz.`name`, uz.`secondname`
              FROM Access ac INNER JOIN Uzvers uz ON ac.`id_uzvers`=uz.`id`
              WHERE ac.`login`='{$login}'
            ";

//    $uzverInfo = array();
//    $rez = $conn->Execute($query);
//    if ($rez && $rez->numrows() > 0) {
//        while (list($level_access, $status, $surname, $name, $secondname) = $rez->FetchRow()) {
//            array_push($uzverInfo, array(
//                "level_access" => $level_access,
//                "status" => $status,
//                "surname" => $surname,
//                "name" => $name,
//                "secondname" => $secondname
//            ));
//        }
//    }
    $uzverInfo = executeQuery($query);

    if (count($uzverInfo) > 1) {
        $uzverInfo = "Не найдены данные о работнике!!!";
    }
    return $uzverInfo;
}

/**
 * Получение списка должностей
 * @param type $name_position
 * @return type
 */
function getListPosition($name_position) {
    global $conn;
    $query_position = "
        SELECT id, full_position
        FROM Position
        WHERE full_position LIKE '%{$name_position}%'
        ";
    //  return $query_position;

    $rez = $conn->Execute($query_position);
    $pos = array();
    if ($rez && $rez->numrows() > 0) {
        while (list($id, $full, $small) = $rez->FetchRow()) {
            array_push($pos, array(
                "id" => $id,
                "full_position" => $full
            ));
        }
    }
    $rez = executeQuery($query_position);
    return $pos;
}

function getPosition() {
    global $conn;
    $query_position = "
        SELECT id, full_position
        FROM position
        ";
    //  return $query_position;

    $rez = $conn->Execute($query_position);
    $pos = array();
    if ($rez && $rez->numrows() > 0) {
        while (list($id, $full) = $rez->FetchRow()) {
            array_push($pos, array(
                "id" => $id,
                "full_position" => $full
            ));
        }
    }
//    $rez = executeQuery($query_position);
    return $pos;
}

function isSetPosition($id, $position) {

    $query = "
        SELECT COUNT(full_position) AS count_position
        FROM position
        WHERE id ='{$id}'
        ";
    $rez = executeQuery($query);

    // должно не существует
    if ($rez[0]["count_position"] == 0) {
        setPosition($position);
    } else {
        updatePosition($id, $position);
    }
//    // должноости нет
    return $rez;
}

function setPosition($position) {
    $query = "
        INSERT INTO position (full_position)
        VALUES ('{$position}')
        ";
    $rez = executeQuery($query);
}

function updatePosition($id, $position) {

    $query = "
        UPDATE position
        SET full_position='{$position}'
        WHERE id='{$id}'
    ";
    $rez = executeQuery($query);
    return $rez;
}

function editUzver() {
    
}

function delUzver($id) {
    $query_delUzver = "
        UPDATE access
        SET `status`=0
        WHERE `id_uzvers`='{$id}'
        ";
    $rez = executeQuery($query_delUzver);
    return $query_delUzver;
}
