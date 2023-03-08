<?php

namespace block_extensao\Service;

require_once('USPDatabase.php');

class Query 
{
    public static function coursesFromThispYear(){
        $year = date('Y');
        $query = "SELECT codcurceu,nomcurceu,dtainc FROM CURSOCEU WHERE dtainc LIKE '%{$year}%'";
        return USPDatabase::fetchAll($query);
    }

}