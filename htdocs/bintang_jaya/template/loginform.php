<?php
	$html = '
		<if criteria="$_GET[msg] == \'loginerror\'">
		<div align="center" style="padding-left: 40px;">
			<b>Maaf, username dan password tidak terdaftar.</b>
		</div><br>
		</endif>
		<div align="center">
			<table border="0" width="365" cellspacing="0" cellpadding="0">
				<tr>
					<td width="365" height="210">
					<table border="0" width="365" cellspacing="0" cellpadding="0">
						<tr>
							<td width="62" height="210">
							<img border="0" src="img/login_key_l.png" width="62" height="210"></td>
							<td width="303" height="210" class="login_box" valign="top">
							<table border="0" width="303" cellspacing="0" cellpadding="0">
								<tr>
									<td width="303" height="33"></td>
								</tr>
								<tr>
									<td width="303" height="18">
									<table border="0" width="303" cellspacing="0" cellpadding="0">
										<tr>
											<td width="225" height="18"></td>
											<td width="26" height="18">
											<img border="0" src="img/tool_minimaze_last.png" width="26" height="18"></td>
											<td width="44" height="18">
											<img border="0" src="img/tool_exit.png" width="44" height="18"></td>
											<td width="8" height="18"></td>
										</tr>
									</table>
									</td>
								</tr>
								<tr>
									<td width="303" height="155">
									<table border="0" width="303" cellspacing="0" cellpadding="0">
										<tr>
											<td width="53" height="155"></td>
											<td width="240" height="155" valign="top">
											<form action="login.php" method="post" onsubmit="return checklogin(this)">
											<table border="0" width="240" cellspacing="0" cellpadding="0">
												<tr>
													<td width="240" height="25" class="white_text" align="left">
													<b>Nama Pengguna</b></td>
												</tr>
												<tr>
													<td width="240" height="30">
													<table border="0" width="240" cellspacing="0" cellpadding="0">
														<tr>
															<td width="10" height="30">
															<img border="0" src="img/text_field_l.png" width="10" height="30"></td>
															<td width="220" height="30" class="text_field_bg">
															<input type="text" name="username" class="inputfield_login"></td>
															<td width="10" height="30">
															<img border="0" src="img/text_field_r.png" width="10" height="30"></td>
														</tr>
													</table>
													</td>
												</tr>
												<tr>
													<td width="240" height="25" class="white_text" align="left">
													<b>Kata Sandi</b></td>
												</tr>
												<tr>
													<td width="240" height="30">
													<table border="0" width="240" cellspacing="0" cellpadding="0">
														<tr>
															<td width="10" height="30">
															<img border="0" src="img/text_field_l.png" width="10" height="30"></td>
															<td width="220" height="30" class="text_field_bg">
															<input type="password" name="password" class="inputfield_login"></td>
															<td width="10" height="30">
															<img border="0" src="img/text_field_r.png" width="10" height="30"></td>
														</tr>
													</table>
													</td>
												</tr>
												<tr>
													<td width="240" height="45">
													<table border="0" width="240" cellspacing="0" cellpadding="0">
														<tr>
															<td width="170" height="25">&nbsp;</td>
															<td width="70" height="25">
															<input type="image" border="0" src="img/button_login.png" width="70" height="25"></td>
														</tr>
													</table>
													</td>
												</tr>
											</table>
											</form></td>
											<td width="10" height="155"></td>
										</tr>
									</table>
									</td>
								</tr>
								<tr>
									<td width="303" height="4"></td>
								</tr>
							</table>
							</td>
						</tr>
					</table>
					</td>
				</tr>
			</table>
		</div>
	';
?>