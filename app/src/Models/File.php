<?php
declare(strict_types=1);

namespace GuzabaPlatform\Assets\Models;


use Azonmedia\Utilities\GeneralUtil;
use Azonmedia\Utilities\StringUtil;
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
use GuzabaPlatform\Platform\Application\Interfaces\ModelInterface;
use JBZoo\Utils\Str;

/**
 * Class File
 * @package GuzabaPlatform\Assets\Models
 *
 * This class represents a File under the store_
 */
class File extends \Azonmedia\Filesystem\File implements BaseInterface, ModelInterface
{

    protected const CONFIG_DEFAULTS = [
        'services'          =>  [
            'GuzabaPlatform',
        ],
        'store_relative_base'       => '/public/assets',// this is relative to the application diretory -> ./app/public/assets
        'object_name_property'      => 'file_name',
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

    public function get_ctime_string() : string
    {
        $GuzabaPlatform = self::get_service('GuzabaPlatform');
        $date_time_format = $GuzabaPlatform->get_date_time_formats()['date_time_format'];
        return date($date_time_format, $this->get_ctime());
    }

    public function get_mtime_string() : string
    {
        $GuzabaPlatform = self::get_service('GuzabaPlatform');
        $date_time_format = $GuzabaPlatform->get_date_time_formats()['date_time_format'];
        return date($date_time_format, $this->get_mtime());
    }

    public function get_atime_string() : string
    {
        $GuzabaPlatform = self::get_service('GuzabaPlatform');
        $date_time_format = $GuzabaPlatform->get_date_time_formats()['date_time_format'];
        return date($date_time_format, $this->get_atime());
    }

    public function get_record_data() : array
    {
        $ret = [];
        foreach ($this->get_property_names() as $property_name) {
            if ($property_name === 'file_contents') {
                continue;
            }
            $ret[$property_name] = $this->{$property_name};
        }
        return $ret;
    }

    /**
     * Returns an indexed array with the relative path to all files.
     * @param string $rel_path
     * @return array
     * @throws RunTimeException
     */
    public static function get_all_files_simple(string $rel_path = '') : array
    {
        $Directory = new \RecursiveDirectoryIterator(self::get_absolute_store_path().$rel_path);
        $Iterator = new \RecursiveIteratorIterator($Directory);
        $ret = [];
        $store_base_path = self::get_absolute_store_path();
        foreach ($Iterator as $absolute_file_path => $SplFileInfo) {
            if (StringUtil::ends_with($absolute_file_path, '..') || StringUtil::ends_with($absolute_file_path, '.')) {
                continue;
            }
            $ret[] = str_replace($store_base_path.'/','',$absolute_file_path);
        }
        return $ret;
    }

    /**
     * @param string $rel_path
     * @return array
     * @throws RunTimeException
     * @throws \Azonmedia\Exceptions\PermissionDeniedException
     * @throws \Azonmedia\Exceptions\RecordNotFoundException
     */
    public static function get_all_files(string $rel_path = '') : array
    {
        $files = self::get_all_files_simple($rel_path);
        foreach ($files as $file_rel_path) {
            $ret[] = (new File($file_rel_path))->get_record_data();
        }
        return $ret;
    }

    /**
     * Returns all files.
     * $index supports ['path'] to return only the files in the provided path
     * Returns only files, no directories.
     * The rest of the arguments are not implemented
     * @param array $index
     * @param int $offset
     * @param int $limit
     * @param bool $use_like
     * @param string|null $sort_by
     * @param bool $sort_desc
     * @param int|null $total_found_rows
     * @return iterable
     */
    public static function get_data_by(array $index, int $offset = 0, int $limit = 0, bool $use_like = FALSE, ?string $sort_by = NULL, bool $sort_desc = FALSE, ?int &$total_found_rows = NULL) : iterable
    {
        //TODO implement the rest of the arguments
        $rel_path = $index['path'] ?? '';
        $ret = self::get_all_files($rel_path);
        return $ret;
    }

    /**
     * @implements ModelInterface
     * @return string
     * @throws RunTimeException
     */
    public function get_object_name() : string
    {
        $object_name_property = static::get_object_name_property();
        return $this->{$object_name_property};
    }

    /**
     * @implements ModelInterface
     * @return string
     * @throws RunTimeException
     * @throws \Azonmedia\Exceptions\InvalidArgumentException
     */
    public static function get_object_name_property(): string
    {
        if (!isset(static::CONFIG_RUNTIME['object_name_property'])) {
            throw new RunTimeException(sprintf(t::_('The class %1s is missing the CONFIG_DEFAULTS[\'object_name_property\'] configuation option.'), static::class));
        }
        return static::CONFIG_RUNTIME['object_name_property'];
    }

}