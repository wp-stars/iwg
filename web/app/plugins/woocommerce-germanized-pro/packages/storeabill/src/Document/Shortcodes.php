<?php

namespace Vendidero\StoreaBill\Document;

use Vendidero\StoreaBill\DataStores\DocumentItem;
use Vendidero\StoreaBill\Document\Document;
use Vendidero\StoreaBill\Interfaces\Customer;
use Vendidero\StoreaBill\Interfaces\Reference;
use Vendidero\StoreaBill\Interfaces\ShortcodeHandleable;
use Vendidero\StoreaBill\Document\Total;
use Vendidero\StoreaBill\Package;
use Vendidero\StoreaBill\References\Product;

defined( 'ABSPATH' ) || exit;

class Shortcodes implements ShortcodeHandleable {

	public function setup() {
		foreach ( $this->get_shortcodes() as $shortcode_tag => $callback ) {
			add_shortcode( $shortcode_tag, $callback );
		}
	}

	public function destroy() {
		foreach ( $this->get_shortcodes() as $shortcode_tag => $callback ) {
			remove_shortcode( $shortcode_tag );
		}
	}

	public function get_shortcodes() {
		$shortcodes = array(
			'document'                        => array( $this, 'document_data' ),
			'customer'                        => array( $this, 'customer_data' ),
			'document_parent'                 => array( $this, 'document_parent_data' ),
			'document_reference'              => array( $this, 'document_reference_data' ),
			'document_item'                   => array( $this, 'document_item_data' ),
			'document_item_reference'         => array( $this, 'document_item_reference_data' ),
			'document_item_product'           => array( $this, 'document_item_product_data' ),
			'document_item_product_parent'    => array( $this, 'document_item_product_parent_data' ),
			'document_total'                  => array( $this, 'document_total_data' ),
			'setting'                         => array( $this, 'setting_data' ),
			'if_document'                     => array( $this, 'if_document_data' ),
			'if_customer'                     => array( $this, 'if_customer_data' ),
			'if_document_reference'           => array( $this, 'if_document_reference_data' ),
			'if_document_item'                => array( $this, 'if_document_item_data' ),
			'if_document_item_reference'      => array( $this, 'if_document_item_reference_data' ),
			'if_document_total'               => array( $this, 'if_document_total_data' ),
			'if_document_item_product'        => array( $this, 'if_document_item_product_data' ),
			'if_document_item_product_parent' => array( $this, 'if_document_item_product_parent_data' ),
		);

		return apply_filters( 'storeabill_document_shortcodes', $shortcodes, $this );
	}

	/**
	 * @return bool|Document
	 */
	public function get_document() {
		$document = isset( $GLOBALS['document'] ) ? $GLOBALS['document'] : false;

		if ( $document && is_a( $document, '\Vendidero\StoreaBill\Document\Document' ) ) {
			return $document;
		}

		return false;
	}

	/**
	 * @return bool|Customer
	 */
	public function get_customer() {
		if ( $document = $this->get_document() ) {
			return $document->get_customer();
		}

		return false;
	}

	/**
	 * @return bool|Document
	 */
	public function get_document_parent() {
		$document = $this->get_document();

		if ( $document && is_callable( array( $document, 'get_parent' ) ) ) {
			return $document->get_parent();
		}

		return false;
	}

	/**
	 * @return bool|Reference
	 */
	public function get_document_reference() {
		$document = $this->get_document();

		if ( $document ) {
			return $document->get_reference();
		}

		return false;
	}

	/**
	 * @return bool|Item
	 */
	public function get_document_item() {
		$item = isset( $GLOBALS['document_item'] ) ? $GLOBALS['document_item'] : false;

		if ( $item && is_a( $item, '\Vendidero\StoreaBill\Document\Item' ) ) {
			return $item;
		}

		return false;
	}

	/**
	 * @return bool|Reference
	 */
	public function get_document_item_reference() {
		if ( $item = $this->get_document_item() ) {
			return $item->get_reference();
		}

		return false;
	}

	/**
	 * @return bool|Total
	 */
	public function get_document_total() {
		$document_total = isset( $GLOBALS['document_total'] ) ? $GLOBALS['document_total'] : false;

		if ( $document_total && is_a( $document_total, '\Vendidero\StoreaBill\Document\Total' ) ) {
			return $document_total;
		}

		return false;
	}

