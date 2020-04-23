<?php
declare(strict_types=1);

namespace GuzabaPlatform\Assets\Controllers;

use Azonmedia\Exceptions\InvalidArgumentException;
use Guzaba2\Authorization\Exceptions\PermissionDeniedException;
use Guzaba2\Base\Exceptions\RunTimeException;
use Guzaba2\Http\Method;
use Guzaba2\Orm\Exceptions\RecordNotFoundException;
use GuzabaPlatform\Assets\Models\File;
use GuzabaPlatform\Platform\Application\BaseController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Guzaba2\Translator\Translator as t;

class Assets extends BaseController
{

    protected const CONFIG_DEFAULTS = [
        'routes'        => [
            '/admin/assets' => [
                Method::HTTP_GET => [self::class, 'main'],
                Method::HTTP_POST => [self::class, 'create_file'],
                //it is not allowed to delete, copy or renamte the root directory
            ],
            '/admin/assets/{path}' => [
                Method::HTTP_GET => [self::class, 'main'],
                Method::HTTP_POST => [self::class, 'create_file'],
                Method::HTTP_DELETE => [self::class, 'delete_file'],
                Method::HTTP_PATCH => [self::class, 'rename_file'],
                Method::HTTP_PUT => [self::class, 'copy_file'],
            ],

            '/admin/asset/properties/{path}' => [
                Method::HTTP_GET => [self::class, 'properties']
            ],
            //'/admin/assets/add-to-navigation/{path}' => [
            //    Method::HTTP_POST => [self::class, 'add_to_navigation']
            //],
        ],
        //'directory_separator'   => '+',//this is used instead of / as this can not be used in the URL / REST endpoints
    ];

    protected const CONFIG_RUNTIME = [];

    /**
     * Lists the files ($path is expected to be a path to directory
     * @return ResponseInterface
     */
    public function main(string $path = './') : ResponseInterface
    {
        if (!$path) {
            $path = './';
        }
        $path = str_replace('//', '/', $path);
        $File = new File($path);

        if ($File->is_file()) {
            $Response = $this->view_file($path);
        } else {
            $files = $File->get_files();
            $struct = ['files' => [] ];
            foreach ($files as $ContainedFile) {
                $struct['files'][] = [
                    //'name'      => $ContainedFile->get_relative_path(),
                    'name'      => $ContainedFile->get_name(),
                    'is_dir'    => $ContainedFile->is_dir(),
                    'mime_type' => $ContainedFile->is_dir() ? '' : $ContainedFile->get_mime_type(),
                ];
            }
            $Response = self::get_structured_ok_response($struct);
        }
        return $Response;
    }

    protected function view_file() : ResponseInterface
    {

    }

    /**
     * @param string $path
     * @param UploadedFileInterface|null $uploaded_file
     * @param string $dir
     * @return ResponseInterface
     * @throws InvalidArgumentException
     */
    public function create_file(string $path = './', ?UploadedFileInterface $uploaded_file = NULL, string $new_directory_name = '') : ResponseInterface
    {
        if ($uploaded_file) {
            $File = File::upload_file($path, $uploaded_file);
            $struct = [
                'message'       => sprintf(t::_('The file %1$s %2$s kb was uploaded sucessfully to %3$s.'), $File->get_name(), $File->get_size() / 1024, $File->get_relative_path() ),
                'file_name'     => $File->get_name(),
                'relative_path' => $File->get_relative_path(),
                'file_size'     => $File->get_size(),
            ];
            return self::get_structured_ok_response($struct);
        } elseif ($new_directory_name) {
            $File = File::create_dir($path, $new_directory_name);
            $struct = [
                'message' => sprintf(t::_('New directory %1$s created as %2$s.'), $new_directory_name, $File->get_relative_path() ),
                'file_name'     => $File->get_name(),
                'relative_path' => $File->get_relative_path(),
                'file_size'     => $File->get_size(),
            ];
            return self::get_structured_ok_response($struct);
        } else {
            return self::get_structured_badrequest_response(['message' => t::_('No file uploaded or new directory name provided.') ]);
        }
    }

    public function delete_file(string $path) : ResponseInterface
    {
        $File = new File($path);
        $file_type = t::_($File->get_type());
        $File->delete();
        $struct = ['message' => sprintf(t::_('The %1$s %s2 was deleted.'), $file_type, $File->get_relative_path() )];
        return self::get_structured_ok_response($struct);
    }

    public function rename_file(string $from_path, string $to_path) : ResponseInterface
    {

    }

    public function copy_file(string $from_path, string $to_path) : ResponseInterface
    {

    }

    public function properties(string $path) : ResponseInterface
    {
        $File = new File($path);
        $struct = [
            'name'      => $File->get_name(),
            'path'      => $File->get_relative_path(),
            'type'      => t::_($File->get_type()),
            'mime_type' => $File->is_file() ? $File->get_mime_type() : '',
            'size'      => sprintf(t::_('%1$s kb'), round($File->get_size() / 1024, 2) ),
            'ctime'     => $File->get_ctime_string(),
            'mtime'     => $File->get_mtime_string(),
            'atime'     => $File->get_atime_string(),
        ];
        return self::get_structured_ok_response($struct);
    }

//this needs to be defined as a hook
//    public function add_to_navigation(string $path) : ResponseInterface
//    {
//
//    }
}