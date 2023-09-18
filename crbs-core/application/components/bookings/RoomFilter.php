<?php

namespace app\components\bookings;

defined('BASEPATH') OR exit('No direct script access allowed');


// use \DateTime;
// use \DateInterval;
// use \DatePeriod;

use app\components\Calendar;


class RoomFilter
{


	// CI instance
	private $CI;


	// Context instance
	private $context;

	private $params = [];
	private $room_groups = [];
	private $rooms = [];
	private $rooms_grouped = [];
	private $current_room = null;
	private $current_date = null;


	public function __construct(Context $context)
	{
		$this->CI =& get_instance();

		$this->CI->load->model([
			'room_groups_model',
			'rooms_model',
		]);

		$this->context = $context;

		$this->params = $this->context->get_query_params();
		$this->room_groups = $this->get_room_groups();
		$this->rooms = $this->get_rooms();

		foreach ($this->rooms as $room) {
			$group_id = $room->room_group_id ?? 'ungrouped';
			$this->rooms_grouped[ $group_id ][ $room->room_id ] = $room;
		}

		$this->current_room = $this->params['room'] ?? null;
		$this->current_date = $this->params['date'] ?? null;
	}


	public function render()
	{
		$menu_html = $this->render_menu();
		return "<div class='block-group room-groups'>{$menu_html}</div>";
	}


	private function render_menu()
	{
		$rooms_html = '';

		foreach ($this->room_groups as $group) {
			$rooms = $this->rooms_grouped[$group->room_group_id] ?? [];
			$rooms_html .= $this->render_group($group->name, $group->description, $rooms);
		}

		// if (isset($this->rooms_grouped['ungrouped'])) {
		// 	$rooms = $this->rooms_grouped['ungrouped'];
		// 	$rooms_html .= $this->render_group("Ungrouped", null, $rooms);
		// }

		return $rooms_html;
	}


	private function render_group($title, $description = null, $rooms = [])
	{
		$title_html = "<h5 style='margin:0'>" . html_escape($title) . "</h5>";

		$desc_html = '';
		if ( ! empty($description)) {
			$desc = html_escape($description);
			$desc_html = "<p style='margin:4px 0 0 0'><small class='hint'>{$desc}</small></p>";
		}

		$items = [];
		foreach ($rooms as $room) {
			$items[] = $this->render_room($room);
		}
		$items_html = implode("\n", $items);

		$list_html = "<ul class='room-list' style='margin-top:16px'>{$items_html}</ul>";

		$block = "<div class='block b-25'>{$title_html}{$desc_html}{$list_html}</div>";

		return $block;
	}


	private function render_room($room)
	{
		$room_name = html_escape($room->name);

		$link_content = "<span class='room-name'>{$room_name}</span>";
		$classes = '';
		if ($room->room_id == $this->current_room) {
			$classes = 'is-selected';
		}

		$meta = [];
		if ($room->location) {
			$meta[] = html_escape($room->location);
		}
		if ($room->user_id) {
			$owner = empty($room->owner->displayname)
				? $room->owner->username
				: $room->owner->displayname;
			$meta[] = html_escape($owner);
		}
		$link_meta = '';
		if ( ! empty($meta)) {
			$meta_html = implode(" &bull; ", $meta);
			$link_meta = "<p><small class='hint'>{$meta_html}</small></p>";
		}

		$query = [
			'room' => $room->room_id,
			'date' => $this->current_date,
		];
		$url = $this->context->base_uri . '?' . http_build_query($query);

		$link = anchor($url, $link_content . $link_meta, [
			'class' => $classes,
			'attrs' => 'up-follow up-preload',
		]);

		$item = "<li>{$link}</li>";
		return $item;
	}


	private function get_room_groups()
	{
		return $this->CI->room_groups_model->get_bookable($this->context->user->user_id);
	}


	private function get_rooms()
	{
		return $this->CI->rooms_model->get_bookable_rooms([
			'user_id' => $this->context->user->user_id,
		]);
	}


}
