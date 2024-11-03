<div class="container py-5">
    <div class="w-50 center border rounded px-3 py-3 mx-auto">
    <h1>Login</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </div>
    @endif
    @if (session()->has('loginError'))
        <div class="pt-3">
            <div class="alert alert-danger">
                {{session('loginError')}}
            </div>
        </div>
    @elseif (session()->has('createSuccess'))
        <div class="pt-3">
            <div class="alert alert-success">
                {{session('createSuccess')}}
            </div>
        </div>
    @endif
    <form action="">
        @csrf
        @if ($create == true)
            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input id="name" title="Nama" type="name" wire:model="name" name="name" class="form-control">
            </div>
        @endif
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" title="Email" type="email" wire:model="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" title="Password" type="password" wire:model="password" name="password" class="form-control">
        </div>
        @if ($create == true)
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Konfirmasi password</label>
                <input id="password_confirmation" title="Konfirmasi Password" type="password" wire:model="password_confirmation" name="password_confirmation" class="form-control">
            </div>
            {{-- <div class="mb-3">
                <label for="passwordConfirm" class="form-label">Konfirmasi password</label>
                <input type="password" wire:model="passwordConfirm" name="passwordConfirm" class="form-control">
            </div> --}}
            <div class="mb-3">
                <label class="form-label" for="inlineFormCustomSelect">Role</label>
                <select wire:model="role" class="custom-select mr-sm-2" id="inlineFormCustomSelect">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        @endif
        <div class="mb-3 d-grid">
        @if ($create == false)
            <button wire:click="loginUser()" name="submit" type="button" class="btn btn-primary btn-block">Login</button>
            <div class="pt-3 text-center">
                <label for="changeMode">Belum punya akun?</label>
                <button id="changeMode" wire:click="createAccount(true)" name="submit" type="button" class="btn btn-link">Buat akun</button>
            </div>
        @elseif($create == true)
            <button wire:click="submitCreate()" name="submit" type="button" class="btn btn-primary btn-block">Buat Akun</button>
            <div class="pt-3 text-center">
                <label for="changeMode">Sudah punya akun?</label>
                <button id="changeMode" wire:click="createAccount(false)" name="submit" type="button" class="btn btn-link">Login</button>
            </div>
        @endif
        </div>
    </form>
</div>
</div>
