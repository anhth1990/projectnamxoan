<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Forms;

use App\Http\Forms\BaseForm;
class FileForm extends BaseForm{
    private $type;
    private $extension;
    private $name;
    private $file;
    private $dataFile;
    private $folderName;
    private $fileLog;
            
    function getType() {
        return $this->type;
    }

    function getExtension() {
        return $this->extension;
    }

    function getName() {
        return $this->name;
    }

    function getFile() {
        return $this->file;
    }

    function setType($type) {
        $this->type = $type;
    }

    function setExtension($extension) {
        $this->extension = $extension;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setFile($file) {
        $this->file = $file;
    }

    function getDataFile() {
        return $this->dataFile;
    }

    function setDataFile($dataFile) {
        $this->dataFile = $dataFile;
    }

    function getFolderName() {
        return $this->folderName;
    }

    function setFolderName($folderName) {
        $this->folderName = $folderName;
    }

    function getFileLog() {
        return $this->fileLog;
    }

    function setFileLog($fileLog) {
        $this->fileLog = $fileLog;
    }


            
}

