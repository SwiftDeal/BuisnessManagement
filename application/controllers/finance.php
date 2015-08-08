<?php

/**
 * Description of finance
 *
 * @author Faizan Ayubi
 */
use Framework\RequestMethods as RequestMethods;

class Finance extends Admin {
    
    /**
     * Shows all project expenses
     * 
     * @before _secure, changeLayout
     */
    public function index() {
        $this->seo(array("title" => "Dashboard", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
    }
    
    /**
     * Show session wise project expenses
     * 
     * @before _secure, changeLayout
     */
    public function expenses() {
        $this->seo(array("title" => "Dashboard", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        
        if(RequestMethods::post("action") == "expenses") {
            $payment = new Payment(array(
                "user_id" => $this->user->id,
                "project_id" => $this->project->id,
                "amount" => RequestMethods::post("amount"),
                "comment" => RequestMethods::post("comment")
            ));
            $payment->save();
            $view->set("message", "Saved Successfully");
        }
        
        $payments = Payment::all(array("project_id = ?" => $this->project->id));
        $view->set("payments", $payments);
    }
}
