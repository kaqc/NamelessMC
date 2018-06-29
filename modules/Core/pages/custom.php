<?php
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-pr3
 *
 *  License: MIT
 *
 *  Custom page
 */

// Get page info from URL
$custom_page = $queries->getWhere('custom_pages', array('url', '=', rtrim($route, '/')));
if(!count($custom_page)){
    require(ROOT_PATH . '/404.php');
    die();
} else
    $custom_page = $custom_page[0];

// Check permissions
$perms = $queries->getWhere('custom_pages_permissions', array('page_id', '=', $custom_page->id));
if($user->isLoggedIn()){
    $groups = $user->getAllGroups($user->data()->id);
    foreach($groups as $group){
        foreach($perms as $perm){
            if($perm->group_id == $group){
                if($perm->view == 1){
                    $can_view = 1;
                    break 2;
                } else
                    break;
            }
        }
    }
} else {
    foreach($perms as $perm){
        if($perm->group_id == 0){
            if($perm->view == 1)
                $can_view = 1;

            break;
        }
    }
}

if(!isset($can_view)){
    require(ROOT_PATH . '/404.php');
    die();
}

// Always define page name
define('PAGE', $custom_page->id);
?>
<!DOCTYPE html>
<html<?php if(defined('HTML_CLASS')) echo ' class="' . HTML_CLASS . '"'; ?> lang="<?php echo (defined('HTML_LANG') ? HTML_LANG : 'en'); ?>" <?php if(defined('HTML_RTL') && HTML_RTL === true) echo ' dir="rtl"'; ?>>
<head>
    <!-- Standard Meta -->
    <meta charset="<?php echo (defined('LANG_CHARSET') ? LANG_CHARSET : 'utf-8'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <!-- Site Properties -->
    <?php
    $title = Output::getClean($custom_page->title);
    require(ROOT_PATH . '/core/templates/header.php');
    ?>
    <link rel="stylesheet" href="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/ckeditor/plugins/spoiler/css/spoiler.css">
    <link rel="stylesheet" href="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/emoji/css/emojione.min.css"/>
    <link rel="stylesheet" href="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/emoji/css/emojione.sprites.css"/>
    <link rel="stylesheet" href="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/emojionearea/css/emojionearea.min.css"/>
</head>
<body>
<?php
require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

$smarty->assign(array(
    'CONTENT' => (($custom_page->all_html == 0) ? Output::getPurified(htmlspecialchars_decode($custom_page->content)) : htmlspecialchars_decode($custom_page->content)),
	'TITLE' => ($title)
));

// Display template
$smarty->display(ROOT_PATH . '/custom/templates/' . TEMPLATE . '/custom.tpl');

require(ROOT_PATH . '/core/templates/scripts.php');
?>
<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/emoji/js/emojione.min.js"></script>
<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/ckeditor/plugins/spoiler/js/spoiler.js"></script>
</body>
</html>

