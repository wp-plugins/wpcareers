{include file='header.tpl'}

<div class="columns-7 featured-content">
   <table class="categoryTable col7"><tbody>
      <tr><td><h2>{$main_link}</h2><HR /><h2>{$lang.J_FRIENDSEND}</h2>{$lang.MARKED_OPTION}</td></tr>
      <tr valign="top"><td>
         <form method="post" id="jp_send_job" name="jp_send_job" onsubmit="this.sub.disabled=true;this.sub.value='Sending Job...';" action="">
            <input type="hidden" name="jp_send_job" value="yes" />
            <table border=0 cellpadding=3 cellspacing=3 >
               <tr><td class="td_left">{$lang.J_TITLE}</td><td class="td_right">{$title}</td></tr>
               <tr><td class="td_left">{$lang.J_DESC}</td><td class="td_right">{$desctext}</td></tr>
               <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_NAME}*</td>
               <td class="td_right"><input type="text" name="wpcareers[yourname]" value="{$yourname}" size="35"></td>
               </tr>
               <tr>
               <td class="td_left">{$lang.J_MAIL}*</td>
               <td class="td_right"><input type="text" name="wpcareers[mailfrom]" value="{$mailfrom}" size="35"></td>
               </tr>
               <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_NAMEFR}*</td>
               <td class="td_right"><input type="text" name="wpcareers[fname]" value="{$fname}" size="35"></td>
               </tr>
               <tr>
               <td class="td_left">{$lang.J_MAILFR}*</td>
               <td class="td_right"><input type="text" name="wpcareers[mailto]" value="{$mailto}" size="35"></td>
               </tr>
               <tr bgcolor="#F4F4F4">
                  <td class="td_left" valign="top">{$lang.J_MESSAGE}*</td>
                  <td class="td_right"><textarea rows="6" name="wpcareers[maildesc]" cols="55">{$maildesc}</textarea><br /><span class ="smallTxt">Maximum 250 characters</span></td>
               </tr>
               {$confirm}
               <tr bgcolor="#F4F4F4"><td></td><td><input type=submit value="{$lang.J_SENDFR}" name=pcareers[send]></p></td></tr>
               <tr><td class="td_left"></td><td class="td_right">{$lang.PRIVATE_POLICY}</td></tr>
            </table>
         </form>
      </td>
      </tr></tbody>
   </table>
</div>


{include file='footer.tpl'}