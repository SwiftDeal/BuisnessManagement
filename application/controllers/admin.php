<?php

/**
 * Description of admin
 *
 * @author Faizan Ayubi
 */
use Shared\Controller as Controller;
use Framework\RequestMethods as RequestMethods;
use Framework\Registry as Registry;

class Admin extends Controller {
    
    /**
     * @readwrite
     */
    protected $_member;
    
    /**
     * @readwrite
     */
    protected $_project;
    
    /**
     * @before _secure, changeLayout
     */
    public function index() {
        $this->seo(array("title" => "Dashboard", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
    }
    
    /**
     * Searchs for data and returns result from db
     * @param type $model the data model
     * @param type $property the property of modal
     * @param type $val the value of property
     * @before _secure, changeLayout
     */
    public function search($model = NULL, $property = NULL, $val = 0, $page=1) {
        $this->seo(array("title" => "Search", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        $model = RequestMethods::get("model", $model);
        $property = RequestMethods::get("key", $property);
        $val = RequestMethods::get("value", $val);
        $page = RequestMethods::get("page", $page);
        $sign = RequestMethods::get("sign", "equal");

        $view->set("items", array());
        $view->set("values", array());
        $view->set("model", $model);
        $view->set("page", $page);
        $view->set("property", $property);
        $view->set("val", $val);
        $view->set("sign", $sign);

        if ($model) {
            if($sign == "like"){
                $where = array("{$property} LIKE ?" => "%{$val}%");
            } else {
                $where = array("{$property} = ?" => $val);
            }
            
            $objects = $model::all($where,array("*"),"created", "desc", 10, $page);
            $count = $model::count($where);$i = 0;
            if ($objects) {
                foreach ($objects as $object) {
                    $properties = $object->getJsonData();
                    foreach ($properties as $key => $property) {
                        $key = substr($key, 1);
                        $items[$i][$key] = $property;
                        $values[$i][] = $key;
                    }
                    $i++;
                }
                $view->set("items", $items);
                $view->set("values", $values[0]);
                $view->set("count", $count);
                //echo '<pre>', print_r($values[0]), '</pre>';
                $view->set("success", "Total Results : {$count}");
            } else {
                $view->set("success", "No Results Found");
            }
        }
    }

    /**
     * Shows any data info
     * 
     * @before _secure, changeLayout
     * @param type $model the model to which shhow info
     * @param type $id the id of object model
     */
    public function info($model = NULL, $id = NULL) {
        $this->seo(array("title" => "{$model} info", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();
        $items = array();
        $values = array();

        $object = $model::first(array("id = ?" => $id));
        $properties = $object->getJsonData();
        foreach ($properties as $key => $property) {
            $key = substr($key, 1);
            if (strpos($key, "_id")) {
                $child = ucfirst(substr($key, 0, -3));
                $childobj = $child::first(array("id = ?" => $object->$key));
                $childproperties = $childobj->getJsonData();
                foreach ($childproperties as $k => $prop) {
                    $k = substr($k, 1);
                    $items[$k] = $prop;
                    $values[] = $k;
                }
            } else {
                $items[$key] = $property;
                $values[] = $key;
            }
        }
        $view->set("items", $items);
        $view->set("values", $values);
        $view->set("model", $model);
    }

    /**
     * Updates any data provide with model and id
     * 
     * @before _secure, changeLayout
     * @param type $model the model object to be updated
     * @param type $id the id of object
     */
    public function update($model = NULL, $id = NULL) {
        $this->seo(array("title" => "Update", "keywords" => "admin", "description" => "admin", "view" => $this->getLayoutView()));
        $view = $this->getActionView();

        $object = $model::first(array("id = ?" => $id));

        $vars = $object->columns;
        $array = array();
        foreach ($vars as $key => $value) {
            array_push($array, $key);
            $vars[$key] = htmlentities($object->$key);
        }
        if (RequestMethods::post("action") == "update") {
            foreach ($array as $field) {
                $object->$field = RequestMethods::post($field, $vars[$field]);
                $vars[$field] = htmlentities($object->$field);
            }
            $object->save();
            $view->set("success", true);
        }

        $view->set("vars", $vars);
        $view->set("array", $array);
        $view->set("model", $model);
        $view->set("id", $id);
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
        Registry::get("session")->set("project", $projects[0]);
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
                
                $member = new Member(array(
                    "user_id" => $user->id,
                    "designation" => "member",
                    "project_id" => "1"
                ));
                $member->save();
                $view->set("message", "Your account has been created contact HR to activate");
            } else {
                $view->set("message", 'Account exists, login from <a href="/admin/login">here</a>');
            }
        }
    }
    
    public function switchProject($project_id) {
        $this->noview();
        $session = Registry::get("session");
        $projects = $session->get("projects");

        foreach ($projects as $project) {
            if ($project_id == $project->id) {
                $session->set("member", Member::first(array(
                    "project_id = ?" => $project->id, 
                    "user_id" => $this->user->id
                )));
                
                $session->set("project", Project::first(array(
                    "id = ?" => $project->id
                )));
                self::redirect("/admin");
            }
        }
        
        echo 'You are not assigned that project';
    }
    
    public function changeLayout() {
        $this->defaultLayout = "layouts/admin";
        $this->setLayout();

        $session = Registry::get("session");
        $projects = $session->get("projects");
        $project = $session->get("project");
        $member = $session->get("member");

        $this->_member = $member;
        $this->_project = $project;

        $this->getActionView()->set("projects", $projects);
        $this->getLayoutView()->set("projects", $projects);
        $this->getActionView()->set("project", $project);
        $this->getLayoutView()->set("project", $project);
        $this->getActionView()->set("member", $member);
        $this->getLayoutView()->set("member", $member);
    }

}
