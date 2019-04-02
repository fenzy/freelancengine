<?php
/**
 * Template Name: Register Page Template
*/
global $post;
	get_header();
	if(!isset($_REQUEST['role'])){
?>
<div class="fre-page-wrapper">
	<div class="fre-page-section">
		<div class="container">
			<div class="fre-authen-wrapper">
				<div class="fre-register-default">
					<h2><?php _e('Sign Up Free Account', ET_DOMAIN)?></h2>
					<div class="fre-register-wrap">
						<div class="row">
							<div class="col-sm-6">
								<div class="register-employer">
									<h3><?php _e('Employer', ET_DOMAIN);?></h3>
									<p><?php _e('Post project, find freelancers and hire favorite to work.', ET_DOMAIN);?></p>
									<a class="fre-small-btn primary-bg-color" href="<?php echo  et_get_page_link( 'register',array('role' => EMPLOYER) );?>"><?php _e('Sign Up', ET_DOMAIN);?></a>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="register-freelancer">
									<h3><?php _e('Freelancer', ET_DOMAIN);?></h3>
									<p><?php _e('Create professional profile and find freelance jobs to work.', ET_DOMAIN);?></p>
									<a class="fre-small-btn primary-bg-color" href="<?php echo  et_get_page_link( 'register',array('role' => FREELANCER) );?>"><?php _e('Sign Up', ET_DOMAIN);?></a>
								</div>
							</div>
						</div>
					</div>
					<div class="fre-authen-footer">
						<?php
			                if(fre_check_register() && function_exists('ae_render_social_button')){
			                    $before_string = __("You can use social account to login", ET_DOMAIN);
			                    ae_render_social_button( array(), array(), $before_string );
			                }
			            ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	}else{
		$role = $_REQUEST['role'];
		$re_url = '';
		if( isset($_GET['ae_redirect_url']) ){
			$re_url = $_GET['ae_redirect_url'];
		}
?>
	<div class="fre-page-wrapper">
		<div class="fre-page-section">
			<div class="container">
				<div class="fre-authen-wrapper">
					<div class="fre-authen-register">
						<?php if($role == 'employer'){ ?>
								<h2><?php _e('Sign Up Employer Account', ET_DOMAIN);?></h2>
						<?php }else{ ?>
								<h2><?php _e('Sign Up Freelancer Account', ET_DOMAIN);?></h2>
						<?php } ?>
						<form role="form" id="signup_form">
							<input type="hidden" name="ae_redirect_url"  value="<?php echo $re_url ?>" />
							<input type="hidden" name="role" id="role" value="<?php echo $role;?>" />
							<div class="fre-input-field">
								<input type="text" name="first_name" id="first_name" placeholder="<?php _e('First Name', ET_DOMAIN);?>">
							</div>
							<div class="fre-input-field">
								<input type="text" name="last_name" id="last_name" placeholder="<?php _e('Last Name', ET_DOMAIN);?>">
							</div>
							<div class="fre-input-field">
								<input type="text" name="user_email" id="user_email" placeholder="<?php _e('Email', ET_DOMAIN);?>">
							</div>

							<?php if($role == 'freelancer'){ ?>

							<div class="fre-input-field">
								<input type="text" name="user_phone_number" id="user_phone_number" placeholder="<?php _e('Phone Number', ET_DOMAIN);?>">
							</div>

							<div class="fre-input-field">
								<input type="text" name="user_address" id="user_address" placeholder="<?php _e('Address', ET_DOMAIN);?>">
							</div>

							<div class="fre-input-field">
								<label for="user_age16"><?php _e('Are you 16 or older?', ET_DOMAIN);?></label>
								<select name="user_age16" id="user_age16">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</div>

							<div class="fre-input-field">
								<label for="user_student"><?php _e('Are you a student?', ET_DOMAIN);?></label>
								<select name="user_student" id="user_student">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</div>

							<div class="fre-input-field">
								<input type="text" name="user_education" id="user_education" placeholder="<?php _e('Education', ET_DOMAIN);?>">
							</div>

							<div class="fre-input-field">
								<input type="text" name="user_skill" id="user_skill" placeholder="<?php _e('Field of work', ET_DOMAIN);?>">
							</div>

							<label><?php _e('Experience (please use the following format: number of years, company, position)', ET_DOMAIN);?></label>
							
							<div class="fre-input-field">
								<table style="width: 100%;">
									<tr>
										<td>a.</td>
										<td><input type="text" name="user_company_a" id="user_company_a"></td>
									</tr>
									<tr>
										<td>b.</td>
										<td><input type="text" name="user_company_b" id="user_company_b"></td>
									</tr>
									<tr>
										<td>c.</td>
										<td><input type="text" name="user_company_c" id="user_company_c"></td>
									</tr>
								</table>
							</div>

							<div class="fre-input-field">
								<input type="text" name="user_hear" id="user_hear" placeholder="<?php _e('How did you hear about?', ET_DOMAIN);?>">
							</div>
							
							<?php } ?>
							
							<div class="fre-input-field">
								<input type="text" name="user_login" id="user_login" placeholder="<?php _e('Username', ET_DOMAIN);?>">
							</div>
							<div class="fre-input-field">
								<input type="password" name="user_pass" id="user_pass" placeholder="<?php _e('Password', ET_DOMAIN);?>">
							</div>
							<div class="fre-input-field">
								<input type="password" name="repeat_pass" id="repeat_pass" placeholder="<?php _e('Confirm Your Password', ET_DOMAIN);?>">
							</div>
							<?php ae_gg_recaptcha( $container = 'fre-input-field' );?>
							<div class="fre-input-field">
								<?php
									$tos = et_get_page_link('tos', array() ,false);
					                $url_tos = '<a href="'.et_get_page_link('tos').'" class="secondary-color" rel="noopener noreferrer" target="_Blank">'.__('Term of Use and Privacy policy', ET_DOMAIN).'</a>';
					                if($tos) {
					                	echo "<p>";
					                	echo '<label><input type ="checkbox" name="term" id="term" /> ';
					                	printf(__('I agree to the  %s', ET_DOMAIN), $url_tos );
					                	echo "</label></p>";
					                }
								?>

							</div>
							<div class="fre-input-field">
								<button class="fre-btn btn-submit primary-bg-color"><?php _e('Sign Up', ET_DOMAIN);?></button>
							</div>
						</form>

						<div class="fre-authen-footer">
							<p><?php _e('Already have an account?', ET_DOMAIN);?> <a href="<?php echo et_get_page_link("login") ?>" class="secondary-color"><?php _e('Log In', ET_DOMAIN);?></a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
	}
	get_footer();
?>