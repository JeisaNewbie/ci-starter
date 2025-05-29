<?php
class Board extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('board_model');
    }

    // ToDo: 코드 정리
    /* 자유게시판 목록 페이지 */
    public function index()
    {
        $query_string = $this->get_qs();

        $query_parameters = [
            'like' => [
                'title' => $query_string['search']
            ],
            'where' => NULL,
            'table' => TABLE_BOARD
        ];

        if ($query_string['category'] != 'ALL')
        {
            $query_parameters['where'] = ['category' => $query_string['category']];
        }

        $data['board'] = $this->board_model->get_board_index($query_string);
        $data['pages'] = $this->get_pages($query_string, $query_parameters);
        $data['search'] = $query_string['search'];
        $data['num'] = $query_string['num'];
        $data['current_page'] = $query_string['page'];
        $data['pages']['total_data'] -= $data['num'] * ($data['current_page'] - 1);
        $data['category'] = $query_string['category'];

        $this->load->view('board/index', $data);
    }

    /* 게시글 상세 페이지 */
    public function view($board_id)
    {
        $data['board'] = $this->board_model->get_board_view($board_id);

        if (empty($data['board']))
        {
            show_404();
        }

        $this->load->view('board/view', $data);
    }

    /* 게시글 생성 */
    public function create_board()
    {
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('content', 'content', 'required');

        if ($this->form_validation->run() === FALSE)
        {
            $this->load->view('board/create_board');
        } 
        else 
        {
            $this->board_model->create_board();
            redirect('board');
        }
    }

    /* 답글 생성 */
    public function create_child_board($parent_id)
    {
        $response = $this->board_model->create_child_board($parent_id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /* 게시글 & 답글 수정 */
    public function update_board($board_id) {
        $response = $this->board_model->update_board($board_id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /* 게시글 & 답글 삭제 */
    public function delete_board($board_id)
    {
        $response = $this->board_model->delete_board($board_id);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
