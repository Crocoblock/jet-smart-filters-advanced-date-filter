<?php
/**
 * Plugin Name: JetSmartFilters - Advanced date filter
 * Plugin URI:  https://crocoblock.com/
 * Description: Allow to filter by multiple meta fields with date range filters
 * Version:     1.0.0
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Jet_Smart_Filters_Advanced_Date_Filter {

	private $base_mask = 'advanced_date::';
	private $custom_query = null;

	public function __construct() {
		add_filter( 'jet-smart-filters/query/final-query', array( $this, 'apply_dates_filter' ), -999 );
	}

	/**
	 * Check if your variable is presented in the query. If yes - unset it and add advanced query parameters to filter by
	 */
	public function apply_dates_filter( $query ) {

		if ( empty( $query['meta_query'] ) ) {
			return $query;
		}

		foreach ( $query['meta_query'] as $index => $meta_query ) {

			if ( false !== strpos( $meta_query['key'], $this->base_mask ) ) {

				$from = $meta_query['value'][0];
				$to   = $meta_query['value'][1];

				$data = explode( '::', $meta_query['key'] );

				$type   = ! empty( $data[1] ) ? $data[1] : false;
				$fields = ! empty( $data[2] ) ? $data[2] : false;

				if ( $type && $fields ) {

					unset( $query['meta_query'][ $index ] );
					$fields = explode( ';', str_replace( '; ', ';', $fields ) );
					$advanced_query = $this->get_advanced_query( $fields, $meta_query['value'], $type );

					if ( $this->custom_query ) {

						global $wpdb;

						$table   = $wpdb->postmeta;
						$field_1 = $fields[0];
						$field_2 = $fields[1];
						$value_1 = $meta_query['value'][0];
						$value_2 = $meta_query['value'][1];

						$post_ids_query = "SELECT pm1.post_id FROM `$table` AS pm1 INNER JOIN `$table` AS pm2 ON ( pm1.post_id = pm2.post_id ) WHERE ( ( ( pm1.meta_key = '$field_1' AND pm1.meta_value = '' ) AND ( pm2.meta_key = '$field_2' AND CAST( pm2.meta_value AS SIGNED ) BETWEEN $value_1 AND $value_2 ) ) OR ( ( pm1.meta_key = '$field_1' AND CAST( pm1.meta_value AS SIGNED ) BETWEEN $value_1 AND $value_2 ) AND ( pm2.meta_key = '$field_2' AND pm2.meta_value = '' ) ) OR ( ( pm1.meta_key = '$field_2' AND CAST( pm1.meta_value AS SIGNED ) BETWEEN $value_1 AND $value_2 ) AND ( pm2.meta_key = '$field_2' AND CAST( pm2.meta_value AS SIGNED ) BETWEEN $value_1 AND $value_2 ) ) OR ( ( pm1.meta_key = '$field_1' AND pm1.meta_value = '' ) AND ( pm2.meta_key = '$field_2' AND pm2.meta_value = '' ) ) )";

						$post_ids = $wpdb->get_results( $post_ids_query );

						if ( empty( $post_ids ) ) {
							$query['post__in'] = 'not_found';
						} else {

							$ids = array();

							foreach ( $post_ids as $id ) {
								$ids[] = $id->post_id;
							}

							$query['post__in'] = $ids;
						}

					} else {
						$query['meta_query'][] = $advanced_query;
					}

				}

			}
		}

		return $query;

	}

	public function get_advanced_query( $fields, $values, $type ) {

		$inside = false;
		$add_custom = false;
		
		// pre-process case 'fields_inside' - when both post fields inside the range
		if ( 'fields_inside' === $type ) {
			return array(
				'realtion' => 'AND',
				array(
					'key'     => $fields[0],
					'value'   => $values[0],
					'type'    => 'NUMERIC',
					'compare' => '>=',
				),
				array(
					'key'     => $fields[1],
					'value'   => $values[1],
					'type'    => 'NUMERIC',
					'compare' => '<=',
				),
			);
		}

		switch ( $type ) {

			case 'each':
				$relation = 'AND';
				break;

			case 'each_empty':
				$add_custom = true;
				$relation = 'AND';
				break;

			case 'inside':
				$inside = true;
				$relation = 'OR';
				break;

			default:
				$relation = 'OR';
				break;
		}

		$result = array(
			'relation' => $relation,
		);

		foreach ( $fields as $field ) {
			$result[] = array(
				'key'     => $field,
				'value'   => $values,
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			);
		}

		if ( $inside ) {
			$result[] = array(
				'realtion' => 'AND',
				array(
					'key'     => $fields[0],
					'value'   => $values[0],
					'type'    => 'NUMERIC',
					'compare' => '<=',
				),
				array(
					'key'     => $fields[1],
					'value'   => $values[1],
					'type'    => 'NUMERIC',
					'compare' => '>=',
				),
			);
		}

		if ( $add_custom ) {
			$this->custom_query = true;
		}

		return $result;

	}

}

new Jet_Smart_Filters_Advanced_Date_Filter();
