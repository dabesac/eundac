<?php
class Horary_Form_HoraryPeriods extends Zend_Form{    
    public function init(){
        $sesion  = Zend_Auth::getInstance();
        $login = $sesion->getStorage()->read();
        
        $this->setName("frmhoraryperiods");

        $where=array('eid'=>$login->eid,'oid'=>$login->oid);
        $facultyDB = new Api_Model_DbTable_Faculty();
        $data_faculty = $facultyDB->_getAll($where);

        $faculty = new Zend_Form_Element_Select("faculty");
        $faculty->removeDecorator('Label');
        $faculty->removeDecorator('HtmlTag');
        $faculty->setAttrib("class","form-control");
        // $faculty->addMultiOption("","Seleccione una Facultad");
        
        if ($data_faculty) {
            foreach ($data_faculty as $k => $data) {
                if ($data['facid']!='TODO' && $data['facid']!='NINGUN') {
                    $faculty->addMultiOption($data['facid'],$data['name'] );
                }
            }
        }

        $school = new Zend_Form_Element_Select("school");
        $school->removeDecorator('Label');
        $school->removeDecorator('HtmlTag');
        $school->setAttrib("class","form-control");
        $school->addMultiOption("","Primero seleccione una Facultad");

        $anio = new Zend_Form_Element_Select("anio");
        $anio->removeDecorator('Label');
        $anio->removeDecorator('HtmlTag');
        $anio->setAttrib("class","form-control");
        $anio->addMultiOption("","Seleccione Año");
        $year=date('Y');
        for ($i=$year; $i > 2000 ; $i--) { 
            $anio->addMultiOption($i,$i);
        }

        $perids = new Zend_Form_Element_Select("perids");
        $perids->removeDecorator('Label');
        $perids->removeDecorator('HtmlTag');
        $perids->setAttrib("class","form-control");
        $perids->addMultiOption("","Primero seleccione Año");
        
        $this-> addElements(array($perids, $anio,$faculty,$school));
    }
}