	/**
	 * @return bool|\Vendidero\StoreaBill\Interfaces\Product
	 */
	public function get_document_item_product() {
		$item = $this->get_document_item();

		if ( $item && is_callable( array( $item, 'get_product' ) ) ) {
			return $item->get_product();
		}

		return false;
	}

	/**
	 * @return bool|Product
	 */
	public function get_document_item_product_parent() {
		if ( $product = $this->get_document_item_product() ) {
			/**
			 * Force parent in case exists.
			 */
			if ( $parent = $product->get_parent() ) {
				return $parent;
			} else {
				return $product;
			}
		}

		return false;
	}

	public function is_editor_preview( $atts ) {
		$atts = wp_parse_args(
			$atts,
			array(
				'is_editor_preview' => 'no',
			)
		);

		if ( 'yes' === $atts['is_editor_preview'] ) {
			return true;
		}

		return false;
	}

	public function supports( $document_type ) {
		return true;
	}

	/**
	 * @param $atts
	 * @param Total $document_total
	 *
	 * @return mixed|void
	 */
	public function get_document_total_data( $atts, $document_total ) {
		$atts   = $this->parse_args( $atts );
		$result = '';

		if ( $document_total ) {
			$placeholders = $document_total->get_placeholders();

			if ( ! empty( $atts['data'] ) ) {
				$getter = 'get_' . $atts['data'];

				if ( is_callable( array( $document_total, $getter ) ) ) {
					$result = $document_total->$getter();
				} elseif ( array_key_exists( '{' . $atts['data'] . '}', $placeholders ) ) {
					$result = $placeholders[ '{' . $atts['data'] . '}' ];
				}
			}
		}

		return apply_filters( 'storeabill_shortcode_get_document_total_data', $result, $atts, $document_total, $this );
	}

	public function document_total_data( $atts ) {
		$atts   = $this->parse_args( $atts );
		$result = '';

		if ( $document_total = $this->get_document_total() ) {
			$result = $this->get_document_total_data( $atts, $document_total );
		}

		return apply_filters( 'storeabill_document_total_data_shortcode_result', $this->format_result( $result, $atts, $this->get_document_total() ), $atts, $document_total, $this );
	}

	public function parse_args( $args, $defaults = array() ) {
		if ( empty( $defaults ) ) {
			$defaults = array(
				'data'   => '',
				'format' => '',
				'args'   => '',
			);
		}

		$args = wp_parse_args( $args, $defaults );

		if ( ! is_array( $args['args'] ) ) {
			$args['args'] = explode( ',', $args['args'] );
			$args['args'] = array_map( 'trim', $args['args'] );
			$args['args'] = array_filter( $args['args'] );

			foreach ( $args['args'] as $key => $arg ) {
				if ( is_string( $arg ) && 'false' === $arg ) {
					$args['args'][ $key ] = false;
				} elseif ( is_string( $arg ) && 'true' === $arg ) {
					$args['args'][ $key ] = true;
				}
			}
		}

		return $args;
	}

	public function parse_comparison_args( $args, $defaults = array() ) {
		if ( empty( $defaults ) ) {
			$defaults = array(
				'data'    => '',
				// In case no value has been transmitted, assume a not empty check.
				'compare' => isset( $args['value'] ) ? 'e' : 'nempty',
				'value'   => '',
				'chain'   => 'OR',
				'format'  => '',
				'args'    => '',
			);
		}

		$args = $this->parse_args( $args, $defaults );

		if ( ! is_array( $args['compare'] ) ) {
			$args['compare'] = array_map( 'trim', explode( '|', $args['compare'] ) );
		}

		if ( strstr( $args['value'], '|' ) ) {
			$args['value'] = array_map( 'trim', explode( '|', $args['value'] ) );
		}

		return $args;
	}

