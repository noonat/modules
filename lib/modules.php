<?php

namespace modules;

spl_autoload_register(function($className) {
    loader()->load($className);
});

function add($filename, $patterns) {
    if (!is_string($filename))
        throw new \Exception('$filename argument must be string');
    if (is_string($patterns))
        $patterns = array($patterns);
    else if (!is_array($patterns))
        throw new \Exception('$patterns argument must be string or array');
    $fullPath = implode('/', fromStack());
    $fullFilename = empty($fullPath) ? $fullPath.'/'.$filename : $filename;
    $module = new Module($filename, realpath($fullFilename), $patterns);
    loader()->addModule($module);
    return $module;
}

function from($path, $func) {
    array_push(_pathStack(), $path);
    $func();
    array_pop(_pathStack());
    return $path;
}

function loader($newLoader=null) {
   static $loader=null;
   if (!$loader || $newLoader)
      $loader = $newLoader ?: new Loader();
   return $loader;
}

function &_pathStack($newStack=null) {
    static $stack = array();
    if (!is_null($newStack))
        $stack = $newStack;
    return $stack;
}

class Loader {
    public $modules = array();

    function addModule($module) {
        $this->modules[$module->name] = $module;
    }

    function findModule($moduleName) {
        if (isset($this->modules[$moduleName]))
            return $this->modules[$moduleName];
        // try it with a .php extension
        $moduleName .= '.php';
        if (isset($this->modules[$moduleName]))
            return $this->modules[$moduleName];
        return null;
    }

    function findModuleMatching($className) {
        foreach ($this->modules as $module) {
            if ($module->matches($className))
                return $module;
        }
        return null;
    }

    function load($moduleName) {
        $module = $this->findModule($moduleName);
        if (!$module)
            return null;
        $module->load();
        return $module;
    }

    function loadClass($className) {
        $module = $this->findModuleMatching($className);
        if (!$module)
            return null;
        $module->load();
        if (!class_exists($className, false))
            throw new \Exception("class {$className} matched module " .
                                 "{$module->name}, but class didn't exist " .
                                 "after loading module");
        return $module;
    }
}

class Module {
    public $filename;
    public $name;
    public $loaded;
    public $patterns;

    function __construct($name, $filename, $patterns) {
        $this->filename = $filename;
        $this->loaded = false;
        $this->name = $name;
        $this->patterns = (array)$patterns;
    }

    function load() {
        if (!$this->loaded) {
            require_once $this->filename;
            $this->loaded = true;
        }
        return true;
    }

    function matches($className) {
        foreach ($this->patterns as $pattern) {
            if (fnmatch($pattern, $className))
                return true;
        }
        return false;
    }
}
