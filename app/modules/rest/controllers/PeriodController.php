<?php

class Rest_PeriodController extends Zend_Rest_Controller {
	public function init() {
		$this->_helper->layout()->disableLayout();
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;

	}

    public function headAction() {}public function indexAction() {}

    public function getAction() {
        $id = $this->_getParam('id');

        //dataBases
        $periodDb = new Api_Model_DbTable_Periods();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $year = substr($id, 2);
        $where = array(
                        'eid'           => $eid,
                        'oid'           => $oid,
                        'left(perid,2)' => $year );
        $attrib = null;
        $order = array('state');
        $periods_pd = $periodDb->_getFilter($where, $attrib, $order);

        $periods_data = array();
        if ($periods_pd) {
            foreach ($periods_pd as $c => $period) {
                    $periods_data[$c] = array(
                                            'idget'          => base64_encode($period['perid']),
                                            'code'           => $period['perid'],
                                            'name'           => $period['name'],
                                            'resolution'     => $period['document_auth'],
                                            'class_start'    => date('d-m-Y', strtotime($period['class_start_date'])),
                                            'class_end'      => date('d-m-Y', strtotime($period['class_end_date'])),
                                            'register_start' => date('d-m-Y', strtotime($period['start_registration'])),
                                            'register_end'   => date('d-m-Y', strtotime($period['end_registration'])),
                                            'first_start'    => date('d-m-Y', strtotime($period['start_register_note_p'])),
                                            'first_end'      => date('d-m-Y', strtotime($period['end_register_note_p'])),
                                            'second_start'   => date('d-m-Y', strtotime($period['start_register_note_s'])),
                                            'second_end'     => date('d-m-Y', strtotime($period['end_register_note_s'])),
                                            'state'          => $period['state'] );
            }
        }
        
        if ($periods_data) {
            return $this->_helper->json->sendJson($periods_data);
        } else {
            return null;
        }
    }


    public function postAction() {
        //Database
        $periodDb = new Api_Model_DbTable_Periods();

        //Form
        $period_form = new Period_Form_Period();

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $uid = $this->sesion->uid;

        $period_data = $this->getRequest()->getRawBody();

        if ($period_data) {
            $period_data = Zend_Json::decode($period_data);
            if ($period_form->isValid($period_data)) {
                $period_save = array(
                                        'eid'                   => $eid,
                                        'oid'                   => $oid,
                                        'perid'                 => $period_data['code'],
                                        'name'                  => $period_data['name'],
                                        'document_auth'         => $period_data['resolution'],
                                        'class_start_date'      => date('Y-m-d', strtotime($period_data['class_start'])),
                                        'class_end_date'        => date('Y-m-d', strtotime($period_data['class_end'])),
                                        'start_registration'    => date('Y-m-d', strtotime($period_data['register_start'])),
                                        'end_registration'      => date('Y-m-d', strtotime($period_data['register_end'])),
                                        'start_register_note_p' => date('Y-m-d', strtotime($period_data['first_start'])),
                                        'end_register_note_p'   => date('Y-m-d', strtotime($period_data['first_end'])),
                                        'start_register_note_s' => date('Y-m-d', strtotime($period_data['second_start'])),
                                        'end_register_note_s'   => date('Y-m-d', strtotime($period_data['second_end'])),
                                        'state'                 => $period_data['state'],
                                        'register'              => $uid,
                                        'created'               => date('Y-m-d h:i:s') );

                if ($periodDb->_save($period_save)) {
                    $result['idget'] = base64_encode($period_data['code']);
                    
                    //renderear si esta en el mismo año...
                    $result['render'] = true;
                    $year = substr($period_data['year'], 2);
                    $year_perid = substr($period_data['code'], 0, 2);
                    if ($year != $year_perid) {
                        $result['render'] = false;
                    }


                    $result['success'] = true;
                }
            } else {
                $result['success'] = false;
                $result['errors']  = array();
                $cError = 0;
                foreach ($period_form->getMessages() as $typeError) {
                    foreach ($typeError as $error) {
                        $result['errors'][$cError]  = $error;
                    }
                    if ($cError == 2) {
                        break;
                    }
                    $cError++;
                }
            }
        }
        return $this->_helper->json->sendJson($result);
    }

    public function putAction() {
        //dataBase
        $periodDb = new Api_Model_DbTable_Periods();

        //Form
        $period_form = new Period_Form_Period();
        
        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;
        $uid = $this->sesion->uid;

        $id    = base64_decode($this->_getParam('id'));
        $perid = $id;

        $period_data = $this->getRequest()->getRawBody();
        if ($period_data) {
            $period_data = Zend_Json::decode($period_data);
            if ($period_form->isValidPartial($period_data)) {
                $result['hard_render'] = false;
                if ($period_data['state_before'] != $period_data['state']) {
                    $result['hard_render'] = true;
                }

                if ($period_data['state'] == 'A') {
                    $where = array(
                                    'eid'            => $eid,
                                    'oid'            => $oid,
                                    'left(perid, 2)' => substr($perid, 0, 2),
                                    'state'          => 'A' );
                    $period_active = $periodDb->_getFilter($where);
                    if ($period_active) {
                        if ($period_active[0]['perid'] != $perid) {
                            $result['new_active'] = true;
                        }
                    }
                }

                $period_pk = array(
                                    'eid'   => $eid,
                                    'oid'   => $oid,
                                    'perid' => $perid );

                $period_updated = array(
                                        'name'                  => $period_data['name'],
                                        'document_auth'         => $period_data['resolution'],
                                        'class_start_date'      => date('Y-m-d', strtotime($period_data['class_start'])),
                                        'class_end_date'        => date('Y-m-d', strtotime($period_data['class_end'])),
                                        'start_registration'    => date('Y-m-d', strtotime($period_data['register_start'])),
                                        'end_registration'      => date('Y-m-d', strtotime($period_data['register_end'])),
                                        'start_register_note_p' => date('Y-m-d', strtotime($period_data['first_start'])),
                                        'end_register_note_p'   => date('Y-m-d', strtotime($period_data['first_end'])),
                                        'start_register_note_s' => date('Y-m-d', strtotime($period_data['second_start'])),
                                        'end_register_note_s'   => date('Y-m-d', strtotime($period_data['second_end'])),
                                        'state'                 => $period_data['state'],
                                        'modified'              => $uid,
                                        'updated'               => date('Y-m-d h:i:s') );

                if ($periodDb->_update($period_updated, $period_pk)) {
                    $result['success'] = true;
                }
            } else {
                $result['success'] = false;
                $result['errors']  = array();
                $cError = 0;
                foreach ($period_form->getMessages() as $typeError) {
                    foreach ($typeError as $error) {
                        $result['errors'][$cError]  = $error;
                    }
                    if ($cError == 2) {
                        break;
                    }
                    $cError++;
                }
            }
        }
        return $this->_helper->json->sendJson($result);
    }

    public function deleteAction() {
        //dataBase
        $periodDb = new Api_Model_DbTable_Periods();

        $id = $this->_getParam('id');
        $perid = base64_decode($id);

        $eid = $this->sesion->eid;
        $oid = $this->sesion->oid;

        $period_pk = array(
                        'eid'   => $eid,
                        'oid'   => $oid,
                        'perid' => $perid );
        if ($periodDb->_delete($period_pk)) {
            $result['success'] = true;
        } else {
            $result['success'] = false;
        }
        return $this->_helper->json->sendJson($result);
    }
}