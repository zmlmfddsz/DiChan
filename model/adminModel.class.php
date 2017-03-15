<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/12
 * Time: 13:07
 */

class adminModel extends NickModel{

    protected $table = 'user';

    public function register($data){

        $res = $this->login($data[0],$data[1]);

        $res =  $res ? false : $this->insert('name,pwd,reg_time',$data);

        return  $res ? $this->lastInsertId() : $res;

    }

    public function login($uname,$upwd){

        return $this->select('id,name,pwd','name="'.$uname.'" and pwd="'.$upwd.'"',false);

    }


} 