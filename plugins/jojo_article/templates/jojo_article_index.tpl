{if $pg_body && $pagenum==1}{$pg_body}{/if}
<div id="articles">
{foreach from=$jojo_articles item=a key=k}
    <div class="media article{if $a.ar_featured} featured{/if}">
    {if $k<1 || $a.weighting==0 || $a.snippet=='full'}
        {if $a.image}<a href="{$SITEURL}/{$a.url}" title="{$a.title}" class="pull-left"><img src="{$SITEURL}/images/{if $a.snippet=='full'}{$a.mainimage}{elseif $a.thumbnail}{$a.thumbnail}{else}s150{/if}/{$a.image}" class="media-object" alt="{$a.title}" /></a>{/if}
        <div class="media-body">
            <h3 class="media-heading">{if $a.snippet=='full'}{$a.title}{else}<a href="{$SITEURL}/{$a.url}" title="{$a.title}">{$a.title}</a>{/if}</h3>
            {if $a.subtitle}<h4>{$a.subtitle}</h4>{/if}
            {if $a.snippet=='full'}{$a.ar_body}{else}<p>{$a.bodyplain|truncate:$a.snippet} <a href="{$SITEURL}/{$a.url}" title="{$a.title}" class="more">{$a.readmore}</a></p>{/if}
            {if $a.showdate}<div class="date">Added: {$a.datefriendly}</div>{/if}
            {if $a.comments && $a.numcomments}<div class="numcomments"><span class="glyphicon glyphicon-comment"></span> {$a.numcomments}</div>{/if}
        </div>

    {elseif $k<10}
        {if $a.image}<a href="{$SITEURL}/{$a.url}" title="{$a.title}" class="pull-left"><img src="images/{$a.thumbnail}/{$a.image}" class="media-object" alt="{$a.title}" /></a>{/if}
        <div class="media-body">
            <h3 class="media-heading"><a href="{$SITEURL}/{$a.url}" title="{$a.title}">{$a.title}</a></h3>
            <p>{$a.bodyplain|truncate:300} <a href="{$SITEURL}/{$a.url}" title="{$a.title}" class="more">{$a.readmore}</a></p>
            {if $a.showdate}<div class="date">Added: {$a.datefriendly}</div>{/if}
            {if $a.comments && $a.numcomments}<div class="numcomments"><span class="glyphicon glyphicon-comment"></span> {$a.numcomments}</div>{/if}
        </div>
    {elseif $k<20}
        {if $a.image}<a href="{$SITEURL}/{$a.url}" title="{$a.title}" class="pull-left"><img src="images/{$a.thumbnail}/{$a.image}" class="media-object" alt="{$a.title}" /></a>{/if}
        <div class="media-body">
            <h3 class="media-heading"><a href="{$SITEURL}/{$a.url}" title="{$a.title}">{$a.title}</a></h3>
            <p>{$a.bodyplain|truncate:200} <a href="{$SITEURL}/{$a.url}" title="{$a.title}" class="more">{$a.readmore}</a></p>
            {if $a.showdate}<div class="date">Added: {$a.datefriendly}</div>{/if}
        </div>
    {else}
        <a href="{$a.url}" title="{$SITEURL}/{$a.title}">{$a.title}</a>{if $OPTIONS.article_show_date=='yes'} - {$a.datefriendly}{/if}<br />
    {/if}
    </div>
{/foreach}
</div>
{$pagination}
