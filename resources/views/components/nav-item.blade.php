@props(['href', 'icon', 'active' => request()->is(trim($href, '/'))])

<a href="{{ $href }}" wire:navigate 
   class="flex items-center gap-3 px-4 py-3 rounded-2xl transition-all font-medium {{ $active ? 'bg-brand text-white shadow-lg shadow-brand/20' : 'hover:bg-slate-800 text-slate-400' }}">
    <span>{{ $icon }}</span>
    <span>{{ $slot }}</span>
</a>
