<?php
class Board_v2 extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('board_model_v2');
        $this->load->model('test_model');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('string');
    }

    public function test($method = NULL)
    {
        if ($method == 'delete')
        {
            $this->test_model->do_delete();
        }
        else if ($method == 'create')
        {
            $this->test_model->do_test();
        }

        redirect('/board_v2');
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

        $this->load->view('board_v2/index', $data);
    }

    public function view($id)
    {
        $data['board'] = $this->board_model_v2->get_board_view($id);

        if (empty($data['board']))
        {
            show_404();
        }

        $this->load->view('board_v2/view', $data);
    }

    public function create_account()
    {
        $result = $this->board_model_v2->create_account();

        if ($result === TRUE)
        {
            echo "OK";
        }
        else
        {
            echo "FAIL";
        }
    }

    public function login()
    {
        $this->form_validation->set_rules('id', 'ID', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');

        if ($this->form_validation->run() === FALSE)
        {
            $this->load->view('board_v2/login');
        } 
        else
        {
            $login = $this->board_model_v2->login();
            if ($login === NULL)
            {
                echo "FAIL";
            }
            else
            {
                $this->session->set_userdata($login);
                echo "OK";
            }
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('id');
        redirect('/board_v2');
    }

    public function create()
    {
        $this->form_validation->set_rules('id', 'Id', 'callback_id_check');
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('content', 'content', 'required');

        if ($this->form_validation->run() === FALSE)
        {
            $this->load->view('board_v2/create');
        } 
        else 
        {
            $this->board_model_v2->set_board();

            redirect('board_v2');
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

    public function delete($id)
    {
        $response = $this->board_model_v2->delete_board($id);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

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

    public function id_check()
    {
        if ($this->session->userdata('id') !== NULL) {
            return TRUE;
        }
        $this->form_validation->set_message('id_check', '로그인이 필요합니다.');
        return FALSE;
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
