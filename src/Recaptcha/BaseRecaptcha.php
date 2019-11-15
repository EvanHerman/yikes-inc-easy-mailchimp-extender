<?php
/**
 * YIKES Inc. Easy Forms.
 *
 * @package   YIKES\EasyForms
 * @author    Freddie Mixell
 * @license   GPL2
 */

namespace YIKES\EasyForms\Recaptcha;

use YIKES\EasyForms\Renderable;
use YIKES\EasyForms\Assets\AssetsAware;
use YIKES\EasyForms\Assets\AssetsAwareness;
use YIKES\EasyForms\Services;

abstract class BaseRecaptcha implements Renderable, AssetsAware, Service {
    use AssetAwareness;

    public function register() {
        $this->register_assets();

        add_action();
    }
}