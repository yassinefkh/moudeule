@extends('layouts.modele')
@section('title', 'Changer MDP')
@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Changer de mot de passe</div>

                    <div class="card-body">
                        @if (session('success'))
                            <x-alert type="success" :message="session('success')" />
                        @endif

                        @if (session('error'))
                            <x-alert type="danger" :message="session('error')" />
                        @endif

                        @if (session('warning'))
                            <x-alert type="warning" :message="session('warning')" />
                        @endif

                        @if (session('info'))
                            <x-alert type="info" :message="session('info')" />
                        @endif
                        <form method="POST" action="{{ route('user.changePassword') }}">
                            @csrf
                            <div class="form-group" style="margin-bottom: 25px;">
                                <input type="password" name="current_password" class="form-control form-control-lg"
                                    placeholder="Ancien mot de passe" />
                                <label class="form-label" for="form3Example3">Ancien mot de passe</label>
                                @error('current_password')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group" style="margin-bottom: 25px;">
                                <label class="form-label"
                                    style="font-weight: bold; margin-bottom: 8px; display: block;">Nouveau mot de
                                    passe</label>
                                <input type="password" class="form-control" name="new_password"
                                    placeholder="Entrez votre nouveau mot de passe">
                                @error('new_password')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group" style="margin-bottom: 25px;">
                                <label class="form-label"
                                    style="font-weight: bold; margin-bottom: 8px; display: block;">Confirmez votre nouveau
                                    mot de passe</label>
                                <input type="password" class="form-control" name="new_password_confirmation"
                                    placeholder="Confirmez votre nouveau mot de passe">
                                @error('new_password_confirmation')
                                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-center text-lg-start mt-4 pt-2">
                                <button type="submit" value="Envoyer" class="btn btn-primary btn-lg">Changer de mot de
                                    passe</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection