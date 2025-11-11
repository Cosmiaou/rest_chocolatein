<?php

include_once("MyAccessBDD.php");

/**
 * Description of Controle
 *
 * @author louis
 */
class Controle {
    private $myAccessBDD;
    
    public function __construct(){
        try{
            $this->myAccessBDD = new MyAccessBDD();
        }catch(Exception $e){
            $this->reponse(500, "erreur serveur");
            die();
        }
    }
    
    public function demande(string $methodeHTTP, string $table, ?string $id, ?array $champs){
        $result = $this->myAccessBDD->demande($methodeHTTP, $table, $id, $champs);
        $this->controleResult($result);
    }
    
    private function controleResult(array|int|null $result) {
        if (!is_null($result)){
            $this->reponse(200, "OK", $result);
        }else{	
            $this->reponse(400, "requete invalide");
        }        
    }
    
    private function reponse(int $code, string $message, array|int|string|null $result="") {
        $retour = array(
            'code' => $code,
            'message' => $message,
            'result' => $result
        );
       echo json_encode($retour, JSON_UNESCAPED_UNICODE);
    }
    
    public function unauthorized() {
        $this->reponse(401, "authentification incorrecte");
    }
    
}
