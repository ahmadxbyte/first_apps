<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ajax extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (empty($this->session->userdata("email"))) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Authentication required.']);
            exit();
        }
    }

    public function get_menu($id_menu)
    {
        header('Content-Type: application/json');
        $kode_user = $this->session->userdata('kode_user');

        $has_access = $this->db->query("
            SELECT 1 
            FROM akses_menu 
            WHERE id_menu = ? AND kode_role IN (
                SELECT kode_role FROM user WHERE kode_user = ?
            )
        ", [$id_menu, $kode_user])->num_rows() > 0;

        if (!$has_access) {
            echo json_encode(['error' => 'Access Denied.']);
            return;
        }

        $menu_item = $this->M_global->getData('m_menu', ['id' => $id_menu]);

        if (!$menu_item) {
            echo json_encode(['error' => 'Menu not found.']);
            return;
        }

        $sub_menu = $this->M_global->getDataResult('sub_menu', ['id_menu' => $id_menu]);
        $menu_data = [
            'id' => $menu_item->id,
            'nama' => $menu_item->nama,
            'url' => $menu_item->url,
            'sub_menu' => []
        ];

        if (!empty($sub_menu)) {
            foreach ($sub_menu as $sm) {
                $sub_menu2 = $this->M_global->getDataResult('sub_menu2', ['id_submenu' => $sm->id]);
                $sm_data = [
                    'id' => $sm->id,
                    'submenu' => $sm->submenu,
                    'url' => $menu_item->url . '/' . $sm->url_submenu,
                    'sub_menu2' => []
                ];
                if (!empty($sub_menu2)) {
                    foreach ($sub_menu2 as $sm2) {
                        $sm_data['sub_menu2'][] = [
                            'id' => $sm2->id,
                            'nama' => $sm2->nama,
                            'url' => $menu_item->url . '/' . $sm2->url_submenu2
                        ];
                    }
                }
                $menu_data['sub_menu'][] = $sm_data;
            }
        }

        echo json_encode($menu_data);
    }
}
