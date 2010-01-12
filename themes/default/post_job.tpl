{*
 * $Revision: $
 * Description: Wordpress wpCareers
 * POST_JOB_Templates
*}


{include file='header.tpl'}

<div class="columns-7 featured-content">
   <table class="categoryTable col7"><tbody>
   <tr>
      <td><h2>{$main_link}</h2><HR />
         <h3>{$lang.J_ADDJOB}</h3>{$lang.MARKED_OPTION}</td></tr>
   
   <tr valign="top"><td>
      <form method="post" id="jp_post_job" name="jp_post_job" enctype="multipart/form-data" onsubmit="this.sub.disabled=true;this.sub.value='Posting Job...';" action="">
         
         <input type="hidden" name="wpcareers_post_topic" value="yes" />
         <table border=0 cellpadding=3 cellspacing=3 >
            <tr>
               <td class="td_left">Set the job to:</td>
               <td class="td_right">
                <input name="status" value="ACT" checked="checked" type="radio"> Active | <input name="status" value="SUS" type="radio"> Suspended 
               </td>
            </tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_CAT}* {$item.category}</td>
               <td class="td_right">
               <select name="wpcareers[category]">
               {html_options values=$categoryId output=$categoryTitle selected=$categorySelected}
               </select></td>
            </tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_TYPE}*</td>
               <td class="td_right">
               <select name="wpcareers[type]">
               {html_options values=$typeId output=$typeTitle selected=$typeSelected}
               </select></td>
            </tr>
            <tr><td><span class="red">{$lang.JH_EMPLOYER}</span></td><td></td></tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_TITLE}*</td>
               <td class="td_right">
               <input type="text" name="wpcareers[title]" value="{$title}" size="50">
            </tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_COMPANY}*</td>
               <td class="td_right">
               <input type="text" name="wpcareers[company]" value="{$company}" size="50">
            </tr>
            <tr>
               <td class="td_left">{$lang.J_PHOTO}</td>
               <td class="td_right">
               <table>
                  <tr>
                     <td class="top"><input type="hidden" name="wpcareers[oldFileName]" value="{$oldFileName}" /><input type="file" name="photo" /></td>
                     <td>&nbsp;&nbsp;{if $_photo}{$_photo}{else}<span class ="smallTxt">{$lang.J_NOIMAGE}</span>{/if}</td>
                  </tr>
                  <tr>
                     <td class="top"><span class ="smallTxt">&nbsp;JPG, GIF or PNG file ({$photomax})</span></td>
                     <td>{if $_photo}<input type=checkbox name="remove_photo">&nbsp;Delete this Photo{/if}</td>
                  </tr>
               </table>      
               </td>
            </tr>
            <tr>
               {assign var=DESCTEXT value=$desctext}
               <td class="td_left" valign="top">{$lang.J_DESC}*</td>
               <td>{php} 
                  create_description($this->get_template_vars('DESCTEXT'), 'desctext', 'jp_post_job');
               {/php}</td>
            </tr>
            <tr>
               <td class="td_left">{$lang.J_REQUIRE}</td>
               <td class="td_right">
               <input type="text" name="wpcareers[requirements]" value="{$requirements}" size="60"></td>
            </tr>
            <tr><td><span class="red">{$lang.JH_AWARDS}</span></td><td></td></tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_PRICE}</td>
               <td class="td_right">
               <input type="text" name="wpcareers[price]" value="{$price}" size="20">&nbsp;
               <select name="wpcareers[pricetype]">
               {html_options values=$priceId output=$priceTitle selected=$priceSelected}
               </select></td>
            </tr>
            <tr><td><span class="red">{$lang.J_REQUIRE}</span></td><td></td></tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_SURNAME}*</td>
               <td class="td_right">
               <input type="text" name="wpcareers[submitter]" value="{$submitter}" size="50"></td>
            </tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_EMAIL}*</td>
               <td class="td_right">
               <input type="text" name="wpcareers[email]" value="{$email}" size="30"><span class="smallTxt">&nbsp;{$lang.J_HATE}</span></td>
            </tr>
            <tr>
               <td class="td_left">{$lang.J_TEL}</td>
               <td class="td_right">
               <input type="text" name="wpcareers[tel]" value="{$tel}" size="20"><span class="smallTxt">&nbsp;like: (800) 555-123 or +49 123-12345</span></td>
            </tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_FAX}</td>
               <td class="td_right">
               <input type="text" name="wpcareers[fax]" value="{$fax}" size="20"><span class="smallTxt">&nbsp;like: (+98) 555-123 or 1800 12345678</span></td>
            </tr>
            <tr>
               <td class="td_left" valign="top">{$lang.J_CONTACTINFO}</td>
               <td class="td_right">
	       <textarea rows="6" name="contactinfo" cols="60" id="contactinfo">{$contactinfo}</textarea><br />
			<span class ="smallTxt"><span id="charLeft"> </span>&nbsp;chars left. Maximum {$wpca_settings.excerpt_length} characters</span>
	       </td>
            </tr>
            <tr><td><span class="red">{$lang.JH_LOCALINFO}</span></td><td></td></tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_TOWN}*</td>
               <td class="td_right">
               <input type="text" name="wpcareers[town]" value="{$town}" size="30"></td>
            </tr>
            <tr>
               <td class="td_left">{$lang.J_STATE}</td>
               <td class="td_right">
               <input type="text" name="wpcareers[state]" value="{$state}" size="30"></td>
            </tr>
            <tr>
               <td class="td_left">{$lang.J_HOWLONG}</td>
               <td class="td_right">
               <input type="text" name="wpcareers[expire]" size="3" maxlength="3" value="{$expire}" /> <span class ="smallTxt">({$expiredefault} days)</span></td>
            </tr>
            <tr>
               <td></td>
               <td class="td_right"><input type="checkbox" id="wpcareers[agree]" name="wpcareers[agree]" checked />&nbsp;Please agree to our policy*</td>
            </tr>
            {$confirm}
            <tr bgcolor="#F4F4F4">
               <td></td><td><p>{$lang.J_AG}<br />
               <input type="hidden" name="lid" value="0" /></p>
               <input type=submit value="{$lang.J_SUBMIT}"></td>
            </tr>
         </table>
      </form>
   </td></tr></tbody></table>
</div>

{include file='footer.tpl'}