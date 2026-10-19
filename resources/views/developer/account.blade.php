<x-developer-layout :navigation-items="$navigation">
    <div class="mx-auto max-w-xl space-y-4"><div><h1 class="text-2xl font-bold text-text">Akun Saya</h1><p class="text-sm text-muted">Ubah kata sandi akun developer.</p></div>
        @if(session('status'))<x-ui.toast>{{ session('status') }}</x-ui.toast>@endif
        @if($errors->any())<x-ui.card class="border-danger text-sm text-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></x-ui.card>@endif
        <x-ui.card><form method="POST" action="{{ route('developer.account.update') }}" class="space-y-4">@csrf @method('PUT')
            <x-ui.input label="Kata sandi saat ini" name="current_password" type="password" autocomplete="current-password" required />
            <x-ui.input label="Kata sandi baru" name="password" type="password" autocomplete="new-password" required />
            <x-ui.input label="Konfirmasi kata sandi baru" name="password_confirmation" type="password" autocomplete="new-password" required />
            <x-ui.button type="submit" class="w-full">Simpan kata sandi</x-ui.button>
        </form></x-ui.card>
    </div>
</x-developer-layout>
