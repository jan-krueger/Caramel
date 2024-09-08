<?php

namespace Caramel\Core\Exception;

use ReflectionClass;
use ReflectionMethod;

class Handler
{

    public function render(\Throwable $exception)
    {   
        
        $name = get_class($exception);
        $message = $exception->getMessage();

        $traces = [];
        $files = [];

        $current = $exception;
        while($current !== null)
        {
            $trace = $current->getTrace();
            array_unshift($trace, [
                'file' => $current->getFile(),
                'line' => $current->getLine(),
                'class' => get_class($current),
            ]);

            // --- map file names
            for($i = 0; $i < count($trace); $i++)
            {

                // --- no file? 
                if(!isset($trace[$i]['file']))
                {
                    $trace[$i]['file'] = (new ReflectionClass($trace[$i]['class']))->getFileName();
                }
                
                // --- no line? 
                if(!isset($trace[$i]['line']))
                {
                    $trace[$i]['line'] = (new ReflectionMethod($trace[$i]['class'], $trace[$i]['function']))->getStartLine();
                }

                $trace[$i]['file_clean'] = self::clean_filename($trace[$i]['file']);

                $key = sprintf('%s-%d', $trace[$i]['file'], $trace[$i]['line']);
                $trace[$i]['key'] = $key;
                $files[$key] = [
                    'file' => $trace[$i]['file'],
                    'file_clean' => $trace[$i]['file_clean'],
                    'line' => $trace[$i]['line'],
                    'code' => self::extract_source_code($trace[$i]['file'], $trace[$i]['line'])
                ];
            }

            $traces[] = $trace;
            $current = $current->getPrevious();
            
        }

        require base_path('src/Core/Exception/ExceptionView.view.php');
    }

    private static function extract_source_code(string $file, int $error_line, $window_size = 15): array
    {
        $error_file_content = explode(PHP_EOL, htmlspecialchars(file_get_contents($file)));
        $lines = [];
        for($i = max(0, $error_line - $window_size - 1); $i < min(count($error_file_content), $error_line + $window_size); $i++)
        {
            $lines[$i + 1] = $error_file_content[$i];
        }
        
        return $lines;
    }

    private static function clean_filename(string $file_name): string
    {
        return str_replace(realpath(base_path()) . '/', "", $file_name);
    }

}