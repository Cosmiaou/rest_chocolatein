<?php

include_once("AccessBDD.php");

/**
 * Description of MyAccessBDD
 *
 * @author 
 */
class MyAccessBDD extends AccessBDD {
    
    public function __construct() {
        try{
            parent::__construct();
        } catch(Exception $e) {
            throw $e;
        }
    }
    
     /**
     * demande de recherche
     * @param string $table
     * @param array|null $champs nom et valeur de chaque champ
     * @return array|null tuples du résultat de la requête ou null si erreur
     * @override
     */	
    protected function traitementSelect(string $table, ?array $champs) : ?array{
        switch($table){
            case "" :
                // return $this->uneFonction(parametres);
            case "produit_specifique" :
                return $this->selectMotCle($champs);
            case "nb_specifique" :
                return $this->selectNombreMotCle($champs);
            default:
                // cas général
                return $this->selectTuplesOneTable($table, $champs);
        }	
    }
    
    private function selectNombreMotCle(?array $champs) : ?array{
        if(empty($champs)){
            return null;
        }
        if(!array_key_exists('cle', $champs)){
            return null;  
        }
        
        $requete = "select p.idgamme, COUNT(p.idgamme) ";
        $requete .= "from produit p left join details_produits dp on (p.id = dp.idproduit) ";
        $requete .= "where p.description like :cle or dp.details like :cle ";
        $requete .= "GROUP BY p.idgamme;";
        
        $champsRequete['cle'] = '%' . $champs['cle'] . '%';        
        return $this->conn->queryBDD($requete, $champsRequete);	
    }
    
    /**
     * récupère le nom, la description et les détails des produits
     * dont 'description' ou 'détails' contient le mot clé présent dans $champs
     * @param array|null $champs contient juste 'cle' avec une valeur de cle
     * @return ?array
     */
    private function selectMotCle(?array $champs) : ?array{
        if(empty($champs)){
            return null;
        }
        if(!array_key_exists('cle', $champs)){
            return null;  
        }
        
        $requete = "select p.nom, p.description, dp.details ";
        $requete .= "from produit p left join details_produits dp on (p.id = dp.idproduit) ";
        $requete .= "where p.description like :cle or dp.details like :cle ";
        $requete .= "order by p.nom";
        
        $champsRequete['cle'] = '%' . $champs['cle'] . '%';        
        return $this->conn->queryBDD($requete, $champsRequete);		
    }
   
    /**
     * récupère les tuples d'une seule table
     * @param string $table
     * @param array|null $champs
     * @return array|null 
     */
    private function selectTuplesOneTable(string $table, ?array $champs) : ?array{
        if(empty($champs)){
            // tous les tuples d'une table
            $requete = "select * from $table;";
            return $this->conn->queryBDD($requete);  
        }else{
            // tuples spécifiques d'une table
            $requete = "select * from $table where ";
            foreach ($champs as $key => $value){
                $requete .= "$key=:$key and ";
            }
            // (enlève le dernier and)
            $requete = substr($requete, 0, strlen($requete)-5);	 
            return $this->conn->queryBDD($requete, $champs);
        }
    }	
    
    /**
     * demande d'ajout (insert)
     * @param string $table
     * @param array|null $champs nom et valeur de chaque champ
     * @return int|null nombre de tuples ajoutés ou null si erreur
     * @override
     */	
    protected function traitementInsert(string $table, ?array $champs) : ?int{
        switch($table){
            case "" :
                // return $this->uneFonction(parametres);
            default:                    
                // cas général
                return $this->insertOneTupleOneTable($table, $champs);	
        }
    }
    
    /**
     * demande d'ajout (insert) d'un tuple dans une table
     * @param string $table
     * @param array|null $champs
     * @return int|null nombre de tuples ajoutés (0 ou 1) ou null si erreur
     */	
    private function insertOneTupleOneTable(string $table, ?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        // construction de la requête
        $requete = "insert into $table (";
        foreach ($champs as $key => $value){
            $requete .= "$key,";
        }
        // (enlève la dernière virgule)
        $requete = substr($requete, 0, strlen($requete)-1);
        $requete .= ") values (";
        foreach ($champs as $key => $value){
            $requete .= ":$key,";
        }
        // (enlève la dernière virgule)
        $requete = substr($requete, 0, strlen($requete)-1);
        $requete .= ");";
        return $this->conn->updateBDD($requete, $champs);
    }
    
    /**
     * demande de modification (update)
     * @param string $table
     * @param string|null $id
     * @param array|null $champs nom et valeur de chaque champ
     * @return int|null nombre de tuples modifiés ou null si erreur
     * @override
     */	
    protected function traitementUpdate(string $table, ?string $id, ?array $champs) : ?int{
        switch($table){
            case "" :
                // return $this->uneFonction(parametres);
            default:                    
                // cas général
                return $this->updateOneTupleOneTable($table, $id, $champs);
        }	
    }  
    
    /**
     * demande de modification (update) d'un tuple dans une table
     * @param string $table
     * @param string\null $id
     * @param array|null $champs 
     * @return int|null nombre de tuples modifiés (0 ou 1) ou null si erreur
     */	
    private function updateOneTupleOneTable(string $table, ?string $id, ?array $champs) : ?int {
        if(empty($champs)){
            return null;
        }
        if(is_null($id)){
            return null;
        }
        // construction de la requête
        $requete = "update $table set ";
        foreach ($champs as $key => $value){
            $requete .= "$key=:$key,";
        }
        // (enlève la dernière virgule)
        $requete = substr($requete, 0, strlen($requete)-1);				
        $champs["id"] = $id;
        $requete .= " where id=:id;";		
        return $this->conn->updateBDD($requete, $champs);	        
    }
    
    /**
     * demande de suppression (delete)
     * @param string $table
     * @param array|null $champs nom et valeur de chaque champ
     * @return int|null nombre de tuples supprimés ou null si erreur
     * @override
     */	
    protected function traitementDelete(string $table, ?array $champs) : ?int{
        switch($table){
            case "" :
                // return $this->uneFonction(parametres);
            default:                    
                // cas général
                return $this->deleteTuplesOneTable($table, $champs);	
        }
    }
   
    /**
     * demande de suppression (delete) d'un ou plusieurs tuples dans une table
     * @param string $table
     * @param array|null $champs
     * @return int|null nombre de tuples supprimés ou null si erreur
     */
    private function deleteTuplesOneTable(string $table, ?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        // construction de la requête
        $requete = "delete from $table where ";
        foreach ($champs as $key => $value){
            $requete .= "$key=:$key and ";
        }
        // (enlève le dernier and)
        $requete = substr($requete, 0, strlen($requete)-5);   
        return $this->conn->updateBDD($requete, $champs);	        
    }
}
