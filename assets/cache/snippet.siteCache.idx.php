a:8:{s:5:"Ditto";s:29117:"/* Description:
 *      Aggregates documents to create blogs, article/news
 *      collections, and more,with full support for templating.
 * 
 * Author: 
 *      Mark Kaplan for MODx CMF
*/

//---Core Settings---------------------------------------------------- //

$ditto_version = "2.1.3";
    // Ditto version being executed

$ditto_base = isset($ditto_base) ? $modx->config['base_path'] . ltrim($ditto_base,'/') : $modx->config['base_path']."assets/snippets/ditto/";
/*
    Param: ditto_base
    
    Purpose:
    Location of Ditto files

    Options:
    Any valid folder location containing the Ditto source code with a trailing slash

    Default:
    [(base_path)]assets/snippets/ditto/
*/
$dittoID = (!isset($id)) ? "" : $id."_";
$GLOBALS["dittoID"] = $dittoID;
/*
    Param: id

    Purpose:
    Unique ID for this Ditto instance for connection with other scripts (like Reflect) and unique URL parameters

    Options:
    Any combination of characters a-z, underscores, and numbers 0-9
    
    Note:
    This is case sensitive

    Default:
    "" - blank
*/      
$language = (isset($language))? $language : $modx->config['manager_language'];
if (!file_exists($ditto_base."lang/".$language.".inc.php")) {
    $language ="english";
}
/*
    Param: language

    Purpose:
    language for defaults, debug, and error messages

    Options:
    Any language name with a corresponding file in the &ditto_base/lang folder

    Default:
    "english"
*/
$format = (isset($format)) ? strtolower($format) : "html" ;
/*
    Param: format

    Purpose:
    Output format to use

    Options:
    - "html"
    - "json"
    - "xml"
    - "atom"
    - "rss"

    Default:
    "html"
*/
$config = (isset($config)) ? $config : "default";
include_once("{$ditto_base}configs/default.config.php");

if(substr($config, 0, 6) === '@CHUNK')
{
	eval('?>' . $modx->getChunk(trim(substr($config, 7))));
}
elseif(substr($config, 0, 5) === '@FILE')
{
	include_once($modx->config['base_path'] . ltrim(trim(substr($config, 6)),'/'));
}
elseif($config !== 'default')
{
	include_once("{$ditto_base}configs/{$config}.config.php");
}

/*
    Param: config

    Purpose:
    Load a custom configuration

    Options:
    "default" - default blank config file
    CONFIG_NAME - Other configs installed in the configs folder or in any folder within the MODx base path via @FILE

    Default:
    "default"
    
    Related:
    - <extenders>
*/
$debug = isset($debug)? $debug : 0;
/*
    Param: debug

    Purpose:
    Output debugging information

    Options:
    0 - off
    1 - on
    
    Default:
    0 - off
    
    Related:
    - <debug>
*/
$phx = (isset($phx))? $phx : 1;
/*
    Param: phx

    Purpose:
    Use PHx formatting

    Options:
    0 - off
    1 - on
    
    Default:
    1 - on
*/      
$extenders = isset($extenders) ? explode(",",$extenders) : array();
/*
    Param: extenders

    Purpose:
    Load an extender which adds functionality to Ditto

    Options:
    Any extender in the extenders folder or in any folder within the MODx base path via @FILE

    Default:
    [NULL]

    Related:
    - <config>
*/
    // Variable: extenders
    // Array that can be added to by configs or formats to load that extender
    
$placeholders = array();
    // Variable: placeholders
    // Initialize custom placeholders array for configs or extenders to add to

$filters = array("custom"=>array(),"parsed"=>array());
    // Variable: filters
    // Holds both the custom filters array for configs or extenders to add to 
    // and the parsed filters array. To add to this array, use the following format
    // (code)
    // $filters["parsed"][] = array("name" => array("source"=>$source,"value"=>$value,"mode"=>$mode));
    // $filters["custom"][] = array("source","callback_function");

$orderBy = (isset($orderBy))? $orderBy : '';
$orderBy = array('parsed'=>array(),'custom'=>array(),'unparsed'=>$orderBy);
    // Variable: orderBy
    // An array that holds all criteria to sort the result set by. 
    // Note that using a custom sort will disable all other sorting.
    // (code)
    // $orderBy["parsed"][] = array("sortBy","sortDir");
    // $orderBy["custom"][] = array("sortBy","callback_function");
        
//---Includes-------------------------------------------------------- //

$files = array (
    "base_language" => $ditto_base."lang/english.inc.php",
    "language" => $ditto_base."lang/$language.inc.php",
    "main_class" => $ditto_base."classes/ditto.class.inc.php",
    "template_class" => $ditto_base."classes/template.class.inc.php",
    "filter_class" => $ditto_base."classes/filter.class.inc.php",
    "format" => $ditto_base."formats/$format.format.inc.php"
);

if ($phx == 1) {
    $files["prePHx_class"] = $ditto_base."classes/phx.pre.class.inc.php";
}
if (isset($randomize)) {
    $files["randomize_class"] = $ditto_base."classes/random.class.inc.php";
}
if ($debug == 1) {
    $files["modx_debug_class"] = $ditto_base."debug/modxDebugConsole.class.php";
    $files["debug_class"] = $ditto_base."classes/debug.class.inc.php";
    $files["debug_templates"] = $ditto_base."debug/debug.templates.php";
}

$files = array_unique($files);
foreach ($files as $filename => $filevalue) {
    if (file_exists($filevalue) && strpos($filename,"class")) {
        include_once($filevalue);
    } else if (file_exists($filevalue)) {
        include($filevalue);
    } else if ($filename == "language") {
        $modx->logEvent(1, 3, "Language file does not exist Please check: " . $filevalue, "Ditto " . $ditto_version);
        return "Language file does not exist Please check: " . $filevalue;
    } else {
        $modx->logEvent(1, 3, $filevalue . " " . $_lang['file_does_not_exist'], "Ditto " . $ditto_version);
        return $filevalue . " " . $_lang['file_does_not_exist'];
    }
}

//---Initiate Class-------------------------------------------------- //
$dbg_templates = (isset($dbg_templates)) ? $dbg_templates : NULL;
if (class_exists('ditto')) {
    $ditto = new ditto($dittoID,$format,$_lang,$dbg_templates);
        // create a new Ditto instance in the specified format and language with the requested debug level
} else {
    $modx->logEvent(1,3,$_lang['invalid_class'],"Ditto ".$ditto_version);
    return $_lang['invalid_class'];
}

//---Initiate Extenders---------------------------------------------- //
if (isset($tagData)) {
    $extenders[] = "tagging";
}
if(count($extenders) > 0) {
    $extenders = array_unique($extenders);
    foreach ($extenders as $extender) {
            if(substr($extender, 0, 5) != "@FILE") {
                $extender_path = $ditto_base."extenders/".$extender.".extender.inc.php";                
            } else {
                $extender_path = $modx->config['base_path'].trim(substr($extender, 5));
            }
            
            if (file_exists($extender_path)){
                include($extender_path);
            } else {
                $modx->logEvent(1, 3, $extender . " " . $_lang['extender_does_not_exist'], "Ditto ".$ditto_version);
                return $extender . " " . $_lang['extender_does_not_exist'];
            }       
    }   
}

//---Parameters------------------------------------------------------- /*
if (isset($startID)) {$parents = $startID;}
if (isset($summarize)) {$display = $summarize;}
if (isset($limit)) {$queryLimit = $limit;}
if (isset($sortBy) || isset($sortDir) || is_null($orderBy['unparsed'])) {
    $sortDir = isset($sortDir) ? strtoupper($sortDir) : 'DESC';
    $sortBy = isset($sortBy) ? $sortBy : "createdon";
    $orderBy['parsed'][]=array($sortBy,$sortDir);
}
    // Allow backwards compatibility

$idType = isset($documents) ? "documents" : "parents";
    // Variable: idType
    // type of IDs provided; can be either parents or documents

$parents = isset($parents) ? $ditto->cleanIDs($parents) : $modx->documentIdentifier;

/*
    Param: parents

    Purpose:
    IDs of containers for Ditto to retrieve their children to &depth depth

    Options:
    Any valid MODx document marked as a container

    Default:
    Current MODx Document

    Related:
    - <documents>
    - <depth>
*/
$documents = isset($documents) ? $ditto->cleanIDs($documents) : false;
/*
    Param: documents

    Purpose:
    IDs of documents for Ditto to retrieve

    Options:
    Any valid MODx document marked as a container

    Default:
    None

    Related:
    - <parents>
*/

$IDs = ($idType == "parents") ? $parents : $documents;
    // Variable: IDs
    // Internal variable which holds the set of IDs for Ditto to fetch

$depth = isset($depth) ? $depth : 1;
/*
    Param: depth

    Purpose:
    Number of levels deep to retrieve documents

    Options:
    Any number greater than or equal to 1
    0 - infinite depth

    Default:
    1

    Related:
    - <seeThruUnpub>
*/
$paginate = isset($paginate)? $paginate : 0;
/*
    Param: paginate

    Purpose:
    Paginate the results set into pages of &display length.
    Use &total to limit the number of documents retreived.

    Options:
    0 - off
    1 - on
    
    Default:
    0 - off
    
    Related:
    - <paginateAlwaysShowLinks>
    - <paginateSplitterCharacter>
    - <display>
*/
$dateSource = isset($dateSource) ? $dateSource : "createdon";
/*
    Param: dateSource

    Purpose:
    Source of the [+date+] placeholder

    Options:
    # - Any UNIX timestamp from MODx fields or TVs such as createdon, pub_date, or editedon
    
    Default:
    "createdon"
    
    Related:
    - <dateFormat>
*/
$dateFormat = isset($dateFormat)? $dateFormat : $_lang["dateFormat"];
/*
    Param: dateFormat

    Purpose:
    Format the [+date+] placeholder in human readable form

    Options:
    Any PHP valid strftime option

    Default:
    [LANG]
    
    Related:
    - <dateSource>
*/
$display = isset($display) ? $display : "all";
/*
    Param: display

    Purpose:
    Number of documents to display in the results

    Options:
    # - Any number
    "all" - All documents found

    Default:
    "all"
    
    Related:
    - <queryLimit>
    - <total>
*/
$total = isset($total) ? $total : "all";
/*
    Param: total

    Purpose:
    Number of documents to retrieve
    
    Options:
    # - Any number
    "all" - All documents found

    Default:
    "all" - All documents found
    
    Related:
    - <display>
    - <queryLimit>
*/
$showPublishedOnly = isset($showPublishedOnly) ? $showPublishedOnly : 1;
/*
    Param: showPublishedOnly

    Purpose:
    Show only published documents

    Options:
    0 - show only unpublished documents
    1 - show both published and unpublished documents
    
    Default:
    1 - show both published and unpublished documents
    
    Related:
    - <seeThruUnpub>
    - <hideFolders>
    - <showPublishedOnly>
    - <where>
*/
$showInMenuOnly = isset($showInMenuOnly) ? $showInMenuOnly : 0;
/*
    Param: showInMenuOnly

    Purpose:
    Show only documents visible in the menu

    Options:
    0 - show all documents
    1 - show only documents with the show in menu flag checked
    
    Default:
    0 - show all documents
    
    Related:
    - <seeThruUnpub>
    - <hideFolders>
    - <where>
*/
$hideFolders = isset($hideFolders)? $hideFolders : 0;
/*
    Param: hideFolders

    Purpose:
    Don't show folders in the returned results

    Options:
    0 - keep folders
    1 - remove folders
    
    Default:
    0 - keep folders
    
    Related:
    - <seeThruUnpub>
    - <showInMenuOnly>
    - <where>
*/
$hidePrivate = isset($hidePrivate)? $hidePrivate : 1;
/*
    Param: hidePrivate

    Purpose:
    Don't show documents the guest or user does not have permission to see

    Options:
    0 - show private documents
    1 - hide private documents
    
    Default:
    1 - hide private documents
    
    Related:
    - <seeThruUnpub>
    - <showInMenuOnly>
    - <where>
*/
$seeThruUnpub = (isset($seeThruUnpub))? $seeThruUnpub : 1 ;
/*
    Param: seeThruUnpub

    Purpose:
    See through unpublished folders to retrive their children
    Used when depth is greater than 1

    Options:
    0 - off
    1 - on
    
    Default:
    0 - off
    
    Related:
    - <hideFolders>
    - <showInMenuOnly>
    - <where>
*/
$queryLimit = (isset($queryLimit))? $queryLimit : 0;
/*
    Param: queryLimit

    Purpose:
    Number of documents to retrieve from the database, same as MySQL LIMIT

    Options:
    # - Any number
    0 - automatic

    Default:
    0 - automatic
    
    Related:
    - <where>
*/
$where = (isset($where))? $where : "";
/*
    Param: where

    Purpose:
    Custom MySQL WHERE statement

    Options:
    A valid MySQL WHERE statement using only document object items (no TVs)

    Default:
    [NULL]
    
    Related:
    - <queryLimit>
*/
$noResults = isset($noResults)? $ditto->getParam($noResults,"no_documents") : $_lang['no_documents'];
/*
    Param: noResults

    Purpose:
    Text or chunk to display when there are no results

    Options:
    Any valid chunk name or text

    Default:
    [LANG]
*/
$removeChunk = isset($removeChunk) ? explode(",",$removeChunk) : false;
/*
    Param: removeChunk

    Purpose:
    Name of chunks to be stripped from content separated by commas
    - Commonly used to remove comments

    Options:
    Any valid chunkname that appears in the output

    Default:
    [NULL]
*/
$hiddenFields = isset($hiddenFields) ? explode(",",$hiddenFields) : false;
/*
    Param: hiddenFields

    Purpose:
    Allow Ditto to retrieve fields its template parser cannot handle such as nested placeholders and [*fields*]

    Options:
    Any valid MODx fieldnames or TVs comma separated

    Default:
    [NULL]
*/
$offset = isset($start) ? $start : 0;
$start = (isset($_GET[$dittoID.'start'])) ? intval($_GET[$dittoID.'start']) : 0;
/*
    Param: start

    Purpose:
    Number of documents to skip in the results
    
    Options:
    Any number

    Default:
    0
*/
$globalFilterDelimiter = isset($globalFilterDelimiter) ? $globalFilterDelimiter : "|";
/*
    Param: globalFilterDelimiter

    Purpose:
    Filter delimiter used to separate filters in the filter string
    
    Options:
    Any character not used in the filters

    Default:
    "|"
    
    Related:
    - <localFilterDelimiter>
    - <filter>
    - <parseFilters>
*/
    
$localFilterDelimiter = isset($localFilterDelimiter) ? $localFilterDelimiter : ",";
/*
    Param: localFilterDelimiter

    Purpose:
    Delimiter used to separate individual parameters within each filter string
    
    Options:
    Any character not used in the filter itself

    Default:
    ","
    
    Related:
    - <globalFilterDelimiter>
    - <filter>
    - <parseFilters>
*/
$filters["custom"] = isset($cFilters) ? array_merge($filters["custom"],$cFilters) : $filters["custom"];
$filters["parsed"] = isset($parsedFilters) ? array_merge($filters["parsed"],$parsedFilters) : $filters["parsed"];
    // handle 2.0.0 compatibility
$filter = (isset($filter) || ($filters["custom"] != false) || ($filters["parsed"] != false)) ? $ditto->parseFilters($filter,$filters["custom"],$filters["parsed"],$globalFilterDelimiter,$localFilterDelimiter) : false;
/*
    Param: filter

    Purpose:
    Removes items not meeting a critera. Thus, if pagetitle == joe then it will be removed.
    Use in the format field,criteria,mode with the comma being the local delimiter

    *Mode* *Meaning*
    
    1 - !=
    2 - ==
    3 - <
    4 - >
    5 - <=
    6 - >=
    7 - Text not in field value
    8 - Text in field value
    9 - case insenstive version of #7
    10 - case insenstive version of #8
    11 - checks leading character of the field
    
    @EVAL:
        @EVAL in filters works the same as it does in MODx exect it can only be used 
        with basic filtering, not custom filtering (tagging, etc). Make sure that
        you return the value you wish Ditto to filter by and that the code is valid PHP.

    Default:
    [NULL]
    
    Related:
    - <localFilterDelimiter>
    - <globalFilterDelimiter>
    - <parseFilters>
*/
$keywords = (isset($keywords))? $keywords : 0;
/*  
    Param: keywords
    
    Purpose: 
    Enable fetching of associated keywords for each document
    Can be used as [+keywords+] or as a tagData source
    
    Options:
    0 - off
    1 - on
    
    Default:
    0 - off
*/

$randomize = (isset($randomize))? $randomize : 0;
/*  
    Param: randomize
    
    Purpose: 
    Randomize the order of the output
    
    Options:
    0 - off
    1 - on
    Any MODx field or TV for weighted random
    
    Default:
    0 - off
*/
$save = (isset($save))? $save : 0;
/*
    Param: save

    Purpose:
    Saves the ditto object and results set to placeholders
    for use by other snippets

    Options:
    0 - off; returns output
    1 - remaining; returns output
    2 - all;
    3 - all; returns ph only

    Default:
        0 - off; returns output
*/
$tplAlt = (isset($tplAlt)) ? $tplAlt : '';
$tplFirst = (isset($tplFirst)) ? $tplFirst : '';
$tplLast = (isset($tplLast)) ? $tplLast : '';
$tplCurrentDocument = (isset($tplCurrentDocument)) ? $tplCurrentDocument : '';
$templates = array(
    "default" => "@CODE".$_lang['default_template'],
    "base" => $tpl,
    "alt" => $tplAlt,
    "first" => $tplFirst,
    "last" => $tplLast,
    "current" => $tplCurrentDocument
);
/*
    Param: tpl

    Purpose:
    User defined chunk to format the documents 

    Options:
    - Any valid chunk name
    - Code via @CODE
    - File via @FILE

    Default:
    [LANG]
*/
/*
    Param: tplAlt

    Purpose:
    User defined chunk to format every other document

    Options:
    - Any valid chunk name
    - Code via @CODE
    - File via @FILE

    Default:
    &tpl
*/
/*
    Param: tplFirst

    Purpose:
    User defined chunk to format the first document 

    Options:
    - Any valid chunk name
    - Code via @CODE
    - File via @FILE

    Default:
    &tpl
*/
/*
    Param: tplLast

    Purpose:
    User defined chunk to format the last document 

    Options:
    - Any valid chunk name
    - Code via @CODE
    - File via @FILE

    Default:
    &tpl
*/
/*
    Param: tplCurrentDocument

    Purpose:
    User defined chunk to format the current document

    Options:
    - Any valid chunk name
    - Code via @CODE
    - File via @FILE

    Default:
    &tpl
*/
$orderBy = $ditto->parseOrderBy($orderBy,$randomize);
/*
    Param: orderBy

    Purpose:
    Sort the result set

    Options:
    Any valid MySQL style orderBy statement

    Default:
    createdon DESC
*/
//-------------------------------------------------------------------- */
$templates = $ditto->template->process($templates);
    // parse the templates for TV's and store them for later use

$ditto->setDisplayFields($ditto->template->fields,$hiddenFields);
    // parse hidden fields
    
$ditto->parseFields($placeholders,$seeThruUnpub,$dateSource,$randomize);
    // parse the fields into the field array
    
$documentIDs = $ditto->determineIDs($IDs, $idType, $ditto->fields["backend"]["tv"], $orderBy, $depth, $showPublishedOnly, $seeThruUnpub, $hideFolders, $hidePrivate, $showInMenuOnly, $where, $keywords, $dateSource, $queryLimit, $display, $filter,$paginate, $randomize);
    // retrieves a list of document IDs that meet the criteria and populates the $resources array with them
$count = count($documentIDs);
    // count the number of documents to be retrieved
$count = $count-$offset;
    // handle the offset

if ($count > 0) {
    // if documents are returned continue with execution
    
    $total = ($total == "all") ? $count : min($total,$count);
        // set total equal to count if all documents are to be included
    
    $display = ($display == "all") ? min($count,$total) : min($display,$total);
        // allow show to use all option

    $stop = ($save != "1") ? min($total-$start,$display) : min($count,$total);
        // set initial stop count

    if($paginate == 1) {
        $paginateAlwaysShowLinks = isset($paginateAlwaysShowLinks)? $paginateAlwaysShowLinks : 0;
        /*
            Param: paginateAlwaysShowLinks

            Purpose:
            Determine whether or not to always show previous next links

            Options:
            0 - off
            1 - on

            Default:
            0 - off
        
            Related:
            - <paginate>
            - <paginateSplitterCharacter>
        */
        $paginateSplitterCharacter = isset($paginateSplitterCharacter)? $paginateSplitterCharacter : $_lang['button_splitter'];
        /*
            Param: paginateSplitterCharacter

            Purpose:
            Splitter to use if always show is disabled

            Options:
            Any valid character

            Default:
            [LANG]
        
            Related:
            - <paginate>
            - <paginateSplitterCharacter>
        */
        $tplPaginatePrevious = isset($tplPaginatePrevious)? $ditto->template->fetch($tplPaginatePrevious) : "<a href='[+url+]' class='ditto_previous_link'>[+lang:previous+]</a>";
        /*
            Param: tplPaginatePrevious

            Purpose:
            Template for the previous link

            Options:
            - Any valid chunk name
            - Code via @CODE
            - File via @FILE

            Placeholders:
            url - URL for the previous link
            lang:previous - value of 'prev' from the language file
        
            Related:
            - <tplPaginateNext>
            - <paginateSplitterCharacter>
        */
        $tplPaginateNext = isset($tplPaginateNext)? $ditto->template->fetch($tplPaginateNext) : "<a href='[+url+]' class='ditto_next_link'>[+lang:next+]</a>";
        /*
            Param: tplPaginateNext

            Purpose:
            Template for the next link

            Options:
            - Any valid chunk name
            - Code via @CODE
            - File via @FILE

            Placeholders:
            url - URL for the next link
            lang:next - value of 'next' from the language file
        
            Related:
            - <tplPaginatePrevious>
            - <paginateSplitterCharacter>
        */
        $tplPaginateNextOff = isset($tplPaginateNextOff)? $ditto->template->fetch($tplPaginateNextOff) : "<span class='ditto_next_off ditto_off'>[+lang:next+]</span>";
        /*
            Param: tplPaginateNextOff

            Purpose:
            Template for the inside of the next link

            Options:
            - Any valid chunk name
            - Code via @CODE
            - File via @FILE

            Placeholders:
            lang:next - value of 'next' from the language file
        
            Related:
            - <tplPaginatePrevious>
            - <paginateSplitterCharacter>
        */
        $tplPaginatePreviousOff = isset($tplPaginatePreviousOff)? $ditto->template->fetch($tplPaginatePreviousOff) : "<span class='ditto_previous_off ditto_off'>[+lang:previous+]</span>";
        /*
            Param: tplPaginatePreviousOff

            Purpose:
            Template for the previous link when it is off

            Options:
            - Any valid chunk name
            - Code via @CODE
            - File via @FILE

            Placeholders:
            lang:previous - value of 'prev' from the language file
    
            Related:
            - <tplPaginatePrevious>
            - <paginateSplitterCharacter>
        */
        $tplPaginatePage = isset($tplPaginatePage)? $ditto->template->fetch($tplPaginatePage) : "<a class='ditto_page' href='[+url+]'>[+page+]</a>";
        /*
            Param: tplPaginatePage

            Purpose:
            Template for the page link

            Options:
            - Any valid chunk name
            - Code via @CODE
            - File via @FILE

            Placeholders:
            url - url for the page
            page - number of the page
    
            Related:
            - <tplPaginatePrevious>
            - <paginateSplitterCharacter>
        */
        $tplPaginateCurrentPage = isset($tplPaginateCurrentPage)? $ditto->template->fetch($tplPaginateCurrentPage) : "<span class='ditto_currentpage'>[+page+]</span>";
        /*
            Param: tplPaginateCurrentPage

            Purpose:
            Template for the current page link

            Options:
            - Any valid chunk name
            - Code via @CODE
            - File via @FILE

            Placeholders:
            page - number of the page
    
            Related:
            - <tplPaginatePrevious>
            - <paginateSplitterCharacter>
        */
        
        $ditto->paginate($start, $stop, $total, $display, $tplPaginateNext, $tplPaginatePrevious, $tplPaginateNextOff, $tplPaginatePreviousOff, $tplPaginatePage, $tplPaginateCurrentPage, $paginateAlwaysShowLinks, $paginateSplitterCharacter);
            // generate the pagination placeholders
    }

    $dbFields = $ditto->fields["display"]["db"];
        // get the database fields
    $TVs = $ditto->fields["display"]["tv"];
        // get the TVs
    
    switch($orderBy['parsed'][0][1]) {
        case "DESC":
            $stop = ($ditto->prefetch === false) ? $stop + $start + $offset : $stop + $offset; 
            $start += $offset;
        break;
        case "ASC":
            $start += $offset;
            $stop += $start;
        break;
    }

    if ($ditto->prefetch !== false) {
        $documentIDs = array_slice($documentIDs,$start,$stop);
            // set the document IDs equal to the trimmed array
        $dbFields = array_diff($dbFields,$ditto->prefetch["fields"]["db"]);
            // calculate the difference between the database fields and those already prefetched
        $dbFields[] = "id";
            // append id to the db fields array
        $TVs = array_diff($TVs,$ditto->prefetch["fields"]["tv"]);
            // calculate the difference between the tv fields and those already prefetched
        $start = 0;
        $stop = min($display,($queryLimit != 0) ? $queryLimit : $display,count($documentIDs));
    } else {
        $queryLimit = ($queryLimit == 0) ? "" : $queryLimit;
    }
    
    $resource = $ditto->getDocuments($documentIDs, $dbFields, $TVs, $orderBy, $showPublishedOnly, 0, $hidePrivate, $where, $queryLimit, $keywords, $randomize, $dateSource);
        // retrieves documents
    $output = $header;
        // initialize the output variable and send the header

    if ($resource) {
        if ($randomize != "0" && $randomize != "1") {
            $resource = $ditto->weightedRandom($resource,$randomize,$stop);
                // randomize the documents
        }
        
        $resource = array_values($resource);

        for ($x=$start;$x<$stop;$x++) {
            $template = $ditto->template->determine($templates,$x,0,$stop,$resource[$x]["id"]);
                // choose the template to use and set the code of that template to the template variable
            $renderedOutput = $ditto->render($resource[$x], $template, $removeChunk, $dateSource, $dateFormat, $placeholders,$phx,abs($start-$x));
                // render the output using the correct template, in the correct format and language
            $modx->setPlaceholder($dittoID."item[".abs($start-$x)."]",$renderedOutput);
            /*
                Placeholder: item[x]

                Content:
                Individual items rendered output
            */
            $output .= $renderedOutput;
                // send the rendered output to the buffer
        }
    } else {
        $output .= $ditto->noResults($noResults,$paginate);
            // if no documents are found return a no documents found string
    }
    $output .= $footer;
        // send the footer

    // ---------------------------------------------------
    // Save Object
    // ---------------------------------------------------

    if($save) {
        $modx->setPlaceholder($dittoID."ditto_object", $ditto);
        $modx->setPlaceholder($dittoID."ditto_resource", ($save == "1") ? array_slice($resource,$display) : $resource);
    }
} else {
    $output = $header.$ditto->noResults($noResults,$paginate).$footer;
}
// ---------------------------------------------------
// Handle Debugging
// ---------------------------------------------------

if ($debug == 1) {
    $ditto_params =& $modx->event_params;
    if (!isset($_GET["ditto_".$dittoID."debug"])) {
    $_SESSION["ditto_debug_$dittoID"] = $ditto->debug->render_popup($ditto, $ditto_base, $ditto_version, $ditto_params, $documentIDs, array("db"=>$dbFields,"tv"=>$TVs), $display, $templates, $orderBy, $start, $stop, $total,$filter,$resource);
    }
    if (isset($_GET["ditto_".$dittoID."debug"])) {
        switch ($_GET["ditto_".$dittoID."debug"]) {
            case "open" :
                exit($_SESSION["ditto_debug_$dittoID"]);
            break;
            case "save" :
                $ditto->debug->save($_SESSION["ditto_debug_$dittoID"],"ditto".strtolower($ditto_version)."_debug_doc".$modx->documentIdentifier.".html");
            break;
        }
    } else {
        $output = $ditto->debug->render_link($dittoID,$ditto_base).$output;
    }
}

return ($save != 3) ? $output : "";";s:5:"eForm";s:765:"# eForm 1.4.4.7 - Electronic Form Snippet
# Original created by Raymond Irving 15-Dec-2004.
# Version 1.3+ extended by Jelle Jager (TobyL) September 2006
# -----------------------------------------------------
# Captcha image support - thanks to Djamoer
# Multi checkbox, radio, select support - thanks to Djamoer
# Form Parser and extened validation - by Jelle Jager
#

# Set Snippet Paths
$snip_dir = isset($snip_dir) ? $snip_dir : 'eform';
$snipPath = "{$modx->config['base_path']}assets/snippets/{$snip_dir}/";

# check if inside manager
if ($modx->isBackend()) return ''; // don't go any further when inside manager

# Start processing

$version = '1.4.4.7';
include_once ("{$snipPath}eform.inc.php");

$output = eForm($modx,$params);

# Return
return $output;";s:10:"eFormProps";s:43:"&sendAsText=テキストで送る;string;1 ";s:9:"TopicPath";s:181:"$version = '1.0.3';
include_once($modx->config['base_path'] . 'assets/snippets/topicpath/topicpath.class.inc.php');
$topicpath = new TopicPath();
return $topicpath->getTopicPath();
";s:14:"TopicPathProps";s:31:"&theme=Theme;list;raw,list;raw ";s:9:"Wayfinder";s:4514:"/*
::::::::::::::::::::::::::::::::::::::::
 Snippet name: Wayfinder
 Short Desc: builds site navigation
 Version: 2.0
 Authors: 
	Kyle Jaebker (muddydogpaws.com)
	Ryan Thrash (vertexworks.com)
 Date: February 27, 2006
::::::::::::::::::::::::::::::::::::::::
Description:
    Totally refactored from original DropMenu nav builder to make it easier to
    create custom navigation by using chunks as output templates. By using templates,
    many of the paramaters are no longer needed for flexible output including tables,
    unordered- or ordered-lists (ULs or OLs), definition lists (DLs) or in any other
    format you desire.
::::::::::::::::::::::::::::::::::::::::
Example Usage:
    [[Wayfinder? &startId=`0`]]
::::::::::::::::::::::::::::::::::::::::
*/

$wf_base_path = $modx->config['base_path'] . 'assets/snippets/wayfinder/';

//Include a custom config file if specified
include_once("{$wf_base_path}configs/default.config.php");

$config = (!isset($config)) ? 'default' : trim($config);
if(substr($config, 0, 6) == '@CHUNK')
{
	$config = trim(substr($config, 7));
	eval('?>' . $modx->getChunk($config));
}
elseif(substr($config, 0, 5) == '@FILE')
{
	include_once($modx->config['base_path'] . trim(substr($config, 6)));
}
elseif(file_exists("{$wf_base_path}configs/{$config}.config.php"))
{
	include_once("{$wf_base_path}configs/{$config}.config.php");
}
elseif(file_exists("{$wf_base_path}configs/{$config}"))
{
	include_once("{$wf_base_path}configs/{$config}");
}
elseif(file_exists($modx->config['base_path'] . ltrim($config, '/')))
{
	include_once($modx->config['base_path'] . ltrim($config, '/'));
}

include_once($wf_base_path . 'wayfinder.inc.php');

if (class_exists('Wayfinder')) {
   $wf = new Wayfinder();
} else {
    return 'error: Wayfinder class not found';
}

$wf->_config = array(
	'id' => isset($startId) ? $startId : $modx->documentIdentifier,
	'level' => isset($level) ? $level : 0,
	'includeDocs' => isset($includeDocs) ? $includeDocs : 0,
	'excludeDocs' => isset($excludeDocs) ? $excludeDocs : 0,
	'ph' => isset($ph) ? $ph : FALSE,
	'debug' => isset($debug) ? TRUE : FALSE,
	'ignoreHidden' => isset($ignoreHidden) ? $ignoreHidden : FALSE,
	'hideSubMenus' => isset($hideSubMenus) ? $hideSubMenus : FALSE,
	'useWeblinkUrl' => isset($useWeblinkUrl) ? $useWeblinkUrl : TRUE,
	'fullLink' => isset($fullLink) ? $fullLink : FALSE,
	'nl' => isset($removeNewLines) ? '' : "\n",
	'sortOrder' => isset($sortOrder) ? strtoupper($sortOrder) : 'ASC',
	'sortBy' => isset($sortBy) ? $sortBy : 'menuindex',
	'limit' => isset($limit) ? $limit : 0,
	'cssTpl' => isset($cssTpl) ? $cssTpl : FALSE,
	'jsTpl' => isset($jsTpl) ? $jsTpl : FALSE,
	'rowIdPrefix' => isset($rowIdPrefix) ? $rowIdPrefix : FALSE,
	'textOfLinks' => isset($textOfLinks) ? $textOfLinks : 'menutitle',
	'titleOfLinks' => isset($titleOfLinks) ? $titleOfLinks : 'pagetitle',
	'displayStart' => isset($displayStart) ? $displayStart : FALSE,
	'showPrivate' => isset($showPrivate) ? $showPrivate : FALSE,
);

//get user class definitions
$wf->_css = array(
	'first' => isset($firstClass) ? $firstClass : '',
	'last' => isset($lastClass) ? $lastClass : 'last',
	'here' => isset($hereClass) ? $hereClass : 'active',
	'parent' => isset($parentClass) ? $parentClass : '',
	'row' => isset($rowClass) ? $rowClass : '',
	'outer' => isset($outerClass) ? $outerClass : '',
	'inner' => isset($innerClass) ? $innerClass : '',
	'level' => isset($levelClass) ? $levelClass: '',
	'self' => isset($selfClass) ? $selfClass : '',
	'weblink' => isset($webLinkClass) ? $webLinkClass : '',
);

//get user templates
$wf->_templates = array(
	'outerTpl' => isset($outerTpl) ? $outerTpl : '',
	'rowTpl' => isset($rowTpl) ? $rowTpl : '',
	'parentRowTpl' => isset($parentRowTpl) ? $parentRowTpl : '',
	'parentRowHereTpl' => isset($parentRowHereTpl) ? $parentRowHereTpl : '',
	'hereTpl' => isset($hereTpl) ? $hereTpl : '',
	'innerTpl' => isset($innerTpl) ? $innerTpl : '',
	'innerRowTpl' => isset($innerRowTpl) ? $innerRowTpl : '',
	'innerHereTpl' => isset($innerHereTpl) ? $innerHereTpl : '',
	'activeParentRowTpl' => isset($activeParentRowTpl) ? $activeParentRowTpl : '',
	'categoryFoldersTpl' => isset($categoryFoldersTpl) ? $categoryFoldersTpl : '',
	'startItemTpl' => isset($startItemTpl) ? $startItemTpl : '',
);

//Process Wayfinder
$output = $wf->run();

if ($wf->_config['debug']) {
	$output .= $wf->renderDebugOutput();
}

//Ouput Results
if ($wf->_config['ph']) {
    $modx->setPlaceholder($wf->_config['ph'],$output);
} else {
    return $output;
}";s:8:"WebLogin";s:2988:"# Created By Raymond Irving 2004
#::::::::::::::::::::::::::::::::::::::::
# Params:	
#
#	&loginhomeid 	- (Optional)
#		redirects the user to first authorized page in the list.
#		If no id was specified then the login home page id or 
#		the current document id will be used
#
#	&logouthomeid 	- (Optional)
#		document id to load when user logs out	
#
#	&pwdreqid 	- (Optional)
#		document id to load after the user has submited
#		a request for a new password
#
#	&pwdactid 	- (Optional)
#		document id to load when the after the user has activated
#		their new password
#
#	&logintext		- (Optional) 
#		Text to be displayed inside login button (for built-in form)
#
#	&logouttext 	- (Optional)
#		Text to be displayed inside logout link (for built-in form)
#	
#	&tpl			- (Optional)
#		Chunk name or document id to as a template
#				  
#	Note: Templats design:
#			section 1: login template
#			section 2: logout template 
#			section 3: password reminder template 
#
#			See weblogin.tpl for more information
#
# Examples:
#
#	[!WebLogin? &loginhomeid=`8` &logouthomeid=`1`!] 
#
#	[!WebLogin? &loginhomeid=`8,18,7,5` &tpl=`Login`!]

# Set Snippet Paths 
$snipPath = $modx->config['base_path'] . "assets/snippets/";

# check if inside manager
if ($m = $modx->isBackend()) {
	return ''; // don't go any further when inside manager
}

# deprecated params - only for backward compatibility
if(isset($loginid))  $loginhomeid=$loginid;
if(isset($logoutid)) $logouthomeid = $logoutid;
if(isset($template)) $tpl = $template;

# Snippet customize settings
$liHomeId   = isset($loginhomeid) ? explode(',',$loginhomeid):array($modx->config['login_home'],$modx->documentIdentifier);
$loHomeId   = isset($logouthomeid)? $logouthomeid:$modx->documentIdentifier;
$pwdReqId   = isset($pwdreqid)    ? $pwdreqid:0;
$pwdActId   = isset($pwdactid)    ? $pwdactid:0;
$loginText  = isset($logintext)   ? $logintext:'Login';
$logoutText = isset($logouttext)  ? $logouttext:'Logout';
$tpl        = isset($tpl)         ? $tpl:'';

# System settings
$webLoginMode  = isset($_REQUEST['webloginmode'])? $_REQUEST['webloginmode']: '';
$isLogOut      = $webLoginMode=='lo' ? 1:0;
$isPWDActivate = $webLoginMode=='actp' ? 1:0;
$isPostBack    = count($_POST) && (isset($_POST['cmdweblogin']) || isset($_POST['cmdweblogin_x']));
$txtPwdRem     = isset($_REQUEST['txtpwdrem'])? $_REQUEST['txtpwdrem']: 0;
$isPWDReminder = $isPostBack && $txtPwdRem=='1' ? 1:0;

$site_id = isset($site_id) ? $site_id: '';
$cookieKey = substr(md5("{$site_id}Web-User"),0,15);

# Start processing
include_once("{$snipPath}weblogin/weblogin.common.inc.php");
include_once("{$snipPath}weblogin/crypt.class.inc.php");

if ($isPWDActivate || $isPWDReminder || $isLogOut || $isPostBack) {
	# include the logger class
	include_once $modx->config['base_path'] . "manager/includes/log.class.inc.php";
	include_once "{$snipPath}weblogin/weblogin.processor.inc.php";
}

include_once "{$snipPath}weblogin/weblogin.inc.php";

# Return
return $output;
";s:13:"WebLoginProps";s:170:"&loginhomeid=Login Home Id;string; &logouthomeid=Logout Home Id;string; &logintext=Login Button Text;string; &logouttext=Logout Button Text;string; &tpl=Template;string; ";}