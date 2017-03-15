<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 2016/12/8
 * Time: 10:33
 */

class infoController extends Nick {

    private $api;

    private $uid = 0;

    public function __construct(){

        parent::__construct();

        $this->uid = intval(cookie('uid'));

        if(!$this->uid){

            exit ('请登录');

        }

        $this->api = $this->load_controller('api');

    }

    public function index(){
		
		$this->load_view('head');
		$this->load_view('info_index');
		$this->load_view('footer');
    		
    }
	
	public function job(){
		
		$this->load_view('head');
		$this->load_view('info_job');
		$this->load_view('footer');
    		
    }
	public function feat(){
		
		$this->load_view('head');
		$this->load_view('info_feat');
		$this->load_view('footer');
    		
    }
	
	public function edu(){
		
		$this->load_view('head');
		$this->load_view('info_edu');
		$this->load_view('footer');
    		
    }
	public function uploadPic(){
		
		$this->load_view('head');
		$this->load_view('info_uploadPic');
		$this->load_view('footer');
    		
    }
	public function phone(){
		
		$this->load_view('head');
		$this->load_view('info_phone');
		$this->load_view('footer');
    		
    }
	public function pwd(){
		
		$this->load_view('head');
		$this->load_view('info_pwd');
		$this->load_view('footer');
    		
    }
	public function email(){
		
		$this->load_view('head');
		$this->load_view('info_email');
		$this->load_view('footer');
    		
    }



} 