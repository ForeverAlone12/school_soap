<?php

/**
 * Функция выполнения запроса и возвращает ассоциативный массив, либо сообщение 
 * об ошибке
 * 
 * @global ADODB $conn2
 * @param string $query
 * @return string|array
 */
function executeQuery($query) {
    global $conn;

    if ($query == "") {
        $result = "ERROR: Пустой запрос";
    } else {
        $res = $conn->Execute($query);
        if ($res) {
            $query = strtolower(substr(trim($query), 0, 10));
            if (substr_count($query, "select") == 1) {
                if ($res->numRows() > 0) {
                    $result = array();                    
                    while ($data = $res->FetchRow()) {                       
                         array_push($result, $data);
                    }
                } else {
                    $result = "";
                }
            } elseif (substr_count($query, "insert") == 1) {
                $result = $conn->insert_Id();
            } 
        } else {
            $result = "ERROR: SQL " . $connection->ErrorMsg() . " QUERY:" . $query;
        }
    }
    return $result;
}

function addDevice($device_name){
    
}

function addСharacteristic($characteristic_name, $device_id){
    
}

function getDevice_Id($device_name){
    
}

function getСharacteristic_Id($characteristic_name){
    
}

function getListSystem(){
    $query_listSystem="
        SELECT `name`,`subcribe`, `path_to_image`, `alt_image`, `url`
        FROM system
        ";
          
    $rez = executeQuery($query_listSystem);
    if (count($rez) < 1) {
        $rez = "Не найдены данные о системах!!!";
    }
    return $rez;
            
            
}