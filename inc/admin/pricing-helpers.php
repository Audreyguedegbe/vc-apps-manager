<?php
/**
 * Normalise la meta vc_pricing et renvoie [$pricing_type, $pricing_plans].
 *
 * Rétro-compatibilité :
 * - Ancienne structure simple : ['general'=>...], ['monthly'=>...], etc.
 * - Ancienne structure multi avec souscription : ['monthly'=>[...], 'yearly'=>[...]]
 * - Nouvelle structure : ['type'=>'...', 'plans'=>[...]]
 *
 * @param int $post_id
 * @param string $has_subscription_meta meta vc_has_subscription (yes|no) si dispo
 * @param string $has_multiplan_meta    meta vc_has_multiplan   (yes|no) si dispo
 * @return array [$type, $plans]
 */
function vc_apps_get_pricing_structured( $post_id, $has_subscription_meta = '', $has_multiplan_meta = '' ) {
	$raw = get_post_meta( $post_id, 'vc_pricing', true );

	$type  = '';
	$plans = [];

	// ✅ Cas 1 : Nouvelle structure
	if ( is_array( $raw ) && isset( $raw['type'], $raw['plans'] ) ) {
		$type  = $raw['type'];
		$plans = $raw['plans'];
	}
	// ✅ Cas 2 : Ancienne structure simple
	elseif ( is_array( $raw ) && isset( $raw['general'] ) && is_array( $raw['general'] ) ) {
		$type  = 'simple_nosub';
		$plans = [ 'general' => $raw['general'] ];
	}
	// ✅ Cas 3 : Ancienne structure simple avec souscription
	elseif ( is_array( $raw ) && isset( $raw['monthly'], $raw['yearly'] ) && !isset( $raw['monthly'][0] ) && !isset( $raw['yearly'][0] ) ) {
		$type  = 'simple_withsub';
		$plans = [
			'monthly' => $raw['monthly'],
			'yearly'  => $raw['yearly'],
		];
	}
	// ✅ Cas 4 : Ancienne structure multi avec souscription
	elseif ( is_array( $raw ) && isset( $raw['monthly'][0], $raw['yearly'][0] ) ) {
		$type  = 'multi_withsub';
		$plans = [
			'monthly' => $raw['monthly'],
			'yearly'  => $raw['yearly'],
		];
	}
	// ✅ Cas 5 : Ancienne structure multi sans souscription
	elseif ( is_array( $raw ) ) {
		$type  = 'multi_nosub';
		$plans = $raw;
	}

	// 🔄 Harmonisation du type pour affichage (si on utilise encore l’ancienne convention)
	if ( $type === 'simple' ) {
		$type = 'simple_nosub';
	} elseif ( $type === 'multi' ) {
		$type = 'multi_nosub';
	} elseif ( $type === 'simple_subscription' ) {
		$type = 'simple_withsub';
	} elseif ( $type === 'multi_subscription' ) {
		$type = 'multi_withsub';
	}

	return [ $type, $plans ];
}