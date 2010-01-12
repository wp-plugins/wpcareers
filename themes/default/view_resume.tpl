{*
 * $Revision: $
 * Description: Wordpress wpCareers
 * VIEW_RESUMES_Templates
*}

{include file='header.tpl'}

<div class="columns-7 featured-content">
   <table class="otherTable col7"><tbody>
      <tr><td colspan=2><h2>{$main_link}</h2><HR /><h2>{$lang.R_DETAIL}</h2></td></tr>
            {if $resume}
            {foreach from=$resume item=item key=key}
               <tr><td style="text-align: center;>{$item.photo}</td>
                     <td class="td_right">
                     <b>{$lang.J_TITLE}</b>&nbsp;{$item.title}&nbsp;&nbsp;&nbsp;<span class="smallTxt">({$item.view}&nbsp;&nbsp;&nbsp;<b>{$lang.J_ADD}</b>&nbsp;{$item.date})</span><br />
                     <b>{$lang.J_SURNAME}</b>&nbsp;{$item.name}<br />
                     {if $item.information}<b>{$lang.R_CAREER}</b>&nbsp;{$item.information}{/if}
                     </td>
               </tr>
               
               <tr><td class="td_left"><span class="red">{$lang.R_DESCEXP}</span></td><td class="td_right"></td></tr>
               <tr><td class="td_left"></td><td class="td_right">{$item.desctext}</td></tr>
               
               {if $item.salary}<tr><td class="td_left"></td><td class="td_right"><b>{$lang.RW_SALARY}</b> {$item.salary}&nbsp;{$item.typesalary}</td></tr>{/if}
               {if $item.startDate}<tr><td class="td_left"></td><td class="td_right"><b>{$lang.RW_START}</b>&nbsp;{$item.startDate}</td></tr>{/if}
               <tr><td colspan=2>&nbsp;</td></tr>
               <tr><td class="td_left"><span class="red">{$lang.J_CONTACTINFO}</span></td><td class="td_right">&nbsp;&nbsp;&nbsp;{if $item._upload}{$item._upload}{/if}</td></tr>

               <tr><td class="td_left"></td><td class="td_right">{$item.contactinfo}</td></tr>
               <tr><td class="td_left"></td><td class="td_right"><b>{$lang.J_TOWN}</b>&nbsp;{$item.town}</td></tr>
               <tr><td class="td_left"></td><td class="td_right"><b>{$lang.J_STATE}</b>&nbsp;{$item.state}</td></tr>
               
               <tr><td class="td_left"></td><td class="middle"><b>{$lang.J_EMAIL_ICON}</b><a href="mailto:{$item.email}"> {$item.email}</a></td></tr>
               <tr><td class="td_left"></td><td class="middle">{if $item.tel}<b>{$lang.J_TEL_ICON}</b>&nbsp;{$item.tel}{/if}</td></tr>
               <tr><td class="td_left"></td><td class="middle">{if $item.fax}<b>{$lang.J_FAX_ICON}</b>&nbsp;{$item.fax}{/if}</td></tr>
               <tr><td class="td_left"></td><td><b>{$job_mustlogin}<b></td></tr>
               <tr><td colspan=2>&nbsp;</td></tr>
               <tr>
                  <td></td><td class="td_right">{$item.sendResumeLink}&nbsp;&nbsp;&nbsp;{$item.modifyResumeLink}&nbsp;&nbsp;&nbsp;{$item.deleteResumeLink}&nbsp;&nbsp;&nbsp;<a href="print.php?op=Rprint&amp;rid=5" target="_blank">{$lang.J_PRINT_ICON} Print </a></td>
               </tr>
            {/foreach}
            {else}
               <tr><td colspan=2><div align="center">{$lang.R_NOLISTING}<b>{$no_ad}</b></div></td></tr>
            {/if}
   </tbody></table>
</div>

{include file='footer.tpl'}