{include file="admin/header.tpl"}
<div  class="row">
    <div id="option-categories" class="col-md-3">
      <h3>Categories</h3>
        <ul>
        {foreach item=c from=$categories}
          <li><span class="glyphicon glyphicon-chevron-right"></span><a href="">{$c.op_category}</a></li>
        {/foreach}
        </ul>
    </div>
    <div id="option-items" class="col-md-9">
        <div class="category">
            <h2>Options</h2>
            <p>Config options change the behaviour of the site, and any plugins that are installed. To edit options, please select a category from the list.<br />
            All options are saved automatically after they are changed.</p>
        </div>
        {foreach from=$categories item=cat}
        <div id="category-{$cat.op_category|replace:' ':'-'}" class="category">
            <h3>{$cat.op_category} Options</h3>
            <p>All options are saved automatically</p>
            {foreach from=$options[$cat.op_category] item=opt}
                {if $opt.op_type != 'hidden'}
                <div class="form-group">
                      <div id="savemsg_{$opt.op_name|replace:".":"_"}" class="saving"></div>
                      <h4>{$opt.op_displayname} <span class="help-block note">({$opt.op_name})</span></h4>
                {if $opt.op_type == 'radio'}
                  {foreach from=$opt.options item=radioOption}
                    <label class="radio-inline"><input type="radio" name="option-{$opt.op_name}" onclick="$('#savemsg_{$opt.op_name|replace:".":"_"}').hide().html('Saving...').show(); frajax('admin-set-options','{$opt.op_name}','{$radioOption}');"{if $radioOption == $opt.op_value} checked="checked"{/if}/>{$radioOption}</label>
                  {/foreach}
                {elseif $opt.op_type == 'select'}
                  <select class="form-control" id="option-{$opt.op_name}" name="option-{$opt.op_name}" onchange="$('#savemsg_{$opt.op_name|replace:".":"_"}').hide().html('Saving...').show(); frajax('admin-set-options','{$opt.op_name}', $('#option-{$opt.op_name} :selected').val());">
                  {foreach from=$opt.options item=option}
                    <option{if $option == $opt.op_value} selected="selected"{/if}>{$option}</option>
                  {/foreach}
                  </select>
                {elseif $opt.op_type == 'checkbox'}
                  {foreach from=$opt.options item=option}
                    <label class="checkbox-inline"><input type="checkbox" name="temp" value="{$option}" {if in_array($option, $opt.values)} checked="checked"{/if} onclick="$('#savemsg_{$opt.op_name|replace:".":"_"}').hide().html('Saving...').show(); frajax('admin-set-options','{$opt.op_name}', $('#{$opt.op_name} input:checked').serialize().replace(/temp=/g, '').replace(/&/g, ',') );" />{$option}</label>
                  {/foreach}
                {elseif $opt.op_type == 'text' || $opt.op_type == 'integer'}
                    <input class="form-control" type="text" size="60" name="option-{$opt.op_name}" value="{$opt.op_value|escape:'html'}" onchange="$('#savemsg_{$opt.op_name|replace:".":"_"}').hide().html('Saving...').show(); frajax('admin-set-options','{$opt.op_name}', this.value);" />
                {else}
                    <textarea class="form-control" rows="8" cols="50" name="option-{$opt.op_name}" onchange="$('#savemsg_{$opt.op_name}').hide().html('Saving...').show();  frajax('admin-set-options','{$opt.op_name}', this.value)">{$opt.op_value|escape:'html'}</textarea>
                {/if}
                <p class="help-block">{if $opt.op_description}{$opt.op_description}<br />{/if}{if $opt.op_default}
                <span class="note">Default Value: {$opt.op_default}</span>{/if}</p>
                </div>
                {/if}
            {/foreach}
        </div>
        {/foreach}
    </div>
</div>
{include file="admin/footer.tpl"}