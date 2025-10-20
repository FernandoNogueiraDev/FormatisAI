<?php
namespace model;

use model\Banco;

class TrilhaEstudo
{

    private $idTrilha_estudo;

    private $aluno_id;

    private $titulo;

    private $descricao;

    private $status;


    /**
     * @return mixed
     */
    public function getIdTrilha_estudo()
    {
        return $this->idTrilha_estudo;
    }

    /**
     * @return mixed
     */
    public function getAluno_id()
    {
        return $this->aluno_id;
    }

    /**
     * @return mixed
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $idTrilha_estudo
     */
    public function setIdTrilha_estudo($idTrilha_estudo)
    {
        $this->idTrilha_estudo = $idTrilha_estudo;
    }

    /**
     * @param mixed $aluno_id
     */
    public function setAluno_id($aluno_id)
    {
        $this->aluno_id = $aluno_id;
    }

    /**
     * @param mixed $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    /**
     * @param mixed $descricao
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public static function create($TrilhaEstudo, $banco)
    {
        $sql = 'INSERT INTO trilha_estudo (aluno_id, titulo, descricao, status) VALUES (?, ?, ?, ?)';
        $stmt = $banco->conexao()->prepare($sql);

        $alunoId = $TrilhaEstudo->getAluno_id();
        $titulo = $TrilhaEstudo->getTitulo();
        $descricao = $TrilhaEstudo->getDescricao();
        $status = $TrilhaEstudo->getStatus();
        $stmt->bindParam(1, $alunoId);
        $stmt->bindParam(2, $titulo);
        $stmt->bindParam(3, $descricao);
        $stmt->bindParam(4, $status);

        $stmt->execute();
        $id = $banco->conexao()->lastInsertId();
        $TrilhaEstudo->setIdTrilha_estudo($id);

        return $TrilhaEstudo;
    }

    public static function update($fonte)
    {
        $sql = 'UPDATE conteudo SET idConteudo = ?, idFonte = ?, titulo = ?, categoria = ?, dificuldade = ?, link = ?, duracao_min = ? WHERE idConteudo = ?';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->bindParam($fonte->getidFonte(), $fonte->gettitulo(), $fonte->getCategoria(), $fonte->getId());
    }

    public static function selectTodos()
    {
        $sql = 'SELECT idConteudo, idFonte, titulo, categoria, dificuldade, link, duracao_min FROM conteudo';
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $listaRS = $resultSet->fetchAll();

        $listaconteudos = [];
        foreach ($listaRS as $item) {
            $listaconteudos[] = new Conteudo($item['idConteudo'], $item['idFonte'], $item['titulo'], $item['categoria'], $item['dificuldade'], $item['dificuldade'], $item['link'], $item['duracao_min']);
        }

        return $listaconteudos;
    }

    public static function selectPorId($id)
    {
        $sql = "SELECT trilha_estudo, idFonte, titulo, categoria, dificuldade, link, duracao_min FROM trilha_estudo WHERE idConteudo = $id";
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $item = $resultSet->fetch();
        $fonte = new Conteudo($item['idConteudo'], $item['idFonte'], $item['titulo'], $item['categoria'], $item['dificuldade'], $item['dificuldade'], $item['link'], $item['duracao_min']);
        return $fonte;
    }
}

?>