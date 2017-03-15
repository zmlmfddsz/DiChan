<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/9
 * Time: 11:47
 */

class stepModel extends NickModel {

    private $tables = array(

        'user'=>'
            id int unsigned primary key auto_increment comment "用户表主键",
            name varchar(36) not null default "" comment "用户名",
            alias varchar(36) not null default "" comment "昵称",
            pwd char(36) not null default "" comment "密码36位",
            sex tinyint unsigned not null default 0 comment "性别",
            email varchar(36) not null default "" comment "邮箱",
            phone char(11) not null default "" comment "手机",
            birthday int unsigned not null default 0 comment "生日",
            icon int unsigned not null default 0 comment "头像关联ID",
            login_time int unsigned not null default 0 comment "登录时间",
            reg_time int unsigned not null default 0 comment "注册时间",
            score tinyint unsigned not null default 0 comment "学分",
            intro varchar(256) not null default "" comment "自我介绍",
            real_name varchar(36) not null default "" comment "真实姓名",
            address smallint unsigned not null default 0 comment "所在区县ID",
            sector varchar(120) not null default "" comment "部门",
            sector_title varchar(120) not null default "" comment "职位"

        ',
        'icon'=>'
            id int unsigned primary key auto_increment comment "头像表主键",
            src varchar(120) not null default "" comment "地址",
            uid int unsigned not null default 0 comment "用户关联ID",
            status tinyint unsigned not null default 0 comment "启用头像开关"
        ',
        'edu'=>'
            id int unsigned primary key auto_increment comment "教育表主键",
            school varchar(120) not null default "" comment "学校名称",
            education tinyint unsigned not null default 0 comment "学历",
            start_time int unsigned not null default 0 comment "开始时间",
            end_time int unsigned not null default 0 comment "结束时间",
            uid int unsigned not null default 0 comment "用户关联ID"
        ',
        'job'=>'
            id int unsigned primary key auto_increment comment "职业表主键",
            firm varchar(120) not null default "" comment "公司名称",
            sector varchar(120) not null default "" comment "部门",
            title varchar(120) not null default "" comment "职位",
            start_time int unsigned not null default 0 comment "开始时间",
            end_time int unsigned not null default 0 comment "结束时间",
            uid int unsigned not null default 0 comment "用户关联ID",
            address smallint unsigned not null default 0 comment "所在地ID"
        ',
        'feat'=>'
            id int unsigned primary key auto_increment comment "专长表主键",
            title varchar(36) not null default "" comment "专长名称",
            uid int unsigned not null default 0 comment "用户关联ID"
        ',
        'encrypted'=>'
            qs1 tinyint unsigned not null default 0 comment "问题1",
            qs2 tinyint unsigned not null default 0 comment "问题2",
            qs3 tinyint unsigned not null default 0 comment "问题3",
            ans1 varchar(120) not null default "" comment "答案1",
            ans2 varchar(120) not null default "" comment "答案2",
            ans3 varchar(120) not null default "" comment "答案3",
            uid int unsigned not null default 0 comment "用户关联ID"
        ',
        'province'=>'
            id tinyint unsigned primary key auto_increment comment "省主键",
            title varchar(36) not null default "" comment "省名称"
        ',
        'city'=>'
            id smallint unsigned primary key auto_increment comment "市主键",
            title varchar(36) not null default "" comment "市名称",
            pid tinyint unsigned not null default 0 comment "省关联ID"
        ',
        'county'=>'
            id smallint unsigned primary key auto_increment comment "区县主键",
            title varchar(36) not null default "" comment "区县名称",
            cid smallint unsigned not null default 0 comment "高关联ID"
        '



    );

    public function install(){

        $this->query('CREATE DATABASE IF NOT EXISTS '.$this->dbname.' charset='.$this->charset);

       $this->use_db($this->dbname);


        foreach($this->tables as $table => $fields){

            $sql = 'CREATE TABLE IF NOT EXISTS '.$this->table_fix.$table.' ('.$fields.') charset='.$this->charset;

            $res = $this->query($sql);

            echo '创建数据表['.$this->table_fix.$table.']'.($res?'成功':'失败').'！<br>';
            if(!$res){
                echo $sql.'<br>';
            }

        }

    }

    public function unstall(){

        $this->use_db($this->dbname);

        foreach($this->tables as $table => $fields){

            $sql = 'DROP TABLE IF EXISTS '.$this->table_fix.$table;

            $res = $this->query($sql);

            echo '删除数据表['.$this->table_fix.$table.']'.($res?'成功':'失败').'！<br>';

            if(!$res){
                echo $sql.'<br>';
            }

        }

        $res = $this->query('DROP DATABASE IF EXISTS '.$this->dbname);

        echo '删除数据库'.$this->dbname.($res?'成功':'失败');


    }

    public function reset(){

        $this->use_db($this->dbname);

        foreach($this->tables as $table => $fields){

            $sql = 'TRUNCATE '.$this->table_fix.$table;

            $res = $this->query($sql);

            echo '重置数据表['.$this->table_fix.$table.']'.($res?'成功':'失败').'！<br>';

            if(!$res){
                echo $sql.'<br>';
            }

        }

    }

} 