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
        $sql = "SELECT inv.usaha_id kode, inv.jenis_usaha uraian, 
                        sum(inv.pokok) pokok, sum(inv.denda) as denda, sum(inv.bunga) as bunga, sum(inv.total) as total
                FROM public.pad_invoice inv
                WHERE TO_CHAR(inv.created,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                GROUP BY inv.usaha_id, inv.jenis_usaha
                ORDER BY 1,2 ";  
        
        $group = $this->get('group');
        if ($group) {
            $group = (int)$group;
            if ($group==1){
                $sql = "SELECT kode, uraian, kd_kecamatan, nm_kecamatan, sum(pokok) pokok, sum(denda) denda, sum(bunga) bunga,
                               sum(total) total 
                        FROM( SELECT 1 source, inv.usaha_id kode, inv.jenis_usaha uraian, kec.kode as kd_kecamatan, 
                                     kec.nama as nm_kecamatan, sum(inv.pokok) pokok, sum(inv.denda) as denda, 
                                     sum(inv.bunga) as bunga, sum(inv.total) as total
                              FROM public.pad_invoice inv
                                   INNER JOIN pad.pad_spt spt ON inv.source_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.pad_kecamatan kec ON cu.kecamatan_id = kec.id
                              WHERE inv.source_nama='pad_spt' AND TO_CHAR(inv.created,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5
                              UNION 
                              SELECT 2 source, inv.usaha_id kode, inv.jenis_usaha uraian, kec.kode as kd_kecamatan, kec.nama as nm_kecamatan,
                                      sum(inv.pokok) pokok, sum(inv.denda) as denda, sum(inv.bunga) as bunga, sum(inv.total) as total
                              FROM public.pad_invoice inv
                                   INNER JOIN pad.pad_stpd stp ON inv.source_id=stp.id
                                   INNER JOIN pad.pad_spt spt ON stp.spt_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.pad_kecamatan kec ON cu.kecamatan_id = kec.id
                              WHERE inv.source_nama='pad_stpd' AND TO_CHAR(inv.created,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5) AS drv
                        GROUP BY 1,2,3,4
                        ORDER BY 1,2,3,4 ";  
            }  
            elseif ($group==2){
                $sql = "SELECT kode, uraian, kd_kecamatan, nm_kecamatan, kd_kelurahan, nm_kelurahan,
                               sum(pokok) pokok, sum(denda) denda, sum(bunga) bunga, sum(total) total 
                        FROM( SELECT 1 source, inv.usaha_id kode, inv.jenis_usaha uraian, kec.kode as kd_kecamatan, 
                                     kec.nama as nm_kecamatan, kel.kode as kd_kelurahan, 
                                     kel.nama as nm_kelurahan, sum(inv.pokok) pokok, sum(inv.denda) as denda, 
                                     sum(inv.bunga) as bunga, sum(inv.total) as total
                              FROM public.pad_invoice inv
                                   INNER JOIN pad.pad_spt spt ON inv.source_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.pad_kecamatan kec ON cu.kecamatan_id = kec.id
                                   INNER JOIN pad.pad_kelurahan kel ON cu.kelurahan_id = kel.id
                              WHERE inv.source_nama='pad_spt' AND TO_CHAR(inv.created,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5,6,7
                              UNION 
                              SELECT 2 source, inv.usaha_id kode, inv.jenis_usaha uraian, kec.kode as kd_kecamatan, 
                                       kec.nama as nm_kecamatan, kel.kode as kd_kelurahan, kel.nama as nm_kelurahan, 
                                      sum(inv.pokok) pokok, sum(inv.denda) as denda, sum(inv.bunga) as bunga, sum(inv.total) as total
                              FROM public.pad_invoice inv
                                   INNER JOIN pad.pad_stpd stp ON inv.source_id=stp.id
                                   INNER JOIN pad.pad_spt spt ON stp.spt_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.pad_kecamatan kec ON cu.kecamatan_id = kec.id
                                   INNER JOIN pad.pad_kelurahan kel ON cu.kelurahan_id = kel.id
                              WHERE inv.source_nama='pad_stpd' AND TO_CHAR(inv.created,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5,6,7) AS drv
                        GROUP BY 1,2,3,4,5,6
                        ORDER BY 1,2,3,4,5,6 ";                
            } 
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
                FROM pad.pad_sspd a 
                  INNER JOIN public.pad_invoice b
                    on a.invoice_id=b.id
                WHERE TO_CHAR(a.sspdtgl,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                GROUP BY usaha_id, jenis_usaha
                ORDER BY 1,2
                ";  
        $group = $this->get('group');
        if ($group) {
            $group = (int)$group;
            if ($group==1){
                $sql = "SELECT kode, uraian, kd_kecamatan, nm_kecamatan, 
                               sum(pokok) pokok, sum(denda) denda, sum(bunga) bunga, sum(total) total 
                        FROM( SELECT 1 source, inv.usaha_id kode, inv.jenis_usaha uraian, 
                                     kec.kode as kd_kecamatan, kec.nama as nm_kecamatan,
                                     sum(a.jml_bayar-a.denda-a.bunga) pokok, sum(a.denda) as denda, sum(a.bunga) as bunga, 
                                     sum(a.jml_bayar) as total
                              FROM pad.pad_sspd a 
                                   INNER JOIN public.pad_invoice inv
                                              on a.invoice_id=inv.id
                                   INNER JOIN pad.pad_spt spt ON inv.source_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.pad_kecamatan kec ON cu.kecamatan_id = kec.id
                              WHERE inv.source_nama='pad_spt' AND TO_CHAR(a.sspdtgl,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5
                              UNION 
                              SELECT 1 source, inv.usaha_id kode, inv.jenis_usaha uraian,
                                     kec.kode as kd_kecamatan, kec.nama as nm_kecamatan,                              
                                     sum(a.jml_bayar-a.denda-a.bunga) pokok, sum(a.denda) as denda, sum(a.bunga) as bunga, 
                                     sum(a.jml_bayar) as total
                              FROM pad.pad_sspd a 
                                   INNER JOIN public.pad_invoice inv
                                              on a.invoice_id=inv.id
                                   INNER JOIN pad.pad_stpd stp ON inv.source_id=stp.id
                                   INNER JOIN pad.pad_spt spt ON stp.spt_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.pad_kecamatan kec ON cu.kecamatan_id = kec.id
                              WHERE inv.source_nama='pad_stpd' AND TO_CHAR(a.sspdtgl,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5) AS drv
                        GROUP BY 1,2,3,4
                        ORDER BY 1,2,3,4 ";  
            }  
            elseif ($group==2){
                $sql = "SELECT kode, uraian, kd_kecamatan, nm_kecamatan, kd_kelurahan, nm_kelurahan, 
                               sum(pokok) pokok, sum(denda) denda, sum(bunga) bunga, sum(total) total 
                        FROM( SELECT 1 source, inv.usaha_id kode, inv.jenis_usaha uraian, 
                                     kec.kode as kd_kecamatan, kec.nama as nm_kecamatan,
                                     kel.kode as kd_kelurahan, kel.nama as nm_kelurahan, 
                                     sum(a.jml_bayar-a.denda-a.bunga) pokok, sum(a.denda) as denda, sum(a.bunga) as bunga, 
                                     sum(a.jml_bayar) as total
                              FROM pad.pad_sspd a 
                                   INNER JOIN public.pad_invoice inv
                                              on a.invoice_id=inv.id
                                   INNER JOIN pad.pad_spt spt ON inv.source_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.pad_kecamatan kec ON cu.kecamatan_id = kec.id
                                   INNER JOIN pad.pad_kelurahan kel ON cu.kelurahan_id = kel.id
                              WHERE inv.source_nama='pad_spt' AND TO_CHAR(a.sspdtgl,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5,6,7
                              UNION 
                              SELECT 1 source, inv.usaha_id kode, inv.jenis_usaha uraian,
                                     kec.kode as kd_kecamatan, kec.nama as nm_kecamatan,                              
                                     kel.kode as kd_kelurahan, kel.nama as nm_kelurahan, 
                                     sum(a.jml_bayar-a.denda-a.bunga) pokok, sum(a.denda) as denda, sum(a.bunga) as bunga, 
                                     sum(a.jml_bayar) as total
                              FROM pad.pad_sspd a 
                                   INNER JOIN public.pad_invoice inv
                                              on a.invoice_id=inv.id
                                   INNER JOIN pad.pad_stpd stp ON inv.source_id=stp.id
                                   INNER JOIN pad.pad_spt spt ON stp.spt_id=spt.id
                                   INNER JOIN pad.pad_customer_usaha cu on cu.id = spt.customer_usaha_id
                                   INNER JOIN pad.pad_kecamatan kec ON cu.kecamatan_id = kec.id
                                   INNER JOIN pad.pad_kelurahan kel ON cu.kelurahan_id = kel.id
                              WHERE inv.source_nama='pad_stpd' AND TO_CHAR(a.sspdtgl,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              GROUP BY 1,2,3,4,5,6,7) AS drv
                        GROUP BY 1,2,3,4,5,6
                        ORDER BY 1,2,3,4,5,6 ";  
            } 
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
