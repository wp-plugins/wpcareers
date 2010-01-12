{*
 * $Revision: $
 * Description: Wordpress wpCareers
 * Header_Templates
*}



<div class="top_title"><a href = "{$mainLink}">HOME</a>
| <a href = "/content/contact-us/">CONTACT US</a> 
| <a href = "/content/terms-of-service/">Terms Of Service</a></div>

<div class="post" id="post-01">
   <div class="loginform">
      <table>
      <tr><td width=670>
      {if $message}
         {$headpic}<div class="jp_message">{$message}</div>
      {else}
         {$headpic}&nbsp;{$headtxt}
      {/if}
      </td>
      <td>
      <div class="jp_login">
         {if $user_ID}
            {$lang.J_HELLO} <strong>{$user_identity}!</strong> Welcome to {php}bloginfo('name'); {/php}<br />
            {if $permission >= 1 }
               <a href="{$mainLink}">Home</a> | <a href="{$siteurl}/wp-admin/profile.php">My Profile</a> | <a href="{$siteurl}/wp-login.php?action=logout">Logout</a>
               <p><b>Your Status Summary:</b></p>
               {if $ljobs}
                  {assign var=cnt value=0}
                  {foreach from=$ljobs item=item key=key}
                     <span class="smallTxt">{$item.viewjob} was viewed {$item.l_view}</span><br />
                  {/foreach}
               {/if}
               {if $lresume}
                  {assign var=cnt value=0}
                  {foreach from=$lresume item=item key=key}
                     <span class="smallTxt">{$item.viewResume} was viewed {$item.r_view}</span><br />
                  {/foreach}
               {/if}
            {/if}
         {else}
            <form action="{$siteurl}/wp-login.php?action=login" method="post">
               <label>User: </label><input type="text" name="log" id="log" class="editbox" value="{$user_login}" size="12" /><br />
               <label>Password:</label><input type="password" name="pwd" id="pwd" class="editbox" size="12" /><br />
               <label>Remember me:</label><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /><BR />
               <input type="submit" name="submit" value="Login" class="button" />
               <input type="hidden" name="redirect_to" value="{$REQUEST_URI}"/>
            </form>
            <P><a href="{$siteurl}/wp-register.php">Register</a> or 
            <a href="{$siteurl}/wp-login.php?action=lostpassword">Recover password?</a>
         {/if}
      </div>
      <!-- jp_login -->
      </td></tr>
      </table>
      {*if $googleAd}{/if*}
   </div>
</div>
<!-- loginform -->
