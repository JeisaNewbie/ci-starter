<?php

class MY_Controller extends CI_Controller
{
    # Parameter reference
    public $params = array();

    public $cookies = array();

    public function __construct()
    {
        parent::__construct();
        # Parameter
        $this->params = $this->getParams();
        $this->cookies = $this->getCookies();
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->helper('string');
    }

    public function get_pages($qs, $query_parameters)
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

    public function get_qs()
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

    private function getParams()
    {

        $aParams = array_merge($this->doGet(), $this->doPost());

        //$this->sql_injection_filter($aParams);

        return $aParams;
    }


    private function getCookies()
    {

        return $this->doCookie();
    }


    private function doGet()
    {
        $aGetData = $this->input->get(NULL, TRUE);
        return (empty($aGetData)) ? array() : $aGetData;
    }

    private function doPost()
    {
        $aPostData = $this->input->post(NULL, TRUE);
        return (empty($aPostData)) ? array() : $aPostData;
    }

    private function doCookie()
    {
        $aCookieData = $this->input->cookie(NULL, TRUE);

        return (empty($aCookieData)) ? array() : $aCookieData;
    }

    public function js($file, $v = '')
    {
        if (is_array($file)) {
            foreach ($file as $iKey => $sValue) {
                $this->optimizer->setJs($sValue, $v);
            }
        } else {
            $this->optimizer->setJs($file, $v);
        }
    }

    public function externaljs($file)
    {
        if (is_array($file)) {
            foreach ($file as $iKey => $sValue) {
                $this->optimizer->setExternalJs($sValue);
            }
        } else {
            $this->optimizer->setExternalJs($file);
        }
    }

    public function css($file, $v = '')
    {
        if (is_array($file)) {
            foreach ($file as $iKey => $sValue) {
                $this->optimizer->setCss($sValue, $v);
            }
        } else {
            $this->optimizer->setCss($file, $v);
        }
    }

    /**
     *  변수 셋팅
     */
    public function setVars($arr = array())
    {
        foreach ($arr as $val) {
            $aVars;
        }

        $this->load->vars($aVars);
    }

    /**
     *  공통 전역 변수 셋팅
     */
    public function setCommonVars()
    {
        $aVars = array();

        $aVars['test'] = array("test1" => "test1");

        $this->load->vars($aVars);
    }
}
