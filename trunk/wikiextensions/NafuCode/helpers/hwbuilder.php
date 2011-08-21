<?php
/**
 *
 * Enter description here ...
 * @author elkuku
 *
 */

$a = 1;

class HWBuilder
{
    public static function tree($input, $projectOnly = false)
    {
        $parts = explode('/', $input);

        array_shift($parts);

        $project = $parts[0];

        if(count($parts > 1))
        $projectSub = $parts[1];

        $base = NAFUCODE_PATH_SOURCES.'/sources/'.$project;

        if($projectSub)
        {
            $base .= '/'.$projectSub;

            if( ! is_dir($base))
            {
                $msg = '';
                $msg .= 'Project dir not found for tree';
                $msg =(DBG_NAFUCODE) ? ' - '.$base : '';

                throw new Exception($msg);
            }
        }

        if( ! file_exists($base.'/links'))
        throw new Exception('Link list not found for tree');

        $lines = file($base.'/links');

        $output = array();

        $items = array();

        $i = 0;

        foreach ($lines as $line)
        {
            $line = trim($line);

            if( ! $line
            || 0 === strpos($line, '#'))
            continue;

            $parts = explode('/', $line);

            $projectNum = array_shift($parts);

            if($projectOnly
            && $projectNum != $projectSub)
            continue;

            $fileName = implode('/', $parts);

            $XfileName = array_pop($parts);

            $s = implode("']['", $parts);

            /*
             * eval is EVAL :|
            */

            eval("if( ! isset(\$items['".$s."'])) \$items['".$s."'] = array();");

            eval("\$items['".$s."'][] = '$projectNum/$fileName';");
        }//foreach


        $lines = self::drawTreeLine($items);

        $output[] = '{| class="dirtree"';
        $output[] = '|-';
        $output[] = '|{{file|hallowelt_paket|zip}}';

        $output = array_merge($output, $lines);

        $output[] = '|-';
        $output[] = '|{{tree|L}}{{file|[[#ADMIN/hallowelt.xml|hallowelt.xml]]|xml}}';

        $output[] = '|}';

        $output = implode("\n", $output);

        return $output;

    }//function

    private static function drawTreeLine($items, $level = 0, $levels = array())
    {
        static $output = array();

        static $levels = array();

        $fileCount = 0;
        $folderCount = 0;

        foreach($items as $folder => $subItems)
        {
            if(is_array($subItems))
            {
                $output[] = '|-';

                $s = '';
                $s .= '|';

                if($level == 0)
                {
                    $s .= '{{tree|T}}';
                }
                else
                {
                    $s .= '{{tree|V}}';

                    $folderCount ++;

                    $s .= self::treeDetectLevel($levels, 0, $folderCount);
                }

                $output[] = $s.'{{folder|'.$folder.'}}';

                $levels[$level] = count($subItems);

                self::drawTreeLine($subItems, $level + 1, $levels);

                unset($levels[$level]);

                continue;
            }

            $fileCount ++;

            $levels[$level - 1] = self::treeFileCount($items);

            $output[] = '|-';

            $s = '';

            $s .= '|';
            $s .= '{{tree|V}}';

            $s .= self::treeDetectLevel($levels, $fileCount, $folderCount);

            $parts = explode('/', $subItems);

            $chapter = sprintf('%02d', array_shift($parts));
            $scope = strtoupper(array_shift($parts));
            $file = array_pop($parts);
            $ext = substr($file, strrpos($file, '.') + 1);
            $path = implode('/', $parts);

            $filePath =($path) ? $path.'/'.$file : $file;

            $baseUriHW = 'Joomla!_Programmierung/Programmierung/Hallo_Welt_J1.6';

            $output[] = $s."{{file|[[$baseUriHW/Teil_$chapter#$scope/$filePath|$file]]|$ext}}";
        }//foreach

        return $output;
    }//function

    private static function treeFileCount($items)
    {
        $i = 0;

        foreach ($items as $item)
        {
            if( ! is_array($item))
            $i ++;
        }//foreach

        return $i;
    }//function

    private static function treeDetectLevel($levels, $fileCount = 0, $folderCount = 0)
    {
        $s = '';
        $i = 1;

        foreach ($levels as $l)
        {
            if($l > 1)
            {
                if($i == count($levels))
                {
                    if($fileCount)
                    {
                        if($fileCount == $l)
                        {
                            $s .= '{{tree|L}}';
                        }
                        else
                        {
                            $s .= '{{tree|T}}';
                        }
                    }
                    else
                    {
                        $s .= '{{tree|T}}';
                    }
                }
                else
                {
                    if($folderCount)
                    {
                        if($folderCount == $l)
                        {
                            $s .= '{{tree|L}}';
                        }
                        else
                        {
                            $s .= '{{tree|V}}';
                        }
                    }
                    else
                    {
                        $s .= '{{tree|V}}';
                    }
                }
            }
            else
            {
                if($i == count($levels))
                {
                    $s .= '{{tree|L}}';

                }
                else
                {
                    $s .= '{{tree|S}}';
                }
            }

            $i ++;
        }//foreach

        return $s;
    }//function



