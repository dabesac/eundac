
<?php

class Acreditacion_IndexController extends Zend_Controller_Action {

    public function init()
    {
       $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
    }

    public function indexAction()
    {
        try {

            

            
        } catch (Exception $e) {
            
        }

           
    }

  function connect (){
           //   // include('/xmlrpc/lib/xmlrpc.inc');
           //  $user = 'admin';
           //  $password = 'sistemas';
           //  $dbname = 'prueba';
           //  $server_url = 'http://localhost:8069/xmlrpc/';

           //  if(isset($_COOKIE["user_id"]) == true)  {
           //     if($_COOKIE["user_id"]>0) {
           //     return $_COOKIE["user_id"];
           //     }
           // }

           // $sock = new xmlrpc_demo_client_client($server_url.'common');
           // $msg = new xmlrpcmsg('login');
           // $msg->addParam(new xmlrpcval($dbname, "string"));
           // $msg->addParam(new xmlrpcval($user, "string"));
           // $msg->addParam(new xmlrpcval($password, "string"));
           // $resp =  $sock->send($msg);
           // $val = $resp->value();
           // $id = $val->scalarval();
           // setcookie("user_id",$id,time()+3600);
           // if($id > 0) {
           //     return $id;
           // }else{
           //     return -1;
           // }

    }


    public function listforelementsAction(){
    	
    }
}
