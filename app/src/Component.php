<?php
declare(strict_types=1);

namespace GuzabaPlatform\Assets;

use Guzaba2\Event\Event;
use Guzaba2\Http\RewritingMiddleware;
use GuzabaPlatform\Components\Base\BaseComponent;
use GuzabaPlatform\Components\Base\Interfaces\ComponentInitializationInterface;
use GuzabaPlatform\Components\Base\Interfaces\ComponentInterface;
use GuzabaPlatform\Platform\Application\Middlewares;
use GuzabaPlatform\Platform\Application\UrlRewritingRules;

/**
 * Class Component
 * @package Azonmedia\Tags
 */
class Component extends BaseComponent implements ComponentInterface, ComponentInitializationInterface
{

    protected const CONFIG_DEFAULTS = [
        'services'      => [
            'Events',
            'FrontendRouter',
        ],
    ];

    protected const CONFIG_RUNTIME = [];

    protected const COMPONENT_NAME = "Assets";
    //https://components.platform.guzaba.org/component/{vendor}/{component}
    protected const COMPONENT_URL = 'https://components.platform.guzaba.org/component/guzaba-platform/assets';
    //protected const DEV_COMPONENT_URL//this should come from composer.json
    protected const COMPONENT_NAMESPACE = __NAMESPACE__;
    protected const COMPONENT_VERSION = '0.0.1';//TODO update this to come from the Composer.json file of the component
    protected const VENDOR_NAME = 'Azonmedia';
    protected const VENDOR_URL = 'https://azonmedia.com';
    protected const ERROR_REFERENCE_URL = 'https://github.com/AzonMedia/component-assets/tree/master/docs/ErrorReference/';

    /**
     * @return array
     */
    public static function run_all_initializations() : array
    {
        //self::update_rewriting_rules();
        self::register_routes();
        //return ['register_routes','update_rewriting_rules'];
        return ['register_routes'];
    }

//this will be needed only for the private assets
//    /**
//     * We need to prevent the Urlrewriter to rewrite the /assets/ requests to /
//     * @throws \Guzaba2\Base\Exceptions\RunTimeException
//     */
//    public static function update_rewriting_rules() : void
//    {
//        $MiddlwareCallback = static function(Event $Event) : void
//        {
//            $Middlewares = $Event->get_subject();
//            $RewritingMiddleware = $Middlewares->get_middleware(RewritingMiddleware::class);
//            if ($RewritingMiddleware) {
//                $Rewriter = $RewritingMiddleware->get_rewriter();
//                $Rewriter->add_prefix('/assets/');
//            }
//        };
//        $Events = self::get_service('Events');
//        $Events->add_class_callback(Middlewares::class, '_after_setup', $MiddlwareCallback);
//    }

    /**
     * @throws \Guzaba2\Base\Exceptions\RunTimeException
     */
    public static function register_routes() : void
    {
        $FrontendRouter = self::get_service('FrontendRouter');

        //the private assets are located in ./app/assets and when requested are going through a wrapper/controller that checks the permissions
        $additional = [
            'name'  => 'Assets',
            'meta' => [
                'in_navigation' => TRUE, //to be shown in the admin navigation
            ],
        ];
        $FrontendRouter->{'/admin'}->add('assets', '@GuzabaPlatform.Assets/AssetsAdmin.vue' ,$additional);

        $additional = [
            'name'  => 'Assets',
        ];
        //$FrontendRouter->{'/admin'}->add('assets/:path', '@GuzabaPlatform.Assets/AssetsAdmin.vue', $additional);
        //use * as the path itself may contain /
        $FrontendRouter->{'/admin'}->add('assets/*', '@GuzabaPlatform.Assets/AssetsAdmin.vue', $additional);
    }
}