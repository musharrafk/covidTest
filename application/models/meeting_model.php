<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	class meeting_model extends CI_Model {

		function getMeetingRooms($location,$date)
		{
			echo "fdsdf";
			die();
			$this->db->select('*');
			$this->db->from('meeting_rooms mr');
			$this->db->join('booking_rooms br','br.meeting_room_id = mr.id');
			$this->db->where('mr.location',$location);
			$query = $this->db->get();
			return $query->result_array();
		}
		function getMeetingRoomsddd($location,$date)
		{
			echo "fdsdf";
			die();
			$this->db->select('*');
			$this->db->from('meeting_rooms mr');
			$this->db->join('booking_rooms br', '(br.meeting_room_id = mr.id AND br.status = "A" AND br.booking_date = '.$date.')');
			$this->db->where('mr.location',$location);
			$query = $this->db->get();
			return $query->result_array();
		}

	}
	?>