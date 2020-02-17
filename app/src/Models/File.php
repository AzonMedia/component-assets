<?php
declare(strict_types=1);

namespace GuzabaPlatform\Assets\Models;


use Azonmedia\Utilities\GeneralUtil;
use Guzaba2\Authorization\Exceptions\PermissionDeniedException;
use Guzaba2\Base\Base;
use Guzaba2\Base\Exceptions\InvalidArgumentException;
use Guzaba2\Base\Exceptions\RunTimeException;
use Guzaba2\Base\Interfaces\BaseInterface;
use Guzaba2\Base\Traits\BaseTrait;
use Guzaba2\Orm\Exceptions\RecordNotFoundException;
use Guzaba2\Orm\Store\Interfaces\StoreInterface;
use GuzabaPlatform\Platform\Application\BaseActiveRecord;
use Guzaba2\Translator\Translator as t;

/**
 * Class File
 * @package GuzabaPlatform\Assets\Models
 *
 * This class represents a File under the store_
 */
class File extends \Azonmedia\Filesystem\File implements BaseInterface
{

    protected const CONFIG_DEFAULTS = [
        'services'          =>  [
            'GuzabaPlatform',
        ],
        'store_relative_base'    => '/public/assets',// this is relative to the application diretory -> ./app/public/assets
    ];

    protected const CONFIG_RUNTIME = [];

    use BaseTrait;

    public static function _initialize_class() : void
    {
        if (!isset(self::CONFIG_RUNTIME['store_relative_base'])) {
            throw new RunTimeException();
        }
        if (self::CONFIG_RUNTIME['store_relative_base'][-1] === '/') {
            throw new RunTimeException(sprintf(t::_('The store_relative_base configuration option has a trailing /.')));
        }
    }

    public static function set_absolute_store_path(string $absolute_path) : void
    {
        throw new RunTimeException(sprintf(t::_('The %s() method is not to be used on class %s. Instead the store absolute path is automatically determined based on the store relative path set in %s::CONFIG_DEFAULTS[\'store_relative_base\'].'), __FUNCTION__, __CLASS__, __CLASS__));
    }

    /**
     * @override
     * @return string
     * @throws RunTimeException
     */
    public static function get_relative_store_path() : string
    {
        if (empty(self::CONFIG_RUNTIME['store_relative_base'])) {
            throw new RunTimeException(sprintf(t::_('The class %s does not have the CONFIG_DEFAULTS[\'store_relative_base\'] config property set.'), __CLASS__ ));
        }
        return self::CONFIG_RUNTIME['store_relative_base'];
    }

    /**
     * @override
     * @return string
     * @throws RunTimeException
     */
    public static function get_absolute_store_path() : string
    {
        $GuzabaPlatform = self::get_service('GuzabaPlatform');

        $store_base_path = $GuzabaPlatform->get_app_dir().self::get_relative_store_path();
        $real_store_base_path = realpath($store_base_path);
        if (!$real_store_base_path) {
            throw new RunTimeException(sprintf(t::_('The store base path %s does not exist.'), $store_base_path));
        }
        return $real_store_base_path;
    }

}