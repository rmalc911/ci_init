<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @param ?string $payout_id
 * @return string<HTML>
 */
function fun($param = false) {
    if (!$param) {
        return "";
    }
    return <<<HTML
    <div class="text-center">
    </div>
    HTML;
}