	/**
	 * @param $atts
	 * @param Document $document
	 *
	 * @return mixed
	 */
	protected function get_document_data( $atts, $document ) {
		$atts = $this->parse_args( $atts );

		/**
		 * Map document properties to short names.
		 */
		$data_mappings = array(
			'date' => 'date_created',
		);

		if ( array_key_exists( $atts['data'], $data_mappings ) ) {
			$atts['data'] = $data_mappings[ $atts['data'] ];
		}

		$result = '';

		if ( 'current_page_no' === $atts['data'] ) {
			$result = $this->get_current_page_no( $atts );
		} elseif ( 'total_pages' === $atts['data'] ) {
			$result = $this->get_total_pages( $atts );
		} elseif ( $document ) {
			if ( ! empty( $atts['data'] ) ) {
				$result = $this->get_object_data( $document, $atts['data'], $atts['args'] );
			}
		}

		return apply_filters( 'storeabill_shortcode_get_document_data', $result, $atts, $document, $this );
	}

	/**
	 * @param $atts
	 * @param Item $document_item
	 *
	 * @return mixed|void
	 */
	public function get_document_item_data( $atts, $document_item ) {
		$atts   = $this->parse_args( $atts );
		$result = '';

		if ( $document_item ) {
			if ( ! empty( $atts['data'] ) ) {
				$result = $this->get_object_data( $document_item, $atts['data'], $atts['args'] );
			}
		}

		return apply_filters( 'storeabill_shortcode_get_document_item_data', $result, $atts, $document_item, $this );
	}

	public function document_item_data( $atts ) {
		$atts = $this->parse_args( $atts );
		$data = '';

		if ( $item = $this->get_document_item() ) {
			$data = $this->get_document_item_data( $atts, $item );
		}

		return apply_filters( 'storeabill_document_item_data_shortcode_result', $this->format_result( $data, $atts, $this->get_document_item() ), $atts, $item, $this );
	}

	public function get_method_return( $object, $getter, $args = array() ) {
		$result = '';

		try {
			if ( is_a( $object, '\Vendidero\StoreaBill\Interfaces\Reference' ) ) {
				if ( ! method_exists( $object, $getter ) ) {
					$object = $object->get_object();
				}
			}

			$ref         = new \ReflectionMethod( $object, $getter );
			$param_count = $ref->getNumberOfParameters();
			$args        = array_slice( $args, 0, $param_count );

			if ( $ref->isPublic() ) {
				if ( $param_count > 0 && ! empty( $args ) ) {
					$result = call_user_func_array( array( $object, $getter ), $args );
				} else {
					$result = $object->$getter();
				}
			}
		} catch ( \Exception $e ) {
			$result = '';
		}

		return $result;
	}

	/**
	 * Gets
	 *
	 * @param Reference|Document|DocumentItem $object
	 */
	protected function get_object_data( $object, $field_key, $args = array() ) {
		$field_key_parts      = explode( '#', $field_key );
		$field_key            = $field_key_parts[0];
		$inner_field_keys     = count( $field_key_parts ) > 1 ? array_slice( $field_key_parts, 1 ) : array();
		$getter               = "get_{$field_key}";
		$fallback_getter      = $field_key;
		$result               = '';
		$is_callable          = false;
		$is_fallback_callable = false;

		if ( is_a( $object, '\Vendidero\StoreaBill\Interfaces\Reference' ) ) {
			$is_callable          = $object->is_callable( $getter );
			$is_fallback_callable = $object->is_callable( $fallback_getter );
		} else {
			$is_callable          = is_callable( array( $object, $getter ) );
			$is_fallback_callable = is_callable( array( $object, $fallback_getter ) );
		}

		if ( $is_callable ) {
			$result = $this->get_method_return( $object, $getter, $args );
		} elseif ( $is_fallback_callable ) {
			$result = $this->get_method_return( $object, $fallback_getter, $args );
		} else {
			$result          = $object->get_meta( $field_key );
			$original_result = $result;

			if ( empty( $result ) ) {
				$prefixed_meta_result = $object->get_meta( '_' . $field_key );

				if ( ! empty( $prefixed_meta_result ) ) {
					$result = $prefixed_meta_result;

					/**
					 * Do only override empty main meta results with hidden meta fields if it is not an ACF field key
					 * which consists of a value like field_6343bbd.
					 */
					if ( is_string( $prefixed_meta_result ) && 'field_' === substr( $prefixed_meta_result, 0, 6 ) ) {
						$result = $original_result;
					}
				}
			}
		}

		/**
		 * Support inner field keys, e.g. [xy data="my_object#field1#field2"]
		 */
		if ( ! empty( $inner_field_keys ) ) {
			foreach ( $inner_field_keys as $inner_field_key ) {
				if ( is_array( $result ) ) {
					$result = array_key_exists( $inner_field_key, $result ) ? $result[ $inner_field_key ] : $result;
				} elseif ( is_object( $result ) ) {
					$result = $this->get_object_data( $result, $inner_field_key, $args );
				}
			}
		}

		/**
		 * Fallback to product parent data in case result is empty string.
		 */
		if ( '' === $result && is_a( $object, '\Vendidero\StoreaBill\Interfaces\Product' ) ) {
			if ( $parent = $object->get_parent() ) {
				return $this->get_object_data( $parent, $field_key, $args );
			}
		}

		return apply_filters( 'storeabill_shortcode_get_object_data', $result, $object, $field_key, $args );
	}

