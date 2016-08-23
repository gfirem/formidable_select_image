<?php

class FormidableSelectImageSettings {

	public static function route() {

		$action = isset( $_REQUEST['frm_action'] ) ? 'frm_action' : 'action';
		$action = FrmAppHelper::get_param( $action );
		if ( $action == 'process-form' ) {
			return self::process_form();
		} else {
			return self::display_form();
		}
	}

	/**
	 * @internal var gManager GManager_1_0
	 */
	public static function display_form( ) {
		$gManager = GManagerFactory::buildManager('FormidableSelectImageManager', 'formidable_select_image', FormidableSelectImageManager::getShort());
		$key  = get_option( FormidableSelectImageManager::getShort() . 'licence_key' );
		?>
		<h3 class="frm_first_h3"><?= FormidableSelectImageManager::t( "Licence Data for Select Image Field" ) ?></h3>
		<table class="form-table">
			<tr>
				<td width="150px"><?= FormidableSelectImageManager::t( "Version: " ) ?></td>
				<td>
					<span><?= FormidableSelectImageManager::getVersion() ?></span>
				</td>
			</tr>
			<tr class="form-field" valign="top">
				<td width="150px">
					<label for="key"><?= FormidableSelectImageManager::t( "Order Key: " ) ?></label>
					<span class="frm_help frm_icon_font frm_tooltip_icon" title="" data-original-title="<?= FormidableSelectImageManager::t( "Order key send to you with order confirmation, to get updates." ) ?>"></span>
				</td>
				<td><input type="text" name="<?= FormidableSelectImageManager::getShort() ?>_key" id="<?= FormidableSelectImageManager::getShort() ?>_key" value="<?= $key ?>"/></td>
			</tr>
			<tr class="form-field" valign="top">
				<td width="150px"><?= FormidableSelectImageManager::t( "Key status: " ) ?></label></td>
				<td><?= $gManager->getStatus() ?></td>
			</tr>
		</table>
	<?php
	}

	public static function process_form() {
		if ( isset( $_POST[FormidableSelectImageManager::getShort() . '_key'] ) && ! empty( $_POST[FormidableSelectImageManager::getShort() . '_key'] ) ) {
			$gManager = GManagerFactory::buildManager('FormidableSelectImageManager', 'formidable_select_image', FormidableSelectImageManager::getShort());
			$gManager->activate($_POST[FormidableSelectImageManager::getShort() . '_key']);
			update_option( FormidableSelectImageManager::getShort() . 'licence_key', $_POST[FormidableSelectImageManager::getShort() . '_key'] );
		}
		self::display_form();
	}
}