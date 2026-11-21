{{-- Shared by create.blade.php and edit.blade.php --}}
@php
    $c = $client ?? null;
    $status = old('status', $c->status ?? 'active');
@endphp

@if ($errors->any())
    <div class="flash flash-bad neu-inset" role="alert">
        <x-icon name="warn" :size="18" /> <span>Please correct the highlighted fields and try again.</span>
    </div>
@endif

<div class="form-grid">
    <div>
        <label for="name">Full Name <i aria-hidden="true">*</i></label>
        <input id="name" name="name" type="text" maxlength="100" required
               class="neu-input @error('name') has-error @enderror" autocomplete="name" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
               value="{{ old('name', $c->name ?? '') }}" placeholder="Enter client name">
        @error('name') <small class="err" id="name-error">{{ $message }}</small> @enderror
    </div>

    <div>
        <label for="email">Email Address <i aria-hidden="true">*</i></label>
        <input id="email" name="email" type="email" maxlength="150" required
               class="neu-input @error('email') has-error @enderror" autocomplete="email" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
               value="{{ old('email', $c->email ?? '') }}" placeholder="name@example.com">
        @error('email') <small class="err" id="email-error">{{ $message }}</small> @enderror
    </div>

    <div>
        <label for="phone">Phone Number <i aria-hidden="true">*</i></label>
        <input id="phone" name="phone" type="text" inputmode="numeric" maxlength="15" required
               class="neu-input @error('phone') has-error @enderror" autocomplete="tel" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror
               value="{{ old('phone', $c->phone ?? '') }}" placeholder="10–15 digits">
        @error('phone') <small class="err" id="phone-error">{{ $message }}</small> @enderror
    </div>

    <div>
        <label for="company_name">Company Name</label>
        <input id="company_name" name="company_name" type="text" maxlength="150"
               class="neu-input @error('company_name') has-error @enderror" autocomplete="organization" @error('company_name') aria-invalid="true" aria-describedby="company_name-error" @enderror
               value="{{ old('company_name', $c->company_name ?? '') }}" placeholder="Company / organisation">
        @error('company_name') <small class="err" id="company_name-error">{{ $message }}</small> @enderror
    </div>

    <div>
        <label for="city">City</label>
        <input id="city" name="city" type="text" maxlength="80"
               class="neu-input @error('city') has-error @enderror" autocomplete="address-level2" @error('city') aria-invalid="true" aria-describedby="city-error" @enderror
               value="{{ old('city', $c->city ?? '') }}" placeholder="City">
        @error('city') <small class="err" id="city-error">{{ $message }}</small> @enderror
    </div>

    <div>
        <label id="status-label">Status <i aria-hidden="true">*</i></label>
        <div class="seg" role="radiogroup" aria-labelledby="status-label">
            <input type="radio" name="status" id="st-active" value="active" @checked($status === 'active')>
            <label for="st-active" class="neu-btn seg-ok">Active</label>

            <input type="radio" name="status" id="st-inactive" value="inactive" @checked($status === 'inactive')>
            <label for="st-inactive" class="neu-btn seg-bad">Inactive</label>
        </div>
        @error('status') <small class="err" id="status-error">{{ $message }}</small> @enderror
    </div>

    <div class="full">
        <label for="address">Address</label>
        <textarea id="address" name="address" rows="3" maxlength="1000"
                  class="neu-input @error('address') has-error @enderror" autocomplete="street-address" @error('address') aria-invalid="true" aria-describedby="address-error" @enderror
                  placeholder="Full address">{{ old('address', $c->address ?? '') }}</textarea>
        @error('address') <small class="err" id="address-error">{{ $message }}</small> @enderror
    </div>
</div>

<div class="acts">
    <button type="submit" class="neu-btn"><x-icon name="check" :size="16" /> {{ $submit }}</button>
    <a href="{{ route('clients.index') }}" class="neu-btn neu-btn-muted">Cancel</a>
</div>
