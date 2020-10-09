<div class="main-content">
	<div class="content-wrapper">
		<div class="container-fluid">
			<?php if ($this->session->flashdata('success_msg')) : ?>
				<div class="alert alert-success">
					<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
					<?php echo $this->session->flashdata('success_msg') ?>
				</div>
			<?php endif ?>
			<?php if ($this->session->flashdata('error_msg')) : ?>
				<div class="alert alert-danger">
					<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
					<?php echo $this->session->flashdata('error_msg') ?>
				</div>
			<?php endif ?>
			<!-- Basic form layout section start -->
			<section id="basic-form-layouts">
				<div class="row">
					<div class="col-md-12">
						<div class="card">
							<div class="clearfix"></div>
							<div class="card-header">
								<div class="page-title-wrap">
									<h4 class="card-title">CMS List</h4>
									<a class="add_bttn title_btn t_btn_list" href="<?= base_url('admin/cms/add') ?>"><span><i class="fa fa-plus" aria-hidden="true"></i></span> Add CMS</a>									
								</div>
							</div>
							<?php //pr($ck_action_falg); ?>
							<div class="card-body">
								<div class="px-3">
									<form class="form">
										<div class="form-body">
											<!-- start -->
											<div class="row">
												<div class="col-md-12">
													<div class="staff_tab_area">
														<ul class="nav nav-tabs" role="tablist">
                                                            <li class="nav-item">
                                                                <a class="nav-link active" data-toggle="tab" href="#active_user">Active Pages</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#inactive_user">Inactive Pages</a>
                                                            </li>
                                                        <!--    <li class="nav-item">
                                                                <a class="nav-link" data-toggle="tab" href="#trash_user">Trash Driver</a>
                                                            </li> -->
                                                        </ul>
                                                        <div class="tab-content">
                                                        	<div id="active_user" class="tab-pane active"><br>
																<div class="table-responsive custom_table_area">
																	<table class="table table-striped table-bordered dom-jQuery-events c_table_style">
																		<thead>
																			<tr>
																				<th>SL No.</th>
																				<th>Page Name</th>
																				<th>Title</th>
																				<!--<th style="min-width: 50px;">Status</th>-->
																				<?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
																				<th class="action_bttn" style="min-width: 50px;" <?php echo $actrion_visibility; ?>>Action</th>
																				<th>Log</th>
																			</tr>
																		</thead>
																		<tbody>
																			<?php if (!empty($cms_active_list)) { ?>
																				<?php foreach ($cms_active_list as $key => $cms_active) { ?>
																					<tr>
																						<td><?=$key +1?></td>
																						<td><?=$cms_active['page_name']?></td>
																						<td><?=$cms_active['short_desc']?></td>
																						<!--<td class="action_td text-center">																								
																							<a title="Active" class="btn_action edit_icon inactive_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $cms_active['page_id'];?>" href="<?=base_url('admin/Cms/changeStatus')?>"></a>
																						</td>-->
																						<td class="action_bttn action_td text-center">
																							<a title="Active" class="edit_bttn btn_action btn-warning active_btn make_inactive" data-id="<?php echo $cms_active['page_id'];?>" href="<?=base_url('admin/Cms/changeStatus')?>"><i class="fa fa-times" aria-hidden="true"></i></a> 
																							<a title="Edit" class="edit_bttn btn_action edit_icon" href="<?= base_url();?>admin/cms/edit_cms/<?= $cms_active['page_id'];?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
																							<!--<a title="Delete" href="<?=base_url('admin/Cms/DeletePage/'.$cms_active['page_id'])?>" class="delete_bttn btn_action btn-danger delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>-->
																						</td>
																						<td class="action_td text-center">
																							<a title="Log" class="btn_action edit_icon log_view" data-column="page_id" data-table="cms" data-id="<?= $cms_active['page_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a>
																						</td>
																					</tr>

																				<?php }
																				}  ?>

																		</tbody>
																	</table>
																</div>
															</div>
															<div id="inactive_user" class="tab-pane fade"><br>
                                                                <div class="table-responsive custom_table_area">
																	<table class="table table-striped table-bordered dom-jQuery-events c_table_style">
																		<thead>
																			<tr>
																				<th>SL No.</th>
																				<th>Page Name</th>
																				<th>Title</th>
																				<!--<th style="min-width: 50px;">Status</th>-->
																				<?php if($this->session->userdata('role_id') == '17' || $this->session->userdata('role_id') == '16'): $actrion_visibility ='data-visible = false';else: $actrion_visibility ='';endif; ?>
																				<th class="action_bttn" style="min-width: 50px;" <?php echo $actrion_visibility; ?>>Action</th>
																				<th>Log</th>
																			</tr>
																		</thead>
																		<tbody>
																			<?php if (!empty($cms_inactive_list)) { ?>
																				<?php foreach ($cms_inactive_list as $key => $cms_inactive) { ?>
																					<tr>
																						<td><?=$key +1?></td>
																						<td><?=$cms_inactive['page_name']?></td>
																						<td><?=$cms_inactive['short_desc']?></td>
																						<!--<td class="action_td text-center">
																							<a title="Inactive" class="btn_action btn-warning active_btn" style="pointer-events: none;cursor: default;text-decoration: none;color: black;" data-id="<?php echo $cms_inactive['page_id'];?>" href="<?=base_url('admin/Cms/changeStatus')?>"><i class="fa fa-check" aria-hidden="true"></i></a>
																						</td>-->
																						<td class="action_bttn action_td text-center" >
																							<a title="Inactive" class="edit_bttn btn_action edit_icon inactive_btn make_active" data-id="<?php echo $cms_inactive['page_id'];?>" href="<?=base_url('admin/Cms/changeStatus')?>"><i class="fa fa-check" aria-hidden="true"></i></a>
																							<a title="Edit" class="edit_bttn btn_action edit_icon" href="<?= base_url();?>admin/cms/edit_cms/<?= $cms_inactive['page_id'];?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
																							<!--<a title="Delete" href="<?=base_url('admin/Cms/DeletePage/'.$cms_inactive['page_id'])?>" class="delete_bttn btn_action btn-danger delete_btn" ><i class="fa fa-trash" aria-hidden="true"></i></a>-->																						
																						</td>
																						<td class="action_td text-center">
																							<a title="Log" class="btn_action edit_icon log_view" data-column="page_id" data-title="CMS" data-id="<?= $cms_inactive['page_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a>
																						</td>
																					</tr>

																				<?php }
																				}  ?>

																		</tbody>
																	</table>
																</div>
                                                            </div>
														</div>
														<!-- Tab panes -->

													</div>
												</div>
											</div>
											<!-- end -->
										</div>

										<!--<div class="form-actions">
								<button type="button" class="btn btn-danger mr-1">
									<i class="icon-trash"></i> Cancel
								</button>
								<button type="button" class="btn btn-success">
									<i class="icon-note"></i> Save
								</button>
							</div>-->
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<!-- // Basic form layout section end -->
		</div>
	</div>
</div>