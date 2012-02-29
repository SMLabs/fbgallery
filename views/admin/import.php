<section class="title">
	<h4>Import Facebook Galleries:&nbsp;<img src="https://graph.facebook.com/<?=$facebook->getUser()?>/picture" width="30" align="absmiddle"  /> <?=$fb_user_profile['name']?></h4>
</section>
<section class="item">
	<table>
	    <thead>
	        <th>Thumbnail</th>
	        <th>Album Name</th>
	        <th>Description</th>
	        <th>Count</th>
	        <th>&nbsp;&nbsp;</th>
	    </thead>
		<tbody>
<?php foreach($albums['data'] as $v):?>
	<?php if(!isset($v['count']))continue;?>
				<td style="width:100px;text-align:center">
					<img src="<?='https://graph.facebook.com/'.$v['id'].'/picture?access_token='.$facebook->getAccessToken().'&type=thumbnail'?>"/>
				</td>
				<td>
					<?=$v['name']?>
				</td>
				<td>
					<?=isset($v['description']) ? $v['description'] : ''?>
				</td>
				<td>
					<?=$v['count']?> photo(s)
				</td>
				<td class="actions collapse">
	<?php if(in_array($v['id'],$imported)):?>
					<a class="fbgal-remove btn red" href="<?=site_url('admin/'.$this->module.'/delete/album/'.$v['id']) ?>">Remove</a>
	<?php else:?>
					<form id="frm_<?=$v['id'] ?>" action="<?=site_url('admin/'.$this->module.'/import') ?>" method="post" >
						<button type="submit" name="btnAction" value="save" class="btn green">Import</button><input type="hidden" value="<?=$v['id'] ?>" name="aid" />
					</form>
	<?php endif;?>
				</td>
			</tr>
<?php endforeach;?>
		</tbody>
     </table>
</section>
