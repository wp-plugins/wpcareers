{include file='header.tpl'}
{include file='sidebar.tpl'}
<P>
<div class="columns-8 featured-content">
   <div class="columns-6 featured-content">
      <table class="categoryTable col6"><tbody>
        <tr><td><div><h2>Search</h2></td></tr>
        <tr valign="top">
         <td>
         <br>
         {if $results}
         {foreach from=$results item=item key=key}
           <table class="col6">
            <tr><td>
            <table>
               <tr><td><h3>Category:</td><td>{$item.viewcategory}</h3></td></tr>
               <tr><td><b>Title:</b></td><td>{$item.viewjob}</td></tr>
               <tr><td><b>Added:</b></td><td>{$item.date}</td></tr>
               <tr><td>Local:</td><td>{$item.town}</td></tr>
            </table>
            </td></tr>
            <tr><td>
              {$item.desctext}<br />
              <img src="{$plugin_url}/images/refer.gif">{$item.sendjob}&nbsp;&nbsp;{$item.modifyJobLink}
              </td></tr>
           </table>
         {/foreach}
         {else}
         <p>{$lang.NOT_MATCH}</p>
         <BR />
         {/if}
         </td>
         </tr></tbody>
      </table>
   </div>
</div>

{include file='footer.tpl'}