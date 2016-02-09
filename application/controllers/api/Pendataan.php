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
class Pendataan extends REST_Controller {
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

    public function wp_get(){
        $query = Null;
        
        $group = $this->get('group')?$this->get('group') : 0;
        $group = (int)$group;
        
        if ($group==0){
                $sql = "SELECT kc.kode kd_kecamatan, kc.nama as nm_kecamatan, 
                     kl.kode kd_kelurahan, kl.nama as nm_kelurahan, count(*) jml
                FROM pad.pad_customer cu
                     INNER JOIN pad.pad_kecamatan kc on cu.kecamatan_id=kc.id
                     INNER JOIN pad.pad_kelurahan kl on cu.kelurahan_id=kl.id
                WHERE cu.enabled=1 
                GROUP BY 1,2,3,4
                ORDER BY 1,2,3,4;
               ";  
        }
        elseif ($group==1){
                $sql = "SELECT kc.kode kd_kecamatan, kc.nama as nm_kecamatan, 
                     count(*) jml
                FROM pad.pad_customer cu
                     INNER JOIN pad.pad_kecamatan kc on cu.kecamatan_id=kc.id
                     INNER JOIN pad.pad_kelurahan kl on cu.kelurahan_id=kl.id
                WHERE cu.enabled=1 
                GROUP BY 1,2
                ORDER BY 1,2;
               ";          
        }
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
    
    public function op_get(){
        $query = Null;
        
        $group = $this->get('group')?$this->get('group') : 0;
        $group = (int)$group;
        
        if ($group==0){
            $sql = "SELECT cu.usaha_id, us.nama, kc.kode as kd_kecamatan, kc.nama as nm_kecamatan, 
                             kl.kode as kd_kelurahan, kl.nama as nm_kelurahan, bandara, count(*) jml
                      FROM pad.pad_customer_usaha cu
                            INNER JOIN pad.pad_kecamatan kc on cu.kecamatan_id=kc.id
                            INNER JOIN pad.pad_kelurahan kl on cu.kelurahan_id=kl.id
                            INNER JOIN pad.pad_usaha us on cu.usaha_id = us.id
                      WHERE cu.enabled=1 
                      GROUP BY 1,2,3,4,5,6,7";  
        }
        elseif ($group==1){
              $sql = "SELECT cu.usaha_id, us.nama, kc.kode as kd_kecamatan, kc.nama as nm_kecamatan, 
                             count(*) jumlah
                      FROM pad.pad_customer_usaha cu
                            INNER JOIN pad.pad_kecamatan kc on cu.kecamatan_id=kc.id
                            INNER JOIN pad.pad_usaha us on cu.usaha_id = us.id
                      WHERE cu.enabled=1 
                      GROUP BY 1,2,3,4";  
        }

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
