
<?php

class Acreditacion_SocialprojectionController extends Zend_Controller_Action {
    public $id_user_openerp;

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
            /**
            ** @param /***atributos para sql**
            ******/
            // $connect = new Eundac_Connect_openerp();
            // $query= array(
            //         array(
            //             'column'=>'identification_id',
            //             'operator'=>'=',
            //             'value'=>$this->sesion->infouser['numdoc'],
            //             'type'=>'string'
            //         )
            //     ); 
            // $attributes = array('idate(format)');
            // $ids = $connect->search('hr.employee',$query);
            // $identification_id = $connect->read($ids,$attributes,'hr.employee');
            // $this->id_user_openerp=$identification_id[0]['id'];

            //  $query_1= array(
            //         array(
            //             'column'=>'author',
            //             'operator'=>'=',
            //             'value'=>trim($this->id_user_openerp),
            //             'type'=>'int'
            //         ),array(
            //             'column'=>'state',
            //             'operator'=>'=',
            //             'value'=>'E',
            //             'type'=>'string'
            //         )
            //     );  
            
            // // $data_project = array();
            // $ids = $connect->search('inv.pro.project',$query_1);
            // if ($ids) {
            //     $attributes = array('id','name');
            //     $data_project = $connect->read($ids,$attributes,'inv.pro.project');
            // }
            // print_r($data_project);
            // $this->view->data_project=$data_project;
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
            if ($form->isValid($formData)) {
                print_r($formData);
                exit();
            }else{
                $this->view->form=$form;
            }
        }else{
            $this->view->form=$form;
        }

        $this->_helper->layout->disableLayout();
        
    }


    public function listforelementsAction(){
    	
    }
}
