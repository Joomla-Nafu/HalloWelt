<?php
class SpecialNafuCode extends SpecialPage
{
    public function __construct()
    {
        parent::__construct('NafuCode');

        //        wfLoadExtensionMessages('NafuCode');
    }//function

    public function execute($par)
    {
        global $wgRequest, $wgOut;
        global $IP;

        $this->setHeaders();

        $selStyle = ' style="border: 1px solid #ffc; border-radius: 5px; background-color: #ffc"';

        $task = $wgRequest->getText('task');

        $html = '';

        $s =('help' == $task) || ( ! $task) ? $selStyle : '';
        $html .= '&bull; <a'.$s.' href="?task=help">'.wfMsg('Help').'</a> &bull; ';
        $s =('projects' == $task) || ('project' == $task) ? $selStyle : '';
        $html .= '<a'.$s.' href="?task=projects">'.wfMsg('Projects').'</a> &bull; ';
        $s =('helpjcode' == $task) ? $selStyle : '';
        $html .= '<a'.$s.' href="?task=helpjcode">'.wfMsg('J! API').'</a> &bull; ';
        $s =('helphelloworld' == $task) ? $selStyle : '';
        $html .= '<a'.$s.' href="?task=helphelloworld">'.wfMsg('Hello world').'</a> &bull; ';
        $html .= '<br />';

        $wgOut->addHtml($html);

        if( ! $task)
        {
            $wgOut->addWikiText($this->HELPpage());

            return;
        }

        try
        {
            require_once 'helpers/nafucode.php';

            switch($task)
            {
                case 'project' :

                    $helper = new NafuCodeHelper;

                    $wgOut->addHtml($helper->listProject());
                    break;

                case 'projects':

                    $helper = new NafuCodeHelper;

                    $wgOut->addHtml('<h3>Projects</h3>'.$helper->listProjects());
                    break;

                case 'update' :

                    $helper = new NafuCodeHelper;

                    $wgOut->addHtml($helper->updateProjectFromRequest());
                    $wgOut->addHtml($helper->listProject());
                    break;

                case 'help' :
                case 'helpjcode' :
                case 'helphelloworld' :
                    $wgOut->addWikiText($this->{$task.'page'}());

                    break;

                default:
                    $wgOut->addWikiText($this->HELPpage());
                break;
            }//switch
        }
        catch(Exception $e)
        {
            $wgOut->addHtml('<b style="color: red;">'.$e->getMessage().'</b>');
        }//try
    }//function

    private function HELPpage()
    {
        $html = '';
        $html .= "
==".wfMsg('Help')."==
=== display ===
"
        .wfMsg('display-desc')
        ."
<source lang=\"html4strict\"><nafucode>PROJECT/RESOURCE</nafucode></source>

Options
* Specific range
** <tt>lines=\"START-END\"</tt>
* Highlight
** <tt>highlight=\"n[,n...]\"</tt>
* Options
** <tt>options=\"[linenumbers][,fancy]\"</tt>

=== @projects ===
List all projects.
<source lang=\"html4strict\"><nafucode>@projects</nafucode></source>

=== @update ===
Update a project from a version control system
<source lang=\"html4strict\"><nafucode>@update/PROJECT[/SUB]</nafucode></source>
<!--
<xnafucode>@update/bp17</nafucode>
-->
==Special==
=== highlight ===
<source lang=\"html4strict\"><nafucode lines=\"14-18\" highlight=\"15,16\">bp17/css/template.css</nafucode></source>
<nafucode lines=\"14-18\" highlight=\"15,16\">bp17/css/template.css</nafucode>

=== fancy ===
<source lang=\"html4strict\"><nafucode lines=\"14-18\" options=\"fancy\">bp17/css/template.css</nafucode></source>
<nafucode lines=\"14-18\" options=\"fancy\">bp17/css/template.css</nafucode>

";

        return $html;
    }//function

    private function HELPJcodePage()
    {
        $wikiText = '';

        $wikiText .= '== Joomla! API code display ==';

        $wikiText .= "
This is used to display the code of the Joomla! framework / platform methods.
=== ".wfMsg('Display the actual version of a method')." ===
<source lang=\"html4strict\"><nafucode>@J/CLASS/METHOD</nafucode></source>
==== ".wfMsg('Example')." ====
<source lang=\"html4strict\"><nafucode>@J/JText/_</nafucode></source>
===== ".wfMsg('Output')." =====
<nafucode>@J/JText/_</nafucode>
=== ".wfMsg('Display the method in a different Joomla! version')." ===
<source lang=\"html4strict\">Max version
<nafucode jversionmin=\"VERSION\">@J/CLASS/METHOD</nafucode>
Min version
<nafucode jversionmax=\"VERSION\">@J/CLASS/METHOD</nafucode></source>
==== ".wfMsg('Example')." ====
<source lang=\"html4strict\"><<nafucode jversionmax=\"1.5.99\">@J/JText/_</nafucode></source>
This will display the method according to the latest Joomla! 1.5 version '''we used for documentation'''.
===== ".wfMsg('Output')." =====
<nafucode jversionmax=\"1.5.99\">@J/JText/_</nafucode>
";

        return $wikiText;
    }//function

    private function HELPHelloWorldPage()
    {
        $wikiText = '';

        $wikiText .= '== Hello world tutorial display ==';

        $wikiText .= "
This is used to display the code of the HelloWorld Tutorials.
=== ".wfMsg('Display code of a file')." ===
<source lang=\"html4strict\"><nafucode>PROJECT/PATH</nafucode></source>
==== ".wfMsg('Example')." ====
<source lang=\"html4strict\"><nafucode>hw16/1/admin/hallowelt.php</nafucode></source>
===== ".wfMsg('Output')." =====
<nafucode>hw16/1/admin/hallowelt.php</nafucode>
=== ".wfMsg('Display the directory tree for a project')." ===
'''@tree'''
<source lang=\"html4strict\"><nafucode>@tree/PROJECT/PATH</nafucode></source>
==== ".wfMsg('Example')." ====
<source lang=\"html4strict\"><nafucode>@tree/hw16/1</nafucode></source>
===== ".wfMsg('Output')." =====
<nafucode>@tree/hw16/1</nafucode>

        ";

        return $wikiText;
    }//function
}//class
