<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 2016/12/12
 * Time: 12:51
 */

class adminController extends Nick{

    private $model;

    public function __construct(){

        parent::__construct();

        $this->model = $this->load_model('admin');

    }

    public function index(){

        $this->load_view('admin_login');
    }
    public function reg(){

        $this->load_view('admin_reg');
    }
	
	public function register(){


        $uname = post('uname');

        $password = post('upwd');

        $time = time();

        $id = $this->model->register(array($uname,$password,$time));

        $this->json_output(array('code'=>-1,'error'=>!$id?'注册失败用户名已存在':'','id'=>$id,'time'=>$time));

    }

    public function login($uname='',$upwd=''){

        $json = array('code'=>'0','error'=>'');

        if(!$uname && !$upwd){

            $uname = post('uname');

            $password = post('upwd');

        }

       $res = $this->model->login($uname,$password);

       if(!$res){

            $json['error'] = '用户名或密码错误！';

           return $this->json_output($json);

       }else{

           $json['code'] = '-1';

           $uid = $res['id'] ;

           cookie('uname',$uname,'/',24*3600);

           cookie('uid',$uid,'/',24*3600);

       }

        $this->json_output($json);

    }

    public function login_out(){

        cookie('uname','','/',-1);

        cookie('uid',0,'/',-1);

        $this->json_output(array('error'=>'','code'=>-1));

    }


} 