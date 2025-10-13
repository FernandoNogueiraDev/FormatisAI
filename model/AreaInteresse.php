<?php
namespace model;
use model\Banco;


class AreaInteresse
{

    private $idArea_interesse;

    private $nome_area;

    function __construct($idArea_interesse, $nome_area)
    {
        $this->idArea_interesse = $idArea_interesse;
        $this->nome_area = $nome_area;

        return $this;
    }

    public function getidArea_interesse()
    {
        return $this->idArea_interesse;
    }

    public function setidArea_interesse($idArea_interesse)
    {
        $this->idArea_interesse = $idArea_interesse;
    }

    public function getnome_area()
    {
        return $this->nome_area;
    }

    public function setnome_area($nome_area)
    {
        $this->nome_area = $nome_area;
    }

    public static function create($AreaInteresse)
    {
        $sql = 'INSERT INTO area_interesse (nome_area) VALUES (?)';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->execute($AreaInteresse->getnome_area());
    }

    public static function update($AreaInteresse)
    {
        $sql = 'UPDATE area_interesse SET nome_area = ? WHERE idArea_interesse = ?';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->execute($AreaInteresse->getnome_area());
    }

    public static function selectTodos()
    {
        $sql = 'SELECT idArea_interesse, nome_area FROM area_interesse';
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $listaRS = $resultSet->fetchAll();

        $listaAlunos = [];
        foreach ($listaRS as $item) {
            $listaAlunos[] = new Aluno($item['idArea_interesse'], $item['nome_area'], $item['email'], $item['senha'], $item['data_nascimento'], $item['criacao_em']);
        }

        return $listaAlunos;
    }

    public static function selectPorId($id)
    {
        $sql = "SELECT idArea_interesse, nome_area FROM aluno WHERE idArea_interesse = $id";
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $item = $resultSet->fetch();
        $Aluno = new Aluno($item['idArea_interesse'], $item['nome_area'], $item['email'], $item['senha'], $item['data_nascimento'], $item['criacao_em']);
        return $Aluno;
    }
}
