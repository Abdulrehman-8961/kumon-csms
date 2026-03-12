      <?php
      
      $GETID = $_GET['id'] ?? ($qry->id ?? null);
      
      $userAccess = explode(',', Auth::user()->access_to_client);
      
      
      $today = date('Y-m-d');
      
      $later = new DateTime($today);
      ?>
      <input type="hidden" name="cloned_client_id" id="cloned_client_id" value="{{ $client->id }}">
      <div class="tab-content" id="nav-tabContent-edit">
          {{-- Main Tab --}}
          <div class="tab-pane fade show active" id="nav-main-client-edit" role="tabpanel"
              aria-labelledby="nav-main-tab-client-edit">

              <div class="block new-block position-relative mt-3">
                  <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
                      <div class="row">
                          <div class="col-sm-12">
                              <h5 class="titillium-web-black mb-3 text-purpule">Mother Information</h5>
                          </div>
                          {{-- <div class="col-sm-2">
                              <div class="custom-dropdown salutation-custom-dropdown border p-2 mb-3 border-style edit-border"
                                  data-selected-id="{{ $client->salutation }}"
                                  data-selected-text="{{ $client->salutation }}">

                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Salutation</h6>

                                  <div
                                      class="dropdown-display d-flex align-items-center justify-content-between pb-1 pl-1 pt-0">
                                      <div class="d-flex align-items-center">
                                          <i class="fa-light fa-certificate text-grey fs-18 constant-icon"></i>
                                          <span
                                              class="selected-value font-titillium text-placeholder fw-300 mb-0 ml-2 edit-field">
                                              Select Salutation
                                          </span>
                                      </div>
                                      <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20"></i>
                                  </div>

                                  <ul class="dropdown-options">
                                      <li class="search-option p-0 mb-2 mt-1">
                                          <input type="text" class="dropdown-search text-darkgrey fw-600"
                                              placeholder="" />
                                      </li>
                                      <li data-value="Dr.">Dr.</li>
                                      <li data-value="Miss.">Miss.</li>
                                      <li data-value="Mr.">Mr.</li>
                                      <li data-value="Mrs.">Mrs.</li>
                                  </ul>

                              </div>
                              <input type="hidden" name="salutation" id="edit_salutation"
                                  value="{{ $client->salutation }}">
                          </div> --}}
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border position-relative">
                                <a href="javascript:void();" class="mandatory-icon position-absolute"
                                      style="right: 9px;" data-toggle="tooltip" data-trigger="hover"
                                      data-placement="top" title="" data-original-title="Mandatory"><i
                                          class="fa-light fa-circle-star text-grey fs-20"></i></a>
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">First Name</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-user text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          placeholder="Enter first name" name="first_name" id="first_name"
                                          value="{{ $client->firstname }}">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border position-relative">
                                <a href="javascript:void();" class="mandatory-icon position-absolute"
                                      style="right: 9px;" data-toggle="tooltip" data-trigger="hover"
                                      data-placement="top" title="" data-original-title="Mandatory"><i
                                          class="fa-light fa-circle-star text-grey fs-20"></i></a>
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Last Name</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-file-signature text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          placeholder="Enter last name" name="last_name" id="last_name"
                                          value="{{ $client->lastname }}">
                                  </div>
                              </div>
                          </div>
                          {{-- line 2 start --}}
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border position-relative">
                                <a href="javascript:void();" class="mandatory-icon position-absolute"
                                      style="right: 9px;" data-toggle="tooltip" data-trigger="hover"
                                      data-placement="top" title="" data-original-title="Mandatory"><i
                                          class="fa-light fa-circle-star text-grey fs-20"></i></a>
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Address</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-address-card text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          placeholder="Enter home address" name="client_address" id="client_address"
                                          value="{{ $client->client_address }}">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="border p-2 mb-3 border-style edit-border position-relative">
<a href="javascript:void();" class="mandatory-icon position-absolute"
                                      style="right: 9px;" data-toggle="tooltip" data-trigger="hover"
                                      data-placement="top" title="" data-original-title="Mandatory"><i
                                          class="fa-light fa-circle-star text-grey fs-20"></i></a>
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">City</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-city text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          placeholder="Enter home address" name="city" id="city"
                                          value="{{ isset($client->city) ? $client->city : 'Laval' }}">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="custom-dropdown province-custom-dropdown border p-2 mb-3 border-style edit-border"
                                  data-selected-id="{{ isset($client->state) ? $client->state : 'Quebec' }}"
                                  data-selected-text="{{ isset($client->state) ? $client->state : 'Quebec' }}">
<a href="javascript:void();" class="mandatory-icon position-absolute"
                                      style="right: 9px;" data-toggle="tooltip" data-trigger="hover"
                                      data-placement="top" title="" data-original-title="Mandatory"><i
                                          class="fa-light fa-circle-star text-grey fs-20"></i></a>
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">
                                      Province
                                  </h6>

                                  <div
                                      class="dropdown-display d-flex align-items-center justify-content-between pb-1 pl-1 pt-0">
                                      <div class="d-flex align-items-center">
                                          <i class="fa-brands fa-canadian-maple-leaf text-grey fs-18 constant-icon"></i>
                                          <span class="selected-value font-titillium fw-300 mb-0 ml-2 edit-field">
                                              Select Province
                                          </span>
                                      </div>
                                      <i class="fa-light fa-circle-xmark clear-icon text-grey fs-20"></i>
                                  </div>

                                  <ul class="dropdown-options">
                                      <li class="search-option p-0 mb-2 mt-1">
                                          <input type="text" class="dropdown-search text-darkgrey fw-600"
                                              placeholder="" />
                                      </li>

                                      <!-- Alphabetically sorted -->
                                      <li data-value="Alberta">Alberta</li>
                                      <li data-value="British Columbia">British Columbia</li>
                                      <li data-value="Manitoba">Manitoba</li>
                                      <li data-value="New Brunswick">New Brunswick</li>
                                      <li data-value="Newfoundland and Labrador">Newfoundland and Labrador</li>
                                      <li data-value="Nova Scotia">Nova Scotia</li>
                                      <li data-value="Ontario">Ontario</li>
                                      <li data-value="Prince Edward Island">Prince Edward Island</li>
                                      <li data-value="Quebec">Quebec</li>
                                      <li data-value="Saskatchewan">Saskatchewan</li>
                                  </ul>

                              </div>
                              <input type="hidden" name="province" id="edit_province"
                                  value="{{ isset($client->state) ? $client->state : 'Quebec' }}">
                          </div>
                          {{-- line 3 start --}}
                          <div class="col-sm-3">
                              <div class="border p-2 mb-3 border-style edit-border position-relative">
                                <a href="javascript:void();" class="mandatory-icon position-absolute"
                                      style="right: 9px;" data-toggle="tooltip" data-trigger="hover"
                                      data-placement="top" title="" data-original-title="Mandatory"><i
                                          class="fa-light fa-circle-star text-grey fs-20"></i></a>
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">
                                      Postal Code
                                  </h6>

                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-address-card text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          name="postal_code" id="postal_code" maxlength="7" autocomplete="off"
                                          value="{{ $client->zip }}">
                                  </div>
                              </div>
                          </div>

                          <div class="col-sm-3">
                              <div class="border p-2 mb-3 border-style edit-border position-relative">
                                <a href="javascript:void();" class="mandatory-icon position-absolute"
                                      style="right: 9px;" data-toggle="tooltip" data-trigger="hover"
                                      data-placement="top" title="" data-original-title="Mandatory"><i
                                          class="fa-light fa-circle-star text-grey fs-20"></i></a>
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">
                                      Telephone No.
                                  </h6>

                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-circle-phone text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          name="telephone_no" id="telephone_no" maxlength="12" autocomplete="off"
                                          value="{{ $client->work_phone }}">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border position-relative">
                                <a href="javascript:void();" class="mandatory-icon position-absolute"
                                      style="right: 9px;" data-toggle="tooltip" data-trigger="hover"
                                      data-placement="top" title="" data-original-title="Mandatory"><i
                                          class="fa-light fa-circle-star text-grey fs-20"></i></a>
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">
                                      Primary Email Address
                                  </h6>

                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-envelope text-grey fs-18 constant-icon"></i>
                                      <input type="email"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          name="primary_email_address" id="primary_email_address"
                                          placeholder="Enter primary email address"
                                          value="{{ $client->email_address }}">
                                  </div>
                              </div>
                          </div>
                          {{-- line 4 start --}}
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style">
                                  <div class="d-flex align-items-center justify-content-between mb-2">
                                      <h6 class="font-titillium text-grey mb-0 fw-700 pl-1">Access to portal</h6>
                                      <button type="button"
                                          class="btn banner-icon affiliate-info ml-auto p-0 cursor-help"
                                          data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                          title="" data-original-title="">
                                          <i class="fa-light fa-circle-question text-grey fs-18 regular-icon"></i>
                                          <i
                                              class="fa-solid fa-circle-question text-primary fs-18 header-solid-icon"></i>
                                      </button>
                                      <div class="affiliate-tooltip font-titillium text-grey fw-300" style="top: 4px; right: 56px;">
                                          <strong class="titillium-web-black fs-18 text-primary"
                                              style="line-height:1.6;">Kumon Portal Access</strong><br>
                                                Enabling this will send an invite to the clients
                                                primary email address to be able to manage
                                                their students vacations
                                      </div>
                                  </div>
                                  <div class="d-flex align-items-center justify-content-between pb-1 pl-1 pt-0">
                                      <div class="custom-control custom-switch">
                                          <input type="checkbox" class="custom-control-input" id="customSwitch3"
                                              name="portal_access" {{ $client->portal_access ? 'checked' : '' }}>
                                          <label class="custom-control-label" for="customSwitch3">
                                              <h6 class="font-titillium text-grey fw-300 mb-0 ml-2 switch-text3">
                                                  Access to manage student vacations disabled
                                              </h6>
                                          </label>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-radio-border position-relative" style="padding-bottom: 5px !important;">
                                <a href="javascript:void();" class="mandatory-icon position-absolute"
                                      style="right: 9px;" data-toggle="tooltip" data-trigger="hover"
                                      data-placement="top" title="" data-original-title="Mandatory"><i
                                          class="fa-light fa-circle-star text-grey fs-20"></i></a>
                                  <h6 class="font-titillium text-grey mb-1 fw-700 pl-1">Payment Method</h6>
                                  <div class="d-flex align-items-center pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-puzzle text-grey fs-18 constant-icon"></i>
                                      <div class="d-flex">
                                          <div class="radio_button ">
                                              <input type="radio" id="payment_method_credit_card"
                                                  name="payment_method" value="Credit Card"
                                                  {{ $client->payment_method == 'Credit Card' ? 'checked' : '' }} />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="payment_method_credit_card">Credit Card</label>
                                          </div>
                                          <div class="radio_button">
                                              <input type="radio" id="payment_method_eft" name="payment_method"
                                                  value="EFT"
                                                  {{ $client->payment_method == 'EFT' ? 'checked' : '' }} />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="payment_method_eft">EFT</label>
                                          </div>
                                          <div class="radio_button"> <input type="radio" id="payment_method_cash"
                                                  name="payment_method" value="Cash"
                                                  {{ $client->payment_method == 'Cash' ? 'checked' : '' }} />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="payment_method_cash">Cash</label>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>


              <div class="block new-block position-relative mt-3">
                  <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
                      <div class="row">
                          <div class="col-sm-12 d-flex justify-content-between mb-3">
                              <h5 class="titillium-web-black text-purpule mb-0">Father Information</h5>
                              <a href="javascript:void(0);" class="btn font-titillium fw-500 py-1 new-ok-btn"
                                  id="copy_address_from_mother_clone" style="width: auto;">Copy Address From Mother</a>
                          </div>
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">First Name</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-user text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          placeholder="Enter first name" name="father_first_name"
                                          id="father_first_name" value="{{ $client->father_firstname ?? '' }}">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Last Name</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-file-signature text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          placeholder="Enter last name" name="father_last_name"
                                          id="father_last_name" value="{{ $client->father_lastname ?? '' }}">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Address</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-address-card text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          placeholder="Enter home address" name="father_client_address"
                                          id="father_client_address" value="{{ $client->father_client_address ?? '' }}">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">City</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-city text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          placeholder="Enter city" name="father_city" id="father_city"
                                          value="{{ $client->father_city ?? '' }}">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="custom-dropdown province-custom-dropdown father-province-custom-dropdown border p-2 mb-3 border-style edit-border"
                                  data-selected-id="{{ $client->father_state ?? '' }}"
                                  data-selected-text="{{ $client->father_state ?? '' }}">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Province</h6>
                                  <div
                                      class="dropdown-display d-flex align-items-center justify-content-between pb-1 pl-1 pt-0">
                                      <div class="d-flex align-items-center">
                                          <i class="fa-brands fa-canadian-maple-leaf text-grey fs-18 constant-icon"></i>
                                          <span
                                              class="selected-value font-titillium {{ !empty($client->father_state) ? 'fw-300' : 'text-placeholder fw-300' }} mb-0 ml-2 edit-field">
                                              {{ !empty($client->father_state) ? $client->father_state : 'Select Province' }}
                                          </span>
                                      </div>
                                      <i class="fa-light fa-circle-xmark clear-icon text-grey {{ !empty($client->father_state) ? '' : 'd-none' }} fs-20"></i>
                                  </div>
                                  <ul class="dropdown-options">
                                      <li class="search-option p-0 mb-2 mt-1">
                                          <input type="text" class="dropdown-search text-darkgrey fw-600"
                                              placeholder="" />
                                      </li>
                                      <li data-value="Alberta">Alberta</li>
                                      <li data-value="British Columbia">British Columbia</li>
                                      <li data-value="Manitoba">Manitoba</li>
                                      <li data-value="New Brunswick">New Brunswick</li>
                                      <li data-value="Newfoundland and Labrador">Newfoundland and Labrador</li>
                                      <li data-value="Nova Scotia">Nova Scotia</li>
                                      <li data-value="Ontario">Ontario</li>
                                      <li data-value="Prince Edward Island">Prince Edward Island</li>
                                      <li data-value="Quebec">Quebec</li>
                                      <li data-value="Saskatchewan">Saskatchewan</li>
                                  </ul>
                              </div>
                              <input type="hidden" name="father_province" id="father_province"
                                  value="{{ $client->father_state ?? '' }}">
                          </div>
                          <div class="col-sm-3">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Postal Code</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-address-card text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          name="father_postal_code" id="father_postal_code" maxlength="7"
                                          autocomplete="off" value="{{ $client->father_zip ?? '' }}">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Telephone No.</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-circle-phone text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          name="father_telephone_no" id="father_telephone_no" maxlength="12"
                                          autocomplete="off" value="{{ $client->father_work_phone ?? '' }}">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Primary Email Address</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-envelope text-grey fs-18 constant-icon"></i>
                                      <input type="email"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          name="father_primary_email_address" id="father_primary_email_address"
                                          placeholder="Enter primary email address"
                                          value="{{ $client->father_email_address ?? '' }}">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style">
                                  <div class="d-flex align-items-center justify-content-between mb-2">
                                      <h6 class="font-titillium text-grey mb-0 fw-700 pl-1">Access to portal</h6>
                                      <button type="button"
                                          class="btn banner-icon affiliate-info ml-auto p-0 cursor-help"
                                          data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                          title="" data-original-title="">
                                          <i class="fa-light fa-circle-question text-grey fs-18 regular-icon"></i>
                                          <i
                                              class="fa-solid fa-circle-question text-primary fs-18 header-solid-icon"></i>
                                      </button>
                                      <div class="affiliate-tooltip font-titillium text-grey fw-300" style="top: 4px; right: 56px;">
                                          <strong class="titillium-web-black fs-18 text-primary"
                                              style="line-height:1.6;">Kumon Portal Access</strong><br>
                                          Enabling this will send an invite to the father's primary email address to be
                                          able to manage their students vacations
                                      </div>
                                  </div>
                                  <div class="d-flex align-items-center justify-content-between pb-1 pl-1 pt-0">
                                      <div class="custom-control custom-switch">
                                          <input type="checkbox" class="custom-control-input" id="customSwitch4"
                                              name="father_portal_access" {{ !empty($client->father_portal_access) ? 'checked' : '' }}>
                                          <label class="custom-control-label" for="customSwitch4">
                                              <h6 class="font-titillium text-grey fw-300 mb-0 ml-2 switch-text4">
                                                  Access to manage student vacations disabled
                                              </h6>
                                          </label>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          {{-- Students Tab --}}
          <div class="tab-pane fade" id="nav-students-client-edit" role="tabpanel"
              aria-labelledby="nav-students-tab-client-edit">
              <div class="block new-block position-relative mt-3">
                  <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
                      <div class="row">
                          <div class="col-sm-12">
                              <h5 class="titillium-web-black mb-3 text-purpule">Students</h5>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style">
                                  <div class="table-responsive small-box small-box-no-arrow">
                                      <table class="table table-sm table-striped align-middle mb-0 studentTable">
                                          <thead>
                                              <th class="py-2 border-0 pl-2" width="3%"></th>
                                              <th class="py-2 border-0 pl-2" width="3%"></th>
                                              <th class="py-2 border-0 pl-2" width="13%">
                                                  <h6
                                                      class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary">
                                                      Student ID</h6>
                                              </th>
                                              <th class="py-2 border-0 pl-2" width="25%">
                                                  <h6
                                                      class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary">
                                                      Student Name</h6>
                                              </th>
                                              <th class="py-2 border-0" width="18%">
                                                  <h6
                                                      class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary text-right pr-2">
                                                      Subjects</h6>
                                              </th>
                                              <th class="py-2 border-0 pl-2" width="13%">
                                                  <h6
                                                      class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary">
                                                      Start Date</h6>
                                              </th>
                                              <th class="py-2 border-0 pl-2" width="13%">
                                                  <h6
                                                      class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary">
                                                      End Date</h6>
                                              </th>
                                              <th class="py-2 border-0" width="20%">
                                                  <h6 class="font-titillium text-table-head mb-0 fw-700 text-right">
                                                      Amount</h6>
                                              </th>
                                              <th class="py-2 border-0 pl-3" width="5%"></th>
                                          </thead>
                                          <tbody></tbody>
                                      </table>
                                      <div
                                          class="font-titillium text-darkgrey fw-400 mb-0 text-center StudentTable-empty py-2">
                                          No details found</div>
                                  </div>
                                  <div class="Student-toast-added" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line added
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="Student-toast-added"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>
                                  <div class="Student-toast-updated" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line updated
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="Student-toast-updated"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>
                                  <div class="Student-toast-recovered" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line recovered
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="Student-toast-recovered"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>
                                  <div class="Student-toast-deleted" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line deleted</span>
                                          </div>
                                          <div class="d-flex align-items-center">
                                              <button type="button"
                                                  class="btn text-darkgrey btn-undo undo-delete-student font-titillium fs-14 mr-2"
                                                  data-action="undo">
                                                  Undo
                                              </button>
                                              <button type="button" data-section="Student-toast-deleted"
                                                  class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                                  <i class="fa-light fa-xmark"></i>
                                              </button>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <button type="button" id="btnAddStudent" data-toggle="modal"
                                  data-target="#addStudentModal"
                                  class="btn font-titillium fw-500 py-1 new-ok-btn float-right"
                                  style="width: 120px;">Add Student</button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          {{-- Payment Tab --}}
          <div class="tab-pane fade show active" id="nav-payments-client-edit" role="tabpanel"
              aria-labelledby="nav-payments-tab-client-edit">

              <div class="block new-block position-relative mt-3" style="margin-bottom: 1rem;">
                  <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
                      <div class="row">
                          <div class="col-sm-12">
                              <h5 class="titillium-web-black mb-3 text-purpule">Payments</h5>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style">
                                  <div class="table-responsive small-box small-box-395 small-box-no-arrow">
                                      <table class="table table-sm table-striped align-middle mb-0 paymentTable">
                                          <thead>
                                              <th class="py-2 border-0 pl-2" width="3%">
                                              </th>
                                              <th class="py-2 border-0 pl-2" width="22%">
                                                  <h6
                                                      class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary">
                                                      Month</h6>
                                              </th>
                                              <th class="py-2 border-0 pl-2" width="18%">
                                                  <h6
                                                      class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary text-center">
                                                      Payment Type</h6>
                                              </th>
                                              <th class="py-2 border-0 pl-2" width="18%">
                                                  <h6
                                                      class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary">
                                                      Reference No.</h6>
                                              </th>
                                              <th class="py-2 border-0" width="18%">
                                                  <h6
                                                      class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary text-right pr-2">
                                                      Amount</h6>
                                              </th>
                                              <th class="py-2 border-0 pl-2" width="21%">
                                                  <h6 class="font-titillium text-table-head mb-0 fw-700">Date</h6>
                                              </th>
                                              <th class="py-2 border-0 pl-3" width="3%"></th>
                                          </thead>
                                          <tbody>

                                          </tbody>
                                      </table>
                                      <div
                                          class="font-titillium text-darkgrey fw-400 mb-0 text-center paymentTable-empty py-2">
                                          No details found</div>
                                  </div>
                                  <div class="payment-toast-added" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line added
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="payment-toast-added"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>

                                  <div class="payment-toast-updated" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line updated
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="payment-toast-updated"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>

                                  <div class="payment-toast-recovered" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line recovered
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="payment-toast-recovered"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>

                                  <div class="payment-toast-deleted" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line deleted</span>
                                          </div>
                                          <div class="d-flex align-items-center">
                                              <button type="button"
                                                  class="btn text-darkgrey btn-undo undo-delete-payment font-titillium fs-14 mr-2"
                                                  data-action="undo">
                                                  Undo
                                              </button>
                                              <button type="button" data-section="payment-toast-deleted"
                                                  class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                                  <i class="fa-light fa-xmark"></i>
                                              </button>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <div class="col-sm-12">
                              <button type="button" id="btnAddPayment" data-toggle="modal"
                                  data-target="#addPaymentModal"
                                  class="btn font-titillium fw-500 py-1 new-ok-btn float-right"
                                  style="width: 120px;">Add Payment</button>
                          </div>
                      </div>
                  </div>
              </div>

          </div>

          {{-- Vacatin Tab --}}

          <div class="tab-pane fade show active" id="nav-vacations-client-edit" role="tabpanel"
              aria-labelledby="nav-vacations-tab-client-edit">
              <div class="block new-block position-relative mt-3">
                  <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
                      <div class="row">
                          <div class="col-sm-12">
                              <h5 class="titillium-web-black mb-3 text-purpule">Vacations</h5>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style">
                                  <div class="table-responsive small-box small-box-395 small-box-no-arrow">
                                      <table class="table table-sm table-striped align-middle mb-0 vacationTable">
  <thead>
    <th class="py-2 border-0 pl-2" width="3%"></th>  <!-- drag -->
    <th class="py-2 border-0 pl-2 text-center" width="3%"></th> <!-- status -->
    <th class="py-2 border-0 pl-2" width="30%">
      <h6 class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary">Student</h6>
    </th>
    <th class="py-2 border-0 pl-2" width="25%">
      <h6 class="font-titillium text-table-head mb-0 fw-700 border-right border-secondary">Subject</h6>
    </th>
    <th class="py-2 border-0 pl-2" width="30%">
      <h6 class="font-titillium text-table-head mb-0 fw-700">Date Range</h6>
    </th>
    <th class="py-2 border-0 pl-2 text-center" width="3%">
      <h6 class="font-titillium text-table-head mb-0 fw-700">Planned</h6>
    </th>
    <th class="py-2 border-0 pl-2" width="50"></th> <!-- actions -->
  </thead>
  <tbody></tbody>
</table>

                                      <div
                                          class="font-titillium text-darkgrey fw-400 mb-0 text-center vacationTable-empty py-2">
                                          No details found</div>
                                  </div>
                                  <div class="vacation-toast-added" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line added
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="vacation-toast-added"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>

                                  <div class="vacation-toast-updated" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line updated
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="vacation-toast-updated"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>

                                  <div class="vacation-toast-recovered" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line recovered
                                                  successfully!</span>
                                          </div>
                                          <button type="button" data-section="vacation-toast-recovered"
                                              class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                              <i class="fa-light fa-xmark"></i>
                                          </button>
                                      </div>
                                  </div>

                                  <div class="vacation-toast-deleted" role="status" aria-live="polite">
                                      <div class="d-flex align-items-center justify-content-between">
                                          <div class="d-flex align-items-center">
                                              <i class="fa-light fa-circle-check mr-2"></i>
                                              <span class="font-titillium fs-14 text-darkgrey">Line deleted</span>
                                          </div>
                                          <div class="d-flex align-items-center">
                                              <button type="button"
                                                  class="btn text-darkgrey btn-undo undo-delete-vacation font-titillium fs-14 mr-2"
                                                  data-action="undo">
                                                  Undo
                                              </button>
                                              <button type="button" data-section="vacation-toast-deleted"
                                                  class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                                  <i class="fa-light fa-xmark"></i>
                                              </button>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <button type="button" id="btnAddVacation" data-toggle="modal"
                                  data-target="#addVacationModal"
                                  class="btn font-titillium fw-500 py-1 new-ok-btn float-right"
                                  style="width: 120px;">Add Vacation</button>
                          </div>
                      </div>
                  </div>
              </div>

          </div>

          {{-- Comments Tab --}}

          <div class="tab-pane fade show active" id="nav-comments-client-edit" role="tabpanel"
              aria-labelledby="nav-details-tab-client-edit">
              <div class="block new-block position-relative mt-3">
                  <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
                      <div class="row">
                          <div class="col-sm-12">
                              <h5 class="titillium-web-black mb-3 text-purpule">Comments</h5>
                          </div>
                          <div class="row col-12" id="commentBlock">
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          {{-- Attachments Tab --}}

          <div class="tab-pane fade show active" id="nav-attachments-client-edit" role="tabpanel"
              aria-labelledby="nav-details-tab-client-edit">

              <div class="block new-block position-relative mt-3">
                  <div class="block-content py-0" style="padding-left: 30px;padding-right: 30px;">
                      <div class="row">
                          <div class="col-sm-12">
                              <h5 class="titillium-web-black mb-3 text-purpule">Attachments</h5>
                          </div>
                          <div class="row col-12" id="attachmentBlock">
                          </div>
                      </div>
                  </div>
              </div>

          </div>


      </div>
      <!-- Edit Purchasing Modal -->
      <div class="modal fade" id="contractDetailModal" tabindex="-1" role="dialog"
          aria-labelledby="contractDetailModalLabel" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
              <div class="modal-content">
                  <form id="contractDetailForm" class="mb-0" method="POST" action="update-detail-contract">
                      @csrf
                      <input type="hidden" id="editRowIndex" value="">
                      <div class="modal-header align-items-center border-0 position-relative">
                          <h5 class="modal-title font-titillium text-primary fs-20 fw-800" id="contractDetailLabel">
                              Detail Entry</h5>
                          <button type="button" class="close" data-dismiss="modal">
                              <i class="fa-solid fa-circle-xmark"></i>
                          </button>
                          {{-- Validation Toast --}}
                          <div class="form-validation-toast" style="bottom: 25px;right: 65px;">
                              <div class="d-flex align-items-center">
                                  <i class="fa-light fa-triangle-exclamation text-orange fs-16 mr-2"></i> <span
                                      class="font-titillium fs-14 text-darkgrey">
                                      Field validation failed.
                                  </span>
                              </div>
                          </div>
                      </div>
                      <div class="modal-body row py-0">
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-radio-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Type</h6>
                                  <div class="d-flex align-items-center pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-file-contract text-grey fs-18 constant-icon"></i>
                                      <div class="d-flex w-100 justify-content-between">
                                          <div class="contract_type_button ">
                                              <input type="radio" id="SFT" data-short="SFT"
                                                  name="contract_type_line" value="Software Support" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="SFT">Software Support</label>
                                          </div>
                                          <div class="contract_type_button">
                                              <input type="radio" id="HDW" data-short="HDW"
                                                  name="contract_type_line" value="Hardware Support" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2  label1"
                                                  for="HDW">Hardware Support</label>
                                          </div>
                                          <div class="contract_type_button">
                                              <input type="radio" id="SUB" data-short="SUB"
                                                  name="contract_type_line" value="Subscription" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="SUB"> Subscription</label>
                                          </div>
                                          <div class="contract_type_button">
                                              <input type="radio" id="MSP" data-short="MSP"
                                                  name="contract_type_line" value="Other" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="MSP"> MSP</label>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Quantity</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-input-numeric text-grey fs-18 constant-icon"></i>
                                      <input type="number"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="qty" placeholder=" Enter Quantity">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-6">

                          </div>
                          <div class="col-sm-3">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Cost</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-calendar-range text-grey fs-18 constant-icon"></i>
                                      <input type="number"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="msrp" step="0.01" placeholder="0.00">
                                  </div>
                              </div>
                          </div>
                          {{-- <div class="col-sm-6">
                              <div class="custom-dropdown assets-custom-dropdown multi-select-dropdown border p-2 mb-3 border-style"
                                  data-selected-ids="">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Assets</h6>

                                  <div class="dropdown-display d-flex align-items-center justify-content-between pb-1 pl-1 pt-0">
                                      <div class="d-flex align-items-center">
                                          <i class="fa-light fa-people-arrows text-grey fs-18"></i>
                                          <span
                                              class="selected-value font-titillium text-placeholder fw-300 mb-0 ml-2">Select
                                              Assets</span>
                                      </div>
                                      <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20"></i>
                                  </div>

                                  <ul class="dropdown-options" id="assets">
                                      <!-- Search bar -->
                                      <li class="search-option p-0 mb-2 mt-1">
                                          <input type="text" class="dropdown-search text-darkgrey fw-600"
                                              placeholder="Search asset..." />
                                      </li>

                                      <!-- Select all / Deselect all -->
                                      <div class="multi-actions d-flex justify-content-between mb-2 px-1">
                                          <button type="button"
                                              class="btn select-all font-titillium fw-300 w-50 new-ok-btn fs-18 mr-1">Select
                                              All</button>
                                          <button type="button"
                                              class="btn deselect-all font-titillium fw-300 w-50 new-ok-btn fs-18 ml-1">Deselect
                                              All</button>
                                      </div>

                                      <!-- Options -->
                                      <?php
                                      //   $client_id = 'from above selected client';
                                      
                                      //   $asset_qry = DB::select("select *,( SELECT row_number FROM (
                                      //                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             SELECT   id,@curRow := @curRow + 1 AS row_number
                                      //                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             FROM (
                                      //                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 SELECT * FROM assets  where is_deleted=0
                                      //                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 ORDER BY id ASC
                                      //                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             ) l
                                      //                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             JOIN (
                                      //                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 SELECT @curRow := 0
                                      //                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             ) r
                                      //                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         ) t where t.id=a.id limit 1) as rownumber from assets as a where a.is_deleted=0 and HasWarranty=1 and a.client_id='$client_id' and  AssetStatus=1   order by a.sn asc");
                                      ?>
                                      @foreach ($asset_qry as $a)
                                          @if ($a->asset_type === 'physical')
                                              <li data-value="{{ $a->id }}"
                                                  data-hostname="{{ $a->hostname }}"
                                                  data-type="{{ $a->asset_type == 'physical' ? 'P' : 'V' }}"
                                                  data-assettype="{{ $a->asset_type }}"
                                                  data-fqdn="{{ $a->fqdn }}" data-sn="{{ $a->sn }}"
                                                  data-type="{{ $a->asset_type }}">
                                                  <label class="d-flex align-items-center m-0">
                                                      <input type="checkbox" class="mr-2"
                                                          value="{{ $a->id }}">
                                                      <span>{{ $a->sn }} [{{ $a->hostname }}]</span>
                                                  </label>
                                              </li>
                                          @else
                                              <li data-value="{{ $a->id }}"
                                                  data-hostname="{{ $a->hostname }}"
                                                  data-type="{{ $a->asset_type == 'physical' ? 'P' : 'V' }}"
                                                  data-assettype="{{ $a->asset_type }}"
                                                  data-fqdn="{{ $a->fqdn }}" data-sn="{{ $a->sn }}"
                                                  data-type="{{ $a->asset_type }}">
                                                  <label class="d-flex align-items-center m-0">
                                                      <input type="checkbox" class="mr-2"
                                                          value="{{ $a->id }}">
                                                      <span>{{ $a->hostname }}</span>
                                                  </label>
                                              </li>
                                          @endif
                                      @endforeach

                                  </ul>
                              </div>
                          </div> --}}

                          <!--             <div class="col-sm-6">
                <div class="border p-2 mb-3 border-style edit-border">
                    <h6 class="font-titillium text-grey mb-2 fw-700">PN #</h6>
                    <div class="d-flex pt-1">
                        <i class="fa-light fa-calendar-range text-grey fs-18 constant-icon"></i>
                        <input type="text" class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field" id="pn_no" placeholder=" Enter PN Number">
                    </div>
                </div>
            </div> -->


                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Description</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-pencil-line text-grey fs-18 constant-icon"></i>
                                      <textarea rows="3"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field small-box-no-arrow"
                                          id="detail_comments" placeholder="Enter Description"></textarea>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer border-0 pt-0">
                          <button type="button" id="saveContractDetail"
                              class="btn font-titillium fw-500 py-1 new-ok-btn">OK</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>

      <!-- Add/Edit Modal -->
      <!-- Contract Detail Modal -->
      <div class="modal fade" id="contractDet ailModal" tabindex="-1" role="dialog"
          aria-labelledby="contractDetailLabel" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
              <div class="modal-content border-0 rounded-2xl shadow-sm">

                  <div class="modal-header border-0 pb-0">
                      <h5 class="modal-title fw-700 text-primary font-titillium" id="contractDetailLabel">Add Contract
                          Detail</h5>
                      <button type="button" class="close fs-24 text-muted" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>

                  <div class="modal-body pt-2">
                      <form id="contractDetailForm">
                          <input type="hidden" id="editRowIndex">

                          <div class="form-row">
                              <!-- Quantity -->
                              <div class="form-group col-md-2">
                                  <label class="font-titillium fw-600">Qty <span class="text-danger">*</span></label>
                                  <input type="number" id="qty" class="form-control" placeholder="0"
                                      min="1">
                              </div>

                              <!-- PN No -->
                              <div class="form-group col-md-3">
                                  <label class="font-titillium fw-600">PN No <span
                                          class="text-danger">*</span></label>
                                  <input type="text" id="pn_no" class="form-control"
                                      placeholder="Enter PN No">
                              </div>

                              <!-- Contract Type -->
                              <div class="form-group col-md-3">
                                  <label class="font-titillium fw-600">Contract Type</label>
                                  <select id="contract_type_line" class="form-control">
                                      <option value="">Select Type</option>
                                      <option value="Standard">Standard</option>
                                      <option value="Premium">Premium</option>
                                      <option value="Extended">Extended</option>
                                  </select>
                              </div>

                              <!-- MSRP -->
                              <div class="form-group col-md-4">
                                  <label class="font-titillium fw-600">Cost (MSRP)</label>
                                  <input type="number" id="msrp" class="form-control" step="0.01"
                                      placeholder="0.00">
                              </div>
                          </div>

                          <!-- Description -->
                          <div class="form-group">
                              <label class="font-titillium fw-600">Description / Comments</label>
                              <textarea id="detail_comments" rows="2" class="form-control" placeholder="Enter details"></textarea>
                          </div>

                          <!-- Multi-select Asset Dropdown -->
                          {{-- <div class="form-group position-relative multi-select-dropdown">
                              <label class="font-titillium fw-600">Assign Assets</label>
                              <div class="dropdown-toggle form-control d-flex justify-content-between align-items-center"
                                  data-toggle="dropdown">
                                  <span class="selected-value text-muted">Select Assets</span>
                                  <i class="fa-light fa-xmark clear-icon d-none text-danger ml-2"
                                      style="cursor:pointer;"></i>
                              </div>
                              <ul class="dropdown-menu w-100 border mt-1 shadow-sm p-2"
                                  style="max-height: 250px; overflow-y: auto;">
                                  @foreach ($asset_qry as $a)
                                      <li data-value="{{ $a->id }}" data-sn="{{ $a->sn }}"
                                          data-hostname="{{ $a->hostname }}" data-type="{{ $a->asset_type }}"
                                          class="mb-1">
                                          <label class="d-flex align-items-center m-0">
                                              <input type="checkbox" class="mr-2" value="{{ $a->id }}">
                                              <span>{{ $a->sn }} [{{ $a->hostname }}]</span>
                                          </label>
                                      </li>
                                  @endforeach
                              </ul>
                          </div> --}}

                      </form>
                  </div>

                  <div class="modal-footer border-0 pt-0">
                      <button type="button" class="btn btn-light px-4" data-dismiss="modal">Cancel</button>
                      <button type="button" id="saveContractDetail" class="btn btn-primary px-4">Save</button>
                  </div>

              </div>
          </div>
      </div>



      <div class="modal fade" id="addEmailModal" tabindex="-1" role="dialog" aria-labelledby="addEmailModalLabel"
          aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <form id=" " class="mb-0" method="POST" action=" ">
                      @csrf
                      <div class="modal-header align-items-center border-0">
                          <div>
                              <h5 class="modal-title font-titillium text-primary fs-20 fw-900">Email Notification</h5>
                              <span class="font-titillium text-darkgrey fs-15 fw-300"> Enter an email address for
                                  contract renewal notifications</span>
                          </div>
                          <button type="button" class="close" data-dismiss="modal">
                              <i class="fa-solid fa-circle-xmark"></i>
                          </button>
                      </div>
                      <div class="modal-body py-0">
                          <input type="hidden" id="contract_id" name="contract_id">
                          <div class="border p-2 mb-3 border-style edit-border">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Email Address</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-envelope text-grey fs-18 constant-icon"></i>
                                  <input type="email"
                                      class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                      id="email_id" placeholder="Enter Email Address" value="">
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer border-0 pt-0">
                          <button type="button" id="add_email"
                              class="btn font-titillium fw-500 py-1 new-ok-btn">OK</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>

      <div class="modal fade" id="AddNewCurrencyModal" tabindex="-1" role="dialog"
          aria-labelledby="AddNewCurrencyModalLabel" aria-hidden="true" data-dropdown="currency-dropdown" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <form id=" " class="mb-0" method="POST" action=" ">
                      @csrf
                      <div class="modal-header align-items-center border-0">
                          <div>
                              <h5 class="modal-title font-titillium text-primary fs-20 fw-900">Add New Currency</h5>
                          </div>
                          <button type="button" class="close" data-dismiss="modal">
                              <i class="fa-solid fa-circle-xmark"></i>
                          </button>
                      </div>
                      <div class="modal-body py-0">
                          <div class="border p-2 mb-3 border-style edit-border">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Currency</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-currency-sign text-grey fs-18 constant-icon"></i>
                                  <input type="text"
                                      class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                      name="new_value" placeholder="Enter Currency" maxlength="3" value="">
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer border-0 pt-0">
                          <button type="button" id="NewCurrencySave"
                              class="btn font-titillium fw-500 py-1 new-ok-btn addable-save-btn">OK</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>

      <div class="modal fade" id="AddNewManageByModal" tabindex="-1" role="dialog"
          aria-labelledby="AddNewManageByModalLabel" aria-hidden="true" data-dropdown="managed-by-dropdown" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <form id=" " class="mb-0" method="POST" action=" ">
                      @csrf
                      <div class="modal-header align-items-center border-0">
                          <div>
                              <h5 class="modal-title font-titillium text-primary fs-20 fw-900">Add New Managed</h5>
                          </div>
                          <button type="button" class="close" data-dismiss="modal">
                              <i class="fa-solid fa-circle-xmark"></i>
                          </button>
                      </div>
                      <div class="modal-body py-0">
                          <div class="border p-2 mb-3 border-style edit-border">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Managed By</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-crosshairs-simple text-grey fs-18 constant-icon"></i>
                                  <input type="text"
                                      class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                      name="new_value" placeholder="Enter Managed By" value="">
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer border-0 pt-0">
                          <button type="button"
                              class="btn font-titillium fw-500 py-1 new-ok-btn addable-save-btn">OK</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>

      <div class="modal fade" id="AddNewPNModal" tabindex="-1" role="dialog" aria-labelledby="AddNewPNModalLabel"
          aria-hidden="true" data-dropdown="pn-no-dropdown" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <form id=" " class="mb-0" method="POST" action=" ">
                      @csrf
                      <div class="modal-header align-items-center border-0">
                          <div>
                              <h5 class="modal-title font-titillium text-primary fs-20 fw-900">Add New PN #</h5>
                          </div>
                          <button type="button" class="close" data-dismiss="modal">
                              <i class="fa-solid fa-circle-xmark"></i>
                          </button>
                      </div>
                      <div class="modal-body py-0">
                          <div class="border p-2 mb-3 border-style edit-border">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">PN #</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-input-numeric text-grey fs-18 constant-icon"></i>
                                  <input type="text"
                                      class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field capped-field"
                                      name="new_value" placeholder="Enter PN #" value="">
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer border-0 pt-0">
                          <button type="button"
                              class="btn font-titillium fw-500 py-1 new-ok-btn addable-save-btn">OK</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>



      <!-- Student Modals -->
      <div class="modal fade" id="addStudentModal" tabindex="-1" role="dialog"
          aria-labelledby="addStudentModalLabel" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <form id="editDistributionForm" class="mb-0" method="POST"
                      action="update-distribution-contract">
                      @csrf
                      <div class="modal-header align-items-center border-0 position-relative">
                          <div class="d-flex flex-column">
                              <h5 class="modal-title font-titillium text-primary fs-20 fw-800">Add Student</h5>
                              <small class="font-titillium">Enter a new student</small>
                          </div>
                          <button type="button" class="close" data-dismiss="modal">
                              <i class="fa-solid fa-circle-xmark"></i>
                          </button>
                          {{-- Validation Toast --}}
                          <div class="form-validation-toast">
                              <div class="d-flex align-items-center">
                                  <i class="fa-light fa-triangle-exclamation text-orange fs-16 mr-2"></i> <span
                                      class="font-titillium fs-14 text-darkgrey">
                                      Field validation failed.
                                  </span>
                              </div>
                          </div>
                      </div>
                      <div class="modal-body py-0">
                        <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Student ID</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-input-numeric text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="new_student_id" placeholder="Enter student's ID">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Student Name</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-graduation-cap text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="new_student_name" placeholder="Enter student's name">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border calendar-expandable bg-white"
                                  id="startDateContainer">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Start Date</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-calendar-day text-grey fs-18 constant-icon"></i>
                                      <div class="date-input-area flex-grow-1 position-relative">
                                          <input type="text"
                                              class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                              id="student_start_date" name="student_start_date"
                                              placeholder="Select date" readonly>
                                          <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20 position-absolute"
                                              id="clear_start_date"
                                              style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                      </div>
                                  </div>
                                  <div class="inline-calendar-container"></div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-radio-border edit-border">
                                  <h6 class="font-titillium text-grey mb-1 fw-700 pl-1">Subjects</h6>
                                  <div class="d-flex align-items-center pb-1 pl-1 pt-0" id="add_subjects">
                                      <i class="fa-light fa-seal text-grey fs-18 constant-icon"></i>
                                      <div class="d-flex">
                                          <div class="radio_button ">
                                              <input type="checkbox" id="subject_math" name="subject"
                                                  value="1" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="subject_math">Math</label>
                                          </div>
                                          <div class="radio_button">
                                              <input type="checkbox" id="subject_reading" name="subject"
                                                  value="2" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="subject_reading">Reading</label>
                                          </div>
                                          <div class="radio_button"> <input type="checkbox" id="subject_efl"
                                                  name="subject" value="3" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="subject_efl"> EFL</label>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-5">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Amount</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-money-bill-1-wave text-grey fs-18"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="amount" value="135.00" inputmode="decimal" autocomplete="off">
                                  </div>
                              </div>
                          </div>


                      </div>
                      <div class="modal-footer border-0 pt-0">
                          <button type="button" id="add_student"
                              class="btn font-titillium fw-500 py-1 new-ok-btn">OK</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
      <div class="modal fade" id="editStudentModal" tabindex="-1" role="dialog"
          aria-labelledby="editStudentModalLabel" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <form id="editDistributionForm" class="mb-0" method="POST"
                      action="update-distribution-contract">
                      @csrf
                      <div class="modal-header align-items-center border-0 position-relative">
                          <div class="d-flex flex-column">
                              <h5 class="modal-title font-titillium text-primary fs-20 fw-800">Edit Student</h5>
                              <small class="font-titillium">Edit student information</small>
                          </div>
                          <button type="button" class="close" data-dismiss="modal">
                              <i class="fa-solid fa-circle-xmark"></i>
                          </button>
                          {{-- Validation Toast --}}
                          <div class="form-validation-toast">
                              <div class="d-flex align-items-center">
                                  <i class="fa-light fa-triangle-exclamation text-orange fs-16 mr-2"></i> <span
                                      class="font-titillium fs-14 text-darkgrey">
                                      Field validation failed.
                                  </span>
                              </div>
                          </div>
                      </div>
                      <input type="hidden" id="studentIndex">
                      <div class="modal-body py-0">
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Student ID</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-input-numeric text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="student_id_edit" placeholder="Enter student's ID">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Student Name</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-graduation-cap text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="student_name_edit" placeholder="Enter student's name">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border calendar-expandable bg-white"
                                  id="startDateContainerEdit">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Start Date</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-calendar-day text-grey fs-18 constant-icon"></i>
                                      <div class="date-input-area flex-grow-1 position-relative">
                                          <input type="text"
                                              class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                              id="student_start_date_edit" name="student_start_date_edit"
                                              placeholder="Select date" readonly>
                                          <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20 position-absolute"
                                              id="clear_start_date_edit"
                                              style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                      </div>
                                  </div>
                                  <div class="inline-calendar-container"></div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-radio-border">
                                  <h6 class="font-titillium text-grey mb-1 fw-700 pl-1">Subjects</h6>
                                  <div class="d-flex align-items-center pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-seal text-grey fs-18 constant-icon"></i>
                                      <div class="d-flex">
                                          <div class="radio_button ">
                                              <input type="checkbox" id="subject_math_edit" name="subject_edit"
                                                  value="1" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="subject_math_edit">Math</label>
                                          </div>
                                          <div class="radio_button">
                                              <input type="checkbox" id="subject_reading_edit" name="subject_edit"
                                                  value="2" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="subject_reading_edit">Reading</label>
                                          </div>
                                          <div class="radio_button"> <input type="checkbox" id="subject_efl_edit"
                                                  name="subject_edit" value="3" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="subject_efl_edit"> EFL</label>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-5">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Amount</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-money-bill-1-wave text-grey fs-18"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="amount_edit" value="135.00" inputmode="decimal"
                                          autocomplete="off">
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer border-0 pt-0">
                          <button type="button" id="update_student"
                              class="btn font-titillium fw-500 py-1 new-ok-btn">OK</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
      {{-- Payment Modals --}}
      <div class="modal fade" id="studentStatusDateModal" tabindex="-1" role="dialog"
          aria-labelledby="studentStatusDateModalLabel" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <div class="modal-header align-items-center border-0">
                      <h5 class="modal-title font-titillium fw-800 text-header-blue" id="studentStatusDateModalLabel"
                          style="font-size: 18pt;">Deactivate Student</h5>
                      <button type="button" class="close" data-dismiss="modal">
                          <i class="fa-solid fa-circle-xmark"></i>
                      </button>
                  </div>
                  <div class="modal-body py-0">
                      <input type="hidden" id="student_status_modal_index">
                      <input type="hidden" id="student_status_modal_action">
                      <div class="border p-2 mb-3 border-style edit-border calendar-expandable bg-white"
                          id="studentStatusDateContainer">
                          <h6 class="font-titillium text-grey mb-2 fw-700 pl-1" id="studentStatusDateLabel">End Date</h6>
                          <div class="d-flex pb-1 pl-1 pt-0">
                              <i class="fa-light fa-envelope text-grey fs-18 constant-icon"></i>
                              <div class="date-input-area flex-grow-1 position-relative">
                                  <input type="text"
                                      class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                      id="student_status_date" placeholder="Select date" readonly>
                                  <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20 position-absolute"
                                      id="clear_student_status_date"
                                      style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                              </div>
                          </div>
                          <div class="inline-calendar-container"></div>
                      </div>
                  </div>
                  <div class="modal-footer border-0 pt-0" style="justify-content: space-evenly;">
                      <button type="button" class="btn ok-btn btn-primary" id="confirmStudentStatusDateBtn">OK</button>
                  </div>
              </div>
          </div>
      </div>
      <div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog"
          aria-labelledby="addPaymentModalLabel" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <form id="editDistributionForm" class="mb-0" method="POST"
                      action="update-distribution-contract">
                      @csrf
                      <div class="modal-header align-items-center border-0 position-relative">
                          <div class="d-flex flex-column">
                              <h5 class="modal-title font-titillium text-primary fs-20 fw-800">Add Payment</h5>
                              <small class="font-titillium">Enter a new payment</small>
                          </div>
                          <button type="button" class="close" data-dismiss="modal">
                              <i class="fa-solid fa-circle-xmark"></i>
                          </button>
                          {{-- Validation Toast --}}
                          <div class="form-validation-toast">
                              <div class="d-flex align-items-center">
                                  <i class="fa-light fa-triangle-exclamation text-orange fs-16 mr-2"></i> <span
                                      class="font-titillium fs-14 text-darkgrey">
                                      Field validation failed.
                                  </span>
                              </div>
                          </div>
                      </div>
                      <div class="modal-body py-0">
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border calendar-expandable bg-white"
                                  id="paymentDateContainer">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Payment Date</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-calendar-day text-grey fs-18 constant-icon"></i>
                                      <div class="date-input-area flex-grow-1 position-relative">
                                          <input type="text"
                                              class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                              id="payment_date" name="payment_date" placeholder="Select date"
                                              value="{{ date('d-M-Y') }}" readonly>
                                          <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20 position-absolute"
                                              id="clear_payment_date"
                                              style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                      </div>
                                  </div>
                                  <div class="inline-calendar-container"></div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border calendar-expandable bg-white"
                                  id="kumonMonthContainer">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Kumon Month</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-calendar-days text-grey fs-18 constant-icon"></i>
                                      <div class="date-input-area flex-grow-1 position-relative">
                                          <input type="text"
                                              class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                              id="kumon_month" name="kumon_month" placeholder="Select month"
                                              value="{{ date('M-Y') }}" readonly>
                                          <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20 position-absolute"
                                              id="clear_kumon_month"
                                              style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                      </div>
                                  </div>
                                  <div class="inline-calendar-container"></div>
                              </div>
                          </div>

                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-radio-border edit-border">
                                  <h6 class="font-titillium text-grey mb-1 fw-700 pl-1">Payment Type</h6>
                                  <div class="d-flex align-items-center pb-1 pl-1 pt-0" id="add_subjects">
                                      <i class="fa-light fa-seal text-grey fs-18 constant-icon"></i>
                                      <div class="d-flex">
                                          <div class="radio_button ">
                                              <input type="radio" id="payment_cash" name="payment_type"
                                                  value="Cash" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="payment_cash">Cash</label>
                                          </div>
                                          <div class="radio_button">
                                              <input type="radio" id="payment_check" name="payment_type"
                                                  value="Check" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="payment_check">Check</label>
                                          </div>
                                          <div class="radio_button"> <input type="radio" id="payment_e_transfer"
                                                  name="payment_type" value="E-Transfer" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="payment_e_transfer">E-Transfer</label>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Reference No.</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-hashtag text-grey fs-18"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="reference_no" autocomplete="off">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Amount</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-money-bill-1-wave text-grey fs-18"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="payment_amount" inputmode="decimal" autocomplete="off">
                                  </div>
                              </div>
                          </div>


                      </div>
                      <div class="modal-footer border-0 pt-0">
                          <button type="button" id="add_payment"
                              class="btn font-titillium fw-500 py-1 new-ok-btn">OK</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>
      <div class="modal fade" id="editPaymentModal" tabindex="-1" role="dialog"
          aria-labelledby="editPaymentModalLabel" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <form id="editDistributionForm" class="mb-0" method="POST"
                      action="update-distribution-contract">
                      @csrf
                      <div class="modal-header align-items-center border-0 position-relative">
                          <div class="d-flex flex-column">
                              <h5 class="modal-title font-titillium text-primary fs-20 fw-800">Edit Payment</h5>
                              <small class="font-titillium">Edit Payment</small>
                          </div>
                          <button type="button" class="close" data-dismiss="modal">
                              <i class="fa-solid fa-circle-xmark"></i>
                          </button>
                          {{-- Validation Toast --}}
                          <div class="form-validation-toast">
                              <div class="d-flex align-items-center">
                                  <i class="fa-light fa-triangle-exclamation text-orange fs-16 mr-2"></i> <span
                                      class="font-titillium fs-14 text-darkgrey">
                                      Field validation failed.
                                  </span>
                              </div>
                          </div>
                      </div>
                      <div class="modal-body py-0">
                          <input type="hidden" id="paymentIndex">

                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border calendar-expandable bg-white"
                                  id="paymentDateContainerEdit">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Payment Date</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-calendar-day text-grey fs-18 constant-icon"></i>
                                      <div class="date-input-area flex-grow-1 position-relative">
                                          <input type="text"
                                              class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                              id="payment_date_edit" name="payment_date_edit"
                                              placeholder="Select date" value="{{ date('d-M-Y') }}" readonly>
                                          <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20 position-absolute"
                                              id="clear_payment_date_edit"
                                              style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                      </div>
                                  </div>
                                  <div class="inline-calendar-container"></div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border calendar-expandable bg-white"
                                  id="kumonMonthContainerEdit">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Kumon Month</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-calendar-days text-grey fs-18 constant-icon"></i>
                                      <div class="date-input-area flex-grow-1 position-relative">
                                          <input type="text"
                                              class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                              id="kumon_month_edit" name="kumon_month_edit"
                                              placeholder="Select month" value="{{ date('M-Y') }}" readonly>
                                          <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20 position-absolute"
                                              id="clear_kumon_month_edit"
                                              style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                      </div>
                                  </div>
                                  <div class="inline-calendar-container"></div>
                              </div>
                          </div>

                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-radio-border edit-border">
                                  <h6 class="font-titillium text-grey mb-1 fw-700 pl-1">Payment Type</h6>
                                  <div class="d-flex align-items-center pb-1 pl-1 pt-0" id="add_subjects">
                                      <i class="fa-light fa-seal text-grey fs-18 constant-icon"></i>
                                      <div class="d-flex">
                                          <div class="radio_button ">
                                              <input type="radio" id="payment_cash_edit"
                                                  name="payment_type_edit" value="Cash" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="payment_cash_edit">Cash</label>
                                          </div>
                                          <div class="radio_button">
                                              <input type="radio" id="payment_check_edit"
                                                  name="payment_type_edit" value="Check" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="payment_check_edit">Check</label>
                                          </div>
                                          <div class="radio_button"> <input type="radio"
                                                  id="payment_e_transfer_edit" name="payment_type_edit"
                                                  value="E-Transfer" />
                                              <label
                                                  class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                                  for="payment_e_transfer_edit">E-Transfer</label>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Reference No.</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-hashtag text-grey fs-18"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="reference_no_edit" autocomplete="off">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-12">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Amount</h6>
                                  <div class="d-flex pb-1 pl-1 pt-0">
                                      <i class="fa-light fa-money-bill-1-wave text-grey fs-18"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="payment_amount_edit" inputmode="decimal" autocomplete="off">
                                  </div>
                              </div>
                          </div>


                      </div>
                      <div class="modal-footer border-0 pt-0">
                          <button type="button" id="updte_payment"
                              class="btn font-titillium fw-500 py-1 new-ok-btn">OK</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>

      {{-- Vacation Modal --}}
      <div class="modal fade" id="addVacationModal" tabindex="-1" role="dialog"
          aria-labelledby="addVacationModalLabel" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <div class="modal-header align-items-center border-0 position-relative">
                      <div class="d-flex flex-column">
                          <h5 class="modal-title font-titillium text-primary fs-20 fw-800">Add Vacation</h5>
                          <small class="font-titillium">Enter a new vacation</small>
                      </div>
                      <button type="button" class="close" data-dismiss="modal">
                          <i class="fa-solid fa-circle-xmark"></i>
                      </button>
                      {{-- Validation Toast --}}
                      <div class="form-validation-toast">
                          <div class="d-flex align-items-center">
                              <i class="fa-light fa-triangle-exclamation text-orange fs-16 mr-2"></i> <span
                                  class="font-titillium fs-14 text-darkgrey">
                                  Field validation failed.
                              </span>
                          </div>
                      </div>
                  </div>
                  <div class="modal-body py-0">
                      <div class="col-sm-12">
                          <div class="custom-dropdown border p-2 mb-3 border-style edit-border student-dropdown" style="padding-bottom: 0px !important;"
                              data-selected-id="" data-selected-text="">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Student</h6>
                              <div
                                  class="dropdown-display d-flex align-items-center justify-content-between pb-1 pl-1 pt-0">
                                  <div class="d-flex align-items-center">
                                      <i class="fa-light fa-graduation-cap text-grey fs-18 constant-icon"></i>
                                      <span
                                          class="selected-value font-titillium text-placeholder fw-300 mb-0 ml-2 edit-field">Enter
                                          student's name</span>
                                  </div>
                                  <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20"></i>
                              </div>

                              <ul class="dropdown-options">
                                  <li class="search-option p-0 mb-2 mt-1">
                                      <input type="text" class="dropdown-search text-darkgrey fw-600"
                                          placeholder="" />
                                  </li>
                              </ul>
                          </div>
                          <input type="hidden" name="selected_student" id="selected_student">
                      </div>
                      <div class="col-sm-12">
                          <div class="border p-2 mb-3 border-style edit-radio-border edit-border">
                              <h6 class="font-titillium text-grey mb-1 fw-700 pl-1">Subject</h6>
                              <div class="d-flex align-items-center pb-1 pl-1 pt-0" id="add_subjects">
                                  <i class="fa-light fa-seal text-grey fs-18 constant-icon"></i>
                                  <div class="d-flex">
                                      <div class="radio_button ">
                                          <input type="checkbox" id="sub_math" name="sub_name"
                                              value="1" />
                                          <label class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                              for="sub_math">Math</label>
                                      </div>
                                      <div class="radio_button">
                                          <input type="checkbox" id="sub_reading" name="sub_name"
                                              value="2" />
                                          <label class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                              for="sub_reading">Reading</label>
                                      </div>
                                      <div class="radio_button">
                                          <input type="checkbox" id="sub_efl" name="sub_name"
                                              value="3" />
                                          <label class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                              for="sub_efl">EFL</label>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-sm-12">
                          <div class="border p-2 mb-3 border-style edit-border calendar-expandable bg-white"
                              id="vacationDateRangeContainer">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Date Range</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-calendar-range text-grey fs-18 constant-icon"></i>
                                  <div class="date-input-area flex-grow-1 position-relative">
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="vacation_date_range" name="vacation_date_range" readonly>
                                      <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20 position-absolute"
                                          id="clear_vacation_date_range"
                                          style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                  </div>
                              </div>
                              <div class="inline-calendar-container"></div>
                          </div>
                      </div>
                      <div class="col-sm-12">
                          <div class="border p-2 mb-3 border-style">
                              <div class="d-flex align-items-center justify-content-between mb-2">
                                  <h6 class="font-titillium text-grey mb-0 fw-700 pl-1">Take Work Home</h6>
                                  <button type="button"
                                      class="btn banner-icon affiliate-info ml-auto p-0 cursor-help"
                                      data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                      title="" data-original-title="">
                                      <i class="fa-light fa-circle-question text-grey fs-18 regular-icon"></i>
                                      <i class="fa-solid fa-circle-question text-primary fs-18 header-solid-icon"></i>
                                  </button>
                                  <div class="affiliate-tooltip font-titillium text-grey fw-300">
                                      <strong class="titillium-web-black fs-18 text-primary"
                                          style="line-height:1.6;">Take Work Home</strong><br>
                                      Enabling this option will send reminders to prepare work for this student
                                      during their vacation period
                                  </div>

                              </div>
                              <div class="d-flex align-items-center justify-content-between pb-1 pl-1 pt-0">
                                  <div class="custom-control custom-switch">
                                      <input type="checkbox" class="custom-control-input" id="customSwitch1"
                                          name="take_work_home">
                                      <label class="custom-control-label" for="customSwitch1"
                                          name="take_work_home">
                                          <h6 class="font-titillium text-grey fw-300 mb-0 ml-2 switch-text">
                                              Take work home not required
                                          </h6>
                                      </label>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-sm-12">
                          <div class="border p-2 mb-3 border-style d-none" id="reducedWorkloadWrapper">
                              <div class="d-flex align-items-center justify-content-between mb-2">
                                  <h6 class="font-titillium text-grey mb-0 fw-700 pl-1">Reduced Workload</h6>
                              </div>
                              <div class="d-flex align-items-center justify-content-between pb-1 pl-1 pt-0">
                                  <div class="custom-control custom-switch">
                                      <input type="checkbox" class="custom-control-input" id="reducedWorkloadSwitch"
                                          name="reduced_workload">
                                      <label class="custom-control-label" for="reducedWorkloadSwitch"
                                          name="reduced_workload">
                                          <h6 class="font-titillium text-grey fw-300 mb-0 ml-2 switch-text-load">
                                              Reduced workload not required
                                          </h6>
                                      </label>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-sm-12">
                          <div class="border p-2 mb-3 border-style edit-border">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Comment</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-comment text-grey fs-18 constant-icon"></i>
                                  <textarea rows="3"
                                      class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field small-box-no-arrow"
                                      id="vacation_comments" placeholder="Enter a comment for Kumon instructor"></textarea>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="modal-footer border-0 pt-0">
                      <button type="button" id="add_vacation"
                          class="btn font-titillium fw-500 py-1 new-ok-btn">OK</button>
                  </div>
              </div>
          </div>
      </div>
      <div class="modal fade" id="editVacationModal" tabindex="-1" role="dialog"
          aria-labelledby="editVacationModalLabel" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <div class="modal-header align-items-center border-0 position-relative">
                      <div class="d-flex flex-column">
                          <h5 class="modal-title font-titillium text-primary fs-20 fw-800">Edit Vacation</h5>
                          <small class="font-titillium">Edit vacation entry</small>
                      </div>
                      <button type="button" class="close" data-dismiss="modal">
                          <i class="fa-solid fa-circle-xmark"></i>
                      </button>
                      {{-- Validation Toast --}}
                      <div class="form-validation-toast">
                          <div class="d-flex align-items-center">
                              <i class="fa-light fa-triangle-exclamation text-orange fs-16 mr-2"></i> <span
                                  class="font-titillium fs-14 text-darkgrey">
                                  Field validation failed.
                              </span>
                          </div>
                      </div>
                  </div>
                  <input type="hidden" id="vacationIndex">

                  <div class="modal-body py-0">
                      <div class="col-sm-12">
                          <div class="custom-dropdown border p-2 mb-3 border-style edit-border student-dropdown"
                              data-selected-id="" data-selected-text="">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Student</h6>

                              <div
                                  class="dropdown-display d-flex align-items-center justify-content-between pb-1 pl-1 pt-0">
                                  <div class="d-flex align-items-center">
                                      <i class="fa-light fa-graduation-cap text-grey fs-18 constant-icon"></i>
                                      <span
                                          class="selected-value font-titillium text-placeholder fw-300 mb-0 ml-2 edit-field">Enter
                                          student's name</span>
                                  </div>
                                  <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20"></i>
                              </div>

                              <ul class="dropdown-options">
                                  <li class="search-option p-0 mb-2 mt-1">
                                      <input type="text" class="dropdown-search text-darkgrey fw-600"
                                          placeholder="" />
                                  </li>
                              </ul>
                          </div>
                          <input type="hidden" name="selected_student_edit" id="selected_student_edit">
                      </div>
                      <div class="col-sm-12">
                          <div class="border p-2 mb-3 border-style edit-radio-border edit-border">
                              <h6 class="font-titillium text-grey mb-1 fw-700 pl-1">Subject</h6>
                              <div class="d-flex align-items-center pb-1 pl-1 pt-0" id="add_subjects">
                                  <i class="fa-light fa-seal text-grey fs-18 constant-icon"></i>
                                  <div class="d-flex">
                                      <div class="radio_button ">
                                          <input type="checkbox" id="sub_math_edit" name="sub_name_edit"
                                              value="1" />
                                          <label class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                              for="sub_math_edit">Math</label>
                                      </div>
                                      <div class="radio_button">
                                          <input type="checkbox" id="sub_reading_edit" name="sub_name_edit"
                                              value="2" />
                                          <label class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                              for="sub_reading_edit">Reading</label>
                                      </div>
                                      <div class="radio_button">
                                          <input type="checkbox" id="sub_efl_edit" name="sub_name_edit"
                                              value="3" />
                                          <label class="btn radio-label py-1 font-titillium fs-14 fw-300 mx-2 label1"
                                              for="sub_efl_edit">EFL</label>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-sm-12">
                          <div class="border p-2 mb-3 border-style edit-border calendar-expandable bg-white"
                              id="vacationDateRangeContainerEdit">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Date Range</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-calendar-range text-grey fs-18 constant-icon"></i>
                                  <div class="date-input-area flex-grow-1 position-relative">
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="vacation_date_range_edit" name="vacation_date_range_edit" readonly>
                                      <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20 position-absolute"
                                          id="clear_vacation_date_range_edit"
                                          style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                  </div>
                              </div>
                              <div class="inline-calendar-container"></div>
                          </div>
                      </div>
                      <div class="col-sm-12">
                          <div class="border p-2 mb-3 border-style">
                              <div class="d-flex align-items-center justify-content-between mb-2">
                                  <h6 class="font-titillium text-grey mb-0 fw-700 pl-1">Take Work Home</h6>
                                  <button type="button"
                                      class="btn banner-icon affiliate-info ml-auto p-0 cursor-help"
                                      data-toggle="tooltip" data-trigger="hover" data-placement="top"
                                      title="" data-original-title="">
                                      <i class="fa-light fa-circle-question text-grey fs-18 regular-icon"></i>
                                      <i class="fa-solid fa-circle-question text-primary fs-18 header-solid-icon"></i>
                                  </button>
                                  <div class="affiliate-tooltip font-titillium text-grey fw-300">
                                      <strong class="titillium-web-black fs-18 text-primary"
                                          style="line-height:1.6;">Take Work Home</strong><br>
                                      Enabling this option will send reminders to prepare work for this student
                                      during their vacation period
                                  </div>

                              </div>
                              <div class="d-flex align-items-center justify-content-between pb-1 pl-1 pt-0">
                                  <div class="custom-control custom-switch">
                                      <input type="checkbox" class="custom-control-input" id="customSwitch1Edit"
                                          name="take_work_home_edit">
                                      <label class="custom-control-label" for="customSwitch1Edit"
                                          name="take_work_home_edit">
                                          <h6 class="font-titillium text-grey fw-300 mb-0 ml-2 switch-text">
                                              Take work home not required
                                          </h6>
                                      </label>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-sm-12">
                          <div class="border p-2 mb-3 border-style d-none" id="reducedWorkloadWrapperEdit">
                              <div class="d-flex align-items-center justify-content-between mb-2">
                                  <h6 class="font-titillium text-grey mb-0 fw-700 pl-1">Reduced Workload</h6>
                              </div>
                              <div class="d-flex align-items-center justify-content-between pb-1 pl-1 pt-0">
                                  <div class="custom-control custom-switch">
                                      <input type="checkbox" class="custom-control-input" id="reducedWorkloadSwitchEdit"
                                          name="reduced_workload_edit">
                                      <label class="custom-control-label" for="reducedWorkloadSwitchEdit"
                                          name="reduced_workload_edit">
                                          <h6 class="font-titillium text-grey fw-300 mb-0 ml-2 switch-text-load-edit">
                                              Reduced workload not required
                                          </h6>
                                      </label>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-sm-12">
                          <div class="border p-2 mb-3 border-style edit-border">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Comment</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-comment text-grey fs-18 constant-icon"></i>
                                  <textarea rows="3"
                                      class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field small-box-no-arrow"
                                      id="vacation_comments_edit" placeholder="Enter a comment for Kumon instructor"></textarea>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="modal-footer border-0 pt-0">
                      <button type="button" id="update_vacation"
                          class="btn font-titillium fw-500 py-1 new-ok-btn">OK</button>
                  </div>
              </div>
          </div>
      </div>

      <!-- Add Purchasing Modal -->
      {{-- <div class="modal fade" id="addPurchasingModal" tabindex="-1" role="dialog"
          aria-labelledby="editPurchasingModalLabel" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
              <div class="modal-content">
                  <form id="editPurchasingForm" class="mb-0" method="POST"
                      action="update-purchasing-contract">
                      @csrf
                      <div class="modal-header align-items-center border-0">
                          <h5 class="modal-title font-titillium text-primary fs-20 fw-800">Purchasing Entry</h5>
                          <button type="button" class="close" data-dismiss="modal">
                              <i class="fa-solid fa-circle-xmark"></i>
                          </button>
                      </div>
                      <div class="modal-body row py-0">
                          <input type="hidden" id="puchasing_index">
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700">Estimate No.</h6>
                                  <div class="d-flex pt-1">
                                      <i class="fa-light fa-square-quote text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="add_estimate_no" value="">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700">Sales Order No.</h6>
                                  <div class="d-flex pt-1">
                                      <i class="fa-light fa-input-numeric text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="add_sales_order_no" value="">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700">Invoice No.</h6>
                                  <div class="d-flex pt-1">
                                      <i class="fa-light fa-file-invoice text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="add_invoice_no" value="">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border calendar-expandable"
                                  id="invoiceDateContainer">
                                  <h6 class="font-titillium text-grey mb-2 fw-700">Invoice Date</h6>
                                  <div class="d-flex pt-1">
                                      <i class="fa-light fa-calendar-day text-grey fs-18 constant-icon"></i>
                                      <div class="date-input-area flex-grow-1 position-relative">
                                          <input type="text"
                                              class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                              id="add_invoice_date" value="" name="invoice_date"
                                              placeholder="Select date" readonly>
                                          <i class="fa-light fa-calendar-day clear-icon text-grey d-none fs-20 position-absolute"
                                              id="clear_invoice_date"
                                              style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                      </div>
                                  </div>
                                  <div class="inline-calendar-container"></div>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border">
                                  <h6 class="font-titillium text-grey mb-2 fw-700">PO No.</h6>
                                  <div class="d-flex pt-1">
                                      <i class="fa-light fa-file-contract text-grey fs-18 constant-icon"></i>
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="add_po_no" value="">
                                  </div>
                              </div>
                          </div>
                          <div class="col-sm-6">
                              <div class="border p-2 mb-3 border-style edit-border calendar-expandable"
                                  id="poDateContainer">
                                  <h6 class="font-titillium text-grey mb-2 fw-700">PO Date</h6>
                                  <div class="d-flex pt-1">
                                      <i class="fa-light fa-calendar-day text-grey fs-18 constant-icon"></i>
                                      <div class="date-input-area flex-grow-1 position-relative">
                                          <input type="text"
                                              class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                              id="add_po_date" value="" name="po_date"
                                              placeholder="Select date" readonly>
                                          <i class="fa-light fa-calendar-day clear-icon text-grey d-none fs-20 position-absolute"
                                              id="clear_po_date"
                                              style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                      </div>
                                  </div>
                                  <div class="inline-calendar-container"></div>
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer border-0 pt-0">
                          <button type="button" id="add_purchasing"
                              class="btn font-titillium fw-500 py-1 new-ok-btn">OK</button>
                      </div>
                  </form>
              </div>
          </div>
      </div> --}}
      <!-- Edit Purchasing Modal -->
      <div class="modal fade" id="editPurchasingModal" tabindex="-1" role="dialog"
          aria-labelledby="editPurchasingModalLabel" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
              <div class="modal-content">
                  <div class="modal-header align-items-center border-0 position-relative">
                      <h5 class="modal-title font-titillium text-primary fs-20 fw-800">Purchasing Entry</h5>
                      <button type="button" class="close" data-dismiss="modal">
                          <i class="fa-solid fa-circle-xmark"></i>
                      </button>
                      {{-- Validation Toast --}}
                      <div class="form-validation-toast" style="bottom: 25px;right: 65px;">
                          <div class="d-flex align-items-center">
                              <i class="fa-light fa-triangle-exclamation text-orange fs-16 mr-2"></i> <span
                                  class="font-titillium fs-14 text-darkgrey">
                                  Field validation failed.
                              </span>
                          </div>
                      </div>
                  </div>
                  <div class="modal-body row py-0">
                      <input type="hidden" id="puchasing_index">
                      <div class="col-sm-6">
                          <div class="border p-2 mb-3 border-style edit-border">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Estimate No.</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-square-quote text-grey fs-18 constant-icon"></i>
                                  <input type="text"
                                      class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field capped-field"
                                      id="edit_estimate_no" value="">
                              </div>
                          </div>
                      </div>
                      <div class="col-sm-6">
                          <div class="border p-2 mb-3 border-style edit-border">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Sales Order No.</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-input-numeric text-grey fs-18 constant-icon"></i>
                                  <input type="text"
                                      class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field capped-field"
                                      id="edit_sales_order_no" value="">
                              </div>
                          </div>
                      </div>
                      <div class="col-sm-6">
                          <div class="border p-2 mb-3 border-style edit-border">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Invoice No.</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-file-invoice text-grey fs-18 constant-icon"></i>
                                  <input type="text"
                                      class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field capped-field"
                                      id="edit_invoice_no" value="">
                              </div>
                          </div>
                      </div>
                      <div class="col-sm-6">
                          <div class="border p-2 mb-3 border-style edit-border calendar-expandable bg-white"
                              id="invoiceDateContainer">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">Invoice Date</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-calendar-day text-grey fs-18 constant-icon"></i>
                                  <div class="date-input-area flex-grow-1 position-relative">
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="edit_invoice_date" value="" name="invoice_date"
                                          placeholder="Select date" readonly>
                                      <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20 position-absolute"
                                          id="clear_invoice_date"
                                          style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                  </div>
                              </div>
                              <div class="inline-calendar-container"></div>
                          </div>
                      </div>
                      <div class="col-sm-6">
                          <div class="border p-2 mb-3 border-style edit-border">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">PO No.</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-file-contract text-grey fs-18 constant-icon"></i>
                                  <input type="text"
                                      class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field capped-field"
                                      id="edit_po_no" value="">
                              </div>
                          </div>
                      </div>
                      <div class="col-sm-6">
                          <div class="border p-2 mb-3 border-style edit-border calendar-expandable bg-white"
                              id="poDateContainer">
                              <h6 class="font-titillium text-grey mb-2 fw-700 pl-1">PO Date</h6>
                              <div class="d-flex pb-1 pl-1 pt-0">
                                  <i class="fa-light fa-calendar-day text-grey fs-18 constant-icon"></i>
                                  <div class="date-input-area flex-grow-1 position-relative">
                                      <input type="text"
                                          class="form-control font-titillium text-grey fw-300 mb-0 ml-2 border-0 p-0 fs-18 rounded-0 edit-field"
                                          id="edit_po_date" value="" name="po_date"
                                          placeholder="Select date" readonly>
                                      <i class="fa-light fa-circle-xmark clear-icon text-grey d-none fs-20 position-absolute"
                                          id="clear_po_date"
                                          style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
                                  </div>
                              </div>
                              <div class="inline-calendar-container"></div>
                          </div>
                      </div>
                  </div>
                  <div class="modal-footer border-0 pt-0">
                      <button type="button" id="add_purchasing"
                          class="btn font-titillium fw-500 py-1 new-ok-btn">OK</button>
                  </div>
              </div>
          </div>
      </div>

      <!-- Close Modal -->
      <div class="modal fade" id="CloseModal" tabindex="-1" role="dialog" aria-labelledby="CloseModalLabel"
          aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <form id="leaveForm" class="mb-0" method="POST" action="delete-comment-contract">
                      @csrf
                      <div class="modal-header align-items-center border-0">
                          <h5 class="modal-title font-titillium fw-800 text-header-blue" style="font-size: 18pt;">
                              Leave this page?</h5>
                      </div>
                      <div class="modal-body py-0">
                          <div class="d-flex align-items-center">
                              <i class="fa-light fa-triangle-exclamation text-warning fs-30 mr-2"></i>
                              <span class="font-titillium text-darkgrey" style="font-size: 14pt;">If you leave, your
                                  unsaved changes will be discarded.</span>
                          </div>
                          <hr>
                      </div>
                      <div class="modal-footer border-0 pt-0" style="justify-content: space-evenly;">
                          <button type="button" class="btn ok-btn btn-primary" data-dismiss="modal">Stay
                              Here</button>
                          <button type="button" id="viewReadBtn" data=""
                              class="btn cancel-btn">Leave</button>
                      </div>
                  </form>
              </div>
          </div>
      </div>

      <!-- Save Modal -->
      <div class="modal fade" id="SaveModal" tabindex="-1" role="dialog" aria-labelledby="SaveModalLabel"
          aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                  <div class="modal-header align-items-center border-0 px-4">
                      <h5 class="modal-title font-titillium fw-800 text-header-blue" style="font-size: 18pt;">Saving
                      </h5>
                  </div>
                  <div class="modal-body pt-0 pb-4 px-4">
                      <div class="d-flex align-items-center">
                          <i class="fa-light fa-gear-complex fa-spin text-darkgrey fs-30 mr-2"></i>
                          <span class="font-titillium text-darkgrey" style="font-size: 14pt;">Please wait while
                              saving Client ...</span>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <div class="modal fade" id="CommentModalAdd" tabindex="-1" role="dialog" data-backdrop="static"
          aria-labelledby="modal-block-large" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
              <div class="modal-content">
                  <div class="block  block-transparent mb-0">
                      <div class="block-header   ">
                          <span class="b e section-header font-titillium fw-600 fs-20 text-darkgrey">Comment</span>
                          <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <i class="fa fa-fw fa-times"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </button> -->
                          </div>
                      </div>
                      <div class="block-content pt-0 row">
                          <div class="col-sm-12 px-0">
                              <textarea class="form-control  " rows="5" required="" name="comment"></textarea>
                              <hr class="mb-1 mt-4">
                          </div>
                      </div>
                      <div class="modal-footer border-0 pb-4" style="justify-content: space-evenly;">
                          <button type="button" class="btn ok-btn btn-primary" id="CommentSave">Save</button>
                          <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="modal fade" id="editCommentModalAdd" tabindex="-1" role="dialog" data-backdrop="static"
          aria-labelledby="modal-block-large" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
              <div class="modal-content">
                  <div class="block  block-transparent mb-0">
                      <div class="block-header   ">
                          <span class="b e section-header font-titillium fw-600 fs-20 text-darkgrey">Comment</span>
                          <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <i class="fa fa-fw fa-times"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </button> -->
                          </div>
                      </div>
                      <div class="block-content pt-0 row">
                          <input type="hidden" name="comment_id_edit">
                          <div class="col-sm-12 px-0">
                              <textarea class="form-control  " rows="5" required="" name="comment_edit"></textarea>
                              <hr class="mb-1 mt-4">
                          </div>
                      </div>
                      <div class="modal-footer border-0 pb-4" style="justify-content: space-evenly;">
                          <button type="button" class="btn ok-btn btn-primary" id="CommentSaveEdit">Save</button>
                          <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                      </div>
                  </div>
              </div>
          </div>
      </div>
      <div class="modal fade" id="AttachmentModalAdd" tabindex="-1" role="dialog" data-backdrop="static"
          aria-labelledby="modal-block-large" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered modal-bac " role="document">
              <div class="modal-content">
                  <div class="block  block-transparent mb-0">
                      <div class="block-header   ">
                          <span
                              class="b e section-header font-titillium text-darkgrey fs-20 fw-600">Attachments</span>
                          <div class="block-options">
                              <!--   <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  <i class="fa fa-fw fa-times"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </button> -->
                          </div>
                      </div>
                      <div class="block-content pt-0 row">
                          <div class="col-sm-12    px-0">
                              <input type="file" class="  attachment" multiple="" style=""
                                  id="attachmentAdd" name="attachment" placeholder="">
                              <hr class="mb-1 mt-4">
                          </div>
                      </div>
                      <div class="modal-footer border-0 pb-4" style="justify-content: space-evenly;">
                          <button type="button" class="btn ok-btn btn-primary" id="AttachmentSave">Save</button>
                          <button type="button" class="btn cancel-btn" id="AttachmentClose"
                              data-dismiss="modal">Cancel</button>
                      </div>
                      <!-- <div class="modal-footer border-0 pt-0" style="justify-content: space-evenly;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <button type="button" id="updateCommentBtn" class="btn ok-btn btn-primary">Update</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              <button type="button" class="btn cancel-btn" data-dismiss="modal">Cancel</button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div> -->
                  </div>
              </div>
          </div>
      </div>
      <input type="hidden" name="user_iamge" id="user_iamge" value="{{ Auth::user()->user_image }}">
      <script>
        (function() {

              let trapEnabled = true;
              let pendingAction = null; // { type, target, dataAttr }

              const TRAP_CLICK_SELECTORS = `
                    .main-header a,
                    .dropdown-menu-right a,
                    .dropdown-item,
                    .bubble,
                    .btn-setting,
                    .viewContent,
                    .btn-add,
                    .page-link
                `;

              const TRAP_FORM_SELECTORS = `form:not(.allow-submit)`;

              /* ===========================
                 CLICK TRAP (capture phase)
              ============================ */
              window._editTrapNativeClickListener = function(e) {
                  if (!trapEnabled) return;

                  // Ignore clicks inside the loaded edit partial; trap only leave/navigation actions.
                  if (e.target.closest('#nav-main-contract-edit')) return;

                  const el = e.target.closest(
                      '.viewContent, .btn-setting, .main-header a, .bubble, .btn-add, .page-link, .nav-main-link, #logoutBtn, .dropdown-menu-right a'
                  );
                  if (!el) return;

                  e.preventDefault();
                  e.stopPropagation();

                  pendingAction = {
                      type: 'click',
                      target: el,
                      dataAttr: el.getAttribute('data') // save data attribute if present
                  };

                  $('#unsavedChangesModal').modal('show');
              };
              document.addEventListener('click', window._editTrapNativeClickListener, true);

              /* ===========================
                 FORM SUBMIT TRAP
              ============================ */
              window._editTrapNativeSubmitListener = function(e) {
                  const form = e.target;
                  if (!trapEnabled) return;
                  if ($(form).hasClass('allow-submit')) return;

                  e.preventDefault();

                  pendingAction = {
                      type: 'submit',
                      target: form
                  };
                  $('#unsavedChangesModal').modal('show');
              };
              document.addEventListener('submit', window._editTrapNativeSubmitListener, true);

              // Optional jQuery delegation for dynamically added forms
              $(document).on('submit.editTrap', TRAP_FORM_SELECTORS, function(e) {
                  if (!trapEnabled) return;

                  e.preventDefault();
                  e.stopPropagation();

                  pendingAction = {
                      type: 'submit',
                      target: this
                  };
                  $('#unsavedChangesModal').modal('show');
              });

              /* ===========================
                 CONFIRM EXIT (ALLOW ACTION)
              ============================ */
              $(document).on('click', '#confirmExit', function() {
                  if (!pendingAction) return;

                  const {
                      type,
                      target,
                      dataAttr
                  } = pendingAction;

                  // 1️⃣ Disable trap first
                  disableTrap();

                  // 2️⃣ Hide modal
                  $('#unsavedChangesModal').modal('hide');

                  // 3️⃣ Execute original action asynchronously
                  setTimeout(() => {

                      if (type === 'click') {

                          // LOGOUT
                          if (target.id === 'logoutBtn') {
                              $('#logout-form')
                                  .addClass('allow-submit')
                                  .trigger('submit');
                              return;
                          }

                          // viewContent (bubble)
                          if (dataAttr) {
                              const el = document.querySelector(`.viewContent[data="${dataAttr}"]`);
                              if (el) el.click(); // native click
                              return;
                          }

                          // MAIN MENU LINKS (anchors)
                          if (target.tagName === 'A' && target.href) {
                              trapEnabled = false; // 🔑 disable trap before navigation
                              window.location.href = target.href;
                              return;
                          }

                          // buttons / other elements
                          trapEnabled = false;
                          target.click(); // native click
                      } else if (type === 'submit') {
                          $(target)
                              .addClass('allow-submit')
                              .trigger('submit');
                      }

                  }, 0);

                  pendingAction = null;
              });


              /* ===========================
                 EXPLICIT SAFE ACTIONS
                 (Save / Cancel / View)
              ============================ */
              $(document).on('click.editTrap', '#viewReadBtn, .btn-save, .btn-save-continue, .btn-cancel', function() {
                  disableTrap();
              });

              /* ===========================
                 DISABLE TRAP FUNCTION
              ============================ */
              function disableTrap() {
                  trapEnabled = false;
                  pendingAction = null;

                  // remove jQuery delegated handlers
                  $(document).off('.editTrap');

                  // remove native capture-phase listeners
                  if (window._editTrapNativeClickListener) {
                      document.removeEventListener('click', window._editTrapNativeClickListener, true);
                      window._editTrapNativeClickListener = null;
                  }
                  if (window._editTrapNativeSubmitListener) {
                      document.removeEventListener('submit', window._editTrapNativeSubmitListener, true);
                      window._editTrapNativeSubmitListener = null;
                  }
              }

          })();
          (function() {

              /* ==============================
                 UTILITY: Prevent duplicate bind
              ============================== */
              function alreadyBound(el) {
                  if (!el || el.dataset.bound === '1') return true;
                  el.dataset.bound = '1';
                  return false;
              }

              /* ==============================
                 DECIMAL INPUT (2 DP)
              ============================== */
              function bindDecimalInput(id, defaultValue = null) {
                  const el = document.getElementById(id);
                  if (alreadyBound(el)) return;

                  el.addEventListener('input', function() {
                      let value = this.value.replace(/[^0-9.]/g, '');

                      const parts = value.split('.');
                      if (parts.length > 2) {
                          value = parts[0] + '.' + parts.slice(1).join('');
                      }

                      if (parts[1]?.length > 2) {
                          value = parts[0] + '.' + parts[1].substring(0, 2);
                      }

                      this.value = value;
                  });

                  if (defaultValue !== null) {
                      el.addEventListener('blur', function() {
                          if (this.value === '' || parseFloat(this.value) <= 0) {
                              this.value = defaultValue;
                          } else {
                              this.value = parseFloat(this.value).toFixed(2);
                          }
                      });
                  }
              }

              /* ==============================
                 POSTAL CODE (X9X 9X9)
              ============================== */
              function bindPostalCode(id) {
                  const el = document.getElementById(id);
                  if (alreadyBound(el)) return;

                  el.addEventListener('input', function() {
                      let value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                      if (value.length > 3) {
                          value = value.slice(0, 3) + ' ' + value.slice(3, 6);
                      }
                      this.value = value;
                  });
              }

              /* ==============================
                 EMAIL FILTER
              ============================== */
              function bindEmail(id) {
                  const el = document.getElementById(id);
                  if (alreadyBound(el)) return;

                  el.addEventListener('input', function() {
                      this.value = this.value.replace(/[^a-zA-Z0-9@.]/g, '');
                  });
              }

              /* ==============================
                 TELEPHONE (999-999-9999)
              ============================== */
              function bindTelephone(id) {
                  const el = document.getElementById(id);
                  if (alreadyBound(el)) return;

                  el.addEventListener('input', function() {
                      let value = this.value.replace(/\D/g, '');

                      if (value.length > 3 && value.length <= 6) {
                          value = value.slice(0, 3) + '-' + value.slice(3);
                      } else if (value.length > 6) {
                          value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
                      }

                      this.value = value;
                  });
              }

              /* ==============================
                 INITIALIZE (SAFE FOR AJAX)
              ============================== */
              function initChildPageInputs() {

                  // Amounts
                  bindDecimalInput('amount', '135.00');
                  bindDecimalInput('amount_edit');
                  bindDecimalInput('payment_amount');
                  bindDecimalInput('payment_amount_edit');

                  // Others
                  bindPostalCode('postal_code');
                  bindPostalCode('father_postal_code');
                  bindEmail('primary_email_address');
                  bindEmail('father_primary_email_address');
                  bindTelephone('telephone_no');
                  bindTelephone('father_telephone_no');
              }

              /* ==============================
                 AUTO INIT
                 Call again after AJAX load
              ============================== */
              initChildPageInputs();

              // Optional: expose for manual AJAX re-init
              window.initChildPageInputs = initChildPageInputs;

          })();
      </script>

      <script type="text/javascript">
          $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip({
                            html: true,
                        });
              $(document).off('.viewPage');
              $(document).off('.editPage');
              $(document).off('.viewPage');
              $(document).off('.addPage');
              $(document).off('.clonePage');

              $(document).on('keydown', '#addEmailModal', function(e) {
                  if (e.key === 'Enter') {
                      e.preventDefault(); // stop form submit
                      $('#add_email').trigger('click.clonePage'); // act like OK button
                  }
              });
              $(document).on('keydown', '#addAffiliateClient', function(e) {
                  // Check if Enter key
                  if (e.key === 'Enter') {

                      // Allow Enter in textarea / memo fields
                      if ($(e.target).is('textarea')) {
                          return true;
                      }

                      // Prevent default form submit
                      e.preventDefault();

                      // Trigger OK button
                      $('#add_affiliate').trigger('click.clonePage');
                  }
              });
              $(document).on('keydown', '#addDistributionModal', function(e) {
                  // Check if Enter key
                  if (e.key === 'Enter') {

                      // Allow Enter in textarea / memo fields
                      if ($(e.target).is('textarea')) {
                          return true;
                      }

                      // Prevent default form submit
                      e.preventDefault();

                      // Trigger OK button
                      $('#addDistributionModal .new-ok-btn').click();
                  }
              });
              $(document).on('keydown', '#editDistributionModal', function(e) {
                  // Check if Enter key
                  if (e.key === 'Enter') {

                      // Allow Enter in textarea / memo fields
                      if ($(e.target).is('textarea')) {
                          return true;
                      }

                      // Prevent default form submit
                      e.preventDefault();

                      // Trigger OK button
                      $('#editDistributionModal .new-ok-btn').click();
                  }
              });
              $(document).on('keydown', '#editPurchasingModal', function(e) {
                  // Check if Enter key
                  if (e.key === 'Enter') {

                      // Allow Enter in textarea / memo fields
                      if ($(e.target).is('textarea')) {
                          return true;
                      }

                      // Prevent default form submit
                      e.preventDefault();

                      // Trigger OK button
                      $('#editPurchasingModal .new-ok-btn').click();
                  }
              });
              $(document).on('keydown', '#contractDetailModal', function(e) {
                  // Check if Enter key
                  if (e.key === 'Enter') {

                      // Allow Enter in textarea / memo fields
                      if ($(e.target).is('textarea')) {
                          return true;
                      }

                      // Prevent default form submit
                      e.preventDefault();

                      // Trigger OK button
                      $('#contractDetailModal .new-ok-btn').click();
                  }
              });

              var content3_image = []
              setTimeout(() => {
                  initializeFilePond('.attachment');
              }, 200);
              let filePond;
              // Function to initialize FilePond for a specific element
              function initializeFilePond() {
                  filePond = FilePond.create(
                      document.querySelector('.attachment'), {
                          name: 'attachment',
                          allowMultiple: true,
                          allowImagePreview: true,

                          imagePreviewFilterItem: false,
                          imagePreviewMarkupFilter: false,

                          dataMaxFileSize: "2MB",



                          // server
                          server: {
                              process: {
                                  url: '{{ url('uploadNetworkAttachment') }}',
                                  method: 'POST',
                                  headers: {
                                      'x-customheader': 'Processing File'
                                  },
                                  onload: (response) => {

                                      response = response.replaceAll('"', '');
                                      content3_image.push(response);

                                      var attachemnts = $('input[name=attachment_array]').val()
                                      var attachment_array = attachemnts.split(',');
                                      attachment_array.push(response);
                                      $('input[name=attachment_array]').val(content3_image.join(','));

                                      return response;

                                  },
                                  onerror: (response) => {



                                      return response
                                  },
                                  ondata: (formData) => {
                                      window.h = formData;

                                      return formData;
                                  }
                              },
                              revert: (uniqueFileId, load, error) => {

                                  const formData = new FormData();
                                  formData.append("key", uniqueFileId);

                                  content3_image = content3_image.filter(function(ele) {
                                      return ele != uniqueFileId;
                                  });

                                  var attachemnts = $('input[name=attachment_array]').val()
                                  var attachment_array = attachemnts.split(',');
                                  attachment_array = attachment_array.filter(function(ele) {
                                      return ele != uniqueFileId;
                                  });

                                  $('input[name=attachment_array]').val(content3_image.join(','));


                                  fetch(`{{ url('revertContractAttachment') }}?key=${uniqueFileId}`, {
                                          method: "DELETE",
                                          body: formData,
                                      })
                                      .then(res => res.json())
                                      .then(json => {
                                          console.log(json);


                                          // Should call the load method when done, no parameters required

                                          load();

                                      })
                                      .catch(err => {
                                          console.log(err)
                                          // Can call the error method if something is wrong, should exit after
                                          error(err.message);
                                      })
                              },



                              remove: (uniqueFileId, load, error) => {
                                  // Should somehow send `source` to server so server can remove the file with this source
                                  content3_image = content3_image.filter(function(ele) {
                                      return ele != uniqueFileId;
                                  });


                                  // Should call the load method when done, no parameters required
                                  load();
                              },

                          }
                      }
                  );
              }

              function initializeStudentDropdown() {
                  // Initialize both dropdowns separately
                  $('.custom-dropdown.student-dropdown').each(function() {
                      const dropdown = $(this);
                      const optionsContainer = dropdown.find('.dropdown-options');
                      const selectedValue = dropdown.find('.selected-value');
                      const hiddenInput = dropdown.next('input[type="hidden"]');
                      const clearBtn = dropdown.find('.clear-icon');

                      // Get the hidden input name/id to identify which dropdown this is
                      const inputName = hiddenInput.attr('name') || hiddenInput.attr('id') || '';

                      // Clear existing options (except the search input)
                      optionsContainer.find('li:not(.search-option)').remove();

                      // Add students from students_array to dropdown
                      students_array.forEach(student => {
                          const option = $('<li></li>')
                              .attr('data-key', student.key)
                              .attr('data-value', student.student_name)
                              .text(student.student_name);
                          optionsContainer.append(option);
                      });

                      // Only set default for the first dropdown (selected_student)
                      if (inputName === 'selected_student' && students_array.length === 1) {
                          const onlyStudent = students_array[0];
                          selectedValue.text(onlyStudent.student_name).css('color', '#3f3f3f');
                          hiddenInput.val(onlyStudent.student_name);
                          clearBtn.removeClass('d-none');
                          dropdown.data('selected-id', onlyStudent.key);
                          dropdown.data('selected-text', onlyStudent.student_name);

                          // Mark the option as active
                          optionsContainer.find(`[data-value="${onlyStudent.student_name}"]`).addClass(
                              'active');
                      } else if (inputName === 'selected_student_edit') {
                          // For edit dropdown, always show placeholder even with one student
                          selectedValue.text('Enter student\'s name').css('color', '#999');
                          hiddenInput.val('');
                          clearBtn.addClass('d-none');
                      } else {
                          // For first dropdown with 0 or multiple students
                          if (students_array.length === 0) {
                              selectedValue.text('Enter student\'s name').css('color', '#999');
                              hiddenInput.val('');
                              clearBtn.addClass('d-none');
                          } else {
                              // Multiple students, show placeholder
                              selectedValue.text('Enter student\'s name').css('color', '#999');
                              hiddenInput.val('');
                              clearBtn.addClass('d-none');
                          }
                      }
                  });
                  bindStudentDropdownEvents();
              }

              function bindStudentDropdownEvents() {
                  $('.custom-dropdown.student-dropdown').each(function() {
                      const box = $(this);
                      if (box.hasClass('multi-select-dropdown')) return;

                      const options = box.find('.dropdown-options');
                      const selectedValue = box.find('.selected-value');
                      const hiddenInput = box.next('input[type="hidden"]');
                      const clearBtn = box.find('.clear-icon');
                      const searchInput = box.find('.dropdown-search');
                      const title = box.find('h6');
                      const icon = box.find('.constant-icon');

                      // Remove existing event handlers to avoid duplicates
                      options.off('click.studentOptions');

                      // Bind new event handler with delegation for dynamic options
                      options.on('click.studentOptions', 'li:not(.search-option)', function(e) {
                          e.stopPropagation();

                          const text = $(this).text();
                          const value = $(this).data('value');

                          console.log('Option clicked:', text, value);

                          selectedValue.text(text).css('color', '#3f3f3f');
                          hiddenInput.val(value);
                          clearBtn.removeClass('d-none');

                          // Mark option as active
                          options.find('li').removeClass('active');
                          $(this).addClass('active');

                          box.removeClass('open');
                          title.css('color', '');
                          icon.css('color', '');
                          searchInput.val('').trigger('keyup');

                          // Clear validation error
                          var borderDiv = box.closest('.edit-border');
                          borderDiv.siblings('.validation-error').remove();

                          // Run validation
                          const selector = hiddenInput.attr('id') || hiddenInput.attr('name') || '';
                          if (selector) {
                              validateFieldByIdOrName(selector);
                          }

                          // Reset dropdown positioning
                          setTimeout(() => {
                              box.css({
                                  position: '',
                                  width: '',
                                  zIndex: '',
                                  'border-color': '',
                                  'box-shadow': ''
                              });
                              box.next('.dropdown-spacer').remove();
                          }, 350);
                      });

                      // Clear button handler
                      clearBtn.off('click.studentClear').on('click.studentClear', function(e) {
                          e.stopPropagation();
                          selectedValue.text('Enter student\'s name').css('color', '#999');
                          hiddenInput.val('');
                          options.find('li').removeClass('active');
                          $(this).addClass('d-none');
                          box.removeClass('open active');
                          searchInput.val('').trigger('keyup');
                      });

                      // Search filter handler
                      searchInput.off('keyup.studentSearch').on('keyup.studentSearch', function() {
                          const term = $(this).val().toLowerCase();
                          options.find('li').each(function() {
                              if (!$(this).hasClass('search-option')) {
                                  const text = $(this).text().toLowerCase();
                                  $(this).toggle(text.includes(term));
                              }
                          });
                      });
                  });
              }

              //   add student start
              var students_array = [];
              var studentKey = 0;
              $(document).on('click.clonePage', '#add_student', function() {

                  const modal = $('#addStudentModal');

                  // Get values
                  const student_id = modal.find('#new_student_id').val().trim();
                  const student_name = modal.find('#new_student_name').val().trim();
                  const start_date = modal.find('#student_start_date').val().trim();
                  const amount = modal.find('#amount').val().trim();

                  // Get selected subjects
                  let subjects = [];
                  modal.find('input[name="subject"]:checked').each(function() {
                      subjects.push($(this).val());
                  });

                  validateFieldByIdOrName('new_student_id', 0)
                  validateFieldByIdOrName('new_student_name', 0)
                  validateFieldByIdOrName('student_start_date', 0)
                  validateFieldByIdOrName('amount', 0)
                  var borderDiv = modal.find('.edit-radio-border');
                  let title = borderDiv.find('h6');
                  let icon = borderDiv.find('.constant-icon');
                  if (subjects.length == 0) {
                      borderDiv.attr('style', (i, s) => (s || '') +
                          'border-color: #C41E3A !important; box-shadow: 0 0 4pt 2pt rgba(196,30,58,0.6) !important;'
                      );
                      title.attr('style', (i, s) => (s || '') + 'color: #C41E3A !important;');
                      icon.attr('style', (i, s) => (s || '') + 'color: #C41E3A !important;');
                  } else {
                      borderDiv.css({
                          'border-color': '',
                          'box-shadow': ''
                      });
                      title.css('color', '');
                      icon.css('color', '');
                  }

                  // Validation
                  if (student_name === '' || start_date === '' || subjects.length === 0 || amount === '') {
                      modal.find('.form-validation-toast').fadeIn();
                      setTimeout(() => {
                          modal.find('.form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  }

                  // Push data to students array
                  students_array.push({
                      key: studentKey,
                      student_id: student_id,
                      student_name: student_name,
                      start_date: start_date,
                      end_date: '',
                      subjects: subjects,
                      amount: amount,
                      status: 'active'
                  });

                  // Close modal
                  modal.modal('hide');

                  // Reset fields
                  modal.find('#new_student_id').val('');
                  modal.find('#new_student_name').val('');
                  modal.find('#student_start_date').val('');
                  modal.find('#amount').val('135.00');
                  modal.find('input[name="subject"]').prop('checked', false);
                  modal.find('.clear-icon').addClass('d-none');

                  // Optional UI refresh functions
                  showStudentAdd?.();
                  showToast?.('student-toast-added');
                  initializeStudentDropdown()
                  studentKey++;
              });
              $("#addStudentModal").on('hidden.bs.modal', function() {
                  $('#addStudentModal .form-validation-toast').hide();
                  clearFieldValidation('new_student_id');
                  clearFieldValidation('new_student_name');
                  clearFieldValidation('student_start_date');
                  clearFieldValidation('amount');
                  const modal = $('#addStudentModal');
                  var borderDiv = modal.find('.edit-radio-border');
                  let title = borderDiv.find('h6');
                  let icon = borderDiv.find('.constant-icon');
                  borderDiv.css({
                      'border-color': '',
                      'box-shadow': ''
                  });
                  title.css('color', '');
                  icon.css('color', '');
              });

              function getSubjectIcons(subjects) {
                  let icons = '';

                  subjects.forEach(sub => {
                      if (sub === '1') {
                          icons +=
                              `<span data-toggle="tooltip" data-trigger="hover"
                                          data-placement="top" title="" data-original-title="Math"><i class="fa-light fa-calculator text-grey fs-16 ml-2" data-contract=""></i></span>`;
                      }
                      if (sub === '2') {
                          icons +=
                              `<span data-toggle="tooltip" data-trigger="hover"
                                          data-placement="top" title="" data-original-title="Reading"><i class="fa-light fa-book text-grey fs-16 ml-2" data-contract="" ></i></span>`;
                      }
                      if (sub === '3') {
                          icons +=
                              `<span data-toggle="tooltip" data-trigger="hover"
                                          data-placement="top" title="" data-original-title="EFL"><i class="fa-light fa-language text-grey fs-16 ml-2" data-contract="" ></i></span>`;
                      }
                  });

                  return icons;
              }

              function showStudentAdd() {
                  let html = '';

                  for (let i = 0; i < students_array.length; i++) {
                      const subjects = students_array[i].subjects.join(', ');
                      var statusIcon = students_array[i].status == 'active' ? `<i class="fa-light fa-circle-check text-green fs-20"
              data-toggle="tooltip" data-placement="top" title="Active"></i>` : `<i class="fa-light fa-circle-xmark text-red fs-20"
        data-toggle="tooltip" data-placement="top" title="Inactive"></i>`;

                      html += `
        <tr class="student-item" data="${i}" data-key="${students_array[i].key}">
            <td class="py-2 border-0 align-middle" width="20">
                <i class="fa-light fa-grip-vertical drag-handle cursor-grab text-grey fs-16"
                   style="opacity:0"></i>
            </td>
            <td class="py-2 border-0 pl-2">
                ${statusIcon}
            </td>
            <td class="py-2 border-0 pl-2">
                <span class="fw-300 text-darkgrey fs-15">${students_array[i].student_id || ''}</span>
            </td>
            <td class="py-2 border-0 pl-2">
                <span class="fw-300 text-darkgrey fs-15">${students_array[i].student_name}</span>
            </td>
            <td class="py-2 border-0 text-right pr-2">
                ${getSubjectIcons(students_array[i].subjects)}
            </td>
            <td class="py-2 border-0 pl-2">
                <span class="fw-300 text-darkgrey fs-15">${students_array[i].start_date}</span>
            </td>
            <td class="py-2 border-0 pl-2">
                <span class="fw-300 text-darkgrey fs-15">${students_array[i].end_date || ''}</span>
            </td>
            <td class="py-2 border-0 pl-2 text-right">
                <span class="fw-300 text-darkgrey fs-15">${students_array[i].amount}</span>
            </td>
            <td class="py-2 border-0 text-right drag-handle" width="50" style="opacity:0">
                <a class="dropdown-toggle text-grey" data-toggle="dropdown" href="javascript:;">
                    <i class="fa-thin fa-ellipsis-stroke-vertical fs-20"></i>
                </a>
                <div class="dropdown-menu py-0">
                    <a data="${i}" class="dropdown-item edit-student">
                        <i class="fa-light fa-pencil mr-2"></i>Edit
                    </a>
                    <a data="${i}" class="dropdown-item update-status-student" data-status="${students_array[i].status}" data-key="${students_array[i].key}">
                        ${students_array[i].status == 'active' ? '<i class="fa-light fa-octagon-exclamation mr-2"></i>Deactivate' : '<i class="fa-light fa-arrow-up-to-arc mr-2"></i>Activate' }
                    </a>
                    <a data="${i}" class="dropdown-item delete-student">
                        <i class="fa-light fa-circle-xmark mr-2"></i>Delete
                    </a>
                </div>
            </td>
        </tr>`;
                  }

                  $('.studentTable tbody').html(html);

                  $('.studentTable-empty').toggle(students_array.length === 0);
                  $('[data-toggle="tooltip"]').tooltip();
              }
              $(document).on('mouseenter', '.student-item', function() {
                  $(this).find('.drag-handle').css('opacity', '1');
              }).on('mouseleave', '.student-item', function() {
                  $(this).find('.drag-handle').css('opacity', '0');
              });
              $(document).on('mouseenter', '.vacation-item', function() {
                  $(this).find('.drag-handle').css('opacity', '1');
              }).on('mouseleave', '.vacation-item', function() {
                  $(this).find('.drag-handle').css('opacity', '0');
              });
              $(document).on('mouseenter', '.payment-item', function() {
                  $(this).find('.drag-handle').css('opacity', '1');
              }).on('mouseleave', '.payment-item', function() {
                  $(this).find('.drag-handle').css('opacity', '0');
              });
              $(document).on('click.clonePage', '.edit-student', function() {
                  const index = $(this).attr('data');
                  const student = students_array[index];

                  $('#studentIndex').val(index);
                  $('#student_id_edit').val(student.student_id || '');
                  $('#student_name_edit').val(student.student_name);
                  $('#student_start_date_edit').val(student.start_date);
                  $('#amount_edit').val(student.amount);

                  $('input[name="subject_edit"]').prop('checked', false);
                  student.subjects.forEach(sub => {
                      $(`input[name="subject_edit"][value="${sub}"]`).prop('checked', true);
                  });

                  $('#editStudentModal').modal('show');
              });
              $(document).on('click.clonePage', '.update-status-student', function() {
                  const index = $(this).attr('data');
                  var currentStatus = (students_array[index].status || '').toLowerCase();
                  var action = currentStatus === 'active' ? 'deactivate' : 'reactivate';
                  var today = new Date();
                  var defaultDate = today.toLocaleDateString('en-GB', {
                      day: '2-digit',
                      month: 'short',
                      year: 'numeric'
                  }).replace(/ /g, '-');

                  $('#student_status_modal_index').val(index);
                  $('#student_status_modal_action').val(action);
                  $('#student_status_date').val(defaultDate);
                  $('#clear_student_status_date').removeClass('d-none');

                  if (action === 'deactivate') {
                      $('#studentStatusDateModalLabel').text('Deactivate Student');
                      $('#studentStatusDateLabel').text('End Date');
                  } else {
                      $('#studentStatusDateModalLabel').text('Reactivate Student');
                      $('#studentStatusDateLabel').text('Activation Date');
                  }

                  $('#studentStatusDateModal').modal('show');
              });
              $(document).on('click.clonePage', '#confirmStudentStatusDateBtn', function() {
                  var index = parseInt($('#student_status_modal_index').val(), 10);
                  var action = $('#student_status_modal_action').val();
                  var selectedDate = $('#student_status_date').val().trim();

                  if (isNaN(index) || !students_array[index] || selectedDate === '') {
                      return;
                  }

                  if (action === 'deactivate') {
                      students_array[index].status = 'inactive';
                      students_array[index].end_date = selectedDate;
                  } else {
                      students_array[index].status = 'active';
                      students_array[index].start_date = selectedDate;
                      students_array[index].end_date = '';
                  }

                  $('#studentStatusDateModal').modal('hide');
                  showStudentAdd();
              });
              $(document).on('click.clonePage', '#update_student', function() {

                  const index = $('#studentIndex').val();
                  const modal = $('#editStudentModal');

                  const student_id = $('#student_id_edit').val().trim();
                  const student_name = $('#student_name_edit').val().trim();
                  const start_date = $('#student_start_date_edit').val().trim();
                  const amount = $('#amount_edit').val().trim();

                  let subjects = [];
                  $('input[name="subject_edit"]:checked').each(function() {
                      subjects.push($(this).val());
                  });

                  if (!student_name || !start_date || subjects.length === 0 || !amount) {
                      modal.find('.form-validation-toast').fadeIn();
                      setTimeout(() => modal.find('.form-validation-toast').fadeOut(), 3000);
                      return;
                  }

                  students_array[index] = {
                      key: students_array[index].key,
                      student_id,
                      student_name,
                      start_date,
                      end_date: students_array[index].end_date || '',
                      subjects,
                      amount,
                      status: students_array[index].status ?? 'active'
                  };

                  modal.modal('hide');
                  showStudentAdd();
                  showToast('student-toast-updated');
              });

              let temp_students_array = [];

              $(document).on('click.clonePage', '.delete-student', function() {
                  const index = $(this).attr('data');

                  temp_students_array.push(students_array[index]);
                  $('.undo-delete-student').attr('data', index);

                  students_array.splice(index, 1);
                  showStudentAdd();
                  showToast('student-toast-deleted', 5000);
              });

              $(document).on('click.clonePage', '.undo-delete-student', function() {
                  const index = $(this).attr('data');
                  const student = temp_students_array.pop();

                  if (student) {
                      students_array.splice(index, 0, student);
                      showStudentAdd();
                  }

                  showToast('student-toast-undo');
              });


              //   add student end

              //   add payement start
              $(document).on('keydown', '#addPaymentModal input', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault(); // stop accidental form submit / reload
                        $('#add_payment').trigger('click');
                    }
                });
              $(document).on('keydown', '#editPaymentModal input', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault(); // stop accidental form submit / reload
                        $('#updte_payment').trigger('click');
                    }
                });
              var payments_array = [];
              var paymentKey = 0;
              var temp_payments_array = [];

              $(document).on('click.clonePage', '#add_payment', function() {

                  const modal = $('#addPaymentModal');

                  const payment_date = modal.find('#payment_date').val().trim();
                  const kumon_month = modal.find('#kumon_month').val().trim();
                  const payment_type = modal.find('input[name="payment_type"]:checked').val();
                  const reference_no = modal.find('#reference_no').val().trim();
                  const amount = modal.find('#payment_amount').val().trim();

                  validateFieldByIdOrName('payment_date', 0);
                  validateFieldByIdOrName('kumon_month', 0);
                  validateFieldByIdOrName('payment_amount', 0);
                  var borderDiv = modal.find('.edit-radio-border');
                  let title = borderDiv.find('h6');
                  let icon = borderDiv.find('.constant-icon');
                  if (!payment_type) {
                      borderDiv.attr('style', (i, s) => (s || '') +
                          'border-color: #C41E3A !important; box-shadow: 0 0 4pt 2pt rgba(196,30,58,0.6) !important;'
                      );
                      title.attr('style', (i, s) => (s || '') + 'color: #C41E3A !important;');
                      icon.attr('style', (i, s) => (s || '') + 'color: #C41E3A !important;');
                  } else {
                      borderDiv.css({
                          'border-color': '',
                          'box-shadow': ''
                      });
                      title.css('color', '');
                      icon.css('color', '');
                  }
                  if (!payment_date || !kumon_month || !payment_type || !amount) {
                      modal.find('.form-validation-toast').fadeIn();
                      setTimeout(() => modal.find('.form-validation-toast').fadeOut(), 3000);
                      return;
                  }

                  payments_array.push({
                      key: paymentKey,
                      payment_date,
                      kumon_month,
                      payment_type,
                      reference_no,
                      amount
                  });

                  paymentKey++;

                  modal.modal('hide');
                  resetAddPaymentModal();
                  showPaymentAdd();
                  showToast('payment-toast-added');
              });

              function resetAddPaymentModal() {
                  $('#payment_date').val('');
                  $('#kumon_month').val('');
                  $('#payment_amount').val('');
                  $('#reference_no').val('');
                  $('input[name="payment_type"]').prop('checked', false);
                  $('#addStudentModal .form-validation-toast').hide();
              }

              $("#addPaymentModal").on('hidden.bs.modal', function() {
                  $('#addPaymentModal .form-validation-toast').hide();
                  clearFieldValidation('payment_date');
                  clearFieldValidation('kumon_month');
                  clearFieldValidation('payment_amount');
                  const modal = $('#addPaymentModal');
                  var borderDiv = modal.find('.edit-radio-border');
                  let title = borderDiv.find('h6');
                  let icon = borderDiv.find('.constant-icon');
                  borderDiv.css({
                      'border-color': '',
                      'box-shadow': ''
                  });
                  title.css('color', '');
                  icon.css('color', '');
              });

              function showPaymentAdd() {
                  let html = '';

                  for (let i = 0; i < payments_array.length; i++) {

                      html += `
        <tr class="payment-item" data="${i}" data-key="${payments_array[i].key}">
            <td class="py-2 border-0 align-middle" width="20">
                <i class="fa-light fa-grip-vertical drag-handle cursor-grab text-grey fs-16"
                   style="opacity:0"></i>
            </td>
            <td class="py-2 border-0 pl-2">
                <span class="fw-300 text-darkgrey fs-15">
                    ${payments_array[i].kumon_month}
                </span>
            </td>
                <td class="py-2 border-0 text-center pr-2" width="10%">
                    <span class="fw-300 text-darkgrey fs-15">
                        ${payments_array[i].payment_type}
                </span>
            </td>
            <td class="py-2 border-0 pl-2">
                <span class="fw-300 text-darkgrey fs-15">
                    ${payments_array[i].reference_no || '-'}
                </span>
            </td>
            <td class="py-2 border-0 pl-2 text-right pr-2">
                <span class="fw-300 text-darkgrey fs-15 pr-1">
                    $ ${parseFloat(payments_array[i].amount).toFixed(2)}
                </span>
            </td>
            <td class="py-2 border-0 pl-2">
                <span class="fw-300 text-darkgrey fs-15">
                    ${payments_array[i].payment_date}
                </span>
            </td>


            

            

            <td class="py-2 border-0 text-right drag-handle" width="50" style="opacity:0">
                <a class="dropdown-toggle text-grey" data-toggle="dropdown" href="javascript:;">
                    <i class="fa-thin fa-ellipsis-stroke-vertical fs-20"></i>
                </a>
                <div class="dropdown-menu py-0">
                    <a data="${i}" class="dropdown-item edit-payment">
                        <i class="fa-light fa-pencil mr-2"></i>Edit
                    </a>
                    <a data="${i}" class="dropdown-item delete-payment">
                        <i class="fa-light fa-circle-xmark mr-2"></i>Delete
                    </a>
                </div>
            </td>
        </tr>`;
                  }

                  $('.paymentTable tbody').html(html);

                  $('.paymentTable-empty').toggle(payments_array.length === 0);
                  $('[data-toggle="tooltip"]').tooltip();
              }


              $(document).on('click.clonePage', '.edit-payment', function() {

                  const index = $(this).attr('data');
                  const payment = payments_array[index];

                  $('#paymentIndex').val(index);

                  $('#payment_date_edit').val(payment.payment_date);
                  $('#kumon_month_edit').val(payment.kumon_month);
                  $('#payment_amount_edit').val(payment.amount);
                  $('#reference_no_edit').val(payment.reference_no || '');

                  // Reset radios
                  $('input[name="payment_type_edit"]').prop('checked', false);

                  // Set selected type
                  $(`input[name="payment_type_edit"][value="${payment.payment_type}"]`)
                      .prop('checked', true);

                  $('#editPaymentModal').modal('show');
              });

              $(document).on('click.clonePage', '#updte_payment', function() {

                  const index = $('#paymentIndex').val();
                  const modal = $('#editPaymentModal');

                  const payment_date = $('#payment_date_edit').val().trim();
                  const kumon_month = $('#kumon_month_edit').val().trim();
                  const amount = $('#payment_amount_edit').val().trim();
                  const reference_no = $('#reference_no_edit').val().trim();

                  const payment_type = $('input[name="payment_type_edit"]:checked').val();

                  // Validation
                  if (!payment_date || !kumon_month || !payment_type || !amount) {
                      modal.find('.form-validation-toast').fadeIn();
                      setTimeout(() => modal.find('.form-validation-toast').fadeOut(), 3000);
                      return;
                  }

                  // Update array
                  payments_array[index] = {
                      key: payments_array[index].key,
                      payment_date,
                      kumon_month,
                      payment_type,
                      reference_no,
                      amount
                  };

                  modal.modal('hide');
                  showPaymentAdd(); // refresh list
                  showToast('payment-updated-toast');
              });

              $(document).on('click.clonePage', '.delete-payment', function() {
                  const index = $(this).attr('data');

                  temp_payments_array.push(payments_array[index]);
                  payments_array.splice(index, 1);

                  showPaymentAdd();
                  showToast('payment-toast-deleted', 5000);
              });

              $(document).on('click.clonePage', '.undo-delete-payment', function() {
                  const payment = temp_payments_array.pop();
                  if (payment) {
                      payments_array.push(payment);
                      showPaymentAdd();
                  }
                  showToast('payment-toast-undo');
              });


              //   add payement end

              //   add vacation start
              let vacation_array = [];
              let deleted_vacation = null;
              const $takeWorkHome = $('#customSwitch1');
              const $takeWorkHomeEdit = $('#customSwitch1Edit');
              const $reducedWorkloadWrapper = $('#reducedWorkloadWrapper');
              const $reducedWorkloadWrapperEdit = $('#reducedWorkloadWrapperEdit');
              const $reducedWorkloadSwitch = $('#reducedWorkloadSwitch');
              const $reducedWorkloadSwitchEdit = $('#reducedWorkloadSwitchEdit');

              function syncReducedWorkloadVisibility() {
                  if ($takeWorkHome.is(':checked')) {
                      $reducedWorkloadWrapper.removeClass('d-none');
                  } else {
                      $reducedWorkloadWrapper.addClass('d-none');
                      $reducedWorkloadSwitch.prop('checked', false);
                  }
              }

              function syncReducedWorkloadEditVisibility() {
                  if ($takeWorkHomeEdit.is(':checked')) {
                      $reducedWorkloadWrapperEdit.removeClass('d-none');
                  } else {
                      $reducedWorkloadWrapperEdit.addClass('d-none');
                      $reducedWorkloadSwitchEdit.prop('checked', false);
                  }
              }

              $takeWorkHome.on('change', syncReducedWorkloadVisibility);
              $takeWorkHomeEdit.on('change', syncReducedWorkloadEditVisibility);
              syncReducedWorkloadVisibility();
              syncReducedWorkloadEditVisibility();

              function getSubjectName(subs) {
                  return subs.map(sub => {
                      if (sub === "1") return "Math";
                      if (sub === "2") return "Reading";
                      return "EFL";
                  }).join(", ");
              }

              function getStatusIcon(rangeStr) {

  // helper — parse "DD-MMM-YYYY" safely
  function parseDMY(str) {
    if (!str) return null;
    const d = new Date(str);
    if (!isNaN(d)) return d;

    // fallback manual parse (for safety)
    const parts = str.trim().split('-');
    if (parts.length !== 3) return null;

    const day = parseInt(parts[0], 10);
    const mon = parts[1];
    const yr = parseInt(parts[2], 10);

    const months = {
      Jan:0, Feb:1, Mar:2, Apr:3, May:4, Jun:5,
      Jul:6, Aug:7, Sep:8, Oct:9, Nov:10, Dec:11
    };

    if (!(mon in months)) return null;
    return new Date(yr, months[mon], day);
  }

  // default = inactive
  const inactiveIcon =
    `<i class="fa-light fa-circle-xmark text-red fs-20"
        data-toggle="tooltip" data-placement="top" title="Inactive"></i>`;

  if (!rangeStr) return inactiveIcon;

  const parts = rangeStr.split(' to ');
  if (parts.length !== 2) return inactiveIcon;

  const start = parseDMY(parts[0]);
  const end   = parseDMY(parts[1]);

  if (!start || !end) return inactiveIcon;

  start.setHours(0,0,0,0);
  end.setHours(23,59,59,999);

  const today = new Date();
  today.setHours(0,0,0,0);

  // ✅ ACTIVE
  if (today >= start && today <= end) {
    return `<i class="fa-light fa-circle-check text-green fs-20"
              data-toggle="tooltip" data-placement="top" title="Active"></i>`;
  }

  // ✅ UPCOMING (client spec — yellow triangle)
  if (today < start) {
    return `<i class="fa-light fa-triangle-exclamation text-warning fs-16"
              data-toggle="tooltip" data-placement="top" title="Upcoming"></i>`;
  }

  // ✅ INACTIVE (past)
  return inactiveIcon;
}


              function isUpcoming(rangeStr) {
                  if (!rangeStr) return false;
                  const parts = rangeStr.split(' to ');
                  if (parts.length !== 2) return false;
                  const start = new Date(parts[0]);
                  start.setHours(0, 0, 0, 0);
                  const today = new Date();
                  today.setHours(0, 0, 0, 0);
                  return start > today;
              }

              function getBooleanText(value) {
                  return value ? 'True' : 'False';
              }

              function showVacationAdd() {
  let html = '';

  for (let i = 0; i < vacation_array.length; i++) {
    const v = vacation_array[i];

    const commentBtn = v.comment
      ? `<button type="button" data-comment="${escapeHtml(v.comment)}" class="btn vacation-info ml-auto p-0 cursor-help">
           <i class="fa-light fa-circle-info text-grey fs-18"></i>
         </button>`
      : '';

    // Status icon: Active / Upcoming / Inactive with tooltip
    // (Make sure getStatusIcon returns icon WITH data-toggle="tooltip" title="Active|Upcoming|Inactive")
    const statusIcon = getStatusIcon(v.date_range);

    // Reduced Workload: icon only if true, aligned RIGHT (inside subject cell)
    const reducedIcon = v.reduced_workload
      ? `<span data-toggle="tooltip" data-placement="left" title="Reduced Workload" class="vac-icon reduced-icon">
           <i class="fa-duotone fa-solid fa-arrow-down-to-line text-charcoal"></i>
         </span>`
      : '';

    // Take Work Home: always show icon (true=books, false=island-tropical), aligned CENTER (inside subject cell)
    const takeWorkIcon = v.take_work_home
      ? `<span data-toggle="tooltip" data-placement="top" title="Take Work" class="vac-icon takework-icon">
           <i class="fa-duotone fa-solid fa-books text-charcoal"></i>
         </span>`
      : `<span data-toggle="tooltip" data-placement="top" title="No Work" class="vac-icon takework-icon">
           <i class="fa-duotone fa-solid fa-island-tropical text-charcoal"></i>
         </span>`;

    // Planned: icon only if true, centered in planned column
    const plannedIcon = v.planned
      ? `<span data-toggle="tooltip" data-placement="top" title="Planned">
           <i class="fa-duotone fa-regular fa-sparkles text-charcoal"></i>
         </span>`
      : '';

    html += `
      <tr class="vacation-item" data="${i}">
        <!-- drag -->
        <td class="py-2 border-0" width="3%">
          <i class="fa-light fa-grip-vertical drag-handle cursor-grab text-grey fs-16" style="opacity:0"></i>
        </td>

        <!-- status -->
        <td class="py-2 border-0 text-center" width="3%">
          ${statusIcon}
        </td>

        <!-- student -->
        <td width="30%" class="py-2 border-0 pl-2">
          <span class="fw-300 text-darkgrey fs-15">${escapeHtml(v.student)}</span>
        </td>

        <!-- subject + icons -->
        <td width="25%" class="py-2 border-0 pl-2">
          <div class="vac-subject-row">
            <div class="vac-subject-left">
              ${commentBtn}
              <span class="fw-300 text-darkgrey fs-15 subject-text" style="white-space:nowrap;">
                ${escapeHtml(getSubjectName(v.subjects))}
              </span>
            </div>

            <div class="vac-subject-icons">
                <div class="vac-icon-right">
                  ${reducedIcon}
                </div>
              <div class="vac-icon-center">
                ${takeWorkIcon}
              </div>
            </div>
          </div>
        </td>

        <!-- date range -->
        <td width="30%" class="py-2 border-0 pl-2">
          <span class="fw-300 text-darkgrey fs-15 date-range">${escapeHtml(v.date_range)}</span>
        </td>

        <!-- planned -->
        <td class="py-2 border-0 text-center" width="3%">
          ${plannedIcon}
        </td>

        <!-- actions -->
        <td class="py-2 border-0 text-right" width="50">
          <a class="dropdown-toggle text-grey" data-toggle="dropdown" href="javascript:;">
            <i class="fa-thin fa-ellipsis-stroke-vertical fs-20"></i>
          </a>
          <div class="dropdown-menu py-0">
            <a data="${i}" class="dropdown-item edit-vacation"><i class="fa-light fa-pencil mr-2"></i>Edit</a>
            <a data="${i}" class="dropdown-item clone-vacation"><i class="fa-light fa-clone mr-2"></i>Clone</a>
            <a data="${i}" class="dropdown-item delete-vacation"><i class="fa-light fa-circle-xmark mr-2"></i>Delete</a>
          </div>
        </td>
      </tr>
    `;
  }

  $('.vacationTable tbody').html(html);
  $('.vacationTable-empty').toggle(vacation_array.length === 0);

  // re-init bootstrap tooltips
  $('[data-toggle="tooltip"]').tooltip();
}

function escapeHtml(str) {
  return String(str ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}


              // 1. Create a single, reusable tooltip element and hide it.
              $('body').append(
                  '<div id="vacation-comment-tooltip" class="font-titillium text-grey fw-300" ' +
                  'style="font-family:\'Titillium Web\', sans-serif; display:none; position:absolute; ' +
                  'border:1px solid #ccc; padding:10px 12px; border-radius:6px; width:270px; ' +
                  'line-height:1.3; z-index:1000; background:#f9f9f9; ' +
                  'box-shadow:0 2px 10px rgba(0,0,0,0.1);">' +
                  '</div>'
              );


              const tooltip = $('#vacation-comment-tooltip');

              // 2. Use event delegation for hover events on dynamically created buttons.
              $('.vacationTable').on('mouseenter', '.vacation-info', function() {
                  const button = $(this);
                  const comment = button.data('comment');
                  button.find('i').removeClass('fa-light').addClass('fa-solid')
                  if (comment) {
                      // 3. Create the tooltip's inner HTML and populate it.
                      const tooltipContent =
                          `<strong class="titillium-web-black fs-18 text-primary" style="line-height:1.6;">Parent Comment</strong><br>${comment}`;
                      tooltip.html(tooltipContent);

                      // 4. Calculate the correct position.
                      const iconPosition = button.offset();
                      const iconWidth = button.outerWidth();

                      // Position and show the tooltip
                      tooltip.css({
                          top: iconPosition.top, // Align top with the button
                          left: iconPosition.left + iconWidth + 10 // 10px to the right
                      }).show();
                  }
              }).on('mouseleave', '.vacation-info', function() {
                  // 5. Hide the tooltip on mouse out.
                  $(this).find('i').removeClass('fa-solid').addClass('fa-light')
                  tooltip.hide();
              });


              $(document).on('click.clonePage', '.clone-vacation', function() {

                  const index = $(this).attr('data');
                  const v = vacation_array[index];

                  const modal = $('#addVacationModal');

                  // ----------------------------
                  // Copy all fields EXCEPT subject
                  // ----------------------------
                  $('#selected_student').val(v.student);
                  $('#vacation_date_range').val(v.date_range);
                  $('#vacation_comments').val(v.comment);
                  $('#customSwitch1').prop('checked', v.take_work_home);
                  $('#reducedWorkloadSwitch').prop('checked', v.reduced_workload);
                  syncReducedWorkloadVisibility();

                  // Reset SUBJECT (important)
                  $('input[name="sub_name"]').prop('checked', false);

                  // Sync dropdown UI state
                  modal.find('.student-dropdown')
                      .attr('data-selected-id', v.student)
                      .attr('data-selected-text', v.student);
                  modal.find('.selected-value')
                      .text(v.student)
                      .removeClass('text-placeholder')
                      .css('color', '#3f3f3f');
                  modal.find('.student-dropdown .clear-icon').removeClass('d-none');

                  // Show add modal
                  modal.modal('show');
              });

              $(document).on('click.clonePage', '#add_vacation', function() {

                  const modal = $('#addVacationModal');

                  const student = modal.find('#selected_student').val();
                  const date_range = modal.find('#vacation_date_range').val().trim();
                  const comment = modal.find('#vacation_comments').val().trim();
                  const take_work_home = modal.find('#customSwitch1').is(':checked');
                  const reduced_workload = modal.find('#reducedWorkloadSwitch').is(':checked');

                  let subjects = [];
                  modal.find('input[name="sub_name"]:checked').each(function() {
                      subjects.push($(this).val());
                  });

                  validateFieldByIdOrName('selected_student', 0);
                  validateFieldByIdOrName('vacation_date_range', 0);
                  var borderDiv = modal.find('.edit-radio-border');
                  let title = borderDiv.find('h6');
                  let icon = borderDiv.find('.constant-icon');
                  if (subjects.length === 0) {
                      borderDiv.attr('style', (i, s) => (s || '') +
                          'border-color: #C41E3A !important; box-shadow: 0 0 4pt 2pt rgba(196,30,58,0.6) !important;'
                      );
                      title.attr('style', (i, s) => (s || '') + 'color: #C41E3A !important;');
                      icon.attr('style', (i, s) => (s || '') + 'color: #C41E3A !important;');
                  } else {
                      borderDiv.css({
                          'border-color': '',
                          'box-shadow': ''
                      });
                      title.css('color', '');
                      icon.css('color', '');
                  }

                  if (!student || !date_range || subjects.length === 0) {
                      modal.find('.form-validation-toast').fadeIn();
                      setTimeout(() => modal.find('.form-validation-toast').fadeOut(), 3000);
                      return;
                  }

                      vacation_array.push({
                          key: Date.now(),
                          student,
                          subjects,
                          date_range,
                          take_work_home,
                          reduced_workload,
                          planned: 0,
                          comment
                      });

                  modal.modal('hide');
                  resetAddVacationModal(modal);

                  showVacationAdd();
                  showToast('vacation-toast-added');
              });

              function resetAddVacationModal(modal) {
                  // Keep checkbox value attributes intact; reset only intended fields.
                  modal.find('#selected_student').val('');
                  modal.find('#vacation_date_range').val('');
                  modal.find('#vacation_comments').val('');
                  modal.find('input[name="sub_name"]').prop('checked', false);
                  modal.find('#customSwitch1').prop('checked', false);
                  modal.find('#reducedWorkloadSwitch').prop('checked', false);

                  const studentDropdown = modal.find('.student-dropdown');
                  studentDropdown
                      .attr('data-selected-id', '')
                      .attr('data-selected-text', '');
                  studentDropdown.find('.selected-value')
                      .text("Enter student's name")
                      .addClass('text-placeholder')
                      .css('color', '#999');
                  studentDropdown.find('.clear-icon').addClass('d-none');
                  studentDropdown.find('.dropdown-options li').removeClass('active');
                  studentDropdown.find('.dropdown-search').val('');

                  $('#clear_vacation_date_range').addClass('d-none');
                  syncReducedWorkloadVisibility();
              }

              $("#addVacationModal").on('hidden.bs.modal', function() {
                  $('#addVacationModal .form-validation-toast').hide();
                  clearFieldValidation('selected_student');
                  clearFieldValidation('vacation_date_range');
                  const modal = $('#addVacationModal');
                  resetAddVacationModal(modal);
                  var borderDiv = modal.find('.edit-radio-border');
                  let title = borderDiv.find('h6');
                  let icon = borderDiv.find('.constant-icon');
                  borderDiv.css({
                      'border-color': '',
                      'box-shadow': ''
                  });
                  title.css('color', '');
                  icon.css('color', '');
                  $('.flatpickr-day').removeClass('selected')
                  $('.flatpickr-day').removeClass('startRange')
                  $('.flatpickr-day').removeClass('inRange')
                  $('.flatpickr-day').removeClass('endRange')
                  $('#clear_vacation_date_range').addClass('d-none')
              });

              $(document).on('click.clonePage', '.edit-vacation', function() {

                  const index = $(this).attr('data');
                  const v = vacation_array[index];
                  

                  $('#vacationIndex').val(index);
                  $('#selected_student_edit').val(v.student);
                  $('#editVacationModal .student-dropdown')
                    .attr('data-selected-id', v.student)
                    .attr('data-selected-text', v.student)
                    .find('.selected-value').text(v.student);
                    $('#editVacationModal .student-dropdown .clear-icon').toggleClass('d-none', v.student === '');
                  $('#vacation_date_range_edit').val(v.date_range);
                  $('#vacation_comments_edit').val(v.comment);
                  $('#customSwitch1Edit').prop('checked', v.take_work_home);
                  $('#reducedWorkloadSwitchEdit').prop('checked', v.reduced_workload);
                  syncReducedWorkloadEditVisibility();

                  $('input[name="sub_name_edit"]').prop('checked', false);
                  v.subjects.forEach(sub => {
                      $(`input[name="sub_name_edit"][value="${sub}"]`).prop('checked', true);
                  });

                  $('#editVacationModal').modal('show');
              });
              $(document).on('click.clonePage', '#update_vacation', function() {

                  const modal = $('#editVacationModal');
                  const index = $('#vacationIndex').val();

                  const student = $('#selected_student_edit').val();
                  const date_range = $('#vacation_date_range_edit').val().trim();
                  const comment = $('#vacation_comments_edit').val().trim();
                  const take_work_home = $('#customSwitch1Edit').is(':checked');
                  const reduced_workload = $('#reducedWorkloadSwitchEdit').is(':checked');

                  let subjects = [];
                  $('input[name="sub_name_edit"]:checked').each(function() {
                      subjects.push($(this).val());
                  });

                  if (!student || !date_range || subjects.length === 0) {
                      modal.find('.form-validation-toast').fadeIn();
                      setTimeout(() => modal.find('.form-validation-toast').fadeOut(), 3000);
                      return;
                  }

                  vacation_array[index] = {
                      key: vacation_array[index].key,
                      student,
                      subjects,
                      date_range,
                      take_work_home,
                      reduced_workload,
                      planned: vacation_array[index].planned || 0,
                      comment
                  };

                  modal.modal('hide');
                  showVacationAdd();
                  showToast('vacation-toast-updated');
              });

              $(document).on('click.clonePage', '.delete-vacation', function() {
                  const index = $(this).attr('data');
                  deleted_vacation = vacation_array[index];

                  vacation_array.splice(index, 1);
                  showVacationAdd();
                  showToast('vacation-toast-deleted');
              });

              $(document).on('click.clonePage', '.undo-delete-vacation', function() {
                  if (deleted_vacation) {
                      vacation_array.push(deleted_vacation);
                      deleted_vacation = null;
                      showVacationAdd();
                      showToast('vacation-toast-recovered');
                  }
              });

              //   add vacation end

              $(document).on('click.clonePage', '#btn-continue', function() {
                  var client_id = $('input[name=client_id]').val();
                  var site_id = $('input[name=site_id]').val();
                  if (client_id == undefined || client_id == '') {
                      showError("Please select client.")
                      validateFieldByIdOrName('edit_client_id')
                      return
                  } else {
                      clearFieldValidation('edit_client_id')
                  }
                  if (site_id == undefined || site_id == '') {
                      showError("Please select Site.")
                      validateFieldByIdOrName('edit_site_id')
                      return
                  } else {
                      clearFieldValidation('edit_site_id')
                  }
                  $('.btn-div').remove();
              })

              $('.header-item-code').text('Clone Client');
              $('.header-desc').text('{{ $desc }}');
            //   $('.header-desc').text('Clone Kumon Math & Reading Client');

              $(document).on('click.clonePage', '.closeEditBtn', function() {
                  const contractId = $(this).data('item-id');

                  // Populate modal fields
                  $('#CloseModal #viewReadBtn').val(contractId);

                  // Show modal
                  $('#CloseModal').modal('show');
              });
              $(document).on('click.clonePage', '#viewReadBtn', function() {
                var val = $(this).attr('value');
                  $('#showEditData').addClass('d-none');
                  $('#showData').removeClass('d-none');
                  $('#showCards').removeClass('d-none');
                  $('.edit-nav-tabs, .edit-header').addClass('d-none');
                  $('.read-nav-tabs, .read-header').removeClass('d-none');

                  // 🔹 Reset tabs to first (Main)
                  $('.sub-nav-tabs .nav-link').removeClass('active');
                  $('.tab-pane').removeClass('show active');
                  $('#nav-main-tab-contract').addClass('active');
                  $('#nav-main-contract').addClass('show active');

                  $('.header-item-code').text('Support Contract');

                  $('#CloseModal').modal('hide');
                //   let id = '{{ @$GETID }}';
                  if (val) showData(val);
              });

              function showData(id) {
                  $('.c-active').removeClass('c-active');
                  $('.viewContent[data=' + id + ']').addClass('c-active');
                  $('.viewContent[data=' + id + ']').click();
                //   $.ajax({
                //       type: 'get',
                //       data: {
                //           id: id
                //       },
                //       url: '{{ url('get-client-content') }}',
                //       dataType: 'json',
                //       beforeSend() {
                //           Dashmix.layout('header_loader_on');

                //       },

                //       success: function(res) {

                //           Dashmix.layout('header_loader_off');
                //           $('#showData').removeClass('d-none').html(res.content);
                //           $('#showCards').removeClass('d-none').html(res.cards);
                //           $('.header-new-text').html(res.header_text);
                //           $('.header-new-subtext').html(res.header_sub_text);
                //           $('.header-item-code').text(res.contract_no);
                //           $('.header-desc').text(res.header_desc);
                //           // $('.header-image').attr('src', res.header_img);
                //           $('.btn-edit').attr('data', res.id);
                //           $('.btnDelete').attr('data', res.id);
                //           $('.btn-clone').attr('data', res.id);
                //           $('.btn-pdf').attr('href', res.pdfUrl);
                //           $('.icon-html').html(res.iconHtml);
                //           $('[data-toggle=tooltip]').tooltip();

                //           // 🔹 Reset tabs to first (Main)
                //           $('.sub-nav-tabs .nav-link').removeClass('active');
                //           $('.tab-pane').removeClass('show active');
                //           $('#nav-main-tab-contract').addClass('active');
                //           $('#nav-main-contract').addClass('show active');

                //           let savedState = localStorage.getItem('cardsToggleState');

                //           if (savedState === "on") {
                //               toggleCards(true); // ON
                //           } else {
                //               toggleCards(false); // OFF
                //           }

                //           setTimeout(() => {
                //               document.querySelectorAll(
                //                   '.distributer-table, .purchasing-table'
                //               ).forEach(table => {
                //                   const tbody = table.querySelector(
                //                       'tbody.scrollable-tbody');
                //                   const thead = table.querySelector('thead');

                //                   function syncHeader() {
                //                       const hasScrollbar = tbody.scrollHeight > tbody
                //                           .clientHeight;

                //                       thead.classList.toggle('has-scrollbar',
                //                           hasScrollbar);
                //                   }

                //                   syncHeader();
                //                   window.addEventListener('resize', syncHeader);
                //               });
                //           }, 200);
                //       }
                //   })
              }

              function toggleCards(isOn) {

                  let btn = $('.card-toggle');
                  let icon = btn.find('i');

                  if (isOn) {
                      // ===== TOGGLE ON =====
                      btn.addClass('active');
                      icon.removeClass('fa-solid').addClass('fa-thin');
                      btn.attr('data-original-title', 'Hide Cards').tooltip('update');

                      $('.cards-container').slideDown(150);
                  } else {
                      // ===== TOGGLE OFF =====
                      btn.removeClass('active');
                      icon.removeClass('fa-thin').addClass('fa-solid');
                      btn.attr('data-original-title', 'Show Cards').tooltip('update');

                      $('.cards-container').slideUp(150);
                  }
              }

              //   $(document).on('click.clonePage', '.edit-distribution', function() {
              //       // Show modal
              //       $('#editDistributionModal').modal('show');
              //   });



              //   $(document).on('click.clonePage', '#add_purchasing', function() {
              //       // Get values from modal inputs
              //       var edit_estimate_no = $('#edit_estimate_no').val().trim();
              //       var edit_sales_order_no = $('#edit_sales_order_no').val().trim();
              //       var edit_invoice_no = $('#edit_invoice_no').val().trim();
              //       var edit_invoice_date = $('#edit_invoice_date').val().trim();
              //       var edit_po_no = $('#edit_po_no').val().trim();
              //       var edit_po_date = $('#edit_po_date').val().trim();

              //       // Update the corresponding spans and hidden inputs
              //       $('span.estimate_no').text(edit_estimate_no);
              //       $('input.estimate_no').val(edit_estimate_no);

              //       $('span.sales_order_no').text(edit_sales_order_no);
              //       $('input.sales_order_no').val(edit_sales_order_no);

              //       $('span.invoice_no').text(edit_invoice_no);
              //       $('input.invoice_no').val(edit_invoice_no);

              //       $('span.invoice_date').text(edit_invoice_date);
              //       $('input.invoice_date').val(edit_invoice_date);

              //       $('span.po_no').text(edit_po_no);
              //       $('input.po_no').val(edit_po_no);

              //       $('span.po_date').text(edit_po_date);
              //       $('input.po_date').val(edit_po_date);

              //       // Close the modal
              //       $('#editPurchasingModal').modal('hide');
              //   });

              // When a client is selected
              $(document).on('click.clonePage', '.client-custom-dropdown ul.dropdown-options li:not(.search-option)',
                  function(
                      e) {
                      e.stopPropagation(); // prevent outer clicks

                      const $li = $(this);
                      const dropdown = $li.closest('.custom-dropdown');
                      const value = $li.data('value');
                      const text = $li.text().trim();

                      if (text) {

                      }

                      // --- Update Client dropdown UI manually ---
                      dropdown.find('.selected-value').text(text).css('color', '#3f3f3f');
                      dropdown.attr('data-selected-id', value);
                      dropdown.find('input[type="hidden"]').val(value);
                      dropdown.find('.clear-icon').removeClass('d-none');
                      dropdown.find('ul.dropdown-options li').removeClass('active');
                      $li.addClass('active');
                      dropdown.removeClass('open');

                      // --- Reset Site dropdown hidden input and display ---
                      const siteDropdown = $('h6:contains("Site")').closest('.custom-dropdown');
                      const siteHiddenInput = siteDropdown.next('input[type="hidden"]');
                      siteHiddenInput.val(''); // clear value
                      siteDropdown.find('.selected-value').text('Select Site').css('color', '#999');
                      siteDropdown.find('.clear-icon').addClass('d-none');

                      // --- Then load sites for this client ---
                      loadSitesByClient(value);
                      loadDefaultEmailsByClient(value);
                  });

              // Delegated click for dynamically added Site <li>s
              $(document).on('click.clonePage', '.custom-dropdown ul.dropdown-options li:not(.search-option)', function(
                  e) {
                  const $dropdown = $(this).closest('.custom-dropdown');

                  // Only for Site dropdown
                  if ($dropdown.find('h6').text().trim() === 'Site') {
                      const $li = $(this);
                      const value = $li.data('value');
                      const text = $li.text().trim();

                      const hiddenInput = $('#edit_site_id'); // <-- use next()

                      // --- Update Site dropdown UI manually ---
                      $dropdown.find('.selected-value').text(text).css('color', '#3f3f3f');
                      $dropdown.attr('data-selected-id', value);
                      hiddenInput.val(value); // <-- now updates correctly
                      $dropdown.find('.clear-icon').removeClass('d-none');
                      $dropdown.find('ul.dropdown-options li').removeClass('active');
                      $li.addClass('active');
                      $dropdown.removeClass('open');
                  }
              });

              function loadSitesByClient(client_id) {
                  if (!client_id) return;

                  $.ajax({
                      url: '{{ url('get-sites-by-client') }}/' + client_id,
                      type: 'GET',
                      success: function(response) {
                          const siteDropdown = $('h6:contains("Site")').closest('.custom-dropdown');
                          const optionsList = siteDropdown.find('ul.dropdown-options');

                          // Keep search input, remove old options
                          optionsList.find('li:not(.search-option)').remove();

                          if (response.length > 0) {
                              $.each(response, function(index, site) {
                                  optionsList.append(
                                      `<li data-value="${site.id}">${site.site_name}</li>`);
                              });
                          } else {
                              optionsList.append('<li class="text-muted px-2">No sites found</li>');
                          }

                          // Reset selected value
                          siteDropdown.find('.selected-value').text('Select Site').css('color', '#999');
                          siteDropdown.attr('data-selected-id', '');
                          siteDropdown.find('input[type="hidden"]').val('');
                          siteDropdown.find('.clear-icon').addClass('d-none');
                      },
                      error: function() {
                          alert('Failed to load sites.');
                      }
                  });
              }

              // --- Function to load assets by client and site ---
              function loadAssets(client_id, site_id) {
                  if (!client_id || !site_id) return;

                  const assetsDropdown = $('.assets-custom-dropdown');
                  const assetsHiddenInput = assetsDropdown.next('input[type="hidden"]');

                  // Reset dropdown UI
                  const optionsList = assetsDropdown.find('ul.dropdown-options');
                  optionsList.find('li:not(.search-option)').remove();
                  assetsHiddenInput.val('');
                  assetsDropdown.find('.selected-value').text('Select Assets').css('color', '#999');
                  assetsDropdown.find('.clear-icon').addClass('d-none');

                  // Fetch assets dynamically
                  $.ajax({
                      url: '{{ url('get-assets-by-client-site') }}/' + client_id + '/' + site_id,
                      type: 'GET',
                      success: function(response) {
                          if (response.length > 0) {
                              $.each(response, function(index, asset) {
                                  const labelText = asset.asset_type === 'physical' ?
                                      `${asset.sn} [${asset.hostname}]` : asset.hostname;
                                  optionsList.append(`
                        <li 
                            data-value="${asset.id}"
                            data-hostname="${asset.hostname}"
                            data-type="${asset.asset_type === 'physical' ? 'P' : 'V'}"
                            data-assettype="${asset.asset_type}"
                            data-fqdn="${asset.fqdn}"
                            data-sn="${asset.sn}"
                        >
                            <label class="d-flex align-items-center m-0">
                                <input type="checkbox" class="mr-2" value="${asset.id}">
                                <span>${labelText}</span>
                            </label>
                        </li>
                    `);
                              });
                          } else {
                              optionsList.append('<li class="text-muted px-2">No assets found</li>');
                          }
                      },
                      error: function() {
                          alert('Failed to load assets.');
                      }
                  });
              }

              // --- Whenever Site changes ---
              $(document).on('click.clonePage', '.custom-dropdown ul.dropdown-options li:not(.search-option)',
                  function() {
                      const dropdown = $(this).closest('.custom-dropdown');
                      if (dropdown.find('h6').text().trim() === 'Site') {
                          const siteValue = $(this).data('value');
                          const clientValue = $('.client-custom-dropdown').attr('data-selected-id');
                          if (clientValue && siteValue) {
                              loadAssets(clientValue, siteValue);
                          }
                      }
                  });

              // --- Checkbox selection for Assets (same as before) ---
              $(document).on('change', '.assets-custom-dropdown ul.dropdown-options li input[type="checkbox"]',
                  function() {
                      const assetsDropdown = $(this).closest('.assets-custom-dropdown');
                      const hiddenInput = assetsDropdown.next('input[type="hidden"]');
                      if (assetsDropdown.find('h6').text().trim() !== 'Assets') return;
                      let selectedIds = [];
                      assetsDropdown.find('ul.dropdown-options li input[type="checkbox"]:checked').each(
                          function() {
                              selectedIds.push($(this).val());
                          });

                      hiddenInput.val(selectedIds.join(','));
                      if (selectedIds.length > 0) {
                          assetsDropdown.find('.selected-value').text(`${selectedIds.length} selected`).css(
                              'color', '#333');
                          assetsDropdown.find('.clear-icon').removeClass('d-none');
                      } else {
                          assetsDropdown.find('.selected-value').text('Select Assets').css('color', '#999');
                          assetsDropdown.find('.clear-icon').addClass('d-none');
                      }
                  });

              // --- Clear / Select All / Deselect All (same as before) ---
              $(document).on('click.clonePage', '.assets-custom-dropdown .clear-icon', function(e) {
                  e.stopPropagation();
                  const assetsDropdown = $(this).closest('.assets-custom-dropdown');
                  const hiddenInput = assetsDropdown.next('input[type="hidden"]');

                  assetsDropdown.find('ul.dropdown-options li input[type="checkbox"]').prop('checked', false);
                  hiddenInput.val('');
                  assetsDropdown.find('.selected-value').text('Select Assets').css('color', '#999');
                  $(this).addClass('d-none');
              });

              $(document).on('click.clonePage', '.assets-custom-dropdown .select-all', function(e) {
                  e.stopPropagation();
                  const assetsDropdown = $(this).closest('.assets-custom-dropdown');
                  assetsDropdown.find('ul.dropdown-options li input[type="checkbox"]').prop('checked', true)
                      .trigger('change');
              });

              $(document).on('click.clonePage', '.assets-custom-dropdown .deselect-all', function(e) {
                  e.stopPropagation();
                  const assetsDropdown = $(this).closest('.assets-custom-dropdown');
                  assetsDropdown.find('ul.dropdown-options li input[type="checkbox"]').prop('checked', false)
                      .trigger('change');
              });


              // -------------------------
              // BASIC / ADD-BUTTON DROPDOWNS
              // -------------------------
              $('.custom-dropdown').each(function() {
                  const box = $(this);

                  // Skip multi-selects
                  if (box.hasClass('multi-select-dropdown')) return;

                  const selectedValue = box.find('.selected-value');
                  const options = box.find('.dropdown-options li');
                  const clearBtn = box.find('.clear-icon');
                  const hiddenInput = box.next('input[type="hidden"]');
                  const searchInput = box.find('.dropdown-search');
                  const title = box.find('h6');
                  const icon = box.find('.constant-icon');
                  // --- Initialize default from backend ---
                  const defaultId = box.data('selected-id');
                  const defaultText = box.data('selected-text');

                  if (defaultId && defaultText) {
                      selectedValue.text(defaultText).css('color', '#3f3f3f');
                      hiddenInput.val(defaultId);
                      clearBtn.removeClass('d-none');
                      options.filter(`[data-value="${defaultId}"]`).addClass('active');
                  }

                  box.on('click.clonePage', '.dropdown-display', function(e) {
                      e.stopPropagation();
                      const width = box.outerWidth();
                      // Close all others
                      $('.custom-dropdown').not(box).each(function() {
                          $(this).removeClass('open').css({
                              position: '',
                              width: '',
                              zIndex: '',
                          }).next('.dropdown-spacer').remove();
                      });

                      box.toggleClass('open');

                      if (box.hasClass('open')) {

                          // 1. Measure the dropdown height
                          const h = box.outerHeight();

                          // 2. Add spacer after dropdown to stop layout shift
                          if (!box.next().hasClass('dropdown-spacer')) {
                              box.after(`<div class="dropdown-spacer" style="height:${h}px;"></div>`);
                          }

                          // 3. Apply absolute positioning
                          box.css({
                              position: 'absolute',
                              width: width + 'px',
                              zIndex: 9999,
                          });
                      } else {
                          searchInput.val('').trigger('keyup');
                          // Closing dropdown
                          setTimeout(() => {
                              box.css({
                                  position: '',
                                  width: '',
                                  zIndex: '',
                              });
                              box.next('.dropdown-spacer').remove();
                          }, 330);
                      }
                  });



                  // Remove styles when option is selected
                  options.on('click.clonePage', function() {
                      const text = $(this).text();
                      const value = $(this).data('value');
                      selectedValue.text(text).css('color', '#3f3f3f');
                      console.log(value);
                      console.log(hiddenInput);

                      hiddenInput.val(value);
                      clearBtn.removeClass('d-none');
                      options.removeClass('active');
                      $(this).addClass('active');
                      box.removeClass('open');
                      title.css('color', ''); // retain title color
                      icon.css('color', ''); // retain title color
                      searchInput.val('').trigger('keyup');

                      var borderDiv = options.closest('.edit-border');

                      borderDiv.siblings('.validation-error').remove();

                      // Run validation helper for the associated hidden input (by id or name)
                      const selector = hiddenInput.attr('id') || hiddenInput.attr('name') || '';
                      if (selector) {
                          // validateFieldByIdOrName will mark/clear validation as needed
                          validateFieldByIdOrName(selector);
                      }
                      // Remove inline styles when closing

                      setTimeout(() => {
                          box.css({
                              position: '',
                              width: '',
                              zIndex: '',
                              'border-color': '',
                              'box-shadow': ''
                          });
                          box.next('.dropdown-spacer').remove();
                      }, 350);
                  }); // --- Select option ---
                  options.on('click.clonePage', function() {
                      const text = $(this).text();
                      const value = $(this).data('value');
                      selectedValue.text(text).css('color', '#3f3f3f');
                      hiddenInput.val(value);
                      clearBtn.removeClass('d-none');
                      options.removeClass('active');
                      $(this).addClass('active');
                      box.removeClass('open');
                  });

                  // --- Clear selection ---
                  clearBtn.on('click.clonePage', function(e) {
                      e.stopPropagation();
                      selectedValue.text('Select').css('color', '#999');
                      hiddenInput.val('');
                      options.removeClass('active');
                      $(this).addClass('d-none');
                      box.removeClass('open active');
                      searchInput.val('').trigger('keyup');
                  });

                  // --- Search filter ---
                  searchInput.on('click keyup', function(e) {
                      e.stopPropagation();
                  });
                  searchInput.on('keyup', function() {
                      const term = $(this).val().toLowerCase();
                      options.each(function() {
                          const text = $(this).text().toLowerCase();
                          if (!$(this).hasClass('search-option')) {
                              $(this).toggle(text.includes(term));
                          }
                      });
                  });

                  // --- Close on outside click ---
                  //   $(document).on('click.clonePage', function(e) {
                  //       if (!box.is(e.target) && box.has(e.target).length === 0) {
                  //           box.removeClass('open');
                  //       }
                  //   });
                  $(document).on('click.clonePage', function(e) {
                      if (!box.is(e.target) && box.has(e.target).length === 0) {

                          box.removeClass('open');

                          // Reset styles ALWAYS on outside click
                          setTimeout(() => {
                              box.css({
                                  position: '',
                                  width: '',
                                  zIndex: '',
                                  minHeight: ''
                              });
                              box.next('.dropdown-spacer').remove();
                          }, 330);
                      }
                      searchInput.val('').trigger('keyup');
                  });

              });



              // -------------------------
              // MULTI SELECT DROPDOWNS
              // -------------------------
              //   $(document).on('click.clonePage', '.multi-select-dropdown .dropdown-display', function(e) {
              //       e.stopPropagation();
              //       console.log('hhhh');

              //       const dropdown = $(this).closest('.multi-select-dropdown');
              //       $('.multi-select-dropdown').not(dropdown).removeClass('open');
              //       dropdown.toggleClass('open');
              //   });
              $(document)
                  .off('click.multiSelect', '.multi-select-dropdown .dropdown-display')
                  .on('click.multiSelect', '.multi-select-dropdown .dropdown-display', function(e) {
                      e.stopPropagation();

                      const dropdown = $(this).closest('.multi-select-dropdown');
                      $('.multi-select-dropdown').not(dropdown).removeClass('open');
                      dropdown.toggleClass('open');
                  });


              function updateSelectedValues(dropdown) {
                  const selectedCheckboxes = dropdown.find("input[type='checkbox']:checked");
                  const selected = selectedCheckboxes.map(function() {
                      return $(this).val();
                  }).get();
                  const textList = selectedCheckboxes.map(function() {
                      return $(this).closest('label').text().trim();
                  }).get();

                  if (selected.length > 0) {
                      dropdown.find('.selected-value').text(textList.join(', '));
                      dropdown.find('.clear-icon').removeClass('d-none');
                  } else {
                      dropdown.find('.selected-value').text('Select');
                      dropdown.find('.clear-icon').addClass('d-none');
                  }

                  dropdown.find('input[type=hidden]').val(selected.join(','));
              }

              function updateOkButtonState(dropdown) {
                  const checkedCount = dropdown.find("input[type='checkbox']:checked").length;
                  $('#add_affiliate').prop('disabled', checkedCount === 0);
              }

              $(document).on('click.clonePage', '.multi-select-dropdown input[type="checkbox"]', function(e) {
                  e.stopPropagation();
                  const dropdown = $(this).closest('.multi-select-dropdown');
                  updateSelectedValues(dropdown);
                  updateOkButtonState(dropdown);
                  setTimeout(() => dropdown.addClass('open'), 0);
              });

              $(document).on('click.clonePage', '.multi-select-dropdown .select-all', function(e) {
                  e.stopPropagation();
                  const dropdown = $(this).closest('.multi-select-dropdown');
                  dropdown.find("input[type='checkbox']").prop('checked', true);
                  updateSelectedValues(dropdown);
                  updateOkButtonState(dropdown);
                  dropdown.addClass('open');
              });

              $(document).on('click.clonePage', '.multi-select-dropdown .deselect-all', function(e) {
                  e.stopPropagation();
                  const dropdown = $(this).closest('.multi-select-dropdown');
                  dropdown.find("input[type='checkbox']").prop('checked', false);
                  updateSelectedValues(dropdown);
                  updateOkButtonState(dropdown);
                  dropdown.addClass('open');
              });

              // ✅ Clear icon works open or closed
              $(document).on('click.clonePage', '.multi-select-dropdown .clear-icon', function(e) {
                  e.stopPropagation();
                  const dropdown = $(this).closest('.multi-select-dropdown');
                  dropdown.find("input[type='checkbox']").prop('checked', false);
                  updateSelectedValues(dropdown);
                  updateOkButtonState(dropdown);
                  dropdown.removeClass('open active');
              });

              //   $(document).on('keyup', '.multi-select-dropdown .dropdown-search', function() {
              //       const searchText = $(this).val().toLowerCase();
              //       const dropdown = $(this).closest('.multi-select-dropdown');

              //       dropdown.find('.dropdown-options li').each(function() {
              //           const text = $(this).text().toLowerCase();
              //           $(this).toggle(
              //               text.includes(searchText) ||
              //               $(this).hasClass('search-option') ||
              //               $(this).find('.multi-actions').length
              //           );
              //       });
              //       dropdown.addClass('open');
              //   });
              $(document).on('keyup', '.multi-select-dropdown .dropdown-search', function() {
                  const searchText = $(this).val().toLowerCase();
                  const dropdown = $(this).closest('.multi-select-dropdown');

                  dropdown.find('.dropdown-options li').each(function(index) {
                      const text = $(this).text().toLowerCase();
                      const isSearchOption = $(this).hasClass('search-option');
                      const hasMultiActions = $(this).find('.multi-actions').length > 0;

                      const shouldShow =
                          text.includes(searchText) ||
                          isSearchOption ||
                          hasMultiActions;

                      $(this).toggle(shouldShow);
                  });

                  dropdown.addClass('open');
              });


              $(document).on('click.clonePage', function(e) {
                  if (!$(e.target).closest('.multi-select-dropdown').length) {
                      $('.multi-select-dropdown').removeClass('open');
                  }
              });

              $('#add_affiliate').prop('disabled', true);
              $('#add_email').prop('disabled', true);


              // -------------------------
              // ADD-BUTTON DROPDOWNS (like Currency)
              // -------------------------
              $(document).on('click.clonePage', '.addable-dropdown .dropdown-display', function(e) {
                  e.stopPropagation();
                  const dropdown = $(this).closest('.custom-dropdown');
                  $('.custom-dropdown').not(dropdown).removeClass('open');
                  dropdown.toggleClass('open');
              });

              $(document).on('click.clonePage', '.addable-dropdown .dropdown-options li', function(e) {
                  const $item = $(this);
                  const dropdown = $item.closest('.custom-dropdown');
                  const value = $item.data('value');
                  const text = $item.text();

                  dropdown.find('.selected-value').text(text).css('color', '#3f3f3f');
                  dropdown.find('li').removeClass('active');
                  $item.addClass('active');
                  dropdown.removeClass('open');

                  dropdown.find('input[type=hidden]').val(value);
                  dropdown.find('.clear-icon').removeClass('d-none');
              });

              $(document).on('input', '.addable-dropdown .dropdown-search', function() {
                  const search = $(this).val().toLowerCase();
                  $(this).closest('.dropdown-options').find('li[data-value]').each(function() {
                      const text = $(this).text().toLowerCase();
                      $(this).toggle(text.includes(search));
                  });
              });

              $(document).on('click.clonePage', '.addable-dropdown .add-new-btn', function(e) {
                  e.preventDefault();
                  e.stopPropagation();
                  const targetModal = $(this).data('target');
                  $(targetModal).find('input').val('');
                  $(targetModal).modal('show');
              });

              $(document).on('click.clonePage', '.addable-save-btn', function() {
                  const btn = $(this);
                  const modal = btn.closest('.modal');
                  const dropdownClass = modal.data('dropdown'); // e.g. 'currency-dropdown'

                  // Look for input with common names
                  const inputField =
                      modal.find('input[name=new_value]');
                  const newValue = (inputField.val() || '').trim();

                  if (newValue === '') {
                      Dashmix.helpers('notify', {
                          message: '⚠ Please enter a value',
                          delay: 3000
                      });
                      return;
                  }

                  const dropdown = $(`.${dropdownClass}`);

                  const exists = dropdown.find('li[data-value]').filter(function() {
                      return $(this).text().trim().toLowerCase() === newValue.toLowerCase();
                  }).length > 0;

                  if (exists) {
                      Dashmix.helpers('notify', {
                          message: '⚠ This value already exists',
                          delay: 3000
                      });
                      return;
                  }

                  const newOption = $(`<li data-value="${newValue}" class="active">${newValue}</li>`);
                  dropdown.find('li').removeClass('active');
                  dropdown.find('.dropdown-options').append(newOption);
                  dropdown.find('.selected-value').text(newValue);
                  dropdown.find('input[type=hidden]').val(newValue);
                  dropdown.find('.clear-icon').removeClass('d-none');
                  modal.modal('hide');

                  var toast = $('.' + dropdownClass).find('.toast-added');
                  if (toast) {
                      toast.fadeIn();
                      setTimeout(() => {
                          toast.fadeOut();
                      }, 3000);
                  } else {
                      Dashmix.helpers('notify', {
                          message: '✅ Added successfully',
                          delay: 2000
                      });

                  }
              });



              $('.select2').select2();

              $('.selectpicker').selectpicker();

              const $switch2 = $('#customSwitch2');
              const $switch4 = $('#customSwitch4');
              const $btn2 = $('.addEmail');

              // 🔒 Disable button by default
              $btn2.prop('disabled', true).css({
                  opacity: 0.5,
                  cursor: 'not-allowed'
              });

              // 🔧 Function to update UI based on switch state
              function updateSwitch2State() {
                  if ($switch2.is(':checked')) {
                      $('.switch-text2').text('Email notifications enabled');
                      $btn2.prop('disabled', false).css({
                          opacity: 1,
                          cursor: 'pointer'
                      });
                  } else {
                      $('.switch-text2').text('Email notifications disabled');
                      $btn2.prop('disabled', true).css({
                          opacity: 0.5,
                          cursor: 'not-allowed'
                      });
                  }
              }

              function updateSwitch4State() {
                  if ($switch4.is(':checked')) {
                      $('.switch-text4').text('Access to manage student vacations enabled');
                  } else {
                      $('.switch-text4').text('Access to manage student vacations disabled');
                  }
              }

              function syncFatherProvinceDisplay(value) {
                  const $dropdown = $('.father-province-custom-dropdown');
                  const text = value || 'Select Province';
                  $dropdown.attr('data-selected-id', value || '');
                  $dropdown.attr('data-selected-text', value || '');
                  $dropdown.find('.selected-value')
                      .text(text)
                      .toggleClass('text-placeholder', !value);
                  $dropdown.find('.clear-icon').toggleClass('d-none', !value);
                  $('#father_province').val(value || '');
                  $dropdown.find('li').removeClass('selected');
                  if (value) {
                      $dropdown.find(`li[data-value="${value}"]`).addClass('selected');
                  }
              }

              // ✅ Initialize correct state based on PHP value
              updateSwitch2State();
              updateSwitch4State();

              // 🔄 Listen for toggle change
              $switch2.on('change', updateSwitch2State);
              $switch4.on('change', updateSwitch4State);

              $(document).off('click.clonePage', '#copy_address_from_mother_clone').on('click.clonePage',
                  '#copy_address_from_mother_clone',
                  function(e) {
                      e.preventDefault();
                      $('#father_client_address').val($('#client_address').val());
                      $('#father_city').val($('#city').val());
                      $('#father_postal_code').val($('#postal_code').val());
                      syncFatherProvinceDisplay($('#edit_province').val());
                  });

              let editingRow = null;

              // Add Email Button (open modal)
              $('.addEmail').click(function() {
                  editingRow = null; // reset edit mode
                  var contract = $(this).data('contract');
                  $('#addEmailModal #contract_id').val(contract);
                  $('#email_id').val('');
                  $('#add_email').text('OK');
                  $('#add_email').prop('disabled', true);
                  $('#addEmailModal').modal('show');
              });

              // Enable/disable OK button only if valid email
              $('#email_id').on('input', function() {
                  const emailVal = $(this).val().trim();
                  const isValidEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal);

                  $('#add_email').prop('disabled', !isValidEmail);
              });

              // Emails from backend
              var existingEmails = @json($emails ?? []);

              existingEmails.forEach(function(email) {
                  if (!email) return;

                  var contract_id = "{{ $contract->id ?? '' }}";

                  var emailRow = `
            <tr class="affiliate-item banner-icon" data-email-id="${email}">
                <td class="py-2 border-0 align-middle" width="20" style="border-radius: 13px 0 0 13px;">
                    <i class="fa-light fa-grip-vertical drag-handle cursor-grab text-grey fs-16" 
                       title="Drag" style="opacity:0; transition:opacity 0.2s;"></i>
                </td>
                <td class="py-2 border-0 ">
                    <button type="button" class="btn mr-1 p-0">
                        <i class="fa-thin fa-envelope text-grey fs-18 regular-icon"></i>
                        <i class="fa-solid fa-envelope text-darkgrey fs-18 header-solid-icon"></i>
                    </button>
                    <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">${email}</span>
                    <input type="hidden" name="email_ids[]" value="${email}">
                </td>
                <td class="py-2 border-0 text-right align-middle drag-handle" width="50" style="border-radius: 0 13px 13px 0;opacity:0">
                    <a class="dropdown-toggle drag-handle text-grey banner-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:;">
                        <i class="fa-thin fa-ellipsis-stroke-vertical fs-20"></i>
                    </a>
                    <div class="dropdown-menu py-0" aria-labelledby="dropdown-dropright-primary">
                        <a class="dropdown-item d-flex align-items-center edit-email mb-0" 
                           data-email="${email}" data-contract="${contract_id}">
                            <i class="fa-light fa-pencil mr-2 fs-15"></i>
                            <span class="fs-15 fw-400">Edit</span>
                        </a>
                        <a class="dropdown-item d-flex align-items-center remove-email mb-0">
                            <i class="fa-light fa-circle-xmark mr-2 fs-15"></i>
                            <span class="fs-15 fw-400">Delete</span>
                        </a>
                    </div>
                </td>
            </tr>
        `;

                  $('.added-emails tbody').append(emailRow);
                  setTimeout(() => {
                      if ($('.added-emails tbody tr').length > 0) {
                          $('.no-email').hide()
                      } else {
                          $('.no-email').show()
                      }
                  }, 200);
              });

              $('#edit_client_id').change(function() {
                  var id = $(this).val();
                  console.log(id);
                  $.ajax({
                      type: 'get',
                      data: {
                          id: id
                      },
                      url: '{{ url('get-contract-notification') }}',
                      success: function(res) {
                          console.log(res);
                      }
                  })
              })

              function loadDefaultEmailsByClient(id) {
                  $.ajax({
                      type: 'get',
                      data: {
                          id
                      },
                      url: '{{ url('get-contract-notification') }}',
                      success: function(res) {

                          $('.added-emails tbody').empty();

                          if (res.length > 0) {
                              res.forEach(item => {
                                  appendEmailRow(item.renewal_email);
                              });
                          }

                          if ($('.added-emails tbody tr').length === 0) {
                              $('.no-email').show();
                          }
                      }
                  });
              }


              function renderEmailRow(emailId) {
                  return `
                        <tr class="affiliate-item banner-icon" data-email-id="${emailId}">
                            <td class="py-2 border-0 align-middle" width="20" style="border-radius: 13px 0 0 13px;">
                                <i class="fa-light fa-grip-vertical drag-handle cursor-grab text-grey fs-16"
                                title="Drag" style="opacity:0; transition:opacity 0.2s;"></i>
                            </td>
                            <td class="py-2 border-0">
                                <button type="button" class="btn mr-1 p-0">
                                    <i class="fa-thin fa-envelope text-grey fs-18 regular-icon"></i>
                                    <i class="fa-solid fa-envelope text-darkgrey fs-18 header-solid-icon"></i>
                                </button>
                                <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">${emailId}</span>
                                <input type="hidden" name="email_ids[]" value="${emailId}">
                            </td>
                            <td class="py-2 border-0 text-right align-middle drag-handle" width="50" style="border-radius: 0 13px 13px 0;opacity:0">
                                <a class="dropdown-toggle drag-handle text-grey banner-icon" data-toggle="dropdown" href="javascript:;">
                                    <i class="fa-thin fa-ellipsis-stroke-vertical fs-20"></i>
                                </a>
                                <div class="dropdown-menu py-0">
                                    <a class="dropdown-item d-flex align-items-center edit-email"
                                    data-email="${emailId}" data-contract="">
                                        <i class="fa-light fa-pencil mr-2 fs-15"></i>
                                        <span class="fs-15 fw-400">Edit</span>
                                    </a>
                                    <a class="dropdown-item d-flex align-items-center remove-email">
                                        <i class="fa-light fa-circle-xmark mr-2 fs-15"></i>
                                        <span class="fs-15 fw-400">Delete</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
              }

              function emailExists(email) {
                  let exists = false;

                  $('.added-emails tbody input[name="email_ids[]"]').each(function() {
                      if ($(this).val().toLowerCase() === email.toLowerCase()) {
                          exists = true;
                          return false; // break loop
                      }
                  });

                  return exists;
              }

              function appendEmailRow(emailId) {
                  if (emailExists(emailId)) {
                      return false; // duplicate — do not append
                  }

                  $('.added-emails tbody').append(renderEmailRow(emailId));
                  $('.no-email').hide();

                  return true;
              }


              // OK button (Add or Update)
              $('#add_email').on('click.clonePage', function() {
                  var emailId = $("#email_id").val().trim();
                  var contract_id = $('#addEmailModal #contract_id').val();

                  // Validate email again before proceeding
                  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailId)) {
                      return; // invalid — do nothing
                  }

                  // If editing an existing row
                  if (editingRow) {
                      $(editingRow).find('.selected-aff-client').text(emailId);
                      $(editingRow).find('input[name="email_ids[]"]').val(emailId);

                      // Update both attr and jQuery data cache
                      const editLink = $(editingRow).find('.edit-email');
                      editLink.attr('data-email', emailId);
                      editLink.data('email', emailId);

                      editingRow = null;
                  }
                  // Else add new row
                  else {
                      var affiliateRow = `
                            <tr class="affiliate-item banner-icon" data-email-id="${emailId}">
                                <td class="py-2 border-0 align-middle" width="20" style="border-radius: 13px 0 0 13px;">
                                    <i class="fa-light fa-grip-vertical drag-handle cursor-grab text-grey fs-16" 
                                    title="Drag" style="opacity:0; transition:opacity 0.2s;"></i>
                                </td>
                                <td class="py-2 border-0 ">
                                    <button type="button" class="btn mr-1 p-0">
                                        <i class="fa-thin fa-envelope text-grey fs-18 regular-icon"></i>
                                        <i class="fa-solid fa-envelope text-darkgrey fs-18 header-solid-icon"></i>
                                    </button>
                                    <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">${emailId}</span>
                                    <input type="hidden" name="email_ids[]" value="${emailId}">
                                </td>
                                <td class="py-2 border-0 text-right align-middle drag-handle" width="50" style="border-radius: 0 13px 13px 0;opacity:0">
                                    <a class="dropdown-toggle drag-handle text-grey banner-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:;">
                                        <i class="fa-thin fa-ellipsis-stroke-vertical fs-20"></i>
                                    </a>
                                    <div class="dropdown-menu py-0" aria-labelledby="dropdown-dropright-primary">
                                        <a class="dropdown-item d-flex align-items-center edit-email mb-0" 
                                        data-email="${emailId}" data-contract="${contract_id}">
                                            <i class="fa-light fa-pencil mr-2 fs-15"></i>
                                            <span class="fs-15 fw-400">Edit</span>
                                        </a>
                                        <a class="dropdown-item d-flex align-items-center remove-email mb-0">
                                            <i class="fa-light fa-circle-xmark mr-2 fs-15"></i>
                                            <span class="fs-15 fw-400">Delete</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `;
                      $('.added-emails tbody').append(affiliateRow);
                  }

                  // Reset modal after success
                  $('#addEmailModal').modal('hide');
                  $('#email_id').val('');
                  $('#add_email').prop('disabled', true);
                  $('#add_email').text('OK');
                  showToast(editingRow ? 'email-toast-updated' : 'email-toast-added');
                  setTimeout(() => {
                      if ($('.added-emails tbody tr').length > 0) {
                          $('.no-email').hide()
                      } else {
                          $('.no-email').show()
                      }
                  }, 200);
              });

              //   show toast notification
              function showToast(cls, timeout = 3000) {
                  const $el = $('.' + cls);
                  $el.stop(true, true);
                  const prev = $el.data('toastTimeout');
                  if (prev) clearTimeout(prev);

                  $el.fadeIn(200);
                  const t = setTimeout(() => {
                      $el.fadeOut(200);
                      $el.removeData('toastTimeout');
                  }, timeout);
                  $el.data('toastTimeout', t);
              } // Edit Email Row
              $(document).on('click.clonePage', '.edit-email', function() {
                  var email = $(this).data('email');
                  var contract = $(this).data('contract');
                  editingRow = $(this).closest('tr');

                  $('#addEmailModal #contract_id').val(contract);
                  $('#email_id').val(email);
                  $('#add_email').text('Update');

                  // Validate and enable only if email is valid
                  const isValidEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                  $('#add_email').prop('disabled', !isValidEmail);

                  $('#addEmailModal').modal('show');
              });

              // Delete Email Row
              $(document).on('click.clonePage', '.remove-email', function() {
                  var $tr = $(this).closest('tr');
                  var rowHtml = $tr.prop('outerHTML');
                  var index = $tr.index();
                  $('.dropdown-menu').removeClass('show');
                  $('.dropdown-menu').removeAttr('style');
                  // var dropdown = $(this).closest('.dropdown-menu');
                  // dropdown.removeClass('show'); // Close dropdown

                  // Ensure global stack for deleted emails
                  window._deletedEmails = window._deletedEmails || [];

                  // Remove row from DOM (animate then remove)
                  $tr.fadeOut(150, function() {
                      $(this).remove();
                  });

                  // Push deleted item onto stack with a timeout to finalize removal
                  var item = {
                      html: rowHtml,
                      index: index,
                      timeoutId: null
                  };

                  // After 6s, finalize (remove from stack) if not undone
                  item.timeoutId = setTimeout(function() {
                      var i = window._deletedEmails.indexOf(item);
                      if (i !== -1) window._deletedEmails.splice(i, 1);
                  }, 6000);

                  window._deletedEmails.push(item);

                  // Show deleted toast (this toast contains the Undo button)
                  showToast('email-toast-deleted', 5000);
                  setTimeout(() => {
                      if ($('.added-emails tbody tr').length > 0) {
                          $('.no-email').hide()
                      } else {
                          $('.no-email').show()
                      }
                  }, 200);

              });

              // Undo handler (delegated)
              $(document).on('click.clonePage', '.undo-delete-email', function(e) {
                  e.preventDefault();

                  window._deletedEmails = window._deletedEmails || [];
                  if (window._deletedEmails.length === 0) return;

                  // Pop last deleted item (LIFO undo)
                  var item = window._deletedEmails.pop();

                  // Cancel finalize timeout
                  if (item.timeoutId) clearTimeout(item.timeoutId);

                  var $tbody = $('.added-emails tbody');

                  // Insert back at original position if possible
                  if ($tbody.children().length > item.index) {
                      $tbody.children().eq(item.index).before(item.html);
                  } else {
                      $tbody.append(item.html);
                  }

                  // Small visual feedback
                  var $restoredRow = $tbody.children().eq(Math.min(item.index, $tbody.children().length - 1));
                  $restoredRow.css('background-color', '#e9f7ef').hide().fadeIn(200).delay(600).queue(
                      function(next) {
                          $(this).css('background-color', '');
                          next();
                      });

                  // Show recovered toast briefly
                  showToast('email-toast-recovered');
                  setTimeout(() => {
                      if ($('.added-emails tbody tr').length > 0) {
                          $('.no-email').hide()
                      } else {
                          $('.no-email').show()
                      }
                  }, 200);
              });



              const $switch = $('#customSwitch1');
              const $switchEdit = $('#customSwitch1Edit');
              const $btn = $('.addAffiliateClient');

              // 🔒 Disable button by default
              $btn.prop('disabled', true).css({
                  opacity: 0.5,
                  cursor: 'not-allowed'
              });

              // ✅ Handle toggle change
              $switch.on('change', function() {
                  if ($(this).is(':checked')) {
                      $('.switch-text').text('Take work home required');
                      $btn.prop('disabled', false).css({
                          opacity: 1,
                          cursor: 'pointer'
                      });
                  } else {
                      $('.switch-text').text('Take work home not required');
                      $btn.prop('disabled', true).css({
                          opacity: 0.5,
                          cursor: 'not-allowed'
                      });
                  }
              });
              $switchEdit.on('change', function() {
                  if ($(this).is(':checked')) {
                      $('.switch-text').text('Take work home required');
                      $btn.prop('disabled', false).css({
                          opacity: 1,
                          cursor: 'pointer'
                      });
                  } else {
                      $('.switch-text').text('Take work home not required');
                      $btn.prop('disabled', true).css({
                          opacity: 0.5,
                          cursor: 'not-allowed'
                      });
                  }
              });

              $('.addAffiliateClient').click(function() {
                  var contract = $(this).data('contract');
                  $('#addAffiliateClient #contract_id').val(contract);
                  $('#addAffiliateClient').modal('show');
              });


              // Initialize SortableJS on the table body
              document.querySelectorAll(
                      '.added-affiliates tbody, .added-emails tbody, .studentTable tbody, .paymentTable tbody, .vacationTable tbody'
                  )
                  .forEach(function(el) {
                      new Sortable(el, {
                          handle: '.drag-handle',
                          animation: 150
                      });
                  });

              // Handle OK button click
              $('#add_affiliate').on('click.clonePage', function() {

                  // var selectedOptions = $('.multi-select-dropdown .dropdown-options input[type="checkbox"]:checked');
                  // Select all checked checkboxes in multi-select dropdowns except assets-custom-dropdown
                  var selectedOptions = $(
                      '.multi-select-dropdown:not(.assets-custom-dropdown) .dropdown-options input[type="checkbox"]:checked'
                  );

                  selectedOptions.each(function() {
                      var clientId = $(this).val();
                      var clientName = $(this).closest('label').find('span').text().trim();

                      if (!clientId || clientName === '') return;
                      if ($('.added-affiliates').find('[data-client-id="' + clientId + '"]').length >
                          0) return;

                      var affiliateRow = `
                                <tr class="affiliate-item banner-icon" data-client-id="${clientId}">
                                    <td class="py-2 border-0 align-middle" width="20" style="border-radius: 13px 0 0 13px;">
                                        <i class="fa-light fa-grip-vertical drag-handle cursor-grab text-grey fs-16" 
                                        title="Drag" 
                                        style="opacity:0; transition:opacity 0.2s;"></i>
                                    </td>
                                    <td class="py-2 border-0 ">
                                        <button type="button" class="btn mr-1 p-0">
                                            <i class="fa-thin fa-people-arrows text-grey fs-18 regular-icon"></i>
                                            <i class="fa-solid fa-people-arrows text-darkgrey fs-18 header-solid-icon"></i>
                                        </button>
                                        <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">${clientName}</span>
                                        <input type="hidden" name="affiliate_ids[]" value="${clientId}">
                                    </td>
                                    <td class="py-2 border-0 text-right align-middle drag-handle" width="50" style="border-radius: 0 13px 13px 0;opacity:0">
                                        <a class="dropdown-toggle drag-handle text-grey banner-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:;">
                                            <i class="fa-thin fa-ellipsis-stroke-vertical fs-20"></i>
                                        </a>
                                        <div class="dropdown-menu py-0"
                                        aria-labelledby="dropdown-dropright-primary">
                                        <a class="dropdown-item d-flex align-items-center remove-affiliate mb-0"
                                            data="">
                                            <i class="fa-light fa-circle-xmark mr-2 fs-15"></i>
                                            <span class="fs-15 fw-400">Delete</span>
                                        </a>
                                        </div>
                                    </td>
                                </tr>
                            `;

                      $('.added-affiliates tbody').append(affiliateRow);
                  });
                  setTimeout(() => {
                      if ($('.added-affiliates tbody tr').length > 0) {
                          $('.no-affilate').hide()
                      } else {
                          $('.no-affilate').show()
                      }
                  }, 200);

                  if (selectedOptions.length > 0) {
                      $('#addAffiliateClient').modal('hide');
                  }

                  $('#modal_client option[value=""]').prop('selected', false);
                  $('#modal_client').val('').selectpicker('refresh');
                  showToast('affiliate-toast-added');
              });

              // Remove affiliate row
              $(document).on('click.clonePage', '.remove-affiliate', function() {
                  var $tr = $(this).closest('tr');
                  var rowHtml = $tr.prop('outerHTML');
                  var index = $tr.index();
                  $('.dropdown-menu').removeClass('show');
                  $('.dropdown-menu').removeAttr('style');

                  // ensure global stack
                  window._deletedAffiliates = window._deletedAffiliates || [];

                  // remove from DOM (animate)
                  $tr.fadeOut(150, function() {
                      $(this).remove();
                  });

                  // push to stack with finalize timeout
                  var item = {
                      html: rowHtml,
                      index: index,
                      timeoutId: null
                  };
                  item.timeoutId = setTimeout(function() {
                      var i = window._deletedAffiliates.indexOf(item);
                      if (i !== -1) window._deletedAffiliates.splice(i, 1);
                  }, 6000);
                  window._deletedAffiliates.push(item);

                  // show undo notification (uses Dashmix notify so we don't need extra DOM toast markup)
                  showToast('affiliate-toast-deleted', 5000);
                  setTimeout(() => {
                      if ($('.added-affiliates tbody tr').length > 0) {
                          $('.no-affilate').hide()
                      } else {
                          $('.no-affilate').show()
                      }
                  }, 200);
              });

              // Undo handler for affiliates
              $(document).on('click.clonePage', '.undo-delete-affiliate', function(e) {
                  e.preventDefault();
                  window._deletedAffiliates = window._deletedAffiliates || [];
                  if (window._deletedAffiliates.length === 0) return;

                  // LIFO undo (restore last removed affiliate)
                  var item = window._deletedAffiliates.pop();

                  // cancel finalize timeout
                  if (item.timeoutId) clearTimeout(item.timeoutId);

                  var $tbody = $('.added-affiliates tbody');

                  // Insert back at original position if possible
                  if ($tbody.children().length > item.index) {
                      $tbody.children().eq(item.index).before(item.html);
                  } else {
                      $tbody.append(item.html);
                  }

                  // small visual feedback
                  var $restoredRow = $tbody.children().eq(Math.min(item.index, $tbody.children().length - 1));
                  $restoredRow.css('background-color', '#e9f7ef').hide().fadeIn(200).delay(600).queue(
                      function(next) {
                          $(this).css('background-color', '');
                          next();
                      });

                  // optional recovered toast
                  showToast('affiliate-toast-recovered');
                  setTimeout(() => {
                      if ($('.added-affiliates tbody tr').length > 0) {
                          $('.no-affilate').hide()
                      } else {
                          $('.no-affilate').show()
                      }
                  }, 200);
              }); // Show drag icon only on hover
              $(document).on('mouseenter', '.affiliate-item', function() {
                  $(this).find('.drag-handle').css('opacity', '1');
              }).on('mouseleave', '.affiliate-item', function() {
                  $(this).find('.drag-handle').css('opacity', '0');
              });


              // When modal closes, reset dropdown selections
              $('#addAffiliateClient').on('hidden.bs.modal', function() {
                  const dropdown = $(this).find('.multi-select-dropdown');

                  // Uncheck all checkboxes
                  dropdown.find('input[type="checkbox"]').prop('checked', false);

                  // Reset text and clear icon
                  dropdown.find('.selected-value').text('Select Client');
                  dropdown.find('.clear-icon').addClass('d-none');

                  // Clear hidden input
                  $('#client_ids').val('');

                  // Close dropdown visually
                  dropdown.removeClass('open');

                  $('#add_affiliate').prop('disabled', true);

              });


            //   function initCustomCalendar(containerId, inputId, clearBtnId, defaultDate) {
            //       const dateContainer = document.getElementById(containerId);
            //       const dateInput = document.getElementById(inputId);
            //       const clearDateBtn = document.getElementById(clearBtnId);

            //       // Check if elements exist
            //       if (!dateContainer || !dateInput || !clearDateBtn) {
            //           console.error('Required elements not found:', {
            //               containerId,
            //               inputId,
            //               clearBtnId
            //           });
            //           return;
            //       }

            //       const inlineCalendar = dateContainer.querySelector('.inline-calendar-container');
            //       if (!inlineCalendar) {
            //           console.error('inline-calendar-container not found in', containerId);
            //           return;
            //       }

            //       // Remove any existing listeners to prevent duplicates
            //       dateInput.removeEventListener('click', handleInputClick);
            //       clearDateBtn.removeEventListener('click', handleClearClick);
            //       document.removeEventListener('click', handleOutsideClick);

            //       // Clear any existing content
            //       inlineCalendar.innerHTML = '';

            //       let flatpickrInstance = null;
            //       let isOpen = false;
            //       let spacer = null;

            //       // Create a hidden input for flatpickr
            //       const calendarInput = document.createElement('input');
            //       calendarInput.type = 'text';
            //       calendarInput.style.display = 'none';
            //       inlineCalendar.appendChild(calendarInput);

            //       // Parse defaultDate if it's a string
            //       let parsedDefaultDate = null;
            //       if (defaultDate) {
            //           if (typeof defaultDate === 'string') {
            //               parsedDefaultDate = new Date(defaultDate);
            //           } else if (defaultDate instanceof Date) {
            //               parsedDefaultDate = defaultDate;
            //           }
            //       }

            //       // Initialize flatpickr
            //       try {
            //           flatpickrInstance = flatpickr(calendarInput, {
            //               inline: true,
            //               defaultDate: parsedDefaultDate,
            //               dateFormat: "Y-m-d",
            //               altFormat: "d-M-Y",
            //               altInput: false,
            //               static: true,
            //               appendTo: inlineCalendar,
            //               onReady: function(selectedDates, dateStr, instance) {
            //                   // Simple styling
            //                   const calendarElement = instance.calendarContainer;
            //                   if (calendarElement) {
            //                       calendarElement.style.width = '100%';
            //                       calendarElement.style.border = 'none';
            //                       calendarElement.style.boxShadow = 'none';
            //                       calendarElement.style.marginTop = '15px';
            //                   }

            //                   // Initially hide the calendar
            //                   instance.calendarContainer.style.display = 'none';

            //                   // Set initial value if defaultDate is provided
            //                   if (selectedDates.length > 0 && parsedDefaultDate) {
            //                       dateInput.value = instance.formatDate(selectedDates[0], "d-M-Y");
            //                       clearDateBtn.classList.remove('d-none');
            //                   }
            //               },
            //               onChange: function(selectedDates, dateStr, instance) {
            //                   if (selectedDates.length > 0) {
            //                       dateInput.value = instance.formatDate(selectedDates[0], "d-M-Y");
            //                       dateContainer.removeAttribute("style");
            //                       const title = dateContainer.querySelector("h6");
            //                       if (title) title.removeAttribute("style");
            //                       const calendarIcon = dateContainer.querySelector(".constant-icon");
            //                       if (calendarIcon) calendarIcon.removeAttribute("style");

            //                       // Remove validation errors if using jQuery
            //                       if (window.$) {
            //                           $(dateContainer).siblings('.validation-error').remove();
            //                       }

            //                       toggleCalendar();
            //                       clearDateBtn.classList.remove('d-none');
            //                   } else {
            //                       dateInput.value = '';
            //                       clearDateBtn.classList.add('d-none');
            //                   }
            //               }
            //           });
            //       } catch (error) {
            //           console.error('Failed to initialize flatpickr:', error);
            //           return;
            //       }

            //       // Create spacer element
            //       function createSpacer() {
            //           spacer = document.createElement('div');
            //           spacer.className = 'calendar-spacer';
            //           spacer.style.height = dateContainer.offsetHeight + 'px';
            //           spacer.style.visibility = 'hidden';
            //           spacer.style.opacity = '0';
            //           spacer.style.pointerEvents = 'none';
            //           dateContainer.parentNode.insertBefore(spacer, dateContainer.nextSibling);
            //       }

            //       // Remove spacer element
            //       function removeSpacer() {
            //           if (spacer && spacer.parentNode) {
            //               spacer.parentNode.removeChild(spacer);
            //               spacer = null;
            //           }
            //       }

            //       // Get the offset parent
            //       function getOffsetParent(element) {
            //           let offsetParent = element.offsetParent;

            //           while (offsetParent &&
            //               offsetParent !== document.body &&
            //               window.getComputedStyle(offsetParent).position === 'static') {
            //               offsetParent = offsetParent.offsetParent;
            //           }

            //           return offsetParent || document.body;
            //       }

            //       // Toggle calendar visibility
            //       function toggleCalendar() {
            //           if (!flatpickrInstance) return;

            //           if (isOpen) {
            //               // Close calendar
            //               flatpickrInstance.calendarContainer.style.display = 'none';
            //               dateContainer.classList.remove('expanded');
            //               dateContainer.style.position = '';
            //               dateContainer.style.zIndex = '';
            //               dateContainer.style.width = '';
            //               dateContainer.style.top = '';
            //               dateContainer.style.left = '';

            //               // Remove spacer
            //               removeSpacer();

            //               isOpen = false;
            //           } else {
            //               // Store original dimensions
            //               const containerWidth = dateContainer.offsetWidth;
            //               const containerHeight = dateContainer.offsetHeight;

            //               // Create spacer to maintain layout
            //               createSpacer();

            //               // Get positioning context
            //               const offsetParent = getOffsetParent(dateContainer);
            //               const offsetParentRect = offsetParent.getBoundingClientRect();
            //               const containerRect = dateContainer.getBoundingClientRect();

            //               // Calculate position relative to offset parent
            //               let top = containerRect.top - offsetParentRect.top;
            //               let left = containerRect.left - offsetParentRect.left;

            //               // Set container as positioned relative to its offset parent
            //               dateContainer.style.position = 'absolute';
            //               dateContainer.style.zIndex = '1000';
            //               dateContainer.style.width = containerWidth + 'px';
            //               dateContainer.style.top = top + 'px';
            //               dateContainer.style.left = left + 'px';

            //               // Set the container's position relative to its offset parent
            //               if (offsetParent !== document.body) {
            //                   const offsetParentComputed = window.getComputedStyle(offsetParent);
            //                   if (offsetParentComputed.position === 'static') {
            //                       offsetParent.style.position = 'relative';
            //                   }
            //               }

            //               flatpickrInstance.calendarContainer.style.display = 'block';
            //               dateContainer.classList.add('expanded');

            //               // Get calendar height
            //               const calendarHeight = flatpickrInstance.calendarContainer.offsetHeight;

            //               // Calculate available space
            //               const spaceBelow = offsetParentRect.height - (top + containerHeight);
            //               const spaceAbove = top;

            //               // Check if calendar fits below
            //               if (spaceBelow >= calendarHeight || (spaceBelow >= 0 && spaceBelow > spaceAbove)) {
            //                   // Position below - already positioned correctly
            //               } else if (spaceAbove >= calendarHeight) {
            //                   // Position above
            //                   dateContainer.style.top = (top - calendarHeight - 5) + 'px';
            //               } else {
            //                   // Not enough space in either direction
            //                   if (spaceBelow > spaceAbove) {
            //                       // Position below
            //                   } else {
            //                       // Position above
            //                       dateContainer.style.top = (top - calendarHeight - 5) + 'px';
            //                   }
            //               }

            //               isOpen = true;
            //           }
            //       }

            //       // Event handler functions
            //       function handleInputClick(e) {
            //           e.stopPropagation();
            //           toggleCalendar();
            //       }

            //       function handleClearClick(e) {
            //           e.stopPropagation();
            //           if (flatpickrInstance) {
            //               flatpickrInstance.clear();
            //           }
            //           dateInput.value = '';
            //           clearDateBtn.classList.add('d-none');
            //           if (isOpen) {
            //               toggleCalendar();
            //           }
            //       }

            //       function handleOutsideClick(e) {
            //           if (!dateContainer.contains(e.target) && isOpen) {
            //               toggleCalendar();
            //           }
            //       }

            //       // Add event listeners with unique names
            //       dateInput.addEventListener('click', handleInputClick);

            //       const dateInputArea = dateInput.closest('.date-input-area');
            //       if (dateInputArea) {
            //           dateInputArea.addEventListener('click', function(e) {
            //               if (e.target !== clearDateBtn && !e.target.closest('.clear-icon')) {
            //                   toggleCalendar();
            //               }
            //           });
            //       }

            //       clearDateBtn.addEventListener('click', handleClearClick);
            //       document.addEventListener('click', handleOutsideClick);

            //       // Show clear button if date exists
            //       if (dateInput.value) {
            //           clearDateBtn.classList.remove('d-none');
            //       }

            //       // Return the instance for external control if needed
            //       return {
            //           instance: flatpickrInstance,
            //           toggleCalendar,
            //           destroy: function() {
            //               // Clean up event listeners
            //               dateInput.removeEventListener('click', handleInputClick);
            //               clearDateBtn.removeEventListener('click', handleClearClick);
            //               document.removeEventListener('click', handleOutsideClick);

            //               if (flatpickrInstance) {
            //                   flatpickrInstance.destroy();
            //               }
            //           }
            //       };
            //   }
function initCustomCalendar(containerId, inputId, clearBtnId, defaultDate) {
    const dateContainer = document.getElementById(containerId);
    const dateInput = document.getElementById(inputId);
    const clearDateBtn = document.getElementById(clearBtnId);

    // Check if elements exist
    if (!dateContainer || !dateInput || !clearDateBtn) {
        console.error('Required elements not found:', { containerId, inputId, clearBtnId });
        return;
    }

    const inlineCalendar = dateContainer.querySelector('.inline-calendar-container');
    if (!inlineCalendar) {
        console.error('inline-calendar-container not found in', containerId);
        return;
    }

    // ✅ Destroy previous instance on this container (prevents duplicate listeners / double toggle / extra spacer)
    if (dateContainer._customCalendarApi && typeof dateContainer._customCalendarApi.destroy === 'function') {
        dateContainer._customCalendarApi.destroy();
        dateContainer._customCalendarApi = null;
    }

    // ✅ Remove any leftover spacers from previous runs
    dateContainer.parentNode?.querySelectorAll('.calendar-spacer')?.forEach(s => s.remove());

    // Clear any existing content
    inlineCalendar.innerHTML = '';

    let flatpickrInstance = null;
    let isOpen = false;
    let spacer = null;

    // Create a hidden input for flatpickr
    const calendarInput = document.createElement('input');
    calendarInput.type = 'text';
    calendarInput.style.display = 'none';
    inlineCalendar.appendChild(calendarInput);

    // Parse defaultDate
    let parsedDefaultDate = null;
    if (defaultDate) {
        if (typeof defaultDate === 'string') parsedDefaultDate = new Date(defaultDate);
        else if (defaultDate instanceof Date) parsedDefaultDate = defaultDate;
    }

    // Prefer existing input value over defaultDate (prevents reset issues)
    function parseDisplayDate(str) {
        // expects "d-M-Y" e.g. "09-Feb-2026"
        if (!str) return null;
        const d = String(str).trim();
        const dt = new Date(d);
        return isNaN(dt.getTime()) ? null : dt;
    }

    // ✅ if input already has value, use it as initial date
    const fromInput = parseDisplayDate(dateInput.value);
    if (fromInput) parsedDefaultDate = fromInput;

    // Get the offset parent
    function getOffsetParent(element) {
        let offsetParent = element.offsetParent;
        while (
            offsetParent &&
            offsetParent !== document.body &&
            window.getComputedStyle(offsetParent).position === 'static'
        ) {
            offsetParent = offsetParent.offsetParent;
        }
        return offsetParent || document.body;
    }

    // Create spacer element (guarded)
    function createSpacer() {
        if (spacer && spacer.parentNode) return;

        // also guard against stray spacers
        const existing = dateContainer.parentNode?.querySelector('.calendar-spacer');
        if (existing) existing.remove();

        spacer = document.createElement('div');
        spacer.className = 'calendar-spacer';
        spacer.style.height = dateContainer.offsetHeight + 'px';
        spacer.style.visibility = 'hidden';
        spacer.style.opacity = '0';
        spacer.style.pointerEvents = 'none';
        dateContainer.parentNode.insertBefore(spacer, dateContainer.nextSibling);
    }

    function removeSpacer() {
        if (spacer && spacer.parentNode) {
            spacer.parentNode.removeChild(spacer);
            spacer = null;
        }
        // remove any stray spacers too
        dateContainer.parentNode?.querySelectorAll('.calendar-spacer')?.forEach(s => s.remove());
    }

    function updatePosition() {
        const offsetParent = getOffsetParent(dateContainer);
        const offsetParentRect = offsetParent.getBoundingClientRect();
        const containerRect = dateContainer.getBoundingClientRect();

        dateContainer.style.top = (containerRect.top - offsetParentRect.top) + 'px';
        dateContainer.style.left = (containerRect.left - offsetParentRect.left) + 'px';
    }

    // Toggle calendar visibility
    function toggleCalendar(forceOpen) {
        if (!flatpickrInstance) return;

        const shouldOpen = typeof forceOpen === 'boolean' ? forceOpen : !isOpen;

        if (!shouldOpen && isOpen) {
            // Close
            flatpickrInstance.calendarContainer.style.display = 'none';
            dateContainer.classList.remove('expanded');
            dateContainer.style.position = '';
            dateContainer.style.zIndex = '';
            dateContainer.style.width = '';
            dateContainer.style.top = '';
            dateContainer.style.left = '';
            dateContainer.style.backgroundColor = '';
            dateContainer.style.boxShadow = '';
            dateContainer.style.boxSizing = '';

            removeSpacer();
            isOpen = false;
            return;
        }

        if (shouldOpen && !isOpen) {
            const containerWidth = dateContainer.offsetWidth;
            const containerHeight = dateContainer.offsetHeight;

            createSpacer();

            const offsetParent = getOffsetParent(dateContainer);
            const offsetParentRect = offsetParent.getBoundingClientRect();
            const containerRect = dateContainer.getBoundingClientRect();

            // Ensure offset parent has positioning context
            if (offsetParent !== document.body) {
                const s = window.getComputedStyle(offsetParent);
                if (s.position === 'static') offsetParent.style.position = 'relative';
            }

            dateContainer.style.position = 'absolute';
            dateContainer.style.zIndex = '1000';
            dateContainer.style.width = containerWidth + 'px';
            dateContainer.style.top = (containerRect.top - offsetParentRect.top) + 'px';
            dateContainer.style.left = (containerRect.left - offsetParentRect.left) + 'px';
            dateContainer.style.backgroundColor = 'white';
            dateContainer.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
            dateContainer.style.boxSizing = 'border-box';

            flatpickrInstance.calendarContainer.style.display = 'block';
            dateContainer.classList.add('expanded');

            // Fit above/below
            const calendarHeight = flatpickrInstance.calendarContainer.offsetHeight;
            const top = parseFloat(dateContainer.style.top) || 0;

            const spaceBelow = offsetParentRect.height - (top + containerHeight);
            const spaceAbove = top;

            if (!(spaceBelow >= calendarHeight || (spaceBelow >= 0 && spaceBelow > spaceAbove))) {
                if (spaceAbove >= calendarHeight || spaceAbove > spaceBelow) {
                    dateContainer.style.top = (top - calendarHeight - 5) + 'px';
                }
            }

            isOpen = true;
        }
    }

    // Initialize flatpickr
    try {
        flatpickrInstance = flatpickr(calendarInput, {
            inline: true,
            defaultDate: parsedDefaultDate,
            dateFormat: "Y-m-d",
            altFormat: "d-M-Y",
            altInput: false,
            static: true,
            appendTo: inlineCalendar,
            onReady: function (selectedDates, dateStr, instance) {
                const calendarElement = instance.calendarContainer;
                if (calendarElement) {
                    calendarElement.style.width = '100%';
                    calendarElement.style.border = 'none';
                    calendarElement.style.boxShadow = 'none';
                    calendarElement.style.marginTop = '15px';
                }

                instance.calendarContainer.style.display = 'none';

                // Set initial display if we have a date
                if (selectedDates.length > 0) {
                    dateInput.value = instance.formatDate(selectedDates[0], "d-M-Y");
                    clearDateBtn.classList.remove('d-none');
                } else {
                    clearDateBtn.classList.add('d-none');
                }
            },
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    dateInput.value = instance.formatDate(selectedDates[0], "d-M-Y");

                    // Optional: reset styles if you have validation styling
                    dateContainer.removeAttribute("style");
                    const title = dateContainer.querySelector("h6");
                    if (title) title.removeAttribute("style");
                    const calendarIcon = dateContainer.querySelector(".constant-icon");
                    if (calendarIcon) calendarIcon.removeAttribute("style");

                    if (window.$) {
                        $(dateContainer).siblings('.validation-error').remove();
                    }

                    clearDateBtn.classList.remove('d-none');
                    toggleCalendar(false); // ✅ close
                } else {
                    dateInput.value = '';
                    clearDateBtn.classList.add('d-none');
                }
            }
        });
    } catch (error) {
        console.error('Failed to initialize flatpickr:', error);
        return;
    }

    // ✅ ONE opener handler using pointerdown (fixes “need to click twice”)
    const dateInputArea = dateInput.closest('.date-input-area') || dateInput;

    function handleOpenPointerDown(e) {
        if (e.target === clearDateBtn || e.target.closest('.clear-icon')) return;

        e.preventDefault();
        e.stopPropagation();
        toggleCalendar();
    }

    function handleClearClick(e) {
        e.preventDefault();
        e.stopPropagation();

        if (flatpickrInstance) flatpickrInstance.clear();

        dateInput.value = '';
        clearDateBtn.classList.add('d-none');
        toggleCalendar(false);
    }

    function handleOutsidePointerDown(e) {
        if (!dateContainer.contains(e.target) && isOpen) {
            toggleCalendar(false);
        }
    }

    function handleResizeOrScroll() {
        if (!isOpen) return;
        updatePosition();
    }

    function handleKeydown(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            e.stopPropagation();
            toggleCalendar();
        }
        if (e.key === 'Escape' && isOpen) {
            e.preventDefault();
            e.stopPropagation();
            toggleCalendar(false);
        }
    }

    function cleanup() {
        dateInputArea.removeEventListener('pointerdown', handleOpenPointerDown);
        dateInput.removeEventListener('keydown', handleKeydown);
        clearDateBtn.removeEventListener('click', handleClearClick);
        document.removeEventListener('pointerdown', handleOutsidePointerDown);

        window.removeEventListener('resize', handleResizeOrScroll);
        window.removeEventListener('scroll', handleResizeOrScroll, true);
        removeSpacer();

        if (flatpickrInstance) {
            flatpickrInstance.destroy();
            flatpickrInstance = null;
        }
    }

    // Listeners
    dateInputArea.addEventListener('pointerdown', handleOpenPointerDown);
    dateInput.addEventListener('keydown', handleKeydown);

    clearDateBtn.addEventListener('click', handleClearClick);
    document.addEventListener('pointerdown', handleOutsidePointerDown);

    window.addEventListener('resize', handleResizeOrScroll);
    window.addEventListener('scroll', handleResizeOrScroll, true);

    // clear button visibility
    if (dateInput.value) clearDateBtn.classList.remove('d-none');
    else clearDateBtn.classList.add('d-none');

    // Return API for external control + store on container
    const api = {
        instance: flatpickrInstance,
        toggleCalendar,
        destroy: cleanup
    };

    dateContainer._customCalendarApi = api;
    return api;
}
              function initCustomRangeCalendar(containerId, inputId, clearBtnId, defaultDate) {
                  const dateContainer = document.getElementById(containerId);
                  const dateInput = document.getElementById(inputId);
                  const clearDateBtn = document.getElementById(clearBtnId);

                  if (!dateContainer || !dateInput || !clearDateBtn) {
                      console.error('Required elements not found for range calendar');
                      return;
                  }

                  const inlineCalendar = dateContainer.querySelector('.inline-calendar-container');
                  if (!inlineCalendar) {
                      console.error('inline-calendar-container not found');
                      return;
                  }

                  let isOpen = false;
                  let spacer = null;
                  let flatpickrInstance = null; // Declare here at the top

                  // Remove any existing event listeners
                  const existingListeners = dateContainer.dataset.listenersRemoved;
                  if (!existingListeners) {
                      // Initialize cleanup function after variables are declared
                      const cleanupExistingListeners = function() {
                          dateInput.removeEventListener('click', handleInputClick);
                          clearDateBtn.removeEventListener('click', handleClearClick);
                          document.removeEventListener('click', handleOutsideClick);
                          window.removeEventListener('resize', handleResizeOrScroll);
                          window.removeEventListener('scroll', handleResizeOrScroll);
                          window.removeEventListener('beforeunload', handleBeforeUnload);

                          const dateInputArea = dateInput.closest('.date-input-area');
                          if (dateInputArea) {
                              dateInputArea.removeEventListener('click', handleDateInputAreaClick);
                          }

                          if (flatpickrInstance) {
                              flatpickrInstance.destroy();
                          }
                      };

                      // Store cleanup function for later use
                      dateContainer._cleanupFn = cleanupExistingListeners;

                      // Call cleanup to remove any existing listeners
                      cleanupExistingListeners();

                      dateContainer.dataset.listenersRemoved = 'true';
                  }

                  inlineCalendar.innerHTML = '';

                  const calendarInput = document.createElement('input');
                  calendarInput.type = 'text';
                  calendarInput.style.display = 'none';
                  inlineCalendar.appendChild(calendarInput);

                  // Create spacer element
                  function createSpacer() {
                      spacer = document.createElement('div');
                      spacer.className = 'calendar-spacer';
                      spacer.style.height = dateContainer.offsetHeight + 'px';
                      spacer.style.visibility = 'hidden';
                      spacer.style.opacity = '0';
                      spacer.style.pointerEvents = 'none';
                      dateContainer.parentNode.insertBefore(spacer, dateContainer.nextSibling);
                  }

                  // Remove spacer element
                  function removeSpacer() {
                      if (spacer && spacer.parentNode) {
                          spacer.parentNode.removeChild(spacer);
                          spacer = null;
                      }
                  }

                  // Get the offset parent
                  function getOffsetParent(element) {
                      let offsetParent = element.offsetParent;
                      while (offsetParent &&
                          offsetParent !== document.body &&
                          window.getComputedStyle(offsetParent).position === 'static') {
                          offsetParent = offsetParent.offsetParent;
                      }
                      return offsetParent || document.body;
                  }

                  // Parse default dates
                  let defaultDates = null;
                  if (defaultDate) {
                      try {
                          const parts = defaultDate.split(' → ');
                          if (parts.length === 2) {
                              defaultDates = parts;
                          } else if (defaultDate.includes(' to ')) {
                              const parts = defaultDate.split(' to ');
                              if (parts.length === 2) {
                                  defaultDates = parts;
                              }
                          }
                      } catch (e) {
                          console.warn('Error parsing default date for range:', e);
                      }
                  }

                  try {
                      flatpickrInstance = flatpickr(calendarInput, {
                          inline: true,
                          mode: "range",
                          defaultDate: defaultDates,
                          dateFormat: "Y-m-d",
                          altFormat: "d-M-Y",
                          altInput: false,
                          static: true,
                          appendTo: inlineCalendar,
                          onReady: function(selectedDates, dateStr, instance) {
                              const calendarElement = instance.calendarContainer;
                              if (calendarElement) {
                                  calendarElement.style.width = '100%';
                                  calendarElement.style.border = 'none';
                                  calendarElement.style.boxShadow = 'none';
                                  calendarElement.style.marginTop = '15px';
                              }
                              instance.calendarContainer.style.display = 'none';

                              // Set initial value if default dates exist
                              if (selectedDates.length === 2 && defaultDates) {
                                  const start = instance.formatDate(selectedDates[0], "d-M-Y");
                                  const end = instance.formatDate(selectedDates[1], "d-M-Y");
                                  dateInput.value = `${start} to ${end}`;
                                  clearDateBtn.classList.remove('d-none');
                              }
                          },
                          onChange: function(selectedDates, dateStr, instance) {
                              if (selectedDates.length === 2) {
                                  const start = instance.formatDate(selectedDates[0], "d-M-Y");
                                  const end = instance.formatDate(selectedDates[1], "d-M-Y");

                                  dateInput.value = `${start} to ${end}`;
                                  clearDateBtn.classList.remove('d-none');

                                  // Close after a short delay
                                  setTimeout(() => {
                                      if (isOpen) {
                                          toggleCalendar();
                                      }
                                  }, 300);
                              } else {
                                  dateInput.value = '';
                                  clearDateBtn.classList.add('d-none');
                              }
                          }
                      });
                  } catch (error) {
                      console.error('Failed to initialize flatpickr for range:', error);
                      return;
                  }

                  // Event handler functions
                  function handleInputClick(e) {
                      e.stopPropagation();
                      toggleCalendar();
                  }

                  function handleDateInputAreaClick(e) {
                      if (e.target !== clearDateBtn && !e.target.closest('.clear-icon')) {
                          toggleCalendar();
                      }
                  }

                  function handleClearClick(e) {
                      e.stopPropagation();
                      if (flatpickrInstance) {
                          flatpickrInstance.clear();
                      }
                      dateInput.value = '';
                      clearDateBtn.classList.add('d-none');
                      if (isOpen) {
                          toggleCalendar();
                      }
                  }

                  function handleOutsideClick(e) {
                      if (!dateContainer.contains(e.target) && isOpen) {
                          toggleCalendar();
                      }
                  }

                  function handleResizeOrScroll() {
                      if (isOpen) {
                          const offsetParent = getOffsetParent(dateContainer);
                          const offsetParentRect = offsetParent.getBoundingClientRect();
                          const containerRect = dateContainer.getBoundingClientRect();

                          let top = containerRect.top - offsetParentRect.top;
                          let left = containerRect.left - offsetParentRect.left;

                          dateContainer.style.top = top + 'px';
                          dateContainer.style.left = left + 'px';
                      }
                  }

                  function handleBeforeUnload() {
                      removeSpacer();
                      if (dateContainer._cleanupFn) {
                          dateContainer._cleanupFn();
                      }
                  }

                  // Clean up function (redefined after flatpickrInstance is available)
                  function cleanupExistingListeners() {
                      dateInput.removeEventListener('click', handleInputClick);
                      clearDateBtn.removeEventListener('click', handleClearClick);
                      document.removeEventListener('click', handleOutsideClick);
                      window.removeEventListener('resize', handleResizeOrScroll);
                      window.removeEventListener('scroll', handleResizeOrScroll);
                      window.removeEventListener('beforeunload', handleBeforeUnload);

                      const dateInputArea = dateInput.closest('.date-input-area');
                      if (dateInputArea) {
                          dateInputArea.removeEventListener('click', handleDateInputAreaClick);
                      }

                      if (flatpickrInstance) {
                          flatpickrInstance.destroy();
                      }
                  }

                  // Update the stored cleanup function
                  dateContainer._cleanupFn = cleanupExistingListeners;

                  // Toggle calendar visibility
                  function toggleCalendar() {
                      if (!flatpickrInstance) return;

                      if (isOpen) {
                          // Close calendar
                          flatpickrInstance.calendarContainer.style.display = 'none';
                          dateContainer.classList.remove('expanded');
                          dateContainer.style.position = '';
                          dateContainer.style.zIndex = '';
                          dateContainer.style.width = '';
                          dateContainer.style.top = '';
                          dateContainer.style.left = '';

                          // Remove spacer
                          removeSpacer();

                          isOpen = false;
                      } else {
                          // Store original dimensions
                          const containerWidth = dateContainer.offsetWidth;
                          const containerHeight = dateContainer.offsetHeight;

                          // Create spacer to maintain layout
                          createSpacer();

                          // Get positioning context
                          const offsetParent = getOffsetParent(dateContainer);
                          const offsetParentRect = offsetParent.getBoundingClientRect();
                          const containerRect = dateContainer.getBoundingClientRect();

                          // Calculate position relative to offset parent
                          let top = containerRect.top - offsetParentRect.top;
                          let left = containerRect.left - offsetParentRect.left;

                          // Set container as positioned relative to its offset parent
                          dateContainer.style.position = 'absolute';
                          dateContainer.style.zIndex = '1000';
                          dateContainer.style.width = containerWidth + 'px';
                          dateContainer.style.top = top + 'px';
                          dateContainer.style.left = left + 'px';

                          // Set the container's position relative to its offset parent
                          if (offsetParent !== document.body) {
                              const offsetParentComputed = window.getComputedStyle(offsetParent);
                              if (offsetParentComputed.position === 'static') {
                                  offsetParent.style.position = 'relative';
                              }
                          }

                          flatpickrInstance.calendarContainer.style.display = 'block';
                          dateContainer.classList.add('expanded');

                          // Get calendar height
                          const calendarHeight = flatpickrInstance.calendarContainer.offsetHeight;

                          // Calculate available space
                          const spaceBelow = offsetParentRect.height - (top + containerHeight);
                          const spaceAbove = top;

                          // Check if calendar fits below
                          if (spaceBelow >= calendarHeight || (spaceBelow >= 0 && spaceBelow > spaceAbove)) {
                              // Position below
                          } else if (spaceAbove >= calendarHeight) {
                              // Position above
                              dateContainer.style.top = (top - calendarHeight - 5) + 'px';
                          } else {
                              // Not enough space
                              if (spaceBelow > spaceAbove) {
                                  // Position below
                              } else {
                                  // Position above
                                  dateContainer.style.top = (top - calendarHeight - 5) + 'px';
                              }
                          }

                          isOpen = true;
                      }
                  }

                  // Add event listeners
                  dateInput.addEventListener('click', handleInputClick);

                  const dateInputArea = dateInput.closest('.date-input-area');
                  if (dateInputArea) {
                      dateInputArea.addEventListener('click', handleDateInputAreaClick);
                  }

                  clearDateBtn.addEventListener('click', handleClearClick);
                  document.addEventListener('click', handleOutsideClick);
                  window.addEventListener('resize', handleResizeOrScroll);
                  window.addEventListener('scroll', handleResizeOrScroll, true);
                  window.addEventListener('beforeunload', handleBeforeUnload);

                  // Show clear button if date exists
                  if (dateInput.value) {
                      clearDateBtn.classList.remove('d-none');
                  }

                  // Return API for external control
                  return {
                      toggleCalendar,
                      destroy: cleanupExistingListeners,
                      getFlatpickrInstance: function() {
                          return flatpickrInstance;
                      }
                  };
              }

            //   function initMonthYearCalendar(containerId, inputId, clearBtnId, defaultDate) {
            //       const dateContainer = document.getElementById(containerId);
            //       const dateInput = document.getElementById(inputId);
            //       const clearDateBtn = document.getElementById(clearBtnId);

            //       if (!dateContainer || !dateInput || !clearDateBtn) {
            //           console.error('Required elements not found for month-year calendar');
            //           return;
            //       }

            //       const inlineCalendar = dateContainer.querySelector('.inline-calendar-container');
            //       if (!inlineCalendar) {
            //           console.error('inline-calendar-container not found');
            //           return;
            //       }

            //       // Remove any existing event listeners
            //       const existingListeners = dateContainer.dataset.listenersRemoved;
            //       if (!existingListeners) {
            //           cleanupExistingListeners();
            //           dateContainer.dataset.listenersRemoved = 'true';
            //       }

            //       let isOpen = false;
            //       let selectedMonth = null;
            //       let selectedYear = null;
            //       let spacer = null;
            //       let picker = null;

            //       // Parse default date or use current month
            //       const currentDate = new Date();
            //       let currentMonth = currentDate.getMonth();
            //       let currentYear = currentDate.getFullYear();

            //       try {
            //           if (defaultDate) {
            //               const parts = defaultDate.split('-');
            //               if (parts.length === 2) {
            //                   const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
            //                       "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            //                   ];
            //                   const monthStr = parts[0];
            //                   const yearStr = parts[1];

            //                   const monthIndex = monthNames.indexOf(monthStr);
            //                   if (monthIndex !== -1) {
            //                       currentMonth = monthIndex;
            //                       currentYear = parseInt(yearStr);
            //                       selectedMonth = monthIndex;
            //                       selectedYear = parseInt(yearStr);
            //                       dateInput.value = `${monthStr}-${yearStr}`;
            //                   }
            //               }
            //           }
            //       } catch (e) {
            //           console.warn('Error parsing default date:', e);
            //       }

            //       // Create spacer element
            //       function createSpacer() {
            //           spacer = document.createElement('div');
            //           spacer.className = 'calendar-spacer';
            //           spacer.style.height = dateContainer.offsetHeight + 'px';
            //           spacer.style.visibility = 'hidden';
            //           spacer.style.opacity = '0';
            //           spacer.style.pointerEvents = 'none';
            //           dateContainer.parentNode.insertBefore(spacer, dateContainer.nextSibling);
            //       }

            //       // Remove spacer element
            //       function removeSpacer() {
            //           if (spacer && spacer.parentNode) {
            //               spacer.parentNode.removeChild(spacer);
            //               spacer = null;
            //           }
            //       }

            //       // Get the offset parent
            //       function getOffsetParent(element) {
            //           let offsetParent = element.offsetParent;
            //           while (offsetParent &&
            //               offsetParent !== document.body &&
            //               window.getComputedStyle(offsetParent).position === 'static') {
            //               offsetParent = offsetParent.offsetParent;
            //           }
            //           return offsetParent || document.body;
            //       }

            //       // Create month/year picker UI
            //       function createMonthYearPicker() {
            //           inlineCalendar.innerHTML = '';

            //           const pickerContainer = document.createElement('div');
            //           pickerContainer.className = 'month-year-picker';
            //           pickerContainer.style.padding = '15px';
            //           pickerContainer.style.display = 'none';
            //           pickerContainer.style.width = '100%';
            //           pickerContainer.style.backgroundColor = 'white';
            //           pickerContainer.style.boxSizing = 'border-box';

            //           // Year selector
            //           const yearContainer = document.createElement('div');
            //           yearContainer.style.marginBottom = '15px';
            //           yearContainer.style.display = 'flex';
            //           yearContainer.style.alignItems = 'center';
            //           yearContainer.style.justifyContent = 'center';
            //           yearContainer.style.gap = '10px';

            //           const prevYearBtn = document.createElement('button');
            //           prevYearBtn.type = 'button';
            //           prevYearBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
            //           prevYearBtn.style.background = 'none';
            //           prevYearBtn.style.border = '1px solid #ddd';
            //           prevYearBtn.style.borderRadius = '4px';
            //           prevYearBtn.style.padding = '5px 10px';
            //           prevYearBtn.style.cursor = 'pointer';

            //           const yearDisplay = document.createElement('span');
            //           yearDisplay.textContent = selectedYear || currentYear;
            //           yearDisplay.style.fontWeight = 'bold';
            //           yearDisplay.style.fontSize = '16px';

            //           const nextYearBtn = document.createElement('button');
            //           nextYearBtn.type = 'button';
            //           nextYearBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            //           nextYearBtn.style.background = 'none';
            //           nextYearBtn.style.border = '1px solid #ddd';
            //           nextYearBtn.style.borderRadius = '4px';
            //           nextYearBtn.style.padding = '5px 10px';
            //           nextYearBtn.style.cursor = 'pointer';

            //           yearContainer.appendChild(prevYearBtn);
            //           yearContainer.appendChild(yearDisplay);
            //           yearContainer.appendChild(nextYearBtn);

            //           // Month grid
            //           const monthGrid = document.createElement('div');
            //           monthGrid.style.display = 'grid';
            //           monthGrid.style.gridTemplateColumns = 'repeat(3, 1fr)';
            //           monthGrid.style.gap = '8px';

            //           const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
            //               "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            //           ];

            //           monthNames.forEach((month, index) => {
            //               const monthBtn = document.createElement('button');
            //               monthBtn.type = 'button';
            //               monthBtn.textContent = month;
            //               monthBtn.style.padding = '10px 5px';
            //               monthBtn.style.border = '1px solid #ddd';
            //               monthBtn.style.borderRadius = '4px';
            //               monthBtn.style.background = 'none';
            //               monthBtn.style.cursor = 'pointer';
            //               monthBtn.style.transition = 'all 0.2s';
            //               monthBtn.style.fontSize = '14px';
            //               monthBtn.style.boxSizing = 'border-box';

            //               if (selectedMonth === index && selectedYear === parseInt(yearDisplay.textContent)) {
            //                   monthBtn.style.background = '#007bff';
            //                   monthBtn.style.color = 'white';
            //                   monthBtn.style.borderColor = '#007bff';
            //               }

            //               monthBtn.addEventListener('click', function(e) {
            //                   e.preventDefault();
            //                   e.stopPropagation();

            //                   // Update selection
            //                   selectedMonth = index;
            //                   selectedYear = parseInt(yearDisplay.textContent);

            //                   // Update display
            //                   dateInput.value = `${monthNames[selectedMonth]}-${selectedYear}`;
            //                   clearDateBtn.classList.remove('d-none');
            //                   toggleCalendar();
            //               });

            //               monthGrid.appendChild(monthBtn);
            //           });

            //           // Year navigation
            //           prevYearBtn.addEventListener('click', function(e) {
            //               e.preventDefault();
            //               e.stopPropagation();

            //               const currentYearVal = parseInt(yearDisplay.textContent);
            //               yearDisplay.textContent = currentYearVal - 1;
            //               updateMonthButtons();
            //           });

            //           nextYearBtn.addEventListener('click', function(e) {
            //               e.preventDefault();
            //               e.stopPropagation();

            //               const currentYearVal = parseInt(yearDisplay.textContent);
            //               yearDisplay.textContent = currentYearVal + 1;
            //               updateMonthButtons();
            //           });

            //           function updateMonthButtons() {
            //               const buttons = monthGrid.querySelectorAll('button');
            //               const currentYearVal = parseInt(yearDisplay.textContent);

            //               buttons.forEach((btn, index) => {
            //                   btn.style.background = 'none';
            //                   btn.style.color = 'inherit';
            //                   btn.style.borderColor = '#ddd';

            //                   if (selectedMonth === index && selectedYear === currentYearVal) {
            //                       btn.style.background = '#007bff';
            //                       btn.style.color = 'white';
            //                       btn.style.borderColor = '#007bff';
            //                   }
            //               });
            //           }

            //           pickerContainer.appendChild(yearContainer);
            //           pickerContainer.appendChild(monthGrid);
            //           inlineCalendar.appendChild(pickerContainer);

            //           return pickerContainer;
            //       }

            //       // Event handler functions
            //       function handleInputClick(e) {
            //           e.preventDefault();
            //           e.stopPropagation();
            //           toggleCalendar();
            //       }

            //       function handleClearClick(e) {
            //           e.preventDefault();
            //           e.stopPropagation();
            //           selectedMonth = null;
            //           selectedYear = null;
            //           dateInput.value = '';
            //           clearDateBtn.classList.add('d-none');
            //           if (isOpen) {
            //               toggleCalendar();
            //           }
            //           // Recreate picker to reset selection
            //           picker = createMonthYearPicker();
            //       }

            //       function handleOutsideClick(e) {
            //           if (!dateContainer.contains(e.target) && isOpen) {
            //               toggleCalendar();
            //           }
            //       }

            //       function handleDateInputAreaClick(e) {
            //           if (e.target !== clearDateBtn && !e.target.closest('.clear-icon')) {
            //               e.preventDefault();
            //               toggleCalendar();
            //           }
            //       }

            //       function handleResizeOrScroll() {
            //           if (isOpen) {
            //               // Recalculate position on resize or scroll
            //               const offsetParent = getOffsetParent(dateContainer);
            //               const offsetParentRect = offsetParent.getBoundingClientRect();
            //               const containerRect = dateContainer.getBoundingClientRect();

            //               let top = containerRect.top - offsetParentRect.top;
            //               let left = containerRect.left - offsetParentRect.left;

            //               dateContainer.style.top = top + 'px';
            //               dateContainer.style.left = left + 'px';
            //           }
            //       }

            //       function handleKeydown(e) {
            //           if (e.key === 'Enter') {
            //               e.preventDefault();
            //               e.stopPropagation();
            //               toggleCalendar();
            //           }
            //       }

            //       // Clean up existing listeners
            //       function cleanupExistingListeners() {
            //           dateInput.removeEventListener('click', handleInputClick);
            //           dateInput.removeEventListener('keydown', handleKeydown);
            //           clearDateBtn.removeEventListener('click', handleClearClick);
            //           document.removeEventListener('click', handleOutsideClick);
            //           window.removeEventListener('resize', handleResizeOrScroll);
            //           window.removeEventListener('scroll', handleResizeOrScroll);
            //           window.removeEventListener('beforeunload', handleBeforeUnload);
            //       }

            //       function handleBeforeUnload() {
            //           removeSpacer();
            //           cleanupExistingListeners();
            //       }

            //       // Toggle calendar visibility
            //       function toggleCalendar() {
            //           if (isOpen) {
            //               // Close calendar
            //               picker.style.display = 'none';
            //               dateContainer.classList.remove('expanded');
            //               dateContainer.style.position = '';
            //               dateContainer.style.zIndex = '';
            //               dateContainer.style.width = '';
            //               dateContainer.style.top = '';
            //               dateContainer.style.left = '';
            //               dateContainer.style.backgroundColor = '';
            //               dateContainer.style.boxShadow = '';

            //               // Remove spacer
            //               removeSpacer();

            //               isOpen = false;
            //           } else {
            //               // Store original dimensions
            //               const containerWidth = dateContainer.offsetWidth;
            //               const containerHeight = dateContainer.offsetHeight;

            //               // Create spacer to maintain layout
            //               createSpacer();

            //               // Get positioning context
            //               const offsetParent = getOffsetParent(dateContainer);
            //               const offsetParentRect = offsetParent.getBoundingClientRect();
            //               const containerRect = dateContainer.getBoundingClientRect();

            //               // Calculate position relative to offset parent
            //               let top = containerRect.top - offsetParentRect.top;
            //               let left = containerRect.left - offsetParentRect.left;

            //               // Set container as positioned relative to its offset parent
            //               dateContainer.style.position = 'absolute';
            //               dateContainer.style.zIndex = '1000';
            //               dateContainer.style.width = containerWidth + 'px';
            //               dateContainer.style.top = top + 'px';
            //               dateContainer.style.left = left + 'px';
            //               dateContainer.style.backgroundColor = 'white';
            //               dateContainer.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
            //               dateContainer.style.boxSizing = 'border-box';

            //               // Set the container's position relative to its offset parent
            //               if (offsetParent !== document.body) {
            //                   const offsetParentComputed = window.getComputedStyle(offsetParent);
            //                   if (offsetParentComputed.position === 'static') {
            //                       offsetParent.style.position = 'relative';
            //                   }
            //               }

            //               picker.style.display = 'block';
            //               dateContainer.classList.add('expanded');

            //               // Get picker height
            //               const pickerHeight = picker.offsetHeight;

            //               // Calculate available space
            //               const spaceBelow = offsetParentRect.height - (top + containerHeight);
            //               const spaceAbove = top;

            //               // Check if picker fits below
            //               if (spaceBelow >= pickerHeight || (spaceBelow >= 0 && spaceBelow > spaceAbove)) {
            //                   // Position below - already positioned correctly
            //               } else if (spaceAbove >= pickerHeight) {
            //                   // Position above
            //                   dateContainer.style.top = (top - pickerHeight - 5) + 'px';
            //               } else {
            //                   // Not enough space in either direction
            //                   if (spaceBelow > spaceAbove) {
            //                       // Position below
            //                   } else {
            //                       // Position above
            //                       dateContainer.style.top = (top - pickerHeight - 5) + 'px';
            //                   }
            //               }

            //               isOpen = true;
            //           }
            //       }

            //       // Create the picker
            //       picker = createMonthYearPicker();

            //       // Add event listeners
            //       dateInput.addEventListener('click', handleInputClick);

            //       const dateInputArea = dateInput.closest('.date-input-area');
            //       if (dateInputArea) {
            //           dateInputArea.addEventListener('click', handleDateInputAreaClick);
            //       }

            //       clearDateBtn.addEventListener('click', handleClearClick);
            //       document.addEventListener('click', handleOutsideClick);
            //       window.addEventListener('resize', handleResizeOrScroll);
            //       window.addEventListener('scroll', handleResizeOrScroll, true);
            //       window.addEventListener('beforeunload', handleBeforeUnload);
            //       dateInput.addEventListener('keydown', handleKeydown);

            //       // Show clear button if date exists
            //       if (dateInput.value) {
            //           clearDateBtn.classList.remove('d-none');
            //       }

            //       // Return API for external control
            //       return {
            //           toggleCalendar,
            //           destroy: cleanupExistingListeners,
            //           getSelectedDate: function() {
            //               return selectedMonth !== null && selectedYear !== null ?
            //                   `${monthNames[selectedMonth]}-${selectedYear}` :
            //                   null;
            //           }
            //       };
            //   }

function initMonthYearCalendar(containerId, inputId, clearBtnId, defaultDate) {
                const dateContainer = document.getElementById(containerId);
                const dateInput = document.getElementById(inputId);
                const clearDateBtn = document.getElementById(clearBtnId);

                if (!dateContainer || !dateInput || !clearDateBtn) {
                    console.error('Required elements not found for month-year calendar');
                    return;
                }

                const inlineCalendar = dateContainer.querySelector('.inline-calendar-container');
                if (!inlineCalendar) {
                    console.error('inline-calendar-container not found');
                    return;
                }

                // Destroy previous instance on this container (prevents duplicate listeners/spacers)
                if (dateContainer._monthYearApi && typeof dateContainer._monthYearApi.destroy === 'function') {
                    dateContainer._monthYearApi.destroy();
                    dateContainer._monthYearApi = null;
                }

                // Remove any leftover spacers from previous runs
                dateContainer.parentNode?.querySelectorAll('.calendar-spacer')?.forEach(s => s.remove());

                let isOpen = false;
                let selectedMonth = null;
                let selectedYear = null;
                let spacer = null;
                let picker = null;

                const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];

                // Prefer input value over defaultDate so it doesn't keep resetting
                function parseMonthYear(str) {
                    if (!str) return null;
                    const parts = String(str).trim().split('-');
                    if (parts.length !== 2) return null;
                    const m = parts[0].trim();
                    const y = parseInt(parts[1].trim(), 10);
                    const mi = monthNames.indexOf(m);
                    if (mi === -1 || Number.isNaN(y)) return null;
                    return { monthIndex: mi, year: y };
                }

                const initial = parseMonthYear(dateInput.value) || parseMonthYear(defaultDate);
                if (initial) {
                    selectedMonth = initial.monthIndex;
                    selectedYear = initial.year;
                    dateInput.value = `${monthNames[selectedMonth]}-${selectedYear}`;
                }

                function createSpacer() {
                    if (spacer && spacer.parentNode) return;

                    const existing = dateContainer.parentNode?.querySelector('.calendar-spacer');
                    if (existing) existing.remove();

                    spacer = document.createElement('div');
                    spacer.className = 'calendar-spacer';
                    spacer.style.height = dateContainer.offsetHeight + 'px';
                    spacer.style.visibility = 'hidden';
                    spacer.style.opacity = '0';
                    spacer.style.pointerEvents = 'none';
                    dateContainer.parentNode.insertBefore(spacer, dateContainer.nextSibling);
                }

                function removeSpacer() {
                    if (spacer && spacer.parentNode) {
                        spacer.parentNode.removeChild(spacer);
                        spacer = null;
                    }
                    dateContainer.parentNode?.querySelectorAll('.calendar-spacer')?.forEach(s => s.remove());
                }

                function getOffsetParent(element) {
                    let offsetParent = element.offsetParent;
                    while (
                        offsetParent &&
                        offsetParent !== document.body &&
                        window.getComputedStyle(offsetParent).position === 'static'
                    ) {
                        offsetParent = offsetParent.offsetParent;
                    }
                    return offsetParent || document.body;
                }

                function createMonthYearPicker() {
                    inlineCalendar.innerHTML = '';

                    const pickerContainer = document.createElement('div');
                    pickerContainer.className = 'month-year-picker';
                    pickerContainer.style.padding = '15px';
                    pickerContainer.style.display = 'none';
                    pickerContainer.style.width = '100%';
                    pickerContainer.style.backgroundColor = 'white';
                    pickerContainer.style.boxSizing = 'border-box';

                    const yearContainer = document.createElement('div');
                    yearContainer.style.marginBottom = '15px';
                    yearContainer.style.display = 'flex';
                    yearContainer.style.alignItems = 'center';
                    yearContainer.style.justifyContent = 'center';
                    yearContainer.style.gap = '10px';

                    const prevYearBtn = document.createElement('button');
                    prevYearBtn.type = 'button';
                    prevYearBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
                    prevYearBtn.style.background = 'none';
                    prevYearBtn.style.border = '1px solid #ddd';
                    prevYearBtn.style.borderRadius = '4px';
                    prevYearBtn.style.padding = '5px 10px';
                    prevYearBtn.style.cursor = 'pointer';

                    const yearDisplay = document.createElement('span');
                    yearDisplay.textContent = selectedYear ?? new Date().getFullYear();
                    yearDisplay.style.fontWeight = 'bold';
                    yearDisplay.style.fontSize = '16px';

                    const nextYearBtn = document.createElement('button');
                    nextYearBtn.type = 'button';
                    nextYearBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
                    nextYearBtn.style.background = 'none';
                    nextYearBtn.style.border = '1px solid #ddd';
                    nextYearBtn.style.borderRadius = '4px';
                    nextYearBtn.style.padding = '5px 10px';
                    nextYearBtn.style.cursor = 'pointer';

                    yearContainer.appendChild(prevYearBtn);
                    yearContainer.appendChild(yearDisplay);
                    yearContainer.appendChild(nextYearBtn);

                    const monthGrid = document.createElement('div');
                    monthGrid.style.display = 'grid';
                    monthGrid.style.gridTemplateColumns = 'repeat(3, 1fr)';
                    monthGrid.style.gap = '8px';

                    function updateMonthButtons() {
                        const buttons = monthGrid.querySelectorAll('button');
                        const y = parseInt(yearDisplay.textContent, 10);

                        buttons.forEach((btn, index) => {
                            btn.style.background = 'none';
                            btn.style.color = 'inherit';
                            btn.style.borderColor = '#ddd';

                            if (selectedMonth !== null && selectedYear !== null && selectedMonth === index && selectedYear === y) {
                                btn.style.background = '#007bff';
                                btn.style.color = 'white';
                                btn.style.borderColor = '#007bff';
                            }
                        });
                    }

                    monthNames.forEach((month, index) => {
                        const monthBtn = document.createElement('button');
                        monthBtn.type = 'button';
                        monthBtn.textContent = month;
                        monthBtn.style.padding = '10px 5px';
                        monthBtn.style.border = '1px solid #ddd';
                        monthBtn.style.borderRadius = '4px';
                        monthBtn.style.background = 'none';
                        monthBtn.style.cursor = 'pointer';
                        monthBtn.style.transition = 'all 0.2s';
                        monthBtn.style.fontSize = '14px';
                        monthBtn.style.boxSizing = 'border-box';

                        monthBtn.addEventListener('click', function (e) {
                            e.preventDefault();
                            e.stopPropagation();

                            selectedMonth = index;
                            selectedYear = parseInt(yearDisplay.textContent, 10);

                            dateInput.value = `${monthNames[selectedMonth]}-${selectedYear}`;
                            clearDateBtn.classList.remove('d-none');

                            toggleCalendar(false);
                        });

                        monthGrid.appendChild(monthBtn);
                    });

                    prevYearBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        yearDisplay.textContent = String(parseInt(yearDisplay.textContent, 10) - 1);
                        updateMonthButtons();
                    });

                    nextYearBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        yearDisplay.textContent = String(parseInt(yearDisplay.textContent, 10) + 1);
                        updateMonthButtons();
                    });

                    pickerContainer.appendChild(yearContainer);
                    pickerContainer.appendChild(monthGrid);
                    inlineCalendar.appendChild(pickerContainer);

                    pickerContainer._updateMonthButtons = updateMonthButtons;
                    updateMonthButtons();

                    return pickerContainer;
                }

                // IMPORTANT: only ONE opener handler (fixes "need to click twice")
                const dateInputArea = dateInput.closest('.date-input-area') || dateInput;

                // Some UIs fire click after focus weirdly; pointerdown is more reliable
                function handleOpenPointerDown(e) {
                    // ignore clear button clicks
                    if (e.target === clearDateBtn || e.target.closest('.clear-icon')) return;

                    e.preventDefault();
                    e.stopPropagation();

                    // toggle immediately
                    toggleCalendar();
                }

                function handleClearClick(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    selectedMonth = null;
                    selectedYear = null;
                    dateInput.value = '';
                    clearDateBtn.classList.add('d-none');

                    toggleCalendar(false);
                    picker = createMonthYearPicker();
                }

                function handleOutsidePointerDown(e) {
                    if (!dateContainer.contains(e.target) && isOpen) {
                        toggleCalendar(false);
                    }
                }

                function handleResizeOrScroll() {
                    if (!isOpen) return;

                    const offsetParent = getOffsetParent(dateContainer);
                    const offsetParentRect = offsetParent.getBoundingClientRect();
                    const containerRect = dateContainer.getBoundingClientRect();

                    dateContainer.style.top = (containerRect.top - offsetParentRect.top) + 'px';
                    dateContainer.style.left = (containerRect.left - offsetParentRect.left) + 'px';
                }

                function handleKeydown(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        e.stopPropagation();
                        toggleCalendar();
                    }
                    if (e.key === 'Escape' && isOpen) {
                        e.preventDefault();
                        e.stopPropagation();
                        toggleCalendar(false);
                    }
                }

                function handleBeforeUnload() {
                    removeSpacer();
                    cleanupExistingListeners();
                }

                function cleanupExistingListeners() {
                    dateInputArea.removeEventListener('pointerdown', handleOpenPointerDown);
                    dateInput.removeEventListener('keydown', handleKeydown);
                    clearDateBtn.removeEventListener('click', handleClearClick);

                    // use pointerdown for outside close too
                    document.removeEventListener('pointerdown', handleOutsidePointerDown);

                    window.removeEventListener('resize', handleResizeOrScroll);
                    window.removeEventListener('scroll', handleResizeOrScroll, true);
                    window.removeEventListener('beforeunload', handleBeforeUnload);

                    removeSpacer();
                }

                function toggleCalendar(forceOpen) {
                    const shouldOpen = typeof forceOpen === 'boolean' ? forceOpen : !isOpen;

                    if (!shouldOpen && isOpen) {
                        picker.style.display = 'none';
                        dateContainer.classList.remove('expanded');
                        dateContainer.style.position = '';
                        dateContainer.style.zIndex = '';
                        dateContainer.style.width = '';
                        dateContainer.style.top = '';
                        dateContainer.style.left = '';
                        dateContainer.style.backgroundColor = '';
                        dateContainer.style.boxShadow = '';
                        dateContainer.style.boxSizing = '';
                        removeSpacer();
                        isOpen = false;
                        return;
                    }

                    if (shouldOpen && !isOpen) {
                        const containerWidth = dateContainer.offsetWidth;
                        const containerHeight = dateContainer.offsetHeight;

                        createSpacer();

                        const offsetParent = getOffsetParent(dateContainer);
                        const offsetParentRect = offsetParent.getBoundingClientRect();
                        const containerRect = dateContainer.getBoundingClientRect();

                        if (offsetParent !== document.body) {
                            const s = window.getComputedStyle(offsetParent);
                            if (s.position === 'static') offsetParent.style.position = 'relative';
                        }

                        dateContainer.style.position = 'absolute';
                        dateContainer.style.zIndex = '1000';
                        dateContainer.style.width = containerWidth + 'px';
                        dateContainer.style.top = (containerRect.top - offsetParentRect.top) + 'px';
                        dateContainer.style.left = (containerRect.left - offsetParentRect.left) + 'px';
                        dateContainer.style.backgroundColor = 'white';
                        dateContainer.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
                        dateContainer.style.boxSizing = 'border-box';

                        // resync selection from input when opening (no “wrong month highlighted”)
                        const fromInput = parseMonthYear(dateInput.value);
                        if (fromInput) {
                            selectedMonth = fromInput.monthIndex;
                            selectedYear = fromInput.year;
                        }

                        picker.style.display = 'block';
                        dateContainer.classList.add('expanded');
                        picker._updateMonthButtons?.();

                        const pickerHeight = picker.offsetHeight;
                        const top = parseFloat(dateContainer.style.top) || 0;

                        const spaceBelow = offsetParentRect.height - (top + containerHeight);
                        const spaceAbove = top;

                        if (!(spaceBelow >= pickerHeight || (spaceBelow >= 0 && spaceBelow > spaceAbove))) {
                            if (spaceAbove >= pickerHeight || spaceAbove > spaceBelow) {
                                dateContainer.style.top = (top - pickerHeight - 5) + 'px';
                            }
                        }

                        isOpen = true;
                    }
                }

                picker = createMonthYearPicker();

                // listeners
                dateInputArea.addEventListener('pointerdown', handleOpenPointerDown);
                dateInput.addEventListener('keydown', handleKeydown);
                clearDateBtn.addEventListener('click', handleClearClick);

                document.addEventListener('pointerdown', handleOutsidePointerDown);
                window.addEventListener('resize', handleResizeOrScroll);
                window.addEventListener('scroll', handleResizeOrScroll, true);
                window.addEventListener('beforeunload', handleBeforeUnload);

                if (dateInput.value) clearDateBtn.classList.remove('d-none');
                else clearDateBtn.classList.add('d-none');

                const api = {
                    toggleCalendar,
                    destroy: cleanupExistingListeners,
                    getSelectedDate: function () {
                        return selectedMonth !== null && selectedYear !== null
                            ? `${monthNames[selectedMonth]}-${selectedYear}`
                            : null;
                    }
                };

                dateContainer._monthYearApi = api;
                return api;
            }
              var student_start_date = $('#student_start_date').val();
              var payment_date = $('#payment_date').val();
              var payment_date_edit = $('#payment_date_edit').val();
              var kumon_month = $('#kumon_month').val();
              var kumon_month_edit = $('#kumon_month_edit').val();
              var student_start_date_edit = $('#student_start_date_edit').val();
              var student_status_date = $('#student_status_date').val();
              var contract_end_date = $('#contract_end_date').val();
              var invoice_date = $('#edit_invoice_date').val();
              var po_date = $('#edit_po_date').val();

              initCustomRangeCalendar(
                  'vacationDateRangeContainer',
                  'vacation_date_range',
                  'clear_vacation_date_range',
                  $('#vacation_date_range').val()
              );

              initCustomCalendar('startDateContainer', 'student_start_date', 'clear_start_date',
                  student_start_date);
              initCustomCalendar('startDateContainerEdit', 'student_start_date_edit', 'clear_start_date_edit',
                  student_start_date_edit);
              initCustomCalendar('studentStatusDateContainer', 'student_status_date', 'clear_student_status_date',
                  student_status_date);
              initCustomCalendar('paymentDateContainer', 'payment_date', 'clear_payment_date',
                  payment_date);
              initCustomCalendar('paymentDateContainerEdit', 'payment_date_edit', 'clear_payment_date_edit',
                  payment_date_edit);
              initMonthYearCalendar('kumonMonthContainer', 'kumon_month', 'clear_kumon_month', kumon_month);
              initMonthYearCalendar('kumonMonthContainerEdit', 'kumon_month_edit', 'clear_kumon_month_edit',
                  kumon_month_edit);
              //   initCustomCalendar('endDateContainer', 'contract_end_date', 'clear_end_date', contract_end_date);
              //   initCustomCalendar('invoiceDateContainer', 'edit_invoice_date', 'clear_invoice_date', invoice_date);
              //   initCustomCalendar('poDateContainer', 'edit_po_date', 'clear_po_date', po_date);



              let selectedClients = [];

              let selectedSite;

              <?php
              $affliates = [];
              ?>

              selectedClients = JSON.parse('<?php echo json_encode($affliates); ?>');



              $('#saveAffiliation').click(function() {
                  const selectedOptions = $('#modal_client').val() || [];
                  selectedClients = [...selectedOptions];
                  selectedSite = $("#edit_site_id option:selected").val();

                  // Hide the modal
                  $('#addAffiliateClient').modal('hide');
              });


              // $(document).on('click.clonePage', '.bootstrap-select .dropdown-item', function(event) {

              //     if ($(event.target).has('.add-new-currency').length) {

              //         $("#AddNewCurrencyModal input[name=new_currency]").val('')

              //         $("#AddNewCurrencyModal").modal('show')

              //     }

              //     if ($(event.target).closest('.add-new-currency').length) {

              //         $("#AddNewCurrencyModal input[name=new_currency]").val('');

              //         $("#AddNewCurrencyModal").modal('show');

              //     }

              // })

              // $(document).on('click.clonePage', '#NewCurrencySave', function() {

              //     const currency = $("#AddNewCurrencyModal input[name=new_currency]").val().trim();



              //     if (currency != "") {

              //         if ($("#currency option").filter(function() {

              //                 return $(this).text() === currency;

              //             }).length > 0) {

              //             Dashmix.helpers('notify', {

              //                 message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> This currency already exists',

              //                 delay: 5000

              //             });

              //         } else {

              //             $("#currency").val('').selectpicker('destroy');

              //             $("#currency").append(`<option value="${currency}" selected>${currency}</option>`);

              //             $("#currency").selectpicker();

              //             $("#AddNewCurrencyModal").modal('hide');

              //         }

              //     } else {

              //         Dashmix.helpers('notify', {

              //             message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for Currency',

              //             delay: 5000

              //         });

              //     }

              // });    

              //   });



              //   $(function() {

              var deletedRowBackup = null;


              // 🔹 Add New Contract Detail
              $('#btnAddContractDetail').on('click.clonePage', function() {
                  $('#contractDetailForm')[0].reset();
                  $('#editRowIndex').val('');
                  $('#contractDetailLabel').text('Add Contract Detail');
                  $('#detail_comments').text('');

                  // Clear asset selection
                  $('.multi-select-dropdown input[type="checkbox"]').prop('checked', false);
                  $('.multi-select-dropdown .selected-value').text('Select Assets');
                  $('.pn-no-dropdown .selected-value').text('Select PN #');
                  $('.multi-select-dropdown .clear-icon').addClass('d-none');
                  $('.pn-no-dropdown .clear-icon').addClass('d-none');

                  $('#contractDetailModal').modal('show');
              });

              // 🔹 Save (Add or Edit)
              //   $('#saveContractDetail').on('click.clonePage', function() {
              //       const rowIndex = $('#editRowIndex').val();
              //       const qty = $('#qty').val();
              //       const pn_no = $('#pn_no').val();
              //       // const type = $('input[name="contract_type_line"]:checked').val();
              //       const selectedType = $('input[name="contract_type_line"]:checked');
              //       const typeFull = selectedType.val(); // e.g. "Software Support"
              //       const typeShort = selectedType.data('short'); // e.g. "SFT"

              //       const desc = $('#detail_comments').val();
              //       const cost = $('#msrp').val();

              //       if (!qty || !pn_no) {
              //           alert('Please fill required fields.');
              //           return;
              //       }

              //       // ✅ Collect selected assets
              //       const selectedAssets = [];
              //       $('.multi-select-dropdown input[type="checkbox"]:checked').each(function() {
              //           const li = $(this).closest('li');
              //           const id = li.data('value');
              //           const sn = li.data('sn') || '';
              //           const fqdn = li.data('fqdn') || '';
              //           const hostname = li.data('hostname') || '';
              //           const type = li.data('type') || '';
              //           const asset_type = li.data('assettype') || '';

              //           selectedAssets.push({
              //               id,
              //               sn,
              //               fqdn,
              //               hostname,
              //               asset_type,
              //           });
              //       });

              //       // ✅ Build assets preview (limit 5 + ellipsis)
              //       let assetContent = '<div class="text-center">Assigned Assets</div>';
              //       if (selectedAssets.length === 0) {
              //           assetContent +=
              //               `<div class="fs-15 fw-500 font-titillium text-center text-warning mt-1">None</div>`;
              //       } else {
              //           const visible = selectedAssets.slice(0, 5);
              //           visible.forEach(a => {
              //               const snText = a.sn || '';
              //               const fqdnText = a.fqdn || '';
              //               const hostText = a.hostname || '';
              //               const asset_type = a.asset_type || '';
              //               const asset_id = a.id;
              //               // Use snText if asset_type is 'physical', otherwise fqdnText
              //               const displayText = (asset_type.toLowerCase() === 'physical') ? snText :
              //                   fqdnText;

              //               const icon =
              //                   `<i class="fa-thin fa-network-wired text-success mr-2 fs-16"></i>`;
              //               assetContent += `
        //                     <div class="fw-500 font-titillium fs-15 mt-1 d-flex align-items-center text-success" data-id="${asset_id}">
        //                         ${icon}${displayText}
        //                     </div>`;
              //           });

              //           if (selectedAssets.length > 5) {
              //               const remaining = selectedAssets.length - 5;
              //               const dataAssets = htmlspecialchars(JSON.stringify(selectedAssets));
              //               assetContent += `
        //                 <div class="text-muted font-italic mt-2 mb-1">${remaining} more assets</div>
        //                 <a href="javascript:;" class="showAssetsModal mt-0" data-assets="${dataAssets}">
        //                     <i class="fa-thin fa-ellipsis-stroke text-muted fs-22"></i>
        //                 </a>`;
              //           }
              //       }

              //       const popoverHtml = `<div class="asset-popover d-none">${assetContent}</div>`;

              //       const newRow = `
        //             <tr data-assets='${JSON.stringify(selectedAssets)}'>
        //                 <td class="py-2 pl-2 border-0 align-middle" style="border-radius: 13px 0 0 13px;">
        //                 <div class="asset-trigger d-flex align-items-center">
        //                     <i class="fa-duotone fa-server fs-18 mr-2"
        //                     style="--fa-primary-color:#36454f;--fa-secondary-color:#36454f;--fa-secondary-opacity:.2;"></i>
        //                     ${popoverHtml}
        //                 </div>
        //                 </td>
        //                 <td class="py-2 border-0 align-middle pl-1"><span class="fw-300 text-darkgrey font-titillium fs-15 c_qty">${qty}</span></td>
        //                 <td class="py-2 border-0 align-middle pl-1"><span class="fw-300 text-darkgrey font-titillium fs-15 c_pn_no">${pn_no}</span></td>
        //                 <td class="py-2 border-0 align-middle pl-1"><span class="fw-300 text-darkgrey font-titillium fs-15 c_type" data-short="${typeShort}">${typeShort}</span></td>
        //                 <td class="py-2 border-0 align-middle pl-1"><span class="fw-300 text-darkgrey font-titillium fs-15 c_desc">${desc}</span></td>
        //                 <td class="py-2 border-0 align-middle pl-1"><span class="fw-300 text-darkgrey font-titillium fs-15 c_cost">$ ${parseFloat(cost || 0).toFixed(2)}</span></td>
        //                 <td class="py-2 border-0 text-right align-middle" style="border-radius: 0 13px 13px 0;">
        //                 <a class="dropdown-toggle text-grey" data-toggle="dropdown" href="javascript:;">
        //                     <i class="fa-light fa-ellipsis-stroke-vertical fs-20"></i>
        //                 </a>
        //                 <div class="dropdown-menu py-0">
        //                     <a class="dropdown-item d-flex align-items-center edit-contract-detail mb-0">
        //                         <i class="fa-light fa-pencil mr-2 fs-15"></i>
        //                         <span class="fs-15 fw-400">Edit</span>
        //                     </a>
        //                     <a class="dropdown-item d-flex align-items-center delete-contract-detail mb-0">
        //                         <i class="fa-light fa-circle-xmark mr-2 fs-15"></i>
        //                         <span class="fs-15 fw-400">Delete</span>
        //                     </a>
        //                 </div>
        //                 </td>
        //             </tr>`;

              //       if (rowIndex) {
              //           $('#contractDetailsBody tr').eq(parseInt(rowIndex)).replaceWith(newRow);
              //           $('#contract_not_found').remove();
              //       } else {
              //           $('#contractDetailsBody').append(newRow);
              //           $('#contract_not_found').remove();
              //       }

              //       // ✅ Recalculate total
              //       calculateTotalAmount();

              //       $('#contractDetailModal').modal('hide');

              //       if ($('#contractDetailsBody tr').length > 0) {
              //           $('.contractDetailsBody-empty').fadeOut();
              //       } else {
              //           $('.contractDetailsBody-empty').fadeIn();
              //       }
              //       showToast(rowIndex ? 'contract-toast-updated' : 'contract-toast-added');
              //   });
              // 🔹 Save (Add or Edit)
              $('#saveContractDetail').on('click.clonePage', function() {
                  const rowIndex = $('#editRowIndex').val();
                  const qty = $('#qty').val();
                  const pn_no = $('#pn_no').val();

                  const selectedType = $('input[name="contract_type_line"]:checked');
                  const typeFull = selectedType.val();
                  const typeShort = selectedType.data('short');

                  const desc = $('#detail_comments').val();
                  const cost = $('#msrp').val();

                  const qty_ = validateFieldByIdOrName('qty', 0);
                  if (!qty_) {
                      $('#qty').focus();
                      $('#contractDetailModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#contractDetailModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else {
                      clearFieldValidation('qty')
                  }
                  const pn_no_ = validateFieldByIdOrName('pn_no', 0);
                  if (!pn_no_) {
                      $('#pn_no').focus();
                      $('#contractDetailModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#contractDetailModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else {
                      clearFieldValidation('pn_no')
                  }

                  //   $('#contractDetailModal .form-validation-toast').fadeOut();
                  // let qty_ = validateFieldByIdOrName('qty'); // by ID
                  // let pn_no_ = validateFieldByIdOrName('pn_no'); // by ID
                  // if (!qty_ || !pn_no_) {
                  //     showFormValidation();
                  //     return false;

                  // }

                  // --------------------------------------------
                  // ✅ PN#: DO NOT ALLOW DUPLICATES (NEW ONLY)
                  // --------------------------------------------
                  let duplicate = false;

                  $('#contractDetailsBody .c_pn_no').each(function() {
                      if ($(this).text().trim().toLowerCase() === pn_no.trim().toLowerCase()) {
                          duplicate = true;
                          return false;
                      }
                  });

                  // Block duplicate ONLY on new addition
                  if (!rowIndex && duplicate) {
                      alert('This PN# already exists. Please enter a unique PN.');
                      return;
                  }

                  // --------------------------------------------
                  // ✅ TYPE TAG STYLING BASED ON typeShort
                  // --------------------------------------------
                  let typeClass = "";

                  switch (typeShort) {
                      case "SUB": // Subscription
                          typeClass = "tag-subscription";
                          break;
                      case "HDW": // Hardware
                          typeClass = "tag-hardware";
                          break;
                      case "SFT": // Software
                          typeClass = "tag-software";
                          break;
                      case "MSP": // Other (MSP)
                          typeClass = "tag-msp";
                          break;
                      default:
                          typeClass = "";
                  }


                  const newRow = `
                                <tr data-assets='${JSON.stringify(selectedAssets.filter(a => a.pn_no === pn_no))}' data-pn_no="${pn_no}">
                                <td class="py-2 pl-2 border-0 align-middle" style="border-radius: 13px 0 0 13px;color:#0D0D0D!important;">
                                <div class="asset-trigger d-flex align-items-center" data-pn_no="${pn_no}">
                                    <i class="fa-thin fa-server fs-18 mr-2"
                                    style="--fa-primary-color:#36454f;--fa-secondary-color:#36454f;--fa-secondary-opacity:.2;"></i>
                                    <div class="asset-popover d-none" data-pn_no="${pn_no}"></div>
                                </div>
                                </td>
                                <td class="py-2 border-0 align-middle pl-1 text-center"><span class="fw-300 text-darkgrey font-titillium fs-15 c_qty">${qty}</span></td>
                                <td class="py-2 border-0 align-middle pl-1"><span class="fw-300 text-darkgrey font-titillium fs-15 c_pn_no truncate-pn-no">${pn_no}</span></td>
                                <td class="py-2 border-0 align-middle pl-1"><span class="c_type type-tag ${typeClass}" style="color:#0D0D0D!important;" data-short="${typeShort}">${typeShort}</span></td>
                                <td class="py-2 border-0 align-middle pl-1"><span class="fw-300 text-darkgrey font-titillium fs-15 c_desc truncate-desc">${desc}</span></td>
                                <td class="py-2 border-0 align-middle pl-1 text-right"><span class="fw-300 text-darkgrey font-titillium fs-15 c_cost">$ ${parseFloat(cost || 0).toFixed(2)}</span></td>
                                <td class="py-2 border-0 text-right align-middle drag-handle" style="border-radius: 0 13px 13px 0;color:#0D0D0D!important;opacity:0;">
                                <a class="dropdown-toggle text-grey" data-toggle="dropdown"  href="javascript:;">
                                    <i class="fa-thin fa-ellipsis-stroke-vertical fs-20"></i>
                                </a>
                                <div class="dropdown-menu py-0">
                                    <a class="dropdown-item d-flex align-items-center edit-contract-detail mb-0">
                                        <i class="fa-light fa-pencil mr-2 fs-15"></i>
                                        <span class="fs-15 fw-400" style="color:#3f3f3f!important;font-weight:normal!important">Edit</span>
                                    </a>
                                    <a class="dropdown-item d-flex align-items-center delete-contract-detail mb-0">
                                        <i class="fa-light fa-circle-xmark mr-2 fs-15"></i>
                                        <span class="fs-15 fw-400" style="color:#3f3f3f!important;font-weight:normal!important">Delete</span>
                                    </a>
                                </div>
                                </td>
                            </tr>`;

                  if (rowIndex) {
                      $('#contractDetailsBody tr').eq(parseInt(rowIndex)).replaceWith(newRow);
                  } else {
                      $('#contractDetailsBody').append(newRow);
                  }

                  // Recalculate total
                  calculateTotalAmount();
                  clearFieldValidation('qty')
                  clearFieldValidation('pn_no')

                  $('#contractDetailModal').modal('hide');

                  if ($('#contractDetailsBody tr').length > 0) {
                      $('.contractDetailsBody-empty').fadeOut();
                  } else {
                      $('.contractDetailsBody-empty').fadeIn();
                  }
                  $('#pn_no').siblings('.dropdown-options').find('li').removeClass('active');
                  showToast(rowIndex ? 'contract-toast-updated' : 'contract-toast-added');
                  $('[data-toggle="tooltip"]').tooltip();
              });

              $('#contractDetailModal').on('hidden.bs.modal', function() {
                  $('#contractDetailModal .form-validation-toast').hide();
                  clearFieldValidation('qty')
                  clearFieldValidation('pn_no')
              })


              $(document).on('mouseenter', '.affiliate-item', function() {
                  $(this).find('.drag-handle').css('opacity', '1');
              }).on('mouseleave', '.affiliate-item', function() {
                  $(this).find('.drag-handle').css('opacity', '0');
              });
              $(document).on('mouseenter', '#contractDetailsBody tr', function() {
                  $(this).find('.drag-handle').css('opacity', '1');
              }).on('mouseleave', '#contractDetailsBody tr', function() {
                  $(this).find('.drag-handle').css('opacity', '0');
              });


              // 🔹 Edit Row
              $(document).on('click.clonePage', '.edit-contract-detail', function(e) {
                  e.preventDefault();
                  const row = $(this).closest('tr');
                  const index = row.index();

                  $('#qty').val(row.find('td:eq(1) span').text());
                  // $('#pn_no').val(row.find('td:eq(2) span').text());
                  const pnValue = row.find('td:eq(2) span').text().trim();
                  $('#pn_no').val(pnValue);
                  $('.pn-no-dropdown')
                      .attr('data-selected-id', pnValue)
                      .attr('data-selected-text', pnValue)
                      .find('.selected-value').text(pnValue);
                  $('.pn-no-dropdown .clear-icon').toggleClass('d-none', pnValue === '');

                  // $('#contract_type_line').val(row.find('td:eq(3) span').text());
                  const typeShort = row.find('td:eq(3) span').data('short');
                  $('input[name="contract_type_line"][data-short="' + typeShort + '"]').prop('checked', true);

                  $('#detail_comments').text(row.find('td:eq(4) span').text());
                  let msrpText = row.find('td:eq(5) span').text();
                  let msrpValue = msrpText.replace('$', '').replace(/,/g, '').trim(); // remove $ and commas
                  $('#msrp').val(parseFloat(msrpValue));
                  $('#editRowIndex').val(index);

                  $('#contractDetailLabel').text('Edit Contract Detail');
                  $('#contractDetailModal').modal('show');
              });

              // 🔹 Delete Row
              //   $(document).on('click.clonePage', '.delete-contract-detail', function(e) {
              //       e.preventDefault();
              //       if (confirm('Delete this row?')) {
              //           $(this).closest('tr').fadeOut(200, function() {
              //               $(this).remove();
              //               calculateTotalAmount();
              //           });
              //       }
              //   });
              $(document).on('click.clonePage', '.delete-contract-detail', function(e) {
                  e.preventDefault();
                  $('.dropdown-menu').removeClass('show');
                  $('.dropdown-menu').removeAttr('style');
                  const row = $(this).closest('tr');
                  const index = row.index(); // store original position

                  // Save row for undo restore
                  deletedRowBackup = {
                      html: row.prop('outerHTML'),
                      index: index
                  };

                  row.fadeOut(200, function() {
                      row.remove();
                      calculateTotalAmount();
                  });
                  showToast('contract-toast-deleted', 5000);
                  setTimeout(() => {
                      deletedRowBackup = null;
                  }, 5000);
              });

              $(document).on('click.clonePage', '.undo-delete-contract', function() {
                  if (!deletedRowBackup) return;

                  let {
                      html,
                      index
                  } = deletedRowBackup;

                  // Insert row back at same position
                  const rows = $('#contractDetailsBody tr');

                  if (rows.length === 0 || index >= rows.length) {
                      $('#contractDetailsBody').append(html);
                  } else {
                      rows.eq(index).before(html);
                  }

                  $('#undoToast').fadeOut();
                  calculateTotalAmount();

                  deletedRowBackup = null;

                  showToast('contract-toast-recovered');
              });



              // 🔹 Hover popover
              $(document).on('mouseenter', '.asset-trigger', function(event) {
                  var pn_no = $(this).attr('data-pn_no');
                  loadContractAssets(pn_no, $(this));
              });

              $(document).on('mouseleave', '.asset-trigger', function() {
                  var pn_no = $(this).attr('data-pn_no');
                  // Find the asset-popover with matching pn_no and hide it
                  $('.asset-popover[data-pn_no="' + pn_no + '"]').addClass('d-none');
              });

              var selectedAssets = [];

              function loadContractAssets(pn_no, triggerElement) {
                  // Filter assets by pn_no
                  const filteredAssets = selectedAssets.filter(asset => asset.pn_no == pn_no);

                  let assetContent = '<div class="text-center">Assigned Assets</div>';

                  if (filteredAssets.length === 0) {
                      assetContent +=
                          `<div class="fs-15 fw-500 font-titillium text-center text-darkgrey mt-1">None</div>`;
                      assetContent += `
                <a href="javascript:;" class="showAssetsModal_2 mt-0" data-assets="" data-toggle="tooltip" data-trigger="hover"
                                    data-placement="top" title="" data-original-title="View/Remove Assets">
                    <i class="fa-thin fa-ellipsis-stroke text-muted fs-22"></i>
                </a>`;
                  } else {
                      const visible = filteredAssets.slice(0, 5);
                      visible.forEach(a => {
                          const snText = a.sn || '';
                          const fqdnText = a.fqdn || '';
                          const hostText = a.hostname || '';
                          const asset_type = a.asset_type || '';
                          const asset_id = a.id;

                          // Use snText if asset_type is 'physical', otherwise fqdnText
                          const displayText = (asset_type.toLowerCase() === 'physical') ? snText : fqdnText;

                          const icon = `<i class="fa-thin fa-network-wired text-success mr-2 fs-16"></i>`;
                          assetContent += `
                <div class="fw-500 font-titillium fs-15 mt-1 d-flex align-items-center text-success" data-id="${asset_id}">
                    ${icon}${displayText}
                </div>`;
                      });

                      const dataAssets = escapeHtml(JSON.stringify(filteredAssets));

                      function escapeHtml(text) {
                          const div = document.createElement('div');
                          div.textContent = text;
                          return div.innerHTML;
                      }
                      if (filteredAssets.length > 5) {
                          const remaining = filteredAssets.length - 5;
                          // Helper function to escape HTML
                          assetContent += `
                <div class="text-muted font-italic mt-2 mb-1">${remaining} more assets</div>`;
                      }
                      assetContent += `
            <a href="javascript:;" class="showAssetsModal_2 mt-0" data-assets="${dataAssets}" data-toggle="tooltip" data-trigger="hover"
                                    data-placement="top" title="" data-original-title="View/Remove Assets">
                <i class="fa-thin fa-ellipsis-stroke text-muted fs-22"></i>
            </a>`;
                  }

                  // Find the correct popover and update it
                  var popover = $('.asset-popover[data-pn_no="' + pn_no + '"]');

                  if (popover.length) {
                      // Update content and show popover
                      popover.html(assetContent).removeClass('d-none');
                  } else {
                      console.error('No popover found for pn_no:', pn_no);
                  }
              }

              // Modal open with assets
              $(document).on('click.clonePage', '.showAssetsModal_2', function(e) {
                  e.preventDefault();

                  // Instead of trying to parse the broken data-assets attribute,
                  // get the assets from selectedAssets using the pn_no

                  let pn_no = $(this).closest('.asset-trigger').data('pn_no') ||
                      $(this).closest('.asset-popover').data('pn_no') ||
                      '';

                  // Filter assets by pn_no from the global selectedAssets array
                  let assets = selectedAssets.filter(asset => asset.pn_no == pn_no);

                  // Extract IDs
                  let assetIds = assets.map(asset => asset.id);

                  $('#addAssetsModal').attr('data-exclude-ids', assetIds);
                  $('#addAssetsModal').data('pn_no', pn_no);

                  // Rest of your modal code remains the same...
                  let html = `<div class="border p-2 rounded-10px position-relative">
                    <div class="asset-array-toast-recovered" role="status" aria-live="polite">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-light fa-circle-check mr-2"></i>
                                        <span class="font-titillium fs-14 text-darkgrey">Line recovered
                                            successfully!</span>
                                    </div>
                                    <button type="button" data-section="asset-array-toast-recovered"
                                        class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                        <i class="fa-light fa-xmark"></i>
                                    </button>
                                </div>
                            </div>
                    <div class="asset-array-toast-deleted" role="status" aria-live="polite">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="fa-light fa-circle-check mr-2"></i>
                                        <span class="font-titillium fs-14 text-darkgrey">Line deleted</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <button type="button"
                                            class="btn text-darkgrey btn-undo undo-delete-asset-array font-titillium fs-14 mr-2"
                                            data-action="undo">
                                            Undo
                                        </button>
                                        <button type="button" data-section="asset-array-toast-deleted"
                                            class="btn p-0 btn-close-toast text-darkgrey" aria-label="Close">
                                            <i class="fa-light fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
          <table class="table table-sm table-striped table-borderless mb-0 font-titillium" id="assignedAssetsTable">
            <thead>
              <tr>
                <th></th>
                <th>Hostname</th>
                <th>Serial No.</th>
                <th width="80px" class="text-center"></th>
              </tr>
            </thead>
          <tbody>`;

                  if (assets.length === 0) {
                      html +=
                          `<tr><td colspan="4" class="text-center text-muted py-3">No assets assigned</td></tr>`;
                  } else {
                      const escapeHtml = (text) => {
                          if (!text) return '';
                          const div = document.createElement('div');
                          div.textContent = text;
                          return div.innerHTML;
                      };

                      assets.forEach(a => {
                          html += `<tr class="asset-row" data-asset-id="${a.id}">
                <td class="py-2"><i class="fa-light fa-${getAssetIcon(a.asset_type)} text-grey fs-18"></i></td>

                <!-- Hostname -->
                <td class="py-2 fw-300 fs-16 asset-cell">
                    <span class="asset-hostname-copy">${escapeHtml(a.fqdn || '')}</span>
                    <i class="fa-light fa-copy fs-14 text-grey asset-copy-icon hostname-icon mr-2 mt-1 float-right" style="cursor:pointer;" data-toggle="tooltip" 
                        data-html="true" 
                        title="Copy Hostname"></i>
                </td>

                <!-- Serial -->
                <td class="py-2 fw-300 fs-16 asset-cell">
                    <span class="asset-sr-copy">${escapeHtml(a.asset_type === 'physical' ? (a.sn || '') : '')}</span>
                    <i class="fa-light fa-copy fs-14 text-grey asset-copy-icon sr-icon mr-2 mt-1 float-right" style="cursor:pointer;" data-toggle="tooltip" 
                    data-html="true" 
                    title="Copy SN#">
                    </i>
                </td>
                <!-- Delete Button (hidden by default, shown on row hover) -->
            <td class="py-2 text-center" style="vertical-align: middle;">
                <a href="javascript:void();" class="delete-asset-btn" 
                        data-asset-id="${a.id}" 
                        data-asset-pn="${a.pn_no}" 
                        data-asset-name="${escapeHtml(a.fqdn || a.hostname || a.sn || '')}"
                        style="opacity: 0; transition: opacity 0.2s;">
                    <i class="fa-thin fa-circle-x text-darkgrey fs-18"></i>
                </a>
            </td>
             </tr>`;
                      });
                  }

                  html += '</tbody></table></div>';

                  $('#assetsModal_2 .modal-body').html(html);
                  $('#assetsModal_2').modal('show');

                  $('[data-toggle="tooltip"]').tooltip();

                  // Add hover effect to show delete buttons
                  $('#assignedAssetsTable tbody').on('mouseenter', 'tr.asset-row', function() {
                      $(this).find('.delete-asset-btn').css('opacity', '1');
                  }).on('mouseleave', 'tr.asset-row', function() {
                      $(this).find('.delete-asset-btn').css('opacity', '0');
                  });

                  // Add click handler for delete buttons
                  $('#assignedAssetsTable').on('click.clonePage', '.delete-asset-btn', function(e) {
                      e.preventDefault();
                      e.stopPropagation();

                      const assetId = $(this).attr('data-asset-id');
                      const assetName = $(this).data('asset-name');
                      const pn_no = $(this).attr('data-asset-pn');

                      // Confirm deletion
                      removeAssetFromContract(assetId, pn_no);
                  });
              });
              let lastRemovedAsset = null;
              let lastRemovedPnNo = null;
              // Function to remove an asset from the contract
              function removeAssetFromContract(assetId, pn_no) {
                  const assetIdNum = parseInt(assetId);

                  // Find the index of the asset in selectedAssets array
                  const assetIndex = selectedAssets.findIndex(asset =>
                      asset.id === assetIdNum && asset.pn_no == pn_no
                  );

                  if (assetIndex !== -1) {
                      // Remove asset from selectedAssets array
                      lastRemovedAsset = selectedAssets.splice(assetIndex, 1)[0];
                      lastRemovedPnNo = pn_no;

                      // Show success message
                      // showNotification(`Asset "${removedAsset.fqdn || removedAsset.hostname || removedAsset.sn || ''}" removed successfully`, 'success');
                      showToast('asset-array-toast-deleted', 50000)

                      setupUndoFunctionality();

                      // Refresh the modal content
                      refreshAssetsModal(pn_no);

                      // Refresh the popover
                      // refreshAssetPopover(pn_no);

                      // Update the exclude IDs for the "Add Assets" button
                      updateExcludeIds(pn_no);
                  } else {
                      // showNotification('Asset not found', 'warning');
                  }
              }

              function setupUndoFunctionality() {
                  // Remove any existing undo handlers
                  $('.undo-delete-asset-array').off('click.clonePage');

                  // Add new undo handler
                  $('.undo-delete-asset-array').on('click.clonePage', function() {
                      undoLastRemoval();
                  });

                  // Close toast handler
                  $('.btn-close-toast[data-section="asset-array-toast-deleted"]').off('click.clonePage').on(
                      'click.clonePage',
                      function() {
                          clearUndoData();
                          hideToast('asset-array-toast-deleted');
                      });
              }
              // Undo the last removal
              function undoLastRemoval() {
                  if (!lastRemovedAsset || !lastRemovedPnNo) {
                      console.log('No asset to undo');
                      return;
                  }

                  // Check if asset already exists in selectedAssets
                  const exists = selectedAssets.some(asset =>
                      asset.id === lastRemovedAsset.id && asset.pn_no == lastRemovedPnNo
                  );

                  if (!exists) {
                      // Add the asset back to selectedAssets
                      selectedAssets.push(lastRemovedAsset);

                      // Refresh the modal content
                      refreshAssetsModal(lastRemovedPnNo);

                      // Update the exclude IDs for the "Add Assets" button
                      updateExcludeIds(lastRemovedPnNo);

                      // Refresh the popover
                      // refreshAssetPopover(lastRemovedPnNo);

                      // Show success message
                      // showSuccessMessage('Asset restored successfully');
                      showToast('asset-array-toast-recovered')
                  }

                  // Clear undo data
                  clearUndoData();

                  // Hide the toast
                  hideToast('asset-array-toast-deleted');
              }

              // Clear undo data
              function clearUndoData() {
                  lastRemovedAsset = null;
                  lastRemovedPnNo = null;
              }

              function hideToast(toastId) {
                  $('.' + toastId).fadeOut();
              }

              function updateExcludeIds(pn_no) {
                  // Filter assets by pn_no from the global selectedAssets array
                  let assets = selectedAssets.filter(asset => asset.pn_no == pn_no);

                  // Extract IDs
                  let assetIds = assets.map(asset => asset.id);

                  // Update the exclude IDs for the "Add Assets" button
                  $('#addAssetsModal').attr('data-exclude-ids', assetIds);
              }
              // Function to refresh the assets modal content
              function refreshAssetsModal(pn_no) {
                  // Filter assets by pn_no from the global selectedAssets array
                  let assets = selectedAssets.filter(asset => asset.pn_no == pn_no);

                  let html = '';

                  if (assets.length === 0) {
                      html = `<tr><td colspan="4" class="text-center text-muted py-3">No assets assigned</td></tr>`;
                  } else {
                      const escapeHtml = (text) => {
                          if (!text) return '';
                          const div = document.createElement('div');
                          div.textContent = text;
                          return div.innerHTML;
                      };

                      assets.forEach(a => {
                          html += `<tr class="asset-row" data-asset-id="${a.id}">
                <td class="py-2"><i class="fa-light fa-${getAssetIcon(a.asset_type)} text-grey fs-18"></i></td>

                <!-- Hostname -->
                <td class="py-2 fw-300 fs-16 asset-cell">
                    <span class="asset-hostname-copy">${escapeHtml(a.fqdn || '')}</span>
                    <i class="fa-light fa-copy fs-14 text-grey asset-copy-icon hostname-icon mr-2 mt-1 float-right" style="cursor:pointer;" data-toggle="tooltip" 
                        data-html="true" 
                        title="Copy Hostname"></i>
                </td>

                <!-- Serial -->
                <td class="py-2 fw-300 fs-16 asset-cell">
                    <span class="asset-sr-copy">${escapeHtml(a.asset_type === 'physical' ? (a.sn || '') : '')}</span>
                    <i class="fa-light fa-copy fs-14 text-grey asset-copy-icon sr-icon mr-2 mt-1 float-right" style="cursor:pointer;" data-toggle="tooltip" 
                    data-html="true" 
                    title="Copy SN#">
                    </i>
                </td>
                
                <!-- Delete Button -->
                <td class="py-2 text-center" style="vertical-align: middle;">
                    <a href="javascript:void();" class="delete-asset-btn" 
                        data-asset-id="${a.id}" 
                        data-asset-pn="${a.pn_no}" 
                        data-asset-name="${escapeHtml(a.fqdn || a.hostname || a.sn || '')}"
                        style="opacity: 0; transition: opacity 0.2s;">
                    <i class="fa-thin fa-circle-x text-darkgrey fs-18"></i>
                </a>
                </td>
             </tr>`;
                      });
                  }

                  // Update the table body
                  $('#assignedAssetsTable tbody').html(html);

                  // Re-initialize tooltips
                  $('[data-toggle="tooltip"]').tooltip();
              }

              function getAssetIcon(assetType) {
                  const map = {
                      "virtual": "box",
                      "physical": "server",
                      "workstation": "computer",
                      "firewall": "black-brick-wall",
                      "switch": "ethernet",
                      "dsitribution switch": "network-wired",
                      "isp-router": "router",
                      "accesspoint": "circle-wifi",
                      "voip-phone": "phone-office",
                      "printer": "print",
                      "router": "arrows-to-circle",
                      "projector": "projector",
                      "ups": "plug-circle-plus",
                      "laptop": "laptop",
                      "pc": "desktop",
                      "scanner": "scanner-gun"
                  };

                  return map[assetType?.toLowerCase()] || "box"; // default icon
              }
              $(document).on('click.clonePage', '#selectAssets', function() {
                  var pn_no = $(this).attr('data-pn_no');
                  if (pn_no) {
                      addSelectedAssetsToArray(pn_no);
                  }
              })

              function addSelectedAssetsToArray(pn_no) {
                  // Get all checked checkboxes
                  const checkedCheckboxes = $('#allAssetsModal .asset-checkbox:checked');

                  if (checkedCheckboxes.length === 0) {
                      showSuccessNotify('', 'Please select at least one asset');
                      return false;
                  }

                  // Array to store newly added assets
                  const newAssets = [];

                  // Process each selected asset
                  checkedCheckboxes.each(function() {
                      const $checkbox = $(this);
                      const assetData = $checkbox.data('asset');

                      // Parse the asset data if it's stored as a string
                      let asset;
                      if (typeof assetData === 'string') {
                          try {
                              asset = JSON.parse(assetData.replace(/&#39;/g, "'"));
                          } catch (e) {
                              console.error('Error parsing asset data:', e);
                              return; // Skip this asset if parsing fails
                          }
                      } else {
                          asset = assetData;
                      }

                      // Add pn_no to the asset object
                      asset.pn_no = pn_no;

                      // Check if asset already exists in selectedAssets
                      const exists = selectedAssets.some(existingAsset =>
                          existingAsset.id === asset.id && existingAsset.pn_no === pn_no
                      );

                      if (!exists) {
                          // Add to selectedAssets array
                          selectedAssets.push(asset);
                          newAssets.push(asset);
                      }
                  });

                  if (newAssets.length > 0) {
                      // Find row with matching PN#
                      let row = $(`#contractDetailsBody tr[data-pn_no="${pn_no}"]`);

                      // If row exists, update its data-assets JSON attribute
                      if (row.length) {
                          const updatedAssets = selectedAssets.filter(a => a.pn_no === pn_no);
                          row.attr('data-assets', JSON.stringify(updatedAssets));
                      }
                      // Show success message
                      //   showSuccessNotify('', `${newAssets.length} asset(s) added successfully`);
                      $(".contract-assets-toast .toast-text").text(`${newAssets.length} asset(s) added successfully`)
                      showToast('contract-assets-toast');

                      // Close the modal
                      $('#allAssetsModal').modal('hide');

                      return true;
                  } else {
                      //   showNotification('No new assets were added', 'info');
                      console.log('No new assets were added');
                      return false;
                  }

              }

              // Helper to safely encode JSON for HTML attributes
              function htmlspecialchars(str) {
                  return str
                      .replace(/&/g, '&amp;')
                      .replace(/"/g, '&quot;')
                      .replace(/'/g, '&#039;')
                      .replace(/</g, '&lt;')
                      .replace(/>/g, '&gt;');
              }
              $(document).on('click.clonePage', '.pn-no-dropdown .dropdown-options li', function(e) {

                  const dropdown = $(this).closest('.pn-no-dropdown');
                  const value = $(this).data('value');
                  const text = $(this).text().trim();
                  const desc = $(this).data('desc') || ''; // 🔹 get PN description

                  dropdown.find('.selected-value').text(text);
                  dropdown.attr('data-selected-id', value);
                  dropdown.attr('data-selected-text', text);
                  dropdown.find('li').removeClass('active');
                  $(this).addClass('active');
                  dropdown.find('.clear-icon').removeClass('d-none');
                  dropdown.removeClass('open');

                  $('#pn_no').val(value);
                  $('#detail_comments').val(desc); // 🔹 auto-fill description textarea
              });

              function calculateTotalAmount() {
                  let total = 0;
                  $('#contractDetailsBody tr').each(function() {
                      const qty = parseFloat($(this).find('.c_qty').text()) || 0;
                      let costText = $(this).find('.c_cost').text().replace('$', '').trim();
                      costText = costText.replace(/,/g, ''); // ✅ remove commas
                      const cost = parseFloat(costText) || 0;
                      total += qty * cost;
                  });
                  $('#total_amount').text(`$ ${total.toFixed(2)}`);
              }

              calculateTotalAmount();


              var distributor_array = [];
              var distributorKey = 0;
              $(document).on('click.clonePage', '#add_distribution', function() {
                  // Get values from modal inputs
                  var newDistributerName = $('#add_distributor_id').closest('.add-distributor-div').find(
                      '.selected-value').text();
                  var newDistributerId = $('#add_distributor_id').val().trim();
                  var newReferenceNo = $('#add_reference_no').val().trim();
                  var newSalesOrderNo = $('#add_saleOrder_no').val().trim();


                  var newDistributerId_validate = validateFieldByIdOrName('add_distributor_id', 0);
                  if (newDistributerId === '') {
                      $('#add_distributor_id').focus();
                      $('#addDistributionModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#addDistributionModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else {
                      clearFieldValidation('add_distributor_id');
                  }
                  distributor_array.push({
                      key: distributorKey,
                      distributer: newDistributerId,
                      distributername: newDistributerName,
                      reference: newReferenceNo ?? '',
                      salesorder: newSalesOrderNo ?? ''
                  });

                  // Close the modal
                  $('#addDistributionModal').modal('hide');
                  showDistributerAdd();
                  $('.add-distributor-div').find('.selected-value').text('Select').css('color', '#999');
                  $('.add-distributor-div').find('.clear-icon').addClass('d-none');
                  $('#add_distributor_id').val('')
                  $('#add_reference_no').val('')
                  $('#add_saleOrder_no').val('')
                  clearFieldValidation('add_distributor_id');
                  clearFieldValidation('add_reference_no');
                  clearFieldValidation('add_saleOrder_no');
                  showToast('distribution-toast-added');
                  $('.add-distributor-div').find('.custom-dropdown .dropdown-options li').removeClass(
                      'active');
                  distributorKey++;
              });

              $("#addDistributionModal").on('hidden.bs.modal', function() {
                  $('#addDistributionModal .form-validation-toast').hide();
                  clearFieldValidation('add_distributor_id');
                  clearFieldValidation('add_reference_no');
                  clearFieldValidation('add_saleOrder_no');
              })

              function showDistributerAdd() {
                  var html = '';
                  for (var i = 0; i < distributor_array.length; i++) {

                      html += `
                        <tr class="affiliate-item" data="${i}" data-key="${distributor_array[i].key}">
                            <td class="py-2 border-0 align-middle" width="20" style="border-radius: 13px 0 0 13px;">
                                <i class="fa-light fa-grip-vertical drag-handle cursor-grab text-grey fs-16" 
                                   title="Drag" 
                                   style="opacity:0; transition:opacity 0.2s;"></i>
                            </td>
                            <td class="py-2 border-0 pl-2">
                                <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">${distributor_array[i].distributername}</span>
                            </td>
                            <td class="py-2 border-0 pl-2">
                                <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">${distributor_array[i].reference}</span>
                            </td>
                            <td class="py-2 border-0 pl-2">
                                <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">${distributor_array[i].salesorder}</span>
                            </td>
                            <td class="py-2 border-0 text-right align-middle drag-handle" width="50" style="border-radius: 0 13px 13px 0;opacity:0">
                                <a class="dropdown-toggle text-grey banner-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" href="javascript:;">
                                <i class="fa-thin fa-ellipsis-stroke-vertical fs-20"></i>
                            </a>
                            <div class="dropdown-menu py-0" aria-labelledby="dropdown-dropright-primary" x-placement="top-start" style="position: absolute; transform: translate3d(944px, 18px, 0px); top: 0px; left: 0px; will-change: transform;">
                                <a data="${i}" class="dropdown-item d-flex align-items-center edit-distribution mb-0" data-reference="" data-saleorder="">
                                    <i class="fa-light fa-pencil mr-2 fs-15"></i>
                                    <span class="fs-15 fw-400">Edit</span>
                                </a>
                                <a data="${i}" class="dropdown-item d-flex align-items-center delete-distribution mb-0" data-reference="" data-saleorder="">
                                    <i class="fa-light fa-circle-xmark mr-2 fs-15"></i>
                                    <span class="fs-15 fw-400">Delete</span>
                                </a>
                            </div>
                            </td>
                        </tr>
                    `;
                  }
                  $('.distributionTable:visible tbody').html(html);
                  if ($('.distributionTable:visible tbody tr').length > 0) {
                      $('.distributionTable-empty').fadeOut();
                  } else {
                      $('.distributionTable-empty').fadeIn();
                  }
              }
              $(document).on('click.clonePage', '.edit-distribution', function() {



                  var index = $(this).attr('data');

                  $('#distributorIndex').val(index);

                  $('#editDistributionModal').find('.custom-dropdown .selected-value').text(distributor_array[
                      index].distributername).css('color', '#3f3f3f');
                  $('#editDistributionModal').find('.custom-dropdown .clear-icon').removeClass('d-none');
                  $('#editDistributionModal #edit_distributor_id').val(distributor_array[index].distributer);
                  $('#editDistributionModal #edit_reference_no').val(distributor_array[index].reference);
                  $('#editDistributionModal #edit_saleOrder_no').val(distributor_array[index].salesorder);
                  $('#editDistributionModal').modal('show');
              })
              $(document).on('click.clonePage', '#update_distribution', function() {
                  // Get values from modal inputs
                  var index = $('#distributorIndex').val();
                  // Get values from modal inputs
                  var newDistributerName = $('#edit_distributor_id').closest('.edit-distributor-div').find(
                      '.selected-value').text();
                  var newDistributerId = $('#edit_distributor_id').val().trim();
                  var newReferenceNo = $('#edit_reference_no').val().trim();
                  var newSalesOrderNo = $('#edit_saleOrder_no').val().trim();

                  var newDistributerId_validate = validateFieldByIdOrName('edit_distributor_id', 0);
                  if (newDistributerId === '') {
                      $('#edit_distributor_id').focus();
                      $('#editDistributionModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editDistributionModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else {
                      clearFieldValidation('edit_distributor_id');
                  }

                  distributor_array[index] = {
                      key: distributor_array[index].key,
                      distributer: newDistributerId,
                      distributername: newDistributerName,
                      reference: newReferenceNo ?? '',
                      salesorder: newSalesOrderNo ?? ''
                  }

                  // Close the modal
                  $('#editDistributionModal').modal('hide');
                  showDistributerAdd();
                  $('.edit-distributor-div').find('.custom-dropdown .dropdown-options li').removeClass(
                      'active');
                  showToast('distribution-toast-updated');
              });
              var temp_distributor_array = [];
              $(document).on('click.clonePage', '.delete-distribution', function() {
                  var id = $(this).attr('data');
                  var key = distributor_array[id].key;
                  temp_distributor_array.push(distributor_array[id]);
                  $('.undo-delete-distribution').attr('data1', id);
                  $('.undo-delete-distribution').attr('data', key);
                  distributor_array.splice(id, 1);
                  showDistributerAdd();
                  showToast('distribution-toast-deleted', 5000);
              })

              $(document).on('click.clonePage', '.undo-delete-distribution', function() {
                  var id = $(this).attr('data');
                  var key = $(this).attr('data1');
                  let index = temp_distributor_array.filter(l => l.key == id);
                  if (index[0]) {
                      distributor_array.splice(id, 0, index[0]);
                      temp_distributor_array = temp_distributor_array.filter(l => l.key != id);
                      showDistributerAdd();
                  }
                  showToast('distribution-toast-undo');
              })

              //   $(document).on('click.clonePage', '#add_distribution', function() {
              //       // Get values from modal inputs
              //       var newReferenceNo = $('#edit_reference_no').val().trim();
              //       var newSalesOrderNo = $('#edit_saleOrder_no').val().trim();

              //       // Update the corresponding spans and hidden inputs
              //       $('span.reference_no').text(newReferenceNo);
              //       $('input.reference_no').val(newReferenceNo);

              //       $('span.distrubutor_sales_order_no').text(newSalesOrderNo);
              //       $('input.distrubutor_sales_order_no').val(newSalesOrderNo);

              //       // Close the modal
              //       $('#editDistributionModal').modal('hide');
              //   });

              $(document).on('click.clonePage', '#btnAddPurchasing', function() {
                  // Show modal
                  $('#editPurchasingModal').find('button.new-ok-btn').attr('id', 'add_purchasing');
                  $('#editPurchasingModal').modal('show');
              });
              //   $(document).on('click.clonePage', '.edit-purchasing', function() {
              //       // Show modal
              //       $('#editPurchasingModal').modal('show');
              //   });

              var purchasing_array = [];
              var purchasingKey = 0;
              $(document).on('click.clonePage', '#add_purchasing', function() {
                  // Get values from modal inputs
                  var estimate_no = $('#edit_estimate_no').val().trim();
                  var sales_order_no = $('#edit_sales_order_no').val().trim();
                  var invoice_no = $('#edit_invoice_no').val().trim();
                  var invoice_date = $('#edit_invoice_date').val().trim();
                  var po_no = $('#edit_po_no').val().trim();
                  var po_date = $('#edit_po_date').val().trim();

                  // 1. Validate mandatory fields first
                  if (estimate_no === "") {
                      validateFieldByIdOrName('edit_estimate_no', 0);
                      $('#edit_estimate_no').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else {
                      clearFieldValidation('edit_estimate_no');
                  }

                  if (sales_order_no === "") {
                      validateFieldByIdOrName('edit_sales_order_no', 0);
                      $('#edit_sales_order_no').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else {
                      clearFieldValidation('edit_sales_order_no');
                  }

                  //   first reset Validation
                  clearFieldValidation('edit_invoice_no');
                  clearFieldValidation('edit_invoice_date');
                  clearFieldValidation('edit_po_no');
                  clearFieldValidation('edit_po_date');

                  // 2. Validate the "either-or" rule for invoice and PO
                  // Rule: At least one complete pair must be filled (both fields of a pair)
                  var invoicePairEmpty = (invoice_no == "" && invoice_date == "");
                  var poPairEmpty = (po_no == "" && po_date == "");

                  // Check if at least one complete pair is filled
                  if (invoicePairEmpty && poPairEmpty) {
                      // Neither pair is complete, show error
                      validateFieldByIdOrName('edit_invoice_no', 0);
                      $('#edit_invoice_no').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  }

                  // 3. Check for incomplete pairs (one field filled but not the other)
                  if (invoice_no !== "" && invoice_date === "") {
                      validateFieldByIdOrName('edit_invoice_date', 0);
                      $('#edit_invoice_date').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else if (invoice_no === "" && invoice_date !== "") {
                      validateFieldByIdOrName('edit_invoice_no', 0);
                      $('#edit_invoice_no').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else {
                      // Clear validation if pair is either both empty or both filled
                      clearFieldValidation('edit_invoice_no');
                      clearFieldValidation('edit_invoice_date');
                  }

                  if (po_no !== "" && po_date === "") {
                      validateFieldByIdOrName('edit_po_date', 0);
                      $('#edit_po_date').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else if (po_no === "" && po_date !== "") {
                      validateFieldByIdOrName('edit_po_no', 0);
                      $('#edit_po_no').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else {
                      // Clear validation if pair is either both empty or both filled
                      clearFieldValidation('edit_po_no');
                      clearFieldValidation('edit_po_date');
                  }

                  purchasing_array.push({
                      key: purchasingKey,
                      estimate_no: estimate_no,
                      sales_order_no: sales_order_no,
                      invoice_no: invoice_no,
                      invoice_date: invoice_date,
                      po_no: po_no,
                      po_date: po_date,
                  });

                  // Close the modal
                  showPurchasing();
                  $('#edit_estimate_no').val('')
                  $('#edit_sales_order_no').val('')
                  $('#edit_invoice_no').val('')
                  $('#edit_invoice_date').val('')
                  $('#edit_po_no').val('')
                  $('#edit_po_date').val('')
                  purchasingKey++;

                  clearFieldValidation('edit_estimate_no');
                  clearFieldValidation('edit_sales_order_no');
                  clearFieldValidation('edit_invoice_no');
                  clearFieldValidation('edit_invoice_date');
                  clearFieldValidation('edit_po_no');
                  clearFieldValidation('edit_po_date');

                  // Close the modal
                  $('#editPurchasingModal').modal('hide');

                  showToast('purchasing-toast-added');
              });

              $('#editPurchasingModal').on('hidden.bs.modal', function() {
                  clearFieldValidation('edit_estimate_no');
                  clearFieldValidation('edit_sales_order_no');
                  clearFieldValidation('edit_invoice_no');
                  clearFieldValidation('edit_invoice_date');
                  clearFieldValidation('edit_po_no');
                  clearFieldValidation('edit_po_date');
                  $('#editPurchasingModal .form-validation-toast').hide();
              })

              $('#editPurchasingModal').on('hidden.bs.modal', function() {
                  $('#edit_estimate_no').val('')
                  $('#edit_sales_order_no').val('')
                  $('#edit_invoice_no').val('')
                  $('#edit_invoice_date').val('')
                  $('#edit_po_no').val('')
                  $('#edit_po_date').val('')
              });

              function showPurchasing() {
                  var html = '';
                  for (var i = 0; i < purchasing_array.length; i++) {

                      html += `
                        <tr class="affiliate-item" data="${i}" data-key="${purchasing_array[i].key}">
                            <td class="py-2 border-0 align-middle" width="20" style="border-radius: 13px 0 0 13px;">
                                <i class="fa-light fa-grip-vertical drag-handle cursor-grab text-grey fs-16" 
                                   title="Drag" 
                                   style="opacity:0; transition:opacity 0.2s;"></i>
                            </td>
                            <td class="py-2 border-0 pl-2">
                                <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">${purchasing_array[i].estimate_no}</span>
                            </td>
                            <td class="py-2 border-0 pl-1">
                                <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">${purchasing_array[i].sales_order_no}</span>
                            </td>
                            <td class="py-2 border-0 pl-1">
                                <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">${purchasing_array[i].invoice_no}</span>
                            </td>
                            <td class="py-2 border-0 pl-1">
                                <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">${purchasing_array[i].invoice_date}</span>
                            </td>
                            <td class="py-2 border-0 pl-1">
                                <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">${purchasing_array[i].po_no}</span>
                            </td>
                            <td class="py-2 border-0 pl-1">
                                <span class="fw-300 text-darkgrey font-titillium fs-15 selected-aff-client">${purchasing_array[i].po_date}</span>
                            </td>
                            <td class="py-2 border-0 text-right align-middle drag-handle" width="50" style="border-radius: 0 13px 13px 0;opacity:0">
                                <a class="dropdown-toggle text-grey banner-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" href="javascript:;">
                                <i class="fa-thin fa-ellipsis-stroke-vertical fs-20"></i>
                            </a>
                            <div class="dropdown-menu py-0" aria-labelledby="dropdown-dropright-primary" x-placement="top-start" style="position: absolute; transform: translate3d(944px, 18px, 0px); top: 0px; left: 0px; will-change: transform;">
                                <a data="${i}" class="dropdown-item d-flex align-items-center edit-purchasing mb-0" data-reference="" data-saleorder="">
                                    <i class="fa-light fa-pencil mr-2 fs-15"></i>
                                    <span class="fs-15 fw-400">Edit</span>
                                </a>
                                <a data="${i}" class="dropdown-item d-flex align-items-center delete-purchasing mb-0" data-reference="" data-saleorder="">
                                    <i class="fa-light fa-circle-xmark mr-2 fs-15"></i>
                                    <span class="fs-15 fw-400">Delete</span>
                                </a>
                            </div>
                            </td>
                        </tr>
                    `;
                  }
                  $('.purchaseTable:visible tbody').html(html);

                  if ($('.purchaseTable:visible tbody tr').length > 0) {
                      $('.purchaseTable-empty').fadeOut();
                  } else {
                      $('.purchaseTable-empty').fadeIn();
                  }
              }

              $(document).on('click.clonePage', '.edit-purchasing', function() {
                  var index = $(this).attr('data');
                  $('#puchasing_index').val(index);
                  $('#editPurchasingModal').find('button.new-ok-btn').attr('id', 'update_purchasing');
                  $('#editPurchasingModal #edit_estimate_no').val(purchasing_array[index].estimate_no);
                  $('#editPurchasingModal #edit_sales_order_no').val(purchasing_array[index].sales_order_no);
                  $('#editPurchasingModal #edit_invoice_no').val(purchasing_array[index].invoice_no);
                  $('#editPurchasingModal #edit_invoice_date').val(purchasing_array[index].invoice_date);
                  $('#editPurchasingModal #edit_po_no').val(purchasing_array[index].po_no);
                  $('#editPurchasingModal #edit_po_date').val(purchasing_array[index].po_date);
                  $('#editPurchasingModal').modal('show');
              })

              $(document).on('click.clonePage', '#update_purchasing', function() {
                  // Get values from modal inputs
                  var index = $('#puchasing_index').val();
                  // Get values from modal inputs
                  var estimate_no = $('#edit_estimate_no').val().trim();
                  var sales_order_no = $('#edit_sales_order_no').val().trim();
                  var invoice_no = $('#edit_invoice_no').val().trim();
                  var invoice_date = $('#edit_invoice_date').val().trim();
                  var po_no = $('#edit_po_no').val().trim();
                  var po_date = $('#edit_po_date').val().trim();

                  // 1. Validate mandatory fields first
                  if (estimate_no === "") {
                      validateFieldByIdOrName('edit_estimate_no', 0);
                      $('#edit_estimate_no').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else {
                      clearFieldValidation('edit_estimate_no');
                  }

                  if (sales_order_no === "") {
                      validateFieldByIdOrName('edit_sales_order_no', 0);
                      $('#edit_sales_order_no').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else {
                      clearFieldValidation('edit_sales_order_no');
                  }

                  //   first reset Validation
                  clearFieldValidation('edit_invoice_no');
                  clearFieldValidation('edit_invoice_date');
                  clearFieldValidation('edit_po_no');
                  clearFieldValidation('edit_po_date');

                  // 2. Validate the "either-or" rule for invoice and PO
                  // Rule: At least one complete pair must be filled (both fields of a pair)
                  var invoicePairEmpty = (invoice_no == "" && invoice_date == "");
                  var poPairEmpty = (po_no == "" && po_date == "");

                  // Check if at least one complete pair is filled
                  if (invoicePairEmpty && poPairEmpty) {
                      // Neither pair is complete, show error
                      validateFieldByIdOrName('edit_invoice_no', 0);
                      $('#edit_invoice_no').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  }

                  // 3. Check for incomplete pairs (one field filled but not the other)
                  if (invoice_no !== "" && invoice_date === "") {
                      validateFieldByIdOrName('edit_invoice_date', 0);
                      $('#edit_invoice_date').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else if (invoice_no === "" && invoice_date !== "") {
                      validateFieldByIdOrName('edit_invoice_no', 0);
                      $('#edit_invoice_no').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else {
                      // Clear validation if pair is either both empty or both filled
                      clearFieldValidation('edit_invoice_no');
                      clearFieldValidation('edit_invoice_date');
                  }

                  if (po_no !== "" && po_date === "") {
                      validateFieldByIdOrName('edit_po_date', 0);
                      $('#edit_po_date').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else if (po_no === "" && po_date !== "") {
                      validateFieldByIdOrName('edit_po_no', 0);
                      $('#edit_po_no').focus();
                      $('#editPurchasingModal .form-validation-toast').fadeIn();
                      setTimeout(() => {
                          $('#editPurchasingModal .form-validation-toast').fadeOut();
                      }, 3000);
                      return;
                  } else {
                      // Clear validation if pair is either both empty or both filled
                      clearFieldValidation('edit_po_no');
                      clearFieldValidation('edit_po_date');
                  }
                  purchasing_array[index] = {
                      key: purchasing_array[index].key,
                      estimate_no: estimate_no,
                      sales_order_no: sales_order_no,
                      invoice_no: invoice_no,
                      invoice_date: invoice_date,
                      po_no: po_no,
                      po_date: po_date
                  }

                  // Close the modal
                  $('#editPurchasingModal').modal('hide');
                  showPurchasing();
                  showToast('purchasing-toast-updated');
              });

              var temp_purchasing_array = [];
              $(document).on('click.clonePage', '.delete-purchasing', function() {
                  var id = $(this).attr('data');
                  var key = purchasing_array[id].key;
                  temp_purchasing_array.push(purchasing_array[id]);
                  $('.undo-delete-purchasing').attr('data1', id);
                  $('.undo-delete-purchasing').attr('data', key);
                  purchasing_array.splice(id, 1);
                  showPurchasing();
                  showToast('purchasing-toast-deleted', 5000);
              })
              $(document).on('click.clonePage', '.undo-delete-purchasing', function() {
                  var id = $(this).attr('data');
                  var key = $(this).attr('data1');
                  let index = temp_purchasing_array.filter(l => l.key == id);
                  if (index[0]) {
                      purchasing_array.splice(id, 0, index[0]);
                      temp_purchasing_array = temp_purchasing_array.filter(l => l.key != id);
                      showPurchasing();
                  }
                  showToast('purchasing-toast-undo');
              })
              $(document).off('click.clonePage', '.updateBtn').on('click.clonePage', '.updateBtn', function(e) {
                  e.preventDefault();

                  let salutation = $('#edit_salutation').val();
                  let first_name = $('#first_name').val();
                  let last_name = $('#last_name').val();
                  let client_address = $('#client_address').val();
                  let city = $('#city').val();
                  let province = $('#edit_province').val();
                  let postal_code = $('#postal_code').val();
                  let telephone_no = $('#telephone_no').val();
                  let primary_email_address = $('#primary_email_address').val();
                  let father_first_name = $('#father_first_name').val();
                  let father_last_name = $('#father_last_name').val();
                  let father_client_address = $('#father_client_address').val();
                  let father_city = $('#father_city').val();
                  let father_province = $('#father_province').val();
                  let father_postal_code = $('#father_postal_code').val();
                  let father_telephone_no = $('#father_telephone_no').val();
                  let father_primary_email_address = $('#father_primary_email_address').val();
                  let payment_method = $('input[name="payment_method"]:checked').val();
                  let portal_access = $('input[name="portal_access"]:checked').val();
                  let father_portal_access = $('input[name="father_portal_access"]:checked').val();

                  // collect emails to insert
                  let email_ids = [];
                  $('input[name="email_ids[]"]').each(function() {
                      email_ids.push($(this).val());
                  });

                  let validfirst_name = validateFieldByIdOrName('first_name'); // by ID
                  let validlast_name = validateFieldByIdOrName('last_name'); // by ID
                  let validclient_address = validateFieldByIdOrName('client_address'); // by ID
                  let validpostal_code = validateFieldByIdOrName('postal_code'); // by ID
                  let validtelephone_no = validateFieldByIdOrName('telephone_no'); // by ID
                  let validedit_province = validateFieldByIdOrName('edit_province'); // by ID
                  let validcity = validateFieldByIdOrName('city'); // by ID
                  let validprimary_email_address = validateFieldByIdOrName('primary_email_address'); // by ID



                  if (father_portal_access && !father_primary_email_address) {
                      validateFieldByIdOrName('father_primary_email_address', 0);
                      showFormValidation();
                      return false;
                  }

                  if (!validfirst_name || !validlast_name || !validclient_address || !validpostal_code ||
                      !validtelephone_no || !validedit_province || !validcity ||
                      !validprimary_email_address) {
                      showFormValidation();
                      return false;

                  } else {
                      $.ajax({
                          url: '{{ url('insert-client') }}',
                          method: 'POST',
                          data: {
                              _token: $('meta[name="csrf-token"]').attr('content'),
                              salutation,
                              first_name,
                              last_name,
                              client_address,
                              city,
                              province,
                              postal_code,
                              telephone_no,
                              primary_email_address,
                              father_first_name,
                              father_last_name,
                              father_client_address,
                              father_city,
                              father_province,
                              father_postal_code,
                              father_telephone_no,
                              father_primary_email_address,
                              payment_method,
                              portal_access,
                              father_portal_access,
                              email_ids,
                              students_array: JSON.stringify(students_array),
                              payments_array: JSON.stringify(payments_array),
                              vacation_array: JSON.stringify(vacation_array),
                              commentArray: JSON.stringify(commentArray),
                              attachmentArray: JSON.stringify(attachmentArray),
                          },
                          beforeSend: function() {
                              $('.updateBtn').prop('disabled', true);
                              // Show modal
                              $('#SaveModal').modal('show');
                          },
                          success: function(response) {
                            localStorage.removeItem('client_active_tab');
localStorage.removeItem('client_active_tab_target');
                              $('.updateBtn').prop('disabled', false);

                              setTimeout(function() {
                                  $('#SaveModal').modal('hide');
                                  sessionStorage.setItem('successTitle',
                                      'Client Cloned Successfully');
                                  sessionStorage.setItem('successMessage',
                                      'Client has been cloned successfully.');
    
                                  location.reload();

                              }, 500);
                          },
                          error: function(xhr) {
                              $('.updateBtn').prop('disabled', false);
                              alert('Error: ' + xhr.responseText);
                          }
                      });

                  }

              });

              function showSuccessNotify(title, message) {
                  Dashmix.helpers('notify', {
                      type: 'success',
                      message: `
                            <div>
                                <div class="font-titillium" style="font-weight: 800; color: #4EA833; font-size: 15pt;">${title}</div>
                                <div class="d-flex align-items-center">
                                    <div style="font-size: 14pt; margin-right: 8px;"><i class="fa-thin fa-circle-check"></i></div>
                                    <div>${message}</div>
                                </div>
                            </div>
                        `,
                      allow_dismiss: true,
                      delay: 3000,
                      align: 'center',
                  });
              }

              function showFormValidation() {
                  Dashmix.helpers('notify', {
                      type: 'danger',
                      message: `
                            <div>
                                <div class="d-flex align-items-center">
                                    <div style="font-size: 30pt; margin-right: 8px;"><i class="fa-light fa-triangle-exclamation text-orange"></i></div>
                                    <div class="mx-auto text-grey fw-300 fs-18">Form validation failed, form cannot be saved.</div>
                                </div>
                            </div>
                        `,
                      allow_dismiss: true,
                      delay: 3000,
                      align: 'center',
                  });
              }

              function showError(message) {
                  Dashmix.helpers('notify', {
                      type: 'danger',
                      message: `
                                <div>
                                    <div class="d-flex align-items-center">
                                        <div style="font-size: 30pt; margin-right: 8px;"><i class="fa-light fa-triangle-exclamation text-orange"></i></div>
                                        <div class="mx-auto text-grey fw-300 fs-18">${message}</div>
                                    </div>
                                </div>
                            `,
                      allow_dismiss: true,
                      delay: 3000,
                      align: 'center',
                  });
              }

              function dateValidateFieldByIdOrName(selector, showMessage = 1) {
                  let field;

                  // Use #id if selector exists as ID
                  if ($('#' + selector).length) {
                      field = $('#' + selector);
                  } else if ($('[name="' + selector + '"]').length) {
                      field = $('[name="' + selector + '"]');
                  } else {
                      return true; // skip if not found
                  }

                  let fieldVal = field.val()?.trim();

                  // Check for dropdowns with .edit-border or normal inputs
                  let borderDiv = field.closest('.edit-border'); // for inputs inside edit-border
                  if (borderDiv.length === 0) {
                      borderDiv = field.prev('.edit-border'); // for dropdown-type
                  }

                  let title = borderDiv.find('h6');
                  let icon = borderDiv.find('.constant-icon');

                  // Remove previous error message first (if any)
                  field.closest('.form-group, .input-wrapper').find('.validation-error').remove();
                  if (borderDiv.length) {
                      borderDiv.siblings('.validation-error').remove();
                  } else {
                      field.siblings('.validation-error').remove();
                  }

                  if (borderDiv.length) {
                      borderDiv.attr('style', (i, s) => (s || '') +
                          'border-color: #C41E3A !important; box-shadow: 0 0 4pt 2pt rgba(196,30,58,0.6) !important;'
                      );
                      title.attr('style', (i, s) => (s || '') + 'color: #C41E3A !important;');
                      icon.attr('style', (i, s) => (s || '') + 'color: #C41E3A !important;');
                  } else {
                      field.attr('style', (i, s) => (s || '') +
                          'border-color: #C41E3A !important; box-shadow: 0 0 4pt 2pt rgba(196,30,58,0.6) !important;'
                      );
                  }

                  // Determine where to place the error
                  let errorTarget = borderDiv.length ? borderDiv : field;

                  //   if (showMessage == 1) {

                  //       // Only add error if it doesn't already exist
                  //       if (!errorTarget.next('.validation-error').length) {
                  //           errorTarget.after(
                  //               '<div class="validation-error"><i class="fa-light fa-triangle-exclamation text-orange fs-16 mr-2"></i> Field validation failed.</div>'
                  //           );
                  //       }
                  //   }
              }

              function validateFieldByIdOrName(selector, showMessage = 1) {
                  let field;

                  // Use #id if selector exists as ID
                  if ($('#' + selector).length) {
                      field = $('#' + selector);
                  } else if ($('[name="' + selector + '"]').length) {
                      field = $('[name="' + selector + '"]');
                  } else {
                      return true; // skip if not found
                  }

                  let fieldVal = field.val()?.trim();

                  // Check for dropdowns with .edit-border or normal inputs
                  let borderDiv = field.closest('.edit-border'); // for inputs inside edit-border
                  if (borderDiv.length === 0) {
                      borderDiv = field.prev('.edit-border'); // for dropdown-type
                  }

                  let title = borderDiv.find('h6');
                  let icon = borderDiv.find('.constant-icon');

                  // Remove previous error message first (if any)
                  field.closest('.form-group, .input-wrapper').find('.validation-error').remove();
                  if (borderDiv.length) {
                      borderDiv.siblings('.validation-error').remove();
                  } else {
                      field.siblings('.validation-error').remove();
                  }

                  if (!fieldVal) {
                      if (borderDiv.length) {
                          borderDiv.attr('style', (i, s) => (s || '') +
                              'border-color: #C41E3A !important; box-shadow: 0 0 4pt 2pt rgba(196,30,58,0.6) !important;'
                          );
                          title.attr('style', (i, s) => (s || '') + 'color: #C41E3A !important;');
                          icon.attr('style', (i, s) => (s || '') + 'color: #C41E3A !important;');
                      } else {
                          field.attr('style', (i, s) => (s || '') +
                              'border-color: #C41E3A !important; box-shadow: 0 0 4pt 2pt rgba(196,30,58,0.6) !important;'
                          );
                      }

                      // Determine where to place the error
                      let errorTarget = borderDiv.length ? borderDiv : field;

                      if (showMessage == 1) {

                          // Only add error if it doesn't already exist
                          if (!errorTarget.next('.validation-error').length) {
                              errorTarget.after(
                                  '<div class="validation-error"><i class="fa-light fa-triangle-exclamation text-orange fs-16 mr-2"></i> Field validation failed.</div>'
                              );
                          }
                      }

                      return false;
                  } else {
                      // Remove highlight and error - FIXED
                      if (borderDiv.length) {
                          borderDiv.css({
                              'border-color': '',
                              'box-shadow': ''
                          });
                          title.css('color', '');
                          icon.css('color', '');
                          borderDiv.siblings('.validation-error').remove();
                      } else {
                          field.css({
                              'border-color': '',
                              'box-shadow': ''
                          });
                          field.siblings('.validation-error').remove();
                      }

                      // Also check in parent containers
                      field.closest('.form-group, .input-wrapper').find('.validation-error').remove();

                      return true;
                  }
              }

              // Universal blur handler - remove validation on focus out if field has value
              $(document).on('blur',
                  'input[type="text"], input[type="email"], input[type="number"], textarea, .custom-dropdown .selected-value',
                  function() {
                      let field = $(this);
                      let fieldVal = field.val()?.trim();

                      // Get the field's selector (ID or name)
                      let selector = field.attr('id') || field.attr('name');

                      if (!selector) return;

                      // If field has a value, clear validation styles
                      if (fieldVal) {
                          clearFieldValidation(selector);
                      }
                  });

              function clearFieldValidation(selector) {
                  let field;

                  // Detect field by ID or name
                  if ($('#' + selector).length) {
                      field = $('#' + selector);
                  } else if ($('[name="' + selector + '"]').length) {
                      field = $('[name="' + selector + '"]');
                  } else {
                      return; // field not found, exit
                  }

                  let borderDiv = field.closest('.edit-border');
                  if (borderDiv.length === 0) {
                      borderDiv = field.prev('.edit-border');
                  }

                  let title = borderDiv.find('h6');
                  let icon = borderDiv.find('.constant-icon');



                  // ----------------------------
                  //  REMOVE ALL ADDED STYLES
                  // ----------------------------

                  if (borderDiv.length) {
                      borderDiv.css({
                          'border-color': '',
                          'box-shadow': ''
                      });
                      title.css('color', '');
                      icon.css('color', '');
                  } else {
                      title.css('color', '');
                      icon.css('color', '');
                      field.css({
                          'border-color': '',
                          'box-shadow': ''
                      });
                  }

                  // Remove specific inline style overrides applied earlier
                  field.removeAttr("style");
                  borderDiv.removeAttr("style");
                  title.removeAttr("style");
                  icon.removeAttr("style");

                  // ----------------------------
                  //  REMOVE ALL VALIDATION ERROR MESSAGES
                  // ----------------------------

                  borderDiv.siblings('.validation-error').remove();
                  field.siblings('.validation-error').remove();
                  field.closest('.form-group, .input-wrapper').find('.validation-error').remove();
              }

              // Comment ARRAY



              var commentArray = [];
              var comment_key_count = 0;

              $('#CommentSave').click(function() {

                  var comment = $('textarea[name=comment]').val();

                  if (comment == '') {

                      Dashmix.helpers('notify', {

                          message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for Comment',

                          delay: 5000

                      });



                  } else {



                      var l = commentArray.length;

                      if (l < 5) {

                          commentArray.push({

                              key: comment_key_count,

                              comment: comment,

                              date: '{{ date('Y-M-d') }}',

                              time: '{{ date('h:i:s A') }}',

                              name: '{{ Auth::user()->firstname . ' ' . Auth::user()->lastname }}'

                          });

                          showComment()

                          $('#CommentModalAdd').modal('hide')

                          $('textarea[name=comment]').val('')

                          comment_key_count++;

                      }

                  }

              })

              $('#CommentSaveEdit').click(function() {

                  var comment = $('textarea[name=comment_edit]').val();

                  var id = $('input[name=comment_id_edit]').val();

                  if (comment == '') {

                      Dashmix.helpers('notify', {

                          message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1"> Please enter a value for Comment',

                          delay: 5000

                      });



                  } else {



                      var l = commentArray.length;



                      commentArray[id].comment = comment;

                      showComment()

                      $('#editCommentModalAdd').modal('hide')

                      $('textarea[name=comment_edit]').val('')



                  }

              })

              $(document).on('click.clonePage', '.btnEditCommentAdd', function() {

                  var id = $(this).attr('data');

                  $('#editCommentModalAdd').modal('show');

                  $('input[name=comment_id_edit]').val(id);

                  $('textarea[name=comment_edit]').val(commentArray[id].comment);



              })

              var temp_comment = [];

              $(document).on('click.clonePage', '.btnDeleteCommentAdd', function() {

                  var id = $(this).attr('data');

                  // $(this).tooltip('hide');

                  $('[data-toggle=tooltip]').tooltip();

                  var key = commentArray[id].key;

                  temp_comment.push(commentArray[id]);



                  commentArray.splice(id, 1);



                  Dashmix.helpers('notify', {

                      align: 'center',
                      message: `
                            <div>
                                <div class="font-titillium" style="font-weight: 800; color: #4EA833; font-size: 15pt;">Comment Deleted</div>
                                <div class="d-flex justify-content-between align-items-center"><div><img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> Comment Deleted.</div> <a href="javascript:;" class="btn font-titillium fw-500 py-1 px-3 ml-3 new-ok-btn btnCommentUndo" data1='${id}' data='${key}'>Undo</a></div>
                            </div>
                        `,

                      delay: 5000

                  });

                  showComment();



              })

              $(document).on('click.clonePage', '.btnCommentUndo', function() {

                  var id = $(this).attr('data');

                  var key = $(this).attr('data1');



                  let index = temp_comment.filter(l => l.key == id);



                  if (index[0]) {

                      commentArray.splice(id, 0, index[0]); // 2nd parameter means remove one item only

                      temp_comment = temp_comment.filter(l => l.key != id);







                      showComment();

                  }

              })
              showComment()

              function showComment() {
                  var html = '';
                  var user_iamge = $('#user_iamge').val();

                  if (commentArray.length > 0) {
                      $('.commentDiv').removeClass('d-none');
                      html +=
                          '<div class="col-sm-12"><button type="button" data-toggle="modal" data-target="#CommentModalAdd" class="btn font-titillium fw-500 py-1 px-3 ml-3 new-ok-btn mb-3" style="width: fit-content;">Add Comment</button></div>';

                      for (var i = 0; i < commentArray.length; i++) {
                          var image = '';

                          var img_class = '';
                          if (commentArray[i].image != null && commentArray[i].image != "") {

                              image = 'public/client_logos/' + commentArray[i].image;

                          } else if (user_iamge) {

                              image = 'public/client_logos/' + user_iamge;

                          }
                          html += '<div class="col-sm-12">';
                          html += '    <div class="border p-2 mb-3 border-style border-style border-hover-comment">';
                          html += '        <table class="table table-borderless table-vcenter mb-0">';
                          html += '            <tbody>';
                          html += '                <tr>';
                          html += '                    <td class="text-center pr-0 pl-2" style="width: 38px;">';
                          html += '                        <h1 class="mb-0 mr-1 text-white rounded">';

                          // Determine which image/icon to show
                          if (image) {
                              html +=
                                  `<img width="40px" class="bg-dark mr-2 ml-1" height="40" style="border-radius: 50%;" src="{{ asset('${image}') }}"`;
                          } else {
                              html += '<i class="fa-solid fa-circle-user text-darkgrey"></i>';
                          }

                          html += '                        </h1>';
                          html += '                    </td>';
                          html += '                    <td class="js-task-content pl-0">';
                          html += '                        <h6 class="font-titillium text-grey mb-1 fw-700">' +
                              commentArray[i].name + '</h6>';
                          html +=
                              '                        <h6 class="font-titillium text-grey mb-0 fw-300 fs-14">On ' +
                              commentArray[i].date + ' at ' + commentArray[i].time + ' GMT</h6>';
                          html += '                    </td>';
                          html += '                    <td class="align-content-start">';
                          html += '                        <div class="d-flex justify-content-end">';
                          html += '                            <a type="button" data="' + i +
                              '" class="float-right edit-comment-contrac t mr-2 btnEditCommentAdd" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Edit">';
                          html +=
                              '                                <i class="fa-thin fa-pen text-darkgrey fs-18"></i>';
                          html += '                            </a>';
                          html += '                            <a type="button" data="' + i +
                              '" class="float-right delete-comment-cont ract btnDeleteCommentAdd" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">';
                          html +=
                              '                                <i class="fa-thin fa-circle-xmark text-darkgrey fs-18"></i>';
                          html += '                            </a>';
                          html += '                        </div>';
                          html += '                    </td>';
                          html += '                </tr>';
                          html += '                <tr>';
                          html += '                    <td colspan="3" class="pt-0">';
                          html += '                        <h6 class="font-titillium text-darkgrey mb-1 fw-500">' +
                              commentArray[i].comment.replace(/\r?\n/g, '<br />') + '</h6>';
                          html += '                    </td>';
                          html += '                </tr>';
                          html += '            </tbody>';
                          html += '        </table>';
                          html += '    </div>';
                          html += '</div>';
                      }
                  } else {
                      $('.commentDiv').addClass('d-none');
                      html += '<div class="col-sm-12">';
                      html +=
                          '    <div class="font-titillium text-darkgrey mb-0 contractDetailsBody-empty pb-2 pt-0">No comments. Add a comment by using the Add Comment button.</div>';
                      html += '</div>';
                      html += '<div class="col-sm-12">';
                      html +=
                          '    <button type="button" data-toggle="modal" data-target="#CommentModalAdd" class="btn font-titillium fw-500 py-1 px-3 new-ok-btn d-flex" style="width: fit-content;">Add Comment</button>';
                      html += '</div>';
                  }

                  $('#commentBlock').html(html);
              }

              // Attachment ARRAY



              var attachmentArray = [];

              var attachment_key_count = 0;

              $('#AttachmentSave').click(function() {

                  var attachment = content3_image;

                  if (content3_image.length == 0) {

                      Dashmix.helpers('notify', {

                          message: '<img src="{{ asset('public/img/warning-yellow.png') }}" width="30px" class="mt-n1">  Add an attachment before saving.',

                          delay: 5000

                      });



                  } else {



                      var l = attachmentArray.length;







                      for (var i = 0; i < attachment.length; i++) {

                          attachmentArray.push({

                              key: attachment_key_count,

                              attachment: attachment[i],

                              date: '{{ date('Y-M-d') }}',

                              time: '{{ date('h:i:s A') }}',

                              name: '{{ Auth::user()->firstname . ' ' . Auth::user()->lastname }}'

                          });

                          attachment_key_count++;

                      }



                      filePond.removeFiles();

                      content3_image = [];

                      showAttachment()

                      $('#AttachmentModalAdd').modal('hide')







                  }

              })



              var temp_attachment = [];

              $(document).on('click.clonePage', '.btnDeleteAttachment', function() {

                  var id = $(this).attr('data');

                  // $(this).tooltip('hide');

                  $('[data-toggle=tooltip]').tooltip();

                  var key = attachmentArray[id].key;

                  temp_attachment.push(attachmentArray[id]);



                  attachmentArray.splice(id, 1);



                  Dashmix.helpers('notify', {

                      align: 'center',
                      message: `
                            <div>
                                <div class="font-titillium" style="font-weight: 800; color: #4EA833; font-size: 15pt;">Attachment Deleted</div>
                                <div class="d-flex justify-content-between align-items-center"><div><img src="{{ asset('public/img/green-check.png') }}" width="30px" class="mt-n1"> Attachment Deleted.</div> <a href="javascript:;" class="btn font-titillium fw-500 py-1 px-3 ml-3 new-ok-btn btnCommentUndo" data1='${id}' data='${key}'>Undo</a></div>
                            </div>
                        `,

                      delay: 5000

                  });

                  showAttachment();



              })





              $('#AttachmentClose').click(function() {

                  temp_attachment = [];

                  content3_image = [];

                  filePond.removeFiles();

              })



              $(document).on('click.clonePage', '.btnAttachmentUndo', function() {

                  var id = $(this).attr('data');

                  var key = $(this).attr('data1');



                  let index = temp_attachment.filter(l => l.key == id);



                  if (index[0]) {

                      attachmentArray.splice(id, 0, index[0]); // 2nd parameter means remove one item only

                      temp_attachment = temp_attachment.filter(l => l.key != id);

                      showAttachment();

                  }

              })


              showAttachment()

              function showAttachment() {
                  var html = '';

                  var user_iamge = $('#user_iamge').val();


                  if (attachmentArray.length > 0) {
                      $('.attachmentDiv').removeClass('d-none');
                      html +=
                          '<div class="col-sm-12"><button type="button" data-toggle="modal" data-target="#AttachmentModalAdd" class="btn font-titillium fw-500 py-1 px-3 ml-3 new-ok-btn mb-3" style="width: fit-content;">Add Attachment</button></div>';

                      for (var i = 0; i < attachmentArray.length; i++) {
                          if (attachmentArray[i].image != null && attachmentArray[i].image != "") {

                              image = 'public/client_logos/' + attachmentArray[i].image;

                          } else if (user_iamge) {

                              image = 'public/client_logos/' + user_iamge;

                          }
                          // Determine file extension and icon
                          var fileExtension = attachmentArray[i].attachment.split('.').pop().toLowerCase();
                          var icon = 'attachment.png';

                          if (fileExtension == 'pdf') {
                              icon = 'attch-Icon-pdf.png';
                          } else if (fileExtension == 'doc' || fileExtension == 'docx') {
                              icon = 'attch-word.png';
                          } else if (fileExtension == 'txt') {
                              icon = 'attch-word.png';
                          } else if (fileExtension == 'csv' || fileExtension == 'xlsx' || fileExtension == 'xlsm' ||
                              fileExtension == 'xlsb' || fileExtension == 'xltx') {
                              icon = 'attch-excel.png';
                          } else if (fileExtension == 'png' || fileExtension == 'gif' || fileExtension == 'webp' ||
                              fileExtension == 'svg') {
                              icon = 'attch-png icon.png';
                          } else if (fileExtension == 'jpeg' || fileExtension == 'jpg') {
                              icon = 'attch-jpg-icon.png';
                          } else if (fileExtension == 'potx' || fileExtension == 'pptx' || fileExtension == 'ppsx' ||
                              fileExtension == 'thmx') {
                              icon = 'attch-powerpoint.png';
                          }

                          html += '<div class="col-sm-6">';
                          html +=
                              '    <div class="border py-2 mb-3 border-style border-style-hover border-hover-comment">';
                          html += '        <table class="table table-borderless table-vcenter mb-0">';
                          html += '            <tbody>';
                          html += '                <tr>';
                          html += '                    <td class="text-center pr-0 pl-2" style="width: 38px;">';
                          html += '                        <h1 class="mb-0 mr-1 text-white rounded">';

                          // Determine which image/icon to show for user
                          if (image) {
                              html +=
                                  `<img width="40px" class="bg-dark mr-2 ml-1" height="40" style="border-radius: 50%;" src="{{ asset('${image}') }}"`;
                          } else {
                              html += '<i class="fa-solid fa-circle-user text-darkgrey"></i>';
                          }

                          html += '                        </h1>';
                          html += '                    </td>';
                          html += '                    <td class="js-task-content px-0">';
                          html += '                        <h6 class="font-titillium text-grey mb-1 fw-700">' +
                              attachmentArray[i].name + '</h6>';
                          html +=
                              '                        <h6 class="font-titillium text-grey mb-0 fw-300 fs-14">On ' +
                              attachmentArray[i].date + ' at ' + attachmentArray[i].time + ' GMT</h6>';
                          html += '                    </td>';
                          html += '                    <td class="align-content-start">';
                          html += '                        <a type="button" data="' + i +
                              '" class="float-right delete-attachm ent btnDeleteAttachment pt-2 pr-3" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="" data-original-title="Delete">';
                          html +=
                              '                            <i class="fa-solid fa-circle-xmark text-darkgrey fs-25 attachment-cross"></i>';
                          html += '                        </a>';
                          html += '                    </td>';
                          html += '                </tr>';
                          html += '                <tr>';
                          html += '                    <td colspan="3" class="py-0">';
                          html += '                        <a href="public/temp_uploads/' + attachmentArray[i]
                              .attachment + '" download target="_blank" class="">';
                          html +=
                              '                            <h6 class="font-titillium text-darkgrey mb-1 fw-500 d-flex align-items-center attachment-name">';
                          html += '                                <img src="public/img/' + icon +
                              '" width="25px" class="mr-2">';

                          // Truncate filename if too long
                          var fileName = attachmentArray[i].attachment;
                          if (fileName.length > 25) {
                              html += fileName.substring(0, 25);
                          } else {
                              html += fileName;
                          }

                          html += '                            </h6>';
                          html += '                        </a>';
                          html += '                    </td>';
                          html += '                </tr>';
                          html += '            </tbody>';
                          html += '        </table>';
                          html += '    </div>';
                          html += '</div>';
                      }
                  } else {
                      $('.attachmentDiv').addClass('d-none');
                      html += '<div class="col-sm-12">';
                      html +=
                          '    <div class="font-titillium text-darkgrey mb-0 pb-2 pt-0">No attachments. Add an Attachment by using the Add Attachment button.</div>';
                      html += '</div>';
                      html += '<div class="col-sm-12">';
                      html +=
                          '    <button type="button" data-toggle="modal" data-target="#AttachmentModalAdd" class="btn font-titillium fw-500 py-1 px-3 new-ok-btn d-flex" style="width: fit-content;">Add Attachment</button>';
                      html += '</div>';
                  }

                  $('#attachmentBlock').html(html);
              }

              function removeBrackets(){
                    $('.header-desc').each(function () {
                        let text = $(this).text();

                        // Remove all square brackets but keep inner text
                        text = text.replace(/[\[\]]+/g, '');

                        // Normalize extra spaces
                        text = text.replace(/\s{2,}/g, ' ').trim();

                        $(this).text(text);
                    });
                }



              // END Attachment

              //   fet detail lines
              var cloned_client_id = $('#cloned_client_id').val();

              $.ajax({
                  type: 'get',
                  url: "{{ url('get-client-students') }}",
                  data: {
                      id: cloned_client_id
                  },
                  success: function(res) {

                      students_array = [];

                      res.forEach(student => {

                          let subjects = [];
                          if (Array.isArray(student.subjects)) {
                              student.subjects.forEach(sub => {
                                  subjects.push(String(sub
                                      .id)); // keep same type as checkbox values
                              });
                          }

                          students_array.push({
                              key: studentKey++,
                              student_id: student.student_id,
                              student_name: student.student_name,
                              start_date: student.start_date,
                              end_date: student.end_date,
                              subjects: subjects,
                              amount: student.amount,
                              status: student.status ?? 'active'
                          });
                      });

                      showStudentAdd?.();
                      initializeStudentDropdown();
                      removeBrackets();
                      // vacations are not copied on clone
                      getVacation()
                  },
                  error: function() {
                    getVacation()
                  }
              });

              $.ajax({
                  type: 'get',
                  url: "{{ url('get-client-payments') }}",
                  data: {
                      id: cloned_client_id
                  },
                  success: function(res) {
                      payments_array = [];

                      res.forEach(payment => {
                          payments_array.push({
                              key: paymentKey++,
                              payment_date: payment.payment_date,
                              kumon_month: payment.kumon_month,
                              payment_type: payment.payment_type,
                              reference_no: payment.reference_no,
                              amount: payment.amount
                          });
                      });
                      showPaymentAdd?.();
                  }
              });

              function getVacation() {
                  $.ajax({
                      type: 'get',
                      url: "{{ url('get-client-vacation') }}",
                      data: {
                          id: cloned_client_id
                      },
                      success: function(res) {
                          vacation_array = [];

                          res.forEach(vacation => {
                                  vacation_array.push({
                                      key: Date.now(),
                                      student: vacation.student_name,
                                      subjects: vacation.subjects,
                                      date_range: vacation.date_range,
                                      take_work_home: vacation.take_work_home,
                                      reduced_workload: vacation.reduced_workload,
                                      planned: vacation.planned ?? 0,
                                      comment: vacation.comment
                                  });
                          });

                          showVacationAdd?.();
                      }
                  });
              }
              // Payments and vacations are not copied on clone
//   get comments and attachments
            $.ajax({

            type: 'get',

            'method': 'get',

            data: {

                id: '<?php echo $client->id; ?>'

            },

            url: "{{ url('get-comments-clients') }}",

            success: function(res) {

                for (var i = 0; i < res.length; i++) {

                    var date = res[i].date;

                    var newDate = new Date(date);

                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June",

                        "July", "Aug", "Sep", "Oct", "Nov", "Dec"

                    ];

                    var date1 = newDate.getFullYear() + '-' + monthNames[newDate.getMonth()] +

                        '-' +

                        newDate.getDate();

                    var time = newDate.toLocaleString('en-US', {

                        hour: date1.getHours,

                        minute: date1.getSeconds,

                        hour12: true

                    });



                    commentArray.push({

                        key: i,

                        comment: res[i].comment,

                        date: date1,

                        time: time.split(',')[1],

                        name: res[i].name,

                        image: res[i].user_image

                    });

                    comment_key_count = i;

                }

                showComment();



            }

        })
            $.ajax({

            type: 'get',

            'method': 'get',

            data: {

                id: '<?php echo $client->id; ?>'

            },

            url: "{{ url('get-attachment-clients') }}",

            success: function(res) {

                for (var i = 0; i < res.length; i++) {

                    var date = res[i].date;

                    var newDate = new Date(date);

                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "June",

                        "July", "Aug", "Sep", "Oct", "Nov", "Dec"

                    ];

                    var date1 = newDate.getFullYear() + '-' + monthNames[newDate.getMonth()] +

                        '-' +

                        newDate.getDate();

                    var time = newDate.toLocaleString('en-US', {

                        hour: date1.getHours,

                        minute: date1.getSeconds,

                        hour12: true

                    });



                    attachmentArray.push({

                        key: i,

                        attachment: res[i].attachment,

                        date: date1,

                        time: time.split(',')[1],

                        name: res[i].name,

                        image: res[i].user_image

                    });

                    attachment_key_count = i;

                }

                showAttachment();



            }

        });
            const $switch3 = $('#customSwitch3');

              function updateSwitch3State() {
                  if ($switch3.is(':checked')) {
                      $('.switch-text3').text('Access to manage student vacations enabled');

                  } else {
                      $('.switch-text3').text('Access to manage student vacations disabled');

                  }
              }

              updateSwitch3State();
            $switch3.on('change', updateSwitch3State);
          });
      </script>
