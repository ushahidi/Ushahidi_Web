<?php
/**
 * API settings view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
            <div class="bg">
                <h2>
                    <?php admin::settings_subtabs("api"); ?>
                </h2>
                <!-- tabs -->
                <div class="tabs">
                    <!-- tabset -->
                    <ul class="tabset">
                        <li><a href="<?php echo url::site() . 'admin/settings/api' ?>" class="active"><?php echo Kohana::lang('ui_admin.api_settings'); ?></a></li>
                        <li><a href="<?php echo url::site() . 'admin/settings/api/log' ?>"><?php echo Kohana::lang('ui_admin.api_logs');?></a></li>
                        <li><a href="<?php echo url::site() . 'admin/settings/api/banned'?>"><?php echo Kohana::lang('ui_admin.api_banned'); ?></a></li>
                    </ul>
                    <!-- /tabset -->
                    
                    <!-- tab -->
                    <div class="tab">
                        <ul>
                            <li><a href="#" onclick="apiSettingsAction('s', 'SAVE');"><?php echo utf8::strtoupper(Kohana::lang('ui_admin.save_settings')); ?></a></li>
                            <li><a href="#" onclick-"apiSettingsAction('c', 'CANCEL');"><?php echo utf8::strtoupper(Kohana::lang('ui_admin.cancel')); ?></a></li>
                        </ul>
                    </div>
                    <!-- /tab -->
                </div>
                <!-- /tabs -->
                
                <?php print form::open(NULL, array('id'=>'apiSettingsMain', 'name'=>'apiSettingsMain')); ?>
                    <input type="hidden" name="action" id="action" value="" />
                <div class="report-form">
                                        
                    <?php if ($form_error): ?>
                    <!-- red box-->
                    <div class="red-box">
                        <h3><?php echo Kohana::lang('ui_main.error'); ?></h3>
                        <ul>
                            <?php 
                                foreach ($errors as $error_item=>$error_description)
                                {
                                    print (!$error_description)? '' : "<li>".$error_description."</li>";
                                }
                            ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($form_saved): ?>
                        <!-- green box -->
                        <div class="green-box">
                            <h3><?php echo Kohana::lang('ui_main.configuration_saved'); ?></h3>
                        </div>
                    <?php endif; ?>
                    
                    <!--column-->
                    <div class="sms_holder">
                        <div class="row">
                            <h4>
                                <a href="#" class="tooltip" title="<?php echo Kohana::lang('tooltips.settings_api_default_record_limit'); ?>">
                                    <?php echo Kohana::lang('settings.api.default_record_limit'); ?>
                                </a>
                            </h4>
                            <?php print form::input('api_default_record_limit', $form['api_default_record_limit'], ' class="text"'); ?>
                        </div>
                        <div class="row">
                            <h4>
                                <a href="#" class="tooltip" title="<?php echo Kohana::lang('tooltips.settings_api_max_record_limit'); ?>">
                                    <?php echo Kohana::lang('settings.api.maximum_record_limit'); ?>
                                </a>
                            </h4>
                            <?php print form::input('api_max_record_limit', $form['api_max_record_limit'], ' class="text"'); ?>
                        </div>
                        <div class="row">
                            <h4>
                                <a href="#" class="tooltip" title="<?php echo Kohana::lang('tooltips.settings_api_max_requests_per_ip'); ?>">
                                    <?php echo Kohana::lang('settings.api.maximum_requests_per_ip_address'); ?>
                                </a>
                            </h4>
                            
                            <?php print form::input('api_max_requests_per_ip_address', $form['api_max_requests_per_ip_address'], ' class="text"'); ?>
                            <strong> <?php echo Kohana::lang('ui_main.per'); ?> </strong>
                            <?php print form::dropdown('api_max_requests_quota_basis', $max_requests_quota_array, $form['api_max_requests_quota_basis']); ?>
                        </div>
                    </div>                    
                </div>
                <?php print form::close(); ?>
            </div>