<form method="{formMethod}" action="{formAction}" enctype="multipart/form-data">
{textareaDesc}{textarea}<br />
{:if:image1Display}
<img src="{image1Display}" />
{:endif:image1Display}
{image1Desc}<br />
{:if:image2Display}
<img src="{image2Display}" />
{:endif:image2Display}
{image2Desc}<br />
<input type="submit" name="previewBack" value="Back" />
<input type="submit" name="previewSave" value="Save" />
{formHidden}
</form>