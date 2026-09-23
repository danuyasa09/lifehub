@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-surface-secondary text-text border-border focus:border-primary focus:ring-primary rounded-md shadow-sm']) }}>
