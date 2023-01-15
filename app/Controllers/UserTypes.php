<?php

namespace App\Controllers;

class UserTypes extends BaseController
{
    protected $modelName = 'App\Models\UserTypes';
    protected $baseUrl = "/usertypes";
    protected $fields = [
        'id' => [
            "label" => "ID",
            "hidden" => true
        ],
        'name' => [
            "label" => "Name",
            "sort" => true,
            "filter" => true
        ],
        "access" => [
            "label" => "Access",
            "sort" => true,
            "filter" => true,
            "options" => [
                1 => "View",
                2 => "Edit",
                3 => "Create",
                4 => "Full"
            ] 
        ]
    ];
    
    public function index()
    {
        return $this->table("Users Types", $this->baseUrl);
    }

    function view($id){
        $item = $this->getModelById($id);
        $item["access_name"] = $this->fields["access"]["options"][$item["access"]];
        return $this->parserLayout('usertypes/view',['item'=>[$item],'title'=>'View User Type','editurl' => '/usertypes/edit/'.$item['id']]);
    }

    function edit($id){
        $item = $this->getModelById($id);
        $item['password'] = '';
        return $this->layout('usertypes/form',[
            'item'=>$item, 
            'access_names'=> $this->fields["access"]["options"],
            'errors'=>$this->errors,
            'title'=>'Edit User Type'
        ]);
    }

    function update($id){
        $fields = ['name','access'];
        $result = $this->doUpdate($id, $fields);
        if (!$result){
            return $this->edit($id);
        }
        return $this->response->redirect('/usertypes/edit/'.$id);
    }

    function new(){
        $item = [];
        return $this->layout('usertypes/form',[
            'item'=>$item, 
            'title'=>"New User Type",
            'access_names'=> $this->fields["access"]["options"],
        ]);
    }

    function create(){
        $fields = ['name','access'];
        $rules = $this->model->getValidationRules(['only'=>$fields]);
        $id = $this->doCreate($fields,$rules);
        if (!$id){
            return $this->new();
        }
        return $this->response->redirect('/usertypes/edit/'.$id);
    }

    function delete($id){
        $item = $this->getModelById($id);
        $this->model->delete($id);
        return redirect()->back();
    }

}