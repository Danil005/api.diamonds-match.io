<?php

namespace App\Utils;

use Illuminate\Session\Store;

class Img
{
    public $img;

    public $transparent;

    public $width;

    public $height;

    public function __construct($img = null)
    {
        if (!empty($img)) {
            $ext = explode('.', $img)[1];
            if( $ext == 'png' ) {
                $_im = $img;
                $img = \Storage::disk('public')->path(str_replace('storage/', '',$img));
                try {
                    $image = imagecreatefrompng($img);
                } catch (\Exception) {
                    $image = imagecreatefrompng($_im);
                }
                $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                imagealphablending($bg, TRUE);
                imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                imagedestroy($image);
                $quality = 50; // 0 = worst / smaller file, 100 = better / bigger file
                imagejpeg($image, storage_path('app/public/pptx/temp_convert.jpg'), $quality);
                sleep(3);
                $img = \Storage::disk('public')->path('pptx/temp_convert.jpg');
            }
            $this->img = imagecreatefromjpeg($img);
            $this->width = imagesx($this->img);
            $this->height = imagesy($this->img);
            $this->setTransparentColour();
        }
    }

    public function create($width, $height, $transparent)
    {
        $this->img = imagecreatetruecolor($width, $height);
        $this->width = $width;
        $this->height =$height;

        $this->setTransparentColour();

        if (true === $transparent) {
            imagefill($this->img, 0, 0, $this->transparent);
        }
    }

    public function setTransparentColour($red = 255, $green = 0, $blue = 255)
    {
        $this->transparent = imagecolorallocate($this->img, $red, $green, $blue);
        imagecolortransparent($this->img, $this->transparent);
    }

    public function circleCrop()
    {
        $mask = imagecreatetruecolor($this->width, $this->height);
        $black = imagecolorallocate($mask, 0, 0, 0);
        $magenta = imagecolorallocate($mask, 255, 0, 255);

        imagefill($mask, 0, 0, $magenta);

        imagefilledellipse(
            $mask,
            ($this->width / 2),
            ($this->height / 2),
            $this->width,
            $this->height,
            $black
        );

        imagecolortransparent($mask, $black);

        imagecopymerge($this->img, $mask, 0, 0, 0, 0, $this->width, $this->height, 100);

        imagedestroy($mask);
    }

    public function merge(Img $in, $dst_x = 0, $dst_y = 0)
    {
        imagecopymerge(
            $this->img,
            $in->img,
            $dst_x,
            $dst_y,
            0,
            0,
            $in->width,
            $in->height,
            100
        );
    }

    public function render()
    {
        imagepng( $this->img, storage_path('app/public/pptx/temp.png'));
        sleep(3);
        return \Storage::disk('public')->path('pptx/temp.png');
    }
}
