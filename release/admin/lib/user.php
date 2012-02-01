<?php
/**
 * modify user information
 * @since 11.01
 */
class User{
    function __construct($conf=null){
        $this->dataPath=isset($conf['dataPath'])?$conf['dataPath']:USER_DATA_PATH;
        if (isset($conf['user'])){
            $this->user = $conf['user'];
        }else{
            $this->user = array();
        }

        if (isset($conf['salt'])){
            $this->salt = $conf['salt'];
        }else{
            $this->salt = "XPress";
        }
        $this->preComment="//[admin_info]";
        $this->postComment="//[admin_info end]";
        $this->userData=null;
        $this->userDataRe=str_replace("]","\]",str_replace("[","\[","{".$this->preComment.".*".$this->postComment."}is"));
    }

    public function check($username,$password){
        foreach($this->user as $userid=>$userData){
            if(isset($userData['username']) && $username===$userData['username'] && $password===$userData['password']){
                $this->userData=$userData;
                return true;
            }
        }
        return false;
    }

    /**
     * @param {array}  
     * @return {bool}
     */
    public function delete($index){
        if($index!==false){
            unset($this->user[$index]);
        }
        return $this->dump();
	}

    /**
     * @param {array}  
     */
	public function modify($data){
        $res=False;
        if(isset($data['password'])){
            $data['password']=$this->getPwd($data['password']);
        }
        if(isset($data['username'])){
            $index=$this->getIndexByUsername($data['username']);
            if($index!==false){
                $this->modifyUser($index,$data);
            }else{
                $this->user[]=$data;
            }
            $res=$this->dump();
        }
        return $res;
    }

    public function getPwd($text){
        if (isset($text)){
            return md5($this->salt.$text.$this->salt);
        }else{
            error_log("no text in get pwd");
            return false;
        }
    }

    function modifyUser($index,$data){
        if($index && $data &&isset($this->user[$index])){
            foreach ($data as $key=>$value){
                $this->user[$index][$key]=$value;
            }
        }
    }

    function dump(){
        $data=file_get_contents($this->dataPath);
        $newData=$this->userData2Text();
        if ($newData){
            $new=$this->preComment."\n".$newData.$this->postComment;
            $res=preg_replace($this->userDataRe,$new,$data);
            return file_put_contents($this->dataPath,$res);
        }
        return false;
    }

    function userData2Text(){
        $newData="";
        foreach($this->user as $index =>$oneUser){
            foreach($oneUser as $key =>$value){
                $newData.="\$user[".$index."]['".$key."']='".$value."';\n";
            }
        }
        return $newData;
    }

    function getIndexByUsername($name){
        foreach($this->user as $index=>$oneUser){
            if ($oneUser['username']===$name){
                return $index;
            }
        }
        return false;
    }
}
