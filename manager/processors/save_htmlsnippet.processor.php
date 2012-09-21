<?php
if(!defined('IN_MANAGER_MODE') || IN_MANAGER_MODE != 'true') exit();
if (!$modx->hasPermission('save_chunk')) {
    $e->setError(3);
    $e->dumpError();
}

$id = intval($_POST['id']);
$snippet = $modx->db->escape($_POST['post']);
$name = (isset($_POST['name']) && $_POST['name'] !== '') ? $modx->db->escape(trim($_POST['name'])) : 'Untitled chunk';
$description = $modx->db->escape($_POST['description']);
$locked = $_POST['locked'] == 'on' ? 1 : 0;
$editor_type = $_POST['editor_type'] == '1' ? 1 : 0;
$published = $_POST['published'] == '1' ? 1 : 0;
$pub_date = $_POST['pub_date'];
$unpub_date = $_POST['unpub_date'];

// determine published status
$currentdate = time();

if (empty($pub_date)) {
    $pub_date = 0;
} else {
    $pub_date = $modx->toTimeStamp($pub_date);
    if (empty($pub_date)) {
        $modx->manager->saveFormValues(78);
        $url = "index.php?a=78&id={$id}";
        include_once "header.inc.php";
        $modx->webAlert($_lang["mgrlog_dateinvalid"], $url);
        include_once "footer.inc.php";
        exit;
    } elseif ($pub_date < $currentdate)
        $published = 1;
    elseif ($pub_date > $currentdate)
        $published = 0;
}
if (empty($unpub_date))
    $unpub_date = 0;
else {
    $unpub_date = $modx->toTimeStamp($unpub_date);
    if (empty($unpub_date)) {
        $modx->manager->saveFormValues(78);
        $url = "index.php?a=78&id={$id}";
        include_once "header.inc.php";
        $modx->webAlert($_lang["mgrlog_dateinvalid"], $url);
        include_once "footer.inc.php";
        exit;
    } elseif ($unpub_date < $currentdate)
        $published = 0;
}

$tbl_site_htmlsnippets = $modx->getFullTableName('site_htmlsnippets');

//Kyle Jaebker - added category support
if (empty($_POST['newcategory']) && $_POST['categoryid'] > 0) {
    $category = $modx->db->escape($_POST['categoryid']);
} elseif (empty($_POST['newcategory']) && $_POST['categoryid'] <= 0) {
    $category = 0;
} else {
    include_once($modx->config['core_path'].'categories.inc.php');
    $catCheck = checkCategory($modx->db->escape($_POST['newcategory']));
    if ($catCheck) {
        $category = $catCheck;
    } else {
        $category = newCategory($_POST['newcategory']);
    }
}

