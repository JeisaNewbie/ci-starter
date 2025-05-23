<?php
class Board_model_v2 extends My_Model
{
    const TABLE_NAME = 'board_ns';
    public function __construct()
    {
        $this->load->database();
    }

    public function get_total_page($num)
    {
        $data = $this->db->count_all('board_ns');
        return (int)(($data % $num) > 0 ? ($data / $num) + 1 : ($data / $num));
    }

    public function get_board_index($num = NULL, $page = NULL)
    {
        /* 전체 글 조회 */
        $offset = $num * ($page - 1);
        $from_to = array($offset, (int)$num);

        $query = $this->db->query(
            'SELECT id, group_id, CONCAT(REPEAT("Re::", depth), title) AS title, parent_id, depth, status, created_at
            FROM board_ns
            ORDER BY group_id DESC, l_value ASC
            LIMIT ?, ?;', $from_to
        );

        $data = $query->result_array();

        // ToDo: 메서드 추출
        foreach ($data as &$data_item) {
            if ($data_item['status'] === 'INACTIVE') {
                $data_item['title'] = '삭제된 게시글입니다.';
                $data_item['content'] = '삭제된 게시글입니다.';
            }
        }
        return $data;
    }

    public function get_board_view($id = NULL)
    {
        /* 특정 글 조회, parent_id 와 동일한 id를 같이 조회 */
        $query = $this->db->query(
            'WITH recursive cte AS (
            SELECT  *
            FROM    board_ns
            WHERE   id = ?
            UNION ALL
            SELECT	b.*
            FROM board_ns b
            INNER JOIN cte ON b.id = cte.parent_id) SELECT * FROM cte ORDER BY depth;',
            $id
        );

        $data = $query->result_array();

        // ToDo: 메서드 추출
        foreach ($data as &$data_item) {
            if ($data_item['status'] === 'INACTIVE') {
                $data_item['title'] = '삭제된 게시글입니다.';
                $data_item['content'] = '삭제된 게시글입니다.';
            }
        }
        return $data;
    }

    public function set_board()
    {
        $data = array(
            'title' => $this->input->post('title'),
            'content' => $this->input->post('content'),
            'parent_id' => NULL,
            'depth' => 0,
            'l_value' => 1,
            'r_value' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 'ACTIVE'
        );

        $this->db->insert('board_ns', $data);
        $insert_id = $this->db->insert_id();
        // 쿼리 빌더로 수정
        return $this->db->query('UPDATE board_ns SET group_id = id WHERE id = ?', $insert_id);
    }

    // ToDo: 메서드 명 변경 필요
    public function set_content($id)
    {

        $attributes = array('id' => $id);

        $query = $this->db->get_where('board_ns', $attributes);

        $row = $query->row_array();

        $new_left = $row['r_value'];
        $new_right = $new_left + 1;
        
        $data = array(
            'title' => $row['title'],
            'content' => $this->input->post('content'),
            'parent_id' => $id,
            'group_id' => $row['group_id'],
            'depth' => $row['depth'] + 1,
            'l_value' => $new_left,
            'r_value' => $new_right,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 'AVTIVE'
        );

        $where_attr_left = array('l_value >=' => $new_right, 'group_id' => $row['group_id']);
        $where_attr_right = array('r_value >=' => $new_left, 'group_id' => $row['group_id']);

        $this->db->set('l_value', 'l_value + 2', FALSE);
        $this->db->where($where_attr_left);
        $this->db->update('board_ns');

        $this->db->set('r_value', 'r_value + 2', FALSE);
        $this->db->where($where_attr_right);
        $this->db->update('board_ns');

        $this->db->insert('board_ns', $data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    public function delete_board($id)
    {
        $query = $this->getSoftDeleteQuery('board_ns', array('id' => $id));

        log_message('error', $this->db->query($query));
    }
}
