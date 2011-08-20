<?php
class NafuCodeJCodeSVNClient extends NafuCodeClient
{
    protected $baseUri = 'http://joomlacode.org/svn';

    public function checkout($path, $projectData = null)
    {
        set_time_limit(666);//-- Enough ¿

        $baseUri = $this->baseUri.'/'.$projectData->project;

        $svnClient = new phpsvnclient($baseUri, 'anonymous');

        $folder = trim($path, '/');
        $folder = $projectData->url.'/'.$path;
        $folder = "/$folder/";

        $outPath = NAFUCODE_PATH_SOURCES.'/sources/'.$projectData->localdir.'/'.$path;

//         $files = $svnClient->getDirectoryFiles($folder);

//         var_dump($files);

//         return;

        return $svnClient->checkOut($folder, $outPath);
//        $svnClient->createOrUpdateWorkingCopy();
    }//function

    public function checkout2($path, $projectData = null)
    {
        static $data;
        static $subDir;

        if($projectData)
        {
            $data = $projectData;
            $subDir = (string)$data->url;
            $subDir = trim($subDir, '/');
        }
        else
        {
            $subDir = $path;

        }


        //         var_dump($projectData);


        $baseUri = $this->baseUri.'/'.$data->project;

        $svnClient = new phpsvnclient($baseUri, 'anonymous');

        $files = $svnClient->getDirectoryFiles("/$subDir/");

        if( ! $files)
        throw new Exception('phpsvnclient error');

        foreach($files as $file)
        {
            if($file['path'] == $subDir)
            continue;


            if('directory' == $file['type'])
            {
                $this->Checkout($file['path']);

                continue;
            }

            $parts = explode('/', $file['path']);

            $fileName = array_pop($parts);

            $contents = $svnClient->getFile($fileName);

            $path = substr($file['path'], strlen($iniDir) + 1);

            $this->writeFile($path, $contents);
        }

        return true;

        return 'tollsvn';
    }//function
}//class
