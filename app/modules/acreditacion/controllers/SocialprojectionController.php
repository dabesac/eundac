<?php

class Acreditacion_SocialprojectionController extends Zend_Controller_Action {
    public $id_user_openerp;
    public $id_subid_opem;
    public $id_escid_opem;

    public function init()
    {
       $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;
        $this->sesion->open_erp = new stdClass;

    }

    public function indexAction()
        {
        try {
            /**
            ** @param /***atributos para sql**
            ******/
            $connect = new Eundac_Connect_openerp();

            $query= array(
                    array(
                        'column'=>'identification_id',
                        'operator'=>'=',
                        'value'=>$this->sesion->infouser['numdoc'],
                        'type'=>'string'
                    )
                ); 

            $attributes = array('idate(format)');
            $ids = $connect->search('hr.employee',$query);
            $identification_id = $connect->read($ids,$attributes,'hr.employee');
            $this->id_user_openerp=$identification_id[0]['id'];
            $this->view->id_user_openerp=$identification_id[0]['id'];

            $query_cron = array();
            $attributes__cron = array();
            $ids_cronogram = $connect->search('inv.pro.cronogram',$query_cron);
            $data_cronograman = $connect->read($ids_cronogram,$attributes__cron,'inv.pro.cronogram');
            $this->view->data_cronograman = Zend_Json::encode($data_cronograman);
            // print_r($data_cronograman);
            $query_subid = array(
                    array(
                        'column'=>'subid',
                        'operator'=>'=',
                        'value'=>$this->sesion->subid,
                        'type'=>'int'
                    )
                );

            $attributes = array();
            $ids_subid = $connect->search('sede',$query_subid);
            

            $id_subid_opem = $connect->read($ids_subid,$attributes,'sede');
            $this->id_subid_opem= ($id_subid_opem >0)?$id_subid_opem[0]['id']:'';
            $this->view->id_subid_opem=($id_subid_opem>0)?$id_subid_opem[0]['id']:'';

            $query_escid = array(
                    array(
                        'column'=>'of_id',
                        'operator'=>'=',
                        'value'=>$this->sesion->escid,
                        'type'=>'string'
                        )
                );

            $attributes = array();
            $ids_escid = $connect->search('hr.department',$query_escid);
            $id_escid_opem = $connect->read($ids_escid,$attributes,'hr.department');
            $this->id_escid_opem=($id_escid_opem > 0)? $id_escid_opem[0]['id']:'';
            $this->view->id_escid_opem=($id_escid_opem > 0)? $id_escid_opem[0]['id']:'';
            $query_1= array(
                    array(
                        'column'=>'employee_id',
                        'operator'=>'=',
                        'value'=>trim($this->id_user_openerp),
                        'type'=>'int'
                    )
                    //,array(
                    //     'column'=>'state',
                    //     'operator'=>'=',
                    //     'value'=>'B',
                    //     'type'=>'string'
                    // )
                );  
            // $data_project = array();
            $attributes_author = array();
            $ids_project = $connect->search('inv.pro.authors',$query_1);
            $ids_project_data = $connect->read($ids_project,$attributes_author,'inv.pro.authors');
            // print_r($ids_project_data); exit();
            if ($ids_project) {
                $attributes = array('id','name','project','description','comment','type','state');
                $data_project = $connect->read($ids_project,$attributes,'inv.pro.project');
            }
            $this->view->data_project=$data_project;
            print_r($data_project);
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
                $connect = new Eundac_Connect_openerp();
                    $data = array(
                        'create_uid'=>1,
                        'create_date'=>date('Y-m-d H:m:s'),
                        'write_uid'=>1,
                        'cronogram_id'=>$formData['id_cronogram'],
                        'description'=>$formData['description'],
                        'comment'=>$formData['comment'],
                        // 'project'=>$img,
                        'name'=>$formData['name'],
                        'author'=>trim($formData['uid_openerp']),
                        'min_student'=>$formData['min_student'],
                        'max_student'=>$formData['max_student'],
                        'num_horas'=>$formData['num_horas'],
                        'state'=>trim($formData['state']),
                        'type'=>$formData['type'],
                        'sede_id'=>trim($formData['subid_openerp']),
                        'department_id'=>trim($formData['escid_openerp']),
                    );
                // print_r($data); exit();
                $create =  $connect->create('inv.pro.project',$data);
                if ($create) {
                    $upload = new Zend_File_Transfer_Adapter_Http();
                    $filename = $upload->getFilename();
                    $filename = basename($filename);
                    $uniqueToken = md5(uniqid(mt_rand(), true));
                    $filterRename = new Zend_Filter_File_Rename(array('target' => '/srv/www/eundac/html/logo/' . $uniqueToken.$filename, 'overwrite' => false));
                    $upload->addFilter($filterRename);
                    if (!$upload->receive()) {
                       echo 'Error receiving the file';
                        exit();
                    }
                    else{
                        // echo $img;
                        $json = array('status'=>true);
                        $this->_response->setHeader('Content-Type', 'application/json');                   
                        $this->view->json=Zend_Json::encode($json);
                    }

                }else{
                    // $this->view->
                }

            }else{
                $this->view->form=$form;
            }
        }else{
            $this->view->form=$form;
        }

        $this->_helper->layout->disableLayout();
        
    }

    public function modifiedprojectAction(){
        $form = new Acreditacion_Form_Project();
        $form->addElement('hidden', 'id_open');

        $connect = new Eundac_Connect_openerp();
        $query_cron = array();
        $attributes__cron = array();
        $ids_cronogram = $connect->search('inv.pro.cronogram',$query_cron);
        $data_cronograman = $connect->read($ids_cronogram,$attributes__cron,'inv.pro.cronogram');
        $this->view->data_cronograman = Zend_Json::encode($data_cronograman);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                // print_r($formData);exit();
                $data = array(
                        // 'create_date'=>date('Y-m-d H:m:s'),
                        'name'=>$formData['name'],
                        'cronogram_id'=>$formData['id_cronogram'],
                        'description'=>$formData['description'],
                        'comment'=>$formData['comment'],
                        'min_student'=>$formData['min_student'],
                        'max_student'=>$formData['max_student'],
                        'num_horas'=>$formData['num_horas'],
                        'state'=>trim($formData['state']),
                        'type'=>$formData['type'],
                    );
                // print_r($data);
                $ids[0]=$formData['id_open'];
                $modified =  $connect->write('inv.pro.project',$data,$ids);
                if ($modified) {
                    $json = array('status'=>true);
                    $this->_response->setHeader('Content-Type', 'application/json');                   
                    $this->view->json=Zend_Json::encode($json);
                }
            }else{
                $form->populate($formData);
            }
        }else{
            $id = $this->getParam('id');
            $form->id_open->setValue($id);
            $query= array(
                array(
                    'column'=>'id',
                    'operator'=>'=',
                    'value'=>$id,
                    'type'=>'int'
                )
            );
            $attributes = array();
            $ids = $connect->search('inv.pro.project',$query);
            $data_project = $connect->read($ids,$attributes,'inv.pro.project');
            $this->view->data_project=Zend_Json::encode($data_project[0]);
            $form->populate($data_project[0]);
            $this->view->form=$form;
        }

        $this->_helper->layout->disableLayout();
        
    }
    public function deleteprojectAction(){
        $id = $this->getParam('id');
        $connect = new Eundac_Connect_openerp();
        $ids[0] = $id;
        $data_project = $connect->unlink($ids,'inv.pro.project');
        $this->_helper->layout->disableLayout();
        $this->_redirect('/acreditacion/socialprojection/index');
    	
    }
}
