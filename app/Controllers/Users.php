<?php

namespace App\Controllers;

class Users extends BaseController
{
    protected $modelName = 'App\Models\Users';
    protected $route = "users";
    protected $entityName = "User";
    protected $entityGroup = "Users";
    protected $viewFields = ['name','email','user_type','avatar_url','updated_at','login_at','phone','address','city','postal_code'];
    protected $editFields = ['name','email','user_type','avatar_url','password','repeat_password','phone','address','city','postal_code'];
    public $fields = [
        "id" => [
            "label" => "Id",
            "hidden" => true,
        ],
        "name" => [
            "label" => "Name",
            "sort" => true,
            "filter" => true,
        ],
        "email" => [
            "label" => "Email",
            "sort" => true,
            "filter" => true,
        ],
        "user_type" => [
            "label" => "Profile",
            "filter" => true,
        ],
        "users__user_type_name" => [
            "label" => "Profile",
            "hidden" => true,
            "view" => false,
            "field" => "user_types.name",
        ],
        "avatar_url" => [
            "label" => "Avatar",
            "hidden" => true,
            "view_component" => "avatar",
            "component" => "image-upload",
            "default_image" => "/img/avatar.svg",
        ],
        "login_at" => [
            "label" => "Last Login",
            "sort" => true,
        ],
        "created_at" => [
            "label" => "Created",
            "hidden" => true,
        ],
        "updated_at" => [
            "label" => "Updated",
            "sort" => true,
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
        "phone" => [
            "header" => "Contact Information",
            "label" => "Phone",
            "hidden" => true,
        ],
        "address" => [
            "label" => "Address",
            "hidden" => true,
        ],
        "city" => [
            "label" => "City",
            "hidden" => true,
        ],
        "postal_code" => [
            "label" => "Postal Code",
            "hidden" => true,
        ],
    ];

    

    protected function prepareFields($keys=null, $data=null){
        $this->fields["user_type"]["options"] = $this->getUserTypes();
        if (module_access('auth_token',1)){
            $segment = current_url(true)->getSegment(1);
            $showToken = $keys && (module_access('auth_token',4) || $segment == "profile");
            $this->fields["auth_token"] = [
                "header" => "API Access",
                "label" => "API Token",
                "readonly" => true,
                "component" => "password-view",
                "view_component" => "password-view",
            ];
            if ($showToken){
                $keys[] = "auth_token";
            }
            $this->fields["push_token"] = [
                "label" => "Push Token",
                "readonly" => true,
                "maxlength" => "255",
                "component" => "password-view",
                "view_component" => "password-view",
            ];
            if ($showToken){
                $keys[] = "push_token";
            }
        }
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
        }
        return parent::prepareFields($keys);
    }

    function getDetails($data){
        // if (!@$data["item"]){
        //     return null;
        // }
        // if (!@$data["item"]["id"]){
        //     return null;
        // }

        // $_REQUEST["user_options_user_id"] = $data["item"]['id'];

        // $contents = [];
        // $controller = new \App\Controllers\UserOptions();
        // $controller->initController($this->request, $this->response, $this->logger);
        // $controller->prepareFields();
        // $controller->fields['user_id']["hidden"] = true;
        // $controller->viewLink = "/useroptions/view/{id}?user_id={$data["item"]['id']}";
        // $controller->newLink = "/useroptions/new?user_id={$data["item"]['id']}";
        // $controller->editLink = "/useroptions/edit/{id}?user_id={$data["item"]['id']}";
        // $contents[] = view('table',$controller->getTable("container mb-4"));

        // return implode("\n",$contents);
    }

    protected function getQueryModel(){
        $query = parent::getQueryModel();
        $access = profile_access("users");
        if (!is_admin()){
            $query->where("user_types.access<=$access");
        }
        $query->join('user_types','user_types.id=users.user_type',is_admin()?'left':'');
        return $query;
    }

    public function getModelById($id){
        $result = parent::getModelById($id);
        if ($result){
            $perm = new \App\Models\Permissions();
            $permissions = $perm->select(['module','access'])
                ->where("user_type_id",$result["user_type"])
                ->findAll();
            $result["permissions"] = array_column($permissions,"access","module");
            $result["user_options"] = $this->model->getUserOptions($id);
        }
        return $result;
    }

    public function profile($id){
        $this->entityName = "My Profile";
        $this->editLink = "/profile/edit";

        $this->route = "profile";
        $data = $this->getModelById($id);
        $this->prepareFields($this->viewFields, $data);
        return $this->view($id);
    }

    public function editProfile($id){
        $this->entityName = "My Profile";
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

    function getRules($fields,$id=null){
        $rules = $this->model->getValidationRules(['only'=>$fields]);
        $action = $this->getAction();
        $rules['repeat_password'] = [
            "rules" => $id ? 'matches[password]' : 'required|matches[password]',
            "label" => "Repeat password"
        ];
        if (($action=="edit" && $id!=null) || $action=="profile"){
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
