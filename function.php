<?php



/**
 * Includes all available files inside the provided path
 *
 * @param string $rootPath
 * @param array $excludeFiles
 */
function includeAll($rootPath = "",$excludeFiles = array()){
    $rootPath = rtrim($rootPath,"/");
    if(file_exists($rootPath)){
        if(is_dir($rootPath)){
            $files = scandir($rootPath);
            foreach ($files as $key => $file){
                if($key > 1){
                    if(is_dir($file)){
                        includeAll($rootPath.DIRECTORY_SEPARATOR.$file, $excludeFiles);
                    }else{
                        if(!in_array($file,$excludeFiles)) {
                            include_once $rootPath . DIRECTORY_SEPARATOR . $file;
                        }
                    }
                }
            }
        }else{
            include_once $rootPath;
        }
    }
}


/**
 * Extracts the value from the array with the key.
 * Will return the default value if the key is not available in the array.
 *
 * @param $key
 * @param array $from
 * @param string $default
 * @return mixed|string
 */
function extractFromArray($key, array $from, $default = "")
{
    return array_key_exists($key, $from) ? $from[$key] : $default;
}