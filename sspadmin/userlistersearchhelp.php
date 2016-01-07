<?php
require("includeheader.php");
$session= new SSP_Protect("user");
$pageTitle="Dada South - Artists Search Help";
$SSP_template = new SSP_Template($pageTitle, "sspgeneraltemplate.tpl");
$SSP_template->includeTill("menu");
echo '<a href="'.$SSP_Config->userLister.'?advanced=1">Back to Search</a>';
$SSP_template->includeTill("content");
?>
<h1>Search Help</h1>
<p>How to modify the search options that produce the list of artists. </p>
<h2>Search Options</h2>
<h3>Two General Searches</h3>
<p>The two general search entries<br>
<img src="images/generalsearchbox.gif" alt="General search box" width="370" height="35"><br> 
the drop down selects which part of the database is searched and in the empty box you put, in full or part, that for which your are searching. Removing any letters from the second box stops this search. </p>
<p>eg. Say you are looking for someone whos first name you have forgotten, but it began with &quot;el&quot;, select &quot;First Name&quot; in the dropdown and put &quot;el&quot; in the box and click on &quot;Search Now&quot;, a list will be returned with all first names beginning with &quot;el&quot; eg. Ellenor, Elly, Elminster, also you would get Hellen since she has &quot;el&quot; in her name, so the more letters you can remember the better the search works.</p>
<h3>Multiple Search Options</h3>
<p>How Multiple search options are catered for is specified by:<br>
<img src="images/searchoptions.gif" alt="Search optionas" width="139" height="28"><br>
If you have multiple search options like Search Family Name for &quot;el&quot; and County as &quot;Sussex&quot; the following things would happen,</p>
<ol>
  <li> If &quot;and&quot; is selected any members with &quot;el in their family name and &quot;Sussex&quot; as their county would be displayed on pressing &quot;Search Now&quot;.</li>
  <li>If &quot;or&quot; is selected any member with &quot;el&quot; in their family name would be displayed, and also any member with &quot;Sussex&quot; who lived in that county would also be displayed. </li>
</ol>
<p>So in the first case only members with both conditions would be listed, in the second any member with either condition would be displayed.</p>
<h3>Results per page</h3>
<p>You can specify how many results per page are visible before the lister promps for a next page. eg. if the results per page is set to 25 and the number of lines returned is 30 then ther will be two pages of results, however if the results per page is set to 50 then ther will only be one page.</p>
<h2>Side Menu</h2>
<h3>Return to List</h3>
<p>Returns to the list without changing the current search parameters. </p>
<h3>Help</h3>
<p>This help section. </p>
<?php
$SSP_template->displayFooter();
?>

