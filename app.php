<?php
    
    class Dashboard {
        public $dataInicio;
        public $dataFim;
        public $numeroVendas;
        public $totalVendas;
        public $clientesAtivos;
        public $clientesInativos;
        public $totalDeReclamacoes;
        public $totalDeElogios;
        public $totalDeSugestoes;
        public $totalDeDespesas;

        public function __get($attr){
            return $this->$attr;
        }
        public function __set($attr, $valor){
            $this->$attr = $valor;
            return $this;
        }
    }

    class Conexao{
        private $host = 'localhost';
        private $dbname = 'dashboard';
        private $user = 'root';
        private $pass = '';

        public function conectar(){
            try{
                $conexao = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname",
                "$this->user",
                "$this->pass");

                //$conexao->exec('set charset set utf8');

                return $conexao;
            }
            catch(PDOException $e){
                echo '<p>'.$e->getMessage().'</p>';
            }
        }
    }

    class bd{
        private $conexao;
        private $dashboard;

        public function __construct(Conexao $conexao, Dashboard $dashboard){
            $this->conexao = $conexao->conectar();
            $this->dashboard = $dashboard;
        }

        public function getNumeroVendas() {
            $query = 'SELECT count(*) as numeroVendas from tb_vendas where data_venda between :dataInicio and :dataFim';
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':dataInicio', $this->dashboard->__get('dataInicio'));
            $stmt->bindValue(':dataFim', $this->dashboard->__get('dataFim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->numeroVendas;

        }

        public function getTotalVendas() {
            $query = 'SELECT SUM(total) as totalVendas from tb_vendas where data_venda between :dataInicio and :dataFim';
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':dataInicio', $this->dashboard->__get('dataInicio'));
            $stmt->bindValue(':dataFim', $this->dashboard->__get('dataFim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->totalVendas;
        }

        public function getClientesAtivos(){
            $query = 'SELECT COUNT(*) AS clientesAtivos FROM tb_clientes WHERE cliente_ativo = 1';
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->clientesAtivos;
        }

        public function getClientesInativos() {
            $query = 'SELECT COUNT(*) AS clientesInativos FROM tb_clientes WHERE cliente_ativo = 0';
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->clientesInativos;
        }

        public function getTotalDeReclamacoes() {
            $query = 'SELECT COUNT(*) AS totalReclamacoes FROM tb_contatos WHERE tipo_contato = 1';
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->totalReclamacoes;
        }

        public function getTotalDeElogios() {
            $query = 'SELECT COUNT(*) AS totalElogios FROM tb_contatos WHERE tipo_contato = 3';
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->totalElogios;
        }

        public function getTotalDeSugestoes() {
            $query = 'SELECT COUNT(*) AS totalSugestoes FROM tb_contatos WHERE tipo_contato = 2';
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->totalSugestoes;
        }

        public function getTotalDeDespesas() {
            $query = 'SELECT SUM(total) as totalDespesas from tb_despesas where data_despesa between :dataInicio and :dataFim';
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':dataInicio', $this->dashboard->__get('dataInicio'));
            $stmt->bindValue(':dataFim', $this->dashboard->__get('dataFim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->totalDespesas;
        }

    }
    $dashboard = new Dashboard();

    $conexao = new Conexao();

    $competencia = explode('-', $_GET['competencia']);
    $ano = $competencia[0];
    $mes = $competencia[1];

    $diasDoMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    $dashboard->__set('dataInicio', $ano.'-'.$mes.'-01');
    $dashboard->__set('dataFim', $ano.'-'.$mes.'-'.$diasDoMes);

    $bd = new Bd($conexao, $dashboard);

    $dashboard->__set('numeroVendas', $bd->getNumeroVendas());

    $dashboard->__set('totalVendas', $bd->getTotalVendas());

    $dashboard->__set('clientesAtivos', $bd->getClientesAtivos());

    $dashboard->__set('clientesInativos', $bd->getClientesInativos());

    $dashboard->__set('totalDeReclamacoes', $bd->getTotalDeReclamacoes());

    $dashboard->__set('totalDeSugestoes', $bd->getTotalDeSugestoes());

    $dashboard->__set('totalDeElogios', $bd->getTotalDeElogios());
    
    $dashboard->__set('totalDeDespesas', $bd->getTotalDeDespesas());

    echo json_encode($dashboard);

?>