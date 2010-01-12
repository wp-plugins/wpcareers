{*
 * $Revision: $
 * Description: Wordpress wpCareers
 * LIST_RESUME_Templates
*}

{include file='header.tpl'}
{include file='sidebar.tpl'}

<div class="columns-8 featured-content">
   <div class="columns-6 featured-content">
   <table class="categoryTable col6"><tbody>
      <tr><td style="background-image:url({$plugin_url}/images/gelb_klein.jpg); background-repeat:repeat-y; height:2px;" valign="top"></td></tr>
      <tr valign="top"><td><div><h2>{$lang.R_VIEWLISTING}</h2></div></td></tr>
      <tr valign="top"><td>
         <h3>{$category}</h3><br />
         {if $resumes}
         {assign var=cnt value=0}
         {foreach from=$resumes item=item key=key}
            {if $cnt is div by 2} <table class="col6" bgcolor="#fafafa">{else} <table class="col6">{/if}
            <tr>
               <td valign="top">{if $item.photo}{$item.photo}{/if}</td>
               <td><b>Title:&nbsp;{$item.viewResume}</b><br />
                  <b>Local:</b>&nbsp;{$item.town}&nbsp;&nbsp;&nbsp;<span class="smallTxt">(<b>{$lang.J_ADD}</b>&nbsp;{$item.date})</span><br />
               <p>{if $item.desctext <> ""}{$item.desctext}{/if}</p>
               </td>
            </tr>
            <tr>
               <td colspan=2>
                  &nbsp;&nbsp;{$item.viewResumeDetail}
                  &nbsp;&nbsp;{$item.modifyResumeLink}
                  &nbsp;&nbsp;{$item.deletResumeLink}
               </td>
            </tr>
            </table>
            <HR />
            {assign var=cnt value=$cnt+1}
         {/foreach}
         {else}
         <div align="center"><b>{$lang.R_NOLISTING}</b></div>
         <p>&nbsp;</p>
         <dl class="columnsf-1">
                  <dt>{$wpca_settings.new_links} {$lang.J_LASTADD}</dt>
                  {foreach from=$new_jobs item=item key=key}
                     <dd>{$item.previewlink}&nbsp;&nbsp;<span class="gray">({$item.category}&nbsp;{$item.date})</span></dd>
                  {/foreach}
         </dl>
         {/if}
      </td></tr>
   </tbody></table>
   </div>
</div>

{include file='footer.tpl'}
