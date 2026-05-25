<div class="max-w-md mx-auto text-center">
    @include('livewire.partials.alerts')
    <div class="form-card border-red-200 bg-red-50">
        <p class="text-red-800 mb-4">Press below to alert society security and admin immediately.</p>
        <textarea wire:model="message" class="form-control mb-4" rows="2"></textarea>
        <button wire:click="trigger" wire:confirm="Send emergency SOS alert?" class="btn-primary !bg-red-600 !w-full text-lg py-4">🆘 EMERGENCY SOS</button>
    </div>
</div>
