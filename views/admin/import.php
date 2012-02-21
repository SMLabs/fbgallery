<section class="title"><h4>Import Facebook Galleries:&nbsp;&nbsp;</h4><h4><?php if($facebook->getUser()){?><img src="https://graph.facebook.com/<?=$facebook->getUser() ?>/picture" width="30" align="absmiddle"  /> <?=$fb_user_profile['name'] ?><?php }?></h4></section>
<section class="item">
	<table>
	    <tr>
	        <th>Album Name</th>
	        <th>Import</th>
	    </tr>
		<?php foreach($albums['data'] as $v){ ?>
	    <tr>
	        <td><?=$v['name'] ?></td>
	        <td><form id="frm_<?=$v['id'] ?>" action="<?=site_url('admin/'.$this->module.'/import') ?>" method="post" ><a href="javascript: void(0);" onclick="$('#frm_<?=$v['id'] ?>').submit();">Import</a><input type="hidden" value="<?=$v['id'] ?>" name="aid" /></form></td>
	    </tr>        
        <?php }?>        
     </table>
</section>
