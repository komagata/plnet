{if $callback}{$callback}({$feed});{elseif $raw}{$feed};{else}{literal}if(typeof(Plnet) == 'undefined') Plnet = {}; Plnet.feed = {/literal}{$feed};{/if}
