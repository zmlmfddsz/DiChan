<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/12
 * Time: 15:02
 */

class apiModel extends NickModel{

    protected $table = 'user';

    public function base_info($uid){

        return $this->select('name,alias,sex,real_name,birthday,email,intro,address as city,sector,sector_title as title','id='.$uid,false);

    }

    public function set_base_info($uid,$data){

        return $this->update($data,'id='.$uid);

    }

    public function add_province($title){

        $res = $this->query('INSERT INTO '.$this->table_fix.'province (title) values("'.$title.'")');

        return $res ? $this->lastInsertId() : $res;

    }
    public function add_city($pid,$title){

        $this->table = $this->table_fix.'city';

        $res = $this->insert('pid,title',array($pid,$title));

        return $res ? $this->lastInsertId() : $res;

    }
    public function add_county($pid,$title){

        $this->table = $this->table_fix.'county';

        $res = $this->insert('cid,title',array($pid,$title));

        return $res ? $this->lastInsertId() : $res;

    }

    public function province_list(){

        $this->table = $this->table_fix.'province';

        return $this->select('*');

    }
    public function city_list($pid){

        $this->table = $this->table_fix.'city';

        return $this->select('*','pid='.$pid);

    }

    public function check_pwd($uid,$upwd){

        $this->table = $this->table_fix.'user';

        return $this->select('name,pwd','id='.$uid.' AND pwd="'.$upwd.'"',false);

    }

    public function set_pwd($uid,$pwd){

        $this->table = $this->table_fix.'user';

        return $this->update(array('pwd'=>$pwd),'id='.$uid);

    }

    public function set_phone($uid,$phone){

        $this->table = $this->table_fix.'user';

        return $this->update(array('phone'=>$phone),'id='.$uid);

    }
    public function set_email($uid,$email){

        $this->table = $this->table_fix.'user';

        return $this->update(array('email'=>$email),'id='.$uid);

    }
    public function set_question($uid,$data){

        $this->table = $this->table_fix.'encrypted';

        $res = $this->select('*','uid='.$uid,false);

        if($res){

            return $this->update($data,'uid='.$uid);

        }else{

            $data['uid'] = $uid;

            return $this->insert('qs1,ans1,qs2,ans2,qs3,ans3,uid',$data);

        }

    }

    public function add_edu($data){

        $this->table = $this->table_fix.'edu';

        $this->insert('education,school,start_time,uid',$data);

        return $this->lastInsertId();

    }

    public function edu_list($uid){

        $this->table = $this->table_fix.'edu';

        return $this->select('id,education as type,school as title,start_time as year','uid='.$uid);

    }

    public function del_edu($id){

        $this->table = $this->table_fix.'edu';

        return $this->delete('id='.$id);

    }
    public function del_job($id){

        $this->table = $this->table_fix.'job';

        return $this->delete('id='.$id);

    }

    public function check_feat($uid,$title){

        $this->table = $this->table_fix.'feat';

        return $this->select('title','uid='.$uid.' AND title = "'.$title.'"');

    }

    public function feat_list($uid){

        $this->table = $this->table_fix.'feat';

        return $this->select('id,title','uid='.$uid);

    }

    public function job_list($uid){
        $this->table = $this->table_fix.'job';
        return $this->select('id,firm,sector,title,start_time as start,end_time as end,address as city','uid='.$uid);
    }
    public function job_city_list($city){
        $this->table = $this->table_fix.'province';
        $table = $this->table_fix.'city';
        $sql = 'SELECT '.$this->table.'.title as province,'.$this->table.'.id as id,'.$table.'.title as city,'.$table.'.id as cid FROM '.$this->table.' INNER JOIN '.$table.' ON '.$this->table.'.id='.$table.'.pid WHERE '.$table.'.id in('.$city.')';
        $res = $this->query($sql);
        $res = $res? $res->fetchAll(PDO::FETCH_ASSOC) : array();
        return $res;
    }
    public function hot_feat_list(){

        $this->table = $this->table_fix.'feat';

        return $this->select('title,count(1) as num','1 GROUP BY title');

    }
    public function icon_list($uid){

        $this->table = $this->table_fix.'icon';

        return $this->select('id,src','uid='.$uid.' order by id desc limit 3');

    }
    public function cur_icon($uid){

        $this->table = $this->table_fix.'icon';

        return $this->select('id,src','uid='.$uid.' AND status=1 order by id desc limit 1',false);

    }

    public function add_icon($uid,$file){

        $this->table = $this->table_fix.'icon';

        $this->insert('src,uid,status',array($file,$uid,1));

        return $this->lastInsertId();

    }
    public function set_icon($id,$uid){

        $this->table = $this->table_fix.'icon';

        $this->update(array('status'=>0),'uid='.$uid);

        return $this->update(array('status'=>1),'id='.$id);
    }

    public function add_feat($uid,$title){

        $this->table = $this->table_fix.'edu';

        $this->check_feat($uid,$title) or $this->insert('uid,title',array($uid,$title));

        return $this->lastInsertId();

    }
    public function del_feat($id){

        $this->table = $this->table_fix.'feat';

        return $this->delete('id='.$id);

    }
    public function add_job($data){
        $this->table = $this->table_fix.'job';
        $this->insert('firm,sector,title,address,start_time,end_time,uid',$data);
        return $this->lastInsertId();
    }



} 