<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Users extends BaseController
{
    protected $helpers = ['html'];

    public function index()
    {
        $model = new \App\Models\Users();
        $pagerGroup = 'users';
        $pageSize = @$_GET["pagesize_$pagerGroup"]?:10;
        $items = $model->select(['id','name','email','updated_at'])->paginate($pageSize,$pagerGroup);
        $actions = [
            ["tag" => "a", "attributes" => [ 'href' => '/users/view/{id}'], "content" => '👁'],
        ];
        $columns = [
            "actions" => [
                "content" => $actions,
                "cellAttributes" => [
                    "class" => "actions",
                    "width" => "100"
                ]
            ],
            "name" => "Name", 
            "email" => "E-mail", 
            "updated_at" => "Last update",
        ];
        return view('users/index',[
            "title" => "Users", // page $title
            "items" => $items,
            "columns" => $columns,
            "pager" => $model->pager,
            "pagesize" => $pageSize,
            "pager_group" => $pagerGroup
        ]);
    }


    function view($id){
        $model = new \App\Models\Users();
        $item = $model->where('id',$id)->first();
        return view('users/view',['item'=>$item]);
    }


}
