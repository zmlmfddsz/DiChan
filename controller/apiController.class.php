<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 2016/12/12
 * Time: 15:00
 */

class apiController extends Nick{

    private $uid = 0;

    private $model;

    public function __construct(){

        parent::__construct();

        $this->uid = intval(cookie('uid'));

        if(!$this->uid){

           // $this->json_output(array('error'=>'对不起请登录！'));

          //  exit;

        }

        $this->model = $this->load_model('api');

    }

    public function base_info(){

        $result = $this->model->base_info($this->uid);

        $province =  empty($result) ? array() : $this->model->job_city_list($result['city']);

        $result['province'] = $province && isset($province[0]) ? $province[0] : array();

        $this->json_output(array('result'=>$result));

    }

    public function add_province(){

        $title = post('title');

        $res = $this->model->add_province($title);

        $this->json_output(array('error'=>$res?'':'添加省/市失败！','result'=>array('id'=>$res)));

    }
    public function add_city(){

        $province = intval(post('pid'));

        $title = post('title');

        $res = $this->model->add_city($province,$title);

        $this->json_output(array('error'=>$res?'':'添加市失败！','result'=>array('id'=>$res)));

    }
    public function add_county(){

        $city = intval(post('pid'));

        $title = post('title');

        $res = $this->model->add_county($city,$title);

        $this->json_output(array('error'=>$res?'':'添加区/县失败！','result'=>array('id'=>$res)));

    }

    public function province_list(){

        $res = $this->model->province_list();

        $this->json_output(array('result'=>$res));

    }
    public function city_list(){

        $pid = intval(get('pid'));

        $res = $this->model->city_list($pid);

        $this->json_output(array('result'=>$res));

    }

    public function pwd(){

        $pwd = post('pwd');

        $pwd1 = post('pwd1');

        $pwd2 = post('pwd2');

        if($pwd1!=$pwd2){

            $error = '两次密码不一致！请重新输入';

        }else{

            $error =  $this->model->check_pwd($this->uid,$pwd);

            if(!$error){

                $error = '密码错误！';

            }else{

                $error  = $this->model->set_pwd($this->uid,$pwd1) ? '' :'密码修改失败';

            }

        }

        $this->json_output(array('error'=>$error));

    }

    public function phone(){

        $phone = post('phone');

        $code = post('code');

        if(!preg_match('/^1\d{10}$/',$phone)){

            $error = '手机格式错误，请输入有效的手机号！';

        }else{

            $error = $this->model->set_phone($this->uid,$phone)===false ? '绑定手机失败':'';

        }

        $this->json_output(array('error'=>$error));

    }
    public function email(){

        $email = post('email');

        if(!preg_match('/^\w+@\w+\.(com)|(cn)|(net)|(cc)$/',$email)){

            $error = '邮箱格式错误，请输入有效的邮箱！';

        }else{

            $error = $this->model->set_email($this->uid,$email)===false ? '绑定邮箱失败':'';

        }

        $this->json_output(array('error'=>$error));

    }

    public function question(){

        $qs = post('qs');

        $data = array();

        $error = '';

        if(!is_array($qs) || count($qs)!=3){

            $error = '请将密保信息填写完整！';

        }else{

            foreach($qs as $k=>$v){

                $i = $k+1;

                $data['qs'.$i] = $v['qs'];

                $data['ans'.$i] = $v['ans'];

                if(!isset($v['qs']) || !isset($v['ans'])){

                    $error = '请将密保信息填写完整';

                    break;

                }

            }

            if(!$error){

                $error = $this->model->set_question($this->uid,$data) === false ?'更新密保失败！':'';

            }

        }

        $this->json_output(array('error'=>$error));

    }

    public function add_edu(){

        $type = intval(post('type'));

        $title = post('title');

        $year = post('year');

        $id = '';

        if(strlen($title)<4){

            $error = '请输入正确的大学名称！';

        }else if(date('Y',$year)==1970){

            $error = '请选择正确的年份！';

        }else{

            $id = $this->model->add_edu(array($type,$title,$year,$this->uid)) ;

            $error = $id ? '':'添加教育经历失败！';

        }

        $this->json_output(array('error'=>$error,'id'=>$id));


    }

    public function edu_list(){

        $res = $this->model->edu_list($this->uid);

        $this->json_output(array('result'=>$res));

    }

    public function del_edu(){

        $id = intval(get('id'));

        $res = $this->model->del_edu($id);

        $this->json_output(array('error'=>$res?'':'删除失败！'));

    }
    public function del_job(){
        $id = intval(get('id'));

        $res = $this->model->del_job($id);

        $this->json_output(array('error'=>$res?'':'删除失败！'));


    }

