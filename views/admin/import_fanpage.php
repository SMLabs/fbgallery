<?php include("facebook_common.php");?>
<section class="title"><h4>Import Fan Page Galleries:&nbsp;&nbsp;</h4><h4><?php if($facebook->getUser()){?><img src="https://graph.facebook.com/<?php echo $facebook->getUser() ?>/picture" width="30" align="absmiddle"  /> <?php echo $fb_user_profile['name'] ?><?php }?></h4></section>
<section class="item">
<?php if($facebook->getUser()){?>
	<?php if(isset($albums)){ ?>

	<table>
	    <tr>
	        <th>Album Name</th>
	        <th>Import</th>
	    </tr>
        <?php if(count($albums['data'])>0){?>
		<?php foreach($albums['data'] as $v){ ?>
	    <tr>
	        <td><?php echo $v['name'] ?></td>
	        <td><form id="frm_<?php echo $v['id'] ?>" action="<?php echo site_url('admin/'.$this->module.'/import_fanpage') ?>" method="post" ><a href="javascript: void(0);" onclick="$('#frm_<?php echo $v['id'] ?>').submit();">Import</a><input type="hidden" value="<?php echo $v['id'] ?>" name="aid" /></form></td>
	    </tr>        
        <?php }?>        
        <?php }else{ ?>
			<td colspan="2">No record found </td>
		<?php }?>                
     </table>   
	<?php }elseif(isset($accounts)){?> 
		Please select a page from where you want to import Albums 
	<table>
	    <tr>
	        <th>Fan Page Name</th>
	    </tr>
		<?php foreach($accounts['data'] as $v){ ?>
	    <tr>
	        <td>
            <?php echo anchor(site_url('admin/'.$this->module.'/import_fanpage/'. $v['id']),$v['name']) ?>
	    </tr>        
        <?php }?>        
     </table>                   
    <?php }?>        
    
<?php }else{?>
<div>
	Please connect with facebook first:	<a href="#" onclick="loginUser('<?php echo site_url('admin/'.$this->module.'/import_fanpage') ?>'); return false;"><img src="<?php echo ADDONPATH."modules/".$this->module ?>/img/fbconnect.jpg" alt="Facebook Login"  width="100" /> </a>
</div>   
<?php }?>
</section>
