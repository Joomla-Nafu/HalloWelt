<?php
/**
 * @version SVN: $Id$
 * @package
 * @subpackage
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 11.04.2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

error_reporting(E_STRICT);
define('JPATHROOT', dirname(__FILE__));

define('DS', DIRECTORY_SEPARATOR);
define('BR', '<br />');
define('NL', "\n");

define( '_JEXEC', 1);

require_once JPATHROOT.DS.'helpers'.DS.'object.php';
require_once JPATHROOT.DS.'helpers'.DS.'request.php';
require_once JPATHROOT.DS.'helpers'.DS.'filesystem.php';

require_once JPATHROOT.DS.'helpers'.DS.'html.php';

$sourcesDir = JPATHROOT.DS.'sources'.DS.'joomla';
$JVersions = array('1.5.15', '1.6.trunk_install_sql');

$lister = new tableLister($sourcesDir, $JVersions);

#$tableName =(isset($_GET['table'])) ? $_GET['table'] : '';
$tableName = EasyRequest::getVar('table');

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd"
	xml:lang="de-de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="robots" content="index, follow" />
<meta name="keywords" content="joomla, Joomla" />
<meta name="description"
	content="Joomla! - dynamische Portal-Engine und Content-Management-System" />
<meta name="generator"
	content="Joomla! 1.5 - Open Source Content Management" />
<title>Joomla! core database table changes</title>

<link href="assets/images/jfavicon_t.ico" rel="shortcut icon"
	type="image/x-icon" />

<link rel="stylesheet" href="assets/css/default.css" type="text/css" />
<link rel="stylesheet" href="assets/css/tables.css" type="text/css" />
</head>

<body>
<div>
<div id="homeLink"><a href="index.php">Home</a></div>

<?php
if($tableName)
{
    echo '<a class="returnLink" href="drawTables.php" style="float: right;">Return</a>';
    echo $lister->drawTable($tableName);
}
else
{
    echo $lister->drawOverview();
}
?>
</div>

<? EasyHtml::footer(); ?>
</body>

</html>
<?php

/*
 * end..
 */

/**
 *
 */
class tableLister
{
    private $tables = array();

    private $JVersions = array();

    function __construct($sourcesDir, $JVersions)
    {
        $this->JVersions = $JVersions;

        foreach($this->JVersions as $jversion)
        {
            $fileName = $sourcesDir.DS.$jversion.DS.'installation'.DS.'sql'.DS.'mysql'.DS.'joomla.sql';
            $results = $this->parseFile($fileName, $jversion);
        }//foreach
    }//function

    public function drawOverview()
    {
        $html = '<h1>Changes in Joomla! core tables</h1>'.NL;

        $html .= '<table border="1">'.NL;
        $html .= '<tr>'.NL;
        $html .= '<th>Name</th>';
        $html .= '<th>Availability</th>'.NL;
        $html .= '</tr>'.NL;

        foreach($this->tables as $name => $table)
        {
            $html .= '<tr>'.NL;
            $html .= '<td><a class="tableLink" href="drawTables.php?table='.$name.'">'.$name.'</a></td>'.NL;
            $html .= '<td>';

            $counts = array();

            foreach($this->JVersions as $jversion)
            {
                $color =(array_key_exists($jversion, $this->tables[$name])) ? '' : ' red';
                $html .=  '<span class="img2 J_'.$this->stripVersion($jversion, true).$color.'"></span>';

                if(isset($this->tables[$name][$jversion]['fields']))
                {
                    $counts[] = count($this->tables[$name][$jversion]['fields']);
                }

                if(count($counts) > 1)
                {
                    if($counts[0] != $counts[1])
                    {
                        $html .= '<div style="background-color: yellow;">Changed</div>';
                    }
                }
            }//foreach

            $html .= '</td>'.NL;

            $html .= '</tr>'.NL;
        }//foreach
        $html .= '</table>'.NL;

        return $html;
    }//function

