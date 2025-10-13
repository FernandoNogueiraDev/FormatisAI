<?php
namespace model;

require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/Banco.php');

class Fonte
{

    private $idFonte;

    private $nome_fonte;

    function __construct($idFonte, $nome_fonte)
    {
        $this->idFonte = $idFonte;
        $this->idFonte = $nome_fonte;

        return $this;
    }

    public static function constroiVazio()
    {
        return new self(null, null);
    }

    /**
     *
     * @return mixed
     */
    public function getIdFonte()
    {
        return $this->idFonte;
    }

    /**
     *
     * @param mixed $idFonte
     */
    public function setIdFonte($idFonte)
    {
        $this->idFonte = $idFonte;
    }

    /**
     *
     * @return mixed
     */
    public function getNome_fonte()
    {
        return $this->nome_fonte;
    }

    /**
     *
     * @param mixed $nome_fonte
     */
    public function setNome_fonte($nome_fonte)
    {
        $this->nome_fonte = $nome_fonte;
    }

    public static function create($fonte)
    {
        $sql = 'INSERT INTO fonte (nome_fonte) VALUES (:nome)';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $nome_fonte = $fonte->getNome_fonte();
        $stmt->bindParam(':nome', $nome_fonte);
        $stmt->execute();
        $id = $banco->conexao()->lastInsertId();
        $fonte->setIdFonte($id);
        return $fonte;
    }

    public static function update($fonte)
    {
        $sql = 'UPDATE fonte SET nome_fonte = ? WHERE idFonte = ?';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->bindParam($fonte->getNome_fonte(), $fonte->getId());
    }

    public static function selectTodos()
    {
        $sql = 'SELECT idFonte, nome_fonte FROM fonte';
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $listaRS = $resultSet->fetchAll();

        $listaconteudos = [];
        foreach ($listaRS as $item) {
            $listaconteudos[] = new Fonte($item['idFonte'], $item['nome_fonte']);
        }

        return $listaconteudos;
    }

    public static function selectPorId($id)
    {
        $sql = "SELECT idFonte, nome_fonte FROM fonte WHERE idFonte = :id";
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $item = $stmt->fetch();
        $fonte = new Fonte($item['idFonte'], $item['nome_fonte']);
        return $fonte;
    }

    public static function selectPorNome($nome)
    {
        $sql = "SELECT idFonte, nome_fonte FROM fonte WHERE nome_fonte = :nome";
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $listaRS = $stmt->fetch();

        if ($listaRS === false) {
            return null;
        }
        $listaItens = [];
        foreach ($listaRS as $item) {
            $listaItens[] = new Fonte($item['idFonte'], $item['nome_fonte']);
        }

        return $listaItens;
    }
}
