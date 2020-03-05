<?php
declare(strict_types=1);

namespace GuzabaPlatform\Assets\Hooks;

use Guzaba2\Base\Base;
use Guzaba2\Http\Body\Structured;
use GuzabaPlatform\Assets\Models\File;
use Psr\Http\Message\ResponseInterface;

class AfterStaticContentMain extends Base
{
    public function execute_hook(ResponseInterface $Response) : ResponseInterface
    {
        $Body = $Response->getBody();
        $struct = $Body->getStructure();

        //TODO - add file size and mtime in the dropdown - use get_all_files()
        $assets = File::get_all_files_simple();
        //the paths need to be absolute
        $assets = array_map(fn($path) => '/'.$path, $assets);

        //$struct['content']['assets'] = $assets;//this will append the assets... but we need to prepend it
        $old_content = $struct['content'];
        $new_content = ['assets' => $assets];
        foreach ($old_content as $key => $value) {
            $new_content[$key] = $value;
        }
        $struct['content'] = $new_content;

        $Response = $Response->withBody( new Structured($struct) );
        return $Response;
    }
}