<?php

/**
 * Description of Connexion
 *
 * @author louis
 */
class Connexion {
    private static $instance = null;
    /**
     * 
     * @var PDO
     */
    private $conn = null;
    
    private function __construct(string $login, string $pwd, string $bd, string $server, string $port){
        try {
            $this->conn = new PDO("mysql:host=$server;dbname=$bd;port=$port", $login, $pwd);
            $this->conn->query('SET CHARACTER SET utf8');
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public static function getInstance(string $login, string $pwd, string $bd, string $server, string $port) : Connexion{
        if(self::$instance === null) {
            self::$instance = new Connexion($login, $pwd, $bd, $server, $port);
        }
        return self::$instance;
    }
    
    private function prepareRequete(string $requete, ?array $param=null) : \PDOStatement {
        try{
            $requetePrepare = $this->conn->prepare($requete);
            if($param!== null && is_array($param)) {
                foreach ($param as $key => &$value) {
                    $requetePrepare->bindParam(":key", $value);
                }
            }
            return $requetePrepare;
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function updateBDD(string $requete, ?array $param=null): ?int{
        try{
           $result = $this->prepareRequete($requete, $param);
           $reponse = $result->execute();
           if($reponse === true) {
               return $result->rowCount();
           } else {
               return null;
           }
        } catch (Exception $e) {
            return null;
        }
    }
      /**
     * execute une requête select retournant 0 à plusieurs lignes
     * @param string $requete
     * @param array|null $param
     * @return array|null lignes récupérées ou null si erreur
     */
    public function queryBDD(string $requete, ?array $param=null) : ?array{     
        try{
            $result = $this->prepareRequete($requete, $param);
            $reponse = $result->execute();
            if($reponse === true){
                return $result->fetchAll(PDO::FETCH_ASSOC);
            }else{
                return null;
            } 
        }catch(Exception $e){
            return null;
        }
    }
}
