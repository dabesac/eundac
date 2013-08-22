<?php

class Soporte_SubsidiaryController extends Zend_Controller_Action 
{

   public function indexAction() //Accion listar
    {
        try
        {
            $eid="20154605046";
            $oid="1";
            $where['eid']=$eid;
            $where['eid']=$oid;
            $sub= new Api_Model_DbTable_Subsidiary();
            $datasub=$sub->_getAll($where);
            //print_r($datasub);
            $this->view->datasub=$datasub;
        }
        catch (Exception $ex) 
        {
          print "Error al Listar ! ";
        }
    }
    

    public function newAction()
    {
        try 
        {
            $eid="20154605046";
            $oid="1"; 
            $form=new Soporte_Form_Subsidiary();
            $form->save->setLabel("Guardar");
            $this->view->form=$form;
            if ($this->getRequest()->isPost())
            {
                $formdata = $this->getRequest()->getPost();
                if ($form->isValid($formdata))
                { 
                    unset($formdata['save']);
                    $formdata['eid']=$eid;
                    $formdata['oid']=$oid;
                    trim($formdata['subid']);
                    trim($formdata['name']);
                    trim($formdata['state']);
                    $newrec=new Api_Model_DbTable_Subsidiary();
                    $newrec->_save($formdata);
                    $this->_redirect("/soporte/subsidiary");
                }
                else
                {
                    echo "Error! Ingrese nuevamente";
                }
            }
        } 
        catch (Exception $ex) 
        {
            print "Error  al crear Sede";
        }
    }


    public function updateAction()
    {
    	try
        {
            $eid="20154605046";
            $oid="1"; 
            $subid=$this->_getParam('subid');
            $pk['eid']=$eid;
            $pk['oid']=$oid;
            $pk['subid']=$subid;
            $dbsub=new Api_Model_DbTable_Subsidiary();
            $sub=$dbsub->_getOne($pk);
            //print_r ($sub);
            $form=new Soporte_Form_Subsidiary();
            $form->subid->setAttrib("readonly","true");
            $form->populate($sub);
            $this->view->form=$form;
            if($this->getRequest()->isPost())
            {
                $frmdata=$this->getRequest()->getPost();
                if ($form->isValid($frmdata))
                { 
                    unset($frmdata['update']);
                    trim($frmdata['subid']);
                    trim($frmdato['name']);
                    trim($frmdato['state']);
                    $str=array();
                    $str="eid='$eid' and oid='$oid' and subid='$subid'";
                    $dbrec=new Api_Model_DbTable_Subsidiary();
                    $rec=$dbrec->_update($frmdata,$pk);
                    //print_r($frmdata);
                    $this->_redirect("/soporte/subsidiary");
                }
                else
                {
                    echo "Ingrese nuevamente por favor";
                }
            }            
        }
        catch (Exception $ex)
        {
            print "Error Modificando el Recurso: ".$ex->getMessage();
        }
    }

    
    public function deleteAction()
    {
        try 
        {            
            $eid="20154605046";
            $oid="1";
            $subid=$this->_getParam('subid');
            $pk['eid']=$eid;
            $pk['oid']=$oid;
            $pk['subid']=$subid;
            $del=new Api_Model_DbTable_Subsidiary();
            if ($del->_delete($pk))
            {
                $this->_helper->_redirector("index");
            }
            else
            {
                echo "error al eliminar";
            }
        }
        catch (Exception $ex)
        {
            print "Error al Eliminar Sede";
        }
    }
}