<?php
class Admin_Form_Percentage extends Zend_Form{
    public $curid;
    public $escid;
    public $courseid;
    public $perid;
    public $turno;
    public $eid;
    public $oid;
    public $subid;
    public $partial;
    public $units;
    public $silstate;

    public function valorIsNumeric($value){
        
        if (is_numeric($value) && $value > 0 && $value <101){
                return true;
        }else{
                return false;
        }
        
    }
    
    public function setInputHidden($curid,$escid,$courseid,$perid,$turno,$eid,$oid,$subid,$partial,$units,$silstate){
            
            $this->curid  = $curid;
            $this->escid  = $escid;
            $this->courseid = $courseid;
            $this->perid = $perid;
            $this->turno = $turno;
            $this->eid = $eid;
            $this->oid = $oid;
            $this->subid =  $subid;
            $this->partial =  $partial;
            $this->units = $units;
            $this->silstate = $silstate;        
    }
    
    public function addInputHidden(){
        
              
        $hdcurid= new Zend_Form_Element_Hidden("hdcurid");
        $hdcurid->removeDecorator("HtmlTag")->removeDecorator("Label");
        $hdcurid->setValue($this->curid);
        $this->addElement($hdcurid);
        
        $hdescid= new Zend_Form_Element_Hidden("hdescid");
        $hdescid->removeDecorator("HtmlTag")->removeDecorator("Label");
        $hdescid->setValue($this->escid);
        $this->addElement($hdescid);
        
        $hdcourseid= new Zend_Form_Element_Hidden("hdcourseid");
        $hdcourseid->removeDecorator("HtmlTag")->removeDecorator("Label");
        $hdcourseid->setValue($this->courseid);
        $this->addElement($hdcourseid);
        
        $hdperid= new Zend_Form_Element_Hidden("hdperid");
        $hdperid->removeDecorator("HtmlTag")->removeDecorator("Label");
        $hdperid->setValue($this->perid);
        $this->addElement($hdperid);
        
        $hdturno= new Zend_Form_Element_Hidden("hdturno");
        $hdturno->removeDecorator("HtmlTag")->removeDecorator("Label");
        $hdturno->setValue($this->turno);
        $this->addElement($hdturno);
        
        $hdeid= new Zend_Form_Element_Hidden("hdeid");
        $hdeid->removeDecorator("HtmlTag")->removeDecorator("Label");
        $hdeid->setValue($this->eid);
        $this->addElement($hdeid);
        
        $hdoid= new Zend_Form_Element_Hidden("hdoid");
        $hdoid->removeDecorator("HtmlTag")->removeDecorator("Label");
        $hdoid->setValue($this->oid);
        $this->addElement($hdoid);
        
        $hdsubid= new Zend_Form_Element_Hidden("hdsubid");
        $hdsubid->removeDecorator("HtmlTag")->removeDecorator("Label");
        $hdsubid->setValue($this->subid);
        $this->addElement($hdsubid); 
        
        $hdpartial= new Zend_Form_Element_Hidden("hdpartial");
        $hdpartial->removeDecorator("HtmlTag")->removeDecorator("Label");
        $hdpartial->setValue($this->partial);
        $this->addElement($hdpartial); 

        $hdunits= new Zend_Form_Element_Hidden("hdunits");
        $hdunits->removeDecorator("HtmlTag")->removeDecorator("Label");
        $hdunits->setValue($this->units);
        $this->addElement($hdunits); 

        $hdsilstate= new Zend_Form_Element_Hidden("hdsilstate");
        $hdsilstate->removeDecorator("HtmlTag")->removeDecorator("Label");
        $hdsilstate->setValue($this->silstate);
        $this->addElement($hdsilstate); 
        
    }

