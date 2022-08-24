<h1>Simple Site Protection</h1>
{:if:logged_in}
<p>User logged in</p>
{:endif:logged_in}
<p>These set of php routines are designed to allow php developers to easily secure
a site or an application.</p>

<p>Based on the ideas and information written about in <a target="_blank" href="http:///www.amazon.co.uk/Innocent-Code-Security-Wake-up-Programmers/dp/0470857447/ref=sr_1_1?ie=UTF8&amp;s=books&amp;qid=1266594625&amp;sr=1-1">Innocent
		Code</a> by the security consultant <a target="_blank" href="http://shh.thathost.com/">Sverre
H. Huseby</a> the code attepts to make the site resiliant against most forms of
attack. </p>

<p>Attacks hardened against are:</p>
<ul>
  <li>Sql injection.</li>
  <li>Invalid character injection in forms.</li>
  <li>Javascript injection in forms.</li>
  <li>Sesson theft.</li>
  <li>Session takeover.</li>
  <li>One forms out put being used into another.</li>
  <li>Designed to be used with ssl thus helping to prevent man in the middle
    type attacks.</li>
</ul>

<p>Facilities provided by this set of libraries and routines:</p>
<ul>
  <li>Basic joinup routine.</li>
  <li>Password recovery.</li>
  <li>User admin.</li>
  <li>User self admin.</li>
  <li>Fully templated using fast simple template class.</li>
  <li>Powerful (and paranoid) form building class.</li>
  <li>Data checking class.</li>
  <li>Useful lister and html menu list generation classes</li>
  <li>Works with php 5.0 upwards</li>
  <li>Uses database abstraction to work with most databases, has been used with MySql, Access and MS Sql Server.</li>
  <li>Multi lingual capability with browser language checking.</li>
</ul>

<p>Highly configurable session, login and debug:</p>
<ul>
  <li>Http or Https.</li>
  <li>Variable number of actals for ip checking.</li>
  <li>Fully configurable on types of checks to be done.</li>
  <li>Login by email or username.</li>
  <li>Extend the login for other user inputs.</li>
  <li>Error output either to screen or log file for live sites.</li>
</ul>
<p>Full source code available on <a href="https://github.com/julesbl/ssp" target="_blank">Github</a>.</p>
<p>Install via <a href="https://packagist.org/packages/w34u/ssp" target="_blank">Pakagist</a></p>
