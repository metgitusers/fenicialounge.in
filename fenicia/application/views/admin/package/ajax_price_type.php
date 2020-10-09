<div class="col-md-4 pkg_select_div">
    <div><button class="btn pull-right btn-danger delete_pro" id="delete"><i class="fa fa-trash-o"></i></button></div>
    <div class="form-group">                                
      <label>Membership Type<sup>*</sup></label>
        <select name="package_type[]" class="form-control pkg_type" required>
          <option value="">Select membership type</option>
          <?php if(!empty($package_type)): ?>
              <?php   foreach($package_type as $pkg): ?>                                              
                        <option value="<?php echo $pkg['package_type_id']; ?>"><?php echo ucfirst($pkg['package_type_name']); ?></option>
              <?php   endforeach; ?>
          <?php endif; ?>
        </select>
        <div class="input-group pkg_type_price_div" style="display:none">
          <div class="input-group-prepend">
            <span class="input-group-text" id="basic-addon1">₹</span>
          </div> 
          <input class="pkg_type_price" type="number" min="1" name="package_type_price[]" placeholder="price" value="">
            
        </div>
         <!--- added number input box on 15/07/20 by soma --->
        <div class="input-group number"  style="display:none;margin-top: 10px;"> Duration:  
         
         <!-- <input class="number"  type="text" placeholder="number" name="package_type_number[]"> -->
          <input class="number" name="package_type_number[]" type="text" placeholder=" No. of days" >
        </div>
         <!--- added number input box on 15/07/20 by soma --->
    </div>                                
  </div>                              
