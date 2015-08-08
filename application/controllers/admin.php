<?php

/**
 * Description of admin
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\RequestMethods as RequestMethods;

class Admin extends Controller {
    
    /**
     * @readwrite
     */
    protected $_member;
    
    /**
     * @before _secure, changeLayout
     */
    public function index() {
        $this->seo(array("title" => "Dashboard", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
    }

    public function sync($model) {
        $this->noview();
        $db = Framework\Registry::get("database");
        $db->sync(new $model);
    }

    public function login() {
        $this->defaultLayout = "layouts/blank";
        $this->setLayout();
        $this->seo(array("title" => "Login", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        if (RequestMethods::post("action") == "login") {
            $user = User::first(array(
                "email = ?" => RequestMethods::post("email"),
                "password = ?" => sha1(RequestMethods::post("password")),
                "validity" => TRUE
            ));
            if ($user) {
                $members = Member::all(array("user_id = ?" => $user->id));
                foreach ($members as $member) {
                    $projects[] = Project::first(array("id = ?" => $member->project_id));
                }
                $this->session($user, $projects);
                self::redirect("/admin");
            } else {
                $view->set("message", "User not exist or blocked");
            }
        }
    }

    protected function session($user, $projects) {
        $this->setUser($user);
        Registry::get("session")->set("projects", $projects);
        Registry::get("session")->set("member", Member::first(array(
            "project_id = ?" => $projects[0]->id, 
            "user_id" => $this->user->id
        )));
    }

    public function register() {
        $this->defaultLayout = "layouts/blank";
        $this->setLayout();
        $this->seo(array("title" => "Register", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        if (RequestMethods::post("action") == "register") {
            $exist = User::first(array("email = ?" => RequestMethods::post("email")));
            if (!$exist) {
                $user = new User(array(
                    "name" => RequestMethods::post("name"),
                    "email" => RequestMethods::post("email"),
                    "password" => sha1(RequestMethods::post("password")),
                    "phone" => RequestMethods::post("phone"),
                    "validity" => FALSE
                ));
                $user->save();
                $view->set("message", "Your account has been created contact HR to activate");
            } else {
                $view->set("message", 'Account exists, login from <a href="/admin/login">here</a>');
            }
        }
    }
    
    protected function switchProject($project_id) {
        $session = Registry::get("session");
        $projects = $session->get("projects");

        foreach ($projects as $project) {
            if ($project_id == $project->id) {
                $session->set("member", Member::first(array(
                    "project_id = ?" => $project->id, 
                    "user_id" => $this->user->id
                )));
                self::redirect("/admin");
            }
        }
    }
    
    public function changeLayout() {
        $this->defaultLayout = "layouts/admin";
        $this->setLayout();

        $session = Registry::get("session");
        $projects = $session->get("projects");
        $member = $session->get("member");

        $this->_member = $member;

        $this->getActionView()->set("projects", $projects);
        $this->getLayoutView()->set("projects", $projects);
        $this->getActionView()->set("member", $member);
        $this->getLayoutView()->set("member", $member);
    }

}
