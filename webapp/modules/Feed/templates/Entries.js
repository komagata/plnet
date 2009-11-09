{if $callback}{$callback}({$entries});{elseif $raw}{$entries};{else}{literal}if(typeof(Plnet) == 'undefined') Plnet = {}; Plnet.entries = {/literal}{$entries};{/if}
