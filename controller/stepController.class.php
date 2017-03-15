<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 2016/12/12
 * Time: 11:56
 */

class stepController extends Nick{

    private $model;

    private $lock_file = 'NickPhp.install';

    public function __construct(){

        parent::__construct();

        $this->model =  $this->load_model('step');


    }


    public function index(){



    }

    public function install(){

        if(file_exists($this->lock_file)) return print('已经安装');

        $this->model->install();

        file_put_contents($this->lock_file,'qq401541212:安装锁定文件');

    }

    public function unstall(){

        $this->model->unstall();

        if(file_exists($this->lock_file)) unlink($this->lock_file);

    }

    public function reset(){

        $this->model->reset();

    }

} 