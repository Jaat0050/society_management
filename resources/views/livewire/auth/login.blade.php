<div class="w-full max-w-md">
    <div class="form-card shadow-2xl">
        <div class="text-center mb-8">
            <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-indigo-500 to-cyan-400 text-xl font-black text-white shadow-lg">SM</div>
            <h2 class="text-2xl font-bold text-slate-900">Society Login</h2>
            <p class="text-slate-500 text-sm mt-1">Super Admin · Society Admin · Guard · Resident</p>
        </div>

        <form wire:submit.prevent="authenticate" class="space-y-5">
            <div class="form-field">
                <label class="form-label">Email or Mobile</label>
                <input type="text" wire:model="login" class="form-control" placeholder="admin@example.com" autocomplete="username">
                @error('login') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-field">
                <label class="form-label">Password</label>
                <input type="password" wire:model="password" class="form-control" placeholder="Enter your password" autocomplete="current-password">
                @error('password') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                <input type="checkbox" wire:model="remember">
                Remember me
            </label>

            <button type="submit" class="btn-primary w-full !w-full" wire:loading.attr="disabled">
                <span wire:loading.remove>Sign In</span>
                <span wire:loading>Signing in...</span>
            </button>
        </form>
    </div>
</div>
