<?php

class Invoice_Model extends CI_Model {

    var $title   = '';
    var $content = '';
    var $date    = '';
    var $tablenm = 'pad_invoice';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_by_periode()
    {
        $awal  = $_GET['awal'];
        $akhir = $_GET['akhir'];
        
        $query = $this->db->get('pad_invoice', 10);
        return $query->result();
    }

    function get_last_ten_entries()
    {
        $query = $this->db->get('pad_invoice', 10);
        return $query->result();
    }

    function insert_entry()
    {
        $this->title   = $_POST['title']; // please read the below note
        $this->content = $_POST['content'];
        $this->date    = time();

        $this->db->insert('pad_invoice', $this);
    }

    function update_entry()
    {
        $this->title   = $_POST['title'];
        $this->content = $_POST['content'];
        $this->date    = time();

        $this->db->update('pad_invoice', $this, array('id' => $_POST['id']));
    }

}
?>