<?php

namespace App\Controller;

use Light\Front;

class Fs extends \Light\Controller
{
    public function createFolder()
    {
        $config = Front::getInstance()->getConfig();
        mkdir(realpath($config['fs']['path']) . $this->getRequest()->getPost('path') . '/' . $this->getRequest()->getPost('name'));

        return [];
    }

    public function deleteFolder()
    {
        $config = Front::getInstance()->getConfig();
        $dirPath = realpath($config['fs']['path']) . $this->getRequest()->getGet('path');

        self::deleteDir($dirPath);

        return [];
    }

    public function uploadFileApi()
    {
        $files = $this->mapFiles($_FILES);

        $errors = [];

        $config = Front::getInstance()->getConfig();

        $copiedFiles = [];

        foreach ($files as $file) {

            try {

                $folder = realpath($config['fs']['path']) . $this->getRequest()->getPost('path') . '/' . $this->getRequest()->getPost('folder');

                if (!file_exists($folder)) {
                    mkdir($folder);
                }

                $copiedFiles[] = $config['fs']['url'] . $this->getRequest()->getPost('path') . '/' . $this->getRequest()->getPost('folder') . '/' . $file['name'];
                copy($file['tmpName'], $folder . '/' . $file['name']);
            }
            catch (\Exception $e) {
                $errors[] = 'File "' . $file['name'] . '" was not uploaded. Reason: ' . $e->getMessage();
            }
        }

        return [
            'success' => count($errors) == 0,
            'files' => $copiedFiles,
            'errors' => $errors
        ];
    }

    public function uploadFile()
    {
        $files = $this->mapFiles($_FILES);

        $errors = [];

        $config = Front::getInstance()->getConfig();

        foreach ($files as $file) {

            try {
                copy($file['tmpName'], realpath($config['fs']['path']) . $this->getRequest()->getPost('path') . '/' . $file['name']);
            }
            catch (\Exception $e) {
                $errors[] = 'File "' . $file['name'] . '" was not uploaded.';
            }
        }

        return ['errors' => $errors];
    }

    public function deleteFile()
    {
        $config = Front::getInstance()->getConfig();
        $dirPath = realpath($config['fs']['path']) . $this->getRequest()->getGet('path');

        unlink($dirPath);

        return [];
    }

    public function uploadByUrl()
    {
        $name = md5(microtime());

        $file = file_get_contents($this->getRequest()->getPost('url'));

        $config = Front::getInstance()->getConfig();

        $filePath = realpath($config['fs']['path']) . $this->getRequest()->getPost('path') . '/';

        file_put_contents($filePath . $name, $file);

        $mimes = new \Mimey\MimeTypes();
        $extension = $mimes->getExtension(mime_content_type($filePath . $name));

        rename($filePath . $name, $filePath . $name . '.' . $extension);

        return [];
    }

    /**
     * @param string $dirPath
     */
    public static function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new \InvalidArgumentException("$dirPath must be a directory");
        }

        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }

        $files = glob($dirPath . '*', GLOB_MARK);

        foreach ($files as $file) {

            if (is_dir($file)) {
                self::deleteDir($file);
            }
            else {
                unlink($file);
            }
        }

        rmdir($dirPath);
    }

    /**
     * @param array $files
     * @return array
     */
    public function mapFiles(array $files = [])
    {
        return array_map(function($name, $type, $tmpName, $error, $size){
            return [
                'name' => $name,
                'type' => $type,
                'tmpName' => $tmpName,
                'error' => $error,
                'size' => $size
            ];

        },  $_FILES['files']['name'],
            $_FILES['files']['type'],
            $_FILES['files']['tmp_name'],
            $_FILES['files']['error'],
            $_FILES['files']['size']
        );
    }
}
