<?php 
/* 
 * Searches document registry first, then $app, then constansts. 
 */

$cx_ses = (\cx_configure::a_get('cx', 'session_variable')) ? \cx_configure::a_get('cx', 'session_variable') : '';
$cx_const_title = (defined('CX_PAGE_TITLE')) ? CX_PAGE_TITLE : '';
$cx_const_keywords = (defined('CX_KEYWORDS')) ? CX_KEYWORDS : '';
$cx_const_desc = (defined('CX_DESCRIPTION')) ? CX_DESCRIPTION : '';
$title = $local->registry->get('document')->get_title();
$b_title = (! empty($local->title)) ? $local->title : $cx_const_title;
$cx_title = (! empty($title)) ? $title : $b_title;
$desc = $local->registry->get('document')->get_description();
$b_desc = (! empty($local->description)) ? $local->description : $cx_const_desc;
$cx_desc = (! empty($desc)) ? $desc : $b_desc;
$key = $local->registry->get('document')->get_keywords();
$b_keywords = (! empty($local->keywords)) ? $local->keywords : $cx_const_keywords;
$cx_keywords = (! empty($key)) ? $key : $b_keywords;
$cx_keys = (! empty($cx_keywords)) ? "<meta name=\"keywords\" content=\"{$cx_keywords}\">\r\n" : "\r\n";
$cx_const_robots = (defined('CX_ROBOTS')) ? CX_ROBOTS : 'INDEX';
$cx_robots = (! empty($local->robots)) ? $local->robots : $cx_const_robots;

$links = $local->registry->get('document')->get_links();
$styles = $local->registry->get('document')->get_styles();
$scripts = $local->registry->get('document')->get_scripts();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <base href="<?php echo PROJECT_BASE_REF; ?>"/> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $cx_keys; ?>
    <meta name="description" content="<?php echo $cx_desc; ?>">
    <meta name="author" content="Chris Allen, Robert Strutts">
    <meta name="language" content="english">
    <meta name="robots" content="<?php echo $cx_robots; ?>">
    <meta name="copyright" content="2014-<?php echo date('Y'); ?>">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="canonical" href="<?php echo CX_CANONICAL; ?>"> 
    <title><?php echo $cx_title; ?></title>
    
    <?php foreach ($links as $link) { ?>
      <link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
    <?php } ?>

    <?php 
      echo $local->main_styles; 
      echo $local->styles; 
    ?>
  
    <link rel="stylesheet" href="<?php echo PROJECT_BASE_REF. '/assets/footer.css'?>" type="text/css" media="screen" />  
          
    <?php foreach ($styles as $style) { ?>
      <link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
    <?php } ?>
        
  </head>
   
  <body id="my-page" data-ng-app="dealertoolz_app">
    <div id="wrap">
        <div id="autosavemessage"></div>
        
        <?php if (!empty($local->header)) { ?>
        <div class="page-header">
          <h1><?php echo $local->header; ?></h1>
        </div>
        <?php } ?>
        
        <?php if (isset($_SESSION[$cx_ses . 'message'])) { ?>
                <div id="message">
                    <div id="message-content">
                        <?php 
                            echo $_SESSION[$cx_ses . 'message'];
                            unset($_SESSION[$cx_ses . 'message']); 
                        ?>
                    </div>
                </div>
        <?php } ?>

        <?php
        $crumbs = (isset($local->breadcrumb) && is_array($local->breadcrumb)) ? $local->breadcrumb : array();
        if (count($crumbs) > 0 || ! empty($local->active_crumb)) {
        ?>
<!-- Topic Header -->
  <div class="topic">
    <div class="container">
      <div class="row">
        <div class="col-sm-8">
          <ol class="breadcrumb pull-right hidden-xs">
          <?php
          foreach($crumbs as $crumb_link => $crumb_name) {
            echo "<li><a href=\"{$crumb_link}\">{$crumb_name}</a></li>";
          }
          ?>
            <li class="active"><?php echo "{$local->active_crumb}";?></li>
          </ol>
        </div>
      </div>
    </div>
  </div>
        <?php } ?>   

        <?php echo $this->page; ?>

    </div>
<?php if (isset($local->footer) && ! empty($local->footer)) { ?>   
<footer>
    <div id="footer">
      <div class="container">
        <?php echo $local->footer; ?>
      </div>
    </div>
</footer>
<?php } ?> 

    <?php 
      echo $local->main_scripts; 
      echo $local->scripts; 
    ?>  
    <?php foreach ($scripts as $script) { ?>
      <script src="<?php echo $script; ?>" type="text/javascript"></script>
    <?php 
    }
     
    echo $local->js_onready;
    ?>

      
  </body>
</html>
