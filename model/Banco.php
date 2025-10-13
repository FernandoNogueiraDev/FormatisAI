<?php
namespace model;

use PDO;


class Banco extends PDO {
    private $DB_NAME = 'plataformaestudos';
    private $DB_USER = 'root';
    private $DB_PASSWORD = '';
    private $DB_HOST = '127.0.0.1';    
    private $conexao;


    public function __construct(){
        $this->conexao = new PDO("mysql:host=$this->DB_HOST;dbname=$this->DB_NAME", $this->DB_USER, $this->DB_PASSWORD);
    }

    public function conexao(){
        return $this->conexao;
    }
}

?>