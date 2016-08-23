<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormidableSelectImageAdmin {
	protected $version;
	private $slug;
	private $gManager;

	public function __construct( $version, $slug, $gManager ) {
		$this->version  = $version;
		$this->slug     = $slug;
		$this->gManager = $gManager;
	}

	/**
	 * Add new field to formidable list of fields
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function addFormidableSelectImageField( $fields ) {
		$fields['selectimages'] = FormidableSelectImageManager::t( "Select Image" );

		return $fields;
	}

	/**
	 * Set the default options for the field
	 *
	 * @param $fieldData
	 *
	 * @return mixed
	 */
	public function setFormidableSelectImageOptions( $fieldData ) {
		if ( $fieldData['type'] == 'selectimages' ) {
			$fieldData['name'] = FormidableSelectImageManager::t( "Select Image" );
		}

		return $fieldData;
	}

	/**
	 * Set display option for the field
	 *
	 * @param $display
	 *
	 * @return mixed
	 */
	public function addFormidableSelectImageDisplayOptions( $display ) {
		if ( $display['type'] == 'selectimages' ) {
			$display['unique'] = true;
		}

		return $display;
	}

	/**
	 * Show the field placeholder in the admin area
	 *
	 * @param $field
	 */
	public function showFormidableSelectImageAdminField( $field ) {
		if ( $field['type'] != 'selectimages' ) {
			return;
		}
		?>

		<div class="frm_html_field_placeholder">
			<div class="frm_html_field"><?= FormidableSelectImageManager::t( "Show media library in front to select image" ) ?></div>
		</div>
	<?php
	}

	/**
	 * Include script in WP
	 */
	public function enqueue_FormidableSelectImage_js() {
		wp_enqueue_media();
		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Include css in WP
	 */
	public function enqueue_FormidableSelectImage_style() {
		wp_enqueue_style( 'jquery' );
		wp_enqueue_style(
			'formidable_select_image',
			FSIMAGE_CSS_PATH . 'formidable_select_image.css'
		);
	}

	/**
	 * Return html of image with micro size 50px
	 *
	 * @param $src
	 *
	 * @return string
	 */
	private function getMicroImage( $src ) {
		$result = '';
		if ( isset( $src ) && ! empty( $src ) ) {
			$result = wp_get_attachment_image( $src, array( 50, 50 ), true ) . " <a style='vertical-align: top;' target='_blank' href='" . $src . "'>" . FormidableSelectImageManager::t( "Full Image" ) . "</a>";
		}

		return $result;
	}

	/**
	 * Add the HTML for the field on the front end
	 *
	 * @param $field
	 * @param $field_name
	 */
	public function showFormidableSelectImageFrontField( $field, $field_name ) {
		if ( $field['type'] != 'selectimages' ) {
			return;
		}
		$field['value'] = stripslashes_deep( $field['value'] );
		$showContainer  = '';
		if ( empty( $field['value'] ) ) {
			$showContainer = 'style = "display:none;"';
		}
		$imageUrl         = wp_get_attachment_image_url( $field['value'] );
		$imageFullUrl     = wp_get_attachment_url( $field['value'] );
		$attachment_title = basename( get_attached_file( $field['value'] ) );
		?>
		<input id="field_<?= $field['field_key'] ?>" type="hidden" name="<?= $field_name ?>" class="file-upload-input" value="<?php echo esc_attr( $field['value'] ) ?>"/>
		<div class="frm_dropzone" id="image_container_<?= $field['field_key'] ?>">
			<div class="dz-preview dz-complete dz-image-preview">
				<div <?= $showContainer ?> id="image_thumbnail_container_<?= $field_name ?>" class="dz-image"><img id="image_thumbnail_<?= $field_name ?>" alt="<?= $attachment_title ?>" src="<?= $imageUrl ?>"></div>
				<div <?= $showContainer ?> id="image_link_container_<?= $field_name ?>" class="dz-details">
					<div class="dz-filename"><span data-dz-name=""><a id="image_link_<?= $field_name ?>" target="_blank" href="<?= $imageFullUrl ?>"><?= $attachment_title ?></a></span></div>
				</div>
				<div style="text-align: center; margin-top: 10px;">
					<input id="upload_button_<?= $field['field_key'] ?>" name="<?= $field_name ?>" type="button" class="btn btn-default" value="<?= FormidableSelectImageManager::t( "Select Image" ) ?>" style="width: auto !important;"/>
				</div>
			</div>

		</div>

		<script>
			jQuery(document).ready(function ($) {

				var mediaUploader;

				$('input[name="<?= $field_name ?>"][type="button"]').click(function (e) {
					e.preventDefault();
					// If the uploader object has already been created, reopen the dialog
					if (mediaUploader) {
						mediaUploader.open();
						return;
					}
					// Extend the wp.media object
					mediaUploader = wp.media.frames.file_frame = wp.media({
						title: 'Choose Image',
						button: {
							text: 'Choose Image'
						}, multiple: false
					});

					// When a file is selected, grab the URL and set it as the text field's value
					mediaUploader.on('select', function () {
						attachment = mediaUploader.state().get('selection').first().toJSON();
						$('input[name="<?= $field_name ?>"][type="hidden"]').val(attachment.id);
						$('[id="image_thumbnail_<?= $field_name ?>"]').attr('src', attachment.sizes.thumbnail.url);
						$('[id="image_thumbnail_<?= $field_name ?>"]').attr('alt', attachment.filename);
						$('[id="image_link_<?= $field_name ?>"]').attr('href', attachment.url);
						$('[id="image_link_<?= $field_name ?>"]').text(attachment.filename);
						$('[id="image_thumbnail_container_<?= $field_name ?>"]').show();
						$('[id="image_link_container_<?= $field_name ?>"]').show();
					});
					// Open the uploader dialog
					mediaUploader.open();
				});

			});
		</script>
	<?php
	}

	/**
	 * Add the HTML to display the field in the admin area
	 *
	 * @param $value
	 * @param $field
	 * @param $atts
	 *
	 * @return string
	 */
	public function displayFormidableSelectImageAdminField( $value, $field, $atts ) {
		if ( $field->type != 'selectimages' || empty( $value ) ) {
			return $value;
		}

		$value = $this->getMicroImage( $value );

		return $value;
	}

	/**
	 * Process shortCode with attr
	 *
	 * @param $value
	 * @param $tag
	 * @param $attr This be one of next: id, email, name, login
	 * @param $field
	 *
	 * @return string
	 */
	public function shortCodeFormidableSelectImageReplace( $value, $tag, $attr, $field ) {
		if ( $field->type != 'selectimages' || empty( $value ) ) {
			return $value;
		}

		$internal_attr = shortcode_atts( array(
			'output' => 'url',
			'size'   => 'thumbnail',
			'html'   => '0',
		), $attr );

		$result = wp_get_attachment_url( $value );
		if ( $internal_attr['output'] == 'img' ) {
			$result = wp_get_attachment_image( $value, $internal_attr['size'] );
		}

		if ( $internal_attr['html'] == '1' ) {
			$result = "<a style='vertical-align: top;' target='_blank'  href='" . wp_get_attachment_url( $value ) . "' >" . $result . "</a>";
		}

		return $result;
	}

	/**
	 * Add setting page to global formidable settings
	 *
	 * @param $sections
	 *
	 * @return mixed
	 */
	public function addFormidableSelectImageSettingPage( $sections ) {
		$sections['selectimages'] = array(
			'class'    => 'FormidableSelectImageSettings',
			'function' => 'route',
		);

		return $sections;
	}

	/**
	 * Add a "Settings" link to the plugin row in the "Plugins" page.
	 *
	 * @param $links
	 * @param string $pluginFile
	 *
	 * @return array
	 * @internal param array $pluginMeta Array of meta links.
	 */
	public function addFormidableSelectImageSettingLink( $links, $pluginFile ) {
		if ( $pluginFile == 'formidable_select_image/formidable_select_image.php' ) {
			$link = sprintf( '<a href="%s">%s</a>', esc_attr( admin_url( 'admin.php?page=formidable-settings&t=selectimages_settings' ) ), FormidableSelectImageManager::t( "Settings" ) );
			array_unshift( $links, $link );
		}

		return $links;
	}


}