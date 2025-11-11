<?php

include_once("Connexion.php");
/**
 * Description of AccessBDD
 *
 * @author louis
 */
abstract class AccessBDD {
    
    protected $conn = null;	
    
    protected function __construct(){
        try{
            $login = htmlspecialchars($_ENV['BDD_LOGIN'] ?? '');
            $pwd = htmlspecialchars($_ENV['BDD_PWD'] ?? '');
            $bd = htmlspecialchars($_ENV['BDD_BD'] ?? '');
            $server = htmlspecialchars($_ENV['BDD_SERVER'] ?? '');
            $port = htmlspecialchars($_ENV['BDD_PORT'] ?? '');   
            
            $this->conn = Connexion::getInstance($login, $pwd, $bd, $server, $port);
        } catch(Exception $e){
            throw $e;
        }
    }
    
    public function demande(string $methodeHTTP, string $table, ?string $id, ?array $champs) : array|int|null {
        if(is_null($this->conn)) {
            return null;
        } 
        switch ($methodeHTTP) {
            case 'GET' :
                return $this->traitementSelect($table, $champs);
            case 'POST' :
                return $this->traitementInsert($table, $champs);
            case 'PUT' : 
                return $this->traitementUpdate($table, $id, $champs);
            case 'DELETE' : 
                return $this->traitementDelete($table, $champs);
            default :
                return null;   
        }
    }
    
    abstract protected function traitementSelect(string $table, ?array $champs) : ?array;
    abstract protected function traitementInsert(string $table, ?array $champs) : ?int;
    abstract protected function traitementUpdate(string $table, ?string $id, ?array $champs) : ?int;
    abstract protected function traitementDelete(string $table, ?array $champs) : ?int;
    
}
