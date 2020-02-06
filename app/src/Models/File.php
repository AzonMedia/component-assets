<?php
declare(strict_types=1);

namespace GuzabaPlatform\Assets\Models;


use Azonmedia\Utilities\GeneralUtil;
use Guzaba2\Authorization\Exceptions\PermissionDeniedException;
use Guzaba2\Base\Base;
use Guzaba2\Base\Exceptions\InvalidArgumentException;
use Guzaba2\Base\Exceptions\RunTimeException;
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
class File extends Base
{

    protected const CONFIG_DEFAULTS = [
        'services'          =>  [
            'GuzabaPlatform',
        ],
        'store_rel_base'    => '/public/assets/',// this is relative to the application diretory -> ./app/public/assets
    ];

    protected const CONFIG_RUNTIME = [];


    protected string $absolute_path;

    protected string $relative_path;

    /**
     * @param string $relative_path
     */
    public function __construct(string $relative_path)
    {

        $real_store_base_path = self::get_absolute_store_path();
        $relative_path = self::validate_relative_path($relative_path);
        $absolute_path = $real_store_base_path.'/'.$relative_path;
        $real_absolute_path = realpath($absolute_path);
        if (!$real_absolute_path || !file_exists($real_absolute_path)) {
            throw new RecordNotFoundException(sprintf(t::_('The file/dir %s does not exist.'), $relative_path));
        }
        if (!is_readable($real_absolute_path)) {
            throw new PermissionDeniedException(sprintf(t::_('The file/dir %s is not readable.'), $relative_path));
        }
        $this->relative_path = $relative_path;
        $this->absolute_path = $real_absolute_path;
    }


    /**
     * Returns a file based on the provided path.
     * The provided path is relative to the
     * @param string $base_path The base path of the store
     * @param string $absolute_path
     * @return static
     */
    public static function get_by_absolute_path(string $absolute_path) : self
    {

        $real_store_base_path = self::get_absolute_store_path();
        //lets find out is the requested file from private assets (./app/assets) or public assets (./app/public/assets)
        $relative_path = str_replace($real_store_base_path, '', $absolute_path);
        return new self($relative_path);
    }

    /**
     * @param string $relative_path
     * @return string
     * @throws InvalidArgumentException
     * @throws RunTimeException
     */
    public static function validate_relative_path(string $relative_path) : string
    {
        $real_store_base_path = self::get_absolute_store_path();
        if (!$relative_path) {
            throw new InvalidArgumentException(sprintf(t::_('There is no path provided.')));
        }
        if ($relative_path[0] === '/') {
            throw new InvalidArgumentException(sprintf(t::_('The provided path %s is absolute. Relative path (to store base %s) is expected.'), $relative_path, $real_store_base_path ));
        }
//        if ($relative_path === './') {
//            throw new InvalidArgumentException(sprintf(t::_('The provided path %s is invalid.'), $relative_path));
//        }
        if ($relative_path[0] !== '.') {
            $relative_path = './'.$relative_path;
        }
        return $relative_path;
    }

    /**
     * @param string $relative_path
     * @return static
     * @throws PermissionDeniedException
     * @throws RecordNotFoundException
     * @throws RunTimeException
     */
    public static function create_dir(string $relative_path) : self
    {
        self::create_process($relative_path, function() use ($relative_path, $content) {
            if (mkdir($real_absolute_path) === FALSE) {
                throw new RunTimeException(sprintf(t::_('The creation of directory %s failed.'), $relative_path));
            }
        });
        return new self($relative_path);
    }

    public static function create_file(string $relative_path, string $content) : self
    {
        self::create_process($relative_path, function() use ($relative_path, $content) {
            if (file_put_contents($real_absolute_path, $content) === FALSE) {
                throw new RunTimeException(sprintf(t::_('The creation of file %s failed.'), $relative_path));
            }
        });
        return new self($relative_path);
    }

    private static function create_process(string $relative_path, callable $Callback) : void
    {
        $real_store_base_path = self::get_absolute_store_path();
        $relative_path = self::validate_relative_path($relative_path);
        $absolute_path = $real_store_base_path.'/'.$relative_path;
        $real_absolute_path = realpath($absolute_path);
        if (file_exists($real_absolute_path)) {
            throw new RunTimeException(sprintf(t::_('There is already a file/directory %s.'), $relative_path));
        }
        $Callback();
    }

    /**
     * Does not check permissions
     */
    public function delete() : void
    {
        unlink($this->absolute_path);
        $this->absolute_path = '';
        $this->relative_path = '';
    }

    public function move(string $to_relative_path) : void
    {
        $real_store_base_path = self::get_absolute_store_path();
        $to_relative_path = self::validate_relative_path($to_relative_path);
        $absolute_to_path = $real_store_base_path.$to_relative_path;
        $real_absolute_to_path = realpath($absolute_to_path);
        if (!rename($this->absolute_path, $real_absolute_to_path)) {
            throw new RunTimeException(sprintf(t::_('Moving file %s to %s failed.'), $this->relative_path, $to_relative_path));
        }
        $this->absolute_path = $real_absolute_to_path;
        $this->relative_path = $to_relative_path;
    }

    public function copy(string $new_relative_path) : void
    {

    }

    public function get_relative_path() : string
    {
        return $this->relative_path;
    }

    public function get_absolute_path() : string
    {
        return $this->absolute_path;
    }

    public function is_dir() : bool
    {
        return is_dir($this->absolute_path);
    }

    public function is_file() : bool
    {
        return is_file($this->absolute_path);
    }

    public function get_extension() : string
    {
        if ($this->is_dir()) {
            throw new RunTimeException(sprintf(t::_('Can not obtain extension on directory %s.'), $this->relative_path));
        }
    }

    public function get_mime_type() : string
    {
        if ($this->is_dir()) {
            throw new RunTimeException(sprintf(t::_('Can not obtain mime type on directory %s.'), $this->relative_path));
        }
        return mime_content_type($this->absolute_path);
    }

    /**
     * Applicable only on directories (@see self::is_dir())
     * Returns an array of File
     * @return array
     */
    public function get_files() : array
    {
        $ret = [];
        $files = scandir($this->absolute_path);
        foreach ($files as $path) {
            if ($path === '.' || $path === '..') {
                continue;
            }
            if (is_link($this->absolute_path.'/'.$path)) {
                continue;
            }
            $ret[] = new self($this->relative_path.$path);
        }
        return $ret;
        //return array_map( fn(string $path) : self => new self($path) , scandir($this->absolute_path) );
    }

    /**
     * Returns the parent dir (File object), NULL if this is the root of the store
     * @return $this|null
     */
    public function get_parent() : ?self
    {

    }

    /**
     * Applicable only on files (@see self::is_file())
     * @return string
     */
    public function get_contents() : string
    {
        return file_get_contents($this->absolute_path);
    }

    public static function get_relative_store_path() : string
    {
        if (empty(self::CONFIG_RUNTIME['store_rel_base'])) {
            throw new RunTimeException(sprintf(t::_('The class %s does not have the "store_rel_base" config property set.'), __CLASS__ ));
        }
        return self::CONFIG_RUNTIME['store_rel_base'];
    }

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