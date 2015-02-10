<?php
/**
 * Created by PhpStorm.
 * User: blob
 * Date: 24/11/2014
 * Time: 15:24
 */

namespace Harboi\Uploader;

use Harboi\Uploader\fileUploader;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Harboi\Uploader\Exception\RuntimeException;
use Harboi\Uploader\Exception\InvalidArgumentException;

class imageUploader {

    public $maxSize = array(2048,2048);
    public $thumbSize = array(320,240);
    public $useThumb = false;

    function __construct(fileUploader $upload)
    {
        $this->upload = $upload;
        $this->imagine = new Imagine();
    }

    public function save()
    {
        $this->saveImage();
        if($this->useThumb){
            $this->saveThumb();
        }

    }

    public function saveThumb()
    {
        $this->upload->check();
        $this->imagine->open($this->upload->file["tmp_name"])
            ->thumbnail(new Box($this->thumbSize[0],$this->thumbSize[1]), 'inset')
            ->save($this->upload->destinationFolder.'/thumb/'.$this->upload->fileName.'.'.$this->upload->getFileExtension($this->upload->file));
    }

    public function saveImage()
    {
        $this->upload->check();
        $this->imagine->open($this->upload->file["tmp_name"])
            ->thumbnail(new Box($this->maxSize[0],$this->maxSize[1]), 'inset')
            ->save($this->upload->destinationFolder.'/'.$this->upload->fileName.'.'.$this->upload->getFileExtension($this->upload->file));
    }

    /**
     * @param array $maxSize
     * @throws Exception\InvalidArgumentException
     * @return array
     */
    public function setMaxSize(array $maxSize)
    {
        if(! is_array($maxSize) && count($maxSize) === 2) {
            throw new InvalidArgumentException('maxSize must be an array(width,height)');
        }
        $this->maxSize = array(intval($maxSize[0]),intval($maxSize[1]));
    }

      /**
     * @param array $thumbSize
     * @throws Exception\InvalidArgumentException
     * @return array
     */
    public function setThumbSize(array $thumbSize)
    {
        if(! is_array($thumbSize) && count($thumbSize) === 2) {
            throw new InvalidArgumentException('thumbSize must be an array(width,height)');
        }
        $this->thumbSize = array(intval($thumbSize[0]),intval($thumbSize[1]));
    }
}
