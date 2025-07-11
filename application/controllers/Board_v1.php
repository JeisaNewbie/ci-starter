<?php
class Board extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('board_model_v1');
        $this->load->helper('html');
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('string');
    }

    public function index($num = 10, $page = 1)
    {
        // 재귀 쿼리를 통한 계층 목록 조회
        $data['board'] = $this->board_model_v1->get_board_index($num, $page);

        $data['page_num'] = $this->board_model_v1->get_total_page($num);
        $data['num'] = $num;
        $this->load->view('board/index', $data);
    }

    public function view($id = NULL)
    {
        $data['board'] = $this->board_model_v1->get_board_view($id);
        
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
            $this->board_model_v1->set_board();
            redirect('board');
        }
    }

    public function set_comment($id = NULL, $group_id = NULL)
    {
        $insert_id = $this->board_model_v1->set_content($id, $group_id);
        redirect('board/view/' .  $insert_id);
    }

    public function delete($id = NULL)
    {
        /* TODO: 유저 권한 확인 */
        $this->board_model_v1->delete_board($id);
        redirect('board');
    }
}