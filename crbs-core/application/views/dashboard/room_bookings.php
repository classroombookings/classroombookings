<?php
$date_format = setting('date_format_long');
$time_format = setting('time_format_period');
?>

<?php if ($room_bookings): ?>

	<div class="block b-50">

		<div class="box">

			<h3 style="margin: 0 0 16px 0">Bookings in my rooms</h3>
			<ul class="dash-booking-list">

				<?php
				foreach ($room_bookings as $booking) {

					$date_str = $booking->date->format($date_format);
					$time_str = $booking->date->format($time_format);
					$period_name = html_escape($booking->period->name);
					$room_name = html_escape($booking->room->name);
					$user_name = !empty($booking->user->displayname)
						? html_escape($booking->user->displayname)
						: html_escape($booking->user->username);

					$time = "<span class='dash-booking-time'>({$time_str})</span>";

					$title_html = "<div class='dash-booking-title'>{$date_str}</div>";

					$user_html = "<span>{$user_name}</span>";

					$room_url = "rooms/info/{$booking->room->room_id}";
					$room_link = anchor($room_url, $room_name, [
						'up-layer' => 'new drawer',
						'up-position' => 'left',
						'up-target' => '.room-info',
						'up-preload',
					]);
					$room_html = "<div class='dash-booking-subtitle'>{$period_name} {$time} &middot; {$room_link} &middot; {$user_html}</div>";

					$notes_html = !empty($booking->notes)
						? '<span class="dash-booking-notes">' . html_escape($booking->notes) . '</span>'
						: '';

					echo "<li>{$title_html}{$room_html}{$notes_html}</li>";
				}
				?>
			</ul>

		</div>

	</div>

<?php endif; ?>