    public function drawTable($tableName)
    {
        $html = '';


        if( ! array_key_exists($tableName, $this->tables))
        {
            $html .= 'invalid table';

            return $html;
        }

        $html .= '<h1>#__'.$tableName.'</h1>'.NL;
        $table = $this->tables[$tableName];

        $compares = array();

        foreach($this->JVersions as $jversion)
        {
            $color =(array_key_exists($jversion, $this->tables[$tableName])) ? '' : 'red';
            $html .= '<span class="img2 '.$color.' J_'.$this->stripVersion($jversion, true).'">';
            $html .= '</span>';
            if(array_key_exists($jversion, $table))
            {
                $compares[$jversion] = $table[$jversion];
            }
        }//foreach

        $html .= '<table border="1">'.NL;
        $html .= '<tr>'.NL;
        $html .= '<th>Availability</th>';
        $html .= '<th>Name</th>';
        $html .= '<th>Info</th>';
        $html .= '<th>Comment</th>';
        $html .= '</tr>'.NL;

        $baseId = 0;

        if(count($compares) > 1)
        {
            $baseId =(count($compares[$this->JVersions[0]]['fields']) < count($compares[$this->JVersions[1]]['fields'])) ? 1 : 0;
        }

        if( ! isset($compares[$this->JVersions[0]]))
        {
            $baseId = 1;
        }

        $compareId =($baseId == 0) ? 1 : 0;

        $base = $compares[$this->JVersions[$baseId]];
        $alreadyDisplayed = array();

        foreach($base['fields'] as $field)
        {
            $alreadyDisplayed[] = $field['name'];
            $field2 = false;

            if(isset($compares[$this->JVersions[$compareId]])
            && array_key_exists($field['name'], $compares[$this->JVersions[$compareId]]['fields']))
            {
                $field2 = $compares[$this->JVersions[$compareId]]['fields'][$field['name']];

                if(strtolower($field['info']) != strtolower($field2['info']))
                {
                    $html .= '<tr style="background-color: yellow;">'.NL;
                }
                else
                {
                    $html .= '<tr>'.NL;
                }
            }
            else
            {
                 $html .= '<tr>'.NL;
            }

            $html .= '<td><span class="img2 J_'.$this->stripVersion($this->JVersions[$baseId], true).'"></span>';

            if( ! $field2 && count($compares) > 1)
            {
                $html .= '<span class="red img2 J_'.$this->stripVersion($this->JVersions[$compareId], true).'"></span>';
            }

            $html .= '</td>'.NL;

            $html .= '<td>'.$field['name'].'</td>'.NL;
            $html .= '<td>'.$field['info'].'</td>'.NL;

            if($field['comment'])
            {
                $html .= '    <td>'.$field['comment'].'</td>'.NL;
            }
            else
            {
                $html .= '    <td>&nbsp;</td>'.NL;
            }

            $html .= '</tr>'.NL;

            if($field2)
            {
                if(strtolower($field['info']) != strtolower($field2['info']))
                {
                    $html .= '<tr style="background-color: yellow;">'.NL;

                    $html .= '    <td><span class="img2 J_'.$this->stripVersion($this->JVersions[$compareId], true).'"></span></td>'.NL;
                    $html .= '    <td>'.$field2['name'].'</td>'.NL;
                    $html .= '    <td>'.$field2['info'].'</td>'.NL;
                    if($field2['comment'])
                    {
                        $html .= '    <td>'.$field2['comment'].'</td>'.NL;
                    }
                    else
                    {
                        $html .= '    <td>&nbsp;</td>'.NL;
                    }
                    $html .= '</tr>';
                }
            }
        }//foreach

        /*
         * Pass 2
         */

        if(count($compares) > 1
        && count($compares[$this->JVersions[0]]['fields']) != count($compares[$this->JVersions[1]]['fields']))
        {
            $blub = $this->JVersions[$compareId];
            $compare = $compares[$this->JVersions[$compareId]];

            foreach($compare['fields'] as $field)
            {
                if(in_array($field['name'], $alreadyDisplayed)) continue;

                $field2 = false;

                if(isset($compares[$this->JVersions[$baseId]])
                && array_key_exists($field['name'], $compares[$this->JVersions[$baseId]]['fields']))
                {
                    $field2 = $compares[$this->JVersions[$baseId]]['fields'][$field['name']];

                    if($field['info'] != $field2['info'])
                    {
                        $html .= '<tr style="background-color: yellow;">';
                    }
                    else
                    {
                        $html .= '<tr>'.NL;
                    }
                }
                else
                {
                    $html .= '<tr>'.NL;
                }

                $html .= '<td><span class="img2 J_'.$this->stripVersion($this->JVersions[$compareId], true).'"></span>';

                if( ! $field2)
                {
                    $html .= '<span class="red img2 J_'.$this->stripVersion($this->JVersions[$baseId], true).'"></span>';
                }

                $html .= '</td>'.NL;

                $html .= '<td>'.$field['name'].'</td>'.NL;
                $html .= '<td>'.$field['info'].'</td>'.NL;
                if($field['comment'])
                {
                    $html .= '    <td>'.$field['comment'].'</td>'.NL;
                }
                else
                {
                    $html .= '    <td>&nbsp;</td>'.NL;
                }
                $html .= '</tr>'.NL;

                if($field2
                && $field['info'] != $field2['info'])
                {
                    $html .= '<tr style="background-color: yellow;">'.NL;
                    $html .= '    <td>'.$this->stripVersion($this->JVersions[$compareId]).'</td>'.NL;
                    $html .= '    <td>'.$field2['name'].'</td>'.NL;
                    $html .= '    <td>'.$field2['info'].'</td>'.NL;
                    if($field2['comment'])
                    {
                        $html .= '    <td>'.$field2['comment'].'</td>'.NL;
                    }
                    else
                    {
                        $html .= '    <td>&nbsp;</td>'.NL;
                    }
                    $html .= '</tr>';
                }
            }
        }

        $html .= '</table>'.NL;

        return $html;
    }//function

