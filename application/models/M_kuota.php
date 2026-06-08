<?php
class M_kuota extends CI_Model
{
    var $table = 'kuota_prodi';
    var $column_order = array('id', 'nama_prodi', 'kuota_utama', 'persentase_cad', 'kuota_cadangan');
    var $column_search = array('id', 'nama_prodi', 'kuota_utama', 'persentase_cad', 'kuota_cadangan');
    var $order = array('id' => 'asc');

    private function _get_datatables_query()
    {
        $this->db->select('*');
        $this->db->from($this->table);

        $i = 0;

        foreach ($this->column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start(); // PERBAIKAN: dari kuota_start ke group_start
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) {
                    $this->db->group_end(); // PERBAIKAN: dari kuota_end ke group_end
                }
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_by_id($id)
    {
        // PERBAIKAN: Memilih kolom yang benar sesuai struktur tabel kuota_prodi
        $this->db->select('id, nama_prodi, kuota_utama, persentase_cad, kuota_cadangan');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function insert_kuota($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function get_kuota($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row_array();
    }

    public function update_kuota($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_kuota($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
}
