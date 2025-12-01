@extends('layouts.app')

@section('title','会員登録')

@section('css')
<link rel="stylesheet" href="{{asset('/css/register.css')}}">
@endsection

@php
$bodyClass = 'auth';
@endphp

@section('content')
<div class="auth__container">
    <h1 class="auth-page__title">会員登録</h1>
    <form class="form" action="/register" method="post">
        @csrf
            <label class="form__label" for="name">名前</label>
            <input type="text" name="name" id="name" class="form__input" value="{{old('name')}}">
            <div class="form__error">
                @error('name')
                {{$message}}
                @enderror
            </div>
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
            <label class="form__label" for="password_confirmation">パスワード確認</label>
            <input type="password" name="password_confirmation" class="form__input">
            <button class="form__button button" type="submit">登録する</button>
    </form>
    <a class="link-button" href="/login">ログインはこちら</a>
</div>
@endsection