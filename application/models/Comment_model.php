<?php
class Comment_model extends My_Model
{
    public function __construct()
    {
        parent::__construct();
        // 댓글 삭제시 utils 라이브러리 추가
    }

    public function get_comment($board_id, $num, $page)
    {
        $offset = $num * ($page - 1);
        $from_to = array($board_id, $offset, (int)$num);

        $query = $this->db->query(
            'SELECT * FROM comment
            WHERE board_ns_id = ?
            ORDER BY created_at DESC
            LIMIT ?, ?;',
            $from_to
        );

        $data = $query->result_array();

        return $data;
    }

    public function create_comment($board_id)
    {
        $user_id = $this->session->userdata('id');

        $data = [
            'user_id' => $user_id,
            'board_ns_id' => $board_id,
            'comment' => $this->input->post('comment'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 'ACTIVE'
        ];

        $this->db->insert(TABLE_COMMENT, $data);
        // $insert_id = $this->db->insert_id();
        // $this->db->where('id', $insert_id);
        // $query = $this->db->get(TABLE_COMMENT);
        // $inserted_data = $query->row_array();   
        // return $inserted_data;
    }
}