	/**
	 * @param $atts
	 * @param Customer $customer
	 *
	 * @return mixed
	 */
	protected function get_customer_data( $atts, $customer ) {
		$atts   = $this->parse_args( $atts );
		$result = '';

		if ( $customer ) {
			if ( ! empty( $atts['data'] ) ) {
				$result = $this->get_object_data( $customer, $atts['data'], $atts['args'] );
			}
		}

		return apply_filters( 'storeabill_shortcode_get_customer_data', $result, $atts, $customer, $this );
	}

	/**
	 * @param $atts
	 * @param Reference $reference
	 *
	 * @return mixed
	 */
	protected function get_document_reference_data( $atts, $reference ) {
		$atts   = $this->parse_args( $atts );
		$result = '';

		if ( $reference && is_a( $reference, '\Vendidero\StoreaBill\Interfaces\Reference' ) ) {
			if ( ! empty( $atts['data'] ) ) {
				$result = $this->get_object_data( $reference, $atts['data'], $atts['args'] );
			}
		}

		return apply_filters( 'storeabill_shortcode_get_document_reference_data', $result, $atts, $reference, $this );
	}

	public function document_data( $atts ) {
		$atts = $this->parse_args( $atts );

		return apply_filters( 'storeabill_document_data_shortcode_result', $this->format_result( $this->get_document_data( $atts, $this->get_document() ), $atts, $this->get_document() ), $atts, $this->get_document(), $this );
	}

	public function setting_data( $atts ) {
		$atts           = $this->parse_args( $atts );
		$setting_result = Package::get_setting( $atts['data'] );

		return apply_filters( 'storeabill_setting_data_shortcode_result', $this->format_result( $setting_result, $atts, $this->get_document() ), $atts, $this->get_document(), $this );
	}

	public function customer_data( $atts ) {
		$atts = $this->parse_args( $atts );

		return apply_filters( 'storeabill_customer_data_shortcode_result', $this->format_result( $this->get_customer_data( $atts, $this->get_customer() ), $atts, $this->get_customer() ), $atts, $this->get_customer(), $this );
	}

	public function document_reference_data( $atts ) {
		$atts = $this->parse_args( $atts );

		return apply_filters( 'storeabill_document_reference_data_shortcode_result', $this->format_result( $this->get_document_reference_data( $atts, $this->get_document_reference() ), $atts, $this->get_document_reference() ), $atts, $this->get_document(), $this );
	}

	/**
	 * @param $atts
	 * @param Reference $reference
	 *
	 * @return string
	 */
	protected function get_document_item_reference_data( $atts, $reference ) {
		$atts   = $this->parse_args( $atts );
		$result = '';

		if ( $reference && is_a( $reference, '\Vendidero\StoreaBill\Interfaces\Reference' ) ) {
			if ( ! empty( $atts['data'] ) ) {
				$result = $this->get_object_data( $reference, $atts['data'], $atts['args'] );
			}
		}

		return apply_filters( 'storeabill_shortcode_get_document_item_reference_data', $result, $atts, $reference, $this );
	}

	/**
	 * @param $atts
	 * @param \Vendidero\StoreaBill\Interfaces\Product $product
	 *
	 * @return string
	 */
	protected function get_document_item_product_data( $atts, $product ) {
		$atts   = $this->parse_args( $atts );
		$result = '';

		if ( $product && is_a( $product, '\Vendidero\StoreaBill\Interfaces\Product' ) ) {
			if ( ! empty( $atts['data'] ) ) {
				$result = $this->get_object_data( $product, $atts['data'], $atts['args'] );
			}
		}

		return apply_filters( 'storeabill_shortcode_get_item_product_data', $result, $atts, $product, $this );
	}