    public function Persentages(){
        $sp1= new Zend_Form_Element_Text("txtspporcentaje1");
        $sp1->removeDecorator("HtmlTag")->removeDecorator("Label");
        $sp1->setAttrib("maxlength", "2")->setAttrib('size','10');
        $sp1->setAttrib("placeholder", "Conceptual");
        $sp1->setAttrib("class","data-uni-1 form-control");
        $sp1->setRequired(true)->addValidator('NotEmpty',true,array('messages' => '*'));
        $sp1->addValidator('Callback', true, array(
            'callback' => array($this, 'valorIsNumeric'),
            'messages' => '*'
        ));
        $this->addElement($sp1);
        
        $sp2= new Zend_Form_Element_Text("txtspporcentaje2");
        $sp2->removeDecorator("HtmlTag")->removeDecorator("Label");
        $sp2->setAttrib("maxlength", "2")->setAttrib('size','10');
        $sp2->setAttrib("class","data-uni-1 form-control ");
        $sp2->setAttrib("placeholder", "Procedimental");
        $sp2->setRequired(true)->addValidator('NotEmpty',true,array('messages' => '*'));
        $sp2->addValidator('Callback', true, array(
            'callback' => array($this, 'valorIsNumeric'),
            'messages' => '*'
        ));
        $this->addElement($sp2);

        $sp3= new Zend_Form_Element_Text("txtspporcentaje3");
        $sp3->removeDecorator("HtmlTag")->removeDecorator("Label");
        $sp3->setAttrib("maxlength", "2")->setAttrib('size','10');
        $sp3->setAttrib("class","data-uni-1 form-control");
        $sp3->setAttrib("placeholder", "Actitudinal");
        $sp3->setRequired(true)->addValidator('NotEmpty',true,array('messages' => '*'));
        $sp3->addValidator('Callback', true, array(
            'callback' => array($this, 'valorIsNumeric'),
            'messages' => '*'
        ));
        $this->addElement($sp3);
    }
    
    public function init(){
        
        $this->setAttrib("id", "frmCompetition");
        $this->setAction("/docente/percentage/percentagecompetition");
        $this->setEnctype("application/x-www-form-urlencoded");
        $this->setMethod(Zend_Form::METHOD_POST);
        
        
        
        $p1= new Zend_Form_Element_Text("txtppporcentaje1");
        $p1->removeDecorator("HtmlTag")->removeDecorator("Label");
        $p1->setAttrib("maxlength", "2")->setAttrib('size','10');
        $p1->setAttrib("placeholder", "Conceptual");
        $p1->setAttrib("class","data-uni-1 form-control");
        $p1->setRequired(true)->addValidator('NotEmpty',true,array('messages' => '*'));
        $p1->addValidator('Callback', true, array(
            'callback' => array($this, 'valorIsNumeric'),
            'messages' => '*'
        ));
        $this->addElement($p1);
        
        $p2= new Zend_Form_Element_Text("txtppporcentaje2");
        $p2->removeDecorator("HtmlTag")->removeDecorator("Label");
        $p2->setAttrib("maxlength", "2")->setAttrib('size','10');
        $p2->setAttrib("placeholder", "Procedimental");
        $p2->setAttrib("class","data-uni-1 form-control");
        $p2->setRequired(true)->addValidator('NotEmpty',true,array('messages' => '*'));
        $p2->addValidator('Callback', true, array(
            'callback' => array($this, 'valorIsNumeric'),
            'messages' => '*'
        ));
        $this->addElement($p2);

        $p3= new Zend_Form_Element_Text("txtppporcentaje3");
        $p3->removeDecorator("HtmlTag")->removeDecorator("Label");
        $p3->setAttrib("maxlength", "2")->setAttrib('size','10');
        $p3->setAttrib("placeholder", "Actitudinal");
        $p3->setAttrib("class","data-uni-1 form-control");
        $p3->setRequired(true)->addValidator('NotEmpty',true,array('messages' => '*'));
        $p3->addValidator('Callback', true, array(
            'callback' => array($this, 'valorIsNumeric'),
            'messages' => '*'
        ));
        $this->addElement($p3);

        


        
        $this->addElement(
            'submit',
            'btnenvio',
            array(
                'label' => 'Envio')
        );
        
    }
}
?>
