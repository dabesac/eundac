<?php
class Docente_LogoheaderController extends Zend_Controller_Action {

    public function init()
    {
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        if (!$login->rol['module']=="docente"){
            $this->_helper->redirector('index','index','default');
        }
        $this->sesion = $login; 
    }
    public function indexAction(){
        try {
        //print_r($this->sesion);
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;
        $this->view->facultad = $this->sesion->faculty->name;
        $this->view->speciality = $this->sesion->speciality->name;
        $wherescid= array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
        
        $esc= new Api_Model_DbTable_Speciality();
        $datescid=$esc->_getOne($wherescid);      

        $date=$datescid['header'];
        $this->view->header=$date;
        
        $form= new Docente_Form_Logo();
        $wherescid= array('eid'=>$eid,'oid'=>$oid,'escid'=>$escid,'subid'=>$subid);
        $esc= new Api_Model_DbTable_Speciality();
        $datescid=$esc->_getOne($wherescid);
        $form->populate($datescid);

        if ($this->getRequest()->isPost()) 
        {

            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                unset($formData['MAX_FILE_SIZE']);
                unset($formData['save']);
                $upload = new Zend_File_Transfer_Adapter_Http();
                $filterRename = new Zend_Filter_File_Rename(array('target' => '/srv/www/eundac/html/logo/' .$eid."_".$oid."_".$escid."_".$subid, 'overwrite' => false));

                $nombre_fichero = '/srv/www/eundac/html/logo/'.$eid."_".$oid."_".$escid."_".$subid;
                if (file_exists($nombre_fichero)) {
                    unlink($nombre_fichero);
                }

                $upload->addFilter($filterRename);
                if (!$upload->receive()) {
                    $this->view->message = 'Error receiving the file';
                   
                }   
                $tamano=getimagesize($nombre_fichero);
                $tipo=substr($tamano['mime'],6,10);
                switch ($tipo) {
                    case 'png':
                        $original=imagecreatefrompng($nombre_fichero);                        
                        break;
                    case 'jpeg':
                        $original=imagecreatefromjpeg($nombre_fichero);
                        break;    
                    case 'gif':
                        $original=imagecreatefromgif($nombre_fichero);
                        break;
                    default:
                        $original=null;
                        break;

                }
                $max_ancho=100;
                $max_alto=100;
                list($ancho,$alto)=getimagesize($nombre_fichero);
                $x_ratio= $max_ancho/$ancho;
                $y_ratio= $max_alto/$alto;

                if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){
                    $ancho_final = $ancho;
                    $alto_final = $alto;
                }
                elseif (($x_ratio * $alto) < $max_alto){
                    $alto_final = ceil($x_ratio * $alto);
                    $ancho_final = $max_ancho;
                }
                else{
                    $ancho_final = ceil($y_ratio * $ancho);
                    $alto_final = $max_alto;
                }
                $lienzo=imagecreatetruecolor($ancho_final,$alto_final); 

                imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
                imagedestroy($original);
                $cal=90;

                switch ($tipo) {
                    case 'png':
                        imagepng($lienzo,$nombre_fichero,9);
                        break;
                    case 'jpeg':
                        imagejpeg($lienzo,$nombre_fichero,$cal);
                        break;    
                    case 'gif':
                        imagegif($lienzo,$nombre_fichero);
                        break;
                    default:
                        // $original=null;
                        break;

                }
                
                $pk=array();
                $pk['eid']=$eid;
                $pk['oid']=$oid;                           
                $pk['escid']=$escid;                     
                $pk['subid']=$subid;
                $formData['header']=$eid."_".$oid."_".$escid."_".$subid;


                if($esc->_update($formData,$pk)){
                    $this->_redirect('/docente/logoheader/index'); 
                }
            }

         }
            $this->view->form=$form;
        } catch (Exception $e) {
             print "Error: ".$e->getMessage();
        }
    }
}