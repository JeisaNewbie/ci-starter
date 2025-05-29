<?php
class Comment_model extends My_Model
{
    public function __construct()
    {
        parent::__construct();
        // 댓글 삭제시 utils 라이브러리 추가
    }

    public function get_comment($id, $num, $page)
    {
        $offset = $num * ($page - 1);
        $from_to = array($id, $offset, (int)$num);

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

    public function set_comment($id)
    {
        $user_id = $this->session->userdata('id');

        $data = [
            'user_id' => $user_id,
            'board_ns_id' => $id,
            'comment' => $this->input->post('comment'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 'ACTIVE'
        ];

        $this->db->insert(TABLE_COMMENT, $data);
    }
}
