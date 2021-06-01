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

class Jet_Engine_Extend_Form_Actions {

	private $base_mask = 'advanced_date::';

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
					$fields = explode( ',', str_replace( ', ', ',', $fields ) );
					$query['meta_query'][] = $this->get_advanced_query( $fields, $meta_query['value'], $type );
				}

			}
		}

		return $query;

	}

	public function get_advanced_query( $fields, $values, $type ) {

		$inside = false;

		if ( 'each' === $type ) {
			$relation = 'AND';
		} elseif ( 'inside' === $type ) {
			$inside = true;
			$relation = 'OR';
		} else {
			$relation = 'OR';
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

		return $result;

	}

}

new Jet_Engine_Extend_Form_Actions();