	/**
	 * @param $atts
	 * @param Product $product
	 *
	 * @return string
	 */
	protected function get_document_item_product_parent_data( $atts, $product ) {
		$atts   = $this->parse_args( $atts );
		$result = '';

		if ( $product && is_a( $product, '\Vendidero\StoreaBill\Interfaces\Product' ) ) {
			if ( ! empty( $atts['data'] ) ) {
				$result = $this->get_object_data( $product, $atts['data'], $atts['args'] );
			}
		}

		return apply_filters( 'storeabill_shortcode_get_item_product_parent_data', $result, $atts, $product, $this );
	}

	public function document_item_reference_data( $atts ) {
		$atts = $this->parse_args( $atts );

		return apply_filters( 'storeabill_document_item_reference_data_shortcode_result', $this->format_result( $this->get_document_item_reference_data( $atts, $this->get_document_item_reference() ), $atts, $this->get_document_item_reference() ), $atts, $this->get_document(), $this );
	}

	public function document_item_product_data( $atts ) {
		$atts = $this->parse_args( $atts );

		return apply_filters( 'storeabill_document_item_product_data_shortcode_result', $this->format_result( $this->get_document_item_product_data( $atts, $this->get_document_item_product() ), $atts, $this->get_document_item_product() ), $atts, $this->get_document(), $this );
	}

	public function document_item_product_parent_data( $atts ) {
		$atts = $this->parse_args( $atts );

		return apply_filters( 'storeabill_document_item_product_parent_data_shortcode_result', $this->format_result( $this->get_document_item_product_parent_data( $atts, $this->get_document_item_product_parent() ), $atts, $this->get_document_item_product_parent() ), $atts, $this->get_document(), $this );
	}

	public function document_parent_data( $atts ) {
		$atts = $this->parse_args( $atts );
		$data = '';

		if ( $parent = $this->get_document_parent() ) {
			$data = $this->get_document_data( $atts, $parent );
		}

		return apply_filters( 'storeabill_document_parent_data_shortcode_result', $this->format_result( $data, $atts, $this->get_document_parent() ), $atts, $this->get_document(), $this );
	}

	protected function compare( $types, $data, $comparison, $compare = 'OR' ) {
		if ( is_a( $data, 'WC_DateTime' ) ) {
			$data = $data->getOffsetTimestamp();
		}

		$show = false;

		if ( 'AND' === $compare ) {
			$show = true;
		}

		if ( is_array( $data ) && ! is_array( $comparison ) ) {
			$comparison = array( $comparison );
		} elseif ( is_array( $comparison ) && ! is_array( $data ) ) {
			$data = array( $data );
		}

		foreach ( $types as $c ) {
			$inner_show = false;

			if ( 'e' === $c || 'equals' === $c || '==' === $c ) {
				$inner_show = $data == $comparison; // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			} elseif ( 'ne' === $c || 'nequals' === $c || '!=' === $c ) {
				$inner_show = $data != $comparison; // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			} elseif ( 'gt' === $c || 'greater' === $c || '>' === $c ) {
				$inner_show = $data > $comparison;
			} elseif ( 'gte' === $c || '>=' === $c ) {
				$inner_show = $data >= $comparison;
			} elseif ( 'lt' === $c || 'lesser' === $c || '<' === $c ) {
				$inner_show = $data < $comparison;
			} elseif ( 'lte' === $c || '<=' === $c ) {
				$inner_show = $data <= $comparison;
			} elseif ( 'empty' === $c ) {
				$inner_show = empty( $data ) ? true : false;
			} elseif ( 'nempty' === $c ) {
				$inner_show = ! empty( $data ) ? true : false;
			} elseif ( 'true' === $c ) {
				$inner_show = true === $data;
			} elseif ( 'false' === $c ) {
				$inner_show = false === $data;
			} elseif ( 'in' === $c ) {
				$comparison = (array) $comparison;
				$data       = (array) $data;
				$intersect  = array_intersect( $data, $comparison );

				$inner_show = ! empty( $intersect );
			} elseif ( 'nin' === $c && is_array( $data ) ) {
				$comparison = (array) $comparison;
				$data       = (array) $data;
				$intersect  = array_intersect( $data, $comparison );

				$inner_show = empty( $intersect );
			}

			if ( 'OR' === $compare && $inner_show ) {
				$show = true;
				break;
			} elseif ( 'AND' === $compare && ! $inner_show ) {
				$show = false;
				break;
			}
		}

		return $show;
	}

