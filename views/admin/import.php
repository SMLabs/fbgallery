<section class="title">
	<h4>Import Facebook Galleries:&nbsp;<img src="https://graph.facebook.com/<?=$facebook->getUser()?>/picture" width="30" align="absmiddle"  /> <?=$fb_user_profile['name']?></h4>
</section>
<section class="item">
	<table>
	    <thead>
	        <th>Album Name</th>
	        <th>&nbsp;&nbsp;</th>
	    </thead>
		<tbody>
<?php foreach($albums['data'] as $v):?>
			<tr>
				<td class="">
					<div><?=$v['name']?></div>
					<div><img src="http://graph.facebook.com/<?=$v['id']?>/picture" /></div>
				</td>
				<td class="actions collapse">
					<form id="frm_<?=$v['id'] ?>" action="<?=site_url('admin/'.$this->module.'/import') ?>" method="post" >
						<button type="submit" name="btnAction" value="save" class="btn green">Import</button><input type="hidden" value="<?=$v['id'] ?>" name="aid" />
					</form>
				</td>
			</tr>
<?php endforeach;?>
		</tbody>
     </table>
</section>
