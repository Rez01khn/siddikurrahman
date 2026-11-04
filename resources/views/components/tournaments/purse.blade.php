@props(['tournament'])
@if($tournament->tournament_purse_amount !== null)
<div class="border-t border-forest/15 pt-5 mt-6"><p class="text-sm font-medium text-forest">Tournament purse / event prize fund</p><p class="text-lg text-forest mt-2">{{ $tournament->tournament_purse_currency }} {{ $tournament->tournament_purse_amount }}</p><p class="text-xs text-slate-500 leading-6 mt-2">Total event prize fund as recorded by the source. This is not Siddikur Rahman's individual winnings.</p></div>
@endif
