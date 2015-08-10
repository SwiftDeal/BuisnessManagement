<?php

/**
 * Description of hr
 *
 * @author Faizan Ayubi
 */
use Framework\RequestMethods as RequestMethods;

class HR extends Admin {
    
    /**
     * @before _secure, changeLayout
     */
    public function attendance() {
        $this->seo(array("title" => "Attendance Management", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        
        $now = strftime("%Y-%m-%d", strtotime('now'));
        $page = RequestMethods::get("page", 1);
        $attendances = Attendance::all(array("user_id = ?" => $this->user->id), array("*"), "created", "desc", "10", $page);
        $attend = Attendance::first(array("user_id = ?" => $this->user->id, "created LIKE ?" => "%{$now}%"));
        
        if (RequestMethods::post("action") == "createAttendance") {
            $attend = new Attendance(array(
                "user_id" => $this->user->id,
                "start" => strftime("%Y-%m-%d %H:%M:%S", strtotime('now')),
                "end" => ""
            ));
            $attend->save();
        }
        
        if(RequestMethods::post("action") == "endAttendance") {
            $attend->end = strftime("%Y-%m-%d %H:%M:%S", strtotime('now'));
            $attend->save();
        }
        
        $view->set("page", $page);
        $view->set("attendances", $attendances);
        $view->set("attend", $attend);
    }
    
    /**
     * @before _secure, changeLayout
     */
    public function team() {
        $this->seo(array("title" => "Team Members", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        
        $team = Member::all(array("project_id = ?" => $this->project->id));
        $view->set("team", $team);
    }
    
    /**
     * @before _secure, changeLayout
     */
    public function work() {
        $this->seo(array("title" => "Work Logs", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        
        if(RequestMethods::post("details")) {
            $work = new Work(array(
                "user_id" => $this->user->id,
                "project_id" => $this->project->id,
                "details" => RequestMethods::post("details")
            ));
            $work->save();
            $view->set("message", "Saved Successfully");
        }
        $works = Work::all(array("user_id = ?" => $this->user->id, "project_id" => $this->project->id));
        
        $view->set("works", $works);
    }
    
}
