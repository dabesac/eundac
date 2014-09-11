<?php
    
    include("xmlrpc/lib/xmlrpc.inc");
    
class Eundac_Connect_openerp {

    public $server = "http://erp.undac.edu.pe:8069/xmlrpc/";
    //public $database = "erp";
    public $database = "acreditacion2";

    public $uid = "";/**  @uid = once user succesful login then this will asign the user id */
    public $username = "admin"; /*     * * @userid = general name of user which require to login at openerp server */
    public $password = "sistemas";/** @password = password require to login at openerp server * */
    public $auth = false;

    public function __construct(){
        $username= $this->username;
        $password= $this->password;
        $database= $this->database;
        $server= $this->server;
        $auth = $this->login($username,$password,$database,$server);
        if ($auth > 0) {
            $this->auth = true;
        }
    }   

    public function login($username , $password, $database, $server) {

        $sock = new xmlrpc_client($this->server . 'common');
        $msg = new xmlrpcmsg('login');
        $msg->addParam(new xmlrpcval($this->database, "string"));
        $msg->addParam(new xmlrpcval($this->username, "string"));
        $msg->addParam(new xmlrpcval($this->password, "string"));

        $resp = $sock->send($msg);
        if($resp->errno > 0 ){
            print "Error : ". $resp->errstr;
            return -1;
        }
        //$val = $resp->value();
        //$id = $val->scalarval();
        $this->uid = $resp->value()->me['int'];
        if ( $resp->value()->me['int'] ) {
            return $resp->value()->me['int']; //* userid of succesful login person *//
            $this->auth = true;
        } else {
            return -1; //** if userid not exists , username or password wrong.. */
        }
    }
    public function create($model_name,$data=array()) {
        $client = new xmlrpc_client($this->server."object");

        $count = 0;
        foreach ($data as $key => $value) {
            $values[$key]= new xmlrpcval($value,'string'); 
        }

        // print_r($values); exit();
        //   ['execute','userid','password','module.name',{values....}]
        $msg = new xmlrpcmsg('execute');
        $msg->addParam(new xmlrpcval($this->database, "string"));  //* database name */
        $msg->addParam(new xmlrpcval($this->uid, "int")); /* useid */
        $msg->addParam(new xmlrpcval($this->password, "string"));/** password */
        $msg->addParam(new xmlrpcval($model_name, "string"));/** model name where operation will held * */
        $msg->addParam(new xmlrpcval("create", "string"));/** method which u like to execute */
        $msg->addParam(new xmlrpcval($values, "struct"));/** parameters of the methods with values....  */
        $resp = $client->send($msg);



        if ($resp->faultCode())
            return false; /* if the record is not created  */
        else
            return $resp->value()->scalarval();  /* return new generated id of record */
    }

    public function search($model_name,$query=array()){
        $client = new xmlrpc_client($this->server."object");
        $count =0;
        for ($i=0; $i < count($query); $i++) { 
            if (
                array_key_exists('column', $query[$i]) && 
                array_key_exists('operator', $query[$i]) && 
                array_key_exists('value', $query[$i]) &&
                array_key_exists('type', $query[$i])
                ) {
                $query[$i]= new xmlrpcval(
                        array(
                                new xmlrpcval($query[$i]['column'],'string'),
                                new xmlrpcval($query[$i]['operator'],'string'),
                                new xmlrpcval($query[$i]['value'],$query[$i]['type'])
                            ),'array'
                    );
            }else{
                return false;
            }
        }

        $msg = new xmlrpcmsg('execute');
        $msg->addParam(new xmlrpcval($this->database, "string"));
        $msg->addParam(new xmlrpcval($this->uid, "int"));
        $msg->addParam(new xmlrpcval($this->password, "string"));
        $msg->addParam(new xmlrpcval($model_name, "string"));
        $msg->addParam(new xmlrpcval("search", "string"));
        $msg->addParam(new xmlrpcval($query, "array"));

        $resp = $client->send($msg);
        // print_r($)
        if ($resp->faultCode())
            return -1; /* if the record is not created  */
        else
            return $resp->value()->scalarval();  /* return new generated id of record */

    }
    public function write($model_name,$data=array(),$ids) {
        $client = new xmlrpc_client($this->server."object");
        //   ['execute','userid','password','module.name',{values....}]

        $id_val = array();
        $count = 0;
        foreach ($data as $key => $value) {
            $values[$key]= new xmlrpcval($value,'string'); 
        }
        foreach ($ids as $id)
            $id_val[$count++] = new xmlrpcval($id, "int");

        $msg = new xmlrpcmsg('execute');
        $msg->addParam(new xmlrpcval($this->database, "string"));  //* database name */
        $msg->addParam(new xmlrpcval($this->uid, "int")); /* useid */
        $msg->addParam(new xmlrpcval($this->password, "string"));/** password */
        $msg->addParam(new xmlrpcval($model_name, "string"));/** model name where operation will held * */
        $msg->addParam(new xmlrpcval("write", "string"));/** method which u like to execute */
        $msg->addParam(new xmlrpcval($id_val, "array"));/** ids of record which to be updting..   this array must be xmlrpcval array */
        $msg->addParam(new xmlrpcval($values, "struct"));/** parameters of the methods with values....  */
        $resp = $client->send($msg);

        if ($resp->faultCode())
            return -1;  /* if the record is not writable or not existing the ids or not having permissions  */
        else
            return $resp->value()->scalarval();  /* return new generated id of record */
    }

