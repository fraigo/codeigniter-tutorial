<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Users extends BaseController
{
    protected $modelName = 'App\Models\Users';
    protected $route = "users";
    protected $entityName = 'User';
    protected $entityGroup = 'Users';
    protected $viewFields = ['name','email','user_type','updated_at','login_at'];
    protected $editFields = ['name','email','user_type','password','repeat_password'];
    public $fields = [
        "id" => [
            "label" => "ID",
            "hidden" => true
        ],
        "name" => [
            "label" => "Name",
            "sort" => true,
            "filter" => true
        ],
        "email" => [
            "label" => "Email",
            "sort" => true,
            "filter" => true
        ],
        "user_type" => [
            "label" => "Profile",
            "filter" => true
        ],
        "password" => [
            "header" => "Set Password",
            "label" => "Password",
            "hidden" => true,
            "type" => "password",
            "field" => "",
        ],
        "repeat_password" => [
            "field" => "",
            "label" => "Repeat Password",
            "type" => "password",
            "hidden" => true,
        ],
        "updated_at" => [
            "label" => "Last Update",
            "sort" => true,
        ],
        "login_at" => [
            "label" => "Last Login"
        ]
    ];
    
    protected function getQueryModel(){
        $query = parent::getQueryModel();
        $access = profile_access("users");
        if (!is_admin()){
            $query->where("user_types.access<=$access");
        }
        $query->join('user_types','user_types.id=users.user_type');
        return $query;
    }

    protected function prepareFields($keys=null, $data=null){
        $this->fields["user_type"]["options"] = $this->getUserTypes();
        if ($data){
            $userOptions = new \App\Models\UserOptions();
            $optionFields = $userOptions->getListUserOptions();
            $newFields = [];
            foreach ($optionFields as $field=>$label){
                $listOptions = new \App\Models\ListOptions();
                $opts = $listOptions->getOptionsByName($field);
                $fieldname = "user_options[$field]";
                $keys[] = $fieldname;
                $newFields[$fieldname] = [
                    "header" => count($newFields) ? null : "User Options",
                    "label" => $label,
                    "field" => '',
                    "options" => $opts
                ];
            }
            $this->fields = array_merge($this->fields, $newFields);
            $this->editFields = $keys;
            $this->viewFields = $keys;
            $this->fields["auth_token"] = [
                "header" => "API Access",
                "label" => "API Token",
                "component" => "password-view",
                "view_component" => "password-view",
            ];
        }
        return parent::prepareFields($keys);
    }

    public function getModelById($id){
        $result = parent::getModelById($id);
        $result["user_options"] = $this->model->getUserOptions($id);
        return $result;
    }

    public function profile($id){
        $this->entityName = "My Profile";
        $this->editLink = "/profile/edit";
        $this->viewFields[] = "auth_token";
        $data = $this->getModelById($id);
        $this->prepareFields($this->viewFields, $data);
        return $this->view($id);
    }

    public function editProfile($id){
        $this->entityName = "My Profile";
        $this->editFields[] = "auth_token";
        $data = $this->getModelById($id);
        $this->prepareFields($this->editFields, $data);
        return $this->edit($id);
    }

    public function updateProfile($id){
        $this->entityName = "My Profile";
        return $this->update($id);
    }
    
    private function getUserTypes(){
        $userTypes = new \App\Models\UserTypes();
        if (!is_admin()){
            $access = profile_access("users");
            $userTypes->where("access<=$access");
        }
        $result = [];
        foreach($userTypes->findAll() as $row){
            $result[$row["id"]]=$row["name"];
        }
        return $result;
    }

    function getRules($fields){
        $rules = $this->model->getValidationRules(['only'=>$fields]);
        $action = $this->getAction();
        $rules['repeat_password'] = [
            "rules" => 'matches[password]',
            "label" => "Repeat password"
        ];
        if ($action=="edit" || $action=="profile"){
            $password = $this->request->getVar('password');
            if (!$password){
                unset($rules["password"]);
                unset($rules["repeat_password"]);
            }
        }
        return $rules;
    }

    function prepareData($data){
        if (!@$data["password"]){
            unset($data["password"]);
        }
        return $data;
    }

    function update($id=null){
        $options = $this->request->getVar("user_options");
        $userOptions = new \App\Models\UserOptions();
        $res = $userOptions->setUserOptions($id, $options);
        if ($res){
            $this->errors = [ "user_options" => $res];
            if ($this->isJson()){
                return $this->JSONResponse(null,400,$this->errors);
            }
            return $this->edit($id);
        }
        $result = parent::update($id);
        return $result;
    }

}
