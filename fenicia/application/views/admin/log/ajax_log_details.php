<div class="modal-header bg-info white">  
<?php if(!empty($log_lists)){ ?>  
	<h5><?php echo $db_title; ?> Activity Logs #<?php echo $log_lists[0]['id'];?> </h5> 
<?php } else{ ?>
	<h5><?php echo $db_title; ?> Activity Logs</h5> 
<?php }   ?>       
    <button type="button" class="close" id="log_close_btn" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
</div>
 <div class="modal-body">    
    <?php if(!empty($log_lists)){ ?>
        <div>              
    <?php   foreach($log_lists as $val){ ?>
                <div class="row" style="margin:10px 0 10px 10px">    
                    <div><span style="margin-right:8px"><i class="fa fa-file-text" aria-hidden="true"></i></span><?php echo $db_title." ".$val['action'] ?></div>
                    <div class="row col-md-12" style="border-bottom: 1px solid #aaa;">
                        <div class="col-md-10" style="font-size:12px"><?php echo $val['statement']."<br> By ".$val['full_name'] ?></div>
                        <div class="col-md-2" style="font-size:9.5px"><?php echo date('d/m/Y h:i A', strtotime($val['action_on'])); ?></div>
                    </div>
                </div>
    <?php   } ?>
        </div>
    <?php } else {?>
        <div style="margin: 30px">
            <h6>No activity log available</h6>      
        </div>
    <?php } ?>
</div> 