switch ($_POST['mode']) {
    case '77':

        // invoke OnBeforeChunkFormSave event
        $modx->invokeEvent("OnBeforeChunkFormSave", array(
            "mode" => "new",
            "id" => $id
        ));

        // disallow duplicate names for new chunks
        $rs = $modx->db->select('COUNT(id)', $tbl_site_htmlsnippets, "name='{$name}'");
        $count = $modx->db->getValue($rs);
        if ($count > 0) {
            $url = "index.php?a=77";
            $msg = sprintf($_lang['duplicate_name_found_general'], $_lang['chunk'], $name);
            $modx->manager->saveFormValues(77);
            include_once "header.inc.php";
            $modx->webAlert($msg, $url);
            include_once "footer.inc.php";
            exit;
        }
        //do stuff to save the new doc
        $field = compact(explode(',', 'name,description,published,pub_date,unpub_date,snippet,locked,editor_type,category'));
        $rs = $modx->db->insert($field, $tbl_site_htmlsnippets);
        if (!$rs) {
            echo "\$rs not set! New Chunk not saved!";
        } else {
            // get the id
            if (!$newid = $modx->db->getInsertId()) {
                echo "Couldn't get last insert key!";
                exit;
            }

            // invoke OnChunkFormSave event
            $modx->invokeEvent("OnChunkFormSave", array(
                "mode" => "new",
                "id" => $newid
            ));

            // empty cache
            $modx->clearCache(); // first empty the cache
            // finished emptying cache - redirect
            if ($_POST['stay'] != '') {
                $a = ($_POST['stay'] == '2') ? "78&id={$newid}" : "77";
                $header = "Location: index.php?a={$a}&stay={$_POST['stay']}";
            } else {
                $header = "Location: index.php?a=76";
            }
            header($header);
        }
        break;
    case '78':

        // invoke OnBeforeChunkFormSave event
        $modx->invokeEvent("OnBeforeChunkFormSave", array(
            "mode" => "upd",
            "id" => $id
        ));

        if (check_exist_name($name) !== false) {
            $url = "index.php?a=78&id={$id}";
            $msg = sprintf($_lang['duplicate_name_found_general'], $_lang['chunk'], $name);
            $modx->manager->saveFormValues(78);
            include_once "header.inc.php";
            $modx->webAlert($msg, $url);
            include_once "footer.inc.php";
            exit;
        }

        //do stuff to save the edited doc
        $was_name = $modx->db->getValue($modx->db->select('name', $tbl_site_htmlsnippets, "id='{$id}'"));
        $field = compact(explode(',', 'name,description,published,pub_date,unpub_date,snippet,locked,editor_type,category'));
        $rs = $modx->db->update($field, $tbl_site_htmlsnippets, "id='{$id}'");
        if (!$rs) {
            echo "\$rs not set! Edited htmlsnippet not saved!";
        } else {
            $name = stripslashes($name);
            $name = str_replace("'", "''", $name);
            $was_name = str_replace("'", "''", $was_name);
            if ($name !== $was_name) {
                $modx->db->update("content=REPLACE(content,'{{{$was_name}}}','{{{$name}}}')", $modx->getFullTableName('site_content'));
                $modx->db->update("content=REPLACE(content,'{{{$was_name}}}','{{{$name}}}')", $modx->getFullTableName('site_templates'));
                $modx->db->update("snippet=REPLACE(snippet,'{{{$was_name}}}','{{{$name}}}')", $modx->getFullTableName('site_htmlsnippets'));
                $modx->db->update("value=REPLACE(value,    '{{{$was_name}}}','{{{$name}}}')", $modx->getFullTableName('site_tmplvar_contentvalues'));
                $modx->db->update("content=REPLACE(content,'{{{$was_name}:','{{{$name}:')", $modx->getFullTableName('site_content'));
                $modx->db->update("content=REPLACE(content,'{{{$was_name}:','{{{$name}:')", $modx->getFullTableName('site_templates'));
                $modx->db->update("snippet=REPLACE(snippet,'{{{$was_name}:','{{{$name}:')", $modx->getFullTableName('site_htmlsnippets'));
                $modx->db->update("value=REPLACE(value,    '{{{$was_name}:','{{{$name}:')", $modx->getFullTableName('site_tmplvar_contentvalues'));
            }

            // invoke OnChunkFormSave event
            $modx->invokeEvent("OnChunkFormSave", array(
                "mode" => "upd",
                "id" => $id
            ));

            // empty cache
            $modx->clearCache(); // first empty the cache
            // finished emptying cache - redirect
            if ($_POST['stay'] != '') {
                $a = ($_POST['stay'] == '2') ? "78&id={$id}" : "77";
                $header = "Location: index.php?a={$a}&stay={$_POST['stay']}";
            } else {
                $header = "Location: index.php?a=76";
            }
            header($header);
        }
        break;
    default:
}

function check_exist_name($name) { // disallow duplicate names for new chunks
    global $modx;
    $tbl_site_htmlsnippets = $modx->getFullTableName('site_htmlsnippets');
    $where = "name='{$name}'";
    if ($_POST['mode'] == 78) {
        $where = $where . " AND id!={$_POST['id']}";
    }
    $rs = $modx->db->select('COUNT(id)', $tbl_site_htmlsnippets, $where);
    $count = $modx->db->getValue($rs);
    if ($count > 0)
        return true;
    else
        return false;
}
