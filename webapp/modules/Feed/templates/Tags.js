{if $callback}{$callback}({$entries});{elseif $raw}{$entries};{else}{literal}if(typeof(Plnet) == 'undefined') Plnet = {}; Plnet.entries_by_tag = {/literal}{$entries};{/if}
