<?php
class Board_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    public function get_board($id = FALSE)
    {
        if ($id === FALSE)
        {
                $query = $this->db->get('board');
                return $query->result_array();
        }

        $query = $this->db->get_where('board', array('id' => $id));
        return $query->row_array();
    }

    public function set_board()
    {   
        $data = array(
                'title' => $this->input->post('title'),
                'content' => $this->input->post('content'),
                // 'status' => "active",
                'parent_id' => null,
                'group_id' => $this->db->count_all('board') + 1,
                'group_order' => 0, // db 조회 후 최대값 + 1
                'depth' => 0, // db 조회 후 최대값 + 1
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
        );

        return $this->db->insert('board', $data);
    }

    public function set_content($group_id, $parent_id)
    {

        $query = $this->db->get_where('board', array('group_id' => $group_id, 'parent_id' => $parent_id));
        
        if ($query->num_rows() > 0) 
        {
            $parent_id = $query->row()->id;
        } else {
            $parent_id = null;
        }
        

        $data = array(
                'title' => $this->input->post('title'),
                'content' => $this->input->post('content'),
                // 'status' => "active",
                'parent_id' => $parent_id,
                'group_order' => 0, // db 조회 후 최대값 + 1
                'depth' => 0, // db 조회 후 최대값 + 1
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
        );

        return $this->db->insert('board', $data);
    }
}