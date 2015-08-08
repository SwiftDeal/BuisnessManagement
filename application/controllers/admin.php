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
                $this->session($user, $members);
                self::redirect("/admin");
            } else {
                $view->set("message", "User not exist or blocked");
            }
        }
    }

    protected function session($user, $members) {
        $this->setUser($user);
        Registry::get("session")->set("members", $members);
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

}
