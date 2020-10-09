<div class="row">
  <div class="table-responsive custom_table_area export_table_area">
    <table class="table table-striped table-bordered export_btn_dt c_table_style pckg_purchased_report_table">
      <thead>
          <tr>
              <th class="border-top-0">SL No.</th>
              <th class="border-top-0">Membership Name</th>
              <th class="border-top-0">Membership Type</th>
              <th class="border-top-0">Membership Price (₹)</th>
              <th class="border-top-0">Membership Id</th>
              <th class="border-top-0">Membership Owner</th>
              <th class="border-top-0">Registered On</th>
              <th class="border-top-0">Expairy Date</th>
              <th class="border-top-0">Status</th>
          </tr>
      </thead>
      <tbody>
          <?php if (!empty($packages_purchased_list)) { ?>
          <?php     foreach ($packages_purchased_list as $key => $pkglist) { ?>
          <tr>
              <td><?= $key + 1 ?></td>
              <td><?= $pkglist['package_name'] ?></td>
              <td><?= $pkglist['package_type_name'] ?></td>
              <td><?= $pkglist['price'] ?></td>
              <td><?= $pkglist['membership_id'] ?></td>
              <td class="name_space"><?= ucfirst($pkglist['full_name'])."<br>DOB: ".date('d-m-Y', strtotime($pkglist['dob'])) ?></td>
              <td><?= date('l d M Y', strtotime($pkglist['buy_on'])) ?></td>
              <td><?= date('l d M Y', strtotime($pkglist['expiry_date'])) ?></td>
              <?php if($pkglist['package_mapping_status'] == 1): ?>
                      <td><button class="btn" style="pointer-events: none;background-color: #28D094">Active</button></td>
              <?php else: ?>
                      <td><button class="btn" style="pointer-events: none;background-color: #dark grey">Inactive</button></td>
              <?php endif;?>                                                   
          </tr>
          <?php 
          } } else { ?>
              <tr>
                  <td colspan="9" style="text-align:center;">No Data Available</td>
              </tr>

          <?php  } ?>
      </tbody>
    </table>
</div>
</div>