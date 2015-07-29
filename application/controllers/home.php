<?php

/**
 * The Default Example Controller Class
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;

class Home extends Controller {

    public function index() {
        $this->getLayoutView()->set("seo", Framework\Registry::get("seo"));
    }
    
    public function about() {
        $this->seo(array(
            "title" => "About Us",
            "view" => $this->getLayoutView()
        ));
    }
    
    public function work() {
        $this->seo(array(
            "title" => "Our Work",
            "view" => $this->getLayoutView()
        ));
    }
    
    public function team() {
        $this->seo(array(
            "title" => "Our Team",
            "view" => $this->getLayoutView()
        ));
    }
    
    public function contact() {
        $this->seo(array(
            "title" => "Contact",
            "view" => $this->getLayoutView()
        ));
    }

}
