
<?php

class Acreditacion_SocialprojectionController extends Zend_Controller_Action {

    public function init()
    {
       $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
    }
    public $id_user_openerp;
    public function indexAction()
    {
        try {
            /**
            ** @param /***atributos para sql**
            ******/
            $attributes = array('id');
            $query= array(
                    array(
                        'column'=>'author',
                        'operator'=>'=',
                        'value'=>$this->sesion->infouser['numdoc']
                    )
                );
            $data_project = array();
            $connect = new Eundac_Connect_openerp();
            $ids = $connect->search('inv.pro.project',$query);
            if ($ids) {
                $data_project = $connect->read($ids,$attributes,'inv.pro.project');
            }
            $this->view->data_project=$data_project;
            // $data = array(
            //     'state'=>'A',
            //     'name'=>'prueeeeeebita'
            //     );
            // $create =  $connect->create('sede',$data);
            // $ids = array('2');
            // $create =  $connect->write('sede',$data,$ids);


           // if ($create) {
           //     print_r($create);
           // }
        } catch (Exception $e) {
            
        }

           
    }

    public function newprojectAction(){
        $form = new Acreditacion_Form_Project();
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            
        }else{
            $this->view->form=$form;
        }


    }


    public function listforelementsAction(){
    	
    }
}
