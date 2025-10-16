<?php
namespace model;

require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/Fonte.php');


class Conteudo
{

    private $idConteudo;

    private $fonte_id;

    private $titulo;

    private $descricao;

    private $categoria;

    private $dificuldade;

    private $link;

    private $duracao_min;

    function __construct($idConteudo, $fonte_id, $titulo, $descricao, $categoria, $dificuldade, $link, $duracao_min)
    {
        $this->idConteudo = $idConteudo;
        $this->fonte_id = $fonte_id;
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->categoria = $categoria;
        $this->dificuldade = $dificuldade;
        $this->link = $link;
        $this->duracao_min = $duracao_min;

        return $this;
    }
    
    public static function constroiVazio(){
        return new self(null, null, null, null, null, null, null, null);
    }

    public function getIdConteudo()
    {
        return $this->idConteudo;
    }

    public function setIdConteudo($idConteudo)
    {
        $this->idConteudo = $idConteudo;
    }

    public function getfonte_id()
    {
        return $this->fonte_id;
    }

    public function setfonte_id($fonte_id)
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
    
    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->descricao;
    }
    
    /**
     * @param mixed $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
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

    public function setDuracao_min($duracao_min)
    {
        $this->duracao_min = $duracao_min;
    }

    public static function create($conteudo)
    {
        $sql = 'INSERT INTO conteudo (fonte_id, titulo, descricao, categoria, dificuldade, link, duracao_min) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        
        $fonteId = $conteudo->getfonte_id();
        $titulo = $conteudo->getTitulo();
        $descricao = $conteudo->getDescricao();
        $categoria = $conteudo->getCategoria();
        $dificuldade = $conteudo->getDificuldade();
        $link = $conteudo->getLink();
        $duracao_min = $conteudo->getDuracao_min();
        $stmt->bindParam(1, $fonteId);
        $stmt->bindParam(2, $titulo);
        $stmt->bindParam(3, $descricao);
        $stmt->bindParam(4, $categoria);
        $stmt->bindParam(5, $dificuldade);
        $stmt->bindParam(6, $link);
        $stmt->bindParam(7, $duracao_min);
        
        $stmt->execute();
        $id = $banco->conexao()->lastInsertId();
        $conteudo->setIdConteudo();
        
        return $conteudo;
    }

    public static function update($conteudo)
    {
        $sql = 'UPDATE conteudo SET fonte_id = ?, titulo = ?, descricao = ?, categoria = ?, dificuldade = ?, link = ?, duracao_min = ? WHERE idConteudo = ?';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->bindParam($conteudo->getfonte_id(), $conteudo->getTitulo(), $conteudo->getDescricao(), $conteudo->getDificuldade(),
            $conteudo->getLink(), $conteudo->getDuracao_min(), $conteudo->getIdConteudo());
        $stmt->execute();
    }

    public static function selectTodos()
    {
        $sql = 'SELECT idConteudo, fonte_id, titulo, descricao, categoria, dificuldade, link, duracao_min FROM conteudo';
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $listaRS = $resultSet->fetchAll();

        $listaconteudos = [];
        foreach ($listaRS as $item) {
            $listaconteudos[] = new Conteudo($item['idConteudo'], $item['fonte_id'], $item['titulo'], $item['descricao'], $item['categoria'], $item['dificuldade'], $item['dificuldade'], $item['link'], $item['duracao_min']);
        }

        return $listaconteudos;
    }

    public static function selectPorId($id)
    {
        $sql = "SELECT idConteudo, fonte_id, titulo, descricao, categoria, dificuldade, link, duracao_min FROM conteudo WHERE idConteudo = :id";
        
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->bindParam(':id', $id);
        $item = $stmt->fetch();
        $conteudo = new Conteudo($item['idConteudo'], $item['fonte_id'], $item['titulo'], $item['descricao'], $item['categoria'], $item['dificuldade'], $item['dificuldade'], $item['link'], $item['duracao_min']);
        return $conteudo;
    }
}
