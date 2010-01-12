{include file='header.tpl'}
{include file='sidebar.tpl'}
<P>
<div class="columns-8 featured-content">
   <div class="columns-6 featured-content">
      <table class="categoryTable col6"><tbody>
         <tr><td><div><h2>Serach</h2></td></tr>
         <tr valign="top">
            <td>
            <br>
            {if $results}
            {foreach from=$results item=item key=key}
               <table class="col6">
                  <tr><td>
                     <table>
                        <tr><td><b>Category:</b>&nbsp;</td><td>{$item.viewcategory}</td></tr>
                        <tr><td><b>Title:</b></td><td>{$item.viewjob}&nbsp;&nbsp;<span class ="smallTxt">Added:{$item.date}</span></td></tr>
                     </table>
                  </td></tr>
                  <tr><td>
                  Local: {$item.town}<br /> 
                  {$item.desctext}
                  </td></tr>
                  <tr><td>
                  <img src="{$plugin_url}/images/refer.gif">{$item.sendjob}&nbsp;&nbsp;{$item.modifyJobLink}
                  </td></tr>
               </table>
               <BR />--
            {/foreach}
            <BR />
            {/if}
            </td>
         </tr></tbody>
      </table>
   </div>
</div>


{include file='footer.tpl'}
