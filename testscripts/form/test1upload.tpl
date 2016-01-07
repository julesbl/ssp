{errorList}
<form method="{formMethod}" action="{formAction}" enctype="multipart/form-data">
{textareaDesc}{textarea}<br />
{:if:image1Display}
<img src="{image1Display}" />
{:endif:image1Display}
{image1Desc}{image1}<br />
{:if:image2Display}
<img src="{image2Display}" />
{:endif:image2Display}
{image2Desc}{image2}<br />
{submit1}
{formHidden}
</form>