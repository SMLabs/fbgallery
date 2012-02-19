<div id="fb-root"></div>
<section class="title"><h4>Facebook Gallery Settings:</h4></section>
<section class="item">
<form  action="<?php echo site_url('admin/'.$this->module.'/settings') ?>" method="post" >
<table>
    <tr>
        <th>Application ID</th>
        <td><input type="text" name="app_id" value="<?php echo $app_id ?>"  /></td>
    </tr>
    <tr>
        <th>Application Secret</th>
        <td><input type="text" name="app_secret" value="<?php echo $app_secret ?>"  /></td>
    </tr>        
 </table>   
<div align="right"> <input type="submit" value="Update"  /></div>
</form>
</section>
