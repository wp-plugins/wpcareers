{*
 * $Revision: $
 * Description: Wordpress wpCareers
 * SIDEBAR_Templates
*}

<div class="columns-4">
	{* style="background: url({$plugin_url}/images/main/content.jpg) repeat-y;"*}
   <table class="siderbarTable col4" style="background: url({$plugin_url}/images/main/content.jpg) repeat-y;"><tbody>
      <tr><td><h2>{$main_link}</h2><HR /></td></tr>
      <tr><td valign="top"><h3>Post your Job/Resume</h3><br /></td></tr>
      <tr valign="top"><td>
            <p class="tight strong">{$lang.J_EMPLOYERS}</p>
            <ul><li><a href="{$job_link}">Post Jobs or Internships</a></li></ul>
            <p class="tight strong">{$lang.J_SEEKER}</p>
            <ul><li><a href="{$resume_link}">{$lang.J_ADD_RESUME}</a></li></ul>
            <p>&nbsp;</p>
            <div class="jp_search"> 
               <h3>{$lang.J_SEARCH}</h3>
               <form method="post" id="jp_search" name="jp_search" action="{$search_link}">
               <table>
                  <tr>
                     <td>
                     <input type="text" name="search_terms" size="23" value="{$search_terms}">
                     <input type="hidden" name="op" value="search">
                     <input type="submit" value="Search">&nbsp;{$jp_advanced}
                     </td>
                  </tr>
                  <tr>
                     <td align="left">
                     jobs:&nbsp;<input type="radio" value="jobs" name="type" checked>&nbsp;&nbsp;
                     Resum:&nbsp;<input type="radio" value="resume" name="type">&nbsp;&nbsp;
                     All:&nbsp;<input type="radio" value="desc" name="type">&nbsp;&nbsp;
                     </td>   
                  </tr>
               </table>
               </form>
            </div>
      </td></tr>
      <tr>
         <td>
         Post your job or resume today.<br> or start searching for jobs!
         <p>
         There is no charge for postings.<br>Your posting will remain online for 360 days.<br />
         As outlined in the Terms of Service, we reserve the right to accept or decline any job posting.<br />
         Postings which are approved are generally processed within 1-2 business days.
         </p>
         <p>Thank you for posting with us. We look forward to being a part of your company's staffing solution.</p>
         </td>
      </tr>
   </tbody></table>
</div><!--columns-4-->