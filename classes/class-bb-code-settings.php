<?php

final class BB_Code_Settings {

	public static function init() {
		add_action( 'plugins_loaded', __CLASS__ . '::setup_hooks' );
	}

	public static function setup_hooks() {
		if ( ! class_exists( 'FLBuilder' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', __CLASS__ . '::enqueue_builder_scripts' );

		add_filter( 'fl_builder_register_settings_form', __CLASS__ . '::filter_settings_fields', 10, 2 );
		add_filter( 'fl_builder_render_css', __CLASS__ . '::filter_layout_css', 10, 2 );
		add_filter( 'fl_builder_render_js', __CLASS__ . '::filter_layout_js', 10, 2 );
		add_filter( 'fl_builder_ajax_layout_response', __CLASS__ . '::filter_ajax_layout_js' );
	}

	public static function enqueue_builder_scripts() {
		if ( FLBuilderModel::is_builder_active() ) {
			wp_enqueue_style( 'bb-code-settings', BB_CODE_SETTINGS_URL . 'css/settings.css', array(), BB_CODE_SETTINGS_VERSION );
			wp_enqueue_script( 'bb-code-settings', BB_CODE_SETTINGS_URL . 'js/settings.js', array(), BB_CODE_SETTINGS_VERSION );
		}
	}

	public static function filter_settings_fields( $form, $slug ) {
		if ( 'row' === $slug || 'col' === $slug ) {
			$form['tabs']['advanced']['sections']['bb_css_code'] = self::get_css_field_config();
			$form['tabs']['advanced']['sections']['bb_js_code'] = self::get_js_field_config();
		}
		if ( 'module_advanced' === $slug ) {
			$form['sections']['bb_css_code'] = self::get_css_field_config();
			$form['sections']['bb_js_code'] = self::get_js_field_config();
		}
		return $form;
	}

	public static function get_css_field_config() {
		return array(
			'title'  => __( 'CSS' ),
			'fields' => array(
				'bb_css_code' => array(
					'label'       => '',
					'type'        => 'code',
					'editor'      => 'css',
					'rows'        => '18',
					'preview'     => array(
						'type' => 'none',
					),
				),
			),
		);
	}

	public static function get_js_field_config() {
		return array(
			'title'  => __( 'JavaScript' ),
			'fields' => array(
				'bb_js_code' => array(
					'label'       => '',
					'type'        => 'code',
					'editor'      => 'javascript',
					'rows'        => '18',
					'preview'     => array(
						'type' => 'none',
					),
				),
			),
		);
	}

	public static function filter_layout_css( $css, $nodes ) {
		$all_nodes = array_merge( $nodes['rows'], $nodes['columns'],  $nodes['modules'] );

		foreach ( $all_nodes as $node_id => $node ) {
			if ( isset( $node->settings ) ) {
				if ( isset( $node->settings->bb_css_code ) && ! empty( $node->settings->bb_css_code ) ) {
					$code = ".fl-node-$node_id {";
					$code .= $node->settings->bb_css_code;
					$code .= "}";
					$compiler = new ScssPhp\ScssPhp\Compiler();
					$css .= $compiler->compile( $code );
				}
			}
		}

		return $css;
	}

	public static function filter_layout_js( $js, $nodes ) {
		$all_nodes = array_merge( $nodes['rows'], $nodes['columns'],  $nodes['modules'] );
		foreach ( $all_nodes as $node ) {
			$js .= self::get_node_js( $node );
		}
		return $js;
	}

	public static function filter_ajax_layout_js( $response ) {
		if ( $response['partial'] ) {
			$node = FLBuilderModel::get_node( $response['nodeId'] );
			$response['js'] .= self::get_node_js( $node );
		}
		return $response;
	}

	public static function get_node_js( $node ) {
		if ( isset( $node->settings ) ) {
			if ( isset( $node->settings->bb_js_code ) && ! empty( $node->settings->bb_js_code ) ) {
				return $node->settings->bb_js_code;
			}
		}
		return '';
	}
}
