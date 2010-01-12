{*
 * $Revision: $
 * Description: Wordpress wpCareers
*}

{include file='header.tpl'}
<div class="post" id="post-02">
   {include file='sidebar.tpl'}
   <P>
   <div class="columns-8 featured-content">
      <div class="columns-6 featured-content">
         <table class="categoryTable col6"><tbody>
            <tr><td colspan="2"><h2>{$title}</h2></td></tr>
            <tr valign="top"><td>
               <div class="featured-content">
                  <table class="col6">
                        <tr><td>
                        {if $errors} {$errors} {/if}
                        {if $form} {$form} {/if}
                  </td></tr></table>
               </div>
            </td></tr>
         </tbody></table>
      </div>
   </div>
   {include file='footer.tpl'}
</div>
