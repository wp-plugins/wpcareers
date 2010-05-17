{*
 * $Revision: $
 * Description: Wordpress wpCareers
 * VIEW_JOBS_Templates
*}


{include file='header.tpl'}

<div class="columns-7 featured-content">
   <table class="otherTable col7"><tbody>
      <tr><td colspan=2><h2>{$main_link}</h2><HR /><h3>{$lang.J_DETAIL}</h3></td></tr>
         {if $job}
            {foreach from=$job item=item key=key}
               <tr><td style="text-align: center">{$item.photo}</td>
                  <td valign="top">
                  <h3>Category: {$item.viewcategory}</h3><BR />
                  <b>{$lang.J_FROM}</b>&nbsp;{$item.submitter}&nbsp;&nbsp;&nbsp;<span class="smallTxt">({$item.view}&nbsp;&nbsp;&nbsp;<b>{$lang.J_ADD}</b>&nbsp;{$item.date})</span><br />
                  <b>{$lang.J_COMPANY}</b>&nbsp;{$item.company}
                  &nbsp;&nbsp;{$jmodify}&nbsp;&nbsp;{$jdelete}</td>
               </tr>
               <tr><td class="view_left"></td><td class="td_right"><b>{$lang.J_TITLE}</b>&nbsp;{$item.title}  ({$item.type})</td></tr>
               <tr><td class="view_left"><span class="red">{$lang.J_DESC}</span></td><td class="td_right"></td></tr>
               <tr><td class="view_left"></td><td class="td_right">{$item.description}</td></tr>
               <tr><td class="view_left"><span class="red">{$lang.J_REQUIRE}</span></td><td class="td_right"></td></tr>
               <tr><td class="view_left"></td><td class="td_right"><b>{$lang.J_REQUIRE}</b> {$item.requirements}<br /></td></tr>
               {if $item.price}
                  <tr><td class="view_left"></td><td class="td_right"><b>{$lang.J_PRICE}</b> {$item.price}&nbsp;{$item.typeprice}</td></tr>
               {/if}
               <tr><td class="view_left"><span class="red">{$lang.J_REQUIRE}</span></td><td></td></tr>
               <tr><td class="view_left"></td><td class="td_right">{$item.contactinfo}</td></tr>
               <tr><td colspan=2>&nbsp;</td></tr>
               <tr><td class="view_left"></td><td class="td_right"><b>{$lang.J_TOWN}</b> {$item.town}</td></tr>
               <tr><td class="view_left"></td><td class="td_right"><b>{$lang.J_STATE}</b> {$item.state}</td></tr>
               <tr><td class="view_left"></td><td class="middle"><b>{$lang.J_EMAIL_ICON}</b><a href="mailto:{$item.email}"> {$item.email}</a></td></tr>
               <tr><td class="view_left"></td><td class="middle">{if $item.tel}<b>{$lang.J_TEL_ICON}</b> {$item.tel}{/if}</td></tr>
               <tr><td class="view_left"></td><td class="middle">{if $item.fax}<b>{$lang.J_FAX_ICON}</b> {$item.fax}{/if}</td></tr>
               <tr><td class="view_left"></td><td class="td_right"><br><b>{$job_mustlogin}<b></td></tr>
               <tr><td colspan=2>&nbsp;</td></tr>
               <tr>
                  <td class="view_left"></td><td class="td_right">{$item.sendJobLink}&nbsp;&nbsp;&nbsp;&nbsp;{$item.modifyJobLink}&nbsp;&nbsp;&nbsp;{$item.deleteJobLink}&nbsp;&nbsp;&nbsp;<a href="print.php?op=Jprint&amp;lid=5" target="_blank">{$lang.J_PRINT_ICON} Print </a></td>
               </tr>
            {/foreach}
         {else}
            <tr><td colspan=2><div align="center">{$lang.J_NOLISTING}<b>{$no_ad}</b></div></td></tr>
            <tr><td colspan=2>
               <dl class="columnsf-1">
                  <dt>{$wpca_settings.new_links} {$lang.J_LASTADD}</dt>
                  {foreach from=$new_jobs item=item key=key}
                     <dd>{$item.previewlink}&nbsp;&nbsp;<span class="gray">({$item.category}&nbsp;{$item.date})</span></dd>
                  {/foreach}
               </dl>
            </td></tr>
         {/if}
   </tbody></table>
</div>

{include file='footer.tpl'}
