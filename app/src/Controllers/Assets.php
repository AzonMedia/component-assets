<?php
declare(strict_types=1);

namespace GuzabaPlatform\Assets\Controllers;

use Guzaba2\Authorization\Exceptions\PermissionDeniedException;
use Guzaba2\Base\Exceptions\RunTimeException;
use Guzaba2\Http\Method;
use Guzaba2\Orm\Exceptions\RecordNotFoundException;
use GuzabaPlatform\Assets\Models\File;
use GuzabaPlatform\Platform\Application\BaseController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;

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

            '/admin/assets/properties/{path}' => [
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

    /**
     * @param string $path
     * @param UploadedFileInterface|null $uploaded_file
     * @param string $dir
     * @return ResponseInterface
     * @throws \Azonmedia\Exceptions\InvalidArgumentException
     */
    public function create_file(string $path = './', ?UploadedFileInterface $uploaded_file = NULL, string $dir = '') : ResponseInterface
    {
        if ($uploaded_file) {
            $File = File::upload_file($path, $uploaded_file);
            $struct = ['message' => 'OK'];
            $Response = self::get_structured_ok_response($struct);
        } elseif ($dir) {

        } else {
            return self::get_structured_badrequest_response(['message' => t::_('No file uploaded or directory name provided.') ]);
        }

        return $Response;
    }

    public function delete_file(string $path) : ResponseInterface
    {

    }

    public function rename(string $from_path, string $to_path) : ResponseInterface
    {

    }

    public function copy(string $from_path, string $to_path) : ResponseInterface
    {

    }

    public function properties(string $path) : ResponseInterface
    {

    }

//this needs to be defined as a hook
//    public function add_to_navigation(string $path) : ResponseInterface
//    {
//
//    }
}