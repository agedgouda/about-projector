<?php

namespace App\Modifiers;

use Statamic\Modifiers\Modifier;

/**
 * Tags sign-up links with ?ref={page}.{spot} (e.g. "pricing.final-cta") so projecthq.app
 * can record which button a sign-up came from. Any other link passes through unchanged.
 *
 * Usage: {{ cta_url | signup_ref('final-cta') }} or {{ url | signup_ref('hero', count) }} for "hero-2".
 */
class SignupRef extends Modifier
{
    public function index($value, $params, $context)
    {
        $parts = parse_url((string) $value);

        if (($parts['host'] ?? null) !== 'projecthq.app' || rtrim($parts['path'] ?? '', '/') !== '/register') {
            return $value;
        }

        parse_str($parts['query'] ?? '', $query);
        $page = (string) ($context['slug'] ?? '') ?: 'site';
        $query['ref'] = $page.'.'.implode('-', $params);

        return ($parts['scheme'] ?? 'https').'://projecthq.app'.$parts['path'].'?'.http_build_query($query)
            .(isset($parts['fragment']) ? '#'.$parts['fragment'] : '');
    }
}
