<div class="main-content">
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Basic form layout section start -->
      <section id="basic-form-layouts">
        <!--<div class="row">
          <div class="col-sm-12">
            <h2 class="content-header">Driver Master</h2>
          </div>
        </div>-->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <div class="page-title-wrap">
                  <h4 class="card-title">Feedback Details</h4>
                  
                </div>


                <!--<p class="mb-0">This is the most basic and cost estimation form is the default position.</p>-->
              </div>
              <div class="card-body">
                <div class="px-3">

                  <?php
                  if (!empty($row)) { 
                    $food=0;
                    $serving=0;
                    $venue=0;
                    $food=($row['food_varity']+$row['food_quality']+$row['food_serving']+$row['food_presentation'])/4;
                    $serving=($row['service_speed']+$row['service_courtesy']+$row['service_knowledge'])/3;
                    $venue=($row['venue_atmosphere']+$row['venue_cleanliness'])/2;
                  ?>
                        <div class="form-body coment-card-details">
                            <div class="row">                           
                                <div class="col-md-4">
                                   <h4 class="card-title"> Date of Visit:</h4> 
                                </div>
                                 <div class="col-md-4">
                                   <h6><span><?php echo date('d/m/Y',strtotime($row['visit_date'])); ?></span></h6>
                                </div>
                            </div>
                            <?php if($row['first_name']!=''){ ?>
                          <div class="row">                           
                                <div class="col-md-4">
                                   <h4 class="card-title"> Commented By:</h4> 
                                </div>
                                 <div class="col-md-4">
                                   <h6><span><?php echo $row['first_name']." ".$row['last_name']; ?></span></h6>
                                </div>
                            </div>
                           <?php } ?>
                           <div class="row">                           
                                <div class="col-md-4">
                                   <h4 class="card-title"> Mobile:</h4> 
                                </div>
                                 <div class="col-md-4">
                                   <h6><span><?php echo $row['mobile']; ?></span></h6>
                                </div>
                            </div>
                          
                         
                          <div class="row">                           
                            <div class="col-md-4">
                               <h4 class=""><u> Food </u></h4>
                            </div>
                            <div class="col-md-4">
                               <h4 class=""><b><?php echo round($food,1); ?></b></h4>
                            </div>
                          </div>
                          <div class="row">                           
                            <div class="col-md-4">
                               <h4 class="card-title"> Food Varity:</h4> 
                            </div>
                             <div class="col-md-4">
                               <h6><span><?php if($row['food_varity']>0){ echo $row['food_varity']; } else if($row['food_varity']==0){echo "NO FEEDBACK"; }?></span></h6>
                            </div>
                          </div>
                          <div class="row">                           
                            <div class="col-md-4">
                               <h4 class="card-title"> Food Quality:</h4>
                            </div>
                             <div class="col-md-4">
                               <h6><span><?php if($row['food_quality']>0){echo $row['food_quality']; } else if($row['food_quality']==0){echo "NO FEEDBACK"; }?></span></h6>
                            </div>
                          </div>
                          <div class="row">                           
                            <div class="col-md-4">
                               <h4 class="card-title"> Food Serving:</h4>
                            </div>
                             <div class="col-md-4">
                               <h6><span><?php if($row['food_serving']>0){ echo $row['food_serving']; }else if($row['food_serving']==0){echo "NO FEEDBACK"; }?></span></h6>
                            </div>
                          </div>
                          <div class="row">                           
                            <div class="col-md-4">
                               <h4 class="card-title"> Food Presentation:</h4>
                            </div>
                             <div class="col-md-4">
                               <h6><span><?php if($row['food_presentation']>0){echo $row['food_presentation']; } else if($row['food_presentation']==0){echo "NO FEEDBACK"; }?></span></h6>
                            </div>
                          </div>
                            
                            
                          <div class="row">                           
                            <div class="col-md-4">
                               <h4 class=""><u> Service </u></h4>
                            </div>
                           <div class="col-md-4">
                               <h4 class=""><b> <?php echo round($serving,1); ?></b></h4>
                            </div>
                          </div>
                           <div class="row">                           
                            <div class="col-md-4">
                               <h6 class="card-title"> Service Speed:</h6> 
                            </div>
                            <div class="col-md-4">
                               <h6><span><?php if($row['service_speed']>0){echo $row['service_speed']; } else if($row['service_speed']==0){echo "NO FEEDBACK"; }?></span></h6>
                            </div>
                          </div>
                           <div class="row">                           
                            <div class="col-md-4">
                               <h6 class="card-title"> Staff Courtesy:</h6> 
                            </div>
                            <div class="col-md-4">
                               <h6><span><?php if($row['service_courtesy']>0){echo $row['service_courtesy']; } else if($row['service_courtesy']==0){echo "NO FEEDBACK"; }?></span></h6>
                            </div>

                          </div>
                           <div class="row">                           
                            <div class="col-md-4">
                               <h6 class="card-title"> Staff knowledge:</h6> 
                            </div>
                            <div class="col-md-4">
                               <h6><span><?php if($row['service_knowledge']>0){echo $row['service_knowledge']; } else if($row['service_knowledge']==0){echo "NO FEEDBACK"; }?></span></h6>
                            </div>
                          </div>
                          
                          <div class="row">                           
                            <div class="col-md-4">
                               <h4 class=""><u> Venue   <?php echo round($venue,1); ?></u></h4>
                            </div>
                            <div class="col-md-4">
                               <h4 class=""><b> <?php echo round($venue,1); ?></b></h4>
                            </div>
                          </div>
                            <div class="row">                           
                            <div class="col-md-4">
                               <h4 class="card-title"> Venue Atmosphere:</h4>
                            </div>
                             <div class="col-md-4">
                               <h6><span><?php if($row['venue_atmosphere']>0){echo $row['venue_atmosphere']; }else if($row['venue_atmosphere']==0){echo "NO FEEDBACK"; }?></span></h6>
                            </div>
                          </div>
                          <div class="row">                           
                            <div class="col-md-4">
                               <h4 class="card-title"> Venue Cleanliness:</h4>
                            </div>
                             <div class="col-md-4">
                               <h6><span><?php if($row['venue_cleanliness']>0){echo $row['venue_cleanliness']; }
                                 else if($row['venue_cleanliness']==0){echo "NO FEEDBACK"; }?></span></h6>
                            </div>
                          </div>
                          <div class="row">                           
                            <div class="col-md-4">
                               <h4 class="card-title"> Any staff who stood out: </h4>
                            </div>
                             <div class="col-md-4">
                               <h6><span><?php echo $row['staff']; ?></span></h6>
                            </div>
                          </div>
                             <div class="row">                           
                            <div class="col-md-4">
                               <h4 class="card-title"> Suggestions:</h4> 
                            </div>
                            <div class="col-md-4">
                               <h6><span><?php echo $row['suggestion']; ?></span></h6>
                            </div>
                          </div>
                          </div>
                         
                         
                       
                  <?php
                  }
                  ?>
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
