<?php

namespace mf\utils;

class ClassLoader extends AbstractClassLoader{

  public function getFilename(string $classname) : string{
    /* 
    *  construction du chemin pour charger les classes 
    *  à partir de leurs noms
    */
    $url = str_replace('\\', DIRECTORY_SEPARATOR, $classname).'.php';
    return $url;
  }

  public function makePath(string $filename) : string{
    return $this->prefix.DIRECTORY_SEPARATOR.$filename;
  }

  public function loadClass(string $classname){
    $url = $this->getFilename($classname);
    $url = $this->makePath($url);
    if(file_exists($url)){
      require_once($url);
    }
  }
}
