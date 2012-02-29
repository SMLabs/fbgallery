<section class="title"><h4>Import Fan Page Galleries:&nbsp;&nbsp;</h4><h4><?php if($facebook->getUser()){?><img src="https://graph.facebook.com/<?php echo $facebook->getUser() ?>/picture" width="30" align="absmiddle"  /> <?php echo $fb_user_profile['name'] ?><?php }?></h4></section>
<section class="item">
<?php if(isset($albums)): ?>
	<table>
	    <tr>
	    	<th>Thumbnail</th>
			<th>Album Name</th>
			<th>Description</th>
			<th>Count</th>
			<th>&nbsp;&nbsp;</th>
	    </tr>
	<?php if(count($albums['data'])>0):?>
		<?php foreach($albums['data'] as $v): ?>
			<?php if(!isset($v['count'])) continue;?>
	    <tr>
			<td style="width:100px;text-align:center"><img src="<?='https://graph.facebook.com/'.$v['id'].'/picture?access_token='.$facebook->getAccessToken().'&type=thumbnail'?>"/></td>
			<td><?php echo $v['name'] ?></td>
			<td><?=isset($v['description']) ? $v['description'] : ''?></td>
			<td><?=$v['count']?> photo(s)</td>
			<td class="actions">
	<?php if(in_array($v['id'],$imported)):?>
					<a class="fbgal-remove btn red" href="<?=site_url('admin/'.$this->module.'/delete/album/'.$v['id']) ?>">Remove</a>
	<?php else:?>
					<form id="frm_<?=$v['id'] ?>" action="<?=site_url('admin/'.$this->module.'/import_fanpage') ?>" method="post" >
						<button type="submit" name="btnAction" value="save" class="btn green">Import</button><input type="hidden" value="<?=$v['id'] ?>" name="aid" />
					</form>
	<?php endif;?>
			</td>
	    </tr>
	    
		<?php endforeach;?>
	<?php else: ?>
			<td colspan="5">No record found </td>
	<?php endif;?>
     </table>
     
<?php elseif(isset($accounts)):?> 
	<p>Listed below are all of the fanpages that you ahve admin privilage for. select one to get started importing albums.</p>
	<table>
		<tr>
			<th>FanPages</th>
		</tr>
	<?php foreach($accounts['data'] as $v): ?>
		<tr>
			<td>
				<?php echo anchor(site_url('admin/'.$this->module.'/import_fanpage/'. $v['id']),'<img src="http://graph.facebook.com/'.$v['id'].'/picture"/>') ?>&nbsp;&nbsp;
				<?php echo anchor(site_url('admin/'.$this->module.'/import_fanpage/'. $v['id']),$v['name']) ?>
			</td>
		</tr>
	<?php endforeach;?>
     </table>
<?php endif;?>
</section>
