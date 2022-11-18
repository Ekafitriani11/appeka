<?php
defined('BASEPATH') or exit('no direct script access allowed');
class model_menu extends CI_Model
{
    public function getSubMenu()
    {
        $query = "SELECT `USER_SUB_MENU`.*,`user_menu`.`menu`
                            FROM `user_sub_menu` JOIN `user_menu`
                                ON `user_sub_menu`.`menu_id`=`user_menu`.`id`
            ";
         return $this->db->query($query)->result_array();
    }
}