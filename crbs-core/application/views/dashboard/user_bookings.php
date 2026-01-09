<?php if ($user_bookings): ?>

	<div class="block b-50">

		<div class="box">

			<h3 style="margin: 0 0 16px 0"><?= lang('booking.active_bookings') ?></h3>
			<ul class="dash-booking-list">

				<?php
				foreach ($user_bookings as $booking) {

					$date_str = date_output_long($booking->date);
					$time_str = date_output_time($booking->date);
					$period_name = html_escape($booking->period->name);
					$room_name = html_escape($booking->room->name);

					$time = "<span class='dash-booking-time'>({$time_str})</span>";

					$title_html = "<div class='dash-booking-title'>{$date_str}</div>";
					$room_url = "rooms/info/{$booking->room->room_id}";
					$room_link = anchor($room_url, $room_name, [
						'up-layer' => 'new drawer',
						'up-position' => 'left',
						'up-target' => '.room-info',
						'up-preload',
					]);
					$room_html = "<div class='dash-booking-subtitle'>{$period_name} {$time} &middot; {$room_link}</div>";

					$notes_html = !empty($booking->notes)
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
