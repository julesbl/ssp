<!-- testSelectRadio.tpl - selection and radio test with error -->
<style>
	option.option2Class{
		color: blue;
	}
	option.option3Class{
		color: red;
		text-decoration: underline;
	}
</style>
<form method="post">
	{errorList}
	{simpleSelectDesc} {simpleSelect}<br />
	{:if:simpleSelectValue}
	<p>Simple select value: {simpleSelectValue}</p>
	{:endif:simpleSelectValue}
	{selectComplexDesc} {selectComplex}<br />
	{:if:complexSelectValue}
	<p>Complex select value: {complexSelectValue}</p>
	{:endif:complexSelectValue}
	Error simple <input type="text" name="errorSimple" value="5" /><br />
	Error complex <input type="text" name="errorComplex" value="100" /><br />
	No selections valid 1-4 <input type="text" name="noSelect" value="2" /><br />
	{radioSelectDesc} {radioSelect}<br />
	Error radio <input type="text" name="radioSelectError" value="5" /><br />
	<input type="submit" value="Submit" />
	{formHidden}
</form>