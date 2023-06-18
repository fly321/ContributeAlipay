<?php

namespace App\Scan;

class Scanning
{
    // 扫描注解
    protected string $dir;
    protected array $filterDir = [];
    protected array $class = [];
    protected array $method = [];
    public static array $annotation = [];

    public static $instance = null;

    private function __construct()
    {
        static::$annotation = [];
        $this->dir = config("annotationv1.scanDir");
        $this->filterDir = config("annotationv1.filterDir");
    }

    public static function getInstance(): self
    {
        if (is_null(static::$instance) || !(static::$instance instanceof self)) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    public function scan()
    {
        $this->scanDir($this->dir);
    }

    private function scanDir(mixed $dir)
    {
        // 过滤目录
        if (in_array($dir, $this->filterDir)) {
            return;
        }
        // 扫描目录 兼容windows
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->scanDir($path);
            } else {
                $this->scanFile($path);
            }
        }
    }

    private function scanFile(string $path)
    {
        $content = file_get_contents($path);
        $this->scanClass($content);
    }

    // 包括类名和命名空间
    private function scanClass(bool|string $content)
    {
        $pattern = "/namespace\s+(.+);/";
        preg_match($pattern, $content, $matches);
        if (isset($matches[1])) {
            $namespace = $matches[1];
        } else {
            return;
        }
        $pattern = "/class\s+(\w+)/";
        preg_match($pattern, $content, $matches);
        if (isset($matches[1])) {
            $class = $matches[1];
        } else {
            return;
        }

        // 获取类的注解
        $class = $namespace . "\\" . $class;
        // 获取类的注解
        $this->getClassAnnotation($class);

    }



    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return array
     */
    public function getMethod(): array
    {
        return $this->method;
    }

    /**
     * @param array $method
     */
    public function setMethod(array $method): void
    {
        $this->method = $method;
    }

    private function getClassAnnotation(string $class)
    {
        $reflectionClass = new \ReflectionClass($class);
        $n = $reflectionClass->getAttributes();
        foreach ($n as $item) {
            // 过滤掉是注解类
            if ($item->getName() == "Attribute") {
                continue;
            }
            static::$annotation['_c'][$item->getName()][$class] = $item;
        }
        // 读取所有方法
        $methods = $reflectionClass->getMethods();
        foreach ($methods as $method) {
            $this->getMethodAnnotation($method, $class);
        }
    }

    private function getMethodAnnotation(\ReflectionMethod $method, string $class)
    {
        $n = $method->getAttributes();
        foreach ($n as $item) {
            if ($item->getName() == "Attribute") {
                continue;
            }
            static::$annotation['_m'][$item->getName()][$class][$method->getName()] = $item;
        }
    }



    /**
     * @param array $annotation
     */
    public function setAnnotation(array $annotation): void
    {
        $this->annotation = $annotation;
    }


}
