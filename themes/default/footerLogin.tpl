{*
 * $Revision: $
 * Description: Wordpress wpCareers
 * Footer_Templates
*}

<div class="columns-2">
   <table class="otherTable col2"><tbody>
      <tr><td>
         <p><center>{$g120_600}</center></p>
      </td></tr>
   </table>
</div>
<!--columns-2-->
<p>{if $googlebtn}<div class="jp_googleAd">{$jp_googleAd}</div>{/if}</p>
<div class="jp_footer">
  {php}
    echo bloginfo('name')." is proudly powered by <a href=\"http://wordpress.org/\">WordPress</a><br />";
  {/php}
</div>
<br />

{$java}
<!--end jp_footer-->