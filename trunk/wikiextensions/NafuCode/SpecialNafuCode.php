<?php
class SpecialNafuCode extends SpecialPage
{
    function __construct()
    {
        parent::__construct('NafuCode');

        //        wfLoadExtensionMessages('NafuCode');
    }

    function execute($par)
    {
        global $wgRequest, $wgOut;
        global $IP;

        $this->setHeaders();

        $html = '';
        $html .= '<a href="?task=help">'.wfMsg('Help').'</a> &bull; ';
        $html .= '<a href="?task=projects">'.wfMsg('Projects').'</a><br />';

        $wgOut->addHtml($html);

        $task = $wgRequest->getText('task');

        if( ! $task)
        {
            $wgOut->addWikiText($this->HELPpage());

            return;
        }

        try
        {
            require_once 'helpers/nafucode.php';

            switch ($task)
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

                default:
                    $wgOut->addWikiText($this->HELPpage());
                break;
            }//switch
        }
        catch (Exception $e)
        {
            $wgOut->addHtml('<b style="color: red;">'.$e->getMessage().'</b>');
        }
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
 <nowiki><nafucode>PROJECT/RESOURCE</nafucode></nowiki>

Options
* Specific range
** <tt>lines=\"START-END\"</tt>
* Highlight
** <tt>highlight=\"n[,n...]\"</tt>
* Options
** <tt>options=\"[linenumbers][,fancy]\"</tt>

=== @projects ===
List all projects.
 <nowiki><nafucode>@projects</nafucode></nowiki>

=== @update ===
Update a project
 <nowiki><nafucode>@update/PROJECT[/SUB]</nafucode></nowiki>

<xnafucode>@update/bp17</nafucode>

==Special==
=== highlight ===
 <nowiki><nafucode lines=\"9-22\" highlight=\"15,16,17\">bp17/css/template.css</nafucode></nowiki>
<nafucode lines=\"9-22\" highlight=\"15,16,17\">bp17/css/template.css</nafucode>

=== fancy ===
 <nowiki><nafucode lines=\"9-22\" options=\"fancy\">bp17/css/template.css</nafucode></nowiki>
<nafucode lines=\"9-22\" options=\"fancy\">bp17/css/template.css</nafucode>

";

        return $html;
    }//function
}//class
