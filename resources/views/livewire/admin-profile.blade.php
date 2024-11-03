<div class="container mb-3 p-3 bg-white shadow-sm rounded">
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <div class="p-3">
        @if (session()->has('message'))
            <div class="pt-3">
                <div class="alert alert-success">
                    {{session('message')}}
                </div>
            </div>
        @endif
        @if ($errors->any())
            <div class="pt-3 alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if ($isEditing == false)
            <h2>Profil</h2>
            <h5 class="mt-4 row">
                <label class="col-sm-1">Nama</label>
                <div class="col">
                    : {{$startName}}
                </div>
            </h5>
            <h5 class="row">
                <label class="col-sm-1">Email</label>
                <div class="col">
                    : {{preg_replace("/(?!^).(?!$)/", "*", $startEmail)}}
                </div>
            </h5>
            {{-- <h5 class="row">
                <label class="col-sm-1">Password</label>
                <div type="password" class="col">
                    : {{$password}}
                </div>
            </h5> --}}
            <div class="mt-3">
                <button wire:click="edit(true)" class="btn btn-primary">Edit</button>
            </div>
        @else
            <h2>Edit Profil</h2>
            <form>
                @csrf
                <div class="mt-4 form-group">
                    <label for="name" class="col-form-label">Nama</label>
                    <input type="text" class="form-control" id="name" wire:model="name" required>
                </div>
                <div class="form-group">
                    <label for="email" class="col-form-label">Email</label>
                    <input type="email" class="form-control" id="email" wire:model="email" required>
                </div>
                <div class="form-group">
                    <label for="password" class="col-form-label">Password</label>
                    <input type="password" class="form-control" id="password" wire:model="password">
                </div>
                <div class="form-group">
                    <label for="password_confirmation" class="col-form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="password_confirmation" wire:model="password_confirmation">
                </div>
                <div class="mt-3">
                    <button wire:click="edit(false)" type="reset" class="btn btn-danger">Batal</button>
                    <button wire:click="saveData()" name="submit" type="button" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        @endif
    </div>
</div>
