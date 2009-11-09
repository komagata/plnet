{if $callback}{$callback}({$friends});{elseif $raw}{$friends};{else}{literal}if(typeof(Plnet) == 'undefined') Plnet = {}; Plnet.friends = {/literal}{$friends};{/if}
