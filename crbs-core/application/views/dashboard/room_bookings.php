<?php if ($room_bookings): ?>

	<div class="block b-50">

		<div class="box">

			<h3 style="margin: 0 0 16px 0"><?= lang('booking.in_my_rooms') ?></h3>
			<ul class="dash-booking-list">

				<?php
				foreach ($room_bookings as $booking) {

					$is_recurring = !empty($booking->repeat_id);
					$show_user = ($is_recurring)
						? has_permission(Permission::BK_RECUR_VIEW_OTHER_USERS, $booking->room->room_id)
						: has_permission(Permission::BK_SGL_VIEW_OTHER_USERS, $booking->room->room_id)
						;
					$show_notes = ($is_recurring)
						? has_permission(Permission::BK_RECUR_VIEW_OTHER_NOTES, $booking->room->room_id)
						: has_permission(Permission::BK_SGL_VIEW_OTHER_NOTES, $booking->room->room_id)
						;

					$date_str = date_output_long($booking->date);
					$time_str = date_output_time($booking->date);
					$period_name = html_escape($booking->period->name);
					$room_name = html_escape($booking->room->name);
					$user_name = !empty($booking->user->displayname)
						? html_escape($booking->user->displayname)
						: html_escape($booking->user->username);

					$time = "<span class='dash-booking-time'>({$time_str})</span>";

					$title_html = "<div class='dash-booking-title'>{$date_str}</div>";

					if ($show_user) {
						$user_html = "<span>{$user_name}</span>";
					} else {
						$user_html = "<em>User hidden</em>";
					}

					$room_url = "rooms/info/{$booking->room->room_id}";
					$room_link = anchor($room_url, $room_name, [
						'up-layer' => 'new drawer',
						'up-position' => 'left',
						'up-target' => '.room-info',
						'up-preload',
					]);
					$room_html = "<div class='dash-booking-subtitle'>{$period_name} {$time} &middot; {$room_link} &middot; {$user_html}</div>";

					$notes_html = $show_notes && !empty($booking->notes)
						? '<span class="dash-booking-notes">' . html_escape($booking->notes) . '</span>'
						: '';

					$link = img([
						'src' => asset_url('assets/images/ui/calendar.png'),
					]);
					$uri = 'bookings?date=%s&room=%d&room_group=%d&highlight=%d';
					$uri = sprintf($uri,
						$booking->date->format('Y-m-d'),
						$booking->room->room_id,
						$booking->room->room_group_id,
						$booking->booking_id
					);
					$anchor = anchor($uri, $link, ['style' => 'display:inline-block;vertical-align:top;margin: 0 8px 0 0']);

					echo "<li>{$anchor}<div style='display:inline-block'>{$title_html}{$room_html}{$notes_html}</div></li>";
				}
				?>
			</ul>

		</div>

	</div>

<?php endif; ?>