    public function getTableNames()
    {
        return array_keys($this->tables);
    }//function

    public function parseFile($fileName, $jversion)
    {
        if( ! file_exists($fileName))
        {
            echo 'File not found..';

            return '';
        }

        $fileContents = file($fileName);
        $fileContents = implode('', $fileContents);
        $queries = $this->splitSql($fileContents);

        $results = $this->parseQueries($queries, $jversion);

        return $results;
    }//function

    private function parseQueries($queries, $jversion)
    {
        $creates = array();

        foreach($queries as $query)
        {
            $lines = explode("\n", $query);
            $isCreate = false;

            foreach($lines as $line)
            {
                if( ! $line) continue;

                if(strpos($line, 'CREATE TABLE') === 0)
                {
                    $isCreate = true;

                    preg_match('/#__([a-z|_]+)/', $line, $matches);
                    $tblName = $matches[1];
                    $creates[$tblName] = array();

                    continue;
                }
                else if( ! $isCreate)
                {
                    continue 2;
                }

                $elements = explode(' ', trim($line));
                # 	var_dump($elements);

                $isField = false;

                for($i = 0; $i < count($elements); $i++)
                {
                    if(strpos($elements[$i], '`') === 0)
                    {
                        $field = array();
                        $field['name'] = str_replace('`', '', $elements[$i]);

                        //-temp
                        //comment
                        $s = trim(str_replace($elements[$i], '', $line));
                        $search = " COMMENT '";
                        $pos = strpos($s, $search);

                        $info = $s;
                        $comment = '';

                        if($pos)
                        {
                            $info = substr($s, 0, $pos);
                            $comment = substr($s, $pos + strlen($search));
                        }

                        $field['info'] = $info;
                        $field['comment'] = $comment;

                        $creates[$tblName]['fields'][] = $field;
                        $this->tables[$tblName][$jversion]['fields'][$field['name']] = $field;
                        $isField = true;
                        continue;
                    }
                    else if( ! $isField)
                    {
                        //@todo parse this..
                        #      $creates[$tblName]['additionals'][] = $line;
                        $this->tables[$tblName][$jversion]['additionals'][] = $line = array();

                        continue 2;
                    }
                }//for

            }//foreach
        }//foreach

        return $creates;
    }//function


    private function splitSql($sql)
    {
        $sql = trim($sql);
        $sql = preg_replace("/\n\#[^\n]*/", '', "\n".$sql);
        $buffer = array ();
        $ret = array ();
        $in_string = false;

        for ($i = 0; $i < strlen($sql) - 1; $i ++)
        {
            if ($sql[$i] == ";" && !$in_string)
            {
                $ret[] = substr($sql, 0, $i);
                $sql = substr($sql, $i +1);
                $i = 0;
            }

            if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\")
            {
                $in_string = false;
            }
            elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\"))
            {
                $in_string = $sql[$i];
            }

            if (isset ($buffer[1]))
            {
                $buffer[0] = $buffer[1];
            }

            $buffer[1] = $sql[$i];
        }

        if (!empty ($sql))
        {
            $ret[] = $sql;
        }

        return ($ret);
    }//function

    private function stripVersion($version, $css = false)
    {
        $s = substr($version, 0, 3);
        if($css) $s = str_replace('.', '_', $s);

        return $s;

        $parts = explode('_', $version);

        return $parts[0];
    }//function

}//class