	public function if_document_data( $atts, $content = '' ) {
		$atts = $this->parse_comparison_args( $atts );

		$data = $this->get_document_data( $atts, $this->get_document() );
		$show = $this->compare( $atts['compare'], $data, $atts['value'], $atts['chain'] );

		if ( apply_filters( 'storeabill_if_document_shortcode_result', $show, $atts, $this->get_document(), $this ) ) {
			return do_shortcode( $content );
		} else {
			return '';
		}
	}

	public function if_document_item_data( $atts, $content = '' ) {
		$atts = $this->parse_comparison_args( $atts );

		$data = $this->get_document_item_data( $atts, $this->get_document_item() );
		$show = $this->compare( $atts['compare'], $data, $atts['value'], $atts['chain'] );

		if ( apply_filters( 'storeabill_if_document_item_shortcode_result', $show, $atts, $this->get_document_item(), $this ) ) {
			return do_shortcode( $content );
		} else {
			return '';
		}
	}

	public function if_document_item_reference_data( $atts, $content = '' ) {
		$atts = $this->parse_comparison_args( $atts );

		$data = $this->get_document_item_reference_data( $atts, $this->get_document_item_reference() );
		$show = $this->compare( $atts['compare'], $data, $atts['value'], $atts['chain'] );

		if ( apply_filters( 'storeabill_if_document_item_reference_shortcode_result', $show, $atts, $this->get_document_item_reference(), $this ) ) {
			return do_shortcode( $content );
		} else {
			return '';
		}
	}

	public function if_document_item_product_data( $atts, $content = '' ) {
		$atts = $this->parse_comparison_args( $atts );

		$data = $this->get_document_item_product_data( $atts, $this->get_document_item_product() );
		$show = $this->compare( $atts['compare'], $data, $atts['value'], $atts['chain'] );

		if ( apply_filters( 'storeabill_if_document_item_product_shortcode_result', $show, $atts, $this->get_document_item_product(), $this ) ) {
			return do_shortcode( $content );
		} else {
			return '';
		}
	}

	public function if_document_item_product_parent_data( $atts, $content = '' ) {
		$atts = $this->parse_comparison_args( $atts );

		$data = $this->get_document_item_product_parent_data( $atts, $this->get_document_item_product_parent() );
		$show = $this->compare( $atts['compare'], $data, $atts['value'], $atts['chain'] );

		if ( apply_filters( 'storeabill_if_document_item_product_parent_shortcode_result', $show, $atts, $this->get_document_item_product_parent(), $this ) ) {
			return do_shortcode( $content );
		} else {
			return '';
		}
	}

	public function if_customer_data( $atts, $content = '' ) {
		$atts = $this->parse_comparison_args( $atts );

		$data = $this->get_customer_data( $atts, $this->get_customer() );
		$show = $this->compare( $atts['compare'], $data, $atts['value'], $atts['chain'] );

		if ( apply_filters( 'storeabill_if_customer_shortcode_result', $show, $atts, $this->get_customer(), $this ) ) {
			return do_shortcode( $content );
		} else {
			return '';
		}
	}

	public function if_document_total_data( $atts, $content = '' ) {
		$atts = $this->parse_comparison_args( $atts );

		$data = $this->get_document_total_data( $atts, $this->get_document_total() );
		$show = $this->compare( $atts['compare'], $data, $atts['value'], $atts['chain'] );

		if ( apply_filters( 'storeabill_if_document_total_shortcode_result', $show, $atts, $this->get_document_total(), $this ) ) {
			return do_shortcode( $content );
		} else {
			return '';
		}
	}

