<?php

namespace block_extensao;

use PDO;

class Sybase
{
    private static $instance;
    /**
     * Singleton sybase connection
     */
    public static function getInstance(){
        global $CFG;

        $host = $CFG->sybase_host;
        $port = $CFG->sybase_port;
        $db   = $CFG->sybase_dbname;
        $user = $CFG->sybase_user;
        $pass = $CFG->sybase_password;
        
        if (!self::$instance) {
            try {
                $dsn = "dblib:host={$host}:{$port};dbname={$db};charset=UTF-8";
                self::$instance = new PDO($dsn,$user,$pass);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            } catch (\Throwable $t) {
                #print_r($t->getMessage());
                echo "Erro na conexão com o database do replicado! Contate o suporte";
                die();
            }
        }
        return self::$instance;
    }

    public static function utf8_converter($array)
    {
        array_walk_recursive($array, function (&$item, $key) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });
        return $array;
    }

    // overhide fetch
    public static function fetch(string $query, array $param = null)
    {
        try {
            $stmt = self::getInstance()->prepare($query);
            if (!is_null($param)) {
                foreach ($param as $campo => $valor) {
                    $valor = utf8_decode($valor);
                    $stmt->bindValue(":$campo", $valor);
                }
            }
            $stmt->execute();
        } catch (\Throwable $t) {
            #print_r($t->getMessage());
            echo "Erro Interno no replicado: contate o suporte!";
            return false;
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($result)) {
            $result = self::utf8_converter($result);
        }
        return $result;
    }

    public static function fetchAll(string $query, array $param = null)
    {
        try {
            $stmt = self::getInstance()->prepare($query);
            if (!is_null($param)) {
                foreach ($param as $campo => $valor) {
                    $valor = utf8_decode($valor);
                    $stmt->bindValue(":$campo", $valor);
                }
            }
            $stmt->execute();
        } catch (\Throwable $t) {
            #print_r($t->getMessage());
            echo "Erro Interno no replicado: contate o suporte!";
            return false;
        }

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($result)) {
            $result = self::utf8_converter($result);
        }
        return $result;
    }

    /**
     * Método que devolve um array com os cursos em que
     * o $codpes é ministrante.
     */
    public static function oferecimentos($codpes){
        $year = '2020';
        $query = "
            SELECT 
            DISTINCT t1.codofeatvceu,
            t1.dtainiofeatv,
            t1.dtafimofeatv,
            t2.nomcurceu,
            t3.codpes,
            t3.codatc,
            t4.sglund
        
            FROM OFERECIMENTOATIVIDADECEU t1
            
            INNER JOIN CURSOCEU t2
            ON t1.codcurceu = t2.codcurceu
            
            INNER JOIN MINISTRANTECEU t3
            ON t1.codofeatvceu = t3.codofeatvceu
            
            INNER JOIN UNIDADE t4
            ON t2.codund = t4.codund
            
            WHERE (t1.dtainiofeatv >= '{$year}-01-01' AND t1.dtainiofeatv <= '{$year}-12-31')
            AND codpes = convert(int,:codpes)
        ";
        
        $param = [
            'codpes' => $codpes,
        ];

        return self::fetchAll($query,$param);
    }

    /**
     * Método que devolve um array com os cursos em que
     * o $codpes é ministrante.
     */
    public static function curso($codofeatvceu){
        $year = '2020';
        $query = "
            SELECT 
            DISTINCT t1.codofeatvceu,
            t1.dtainiofeatv,
            t1.dtafimofeatv,
            t2.nomcurceu,
            t3.codpes,
            t3.codatc,
            t4.sglund
        
            FROM OFERECIMENTOATIVIDADECEU t1
            
            INNER JOIN CURSOCEU t2
            ON t1.codcurceu = t2.codcurceu
            
            INNER JOIN MINISTRANTECEU t3
            ON t1.codofeatvceu = t3.codofeatvceu
            
            INNER JOIN UNIDADE t4
            ON t2.codund = t4.codund
            
            WHERE t1.codofeatvceu = convert(int,:codofeatvceu)
        ";
        
        $param = [
            'codofeatvceu' => $codofeatvceu,
        ];

        return self::fetch($query,$param);
        
    }

}
