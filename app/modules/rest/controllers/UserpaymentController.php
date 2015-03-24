<?php

class Rest_UserpaymentController extends Zend_Rest_Controller {
	public function init() {
		$this->_helper->layout()->disableLayout();
        $sesion  = Zend_Auth::getInstance();
        if(!$sesion->hasIdentity() ){
            $this->_helper->redirector('index',"index",'default');
        }
        $login = $sesion->getStorage()->read();
        $this->sesion = $login;

	}

    public function headAction() {}

    public function indexAction() {
        //dataBases
        $paymentDb       = new Api_Model_DbTable_Payments();
        $paymentDetailDb = new Api_Model_DbTable_PaymentsDetail();
        $rateDb          = new Api_Model_DbTable_Rates();
        $bankreceiptsDb  = new Api_Model_DbTable_Bankreceipts();

        $eid   = $this->sesion->eid;
        $oid   = $this->sesion->oid;
        $uid   = $this->sesion->uid;
        $pid   = $this->sesion->pid;
        $rid   = $this->sesion->rid;
        $escid = $this->sesion->escid;
        $subid = $this->sesion->subid;
        //$perid = $this->sesion->period->perid;
        $perid = '14A';

        //pago de periodo actual
        $where = array(
                        'eid'   => $eid,
                        'oid'   => $oid,
                        'pid'   => $pid,
                        'uid'   => $uid,
                        'escid' => $escid,
                        'subid' => $subid,
                        'perid' => $perid );
        
        $payment_pd = $paymentDb->_getOne($where);

        $current_payment['total']     = '-';
        $current_payment['state']     = 'EP';
        $current_payment['time']      = null;
        $current_payment['increment'] = null;
        $current_payments_normal      = null;
        $current_payments_conditional = null;

        if ($payment_pd) {
            $payment_total = 0;

            //detalles de sus pagos
            $payment_detail_pd = $paymentDetailDb->_getFilter($where);
            $n_r = 0;
            foreach ($payment_detail_pd as $c => $payment) {
                $current_payments_normal[$c] = array(
                                                    'amount'    => $payment['amount'],
                                                    'operation' => $payment['operation'],
                                                    'concept'   => $payment['pcid'],
                                                    'date'      => $payment['date_payment'] );
                $payment_total = $payment_total + $payment['amount'];
            }

            //pagos condicionales
            $where = array(
                            'code_student' => $uid,
                            'perid'        => $perid,
                            'concept'      => '00000045' );
            $receipts_pd = $bankreceiptsDb->_getFilter($where);

            if ($receipts_pd) {
                foreach ($receipts_pd as $c => $payment) {
                    $current_payments_conditional[$c] = array(
                                                    'amount'    => $payment['amount'],
                                                    'operation' => $payment['operation'],
                                                    'concept'   => $payment['concept'],
                                                    'date'      => $payment['payment_date'] );
                    $payment_total = $payment_total + $payment['amount'];
                }
            }

            $current_payment['total'] = 'S/. '.$payment_total;



            // tasa y fechas de pago
            $where = array(
                            'eid'   => $eid,
                            'oid'   => $oid,
                            'ratid' => $payment_pd['ratid'],
                            'perid' => $perid );

            $rate_pd = $rateDb->_getOne($where);

            $current_payment['rate_name'] = $rate_pd['name'];

            // Verificar fecha de pago
            $payment_date   = date('Y-m-d', strtotime($payment_pd['date_payment']));
            $payment_amount = $payment_pd['amount'];

            $payment_date_end_tn  = date('Y-m-d', strtotime($rate_pd['f_fin_tnd']));
            $payment_date_end_tione = date('Y-m-d', strtotime($rate_pd['f_fin_ti1']));
            $payment_date_end_titwo = date('Y-m-d', strtotime($rate_pd['f_fin_ti2']));
            $payment_date_end_tithree = date('Y-m-d', strtotime($rate_pd['f_fin_ti3']));

            $payment_state = 'N';
            $payment_time  = 'OT';
            $payment_porcentage = null;

            if ($payment_date <= $payment_date_end_tn) {
                $payment_state = 'N';
                $payment_time  = 'N';
                $payment_porcentage = null;
                if ($payment_amount < $rate_pd['t_normal']) {
                    $payment_state = 'LP';
                    $payment_increment = $rate_pd['t_normal'];
                }
            } else if ($payment_date <= $payment_date_end_tione) {
                $payment_time  = 'PI';
                $payment_porcentage = $rate_pd['v_t_incremento1'];
                if ($payment_amount < $rate_pd['t_incremento1']) {
                    $payment_state = 'LP';
                    $payment_increment = $rate_pd['t_incremento1'];
                }
            } else if ($payment_date <= $payment_date_end_titwo) {
                $payment_time  = 'PII';
                $payment_porcentage = $rate_pd['v_t_incremento2'];
                if ($payment_amount < $rate_pd['t_incremento2']) {
                    $payment_state = 'LP';
                    $payment_increment = $rate_pd['t_incremento2'];
                }
            } else if ($payment_date <= $payment_date_end_tithree) {
                $payment_time  = 'PIII';
                $payment_porcentage = $rate_pd['v_t_incremento3'];
                if ($payment_amount < $rate_pd['t_incremento3']) {
                    $payment_state = 'LP';
                    $payment_increment = $rate_pd['t_incremento3'];
                }
            } else {
                $payment_state = 'OT';
            }
            
            $current_payment['state'] = $payment_state;
            $current_payment['time']  = $payment_time;
            $current_payment['increment']  = $payment_porcentage;

            $payment_amount_left = 0;
            if ($payment_state == 'LP') {
                if ($payment_amount < $payment_increment) {
                    $payment_amount_left = $payment_increment - $payment_amount;
                }
            }
            $current_payment['amount_left'] = $payment_amount_left;

        }

        $payment_data['current_payment'] = $current_payment;
        $payment_data['current_payments_normal'] = $current_payments_normal;
        $payment_data['current_payments_conditional'] = $current_payments_conditional;

        return $this->_helper->json->sendJson($payment_data);
    }

    public function getAction() {
        $result['success'] = true;
        return $this->_helper->json->sendJson($result);
    }


    public function postAction() {
        $result['success'] = 'post';
        return $this->_helper->json->sendJson($result);
    }

    public function putAction() {
        $result['success'] = 'put';
        return $this->_helper->json->sendJson($result);
    }

    public function deleteAction() {
        $result['success'] = 'true';
        return $this->_helper->json->sendJson($result);
    }
}