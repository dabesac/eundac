<?php

class Soporte_FacultyController extends Zend_Controller_Action 
{
	public function indexAction() 
    {
        try 
        {
            $where['eid']="20154605046";
            $where['oid']="1";
            $fac= new Api_Model_DbTable_Faculty();
            $datafac=$fac->_getAll($where);
            // print_r($datorg);
            $this->view->datafac=$datafac;
            
        } 
        catch (Exception $ex) 
        {
          print "Error aL listar las Facultades: ".$ex->getMessage();
        }
    }

    public function newAction()
    {
    	try 
        {
            $eid="20154605046";
            $oid="1";
            $reg="000XXX";
            $form= new Soporte_Form_Faculty();
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
                    $formdata['register']=$reg;
                    trim($formdata['facid']);
                    trim($formdata['name']);
                    trim($formdata['abbreviation']);
                    trim($formdata['created']);
                    trim($formdata['state']);
                    $newrec=new Api_Model_DbTable_Faculty();
                    $newrec->_save($formdata);
                    //print_r($formdata);
                    $this->_redirect("/soporte/faculty");
                }
                else
                {
                    echo "Ingrese Nuevamente";
                }
            }
        } 
        catch (Exception $ex) 
        {
          print "Error ".$ex->getMessage();
        }
    }

    public function updateAction()
    {
    	try 
        {
            $eid="20154605046";
            $oid="1";
            $reg="000XXX";
            $facid=$this->_getParam('facid');
            $where['eid']=$eid;
            $where['oid']=$oid;
            $where['facid']=$facid;
            $dbfac= new Api_Model_DbTable_Faculty();
            $fac=$dbfac->_getOne($where);
            //print_r($fac);
            $form= new Soporte_Form_Faculty();
            $form->populate($fac);
            $form->facid->setAttrib("readonly","true");
            $this->view->form=$form;
            if ($this->getRequest()->isPost())
            {
                $formdata = $this->getRequest()->getPost();
                if ($form->isValid($formdata))
                { 
                    unset($formdata['update']);
                    trim($formdata['name']);
                    trim($formdata['abbreviation']);
                    trim($formdata['created']);
                    trim($formdata['state']);
                    $newrec=new Api_Model_DbTable_Faculty();
                    $newrec->_update($formdata,$where);
                    //print_r($formdata);
                    $this->_redirect("/soporte/faculty");
                }
                else
                {
                    echo "Ingrese Nuevamente";
                }
            }
        } 
        catch (Exception $ex) 
        {
          print "Error ".$ex->getMessage();
        }
    }



     public function deleteAction()
    {
        try 
        {            
            $eid="20154605046";
            $oid="1";
            $facid=$this->_getParam('facid');
            $pk['oid']=$oid;
            $pk['eid']=$eid;
            $pk['facid']=$facid;
            $del=new Api_Model_DbTable_Faculty();
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
            print "Error al Eliminar la Organizacion ".$ex->getMessage();
        }
    }
}
