<?php

namespace App\Models;

use CodeIgniter\Model;

class UserOptions extends BaseModel
{
    protected $DBGroup          = 'default';
    protected $table            = 'user_options';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'option',
        'value'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'user_id' => [
            'label' => 'User',
            'rules' => 'required|greater_than_equal_to[0]|unique_fields[user_options,user_id,option]'
        ],
        'option' => [
            'label' => 'Option',
            'rules' => 'required|max_length[64]|unique_fields[user_options,user_id,option]'
        ],
        'value' => [
            'label' => 'Value',
            'rules' => 'max_length[255]'
        ]
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getListUserOptions(){
        $listOptions = new \App\Models\ListOptions();
        $listOptions->where('list_id',1);
        return $listOptions->getListOptions('value', 'name');
    }

    public function getDefaultUserOptions(){
        $listOptions = new \App\Models\ListOptions();
        $listOptions->where('list_id',2);
        return $listOptions->getListOptions('value', 'name');
    }

    public function getUserOptions($user_id){
        $userOptions = new UserOptions();
        $userOptions->where('user_id',$user_id);
        return $userOptions->getListOptions('value', 'option');
    }

    public function createUserOptions($user_id){
        $options = $this->getListUserOptions();
        $defaultValues = $this->getDefaultUserOptions();
        $userModel = new \App\Models\Users();
        $user = $userModel->find($user_id);
        if (!$user) return;
        foreach($options as $opt=>$label){
            $this->insert([
                'user_id' => $user_id,
                'option' => $opt,
                'value' => @$defaultValues[$opt]
            ]);
        }
    }

    public function setUserOptions($user_id, $userOptions){
        $options = $this->getListUserOptions();
        $userModel = new \App\Models\Users();
        $listOptions = new \App\Models\ListOptions();
        $user = $userModel->find($user_id);
        if (!$user) return [];
        if (!$userOptions) return [];
        $errors = [];
        foreach($userOptions as $opt=>$value){
            if (!@$options[$opt]){
                $errors["$opt"] = "Option not found";
            }
            if ("$value"==""){
                $errors["$opt"] = "Empty option value";
            }
            $availableOptions = $listOptions->getOptionsByName($opt);
            if (!@$availableOptions[$value]){
                $errors["$opt"] = "Invalid option value";
            }
        }
        if (!$errors){
            foreach($userOptions as $opt=>$value){
                $query = $this->where(['user_id'=>$user_id,'option'=>$opt]);
                $query->set('value',$value);
                $query->update();
            }
        }
        return $errors;
    }

    public function setUserValues($user_id, $userOptions){
        $userModel = new \App\Models\Users();
        $user = $userModel->find($user_id);
        if (!$user) return [];
        if (!$userOptions) return [];
        $errors = [];
        foreach($userOptions as $opt=>$value){
            $query = $this->where(['user_id'=>$user_id,'option'=>$opt]);
            $exists = $query->first();
            if ($exists){
                $query = $this->where(['user_id'=>$user_id,'option'=>$opt]);
                $query->set('value',$value);
                $query->update();    
            } else {
                $this->insert([
                    'user_id' => $user_id,
                    'option' => $opt,
                    'value' => $value
                ]);
            }
        }
        return $errors;
    }

}
