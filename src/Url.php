<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

require '../vendor/autoload.php';
use Dotenv\Dotenv;

/**
 * Description of Url
 *
 * @author louis
 */
class Url {
    
    private static $instance = null;
    private $data = [];
    private $dotenv;
    
    private function __construct(){
        $this->dotenv = Dotenv::createImmutable(__DIR__);
        $this->dotenv->load();
        $this->data = $this->recupAllData();
    }
    
    public static function getInstance(): Url{
        if(self::$instance === null) {
            self::$instance = new Url();
        }
        return self::$instance;
    }
    
    private function basicAuthentification() : bool{
        $expectedUser = htmlspecialchars($_ENV['AUTH_USER'] ?? '');
        $expectedPw = htmlspecialchars($_ENV['AUTH_PW'] ?? '');  
        
        $authUser = htmlspecialchars($_SERVER['PHP_AUTH_USER'] ?? '');
        $authPw = htmlspecialchars($_SERVER['PHP_AUTH_PW'] ?? '');   
        
    return ($authUser === $expectedUser && $authPw === $expectedPw);
    }
    
    public function authentification(): bool{
        $authentification = htmlspecialchars($_ENV['AUTHENTIFICATION'] ?? '');
        switch ($authentification){
            case '' : return true ;
            case 'basic' : return self::basicAuthentification() ;
            default : return true;
        }
    }
    
    private function recupAllData() : array{
        $data = [];
        if(!empty($_GET)){
            $data = array_merge($data, $_GET);
        }
        if(!empty($_POST)){
            $data = array_merge($data, $_POST);
        }
        $input = file_get_contents('php://input');
        parse_str($input, $postData);
        $data = array_merge($data, $postData);    
        
        $data = array_map(function($value) {
            return htmlspecialchars($value, ENT_NOQUOTES);
        }, $data);
        return $data;
    }
    
        public function recupVariable(string $nom, string $format="string") : string|array|null{
        $variable = $this->data[$nom] ?? '';
        switch ($format){
            case "json" : 
                $variable = $variable ? json_decode($variable, true) : null;
                break;
        }
        return $variable;
    }  
    
    /**
     * récupère la méthode HTTP utilisée pour le transfert
     * @return string
     */
    public function recupMethodeHTTP() : string{
        return filter_input(INPUT_SERVER, 'REQUEST_METHOD');
    }   
    
}
