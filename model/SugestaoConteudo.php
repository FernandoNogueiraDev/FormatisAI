<?php
namespace model;

use model\Banco;

class SugestaoConteudo
{

    private $idSugestao_conteudo;

    private $aluno_id;

    private $trilhaConteudo_id;

    private $titulo_sugerido;

    private $motivo;

    private $origem;

    private $aceito;

    private $criado_em;

   

    /**
     * @return mixed
     */
    public function getIdSugestao_conteudo()
    {
        return $this->idSugestao_conteudo;
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
    public function getTrilhaConteudo_id()
    {
        return $this->trilhaConteudo_id;
    }

    /**
     * @return mixed
     */
    public function getTitulo_sugerido()
    {
        return $this->titulo_sugerido;
    }

    /**
     * @return mixed
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * @return mixed
     */
    public function getOrigem()
    {
        return $this->origem;
    }

    /**
     * @return mixed
     */
    public function getAceito()
    {
        return $this->aceito;
    }

    /**
     * @return mixed
     */
    public function getCriado_em()
    {
        return $this->criado_em;
    }

    /**
     * @param mixed $idSugestao_conteudo
     */
    public function setIdSugestao_conteudo($idSugestao_conteudo)
    {
        $this->idSugestao_conteudo = $idSugestao_conteudo;
    }

    /**
     * @param mixed $aluno_id
     */
    public function setAluno_id($aluno_id)
    {
        $this->aluno_id = $aluno_id;
    }

    /**
     * @param mixed $trilhaConteudo_id
     */
    public function setTrilhaConteudo_id($trilhaConteudo_id)
    {
        $this->trilhaConteudo_id = $trilhaConteudo_id;
    }

    /**
     * @param mixed $titulo_sugerido
     */
    public function setTitulo_sugerido($titulo_sugerido)
    {
        $this->titulo_sugerido = $titulo_sugerido;
    }

    /**
     * @param mixed $motivo
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;
    }

    /**
     * @param mixed $origem
     */
    public function setOrigem($origem)
    {
        $this->origem = $origem;
    }

    /**
     * @param mixed $aceito
     */
    public function setAceito($aceito)
    {
        $this->aceito = $aceito;
    }

    /**
     * @param mixed $criado_em
     */
    public function setCriado_em($criado_em)
    {
        $this->criado_em = $criado_em;
    }

    public static function create($TrilhaConteudo, $banco)
    {
        $sql = 'INSERT INTO sugestao_conteudo (aluno_id, trilhaConteudo_id, titulo_sugerido, motivo, origem, aceito) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $banco->conexao()->prepare($sql);

        $aluno_id = $TrilhaConteudo->getAluno_id();
        $trilhaConteudo_id = $TrilhaConteudo->getTrilhaConteudo_id();
        $titulo_sugerido = $TrilhaConteudo->getTitulo_sugerido();
        $motivo = $TrilhaConteudo->getMotivo();
        $origem = $TrilhaConteudo->getOrigem();
        $aceito = $TrilhaConteudo->getAceito();
        $stmt->bindParam(1, $aluno_id);
        $stmt->bindParam(2, $trilhaConteudo_id);
        $stmt->bindParam(3, $titulo_sugerido);
        $stmt->bindParam(4, $motivo);
        $stmt->bindParam(5, $origem);
        $stmt->bindParam(6, $aceito);

        $stmt->execute();
        $id = $banco->conexao()->lastInsertId();
        $TrilhaConteudo->setIdSugestao_conteudo($id);

        return $TrilhaConteudo;
    }

    public static function update($fonte)
    {
        $sql = 'UPDATE sugestao_conteudo SET idConteudo = ?, idFonte = ?, titulo = ?, categoria = ?, dificuldade = ?, link = ?, duracao_min = ? WHERE idConteudo = ?';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->bindParam($fonte->getidFonte(), $fonte->gettitulo(), $fonte->getCategoria(), $fonte->getId());
    }

    public static function selectTodos()
    {
        $sql = 'SELECT idConteudo, idFonte, titulo, categoria, dificuldade, link, duracao_min FROM sugestao_conteudo';
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
        $sql = "SELECT idConteudo, idFonte, titulo, categoria, dificuldade, link, duracao_min FROM sugestao_conteudo WHERE idConteudo = $id";
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $item = $resultSet->fetch();
        $fonte = new Conteudo($item['idConteudo'], $item['idFonte'], $item['titulo'], $item['categoria'], $item['dificuldade'], $item['dificuldade'], $item['link'], $item['duracao_min']);
        return $fonte;
    }
}

?>