    public function add_feat(){

        $title = post('title');

        $id = '';

        if(!$title){

            $error = '专长不能为空！';

        }else{

            $id = $this->model->add_feat($this->uid,$title);

            $error = $id ? '': '不能重复添加专长！';

        }

        $this->json_output(array('error'=>$error,'id'=>$id));

    }

    public function feat_list(){
        $res = $this->model->feat_list($this->uid);
        $this->json_output(array('result'=>$res));
    }
    public function hot_feat_list(){
        $res = $this->model->hot_feat_list();
        $this->json_output(array('result'=>$res));
    }
    public function del_feat(){
        $res = $this->model->del_feat(intval(get('id')));
        $this->json_output(array('error'=>$res?'':'删除失败！'));
    }
    public function add_job(){
        $data = array(
            'firm'=>post('firm'),
            'sector'=>post('sector'),
            'title'=>post('title'),
            'address'=> intval(post('city')),
            'start_time'=>intval(post('start')),
            'end_time'=>intval(post('end')),
            'uid'=>$this->uid
        );;
        $id = $this->model->add_job($data);
        $this->json_output(array('error'=>$id?'':'添加失败！','id'=>$id));
    }

    public function job_list(){
        $res = $this->model->job_list($this->uid);
        $res = is_array($res) ? $res : array();
       /* $city = array();
        foreach($res as $v){
            $city[$v['city']] = 0;
        }
        $city = implode(array_keys($city),',');
        $city = $this->model->job_city_list($city);
        $city_list = array();
        foreach($city as $v){
            $city_list[$v['cid']] = $v;
            unset($v['cid']);
        }*/

        $this->json_output(array('result'=>$res));
    }

    public function set_base_info(){

        $data = array(
            'alias'=>post('alias'),
            'sector'=>post('sector'),
            'sector_title'=>post('title'),
            'birthday'=>intval(post('birthday')),
            'sex'=>intval(post('sex')),
            'real_name'=>post('real_name'),
            'email'=>post('email'),
            'address'=>intval(post('city')),
            'intro'=>post('intro')

        );

        if(!$data['real_name']){
            unset($data['real_name']);
        }

        $res = $this->model->set_base_info($this->uid,$data);

        $this->json_output(array('error'=>$res===false?'更新失败！':''));

    }

    public function upload_icon(){

        $date = date('Y-m-d').'/';

        $upload_dir = $this->defined_path('upload');

        $types = array(

            'jpg'=>0,
            'jpeg'=>0,
            'png'=>0,
            'gif'=>0
        );

        $errorInfo = array(
            0=>'',
            1=>'上传文件大小超过服务器设置，详细查看php.ini中的配置！',
            2=>'上传文件大小超过HTML表单的限制！',
            3=>'只有部分文件被上传！',
            4=>'没有找到要上传的文件！',
            5=>'服务器临时文件夹丢失！',
            6=>'文件写入到临时文件时出错！',
            7=>'文件写入失败！',
            8=>'未开启文件上传扩展！'

        );

        $max_size = 1024*1024*2;

        is_dir($upload_dir) or mkdir($upload_dir,0644);
        
        is_dir($upload_dir.$date) or mkdir($upload_dir.$date,0644);

        if(!isset($_FILES['icon'])){

            $error = '请设置表单name属性为icon，否则无法获取上传的文件！';

        }else{

            $file = $_FILES['icon'];

            $extension = pathinfo($file['name']);

            $extension = $extension['extension'];

            $id = '';

            if(!isset($types[$extension])){

                $error = '上传文件类型错误，只允许以下文件类型：'.implode($types,',');

            }else{

                $error = isset($errorInfo[$file['error']]) ? $errorInfo[$file['error']] :'上传异常';

                $file_name =$date.time().'_'.$this->uid.'.'.$extension;

                if(!$error && move_uploaded_file($file['tmp_name'],$upload_dir.$file_name)){

                    $id = $this->model->add_icon($this->uid,$file_name);

                }else{

                    $error = '上传失败！';

                }


            }


        }

        $this->json_output(array('error'=>$error,'id'=>$id,'src'=>$file_name));

    }

    public function icon_list(){

        $res = $this->model->icon_list($this->uid);

        $cur = $this->model->cur_icon($this->uid);

        $this->json_output(array('result'=>$res,'cur'=>$cur));

    }

    public function set_icon(){

        $id = intval(get('id'));

        $res = $this->model->set_icon($id,$this->uid);

        $this->json_output(array('error'=>$res?'':'保存头像失败'));


    }



} 