<?php

namespace App\Traits;

use SplFileObject;

trait CSVReader
{
    public function read($path_to_file)
    {
        $file = new SplFileObject($path_to_file);
        $columns = [];
        $rows = collect();
        $i = 0;
        // Loop until we reach the end of the file.
        while (!$file->eof()) {
            $line = trim($file->fgets());
            if (! $line) {
                continue;
            }
            $line = explode(',', $line);
            if ($i == 0) {
                array_push($columns, ...$line);
            } else {
                $ar = [];
                for ($i=0; $i < count($columns); $i++) { 
                    $ar[$columns[$i]] = str_replace('"', '', $line[$i]);
                }
                $rows->push($ar);
            }
            // Echo one line from the file.
            $i++;
        }
        $file = null;
        return $rows;
    }
}