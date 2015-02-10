<?php
/**
 * Created by PhpStorm.
 * User: blob
 * Date: 21/11/2014
 * Time: 02:46
 */

namespace Harboi\uploader;

use Harboi\uploader\Exception\RuntimeException;
use Harboi\uploader\Exception\InvalidArgumentException;


class fileUploader {
    //todo multiple upload (is_array($_FILES['files']['name']))
    //todo allow multiple upload
    //todo multiple upload looks like : $_FILES['files']['name'][0]

    public $file;
    public $destinationFolder;
    public $acceptedFormats = array('pdf','jpg');
    public $maxFileSize = 5242880;
    public $fileName = '0';
    public $maxFileNumber = 1;

    function __construct($fieldName, $destinationFolder)
    {
        $this->file = $_FILES[$fieldName];
        $this->destinationFolder = $destinationFolder;
    }

    public function upload()
    {
        $this->check();
        $this->moveUploadedFile($this->file);
        return true;
    }

    public function check()
    {
        $this->isSetFile($this->file);
        $this->isUploadedFile($this->file);
        $this->isNotExceedingMaxFileSize($this->file);
        $this->isAcceptedFormat($this->file);
        return true;
    }

    /**
     * @param array $acceptedFormats
     * @throws Exception\RuntimeException
     */
    public function setAcceptedFormats(array $acceptedFormats)
    {
        if(! is_array($acceptedFormats)) {
            throw new RuntimeException('acceptedFormats must be an array(ext,ext,ext)');
        }
        $this->acceptedFormats = $acceptedFormats;
    }

    /**
     * @param string $fileName
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public function setFileName($fileName)
    {
        $fileName = preg_replace('/[^a-zA-Z0-9_-]+/', '', $fileName);
        if(empty($fileName))
        {
            throw new InvalidArgumentException('Invalid fileName');
        }
        $this->fileName = $fileName;
    }

    /**
     * @param int $maxFileSize
     * @return int
     */
    public function setMaxFileSize($maxFileSize)
    {
        $this->maxFileSize = intval($maxFileSize);
    }

    /**
     * @param int $maxFileNumber
     * @return string
     */
    public function setMaxFileNumber($maxFileNumber)
    {
        $this->maxFileNumber = intval($maxFileNumber);
    }

    /**
     * @param array $file
     * @throws Exception\InvalidArgumentException
     */
    private function isSetFile($file)
    {
        //todo : safer check (check array : name, type, tmp_name, ...)
        if(!isset($file) || empty($file))
        {
            throw new InvalidArgumentException('Le champ fichier est vide');
        }
    }

    /**
     * @param $file
     * @throws Exception\RuntimeException
     */
    private function isNotExceedingMaxFileSize($file)
    {
        if (intval($file['size']) > intval($this->maxFileSize))
        {
            throw new InvalidArgumentException('Le fichier est trop gros');
        }
    }

    /**
     * @param $file
     * @throws Exception\InvalidArgumentException
     */
    private function isAcceptedFormat($file)
    {
        if (! in_array($this->getFileExtension($file), $this->acceptedFormats))
        {
            throw new InvalidArgumentException('Format de fichier invalide : '.$this->getFileExtension($file));
        }
    }

    /**
     * @param array $file
     * @return string
     */
    public function getFileExtension($file)
    {
        $ext = new \SplFileInfo($file['name']);
        return strtolower($ext->getExtension());
    }

    /**
     * @param $file
     * @throws Exception\RuntimeException
     */
    private function isUploadedFile($file)
    {
        if (!is_string($file['tmp_name']) || !is_uploaded_file($file['tmp_name']))
        {
            throw new RuntimeException('Unable to upload');
        }
    }

    /**
     * @param $file
     * @throws Exception\RuntimeException
     */
    private function moveUploadedFile($file)
    {
        if (! move_uploaded_file($file['tmp_name'], $this->destinationFolder.'/'.$this->fileName.'.'.$this->getFileExtension($file)))
        {
            throw new RuntimeException('Unable to move uploaded file');
        }
    }
}
