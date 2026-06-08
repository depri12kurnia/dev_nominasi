<?php
class M_log_user extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    // Fungsi untuk menyimpan log aktivitas pengguna
    public function save_log($user_id, $action)
    {
        $data = array(
            'user_id' => $user_id,
            'action' => $action,
            'timestamp' => date('Y-m-d H:i:s')
        );

        $this->db->insert('user_logs', $data);
    }

    // Fungsi untuk mengambil log aktivitas pengguna
    var $table = 'user_logs';
    var $column_order = array('user_logs.id', 'users.email', 'user_logs.action', 'user_logs.timestamp');
    var $column_search = array('user_logs.id', 'users.email', 'user_logs.action', 'user_logs.timestamp');
    var $order = array('user_logs.id' => 'asc');

    private function _get_datatables_query()
    {
        $this->db->select('user_logs.id, users.email as user_id, user_logs.action, user_logs.timestamp');
        $this->db->from($this->table);
        $this->db->join('users', 'users.id = user_logs.user_id');
        $this->db->order_by('user_logs.timestamp', 'desc');
        $i = 0;

        foreach ($this->column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) {
                    $this->db->group_end();
                }
                $i++;
            }
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
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function delete_all_activity()
    {
        $this->db->empty_table('user_logs'); // Menghapus semua data di tabel log_user
        return $this->db->affected_rows(); // Mengembalikan jumlah baris yang dihapus
    }
}
