<?php echo form_open('account/login', array('id' => 'login_form'), array('uri' => $this->session->userdata('uri'))) ?>

		<div class="grid_4">&nbsp;</div>

		<div class="grid_4">

			<h3 class="add-bottom"><?php echo lang('login') ?></h3>

			<div class="login-form">

				<label for="username"><?php echo lang('username') ?></label>
				<?php echo form_input(array(
					'name' => 'username',
					'id' => 'username',
					'size' => 30,
					'maxlength' => 104,
					'tabindex' => tab_index(),
					'value' => set_value('username', ''),
				)) ?>

				<label for="password"><?php echo lang('password') ?></label>
				<?php echo form_password(array(
					'name' => 'password',
					'id' => 'password',
					'size' => 30,
					'maxlength' => 104,
					'tabindex' => tab_index(),
				)) ?>

				<br>
				
				<?php
				echo form_button(array(
					'type' => 'submit',
					'class' => 'blue',
					'text' => lang('login'),
					'tab_index' => tab_index()
				));
				?>

			</div>
			
		</div>

		<div class="one-third column">&nbsp;</div>

	</div>

</form>