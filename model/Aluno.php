<?php
namespace model;

use DateTime;

require_once ($_SERVER['DOCUMENT_ROOT'] . '/model/Banco.php');

class Aluno
{

    private $idAluno;

    private $nome;

    private $email;

    private $senha;

    private $data_nascimento;
    
    private $tipo_aluno;

    private $criacao_em;

    function __construct($idAluno, $nome, $email, $senha, $data_nascimento, $tipo_aluno, $criacao_em)
    {
        $this->idAluno = $idAluno;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->data_nascimento = $data_nascimento;
        $this->tipo_aluno = $tipo_aluno;
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
    
    public function getTipo_aluno()
    {
        return $this->tipo_aluno;
    }
    
    public function setTipo_aluno($tipo_aluno)
    {
        $this->tipo_aluno = $tipo_aluno;
    }
    
    
    public function calculaIdade(){    
        
        $dataNascFormatada = new DateTime($this->getDataNascimento());
        // Create a DateTime object for the current date
        $currentDate = new DateTime(); // 'now' or 'today' can also be used
        
        // Calculate the difference between the two dates
        $intervalo = $currentDate->diff($dataNascFormatada);
        
        // Extract the number of years from the DateInterval object
        return $intervalo->y;
    }
    

    public static function create($Aluno)
    {
        $sql = 'INSERT INTO Aluno (nome, email, senha, data_nascimento , tipo_aluno) VALUES (?,?,?,?)';
        $banco = new Banco();
        $stmt = $banco->conexao()->prepare($sql);
        $stmt->bindParam($Aluno->getNome(), $Aluno->getEmail(), $Aluno->getSenha(), $Aluno->getDataNascimento(), $Aluno->getTipo_aluno());
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
        $sql = 'SELECT idAluno, nome, email, senha, data_nascimento, tipo_aluno, criacao_em FROM aluno';
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $listaRS = $resultSet->fetchAll();

        $listaAlunos = [];
        foreach ($listaRS as $item) {
            $listaAlunos[] = new Aluno($item['idAluno'], $item['nome'], $item['email'], $item['senha'], $item['data_nascimento'], $item['tipo_aluno'], $item['criacao_em']);
        }

        return $listaAlunos;
    }

    public static function selectPorId($id)
    {
        $sql = "SELECT idAluno, nome, email, senha, data_nascimento, tipo_aluno, criacao_em FROM aluno WHERE idAluno = $id";
        $banco = new Banco();
        $resultSet = $banco->conexao()->query($sql);
        $item = $resultSet->fetch();
        $Aluno = new Aluno($item['idAluno'], $item['nome'], $item['email'], $item['senha'], $item['data_nascimento'], $item['tipo_aluno'], $item['criacao_em']);
        return $Aluno;
    }
    
    
    
}
