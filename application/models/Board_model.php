<?php
class Board_model extends My_Model {

    public function __construct()
    {
        $this->load->database();
    }

    public function get_total_page($num)
    {
        $data = $this->db->count_all('board');
        return (int)(($data % $num) > 0 ? ($data / $num) + 1 : ($data / $num));
    }

    public function get_board($id = NULL, $group_id = NULL, $num = NULL, $page = NULL)
    {
        /* 전체 글 조회 */
        if ($group_id === NULL)
        {
            $offset = $num * ($page - 1);
            $from_to = array($offset, (int)$num);
            // log_message('error', $num * ($page - 1));
            // log_message('error', $num * $page);
            $query = $this->db->query(
                'WITH recursive cte AS(
                SELECT id, group_id, title, parent_id, depth, CAST(id AS CHAR(100)) lvl, status, created_at
                FROM board
                WHERE parent_id IS NULL
                UNION ALL
                SELECT b.id, b.group_id, b.title, b.parent_id, b.depth, CONCAT(cte.lvl, ",", b.id) lvl, b.status, b.created_at
                FROM board b
                INNER JOIN cte
                ON b.parent_id = cte.id
                ) SELECT id, group_id, CONCAT(REPEAT("Re::", depth), title) AS title, parent_id, depth, lvl, status, created_at
                FROM cte
                ORDER BY group_id DESC, lvl
                LIMIT ?, ?;',$from_to);

            $data = $query->result_array();
            
            // ToDo: 메서드 추출
            foreach ($data as &$data_item)
            {
                if ($data_item['status'] === 'INACTIVE')
                {
                    $data_item['title'] = '삭제된 게시글입니다.';
                    $data_item['content'] = '삭제된 게시글입니다.';
                }
            }
            return $data;
        }

        /* 특정 글 조회, parent_id 와 동일한 id를 같이 조회 */
        $query = $this->db->query(
            'WITH recursive cte AS (
            SELECT  *
            FROM    board
            WHERE   id = ?
            UNION ALL
            SELECT	b.*
            FROM board b
            INNER JOIN cte ON b.id = cte.parent_id) SELECT * FROM cte ORDER BY depth;', $id);
        
        $data = $query->result_array();
    
        // ToDo: 메서드 추출
        foreach ($data as &$data_item)
        {
            if ($data_item['status'] === 'INACTIVE')
            {
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
                'status' => 'ACTIVE',
                'parent_id' => NULL,
                'group_order' => 0,
                'depth' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('board', $data);
        $insert_id = $this->db->insert_id();
        // 쿼리 빌더로 수정
        return $this->db->query('UPDATE board SET group_id = id WHERE id = '. $insert_id);
    }

    public function set_content($id, $group_id)
    {

        $attributes = array('id' => $id);

        $query = $this->db->get_where('board', $attributes);
        
        $row = $query->row_array();

        $data = array(
                'title' => $row['title'],
                'content' => $this->input->post('content'),
                'status' => 'AVTIVE',
                'parent_id' => $id,
                'group_id' => $row['group_id'],
                'group_order' => $row['group_order'] + 1, // db 조회 후 parent의 order + 1
                'depth' => $row['depth'] + 1, // db 조회 후 parent의 depth + 1
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('board', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function delete_board($id)
    {
        $query = $this->getSoftDeleteQuery('board', array('id' => $id));
        
        log_message('error', $this->db->query($query));
    }
}