<?php
namespace model;
use model\Banco;


class TrilhaConteudo
{

    private $idTrilha_conteudo;

    private $trilha_id;

    private $fonte_id;

    private $ordem;

    private $obrigatorio;

    private $estimativa_min;

    /**
     *
     * @return mixed
     */
    public function getIdTrilha_conteudo()
    {
        return $this->idTrilha_conteudo;
    }

    /**
     *
     * @return mixed
     */
    public function getTrilha_id()
    {
        return $this->trilha_id;
    }

    /**
     *
     * @return mixed
     */
    public function getConteudo_id()
    {
        return $this->conteudo_id;
    }

    /**
     *
     * @return mixed
     */
    public function getOrdem()
    {
        return $this->ordem;
    }

    /**
     *
     * @return mixed
     */
    public function getObrigatorio()
    {
        return $this->obrigatorio;
    }

    /**
     *
     * @return mixed
     */
    public function getEstimativa_min()
    {
        return $this->estimativa_min;
    }

    /**
     *
     * @param mixed $idTrilha_conteudo
     */
    public function setIdTrilha_conteudo($idTrilha_conteudo)
    {
        $this->idTrilha_conteudo = $idTrilha_conteudo;
    }

    /**
     *
     * @param mixed $trilha_id
     */
    public function setTrilha_id($trilha_id)
    {
        $this->trilha_id = $trilha_id;
    }

    /**
     *
     * @param mixed $fonte_id
     */
    public function setConteudo_id($fonte_id)
    {
        $this->conteudo_id = $fonte_id;
    }

    /**
     *
     * @param mixed $ordem
     */
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }

    /**
     *
     * @param mixed $obrigatorio
     */
    public function setObrigatorio($obrigatorio)
    {
        $this->obrigatorio = $obrigatorio;
    }

    /**
     *
     * @param mixed $estimativa_min
     */
    public function setEstimativa_min($estimativa_min)
    {
        $this->estimativa_min = $estimativa_min;
    }

    function __construct($idFonte, $nome_fonte)
    {
        $this->idFonte = $idFonte;
        $this->idFonte = $nome_fonte;

        return $this;
    }

    public static function create($fonte)
    {
        $sql = 'INSERT INTO conteudo (idFonte, titulo, categoria) VALUES (?,?,?)';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->bindParam($fonte->getidFonte(), $fonte->gettitulo(), $fonte->getCategoria());
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
        $sql = "SELECT idConteudo, idFonte, titulo, categoria, dificuldade, link, duracao_min FROM conteudo WHERE idConteudo = $id";
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $item = $resultSet->fetch();
        $fonte = new Conteudo($item['idConteudo'], $item['idFonte'], $item['titulo'], $item['categoria'], $item['dificuldade'], $item['dificuldade'], $item['link'], $item['duracao_min']);
        return $fonte;
    }
}

?>