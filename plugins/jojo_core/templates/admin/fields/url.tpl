{if $readonly}
<input type="hidden" name="fm_{$fd_field}" id="fm_{$fd_field}"  size="{$fd_size}" value="{$value}" />
{$value}
{else}
<div class="col-md-5">
<input type="text" name="fm_{$fd_field}" id="fm_{$fd_field}"  size="{$fd_size}" class="form-control" value="{$value}" onchange="validate(this.value,'url')"  title="{$fd_help}" />
</div>
{/if}