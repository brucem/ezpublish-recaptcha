{def $lang=ezini('Display','OverrideLang','recaptcha.ini')} 
{if $lang|eq('')}{set $lang=$attribute.language_code|extract_left(2)}{/if}
<script type="text/javascript">
var RecaptchaTheme='{ezini('Display','Theme','recaptcha.ini')}';
var RecaptchaLang='{$lang}';
{literal}
var RecaptchaOptions = {
theme: RecaptchaTheme,
lang: RecaptchaLang
};
{/literal}
</script>
{recaptcha_get_html()}
