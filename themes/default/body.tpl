{*
 * $Revision: $
 * Description: Wordpress wpCareers
 * BODY_Templates
*}

{include file='header.tpl'}
{include file='sidebar.tpl'}

 
<div class="columns-8 featured-content">
   <div class="columns-6 featured-content">
      <table class="categoryTable col6"><tbody>
      <tr><td colspan="2"><h3>{$lang.J_CATLIST}</h3></td></tr>
         <tr valign="top"><td>
         {if $jobCategories}
         <table class="col6"><tr></td>
            {assign var=cnt value=0}
            {foreach from=$jobCategories item=cat key=cats}
               {if $cat.cp_id == 0}
                  <table style="background:url({$plugin_url}/images/back.gif) repeat-x;">
                     <tr><td width=10px>&nbsp;</td><td width=30px><a onClick="hide('j_{$cat.c_id}');"><img src="{$catImgSrc}" name="imgj_{$cat.c_id}" align="left"></a></td>
                        <td class="jp_category"><b>{$cat.category_link}</b>{if $cat.jcounTotal>0} <span class="smallTxt">&nbsp;({$cat.jcount}, total {$cat.jcounTotal})</span>{/if}</td></tr>
                  </table>
               {/if}
               {if $jobSubCategories}
               <div style="display: none;" id="j_{$cat.c_id}">
                  <table><tr><td>{$cat.catImg}</td>
                     <td width=100%>
                        {foreach from=$jobSubCategories item=sub key=subs}
                           {if $sub.cp_id==$cat.c_id}
                              &nbsp;&nbsp;{$sub.subCategory_link}{if $sub.jcount>0} <span class="smallTxt">&nbsp;({$sub.jcount})</span>{/if}<br />
                           {/if}
                        {/foreach}
                     </td>
                  </td></tr></table>
               </div>
               {if $cnt <2} <script type="text/javascript"> hide('j_{$cat.c_id}'); </script> {/if}
               {assign var=cnt value=$cnt+1}
               {/if}
            {/foreach}
         </td></tr></table>
         {/if}
      </td></tr>
      </tbody></table>
   </div><!--columns-6-->
   <div class="columns-6 featured-content">
      <table class="categoryTable col6"><tbody>
         <tr><td colspan="2"><h3>{$lang.R_CATLIST}</h3></td></tr>
         <tr valign="top"><td>
               {if $resCategories}
                   <table class="col6"><tr></td>
                      {assign var=cnt value=0}
                      {foreach from=$resCategories item=cat key=cats}
                         {if $cat.rcp_id == 0}
                            <table style="background:url({$plugin_url}/images/back.gif) repeat;">
                            <tr>
                              <td width=10px>&nbsp;</td><td width=30px><a onClick="hide('r_{$cat.rc_id}');"><img src="{$catImgSrc}" name="imgr_{$cat.rc_id}" align="left"></a></td>
                              <td class="jp_category"><b>{$cat.resume_link}</b>{if $cat.rcounTotal>0} <span class="smallTxt">&nbsp;({$cat.rcount}, total {$cat.rcounTotal})</span>{/if}</td>
                           </tr>
                           </table>
                        {/if}
                        {if $resSubCategories}
                        <div style="display: none;" id="r_{$cat.rc_id}">
                           <table><tr><td>{$cat.catImg}</td>
                              <td width=100%>
                              {foreach from=$resSubCategories item=sub key=subs}
                                {if $sub.rcp_id==$cat.rc_id}
                                    &nbsp;&nbsp;{$sub.subResume_link}{if $sub.rcount>0} <span class="smallTxt">&nbsp;({$sub.rcount})</span>{/if}<br />
                                {/if}
                              {/foreach}
                              </td>
                           </td></tr></table>
                       </div>
                       {if $cnt <2} <script type="text/javascript"> hide('r_{$cat.rc_id}'); </script> {/if}
                       {assign var=cnt value=$cnt+1}
                     {/if}
                  {/foreach}
               </td></tr></table>
            {/if}
        </td></tr>
      </tbody></table>
   </div><!--columns-6-->
</div>
<!--columns-8-->
{include file='footer.tpl'}