<?php

class MY_Model extends CI_Model {

    protected $_table_name = '';
    protected $_primary_key = 'id';
    protected $_primary_filter = 'intval';
    protected $_order_by = 'id desc';
    public $rules = array();
    protected $_timestamps = FALSE;

    function __construct() {
        parent::__construct();
    }

    public function array_from_post($fields) {
        $data = array();
        foreach ($fields as $field) {
            $data[$field] = $this->input->post($field);
        }
        return $data;
    }

    public function get($id = NULL, $single = FALSE, $limit = NULL) {

        if ($id != NULL) {
            $filter = $this->_primary_filter;
            $id = $filter($id);
            $this->db->where($this->_primary_key, $id);
            $method = 'row';
        } elseif ($single == TRUE) {
            $method = 'row';
        } else {
            $method = 'result';
            if ($limit != NULL) {
                $this->db->limit($limit);
            }
        }

        if (!empty($this->db->_order_by)) {
            $this->db->order_by($this->_order_by);
        }
        //echo $this->db->last_query();
        return $this->db->get($this->_table_name)->$method();
        /* $result = $this->db->get($this->_table_name)->$method();
          echo $this->db->last_query();
          return $result; */
    }

    public function get_by($where, $single = FALSE, $limit = NULL) {
        $this->db->where($where);
        //echo $this->db->last_query();
        return $this->get(NULL, $single, $limit);
    }

    public function save($data) {
        // Insert
        $this->db->set($data);
        $this->db->insert($this->_table_name);
        $id = $this->db->insert_id();
        return $id;
    }

    public function update($data, $id) {
        
        // Update
        if (is_array($id)) {
            $this->db->set($data);
            $this->db->where($id);
        } else {
            $filter = $this->_primary_filter;
            $id = $filter($id);
            $this->db->set($data);
            $this->db->where($this->_primary_key, $id);
        }
        return $this->db->update($this->_table_name);
    }

    public function delete($id) {
        $filter = $this->_primary_filter;
        $id = $filter($id);

        if (!$id) {
            return FALSE;
        }
        $this->db->where($this->_primary_key, $id);
        $this->db->limit(1);
        return $this->db->delete($this->_table_name);
    }

    public function get_updated_expirydate($ExpiryDate) {
        $day = (int) date("N", strtotime($ExpiryDate));
        if ($day == 7) {
            return date('Y-m-d H:i:s', strtotime($ExpiryDate . ' + 1 days'));
        } else {
            return $ExpiryDate;
        }
    }

}
