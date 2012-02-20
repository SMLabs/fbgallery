<?php include("facebook_common.php");?>
<section class="title"><h4>Import Facebook Galleries:&nbsp;&nbsp;</h4><h4><?php if($facebook->getUser()){?><img src="https://graph.facebook.com/<?php echo $facebook->getUser() ?>/picture" width="30" align="absmiddle"  /> <?php echo $fb_user_profile['name'] ?><?php }?></h4></section>
<section class="item">
<?php if($facebook->getUser()){?>
	<table>
	    <tr>
	        <th>Album Name</th>
	        <th>Import</th>
	    </tr>
		<?php foreach($albums['data'] as $v){ ?>
	    <tr>
	        <td><?php echo $v['name'] ?></td>
	        <td><form id="frm_<?php echo $v['id'] ?>" action="<?php echo site_url('admin/'.$this->module.'/import') ?>" method="post" ><a href="javascript: void(0);" onclick="$('#frm_<?php echo $v['id'] ?>').submit();">Import</a><input type="hidden" value="<?php echo $v['id'] ?>" name="aid" /></form></td>
	    </tr>        
        <?php }?>        
     </table>   
<?php }else{?>
<div>
	Please connect with facebook first:	<a href="#" onclick="loginUser('<?php echo site_url('admin/'.$this->module.'/import') ?>'); return false;"><img src="<?php echo ADDONPATH."modules/".$this->module ?>/img/fbconnect.jpg" alt="Facebook Login"  width="100" /> </a>
</div>   
<?php }?>
</section>
