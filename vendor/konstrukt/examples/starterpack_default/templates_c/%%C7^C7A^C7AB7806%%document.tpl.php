<?php /* Smarty version 2.6.23-dev, created on 2009-02-01 16:37:21
         compiled from document.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'document.tpl', 3, false),)), $this); ?>
<html>
  <head>
    <title><?php echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</title>
<?php $_from = $this->_tpl_vars['styles']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['style']):
?>
    <link rel="stylesheet" href="<?php echo ((is_array($_tmp=$this->_tpl_vars['style'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" />
<?php endforeach; endif; unset($_from); ?>
<?php $_from = $this->_tpl_vars['scripts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['script']):
?>
    <script type="text/javascript" src="<?php echo ((is_array($_tmp=$this->_tpl_vars['script'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"></script>
<?php endforeach; endif; unset($_from); ?>
  </head>
  <body>
    <?php echo $this->_tpl_vars['content']; ?>

  </body>
<?php $_from = $this->_tpl_vars['onload']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['javascript']):
?>
    <script type="text/javascript">
      <?php echo $this->_tpl_vars['javascript']; ?>

    </script>
<?php endforeach; endif; unset($_from); ?>
</html>