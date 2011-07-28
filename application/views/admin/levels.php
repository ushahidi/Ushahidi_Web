			<div class="bg">
				<h2>
					<a href="<?php echo url::site() . 'admin/manage' ?>"><?php echo Kohana::lang('ui_main.categories');?></a>
					<a href="<?php echo url::site() . 'admin/manage/forms' ?>"><?php echo Kohana::lang('ui_main.forms');?></a>
					<a href="<?php echo url::site() . 'admin/manage/organizations' ?>"><?php echo Kohana::lang('ui_main.organizations');?></a>
					<a href="<?php echo url::site() . 'admin/manage/feeds' ?>"><?php echo Kohana::lang('ui_main.news_feeds');?></a>
					<a href="<?php echo url::site() . 'admin/manage/levels' ?>" class="active"><?php echo Kohana::lang('ui_main.reporter_levels');?></a>
					<span>(<a href="#add"><?php echo Kohana::lang('ui_main.add_new');?></a>)</span>
					<a href="<?php echo url::site() . 'admin/manage/reporters' ?>"><?php echo Kohana::lang('ui_main.reporters');?></a>
				</h2>
				<?php
				if ($form_error) {
				?>
					<!-- red-box -->
					<div class="red-box">
						<h3><?php echo Kohana::lang('ui_main.error');?></h3>
						<ul>
						<?php
						foreach ($errors as $error_item => $error_description)
						{
							// print "<li>" . $error_description . "</li>";
							print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
						}
						?>
						</ul>
					</div>
				<?php
				}

				if ($form_saved) {
				?>
					<!-- green-box -->
					<div class="green-box">
						<h3><?php echo Kohana::lang('ui_main.level_has_been');?> <?php echo $form_action; ?>!</h3>
					</div>
				<?php
				}
				?>
				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(NULL,array('id' => 'levListing',
					 	'name' => 'levListing')); ?>
						<input type="hidden" name="action" id="action" value="">
						<input type="hidden" name="level_id" id="level_id_action" value="">
						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1">&nbsp;</th>
										<th class="col-2"><?php echo Kohana::lang('ui_main.level');?></th>
										<th class="col-3"><?php echo Kohana::lang('ui_main.weight');?></th>
										<th class="col-4"><?php echo Kohana::lang('ui_main.actions');?></th>
									</tr>
								</thead>
								<tfoot>
									<tr class="foot">
										<td colspan="4">
											<?php echo $pagination; ?>
										</td>
									</tr>
								</tfoot>
								<tbody>
									<?php
									if ($total_items == 0)
									{
									?>
										<tr>
											<td colspan="4" class="col">
												<h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
											</td>
										</tr>
									<?php	
									}
									foreach ($levels as $level)
									{
										$level_id = $level->id;
										$level_title = $level->level_title;
										$level_description = substr($level->level_description, 0, 150);
										$level_weight = $level->level_weight;
										?>
										<tr>
											<td class="col-1">&nbsp;</td>
											<td class="col-2">
												<div class="post">
													<h4><?php echo $level_title; ?></h4>
													<p><?php echo $level_description; ?>...</p>
												</div>
											</td>
											<td class="col-3"> <?php echo $level_weight; ?></td>
											<td class="col-4">
												<ul>
													<li class="none-separator"><a href="#add" onClick="fillFields('<?php echo(rawurlencode($level_id)); ?>','<?php echo(rawurlencode($level_title)); ?>','<?php echo(rawurlencode($level_description)); ?>','<?php echo(rawurlencode($level_weight)); ?>')"><?php echo Kohana::lang('ui_main.edit');?></a></li>
<li><a href="javascript:catAction('d','DELETE','<?php echo(rawurlencode($level_id)); ?>')" class="del"><?php echo Kohana::lang('ui_main.delete');?></a></li>
												</ul>
											</td>
										</tr>
										<?php
									}
									?>
								</tbody>
							</table>
						</div>
					<?php print form::close(); ?>
				</div>
				
				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="#" class="active"><?php echo Kohana::lang('ui_main.add_edit');?></a></li>
						<li><a href="#"><?php echo Kohana::lang('ui_main.add_language');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<?php print form::open(NULL,array('id' => 'catMain',
						 	'name' => 'catMain')); ?>
						<input type="hidden" id="level_id" 
							name="level_id" value="" />
						<input type="hidden" name="action" 
							id="action" value="a"/>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.level_name');?>:</strong><br />
							<?php print form::input('level_title', '', ' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.description');?>:</strong><br />
							<?php print form::input('level_description', '', ' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							<strong><?php echo Kohana::lang('ui_main.weight');?>:</strong><br />
							<?php print form::input('level_weight', '', ' class="text"'); ?>
						</div>
						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::file_loc('img'); ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>			
					</div>
				</div>
			</div>
			<script type="text/javascript">
			// Levels JS
			function fillFields(id, level_title, level_description, level_weight)
			{
				$("#level_id").attr("value", unescape(id));
				$("#level_title").attr("value", unescape(level_title));
				$("#level_description").attr("value", unescape(level_description));
				$("#level_weight").attr("value", unescape(level_weight));
			}
			</script>