    public function read($id_val, $fields, $model_name) {
        if (!$this->auth) {
            return false;
        }
        $client = new xmlrpc_client($this->server."object");
        //   ['execute','userid','password','module.name',{values....}]
        $client->return_type = 'phpvals';

        // $id_val = array();
        // $count = 0;
        // foreach ($ids as $id)
        //     $id_val[$count++] = new xmlrpcval($id, "int");

        $fields_val = array();
        $count = 0;
        foreach ($fields as $field)
            $fields_val[$count++] = new xmlrpcval($field, "string");

        $msg = new xmlrpcmsg('execute');
        $msg->addParam(new xmlrpcval($this->database, "string"));  //* database name */
        $msg->addParam(new xmlrpcval($this->uid, "int")); /* useid */
        $msg->addParam(new xmlrpcval($this->password, "string"));/** password */
        $msg->addParam(new xmlrpcval($model_name, "string"));/** model name where operation will held * */
        $msg->addParam(new xmlrpcval("read", "string"));/** method which u like to execute */
        $msg->addParam(new xmlrpcval($id_val, "array"));/** ids of record which to be updting..   this array must be xmlrpcval array */
        $msg->addParam(new xmlrpcval($fields_val, "array"));/** parameters of the methods with values....  */
//        print_r($msg);
        $resp = $client->send($msg);

//        print_r($resp);

        if ($resp->faultCode())
            return -1;  /* if the record is not writable or not existing the ids or not having permissions  */
        else
            return ( $resp->value() );
    }

    public function unlink($ids , $model_name) {
        
        $client = new xmlrpc_client($this->server."object");
      
        $client->return_type = 'phpvals';

        $id_val = array();
        $count = 0;
        foreach ($ids as $id)
            $id_val[$count++] = new xmlrpcval($id, "int");

        $msg = new xmlrpcmsg('execute');
        $msg->addParam(new xmlrpcval($this->database, "string"));  //* database name */
        $msg->addParam(new xmlrpcval($this->uid, "int")); /* useid */
        $msg->addParam(new xmlrpcval($this->password, "string"));/** password */
        $msg->addParam(new xmlrpcval($model_name, "string"));/** model name where operation will held * */
        $msg->addParam(new xmlrpcval("unlink", "string"));/** method which u like to execute */
        $msg->addParam(new xmlrpcval($id_val, "array"));/** ids of record which to be updting..   this array must be xmlrpcval array */
//        $msg->addParam(new xmlrpcval($fields_val, "array"));/** parameters of the methods with values....  */
        $resp = $client->send($msg);

        if ($resp->faultCode())
            return -1;  /* if the record is not writable or not existing the ids or not having permissions  */
        else
            print_r( $resp->value() );
            //return ( $resp->value() );
    }
}

