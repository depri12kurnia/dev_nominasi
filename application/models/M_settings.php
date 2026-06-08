<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_settings extends CI_Model
{

    public function get_all_settings()
    {
        $query = $this->db->get('settings');
        return $query->result();
    }

    public function insert_setting($data)
    {
        return $this->db->insert('settings', $data);
    }

    public function get_setting_by_id($id)
    {
        $query = $this->db->get_where('settings', array('id' => $id));
        return $query->row();
    }

    public function update_setting($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('settings', $data);
    }

    public function delete_setting($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('settings');
    }
}
