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

class Assets extends BaseController
{

    protected const CONFIG_DEFAULTS = [
        'routes'        => [
            '/admin/assets' => [
                Method::HTTP_GET => [self::class, 'main']
            ],
            '/admin/assets/{path}' => [
                Method::HTTP_GET => [self::class, 'main']
            ],
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

    public function create_dir(string $path) : ResponseInterface
    {

    }

    public function upload_file() : ResponseInterface
    {

    }

    public function view_file(string $path) : ResponseInterface
    {

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
}