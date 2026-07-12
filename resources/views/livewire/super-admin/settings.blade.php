<form wire:submit.prevent="save" class="form-card max-w-xl space-y-4">
    <div class="form-field">
        <label class="form-label">App Name</label>
        <input type="text" wire:model="app_name" class="form-control" placeholder="App name">
    </div>
    <div class="form-field">
        <label class="form-label">Support Email</label>
        <input type="email" wire:model="support_email" class="form-control" placeholder="support@example.com">
    </div>
    <div class="form-field">
        <label class="form-label">Support Phone</label>
        <input type="tel" wire:model="support_phone" class="form-control" placeholder="Support phone">
    </div>
    <div class="form-field">
        <label class="form-label">Currency</label>
        <input type="text" wire:model="currency" class="form-control" placeholder="e.g. INR">
    </div>
    <button type="submit" class="btn-primary">Save Settings</button>
</form>
