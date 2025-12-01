@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/verify-email.css')  }}">
@endsection

@php
$bodyClass = 'auth';
@endphp

@section('content')
<div class="verify__container">
    <p>登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</p>
    <a href="https://mailtrap.io/home" target="_blank">認証はこちらから</a>
    <form action="{{ route('verification.resend') }}" method="post">
        @csrf
        <button type="submit">認証メールを再送する</button>
    </form>
</div>
@endsection