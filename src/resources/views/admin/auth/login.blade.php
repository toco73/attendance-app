@extends('admin.layouts.app')

@section('title','管理者ログイン')

@php
$bodyClass = 'auth';
@endphp

@section('content')
<div class="auth__container">
    <h1 class="auth-page__title">管理者ログイン</h1>
    <form class="form" action="/admin/login" method="post">
        @csrf
        <label class="form__label" for="email">メールアドレス</label>
        <input type="email" name="email" id="email" class="form__input" value="{{old('email')}}">
        <div class="form__error">
            @error('email')
            {{$message}}
            @enderror
        </div>
        <label class="form__label" for="password">パスワード</label>
        <input type="password" name="password" id="password" class="form__input" value="{{old('password')}}">
        <div class="form__error">
            @error('password')
            {{$message}}
            @enderror
        </div>
        <button class="form__button button" type="submit">管理者ログインする</button>
    </form>
</div>
@endsection