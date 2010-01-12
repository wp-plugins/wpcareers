{*
 * $Revision: $
 * Description: Wordpress wpCareers
 * First_Site_Templates
*}



{include file='header.tpl'}
{include file='sidebar.tpl'}

<div class="columns-8 featured-content">
   <div class="columns-6 featured-content">
   <table class="otherTable col6"><tbody>
      <tr valign="top"><td><div><h2>{$lang.J_VIEWLISTING}</h2></div></td></tr>
      <tr valign="top"><td>
         <h3>{$category}</h3><br />
         {if $jobs}
            {assign var=cnt value=0}
            {foreach from=$jobs item=item key=key}
               {if $cnt is div by 2} 
                  <table bgcolor="#fafafa">
               {else} 
                  <table>{/if}
               <tr>
               <td valign="top">{if $item.photo}{$item.photo}{/if}</td>
                   <td><b>Title:&nbsp;{$item.viewjob}</b><br />
                        <b>Local:</b>&nbsp;{$item.town}&nbsp;&nbsp;<span class="smallTxt"><b>Added:</b>&nbsp;{$item.date}</span>
                        <br />{if $item.desctext <> ""}{$item.desctext}{/if}
                   </td>
               </tr>
               <tr>
                  <td colspan=2>
                  &nbsp;&nbsp;{$item.viewJobDetail}
                  &nbsp;&nbsp;{$item.modifyJobLink}
                  &nbsp;&nbsp;{$item.deleteJobLink}
                  </td>
               </tr>
               </table>
               <HR />
               {assign var=cnt value=$cnt+1}
            {/foreach}
         {else}
            <div align="center"><b>{$lang.J_NOLISTING}</b></div>
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
