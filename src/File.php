<?php

namespace WebsiteAnalyzer;

class File
{
    const ERR_FILE_NOT_FOUND = 'The file [%s] does not exist';
    protected $filename;
    protected $data;

    public function __construct($filename, $data = '')
    {
        $this->filename = $filename;
        $this->data = $data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function save()
    {
        file_put_contents($this->getFilename(), $this->getData());
        return $this;
    }

    public function delete()
    {
        $filename = $this->getFilename();
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    public function load()
    {
        $filename = $this->getFilename();
        if (!file_exists($filename)) {
            throw new RuntimeException(sprintf(self::ERR_FILE_NOT_FOUND, $filename));
        }
        $this->setData(file_get_contents($this->getFilename()));
        return $this;
    }

    public function parse()
    {
        $data = $this->getData();
        if (!@unserialize($data)) {
            $this->setData(explode(PHP_EOL, $data));
        } else {
            $this->unserialize();
        }
        return $this;
    }

    function serialize()
    {
        $data = $this->getData();
        if ($data) {
            $this->setData(serialize($data));
        }
        return $this;
    }

    function unserialize()
    {
        $data = trim($this->getData());
        if ($data) {
            $this->setData(unserialize($data));
        }
        return $this;
    }

}
