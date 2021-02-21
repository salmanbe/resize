<?php

namespace Salmanbe\Resize;

use Intervention\Image\Facades\Image;
use Salmanbe\FileName\FileName;
use Config;
use File;

class Resize {

    /**
     * Function to store file name config options
     * @var array
     */
    private $config;

    /**
     * Function to store file name options
     * @var array
     */
    private $options;

    /**
     * Existing image that will be deleted
     * @var string
     */
    private $old_image;

    /**
     * The uploaded image object
     * @var object
     */
    private $image;

    /**
     * New image name
     * @var string
     */
    public $new_image_name = null;

    /**
     * Path to water mark image
     * @var sting
     */
    private $water_mark_image;

    /**
     * Position of water mark
     * @var sting
     */
    private $water_mark_position;

    /**
     * A flag to confirm if image is valid
     * @var boolean
     */
    public $is_ok = true;

    /**
     * To store image errors
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor.
     * @param object $image
     * @param array $options
     * @return void.
     */
    public function __construct($image, $options = []) {

        $this->config = Config::get('resize');
        $this->options = $options;

        $this->image = $image;

        $this->validateMimeType();
        $this->validateImageSize();

        if (count($this->errors)) {
            return;
        }

        $this->setWaterMark();
        $this->setNewFileName();

        if (isset($options['old_image'])) {
            $this->old_image = $options['old_image'];
        }
    }

    /**
     * Function validates the mime type
     * @return void
     */
    private function validateMimeType() {

        if (isset($this->options['mime_types'])) {
            $mime_types = $this->options['mime_types'];
        } elseif (isset($this->config['mime_types'])) {
            $mime_types = $this->config['mime_types'];
        } else {
            $mime_types = ['image/png', 'image/jpeg', 'image/gif'];
        }

        if (!in_array($this->image->getMimeType(), $mime_types)) {
            $this->errors[] = $this->image->getClientOriginalName() . ' has invalid mime type';
            $this->is_ok = null;
        }
    }

    /**
     * Function validates image size
     * @return void
     */
    private function validateImageSize() {

        if (!$this->image->getSize()) {

            $this->errors[] = $this->image->getClientOriginalName() . ' had 0 size';
            $this->is_ok = null;
            return;
        }

        if (isset($this->options['max_image_size'])) {
            $max_image_size = $this->options['max_image_size'];
        } elseif (isset($this->config['max_image_size'])) {
            $max_image_size = $this->config['max_image_size'];
        } else {
            $max_image_size = 1;
        }

        $image_size = $this->image->getSize() / 1024 / 1024;

        if ($image_size > $max_image_size) {
            $this->errors[] = $this->image->getClientOriginalName() . ' is bigger than allowed image size';
            $this->is_ok = null;
        }
    }

    /**
     * Function sets water mark options
     * @return void
     */
    private function setWaterMark() {

        if (isset($this->options['water_mark_image'])) {
            $this->water_mark_image = $this->options['water_mark_image'];
        } else {
            $this->water_mark_image = $this->config['water_mark_image'];
        }

        if (isset($this->options['water_mark_position'])) {
            $this->water_mark_position = $this->options['water_mark_position'];
        } elseif (isset($this->config['water_mark_position'])) {
            $this->water_mark_position = $this->config['water_mark_position'];
        } else {
            $this->water_mark_position = 'top-center';
        }
    }

    /**
     * Function generates new file name
     * @return void
     */
    private function setNewFileName() {

        if (isset($this->options['original_name']) || (isset($this->config['original_name']) && $this->config['original_name'])) {
            $this->new_image_name = $this->image->getClientOriginalName();
        } else {
            $this->new_image_name = FileName::get(
                            $this->image->getClientOriginalName()
                            , isset($this->options['filename']) ? $this->options['filename'] : []
            );
        }
    }

    /**
     * This function resizes the image.
     * @param string $target_dir
     * @param integer $width
     * @param integer $height
     * @param string $type .crop, resize or fit.
     * @param string $watermark
     * @return void.
     */
    public function resize($target_dir, $width, $height, $options = []) {

        if (!File::exists($target_dir)) {
            File::makeDirectory($target_dir, 0755, true);
        }

        if (isset($options['resize_type'])) {
            $type = $options['resize_type'];
        } elseif ($this->config['resize_type']) {
            $type = $this->config['resize_type'];
        } else {
            $type = 'canvas';
        }

        $img = Image::make($this->image);

        if ($type == 'crop') {

            $img->crop($width, $height);
        } elseif ($type == 'resize') {

            $img->resize($width, $height);
        } elseif ($type == 'original') {

            $img->resize($img->width(), $img->height());
        } elseif ($type == 'center') {

            $img->resizeCanvas(null, $height, 'center');
        } elseif ($type == 'fit') {

            if ($img->width() > $img->height()) {

                $img->resize($width, null, function ($constraint) {
                    $constraint->aspectratio();
                });
            }

            if ($img->height() > $img->width()) {
                $img->resizeCanvas($width, null, 'center', false);
            }
        } else {

            if ($img->width() > $img->height()) {

                $img->resize($width, null, function ($constraint) {
                    $constraint->aspectratio();
                });

                $img->resize($width, $height);
            }

            if ($img->height() > $img->width()) {

                $img->resizeCanvas($width, 0, 'center', true, '#ffffff');
                $img->resize($width, $height);
            }
        }

        if (isset($options['water_mark'])) {
            $img = $this->watermark($img);
        }

        $img->save($target_dir . '/' . $this->new_image_name);

        if (isset($this->options['old_image']) && File::exists($target_dir . $this->options['old_image'])) {
            File::delete($target_dir . $this->old_image);
        }
    }

    /**
     * Function ads water mark on image
     * @param object $img
     * @return object $img
     */
    public function watermark($img) {

        if (!$this->water_mark_image || !File::exists($this->water_mark_image)) {
            return;
        }

        $watermark = Image::make($this->water_mark_image);

        /** size of the image minus 20 margins */
        $watermark_size = $img->width() - 20;

        /** half of the image size */
        $watermark_size = $img->width() / 2;

        /** 70% less then an actual image (play with this value) */
        $resize_percentage = 60;

        /** Watermark will be $resizePercentage less then the actual width of the image */
        $watermark_size = round($img->width() * ((100 - $resize_percentage) / 100), 2);

        /** resize watermark width keep height auto */
        $watermark->resize($watermark_size, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        /** insert resized watermark to image center aligned */
        return $img->insert($watermark, $this->water_mark_position);
    }

}
