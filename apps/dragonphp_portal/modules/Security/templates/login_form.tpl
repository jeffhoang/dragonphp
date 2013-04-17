
<div id="content">

<div id="form_content">


<h3>Login</h3>

<table cellpadding=4 style="border: solid 1px #cccccc" cellspacing=2 width=150>


{if sizeof($model->errors) > 0}
<tr><td colspan="2">
<b>Login Error!</b>
<ul>
{foreach from=$model->errors key=key item=item}
  <li>{$item}
{/foreach}
</ul>
</font></b>
</td></tr>

{/if}

<form action="?" method="POST" name="loginForm">

<input type="hidden" name="module" value="{$model->module}">
<input type="hidden" name="controller" value="{$model->controller}">

<tr>
<td align="right">Username:</td>
<td align="left"><input type="text" name="username" value="{$model->username|default:''}" size="16"></td>
</tr>

<tr>
<td align="right" valign="top">Password:</td>
<td align="left"><input type="password" name="password" value="{$model->password|default:''}" size="16"></td>
</tr>

<tr>
<td align="center" colspan=2><input type="submit" name="submit" class="submit_button" value="{$model->submit_button|default:'submit'}"></td>
</tr>
</form>
</table>
<br>

</div>

</div>