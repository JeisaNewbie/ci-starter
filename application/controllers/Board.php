<?php
class Board extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $qs = $this->get_qs();

        $query_parameters = [
            'like' => [
                'title' => $qs['search']
            ],
            'where' => NULL,
            'table' => 'board_ns'
        ];

        if ($qs['category'] !== 'ALL')
        {
            $query_parameters['where'] = ['category' => $qs['category']];
        }

        $data['board'] = $this->board_model_v2->get_board_index($qs);
        $data['pages'] = $this->get_pages($qs, $query_parameters);
        $data['search'] = $qs['search'];
        $data['num'] = $qs['num'];
        $data['current_page'] = $qs['page'];
        $data['pages']['total_data'] -= $data['num'] * ($data['current_page'] - 1);
        $data['category'] = $qs['category'];

        $this->load->view('board/index', $data);
    }

    public function view($id)
    {
        $data['board'] = $this->board_model_v2->get_board_view($id);

        if (empty($data['board']))
        {
            show_404();
        }

        $this->load->view('board/view', $data);
    }

    public function create()
    {
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('content', 'content', 'required');

        if ($this->form_validation->run() === FALSE)
        {
            $this->load->view('board/create');
        } 
        else 
        {
            $this->board_model_v2->set_board();
            redirect('board');
        }
    }

    public function set_content($id)
    {
        $response = $this->board_model_v2->set_content($id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function update_content($id) {
        $response = $this->board_model_v2->update_content($id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function delete($id)
    {
        $response = $this->board_model_v2->delete_board($id);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    // ToDo: 댓글 컨트롤러 분리
    public function get_comment($id, $num = 20, $page = 1)
    {
        $qs = $this->get_qs();

        $query_parameters = [
            'like' => NULL,
            'where' => [
                'board_ns_id' => $id
            ],
            'table' => 'comment'
        ];

        $data['comments'] = $this->board_model_v2->get_comment($id, $num, $page);
        $data['num'] = count($data['comments']);
        $data['pages'] = $this->get_pages($qs, $query_parameters);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function set_comment($id)
    {
        $this->board_model_v2->set_comment($id);
        echo "OK";
    }

    // 라이브러리로 분리
    private function get_pages($qs, $query_parameters)
    {
        $total_data_n_page = $this->get_page_num($qs['num'], $query_parameters);
        
        $page_num = $total_data_n_page['page'];
        $page_num = $page_num == 0 ? 1 : $page_num;

        $quotient = (int)($qs['page'] / 10);

        $mod = $qs['page'] % 10;

        $data['total_data'] = $total_data_n_page['total_data'];
        $data['start_page'] = $mod == 0 ? $quotient * 10 + 1 - 10 : $quotient * 10 + 1;
       
        $data['end_page'] = $mod == 0 ? $quotient * 10 : $quotient * 10 + 10;
        $data['end_page'] = $data['end_page'] > $page_num ? $page_num : $data['end_page'];

        $data['before'] = ($qs['page'] - 10 < 1 ? NULL : $qs['page'] - 10);
        $data['after'] = ($qs['page'] + 10 > $page_num ? $page_num : $qs['page'] + 10);

        $page_num_mod = $page_num % 10 == 0 ? 10 : $page_num % 10;
        $tmp_page_num = $page_num - $page_num_mod + 1;
        $data['after'] = $qs['page'] >= $tmp_page_num ? NULL : $data['after'];

        return $data;
    }

    public function get_page_num($num, $query_parameters)
    {
        if ($query_parameters['like'] !== NULL)
        {
            $this->db->like($query_parameters['like']);
        }

        if ($query_parameters['where'] !== NULL)
        {
            $this->db->where($query_parameters['where']);
        }

        $total_data = $this->db->count_all_results($query_parameters['table']);
        $mod = $total_data % $num;
        $page = (int)($total_data / $num);

        return [
            'total_data' => $total_data,
            'page' => (($page > 0) && ($mod > 0)) ? $page + 1 : $page
        ];

        // return (($page > 0) && ($mod > 0)) ? $page + 1 : $page;
    }
    
    private function get_qs()
    {
        $category = $this->input->get('category');
        $category = $category === NULL ? 'ALL' : $category;

        $search = $this->input->get('search');
        
        $num = $this->input->get('num');
        $num = $num === NULL ? 10 : $num;

        $page = $this->input->get('page');
        $page = $page === NULL ? 1 : $page;

        $qs = [
            'category' => $category,
            'search' => $search,
            'num' => $num,
            'page' => $page
        ];

        return $qs;
    }
}
