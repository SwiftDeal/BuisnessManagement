<?php

/**
 * Description of admin
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\RequestMethods as RequestMethods;

class Admin extends Controller {
    
    public function index() {
        $this->seo(array("title" => "Dashboard", "view" => $this->getLayoutView()));
        
    }
    
    public function login() {
        $this->defaultLayout = "layouts/blank";
        $this->setLayout();
        $this->seo(array("title" => "Login", "view" => $this->getLayoutView()));
        
        if(RequestMethods::post("action") == "login") {
            $user = User::first(array(
                "email = ?" => RequestMethods::post("email"),
                "password = ?" => RequestMethods::post("password")
            ));
            if ($user) {
                
            }
        }
    }
    
}
