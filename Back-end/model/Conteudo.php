<?php

require_once './Banco.php';

class Conteudo {
    private $idConteudo; 
    private $fonte_id;
    private $titulo;
    private $categoria;
    private $dificuldade;
    private $link;
    private $duracao_min;


    function __construct($idConteudo, $fonte_id, $titulo, $categoria, $dificuldade, $link, $duracao_min)
    {
        $this->idConteudo = $idConteudo;
        $this->fonte_id = $fonte_id;
        $this->titulo = $titulo;
        $this->categoria = $categoria;
        $this->dificuldade = $dificuldade;
        $this->link = $link;
        $this->duracao_min = $duracao_min;

        return $this;
    }

    public function getIdConteudo()
    {
        return $this->idConteudo;
    }

    public function setIdConteudo($idConteudo)
    {
        $this->idConteudo = $idConteudo;
    }

    public function getFonte_id()
    {
        return $this->fonte_id;
    }

    public function setFonte_id($fonte_id)
    {
        $this->fonte_id = $fonte_id;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;
    }

    public function getDificuldade()
    {
        return $this->dificuldade;
    }

    public function setDificuldade($dificuldade)
    {
        $this->dificuldade = $dificuldade;
    }

     public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }


     public function getDuracao_min()
    {
        return $this->duracao_min;
    }

    public function setCriacaoEm($duracao_min)
    {
        $this->duracao_min = $duracao_min;
    }

    public static function create($conteudo) {
        $sql = 'INSERT INTO conteudo (fonte_id, titulo, categoria) VALUES (?,?,?)';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->bindParam($conteudo->getfonte_id(), $conteudo->gettitulo(), $conteudo->getCategoria());
    }

    public static function update($conteudo) {
        $sql = 'UPDATE conteudo SET idConteudo = ?, fonte_id = ?, titulo = ?, categoria = ?, dificuldade = ?, link = ?, duracao_min = ? WHERE idConteudo = ?';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->bindParam($conteudo->getfonte_id(), $conteudo->gettitulo(), $conteudo->getCategoria(), $conteudo->getId());
    }

    public static function selectTodos() {
        $sql = 'SELECT idConteudo, fonte_id, titulo, categoria, dificuldade, link, duracao_min FROM conteudo';
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $listaRS = $resultSet->fetchAll();

        $listaconteudos = [];
        foreach($listaRS as $item){
            $listaconteudos[] = new Conteudo($item['idConteudo'], $item['fonte_id'], $item['titulo'], $item['categoria'], $item['dificuldade'], $item['dificuldade'], $item['link'], $item['duracao_min']);
        }

        return $listaconteudos;
    }

    public static function selectPorId($id) {
        $sql = "SELECT idConteudo, fonte_id, titulo, categoria, dificuldade, link, duracao_min FROM conteudo WHERE idConteudo = $id";
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $item = $resultSet->fetch();
        $conteudo = new Conteudo($item['idConteudo'], $item['fonte_id'], $item['titulo'], $item['categoria'], $item['dificuldade'], $item['dificuldade'], $item['link'], $item['duracao_min']);
        return $conteudo;
    }
}
