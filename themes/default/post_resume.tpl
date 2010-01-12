{*
 * $Revision: $
 * Description: Wordpress wpCareers
 * POST_RESUME_Templates
*}


{include file='header.tpl'}

<div class="columns-7 featured-content">
   <table class="categoryTable col7"><tbody>
   <tr>
      <td><h2>{$main_link}</h2><HR />
          <h3>{$lang.J_ADDRESUME}</h3>{$lang.MARKED_OPTION}</td>
   </tr>

   <tr valign="top"><td>
      <form method="post" id="jp_post_resume" name="jp_post_resume" enctype="multipart/form-data" onsubmit="this.sub.disabled=true;this.sub.value='Posting Resume...';" action="">
         <input type="hidden" name="wpcareers_post_topic" value="yes" />
         <table border=0 cellpadding=3 cellspacing=3 >
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_CAT}* {$item.category}</td>
               <td class="td_right"><select name="wpcareers[category]">{html_options values=$categoryId output=$categoryTitle selected=$categorySelected}</select></td>
            </tr>
            {*
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_TYPE}*</td>
               <td class="td_right">
               <select name="wpcareers[type]">
                  {html_options values=$typeId output=$typeTitle selected=$typeSelected}
               </select>
               </td>
            </tr>
            *}
            <tr>
               <td class="td_left">{$lang.J_TITLE}*</td>
               <td class="td_right"><input type="text" name="wpcareers[title]" value="{$title}" size="50"></td>
            </tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.R_NAME}*</td>
               <td><input type="text" name="wpcareers[name]" value="{$name}" size="50"></td>
            </tr>
	         <tr><td><span class="red">{$lang.R_CONTACTINFO}</span></td><td></td></tr>
            <!--tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_SURNAME}</td>
               <td><input type="text" name="wpcareers[information]" value="{$information}" size="50"></td>
            </tr-->
	         <tr>
               <td class="td_left">{$lang.R_CAREER}*</td>
               <td class="td_right">
	            <textarea rows="6" name="contactinfo" cols="60" id="contactinfo">{$information}</textarea><br />
      			<span class ="smallTxt"><span id="charLeft"> </span>&nbsp;chars left. Maximum {$wpca_settings.excerpt_length} characters</span>
	            </td>
            </tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_EMAIL}*</td>
               <td class="td_right">
               <input type="text" name="wpcareers[email]" value="{$email}" size="30"></td>
            </tr>
            <tr>
               <td class="td_left">{$lang.J_TEL}</td>
               <td class="td_right">
               <input type="text" name="wpcareers[tel]" value="{$tel}" size="30"><span class="smallTxt">&nbsp;like: (800) 555-123 or +49 123-12345<span></td>
            </tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.J_FAX}</td>
               <td><input type="text" name="wpcareers[fax]" value="{$fax}" size="30"><span class="smallTxt">&nbsp;like: +98 (311)-123 or 0880 123-45678</span></td>
            </tr>
            <tr>
               <td class="td_left">{$lang.R_TOWN}*</td>
               <td><input type="text" name="wpcareers[town]" value="{$town}" size="30"></td>
            </tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.R_STATE}</td>
               <td><input type="text" name="wpcareers[state]" value="{$state}" size="30"></td>
            </tr>
            <tr><td><span class="red">{$lang.RH_DESCSKILLS}</span></td></tr>
            <tr>
               {assign var=DESCTEXT value=$desctext}
               <td class="td_left" valign="top">{$lang.R_DESCEXP}*</td>
               <td>{php}create_description($this->get_template_vars('DESCTEXT'), 'desctext', 'jp_post_resume');{/php}</td>
            </tr>
            <tr bgcolor="#F4F4F4"><td class="td_left">{$lang.R_PHOTO}</td>
               <td class="td_right">
               <span class ="smallTxt">upload a detailed resume and a photograph.</span><br />
               <table>
                  <tr>
                     <td><input type="hidden" name="wpcareers[oldFileName]" value="{$oldFileName}" /><input type="file" name="photo" /></td>
                     <td>&nbsp;&nbsp;&nbsp;&nbsp;{if $_photo}{$_photo}{/if}</td>
                  </tr>
                  <tr>
                     <td class="top"><span class="smallTxt">&nbsp;JPG, GIF or PNG file ({$photomax})</span></td>
                     <td>{if $_photo}<input type=checkbox name="remove_photo">&nbsp;Delete this Photo{/if}</td>
                  </tr>
               </table>
               </td>
            </tr>
            <tr><td class="td_left">{$lang.R_UPLOAD}</td>
               <td class="td_right">
		            <span class ="smallTxt">If you already have your resume in a MS Word, WordPerfect, etc.</span>
                  <table>
                     <tr>
                        <td><input type="hidden" name="wpcareers[oldUploadName]" value="{$oldUploadName}" /><input type="file" name="upload" /></td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;{if $_upload}{$_upload}{/if}</td>
                     </tr>
                     <tr>
                        <td class="top"><span class ="smallTxt">Upload a file up to ({$uploadmax})</span></td>
                        <td>{if $_upload}<p><input type=checkbox name="remove_upload">&nbsp;Delete this file{/if}</td></tr>
                  </table>
               </td>
            </tr>
            <tr><td><span class="red">{$lang.JH_AWARDS}</span></td><td></td></tr>
            <tr bgcolor="#F4F4F4">
               <td class="td_left">{$lang.R_SALARY}</td>
               <td class="td_right">
               <input type="text" name="wpcareers[salary]" value="{$salary}" size="20">&nbsp;
               <select name="wpcareers[typesalary]">{html_options values=$salaryId output=$salaryTitle selected=$salarySelected}</select></td>
            </tr>
            <tr>
               <td class="td_left">{$lang.R_START}</td>
               <td><input type="text" name="wpcareers[startDate]" value="{$startDate}" size="50"></td>
            </tr>
            
            <tr bgcolor="#F4F4F4">
               <td></td>
               <td class="td_right"><input type="checkbox" name="wpcareers[private]" {if $private== 1}checked{/if}>
               Keep my identifiable details anonymous on this website.<br />&nbsp;&nbsp;&nbsp;This option is not recommended.</td>
            </tr>
            <tr>
               <td class="td_left">{$lang.J_HOWLONG}</td>
               <td class="td_right">
               <input type="text" name="wpcareers[expire]" size="3" maxlength="3" value="{$expire}" />&nbsp;<span class ="smallTxt">({$expiredefault} days)</span></td>
            </tr>
            <tr>
               <td></td>
               <td class="td_right"><input type="checkbox" id="wpcareers[agree]" name="wpcareers[agree]" checked />&nbsp;Please agree to our policy*</td>
            </tr>
            {$confirm}
            <tr bgcolor="#F4F4F4">
               <td></td>
               <td><p>{$lang.J_AG}<br /><input type="hidden" name="rid" value="0" /></p> <input type=submit value="{$lang.J_SUBMIT}"></td>
            </tr>
         </table>
      </form>
   </td></tr></tbody></table>
</div>



{include file='footer.tpl'}