<?php

class Api_IndexController extends Zend_Controller_Action {

    public function init()
    {
       
    }
    public function indexAction()
    {
<<<<<<< HEAD
    	$where['eid']='09887';
        $where['oid']='09887';


    	
    	$eid='43';
        $oid='567';
        $escid='4SI';
echo "fghj";
    $esc = new Api_Model_DbTable_Org();
    $escuela = $esc->_getAll($where);
    print_r($escuela);

    	
=======
        // $method='_getOne';
    	$curso = new Api_Model_DbTable_PeriodsCourses();
        // $where['eid']==""|| $where['oid']=='' || $where['curid']=='' || $where['escid']=="" || $where['subid']=='' || $where['courseid']==''
        $where= array("eid"=> '20154605046' ,"oid"=>'1' ,"escid"=>'4SI','perid'=>'13A' );
        $atrib = array('courseid','turno','semid');
        $order = array('courseid  ASC','turno asc','semid asc');
        $data= $curso->_getFilter($where,$atrib,$order);
>>>>>>> 8803262b07ca247a184ea08bdba4ff433d8ced7b

        // for ($i=0; $i < ; $i++) { 
        //     # code...
        // }
        foreach ($data as $key => $curs) {
            $where = array('eid'=>'20154605046',"oid"=>'1','courseid'=>$curs['courseid']);
            $attrib=array('name','courseid');
            $info_curso[$key]=$curso ->_getinfoCourse($where,$attrib);
            $info_curso['turno']=$curs['turno'];
            // $info_curso[$key]
        }
        // $arrayName = array_values(where);
        print_r($info_curso);
    }
}