    /**
     * Experimental section =;)
     */



    /**
     *
     * Enter description here ...
     * @param unknown_type $input
     * @param unknown_type $projectOnly
     * @throws Exception
     * @return string
     */
    public static function tree2($input, $projectOnly = false)
    {
        $parts = explode('/', $input);

        array_shift($parts);

        $project = $parts[0];

        if(count($parts > 1))
        $projectSub = $parts[1];

        $base = NAFUCODE_PATH_SOURCES.'/sources/'.$project;

        if($projectSub)
        {
            $base .= '/'.$projectSub;

            if( ! is_dir($base))
            {
                $msg = '';
                $msg .= 'Project dir not found for tree';
                $msg =(DBG_NAFUCODE) ? ' - '.$base : '';

                throw new Exception($msg);
            }
        }

        if( ! file_exists($base.'/links'))
        throw new Exception('Link list not found for tree');

        $lines = file($base.'/links');

        $output = array();

        $items = array();

        $i = 0;

        foreach ($lines as $line)
        {
            $line = trim($line);

            if( ! $line
            || 0 === strpos($line, '#'))
            continue;

            $parts = explode('/', $line);

            $projectNum = array_shift($parts);

            if($projectOnly
            && $projectNum != $projectSub)
            continue;

            $fileName = implode('/', $parts);

            $XfileName = array_pop($parts);

            $s = implode("']['", $parts);

            /*
             * eval is EVAL :|
            */

            eval("if( ! isset(\$items['".$s."'])) \$items['".$s."'] = array();");

            eval("\$items['".$s."'][] = '$projectNum/$fileName';");
        }//foreach

        //$output[] = print_r($items, 1);

        //        $output[] = '== Das Installationspaket ==';
        //        $output[] = 'Der Inhalt des [[Teil_01#Ein_Installationspaket_erstellen|Codeverzeichnisses]] außerhalb Ihrer Komponente.';

        $lines = self::drawTreeLine2($items);
        //$output[] = print_r($lines, 1);

        $output[] = '{{#tree:';
        //         $output[] = '|-';
        $output[] = 'hw';//{{file|hallowelt_paket|zip}}';

        $output = array_merge($output, $lines);

        //         $output[] = '|-';
        $output[] = '* hallowelt.xml';//{{tree|L}}{{file|[[#ADMIN/hallowelt.xml|hallowelt.xml]]|xml}}';

        $output[] = '}}';

        $output = implode("\n", $output);

        // $output = $this->parser->recursiveTagParse($output);
        return $output;

    }//function


    private static function drawTreeLine2($items, $level = 0, $levels = array())
    {
        static $output = array();

        static $levels = array();

        $fileCount = 0;
        $folderCount = 0;

        foreach ($items as $folder => $subItems)
        {
            if(is_array($subItems))
            {
                //          $output[] = '|-';

                $s = '';
                //        $s .= '|';

                if($level == 0)
                {
                    $s .= '*';//{{tree|T}}';
                }
                else
                {
                    $s .= '*';//{{tree|V}}';


                    $folderCount ++;
                    $s .= self::treeDetectLevel2($levels, 0, $folderCount);
                }

                $output[] = $s.$folder;//'{{folder|'.$folder.'}}';

                $levels[$level] = count($subItems);

                self::drawTreeLine2($subItems, $level + 1, $levels);

                unset($levels[$level]);

                continue;
            }

            $fileCount ++;

            $levels[$level - 1] = self::treeFileCount($items);

            //             $output[] = '|-';

            $s = '';
            //             $s .= '|';
            $s .= '*';//{{tree|V}}';

            $s .= self::treeDetectLevel2($levels, $fileCount, $folderCount);

            $parts = explode('/', $subItems);

            $chapter = sprintf('%02d', array_shift($parts));
            $scope = strtoupper(array_shift($parts));
            $file = array_pop($parts);
            $ext = substr($file, strrpos($file, '.') + 1);
            $path = implode('/', $parts);

            $filePath =($path) ? $path.'/'.$file : $file;

            $baseUriHW = 'Joomla!_Programmierung/Programmierung/Hallo_Welt_J1.6';
            //    $output[] = $s."{{file|[[{$this->baseUriHW}/Teil_$chapter#$scope/$filePath|$file]]|$ext}}";
            $output[] = $s."[[$baseUriHW/Teil_$chapter#$scope/$filePath|$file]]";//|$ext";
        }//foreach

        return $output;
    }//function

    private static function treeDetectLevel2($levels, $fileCount = 0, $folderCount = 0)
    {
        $s = '';
        $i = 1;

        foreach ($levels as $xxx => $l)
        {
            $s .= '*';//{{tree|V}}';

            $i ++;
        }//foreach

        return $s;
    }//function

}//class
