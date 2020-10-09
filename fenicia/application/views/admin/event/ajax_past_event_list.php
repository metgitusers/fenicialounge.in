<div id="active_user" class="tab-pane active"><br>
    <div class="table-responsive custom_table_area export_table_area">
        <table class="table table-striped table-bordered export_btn_dt c_table_style reservation_list_table">
            <thead>
                <tr>
                    <th>SL No.</th>                                                                                
                    <th>Event Details</th>
                    <!--<th>Location</th>-->
                    <th width="12%">Event Date</th>
                    <th width="12%">Event Time</th>
                    <th width="18%">Action</th>
                    <th>Log</th>
                </tr>
            </thead>
            <tbody>
                    <?php if (!empty($event_past_active_list)) { ?>
                    <?php     foreach ($event_past_active_list as $key => $actv_ent) { ?>
                    <tr>
                        <td><?= $key + 1 ?></td>                                                                                        
                        <td><?= '<p><strong>'.ucfirst($actv_ent['event_name']).'</strong></p>'.$actv_ent['event_location']; ?></td>
                        <td width="12%"><?= date('d/m/Y',strtotime($actv_ent['event_start_date'])) ?></td>
                        <td width="12%"><?= date('h:i A',strtotime($actv_ent['event_start_time'])) ?></td>                                                                                                             
                        <td class="action_td text-center" width="18%">
                            <a title="View Details" style="width:auto; height:auto;" href="<?=base_url('admin/event/viewPastEvent/'.$actv_ent['event_id'])?>" class="view_bttn btn_action edit_icon"><i class="fa fa-eye" aria-hidden="true"></i> Details</a>                                                                                            
                            <a title="View Images" style="width:auto; height:auto;" href="<?=base_url('admin/event/viewPastEventImages/'.$actv_ent['event_id'])?>" class="view_bttn btn_action edit_icon"><i class="fa fa-eye" aria-hidden="true"></i> Images</a>
                        </td>
                        <td class="action_td text-center">
                            <a title="Log" class="btn_action edit_icon log_view" data-column="event_id" data-title="Event" data-id="<?= $actv_ent['event_id'];?>"><i class="fa fa-list" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                <?php 
                } } else { ?>
                <tr>
                    <td colspan="17" style="text-align:center;">No Data Available</td>
                </tr>

            <?php  } ?>

            </tbody>
        </table>
    </div>
</div>
    