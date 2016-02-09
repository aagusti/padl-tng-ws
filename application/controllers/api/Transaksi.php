<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Transaksi extends REST_Controller {
    //$this->load->model('Invoice_Model');
    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        //$this->methods['user_get']['limit'] = 500; // 500 requests per hour per user/key
        //$this->methods['user_post']['limit'] = 100; // 100 requests per hour per user/key
        //$this->methods['user_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function ketetapan_get(){
        if(!$this->get('awal')||!$this->get('akhir'))
           $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid Parameter'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
                
        $awal = preg_replace("/[^0-9]/","",$this->get('awal'));
        $akhir = preg_replace("/[^0-9]/","",$this->get('akhir'));
        $query = Null;
        $sql = "SELECT usaha_id kode, jenis_usaha uraian, 
                        sum(pokok) pokok, sum(denda) as denda, sum(bunga) as bunga, sum(total) as total
                FROM pad_invoice
                WHERE TO_CHAR(created,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                GROUP BY usaha_id, jenis_usaha
                ORDER BY 1,2 ";  
        
        $query = $this->db->query($sql)->result_array();
        
        
        if($query) {
            //$wil = array('wilayah' => LICENSE_TO);
            $ret = $query; // array_merge($query, $wil);
            $this->response($ret, 200); // 200 being the HTTP response code
        } else {
           $this->response([
                    'status' => FALSE,
                    'message' => 'Data Not Found'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }
    
    public function realisasi_get(){
        if(!$this->get('awal')||!$this->get('akhir'))
           $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid Parameter'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
                
        $awal = preg_replace("/[^0-9]/","",$this->get('awal'));
        $akhir = preg_replace("/[^0-9]/","",$this->get('akhir'));
        $query = Null;
        $sql = "SELECT b.usaha_id kode, b.jenis_usaha uraian, 
                       sum(a.jml_bayar-a.denda-a.bunga) pokok, sum(a.denda) as denda, sum(a.bunga) as bunga, 
                       sum(a.jml_bayar) as total
                FROM pad_sspd a 
                  INNER JOIN pad_invoice b
                    on a.invoice_id=b.id
                WHERE TO_CHAR(a.sspdtgl,'YYYYMMDD') BETWEEN '20160101' AND '20160131'
                GROUP BY usaha_id, jenis_usaha
                ORDER BY 1,2
                ";  
                        
        $query = $this->db->query($sql)->result_array();
        
        if($query) {
            //$wil = array('wilayah' => LICENSE_TO);
            $ret = $query; // array_merge($query, $wil);
            $this->response($ret, 200); // 200 being the HTTP response code
        } else {
           $this->response([
                    'status' => FALSE,
                    'message' => 'Data Not Found'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }
}
