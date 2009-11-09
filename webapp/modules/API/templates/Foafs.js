{if $callback}{$callback}({$foafs});{elseif $raw}{$foafs};{else}{literal}if(typeof(Plnet) == 'undefined') Plnet = {}; Plnet.foafs = {/literal}{$foafs};{/if}
