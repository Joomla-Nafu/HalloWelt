<?php
class NafuCodeHelper
{
    private $input;
    private $argv;
    private $parser;

    private $sysPath;

    /**
     *
     * Enter description here ...
     * @param unknown_type $basePath
     * @param unknown_type $input
     * @param unknown_type $argv
     * @param unknown_type $parser
     */
    function __construct($input = '', $argv = array(), $parser = null)
    {
        $this->input = $input;
        $this->argv = $argv;
        $this->parser = $parser;

        $this->sysPath = dirname(__FILE__);
    }//function

    /**
     *
     * Enter description here ...
     * @throws Exception
     */
    public function display()
    {
        global $IP;

        $dirtyPath = $this->input;

        $cleanPath = str_replace('..', '', $dirtyPath);

        //@todo - clean the path even more =;)

        $path = NAFUCODE_PATH_SOURCES.'/sources/'.$cleanPath;

        if( ! file_exists($path))
        throw new Exception('NafuCode Source not found :( '.$path);//@todo debug - remove values

        //-- Get the file
        $lines = file($path);

        $options = array();
        if(isset($this->argv['options']))
        {
            $options = explode(',', $this->argv['options']);
        }

        $cleanLines = array();

        $startLine = 0;
        $endLine = 0;

        if(isset($this->argv['lines']))
        {
            $parts = explode('-', $this->argv['lines']);

            if(2 == count($parts))
            {
                $startLine = $parts[0];
                $endLine = $parts[1];
            }
        }

        foreach ($lines as $i => $line)
        {
            if($startLine && $i + 1 < $startLine)
            continue;

            if($endLine && $i + 1 > $endLine)
            break;

            $line = rtrim($line);
            $cleanLines[] = $line;
        }//foreach

        $code = implode("\n", $cleanLines);

        if( ! class_exists('GeSHi'))
        {
            $path = $IP.'/extensions/SyntaxHighlight_GeSHi/geshi/geshi.php';

            if( ! file_exists($path))
            return '<pre>'.htmlentities($code).'</pre>';

            require_once $path;

            if( ! class_exists('GeSHi'))
            return '<pre>'.htmlentities($code).'</pre>';
        }

        $ext = substr($path, strrpos($path, '.'));

        $geshi = new GeSHi($code, $ext);

        //         $geshi->set_header_content($cleanPath);

        $geshi->enable_line_numbers(GESHI_NO_LINE_NUMBERS);

        if(in_array('linenumbers', $options)
        || $startLine)
        {
            if(in_array('fancy', $options))
            {
                $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 2);
                $geshi->set_line_style('background: #fff;', 'background: #f0f0f0;', 2);
            }
            else
            {
                $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS, 2);
            }
        }

        if($startLine)
        {
            $geshi->start_line_numbers_at($startLine);
        }

        if(isset($this->argv['highlight']))
        {
            $highlights = explode(',', $this->argv['highlight']);

            foreach ($highlights as $i => $highlight)
            {
                $highlights[$i] = ($startLine)
                ? intval($highlight- $startLine + 1)
                : intval($highlight);
            }

            $geshi->highlight_lines_extra($highlights);
        }

        if(function_exists('setupGeSHiForJoomla'))
        setupGeSHiForJoomla($geshi);

        return $geshi->parse_code();
    }//function

    public function updateProjectFromRequest()
    {
        global $wgRequest;

        $project = $this->retrieveProject();


        $dir = $wgRequest->getText('dir');

        $dir = (int)$dir;//@todo: less restrictive :P

        $this->input = 'update/'.(string)$project->localdir;
        $this->input .=($dir) ? '/'.$dir : '';

        return $this->update();
    }

    /**
     *
     * Enter description here ...
     * @throws Exception
     * @return string
     */
    public function update()
    {
        $parts = explode('/', $this->input);

        array_shift($parts);//-- Remove "update"

        if( ! count($parts))
        throw new Exception('Invalid options - Please specify as: "update/PROJECT[/SUBDIR]"');

        $this->project = $parts[0];

        if(count($parts) > 1)
        $this->projectSub = $parts[1];

        array_shift($parts);

        $subPath = implode('/', $parts);

        if( ! $this->project)
        throw new Exception('Please specify as: "update/PROJECT[/SUBDIR]"');

        if( ! file_exists(NAFUCODE_PATH_SOURCES.'/sources/'.$this->project))//@todo sources must exist
        throw new Exception('NafuCode: Invalid Project Dir ');

        $projectData = $this->getProjectData($this->project);

        $this->checkAccess($projectData);

        // var_dump($projectData);

        $vName = (string)$projectData->versioncontrol;

        require_once $this->sysPath.'/clients/client.php';

        if(false !== strpos($vName, 'svn'))
        require_once $this->sysPath.'/svnclient/phpsvnclient.php';

        $client = NafuCodeClient::getClient($vName);

        //      var_dump($client);


        //         $time_start = microtime(true);

        $client->checkout($subPath, $projectData);

        //         $time_end = microtime(true);
        //         $time = $time_end - $time_start;

        //         echo "Did nothing in $time seconds\n";

        return 'NafuCode sources have been updated :)';
        //         require_once $this->IP.'/extensions/NafuCode/svnclient/phpsvnclient.php';

        //         if($this->checkout())
        //         {
        //             return 'NafuCode sources has been updated :)';
        //         }
    }//function

    /**
     *
     * Enter description here ...
     * @param unknown_type $projectData
     * @throws Exception
     */
    private function checkAccess($projectData)
    {
        global $wgUser;

        $userName = $wgUser->getName();

        $maintainers = (string)$projectData->maintainers;

        if( ! $maintainers)
        throw new Exception('No maintainers set for project');

        $list = explode(',', $maintainers);

        if( ! in_array($userName, $list))
        throw new Exception(sprintf('Sorry, %s, you are not allowed to perform this action :(', $userName));
    }//function

    /**
     *
     * Enter description here ...
     */
    public function listProjects()
    {
        $projectList = $this->getProjectList();

        if( ! $projectList)
        return 'No projects found :(';

        $html = '';

        $html .= '<ol>';

        foreach($projectList as $project)
        {
            $href = '?task=project&project='.$project->localdir;

            $html .= '<li>';
            $html .= '<b><big><a href="'.$href.'">'.$project->localdir.'</a></big></b><br />';

            foreach ($project as $k => $v)
            {
                $html .= '<b>'.$k.'</b>: '.$v.'<br />';
            }//foreach

            $html .= '</li>';
        }//foreach

        $html .= '</ol>';

        return $html;
    }

    private function retrieveProject()
    {
        global $wgRequest;

        $project = $wgRequest->getText('project');

        $projectList = $this->getProjectList();

        if( ! array_key_exists($project, $projectList))
        throw new Exception('Unknown project :(');

        return $projectList[$project];
    }//function

    public function listProject()
    {
        $p = $this->retrieveProject();

        $isHW =(0 === strpos($p->localdir, 'hw')) ? true : false;

        $html = '';

        $html .= '<h2>'.$p->name.'</h2>';

        $html .= '<div style="float: right; padding: 0.5em;'
        .' border: 1px solid silver; border-radius: 10px;"><h3>Info</h3>';

        foreach ($p as $k => $v)
        {
            $html .= '<b>'.$k.'</b>: '.$v.'<br />';
        }//foreach

        $html .= '</div>';

        $path = NAFUCODE_PATH_SOURCES.'/sources/'.$p->localdir;

        if( ! is_dir($path))
        {
            $html .= '<p style="color: orange;">Sources not found :(';
            $html .=(DBG_NAFUCODE) ? '<br />'.$path : '';
            $html .= '</p>';

            return $html;
        }

        $html .=(DBG_NAFUCODE) ? $path.'<br />' : '';

        $baseLink = '?task=update&project='.$p->localdir;

        $html .= '<a href="'.$baseLink.'">Update project</a>';

        $html .= '<ul>';

        foreach (new DirectoryIterator($path) as $fileInfo)
        {
            if($fileInfo->isDot())
            continue;

            $fName = $fileInfo->getFilename();

            $html .= '<li>';

            if($fileInfo->isDir())
            {
                $html .=($isHW) ? '<a href="'.$baseLink.'&dir='.$fName.'">'.$fName.'</a>' : 'D-'.$fName;
            }
            else
            {
                $html .= $fName;
            }

            $html .= '</li>';
        }//foreach

        $html .= '<ul>';

        return $html;
    }//function

    /**
     *
     * Enter description here ...
     */
    private function getProjectList()
    {
        $path = NAFUCODE_PATH_SOURCES.'/projects';

        $list = array();

        foreach (new DirectoryIterator($path) as $item)
        {
            if($item->isDot())
            continue;

            if($item->getBasename('.xml') == $item->getFilename())
            continue;

            $data = $this->getProjectData($item->getBasename('.xml'));

            $list[(string)$data->localdir] = $data;
        }//foreach

        return $list;
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $name
     * @throws Exception
     */
    private function getProjectData($name)
    {
        $fileName = NAFUCODE_PATH_SOURCES.'/projects/'.$name.'.xml';

        if( ! file_exists($fileName))
        throw new Exception('Project file not found '.$fileName);

        return simplexml_load_file($fileName);
    }
}//class
