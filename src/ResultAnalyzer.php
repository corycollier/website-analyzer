<?php

namespace WebsiteAnalyzer;

class ResultAnalyzer
{
    public function analyze(Result $subject)
    {
        $body = $subject->getBody();
        if ($this->isStatic($body)) {
            $subject->setType('static');
        }
        if ($this->isWordpress($body)) {
            $subject->setType('wordpress');
        }
        if ($this->isDrupal($body)) {
            $subject->setType('drupal');
        }
        return $this;
    }

    protected function isDrupal($contents) {
      $result = preg_match('/Drupal/', $contents);
      return $result;
    }

    protected function isWordpress($contents) {
      $result = preg_match('/Wordpress/', $contents);
      return $result;
    }

    protected function isStatic($contents) {
      $result = preg_match('/assets\/css/', $contents);
      return $result;
    }
}
