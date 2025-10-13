<?php
namespace model;

require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/Banco.php');

class Aluno
{

    private $idAluno;

    private $nome;

    private $email;

    private $senha;

    private $data_nascimento;

    private $criacao_em;

    function __construct($idAluno, $nome, $email, $senha, $data_nascimento, $criacao_em)
    {
        $this->idAluno = $idAluno;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->data_nascimento = $data_nascimento;
        $this->criacao_em = $criacao_em;

        return $this;
    }

    public function getIdAluno()
    {
        return $this->idAluno;
    }

    public function setIdAluno($idAluno)
    {
        $this->idAluno = $idAluno;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
    }

    public function getDataNascimento()
    {
        return $this->data_nascimento;
    }

    public function setDataNascimento($data_nascimento)
    {
        $this->data_nascimento = $data_nascimento;
    }

    public function getCriacaoEm()
    {
        return $this->criacao_em;
    }

    public function setCriacaoEm($criacaoEm)
    {
        $this->criacao_em = $criacaoEm;
    }

    public static function create($Aluno)
    {
        $sql = 'INSERT INTO Aluno (nome, email, senha data_nascimento) VALUES (?,?,?)';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->bindParam($Aluno->getNome(), $Aluno->getEmail(), $Aluno->getSenha(), $Aluno->getDataNascimento());
        $stmt->execute();
    }

    public static function update($Aluno)
    {
        $sql = 'UPDATE Aluno SET nome = ?, email = ?, senha = ?, data_nascimento = ? WHERE idAluno = ?';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->bindParam($Aluno->getNome(), $Aluno->getEmail(), $Aluno->getSenha(), $Aluno->getDataNascimento(), $Aluno->getIdAluno());
        $stmt->execute();
    }

    public static function selectTodos()
    {
        $sql = 'SELECT idAluno, nome, email, senha data_nascimento, criacao_em FROM aluno';
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $listaRS = $resultSet->fetchAll();

        $listaAlunos = [];
        foreach ($listaRS as $item) {
            $listaAlunos[] = new Aluno($item['idAluno'], $item['nome'], $item['email'], $item['senha'], $item['data_nascimento'], $item['criacao_em']);
        }

        return $listaAlunos;
    }

    public static function selectPorId($id)
    {
        $sql = "SELECT idAluno, nome, email, senha, data_nascimento, criacao_em FROM aluno WHERE idAluno = $id";
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $item = $resultSet->fetch();
        $Aluno = new Aluno($item['idAluno'], $item['nome'], $item['email'], $item['senha'], $item['data_nascimento'], $item['criacao_em']);
        return $Aluno;
    }
}
