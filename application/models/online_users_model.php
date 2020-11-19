<?php
class online_users_model extends CI_model {

function __construct()
{
    parent::__construct();

}

function get_all_session_data()
{

    $query=$this->db->select('user_data')->get('ci_sessions');

     return $query;


}



} 

?>