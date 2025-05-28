@props(['amount', 'status', 'label'])

<div class="flex items-center gap-2">
    <span>Bs. {{ $amount }}</span>
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
        {{ $status === 'success' ? 'bg-success-100 text-success-800' : 'bg-warning-100 text-warning-800' }}">
        {{ $label }}
    </span>
</div> 