	public function if_document_reference_data( $atts, $content = '' ) {
		$atts = $this->parse_comparison_args( $atts );

		$data = $this->get_document_reference_data( $atts, $this->get_document_reference() );
		$show = $this->compare( $atts['compare'], $data, $atts['value'], $atts['chain'] );

		if ( apply_filters( 'storeabill_if_document_reference_shortcode_result', $show, $atts, $this->get_document(), $this ) ) {
			return do_shortcode( $content );
		} else {
			return '';
		}
	}

	protected function format_result( $data, $atts = array(), $object = false ) {
		$return_data = $data;

		/**
		 * Legacy support format string as second argument.
		 */
		if ( ! empty( $atts ) && ! is_array( $atts ) ) {
			$atts = array(
				'format' => $atts,
			);
		}

		$atts               = $this->parse_args( $atts );
		$format             = $atts['format'];
		$sanitized_format   = str_replace( '-', '_', sanitize_key( $format ) );
		$has_applied_format = false;

		if ( ! empty( $sanitized_format ) && has_filter( "storeabill_format_{$sanitized_format}_shortcode" ) ) {
			$has_applied_format = true;
			$return_data        = apply_filters( "storeabill_format_{$sanitized_format}_shortcode", $return_data, $data, $atts, $this, $object );
		} elseif ( is_a( $data, 'WC_DateTime' ) || is_a( $data, 'DateTime' ) ) {
			$format = empty( $format ) ? sab_date_format() : $format;

			if ( ! is_a( $data, 'WC_DateTime' ) ) {
				$data = new \WC_DateTime( "@{$data->getTimestamp()}", new \DateTimeZone( 'UTC' ) );
			}

			try {
				$return_data        = $data->date_i18n( $format );
				$has_applied_format = true;
			} catch ( \Exception $e ) {
				$return_data = '';
			}
		} elseif ( is_a( $data, 'DatePeriod' ) ) {
			$start_date = $data->getStartDate();
			$end_date   = $data->getEndDate();
			$is_period  = false;

			if ( $end_date > $start_date ) {
				$is_period = true;
			}

			$format = empty( $format ) ? sab_date_format() : $format;

			if ( ! is_a( $end_date, 'WC_DateTime' ) ) {
				$end_date = new \WC_DateTime( "@{$end_date->getTimestamp()}", new \DateTimeZone( 'UTC' ) );
			}

			if ( ! is_a( $start_date, 'WC_DateTime' ) ) {
				$start_date = new \WC_DateTime( "@{$start_date->getTimestamp()}", new \DateTimeZone( 'UTC' ) );
			}

			try {
				if ( $is_period ) {
					$return_data = $start_date->date_i18n( $format ) . apply_filters( 'storeabill_date_period_separator', ' - ' ) . $end_date->date_i18n( $format );
				} else {
					$return_data = $start_date->date_i18n( $format );
				}

				$has_applied_format = true;
			} catch ( \Exception $e ) {
				$return_data = '';
			}
		} elseif ( is_a( $data, 'WC_Order_Item' ) ) {
			$return_data = $data->get_name();
		} elseif ( is_a( $data, '\Vendidero\StoreaBill\Document\Item' ) ) {
			$return_data = $data->get_name();
		} elseif ( is_a( $data, '\Vendidero\StoreaBill\Document\Attribute' ) ) {
			$return_data = $data->get_formatted_value();
		} elseif ( is_a( $data, '\Vendidero\StoreaBill\Document\Total' ) ) {
			$return_data = $data->get_formatted_label() . ': ' . $data->get_formatted_total();
		} elseif ( is_array( $data ) ) {
			$count       = 0;
			$filtered    = array_filter( $data );
			$return_data = '';

			if ( ! empty( $atts['args'] ) ) {
				$new_filtered = array();

				foreach ( $atts['args'] as $arg ) {
					if ( isset( $filtered[ $arg ] ) ) {
						$new_filtered = array_merge( is_array( $filtered[ $arg ] ) ? $filtered[ $arg ] : array( $filtered[ $arg ] ), $new_filtered );
					}
				}

				$filtered = $new_filtered;
			}

			if ( ! is_array( $filtered ) ) {
				$filtered = array( $filtered );
			}

			foreach ( $filtered as $d ) {
				$count++;
				$return_data .= ( $count > 1 ? ', ' : '' ) . $this->format_result( $d, $atts, $object );
			}
		} elseif ( is_string( $data ) ) {
			$return_data = wp_kses_post( nl2br( wptexturize( $data ) ) );
		}

		if ( ! $has_applied_format && ! empty( $format ) ) {
			if ( 'price' === $format ) {
				if ( is_a( $object, '\Vendidero\StoreaBill\Interfaces\Reference' ) ) {
					if ( $object->is_callable( 'get_formatted_price' ) ) {
						$return_data        = $object->get_formatted_price( $return_data );
						$has_applied_format = true;
					} elseif ( $object->is_callable( 'get_currency' ) ) {
						$currency           = $object->get_currency();
						$return_data        = sab_format_price( $return_data, array( 'currency' => $currency ) );
						$has_applied_format = true;
					}
				} elseif ( is_callable( array( $object, 'get_formatted_price' ) ) ) {
					$return_data        = $object->get_formatted_price( $data );
					$has_applied_format = true;
				}

				if ( ! $has_applied_format ) {
					$price_args = array();

					if ( $document = $this->get_document() ) {
						if ( is_callable( array( $document, 'get_currency' ) ) ) {
							$price_args['currency'] = $document->get_currency();
						}
					}

					$return_data = sab_format_price( $return_data, $price_args );
				}
			} elseif ( 'decimal' === $format && is_numeric( $return_data ) ) {
				$return_data = sab_print_decimal( $return_data );
			} elseif ( 'weight' === $format ) {
				if ( is_callable( array( $object, 'get_formatted_weight' ) ) ) {
					$return_data = $object->get_formatted_weight( $data );
				} else {
					$return_data = sab_print_decimal( $return_data );
				}
			} elseif ( 'country' === $format && 2 === strlen( $return_data ) ) {
				$return_data = sab_format_country_name( $return_data );
			} elseif ( 'upper' === $format ) {
				$return_data = strtoupper( $return_data );
			} elseif ( 'lower' === $format ) {
				$return_data = strtolower( $return_data );
			} elseif ( 'date:' === substr( $format, 0, 5 ) || ( strstr( strtolower( $format ), 'm' ) && strstr( strtolower( $format ), 'y' ) && strstr( strtolower( $format ), 'd' ) ) ) {
				$datetime = false;
				$format   = 'date:' === substr( $format, 0, 5 ) ? substr( $format, 5 ) : $format;

				// Timestamp vs formatted strings
				if ( is_numeric( $return_data ) && (int) $return_data == $return_data ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
					$datetime = sab_string_to_datetime( $return_data );
				} elseif ( is_string( $return_data ) && strtotime( $return_data ) ) {
					// The date string should be in local site timezone. Convert to UTC
					$timestamp = sab_string_to_timestamp( get_gmt_from_date( $return_data ) );
					$datetime  = new \WC_DateTime( "@{$timestamp}", new \DateTimeZone( 'UTC' ) );

					// Set local timezone or offset.
					if ( get_option( 'timezone_string' ) ) {
						$datetime->setTimezone( new \DateTimeZone( sab_timezone_string() ) );
					} else {
						$datetime->set_utc_offset( sab_timezone_string() );
					}
				}

				if ( is_a( $datetime, 'WC_DateTime' ) ) {
					return $this->format_result( $datetime, $atts, $object );
				}
			} elseif ( ! empty( $return_data ) ) {
				if ( is_callable( $format ) ) {
					try {
						$reflection = is_array( $format ) ? new \ReflectionMethod( $format[0], $format[1] ) : new \ReflectionFunction( $format );

						if ( 1 === $reflection->getNumberOfRequiredParameters() ) {
							$return_data = call_user_func_array( $format, array( $return_data ) );
						}
					} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
					}
				}
			}
		}

		return apply_filters( 'storeabill_format_shortcode_result', $return_data, $format, $data, $atts, $this, $object );
	}

	protected function get_current_page_no( $atts ) {
		if ( $this->is_editor_preview( $atts ) ) {
			return '1';
		} else {
			return '<!--current_page_no-->';
		}
	}

	protected function get_total_pages( $atts ) {
		if ( $this->is_editor_preview( $atts ) ) {
			return '1';
		} else {
			return '<!--total_pages_no-->';
		}
	}
}
