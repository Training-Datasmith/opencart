<?php

declare(strict_types=1);

namespace Opencart\Catalog\Model\Tool;

/**
 * Class Image
 *
 * Can be called using $this->load->model('tool/image');
 *
 * @package Opencart\Catalog\Model\Tool
 */
class Image extends \Opencart\System\Engine\Model
{
    /**
     * Resize
     *
     *
     * @throws \Exception
     *
     */
    public function resize(string $filename, int $width, int $height, string $default = ''): string
    {
        $filename = html_entity_decode($filename, ENT_QUOTES, 'UTF-8');

        if (!is_file(DIR_IMAGE . $filename) || !str_starts_with(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), DIR_IMAGE)) {
            return '';
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $image_old = $filename;
        $image_new = 'cache/' . oc_substr($filename, 0, oc_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

        if (!is_file(DIR_IMAGE . $image_new) || (filemtime(DIR_IMAGE . $image_old) > filemtime(DIR_IMAGE . $image_new))) {
            [$width_orig, $height_orig, $image_type] = getimagesize(DIR_IMAGE . $image_old);

            if (!in_array($image_type, [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_WEBP])) {
                return $this->config->get('config_url') . 'image/' . $image_old;
            }

            $path = '';

            $directories = explode('/', dirname($image_new));

            foreach ($directories as $directory) {
                if (!$path) {
                    $path = $directory;
                } else {
                    $path = $path . '/' . $directory;
                }

                if (!is_dir(DIR_IMAGE . $path)) {
                    @mkdir(DIR_IMAGE . $path, 0777);
                }
            }

            if ($width_orig != $width || $height_orig != $height) {
                $image = new \Opencart\System\Library\Image(DIR_IMAGE . $image_old);
                $image->resize($width, $height, $default);
                $image->save(DIR_IMAGE . $image_new);
            } else {
                copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
            }
        }

        $image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatically changing space from " " to +

        return $this->config->get('config_url') . 'image/' . $image_new;
    }
}
