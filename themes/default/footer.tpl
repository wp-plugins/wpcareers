{*
 * $Revision: $
 * Description: Wordpress wpCareers
 * Footer_Templates
*}

{* begin of third column on right of the page 
 * the column dissappiers, by removing the following section. 
*}
<div class="columns-2">
   <table class="otherTable col2"><tbody>
      <tr><td>
         <p><center>{$g120_600}</center></p>
      </td></tr>
   </table>
</div>
{* end of third column on right of the page *}



<!--columns-2-->
<p>{if $googlebtn}<div class="jp_googleAd">{$jp_googleAd}</div>{/if}</p>
<div class="jp_footer">
   <dl class="columnsf-2">
      <dt>Legende</dt>
      <dd>Categories: {$categories_total}</dd>
      <dd>Jobs: {$jobs_total}</dd>
      <dd>Resume: {$resume_total}</dd>
   </dl>
   <dl class="columnsf-voll">
      <dt>{$wpca_settings.new_links} {$lang.J_LASTADD}</dt>
      <br />
      <b>Jobs</b>
      {foreach from=$new_jobs|@sortby:date item=item key=key}
        <dd>{$item.previewlink}&nbsp;&nbsp;<span class="gray">({$item.category}&nbsp;{$item.date})</span></dd>
      {/foreach}
      <br />
      <b>Resumes</b>
      {foreach from=$new_resumes|@sortby:date item=item key=key}
         <dd>{$item.previewlink}&nbsp;&nbsp;<span class="gray">({$item.category}&nbsp;{$item.date})</span></dd>
      {/foreach}
   </dl>
   {*
   <dl class="columnsf-3 last">
      <dt>Terms &amp; Conditions</dt>
      <dd><a href="/content/terms_of_service/">Terms of service</a></dd>
      <dd><a href = "/content/contact-us/">contact us</a></dd>
   </dl>
   *}
</div>
<br />

<div class="rss_footer">
   
   <p>{$rssurl} WPCareers {$credit}</p>
</div>

{$java}
<!--end jp_footer-->