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
class Bphtb extends REST_Controller {
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
                        sum(inv.pokok) pokok, sum(inv.denda) as denda, sum(inv.bunga) as bunga, 
                        sum(inv.total) as total
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
    
    public function realisasibykode_get(){
        //||!$this->get('kode'))
        if(!$this->get('awal')||!$this->get('akhir'))
           $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid Parameter'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
                
        $awal = preg_replace("/[^0-9]/","",$this->get('awal'));
        $akhir = preg_replace("/[^0-9]/","",$this->get('akhir'));
        $query = Null;
        $sql = "SELECT '0000' as kode, 'BPHTB' as uraian, count(*) jumlah, 
                       sum(a.bayar-a.denda) pokok, sum(a.denda) as denda, 
                       sum(a.bayar) as total
                FROM bphtb.bphtb_bank a 
                WHERE TO_CHAR(a.tanggal,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                ";  
        $group = $this->get('group');
        if ($group) {
            $group = (int)$group;
            $kode = $this->get('kode');
            $kd_kec = substr($kode,0,3);
            $kd_kel = substr($kode,3);
            if ($group==1){
                $sql .= " AND a.kd_kecamatan='$kd_kec'";                        
            }  
            elseif ($group==2){
                $sql .= " AND a.kd_kecamatan='$kd_kec' AND a.kd_kelurahan='$kd_kel' ";                        
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
        $sql = "SELECT '0000' as kode, 'BPHTB' as uraian, count(*) jumlah, 
                       sum(a.bayar-a.denda) pokok, sum(a.denda) as denda, 
                       sum(a.bayar) as total
                FROM bphtb.bphtb_bank a 
                WHERE TO_CHAR(a.tanggal,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                ";  
        $group = $this->get('group');
        if ($group) {
            $group = (int)$group;
            $kode = $this->get('kode');
            $kd_kec = substr($kode,0,3);
            $kd_kel = substr($kode,3);   
            
            if ($group==1){
                $sql = "SELECT '0000' as kode, 'BPHTB' as uraian, a.kd_kecamatan, b.nm_kecamatan,
                               sum(a.bayar-a.denda) pokok, sum(a.denda) as denda, sum(a.bayar) as total
                        FROM bphtb.bphtb_bank a 
                            left join pbb.ref_kecamatan b
                                  on a.kd_propinsi=b.kd_propinsi and a.kd_dati2=b.kd_dati2 
                                     and a.kd_kecamatan=b.kd_kecamatan
                        WHERE TO_CHAR(a.tanggal,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                        GROUP BY 1,2,3,4
                        ORDER BY 1,2,3,4 ";  
            }  
            elseif ($group==2){
                $sql = "SELECT '0000' as kode, 'BPHTB' as uraian, a.kd_kecamatan, b.nm_kecamatan,
                               c.kd_kelurahan, c.nm_kelurahan,
                               sum(a.bayar-a.denda) pokok, sum(a.denda) as denda, sum(a.bayar) as total
                        FROM bphtb.bphtb_bank a 
                            left join pbb.ref_kecamatan b
                                  on a.kd_propinsi=b.kd_propinsi and a.kd_dati2=b.kd_dati2 
                                     and a.kd_kecamatan=b.kd_kecamatan
                            left join pbb.ref_kelurahan c
                                  on a.kd_propinsi=c.kd_propinsi and a.kd_dati2=c.kd_dati2 
                                     and a.kd_kecamatan=c.kd_kecamatan and a.kd_kelurahan=c.kd_kelurahan
                        WHERE TO_CHAR(a.tanggal,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
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
    
    public function monthly_get(){
        if(!$this->get('awal')||!$this->get('akhir'))
           $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid Parameter'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
                
        $awal = preg_replace("/[^0-9]/","",$this->get('awal'));
        $akhir = preg_replace("/[^0-9]/","",$this->get('akhir'));
        $query = Null;
        $sql = "SELECT TO_CHAR(a.tanggal,'MM') as kode, TO_CHAR(a.tanggal,'MON')  as bulan, sum(a.bayar-a.denda) pokok, sum(a.denda) as denda, 
                       sum(a.bayar) as total
                FROM bphtb.bphtb_bank a 
                WHERE TO_CHAR(a.tanggal,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                GROUP BY TO_CHAR(a.tanggal,'MM'),  TO_CHAR(a.tanggal,'MON')
                ORDER BY TO_CHAR(a.tanggal,'MM')
                ";  
        $group = $this->get('group');
        if ($group) {
            $group = (int)$group;
            $kode = $this->get('kode');
            $kd_kec = substr($kode,0,3);
            $kd_kel = substr($kode,3);            
            if ($group==1){
                $sql = "SELECT TO_CHAR(a.tanggal,'MM') as kode, TO_CHAR(a.tanggal,'MON')  as bulan, sum(a.bayar-a.denda) pokok, sum(a.denda) as denda, 
                       sum(a.bayar) as total
                FROM bphtb.bphtb_bank a 
                WHERE TO_CHAR(a.tanggal,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                AND a.kd_kecamatan='$kd_kec'
                GROUP BY TO_CHAR(a.tanggal,'MM'),  TO_CHAR(a.tanggal,'MON')
                ORDER BY TO_CHAR(a.tanggal,'MM')";  
            }  
            elseif ($group==2){
                $sql = "SELECT TO_CHAR(a.tanggal,'MM') as kode, TO_CHAR(a.tanggal,'MON')  as bulan, sum(a.bayar-a.denda) pokok, sum(a.denda) as denda, 
                       sum(a.bayar) as total
                FROM bphtb.bphtb_bank a 
                WHERE TO_CHAR(a.tanggal,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                AND a.kd_kecamatan='$kd_kec' AND a.kd_kelurahan='$kd_kel'
                GROUP BY TO_CHAR(a.tanggal,'MM'),  TO_CHAR(a.tanggal,'MON')
                ORDER BY TO_CHAR(a.tanggal,'MM')"; 
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
    public function realisasibywil_get(){
        //||!$this->get('kode'))
        if(!$this->get('awal')||!$this->get('akhir'))
           $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid Parameter'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
                
        $awal = preg_replace("/[^0-9]/","",$this->get('awal'));
        $akhir = preg_replace("/[^0-9]/","",$this->get('akhir'));
        $query = Null;
        $sql = "SELECT '0000' as kode, 'BPHTB' as uraian, a.kd_kecamatan, b.nm_kecamatan,
                       count(*) jumlah, sum(a.bayar-a.denda) pokok, sum(a.denda) as denda, sum(a.bayar) as total
                FROM bphtb.bphtb_bank a 
                    left join pbb.ref_kecamatan b
                          on a.kd_propinsi=b.kd_propinsi and a.kd_dati2=b.kd_dati2 
                             and a.kd_kecamatan=b.kd_kecamatan
                WHERE TO_CHAR(a.tanggal,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                GROUP BY 1,2,3,4
                ORDER BY 1,2,3,4 ";   
        $group = $this->get('group');
        if ($group) {
            #$group = (int)$group;
            #die($group);
            
            $kode = $this->get('kode');
            $kd_kec = substr($kode,0,3);
            $kd_kel = substr($kode,3);
            if ($group==1){
                        $sql = "SELECT '0000' as kode, 'BPHTB' as uraian, a.kd_kecamatan, b.nm_kecamatan,
                       count(*) jumlah, sum(a.bayar-a.denda) pokok, sum(a.denda) as denda, sum(a.bayar) as total
                FROM bphtb.bphtb_bank a 
                    left join pbb.ref_kecamatan b
                          on a.kd_propinsi=b.kd_propinsi and a.kd_dati2=b.kd_dati2 
                             and a.kd_kecamatan=b.kd_kecamatan
                WHERE TO_CHAR(a.tanggal,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                GROUP BY 1,2,3,4
                ORDER BY 1,2,3,4 ";   
            } else {
                    $sql = "SELECT '0000' as kode, 'BPHTB' as uraian, a.kd_kecamatan, b.nm_kecamatan,
                               c.kd_kelurahan, c.nm_kelurahan,
                               count(*) jumlah, sum(a.bayar-a.denda) pokok, sum(a.denda) as denda, sum(a.bayar) as total
                        FROM bphtb.bphtb_bank a 
                            left join pbb.ref_kecamatan b
                                  on a.kd_propinsi=b.kd_propinsi and a.kd_dati2=b.kd_dati2 
                                     and a.kd_kecamatan=b.kd_kecamatan
                            left join pbb.ref_kelurahan c
                                  on a.kd_propinsi=c.kd_propinsi and a.kd_dati2=c.kd_dati2 
                                     and a.kd_kecamatan=c.kd_kecamatan and a.kd_kelurahan=c.kd_kelurahan
                        WHERE TO_CHAR(a.tanggal,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'
                              AND a.kd_kecamatan='$kd_kec' 
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
    
    public function masuk_get(){
        if(!$this->get('awal')||!$this->get('akhir'))
           $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid Parameter'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
                
        $awal = preg_replace("/[^0-9]/","",$this->get('awal'));
        $akhir = preg_replace("/[^0-9]/","",$this->get('akhir'));
        $query = Null;
        $sql = "SELECT m.tahun||'.'||lpad(m.kode, 2, '0')||'.'||lpad(m.no_urut::text,6,'0')  no_kirim, 
                p.nama ppat,  m.pengirim,  m.tgl_terima,  d.kd_propinsi||'.'||d.kd_dati2||'.'||
                d.kd_kecamatan||'.'||d.kd_kelurahan||'.'||d.kd_blok||'.'||d.no_urut||'.'||d.kd_jns_op nop,
                d.thn_pajak_sppt, d.nominal, s.tahun||'.'||lpad(s.kode,2,'0')||'.'||lpad(s.no_sspd::text,6,'0') no_bphtb
            FROM 
              bphtb.bphtb_berkas_in m, 
              bphtb.bphtb_berkas_in_det d, 
              bphtb.bphtb_ppat p, 
              bphtb.bphtb_sspd s
            WHERE 
              m.id = d.berkas_in_id AND
              m.ppat_id = p.id AND
              d.sspd_id = s.id AND
              TO_CHAR(m.tgl_terima,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'";   
        
        $query = $this->db->query($sql)->result_array();
        
        if($query) {
            $ret = $query; 
            $this->response($ret, 200); // 200 being the HTTP response code
        } else {
           $this->response([
                    'status' => FALSE,
                    'message' => 'Data Not Found'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }    
    public function keluar_get(){
        if(!$this->get('awal')||!$this->get('akhir'))
           $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid Parameter'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
                
        $awal = preg_replace("/[^0-9]/","",$this->get('awal'));
        $akhir = preg_replace("/[^0-9]/","",$this->get('akhir'));
        $query = Null;
        $sql = "SELECT m.tahun||lpad(m.kode,2,'0')||lpad(m.no_urut::text,6,'0') no_berkas,
                    m.tgl_keluar, p.nama, m.penerima, count(*) jumlah, notes 
                FROM 
                  bphtb.bphtb_ppat p, 
                  bphtb.bphtb_berkas_out m, 
                  bphtb.bphtb_berkas_out_det d
                WHERE 
                  m.ppat_id = p.id AND
                  m.id = d.berkas_out_id AND
                  TO_CHAR(m.tgl_keluar,'YYYYMMDD') BETWEEN '$awal' AND '$akhir'   
                GROUP BY 1,2,3,4,6";
        
        $query = $this->db->query($sql)->result_array();
        
        if($query) {
            $ret = $query; 
            $this->response($ret, 200); // 200 being the HTTP response code
        } else {
           $this->response([
                    'status' => FALSE,
                    'message' => 'Data Not Found'
                  ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }    
}
